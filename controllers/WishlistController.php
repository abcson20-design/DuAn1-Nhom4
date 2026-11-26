<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Wishlist.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../services/Csrf.php';

/**
 * Controller Wishlist - Quản lý Danh sách yêu thích (Nghiệp vụ chuẩn Lab Buổi 2)
 */
class WishlistController extends BaseController
{
    private Wishlist $wishlistModel;

    public function __construct()
    {
        $this->wishlistModel = new Wishlist();
    }

    /**
     * GET /wishlist - Hiển thị danh sách sản phẩm yêu thích kèm giá hiện tại
     * Đảm bảo đúng 2 câu SQL thay vì N+1 query
     */
    public function index(): void
    {
        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        if (empty($currentUser)) {
            $this->redirect(BASE_URL . 'index.php?url=auth/login&msg=login_required_wishlist');
            return;
        }

        $userId = (int)$currentUser['id'];
        $items = $this->wishlistModel->getByUserId($userId);

        $categoryModel = new Category();
        $categories = $categoryModel->getAll();

        $pageTitle = 'Danh sách yêu thích - Nexvora Store';
        $this->render('wishlist/index', [
            'items' => $items,
            'categories' => $categories,
            'pageTitle' => $pageTitle
        ]);
    }

    /**
     * POST /wishlist/toggle - Thêm hoặc Bỏ thích sản phẩm (Bảo vệ CSRF & Auth Middleware)
     */
    public function toggle(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->json(['success' => false, 'message' => 'Phương thức không được hỗ trợ'], 405);
            return;
        }

        $currentUser = $_SESSION['user'] ?? $_SESSION['admin_user'] ?? null;
        if (empty($currentUser)) {
            $this->json([
                'success' => false,
                'require_login' => true,
                'login_url' => BASE_URL . 'index.php?url=auth/login&msg=login_required_wishlist',
                'message' => 'Vui lòng đăng nhập tài khoản để lưu danh sách yêu thích!'
            ], 401);
            return;
        }

        $this->requireCsrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        if ($productId <= 0) {
            $this->json(['success' => false, 'message' => 'Sản phẩm không hợp lệ!'], 400);
            return;
        }

        $userId = (int)$currentUser['id'];
        $result = $this->wishlistModel->toggle($userId, $productId);
        $totalCount = $this->wishlistModel->countByUser($userId);

        $this->json([
            'success' => true,
            'action' => $result['action'],
            'is_wishlisted' => $result['is_wishlisted'],
            'message' => $result['message'],
            'count' => $totalCount
        ]);
    }
}
