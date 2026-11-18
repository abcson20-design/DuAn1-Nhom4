<?php
require_once __DIR__ . '/BaseModel.php';

class Category extends BaseModel {
    public function __construct() {
        parent::__construct();
    }

    public function getAll(): array {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
            WHERE c.deleted_at IS NULL
            GROUP BY c.id 
            ORDER BY c.id ASC
        ");
        return $stmt->fetchAll();
    }

    public function getByType(string $type): array {
        $stmt = $this->db->prepare("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
            WHERE c.type = ? AND c.deleted_at IS NULL
            GROUP BY c.id 
            ORDER BY c.id ASC
        ");
        $stmt->execute([$type]);
        return $stmt->fetchAll();
    }

    public function getAccessories(): array {
        return $this->getByType('accessory');
    }

    public function getBySlug(string $slug): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE slug = ? AND deleted_at IS NULL LIMIT 1");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM categories WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function countProducts(int $id): int {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM products WHERE category_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return (int)$stmt->fetchColumn();
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO categories (name, slug, type, icon, description, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['type'] ?? 'accessory',
            $data['icon'] ?? 'fa-solid fa-mobile-screen',
            $data['description'] ?? null
        ]);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE categories 
            SET name = ?, slug = ?, type = ?, icon = ?, description = ?
            WHERE id = ?
        ");
        return $stmt->execute([
            $data['name'],
            $data['slug'],
            $data['type'] ?? 'accessory',
            $data['icon'] ?? 'fa-solid fa-mobile-screen',
            $data['description'] ?? null,
            $id
        ]);
    }

    /**
     * Xóa mềm (chuyển vào Thùng rác)
     */
    public function delete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Khôi phục từ Thùng rác
     */
    public function restore(int $id): bool {
        $stmt = $this->db->prepare("UPDATE categories SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách danh mục trong Thùng rác
     */
    public function getTrash(): array {
        $stmt = $this->db->query("
            SELECT c.*, COUNT(p.id) as product_count 
            FROM categories c 
            LEFT JOIN products p ON c.id = p.category_id AND p.deleted_at IS NULL
            WHERE c.deleted_at IS NOT NULL
            GROUP BY c.id 
            ORDER BY c.deleted_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Đếm số lượng trong thùng rác
     */
    public function countTrash(): int {
        $stmt = $this->db->query("SELECT COUNT(*) FROM categories WHERE deleted_at IS NOT NULL");
        return (int)$stmt->fetchColumn();
    }

    /**
     * Xóa vĩnh viễn khỏi Database
     */
    public function forceDelete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM categories WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
