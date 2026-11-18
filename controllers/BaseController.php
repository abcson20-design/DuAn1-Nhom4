<?php
require_once __DIR__ . '/../config/config.php';

abstract class BaseController {
    protected function render(string $viewPath, array $data = [], string $layout = 'layouts/header'): void {
        extract($data);

        // Load models for global header if needed (Categories for navbar)
        require_once __DIR__ . '/../models/Category.php';
        $categoryModel = new Category();
        $navCategories = $categoryModel->getAll();

        // Cart count for badge
        $cartCount = 0;
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $cartCount += $item['quantity'];
            }
        }

        // Include Header
        if ($layout) {
            require_once __DIR__ . '/../views/' . $layout . '.php';
        }

        // Include main view content
        require_once __DIR__ . '/../views/' . $viewPath . '.php';

        // Include Footer
        if ($layout === 'layouts/header') {
            require_once __DIR__ . '/../views/layouts/footer.php';
        }
    }

    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function redirect(string $url): void {
        header("Location: " . $url);
        exit;
    }

    /**
     * Chống tấn công Open-Redirect: Chỉ chuyển hướng đến các đường dẫn nội bộ hợp lệ
     */
    protected function safeRedirect(string $url, string $fallback = BASE_URL): void {
        $trimmed = trim($url);
        if (empty($trimmed)) {
            $this->redirect($fallback);
            return;
        }

        // Cho phép URL cùng domain BASE_URL hoặc đường dẫn tương đối bắt đầu bằng 1 dấu '/'
        if (str_starts_with($trimmed, BASE_URL) || (str_starts_with($trimmed, '/') && !str_starts_with($trimmed, '//'))) {
            $this->redirect($trimmed);
            return;
        }

        // Cho phép đường dẫn tương đối như 'index.php?url=...'
        if (!preg_match('#^[a-z][a-z0-9+.-]*:#i', $trimmed) && !str_starts_with($trimmed, '//')) {
            $this->redirect(BASE_URL . ltrim($trimmed, '/'));
            return;
        }

        $this->redirect($fallback);
    }

    /**
     * Bắt buộc kiểm tra token CSRF đối với tất cả thao tác POST / PUT / DELETE
     */
    protected function requireCsrf(?string $redirectUrl = null): void {
        if (!Csrf::validateRequest()) {
            http_response_code(403);
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json([
                    'success' => false,
                    'message' => 'Lỗi bảo mật: Token CSRF không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng tải lại trang!'
                ], 403);
            }
            if ($redirectUrl) {
                $_SESSION['flash_err'] = 'Lỗi bảo mật (CSRF Token không hợp lệ). Vui lòng thao tác lại!';
                $this->redirect($redirectUrl);
            }
            die('<!DOCTYPE html><html lang="vi"><head><meta charset="UTF-8"><title>403 Forbidden - CSRF Token Invalid</title><style>body{background:#0b172e;color:#fff;font-family:sans-serif;display:flex;align-items:center;justify-content:center;height:100vh;margin:0;}div{text-align:center;padding:30px;background:#1e293b;border-radius:16px;border:1px solid #334155;max-width:500px;}a{color:#06b6d4;text-decoration:none;font-weight:bold;}</style></head><body><div><h2>403 - Lỗi bảo mật CSRF</h2><p>Mã xác thực token bảo mật của biểu mẫu không hợp lệ hoặc phiên làm việc đã hết hạn.</p><p><a href="javascript:history.back()">Quay lại thử lại</a> hoặc <a href="' . BASE_URL . '">Về trang chủ</a></p></div></body></html>');
        }
    }
}
