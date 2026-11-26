<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';

/**
 * WebhookController - Tự động tiếp nhận Webhook từ Cổng SePay (IPN) & Biến động số dư
 * Kế thừa cơ chế bảo vệ 5 lớp chống gian lận từ Base Shop
 */
class WebhookController extends BaseController
{
    public function handle(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        // 1. Đọc header xác thực bảo mật
        $secretKey = $_SERVER['HTTP_X_SECRET_KEY'] ?? $_SERVER['X_SECRET_KEY'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (is_string($secretKey) && str_starts_with($secretKey, 'Apikey ')) {
            $secretKey = substr($secretKey, 7);
        }

        // Chốt bảo mật số 1: Luôn bắt buộc xác thực Secret Key trên mọi luồng Webhook
        if (!SepayGateway::verifyIpnKey($secretKey)) {
            $this->json(['error' => 'Unauthorized: Invalid X-Secret-Key / API Key'], 401);
            return;
        }

        // 2. Đọc raw JSON body
        $rawInput = file_get_contents('php://input');
        $data = json_decode((string)$rawInput, true);

        if (empty($data) && !empty($_POST)) {
            $data = $_POST;
        }

        if (!is_array($data) || empty($data)) {
            $this->json(['error' => 'No webhook payload received'], 400);
            return;
        }

        $orderModel = new Order();

        // ==============================================================
        // LUỒNG 1: SEPAY GATEWAY IPN CHUẨN (CHECKOUT V1)
        // ==============================================================
        if (isset($data['notification_type']) || isset($data['order']['order_invoice_number'])) {
            $type = (string)($data['notification_type'] ?? '');
            if ($type !== 'ORDER_PAID') {
                $this->json(['success' => true, 'message' => 'Event ' . $type . ' acknowledged'], 200);
                return;
            }

            $orderData = is_array($data['order'] ?? null) ? $data['order'] : [];
            $txnRef = (string)($orderData['order_invoice_number'] ?? '');

            $paymentModel = new Payment();
            $payment = $paymentModel->findByTxnRef($txnRef);

            // Chốt 2: Kiểm tra sự tồn tại của giao dịch
            if ($payment === null) {
                $this->json(['error' => 'Payment transaction not found for invoice: ' . $txnRef], 404);
                return;
            }

            // Chốt 3: Kiểm tra số tiền bằng parseAmount (hỗ trợ int/float/string an toàn)
            $paidAmount = SepayGateway::parseAmount($orderData['order_amount'] ?? null);
            if ($paidAmount === null || $paidAmount < (int)$payment['amount']) {
                $this->json(['error' => 'Invalid amount. Expected: ' . $payment['amount'] . ', Got: ' . $paidAmount], 422);
                return;
            }

            $order = $orderModel->getByCode($payment['order_code']);
            if (!$order) {
                $this->json(['error' => 'Order not found for code: ' . $payment['order_code']], 404);
                return;
            }

            $tx = is_array($data['transaction'] ?? null) ? $data['transaction'] : [];
            $txId = (string)($tx['transaction_id'] ?? $tx['id'] ?? ('SP_' . time()));
            $bankCode = (string)($tx['payment_method'] ?? 'sepay');

            // Chốt 4: Kiểm tra chống "hồi sinh" đơn đã hủy
            if ($order['order_status'] === 'cancelled') {
                $paymentModel->markSuccess((int)$payment['id'], $txId, $bankCode, $type, $data);
                $stmt = $orderModel->getDb()->prepare("UPDATE orders SET payment_status = 'refunded', bank_transaction_id = ?, refund_info = CONCAT(IFNULL(refund_info, ''), ' [Nhận thanh toán sau khi đơn đã hủy]') WHERE id = ?");
                $stmt->execute([$txId, $order['id']]);

                $this->json([
                    'error' => 'Order is already cancelled. Marked as refunded for manual review.',
                    'order_code' => $order['order_code']
                ], 409);
                return;
            }

            // Chốt 5: Cập nhật thành công (Chống trùng lặp Idempotent)
            $updated = $paymentModel->markSuccess((int)$payment['id'], $txId, $bankCode, $type, $data);
            $orderModel->updatePaymentStatus($payment['order_code'], 'paid', $txId);

            // Cảnh báo nếu chuyển thừa tiền
            $transferAmount = (float)($data['transferAmount'] ?? $data['amount'] ?? 0);
            if ($transferAmount > (float)$order['final_amount']) {
                $excess = $transferAmount - (float)$order['final_amount'];
                $excessNote = " [CẢNH BÁO TÀI CHÍNH: Khách chuyển thừa " . number_format($excess, 0, ',', '.') . "đ. Cần đối soát hoàn trả]";
                $stmt = $orderModel->getDb()->prepare("UPDATE orders SET notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ?");
                $stmt->execute([$excessNote, $order['id']]);
            }

            $this->json([
                'success' => true,
                'message' => 'SePay IPN processed successfully',
                'order_code' => $payment['order_code'],
                'is_first_time' => $updated
            ], 200);
            return;
        }

        // ==============================================================
        // LUỒNG 2: BIẾN ĐỘNG SỐ DƯ VIETQR / SMS TRUYỀN THỐNG (FALLBACK)
        // ==============================================================
        $content = '';
        $amount = 0;
        $referenceCode = '';

        if (!empty($data['content'])) {
            $content = (string)$data['content'];
            $amount = (float)($data['transferAmount'] ?? 0);
            $referenceCode = (string)($data['referenceCode'] ?? $data['id'] ?? ('SEPAY_' . time()));
        } elseif (!empty($data['data']) && is_array($data['data'])) {
            $firstTx = $data['data'][0] ?? [];
            $content = (string)($firstTx['description'] ?? '');
            $amount = (float)($firstTx['amount'] ?? 0);
            $referenceCode = (string)($firstTx['tid'] ?? ('CASSO_' . time()));
        }

        if (empty($content)) {
            $this->json(['error' => 'Missing transaction content'], 400);
            return;
        }

        $orderCode = '';
        if (preg_match('/(TS[A-Za-z0-9_-]+)/i', $content, $matches)) {
            $orderCode = strtoupper($matches[1]);
        } elseif (preg_match('/(NEXVORA|TECHSTORE)\s*([A-Za-z0-9_-]+)/i', $content, $matches)) {
            $orderCode = strtoupper($matches[2]);
        }

        if (empty($orderCode)) {
            $this->json(['error' => 'Order code not found in content: ' . $content], 422);
            return;
        }

        $order = $orderModel->getByCode($orderCode);
        if (!$order) {
            $this->json(['error' => 'Order not found for code: ' . $orderCode], 404);
            return;
        }

        // Chốt kiểm tra số tiền chuyển khoản tối thiểu phải bằng giá trị đơn hàng
        $expectedAmount = (float)$order['final_amount'];
        if ($amount < $expectedAmount) {
            $this->json(['error' => "Invalid amount. Expected at least: {$expectedAmount}, Got: {$amount}"], 422);
            return;
        }

        // Chốt chống "hồi sinh" đơn đã hủy
        if ($order['order_status'] === 'cancelled') {
            $stmt = $orderModel->getDb()->prepare("UPDATE orders SET payment_status = 'refunded', bank_transaction_id = ?, refund_info = CONCAT(IFNULL(refund_info, ''), ' [Nhận thanh toán sau khi đơn đã hủy]') WHERE id = ?");
            $stmt->execute([$referenceCode, $order['id']]);

            $this->json([
                'error' => 'Order is already cancelled. Marked as refunded for manual review.',
                'order_code' => $orderCode
            ], 409);
            return;
        }

        if ($order['payment_status'] === 'paid') {
            $this->json(['success' => true, 'message' => 'Order already paid'], 200);
            return;
        }

        $orderModel->updatePaymentStatus($orderCode, 'paid', $referenceCode);

        // Cảnh báo nếu chuyển thừa tiền
        if ($amount > (float)$order['final_amount']) {
            $excess = $amount - (float)$order['final_amount'];
            $excessNote = " [CẢNH BÁO TÀI CHÍNH: Khách chuyển thừa " . number_format($excess, 0, ',', '.') . "đ. Cần đối soát hoàn trả]";
            $stmt = $orderModel->getDb()->prepare("UPDATE orders SET notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ?");
            $stmt->execute([$excessNote, $order['id']]);
        }

        $this->json([
            'success' => true,
            'message' => "Order {$orderCode} marked as PAID via Bank Webhook",
            'order_code' => $orderCode,
            'transaction_id' => $referenceCode
        ], 200);
    }
}
