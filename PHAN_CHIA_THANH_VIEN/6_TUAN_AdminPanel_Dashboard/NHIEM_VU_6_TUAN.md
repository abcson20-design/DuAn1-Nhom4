# NHIỆM VỤ 6: TUẤN (LÊ DANH TUẤN)

* **Thành viên:** Tuấn (Lê Danh Tuấn)
* **Nhánh Git:** `dev-tuan`
* **File click đẩy code:** `DAY_CODE_THANH_VIEN_TUAN.bat`
* **Nhiệm vụ chính thức:** **Lập trình Bảng điều khiển Quản trị (Admin Panel)**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Controller**| `controllers/AdminController.php` | Bộ điều khiển trung tâm Admin: Thống kê doanh thu, CRUD Sản phẩm, Xử lý Đơn hàng, Quản lý Khách hàng |
| **Giao diện** | `views/admin/layout.php` | Khung sườn Admin (Sidebar Dark Cyberpunk, Topbar) |
| **Giao diện** | `views/admin/dashboard.php` | Biểu đồ doanh thu, số đơn hoàn thành, cảnh báo hết hàng, top bán chạy |
| **Giao diện** | `views/admin/products.php` & `product_form.php` | Thêm, sửa, xóa mềm sản phẩm, quản lý biến thể màu sắc |
| **Giao diện** | `views/admin/orders.php` & `order_detail.php` | Duyệt đơn hàng, in vận đơn, **Banner cảnh báo gom đơn** |
| **Giao diện** | `views/admin/categories.php`, `users.php`, `coupons.php` | Quản lý danh mục, người dùng, phát hành mã voucher |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Dashboard thống kê:** Tổng doanh thu, đơn hàng, khách hàng mới lấy bằng các câu lệnh `SUM(final_amount)`, `COUNT(*)`.
2. **Cảnh báo gom đơn:** Thuật toán phát hiện 1 khách đặt nhiều đơn liên tiếp để Admin liên hệ gom chung kiện hàng tiết kiệm phí ship.
3. **Xóa mềm (Soft Delete):** Xóa chỉ gán `deleted_at = NOW()`, có trang Thùng rác để khôi phục bất cứ lúc nào.

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_THANH_VIEN_TUAN.bat`** để tự động commit và đẩy lên nhánh `dev-tuan`.