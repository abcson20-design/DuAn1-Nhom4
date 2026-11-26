# DỰ ÁN 1: HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ BÁN ĐIỆN THOẠI & PHỤ KIỆN (NEXVORA)

Website thương mại điện tử chuyên nghiệp, xây dựng theo kiến trúc **Mô hình MVC (Model - View - Controller)** bằng **PHP thuần**, giao diện hiện đại phong cách **Dark Cyberpunk** tối ưu bằng **Tailwind CSS**, tích hợp **Cổng thanh toán tự động SePay Webhook**, **Quét mã VietQR 24/7**, **SSE Real-time Events (Độ trễ mili-giây)**, và hệ thống quản trị chuyên sâu.

---

## 🌟 CÁC CÔNG NGHỆ CHÍNH ĐƯỢC TÍCH HỢP

1. **Kiến trúc Backend:** Pure PHP MVC (Không dùng framework nặng nề, phân lớp rõ ràng: Controllers, Models, Views, Services).
2. **Cơ sở dữ liệu:** MySQL (`DuAn1_db`), cấu trúc gồm **16 bảng nghiệp vụ cốt lõi chuẩn hóa 3NF** (kèm bảng thứ 17 `guest_reservations` phục vụ giữ chỗ nhanh của khách vãng lai), kết nối an toàn bằng PDO Prepared Statements:
   - `categories`: Danh mục sản phẩm (Điện thoại, Phụ kiện, Cáp sạc, Tai nghe...)
   - `brands`: Thương hiệu đối tác (Apple, Samsung, Xiaomi, Sony, Anker...)
   - `users`: Tài khoản người dùng (Phân quyền: `customer`, `admin`)
   - `products`: Sản phẩm chính (Tên, giá, mô tả, tồn kho tổng, trạng thái)
   - `product_attributes`: Thông số kỹ thuật chi tiết của sản phẩm
   - `product_galleries`: Album ảnh chi tiết và hình ảnh cấu tạo sản phẩm
   - `product_colors`: Biến thể màu sắc & số lượng tồn kho theo từng màu
   - `product_reviews`: Đánh giá, chấm sao và bình luận của khách hàng
   - `orders`: Đơn hàng (Mã đơn, thông tin nhận hàng, trạng thái, tổng tiền)
   - `order_items`: Chi tiết từng mặt hàng trong đơn (Snapshot giá, số lượng, màu sắc độc lập)
   - `payments`: Lịch sử giao dịch và đối soát thanh toán (SePay Webhook, VietQR, COD)
   - `warranties`: Quản lý bảo hành điện tử theo số IMEI / Serial sản phẩm
   - `contacts`: Hộp thư liên hệ & yêu cầu hỗ trợ khách hàng
   - `coupons`: Hệ thống mã giảm giá & voucher khuyến mại
   - `wishlists`: Danh sách sản phẩm yêu thích theo từng tài khoản
   - `user_carts`: Giỏ hàng bền vững (Persistent Cart) lưu CSDL theo người dùng
   - `guest_reservations`: Đặt giữ chỗ nhanh dành cho khách vãng lai & quét IP chống ghim hàng
3. **Giao diện Frontend:** Tailwind CSS, Font Awesome 6.5, Google Fonts (Inter), SweetAlert2, hiệu ứng Cyberpunk Neon Glow.
4. **Thanh toán Trực tuyến & Webhook Tự Động (Online Payment):**
   - **Cổng thanh toán SePay:** Webhook IPN tự động khớp mã đơn hàng và số tiền thực tế, cập nhật trạng thái thanh toán theo thời gian thực (Real-time). Tiếp nhận xử lý overpayment (chuyển thừa tiền) tự động ghi nhận hoá đơn và lưu vết số tiền chênh lệch.
   - **VietQR Napas 247:** Tạo mã QR động chuẩn Napas kèm số tiền và nội dung chuyển khoản tự động.
   - **Chuyển khoản thủ công:** Hỗ trợ nhập mã giao dịch xác nhận.
   - **Ship COD:** Thanh toán tiền mặt khi nhận hàng với quy trình máy trạng thái giao nhận 1 chiều nghiêm ngặt.
5. **Cơ chế Real-time SSE (Server-Sent Events) theo Mili-giây:**
   - Kênh SSE tại `index.php?url=api/orders/sse` đẩy thông báo trạng thái đơn hàng tức thì giữa màn hình Admin và Client mà không cần F5 lại trang.
   - Tự động cập nhật biến thể màu sắc và tồn kho trên trang chi tiết sản phẩm khi Admin thay đổi dữ liệu trong trang quản trị.
6. **Bảo mật & Tối ưu hóa Toàn diện (Security & Reliability):**
   - **Bảo vệ CSRF:** Áp dụng token ngẫu nhiên cho các form POST và thao tác thay đổi dữ liệu (Đăng nhập, Đăng ký, Đặt hàng, Áp dụng mã giảm giá `apply-coupon`, Thêm/Xóa giỏ hàng, Liên hệ, Đánh giá, Thích/Xóa bình luận, Quản trị Admin). Các API `index.php?url=api/...` dạng POST nhận token qua header `X-CSRF-Token` (được gắn tự động cho mọi lệnh `fetch` trong `views/layouts/header.php`).
   - **Chống Brute-Force Độc Lập Session:** `RateLimiter` lưu bộ đếm vi phạm trên disk theo IP + Account; tự động khóa 15 phút và trả về mã lỗi `HTTP 429 Too Many Requests` nếu nhập sai quá 5 lần liên tiếp (ngay cả khi kẻ tấn công xóa session cookie liên tục).
   - **Header Content-Security-Policy (CSP):** Thiết lập CSP toàn diện bảo vệ hệ thống trước tấn công XSS, clickjacking và chèn mã độc.
   - **Quên Mật Khẩu An Toàn (Tokenized Reset Link):** Sử dụng liên kết đổi mật khẩu chứa token ngẫu nhiên (thời hạn 30 phút), đồng nhất thông báo để chống dò quét tài khoản (Enumeration Prevention). Do dự án chưa tích hợp gửi email, liên kết được ghi vào error log của PHP; **chỉ khi `APP_DEBUG=true`** liên kết mới được hiển thị trên màn hình để tiện demo (không bật khi chạy thật vì ai nhập email người khác cũng lấy được link đổi mật khẩu).
   - **Kiểm Soát Phiên Đăng Nhập Tài Khoản Bị Khóa:** Admin khóa/mở khóa tài khoản khách hàng tại trang **Khách Hàng User** (`index.php?url=admin/users`). Hệ thống kiểm tra trạng thái tài khoản trong CSDL trên mọi request (`verifyActiveUserSession`); lập tức đăng xuất và hủy phiên làm việc nếu tài khoản bị Admin khóa.
   - **Chống Giả Lập Thanh Toán Sandbox:** Khóa triệt để endpoint `simulate` khi `APP_ENV=production`; chỉ khi `APP_ENV` khác `production` **và** `SEPAY_ENV=sandbox` thì duy nhất tài khoản Quản trị viên (`admin_user`) mới có quyền bấm giả lập thanh toán. *(Lưu ý: `.env` mặc định đang để `APP_ENV=production`, muốn demo nút giả lập phải đổi thành `APP_ENV=local`.)*
   - **Bảo Vệ Khóa Bí Mật IPN:** Khóa bí mật SePay IPN được cấu hình độc lập qua `.env`, không ghi cứng trong mã nguồn và không log secret vào error log. Không công khai giá trị secret trong tài liệu và không đẩy tệp `.env` thật lên GitHub (dùng `.env.example`).
   - **Bảo Vệ Thông Tin Cá Nhân (Che PII):** Trang xác nhận đơn hàng `order/success` và `order/track` tự động che mờ SĐT, địa chỉ và tên khách hàng nếu người truy cập là người lạ hoặc ẩn danh; mã đơn hàng dài 8 ký tự hex ngẫu nhiên (`TS` + ymd + 8 ký tự hex) ngăn chặn hoàn toàn việc quét mò đơn hàng.
   - **Chống Race Condition & Overselling:** Khóa dòng `SELECT ... FOR UPDATE` khi tạo đơn hàng, đảm bảo kiểm tra tồn kho chính xác tuyệt đối.
   - **Bảo mật Tải Tệp (Upload):** Cấm định dạng SVG (chống SVG XSS Injection), kiểm tra MIME type thực tế bằng Fileinfo cho cả ảnh và video (tệp sai định dạng bị từ chối kèm thông báo lỗi), chặn thực thi mã nguồn trong thư mục uploads qua `public/uploads/.htaccess`.

---

## 🎯 CÁC NGHIỆP VỤ ĐẶC TRƯNG PHỤC VỤ BUỔI BẢO VỆ ĐỒ ÁN (DEMO 2 TRÌNH DUYỆT)

### 1. Mua Hàng Khi Chưa Đăng Nhập (Guest Pre-Order & Chống Ghim Hàng Qua IP)
* Khi khách vãng lai (chưa đăng nhập) bấm *Thêm vào giỏ hàng* hoặc *Mua ngay*:
  * Hệ thống mở Modal thông minh **"⚡ Đặt Giữ Chỗ Nhanh (Không cần đăng nhập)"** hoặc lựa chọn **"Đăng Nhập Tài Khoản"**.
  * Khách chỉ cần nhập: Họ tên, Số điện thoại (10 số), Địa chỉ nhận hàng và Ghi chú.
  * Thông tin được lưu vào bảng `guest_reservations` và hiển thị cho Admin ở mục **Hộp Thư Liên Hệ → Khách Vãng Lai Đặt Giữ Chỗ** để CSKH gọi điện tư vấn chốt đơn.
  * **Thuật toán quét IP chống ghim hàng ảo:** Hệ thống tự động đếm số lần giữ chỗ từ cùng một địa chỉ IP trong vòng 24 giờ. Mỗi IP chỉ được giữ chỗ tối đa **3 lần**. Khi chạm ngưỡng, hệ thống trả về mã lỗi `HTTP 429` yêu cầu đăng nhập tài khoản User để đảm bảo tính minh bạch của tồn kho.

### 2. Đăng Ký Tài Khoản: Validate Tức Thì (Real-time JS) & Check Trùng SĐT Qua API
* Tại trang Đăng ký tài khoản (`index.php?url=auth/register`):
  * **Validate tức thì từng ô input**: Họ tên $\ge 2$ ký tự, Email định dạng chuẩn regex, Mật khẩu $\ge 6$ ký tự có chứa ký tự đặc biệt (`@`, `#`, `$`, `!`, `%`...), Nhập lại mật khẩu tự động kiểm tra trùng khớp với checklist trực quan.
  * **Kiểm tra trùng số điện thoại thời gian thực**: Ngay khi khách nhập đủ 10 chữ số, hệ thống gọi bất đồng bộ tới API `index.php?url=api/check-phone`. Nếu số điện thoại đã tồn tại trong CSDL `users`, ô nhập lập tức chuyển viền đỏ kèm thông báo lỗi và khoá nút Đăng ký; nếu chưa có, xuất hiện dấu tích xanh hợp lệ.

### 3. Chống Spam Đơn Hàng & Cảnh Báo Gom Đơn Cho Admin
* **Giới hạn số lượng sản phẩm giá trị cao:** Các sản phẩm cao cấp có giá trị từ **15.000.000đ** trở lên (như iPhone 15 Pro, iPhone 16 Pro Max...) được giới hạn đặt tối đa **2 chiếc/đơn hàng** (các sản phẩm khác tối đa 10 chiếc) ở cả giao diện Frontend lẫn tầng Controller (Thêm giỏ, nút +/−, API cập nhật số lượng, Mua ngay) và Model để chống hành vi đầu cơ/spam đơn hàng.
* **Lưu ý dữ liệu mẫu:** Giá sản phẩm trong `sql/database.sql` đang để mức demo (vài nghìn đồng, VD 34.990đ) để test thanh toán SePay bằng số tiền nhỏ. Muốn demo giới hạn 2 chiếc hoặc mã giảm giá có đơn tối thiểu lớn, hãy sửa giá sản phẩm trong trang Admin (VD 34.990.000đ).
* **Cảnh báo gom đơn trên trang Quản trị Admin:**
  * Hệ thống tự động quét và phát hiện các số điện thoại có từ **2 đơn hàng chưa giao** (`pending` hoặc `confirmed`) trong vòng 24 giờ qua.
  * Hiển thị **Banner cảnh báo nổi bật ở đầu trang Quản lý đơn hàng** và **Huy hiệu cảnh báo gom đơn** trên từng dòng đơn hàng giúp Admin nhanh chóng liên hệ khách hàng để gộp chung kiện hàng, tiết kiệm chi phí đóng gói & vận chuyển.

### 4. Giỏ Hàng Shopee: Phân Tách Checkbox & Thông Tin Giao Hàng Mặc Định
* Giỏ hàng hỗ trợ checkbox chọn thanh toán từng sản phẩm cụ thể (không bắt buộc thanh toán toàn bộ giỏ hàng).
* Tính toán tổng tiền, chiết khấu mã giảm giá và phí vận chuyển tự động cập nhật động theo các món được chọn.
* Khi chuyển sang trang Thanh toán (`checkout`), hệ thống tự động điền sẵn Họ tên, SĐT, Địa chỉ mặc định từ tài khoản người dùng đang đăng nhập.

### 5. Máy Trạng Thái Đơn Hàng 1 Chiều (State Machine) & Phân Quyền Hoàn Tất Đơn Hàng
* **Quy trình đơn Ship COD 1 chiều:**
  $$\text{Chờ xác nhận} \rightarrow \text{Đã xác nhận (Đóng gói)} \rightarrow \text{Đang giao hàng (Bàn giao ĐVVC)} \rightarrow \text{Giao hàng thành công}$$
* **Chặn huỷ đơn trái phép:** Khách hàng chỉ có quyền huỷ đơn khi đơn chưa bàn giao vận chuyển (`pending`, `confirmed`). Khi đơn đã chuyển sang `shipping`, nút Huỷ đơn tự động bị khoá.
* **Chỉ Client mới được xác nhận "Đã nhận hàng":** Admin bị chặn không thể tự ý chuyển đơn Ship COD từ `shipping` sang `completed` (kiểm tra ở cả giao diện lẫn server). Khi đơn hàng đang ở trạng thái `shipping`, nút **"Đã Nhận Hàng"** sẽ hiển thị trên tài khoản của khách. Chỉ khi khách bấm xác nhận, đơn mới chính thức hoàn thành.
* **Tự động hoàn thành sau 7 ngày:** Nếu sau 7 ngày kể từ khi xuất kho giao hàng (cột `shipped_at`) mà khách không bấm xác nhận, hệ thống sẽ tự động cập nhật trạng thái đơn sang giao thành công.
* **Mã đơn hàng:** dạng `TS` + `ymd` + 8 ký tự hex (VD: `TS261126A1B2C3D4`). Các trạng thái đơn: `pending` (Chờ xác nhận), `confirmed` (Đã xác nhận), `shipping` (Đang giao), `completed` (Hoàn thành), `cancelled` (Đã hủy), `return_requested` (Chờ duyệt trả hàng), `returned` (Đã trả hàng).

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN

### Bước 1: Khởi động XAMPP
1. Mở **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 dịch vụ: **Apache** và **MySQL**.

### Bước 2: Nạp Cơ Sở Dữ Liệu (Database)
1. Mở trình duyệt web truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Nhấn vào tab **Import** (Nhập).
3. Chọn tệp SQL:
   - `sql/database.sql`: Cấu trúc chuẩn 16 bảng nghiệp vụ cốt lõi (kèm bảng 17 `guest_reservations`) và dữ liệu mẫu thực tế.
4. Bấm nút **Import** ở cuối trang để tự động tạo cơ sở dữ liệu `DuAn1_db`.

### Bước 3: Cấu Hình Biến Môi Trường (.env)
* Sao chép `.env.example` thành `.env` (nếu chưa có) rồi chỉnh các giá trị:
  ```env
  APP_ENV=production        # đổi thành local khi demo nút giả lập thanh toán SePay sandbox
  APP_DEBUG=false           # true: hiện chi tiết lỗi DB + link đặt lại mật khẩu trên màn hình (chỉ dùng khi demo)
  DB_HOST=localhost
  DB_PORT=3306
  DB_NAME=DuAn1_db
  DB_USER=root
  DB_PASS=
  SEPAY_SECRET_KEY=<khóa bí mật lấy trong trang quản trị SePay>
  SEPAY_IPN_SECRET=<chuỗi bí mật IPN do bạn tự đặt>
  ```

### Bước 4: Khởi Chạy Website

#### Cách A: Chạy qua XAMPP Apache (Khuyên dùng)
* Đặt thư mục dự án vào `C:\xampp\htdocs\DỰ ÁN 1\DuAn1-Nhom4`.
* Mở trình duyệt truy cập:
  ```
  http://localhost/DỰ%20ÁN%201/DuAn1-Nhom4/
  ```

#### Cách B: Chạy bằng máy chủ PHP Built-in Server (Bảo mật qua router.php)
1. Mở PowerShell hoặc Terminal tại thư mục dự án:
   ```powershell
   cd "C:\xampp\htdocs\DỰ ÁN 1\DuAn1-Nhom4"
   C:\xampp\php\php.exe -S localhost:8000 router.php
   ```
2. Mở trình duyệt truy cập: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 TÀI KHOẢN ĐĂNG NHẬP HỆ THỐNG

### 1. Tài Khoản Quản Trị Viên (Admin)
* **Đường dẫn đăng nhập quản trị:** `index.php?url=admin/login`
* **Email:** `duan1@gmail.com`
* **Mật khẩu:** `123456@`

### 2. Tài Khoản Khách Hàng (User Thử Nghiệm)
* **Đường dẫn đăng nhập:** `index.php?url=auth/login`
* **Email:** `khachhang@gmail.com`
* **Mật khẩu:** `User@123`
* **SĐT:** `0901234567` (tài khoản này được tạo sẵn trong `sql/database.sql`)
*(Hoặc có thể tự đăng ký tài khoản mới trực tiếp trên trang Đăng ký `index.php?url=auth/register`)*.

---

## 🎬 KỊCH BẢN THUYẾT TRÌNH BẢO VỆ ĐỒ ÁN (DEMO 10 PHÚT)

1. **Chuẩn bị 2 cửa sổ trình duyệt (Chia đôi màn hình):**
   - **Trình duyệt trái:** Mở giao diện Khách hàng (User đã đăng nhập hoặc khách vãng lai).
   - **Trình duyệt phải:** Mở giao diện Quản trị viên (Admin đã đăng nhập).
2. **Demo luồng mua hàng & Đặt giữ chỗ:**
   - Thử nghiệm chức năng Mua nhanh không cần đăng nhập $\rightarrow$ Quét IP kiểm soát 3 lần giữ chỗ.
   - Thử nghiệm đăng ký tài khoản $\rightarrow$ Validate tức thì JS và check trùng SĐT qua API.
   - Chọn sản phẩm vào giỏ $\rightarrow$ Dùng checkbox chọn món cần mua $\rightarrow$ Đặt hàng COD hoặc SePay.
3. **Demo Real-time SSE:**
   - Tại màn hình Admin (bên phải): Nhấn xác nhận đơn hàng hoặc chuyển sang Đang giao.
   - Quan sát màn hình User (bên trái): Thông báo SweetAlert2 xuất hiện và thẻ đơn hàng tự động cập nhật trạng thái theo thời gian thực (độ trễ mili-giây) mà **hoàn toàn không cần F5**.
4. **Demo State Machine & Client xác nhận nhận hàng:**
   - Thao tác tại Admin: Chỉ có thể duyệt tới bước xuất kho `shipping`, nút hoàn thành bị chặn với đơn COD (gửi request trực tiếp cũng bị server từ chối).
   - Thao tác tại User: Khách bấm nút **"Đã Nhận Hàng"** $\rightarrow$ Đơn hàng chính thức chuyển sang `completed`.
5. **Demo Cảnh báo gom đơn & Biến thể:**
   - Cho 1 khách hàng đặt 2 đơn liên tiếp $\rightarrow$ Trang Admin lập tức hiển thị Banner cảnh báo gom đơn.
   - Admin thêm 1 màu mới cho sản phẩm $\rightarrow$ Trang chi tiết sản phẩm phía khách hàng cập nhật biến thể ngay lập tức.
