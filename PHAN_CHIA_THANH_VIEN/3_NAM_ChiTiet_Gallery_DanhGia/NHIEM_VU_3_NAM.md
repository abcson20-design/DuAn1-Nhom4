# NHIỆM VỤ 3: NAM

* **Thành viên:** Nam
* **Nhánh Git:** `dev-nam`
* **File click đẩy code:** `DAY_CODE_THANH_VIEN_NAM.bat`
* **Nhiệm vụ chính thức:** **Lập trình Chi tiết sản phẩm, Gallery ảnh & Đánh giá**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Controller**| `controllers/ProductController.php` (`detail`, `review`) | Đọc chi tiết sản phẩm theo slug, tăng lượt xem, xử lý gửi đánh giá |
| **Model** | `models/Product.php` | Hàm lấy chi tiết, thông số kỹ thuật, biến thể màu sắc |
| **Model** | `models/Review.php` | Quản lý đánh giá sao 1-5 sao, bình luận khách hàng |
| **Giao diện** | `views/product/detail.php` | Màn hình chi tiết máy, chọn màu sắc biến thể, album ảnh góc chụp phóng to, bảng thông số kỹ thuật, form đánh giá & danh sách review |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Slug thân thiện:** Đọc sản phẩm bằng đường dẫn chữ (`slug`) thay vì ID lộ liễu, chuẩn SEO Google.
2. **Thư viện ảnh Gallery:** Bấm ảnh nhỏ đổi ảnh lớn chính, hiệu ứng xem ảnh trực quan.
3. **Tính sao trung bình:** Tính điểm trung bình `AVG(rating)` và tỷ lệ % phân bổ theo từng mức sao.

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_THANH_VIEN_NAM.bat`** để tự động commit và đẩy lên nhánh `dev-nam`.