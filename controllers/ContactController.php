<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Contact.php';

class ContactController extends BaseController {
    public function index(): void {
        $user = $_SESSION['user'] ?? null;
        $isGuest = empty($user);
        $success = $_SESSION['contact_success'] ?? '';
        $error = $_SESSION['contact_error'] ?? '';
        unset($_SESSION['contact_success'], $_SESSION['contact_error']);

        $this->render('contact/index', [
            'pageTitle' => 'Liên hệ & Chăm sóc khách hàng',
            'user' => $user,
            'isGuest' => $isGuest,
            'success' => $success,
            'error' => $error
        ]);
    }

    public function send(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . 'index.php?url=contact');
        }

        $this->requireCsrf(BASE_URL . 'index.php?url=contact');

        // BẮT BUỘC: Chỉ User đã đăng nhập mới được gửi liên hệ (Khách chỉ được xem thông tin)
        if (empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=auth/login&msg=login_required_contact');
        }

        require_once __DIR__ . '/../services/RateLimiter.php';
        $ip = RateLimiter::getClientIp();
        if (RateLimiter::isLocked('contact_send', $ip) !== null) {
            $_SESSION['contact_error'] = 'Bạn đã gửi liên hệ quá nhanh. Vui lòng đợi 5 phút trước khi gửi tiếp!';
            $this->redirect(BASE_URL . 'index.php?url=contact');
            return;
        }
        RateLimiter::hit('contact_send', $ip, 3, 300);

        $userId = (int)$_SESSION['user']['id'];
        $fullname = trim($_POST['fullname'] ?? $_SESSION['user']['fullname']);
        $email = trim($_POST['email'] ?? $_SESSION['user']['email']);
        $phone = trim($_POST['phone'] ?? $_SESSION['user']['phone']);
        $subject = trim($_POST['subject'] ?? '');
        $message = trim($_POST['message'] ?? '');

        if (empty($subject) || empty($message)) {
            $_SESSION['contact_error'] = 'Vui lòng nhập đầy đủ tiêu đề và nội dung cần liên hệ!';
            $this->redirect(BASE_URL . 'index.php?url=contact');
            return;
        }

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['contact_error'] = 'Địa chỉ email không đúng định dạng hợp lệ!';
            $this->redirect(BASE_URL . 'index.php?url=contact');
            return;
        }

        if (empty($phone) || !preg_match('/^0[35789][0-9]{8}$/', $phone)) {
            $_SESSION['contact_error'] = 'Số điện thoại không hợp lệ (Yêu cầu 10 chữ số bắt đầu bằng 03, 05, 07, 08, 09)!';
            $this->redirect(BASE_URL . 'index.php?url=contact');
            return;
        }

        $contactModel = new Contact();
        $saved = $contactModel->create([
            'user_id' => $userId,
            'fullname' => $fullname,
            'email' => $email,
            'phone' => $phone,
            'subject' => $subject,
            'message' => $message
        ]);

        if ($saved) {
            $_SESSION['contact_success'] = 'Cảm ơn bạn! Thông tin liên hệ đã được gửi thành công tới ban quản trị NEXVORA. Đội ngũ CSKH sẽ phản hồi trong 24h.';
        } else {
            $_SESSION['contact_error'] = 'Không thể gửi liên hệ lúc này. Vui lòng thử lại!';
        }

        $this->redirect(BASE_URL . 'index.php?url=contact');
    }
}
