<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';
require_once __DIR__ . '/../models/Review.php';

class ProductController extends BaseController {
    /**
     * TRANG ĐIỆN THOẠI CHUYÊN BIỆT (Nhiều hãng: Apple, Samsung, Xiaomi, OPPO...)
     */
    public function phones(): void {
        $productModel = new Product();
        $brandModel = new Brand();

        $selectedBrandId = !empty($_GET['brand']) ? (int)$_GET['brand'] : null;

        $filters = [
            'category_id' => 1, // Luôn luôn là Điện thoại thông minh
            'brand_id' => $selectedBrandId,
            'keyword' => !empty($_GET['q']) ? trim($_GET['q']) : null,
            'min_price' => !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $productModel->searchAndFilter($filters, $limit, $offset);
        $totalProducts = $productModel->countSearchAndFilter($filters);
        $totalPages = ceil($totalProducts / $limit);

        // Lấy danh sách các hãng điện thoại
        $phoneBrands = $brandModel->getPhoneBrands();

        $this->render('product/phones', [
            'pageTitle' => 'Điện Thoại Flagship & Smartphone Chính Hãng',
            'products' => $products,
            'phoneBrands' => $phoneBrands,
            'selectedBrandId' => $selectedBrandId,
            'filters' => $filters,
            'totalProducts' => $totalProducts,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    /**
     * TRANG PHỤ KIỆN CHUYÊN BIỆT (Nhiều loại: Tai nghe Bluetooth, có dây, củ sạc, cáp sạc, pin, ốp lưng...)
     */
    public function accessories(): void {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $selectedCatId = !empty($_GET['category']) ? (int)$_GET['category'] : null;

        $filters = [
            'exclude_category_id' => 1, // Loại trừ điện thoại, chỉ lấy phụ kiện
            'category_id' => $selectedCatId,
            'brand_id' => !empty($_GET['brand']) ? (int)$_GET['brand'] : null,
            'keyword' => !empty($_GET['q']) ? trim($_GET['q']) : null,
            'device' => !empty($_GET['device']) ? trim($_GET['device']) : null,
            'min_price' => !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $productModel->searchAndFilter($filters, $limit, $offset);
        $totalProducts = $productModel->countSearchAndFilter($filters);
        $totalPages = ceil($totalProducts / $limit);

        // Lấy danh sách các loại phụ kiện & thương hiệu phụ kiện
        $accessoryCategories = $categoryModel->getAccessories();
        $accessoryBrands = $brandModel->getAccessoryBrands();

        $this->render('product/accessories', [
            'pageTitle' => 'Phụ Kiện Điện Thoại, Tai Nghe & Đồ Chơi Công Nghệ',
            'products' => $products,
            'accessoryCategories' => $accessoryCategories,
            'accessoryBrands' => $accessoryBrands,
            'selectedCatId' => $selectedCatId,
            'filters' => $filters,
            'totalProducts' => $totalProducts,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $categorySlug = $_GET['category'] ?? null;
        $currentCategory = null;
        $categoryId = null;

        if ($categorySlug) {
            $currentCategory = $categoryModel->getBySlug($categorySlug);
            if ($currentCategory) {
                $categoryId = $currentCategory['id'];
            }
        }

        $filters = [
            'category_id' => $categoryId,
            'brand_id' => !empty($_GET['brand']) ? (int)$_GET['brand'] : null,
            'keyword' => !empty($_GET['q']) ? trim($_GET['q']) : null,
            'device' => !empty($_GET['device']) ? trim($_GET['device']) : null,
            'min_price' => !empty($_GET['min_price']) ? (float)$_GET['min_price'] : null,
            'max_price' => !empty($_GET['max_price']) ? (float)$_GET['max_price'] : null,
            'sort' => $_GET['sort'] ?? 'newest'
        ];

        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $limit = 12;
        $offset = ($page - 1) * $limit;

        $products = $productModel->searchAndFilter($filters, $limit, $offset);
        $totalProducts = $productModel->countSearchAndFilter($filters);
        $totalPages = ceil($totalProducts / $limit);

        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        $this->render('product/index', [
            'pageTitle' => $currentCategory ? $currentCategory['name'] : 'Tất Cả Sản Phẩm Công Nghệ',
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
            'currentCategory' => $currentCategory,
            'filters' => $filters,
            'totalProducts' => $totalProducts,
            'page' => $page,
            'totalPages' => $totalPages
        ]);
    }

    public function detail(): void {
        $slug = $_GET['slug'] ?? '';
        $id = !empty($_GET['id']) ? (int)$_GET['id'] : 0;
        if (!$slug && !$id) {
            $this->redirect(BASE_URL . 'index.php?url=products');
        }

        $productModel = new Product();
        $product = $slug ? $productModel->getBySlug($slug) : $productModel->getById($id);

        if (!$product) {
            $this->redirect(BASE_URL . 'index.php?url=products');
        }

        // Tăng lượt xem
        $productModel->incrementViews($product['id']);

        // Giải mã thông số kỹ thuật (hỗ trợ cả JSON, dòng phân cách Tab hoặc Dấu hai chấm)
        $specs = [];
        if (!empty($product['specs'])) {
            $decoded = json_decode($product['specs'], true);
            if (is_array($decoded)) {
                $specs = $decoded;
            } else {
                $lines = explode("\n", str_replace(["\r\n", "\r"], "\n", $product['specs']));
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    if (strpos($line, "\t") !== false) {
                        $parts = explode("\t", $line, 2);
                        $specs[trim($parts[0])] = trim($parts[1]);
                    } elseif (strpos($line, ':') !== false) {
                        $parts = explode(':', $line, 2);
                        $specs[trim($parts[0])] = trim($parts[1]);
                    }
                }
            }
        }

        // ƯU TIÊN LẤY DỮ LIỆU TỪ BẢNG QUAN HỆ ATTRIBUTES & GALLERIES & COLORS (Nếu có trong DB)
        $dbAttrs = $productModel->getAttributes($product['id']);
        if (!empty($dbAttrs)) {
            $specs = [];
            foreach ($dbAttrs as $attr) {
                $specs[$attr['name']] = $attr['value'];
            }
        }

        $dbGalleries = $productModel->getGalleries($product['id']);
        if (!empty($dbGalleries)) {
            $customGallery = [];
            foreach ($dbGalleries as $g) {
                $customGallery[] = [
                    'image' => $g['image_url'],
                    'title' => $g['title'],
                    'tag' => $g['tag'] ?? 'Cấu tạo',
                    'desc' => $g['description'] ?? ''
                ];
            }
            $product['gallery'] = json_encode($customGallery, JSON_UNESCAPED_UNICODE);
        }

        $dbColors = $productModel->getColors($product['id']);
        if (!empty($dbColors)) {
            $customColors = [];
            foreach ($dbColors as $c) {
                $customColors[] = [
                    'name' => $c['color_name'],
                    'image' => $c['color_image'],
                    'quantity' => (int)$c['quantity']
                ];
            }
            $product['colors'] = json_encode($customColors, JSON_UNESCAPED_UNICODE);
        }

        // Lấy sản phẩm liên quan
        $relatedProducts = $productModel->getRelated($product['category_id'], $product['id'], 4);

                // Lấy bình luận và đánh giá sản phẩm
        $reviewModel = new Review();
        $reviews = $reviewModel->getByProductId((int)$product['id']);
        $reviewStats = $reviewModel->getStats((int)$product['id']);
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;

        $this->render('product/detail', [
            'pageTitle' => $product['name'] . ' - NEXVORA',
            'product' => $product,
            'specs' => $specs,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
            'reviewStats' => $reviewStats,
            'currentUser' => $currentUser
        ]);
    }

    /**
     * API Gợi ý tìm kiếm nhanh (AJAX Live Search)
     */
    public function suggest(): void {
        header('Content-Type: application/json; charset=utf-8');
        $keyword = trim($_GET['q'] ?? '');
        if ($keyword === '') {
            echo json_encode(['success' => true, 'data' => []]);
            exit;
        }

        $productModel = new Product();
        $results = $productModel->liveSuggest($keyword, 6);

        // Format lại dữ liệu cho frontend
        $formatted = array_map(function($item) {
            return [
                'id' => (int)$item['id'],
                'name' => $item['name'],
                'slug' => $item['slug'],
                'price' => (float)$item['price'],
                'price_formatted' => number_format((float)$item['price'], 0, ',', '.') . 'đ',
                'old_price' => !empty($item['old_price']) ? (float)$item['old_price'] : null,
                'old_price_formatted' => !empty($item['old_price']) ? number_format((float)$item['old_price'], 0, ',', '.') . 'đ' : null,
                'image' => $item['image'],
                'brand_name' => $item['brand_name'] ?? '',
                'category_name' => $item['category_name'] ?? '',
                'compatible_device' => $item['compatible_device'] ?? '',
                'detail_url' => BASE_URL . 'index.php?url=products/detail&slug=' . urlencode($item['slug'])
            ];
        }, $results);

        echo json_encode([
            'success' => true,
            'count' => count($formatted),
            'keyword' => $keyword,
            'all_url' => BASE_URL . 'index.php?url=products&q=' . urlencode($keyword),
            'data' => $formatted
        ]);
        exit;
    }

    /**
     * Xử lý gửi bình luận & đánh giá sản phẩm
     */
    public function addComment(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL);
        }

        $this->requireCsrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        $slug = trim($_POST['product_slug'] ?? '');
        $authorName = trim($_POST['author_name'] ?? '');
        $authorPhone = trim($_POST['author_phone'] ?? '');
        $rating = max(1, min(5, (int)($_POST['rating'] ?? 5))); // Giới hạn 1-5 sao
        $content = trim($_POST['content'] ?? '');

        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        $userId = !empty($currentUser['id']) ? (int)$currentUser['id'] : null;

        if ($currentUser && empty($authorName)) {
            $authorName = $currentUser['fullname'] ?? 'Khách hàng NEXVORA';
        }

        $redirectUrl = !empty($slug) ? BASE_URL . 'index.php?url=products/detail&slug=' . urlencode($slug) . '#reviews-section' : BASE_URL;

        // 1. Bắt buộc đăng nhập mới được đánh giá / bình luận
        if (empty($currentUser)) {
            $_SESSION['review_error'] = 'Vui lòng đăng nhập tài khoản Khách hàng để viết đánh giá và bình luận sản phẩm!';
            $loginUrl = BASE_URL . 'index.php?url=auth/login&msg=login_required_review&redirect=' . urlencode('index.php?url=products/detail&slug=' . $slug . '#reviews-section');
            $this->redirect($loginUrl);
        }

        // 2. Chặn tài khoản Quản trị viên (Admin) không được phép đánh giá sản phẩm
        $isAdmin = !empty($_SESSION['admin_user']) || (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin');
        if ($isAdmin) {
            $_SESSION['review_error'] = 'Tài khoản Quản trị viên (Admin) không được phép tự đánh giá sản phẩm nhằm đảm bảo tính khách quan và minh bạch cho khách hàng!';
            $this->redirect($redirectUrl);
        }

        if ($productId <= 0 || empty($content) || empty($authorName)) {
            $_SESSION['review_error'] = 'Vui lòng nhập nội dung đánh giá và chọn số sao!';
            $this->safeRedirect($redirectUrl, BASE_URL);
            return;
        }

        // Kiểm tra chống spam: 1 người dùng chỉ được gửi tối đa 1 đánh giá cho cùng 1 sản phẩm
        $reviewModel = new Review();
        if ($userId) {
            $existingReview = $reviewModel->getDb()->prepare("SELECT COUNT(*) FROM product_reviews WHERE product_id = ? AND user_id = ?"); // [SỬA LỖI] bảng 'reviews' không tồn tại -> lỗi 500 khi gửi đánh giá
            $existingReview->execute([$productId, $userId]);
            if ((int)$existingReview->fetchColumn() > 0) {
                $_SESSION['review_error'] = 'Bạn đã gửi đánh giá cho sản phẩm này rồi! Mỗi tài khoản chỉ được đánh giá 1 lần cho mỗi sản phẩm.';
                $this->safeRedirect($redirectUrl, BASE_URL);
                return;
            }
        }

        // Kiểm tra thực tế xem khách hàng đã từng mua và nhận hàng thành công (completed) chưa
        $isPurchased = 0;
        require_once __DIR__ . '/../models/Order.php';
        $orderModel = new Order();
        if ($userId) {
            $chkStmt = $orderModel->getDb()->prepare("
                SELECT COUNT(*) FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.user_id = ? AND oi.product_id = ? AND o.order_status = 'completed'
            ");
            $chkStmt->execute([$userId, $productId]);
            $isPurchased = ((int)$chkStmt->fetchColumn() > 0) ? 1 : 0;
        } elseif (!empty($authorPhone)) {
            $chkStmt = $orderModel->getDb()->prepare("
                SELECT COUNT(*) FROM order_items oi
                JOIN orders o ON oi.order_id = o.id
                WHERE o.customer_phone = ? AND oi.product_id = ? AND o.order_status = 'completed'
            ");
            $chkStmt->execute([$authorPhone, $productId]);
            $isPurchased = ((int)$chkStmt->fetchColumn() > 0) ? 1 : 0;
        }

        $reviewModel = new Review();
        $success = $reviewModel->addReview([
            'product_id' => $productId,
            'user_id' => $userId,
            'author_name' => $authorName,
            'author_phone' => $authorPhone,
            'rating' => $rating,
            'content' => $content,
            'is_purchased' => $isPurchased
        ]);

        if ($success) {
            $_SESSION['review_success'] = 'Cảm ơn bạn! Đánh giá của bạn đã được đăng thành công.';
        } else {
            $_SESSION['review_error'] = 'Không thể gửi bình luận, vui lòng thử lại sau.';
        }

        $this->safeRedirect($redirectUrl, BASE_URL);
    }

    /**
     * AJAX Thích bình luận hữu ích (Có chống spam và giới hạn lượt thích)
     */
    public function likeComment(): void {
        header('Content-Type: application/json; charset=utf-8');
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !Csrf::validateRequest()) {
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ (CSRF). Vui lòng tải lại trang!']);
            exit;
        }
        $reviewId = (int)($_POST['review_id'] ?? 0);

        if (!isset($_SESSION['liked_reviews'])) {
            $_SESSION['liked_reviews'] = [];
        }

        if ($reviewId > 0) {
            require_once __DIR__ . '/../services/RateLimiter.php';
            $clientIp = RateLimiter::getClientIp();
            $ipKey = 'like_rev_' . $reviewId;

            // Giới hạn 1 lượt like / IP / ngày và chặn spam theo session
            // [SỬA LỖI] RateLimiter không có hàm tooManyAttempts() -> Fatal Error 500 mỗi khi bấm Thích. Dùng isLocked()
            if (!empty($_SESSION['liked_reviews'][$reviewId]) || RateLimiter::isLocked($ipKey, $clientIp) !== null) {
                echo json_encode(['success' => false, 'message' => 'Bạn hoặc địa chỉ IP này đã đánh giá hữu ích cho nhận xét này rồi!']);
                exit;
            }

            RateLimiter::hit($ipKey, $clientIp, 1, 86400);
            $reviewModel = new Review();
            $reviewModel->likeReview($reviewId);
            $_SESSION['liked_reviews'][$reviewId] = true;
            echo json_encode(['success' => true]);
            exit;
        }
        echo json_encode(['success' => false]);
        exit;
    }

    /**
     * Quản trị viên (Admin) xóa bình luận / đánh giá
     */
    public function deleteComment(): void {
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        $isAdmin = !empty($_SESSION['admin_user']) || (!empty($currentUser) && ($currentUser['role'] ?? '') === 'admin');

        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest'
               || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/json') !== false)
               || isset($_POST['ajax']);

        $reviewId = (int)($_POST['review_id'] ?? $_GET['id'] ?? 0);
        $redirectUrl = !empty($_POST['redirect']) ? $_POST['redirect'] : (!empty($_GET['redirect']) ? $_GET['redirect'] : (!empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : BASE_URL));
        // [SỬA LỖI BẢO MẬT] Chống Open Redirect: chỉ cho phép chuyển hướng về đường dẫn nội bộ
        if (!str_starts_with((string)$redirectUrl, BASE_URL) && !(str_starts_with((string)$redirectUrl, 'index.php'))) {
            $redirectUrl = BASE_URL;
        }

        if (!$isAdmin) {
            if ($isAjax || isset($_POST['review_id'])) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Bạn không có quyền quản trị để xóa đánh giá này!']);
                exit;
            }
            $_SESSION['review_error'] = 'Chỉ Quản trị viên (Admin) mới có quyền xóa đánh giá!';
            $this->safeRedirect($redirectUrl, BASE_URL);
        }

        // [SỬA LỖI BẢO MẬT] Xóa đánh giá là thao tác thay đổi dữ liệu -> bắt buộc CSRF token
        if (!Csrf::validateRequest()) {
            if ($isAjax || isset($_POST['review_id'])) {
                header('Content-Type: application/json; charset=utf-8');
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'Token CSRF không hợp lệ hoặc phiên làm việc đã hết hạn. Vui lòng tải lại trang!']);
                exit;
            }
            $_SESSION['review_error'] = 'Lỗi bảo mật (CSRF Token không hợp lệ). Vui lòng thao tác lại!';
            $this->safeRedirect($redirectUrl, BASE_URL);
        }

        if ($reviewId <= 0) {
            if ($isAjax || isset($_POST['review_id'])) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode(['success' => false, 'message' => 'Mã đánh giá không hợp lệ!']);
                exit;
            }
            $_SESSION['review_error'] = 'Mã đánh giá không hợp lệ!';
            $this->safeRedirect($redirectUrl, BASE_URL);
        }

        $reviewModel = new Review();
        $success = $reviewModel->deleteReview($reviewId);

        if ($isAjax || isset($_POST['review_id'])) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode([
                'success' => $success,
                'message' => $success ? 'Đã xóa đánh giá thành công!' : 'Không thể xóa đánh giá này.'
            ]);
            exit;
        }

        if ($success) {
            $_SESSION['review_success'] = 'Đã xóa đánh giá thành công!';
        } else {
            $_SESSION['review_error'] = 'Có lỗi xảy ra khi xóa đánh giá!';
        }

        $this->safeRedirect($redirectUrl, BASE_URL);
    }
}
