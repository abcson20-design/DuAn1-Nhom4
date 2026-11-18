-- ==============================================================
-- NÂNG CẤP DATABASE CHO TÍNH NĂNG TRẢ HÀNG & HOÀN TIỀN
-- Dùng cho database DuAn1_db ĐÃ TẠO TRƯỚC ĐÓ (database mới import từ database.sql thì không cần chạy).
-- Cách chạy: phpMyAdmin -> chọn DuAn1_db -> tab SQL -> dán nội dung tệp này -> Thực hiện.
-- (Website cũng tự động nâng cấp khi chạy lần đầu, tệp này để chạy thủ công nếu cần.)
-- ==============================================================
USE DuAn1_db;

-- 1. Thêm trạng thái 'return_requested' (khách yêu cầu trả hàng, chờ duyệt) và 'returned' (đã trả hàng)
ALTER TABLE `orders`
  MODIFY `order_status` enum('pending','confirmed','shipping','completed','return_requested','returned','cancelled')
  DEFAULT 'pending' COMMENT 'Tiến trình đơn hàng (return_requested: khách yêu cầu trả hàng chờ duyệt, returned: đã trả hàng)';

-- 2. Thêm cột ngày giao hàng thành công (tính hạn đổi trả "kể từ khi nhận hàng")
ALTER TABLE `orders`
  ADD COLUMN IF NOT EXISTS `completed_at` datetime DEFAULT NULL COMMENT 'Thời điểm giao hàng thành công (tính hạn đổi trả)' AFTER `bank_transaction_id`;
