<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController extends BaseController {
    public function success(): void {
        $orderCode = trim($_GET['code'] ?? '');
        if (!$orderCode) {
            $this->redirect(BASE_URL);
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);

        if (!$order) {
            $this->redirect(BASE_URL);
            return;
        }

        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        $isOwnerOrAdmin = false;

        // 1. Nếu là User đăng nhập khớp với chủ đơn
        if (!empty($currentUser['id']) && (int)$order['user_id'] === (int)$currentUser['id']) {
            $isOwnerOrAdmin = true;
        }
        // 2. Nếu là Quản trị viên Admin
        elseif (!empty($_SESSION['admin_user'])) {
            $isOwnerOrAdmin = true;
        }
        // 3. Nếu là Khách vừa hoàn tất đặt đơn trong phiên làm việc hiện tại
        elseif ((!empty($_SESSION['last_order_code']) && $_SESSION['last_order_code'] === $orderCode) ||
                (!empty($_SESSION['recent_orders']) && in_array($orderCode, (array)$_SESSION['recent_orders'], true))) {
            $isOwnerOrAdmin = true;
        }

        // Nếu người lạ/ẩn danh truy cập: Che mờ toàn bộ thông tin cá nhân (PII)
        if (!$isOwnerOrAdmin) {
            if (!empty($order['customer_phone'])) {
                $order['customer_phone'] = preg_replace('/(\d{3})\d{4}(\d{3})/', '$1****$2', $order['customer_phone']);
            }
            if (!empty($order['customer_email'])) {
                $order['customer_email'] = preg_replace('/(.).+(@.+)/', '$1***$2', $order['customer_email']);
            }
            if (!empty($order['customer_name'])) {
                $parts = explode(' ', trim($order['customer_name']));
                if (count($parts) > 1) {
                    $first = array_shift($parts);
                    $last = array_pop($parts);
                    $order['customer_name'] = $first . ' *** ' . $last;
                } else {
                    $order['customer_name'] = substr($order['customer_name'], 0, 1) . '***';
                }
            }
            if (!empty($order['customer_address'])) {
                $parts = explode(',', $order['customer_address']);
                $order['customer_address'] = (count($parts) > 1) ? ('***, ' . trim(end($parts))) : '*** (Đã ẩn bảo mật PII)';
            }
        }

        $this->render('order/success', [
            'pageTitle' => 'Đặt hàng thành công - ' . $order['order_code'],
            'order' => $order,
            'isOwnerOrAdmin' => $isOwnerOrAdmin
        ]);
    }

    public function track(): void {
        $orderModel = new Order();
        $keyword = trim($_GET['search'] ?? $_GET['q'] ?? '');
        $orders = [];
        $searched = false;
        $securityNotice = null;

        if ($keyword !== '') {
            $searched = true;
            // 1. Nếu tìm kiếm theo Mã đơn hàng (VD: TS...)
            $order = $orderModel->getByCode($keyword);
            if ($order) {
                $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
                $isOwnerOrAdmin = (!empty($currentUser['id']) && (int)$order['user_id'] === (int)$currentUser['id']) || !empty($_SESSION['admin_user']);

                // Nếu khách tra cứu công khai không phải chủ tài khoản: Che thông tin cá nhân nhạy cảm
                if (!$isOwnerOrAdmin) {
                    if (!empty($order['customer_phone'])) {
                        $order['customer_phone'] = preg_replace('/(\d{3})\d{4}(\d{3})/', '$1****$2', $order['customer_phone']);
                    }
                    if (!empty($order['customer_email'])) {
                        $order['customer_email'] = preg_replace('/(.).+(@.+)/', '$1***$2', $order['customer_email']);
                    }
                    if (!empty($order['customer_address'])) {
                        $parts = explode(',', $order['customer_address']);
                        $order['customer_address'] = (count($parts) > 1) ? ('***, ' . trim(end($parts))) : '*** (Đã ẩn bảo mật)';
                    }
                }
                $orders = [$order];
            } else {
                // 2. Tra cứu theo số điện thoại:
                // RÀNG BUỘC BẢO MẬT: Chỉ cho phép tra cứu toàn bộ danh sách đơn khi:
                // - Đã đăng nhập và SĐT tìm kiếm trùng khớp với tài khoản cá nhân
                // - Hoặc là Quản trị viên (Admin)
                $currentUser = $_SESSION['user'] ?? null;
                $isAdmin = !empty($_SESSION['admin_user']);

                if ($isAdmin || (!empty($currentUser['phone']) && $currentUser['phone'] === $keyword)) {
                    $orders = $orderModel->getByPhone($keyword);
                } elseif (!preg_match('/^0\d{9}$/', $keyword)) {
                    // [SỬA LỖI] Từ khóa không phải mã đơn hợp lệ và cũng không phải SĐT -> báo không tìm thấy (trước đây hiện nhầm thông báo bảo mật)
                    $orders = [];
                } else {
                    $securityNotice = 'Để bảo vệ quyền riêng tư và thông tin mua hàng của khách hàng, việc tra cứu toàn bộ lịch sử đơn theo Số Điện Thoại yêu cầu Đăng nhập vào tài khoản của bạn, hoặc vui lòng nhập chính xác Mã Đơn Hàng (Ví dụ: TS...).';
                }
            }
        }

        if (!empty($orders)) {
            foreach ($orders as &$ord) {
                $ord['return_eligibility'] = $orderModel->checkReturnEligibility($ord);
            }
            unset($ord);
        }

        $this->render('order/track', [
            'pageTitle' => 'Tra cứu hành trình đơn hàng & Bảo hành điện tử',
            'orders' => $orders,
            'keyword' => $keyword,
            'searched' => $searched,
            'securityNotice' => $securityNotice
        ]);
    }

    /**
     * Khách hàng xác nhận đã nhận được hàng -> Đơn hàng hoàn thành & đã thanh toán
     */
    public function confirmReceived(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        if (!Csrf::validateRequest()) {
            $_SESSION['flash_user_order_err'] = 'Phiên làm việc không hợp lệ (CSRF). Vui lòng thử lại!';
            $this->redirect(BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $orderCode = trim($_POST['order_code'] ?? '');
        if (!$orderCode) {
            $this->redirect(BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);

        if ($order) {
            $currentUser = $_SESSION['user'] ?? null;
            // Kiểm tra quyền đơn hàng thuộc về user đang đăng nhập
            if ($currentUser && (int)$order['user_id'] === (int)$currentUser['id']) {
                // Ràng buộc nghiệp vụ: Đơn hàng chỉ được xác nhận "Đã nhận hàng" khi ĐANG GIAO (shipping)
                if ($order['order_status'] !== 'shipping') {
                    $_SESSION['flash_user_order_err'] = "Đơn hàng #{$orderCode} hiện chưa trong quá trình giao hàng, không thể xác nhận nhận hàng lúc này.";
                    $this->redirect(BASE_URL . 'index.php?url=auth/orders');
                    return;
                }

                // Đối với phương thức thanh toán online (chuyển khoản, VietQR, SePay):
                // Bắt buộc phải đã thanh toán (payment_status = 'paid')
                if ($order['payment_method'] !== 'cod' && $order['payment_status'] !== 'paid') {
                    $_SESSION['flash_user_order_err'] = "Đơn hàng trực tuyến này chưa được ghi nhận đã thanh toán, không thể hoàn tất!";
                    $this->redirect(BASE_URL . 'index.php?url=auth/orders');
                    return;
                }

                $orderModel->updateOrderStatus((int)$order['id'], 'completed', 'paid');
                $_SESSION['flash_user_order_msg'] = "Cảm ơn quý khách! Bạn đã xác nhận nhận hàng thành công cho đơn #" . htmlspecialchars($orderCode) . ". Đơn hàng đã hoàn thành!";
            }
        }

        $this->redirect(BASE_URL . 'index.php?url=auth/orders');
    }

    /**
     * Khách hàng hủy đơn hàng (kèm thông tin hoàn tiền nếu đã thanh toán)
     */
    public function cancel(): void {
        $orderCode = trim($_POST['order_code'] ?? $_GET['code'] ?? '');
        $redirectUrl = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : BASE_URL . 'index.php?url=auth/orders';

        if (!Csrf::validateRequest()) {
            $_SESSION['flash_user_order_err'] = 'Phiên làm việc không hợp lệ (CSRF). Vui lòng thử lại!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        if (empty($orderCode)) {
            $_SESSION['flash_user_order_err'] = 'Thiếu mã đơn hàng cần hủy!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);

        if (!$order) {
            $_SESSION['flash_user_order_err'] = 'Không tìm thấy đơn hàng #' . htmlspecialchars($orderCode) . '!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        // Kiểm tra quyền hủy: Phải là chủ đơn hàng (user_id) hoặc khách hàng tra cứu có SĐT đúng hoặc Admin
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        $customerPhone = trim($_POST['customer_phone'] ?? '');

        $hasPermission = false;
        if (!empty($currentUser['id']) && (int)$order['user_id'] === (int)$currentUser['id']) {
            $hasPermission = true;
        } elseif (!empty($_SESSION['admin_user'])) {
            $hasPermission = true;
        } elseif (empty($order['user_id']) && !empty($customerPhone) && $customerPhone === $order['customer_phone']) {
            // [SỬA LỖI] Xác minh bằng SĐT chỉ áp dụng cho đơn không gắn tài khoản. Đơn của thành viên phải đăng nhập đúng chủ tài khoản
            // (trước đây ai biết mã đơn + SĐT đều hủy/trả hàng thay được đơn của thành viên khác).
            $hasPermission = true;
        }

        if (!$hasPermission) {
            $_SESSION['flash_user_order_err'] = 'Bạn không có quyền thao tác trên đơn hàng này!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        // Kiểm tra trạng thái đơn: Chỉ cho phép hủy khi đang Chờ xử lý hoặc Đã xác nhận (chưa xuất kho giao)
        if (!in_array($order['order_status'], ['pending', 'confirmed'])) {
            $_SESSION['flash_user_order_err'] = 'Đơn hàng #' . htmlspecialchars($orderCode) . ' đang được giao hoặc đã hoàn thành, không thể tự hủy trực tiếp. Vui lòng liên hệ hotline CSKH!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $reason = trim($_POST['cancel_reason'] ?? $_POST['reason'] ?? 'Khách yêu cầu hủy đơn');
        $customReason = trim($_POST['custom_reason'] ?? '');
        if ($reason === 'other' || !empty($customReason)) {
            $reason = !empty($customReason) ? $customReason : 'Lý do khác';
        }

        $refundData = null;
        if ($order['payment_status'] === 'paid') {
            $bankName = trim($_POST['bank_name'] ?? '');
            $accountNumber = trim($_POST['account_number'] ?? '');
            $accountHolder = strtoupper(trim($_POST['account_holder'] ?? ''));
            $refundNote = trim($_POST['refund_note'] ?? '');

            if (empty($bankName) || empty($accountNumber) || empty($accountHolder)) {
                $_SESSION['flash_user_order_err'] = 'Đơn hàng này đã thanh toán online. Vui lòng nhập đầy đủ Tên ngân hàng, Số tài khoản và Tên chủ tài khoản để NEXVORA hoàn trả tiền!';
                $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
                return;
            }

            $refundData = [
                'bank_name' => $bankName,
                'account_number' => $accountNumber,
                'account_holder' => $accountHolder,
                'note' => $refundNote
            ];
        }

        $success = $orderModel->cancelOrder((int)$order['id'], $reason, $refundData);

        if ($success) {
            if (!empty($_POST['hide_after_cancel'])) {
                $orderModel->hideOrderByUser((int)$order['id'], (int)$order['user_id']);
            }

            if ($order['payment_status'] === 'paid') {
                $_SESSION['flash_user_order_msg'] = 'Đã hủy đơn hàng #' . htmlspecialchars($orderCode) . ' thành công! NEXVORA đã ghi nhận yêu cầu hoàn tiền ' . number_format($order['final_amount'], 0, ',', '.') . 'đ về tài khoản ' . htmlspecialchars($refundData['account_number']) . ' (' . htmlspecialchars($refundData['bank_name']) . '). Tiền sẽ được hoàn trả sớm nhất!';
            } else {
                $_SESSION['flash_user_order_msg'] = 'Đã hủy đơn hàng #' . htmlspecialchars($orderCode) . ' thành công! Số lượng sản phẩm đã được hoàn lại vào kho.';
            }
        } else {
            $_SESSION['flash_user_order_err'] = 'Không thể hủy đơn hàng vào lúc này. Vui lòng thử lại sau!';
        }

        $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
    }

    /**
     * Khách hàng xóa đơn hàng khỏi danh sách cá nhân
     * TUYỆT ĐỐI KHÔNG ẢNH HƯỞNG ĐẾN DỮ LIỆU ĐƠN HÀNG PHÍA ADMIN
     */
    public function deleteUserOrder(): void {
        $orderCode = trim($_POST['order_code'] ?? $_GET['code'] ?? '');
        $redirectUrl = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : BASE_URL . 'index.php?url=auth/orders';

        if (!Csrf::validateRequest()) {
            $_SESSION['flash_user_order_err'] = 'Phiên làm việc không hợp lệ (CSRF). Vui lòng thử lại!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        if (empty($orderCode)) {
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);

        if (!$order) {
            $_SESSION['flash_user_order_err'] = 'Không tìm thấy đơn hàng #' . htmlspecialchars($orderCode);
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $currentUser = $_SESSION['user'] ?? null;
        if (!$currentUser || (int)$order['user_id'] !== (int)$currentUser['id']) {
            $_SESSION['flash_user_order_err'] = 'Bạn không có quyền thao tác trên đơn hàng này!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $res = $orderModel->hideOrderByUser((int)$order['id'], (int)$currentUser['id']);
        if ($res) {
            $_SESSION['flash_user_order_msg'] = 'Đã xóa đơn hàng #' . htmlspecialchars($orderCode) . ' khỏi lịch sử mua sắm của bạn thành công!';
        } else {
            $_SESSION['flash_user_order_err'] = 'Không thể xóa đơn hàng lúc này!';
        }

        $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
    }

    /**
     * Khách hàng yêu cầu Trả Hàng & Hoàn Tiền
     * Hỗ trợ chính sách theo yêu cầu:
     * - Điện thoại: trong vòng 6 tháng kể từ khi nhận hàng.
     * - Tai nghe Bluetooth: trong vòng 7 ngày kể từ khi nhận hàng.
     * - Các phụ kiện khác: có thể trả khi xem hàng (đang giao). Nếu đã xác nhận lấy hàng thì không thể trả.
     * - Khi khách đã thanh toán mà trả hàng -> hoàn lại tiền, trừ doanh thu và hoàn lại kho.
     */
    public function returnOrder(): void {
        $orderCode = trim($_POST['order_code'] ?? $_GET['code'] ?? '');
        $redirectUrl = !empty($_POST['redirect_to']) ? $_POST['redirect_to'] : BASE_URL . 'index.php?url=auth/orders';

        if (!Csrf::validateRequest()) {
            $_SESSION['flash_user_order_err'] = 'Phiên làm việc không hợp lệ (CSRF). Vui lòng thử lại!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        if (empty($orderCode)) {
            $_SESSION['flash_user_order_err'] = 'Thiếu mã đơn hàng cần yêu cầu trả hàng!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $orderModel = new Order();
        $order = $orderModel->getByCode($orderCode);

        if (!$order) {
            $_SESSION['flash_user_order_err'] = 'Không tìm thấy đơn hàng #' . htmlspecialchars($orderCode) . '!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        // Kiểm tra quyền: Phải là chủ đơn hàng hoặc Admin
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        $customerPhone = trim($_POST['customer_phone'] ?? '');

        $hasPermission = false;
        if (!empty($currentUser['id']) && (int)$order['user_id'] === (int)$currentUser['id']) {
            $hasPermission = true;
        } elseif (!empty($_SESSION['admin_user'])) {
            $hasPermission = true;
        } elseif (empty($order['user_id']) && !empty($customerPhone) && $customerPhone === $order['customer_phone']) {
            // [SỬA LỖI] Xác minh bằng SĐT chỉ áp dụng cho đơn không gắn tài khoản. Đơn của thành viên phải đăng nhập đúng chủ tài khoản
            // (trước đây ai biết mã đơn + SĐT đều hủy/trả hàng thay được đơn của thành viên khác).
            $hasPermission = true;
        }

        if (!$hasPermission) {
            $_SESSION['flash_user_order_err'] = 'Bạn không có quyền thao tác trên đơn hàng này!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        // Kiểm tra điều kiện chính sách trả hàng
        $eligibility = $orderModel->checkReturnEligibility($order);
        if (!$eligibility['can_return']) {
            $_SESSION['flash_user_order_err'] = 'Không thể trả hàng: ' . ($eligibility['reason_blocked'] ?? $eligibility['message']);
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $reason = trim($_POST['return_reason'] ?? $_POST['reason'] ?? 'Khách yêu cầu trả hàng');
        $customReason = trim($_POST['custom_reason'] ?? '');
        if ($reason === 'other' || !empty($customReason)) {
            $reason = !empty($customReason) ? $customReason : 'Lý do khác';
        }

        // Luôn thu thập thông tin tài khoản ngân hàng để hoàn tiền trong mọi tình huống
        $bankName = trim($_POST['bank_name'] ?? '');
        $accountNumber = trim($_POST['account_number'] ?? '');
        $accountHolder = strtoupper(trim($_POST['account_holder'] ?? ''));
        $refundNote = trim($_POST['refund_note'] ?? '');

        if ($order['payment_status'] === 'paid' && (empty($bankName) || empty($accountNumber) || empty($accountHolder))) {
            $_SESSION['flash_user_order_err'] = 'Đơn hàng đã thanh toán. Vui lòng nhập đầy đủ Ngân hàng, Số tài khoản và Tên chủ tài khoản để nhận hoàn tiền!';
            $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
            return;
        }

        $evidenceUrl = null;
        if (!empty($_FILES['evidence_image']['name']) && $_FILES['evidence_image']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../public/uploads/returns/';
            if (!is_dir($uploadDir)) {
                @mkdir($uploadDir, 0777, true);
            }
            $ext = strtolower(pathinfo($_FILES['evidence_image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                $fileName = 'return_' . $order['order_code'] . '_' . time() . '.' . $ext;
                $dest = $uploadDir . $fileName;
                if (@move_uploaded_file($_FILES['evidence_image']['tmp_name'], $dest)) {
                    $evidenceUrl = BASE_URL . 'public/uploads/returns/' . $fileName;
                }
            }
        }

        $refundData = [
            'bank_name' => $bankName,
            'account_number' => $accountNumber,
            'account_holder' => $accountHolder,
            'note' => $refundNote,
            'evidence_image' => $evidenceUrl
        ];

        $success = $orderModel->processReturnOrder((int)$order['id'], $reason, $refundData);

        if ($success) {
            // [SỬA LỖI] Chỉ GỬI YÊU CẦU - cửa hàng nhận lại & kiểm tra hàng rồi mới duyệt hoàn tiền
            $amountFormatted = number_format($order['final_amount'], 0, ',', '.') . 'đ';
            if ($order['payment_status'] === 'paid') {
                $_SESSION['flash_user_order_msg'] = "Đã gửi yêu cầu trả hàng cho đơn #{$orderCode}! Sau khi cửa hàng nhận lại và kiểm tra sản phẩm, số tiền {$amountFormatted} sẽ được hoàn về tài khoản {$accountNumber} ({$bankName}).";
            } else {
                $_SESSION['flash_user_order_msg'] = "Đã gửi yêu cầu trả hàng cho đơn #{$orderCode}! Cửa hàng sẽ liên hệ và xử lý trong thời gian sớm nhất.";
            }
        } else {
            $_SESSION['flash_user_order_err'] = 'Có lỗi xảy ra khi xử lý yêu cầu trả hàng. Vui lòng thử lại sau!';
        }

        $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=auth/orders');
    }
}
