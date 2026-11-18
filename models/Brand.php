<?php
require_once __DIR__ . '/BaseModel.php';

class Brand extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT b.*, COUNT(p.id) as product_count
            FROM brands b
            LEFT JOIN products p ON b.id = p.brand_id AND p.deleted_at IS NULL
            WHERE b.deleted_at IS NULL
            GROUP BY b.id
            ORDER BY b.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getPhoneBrands(): array {
        $stmt = $this->db->query("
            SELECT b.*, COUNT(p.id) as product_count
            FROM brands b
            LEFT JOIN products p ON b.id = p.brand_id AND p.category_id = 1 AND p.deleted_at IS NULL
            WHERE b.is_phone_brand = 1 AND b.deleted_at IS NULL
            GROUP BY b.id
            ORDER BY b.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getAccessoryBrands(): array {
        $stmt = $this->db->query("
            SELECT b.*, COUNT(p.id) as product_count
            FROM brands b
            LEFT JOIN products p ON b.id = p.brand_id AND p.category_id != 1 AND p.deleted_at IS NULL
            WHERE b.is_phone_brand = 0 AND b.deleted_at IS NULL
            GROUP BY b.id
            ORDER BY b.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM brands WHERE slug = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countProducts(int $id): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE brand_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO brands (name, slug, is_phone_brand, logo)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['is_phone_brand'] ?? 0,
            $data['logo'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE brands 
            SET name = ?, slug = ?, is_phone_brand = ?, logo = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['is_phone_brand'] ?? 0,
            $data['logo'] ?? null,
            $id
        ]);
    }

    /**
     * Xóa mềm (chuyển vào Thùng rác)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE brands SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Khôi phục từ Thùng rác
     */
    public function restore(int $id): bool {
        $stmt = $this->db->prepare("UPDATE brands SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách thương hiệu trong Thùng rác
     */
    public function getTrash(): array {
        $stmt = $this->db->query("
            SELECT b.*, COUNT(p.id) as product_count
            FROM brands b
            LEFT JOIN products p ON b.id = p.brand_id AND p.deleted_at IS NULL
            WHERE b.deleted_at IS NOT NULL
            GROUP BY b.id
            ORDER BY b.deleted_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Đếm số lượng thương hiệu trong Thùng rác
     */
    public function countTrash(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM brands WHERE deleted_at IS NOT NULL");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Xóa vĩnh viễn khỏi Database
     */
    public function forceDelete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM brands WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
