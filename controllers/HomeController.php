<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Category.php';
require_once __DIR__ . '/../models/Brand.php';

class HomeController extends BaseController {
    public function index(): void {
        $productModel = new Product();
        $categoryModel = new Category();
        $brandModel = new Brand();

        $featuredProducts = $productModel->getFeatured(8);
        $hotProducts = $productModel->getHot(6);
        $categories = $categoryModel->getAll();
        $brands = $brandModel->getAll();

        // Lấy sản phẩm theo từng nhóm danh mục chính để hiển thị tab trang chủ
        // Nhóm 1: Điện thoại
        $phones = $productModel->searchAndFilter(['category_id' => 1], 4);
        // Nhóm 2: Tai nghe Bluetooth & Có dây
        $headphones = $productModel->searchAndFilter(['category_id' => 2], 4);
        // Nhóm 3: Củ sạc & Cáp sạc (Tự động lấy sản phẩm sạc/cáp, nếu chưa có thì lấy phụ kiện khác)
        $chargers = $productModel->searchAndFilter(['category_id' => 4], 4);
        if (empty($chargers)) {
            // Tự động tìm các sản phẩm phụ kiện khác (Tai nghe, pin sạc, ốp lưng...) để gợi ý combo
            $extraAccessories = $productModel->searchAndFilter([], 10);
            $chargers = array_values(array_filter($extraAccessories, function($p) {
                return isset($p['category_id']) && $p['category_id'] != 1;
            }));
            $chargers = array_slice($chargers, 0, 4);
        }

        $this->render('home/index', [
            'pageTitle' => 'Trang chủ - Điện thoại, Tai nghe & Phụ kiện cao cấp',
            'featuredProducts' => $featuredProducts,
            'hotProducts' => $hotProducts,
            'categories' => $categories,
            'brands' => $brands,
            'phones' => $phones,
            'headphones' => $headphones,
            'chargers' => $chargers
        ]);
    }
}
