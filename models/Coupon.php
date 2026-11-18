<?php
require_once __DIR__ . '/BaseModel.php';

class Coupon extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(bool $onlyActive = false): array {
        $sql = "SELECT * FROM coupons WHERE deleted_at IS NULL";
        if ($onlyActive) {
            $sql .= " AND status = 'active' AND (end_date IS NULL OR end_date >= NOW()) AND quantity > 0";
        }
        $sql .= " ORDER BY id DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM coupons WHERE code = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([strtoupper(trim($code))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    /**
     * Lấy danh sách các mã khuyến mại có thể sử dụng (hợp lệ cho khách chọn)
     */
    public function getAvailableCoupons(float $orderAmount = 0): array {
        $stmt = $this->db->query("
            SELECT * FROM coupons 
            WHERE deleted_at IS NULL 
              AND status = 'active' 
              AND (start_date IS NULL OR start_date <= NOW())
              AND (end_date IS NULL OR end_date >= NOW()) 
              AND quantity > 0
            ORDER BY min_order_amount ASC, id DESC
        ");
        $list = $stmt->fetchAll();

        // Gắn thêm cờ hợp lệ dựa vào giá trị đơn hàng
        foreach ($list as &$c) {
            $c['is_eligible'] = ($orderAmount >= (float)$c['min_order_amount']);
            if ($c['discount_type'] === 'percent') {
                $calc = ($orderAmount * (float)$c['discount_value']) / 100;
                if (!empty($c['max_discount']) && $calc > (float)$c['max_discount']) {
                    $calc = (float)$c['max_discount'];
                }
                $c['estimated_discount'] = $calc;
            } else {
                $c['estimated_discount'] = (float)$c['discount_value'];
            }
        }
        unset($c);

        return $list;
    }

    /**
     * Kiểm tra tính hợp lệ và tính toán số tiền giảm
     */
    public function checkAndCalculate(string $code, float $orderAmount): array {
        $coupon = $this->getByCode($code);
        if (!$coupon) {
            return ['valid' => false, 'message' => 'Mã khuyến mại không tồn tại!'];
        }

        if ($coupon['status'] !== 'active') {
            return ['valid' => false, 'message' => 'Mã khuyến mại hiện đang tạm khóa!'];
        }

        if (!empty($coupon['start_date']) && strtotime($coupon['start_date']) > time()) {
            return ['valid' => false, 'message' => 'Mã khuyến mại chưa đến ngày bắt đầu áp dụng!'];
        }

        if (!empty($coupon['end_date']) && strtotime($coupon['end_date']) < time()) {
            return ['valid' => false, 'message' => 'Mã khuyến mại này đã hết hạn sử dụng!'];
        }

        if ($coupon['quantity'] <= 0) {
            return ['valid' => false, 'message' => 'Mã khuyến mại này đã hết lượt sử dụng!'];
        }

        if ($orderAmount < (float)$coupon['min_order_amount']) {
            $needMore = (float)$coupon['min_order_amount'] - $orderAmount;
            return [
                'valid' => false, 
                'message' => 'Đơn hàng chưa đạt mức tối thiểu ' . number_format($coupon['min_order_amount'], 0, ',', '.') . 'đ (Cần mua thêm ' . number_format($needMore, 0, ',', '.') . 'đ)!'
            ];
        }

        $discount = 0;
        if ($coupon['discount_type'] === 'percent') {
            $discount = ($orderAmount * (float)$coupon['discount_value']) / 100;
            if (!empty($coupon['max_discount']) && $discount > (float)$coupon['max_discount']) {
                $discount = (float)$coupon['max_discount'];
            }
        } else {
            $discount = (float)$coupon['discount_value'];
        }

        // Không giảm quá tổng giá trị đơn
        if ($discount > $orderAmount) {
            $discount = $orderAmount;
        }

        return [
            'valid' => true,
            'coupon' => $coupon,
            'discount' => $discount,
            'message' => 'Áp dụng mã "' . $coupon['code'] . '" thành công! Giảm ' . number_format($discount, 0, ',', '.') . 'đ.'
        ];
    }

    public function increaseUsed(string $code): bool {
        $stmt = $this->db->prepare("
            UPDATE coupons 
            SET used_count = used_count + 1, quantity = GREATEST(0, quantity - 1) 
            WHERE code = ?
        ");
        return $stmt->execute([strtoupper(trim($code))]);
    }

    public function decreaseUsed(string $code): bool {
        $stmt = $this->db->prepare("
            UPDATE coupons 
            SET used_count = GREATEST(0, used_count - 1), quantity = quantity + 1 
            WHERE code = ?
        ");
        return $stmt->execute([strtoupper(trim($code))]);
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO coupons (code, title, discount_type, discount_value, max_discount, min_order_amount, quantity, start_date, end_date, status, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            strtoupper(trim($data['code'])),
            $data['title'],
            $data['discount_type'] ?? 'fixed',
            $data['discount_value'] ?? 0,
            !empty($data['max_discount']) ? $data['max_discount'] : null,
            $data['min_order_amount'] ?? 0,
            $data['quantity'] ?? 100,
            !empty($data['start_date']) ? $data['start_date'] : date('Y-m-d H:i:s'),
            !empty($data['end_date']) ? $data['end_date'] : null,
            $data['status'] ?? 'active'
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE coupons 
            SET code = ?, title = ?, discount_type = ?, discount_value = ?, max_discount = ?, min_order_amount = ?, quantity = ?, start_date = ?, end_date = ?, status = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            strtoupper(trim($data['code'])),
            $data['title'],
            $data['discount_type'] ?? 'fixed',
            $data['discount_value'] ?? 0,
            !empty($data['max_discount']) ? $data['max_discount'] : null,
            $data['min_order_amount'] ?? 0,
            $data['quantity'] ?? 100,
            !empty($data['start_date']) ? $data['start_date'] : date('Y-m-d H:i:s'),
            !empty($data['end_date']) ? $data['end_date'] : null,
            $data['status'] ?? 'active',
            $id
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE coupons SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int $id): bool {
        $stmt = $this->db->prepare("UPDATE coupons SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function getTrash(): array {
        $stmt = $this->db->query("SELECT * FROM coupons WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        return $stmt->fetchAll();
    }

    public function countTrash(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM coupons WHERE deleted_at IS NOT NULL")->fetchColumn();
    }

    public function forceDelete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM coupons WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
