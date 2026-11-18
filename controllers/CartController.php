<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';

class CartController extends BaseController {
    // Helper kiểm tra quyền: Khách không có quyền sử dụng giỏ hàng
    private function requireUser(): void {
        if (empty($_SESSION['user']) && empty($_SESSION['admin_user'])) {
            $this->redirect(BASE_URL . 'index.php?url=auth/login&msg=login_required_cart');
        }
    }

    /**
     * Tự động xác thực và làm mới quyền lợi mã giảm giá theo tổng tiền giỏ hàng thực tế
     * Khắc phục triệt để lỗi: Giảm giỏ hàng dưới mức tối thiểu nhưng mã vẫn giảm tiền
     */
    public static function refreshCouponSession(): void {
        if (empty($_SESSION['applied_coupon']['code'])) {
            unset($_SESSION['applied_coupon'], $_SESSION['coupon_code'], $_SESSION['discount_amount'], $_SESSION['coupon_message']);
            return;
        }

        $subtotal = 0;
        if (!empty($_SESSION['is_buy_now']) && !empty($_SESSION['buy_now_item'])) {
            $subtotal = (float)$_SESSION['buy_now_item']['price'] * (int)$_SESSION['buy_now_item']['quantity'];
        } elseif (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                $subtotal += (float)$item['price'] * (int)$item['quantity'];
            }
        }

        if ($subtotal <= 0) {
            unset($_SESSION['applied_coupon'], $_SESSION['coupon_code'], $_SESSION['discount_amount'], $_SESSION['coupon_message']);
            return;
        }

        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $res = $couponModel->checkAndCalculate($_SESSION['applied_coupon']['code'], $subtotal);
        if ($res['valid']) {
            $_SESSION['applied_coupon'] = $res['coupon'];
            $_SESSION['discount_amount'] = (float)$res['discount'];
            $_SESSION['coupon_message'] = $res['message'];
        } else {
            // Không còn đủ điều kiện áp dụng mã (VD: Đơn dưới mức tối thiểu)
            $_SESSION['coupon_error'] = 'Mã ' . $_SESSION['applied_coupon']['code'] . ' không thể áp dụng: ' . $res['message'];
            unset($_SESSION['applied_coupon'], $_SESSION['coupon_code'], $_SESSION['discount_amount'], $_SESSION['coupon_message']);
        }
    }

    /**
     * Đồng bộ giỏ hàng với Database: Xóa sản phẩm bị soft-delete, cập nhật giá mới nhất và giới hạn tồn kho
     */
    public static function syncCartWithDatabase(): void {
        if (empty($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            return;
        }

        require_once __DIR__ . '/../models/Product.php';
        $productModel = new Product();
        $updated = false;

        foreach ($_SESSION['cart'] as $k => &$item) {
            $prod = $productModel->getById((int)$item['id']);
            if (!$prod || !empty($prod['deleted_at']) || ($prod['status'] ?? 'active') !== 'active') {
                unset($_SESSION['cart'][$k]);
                $_SESSION['cart_warning'] = 'Sản phẩm "' . htmlspecialchars($item['name']) . '" đã ngừng kinh doanh và được xóa khỏi giỏ hàng.';
                $updated = true;
                continue;
            }

            // Đồng bộ giá mới nhất từ database
            if ((float)$item['price'] !== (float)$prod['price']) {
                $item['price'] = (float)$prod['price'];
                $item['name'] = $prod['name'];
                $updated = true;
            }

            // Giới hạn số lượng theo tồn kho thực tế
            if ((int)$item['quantity'] > (int)$prod['quantity']) {
                $item['quantity'] = max(1, (int)$prod['quantity']);
                $updated = true;
            }
        }
        unset($item);

        if ($updated) {
            self::refreshCouponSession();
        }
    }

    public function index(): void {
        // Phân quyền: Khách chỉ được xem giao diện, không có giỏ hàng
        $this->requireUser();

        // Xóa phiên Mua ngay tạm thời khi người dùng chủ động vào xem giỏ hàng
        unset($_SESSION['is_buy_now'], $_SESSION['buy_now_item']);

        // Đồng bộ sản phẩm trong giỏ với database (kiểm tra xóa mềm, giá mới, tồn kho)
        self::syncCartWithDatabase();

        // Đồng bộ lại mã giảm giá theo giỏ hàng
        self::refreshCouponSession();

        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;

        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        $discount = $_SESSION['discount_amount'] ?? 0;
        $shipping = 0; // Phí vận chuyển mặc định: Miễn phí (0đ)
        $finalTotal = max(0, $subtotal - $discount + $shipping);

        $this->render('cart/index', [
            'pageTitle' => 'Giỏ hàng của bạn',
            'cart' => $cart,
            'subtotal' => $subtotal,
            'discount' => $discount,
            'shipping' => $shipping,
            'finalTotal' => $finalTotal,
            'couponCode' => $_SESSION['coupon_code'] ?? ''
        ]);
    }

    public function add(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->redirect(BASE_URL . 'index.php?url=cart');
            return;
        }

        $this->requireCsrf();

        // Phân quyền: Khách không được thêm vào giỏ hàng
        if (empty($_SESSION['user']) && empty($_SESSION['admin_user'])) {
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json([
                    'success' => false,
                    'require_login' => true,
                    'login_url' => BASE_URL . 'index.php?url=auth/login&msg=login_required_cart',
                    'message' => 'Bạn đang duyệt với tư cách Khách. Vui lòng đăng nhập tài khoản User để thêm sản phẩm vào giỏ hàng!'
                ], 401);
            }
            $this->redirect(BASE_URL . 'index.php?url=auth/login&msg=login_required_cart');
        }

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId <= 0) {
            $this->redirect(BASE_URL . 'index.php?url=cart');
        }

        $productModel = new Product();
        $product = $productModel->getById($productId);

        if (!$product) {
            $this->redirect(BASE_URL . 'index.php?url=products');
        }

        // Chốt kiểm tra tồn kho: Không cho thêm quá số lượng tồn
        $stock = (int)($product['quantity'] ?? 0);
        if ($stock <= 0) {
            $msg = 'Sản phẩm "' . $product['name'] . '" hiện đã hết hàng trong kho!';
            if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                $this->json(['success' => false, 'message' => $msg], 400);
            }
            $_SESSION['flash_cart_err'] = $msg;
            $this->redirect(BASE_URL . 'index.php?url=products/detail&id=' . $productId);
            return;
        }

        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        $color = trim($_POST['color'] ?? '');
        $cartKey = $color ? ($productId . '_' . md5($color)) : (string)$productId;

        $curQty = isset($_SESSION['cart'][$cartKey]) ? (int)$_SESSION['cart'][$cartKey]['quantity'] : 0;
        $newQty = $curQty + $quantity;

        if ($newQty > $stock) {
            $availableToAdd = max(0, $stock - $curQty);
            if ($availableToAdd <= 0) {
                $msg = "Bạn đã có {$curQty} chiếc trong giỏ. Kho chỉ còn {$stock} chiếc!";
                if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                    $this->json(['success' => false, 'message' => $msg], 400);
                }
                $_SESSION['flash_cart_err'] = $msg;
                $this->redirect(BASE_URL . 'index.php?url=cart');
                return;
            }
            $quantity = $availableToAdd;
            $newQty = $stock;
        }

        if (isset($_SESSION['cart'][$cartKey])) {
            $_SESSION['cart'][$cartKey]['quantity'] = $newQty;
        } else {
            $_SESSION['cart'][$cartKey] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'color' => $color,
                'slug' => $product['slug'],
                'price' => (float)$product['price'],
                'image' => $product['image'],
                'quantity' => $quantity,
                'category_name' => $product['category_name'] ?? 'Phụ kiện'
            ];
        }

        self::refreshCouponSession();

        // Nếu là yêu cầu AJAX
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $totalCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
            $this->json([
                'success' => true,
                'message' => 'Đã thêm ' . $product['name'] . ' vào giỏ hàng!',
                'cartCount' => $totalCount
            ]);
        }

        // Nếu có cờ buy_now thì chuyển thẳng tới checkout
        if (!empty($_POST['buy_now']) || !empty($_GET['buy_now'])) {
            $this->redirect(BASE_URL . 'index.php?url=checkout');
        }

        $this->redirect(BASE_URL . 'index.php?url=cart');
    }

    /**
     * Mua ngay: Chuyển thẳng đến trang checkout cho sản phẩm này mà KHÔNG thêm vào giỏ hàng chính
     */
    public function buyNow(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            $this->redirect(BASE_URL . 'index.php?url=cart');
            return;
        }

        $this->requireCsrf();

        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        if ($productId > 0) {
            $productModel = new Product();
            $product = $productModel->getById($productId);

            if ($product) {
                $stock = (int)($product['quantity'] ?? 0);
                if ($stock <= 0) {
                    $_SESSION['flash_cart_err'] = 'Sản phẩm "' . $product['name'] . '" đã hết hàng, không thể Mua Ngay!';
                    $this->redirect(BASE_URL . 'index.php?url=products/detail&id=' . $productId);
                    return;
                }

                if ($quantity > $stock) {
                    $quantity = $stock;
                }

                $color = trim($_POST['color'] ?? '');
                if (empty($color)) {
                    $productColors = $productModel->getColors($productId);
                    if (!empty($productColors) && !empty($productColors[0]['color_name'])) {
                        $color = $productColors[0]['color_name'];
                    }
                }

                $_SESSION['is_buy_now'] = true;
                $_SESSION['buy_now_item'] = [
                    'id' => $product['id'],
                    'name' => $product['name'],
                    'color' => $color,
                    'slug' => $product['slug'],
                    'price' => (float)$product['price'],
                    'image' => $product['image'],
                    'quantity' => $quantity,
                    'category_name' => $product['category_name'] ?? 'Phụ kiện'
                ];

                self::refreshCouponSession();
            }
        }

        $this->redirect(BASE_URL . 'index.php?url=checkout');
    }

    public function update(): void {
        $this->requireUser();
        $this->requireCsrf(BASE_URL . 'index.php?url=cart');
        $productModel = new Product();

        // 1. Cập nhật hàng loạt từ form giỏ hàng: name="quantities[key]"
        if (!empty($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $k => $qty) {
                $qty = (int)$qty;
                if (isset($_SESSION['cart'][$k])) {
                    if ($qty <= 0) {
                        unset($_SESSION['cart'][$k]);
                    } else {
                        $pId = (int)$_SESSION['cart'][$k]['id'];
                        $pData = $productModel->getById($pId);
                        $maxStock = (int)($pData['quantity'] ?? 9999);
                        $_SESSION['cart'][$k]['quantity'] = min($qty, $maxStock);
                    }
                }
            }
            self::refreshCouponSession();
            $_SESSION['flash_success'] = 'Đã cập nhật số lượng giỏ hàng thành công!';
            $this->redirect(BASE_URL . 'index.php?url=cart');
            return;
        }

        // 2. Cập nhật từng sản phẩm (qua nút bấm hoặc AJAX)
        $key = $_POST['key'] ?? '';
        $productId = (int)($_POST['product_id'] ?? 0);
        $action = $_POST['action_type'] ?? '';

        $targetKey = null;
        if (!empty($key) && isset($_SESSION['cart'][$key])) {
            $targetKey = $key;
        } elseif ($productId > 0) {
            if (isset($_SESSION['cart'][$productId])) {
                $targetKey = $productId;
            } elseif (isset($_SESSION['cart'][(string)$productId])) {
                $targetKey = (string)$productId;
            } else {
                foreach ($_SESSION['cart'] ?? [] as $k => $item) {
                    if ((int)($item['id'] ?? 0) === $productId) {
                        $targetKey = $k;
                        break;
                    }
                }
            }
        }

        if ($targetKey !== null && isset($_SESSION['cart'][$targetKey])) {
            $pId = (int)$_SESSION['cart'][$targetKey]['id'];
            $pData = $productModel->getById($pId);
            $maxStock = (int)($pData['quantity'] ?? 9999);

            if ($action === 'increase') {
                if ($_SESSION['cart'][$targetKey]['quantity'] < $maxStock) {
                    $_SESSION['cart'][$targetKey]['quantity']++;
                }
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$targetKey]['quantity']--;
                if ($_SESSION['cart'][$targetKey]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$targetKey]);
                }
            } elseif (isset($_POST['quantity'])) {
                $qty = (int)$_POST['quantity'];
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$targetKey]);
                } else {
                    $_SESSION['cart'][$targetKey]['quantity'] = min($qty, $maxStock);
                }
            }
        }

        self::refreshCouponSession();

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            $subtotal = 0;
            foreach ($_SESSION['cart'] ?? [] as $item) {
                $subtotal += $item['price'] * $item['quantity'];
            }
            $discount = $_SESSION['discount_amount'] ?? 0;
            $finalTotal = max(0, $subtotal - $discount);
            $itemQty = isset($_SESSION['cart'][$targetKey]) ? $_SESSION['cart'][$targetKey]['quantity'] : 0;
            $itemTotal = isset($_SESSION['cart'][$targetKey]) ? ($_SESSION['cart'][$targetKey]['price'] * $itemQty) : 0;

            $this->json([
                'success' => true,
                'item_quantity' => $itemQty,
                'item_total' => number_format($itemTotal, 0, ',', '.') . 'đ',
                'subtotal' => number_format($subtotal, 0, ',', '.') . 'đ',
                'discount' => number_format($discount, 0, ',', '.') . 'đ',
                'final_total' => number_format($finalTotal, 0, ',', '.') . 'đ'
            ]);
        }

        $this->redirect(BASE_URL . 'index.php?url=cart');
    }

    public function remove(): void {
        $this->requireUser();

        // 1. Hỗ trợ làm trống toàn bộ giỏ hàng
        if (!empty($_GET['clear_all'])) {
            unset($_SESSION['cart'], $_SESSION['applied_coupon'], $_SESSION['discount_amount'], $_SESSION['coupon_code']);
            $_SESSION['flash_success'] = 'Đã làm trống giỏ hàng thành công!';
            $this->redirect(BASE_URL . 'index.php?url=cart');
            return;
        }

        $key = $_GET['key'] ?? $_POST['key'] ?? '';
        $id = (int)($_GET['id'] ?? $_POST['product_id'] ?? 0);
        $deleted = false;

        // 2. Xóa theo key chính xác
        if (!empty($key) && isset($_SESSION['cart'][$key])) {
            unset($_SESSION['cart'][$key]);
            $deleted = true;
        }

        // 3. Fallback: duyệt xóa theo product_id
        if (!$deleted && $id > 0) {
            if (isset($_SESSION['cart'][$id])) {
                unset($_SESSION['cart'][$id]);
                $deleted = true;
            }
            if (isset($_SESSION['cart'][(string)$id])) {
                unset($_SESSION['cart'][(string)$id]);
                $deleted = true;
            }
            if (!empty($_SESSION['cart'])) {
                foreach ($_SESSION['cart'] as $k => $item) {
                    if ((int)($item['id'] ?? 0) === $id) {
                        unset($_SESSION['cart'][$k]);
                        $deleted = true;
                    }
                }
            }
        }

        if (empty($_SESSION['cart'])) {
            unset($_SESSION['applied_coupon'], $_SESSION['discount_amount'], $_SESSION['coupon_code']);
        } else {
            self::refreshCouponSession();
        }

        if ($deleted) {
            $_SESSION['flash_success'] = 'Đã xóa sản phẩm khỏi giỏ hàng thành công!';
        }

        $this->redirect(BASE_URL . 'index.php?url=cart');
    }

    public function applyCoupon(): void {
        $this->requireUser();
        $this->requireCsrf(BASE_URL . 'index.php?url=cart');
        $code = trim($_POST['coupon_code'] ?? '');
        $cart = $_SESSION['cart'] ?? [];
        $subtotal = 0;
        foreach ($cart as $item) {
            $subtotal += $item['price'] * $item['quantity'];
        }

        if (empty($code)) {
            $_SESSION['coupon_error'] = 'Vui lòng nhập mã khuyến mại!';
            $this->redirect(BASE_URL . 'index.php?url=cart');
            return;
        }

        require_once __DIR__ . '/../models/Coupon.php';
        $couponModel = new Coupon();
        $res = $couponModel->checkAndCalculate($code, $subtotal);

        if ($res['valid']) {
            $_SESSION['coupon_code'] = $res['coupon']['code'];
            $_SESSION['applied_coupon'] = $res['coupon'];
            $_SESSION['discount_amount'] = (float)$res['discount'];
            $_SESSION['coupon_message'] = $res['message'];
            unset($_SESSION['coupon_error']);
        } else {
            $_SESSION['coupon_error'] = $res['message'];
        }
        $this->redirect(BASE_URL . 'index.php?url=cart');
    }
}
