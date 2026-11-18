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
    }

    /**
     * Nạp dữ liệu mẫu ban đầu cho một số sản phẩm hot (Chỉ chạy 1 lần duy nhất)
     */
    public function seedSampleReviews(): void {
        if (!$this->db) return;

        // Dùng file cờ để đảm bảo chỉ seed đúng 1 lần duy nhất trong toàn bộ vòng đời ứng dụng.
        // Khi Admin đã xóa đánh giá, tuyệt đối KHÔNG tự ý chèn lại dữ liệu mẫu!
        $flagFile = __DIR__ . '/../public/uploads/.reviews_seeded';
        if (file_exists($flagFile)) {
            return;
        }

        $count = (int)$this->db->query("SELECT COUNT(*) FROM `product_reviews`")->fetchColumn();
        if ($count > 0) {
            @file_put_contents($flagFile, date('Y-m-d H:i:s'));
            return;
        }

        @file_put_contents($flagFile, date('Y-m-d H:i:s'));

        $samples = [
            // iPhone 15 Pro Max (ID: 1)
            [
                'product_id' => 1,
                'author_name' => 'Nguyễn Hoàng Nam',
                'author_phone' => '0912****88',
                'rating' => 5,
                'content' => 'Máy dùng cực kỳ mượt mà, khung viền titan cầm nhẹ tay hơn hẳn bản 14 Pro Max cũ của mình. Chụp ảnh ban đêm zoom 5x cực kỳ nét. Shop giao hàng hỏa tốc trong ngày tại Thanh Hóa rất uy tín!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            [
                'product_id' => 1,
                'author_name' => 'Trần Thị Thu Hà',
                'author_phone' => '0988****32',
                'rating' => 5,
                'content' => 'Hàng nguyên seal chuẩn chính hãng VNA, check kích hoạt bảo hành điện tử trên web ngon lành. Shop tư vấn nhiệt tình, đóng gói bọc xốp chống sốc rất dày dặn.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
            ],
            [
                'product_id' => 1,
                'author_name' => 'Lê Minh Đức',
                'author_phone' => '0977****19',
                'rating' => 4,
                'content' => 'Máy đẹp, pin trâu dùng cả ngày xem phim lướt web còn 35%. Mỗi tội trong hộp không kèm củ sạc, may mua kèm combo củ sạc Anker 30W ở shop được giá hời.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 week'))
            ],
            // Samsung Galaxy S25 Ultra (ID: 2)
            [
                'product_id' => 2,
                'author_name' => 'Võ Đình Trọng',
                'author_phone' => '0905****45',
                'rating' => 5,
                'content' => 'Galaxy AI dịch thuật cuộc gọi trực tiếp đỉnh thực sự, màn hình Dynamic AMOLED phẳng xem phim cực đã mắt. Bút S-Pen tiện ghi chú công việc nhanh.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ],
            // AirPods Pro 2 (ID: 11)
            [
                'product_id' => 11,
                'author_name' => 'Phạm Quốc Bảo',
                'author_phone' => '0934****66',
                'rating' => 5,
                'content' => 'Chống ồn ANC đỉnh thật sự, đi xe bus bật lên là cách ly hoàn toàn với tiếng động cơ ồn ào. Âm bass trầm ấm, cổng Type-C tiện sạc chung với điện thoại.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            [
                'product_id' => 11,
                'author_name' => 'Đỗ Thu Trang',
                'author_phone' => '0915****22',
                'rating' => 5,
                'content' => 'Tai nghe đeo êm tai không bị cấn, mic đàm thoại gọi điện cho bạn bè nghe rất rõ. Rất hài lòng với dịch vụ và chế độ bảo hành của NEXVORA.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
            ],
            // Củ sạc Anker 511 Nano 3 30W (ID: 31)
            [
                'product_id' => 31,
                'author_name' => 'Vũ Đình Nam',
                'author_phone' => '0982****90',
                'rating' => 5,
                'content' => 'Củ sạc siêu nhỏ gọn mà tận 30W, sạc chiếc 15 Pro Max lên 50% chỉ tầm 25 phút. Dùng công nghệ GaN cắm sạc liên tục không bị nóng ran.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-2 days'))
            ],
            // Kính cường lực KingKong (ID: 41)
            [
                'product_id' => 41,
                'author_name' => 'Nguyễn Đặng Minh Quân',
                'author_phone' => '0963****12',
                'rating' => 5,
                'content' => 'Kính tự hút khí dán cực dễ tại nhà, vuốt mượt mà không rít tay, viền vát 2.5D không bị cấn ốp lưng. Đáng tiền!',
                'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
            ],
            // Pin Anker 533 (ID: 51)
            [
                'product_id' => 51,
                'author_name' => 'Lê Danh Tuấn',
                'author_phone' => '0948****77',
                'rating' => 5,
                'content' => 'Màn hình hiển thị % pin và thời gian sạc rất trực quan, công suất 30W sạc nhanh cho cả iPad. Pin nhỏ gọn bỏ vừa túi đeo chéo.',
                'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
            ]
        ];

        $stmt = $this->db->prepare("INSERT INTO `product_reviews` 
            (`product_id`, `author_name`, `author_phone`, `rating`, `content`, `is_purchased`, `status`, `created_at`) 
            VALUES (?, ?, ?, ?, ?, 1, 'approved', ?)");
        
        foreach ($samples as $s) {
            $chk = $this->db->prepare("SELECT id FROM `products` WHERE id = ?");
            $chk->execute([$s['product_id']]);
            if (!$chk->fetchColumn()) {
                continue;
            }

            $stmt->execute([
                $s['product_id'],
                $s['author_name'],
                $s['author_phone'],
                $s['rating'],
                $s['content'],
                $s['created_at']
            ]);
            $this->updateProductRating($s['product_id']);
        }
    }
}
