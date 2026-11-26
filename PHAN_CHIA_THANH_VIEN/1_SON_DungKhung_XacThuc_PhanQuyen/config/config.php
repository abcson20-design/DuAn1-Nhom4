<?php
// Thiết lập múi giờ Việt Nam
date_default_timezone_set('Asia/Ho_Chi_Minh');

// ==============================================================
// CẤU HÌNH HỆ THỐNG NEXVORA STORE
// ==============================================================

// 1. Tự động đọc tệp .env nếu tồn tại (Không cần composer/vlucas)
$envFile = __DIR__ . '/../.env';
if (file_exists($envFile)) {
    $lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#')) {
            continue;
        }
        if (str_contains($line, '=')) {
            [$key, $val] = explode('=', $line, 2);
            $key = trim($key);
            $val = trim($val);
            // Xóa dấu nháy bao quanh nếu có
            if ((str_starts_with($val, '"') && str_ends_with($val, '"')) ||
                (str_starts_with($val, "'") && str_ends_with($val, "'"))) {
                $val = substr($val, 1, -1);
            }
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $val;
                putenv("{$key}={$val}");
            }
        }
    }
}

// Helper lấy biến môi trường
if (!function_exists('env')) {
    function env(string $key, $default = null) {
        $val = $_ENV[$key] ?? getenv($key);
        if ($val === false || $val === null) {
            return $default;
        }
        if (strtolower((string)$val) === 'true') return true;
        if (strtolower((string)$val) === 'false') return false;
        return $val;
    }
}

// Cấu hình chung của Website NEXVORA Store
define('APP_NAME', 'NEXVORA - Cửa Hàng Điện Thoại & Phụ Kiện Chính Hãng');
define('APP_VERSION', '2.0.0');

// ==============================================================
// CẤU HÌNH ĐƯỜNG DẪN GỐC (BASE_URL) & HỖ TRỢ CLOUDFLARE TUNNEL
// ==============================================================
// Ghi chú: Hệ thống tự động nhận diện cả Localhost lẫn Cloudflare Tunnel (*.trycloudflare.com)
$appUrl = env('APP_URL');
if (!empty($appUrl)) {
    define('BASE_URL', rtrim($appUrl, '/') . '/');
} else {
    // 1. Nhận diện giao thức HTTPS: Hỗ trợ kết nối trực tiếp hoặc qua Cloudflare / Reverse Proxy
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? 80) == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https')
        || (isset($_SERVER['HTTP_CF_VISITOR']) && str_contains($_SERVER['HTTP_CF_VISITOR'], 'https'));

    $protocol = $isHttps ? "https://" : "http://";
    $rawHost = $_SERVER['HTTP_HOST'] ?? 'localhost';
    $hostOnly = strtolower(explode(':', $rawHost)[0]);

    // 2. Danh sách tên miền hợp lệ (Chống tấn công Host Header Injection)
    $allowedHosts = array_map('trim', explode(',', strtolower((string)env('ALLOWED_HOSTS', 'localhost,127.0.0.1,::1'))));

    // 3. Tự động hỗ trợ các tên miền Cloudflare Tunnel (*.trycloudflare.com) và tunnel thông dụng
    $isTunnelHost = str_ends_with($hostOnly, '.trycloudflare.com')
        || str_ends_with($hostOnly, '.ngrok-free.app')
        || str_ends_with($hostOnly, '.loca.lt');

    if (!$isTunnelHost && !in_array($hostOnly, $allowedHosts, true) && !in_array(strtolower($rawHost), $allowedHosts, true)) {
        // Host lạ không nằm trong danh sách an toàn -> Fallback về localhost
        $rawHost = 'localhost';
    }

    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    $baseUrl = rtrim($protocol . $rawHost . $scriptDir, '/') . '/';
    define('BASE_URL', $baseUrl);
}

// Cấu hình Database MySQL
define('DB_HOST', env('DB_HOST', 'localhost'));
define('DB_PORT', env('DB_PORT', '3306'));
define('DB_NAME', env('DB_NAME', 'DuAn1_db'));
define('DB_USER', env('DB_USER', 'root'));
define('DB_PASS', env('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

// Cấu hình Tài khoản Ngân Hàng & VietQR Online API (NEXVORA)
define('BANK_ID', env('BANK_ID', 'ICB'));
define('BANK_NAME', env('BANK_NAME', 'VietinBank - Ngân hàng TMCP Công Thương Việt Nam'));
define('BANK_ACCOUNT_NO', env('BANK_ACCOUNT_NO', '101883603231'));
define('BANK_ACCOUNT_NAME', env('BANK_ACCOUNT_NAME', 'LE VAN SON'));
define('VIETQR_TEMPLATE', env('VIETQR_TEMPLATE', 'compact'));

// Thông tin liên hệ cửa hàng
define('SHOP_HOTLINE', '036 222 5438');
define('SHOP_EMAIL', 'abcson20@gmail.com');
define('SHOP_ADDRESS', 'Số 01 - Ngõ Cụt - Đường Sơn Tinh - Xã Cẩm Vân - Tỉnh Thanh Hóa');
define('SHOP_ZALO', 'https://zalo.me/0362225438');
define('SHOP_FACEBOOK', 'https://facebook.com');

// Cấu hình môi trường hiển thị lỗi và gắn Security Headers
define('APP_ENV', env('APP_ENV', 'production'));
if (APP_ENV === 'development') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    ini_set('display_startup_errors', '0');
    error_reporting(0);
}

if (!headers_sent()) {
    header_remove('X-Powered-By');
    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: SAMEORIGIN');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; img-src 'self' data: https: blob:; font-src 'self' https: data:;");
}

// Khởi động session với thuộc tính cookie an toàn (HttpOnly, SameSite=Lax, Secure khi chạy HTTPS/Cloudflare)
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https');
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}

// Cấu hình Cổng thanh toán tự động SePay (Checkout V1 & IPN)
define('SEPAY_ENABLED', env('SEPAY_ENABLED', true));
define('SEPAY_ENV', env('SEPAY_ENV', 'sandbox'));
define('SEPAY_MERCHANT_ID', env('SEPAY_MERCHANT_ID', ''));
define('SEPAY_SECRET_KEY', env('SEPAY_SECRET_KEY', ''));
define('SEPAY_IPN_SECRET', env('SEPAY_IPN_SECRET', ''));
define('SEPAY_RECONCILE_ON_RETURN', env('SEPAY_RECONCILE_ON_RETURN', true));

// Nạp các dịch vụ hỗ trợ (Services)
require_once __DIR__ . '/../services/Csrf.php';
require_once __DIR__ . '/../services/RateLimiter.php';
require_once __DIR__ . '/../services/SepayGateway.php';

// Các hàm tiện ích hỗ trợ View & Bảo mật
if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return Csrf::token();
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field(): string {
        return Csrf::field();
    }
}
