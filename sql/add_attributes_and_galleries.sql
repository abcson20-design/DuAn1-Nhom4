-- ==============================================================
-- DỰ ÁN 1: NÂNG CẤP DATABASE VỚI BẢNG THUỘC TÍNH (ATTRIBUTES) & ALBUM (GALLERIES)
-- CƠ SỞ DỮ LIỆU: DuAn1_db
-- MỤC ĐÍCH: 
--   1. Phân biệt rõ thuộc tính Điện thoại (Màn hình, CPU, RAM, Camera...) và Tai nghe/Phụ kiện (Bluetooth, ANC, Pin, Driver...)
--   2. Quản lý Album ảnh chi tiết từng góc chụp / cấu tạo
--   3. Quản lý Biến thể màu sắc & Tồn kho riêng từng màu
-- ==============================================================

USE `DuAn1_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------------
-- BƯỚC 1: TẠO 3 BẢNG THUỘC TÍNH, ALBUM ẢNH & MÀU SẮC BIẾN THỂ
-- --------------------------------------------------------------

-- 1. Bảng `product_attributes` (Thuộc tính sản phẩm)
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `name` varchar(100) NOT NULL COMMENT 'Tên thuộc tính (Màn hình, CPU, RAM, Bluetooth, Chống ồn ANC...)',
  `value` text NOT NULL COMMENT 'Giá trị thuộc tính',
  `group_name` varchar(50) DEFAULT 'Thông số kỹ thuật' COMMENT 'Nhóm thuộc tính: Màn hình, Hiệu năng, Âm thanh, Kết nối...',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  PRIMARY KEY (`id`),
  KEY `fk_attr_prod` (`product_id`),
  CONSTRAINT `fk_attr_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng thuộc tính thông số sản phẩm (Attributes)';

-- 2. Bảng `product_galleries` (Album ảnh sản phẩm)
CREATE TABLE IF NOT EXISTS `product_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `image_url` varchar(500) NOT NULL COMMENT 'Đường dẫn ảnh trong album',
  `title` varchar(255) DEFAULT NULL COMMENT 'Tiêu đề góc chụp / Bộ phận',
  `tag` varchar(50) DEFAULT 'Ngoại quan' COMMENT 'Tag phân loại: Ngoại hình, Màn hình, Âm thanh, Camera AI...',
  `description` text DEFAULT NULL COMMENT 'Mô tả chi tiết ảnh / Linh kiện',
  `sort_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị ảnh',
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_gallery_prod` (`product_id`),
  CONSTRAINT `fk_gallery_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng album ảnh chi tiết sản phẩm (Galleries/Albums)';

-- 3. Bảng `product_colors` (Biến thể màu sắc & tồn kho)
CREATE TABLE IF NOT EXISTS `product_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `color_name` varchar(100) NOT NULL COMMENT 'Tên màu sắc (VD: Titan Sa Mạc, Đen Cổ Điển...)',
  `color_image` varchar(500) DEFAULT NULL COMMENT 'Ảnh đại diện riêng của màu này',
  `quantity` int(11) NOT NULL DEFAULT 50 COMMENT 'Số lượng tồn kho của màu này',
  PRIMARY KEY (`id`),
  KEY `fk_color_prod` (`product_id`),
  CONSTRAINT `fk_color_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Bảng biến thể màu sắc và tồn kho theo màu';

-- --------------------------------------------------------------
-- BƯỚC 2: NẠP DỮ LIỆU THUỘC TÍNH (ATTRIBUTES) CHO 5 SẢN PHẨM MẪU
-- (Phân biệt rạch ròi giữa Điện thoại vs Phụ kiện/Tai nghe)
-- --------------------------------------------------------------
DELETE FROM `product_attributes` WHERE `product_id` IN (1, 2, 3, 4, 5);

-- THUỘC TÍNH ĐIỆN THOẠI 1: iPhone 16 Pro Max 256GB
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
(1, 'Chất liệu', 'Khung Titan cấp 5, Kính Ceramic Shield thế hệ mới', 'Thiết kế', 12);

-- THUỘC TÍNH ĐIỆN THOẠI 2: Samsung Galaxy S24 Ultra 5G
INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
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
(2, 'Chất liệu', 'Khung Titanium nguyên khối, Kính Corning Gorilla Armor', 'Thiết kế', 12);

-- THUỘC TÍNH ĐIỆN THOẠI 3: Xiaomi 14T Pro 5G Leica
INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
(3, 'Màn hình', '6.67 inch, AMOLED 144Hz, 1.5K, 4000 nits, Dolby Vision', 'Màn hình & Hiển thị', 1),
(3, 'Hệ điều hành', 'Xiaomi HyperOS (Android 14)', 'Phần mềm', 2),
(3, 'Vi xử lý (CPU)', 'MediaTek Dimensity 9300+ (4nm) 3.4GHz', 'Hiệu năng', 3),
(3, 'RAM', '12 GB LPDDR5X', 'Hiệu năng', 4),
(3, 'Bộ nhớ trong', '512 GB UFS 4.0', 'Bộ nhớ', 5),
(3, 'Camera sau', 'Chính Leica 50MP OIS + Tele Leica 50MP + Siêu rộng 12MP', 'Camera', 6),
(3, 'Camera trước', '32MP HDR, quay 4K', 'Camera', 7),
(3, 'Pin & Sạc', '5.000 mAh, Sạc siêu tốc HyperCharge 120W (19 phút đầy pin)', 'Pin & Nguồn', 8),
(3, 'Kháng nước / bụi', 'IP68', 'Thiết kế', 9);

-- THUỘC TÍNH PHỤ KIỆN / TAI NGHE 4: Sony WH-1000XM5 ANC (Thuộc tính chuyên biệt cho Âm thanh)
INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
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
(4, 'Tương thích', 'iPhone, iPad, Android, Windows, macOS qua Bluetooth hoặc 3.5mm', 'Tương thích', 12);

-- THUỘC TÍNH PHỤ KIỆN / CỦ SẠC 5: Củ sạc nhanh Anker Prime 67W GaN (Thuộc tính chuyên biệt cho Nguồn sạc)
INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
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

-- --------------------------------------------------------------
-- BƯỚC 3: NẠP DỮ LIỆU ALBUM ẢNH (GALLERIES) CHO 5 SẢN PHẨM
-- --------------------------------------------------------------
DELETE FROM `product_galleries` WHERE `product_id` IN (1, 2, 3, 4, 5);

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

-- --------------------------------------------------------------
-- BƯỚC 4: NẠP DỮ LIỆU MÀU SẮC BIẾN THỂ (PRODUCT_COLORS) CHO 5 SẢN PHẨM
-- --------------------------------------------------------------
DELETE FROM `product_colors` WHERE `product_id` IN (1, 2, 3, 4, 5);

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

-- Bật lại kiểm tra khóa ngoại
SET FOREIGN_KEY_CHECKS = 1;

-- ==============================================================
-- HOÀN TẤT THIẾT LẬP BẢNG THUỘC TÍNH, ALBUM ẢNH & MÀU SẮC BIẾN THỂ!
-- ==============================================================
