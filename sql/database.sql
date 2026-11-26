-- ==============================================================
-- DỰ ÁN 1: HỆ THỐNG WEBSITE BÁN ĐIỆN THOẠI & PHỤ KIỆN (NEXVORA STORE)
-- BẢN CƠ SỞ DỮ LIỆU HOÀN CHỈNH TẤT-CẢ-TRONG-MỘT (ALL-IN-ONE DATABASE)
-- CƠ SỞ DỮ LIỆU: DuAn1_db (hoặc duan1_db)
-- BẢNG MÃ: UTF8MB4 (Hỗ trợ tiếng Việt đầy đủ 100%)
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `DuAn1_db` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `DuAn1_db`;

-- Tắt kiểm tra khóa ngoại để xóa và tạo mới an toàn
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ==============================================================
-- BƯỚC 1: XÓA CÁC BẢNG CŨ ĐỂ TẠO MỚI SẠCH SẼ 100%
-- ==============================================================
DROP TABLE IF EXISTS `coupon_usages`;
DROP TABLE IF EXISTS `guest_reservations`;
DROP TABLE IF EXISTS `user_carts`;
DROP TABLE IF EXISTS `wishlists`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `payments`;
DROP TABLE IF EXISTS `warranties`;
DROP TABLE IF EXISTS `product_reviews`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `contacts`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `product_attributes`;
DROP TABLE IF EXISTS `product_galleries`;
DROP TABLE IF EXISTS `product_colors`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `brands`;
DROP TABLE IF EXISTS `users`;

-- ==============================================================
-- BƯỚC 2: TẠO CẤU TRÚC CÁC BẢNG CHUẨN HÓA
-- ==============================================================

-- 1. BẢNG: `categories` (Danh mục sản phẩm)
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL COMMENT 'Tên danh mục',
  `slug` varchar(255) NOT NULL COMMENT 'Đường dẫn tĩnh chuẩn SEO',
  `type` enum('phone','accessory') DEFAULT 'accessory' COMMENT 'Phân loại: phone hoặc accessory',
  `icon` varchar(50) DEFAULT 'fa-solid fa-mobile-screen' COMMENT 'Icon FontAwesome',
  `description` text DEFAULT NULL COMMENT 'Mô tả danh mục',
  `created_at` datetime DEFAULT current_timestamp(),
  `deleted_at` datetime DEFAULT NULL COMMENT 'Thời gian xóa mềm (NULL: hoạt động, có giá trị: trong thùng rác)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh mục sản phẩm';

-- 2. BẢNG: `brands` (Thương hiệu / Hãng sản xuất)
CREATE TABLE `brands` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT 'Tên thương hiệu',
  `slug` varchar(100) NOT NULL COMMENT 'Slug thương hiệu',
  `is_phone_brand` tinyint(1) DEFAULT 0 COMMENT '1: Hãng điện thoại, 0: Hãng phụ kiện',
  `logo` varchar(255) DEFAULT NULL COMMENT 'Ảnh logo thương hiệu',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Thời gian xóa mềm (NULL: hoạt động, có giá trị: trong thùng rác)',
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thương hiệu sản phẩm';

-- 3. BẢNG: `users` (Tài khoản người dùng & Quản trị viên)
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `fullname` varchar(150) NOT NULL COMMENT 'Họ và tên',
  `email` varchar(150) NOT NULL COMMENT 'Email đăng nhập',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `password` varchar(255) NOT NULL COMMENT 'Mật khẩu đã mã hóa bcrypt',
  `role` enum('admin','customer') DEFAULT 'customer' COMMENT 'Vai trò tài khoản',
  `address` text DEFAULT NULL COMMENT 'Địa chỉ giao hàng',
  `status` enum('active','blocked') DEFAULT 'active' COMMENT 'Trạng thái hoạt động',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng người dùng và quản trị';

-- 4. BẢNG: `products` (Sản phẩm điện thoại & Phụ kiện - HỖ TRỢ XÓA MỀM DELETED_AT)
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT 'ID danh mục',
  `brand_id` int(11) DEFAULT NULL COMMENT 'ID thương hiệu',
  `name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm',
  `slug` varchar(255) NOT NULL COMMENT 'Đường dẫn tĩnh chuẩn SEO',
  `price` decimal(12,0) NOT NULL COMMENT 'Giá bán hiện tại (VNĐ)',
  `old_price` decimal(12,0) DEFAULT NULL COMMENT 'Giá niêm yết cũ (VNĐ)',
  `image` varchar(500) NOT NULL COMMENT 'Ảnh đại diện chính',
  `gallery` text DEFAULT NULL COMMENT 'Thư viện ảnh cấu tạo JSON',
  `video` varchar(500) DEFAULT NULL COMMENT 'Đường dẫn Video YouTube trải nghiệm',
  `colors` text DEFAULT NULL COMMENT 'Cấu hình 4 màu sắc & tồn kho từng màu JSON',
  `media_type` enum('slideshow','video') DEFAULT 'slideshow' COMMENT 'Loại media ưu tiên: slideshow hoặc video',
  `quantity` int(11) DEFAULT 50 COMMENT 'Tổng số lượng tồn kho',
  `description` longtext DEFAULT NULL COMMENT 'Mô tả chi tiết sản phẩm',
  `specs` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Bảng thông số kỹ thuật chuẩn JSON',
  `compatible_device` varchar(255) DEFAULT 'Tất cả dòng máy' COMMENT 'Dòng máy tương thích',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT 'Sản phẩm nổi bật (1: Có, 0: Không)',
  `is_hot` tinyint(1) DEFAULT 0 COMMENT 'Sản phẩm Hot bán chạy',
  `rating` decimal(2,1) DEFAULT 5.0 COMMENT 'Đánh giá trung bình (1.0 - 5.0)',
  `views` int(11) DEFAULT 0 COMMENT 'Lượt xem sản phẩm',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Thời gian xóa mềm (Thùng rác). NULL: đang bán bình thường',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `fk_prod_cat` (`category_id`),
  KEY `fk_prod_brand` (`brand_id`),
  KEY `idx_deleted_at` (`deleted_at`),
  CONSTRAINT `fk_prod_brand` FOREIGN KEY (`brand_id`) REFERENCES `brands` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_prod_cat` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng sản phẩm điện thoại và phụ kiện (có xóa mềm)';

-- 5. BẢNG: `product_attributes` (Thuộc tính & Thông số kỹ thuật sản phẩm - ATTRIBUTES)
CREATE TABLE `product_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `name` varchar(100) NOT NULL COMMENT 'Tên thuộc tính (Màn hình, CPU, RAM, Bluetooth, Chống ồn ANC...)',
  `value` text NOT NULL COMMENT 'Giá trị thuộc tính',
  `group_name` varchar(50) DEFAULT 'Thông số kỹ thuật' COMMENT 'Nhóm thuộc tính: Màn hình, Hiệu năng, Âm thanh, Kết nối...',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Thứ tự ưu tiên hiển thị',
  PRIMARY KEY (`id`),
  KEY `fk_attr_prod` (`product_id`),
  CONSTRAINT `fk_attr_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thuộc tính thông số sản phẩm (Attributes)';

-- 6. BẢNG: `product_galleries` (Album ảnh chi tiết & Thư viện ảnh cấu tạo sản phẩm - ALBUMS / GALLERIES)
CREATE TABLE `product_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `image_url` varchar(500) NOT NULL COMMENT 'Đường dẫn ảnh trong album',
  `title` varchar(255) DEFAULT NULL COMMENT 'Tiêu đề góc chụp / Bộ phận cấu tạo',
  `tag` varchar(50) DEFAULT 'Ngoại quan' COMMENT 'Tag phân loại: Ngoại hình, Màn hình, Camera AI, Âm thanh...',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết ảnh / Linh kiện',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị ảnh trong album',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_gallery_prod` (`product_id`),
  CONSTRAINT `fk_gallery_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng album ảnh chi tiết sản phẩm (Albums / Galleries)';

-- 7. BẢNG: `product_colors` (Biến thể màu sắc & Số lượng tồn kho theo màu)
CREATE TABLE `product_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `color_name` varchar(100) NOT NULL COMMENT 'Tên màu sắc (VD: Titan Sa Mạc, Đen Cổ Điển...)',
  `color_image` varchar(500) DEFAULT NULL COMMENT 'Ảnh chụp đại diện riêng của màu này',
  `quantity` int(11) NOT NULL DEFAULT 50 COMMENT 'Số lượng hàng tồn kho của màu này',
  PRIMARY KEY (`id`),
  KEY `fk_color_prod` (`product_id`),
  CONSTRAINT `fk_color_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng biến thể màu sắc và tồn kho theo màu';

-- 8. BẢNG: `product_reviews` (Đánh giá & Bình luận sản phẩm)
CREATE TABLE `product_reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'ID sản phẩm được đánh giá',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID tài khoản đánh giá (nếu có)',
  `author_name` varchar(150) NOT NULL COMMENT 'Họ tên người đánh giá',
  `author_email` varchar(150) DEFAULT NULL COMMENT 'Email người đánh giá',
  `author_phone` varchar(20) DEFAULT NULL COMMENT 'SĐT người đánh giá',
  `rating` tinyint(4) NOT NULL DEFAULT 5 COMMENT 'Số sao đánh giá (1-5 sao)',
  `content` text NOT NULL COMMENT 'Nội dung nhận xét đánh giá',
  `is_purchased` tinyint(1) DEFAULT 1 COMMENT '1: Đã mua hàng tại shop, 0: Chưa mua',
  `status` enum('approved','pending','hidden') DEFAULT 'approved' COMMENT 'Trạng thái kiểm duyệt',
  `likes` int(11) DEFAULT 0 COMMENT 'Lượt thích đánh giá',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_prod_status` (`product_id`,`status`),
  CONSTRAINT `fk_rev_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đánh giá sản phẩm';

-- 9. BẢNG: `orders` (Đơn hàng mua sắm)
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_code` varchar(50) NOT NULL COMMENT 'Mã đơn hàng duy nhất',
  `user_id` int(11) DEFAULT NULL COMMENT 'ID tài khoản đặt hàng (nếu có)',
  `customer_name` varchar(150) NOT NULL COMMENT 'Tên khách hàng nhận hàng',
  `customer_phone` varchar(20) NOT NULL COMMENT 'SĐT khách hàng',
  `customer_email` varchar(150) DEFAULT NULL COMMENT 'Email khách hàng',
  `customer_address` text NOT NULL COMMENT 'Địa chỉ nhận hàng chi tiết',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú đơn hàng từ khách',
  `total_amount` decimal(12,0) NOT NULL COMMENT 'Tổng tiền hàng',
  `discount_amount` decimal(12,0) DEFAULT 0 COMMENT 'Số tiền giảm giá voucher',
  `coupon_code` varchar(50) DEFAULT NULL COMMENT 'Mã voucher áp dụng cho đơn',
  `shipping_fee` decimal(12,0) DEFAULT 0 COMMENT 'Phí vận chuyển',
  `final_amount` decimal(12,0) NOT NULL COMMENT 'Số tiền thanh toán cuối cùng',
  `payment_method` enum('cod','vietqr','bank_transfer','sepay') NOT NULL DEFAULT 'sepay' COMMENT 'Phương thức thanh toán',
  `payment_status` enum('pending','paid','failed','refunded') DEFAULT 'pending' COMMENT 'Trạng thái thanh toán',
  `order_status` enum('pending','confirmed','shipping','completed','return_requested','returned','cancelled') DEFAULT 'pending' COMMENT 'Tiến trình đơn hàng (return_requested: khách yêu cầu trả hàng chờ duyệt, returned: đã trả hàng)',
  `cancel_reason` text DEFAULT NULL COMMENT 'Lý do khách hủy đơn',
  `refund_info` text DEFAULT NULL COMMENT 'Thông tin STK hoàn tiền của khách',
  `is_hidden_by_user` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Khách hàng ẩn đơn khỏi lịch sử cá nhân (1: Đã ẩn, 0: Bình thường)',
  `bank_transaction_id` varchar(100) DEFAULT NULL COMMENT 'Mã giao dịch ngân hàng / SePay',
  `completed_at` datetime DEFAULT NULL COMMENT 'Thời điểm giao hàng thành công (tính hạn đổi trả)',
  `shipped_at` datetime DEFAULT NULL COMMENT 'Thời điểm bắt đầu giao hàng',
  `is_stock_restored` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã hoàn kho',
  `is_coupon_released` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'Đã hoàn mã giảm giá',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Thời gian xóa mềm (Thùng rác)',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `fk_order_user` (`user_id`),
  CONSTRAINT `fk_order_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đơn hàng mua sắm';

-- 10. BẢNG: `order_items` (Chi tiết các mặt hàng trong đơn)
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu orders',
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu products',
  `product_name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm tại thời điểm mua',
  `product_image` varchar(500) DEFAULT NULL COMMENT 'Ảnh sản phẩm',
  `price` decimal(12,0) NOT NULL COMMENT 'Đơn giá mua',
  `quantity` int(11) NOT NULL COMMENT 'Số lượng mua',
  `total` decimal(12,0) NOT NULL COMMENT 'Thành tiền (giá x số lượng)',
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `idx_item_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng chi tiết mặt hàng trong đơn';

-- 11. BẢNG: `payments` (Lịch sử giao dịch thanh toán trực tuyến SePay)
CREATE TABLE `payments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL COMMENT 'ID đơn hàng tham chiếu orders',
  `order_code` varchar(50) NOT NULL COMMENT 'Mã đơn hàng (ví dụ: TS261112A1B2C)',
  `gateway` varchar(30) NOT NULL DEFAULT 'sepay' COMMENT 'Cổng thanh toán: sepay',
  `txn_ref` varchar(64) NOT NULL COMMENT 'Mã hoá đơn SePay (order_invoice_number)',
  `amount` decimal(12,0) NOT NULL COMMENT 'Số tiền thanh toán (VNĐ)',
  `status` enum('pending','success','failed') NOT NULL DEFAULT 'pending' COMMENT 'Trạng thái giao dịch',
  `transaction_no` varchar(100) DEFAULT NULL COMMENT 'Mã giao dịch ngân hàng trả về',
  `bank_code` varchar(50) DEFAULT NULL COMMENT 'Hình thức (BANK_TRANSFER, NAPAS...)',
  `response_code` varchar(50) DEFAULT NULL COMMENT 'Mã phản hồi từ SePay (ORDER_PAID)',
  `raw_data` longtext DEFAULT NULL COMMENT 'Toàn bộ dữ liệu JSON Webhook SePay gửi sang',
  `paid_at` datetime DEFAULT NULL COMMENT 'Thời gian thanh toán thành công',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `uk_txn_ref` (`txn_ref`),
  KEY `idx_order_id` (`order_id`),
  KEY `idx_order_code` (`order_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Lịch sử giao dịch thanh toán SePay';

-- 12. BẢNG: `warranties` (Bảo hành điện tử theo Mã IMEI / Số điện thoại)
CREATE TABLE `warranties` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT NULL COMMENT 'Khóa ngoại tham chiếu orders',
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu products',
  `imei_serial` varchar(100) NOT NULL COMMENT 'Mã IMEI hoặc Serial Number máy',
  `customer_phone` varchar(20) NOT NULL COMMENT 'SĐT kích hoạt bảo hành',
  `customer_name` varchar(150) NOT NULL COMMENT 'Tên khách hàng sở hữu',
  `warranty_months` int(11) DEFAULT 12 COMMENT 'Thời gian bảo hành (tháng)',
  `start_date` date NOT NULL COMMENT 'Ngày bắt đầu bảo hành',
  `end_date` date NOT NULL COMMENT 'Ngày hết hạn bảo hành',
  `status` enum('valid','expired','servicing') DEFAULT 'valid' COMMENT 'Trạng thái bảo hành',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú kỹ thuật',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `imei_serial` (`imei_serial`),
  KEY `fk_warranty_order` (`order_id`),
  KEY `fk_warranty_prod` (`product_id`),
  CONSTRAINT `fk_warranty_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE SET NULL,
  CONSTRAINT `fk_warranty_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng bảo hành điện tử';

-- 13. BẢNG: `contacts` (Hộp thư liên hệ & Góp ý khách hàng)
CREATE TABLE `contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'ID tài khoản liên hệ',
  `fullname` varchar(150) NOT NULL COMMENT 'Họ tên người gửi',
  `email` varchar(150) NOT NULL COMMENT 'Email người gửi',
  `phone` varchar(20) DEFAULT NULL COMMENT 'Số điện thoại',
  `subject` varchar(255) NOT NULL COMMENT 'Tiêu đề liên hệ',
  `message` text NOT NULL COMMENT 'Nội dung tin nhắn',
  `status` enum('new','read','replied') DEFAULT 'new' COMMENT 'Trạng thái xử lý',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_contact_user` (`user_id`),
  CONSTRAINT `fk_contact_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng hộp thư liên hệ';

-- 14. BẢNG: `coupons` (Mã khuyến mại & Voucher giảm giá trực tiếp)
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL COMMENT 'Mã voucher (UPPERCASE)',
  `title` varchar(255) NOT NULL COMMENT 'Tên ưu đãi hiển thị',
  `discount_type` enum('fixed','percent') DEFAULT 'fixed' COMMENT 'fixed: giảm tiền mặt, percent: giảm phần trăm',
  `apply_scope` enum('all','category','product') NOT NULL DEFAULT 'all' COMMENT 'Phạm vi áp dụng: all: tất cả, category: 1 danh mục, product: 1 sản phẩm',
  `category_id` int(11) DEFAULT NULL COMMENT 'ID danh mục áp dụng (nếu apply_scope = category)',
  `product_id` int(11) DEFAULT NULL COMMENT 'ID sản phẩm áp dụng (nếu apply_scope = product)',
  `discount_value` decimal(12,0) NOT NULL COMMENT 'Mức giảm (VNĐ hoặc %)',
  `max_discount` decimal(12,0) DEFAULT NULL COMMENT 'Mức giảm tối đa cho phần trăm (VNĐ)',
  `min_order_amount` decimal(12,0) DEFAULT 0 COMMENT 'Giá trị đơn tối thiểu để áp dụng',
  `quantity` int(11) DEFAULT 100 COMMENT 'Số lượt sử dụng còn lại',
  `used_count` int(11) DEFAULT 0 COMMENT 'Số lượt đã sử dụng',
  `start_date` datetime DEFAULT current_timestamp() COMMENT 'Thời điểm bắt đầu áp dụng',
  `end_date` datetime DEFAULT NULL COMMENT 'Thời điểm hết hạn',
  `status` enum('active','inactive') DEFAULT 'active' COMMENT 'Trạng thái hoạt động',
  `deleted_at` datetime DEFAULT NULL COMMENT 'Xóa mềm / Thùng rác',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng mã khuyến mại & voucher giảm giá';

-- 15. BẢNG: `wishlists` (Danh sách sản phẩm yêu thích)
CREATE TABLE IF NOT EXISTS `wishlists` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu users(id)',
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu products(id)',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `wishlists_user_product_unique` (`user_id`, `product_id`),
  KEY `fk_wishlist_product` (`product_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_wishlist_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng danh sách yêu thích Wishlist';

-- 16. BẢNG: `user_carts` (Giỏ hàng bền vững lưu theo tài khoản - Persistent Cart)
CREATE TABLE IF NOT EXISTS `user_carts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu users(id)',
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu products(id)',
  `color` varchar(100) NOT NULL DEFAULT '' COMMENT 'Tùy chọn màu sắc sản phẩm',
  `quantity` int(11) NOT NULL DEFAULT 1 COMMENT 'Số lượng sản phẩm trong giỏ',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_product_color_unique` (`user_id`, `product_id`, `color`),
  KEY `fk_user_carts_product` (`product_id`),
  CONSTRAINT `fk_user_carts_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_user_carts_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng giỏ hàng bền vững theo người dùng';

-- 17. BẢNG: `guest_reservations` (Đặt giữ chỗ nhanh dành cho khách chưa đăng nhập & quét IP chống ghim hàng)
CREATE TABLE IF NOT EXISTS `guest_reservations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'ID sản phẩm giữ chỗ',
  `product_name` varchar(255) NOT NULL COMMENT 'Tên sản phẩm tại thời điểm giữ chỗ',
  `color` varchar(100) DEFAULT '' COMMENT 'Tùy chọn màu sắc',
  `quantity` int(11) DEFAULT 1 COMMENT 'Số lượng đặt giữ chỗ',
  `price` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Đơn giá sản phẩm',
  `customer_name` varchar(150) NOT NULL COMMENT 'Họ tên khách hàng',
  `customer_phone` varchar(20) NOT NULL COMMENT 'Số điện thoại liên hệ nhận hàng',
  `customer_address` text NOT NULL COMMENT 'Địa chỉ giao hàng',
  `notes` text DEFAULT NULL COMMENT 'Ghi chú của khách hàng',
  `ip_address` varchar(45) NOT NULL COMMENT 'Địa chỉ IP để quét chống ghim hàng ảo',
  `status` enum('pending','contacted','completed','cancelled') DEFAULT 'pending' COMMENT 'Trạng thái xử lý CSKH',
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_guest_reservations_ip` (`ip_address`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng đặt giữ chỗ nhanh cho khách vãng lai';

-- 18. BẢNG: `coupon_usages` (Sổ ghi tên khách hàng sử dụng mã giảm giá - Mỗi người 1 lần, chống race-condition)
CREATE TABLE IF NOT EXISTS `coupon_usages` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `coupon_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu coupons(id)',
  `user_id` int(11) NOT NULL COMMENT 'Khóa ngoại tham chiếu users(id)',
  `order_id` int(11) DEFAULT NULL COMMENT 'Khóa ngoại tham chiếu orders(id)',
  `used_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Thời điểm áp dụng mã',
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_coupon_user` (`coupon_id`, `user_id`),
  KEY `fk_coupon_usages_coupon` (`coupon_id`),
  KEY `fk_coupon_usages_user` (`user_id`),
  CONSTRAINT `fk_coupon_usages_coupon` FOREIGN KEY (`coupon_id`) REFERENCES `coupons` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_coupon_usages_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Sổ ghi tên khách hàng sử dụng mã giảm giá (Mỗi người 1 lần)';


-- ==============================================================
-- BƯỚC 3: DỮ LIỆU CẤU HÌNH GỐC HỆ THỐNG
-- (6 DANH MỤC, 10 THƯƠNG HIỆU & 1 TÀI KHOẢN ADMIN)
-- ==============================================================

-- 1. Danh mục sản phẩm gốc (Phân định rõ phone vs accessory)
INSERT INTO `categories` (`id`, `name`, `slug`, `type`, `icon`, `description`, `created_at`) VALUES
(1, 'Điện thoại thông minh', 'dien-thoai', 'phone', 'fa-solid fa-mobile-screen', 'Smartphone chính hãng các dòng Flagship và tầm trung', NOW()),
(2, 'Tai nghe Bluetooth', 'tai-nghe-bluetooth', 'accessory', 'fa-solid fa-headphones-simple', 'Tai nghe True Wireless (TWS), Headphone chống ồn ANC', NOW()),
(3, 'Tai nghe có dây', 'tai-nghe-co-day', 'accessory', 'fa-solid fa-headphones', 'Tai nghe cổng Type-C, Lightning, 3.5mm Hi-Res', NOW()),
(4, 'Củ sạc & Cáp sạc nhanh', 'cu-sac-cap-sac', 'accessory', 'fa-solid fa-bolt', 'Củ sạc công nghệ GaN siêu nhanh, cáp bọc dù bền bỉ', NOW()),
(5, 'Ốp lưng & Kính cường lực', 'op-lung-cuong-luc', 'accessory', 'fa-solid fa-shield-halved', 'Phụ kiện bảo vệ máy chuẩn quân đội, chống sốc, kính 9H', NOW()),
(6, 'Pin dự phòng', 'pin-du-phong', 'accessory', 'fa-solid fa-battery-three-quarters', 'Pin sạc dự phòng sạc nhanh, MagSafe không dây', NOW());

-- 2. Thương hiệu gốc hệ thống (10 thương hiệu lớn)
INSERT INTO `brands` (`id`, `name`, `slug`, `is_phone_brand`, `logo`) VALUES
(1, 'Apple', 'apple', 1, NULL),
(2, 'Samsung', 'samsung', 1, NULL),
(3, 'Xiaomi', 'xiaomi', 1, NULL),
(4, 'OPPO', 'oppo', 1, NULL),
(5, 'Sony', 'sony', 0, NULL),
(6, 'Anker', 'anker', 0, NULL),
(7, 'Baseus', 'baseus', 0, NULL),
(8, 'Soundpeats', 'soundpeats', 0, NULL),
(9, 'Ugreen', 'ugreen', 0, NULL),
(10, 'Hoco', 'hoco', 0, NULL);

-- 3. TÀI KHOẢN ADMIN ĐỂ ĐĂNG NHẬP QUẢN TRỊ
-- Email: duan1@gmail.com
-- Mật khẩu: 123456@
-- 3. TÀI KHOẢN NGƯỜI DÙNG KIỂM THỬ (ADMIN & KHÁCH HÀNG DEMO)
-- Mật khẩu chung: 123456@
INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `address`, `status`, `created_at`) VALUES
(1, 'Quản trị viên Admin', 'duan1@gmail.com', '0988888888', '$2y$10$30DnRIKFzl5XU2uL0tCNWu/YQpTscmUOuvebawf/3iDe7nHADtY7S', 'admin', 'Hà Nội', 'active', NOW()),
(2, 'Son Abc', 'abcson20@gmail.com', '0362225438', '$2y$10$30DnRIKFzl5XU2uL0tCNWu/YQpTscmUOuvebawf/3iDe7nHADtY7S', 'customer', 'Vân Cát - Cẩm Vân - Thanh Hóa', 'active', NOW());

-- Mã khuyến mại & Voucher ưu đãi mặc định
INSERT INTO `coupons` (`id`, `code`, `title`, `discount_type`, `apply_scope`, `category_id`, `product_id`, `discount_value`, `max_discount`, `min_order_amount`, `quantity`, `used_count`, `start_date`, `end_date`, `status`) VALUES
(1, 'NEXVORA50', 'Giảm ngay 50.000đ cho mọi đơn hàng từ 200k', 'fixed', 'all', NULL, NULL, 50000, NULL, 200000, 100, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(2, 'FLAGSHIP200', 'Ưu đãi siêu phẩm - Giảm 200.000đ điện thoại cao cấp', 'fixed', 'category', 1, NULL, 200000, NULL, 10000000, 50, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(3, 'PHUKIEN15', 'Giảm 15% tổng đơn phụ kiện & tai nghe (tối đa 150k)', 'percent', 'category', 4, NULL, 15, 150000, 300000, 80, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(4, 'FREESHIP', 'Miễn phí vận chuyển hỏa tốc toàn quốc (trừ 30.000đ)', 'fixed', 'all', NULL, NULL, 30000, NULL, 100000, 200, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active');

-- Bật lại kiểm tra khóa ngoại sau khi hoàn tất
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================
-- HOÀN TẤT KHỞI TẠO CƠ SỞ DỮ LIỆU ĐẦY ĐỦ 100% (ĐÃ BỎ SẢN PHẨM MẪU)!
-- SẴN SÀNG ĐỂ NGƯỜI DÙNG TỰ THÊM SẢN PHẨM VÀ NỘP BÀI!
-- ==============================================================
