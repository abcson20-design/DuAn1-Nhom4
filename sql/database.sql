-- ==============================================================
-- DỰ ÁN 1: HỆ THỐNG WEBSITE BÁN ĐIỆN THOẠI & PHỤ KIỆN (NEXVORA STORE)
-- BẢN CƠ SỞ DỮ LIỆU HOÀN CHỈNH TẤT-CẢ-TRONG-MỘT (ALL-IN-ONE DATABASE)
-- CƠ SỞ DỮ LIỆU: DuAn1_db (hoặc duan1_db)
-- BẢNG MÃ: UTF8MB4 (Hỗ trợ tiếng Việt đầy đủ 100%)
-- ==============================================================
-- ĐẶC ĐIỂM HỆ THỐNG:
--   1. ĐẦY ĐỦ CẤU TRÚC 14 BẢNG CHUẨN HÓA 3NF MỚI NHẤT:
--       - categories: 6 danh mục (phân loại rõ 'phone' và 'accessory')
--       - brands: 10 thương hiệu lớn (Apple, Samsung, Xiaomi, OPPO, Sony, Anker...)
--       - users: Tài khoản người dùng & Quản trị viên Admin
--       - products: Sản phẩm điện thoại & phụ kiện (tích hợp xóa mềm deleted_at)
--       - product_attributes: Thuộc tính kỹ thuật riêng (phân biệt Smartphone vs Tai nghe/Phụ kiện)
--       - product_galleries: Album ảnh chi tiết từng góc máy, linh kiện cấu tạo
--       - product_colors: Biến thể màu sắc & số lượng tồn kho từng màu
--       - product_reviews: Đánh giá & bình luận sản phẩm 5 sao
--       - orders: Đơn hàng mua sắm
--       - order_items: Chi tiết các mặt hàng trong đơn
--       - payments: Lịch sử giao dịch thanh toán tự động qua cổng SePay
--       - warranties: Bảo hành điện tử theo IMEI / Serial / SĐT
--       - contacts: Hộp thư góp ý & liên hệ khách hàng
--       - coupons: Mã khuyến mại & voucher giảm giá trực tiếp
--   2. SẴN SÀNG 5 SẢN PHẨM MẪU CHUẨN THỰC TẾ:
--       - iPhone 16 Pro Max 256GB VN/A (Apple - Flagship Điện thoại)
--       - Samsung Galaxy S24 Ultra 5G (Samsung - Flagship Điện thoại)
--       - Xiaomi 14T Pro 5G Leica (Xiaomi - Flagship Điện thoại)
--       - Tai nghe chống ồn Sony WH-1000XM5 (Sony - Phụ kiện Tai nghe ANC)
--       - Củ sạc nhanh Anker Prime 67W GaN (Anker - Phụ kiện Củ sạc)
--   3. TÀI KHOẢN ADMIN MẶC ĐỊNH:
--       - Email: duan1@gmail.com
--       - Mật khẩu: 123456@
--       - Vai trò: admin
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
-- BƯỚC 2: TẠO CẤU TRÚC 14 BẢNG CHUẨN HÓA
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
  `deleted_at` datetime DEFAULT NULL COMMENT 'Thời gian xóa mềm (Thùng rác)',
  `created_at` datetime DEFAULT current_timestamp(),
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
INSERT INTO `users` (`id`, `fullname`, `email`, `phone`, `password`, `role`, `address`, `status`, `created_at`) VALUES
(1, 'Quản trị viên Admin', 'duan1@gmail.com', '0988888888', '$2y$10$30DnRIKFzl5XU2uL0tCNWu/YQpTscmUOuvebawf/3iDe7nHADtY7S', 'admin', 'Hà Nội', 'active', NOW());


-- ==============================================================
-- BƯỚC 4: NẠP 5 SẢN PHẨM MẪU CHUẨN ĐẸP 100%
-- (BAO GỒM CẢ ATTRIBUTES, GALLERIES, COLORS & REVIEWS)
-- ==============================================================

-- 1. BẢNG `products`
INSERT INTO `products` (
  `id`, `category_id`, `brand_id`, `name`, `slug`, `price`, `old_price`, 
  `image`, `gallery`, `video`, `colors`, `media_type`, `quantity`, 
  `description`, `specs`, `compatible_device`, `is_featured`, `is_hot`, `rating`, `views`, `deleted_at`, `created_at`
) VALUES
(
  1, 1, 1,
  'iPhone 16 Pro Max 256GB Chính Hãng VN/A',
  'iphone-16-pro-max-256gb',
  34990000, 36990000,
  'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80',
  '[{"tag":"Ngoại hình","title":"Khung viền Titan sa mạc đẳng cấp","desc":"Chất liệu titan cấp 5 siêu nhẹ, bền bỉ cùng viền màn hình mỏng nhất từng có trên iPhone.","icon":"fa-solid fa-mobile-screen-button","image":"https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80"},{"tag":"Màn hình","title":"Màn hình Super Retina XDR 6.9 inch","desc":"Tần số quét thích ứng ProMotion 120Hz, độ sáng ngoài trời đạt 2000 nits đỉnh cao.","icon":"fa-solid fa-tv","image":"https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop"},{"tag":"Camera AI","title":"Camera Fusion 48MP & Nút Điều Khiển Camera","desc":"Cảm biến quad-pixel thế hệ 2, quay video 4K 120fps Dolby Vision điện ảnh đỉnh cao.","icon":"fa-solid fa-camera","image":"https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop"},{"tag":"Hiệu năng","title":"Chip Apple A18 Pro 3nm vượt trội","desc":"CPU 6 lõi nhanh nhất trên smartphone, GPU 6 lõi dò tia phần cứng tăng tốc xử lý AI.","icon":"fa-solid fa-microchip","image":"https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop"},{"tag":"Pin & Sạc","title":"Thời lượng pin lên đến 33 giờ xem video","desc":"Hỗ trợ sạc nhanh không dây MagSafe 25W và cổng sạc chuẩn Type-C USB 3 siêu tốc.","icon":"fa-solid fa-bolt","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop"}]',
  'https://www.youtube.com/watch?v=eDqfg_LexCQ',
  '[{"name":"Titan Sa Mạc","image":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-deserttitanium-select?wid=470&hei=556&fmt=png-alpha","quantity":35},{"name":"Titan Tự Nhiên","image":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-naturaltitanium-select?wid=470&hei=556&fmt=png-alpha","quantity":30},{"name":"Titan Trắng","image":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-whitetitanium-select?wid=470&hei=556&fmt=png-alpha","quantity":25},{"name":"Titan Đen","image":"https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-blacktitanium-select?wid=470&hei=556&fmt=png-alpha","quantity":20}]',
  'slideshow',
  110,
  'iPhone 16 Pro Max mở ra kỷ nguyên mới với thiết kế Titan sang trọng bậc nhất, màn hình lớn 6.9 inch viền siêu mỏng cùng sức mạnh vượt trội từ chip Apple A18 Pro và bộ công cụ Apple Intelligence.\n\nĐặc điểm nổi bật:\n- Khung viền Titan cấp 5 hoàn thiện tinh xảo, bề mặt phun cát mịn màng cho cảm giác cầm nắm đầm tay và chắc chắn.\n- Màn hình Super Retina XDR OLED 6.9 inch hiển thị sống động rực rỡ với dải màu rộng P3, True Tone và tần số quét thông minh ProMotion 1-120Hz.\n- Nút Điều Khiển Camera (Camera Control) mới mang lại trải nghiệm bấm chụp cơ học nhạy bén, trượt ngón tay để phóng to thu nhỏ và chuyển đổi chế độ chuyên nghiệp.\n- Cụm 3 camera sau gồm camera chính Fusion 48MP cảm biến thế hệ mới, camera góc siêu rộng 48MP và camera tele 5x tiêu cự 120mm sắc nét.\n- Quay phim điện ảnh 4K tốc độ 120 khung hình/giây chuẩn Dolby Vision đầu tiên trên thế giới kết hợp 4 micro chuẩn phòng thu lọc gió ấn tượng.',
  '{"Màn hình":"6.9 inch, Super Retina XDR OLED, 120Hz ProMotion, 2868 x 1320 pixels, 2000 nits","Hệ điều hành":"iOS 18 (Hỗ trợ Apple Intelligence)","Camera sau":"Chính 48MP OIS + Góc siêu rộng 48MP + Tele 12MP 5x quang học","Camera trước":"12MP TrueDepth, khẩu độ f/1.9, Tự động lấy nét Autofocus","Vi xử lý (CPU)":"Apple A18 Pro 6 nhân (Tiến trình 3nm thế hệ thứ 2)","Đồ họa (GPU)":"Apple GPU 6 nhân hỗ trợ Ray Tracing phần cứng","RAM":"8 GB","Bộ nhớ trong":"256 GB chuẩn NVMe siêu tốc","Cổng kết nối / Sạc":"Type-C (Chuẩn USB 3 - Tốc độ 10Gbps), Hỗ trợ DisplayPort","Dung lượng Pin":"4.685 mAh, Sạc nhanh 50% trong 30 phút, MagSafe 25W","SIM":"2 SIM (1 Nano SIM vật lý + 1 eSIM hoặc 2 eSIM)","Kháng nước / bụi":"Chuẩn IP68 (chịu nước ở độ sâu 6m tối đa 30 phút)","Chất liệu":"Khung Titan cấp 5, Kính Ceramic Shield thế hệ mới nhất","Trọng lượng":"227 g"}',
  'Hệ sinh thái Apple (Apple Watch, AirPods, iPad, Mac) & Phụ kiện chuẩn Type-C',
  1, 1, 5.0, 1420, NULL, NOW()
),
(
  2, 1, 2,
  'Samsung Galaxy S24 Ultra 5G 12GB/256GB',
  'samsung-galaxy-s24-ultra-5g-256gb',
  28990000, 31990000,
  'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80',
  '[{"tag":"Ngoại hình","title":"Khung viền Titanium cứng cáp & Bút S-Pen","desc":"Thiết kế phẳng nguyên khối tích hợp bút S-Pen quyền năng với độ trễ cực thấp 2.8ms.","icon":"fa-solid fa-pen","image":"https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80"},{"tag":"Màn hình","title":"Màn hình Dynamic AMOLED 2X 6.8 inch phẳng","desc":"Độ sáng 2600 nits, kính Corning Gorilla Armor chống chói quang học giảm phản xạ 75%.","icon":"fa-solid fa-tv","image":"https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop"},{"tag":"Camera AI","title":"Cụm Camera Mắt Thần 200MP & ProVisual Engine","desc":"Hệ thống 4 camera sau hỗ trợ zoom chuẩn quang học 100x Space Zoom kết hợp AI Nightography.","icon":"fa-solid fa-camera","image":"https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop"},{"tag":"Hiệu năng","title":"Chip Snapdragon 8 Gen 3 for Galaxy","desc":"Buồng hơi tản nhiệt Vapor Chamber mở rộng gấp 1.9 lần giữ máy luôn mát khi chơi game nặng.","icon":"fa-solid fa-microchip","image":"https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop"},{"tag":"Pin & Sạc","title":"Dung lượng Pin 5000 mAh & Sạc siêu nhanh 45W","desc":"Thời lượng sử dụng vượt trội cả ngày dài với công nghệ AI tiết kiệm điện năng thông minh.","icon":"fa-solid fa-bolt","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop"}]',
  'https://www.youtube.com/watch?v=J7x0EW_T3c0',
  '[{"name":"Xám Titan","image":"https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=500&auto=format&fit=crop&q=80","quantity":30},{"name":"Đen Titan","image":"https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&auto=format&fit=crop&q=80","quantity":25},{"name":"Tím Titan","image":"https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&auto=format&fit=crop&q=80","quantity":20},{"name":"Vàng Titan","image":"https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=500&auto=format&fit=crop&q=80","quantity":15}]',
  'slideshow',
  90,
  'Galaxy S24 Ultra dẫn đầu xu hướng trí tuệ nhân tạo trên di động với hệ sinh thái Galaxy AI đa năng: Khoanh vùng tìm kiếm (Circle to Search), Dịch cuộc gọi trực tiếp hai chiều, Trợ lý note thông minh và chỉnh sửa ảnh chuyên sâu.\n\nĐặc điểm nổi bật:\n- Khung viền Titanium cứng cáp kết hợp kính Corning Gorilla Armor thế hệ mới giảm độ phản chiếu ánh sáng đến 75% cho tầm nhìn trong trẻo dưới trời nắng.\n- Màn hình phẳng 6.8 inch QHD+ Dynamic AMOLED 2X rực rỡ với tần số quét 1-120Hz mượt mà.\n- Cảm biến camera 200MP tái hiện chi tiết sắc nét phi thường cùng ống kính tele 50MP quang học 5x kết hợp công nghệ ProVisual Engine xóa nhòa bóng tối.\n- Vi xử lý Snapdragon 8 Gen 3 for Galaxy tiến trình 4nm cân mọi tựa game đồ họa Ray Tracing và tác vụ AI nặng nề.\n- Bút S-Pen tích hợp sẵn trong thân máy giúp ghi chú, phác thảo ý tưởng và điều khiển từ xa nhanh chóng.',
  '{"Màn hình":"6.8 inch, Dynamic AMOLED 2X, 120Hz thích ứng, QHD+ (3120 x 1440 pixels), 2600 nits","Hệ điều hành":"Android 14, Giao diện One UI 6.1 (Tích hợp Galaxy AI)","Camera sau":"Chính 200MP OIS + Góc siêu rộng 12MP + Tele 50MP 5x quang học + Tele 10MP 3x","Camera trước":"12MP Dual Pixel PDAF, f/2.2","Vi xử lý (CPU)":"Qualcomm Snapdragon 8 Gen 3 for Galaxy (4nm) 8 nhân","Đồ họa (GPU)":"Adreno 750 (1 GHz)","RAM":"12 GB LPDDR5X","Bộ nhớ trong":"256 GB chuẩn UFS 4.0","Cổng kết nối / Sạc":"Type-C (USB 3.2 Gen 1), Hỗ trợ Samsung DeX không dây","Dung lượng Pin":"5.000 mAh, Sạc siêu nhanh 45W, Sạc không dây 15W","SIM":"2 Nano SIM hoặc 1 Nano SIM + 1 eSIM","Kháng nước / bụi":"IP68 (Kháng nước ở độ sâu 1.5m trong 30 phút)","Chất liệu":"Khung Titanium nguyên khối, Mặt kính Corning Gorilla Armor","Trọng lượng":"232 g"}',
  'Hệ sinh thái Galaxy (Galaxy Watch, Galaxy Buds, Galaxy Tab) & Bút S-Pen',
  1, 1, 4.9, 980, NULL, NOW()
),
(
  3, 1, 3,
  'Xiaomi 14T Pro 5G 12GB/512GB Ống Kính Leica',
  'xiaomi-14t-pro-5g-512gb',
  16490000, 17990000,
  'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80',
  '[{"tag":"Ngoại hình","title":"Thiết kế Deco kim loại sang trọng","desc":"Khung hợp kim nhôm CNC hoàn thiện mờ chống bám vân tay, viền màn hình siêu mỏng 1.9mm.","icon":"fa-solid fa-mobile","image":"https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80"},{"tag":"Màn hình","title":"Màn hình CrystalRes AMOLED 144Hz 1.5K","desc":"Độ sáng cực đỉnh lên đến 4000 nits, chứng nhận bảo vệ mắt TÜV Rheinland không mỏi mắt.","icon":"fa-solid fa-tv","image":"https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop"},{"tag":"Camera Leica","title":"Hệ thống ống kính quang học Leica Summilux","desc":"Cảm biến ảnh Light Fusion 900 dải động cực cao 13.57 EV cho bức ảnh đậm chất điện ảnh.","icon":"fa-solid fa-camera","image":"https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop"},{"tag":"Hiệu năng","title":"Siêu chip MediaTek Dimensity 9300+ (4nm)","desc":"Xung nhịp CPU 3.4GHz, hệ thống tản nhiệt Xiaomi 3D IceLoop độc quyền mát lạnh.","icon":"fa-solid fa-microchip","image":"https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop"},{"tag":"Pin & Sạc","title":"Sạc siêu tốc HyperCharge 120W","desc":"Nạp đầy 100% dung lượng pin 5000mAh chỉ trong vỏn vẹn 19 phút.","icon":"fa-solid fa-bolt","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop"}]',
  'https://www.youtube.com/watch?v=FfQ99Z99W6E',
  '[{"name":"Xám Titan","image":"https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=500&auto=format&fit=crop&q=80","quantity":30},{"name":"Xanh Titan","image":"https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=500&auto=format&fit=crop&q=80","quantity":25},{"name":"Đen Titan","image":"https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&auto=format&fit=crop&q=80","quantity":25}]',
  'slideshow',
  80,
  'Xiaomi 14T Pro 5G là chiếc smartphone đỉnh cao kết hợp với huyền thoại nhiếp ảnh Leica. Trang bị cụm 3 camera Leica Summilux, màn hình AMOLED 144Hz và sức mạnh vô địch từ Dimensity 9300+.\n\nĐặc điểm nổi bật:\n- Ống kính quang học Leica Summilux với khẩu độ lớn f/1.6 giúp thu trọn ánh sáng đêm và tạo bokeh xóa phông tự nhiên chuẩn nhiếp ảnh.\n- Cảm biến Light Fusion 900 kích thước lớn 1/1.31 inch, hỗ trợ chế độ màu Leica Authentic và Leica Vibrant độc quyền.\n- Màn hình 6.67 inch độ phân giải 1.5K sắc nét với tần số quét 144Hz siêu tốc mang đến trải nghiệm cuộn lướt và chơi game mượt mà không độ trễ.\n- Công nghệ sạc siêu tốc Xiaomi HyperCharge 120W đi kèm bộ sạc nhanh trong hộp máy và hỗ trợ sạc không dây 50W.\n- Khả năng kháng bụi kháng nước chuẩn IP68 yên tâm sử dụng trong mọi điều kiện thời tiết.',
  '{"Màn hình":"6.67 inch, AMOLED 144Hz, 1.5K (2712 x 1220 pixels), 4000 nits, Dolby Vision, HDR10+","Hệ điều hành":"Xiaomi HyperOS (nền tảng Android 14)","Camera sau":"Chính Leica 50MP OIS (Light Fusion 900) + Tele Leica 50MP 2.6x + Siêu rộng 12MP","Camera trước":"32MP HDR, Khẩu độ f/2.0, Quay video 4K 30fps","Vi xử lý (CPU)":"MediaTek Dimensity 9300+ (4nm) 8 nhân, tốc độ tối đa 3.4GHz","Đồ họa (GPU)":"Immortalis-G720 MC12","RAM":"12 GB LPDDR5X","Bộ nhớ trong":"512 GB chuẩn UFS 4.0","Cổng kết nối / Sạc":"Type-C, Sạc có dây 120W HyperCharge, Sạc không dây 50W","Dung lượng Pin":"5.000 mAh (Chu kỳ sạc bền bỉ 1600 lần)","SIM":"2 Nano SIM (Hỗ trợ 5G 2 sóng online)","Kháng nước / bụi":"IP68","Chất liệu":"Khung hợp kim nhôm cao cấp, Kính cường lực 3D","Trọng lượng":"209 g"}',
  'Tất cả thiết bị kết nối chuẩn Type-C và hệ sinh thái Xiaomi Smart Home',
  1, 0, 4.8, 650, NULL, NOW()
),
(
  4, 2, 5,
  'Tai nghe không dây Sony WH-1000XM5 Chống Ồn ANC',
  'tai-nghe-chup-tai-sony-wh-1000xm5',
  6990000, 8490000,
  'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80',
  '[{"tag":"Thiết kế","title":"Thiết kế công thái học êm ái","desc":"Đệm da mềm mại giảm áp lực lên đầu, khớp xoay êm ái không tiếng động.","icon":"fa-solid fa-headphones","image":"https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80"},{"tag":"Chống ồn","title":"2 vi xử lý V1 & QN1 điều khiển 8 micro","desc":"Khử ồn tự động tối ưu theo môi trường và áp suất khí quyển Auto NC Optimizer.","icon":"fa-solid fa-shield-halved","image":"https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop"},{"tag":"Âm thanh","title":"Driver 30mm sợi carbon gia cố","desc":"Âm thanh chi tiết độ trung thực cao Hi-Res Audio Wireless cùng bộ giải mã LDAC.","icon":"fa-solid fa-music","image":"https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&auto=format&fit=crop"},{"tag":"Pin","title":"Thời lượng pin 30 giờ & Sạc nhanh USB-PD","desc":"Chỉ 3 phút sạc cho 3 giờ nghe nhạc liên tục qua củ sạc hỗ trợ Power Delivery.","icon":"fa-solid fa-battery-full","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop"}]',
  NULL,
  '[{"name":"Đen Cổ Điển","image":"https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=80","quantity":40},{"name":"Bạc Ánh Kim","image":"https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&auto=format&fit=crop&q=80","quantity":35}]',
  'slideshow',
  75,
  'Tai nghe Sony WH-1000XM5 tái định nghĩa chuẩn mực chống ồn chủ động (ANC) với 2 bộ xử lý âm thanh độc quyền và 8 micro định dạng sóng. Tận hưởng chất âm trong trẻo chuẩn phòng thu ở bất kỳ đâu.\n\nĐặc điểm nổi bật:\n- Công nghệ Auto NC Optimizer tự động nhận biết môi trường xung quanh và vị trí đeo để điều chỉnh mức độ khử ồn thích ứng chuẩn xác nhất.\n- Bộ củ loa 30mm được chế tạo từ vật liệu sợi carbon tổng hợp siêu nhẹ mang lại âm trầm sâu lắng và dải cao tách bạch.\n- Khả năng đàm thoại xuất sắc với 4 micro Beamforming tích hợp thuật toán khử nhiễu giọng nói AI chuẩn xác ngay cả ở ngã tư đông đúc.\n- Tính năng Speak-to-Chat tự động dừng phát nhạc khi bạn bắt đầu nói chuyện và tiếp tục phát khi cuộc trò chuyện kết thúc.\n- Hỗ trợ kết nối đồng thời 2 thiết bị Bluetooth và chuyển đổi nguồn phát mượt mà tức thì.',
  '{"Kiểu tai nghe":"Chụp tai (Over-ear) không dây chống ồn chủ động","Công nghệ chống ồn":"Active Noise Cancelling (ANC) với 2 chip V1 & QN1 điều khiển 8 micro","Kích thước màng loa":"Driver 30mm gia cố bằng vật liệu sợi carbon nhẹ cứng","Chuẩn âm thanh":"Hi-Res Audio, Hi-Res Audio Wireless, LDAC, DSEE Extreme","Kết nối không dây":"Bluetooth 5.2 (Hỗ trợ kết nối cùng lúc 2 thiết bị Multi-point)","Phạm vi kết nối":"10 mét ổn định","Thời lượng pin":"30 giờ (bật ANC), 40 giờ (tắt ANC)","Thời gian sạc đầy":"Khoảng 3.5 giờ (Sạc nhanh 3 phút nghe được 3 giờ qua chuẩn USB-PD)","Cổng sạc":"Type-C","Cổng cắm dây":"Jack 3.5mm mạ vàng chống nhiễu (Dây cáp tháo rời dài 1.2m đi kèm)","Điều khiển":"Cảm ứng chạm trên củ tai phải + Nút bấm vật lý chuyển chế độ ANC/Ambient","Micro đàm thoại":"4 micro Beamforming tích hợp AI lọc gió và tiếng ồn môi trường","Trọng lượng":"250 g siêu nhẹ êm ái","Tương thích":"iPhone, iPad, Android, Windows PC, macOS qua Bluetooth hoặc Jack 3.5mm"}',
  'iPhone, Android, Máy tính bảng, PC, Laptop, TV thông minh',
  1, 1, 5.0, 840, NULL, NOW()
),
(
  5, 4, 6,
  'Củ sạc nhanh Anker Prime 67W GaN 3 Cổng (A2669)',
  'cu-sac-nhanh-anker-prime-67w-gan-3-cong',
  990000, 1250000,
  'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop&q=80',
  '[{"tag":"Thiết kế","title":"Kích thước siêu nhỏ gọn GaNPrime","desc":"Nhỏ hơn 51% so với củ sạc 67W nguyên bản truyền thống, chân gập linh hoạt bỏ túi dễ dàng.","icon":"fa-solid fa-cube","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop&q=80"},{"tag":"Công nghệ","title":"Công nghệ bán dẫn GaN thế hệ mới","desc":"Hiệu suất chuyển đổi năng lượng lên tới 95%, tỏa nhiệt cực thấp và chống suy hao điện năng.","icon":"fa-solid fa-microchip","image":"https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop"},{"tag":"Công suất","title":"Sạc nhanh tối đa 67W PowerIQ 4.0","desc":"Tự động phát hiện thiết bị và điều phối công suất dòng điện thông minh, an toàn tuyệt đối.","icon":"fa-solid fa-bolt","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop"},{"tag":"Bảo vệ","title":"Hệ thống an toàn ActiveShield 2.0","desc":"Cảm biến đo nhiệt độ hơn 3.000.000 lần mỗi ngày để bảo vệ tối đa cho pin của thiết bị.","icon":"fa-solid fa-shield-halved","image":"https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop"}]',
  NULL,
  '[{"name":"Đen Phantom","image":"https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=500&auto=format&fit=crop&q=80","quantity":50},{"name":"Bạc Kim Loại","image":"https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&auto=format&fit=crop&q=80","quantity":50}]',
  'slideshow',
  100,
  'Củ sạc nhanh Anker Prime 67W GaN (mã hiệu A2669) tích hợp 3 cổng sạc công suất lớn (2 cổng USB-C và 1 cổng USB-A) với kích thước chỉ bằng một bao diêm, có thể sạc cùng lúc cả MacBook Air, iPhone và Apple Watch nhanh chóng.\n\nĐặc điểm nổi bật:\n- Công nghệ GaNPrime tân tiến hàng đầu của Anker tối ưu kích thước thu nhỏ 51% so với củ sạc MacBook 67W tiêu chuẩn.\n- Trang bị 2 cổng Type-C hỗ trợ chuẩn sạc Power Delivery 3.0 và PPS, sạc đầy 50% pin cho MacBook Air 13 inch chỉ trong 30 phút hoặc sạc tối đa công suất cho iPhone 16 Pro Max.\n- Cổng USB-A hỗ trợ chuẩn PowerIQ 22.5W tương thích các thiết bị Android sạc nhanh công nghệ SCP/FCP.\n- Công nghệ ActiveShield 2.0 độc quyền theo dõi nhiệt độ liên tục và chủ động hạ dòng điện khi phát hiện quá nhiệt, đảm bảo tuổi thọ thiết bị ở mức tốt nhất.\n- Thiết kế chân cắm gập 90 độ tiện dụng, độ hoàn thiện nhôm xước mờ cao cấp chống trầy xước.',
  '{"Công nghệ sạc":"Anker GaNPrime™, PowerIQ 4.0, Dynamic Power Distribution","Tổng công suất tối đa":"67W","Số lượng cổng ra":"3 cổng (2 x USB-C + 1 x USB-A)","Điện áp đầu vào":"100V - 240V ~ 50/60Hz 1.8A (Sử dụng tiện lợi toàn cầu)","Công suất đơn cổng USB-C1 / C2":"Tối đa 67W (5V/3A, 9V/3A, 12V/3A, 15V/3A, 20V/3.35A)","Công suất đơn cổng USB-A":"Tối đa 22.5W (4.5V/5A, 5V/4.5A, 5V/3A, 9V/2A, 10V/2.25A, 12V/1.5A)","Sạc đồng thời 2 cổng (C1 + C2)":"C1 (45W) + C2 (20W)","Sạc đồng thời 3 cổng (C1 + C2 + A)":"C1 (42W) + C2 (15W) + A (10W) - Tổng 67W","Chuẩn sạc tương thích":"PD 3.0, PPS, Quick Charge 4.0+, SCP, FCP, Apple 2.4A","Tính năng an toàn":"ActiveShield 2.0 cảm biến nhiệt độ 3 triệu lần/ngày","Chân cắm điện":"Chân gập US gọn gàng, tiện lợi bỏ túi du lịch","Kích thước củ sạc":"40 x 39 x 50 mm","Trọng lượng":"135 g"}',
  'iPhone 16/15 series, Samsung Galaxy S24, MacBook Air, iPad Pro/Air, Tai nghe không dây',
  0, 1, 4.9, 520, NULL, NOW()
);

-- 2. BẢNG `product_attributes` (Thuộc tính chuẩn hóa cho Điện thoại & Phụ kiện)
INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
(1, 'Màn hình', '6.9 inch, Super Retina XDR OLED, 120Hz ProMotion, 2000 nits', 'Màn hình & Hiển thị', 1),
(1, 'Hệ điều hành', 'iOS 18 (Hỗ trợ Apple Intelligence)', 'Phần mềm', 2),
(1, 'Vi xử lý (CPU)', 'Apple A18 Pro 6 nhân (3nm thế hệ thứ 2)', 'Hiệu năng', 3),
(1, 'Đồ họa (GPU)', 'Apple GPU 6 nhân hỗ trợ Ray Tracing', 'Hiệu năng', 4),
(1, 'RAM', '8 GB', 'Hiệu năng', 5),
(1, 'Bộ nhớ trong', '256 GB chuẩn NVMe siêu tốc', 'Bộ nhớ', 6),
(1, 'Camera sau', 'Chính 48MP OIS + Góc siêu rộng 48MP + Tele 12MP 5x quang', 'Camera', 7),
(1, 'Camera trước', '12MP TrueDepth, f/1.9, Autofocus', 'Camera', 8),
(1, 'Pin & Sạc', '4.685 mAh, Sạc nhanh 50% trong 30 phút, MagSafe 25W', 'Pin & Nguồn', 9),
(1, 'Cổng kết nối', 'Type-C (Chuẩn USB 3 - Tốc độ 10Gbps)', 'Kết nối', 10),
(1, 'Kháng nước / bụi', 'Chuẩn IP68 (chịu nước sâu 6m tối đa 30 phút)', 'Thiết kế', 11),
(1, 'Chất liệu', 'Khung Titan cấp 5, Kính Ceramic Shield thế hệ mới', 'Thiết kế', 12),

(2, 'Màn hình', '6.8 inch, Dynamic AMOLED 2X, 120Hz thích ứng, QHD+, 2600 nits', 'Màn hình & Hiển thị', 1),
(2, 'Hệ điều hành', 'Android 14, One UI 6.1 (Tích hợp Galaxy AI)', 'Phần mềm', 2),
(2, 'Vi xử lý (CPU)', 'Snapdragon 8 Gen 3 for Galaxy (4nm) 8 nhân', 'Hiệu năng', 3),
(2, 'Đồ họa (GPU)', 'Adreno 750 (1 GHz)', 'Hiệu năng', 4),
(2, 'RAM', '12 GB LPDDR5X', 'Hiệu năng', 5),
(2, 'Bộ nhớ trong', '256 GB chuẩn UFS 4.0', 'Bộ nhớ', 6),
(2, 'Camera sau', 'Chính 200MP OIS + Siêu rộng 12MP + Tele 50MP 5x + Tele 10MP 3x', 'Camera', 7),
(2, 'Camera trước', '12MP Dual Pixel PDAF', 'Camera', 8),
(2, 'Pin & Sạc', '5.000 mAh, Sạc siêu nhanh 45W, Sạc không dây 15W', 'Pin & Nguồn', 9),
(2, 'Bút cảm ứng', 'Bút S-Pen tích hợp trong thân máy, độ trễ 2.8ms', 'Tính năng đặc biệt', 10),
(2, 'Kháng nước / bụi', 'IP68', 'Thiết kế', 11),
(2, 'Chất liệu', 'Khung Titanium nguyên khối, Kính Corning Gorilla Armor', 'Thiết kế', 12),

(3, 'Màn hình', '6.67 inch, AMOLED 144Hz, 1.5K, 4000 nits, Dolby Vision', 'Màn hình & Hiển thị', 1),
(3, 'Hệ điều hành', 'Xiaomi HyperOS (Android 14)', 'Phần mềm', 2),
(3, 'Vi xử lý (CPU)', 'MediaTek Dimensity 9300+ (4nm) 3.4GHz', 'Hiệu năng', 3),
(3, 'RAM', '12 GB LPDDR5X', 'Hiệu năng', 4),
(3, 'Bộ nhớ trong', '512 GB UFS 4.0', 'Bộ nhớ', 5),
(3, 'Camera sau', 'Chính Leica 50MP OIS + Tele Leica 50MP + Siêu rộng 12MP', 'Camera', 6),
(3, 'Camera trước', '32MP HDR, quay 4K', 'Camera', 7),
(3, 'Pin & Sạc', '5.000 mAh, Sạc siêu tốc HyperCharge 120W (19 phút đầy pin)', 'Pin & Nguồn', 8),
(3, 'Kháng nước / bụi', 'IP68', 'Thiết kế', 9),

(4, 'Kiểu tai nghe', 'Chụp tai (Over-ear) không dây chống ồn chủ động', 'Thiết kế', 1),
(4, 'Công nghệ chống ồn', 'Active Noise Cancelling (ANC) với 2 chip V1 & QN1 điều khiển 8 micro', 'Tính năng âm thanh', 2),
(4, 'Màng loa (Driver)', 'Driver 30mm sợi carbon gia cố siêu nhẹ', 'Tính năng âm thanh', 3),
(4, 'Chuẩn âm thanh', 'Hi-Res Audio, Hi-Res Audio Wireless, LDAC, DSEE Extreme', 'Chất lượng âm thanh', 4),
(4, 'Kết nối không dây', 'Bluetooth 5.2 (Kết nối cùng lúc 2 thiết bị Multi-point)', 'Kết nối', 5),
(4, 'Phạm vi kết nối', '10 mét ổn định', 'Kết nối', 6),
(4, 'Thời lượng pin', '30 giờ (bật ANC), 40 giờ (tắt ANC)', 'Pin & Nguồn', 7),
(4, 'Thời gian sạc', 'Sạc nhanh 3 phút nghe được 3 giờ qua USB-PD', 'Pin & Nguồn', 8),
(4, 'Cổng sạc & cắm dây', 'Cổng sạc Type-C + Jack tai nghe 3.5mm tháo rời', 'Kết nối', 9),
(4, 'Micro đàm thoại', '4 micro Beamforming tích hợp thuật toán AI khử ồn gió', 'Đàm thoại', 10),
(4, 'Trọng lượng', '250 g siêu êm ái', 'Thiết kế', 11),
(4, 'Tương thích', 'iPhone, iPad, Android, Windows, macOS qua Bluetooth hoặc 3.5mm', 'Tương thích', 12),

(5, 'Công nghệ sạc', 'Anker GaNPrime™, PowerIQ 4.0, Phân bổ dòng điện thông minh', 'Công nghệ', 1),
(5, 'Tổng công suất tối đa', '67W', 'Công suất', 2),
(5, 'Số lượng cổng ra', '3 cổng (2 x USB-C + 1 x USB-A)', 'Cổng kết nối', 3),
(5, 'Nguồn điện vào', '100V - 240V ~ 50/60Hz 1.8A (Sử dụng toàn cầu)', 'Nguồn điện', 4),
(5, 'Công suất đơn cổng Type-C', 'Tối đa 67W (5V/3A, 9V/3A, 12V/3A, 15V/3A, 20V/3.35A)', 'Công suất cổng', 5),
(5, 'Công suất đơn cổng USB-A', 'Tối đa 22.5W (4.5V/5A, 5V/4.5A, 9V/2A, 10V/2.25A)', 'Công suất cổng', 6),
(5, 'Tính năng an toàn', 'ActiveShield 2.0 cảm biến nhiệt độ hơn 3 triệu lần/ngày', 'An toàn', 7),
(5, 'Chân cắm điện', 'Chân gập US 90 độ gọn gàng du lịch', 'Thiết kế', 8),
(5, 'Kích thước & Trọng lượng', '40 x 39 x 50 mm, Nặng 135 g', 'Thiết kế', 9),
(5, 'Thiết bị tương thích', 'iPhone 16/15 series, MacBook Air, iPad, Samsung Galaxy, Tai nghe', 'Tương thích', 10);

-- 3. BẢNG `product_galleries` (Album ảnh chi tiết)
INSERT INTO `product_galleries` (`product_id`, `image_url`, `title`, `tag`, `description`, `sort_order`) VALUES
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80', 'Khung viền Titan sa mạc đẳng cấp', 'Ngoại hình', 'Chất liệu titan cấp 5 siêu nhẹ, bền bỉ', 1),
(1, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop', 'Màn hình Super Retina XDR 6.9 inch', 'Màn hình', 'Tần số quét thích ứng ProMotion 120Hz, 2000 nits', 2),
(1, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop', 'Cụm Camera Fusion 48MP & Nút Camera Control', 'Camera AI', 'Cảm biến quad-pixel thế hệ 2, quay 4K 120fps Dolby Vision', 3),
(1, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop', 'Chip Apple A18 Pro 3nm vượt trội', 'Hiệu năng', 'CPU 6 lõi nhanh nhất, GPU 6 lõi Ray Tracing', 4),
(1, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop', 'Thời lượng pin 33 giờ xem video', 'Pin & Sạc', 'Hỗ trợ sạc MagSafe 25W và Type-C USB 3 siêu tốc', 5),

(2, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80', 'Khung viền Titanium cứng cáp & Bút S-Pen', 'Ngoại hình', 'Thiết kế phẳng nguyên khối tích hợp bút S-Pen quyền năng', 1),
(2, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop', 'Màn hình Dynamic AMOLED 2X 6.8 inch', 'Màn hình', 'Độ sáng 2600 nits, kính Corning Gorilla Armor chống chói', 2),
(2, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop', 'Cụm Camera Mắt Thần 200MP & ProVisual', 'Camera AI', 'Zoom chuẩn quang học 100x Space Zoom kết hợp AI', 3),
(2, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop', 'Chip Snapdragon 8 Gen 3 for Galaxy', 'Hiệu năng', 'Buồng hơi tản nhiệt Vapor Chamber mở rộng gấp 1.9 lần', 4),
(2, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop', 'Dung lượng Pin 5000 mAh & Sạc nhanh 45W', 'Pin & Sạc', 'Thời lượng sử dụng vượt trội cả ngày dài với Galaxy AI', 5),

(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80', 'Thiết kế Deco kim loại sang trọng', 'Ngoại hình', 'Khung hợp kim nhôm CNC viền siêu mỏng 1.9mm', 1),
(3, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop', 'Màn hình CrystalRes AMOLED 144Hz 1.5K', 'Màn hình', 'Độ sáng 4000 nits, bảo vệ mắt TÜV Rheinland', 2),
(3, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop', 'Hệ thống ống kính quang học Leica Summilux', 'Camera Leica', 'Cảm biến ảnh Light Fusion 900 dải động cực cao 13.57 EV', 3),
(3, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop', 'Chip MediaTek Dimensity 9300+ 4nm', 'Hiệu năng', 'Hệ thống tản nhiệt Xiaomi 3D IceLoop độc quyền', 4),
(3, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop', 'Sạc siêu tốc HyperCharge 120W', 'Pin & Sạc', 'Nạp đầy 100% dung lượng pin trong 19 phút', 5),

(4, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80', 'Thiết kế công thái học êm ái', 'Thiết kế', 'Đệm da vừa vặn mềm mại giảm áp lực lên đầu', 1),
(4, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop', '2 vi xử lý V1 & QN1 điều khiển 8 micro', 'Chống ồn', 'Khử ồn thích ứng môi trường Auto NC Optimizer', 2),
(4, 'https://images.unsplash.com/photo-1583394838336-acd977736f90?w=800&auto=format&fit=crop', 'Driver 30mm sợi carbon gia cố', 'Âm thanh', 'Hi-Res Audio Wireless cùng bộ giải mã LDAC', 3),
(4, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop', 'Pin 30 giờ & Sạc nhanh USB-PD', 'Pin', '3 phút sạc nghe được 3 giờ liên tục', 4),

(5, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop&q=80', 'Kích thước siêu nhỏ gọn GaNPrime', 'Thiết kế', 'Nhỏ hơn 51% so với củ sạc 67W nguyên bản của Apple', 1),
(5, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop', 'Công nghệ bán dẫn GaN thế hệ mới', 'Công nghệ', 'Hiệu suất truyền tải điện năng 95%, tỏa nhiệt cực thấp', 2),
(5, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop', 'Sạc nhanh tối đa 67W PowerIQ 4.0', 'Công suất', 'Tự động phát hiện thiết bị và điều phối công suất dòng điện', 3),
(5, 'https://images.unsplash.com/photo-1605236453806-6ff36851218e?w=800&auto=format&fit=crop', 'Hệ thống an toàn ActiveShield 2.0', 'Bảo vệ', 'Cảm biến đo nhiệt độ 3 triệu lần mỗi ngày', 4);

-- 4. BẢNG `product_colors` (Biến thể màu sắc & Tồn kho)
INSERT INTO `product_colors` (`product_id`, `color_name`, `color_image`, `quantity`) VALUES
(1, 'Titan Sa Mạc', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-deserttitanium-select?wid=470&hei=556&fmt=png-alpha', 35),
(1, 'Titan Tự Nhiên', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-naturaltitanium-select?wid=470&hei=556&fmt=png-alpha', 30),
(1, 'Titan Trắng', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-whitetitanium-select?wid=470&hei=556&fmt=png-alpha', 25),
(1, 'Titan Đen', 'https://store.storeimages.cdn-apple.com/4982/as-images.apple.com/is/iphone-16-pro-blacktitanium-select?wid=470&hei=556&fmt=png-alpha', 20),

(2, 'Xám Titan', 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=500&auto=format&fit=crop&q=80', 30),
(2, 'Đen Titan', 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=500&auto=format&fit=crop&q=80', 25),
(2, 'Tím Titan', 'https://images.unsplash.com/photo-1592750475338-74b7b21085ab?w=500&auto=format&fit=crop&q=80', 20),
(2, 'Vàng Titan', 'https://images.unsplash.com/photo-1565849904461-04a58ad377e0?w=500&auto=format&fit=crop&q=80', 15),

(3, 'Xám Titan', 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=500&auto=format&fit=crop&q=80', 30),
(3, 'Xanh Titan', 'https://images.unsplash.com/photo-1574944985070-8f3ebc6b79d2?w=500&auto=format&fit=crop&q=80', 25),
(3, 'Đen Titan', 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&auto=format&fit=crop&q=80', 25),

(4, 'Đen Cổ Điển', 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&auto=format&fit=crop&q=80', 40),
(4, 'Bạc Ánh Kim', 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&auto=format&fit=crop&q=80', 35),

(5, 'Đen Phantom', 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=500&auto=format&fit=crop&q=80', 50),
(5, 'Bạc Kim Loại', 'https://images.unsplash.com/photo-1510557880182-3d4d3cba35a5?w=500&auto=format&fit=crop&q=80', 50);

-- 5. BẢNG `product_reviews` (5 đánh giá mẫu)
INSERT INTO `product_reviews` (
  `id`, `product_id`, `user_id`, `author_name`, `author_email`, `author_phone`, 
  `rating`, `content`, `is_purchased`, `status`, `likes`, `created_at`
) VALUES
(
  1, 1, NULL, 'Trần Minh Quân', 'quan.tm@gmail.com', '0912345678',
  5, 'Máy cầm rất đầm tay, viền màn hình siêu mỏng nhìn rất đã mắt! Nút Camera Control mới dùng rất tiện khi chụp ảnh một tay. Giao hàng tại shop đóng gói rất kỹ, nguyên seal chính hãng!', 1, 'approved', 12, NOW()
),
(
  2, 2, NULL, 'Nguyễn Hoàng Nam', 'nam.nh@gmail.com', '0987654321',
  5, 'Màn hình chống chói của S24 Ultra quá xuất sắc khi ra đường trời nắng gắt. Bút S-Pen mượt, tính năng Khoanh tròn tìm kiếm Galaxy AI xịn sò thực sự!', 1, 'approved', 8, NOW()
),
(
  3, 3, NULL, 'Lê Văn Đạt', 'dat.le@gmail.com', '0903112233',
  5, 'Chụp chân dung bằng tông màu Leica nhìn rất có chiều sâu và nghệ thuật. Sạc 120W đúng nghĩa cắm rửa mặt xong là đầy pin, quá tiện lợi!', 1, 'approved', 5, NOW()
),
(
  4, 4, NULL, 'Phạm Thu Trang', 'trang.pham@gmail.com', '0934567890',
  5, 'Chống ồn đỉnh của chóp, đeo lên xe bus hay văn phòng là tĩnh lặng hoàn toàn. Đệm tai êm ái đeo cả ngày không bị đau tai chút nào.', 1, 'approved', 15, NOW()
),
(
  5, 5, NULL, 'Vũ Đức Thành', 'thanh.vd@gmail.com', '0945678912',
  5, 'Củ sạc nhỏ gọn mà cắm sạc cùng lúc cả MacBook Air và điện thoại không hề bị nóng máy. Hàng Anker thì quá yên tâm về độ bền rồi!', 1, 'approved', 7, NOW()
);

-- 8. Mã khuyến mại & Voucher ưu đãi mặc định
INSERT INTO `coupons` (`id`, `code`, `title`, `discount_type`, `discount_value`, `max_discount`, `min_order_amount`, `quantity`, `used_count`, `start_date`, `end_date`, `status`) VALUES
(1, 'NEXVORA50', 'Giảm ngay 50.000đ cho mọi đơn hàng từ 200k', 'fixed', 50000, NULL, 200000, 100, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(2, 'FLAGSHIP200', 'Ưu đãi siêu phẩm - Giảm 200.000đ điện thoại cao cấp', 'fixed', 200000, NULL, 10000000, 50, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(3, 'PHUKIEN15', 'Giảm 15% tổng đơn phụ kiện & tai nghe (tối đa 150k)', 'percent', 15, 150000, 300000, 80, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active'),
(4, 'FREESHIP', 'Miễn phí vận chuyển hỏa tốc toàn quốc (trừ 30.000đ)', 'fixed', 30000, NULL, 100000, 200, 0, NOW(), DATE_ADD(NOW(), INTERVAL 60 DAY), 'active');

-- Bật lại kiểm tra khóa ngoại sau khi hoàn tất
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================
-- HOÀN TẤT KHỞI TẠO CƠ SỞ DỮ LIỆU ĐẦY ĐỦ 100%!
-- SẴN SÀNG CHẠY THỬ NGHIỆM VÀ NỘP BÀI ĐỒ ÁN!
-- ==============================================================
