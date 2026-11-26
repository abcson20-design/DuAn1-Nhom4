<?php
require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    public static function getConnection(): ?PDO {
        if (self::$instance === null) {
            try {
                $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$instance = new PDO($dsn, DB_USER, DB_PASS, $options);
            } catch (PDOException $e) {
                // Lưu lại thông báo lỗi để hiển thị hướng dẫn cài đặt database thân thiện
                error_log('[DB] ' . $e->getMessage());
                // [SỬA LỖI] Trả mã 503 và chỉ hiện chi tiết lỗi khi APP_DEBUG=true (tránh lộ host/user DB khi chạy thật)
                if (!headers_sent()) { http_response_code(503); }
                $debug = function_exists('env') ? (env('APP_DEBUG', false) === true || env('APP_DEBUG', false) === 'true') : false;
                $errorMsg = $debug ? $e->getMessage() : 'Không kết nối được tới MySQL (bật APP_DEBUG=true trong .env để xem chi tiết).';
                self::renderSetupGuide($errorMsg);
                exit;
            }
        }
        return self::$instance;
    }

    private static function renderSetupGuide(string $errorMsg): void {
        ?>
        <!DOCTYPE html>
        <html lang="vi">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Chưa kết nối được Database - NEXVORA Store</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
        </head>
        <body class="bg-light py-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow border-0 rounded-4">
                            <div class="card-body p-4 p-md-5">
                                <div class="text-center mb-4">
                                    <div class="badge bg-danger-subtle text-danger px-3 py-2 rounded-pill fs-6 mb-2">
                                        <i class="bi bi-exclamation-triangle-fill me-1"></i> Chưa tìm thấy Database
                                    </div>
                                    <h2 class="fw-bold text-dark">Khởi tạo Database cho NEXVORA Store</h2>
                                    <p class="text-muted">Hệ thống chưa kết nối được tới cơ sở dữ liệu MySQL `<?= htmlspecialchars(DB_NAME) ?>`.</p>
                                </div>

                                <div class="alert alert-secondary font-monospace small mb-4">
                                    <strong>Chi tiết lỗi:</strong> <?= htmlspecialchars($errorMsg) ?>
                                </div>

                                <h5 class="fw-bold mb-3"><i class="bi bi-check-circle-fill text-success me-2"></i>Hướng dẫn kích hoạt trong 1 phút:</h5>
                                <ol class="list-group list-group-numbered mb-4">
                                    <li class="list-group-item">Mở bảng điều khiển <strong>XAMPP Control Panel</strong> và bấm <strong>Start</strong> cả 2 dịch vụ <code>Apache</code> và <code>MySQL</code>.</li>
                                    <li class="list-group-item">Mở trình duyệt truy cập: <a href="http://localhost/phpmyadmin" target="_blank" class="fw-bold">http://localhost/phpmyadmin</a></li>
                                    <li class="list-group-item">Chọn tab <strong>Import</strong> (Nhập), chọn tệp: <br>tệp <code>sql/database.sql</code> trong thư mục dự án (VD: <code>C:\xampp\htdocs\DuAn1-Nhom4\sql\database.sql</code>)</li>
                                    <li class="list-group-item">Bấm nút <strong>Import (Thực hiện)</strong> ở cuối trang để nạp toàn bộ bảng và dữ liệu mẫu.</li>
                                </ol>

                                <div class="text-center">
                                    <a href="index.php" class="btn btn-primary btn-lg rounded-pill px-4">
                                        <i class="bi bi-arrow-clockwise me-1"></i> Tải lại trang (F5)
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </body>
        </html>
        <?php
    }
}
