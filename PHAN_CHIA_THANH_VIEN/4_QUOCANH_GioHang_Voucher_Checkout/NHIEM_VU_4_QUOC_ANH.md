# NHIỆM VỤ 4: QUỐC ANH

* **Thành viên:** Quốc Anh
* **Nhánh Git:** `dev-quocanh`
* **File click đẩy code:** `DAY_CODE_THANH_VIEN_QUOCANH.bat`
* **Nhiệm vụ chính thức:** **Lập trình Giỏ hàng, Mã giảm giá & Đặt hàng (Checkout)**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Controller**| `controllers/CartController.php` | Thêm vào giỏ, tăng giảm SL, xóa món, tính tổng tiền |
| **Controller**| `controllers/CheckoutController.php` | Form đặt hàng, tính tiền các món được chọn, áp voucher, tạo đơn |
| **API** | `controllers/ApiController.php` | Các API giỏ hàng: toggle chọn món, đổi số lượng Ajax |
| **Model** | `models/Cart.php` | Giỏ hàng bền vững (Persistent Cart) lưu vào CSDL bảng `user_carts` |
| **Model** | `models/Coupon.php` | Mã giảm giá voucher: Kiểm tra điều kiện, tự động ẩn mã đã dùng |
| **Giao diện** | `views/cart/index.php` | Giỏ hàng Shopee: Checkbox chọn món, chọn tất cả, thanh toán dính chân trang |
| **Giao diện** | `views/checkout/index.php` | Màn hình điền thông tin giao hàng, chọn voucher dropdown/modal |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Chọn món chuẩn Shopee:** Chỉ những món được tick chọn mới tính tiền sang đơn hàng; món bỏ chọn vẫn giữ lại trong giỏ.
2. **Persistent Cart:** Giỏ hàng lưu CSDL nên đổi máy, đóng trình duyệt vẫn không bị mất giỏ.
3. **Kiểm tra Voucher:** Mỗi tài khoản/SĐT chỉ dùng 1 lần (dùng rồi tự ẩn khỏi danh sách), kiểm tra đơn tối thiểu, chặn giảm quá mức tối đa.

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_THANH_VIEN_QUOCANH.bat`** để tự động commit và đẩy lên nhánh `dev-quocanh`.