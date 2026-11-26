<?php
require_once __DIR__ . '/BaseModel.php';

class Review extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    /**
     * Lấy danh sách bình luận đã duyệt của sản phẩm
     */
    public function getByProductId(int $productId, int $limit = 50): array {
        if (!$this->db) return [];
        $sql = "SELECT r.*, u.fullname as user_fullname, u.role as user_role
                FROM `product_reviews` r
                LEFT JOIN `users` u ON r.user_id = u.id
                WHERE r.product_id = :product_id AND r.status = 'approved'
                ORDER BY r.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':product_id', $productId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thống kê số sao và điểm đánh giá trung bình
     */
    public function getStats(int $productId): array {
        $defaultStats = [
            'total' => 0,
            'avg_rating' => 5.0,
            'stars' => [
                5 => ['count' => 0, 'percent' => 0],
                4 => ['count' => 0, 'percent' => 0],
                3 => ['count' => 0, 'percent' => 0],
                2 => ['count' => 0, 'percent' => 0],
                1 => ['count' => 0, 'percent' => 0]
            ]
        ];

        if (!$this->db) return $defaultStats;

        $stmt = $this->db->prepare("SELECT rating, COUNT(*) as cnt 
                                    FROM `product_reviews` 
                                    WHERE product_id = ? AND status = 'approved' 
                                    GROUP BY rating");
        $stmt->execute([$productId]);
        $rows = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $total = array_sum($rows);
        if ($total === 0) {
            // Lấy rating mặc định từ bảng products nếu chưa có review nào
            $pStmt = $this->db->prepare("SELECT rating FROM `products` WHERE id = ?");
            $pStmt->execute([$productId]);
            $prodRating = (float)($pStmt->fetchColumn() ?: 5.0);
            $defaultStats['avg_rating'] = $prodRating;
            return $defaultStats;
        }

        $sumStars = 0;
        foreach ($rows as $star => $count) {
            $sumStars += ((int)$star) * ((int)$count);
        }

        $avg = round($sumStars / $total, 1);
        $stars = [];
        for ($s = 5; $s >= 1; $s--) {
            $c = (int)($rows[$s] ?? 0);
            $stars[$s] = [
                'count' => $c,
                'percent' => $total > 0 ? round(($c / $total) * 100) : 0
            ];
        }

        return [
            'total' => $total,
            'avg_rating' => $avg,
            'stars' => $stars
        ];
    }

    /**
     * Thêm bình luận / đánh giá mới
     */
    public function addReview(array $data): bool {
        if (!$this->db) return false;
        $sql = "INSERT INTO `product_reviews` 
                (`product_id`, `user_id`, `author_name`, `author_email`, `author_phone`, `rating`, `content`, `is_purchased`, `status`) 
                VALUES (:product_id, :user_id, :author_name, :author_email, :author_phone, :rating, :content, :is_purchased, 'approved')";
        
        $stmt = $this->db->prepare($sql);
        $success = $stmt->execute([
            ':product_id' => $data['product_id'],
            ':user_id' => $data['user_id'] ?? null,
            ':author_name' => $data['author_name'],
            ':author_email' => $data['author_email'] ?? null,
            ':author_phone' => $data['author_phone'] ?? null,
            ':rating' => max(1, min(5, (int)($data['rating'] ?? 5))),
            ':content' => $data['content'],
            ':is_purchased' => !empty($data['is_purchased']) ? 1 : 0
        ]);

        if ($success) {
            // Cập nhật lại điểm rating trung bình vào bảng products
            $this->updateProductRating((int)$data['product_id']);
        }

        return $success;
    }

    /**
     * Cập nhật điểm rating trung bình vào bảng products
     */
    private function updateProductRating(int $productId): void {
        $stats = $this->getStats($productId);
        if ($stats['total'] > 0) {
            $stmt = $this->db->prepare("UPDATE `products` SET `rating` = ? WHERE `id` = ?");
            $stmt->execute([$stats['avg_rating'], $productId]);
        } else {
            $stmt = $this->db->prepare("UPDATE `products` SET `rating` = 5.0 WHERE `id` = ?");
            $stmt->execute([$productId]);
        }
    }

    /**
     * Tăng lượt thích bình luận hữu ích
     */
    public function likeReview(int $reviewId): bool {
        if (!$this->db) return false;
        $stmt = $this->db->prepare("UPDATE `product_reviews` SET `likes` = `likes` + 1 WHERE `id` = ?");
        return $stmt->execute([$reviewId]);
    }

    /**
     * Xóa đánh giá / bình luận (Chỉ dành cho Admin)
     */
    public function deleteReview(int $reviewId): bool {
        if (!$this->db) return false;
        // Lấy product_id trước khi xóa để cập nhật lại rating cho sản phẩm
        $stmt = $this->db->prepare("SELECT product_id FROM `product_reviews` WHERE id = ?");
        $stmt->execute([$reviewId]);
        $productId = (int)$stmt->fetchColumn();

        $delStmt = $this->db->prepare("DELETE FROM `product_reviews` WHERE id = ?");
        $success = $delStmt->execute([$reviewId]);

        if ($success && $productId > 0) {
            $this->updateProductRating($productId);
        }

        return $success;
    }

    /**
     * Lấy tất cả đánh giá phục vụ trang quản trị Admin
     */
    public function getAllReviews(int $limit = 100): array {
        if (!$this->db) return [];
        $sql = "SELECT r.*, p.name as product_name, p.slug as product_slug, p.image as product_image
                FROM `product_reviews` r
                LEFT JOIN `products` p ON r.product_id = p.id
                ORDER BY r.created_at DESC
                LIMIT :limit";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }}
