# BÁO CÁO KIỂM THỬ – DỰ ÁN 1 NHÓM 4 (NEXVORA)

**Ngày kiểm thử:** 26/11/2026
**Môi trường:** PHP 8.3, Apache 2.4, MariaDB; dữ liệu nạp từ `sql/database.sql`; trình duyệt Chrome (Playwright) cho các phần giao diện.
**Phạm vi:** toàn bộ mã nguồn, `README.md` và 155 test case trong `TEST_CASES_DU_AN_1.docx` (26 bảng).

## 1. Kết quả tổng hợp

- Chạy lần đầu (mã nguồn gốc): phát hiện **22 nhóm lỗi** trong code. Có 7 lỗi làm sập trang (HTTP 500 / Fatal Error), 1 lỗi JavaScript và 5 lỗi bảo mật.
- Sau khi sửa: **210/210 kiểm tra tự động đạt**, gồm 155 test case và 55 kiểm tra bổ sung (CRUD thương hiệu/danh mục/mã giảm giá/đơn hàng, thùng rác, trả hàng, đổi phương thức thanh toán...).
- Đã quét toàn bộ trang (khách, khách hàng, Admin) với chế độ ghi mọi lỗi PHP (Warning/Notice/Fatal): **không còn lỗi PHP nào**. Đã mở 36 trang bằng Chrome: **không còn lỗi JavaScript**.
- **154/155 test case Đạt.** Test case còn lại (Bảng 25, STT 3: đối soát SePay) chưa kiểm được vì cần kết nối tới máy chủ SePay thật.
- **Giao diện giữ nguyên.** Chỉ có 3 phần được thêm vào giao diện quản trị:
  - nút Khóa/Mở khóa tài khoản;
  - danh sách khách đặt giữ chỗ;
  - ô báo lỗi khi nhập sản phẩm.
  Các phần này dùng lại đúng style có sẵn.

| Bảng | Chức năng | Kết quả |
|---|---|---|
| 1 | Đăng ký | Đạt (sửa: API check trùng SĐT lỗi 500, server cho trùng SĐT) |
| 2 | Đăng nhập | Đạt (sửa TC: tài khoản mẫu) |
| 3 | Quên mật khẩu | Đạt (sửa lỗi bảo mật: link đặt lại hiện trên màn hình) |
| 4 | Hồ sơ & đổi mật khẩu | Đạt (bổ sung chặn trùng SĐT) |
| 5 | Đăng xuất | Đạt |
| 6–7 | Yêu thích | Đạt (sửa TC Bảng 6 STT 1) |
| 8 | Giỏ hàng | Đạt (sửa: SL âm, vượt kho, SP không tồn tại, giới hạn 2 chiếc bị vượt qua; sửa TC STT 1) |
| 9 | Giỏ bền vững | Đạt |
| 10 | Mã giảm giá | Đạt (sửa: thông báo thu hồi mã không hiện, hoàn lượt mã; sửa TC thông báo) |
| 11 | Thông tin giao hàng | Đạt (sửa TC thông báo) |
| 12 | COD | Đạt (sửa: Admin vẫn hoàn thành được đơn COD qua request; tự hoàn thành tính sai mốc) |
| 13 | SePay | Đạt (sửa: Fatal Error gọi static, "Quay lại sửa giỏ" lỗi 500; sửa TC trạng thái) |
| 14 | Tra cứu đơn | Đạt (sửa: mã không tồn tại báo sai; sửa TC mã TS) |
| 15 | Hủy / Đã nhận hàng | Đạt (sửa: API Đã nhận hàng lỗi 500, hủy đơn người khác bằng SĐT) |
| 16 | Trả hàng | Đạt (sửa TC theo chính sách thật: điện thoại 180 ngày, tai nghe BT 7 ngày) |
| 17–19 | Tìm kiếm, lọc, chi tiết SP | Đạt |
| 20 | Đánh giá | Đạt (sửa: gửi đánh giá lỗi 500, Thích bình luận lỗi 500, open redirect) |
| 21–22 | Liên hệ, phân quyền | Đạt (sửa TC trạng thái `new`) |
| 23 | Quản lý sản phẩm | Đạt (sửa: upload file sai định dạng vẫn tạo SP, không hiện lỗi) |
| 24 | Danh mục / mã giảm giá | Đạt (sửa: cho tạo danh mục trùng tên; sửa TC 1–99%) |
| 25 | Quản lý đơn | Đạt, riêng STT 3 cần SePay thật |
| 26 | Bảo mật | Đạt (sửa: SSE lỗi 500, tài khoản bị khóa vẫn đăng nhập, thiếu CSRF, trang lỗi DB lộ thông tin) |

## 2. Lỗi trong mã nguồn đã sửa
Danh sách đầy đủ 22 lỗi và cách sửa nằm ở mục **"NHẬT KÝ SỬA LỖI SAU KIỂM THỬ"** cuối `README.md`. Mọi chỗ sửa trong code đều có chú thích `[SỬA LỖI]` hoặc `[BỔ SUNG]` để dễ tìm.

## 3. Lỗi trong README đã sửa
- In công khai `SEPAY_IPN_SECRET`: đã thay bằng chỗ trống để tự điền.
- Ghi `DB_HOST=127.0.0.1`, khác với `.env` (`localhost`): đã sửa cho khớp.
- Tài khoản `khachhang@gmail.com / User@123` không có trong CSDL: đã thêm vào `database.sql`, SĐT 0901234567.
- Ghi "CSRF 100%" và "Admin giả lập được SePay sandbox", nhưng `.env` đặt `APP_ENV=production` nên Admin bị chặn: đã ghi rõ điều kiện.
- Chưa có ghi chú về `APP_DEBUG`, giá dữ liệu mẫu (vài nghìn đồng), mã đơn `TS…` và danh sách trạng thái đơn: đã bổ sung.

## 4. Lỗi trong file test case đã sửa
- **STT trùng hoặc nhảy số:**
  - Bảng 8: 8–11 lặp lại.
  - Bảng 10: 7–9 lặp lại.
  - Bảng 12: đánh số 1, 4, 5.
  - Bảng 13: hai dòng số 5.
  - Bảng 15: thiếu số 4.
  Tất cả đã được đánh lại số.
- **Ô kết quả mong đợi bị trống:** Bảng 3, STT 2 và 3.
- **Mô tả sai so với chương trình:**
  - Mã đơn `NEX…`; đúng là `TS…`.
  - Trạng thái `processing` / `delivered`; đúng là `confirmed` / `completed`.
  - Liên hệ ở trạng thái `pending`; đúng là `new`.
  - Đổi trả "30 ngày"; chương trình áp dụng theo loại sản phẩm.
  - Mã giảm "≤100%"; chương trình yêu cầu 1–99%.
  - Tên cột `expires_at` / `usage_limit`; đúng là `end_date` / `quantity`.
  - Nhiều câu thông báo lỗi khác với thông báo thật.
- **Các dòng mâu thuẫn hoặc trùng nhau:**
  - Bảng 8 STT 1 nói SweetAlert chỉ dành cho thành viên, trong khi STT 12 nói mở modal giữ chỗ.
  - Bảng 15 có các dòng trùng nhau (STT 2/6 cũ và STT 3/5 cũ).
  - Bảng 20 STT 5 ghi tác giả tự xóa bình luận, nhưng chương trình chỉ cho Admin xóa.
- **Đã thêm:** cột **"Kết quả test"** và 3 dòng tóm tắt kết quả ở đầu tài liệu. Các đoạn trống thừa đã được xóa.

## 5. Lưu ý khi nộp / chạy
1. Import lại `sql/database.sql`. Bảng `orders` đã có thêm cột `updated_at` và `shipped_at`. Code cũng tự thêm hai cột này nếu dùng CSDL cũ.
2. Tệp `.env` vẫn chứa khóa SePay **test** của nhóm. Không đẩy tệp này lên GitHub; `.gitignore` đã chặn sẵn.
3. Muốn demo nút giả lập thanh toán SePay: đặt `APP_ENV=local`.
4. Muốn demo link quên mật khẩu trên màn hình: đặt `APP_DEBUG=true`.
5. Muốn demo giới hạn 2 chiếc (≥ 15 triệu) hoặc mã giảm giá có đơn tối thiểu lớn: sửa giá sản phẩm trong trang Admin, vì giá mẫu chỉ vài nghìn đồng.
