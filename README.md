# DỰ ÁN 1: HỆ THỐNG THƯƠNG MẠI ĐIỆN TỬ BÁN ĐIỆN THOẠI & PHỤ KIỆN (NEXVORA)

Dự án website thương mại điện tử chuyên nghiệp, xây dựng theo kiến trúc **Mô hình MVC (Model - View - Controller)** bằng **PHP thuần**, giao diện hiện đại phong cách **Dark Cyberpunk** tối ưu bằng **Tailwind CSS**, tích hợp **Cổng thanh toán tự động SePay Webhook**, **Quét mã VietQR 24/7**, và hệ thống quản trị chuyên sâu.

---

## 🌟 CÁC CÔNG NGHỆ CHÍNH ĐƯỢC TÍCH HỢP

1. **Kiến trúc Backend:** Pure PHP MVC (Không dùng framework nặng nề, phân lớp rõ ràng: Controllers, Models, Views, Services).
2. **Cơ sở dữ liệu:** MySQL (`DuAn1_db`), cấu trúc 13 bảng chuẩn hóa 3NF, kết nối an toàn bằng PDO Prepared Statements.
3. **Giao diện Frontend:** Tailwind CSS, Font Awesome 6.5, Google Fonts (Inter), SweetAlert2, hiệu ứng Cyberpunk Neon Glow.
4. **Thanh toán Trực tuyến (Online Payment):**
   - **Cổng thanh toán SePay:** Webhook tự động khớp mã đơn hàng và số tiền thực tế, cập nhật trạng thái thanh toán theo thời gian thực (Real-time).
   - **VietQR Napas 247:** Tạo mã QR động kèm số tiền và nội dung chuyển khoản tự động.
   - **Chuyển khoản thủ công:** Hỗ trợ nhập mã giao dịch xác nhận.
   - **Ship COD:** Thanh toán tiền mặt khi nhận hàng kèm xác thực quy trình giao nhận.
5. **Bảo mật & Tối ưu hóa Toàn diện (Security & Reliability):**
   - **Bảo vệ CSRF:** Áp dụng token ngẫu nhiên cho **100% form POST và thao tác thay đổi dữ liệu** (Đăng nhập, Đăng ký, Đặt hàng, Thêm giỏ hàng, Liên hệ, Đánh giá, Quản trị Admin).
   - **Chống Brute-Force Độc Lập Session:** `RateLimiter` lưu bộ đếm vi phạm trên disk theo IP + Account; tự động khóa 15 phút và trả về mã lỗi `HTTP 429 Too Many Requests` nếu nhập sai quá 5 lần liên tiếp (ngay cả khi kẻ tấn công xóa session cookie liên tục).
   - **Cookie Session An Toàn:** Tự động thiết lập `HttpOnly`, `SameSite=Lax`, `Secure` cho cookie phiên làm việc.
   - **Bảo vệ Thông Tin Cá Nhân (Che PII):** Trang xác nhận đơn hàng `order/success` và `order/track` tự động che mờ SĐT, địa chỉ và tên khách hàng nếu người truy cập là người lạ hoặc ẩn danh; mã đơn hàng dài 8 ký tự hex ngẫu nhiên (`TS` + ymd + 8 ký tự hex) ngăn chặn hoàn toàn việc quét mò đơn hàng.
   - **Bảo vệ Thao Tác Thanh Toán:** Chỉ chủ đơn hàng, khách vừa hoàn tất đặt đơn trong phiên hoặc Admin mới có quyền xác nhận gửi thông tin chuyển khoản (`confirmPayment`).
   - **Chống Race Condition & Overselling:** Khóa dòng `SELECT ... FOR UPDATE` khi tạo đơn hàng, đảm bảo kiểm tra tồn kho chính xác tuyệt đối.
   - **Giỏ hàng POST-Only:** Chuyển toàn bộ thao tác thêm hàng vào giỏ và mua ngay sang phương thức POST có kèm CSRF Token.
   - **Bảo mật Tải Tệp (Upload):** Cấm định dạng SVG (chống SVG XSS Injection), kiểm tra MIME type thực tế bằng Fileinfo cho cả ảnh và video, chặn thực thi mã nguồn trong thư mục uploads qua `.htaccess`.
   - **Bảo vệ File Nhạy Cảm & Router php -S:** Chặn truy cập trực tiếp `.sql`, `.md`, `.env`, `.json`, `.log` và các thư mục mã nguồn (`config/`, `models/`, `views/`, `controllers/`, `services/`, `sql/`) trên cả Apache và PHP Built-in Server (qua `router.php`).
   - **Biến Môi Trường (.env):** Đưa toàn bộ cấu hình nhạy cảm (Database, STK ngân hàng, Secret Key SePay) ra tệp `.env`.
   - **Dọn Sạch DDL Khỏi Model:** Loại bỏ 100% các câu lệnh `CREATE TABLE` / `ALTER TABLE` trong constructor của các model, tối ưu hóa tối đa tốc độ truy vấn.
6. **Tính năng độc quyền & Trải nghiệm người dùng:**
   - **Mã giảm giá (Coupons):** Tự động áp dụng, kiểm tra giá trị đơn hàng tối thiểu theo thời gian thực, hoàn lượt sử dụng khi hủy đơn.
   - **Hệ thống Xóa mềm (Soft Delete):** Áp dụng cho Sản phẩm, Danh mục, Thương hiệu, Đơn hàng; có Thùng rác khôi phục an toàn mà không làm sai lệch thống kê doanh thu.
   - **Hủy đơn & Hoàn tiền:** Khách hàng chủ động hủy đơn khi chưa giao; tự động thu thập thông tin tài khoản ngân hàng để hoàn tiền nếu đã thanh toán trước.
   - **Trình nghe thử âm thanh ảo (Hi-Res Audio Demo):** Trải nghiệm dải Bass, Treble, Vocal và mô phỏng khử tiếng ồn ANC.
   - **Kiểm tra độ tương thích thiết bị (Compatibility Checker)** trên từng trang chi tiết sản phẩm.
   - **Dashboard Admin phân tích doanh thu:** Biểu đồ, top sản phẩm bán chạy, phân bổ danh mục, quản lý bảo hành điện tử theo IMEI/Serial.

---

## 🚀 HƯỚNG DẪN CÀI ĐẶT & CHẠY DỰ ÁN

### Bước 1: Khởi động XAMPP
1. Mở **XAMPP Control Panel**.
2. Nhấn nút **Start** ở cả 2 dịch vụ: **Apache** và **MySQL**.

### Bước 2: Nạp Cơ Sở Dữ Liệu (Database)
1. Mở trình duyệt web truy cập: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Nhấn vào tab **Import** (Nhập).
3. Chọn tệp SQL:
   - `sql/database.sql`: Đầy đủ 13 bảng cấu trúc kèm 5 sản phẩm mẫu thực tế chất lượng cao.
4. Bấm nút **Import** ở cuối trang để tự động tạo cơ sở dữ liệu `DuAn1_db`.

### Bước 3: Cấu Hình Biến Môi Trường (.env)
* Tệp `.env` đã được cấu hình sẵn tại thư mục gốc của dự án.
* Bạn có thể điều chỉnh thông tin kết nối Database, STK ngân hàng và SePay API Key trong tệp `.env`.

### Bước 4: Truy cập Website

#### Cách A: Chạy qua XAMPP Apache (Khuyên dùng)
* Đặt thư mục dự án vào `C:\xampp\htdocs\DỰ ÁN 1\BanHangOnline` (hoặc cấu hình VirtualHost tương ứng).
* Truy cập: `http://localhost/DỰ%20ÁN%201/BanHangOnline/` (hoặc đường dẫn thư mục của bạn).

#### Cách B: Chạy bằng máy chủ PHP Built-in Server (Bảo mật qua router.php)
1. Mở PowerShell hoặc Terminal tại thư mục dự án:
   ```powershell
   cd "C:\xampp\htdocs\DỰ ÁN 1\BanHangOnline"
   C:\xampp\php\php.exe -S localhost:8000 router.php
   ```
   *(Lưu ý: Luôn truyền tham số `router.php` để kích hoạt cơ chế bảo vệ tệp tin nhạy cảm và điều hướng URL chuẩn).*
2. Mở trình duyệt truy cập: **[http://localhost:8000](http://localhost:8000)**

---

## 🔑 TÀI KHOẢN ĐĂNG NHẬP HỆ THỐNG

### 1. Tài Khoản Quản Trị Viên (Admin)
* **Đường dẫn đăng nhập quản trị:** `http://localhost:8000/index.php?url=admin/login`
* **Email:** `duan1@gmail.com`
* **Mật khẩu:** `123456@`

### 2. Tài Khoản Khách Hàng (User Thử Nghiệm)
* Khách hàng có thể đăng ký tài khoản mới trực tiếp trên website qua trang **Đăng ký** (`index.php?url=auth/register`).

---

## 💳 CẤU HÌNH THANH TOÁN (SEPAY & VIETQR)
Bạn có thể cấu hình tài khoản ngân hàng và cổng SePay tại tệp `.env`:
* `BANK_ID`: Mã ngân hàng (ví dụ: `VCB`, `MB`, `TCB`, `ACB`, `ICB`...)
* `BANK_ACCOUNT_NO`: Số tài khoản nhận tiền.
* `BANK_ACCOUNT_NAME`: Tên chủ tài khoản (Viết hoa không dấu).
* `SEPAY_MERCHANT_ID`: Merchant ID SePay.
* `SEPAY_SECRET_KEY`: Secret Key SePay.
* `SEPAY_IPN_SECRET`: Khóa bí mật IPN SePay.
