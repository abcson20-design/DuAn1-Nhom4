<?php

declare(strict_types=1);

require_once __DIR__ . '/BaseModel.php';

class Product extends BaseModel
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getAll(int $limit = 500, int $offset = 0): array
    {
        return $this->searchAndFilter([], $limit, $offset);
    }

    public function getFeatured(int $limit = 8): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.is_featured = 1 AND p.deleted_at IS NULL
            ORDER BY p.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getHot(int $limit = 6): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.is_hot = 1 AND p.deleted_at IS NULL
            ORDER BY p.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getBySlug(string $slug): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.slug = ? AND p.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$slug]);
        $row = $stmt->fetch();
        return $row ? $this->normalizeProduct($row) : null;
    }

    public function getById(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.id = ? AND p.deleted_at IS NULL
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->normalizeProduct($row) : null;
    }

    /**
     * Lấy sản phẩm kể cả đã xóa mềm (dùng cho Admin và xem đơn hàng cũ)
     */
    public function getByIdWithTrashed(int $id): ?array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        return $row ? $this->normalizeProduct($row) : null;
    }

    private function normalizeProduct(array $row): array
    {
        if (!empty($row['slug']) && $row['slug'] === 'oppo-reno12-5g-vu-tru-ai' && !empty($row['image']) && strpos($row['image'], '1546868871-7041f2a55e12') !== false) {
            $row['image'] = 'https://images.unsplash.com/photo-1592899677977-9c10ca588bbd?w=800&auto=format&fit=crop';
        }
        return $row;
    }

    public function getRelated(int $categoryId, int $currentId, int $limit = 4): array
    {
        $stmt = $this->db->prepare("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.category_id = ? AND p.id != ? AND p.deleted_at IS NULL
            ORDER BY p.id DESC
            LIMIT ?
        ");
        $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
        $stmt->bindValue(2, $currentId, PDO::PARAM_INT);
        $stmt->bindValue(3, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function searchAndFilter(array $filters = [], int $limit = 12, int $offset = 0): array
    {
        $sql = "
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }

        if (!empty($filters['category_type'])) {
            $sql .= " AND c.type = ?";
            $params[] = $filters['category_type'];
        }

        if (!empty($filters['exclude_category_id'])) {
            $sql .= " AND p.category_id != ?";
            $params[] = $filters['exclude_category_id'];
        }

        if (!empty($filters['brand_id'])) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $filters['brand_id'];
        }

        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.compatible_device LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
            $kw = "%" . $filters['keyword'] . "%";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }

        if (!empty($filters['device'])) {
            $sql .= " AND (p.compatible_device LIKE ? OR p.compatible_device = 'Tất cả dòng máy')";
            $params[] = "%" . $filters['device'] . "%";
        }

        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        $sort = $filters['sort'] ?? 'newest';
        switch ($sort) {
            case 'price_asc':
                $sql .= " ORDER BY p.price ASC";
                break;
            case 'price_desc':
                $sql .= " ORDER BY p.price DESC";
                break;
            case 'popular':
                $sql .= " ORDER BY p.views DESC, p.id DESC";
                break;
            default:
                $sql .= " ORDER BY p.id DESC";
                break;
        }

        $sql .= " LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $key => $val) {
            $type = is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR;
            $stmt->bindValue($key + 1, $val, $type);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function countSearchAndFilter(array $filters = []): int
    {
        $sql = "
            SELECT COUNT(*) as total 
            FROM products p 
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.deleted_at IS NULL
        ";
        $params = [];

        if (!empty($filters['category_id'])) {
            $sql .= " AND p.category_id = ?";
            $params[] = $filters['category_id'];
        }
        if (!empty($filters['category_type'])) {
            $sql .= " AND c.type = ?";
            $params[] = $filters['category_type'];
        }
        if (!empty($filters['exclude_category_id'])) {
            $sql .= " AND p.category_id != ?";
            $params[] = $filters['exclude_category_id'];
        }
        if (!empty($filters['brand_id'])) {
            $sql .= " AND p.brand_id = ?";
            $params[] = $filters['brand_id'];
        }
        if (!empty($filters['keyword'])) {
            $sql .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.compatible_device LIKE ? OR b.name LIKE ? OR c.name LIKE ?)";
            $kw = "%" . $filters['keyword'] . "%";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }
        if (!empty($filters['device'])) {
            $sql .= " AND (p.compatible_device LIKE ? OR p.compatible_device = 'Tất cả dòng máy')";
            $params[] = "%" . $filters['device'] . "%";
        }
        if (!empty($filters['min_price'])) {
            $sql .= " AND p.price >= ?";
            $params[] = $filters['min_price'];
        }
        if (!empty($filters['max_price'])) {
            $sql .= " AND p.price <= ?";
            $params[] = $filters['max_price'];
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }

    public function liveSuggest(string $keyword, int $limit = 6): array
    {
        $keyword = trim($keyword);
        if ($keyword === '') return [];

        $sql = "
            SELECT p.id, p.name, p.slug, p.price, p.old_price, p.image, p.compatible_device,
                   c.name as category_name, b.name as brand_name
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.deleted_at IS NULL 
              AND (p.name LIKE ? OR p.description LIKE ? OR p.compatible_device LIKE ? OR b.name LIKE ? OR c.name LIKE ?)
            ORDER BY 
                CASE WHEN p.name LIKE ? THEN 1 ELSE 2 END,
                p.views DESC, p.id DESC
            LIMIT ?
        ";
        $kw = "%" . $keyword . "%";
        $kwStart = $keyword . "%";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $kw, PDO::PARAM_STR);
        $stmt->bindValue(2, $kw, PDO::PARAM_STR);
        $stmt->bindValue(3, $kw, PDO::PARAM_STR);
        $stmt->bindValue(4, $kw, PDO::PARAM_STR);
        $stmt->bindValue(5, $kw, PDO::PARAM_STR);
        $stmt->bindValue(6, $kwStart, PDO::PARAM_STR);
        $stmt->bindValue(7, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function incrementViews(int $id): void
    {
        $stmt = $this->db->prepare("UPDATE products SET views = views + 1 WHERE id = ?");
        $stmt->execute([$id]);
    }

    // ==============================================================
    // CRUD & XÓA MỀM (SOFT DELETE) KẾ THỪA TỪ BASE SHOP
    // ==============================================================
    public function create(array $data): int
    {
        $sql = "INSERT INTO products (category_id, brand_id, name, slug, price, old_price, image, gallery, video, colors, media_type, quantity, compatible_device, description, specs, is_featured, is_hot)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $price = max(0, (float)($data['price'] ?? 0));
        $oldPrice = !empty($data['old_price']) ? max(0, (float)$data['old_price']) : null;
        $quantity = max(0, (int)($data['quantity'] ?? 0));

        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['category_id'],
            $data['brand_id'] ?: null,
            $data['name'],
            $data['slug'],
            $price,
            $oldPrice,
            $data['image'],
            $data['gallery'] ?? null,
            $data['video'] ?? null,
            $data['colors'] ?? null,
            $data['media_type'] ?? 'slideshow',
            $quantity,
            $data['compatible_device'] ?? 'Tất cả dòng máy',
            $data['description'] ?? '',
            $data['specs'] ?? null,
            !empty($data['is_featured']) ? 1 : 0,
            !empty($data['is_hot']) ? 1 : 0
        ]);
        $newId = (int)$this->db->lastInsertId();

        // Tự động đồng bộ sang 3 bảng quan hệ: Thuộc tính (attributes), Album (galleries), Màu sắc (colors)
        $this->autoSyncRelationalTables($newId, $data);

        return $newId;
    }

    public function update(int $id, array $data): bool
    {
        $price = max(0, (float)($data['price'] ?? 0));
        $oldPrice = !empty($data['old_price']) ? max(0, (float)$data['old_price']) : null;
        $quantity = max(0, (int)($data['quantity'] ?? 0));

        $sql = "UPDATE products SET 
                category_id = ?, brand_id = ?, name = ?, slug = ?, price = ?, old_price = ?, 
                image = ?, gallery = ?, video = ?, colors = ?, media_type = ?, quantity = ?, compatible_device = ?, description = ?, specs = ?, 
                is_featured = ?, is_hot = ?
                WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([
            $data['category_id'],
            $data['brand_id'] ?: null,
            $data['name'],
            $data['slug'],
            $price,
            $oldPrice,
            $data['image'],
            $data['gallery'] ?? null,
            $data['video'] ?? null,
            $data['colors'] ?? null,
            $data['media_type'] ?? 'slideshow',
            $quantity,
            $data['compatible_device'] ?? 'Tất cả dòng máy',
            $data['description'] ?? '',
            $data['specs'] ?? null,
            !empty($data['is_featured']) ? 1 : 0,
            !empty($data['is_hot']) ? 1 : 0,
            $id
        ]);

        if ($res) {
            $this->autoSyncRelationalTables($id, $data);
        }

        return $res;
    }

    /**
     * Tự động đồng bộ từ JSON sang 3 bảng quan hệ: product_attributes, product_galleries, product_colors
     */
    private function autoSyncRelationalTables(int $productId, array $data): void
    {
        try {
            // 1. Đồng bộ Thuộc tính (product_attributes)
            if (!empty($data['specs'])) {
                $specsArr = is_array($data['specs']) ? $data['specs'] : json_decode($data['specs'], true);
                if (is_array($specsArr)) {
                    $this->syncAttributes($productId, $specsArr);
                }
            }

            // 2. Đồng bộ Album ảnh (product_galleries)
            if (!empty($data['gallery'])) {
                $galleryArr = is_array($data['gallery']) ? $data['gallery'] : json_decode($data['gallery'], true);
                if (is_array($galleryArr)) {
                    $this->syncGalleries($productId, $galleryArr);
                }
            }

            // 3. Đồng bộ Màu sắc (product_colors)
            if (!empty($data['colors'])) {
                $colorsArr = is_array($data['colors']) ? $data['colors'] : json_decode($data['colors'], true);
                if (is_array($colorsArr)) {
                    $this->syncColors($productId, $colorsArr);
                }
            }
        } catch (Throwable $e) {
            // Không làm gián đoạn nếu bảng chưa tạo
        }
    }

    /**
     * Lấy danh sách Thuộc tính từ bảng `product_attributes`
     */
    public function getAttributes(int $productId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_attributes WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([$productId]);
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Lấy danh sách Album ảnh từ bảng `product_galleries`
     */
    public function getGalleries(int $productId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_galleries WHERE product_id = ? ORDER BY sort_order ASC, id ASC");
            $stmt->execute([$productId]);
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Lấy danh sách Biến thể màu sắc từ bảng `product_colors`
     */
    public function getColors(int $productId): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM product_colors WHERE product_id = ? ORDER BY id ASC");
            $stmt->execute([$productId]);
            return $stmt->fetchAll() ?: [];
        } catch (Throwable $e) {
            return [];
        }
    }

    /**
     * Đồng bộ Thuộc tính vào bảng `product_attributes`
     */
    public function syncAttributes(int $productId, array $attributes): void
    {
        try {
            $this->db->prepare("DELETE FROM product_attributes WHERE product_id = ?")->execute([$productId]);
            $stmt = $this->db->prepare("INSERT INTO product_attributes (product_id, name, value, group_name, sort_order) VALUES (?, ?, ?, ?, ?)");
            $order = 1;
            foreach ($attributes as $key => $val) {
                if (is_array($val)) {
                    $name = $val['name'] ?? $key;
                    $value = $val['value'] ?? '';
                    $group = $val['group_name'] ?? 'Thông số kỹ thuật';
                    $stmt->execute([$productId, $name, $value, $group, $order++]);
                } else {
                    $stmt->execute([$productId, (string)$key, (string)$val, 'Thông số kỹ thuật', $order++]);
                }
            }
        } catch (Throwable $e) {}
    }

    /**
     * Đồng bộ Album ảnh vào bảng `product_galleries`
     */
    public function syncGalleries(int $productId, array $galleries): void
    {
        try {
            $this->db->prepare("DELETE FROM product_galleries WHERE product_id = ?")->execute([$productId]);
            $stmt = $this->db->prepare("INSERT INTO product_galleries (product_id, image_url, title, tag, description, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $order = 1;
            foreach ($galleries as $g) {
                $img = $g['image'] ?? ($g['image_url'] ?? '');
                if (!empty($img)) {
                    $title = $g['title'] ?? null;
                    $tag = $g['tag'] ?? 'Cấu tạo';
                    $desc = $g['desc'] ?? ($g['description'] ?? null);
                    $stmt->execute([$productId, $img, $title, $tag, $desc, $order++]);
                }
            }
        } catch (Throwable $e) {}
    }

    /**
     * Đồng bộ Biến thể màu sắc vào bảng `product_colors`
     */
    public function syncColors(int $productId, array $colors): void
    {
        try {
            $this->db->prepare("DELETE FROM product_colors WHERE product_id = ?")->execute([$productId]);
            $stmt = $this->db->prepare("INSERT INTO product_colors (product_id, color_name, color_image, quantity) VALUES (?, ?, ?, ?)");
            foreach ($colors as $c) {
                if (!empty($c['name']) || !empty($c['color_name'])) {
                    $name = $c['name'] ?? $c['color_name'];
                    $img = $c['image'] ?? ($c['color_image'] ?? null);
                    $qty = isset($c['quantity']) ? (int)$c['quantity'] : 50;
                    $stmt->execute([$productId, $name, $img, $qty]);
                }
            }
        } catch (Throwable $e) {}
    }

    /**
     * XÓA MỀM (Soft Delete): Đưa sản phẩm vào Thùng rác (bảo vệ đơn hàng cũ)
     */
    public function delete(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Khôi phục sản phẩm từ Thùng rác
     */
    public function restore(int $id): bool
    {
        $stmt = $this->db->prepare("UPDATE products SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Lấy danh sách sản phẩm trong Thùng rác
     */
    public function getTrash(): array
    {
        $stmt = $this->db->query("
            SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            WHERE p.deleted_at IS NOT NULL
            ORDER BY p.deleted_at DESC
        ");
        return $stmt->fetchAll();
    }

    /**
     * Đếm số lượng sản phẩm trong Thùng rác
     */
    public function countTrash(): int
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM products WHERE deleted_at IS NOT NULL");
        $res = $stmt->fetch();
        return (int)($res['total'] ?? 0);
    }

    /**
     * Kiểm tra sản phẩm đã từng có trong đơn hàng nào chưa
     */
    public function hasOrders(int $id): bool
    {
        $stmt = $this->db->prepare("SELECT 1 FROM order_items WHERE product_id = ? LIMIT 1");
        $stmt->execute([$id]);
        return (bool)$stmt->fetch();
    }

    /**
     * XÓA VĨNH VIỄN (Force Delete): Chỉ cho phép khi sản phẩm CHƯA TỪNG có đơn hàng
     */
    public function forceDelete(int $id): bool
    {
        if ($this->hasOrders($id)) {
            return false;
        }

        $stmt = $this->db->prepare("DELETE FROM products WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
