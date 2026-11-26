# NHIỆM VỤ 5: QUÂN

* **Thành viên:** Quân
* **Nhánh Git:** `dev-quan`
* **File click đẩy code:** `DAY_CODE_THANH_VIEN_QUAN.bat`
* **Nhiệm vụ chính thức:** **Lập trình Thanh toán Online & Theo dõi đơn hàng**

---

## 📂 CÁC FILE MÃ NGUỒN PHỤ TRÁCH CHÍNH

| Thành phần | Đường dẫn file | Nội dung & Nghiệp vụ chi tiết |
| :--- | :--- | :--- |
| **Controller**| `controllers/PaymentController.php` & `CheckoutController.php` | Điều hướng thanh toán QR, tiếp nhận Webhook SePay |
| **Controller**| `controllers/OrderController.php` | Chi tiết đơn hàng, tra cứu đơn, khách hủy đơn, khách bấm "Đã nhận hàng" |
| **Service** | `services/PaymentService.php` | Khởi tạo giao dịch VietQR Napas 24/7 tự động |
| **Model** | `models/Order.php` | Quản lý bảng `orders`, `order_items`, máy trạng thái State Machine |
| **Giao diện** | `views/checkout/payment_qr.php` | Màn hình quét mã QR chuyển khoản, đếm ngược 10 phút, tự bắt giao dịch thành công |
| **Giao diện** | `views/order/track.php` & `views/auth/orders.php` | Màn hình tra cứu timeline vận chuyển & lịch sử đơn hàng realtime SSE |

---

## 🎯 CÂU HỎI VẤN ĐÁP & ĐIỂM CHẤM ĐỒ ÁN
1. **Cổng SePay Webhook:** Tiếp nhận thông báo tiền về từ ngân hàng, khớp mã đơn và tự động chuyển trạng thái đơn hàng trong 3 giây.
2. **SSE Real-time:** Kênh `api/orders/sse` đẩy thông báo đổi trạng thái đơn tức thì tới màn hình khách hàng không cần F5.
3. **Quy trình State Machine:** Đơn COD chỉ khách mới có nút "Đã Nhận Hàng" để hoàn tất đơn; đơn đã giao `shipping` thì khách không được hủy trái phép.

---

## 🚀 HƯỚNG DẪN ĐẨY CODE LÊN GITHUB
Chạy file 👉 **`DAY_CODE_THANH_VIEN_QUAN.bat`** để tự động commit và đẩy lên nhánh `dev-quan`.