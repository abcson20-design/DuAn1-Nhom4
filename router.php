<?php
/**
 * Router Script an toàn cho máy chủ PHP Built-in Server (php -S).
 * Bảo vệ các tệp nhạy cảm (.sql, .md, .env, cấu hình, mã nguồn) tương đương .htaccess trên Apache.
 * Cách chạy: C:\xampp\php\php.exe -S localhost:8000 router.php
 */

declare(strict_types=1);

// Gỡ bỏ header lộ phiên bản PHP và gắn các Security Headers chuẩn
header_remove('X-Powered-By');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

$uri = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH);
$uri = rawurldecode($uri);

// 1. CHẶN CÁC TỆP TIN NHẠY CẢM
$blockedExtensions = ['sql', 'md', 'env', 'json', 'log', 'lock', 'git', 'yml', 'yaml', 'ini', 'sh', 'bat'];
$ext = strtolower(pathinfo($uri, PATHINFO_EXTENSION));

if (in_array($ext, $blockedExtensions, true)) {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "403 Forbidden: Truy cập tệp tin định dạng .{$ext} bị nghiêm cấm.";
    exit;
}

// 2. CHẶN TRUY CẬP TRỰC TIẾP CÁC THƯ MỤC MÃ NGUỒN VÀ DỮ LIỆU
$normalizedUri = '/' . ltrim(str_replace('\\', '/', $uri), '/');
$blockedDirs = [
    '/config/',
    '/models/',
    '/views/',
    '/controllers/',
    '/services/',
    '/sql/',
    '/scratch/',
    '/tests/',
    '/.git/'
];

foreach ($blockedDirs as $dir) {
    if (str_starts_with($normalizedUri, $dir) || str_contains($normalizedUri, $dir)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo "403 Forbidden: Truy cập trực tiếp thư mục hệ thống bị từ chối.";
        exit;
    }
}

// Chặn các file ẩn bắt đầu bằng dấu chấm (VD: .env, .htaccess)
$filename = basename($normalizedUri);
if (str_starts_with($filename, '.') && $filename !== '.') {
    http_response_code(403);
    header('Content-Type: text/plain; charset=utf-8');
    echo "403 Forbidden: Không thể truy cập tệp ẩn.";
    exit;
}

// 3. NẾU LÀ TỆP TĨNH HỢP LỆ TRONG THƯ MỤC PUBLIC (ẢNH, CSS, JS, VIDEO, FONT)
$filePath = __DIR__ . $normalizedUri;
if ($normalizedUri !== '/' && is_file($filePath)) {
    // Chỉ cho phép phục vụ tệp tĩnh nếu nằm trong thư mục public/
    $realPath = realpath($filePath);
    $publicDir = realpath(__DIR__ . '/public');
    
    // [SỬA LỖI] Không cho php -S thực thi tệp .php nằm trong public/ (VD: public/uploads) – tương đương public/uploads/.htaccess
    if (preg_match('/\.(php\d?|phtml|phar|pht)$/i', $normalizedUri)) {
        http_response_code(403);
        header('Content-Type: text/plain; charset=utf-8');
        echo "403 Forbidden";
        exit;
    }
    if ($realPath && $publicDir && str_starts_with($realPath, $publicDir)) {
        return false; // Trả về false để PHP built-in server tự phục vụ file tĩnh
    }
}

// 4. MỌI YÊU CẦU KHÁC ĐỀU ĐIỀU HƯỚNG VÀO index.php (MVC Router)
require_once __DIR__ . '/index.php';
