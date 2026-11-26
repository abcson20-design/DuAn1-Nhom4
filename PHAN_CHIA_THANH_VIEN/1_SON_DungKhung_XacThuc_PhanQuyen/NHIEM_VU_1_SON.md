# NHIỆM VỤ 1: SƠN (TRƯỞNG NHÓM)

* **Thành viên:** Sơn (Lê Văn Sơn - Trưởng nhóm)
* **Nhánh Git:** `dev-son` & `main`
* **File click đẩy code:** `DAY_CODE_TRUONG_NHOM_SON.bat`
* **Nhiệm vụ chính thức:** **Dựng khung hệ thống, Xác thực tài khoản, Phân quyền & Ghép nối mã nguồn**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Router** | `index.php` | Định tuyến toàn bộ URL hệ thống |
| **Cấu hình** | `config/config.php` & `config/database.php` | BASE_URL, kết nối PDO MySQL chuẩn UTF-8 |
| **Core Base**| `controllers/BaseController.php` & `models/BaseModel.php` | Hàm render view, json response, PDO instance |
| **Bảo mật** | `services/Csrf.php` | Tạo và xác thực CSRF Token chống tấn công giả mạo |
| **Controller**| `controllers/AuthController.php` | Đăng ký, đăng nhập, đổi mật khẩu, hồ sơ cá nhân |
| **Model** | `models/User.php` | Quản lý bảng `users`, mã hóa password bcrypt |
| **Giao diện** | `views/layouts/header.php` & `views/layouts/footer.php` | Header, Footer chung, Menu điều hướng, Modal giỏ hàng |
| **Giao diện** | `views/auth/login.php`, `views/auth/register.php`,... | Form đăng nhập, form đăng ký kiểm tra dữ liệu |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Kiến trúc MVC:** Trình bày rõ ràng luồng dữ liệu từ Request -> Router (`index.php`) -> Controller gọi Model lấy CSDL -> Render dữ liệu sang View.
2. **Cơ chế mã hóa:** Dùng hàm `password_hash()` thuật toán BCRYPT, kiểm tra bằng `password_verify()`.
3. **Phân quyền người dùng:** Cột `role` trong bảng `users` (`customer` và `admin`). Khách chưa đăng nhập bị chặn không cho đặt hàng (`requireUser()`).

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_TRUONG_NHOM_SON.bat`** để tự động commit và đẩy lên nhánh `dev-son` và `main`.