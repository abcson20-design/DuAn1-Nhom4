<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

/**
 * Model Wishlist - Danh sách yêu thích (Nghiệp vụ chuẩn Lab Buổi 2)
 * Tính chất:
 * 1. UNIQUE KEY (user_id, product_id) chống trùng khóa
 * 2. Khóa ngoại ON DELETE CASCADE (Xóa user/product tự động dọn sạch)
 * 3. Lấy danh sách sản phẩm yêu thích bằng ĐÚNG 2 câu SQL thay vì N+1
 */
class Wishlist extends BaseModel
{
    private static bool $schemaChecked = false;

    public function __construct()
    {
        parent::__construct();
        $this->ensureSchema();
    }

    private function ensureSchema(): void
    {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;
        try {
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS `wishlists` (
                    `id` int(11) NOT NULL AUTO_INCREMENT,
                    `user_id` int(11) NOT NULL,
                    `product_id` int(11) NOT NULL,
                    `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (`id`),
                    UNIQUE KEY `wishlists_user_product_unique` (`user_id`, `product_id`),
                    KEY `fk_wishlist_product` (`product_id`),
                    CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                    CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
            ");
        } catch (Throwable $e) {
            error_log('[Wishlist] Schema init: ' . $e->getMessage());
        }
    }

    /**
     * Bấm thích / bỏ thích (Toggle): Có thì xóa, chưa có thì thêm
     */
    public function toggle(int $userId, int $productId): array
    {
        if ($this->isWishlisted($userId, $productId)) {
            $stmt = $this->db->prepare("DELETE FROM wishlists WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$userId, $productId]);
            return [
                'action' => 'removed',
                'is_wishlisted' => false,
                'message' => 'Đã bỏ sản phẩm khỏi danh sách yêu thích'
            ];
        }

        try {
            $stmt = $this->db->prepare("INSERT INTO wishlists (user_id, product_id, created_at) VALUES (?, ?, NOW())");
            $stmt->execute([$userId, $productId]);
            return [
                'action' => 'added',
                'is_wishlisted' => true,
                'message' => 'Đã thêm sản phẩm vào danh sách yêu thích'
            ];
        } catch (PDOException $e) {
            // Nếu bấm 2 lần nhanh liên tiếp (tranh chấp / race condition), UNIQUE KEY bảo vệ không bao giờ trùng lặp
            return [
                'action' => 'already_exists',
                'is_wishlisted' => true,
                'message' => 'Sản phẩm đã nằm trong danh sách yêu thích'
            ];
        }
    }

    public function isWishlisted(int $userId, int $productId): bool
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$userId, $productId]);
        return (int)$stmt->fetchColumn() > 0;
    }

    /**
     * Lấy danh sách sản phẩm yêu thích kèm GIÁ HIỆN TẠI (Dùng đúng 2 câu SQL, không bị N+1 query)
     */
    public function getByUserId(int $userId): array
    {
        // Câu 1: Lấy danh sách product_id đã thích của user
        $stmt = $this->db->prepare("SELECT product_id, created_at as wishlisted_at FROM wishlists WHERE user_id = ? ORDER BY id DESC");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll();

        if (empty($rows)) {
            return [];
        }

        $prodIds = array_column($rows, 'product_id');
        $placeholders = implode(',', array_fill(0, count($prodIds), '?'));

        // Câu 2: Lấy thông tin chi tiết các sản phẩm với giá bán mới nhất tại thời điểm hiện tại
        $pStmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.id IN ($placeholders) AND p.deleted_at IS NULL
        ");
        $pStmt->execute($prodIds);
        $products = $pStmt->fetchAll();

        $byId = [];
        foreach ($products as $p) {
            $byId[$p['id']] = $p;
        }

        // Ghép kết quả theo thứ tự yêu thích
        $result = [];
        foreach ($rows as $r) {
            $pid = $r['product_id'];
            if (isset($byId[$pid])) {
                $p = $byId[$pid];
                $p['wishlisted_at'] = $r['wishlisted_at'];
                $result[] = $p;
            }
        }

        return $result;
    }

    public function countByUser(int $userId): int
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM wishlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Lấy mảng danh sách ID các sản phẩm đã yêu thích của user (VD: [1, 5, 12])
     */
    public function getProductIdsByUserId(int $userId): array
    {
        $stmt = $this->db->prepare("SELECT product_id FROM wishlists WHERE user_id = ?");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_COLUMN);
        return !empty($rows) ? array_map('intval', $rows) : [];
    }
}
