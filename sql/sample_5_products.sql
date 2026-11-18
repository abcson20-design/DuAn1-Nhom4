-- ==============================================================
-- DỰ ÁN 1: HỆ THỐNG WEBSITE BÁN ĐIỆN THOẠI & PHỤ KIỆN (NEXVORA)
-- TẬP DỮ LIỆU 5 SẢN PHẨM MẪU CHUẨN ĐẸP 100% ĐỂ NẠP VÀO DATABASE
-- CƠ SỞ DỮ LIỆU: DuAn1_db
-- ==============================================================
-- DANH SÁCH 5 SẢN PHẨM:
-- 1. iPhone 16 Pro Max 256GB VN/A (Apple - Danh mục Điện thoại)
-- 2. Samsung Galaxy S24 Ultra 5G 256GB (Samsung - Danh mục Điện thoại)
-- 3. Xiaomi 14T Pro 5G 512GB Leica (Xiaomi - Danh mục Điện thoại)
-- 4. Tai nghe Sony WH-1000XM5 Chống Ồn ANC (Sony - Danh mục Tai nghe Bluetooth)
-- 5. Củ sạc nhanh Anker Prime 67W GaN 3 Cổng (Anker - Danh mục Củ sạc & Cáp)
-- ==============================================================

USE `DuAn1_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- Làm sạch bảng sản phẩm và đánh giá cũ (nếu muốn nạp mới)
DELETE FROM `product_reviews` WHERE `product_id` IN (1, 2, 3, 4, 5);
DELETE FROM `products` WHERE `id` IN (1, 2, 3, 4, 5);

-- --------------------------------------------------------------
-- 1. THÊM 5 SẢN PHẨM MẪU VÀO BẢNG `products`
-- --------------------------------------------------------------
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

-- --------------------------------------------------------------
-- 2. THÊM 5 ĐÁNH GIÁ MẪU VÀO BẢNG `product_reviews` (Cho giao diện sống động)
-- --------------------------------------------------------------
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

-- --------------------------------------------------------------
-- 3. ĐỒNG BỘ BẢNG THUỘC TÍNH (product_attributes) CHO ĐIỆN THOẠI & PHỤ KIỆN
-- --------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_attributes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'Khóa ngoại trỏ đến products(id)',
  `name` varchar(100) NOT NULL,
  `value` text NOT NULL,
  `group_name` varchar(50) DEFAULT 'Thông số kỹ thuật',
  `sort_order` int(11) DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `fk_attr_prod` (`product_id`),
  CONSTRAINT `fk_attr_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM `product_attributes` WHERE `product_id` IN (1, 2, 3, 4, 5);

INSERT INTO `product_attributes` (`product_id`, `name`, `value`, `group_name`, `sort_order`) VALUES
(1, 'Màn hình', '6.9 inch, Super Retina XDR OLED, 120Hz ProMotion, 2000 nits', 'Màn hình', 1),
(1, 'Hệ điều hành', 'iOS 18 (Hỗ trợ Apple Intelligence)', 'Phần mềm', 2),
(1, 'Vi xử lý (CPU)', 'Apple A18 Pro 6 nhân (3nm thế hệ thứ 2)', 'Hiệu năng', 3),
(1, 'Đồ họa (GPU)', 'Apple GPU 6 nhân hỗ trợ Ray Tracing', 'Hiệu năng', 4),
(1, 'RAM', '8 GB', 'Hiệu năng', 5),
(1, 'Bộ nhớ trong', '256 GB chuẩn NVMe siêu tốc', 'Bộ nhớ', 6),
(1, 'Camera sau', 'Chính 48MP OIS + Góc siêu rộng 48MP + Tele 12MP 5x quang', 'Camera', 7),
(1, 'Camera trước', '12MP TrueDepth, f/1.9, Autofocus', 'Camera', 8),
(1, 'Pin & Sạc', '4.685 mAh, Sạc nhanh 50% trong 30 phút, MagSafe 25W', 'Pin & Nguồn', 9),
(1, 'Kháng nước / bụi', 'Chuẩn IP68 (chịu nước sâu 6m tối đa 30 phút)', 'Thiết kế', 10),

(2, 'Màn hình', '6.8 inch, Dynamic AMOLED 2X, 120Hz thích ứng, QHD+, 2600 nits', 'Màn hình', 1),
(2, 'Hệ điều hành', 'Android 14, One UI 6.1 (Tích hợp Galaxy AI)', 'Phần mềm', 2),
(2, 'Vi xử lý (CPU)', 'Snapdragon 8 Gen 3 for Galaxy (4nm) 8 nhân', 'Hiệu năng', 3),
(2, 'RAM', '12 GB LPDDR5X', 'Hiệu năng', 4),
(2, 'Bộ nhớ trong', '256 GB chuẩn UFS 4.0', 'Bộ nhớ', 5),
(2, 'Camera sau', 'Chính 200MP OIS + Siêu rộng 12MP + Tele 50MP 5x + Tele 10MP 3x', 'Camera', 6),
(2, 'Pin & Sạc', '5.000 mAh, Sạc siêu nhanh 45W, Sạc không dây 15W', 'Pin & Nguồn', 7),
(2, 'Bút cảm ứng', 'Bút S-Pen tích hợp trong thân máy, độ trễ 2.8ms', 'Tính năng', 8),

(3, 'Màn hình', '6.67 inch, AMOLED 144Hz, 1.5K, 4000 nits, Dolby Vision', 'Màn hình', 1),
(3, 'Hệ điều hành', 'Xiaomi HyperOS (Android 14)', 'Phần mềm', 2),
(3, 'Vi xử lý (CPU)', 'MediaTek Dimensity 9300+ (4nm) 3.4GHz', 'Hiệu năng', 3),
(3, 'RAM', '12 GB LPDDR5X', 'Hiệu năng', 4),
(3, 'Bộ nhớ trong', '512 GB UFS 4.0', 'Bộ nhớ', 5),
(3, 'Camera sau', 'Chính Leica 50MP OIS + Tele Leica 50MP + Siêu rộng 12MP', 'Camera', 6),
(3, 'Pin & Sạc', '5.000 mAh, Sạc siêu tốc HyperCharge 120W (19 phút)', 'Pin & Nguồn', 7),

(4, 'Kiểu tai nghe', 'Chụp tai (Over-ear) không dây chống ồn chủ động', 'Thiết kế', 1),
(4, 'Công nghệ chống ồn', 'Active Noise Cancelling (ANC) với 2 chip V1 & QN1, 8 micro', 'Âm thanh', 2),
(4, 'Màng loa (Driver)', 'Driver 30mm sợi carbon gia cố siêu nhẹ', 'Âm thanh', 3),
(4, 'Chuẩn âm thanh', 'Hi-Res Audio, Hi-Res Audio Wireless, LDAC, DSEE Extreme', 'Âm thanh', 4),
(4, 'Kết nối không dây', 'Bluetooth 5.2 (Kết nối 2 thiết bị cùng lúc)', 'Kết nối', 5),
(4, 'Thời lượng pin', '30 giờ (bật ANC), 40 giờ (tắt ANC)', 'Pin', 6),
(4, 'Cổng sạc & cắm dây', 'Type-C + Jack tai nghe 3.5mm tháo rời', 'Kết nối', 7),

(5, 'Công nghệ sạc', 'Anker GaNPrime™, PowerIQ 4.0, Phân bổ dòng điện thông minh', 'Công nghệ', 1),
(5, 'Tổng công suất tối đa', '67W', 'Công suất', 2),
(5, 'Số lượng cổng ra', '3 cổng (2 x USB-C + 1 x USB-A)', 'Cổng cắm', 3),
(5, 'Công suất cổng Type-C', 'Tối đa 67W (hỗ trợ sạc MacBook Air & iPhone 16)', 'Công suất', 4),
(5, 'Tính năng an toàn', 'ActiveShield 2.0 cảm biến nhiệt độ hơn 3 triệu lần/ngày', 'An toàn', 5);

-- --------------------------------------------------------------
-- 4. ĐỒNG BỘ BẢNG ALBUM ẢNH (product_galleries)
-- --------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_galleries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `image_url` varchar(500) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `tag` varchar(50) DEFAULT 'Ngoại quan',
  `description` text DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_gallery_prod` (`product_id`),
  CONSTRAINT `fk_gallery_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DELETE FROM `product_galleries` WHERE `product_id` IN (1, 2, 3, 4, 5);

INSERT INTO `product_galleries` (`product_id`, `image_url`, `title`, `tag`, `description`, `sort_order`) VALUES
(1, 'https://images.unsplash.com/photo-1695048133142-1a20484d2569?w=800&auto=format&fit=crop&q=80', 'Khung viền Titan sa mạc đẳng cấp', 'Ngoại hình', 'Chất liệu titan cấp 5 siêu nhẹ, bền bỉ', 1),
(1, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop', 'Màn hình Super Retina XDR 6.9 inch', 'Màn hình', 'Tần số quét thích ứng ProMotion 120Hz', 2),
(1, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop', 'Cụm Camera Fusion 48MP', 'Camera AI', 'Cảm biến quad-pixel thế hệ 2, quay 4K 120fps', 3),

(2, 'https://images.unsplash.com/photo-1610945265064-0e34e5519bbf?w=800&auto=format&fit=crop&q=80', 'Khung viền Titanium & Bút S-Pen', 'Ngoại hình', 'Thiết kế phẳng tích hợp bút S-Pen quyền năng', 1),
(2, 'https://images.unsplash.com/photo-1556656793-08538906a9f8?w=800&auto=format&fit=crop', 'Màn hình Dynamic AMOLED 2X 6.8 inch', 'Màn hình', 'Kính Corning Gorilla Armor chống chói 75%', 2),

(3, 'https://images.unsplash.com/photo-1598327105666-5b89351aff97?w=800&auto=format&fit=crop&q=80', 'Thiết kế Deco kim loại sang trọng', 'Ngoại hình', 'Khung hợp kim nhôm CNC viền siêu mỏng 1.9mm', 1),
(3, 'https://images.unsplash.com/photo-1617788138017-80ad40651399?w=800&auto=format&fit=crop', 'Cụm 3 Camera Leica Summilux 50MP', 'Camera Leica', 'Cảm biến ảnh Light Fusion 900 dải động cực cao', 2),

(4, 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=800&auto=format&fit=crop&q=80', 'Thiết kế công thái học êm ái', 'Thiết kế', 'Đệm da vừa vặn mềm mại giảm áp lực lên đầu', 1),
(4, 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=800&auto=format&fit=crop', '2 vi xử lý V1 & QN1 khử ồn chủ động', 'Chống ồn', 'Tự động thích ứng môi trường Auto NC Optimizer', 2),

(5, 'https://images.unsplash.com/photo-1583863788434-e58a36330cf0?w=800&auto=format&fit=crop&q=80', 'Kích thước siêu nhỏ gọn GaNPrime', 'Thiết kế', 'Nhỏ hơn 51% so với củ sạc 67W của Apple', 1),
(5, 'https://images.unsplash.com/photo-1518770660439-4636190af475?w=800&auto=format&fit=crop', 'Công nghệ bán dẫn GaN thế hệ mới', 'Công nghệ', 'Hiệu suất truyền tải điện 95%, tỏa nhiệt cực thấp', 2);

-- --------------------------------------------------------------
-- 5. ĐỒNG BỘ BẢNG MÀU SẮC BIẾN THỂ (product_colors)
-- --------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `product_colors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `color_name` varchar(100) NOT NULL,
  `color_image` varchar(500) DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 50,
  PRIMARY KEY (`id`),
  KEY `fk_color_prod` (`product_id`),
  CONSTRAINT `fk_color_prod` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
-- HOÀN TẤT NẠP DỮ LIỆU ĐẦY ĐỦ VÀO DATABASE CHO 5 SẢN PHẨM MẪU!
-- ==============================================================
