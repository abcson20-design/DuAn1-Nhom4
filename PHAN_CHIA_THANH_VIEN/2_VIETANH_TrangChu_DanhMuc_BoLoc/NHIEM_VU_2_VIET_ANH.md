# NHIỆM VỤ 2: VIỆT ANH

* **Thành viên:** Việt Anh
* **Nhánh Git:** `dev-vietanh`
* **File click đẩy code:** `DAY_CODE_THANH_VIEN_VIETANH.bat`
* **Nhiệm vụ chính thức:** **Lập trình Trang chủ, Danh mục & Bộ lọc sản phẩm**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Controller**| `controllers/HomeController.php` | Đổ dữ liệu trang chủ: Banner slider, Sản phẩm nổi bật, Hàng mới |
| **Controller**| `controllers/ProductController.php` | Lọc sản phẩm theo danh mục, thương hiệu, mức giá, tìm kiếm |
| **Model** | `models/Category.php` | Quản lý danh mục (Điện thoại, Phụ kiện, Cáp sạc,...) |
| **Model** | `models/Brand.php` | Quản lý hãng (Apple, Samsung, Xiaomi, Sony,...) |
| **Model** | `models/Product.php` | Hàm lấy danh sách lọc `getFilteredProducts()` |
| **Giao diện** | `views/home/index.php` | Trang chủ Dark Cyberpunk, Slider banner tự động |
| **Giao diện** | `views/product/index.php` | Lưới sản phẩm, Sidebar bộ lọc đa tiêu chí, phân trang |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Bộ lọc nhiều tiêu chí:** Câu lệnh SQL động kết hợp nhiều điều kiện `AND` dùng Prepared Statement chống SQL Injection.
2. **Phân trang sản phẩm:** Tính `LIMIT` và `OFFSET` theo trang hiện tại (12 sản phẩm/trang).
3. **Slider tự động:** JavaScript tự động trượt slide sau 4 giây, hỗ trợ bấm nút prev/next.

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_THANH_VIEN_VIETANH.bat`** để tự động commit và đẩy lên nhánh `dev-vietanh`.