<?php

declare(strict_types=1);

require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/SepayGateway.php';

/**
 * PaymentService - Điều phối thanh toán (Kế thừa nguyên bản từ Base Shop)
 * Quản lý vòng đời lượt thanh toán (bảng payments) và xử lý kết quả trả về từ cổng SePay.
 */
final class PaymentService
{
    private Payment $paymentModel;
    private Order $orderModel;
    private SepayGateway $sepayGateway;

    public function __construct(?SepayGateway $sepayGateway = null)
    {
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
        $this->sepayGateway = $sepayGateway ?? new SepayGateway();
    }

    /**
     * Bắt đầu thanh toán cho đơn. Trả về form cần gửi sang cổng hoặc null nếu là COD.
     *
     * @param array<string, mixed> $order
     * @param string|null $method Phương thức phụ SePay (BANK_TRANSFER, NAPAS_BANK_TRANSFER, CARD, hoặc null)
     * @return array{action: string, fields: array<string, string>}|null
     */
    public function start(array $order, ?string $method = null): ?array
    {
        $methodCode = (string)($order['payment_method'] ?? 'cod');
        if ($methodCode === 'cod') {
            return null;
        }

        if (!$this->sepayGateway->isEnabled()) {
            throw new DomainException('Cổng thanh toán SePay đang tạm tắt hoặc chưa cấu hình đầy đủ.');
        }

        if (($order['payment_status'] ?? '') === 'paid') {
            throw new DomainException('Đơn hàng này đã được thanh toán trước đó.');
        }

        if (($order['order_status'] ?? '') === 'cancelled') {
            throw new DomainException('Đơn hàng đã bị huỷ, không thể thanh toán.');
        }

        $orderAmount = (int)($order['final_amount'] ?? $order['total'] ?? 0);
        $orderCode = (string)($order['order_code'] ?? $order['code'] ?? '');
        $orderId = (int)($order['id'] ?? 0);

        // Sinh mã hoá đơn SePay duy nhất (mỗi lần bấm thanh toán là 1 txn_ref riêng)
        $txnRef = $this->paymentModel->createPayment($orderId, $orderCode, $orderAmount, 'sepay');

        return $this->sepayGateway->createCheckoutInstance([
            'txn_ref' => $txnRef,
            'amount' => $orderAmount,
            'order_info' => 'Thanh toan don ' . $orderCode,
            'method' => $method,
            'success_url' => rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/success',
            'error_url' => rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/error',
            'cancel_url' => rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/cancel',
        ]);
    }

    /**
     * Xử lý IPN của SePay (POST JSON). Idempotent: SePay gửi lặp vẫn an toàn.
     *
     * @param string|null $secretKey giá trị header X-Secret-Key
     * @param array<string, mixed> $body JSON SePay gửi
     * @return array{status: int, body: array<string, mixed>}
     */
    public function handleSepayIpn(?string $secretKey, array $body): array
    {
        try {
            // 1. Kiểm tra khoá bí mật IPN
            if (!$this->sepayGateway->verifyIpnKeyInstance($secretKey)) {
                error_log('[SePay IPN] Xác thực thất bại: X-Secret-Key không hợp lệ');
                return ['status' => 401, 'body' => ['error' => 'Unauthorized: Invalid X-Secret-Key']];
            }

            $type = (string) ($body['notification_type'] ?? '');
            if ($type !== 'ORDER_PAID') {
                // Sự kiện khác (như TRANSACTION_VOID): trả 200 để SePay không gửi lại
                error_log('[SePay IPN] Bỏ qua notification_type: ' . $type);
                return ['status' => 200, 'body' => ['success' => true, 'message' => 'Ignored non-ORDER_PAID']];
            }

            // 2. Tìm payment theo mã hoá đơn
            $orderData = is_array($body['order'] ?? null) ? $body['order'] : [];
            $invoiceNumber = (string) ($orderData['order_invoice_number'] ?? '');
            $payment = $this->paymentModel->findByTxnRef($invoiceNumber);
            if ($payment === null) {
                return ['status' => 404, 'body' => ['error' => 'Payment transaction not found for invoice: ' . $invoiceNumber]];
            }

            // 3. Kiểm tra số tiền: Chấp nhận chuyển đủ hoặc chuyển thừa tiền (chống từ chối 422 khi khách chuyển thừa)
            $paid = SepayGateway::parseAmount($orderData['order_amount'] ?? null);
            $expectedAmount = (int) $payment['amount'];
            if ($paid === null || $paid < $expectedAmount) {
                error_log('[SePay IPN] Thiếu tiền đơn hàng. Kỳ vọng: ' . $expectedAmount . ', Thực nhận: ' . ($paid ?? 'null'));
                return ['status' => 422, 'body' => ['error' => 'Underpaid amount: received less than required total']];
            }
            $isOverpaid = ($paid > $expectedAmount);
            $overpaidDiff = $paid - $expectedAmount;

            // 4. Kiểm tra đơn hàng không bị hủy (chống hồi sinh đơn đã hủy)
            $order = $this->orderModel->getByCode($payment['order_code']);
            if ($order && ($order['order_status'] ?? '') === 'cancelled') {
                $transaction = is_array($body['transaction'] ?? null) ? $body['transaction'] : [];
                $txId = (string)($transaction['transaction_id'] ?? $transaction['id'] ?? ('SP_' . time()));
                $this->paymentModel->markSuccess((int)$payment['id'], $txId, 'sepay', $type, $body);

                // Đánh dấu cần hoàn tiền vì đơn đã bị hủy trước đó
                $stmt = $this->orderModel->getDb()->prepare(
                    "UPDATE orders SET payment_status = 'refunded', bank_transaction_id = ?, refund_info = CONCAT(IFNULL(refund_info, ''), ' [Nhận thanh toán sau khi đơn đã hủy]') WHERE id = ?"
                );
                $stmt->execute([$txId, $order['id']]);

                return ['status' => 409, 'body' => ['error' => 'Order is already cancelled. Marked as refunded.']];
            }

            // 5. Cập nhật thành công (Idempotent 100%)
            $transaction = is_array($body['transaction'] ?? null) ? $body['transaction'] : [];
            $txId = (string)($transaction['transaction_id'] ?? $transaction['id'] ?? ('SP_' . time()));
            $bankCode = (string)($transaction['payment_method'] ?? 'sepay');

            $this->markSuccess($payment, [
                'transaction_no' => $txId,
                'bank_code' => $bankCode,
                'response_code' => $type,
                'raw' => $body,
            ], 'IPN');

            if ($isOverpaid) {
                $overpaidMsg = "\n[CẢNH BÁO IPN " . date('d/m/Y H:i') . "]: Khách thanh toán thừa " . number_format($overpaidDiff, 0, ',', '.') . "đ (Cần thanh toán: " . number_format($expectedAmount, 0, ',', '.') . "đ, Thực chuyển: " . number_format($paid, 0, ',', '.') . "đ).";
                $this->orderModel->getDb()->prepare("UPDATE orders SET notes = CONCAT(IFNULL(notes, ''), ?) WHERE order_code = ?")->execute([$overpaidMsg, $payment['order_code']]);
            }

            return ['status' => 200, 'body' => ['success' => true]];
        } catch (Throwable $e) {
            error_log('[SePay IPN] Lỗi hệ thống: ' . $e->getMessage());
            return ['status' => 500, 'body' => ['error' => 'Server error: ' . $e->getMessage()]];
        }
    }

    /**
     * Đối soát 1 lượt thanh toán SePay qua API chi tiết đơn (GET /v1/order/detail/{invoiceNumber}).
     * Dùng cho Admin và cho success_url khi SEPAY_RECONCILE_ON_RETURN = true.
     *
     * @param array<string, mixed> $payment
     * @return array{updated: bool, paid: bool, message: string, response: array<string, mixed>}
     */
    public function reconcileSepay(array $payment): array
    {
        $response = $this->sepayGateway->retrieveOrderInstance((string) $payment['txn_ref']);
        $order = SepayGateway::orderFromResponse($response);

        $status = (string) ($order['order_status'] ?? '');
        $amountOk = SepayGateway::parseAmount($order['order_amount'] ?? null) === (int) $payment['amount'];
        $paid = $status === SepayGateway::PAID_ORDER_STATUS && $amountOk;

        $updated = false;
        if ($paid) {
            $updated = $this->markSuccess($payment, [
                'transaction_no' => (string)($order['id'] ?? ('SEPAY_' . $payment['txn_ref'])),
                'bank_code' => (string)($order['payment_method'] ?? 'sepay'),
                'response_code' => $status,
                'raw' => $response,
            ], 'đối soát');
        }

        $message = match (true) {
            $status === '' => 'SePay không trả về trạng thái đơn hàng.',
            $status === SepayGateway::PAID_ORDER_STATUS && !$amountOk => 'SePay báo đã thu tiền nhưng số tiền không khớp, không cập nhật.',
            default => 'Trạng thái đơn trên SePay: ' . $status . ($updated ? ' — Đã cập nhật đơn thành công.' : '.'),
        };

        return [
            'updated' => $updated,
            'paid' => $paid,
            'message' => $message,
            'response' => $response,
        ];
    }

    /**
     * Ghi nhận thanh toán thành công vào DB.
     * UPDATE có điều kiện status = 'pending' đảm bảo tính Idempotent an toàn khi IPN và Đối soát gọi cùng lúc.
     *
     * @param array<string, mixed> $payment
     * @param array{transaction_no: ?string, bank_code: ?string, response_code: string, raw: array<string, mixed>} $data
     */
    public function markSuccess(array $payment, array $data, string $source): bool
    {
        $updated = $this->paymentModel->markSuccess(
            (int) $payment['id'],
            $data['transaction_no'] ?? null,
            $data['bank_code'] ?? null,
            $data['response_code'],
            $data['raw']
        );

        if (!$updated) {
            return false; // Đã được xử lý trước đó
        }

        // Cập nhật trạng thái đơn hàng sang paid
        $orderCode = (string)$payment['order_code'];
        $txId = (string)($data['transaction_no'] ?? ('SEPAY_' . $payment['txn_ref']));
        $this->orderModel->updatePaymentStatus($orderCode, 'paid', $txId);

        error_log("[PaymentService] Giao dịch {$payment['txn_ref']} đã xử lý thành công ({$source})");
        return true;
    }

    public function getGateway(): SepayGateway
    {
        return $this->sepayGateway;
    }
}
