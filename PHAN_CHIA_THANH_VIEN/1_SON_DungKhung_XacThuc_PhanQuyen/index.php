<?php
/**
 * NEXVORA Store - Hệ thống Website Bán Điện Thoại & Phụ Kiện Chính Hãng
 * Mô hình: Pure PHP MVC
 */

require_once __DIR__ . '/config/config.php';

// Xử lý Route qua tham số GET 'url'
$url = $_GET['url'] ?? 'home';
$url = trim($url, '/');
$parts = explode('/', $url);

$route = $parts[0] ?: 'home';
$action = $parts[1] ?? 'index';

switch ($route) {
    case 'home':
        require_once __DIR__ . '/controllers/HomeController.php';
        $controller = new HomeController();
        $controller->index();
        break;

    // API Endpoints chuẩn Shopee (Cart, Orders, Checkout, Admin)
    case 'api':
        require_once __DIR__ . '/controllers/ApiController.php';
        $controller = new ApiController();
        $resource = $parts[1] ?? 'cart';
        $subAction = $parts[2] ?? 'list';
        $controller->handle($resource, $subAction);
        break;

    // Trang Điện thoại chuyên biệt (Nhiều hãng: Apple, Samsung, Xiaomi, OPPO...)
    case 'phones':
    case 'dien-thoai':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController();
        $controller->phones();
        break;

    // Trang Phụ kiện chuyên biệt (Nhiều loại: Tai nghe Bluetooth, có dây, củ sạc, cáp, pin, ốp lưng...)
    case 'accessories':
    case 'phu-kien':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController();
        $controller->accessories();
        break;

    // Trang Liên hệ & CSKH
    case 'contact':
    case 'lien-he':
        require_once __DIR__ . '/controllers/ContactController.php';
        $controller = new ContactController();
        if ($action === 'send') {
            $controller->send();
        } else {
            $controller->index();
        }
        break;

    // Xác thực tài khoản Khách hàng (Login / Register / Logout / Profile / Orders)
    case 'auth':
    case 'user':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'register') {
            $controller->register();
        } elseif ($action === 'logout') {
            $controller->logout();
        } elseif ($action === 'orders') {
            $controller->orders();
        } elseif ($action === 'profile') {
            $controller->profile();
        } elseif ($action === 'forgot-password' || $action === 'forgot_password' || $action === 'forgot') {
            $controller->forgotPassword();
        } elseif ($action === 'reset-password' || $action === 'reset_password' || $action === 'reset') {
            $controller->resetPassword();
        } else {
            $controller->login();
        }
        break;

    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->login();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->register();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        $controller = new AuthController();
        $controller->logout();
        break;

    case 'products':
    case 'san-pham':
    case 'search':
    case 'tim-kiem':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController();
        if ($action === 'detail') {
            $controller->detail();
        } elseif ($action === 'suggest' || $action === 'ajaxSearch') {
            $controller->suggest();
        } elseif ($action === 'comment' || $action === 'add-review' || $action === 'review' || $action === 'add_comment') {
            $controller->addComment();
        } elseif ($action === 'delete-comment' || $action === 'delete_comment' || $action === 'delete-review') {
            $controller->deleteComment();
        } elseif ($action === 'like-comment' || $action === 'like_comment') {
            $controller->likeComment();
        } else {
            $controller->index();
        }
        break;

    case 'comment':
    case 'binh-luan':
        require_once __DIR__ . '/controllers/ProductController.php';
        $controller = new ProductController();
        $controller->addComment();
        break;

    case 'cart':
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        if ($action === 'add') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                die('405 Method Not Allowed: Thao tác thêm vào giỏ hàng yêu cầu phương thức POST và mã xác thực CSRF.');
            }
            $controller->add();
        } elseif ($action === 'buy-now' || $action === 'buy_now' || $action === 'buyNow') {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                http_response_code(405);
                die('405 Method Not Allowed: Thao tác Mua ngay yêu cầu phương thức POST và mã xác thực CSRF.');
            }
            $controller->buyNow();
        } elseif ($action === 'update') {
            $controller->update();
        } elseif ($action === 'remove') {
            $controller->remove();
        } elseif ($action === 'coupon') {
            $controller->applyCoupon();
        } else {
            $controller->index();
        }
        break;

    case 'buy-now':
    case 'buy_now':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            die('405 Method Not Allowed: Thao tác Mua ngay yêu cầu phương thức POST và mã xác thực CSRF.');
        }
        require_once __DIR__ . '/controllers/CartController.php';
        $controller = new CartController();
        $controller->buyNow();
        break;

    case 'checkout':
        require_once __DIR__ . '/controllers/CheckoutController.php';
        $controller = new CheckoutController();
        if ($action === 'process') {
            $controller->process();
        } elseif ($action === 'apply-coupon') {
            $controller->applyCoupon();
        } elseif ($action === 'remove-coupon') {
            $controller->removeCoupon();
        } elseif ($action === 'payment-qr') {
            $controller->paymentQr();
        } elseif ($action === 'payment-bank') {
            $controller->paymentBank();
        } elseif ($action === 'confirm-payment') {
            $controller->confirmPayment();
        } elseif ($action === 'check-status') {
            $controller->checkStatus();
        } elseif ($action === 'simulate-paid' || $action === 'simulatePaid') {
            $controller->simulatePaid();
        } elseif ($action === 'change-payment-method') {
            $controller->changePaymentMethod();
        } elseif ($action === 'rollback-to-cart') {
            $controller->rollbackToCart();
        } elseif ($action === 'rollback-to-checkout') {
            $controller->rollbackToCheckout();
        } else {
            $controller->index();
        }
        break;

    // Cổng thanh toán trực tuyến SePay (Đồng bộ 100% Base Shop: IPN, Success, Cancel, Error, Simulate)
    case 'payment':
        require_once __DIR__ . '/controllers/PaymentController.php';
        $controller = new PaymentController();
        $subAction = $parts[2] ?? '';
        if ($action === 'sepay') {
            if ($subAction === 'ipn') {
                $controller->sepayIpn();
            } elseif ($subAction === 'success') {
                $controller->sepaySuccess();
            } elseif ($subAction === 'simulate') {
                $controller->sepaySimulate();
            } elseif ($subAction === 'cancel') {
                $controller->sepayCancel();
            } elseif ($subAction === 'error') {
                $controller->sepayError();
            } else {
                $controller->sepaySuccess();
            }
        } else {
            header('Location: ' . BASE_URL);
            exit;
        }
        break;

    case 'webhook':
    case 'api-webhook':
        // Hỗ trợ cả 2 chuẩn IPN: SePay Payment Gateway & Biến động số dư
        require_once __DIR__ . '/controllers/PaymentController.php';
        $controller = new PaymentController();
        $controller->sepayIpn();
        break;

    // Danh sách yêu thích Wishlist (Chuẩn Bài Lab Buổi 2)
    case 'wishlist':
    case 'yeu-thich':
        require_once __DIR__ . '/controllers/WishlistController.php';
        $controller = new WishlistController();
        if ($action === 'toggle') {
            $controller->toggle();
        } else {
            $controller->index();
        }
        break;

    case 'order':
        require_once __DIR__ . '/controllers/OrderController.php';
        $controller = new OrderController();
        if ($action === 'success') {
            $controller->success();
        } elseif ($action === 'track') {
            $controller->track();
        } elseif ($action === 'received') {
            $controller->confirmReceived();
        } elseif ($action === 'cancel') {
            $controller->cancel();
        } elseif ($action === 'return' || $action === 'request-return') {
            $controller->returnOrder();
        } elseif ($action === 'delete-user-order' || $action === 'delete') {
            $controller->deleteUserOrder();
        } else {
            $controller->track();
        }
        break;

    case 'admin':
        require_once __DIR__ . '/controllers/AdminController.php';
        $controller = new AdminController();
        if ($action === 'login') {
            $controller->login();
        } elseif ($action === 'logout') {
            $controller->logout();
        } elseif ($action === 'statistics' || $action === 'stats') {
            $controller->statistics();
        } elseif ($action === 'products') {
            $controller->products();
        } elseif ($action === 'product-create') {
            $controller->productCreate();
        } elseif ($action === 'product-edit') {
            $controller->productEdit();
        } elseif ($action === 'product-delete') {
            $controller->productDelete();
        } elseif ($action === 'product-trash' || $action === 'products-trash') {
            $controller->productTrash();
        } elseif ($action === 'product-restore') {
            $controller->productRestore();
        } elseif ($action === 'product-force-delete') {
            $controller->productForceDelete();
        } elseif ($action === 'categories') {
            $controller->categories();
        } elseif ($action === 'category-create') {
            $controller->categoryCreate();
        } elseif ($action === 'category-edit') {
            $controller->categoryEdit();
        } elseif ($action === 'category-delete') {
            $controller->categoryDelete();
        } elseif ($action === 'category-trash') {
            $controller->categoryTrash();
        } elseif ($action === 'category-restore') {
            $controller->categoryRestore();
        } elseif ($action === 'category-force-delete') {
            $controller->categoryForceDelete();
        } elseif ($action === 'brands') {
            $controller->brands();
        } elseif ($action === 'brand-create') {
            $controller->brandCreate();
        } elseif ($action === 'brand-edit') {
            $controller->brandEdit();
        } elseif ($action === 'brand-delete') {
            $controller->brandDelete();
        } elseif ($action === 'brand-trash') {
            $controller->brandTrash();
        } elseif ($action === 'brand-restore') {
            $controller->brandRestore();
        } elseif ($action === 'brand-force-delete') {
            $controller->brandForceDelete();
        } elseif ($action === 'coupons') {
            $controller->coupons();
        } elseif ($action === 'coupon-create') {
            $controller->couponCreate();
        } elseif ($action === 'coupon-edit') {
            $controller->couponEdit();
        } elseif ($action === 'coupon-delete') {
            $controller->couponDelete();
        } elseif ($action === 'coupon-trash') {
            $controller->couponTrash();
        } elseif ($action === 'coupon-restore') {
            $controller->couponRestore();
        } elseif ($action === 'coupon-force-delete') {
            $controller->couponForceDelete();
        } elseif ($action === 'orders') {
            $controller->orders();
        } elseif ($action === 'order-status') {
            $controller->updateOrderStatus();
        } elseif ($action === 'order-delete') {
            $controller->orderDelete();
        } elseif ($action === 'orders-trash' || $action === 'order-trash') {
            $controller->orderTrash();
        } elseif ($action === 'order-restore') {
            $controller->orderRestore();
        } elseif ($action === 'order-force-delete') {
            $controller->orderForceDelete();
        } elseif ($action === 'reconcile-sepay') {
            $controller->reconcileSepay();
        } elseif ($action === 'contacts') {
            $controller->contacts();
        } elseif ($action === 'contact-status') {
            $controller->updateContactStatus();
        } elseif ($action === 'reviews' || $action === 'danh-gia') {
            $controller->reviews();
        } elseif ($action === 'review-delete' || $action === 'review_delete') {
            $controller->reviewDelete();
        } elseif ($action === 'users' || $action === 'user-status') {
            $controller->users();
        } else {
            $controller->index();
        }
        break;

    default:
        // 404 Not Found
        header("HTTP/1.0 404 Not Found");
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>404 - Trang không tồn tại - NEXVORA</title><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-[#090d16] text-white text-center py-24"><div class="max-w-md mx-auto p-8 rounded-3xl border border-slate-800 bg-slate-900/80 backdrop-blur"><h1 class="text-6xl font-black text-cyan-400 mb-4">404</h1><p class="text-slate-400 mb-6">Trang bạn tìm kiếm không tồn tại.</p><a href="' . BASE_URL . '" class="bg-indigo-600 hover:bg-indigo-500 text-white font-bold py-3 px-6 rounded-xl transition">Về Trang Chủ</a></div></body></html>';
        break;
}
