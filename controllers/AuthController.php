<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Order.php';

class AuthController extends BaseController {
    public function login(): void {
        // Nếu đã đăng nhập, chuyển hướng về trang phù hợp
        if (!empty($_SESSION['user'])) {
            if ($_SESSION['user']['role'] === 'admin') {
                $this->redirect(BASE_URL . 'index.php?url=admin');
            } else {
                $this->redirect(BASE_URL . 'index.php?url=auth/orders');
            }
        }

        $error = '';
        $redirectUrl = $_GET['redirect'] ?? '';
        $notice = '';

        // Thông báo khi phiên bị hủy do tài khoản bị khóa
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !empty($_SESSION['login_error'])) {
            $error = $_SESSION['login_error'];
            unset($_SESSION['login_error']);
        }
        if (!empty($_SESSION['login_success'])) {
            $notice = $_SESSION['login_success'];
            unset($_SESSION['login_success']);
        }

        $clientIp = RateLimiter::getClientIp();
        $ipLockTime = RateLimiter::isLocked('login_user_ip', $clientIp);
        if ($ipLockTime !== null) {
            http_response_code(429);
            $wait = ceil($ipLockTime / 60);
            $error = "IP của bạn đã bị tạm khóa do nhập sai mật khẩu quá 5 lần. Vui lòng quay lại sau {$wait} phút!";
        }

        if (!empty($_GET['msg'])) {
            if ($_GET['msg'] === 'login_required_cart') {
                $notice = 'Quý khách đang duyệt với tư cách Khách. Vui lòng đăng nhập tài khoản để xem giỏ hàng và mua sắm!';
            } elseif ($_GET['msg'] === 'login_required_checkout') {
                $notice = 'Quý khách vui lòng đăng nhập tài khoản để hoàn tất đơn hàng và thanh toán trực tuyến!';
            } elseif ($_GET['msg'] === 'login_required_contact') {
                $notice = 'Quý khách vui lòng đăng nhập tài khoản để gửi thư liên hệ / yêu cầu hỗ trợ tới NEXVORA!';
            } elseif ($_GET['msg'] === 'login_required_review') {
                $notice = 'Quý khách vui lòng đăng nhập tài khoản Khách hàng để viết đánh giá và bình luận sản phẩm!';
            } elseif ($_GET['msg'] === 'login_required_wishlist') {
                $notice = 'Quý khách vui lòng đăng nhập tài khoản để lưu và quản lý danh sách sản phẩm yêu thích!';
            } elseif ($_GET['msg'] === 'registered_success') {
                $notice = 'Đăng ký tài khoản thành công! Vui lòng đăng nhập để tiếp tục.';
            }
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            // Kiểm tra RateLimit độc lập: 1) Theo Tài khoản Email, 2) Theo IP
            $accountKey = strtolower($email);
            $accountLockTime = RateLimiter::isLocked('login_user_account', $accountKey);

            if ($accountLockTime !== null) {
                http_response_code(429);
                $wait = ceil($accountLockTime / 60);
                $error = "Tài khoản \"{$email}\" đã bị tạm khóa 15 phút do nhập sai mật khẩu quá 5 lần. Vui lòng quay lại sau {$wait} phút!";
            } elseif ($ipLockTime !== null) {
                http_response_code(429);
                $wait = ceil($ipLockTime / 60);
                $error = "Địa chỉ IP của bạn đã bị tạm khóa 15 phút do nhập sai mật khẩu quá nhiều lần. Vui lòng quay lại sau {$wait} phút!";
            } elseif (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu!';
            } else {
                $userModel = new User();
                $user = $userModel->login($email, $password);

                if ($user) {
                    // Đăng nhập thành công: Xóa bộ đếm vi phạm cho cả tài khoản và IP
                    RateLimiter::clear('login_user_account', $accountKey);
                    RateLimiter::clear('login_user_ip', $clientIp);

                    // Chống tấn công Session Fixation
                    session_regenerate_id(true);

                    $_SESSION['user'] = $user;

                    // Khôi phục giỏ hàng bền vững từ CSDL cho tài khoản (Persistent Cart)
                    require_once __DIR__ . '/../models/Cart.php';
                    $cartModel = new Cart();
                    $cartModel->mergeOnLogin((int)$user['id']);

                    // Nếu là Admin, thiết lập thêm session admin_user
                    if ($user['role'] === 'admin') {
                        $_SESSION['admin_user'] = $user;
                        $this->redirect(BASE_URL . 'index.php?url=admin');
                        return;
                    }

                    // Điều hướng an toàn chống Open Redirect
                    if (!empty($redirectUrl)) {
                        // [SỬA LỖI] Đưa nguyên giá trị vào safeRedirect: URL ngoài domain -> quay về trang chủ
                        $this->safeRedirect($redirectUrl, BASE_URL . 'index.php?url=home');
                    } else {
                        $this->redirect(BASE_URL . 'index.php?url=home');
                    }
                    return;
                } else {
                    // Ghi nhận hit thất bại độc lập: theo Tài khoản VÀ theo IP
                    $ipRes = RateLimiter::hit('login_user_ip', $clientIp, 5, 900);
                    $accRes = RateLimiter::hit('login_user_account', $accountKey, 5, 900);

                    if (!$accRes['allowed']) {
                        http_response_code(429);
                        $error = 'Tài khoản này đã bị khóa 15 phút do nhập sai mật khẩu 5 lần liên tiếp!';
                    } elseif (!$ipRes['allowed']) {
                        http_response_code(429);
                        $error = 'Địa chỉ IP của bạn đã bị khóa 15 phút do thử sai quá 5 lần!';
                    } else {
                        $remaining = min($accRes['remaining'], $ipRes['remaining']);
                        $customErr = $_SESSION['login_error'] ?? null;
                        unset($_SESSION['login_error']);
                        $error = $customErr ?: "Email hoặc mật khẩu không chính xác! (Còn {$remaining} lần thử)";
                    }
                }
            }
        }

        $this->render('auth/login', [
            'pageTitle' => 'Đăng nhập tài khoản',
            'error' => $error,
            'notice' => $notice,
            'redirectUrl' => $redirectUrl
        ]);
    }

    public function register(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=home');
        }

        $error = '';
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();

            $fullname = trim($_POST['fullname'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $passwordConfirm = trim($_POST['password_confirm'] ?? '');
            $address = trim($_POST['address'] ?? '');

            if (empty($fullname) || empty($email) || empty($phone) || empty($password)) {
                $error = 'Vui lòng điền đầy đủ các thông tin bắt buộc!';
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = 'Địa chỉ email không đúng định dạng!';
            } elseif (!preg_match('/^0[35789][0-9]{8}$/', $phone)) {
                $error = 'Số điện thoại không hợp lệ! Vui lòng nhập số điện thoại Việt Nam 10 chữ số (bắt đầu bằng 03, 05, 07, 08, 09).';
            } elseif ($password !== $passwordConfirm) {
                $error = 'Mật khẩu xác nhận không khớp!';
            } elseif (strlen($password) < 6) {
                $error = 'Mật khẩu phải từ 6 ký tự trở lên!';
            } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $password)) {
                $error = 'Mật khẩu bắt buộc phải chứa ít nhất 1 ký tự đặc biệt (ví dụ: @, #, $, %, !...)!';
            } else {
                $userModel = new User();
                if ($userModel->emailExists($email)) {
                    $error = 'Email này đã được sử dụng. Vui lòng chọn Email khác hoặc Đăng nhập!';
                } elseif ($userModel->phoneExists($phone)) {
                    // [SỬA LỖI] Trước đây chỉ kiểm tra trùng SĐT bằng JavaScript -> gửi thẳng form vẫn tạo được tài khoản trùng SĐT
                    $error = 'Số điện thoại này đã được đăng ký tài khoản. Vui lòng dùng số khác hoặc Đăng nhập!';
                } else {
                    $newUserId = $userModel->register($fullname, $email, $phone, $password, $address);
                    if ($newUserId) {
                        // Tự động đăng nhập luôn sau khi đăng ký thành công
                        $user = $userModel->getById($newUserId);
                        session_regenerate_id(true); // Chống Session Fixation
                        $_SESSION['user'] = $user;

                    // Khôi phục giỏ hàng bền vững từ CSDL cho tài khoản (Persistent Cart)
                    require_once __DIR__ . '/../models/Cart.php';
                    $cartModel = new Cart();
                    $cartModel->mergeOnLogin((int)$user['id']);
                        $this->redirect(BASE_URL . 'index.php?url=home');
                    } else {
                        $error = 'Đã có lỗi xảy ra trong quá trình đăng ký. Vui lòng thử lại!';
                    }
                }
            }
        }

        $this->render('auth/register', [
            'pageTitle' => 'Đăng ký tài khoản User mới',
            'error' => $error
        ]);
    }

    public function logout(): void {
        if (!empty($_SESSION['user']['id']) && isset($_SESSION['cart'])) {
            require_once __DIR__ . '/../models/Cart.php';
            $cartModel = new Cart();
            $cartModel->saveCart((int)$_SESSION['user']['id'], $_SESSION['cart']);
        }
        unset($_SESSION['cart']);
        unset($_SESSION['applied_coupon']);
        unset($_SESSION['discount_amount']);
        unset($_SESSION['coupon_code']);
        unset($_SESSION['user']);
        unset($_SESSION['admin_user']);
        $this->redirect(BASE_URL . 'index.php?url=home');
    }

    public function orders(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=auth/login&msg=login_required_cart');
        }

        $userId = (int)$_SESSION['user']['id'];
        $orderModel = new Order();
        $myOrders = $orderModel->getByUserId($userId);
        foreach ($myOrders as &$ord) {
            $ord['return_eligibility'] = $orderModel->checkReturnEligibility($ord);
        }
        unset($ord);

        $this->render('auth/orders', [
            'pageTitle' => 'Đơn hàng của tôi',
            'orders' => $myOrders,
            'user' => $_SESSION['user']
        ]);
    }

    public function profile(): void {
        if (empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=auth/login');
        }

        $userId = (int)$_SESSION['user']['id'];
        $userModel = new User();
        $msg = '';
        $errMsg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $action = $_POST['action_type'] ?? 'update_profile';

            if ($action === 'change_password') {
                $oldPassword = (string)($_POST['old_password'] ?? '');
                $newPassword = (string)($_POST['new_password'] ?? '');
                $confirmPassword = (string)($_POST['confirm_password'] ?? '');

                if (empty($oldPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $errMsg = 'Vui lòng nhập đầy đủ mật khẩu hiện tại và mật khẩu mới!';
                } elseif (!$userModel->verifyUserPassword($userId, $oldPassword)) {
                    $errMsg = 'Mật khẩu hiện tại không chính xác!';
                } elseif ($newPassword !== $confirmPassword) {
                    $errMsg = 'Mật khẩu mới và xác nhận mật khẩu không khớp!';
                } elseif (strlen($newPassword) < 6) {
                    $errMsg = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
                } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $newPassword)) {
                    $errMsg = 'Mật khẩu mới phải chứa ít nhất 1 ký tự đặc biệt (!, @, #, $, %...)!';
                } else {
                    $userModel->updatePassword($userId, $newPassword);
                    $msg = 'Đổi mật khẩu thành công! Hãy ghi nhớ mật khẩu mới của bạn.';
                }
            } else {
                $fullname = trim($_POST['fullname'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $address = trim($_POST['address'] ?? '');

                if (empty($fullname)) {
                    $errMsg = 'Họ và tên không được để trống!';
                } elseif (!empty($phone) && !preg_match('/^0[35789][0-9]{8}$/', $phone)) {
                    $errMsg = 'Số điện thoại không hợp lệ (10 chữ số, bắt đầu bằng 03, 05, 07, 08, 09)!';
                } elseif (!empty($phone) && $userModel->phoneExists($phone, $userId)) {
                    $errMsg = 'Số điện thoại này đã được dùng cho một tài khoản khác!';
                } else {
                    $userModel->updateProfile($userId, [
                        'fullname' => $fullname,
                        'phone' => $phone,
                        'address' => $address
                    ]);
                    $_SESSION['user']['fullname'] = $fullname;
                    $_SESSION['user']['phone'] = $phone;
                    $_SESSION['user']['address'] = $address;
                    $msg = 'Cập nhật thông tin hồ sơ thành công!';
                }
            }
        }

        $currentUser = $userModel->getById($userId);

        $this->render('auth/profile', [
            'pageTitle' => 'Hồ sơ người dùng - NEXVORA',
            'user' => $currentUser,
            'msg' => $msg,
            'errMsg' => $errMsg
        ]);
    }

    public function forgotPassword(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=home');
        }

        $msg = '';
        $errMsg = '';
        $demoResetLink = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $email = trim($_POST['email'] ?? '');

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errMsg = 'Vui lòng nhập địa chỉ email hợp lệ!';
            } else {
                $userModel = new User();
                $stmt = $userModel->getDb()->prepare("SELECT id, fullname FROM users WHERE email = ? AND role = 'customer' LIMIT 1");
                $stmt->execute([$email]);
                $foundUser = $stmt->fetch();

                // Tạo token an toàn bảo mật có thời hạn 30 phút
                if ($foundUser) {
                    $token = bin2hex(random_bytes(32));
                    $expiresAt = date('Y-m-d H:i:s', time() + 1800);
                    $userModel->setResetToken($email, $token, $expiresAt);
                    $resetLink = BASE_URL . 'index.php?url=auth/reset-password&token=' . urlencode($token);
                    
                    // [SỬA LỖI BẢO MẬT] Trước đây link đặt lại mật khẩu luôn hiện trên màn hình -> bất kỳ ai nhập email
                    // của nạn nhân đều đổi được mật khẩu (chiếm tài khoản). Giờ chỉ hiện khi bật APP_DEBUG=true
                    // trong .env (chạy demo trên máy cá nhân, không có mail server). Link luôn được ghi vào error log.
                    if (env('APP_DEBUG', false) === true) {
                        $demoResetLink = $resetLink;
                    }
                    error_log("[Password Reset] Link reset cho email {$email}: {$resetLink}");
                }

                // Thông báo chung an toàn: KHÔNG để lộ email có tồn tại trong hệ thống hay không (Chống User Enumeration)
                $msg = 'Nếu địa chỉ email này tồn tại trong hệ thống, hướng dẫn đặt lại mật khẩu đã được gửi đến hộp thư của bạn. Vui lòng kiểm tra email (bao gồm cả thư mục Spam) trong vòng 30 phút!';
            }
        }

        $this->render('auth/forgot_password', [
            'pageTitle' => 'Khôi phục mật khẩu - NEXVORA',
            'msg' => $msg,
            'errMsg' => $errMsg,
            'demoResetLink' => $demoResetLink
        ]);
    }

    public function resetPassword(): void {
        if (!empty($_SESSION['user'])) {
            $this->redirect(BASE_URL . 'index.php?url=home');
        }

        $token = trim((string)($_GET['token'] ?? $_POST['token'] ?? ''));
        $userModel = new User();
        $user = $userModel->getUserByResetToken($token);

        if (!$user) {
            $this->render('auth/reset_password', [
                'pageTitle' => 'Đặt lại mật khẩu - NEXVORA',
                'valid' => false,
                'errMsg' => 'Liên kết đặt lại mật khẩu không hợp lệ hoặc đã hết hạn (quá 30 phút). Vui lòng gửi lại yêu cầu mới!',
                'token' => $token
            ]);
            return;
        }

        $msg = '';
        $errMsg = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrf();
            $newPassword = $_POST['password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (strlen($newPassword) < 6) {
                $errMsg = 'Mật khẩu mới phải có ít nhất 6 ký tự!';
            } elseif (!preg_match('/[!@#$%^&*()_+\-=\[\]{};\':"\\|,.<>\/?]/', $newPassword)) {
                // Đồng bộ chính sách mật khẩu với trang Đăng ký / Đổi mật khẩu
                $errMsg = 'Mật khẩu mới phải chứa ít nhất 1 ký tự đặc biệt (!, @, #, $, %...)!';
            } elseif ($newPassword !== $confirmPassword) {
                $errMsg = 'Xác nhận mật khẩu mới không trùng khớp!';
            } else {
                $userModel->updatePassword((int)$user['id'], $newPassword);
                $userModel->clearResetToken((int)$user['id']);
                $_SESSION['login_success'] = 'Đặt lại mật khẩu thành công! Vui lòng đăng nhập bằng mật khẩu mới.';
                $this->redirect(BASE_URL . 'index.php?url=auth/login');
                return;
            }
        }

        $this->render('auth/reset_password', [
            'pageTitle' => 'Đặt lại mật khẩu - NEXVORA',
            'valid' => true,
            'user' => $user,
            'token' => $token,
            'msg' => $msg,
            'errMsg' => $errMsg
        ]);
    }
}
