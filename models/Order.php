<?php
require_once __DIR__ . '/BaseModel.php';
require_once __DIR__ . '/Coupon.php';

class Order extends BaseModel {
    /** Đã kiểm tra cấu trúc bảng orders trong request hiện tại hay chưa */
    private static bool $schemaChecked = false;

    public function __construct() {
        parent::__construct();
        $this->ensureReturnSchema();
    }

    /**
     * [SỬA LỖI] Tự động nâng cấp bảng orders cho tính năng Trả hàng (chạy 1 lần/request, an toàn khi chạy lặp lại):
     *  - Thêm trạng thái 'return_requested' (khách gửi yêu cầu, chờ admin duyệt) và 'returned' (đã duyệt trả hàng)
     *  - Thêm cột completed_at (ngày giao hàng thành công) để tính đúng hạn đổi trả "kể từ khi nhận hàng"
     */
    private function ensureReturnSchema(): void {
        if (self::$schemaChecked) {
            return;
        }
        self::$schemaChecked = true;
        try {
            $colType = (string)$this->db->query("SELECT COLUMN_TYPE FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'order_status'")->fetchColumn();
            if ($colType !== '' && (strpos($colType, "'return_requested'") === false || strpos($colType, "'returned'") === false)) {
                $this->db->exec("ALTER TABLE orders MODIFY order_status ENUM('pending','confirmed','shipping','completed','return_requested','returned','cancelled') DEFAULT 'pending' COMMENT 'Tiến trình đơn hàng'");
            }
            $hasCompletedAt = (int)$this->db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'completed_at'")->fetchColumn();
            if ($hasCompletedAt === 0) {
                $this->db->exec("ALTER TABLE orders ADD COLUMN completed_at DATETIME DEFAULT NULL COMMENT 'Thời điểm giao hàng thành công' AFTER bank_transaction_id");
            }
            $hasRestoredCol = (int)$this->db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'is_stock_restored'")->fetchColumn();
            if ($hasRestoredCol === 0) {
                $this->db->exec("ALTER TABLE orders ADD COLUMN is_stock_restored TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Đã hoàn kho'");
            }
            $hasCouponReleasedCol = (int)$this->db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'is_coupon_released'")->fetchColumn();
            if ($hasCouponReleasedCol === 0) {
                $this->db->exec("ALTER TABLE orders ADD COLUMN is_coupon_released TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Đã hoàn mã giảm giá'");
            }
            $hasUpdatedAtCol = (int)$this->db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'updated_at'")->fetchColumn();
            if ($hasUpdatedAtCol === 0) {
                $this->db->exec("ALTER TABLE orders ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
            }
            $hasShippedAtCol = (int)$this->db->query("SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'orders' AND COLUMN_NAME = 'shipped_at'")->fetchColumn();
            if ($hasShippedAtCol === 0) {
                $this->db->exec("ALTER TABLE orders ADD COLUMN shipped_at DATETIME DEFAULT NULL COMMENT 'Thời điểm bắt đầu giao hàng'");
            }

            // Tự động tạo bảng lưu trữ Khách đặt giữ chỗ (CSKH liên hệ sau - chống ghim hàng)
            $this->db->exec("
                CREATE TABLE IF NOT EXISTS guest_reservations (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    product_id INT NOT NULL,
                    product_name VARCHAR(255) NOT NULL,
                    color VARCHAR(100) DEFAULT '',
                    quantity INT DEFAULT 1,
                    price DECIMAL(15,2) NOT NULL DEFAULT 0,
                    customer_name VARCHAR(150) NOT NULL,
                    customer_phone VARCHAR(20) NOT NULL,
                    customer_address TEXT NOT NULL,
                    notes TEXT NULL,
                    ip_address VARCHAR(45) NOT NULL,
                    status ENUM('pending', 'contacted', 'completed', 'cancelled') DEFAULT 'pending',
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
            ");

            // Nghiệp vụ bảo vệ: Tự động hoàn thành các đơn hàng đang giao (shipping) quá 7 ngày nếu khách hàng chưa bấm xác nhận
            $this->db->exec("UPDATE orders SET order_status = 'completed', completed_at = NOW(), payment_status = 'paid' WHERE order_status = 'shipping' AND updated_at <= NOW() - INTERVAL 7 DAY AND deleted_at IS NULL");
        } catch (Exception $e) {
            error_log('[Order] Không thể tự nâng cấp bảng orders: ' . $e->getMessage() . ' — Hãy chạy sql/update_return_feature.sql');
        }
    }

    public function generateOrderCode(): string {
        return 'TS' . date('ymd') . strtoupper(bin2hex(random_bytes(4)));
    }

    public function createOrder(array $orderData, array $cartItems): ?string {
        try {
            $this->db->beginTransaction();

            // 1. Kiểm tra tồn tại, trạng thái xóa mềm (deleted_at), và khóa hàng (FOR UPDATE)
            $prodLockStmt = $this->db->prepare("SELECT id, name, price, quantity, colors, deleted_at FROM products WHERE id = ? FOR UPDATE");
            $calculatedTotal = 0;
            $verifiedItems = [];

            foreach ($cartItems as $item) {
                $prodLockStmt->execute([$item['id']]);
                $prodRow = $prodLockStmt->fetch();
                if (!$prodRow || $prodRow['deleted_at'] !== null) {
                    throw new Exception("Sản phẩm '" . ($item['name'] ?? 'Sản phẩm') . "' đã ngừng kinh doanh hoặc không còn tồn tại trên hệ thống!");
                }
                $availableStock = (int)$prodRow['quantity'];
                $requestedQty = (int)$item['quantity'];
                if ($requestedQty <= 0) {
                    throw new Exception("Số lượng đặt của sản phẩm '" . $prodRow['name'] . "' không hợp lệ!");
                }
                if ($availableStock < $requestedQty) {
                    throw new Exception("Sản phẩm '" . $prodRow['name'] . "' không đủ tồn kho (kho còn {$availableStock} chiếc, bạn đặt {$requestedQty} chiếc).");
                }

                // Nghiệp vụ bảo vệ: Giới hạn tối đa 2 chiếc trong 1 lần đặt cho sản phẩm giá trị cao (từ 15 triệu trở lên) để chống spam/đầu cơ
                if ((float)$prodRow['price'] >= 15000000 && $requestedQty > 2) {
                    throw new Exception("Sản phẩm giá trị cao '" . $prodRow['name'] . "' được giới hạn tối đa 2 chiếc trong cùng 1 lần đặt để chống đầu cơ/spam đơn hàng!");
                }

                // Luôn cập nhật giá mới nhất từ DB để tránh mua theo giá cũ bị cache trong session
                $currentPrice = (float)$prodRow['price'];
                $itemSubtotal = $currentPrice * $requestedQty;
                $calculatedTotal += $itemSubtotal;

                $verifiedItems[] = [
                    'id' => (int)$prodRow['id'],
                    'name' => $prodRow['name'],
                    'color' => $item['color'] ?? '',
                    'image' => $item['image'] ?? '',
                    'price' => $currentPrice,
                    'quantity' => $requestedQty,
                    'total' => $itemSubtotal,
                    'db_row' => $prodRow
                ];
            }

            // 2. Trừ lượt mã khuyến mại nguyên tử (Atomic Coupon Decrement) trong cùng transaction
            $couponCode = !empty($orderData['coupon_code']) ? strtoupper(trim($orderData['coupon_code'])) : null;
            $discountAmount = (float)($orderData['discount_amount'] ?? 0);
            $couponData = null;
            $userId = !empty($orderData['user_id']) ? (int)$orderData['user_id'] : null;

            if ($couponCode) {
                // Kiểm tra ràng buộc: Mỗi tài khoản chỉ áp dụng mã 1 lần (sổ ghi tên coupon_usages)
                $couponModel = new Coupon();
                $couponData = $couponModel->getByCode($couponCode);
                if (!$couponData) {
                    throw new Exception("Mã khuyến mại '" . $couponCode . "' không tồn tại hoặc đã bị xóa!");
                }

                if ($userId && $couponModel->hasUserUsedCoupon($couponCode, $userId)) {
                    throw new Exception("Mã khuyến mại '" . $couponCode . "' chỉ áp dụng mỗi tài khoản 1 lần thanh toán. Bạn đã sử dụng mã này trong đơn hàng trước!");
                }

                $couponStmt = $this->db->prepare("
                    UPDATE coupons 
                    SET quantity = quantity - 1, used_count = used_count + 1 
                    WHERE code = ? 
                      AND quantity > 0 
                      AND status = 'active' 
                      AND (start_date IS NULL OR start_date <= NOW()) 
                      AND (end_date IS NULL OR end_date >= NOW()) 
                      AND deleted_at IS NULL
                ");
                $couponStmt->execute([$couponCode]);
                if ($couponStmt->rowCount() === 0) {
                    throw new Exception("Mã khuyến mại '" . $couponCode . "' đã hết lượt sử dụng hoặc hết hạn!");
                }
            }

            // Tính toán lại tổng tiền cuối cùng dựa trên giá thực tế
            $shippingFee = (float)($orderData['shipping_fee'] ?? 0);
            $finalAmount = max(0, $calculatedTotal - $discountAmount + $shippingFee);

            // Kiểm tra hạn mức giá trị đơn hàng chống tràn số hoặc spam giá trị rác
            if ($calculatedTotal > 2000000000 || $finalAmount > 2000000000) {
                throw new Exception("Giá trị đơn hàng vượt quá hạn mức thanh toán cho phép của hệ thống!");
            }

            // Kiểm tra phương thức thanh toán hợp lệ (chống inject giá trị rác gây lỗi SQL)
            $allowedPaymentMethods = ['cod', 'sepay'];
            $paymentMethod = strtolower(trim((string)($orderData['payment_method'] ?? 'cod')));
            if (!in_array($paymentMethod, $allowedPaymentMethods, true)) {
                throw new Exception("Phương thức thanh toán '" . htmlspecialchars($paymentMethod) . "' không hợp lệ!");
            }

            // Làm sạch ghi chú của khách: loại bỏ các thẻ hệ thống đóng mở ngoặc vuông
            $customerNote = !empty($orderData['notes']) ? trim((string)$orderData['notes']) : null;
            if ($customerNote !== null) {
                $customerNote = preg_replace('/\[.*?\]/u', '', $customerNote);
                $customerNote = trim((string)$customerNote) ?: null;
            }

            $orderCode = $this->generateOrderCode();
            $sql = "INSERT INTO orders (
                        order_code, user_id, customer_name, customer_phone, customer_email,
                        customer_address, notes, total_amount, discount_amount, coupon_code, shipping_fee,
                        final_amount, payment_method, payment_status, order_status, bank_transaction_id
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $orderCode,
                $orderData['user_id'] ?? null,
                $orderData['customer_name'],
                $orderData['customer_phone'],
                $orderData['customer_email'] ?? null,
                $orderData['customer_address'],
                $customerNote,
                $calculatedTotal,
                $discountAmount,
                $couponCode,
                $shippingFee,
                $finalAmount,
                $paymentMethod,
                $orderData['payment_status'] ?? 'pending',
                'pending',
                $orderData['bank_transaction_id'] ?? null
            ]);

            $orderId = (int)$this->db->lastInsertId();

            // Sổ ghi tên coupon_usages: Ghi tên vào sổ trong cùng transaction.
            // UNIQUE KEY (coupon_id, user_id) sẽ ném lỗi PDOException nếu dùng lần 2 -> rollback ngay lập tức!
            if ($couponCode && !empty($couponData['id']) && !empty($userId)) {
                $usageStmt = $this->db->prepare("
                    INSERT INTO coupon_usages (coupon_id, user_id, order_id, used_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $usageStmt->execute([(int)$couponData['id'], $userId, $orderId]);
            }

            // Thêm các món trong giỏ hàng vào bảng order_items & Trừ tồn kho nguyên tử (Atomic Update)
            $itemSql = "INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, total)
                        VALUES (?, ?, ?, ?, ?, ?, ?)";
            $itemStmt = $this->db->prepare($itemSql);
            // Trừ kho nguyên tử: kiểm tra và trừ trong MỘT câu lệnh (Atomic decrement có điều kiện)
            $upStmt = $this->db->prepare("UPDATE products SET quantity = quantity - ?, colors = ? WHERE id = ? AND quantity >= ?");

            foreach ($verifiedItems as $item) {
                $displayName = $item['name'] . (!empty($item['color']) ? ' (' . $item['color'] . ')' : '');
                $itemStmt->execute([
                    $orderId,
                    $item['id'],
                    $displayName,
                    $item['image'],
                    $item['price'],
                    $item['quantity'],
                    $item['total']
                ]);

                // Trừ số lượng tồn kho của sản phẩm và màu sắc đã mua
                $prodRow = $item['db_row'];
                $newColorsJson = $prodRow['colors'];

                if (!empty($prodRow['colors']) && !empty($item['color'])) {
                    $pColors = json_decode($prodRow['colors'], true);
                    if (is_array($pColors)) {
                        $matched = false;
                        foreach ($pColors as &$pc) {
                            if (isset($pc['name']) && mb_strtolower(trim($pc['name'])) === mb_strtolower(trim($item['color']))) {
                                $curColorQty = isset($pc['quantity']) ? (int)$pc['quantity'] : 50;
                                $pc['quantity'] = max(0, $curColorQty - (int)$item['quantity']);
                                $matched = true;
                                break;
                            }
                        }
                        if ($matched) {
                            $newColorsJson = json_encode($pColors, JSON_UNESCAPED_UNICODE);
                        }
                    }
                }

                $upStmt->execute([(int)$item['quantity'], $newColorsJson, (int)$item['id'], (int)$item['quantity']]);
                if ($upStmt->rowCount() === 0) {
                    throw new Exception("Sản phẩm '" . $item['name'] . "' không đủ hàng trong kho (tranh chấp đặt hàng)!");
                }
            }

            $this->db->commit();
            return $orderCode;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            error_log("Order Creation Error: " . $e->getMessage());
            throw $e;
        }
    }

    public function getByCode(string $code): ?array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE order_code = ? LIMIT 1");
        $stmt->execute([$code]);
        $order = $stmt->fetch();
        if ($order) {
            $itemsStmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$order['id']]);
            $order['items'] = $itemsStmt->fetchAll();
        }
        return $order ?: null;
    }

    /**
     * Gom toàn bộ chi tiết đơn hàng (order_items) trong ĐÚNG 1 CÂU SQL duy nhất thay vì N câu (Xóa triệt để N+1 Query)
     */
    private function attachItems(array &$orders): void {
        if (empty($orders)) {
            return;
        }
        $orderIds = array_filter(array_column($orders, 'id'));
        if (empty($orderIds)) {
            return;
        }
        $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id IN ($placeholders)");
        $stmt->execute($orderIds);
        $itemsGrouped = [];
        foreach ($stmt->fetchAll() as $item) {
            $itemsGrouped[(int)$item['order_id']][] = $item;
        }
        foreach ($orders as &$ord) {
            $ord['items'] = $itemsGrouped[(int)$ord['id']] ?? [];
        }
    }

    public function getByPhone(string $phone): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE customer_phone = ? AND is_hidden_by_user = 0 AND deleted_at IS NULL ORDER BY id DESC");
        $stmt->execute([$phone]);
        $orders = $stmt->fetchAll();
        $this->attachItems($orders);
        return $orders;
    }

    public function getByUserId(int $userId): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE user_id = ? AND is_hidden_by_user = 0 AND deleted_at IS NULL ORDER BY id DESC");
        $stmt->execute([$userId]);
        $orders = $stmt->fetchAll();
        $this->attachItems($orders);
        return $orders;
    }

    public function getAll(int $limit = 100, int $offset = 0, string $statusFilter = 'all', array $filters = []): array {
        $where = "WHERE deleted_at IS NULL";
        $params = [];
        if ($statusFilter === 'returned') {
            // Tab "Đã trả hàng": hiển thị cả các yêu cầu trả hàng đang chờ duyệt
            $where .= " AND order_status IN ('returned', 'return_requested')";
        } elseif ($statusFilter !== 'all' && in_array($statusFilter, ['pending', 'confirmed', 'shipping', 'completed', 'return_requested', 'cancelled'])) {
            $where .= " AND order_status = ?";
            $params[] = $statusFilter;
        }

        // 1. Lọc theo từ khóa (Mã đơn, Tên khách hàng, SĐT, Email)
        if (!empty($filters['keyword'])) {
            $kw = '%' . trim((string)$filters['keyword']) . '%';
            $where .= " AND (order_code LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ? OR customer_email LIKE ?)";
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
            $params[] = $kw;
        }

        // 2. Lọc theo phương thức thanh toán
        if (!empty($filters['payment_method']) && in_array($filters['payment_method'], ['cod', 'sepay'], true)) {
            $where .= " AND payment_method = ?";
            $params[] = $filters['payment_method'];
        }

        // 3. Lọc theo trạng thái thanh toán
        if (!empty($filters['payment_status']) && in_array($filters['payment_status'], ['pending', 'paid', 'refunded'], true)) {
            $where .= " AND payment_status = ?";
            $params[] = $filters['payment_status'];
        }

        // 4. Lọc theo ngày đặt hàng: Từ ngày
        if (!empty($filters['date_from'])) {
            $where .= " AND created_at >= ?";
            $params[] = $filters['date_from'] . ' 00:00:00';
        }

        // 5. Lọc theo ngày đặt hàng: Đến ngày
        if (!empty($filters['date_to'])) {
            $where .= " AND created_at <= ?";
            $params[] = $filters['date_to'] . ' 23:59:59';
        }

        // 6. Sắp xếp đơn hàng
        $orderBy = "ORDER BY id DESC";
        $sort = $filters['sort'] ?? 'newest';
        if ($sort === 'oldest') {
            $orderBy = "ORDER BY id ASC";
        } elseif ($sort === 'amount_desc') {
            $orderBy = "ORDER BY final_amount DESC, id DESC";
        } elseif ($sort === 'amount_asc') {
            $orderBy = "ORDER BY final_amount ASC, id DESC";
        }

        $stmt = $this->db->prepare("SELECT * FROM orders {$where} {$orderBy} LIMIT ? OFFSET ?");
        $paramIdx = 1;
        foreach ($params as $p) {
            $stmt->bindValue($paramIdx++, $p, PDO::PARAM_STR);
        }
        $stmt->bindValue($paramIdx++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($paramIdx++, $offset, PDO::PARAM_INT);
        $stmt->execute();
        $orders = $stmt->fetchAll();

        $this->attachItems($orders);

        return $orders;
    }

    public function softDelete(int $id): bool {
        $stmt = $this->db->prepare("UPDATE orders SET deleted_at = NOW() WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function restore(int $id): bool {
        $stmt = $this->db->prepare("UPDATE orders SET deleted_at = NULL WHERE id = ?");
        return $stmt->execute([$id]);
    }

    public function forceDelete(int $id): bool {
        $this->db->beginTransaction();
        try {
            $stmtItems = $this->db->prepare("DELETE FROM order_items WHERE order_id = ?");
            $stmtItems->execute([$id]);

            $stmtPayments = $this->db->prepare("DELETE FROM payments WHERE order_id = ?");
            $stmtPayments->execute([$id]);

            $stmtOrder = $this->db->prepare("DELETE FROM orders WHERE id = ?");
            $stmtOrder->execute([$id]);

            $this->db->commit();
            return true;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getTrash(): array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE deleted_at IS NOT NULL ORDER BY deleted_at DESC");
        $stmt->execute();
        $orders = $stmt->fetchAll();
        foreach ($orders as &$ord) {
            $itemsStmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$ord['id']]);
            $ord['items'] = $itemsStmt->fetchAll();
        }
        return $orders;
    }

    public function countTrash(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NOT NULL")->fetchColumn();
    }

    public function updatePaymentStatus(string $orderCode, string $paymentStatus, ?string $transactionId = null): bool {
        $sql = "UPDATE orders SET payment_status = ?";
        $params = [$paymentStatus];

        if ($transactionId !== null) {
            $sql .= ", bank_transaction_id = ?";
            $params[] = $transactionId;
        }

        if ($paymentStatus === 'paid') {
            // Chỉ xác nhận tự động nếu đơn đang chờ duyệt, TUYỆT ĐỐI không hồi sinh đơn đã hủy
            $sql .= ", order_status = CASE WHEN order_status = 'pending' THEN 'confirmed' ELSE order_status END";
        }

        $sql .= " WHERE order_code = ?";
        $params[] = $orderCode;

        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM orders WHERE id = ? LIMIT 1");
        $stmt->execute([$id]);
        $order = $stmt->fetch();
        if ($order) {
            $itemsStmt = $this->db->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$order['id']]);
            $order['items'] = $itemsStmt->fetchAll();
        }
        return $order ?: null;
    }

    /**
     * Trích xuất màu sắc an toàn từ tên sản phẩm dựa theo danh sách màu của chính sản phẩm
     */
    private function resolveColorFromProduct(string $productName, ?array $prodRow): string {
        if (!empty($prodRow['colors'])) {
            $pColors = json_decode($prodRow['colors'], true);
            if (is_array($pColors)) {
                foreach ($pColors as $pc) {
                    if (!empty($pc['name'])) {
                        $cName = trim($pc['name']);
                        if (stripos($productName, '(' . $cName . ')') !== false || 
                            stripos($productName, '[' . $cName . ']') !== false) {
                            return $cName;
                        }
                    }
                }
            }
        }
        // Fallback kiểm tra regex cuối chuỗi nhưng loại trừ dung lượng bộ nhớ
        if (preg_match('/[\(\[](.*?)[\)\]]\s*$/u', $productName, $m)) {
            $candidate = trim($m[1]);
            if (!preg_match('/^(?:\d+GB|\d+TB|VN\/A|LL\/A|Chính hãng)$/i', $candidate)) {
                return $candidate;
            }
        }
        return '';
    }

    /**
     * Hoàn lại số lượng tồn kho của sản phẩm khi đơn hàng bị hủy hoặc hoàn trả
     */
    public function restoreStock(int $orderId): bool {
        try {
            $orderStmt = $this->db->prepare("SELECT id, is_stock_restored, notes FROM orders WHERE id = ?");
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();
            if (!$order) {
                return false;
            }

            // Chống hoàn kho trùng lặp (Idempotent): kiểm tra cờ is_stock_restored trong DB
            if ((int)($order['is_stock_restored'] ?? 0) === 1) {
                error_log("[Order] Order #{$orderId} already restored stock, skipping.");
                return true;
            }

            error_log("[Order] Restoring stock for Order #{$orderId}");

            $itemsStmt = $this->db->prepare("SELECT product_id, product_name, quantity FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll();

            $prodStmt = $this->db->prepare("SELECT id, quantity, colors FROM products WHERE id = ?");
            // Hoàn kho nguyên tử (Atomic increment)
            $upStmt = $this->db->prepare("UPDATE products SET quantity = quantity + ?, colors = ? WHERE id = ?");

            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $qty = (int)$item['quantity'];
                if ($qty <= 0) continue;

                $prodStmt->execute([$productId]);
                $prodRow = $prodStmt->fetch();
                if ($prodRow) {
                    $color = $this->resolveColorFromProduct($item['product_name'], $prodRow);
                    $newColorsJson = $prodRow['colors'];

                    if (!empty($prodRow['colors']) && !empty($color)) {
                        $pColors = json_decode($prodRow['colors'], true);
                        if (is_array($pColors)) {
                            foreach ($pColors as &$pc) {
                                if (isset($pc['name']) && mb_strtolower(trim($pc['name'])) === mb_strtolower(trim($color))) {
                                    $curColorQty = isset($pc['quantity']) ? (int)$pc['quantity'] : 0;
                                    $pc['quantity'] = $curColorQty + $qty;
                                    break;
                                }
                            }
                            $newColorsJson = json_encode($pColors, JSON_UNESCAPED_UNICODE);
                        }
                    }

                    $upStmt->execute([$qty, $newColorsJson, $productId]);
                }
            }

            // Ghi nhận dấu mốc hoàn kho vào cột cờ is_stock_restored và nhật ký notes
            $tagNote = "\n[KHO_DA_HOAN " . date('d/m/Y H:i') . "]: Đã hoàn trả lại số lượng sản phẩm vào kho.";
            $upOrderStmt = $this->db->prepare("UPDATE orders SET is_stock_restored = 1, notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ?");
            $upOrderStmt->execute([$tagNote, $orderId]);

            return true;
        } catch (Exception $e) {
            error_log("Restore Stock Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Trừ lại tồn kho khi đơn hàng từ trạng thái Hủy/Hoàn trả được kích hoạt lại
     */
    public function reduceStock(int $orderId): bool {
        try {
            $orderStmt = $this->db->prepare("SELECT id, is_stock_restored, notes FROM orders WHERE id = ?");
            $orderStmt->execute([$orderId]);
            $order = $orderStmt->fetch();
            if (!$order) {
                return false;
            }

            // Chỉ trừ lại tồn kho nếu đơn này trước đó ĐÃ TỪNG HOÀN KHO (is_stock_restored = 1)
            if ((int)($order['is_stock_restored'] ?? 0) === 0) {
                return true;
            }

            $itemsStmt = $this->db->prepare("SELECT product_id, product_name, quantity FROM order_items WHERE order_id = ?");
            $itemsStmt->execute([$orderId]);
            $items = $itemsStmt->fetchAll();

            $prodStmt = $this->db->prepare("SELECT id, quantity, colors FROM products WHERE id = ?");
            $downStmt = $this->db->prepare("UPDATE products SET quantity = GREATEST(0, quantity - ?), colors = ? WHERE id = ?");

            foreach ($items as $item) {
                $productId = (int)$item['product_id'];
                $qty = (int)$item['quantity'];
                if ($qty <= 0) continue;

                $prodStmt->execute([$productId]);
                $prodRow = $prodStmt->fetch();
                if ($prodRow) {
                    $color = $this->resolveColorFromProduct($item['product_name'], $prodRow);
                    $newColorsJson = $prodRow['colors'];

                    if (!empty($prodRow['colors']) && !empty($color)) {
                        $pColors = json_decode($prodRow['colors'], true);
                        if (is_array($pColors)) {
                            foreach ($pColors as &$pc) {
                                if (isset($pc['name']) && mb_strtolower(trim($pc['name'])) === mb_strtolower(trim($color))) {
                                    $curColorQty = isset($pc['quantity']) ? (int)$pc['quantity'] : 0;
                                    $pc['quantity'] = max(0, $curColorQty - $qty);
                                    break;
                                }
                            }
                            $newColorsJson = json_encode($pColors, JSON_UNESCAPED_UNICODE);
                        }
                    }

                    $downStmt->execute([$qty, $newColorsJson, $productId]);
                }
            }

            $this->db->prepare("UPDATE orders SET is_stock_restored = 0 WHERE id = ?")->execute([$orderId]);
            return true;
        } catch (Exception $e) {
            error_log("Reduce Stock Error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Hủy đơn hàng và tự động xử lý hoàn tiền nếu khách đã thanh toán online
     */
    public function cancelOrder(int $orderId, string $reason = '', ?array $refundData = null): bool {
        $order = $this->getById($orderId);
        if (!$order) {
            return false;
        }

        // Chỉ cho phép hủy khi đơn ở trạng thái pending hoặc confirmed
        if (!in_array($order['order_status'], ['pending', 'confirmed'])) {
            return false;
        }

        $refundInfoStr = null;
        if (!empty($refundData)) {
            $refundInfoStr = "Ngân hàng: " . ($refundData['bank_name'] ?? '') .
                             " | STK: " . ($refundData['account_number'] ?? '') .
                             " | Chủ TK: " . ($refundData['account_holder'] ?? '');
            if (!empty($refundData['note'])) {
                $refundInfoStr .= " | Ghi chú: " . $refundData['note'];
            }
        }

        // Nếu đã thanh toán ('paid') -> chuyển sang 'refunded' (Đã hủy & Đang hoàn tiền)
        $newPaymentStatus = ($order['payment_status'] === 'paid') ? 'refunded' : $order['payment_status'];

        $appendNote = "\n[HỦY ĐƠN " . date('d/m/Y H:i') . "]: Lý do: " . $reason;
        if ($refundInfoStr !== null) {
            $appendNote .= " | Hoàn tiền về: " . $refundInfoStr;
        }

        $sql = "UPDATE orders SET order_status = 'cancelled', payment_status = ?, cancel_reason = ?, refund_info = ?, notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $res = $stmt->execute([$newPaymentStatus, $reason, $refundInfoStr, $appendNote, $orderId]);

        if ($res) {
            // Hoàn lại kho sản phẩm
            $this->restoreStock($orderId);

            // Hoàn lại lượt dùng mã khuyến mại: Ưu tiên cột coupon_code, fallback chuỗi chuẩn hệ thống [Áp dụng mã: CODE]
            $couponCode = !empty($order['coupon_code']) ? trim($order['coupon_code']) : null;
            if (empty($couponCode) && !empty($order['notes']) && preg_match('/\[Áp dụng mã:\s*([A-Za-z0-9_-]+)/i', $order['notes'], $m)) {
                $couponCode = trim($m[1]);
            }
            if (!empty($couponCode)) {
                require_once __DIR__ . '/Coupon.php';
                $couponModel = new Coupon();
                $couponModel->decreaseUsed($couponCode);
            }
        }

        return $res;
    }

    public function updateOrderStatus(int $id, string $status, ?string $paymentStatus = null): bool {
        $curOrder = $this->getById($id);
        if (!$curOrder) {
            return false;
        }

        // Ràng buộc bảo vệ: Đơn đã hủy không thể cập nhật trừ khi đổi sang trạng thái khác
        if ($curOrder['order_status'] === 'cancelled' && $status === 'cancelled') {
            return false;
        }

        // Tự động đồng bộ: Nếu đơn hàng hoàn thành (completed) mà chưa có trạng thái thanh toán thì cập nhật là 'paid'
        if ($status === 'completed' && ($paymentStatus === null || $paymentStatus === '')) {
            $paymentStatus = 'paid';
        }

        // [SỬA LỖI] Duyệt trả hàng ('returned'): CHỈ chuyển sang 'refunded' khi đơn THỰC SỰ đã thanh toán.
        // Đơn chưa thanh toán (VD: COD từ chối nhận khi xem hàng) thì giữ nguyên trạng thái thanh toán.
        if ($status === 'returned') {
            $effectivePayment = ($paymentStatus === null || $paymentStatus === '') ? $curOrder['payment_status'] : $paymentStatus;
            $paymentStatus = ($effectivePayment === 'paid' || $effectivePayment === 'refunded') ? 'refunded' : $effectivePayment;
        }

        // 1. Khi đơn chuyển sang HỦY ĐƠN hoặc TRẢ HÀNG / HOÀN TIỀN: Cập nhật hoàn trả sản phẩm lại vào kho
        $isCancelledOrRefunded = ($status === 'cancelled' || $status === 'returned' || $paymentStatus === 'refunded');
        if ($isCancelledOrRefunded) {
            $this->restoreStock($id);
        }

        // 2. Chuyển từ HỦY/TRẢ HÀNG ngược lại trạng thái ĐANG XỬ LÝ / HOÀN THÀNH: Trừ lại tồn kho tương ứng
        $wasCancelledOrRefunded = ($curOrder['order_status'] === 'cancelled' || $curOrder['order_status'] === 'returned' || $curOrder['payment_status'] === 'refunded');
        if (!$isCancelledOrRefunded && $wasCancelledOrRefunded) {
            $this->reduceStock($id);
        }

        $sql = "UPDATE orders SET order_status = ?";
        $params = [$status];

        if ($paymentStatus !== null && $paymentStatus !== '') {
            $sql .= ", payment_status = ?";
            $params[] = $paymentStatus;
        }

        // [SỬA LỖI] Ghi nhận ngày giao hàng thành công để tính hạn đổi trả "kể từ khi nhận hàng"
        if ($status === 'completed') {
            $sql .= ", completed_at = COALESCE(completed_at, NOW())";
        }

        // Admin TỪ CHỐI yêu cầu trả hàng (chuyển về trạng thái khác ngoài 'returned'/'cancelled'):
        // xóa lý do & STK hoàn tiền đang chờ, lưu lại lịch sử vào ghi chú
        if ($curOrder['order_status'] === 'return_requested' && !in_array($status, ['return_requested', 'returned', 'cancelled'], true)) {
            $sql .= ", cancel_reason = NULL, refund_info = NULL, notes = CONCAT(IFNULL(notes, ''), ?)";
            $params[] = "\n[TỪ CHỐI TRẢ HÀNG " . date('d/m/Y H:i') . "]: Cửa hàng không duyệt yêu cầu trả hàng (lý do khách gửi: " . ($curOrder['cancel_reason'] ?? '') . ")";
        }

        $sql .= " WHERE id = ?";
        $params[] = $id;

        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute($params);

        // Admin DUYỆT trả hàng hoặc HỦY ĐƠN: hoàn lại lượt dùng mã giảm giá (Idempotent 100%)
        if ($ok && in_array($status, ['returned', 'cancelled'], true) && !in_array($curOrder['order_status'], ['returned', 'cancelled'], true)) {
            $this->releaseCoupon($curOrder);
        }

        return $ok;
    }

    /**
     * Hoàn lại 1 lượt dùng mã giảm giá của đơn hàng (nếu có, bảo vệ an toàn Idempotent)
     */
    private function releaseCoupon(array $order): void {
        $orderId = (int)($order['id'] ?? 0);
        if ($orderId <= 0) return;

        $checkStmt = $this->db->prepare("SELECT id, is_coupon_released, coupon_code FROM orders WHERE id = ?");
        $checkStmt->execute([$orderId]);
        $row = $checkStmt->fetch();
        if (!$row || (int)($row['is_coupon_released'] ?? 0) === 1) {
            return; // Đã hoàn rồi hoặc không có đơn
        }

        $couponCode = !empty($row['coupon_code']) ? trim($row['coupon_code']) : (!empty($order['coupon_code']) ? trim($order['coupon_code']) : null);
        if (!empty($couponCode)) {
            require_once __DIR__ . '/Coupon.php';
            $couponModel = new Coupon();
            $couponModel->decreaseUsed($couponCode);

            // Hoàn lại quyền trong sổ ghi tên coupon_usages
            $userId = (int)($row['user_id'] ?? $order['user_id'] ?? 0);
            if ($userId > 0) {
                $couponData = $couponModel->getByCode($couponCode);
                if ($couponData) {
                    try {
                        $delUsageStmt = $this->db->prepare("DELETE FROM coupon_usages WHERE coupon_id = ? AND user_id = ?");
                        $delUsageStmt->execute([(int)$couponData['id'], $userId]);
                    } catch (Throwable $e) {
                        error_log('[Order] releaseCoupon remove usage: ' . $e->getMessage());
                    }
                }
            }

            $this->db->prepare("UPDATE orders SET is_coupon_released = 1 WHERE id = ?")->execute([$orderId]);
        }
    }

    public function count(): int {
        return (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE deleted_at IS NULL")->fetchColumn();
    }

    /**
     * Thống kê tổng quan cho Dashboard Admin
     * QUY TẮC DOANH THU: Chỉ tính các đơn ĐÃ GIAO THÀNH CÔNG (order_status = 'completed') VÀ ĐÃ THANH TOÁN (payment_status = 'paid').
     * Các đơn chưa hoàn thành hoặc đã trả hàng (returned) / đã hủy (cancelled) / đã hoàn tiền (refunded) TUYỆT ĐỐI không tính vào doanh thu.
     */
    public function getStats(): array {
        $revenue = (float)$this->db->query("SELECT SUM(final_amount) FROM orders WHERE order_status IN ('completed', 'return_requested') AND payment_status = 'paid'")->fetchColumn();
        $ordersCount = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status != 'cancelled' AND order_status != 'returned'")->fetchColumn();
        $pendingOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();
        $customersCount = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
        $productsCount = (int)$this->db->query("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL")->fetchColumn();

        return [
            'revenue' => $revenue,
            'total_revenue' => $revenue,
            'orders' => $ordersCount,
            'total_orders' => $ordersCount,
            'pending_orders' => $pendingOrders,
            'customers' => $customersCount,
            'total_users' => $customersCount,
            'products' => $productsCount,
            'total_products' => $productsCount
        ];
    }

    /**
     * Thống kê chi tiết chuyên sâu cho Báo cáo tài chính & Biểu đồ doanh thu
     */
    public function getDetailedStats(): array {
        // Doanh thu chỉ tính đơn GIAO THÀNH CÔNG (completed) VÀ ĐÃ THANH TOÁN (paid)
        $totalRevenue = (float)$this->db->query("SELECT SUM(final_amount) FROM orders WHERE order_status IN ('completed', 'return_requested') AND payment_status = 'paid'")->fetchColumn();
        $todayRevenue = (float)$this->db->query("SELECT SUM(final_amount) FROM orders WHERE order_status IN ('completed', 'return_requested') AND payment_status = 'paid' AND DATE(created_at) = CURDATE()")->fetchColumn();

        $statusRows = $this->db->query("SELECT order_status, COUNT(*) as count FROM orders GROUP BY order_status")->fetchAll();
        $statusCounts = [
            'pending' => 0,
            'confirmed' => 0,
            'shipping' => 0,
            'completed' => 0,
            'cancelled' => 0,
            'return_requested' => 0,
            'returned' => 0
        ];
        foreach ($statusRows as $r) {
            $statusCounts[$r['order_status']] = (int)$r['count'];
        }

        // Doanh thu theo phương thức: Đã giao thành công và thu tiền
        $payRows = $this->db->query("SELECT payment_method, COUNT(*) as count, SUM(final_amount) as total 
                                      FROM orders 
                                      WHERE order_status IN ('completed', 'return_requested') AND payment_status = 'paid' 
                                      GROUP BY payment_method")->fetchAll();
        $paymentMethods = [
            'vietqr' => 0,
            'bank_transfer' => 0,
            'cod' => 0,
            'sepay' => 0
        ];
        foreach ($payRows as $r) {
            $m = (string)$r['payment_method'];
            $paymentMethods[$m] = (int)$r['count'];
        }
        if (!empty($paymentMethods['sepay']) && empty($paymentMethods['vietqr'])) {
            $paymentMethods['vietqr'] = $paymentMethods['sepay'];
        }

        // Top sản phẩm theo doanh thu thực thu (giao thành công & đã thanh toán)
        $topProducts = $this->db->query("SELECT COALESCE(p.id, oi.product_id) as product_id, 
                                                COALESCE(p.name, oi.product_name) as product_name, 
                                                COALESCE(p.image, oi.product_image) as product_image, 
                                                COALESCE(c.name, 'Sản phẩm') as category_name, 
                                                SUM(oi.quantity) as total_sold, 
                                                SUM(oi.total) as total_revenue
                                         FROM order_items oi
                                         JOIN orders o ON oi.order_id = o.id
                                         LEFT JOIN products p ON oi.product_id = p.id
                                         LEFT JOIN categories c ON p.category_id = c.id
                                         WHERE o.order_status IN ('completed', 'return_requested') AND o.payment_status = 'paid'
                                         GROUP BY COALESCE(p.id, oi.product_id), 
                                                  COALESCE(p.name, oi.product_name), 
                                                  COALESCE(p.image, oi.product_image), 
                                                  COALESCE(c.name, 'Sản phẩm')
                                         ORDER BY total_sold DESC, total_revenue DESC 
                                         LIMIT 5")->fetchAll();

        // Doanh thu theo danh mục: Chỉ tính đơn đã giao thành công và thanh toán
        $categoryRevenue = $this->db->query("SELECT c.type, c.name, 
                                                    SUM(oi.total) as revenue, 
                                                    SUM(oi.quantity) as sold_qty
                                             FROM order_items oi
                                             JOIN orders o ON oi.order_id = o.id
                                             JOIN products p ON oi.product_id = p.id
                                             JOIN categories c ON p.category_id = c.id
                                             WHERE o.order_status IN ('completed', 'return_requested') AND o.payment_status = 'paid'
                                             GROUP BY c.id, c.type, c.name
                                             ORDER BY revenue DESC")->fetchAll();

        $totalOrders = (int)$this->db->query("SELECT COUNT(*) FROM orders WHERE order_status != 'cancelled' AND order_status != 'returned'")->fetchColumn();
        $totalUsers = (int)$this->db->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn();
        $totalProducts = (int)$this->db->query("SELECT COUNT(*) FROM products WHERE deleted_at IS NULL")->fetchColumn();

        return [
            'total_revenue' => $totalRevenue,
            'today_revenue' => $todayRevenue,
            'total_orders' => $totalOrders,
            'total_users' => $totalUsers,
            'total_products' => $totalProducts,
            'status_counts' => $statusCounts,
            'payment_methods' => $paymentMethods,
            'top_products' => $topProducts,
            'category_revenue' => $categoryRevenue
        ];
    }

    /**
     * Lấy danh sách sản phẩm trong đơn hàng kèm thông tin phân loại danh mục (phone, tai nghe bluetooth, phụ kiện khác)
     */
    public function getOrderItemsWithCategory(int $orderId): array {
        $stmt = $this->db->prepare("
            SELECT oi.*, p.category_id, c.name as category_name, c.slug as category_slug, c.type as category_type
            FROM order_items oi
            LEFT JOIN products p ON oi.product_id = p.id
            LEFT JOIN categories c ON p.category_id = c.id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Kiểm tra chính sách trả hàng cho đơn hàng:
     * - Điện thoại (type = phone): Đã thanh toán & nhận hàng -> Trả trong vòng 6 tháng (180 ngày).
     * - Tai nghe Bluetooth (slug = tai-nghe-bluetooth): Đã thanh toán & nhận hàng -> Trả trong vòng 7 ngày.
     * - Các phụ kiện khác: Trả khi xem hàng (khi đang giao). NẾU ĐÃ XÁC NHẬN LẤY HÀNG (completed) THÌ KHÔNG THỂ TRẢ HÀNG.
     *
     * @param array $order Dữ liệu đơn hàng
     * @return array{can_return: bool, badge_text: string, policy_type: string, message: string, remaining_days: int|null, reason_blocked: string|null}
     */
    public function checkReturnEligibility(array $order): array {
        // Đơn đã hủy hoặc đã trả hàng
        if ($order['order_status'] === 'cancelled' || $order['order_status'] === 'returned' || $order['payment_status'] === 'refunded') {
            return [
                'can_return' => false,
                'badge_text' => ($order['order_status'] === 'returned' || $order['payment_status'] === 'refunded') ? 'Đã hoàn tất trả hàng' : 'Đơn đã hủy',
                'policy_type' => 'already_processed',
                'message' => 'Đơn hàng này đã được hủy hoặc đã hoàn tất quy trình đổi trả / hoàn tiền.',
                'remaining_days' => null,
                'reason_blocked' => 'Đơn hàng không còn ở trạng thái hợp lệ để yêu cầu trả hàng.'
            ];
        }

        // [SỬA LỖI] Đơn đã gửi yêu cầu trả hàng, đang chờ cửa hàng duyệt -> không gửi trùng
        if ($order['order_status'] === 'return_requested') {
            return [
                'can_return' => false,
                'badge_text' => 'Đang chờ duyệt trả hàng',
                'policy_type' => 'return_pending',
                'message' => 'Yêu cầu trả hàng của quý khách đã được ghi nhận và đang chờ cửa hàng duyệt.',
                'remaining_days' => null,
                'reason_blocked' => 'Đơn hàng này đã có yêu cầu trả hàng đang chờ duyệt.'
            ];
        }

        // [SỬA LỖI] Đơn chưa giao (chờ xác nhận / đã xác nhận): dùng chức năng HỦY ĐƠN, không phải trả hàng
        if ($order['order_status'] === 'pending' || $order['order_status'] === 'confirmed') {
            return [
                'can_return' => false,
                'badge_text' => 'Chưa giao hàng (có thể Hủy đơn)',
                'policy_type' => 'use_cancel',
                'message' => 'Đơn hàng chưa được giao. Quý khách vui lòng sử dụng chức năng Hủy đơn.',
                'remaining_days' => null,
                'reason_blocked' => 'Đơn hàng chưa được giao, vui lòng sử dụng chức năng Hủy đơn.'
            ];
        }

        // Tình huống 1: Đơn đang giao hàng (shipping)
        // Áp dụng cho: Tất cả sản phẩm và đặc biệt là "các phụ kiện khác có thể trả hàng khi xem hàng"
        if ($order['order_status'] === 'shipping') {
            return [
                'can_return' => true,
                'badge_text' => 'Được trả hàng khi xem hàng',
                'policy_type' => 'inspect_goods',
                'message' => 'Quý khách được quyền kiểm tra sản phẩm khi nhận hàng. Nếu không ưng ý, có thể từ chối nhận và yêu cầu hoàn tiền.',
                'remaining_days' => null,
                'reason_blocked' => null
            ];
        }

        // Tình huống 2: Đơn đã nhận hàng (order_status = 'completed')
        if ($order['order_status'] === 'completed') {
            // Lấy danh sách sản phẩm và phân loại
            $items = $this->getOrderItemsWithCategory((int)$order['id']);
            // [SỬA LỖI] Tính hạn đổi trả KỂ TỪ KHI NHẬN HÀNG (completed_at); đơn cũ chưa có thì dùng ngày đặt
            $receivedAt = !empty($order['completed_at']) ? $order['completed_at'] : ($order['created_at'] ?? null);
            $orderTime = !empty($receivedAt) ? strtotime($receivedAt) : time();
            $daysPassed = max(0, (int)floor((time() - $orderTime) / 86400));

            $hasPhone = false;
            $hasBluetooth = false;
            $hasOtherAccessory = false;

            foreach ($items as $it) {
                $type = strtolower((string)($it['category_type'] ?? ''));
                $slug = strtolower((string)($it['category_slug'] ?? ''));

                if ($type === 'phone' || $slug === 'dien-thoai') {
                    $hasPhone = true;
                } elseif ($slug === 'tai-nghe-bluetooth') {
                    $hasBluetooth = true;
                } else {
                    $hasOtherAccessory = true;
                }
            }

            // A. Đơn có chứa Điện thoại: Áp dụng thời hạn 6 tháng (180 ngày)
            if ($hasPhone) {
                $maxDays = 180;
                $remaining = max(0, $maxDays - $daysPassed);
                if ($daysPassed <= $maxDays) {
                    return [
                        'can_return' => true,
                        'badge_text' => "Đổi trả 6 tháng (Còn {$remaining} ngày)",
                        'policy_type' => 'phone_6_months',
                        'message' => "Sản phẩm Điện thoại được áp dụng chính sách đổi trả & hoàn tiền trong vòng 6 tháng kể từ khi nhận hàng. Quý khách còn {$remaining} ngày.",
                        'remaining_days' => $remaining,
                        'reason_blocked' => null
                    ];
                } else {
                    return [
                        'can_return' => false,
                        'badge_text' => 'Hết hạn trả hàng (Quá 6 tháng)',
                        'policy_type' => 'phone_expired',
                        'message' => 'Đã quá thời hạn 6 tháng đổi trả đối với sản phẩm Điện thoại.',
                        'remaining_days' => 0,
                        'reason_blocked' => 'Đã quá thời hạn 6 tháng kể từ khi nhận hàng.'
                    ];
                }
            }

            // B. Đơn có chứa Tai nghe Bluetooth: Áp dụng thời hạn 7 ngày
            if ($hasBluetooth) {
                $maxDays = 7;
                $remaining = max(0, $maxDays - $daysPassed);
                if ($daysPassed <= $maxDays) {
                    return [
                        'can_return' => true,
                        'badge_text' => "Đổi trả 7 ngày (Còn {$remaining} ngày)",
                        'policy_type' => 'bluetooth_7_days',
                        'message' => "Sản phẩm Tai nghe Bluetooth được áp dụng chính sách đổi trả & hoàn tiền trong vòng 7 ngày. Quý khách còn {$remaining} ngày.",
                        'remaining_days' => $remaining,
                        'reason_blocked' => null
                    ];
                } else {
                    return [
                        'can_return' => false,
                        'badge_text' => 'Hết hạn trả hàng (Quá 7 ngày)',
                        'policy_type' => 'bluetooth_expired',
                        'message' => 'Đã quá thời hạn 7 ngày đổi trả đối với sản phẩm Tai nghe Bluetooth.',
                        'remaining_days' => 0,
                        'reason_blocked' => 'Đã quá thời hạn 7 ngày kể từ khi nhận hàng.'
                    ];
                }
            }

            // C. Đơn chỉ chứa các phụ kiện khác (cáp sạc, củ sạc, ốp lưng, tai nghe có dây...):
            // Quy tắc: "các phụ kiện khác có thể trả hàng khi xem hàng nếu đã xác nhận lấy hàng thì không thể trả hàng"
            return [
                'can_return' => false,
                'badge_text' => 'Không áp dụng trả sau khi đã nhận',
                'policy_type' => 'accessory_no_return',
                'message' => 'Theo chính sách, các phụ kiện khác chỉ hỗ trợ kiểm tra và trả hàng khi xem hàng. Khi đã bấm xác nhận nhận hàng sẽ không thể trả hàng.',
                'remaining_days' => null,
                'reason_blocked' => 'Các phụ kiện khác không áp dụng trả hàng sau khi đã xác nhận nhận hàng.'
            ];
        }

        // Trạng thái không xác định -> không cho trả hàng
        return [
            'can_return' => false,
            'badge_text' => '',
            'policy_type' => 'unknown',
            'message' => 'Đơn hàng không ở trạng thái hợp lệ để yêu cầu trả hàng.',
            'remaining_days' => null,
            'reason_blocked' => 'Đơn hàng không ở trạng thái hợp lệ để yêu cầu trả hàng.'
        ];
    }

    /**
     * [SỬA LỖI] Khách GỬI YÊU CẦU Trả hàng & Hoàn tiền:
     * - Chỉ chuyển đơn sang 'return_requested' (chờ cửa hàng duyệt) và lưu lý do + tài khoản nhận hoàn tiền.
     * - KHÔNG tự hoàn kho, KHÔNG tự hoàn tiền, KHÔNG trừ doanh thu khi cửa hàng chưa nhận lại & kiểm tra hàng.
     * - Admin duyệt bằng cách chuyển trạng thái đơn sang 'returned' (Đã trả hàng) -> lúc đó mới hoàn kho,
     *   chuyển 'refunded' (nếu đơn đã thanh toán) và trừ doanh thu. Từ chối: chuyển lại 'completed'/'shipping'.
     */
    public function processReturnOrder(int $orderId, string $reason, ?array $refundData = null): bool {
        $order = $this->getById($orderId);
        if (!$order) {
            return false;
        }

        $refundInfoStr = null;
        if (!empty($refundData) && (!empty($refundData['bank_name']) || !empty($refundData['account_number']))) {
            $refundInfoStr = "Ngân hàng: " . ($refundData['bank_name'] ?? '') .
                             " | STK: " . ($refundData['account_number'] ?? '') .
                             " | Chủ TK: " . ($refundData['account_holder'] ?? '');
            if (!empty($refundData['note'])) {
                $refundInfoStr .= " | Ghi chú: " . $refundData['note'];
            }
            if (!empty($refundData['evidence_image'])) {
                $refundInfoStr .= " | Ảnh minh chứng: " . $refundData['evidence_image'];
            }
        }

        $appendNote = "\n[YÊU CẦU TRẢ HÀNG " . date('d/m/Y H:i') . " - từ trạng thái: " . $order['order_status'] . "]: Lý do: " . $reason;
        if ($refundInfoStr !== null) {
            $appendNote .= " | Hoàn tiền về: " . $refundInfoStr;
        }

        $sql = "UPDATE orders SET order_status = 'return_requested', cancel_reason = ?, refund_info = ?, notes = CONCAT(IFNULL(notes, ''), ?) WHERE id = ? AND order_status IN ('shipping', 'completed')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$reason, $refundInfoStr, $appendNote, $orderId]);

        return $stmt->rowCount() > 0;
    }

    /**
     * TỰ ĐỘNG HOÀN TẤT ĐƠN HÀNG SAU X NGÀY (Mặc định 7 ngày kể từ khi giao hàng)
     */
    public function autoCompleteOrdersAfterDays(int $days = 7): int {
        try {
            $sql = "UPDATE orders 
                    SET order_status = 'completed', 
                        completed_at = IFNULL(completed_at, NOW()),
                        payment_status = IF(payment_method = 'cod' AND payment_status = 'pending', 'paid', payment_status),
                        notes = CONCAT(IFNULL(notes, ''), '\n[HỆ THỐNG]: Tự động hoàn tất đơn hàng sau {$days} ngày giao hàng.')
                    WHERE deleted_at IS NULL 
                      AND order_status = 'shipping' 
                      AND (created_at <= (NOW() - INTERVAL ? DAY) OR (completed_at IS NOT NULL AND completed_at <= (NOW() - INTERVAL ? DAY)))";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$days, $days]);
            return $stmt->rowCount();
        } catch (Exception $e) {
            return 0;
        }
    }
}
