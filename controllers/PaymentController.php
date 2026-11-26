<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../services/SepayGateway.php';
require_once __DIR__ . '/../services/PaymentService.php';

final class PaymentController extends BaseController
{
    private PaymentService $paymentService;
    private Payment $paymentModel;
    private Order $orderModel;

    public function __construct(?PaymentService $paymentService = null)
    {
        parent::__construct(); // [SỬA LỖI] Không gọi constructor cha -> bỏ qua kiểm tra phiên/tài khoản bị khóa
        $this->paymentService = $paymentService ?? new PaymentService();
        $this->paymentModel = new Payment();
        $this->orderModel = new Order();
    }

    /**
     * IPN: SePay gọi server-to-server (POST JSON + header X-Secret-Key).
     * Nơi cập nhật đơn chính thức và an toàn nhất.
     */
    public function sepayIpn(): void
    {
        $secretKey = $_SERVER['HTTP_X_SECRET_KEY'] ?? $_SERVER['X_SECRET_KEY'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? null;
        if (is_string($secretKey) && str_starts_with($secretKey, 'Apikey ')) {
            $secretKey = substr($secretKey, 7);
        }

        $rawInput = file_get_contents('php://input');
        $body = json_decode((string)$rawInput, true);
        if (!is_array($body) || empty($body)) {
            $body = $_POST;
        }

        $result = $this->paymentService->handleSepayIpn($secretKey, (array)$body);
        $this->json($result['body'], $result['status']);
    }

    /**
     * success_url: SePay redirect trình duyệt về đây.
     * KHÔNG có chữ ký => không tin URL, trạng thái hiển thị luôn lấy từ DB.
     * Nếu bật SEPAY_RECONCILE_ON_RETURN (dev/localhost) => tự đối soát API SePay ngay.
     */
    public function sepaySuccess(): void
    {
        $ref = (string)($_GET['ref'] ?? '');
        $payment = $ref !== '' ? $this->paymentModel->findByTxnRef($ref) : null;
        $note = null;

        $reconcileEnabled = (bool)($this->paymentService->getGateway()->config('reconcile_on_return', true));

        if ($payment !== null && $payment['status'] === Payment::STATUS_PENDING && $reconcileEnabled) {
            try {
                $reconcile = $this->paymentService->reconcileSepay($payment);
                if (!$reconcile['paid']) {
                    $note = $reconcile['message'];
                }
            } catch (Throwable $e) {
                error_log('SePay: đối soát tại success_url lỗi: ' . $e->getMessage());
                $note = 'Chưa thể đối soát với SePay: ' . $e->getMessage();
            }
            $payment = $this->paymentModel->findByTxnRef($ref);
        }

        $this->result($payment, 'success', $note);
    }

    /**
     * error_url: SePay báo thanh toán lỗi.
     */
    public function sepayError(): void
    {
        $ref = (string)($_GET['ref'] ?? '');
        $payment = $ref !== '' ? $this->paymentModel->findByTxnRef($ref) : null;
        $this->result($payment, 'error');
    }

    /**
     * cancel_url: Khách huỷ thanh toán trên cổng SePay.
     */
    public function sepayCancel(): void
    {
        $ref = (string)($_GET['ref'] ?? '');
        $payment = $ref !== '' ? $this->paymentModel->findByTxnRef($ref) : null;
        $this->result($payment, 'cancel');
    }

    /**
     * simulate: Giả lập thanh toán nhanh cho môi trường Sandbox / Demo đồ án.
     */
    public function sepaySimulate(): void
    {
        $ref = (string)($_GET['ref'] ?? $_POST['ref'] ?? '');
        if (empty($ref)) {
            $this->redirect(BASE_URL);
            return;
        }

        // [CHỐT BẢO MẬT TUYỆT ĐỐI] Chống khách hàng tự ý giả lập thanh toán đơn của mình
        // 1. Môi trường Production: Vô hiệu hóa 100% tính năng simulate
        // 2. Chế độ Sandbox: CHỈ DUY NHẤT Quản trị viên (Admin) mới có quyền test simulate, cấm tuyệt đối khách thường
        $isProduction = (defined('APP_ENV') && APP_ENV === 'production');
        $isAdmin = !empty($_SESSION['admin_user']);

        if ($isProduction || SepayGateway::env() !== 'sandbox' || !$isAdmin || !Csrf::verify($_GET['csrf_token'] ?? $_POST['_csrf_token'] ?? null)) {
            http_response_code(403);
            $_SESSION['flash_err'] = 'Tính năng giả lập thanh toán bị khóa vì lý do an toàn bảo mật!';
            $this->redirect(BASE_URL);
            return;
        }

        $payment = $this->paymentModel->findByTxnRef($ref);
        if (!$payment) {
            $this->redirect(BASE_URL);
            return;
        }

        $order = $this->orderModel->getByCode($payment['order_code']);
        if (!$order || ($order['order_status'] ?? '') === 'cancelled') {
            $_SESSION['flash_user_order_err'] = 'Đơn hàng này đã bị hủy, không thể kích hoạt thanh toán!';
            $this->redirect(BASE_URL . 'index.php?url=payment/sepay/error&ref=' . urlencode($ref));
            return;
        }

        $simulatedTxId = 'SEPAY_SIM_' . strtoupper(bin2hex(random_bytes(5)));
        $mockResponse = [
            'simulated' => true,
            'source' => 'NEXVORA SePay Sandbox Simulator',
            'amount' => (int)$payment['amount'],
            'order_status' => 'CAPTURED',
            'transaction_id' => $simulatedTxId,
            'paid_at' => date('Y-m-d H:i:s'),
        ];

        $this->paymentService->markSuccess($payment, [
            'transaction_no' => $simulatedTxId,
            'bank_code' => 'BANK_TRANSFER',
            'response_code' => 'CAPTURED',
            'raw' => $mockResponse,
        ], 'giả lập');

        $this->redirect(BASE_URL . 'index.php?url=payment/sepay/success&ref=' . urlencode($ref));
    }

    /**
     * Hiển thị giao diện kết quả thanh toán đồng bộ 100% với dự án Base.
     */
    private function result(?array $payment, string $redirect, ?string $note = null): void
    {
        $order = ($payment && !empty($payment['order_code'])) ? $this->orderModel->getByCode($payment['order_code']) : null;
        $success = ($payment !== null && ($payment['status'] ?? '') === Payment::STATUS_SUCCESS);

        $message = match (true) {
            $payment === null => 'Không tìm thấy thông tin giao dịch tương ứng.',
            $success => 'Hệ thống SePay đã xác nhận thanh toán thành công!',
            $redirect === 'cancel' => 'Bạn đã huỷ thanh toán trên cổng SePay. Đơn hàng vẫn được lưu giữ, bạn có thể thanh toán lại bất kỳ lúc nào.',
            $redirect === 'error' => 'Giao dịch thanh toán không thành công. Bạn có thể kiểm tra lại số dư hoặc thử thanh toán lại.',
            default => 'Đang chờ hệ thống SePay xác nhận (IPN). Trạng thái đơn sẽ được cập nhật tự động trong ít phút.',
        };

        $this->render('payment/result', [
            'pageTitle' => $success ? 'Thanh toán thành công' : 'Kết quả thanh toán SePay',
            'success' => $success,
            'message' => $message,
            'note' => $note,
            'order' => $order,
            'payment' => $payment,
            'redirect' => $redirect,
            'methods' => SepayGateway::PAYMENT_METHODS,
        ]);
    }
}
