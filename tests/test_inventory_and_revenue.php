<?php
/**
 * Test nghiệp vụ kho & doanh thu:
 * 1. Khách đặt hàng (chưa xác nhận thanh toán): Trừ tồn kho, KHÔNG cộng doanh thu.
 * 2. Khách thanh toán thành công: Cập nhật doanh thu.
 * 3. Hủy đơn / Hoàn trả: Hoàn lại sản phẩm vào kho, KHÔNG tính doanh thu.
 * 4. Chống cộng trùng tồn kho (Idempotency).
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';

$orderModel = new Order();
$productModel = new Product();
$db = $orderModel->getDb();

echo "=== BẮT ĐẦU KIỂM THỬ TỒN KHO & DOANH THU ===\n";

// 1. Lấy 1 sản phẩm mẫu để test
$product = $db->query("SELECT * FROM products WHERE deleted_at IS NULL AND quantity > 5 LIMIT 1")->fetch();
if (!$product) {
    echo "[SKIP] Không tìm thấy sản phẩm có sẵn kho > 5 để test\n";
    exit(0);
}

$productId = (int)$product['id'];
$initialQty = (int)$product['quantity'];
$price = (float)$product['price'];
$testBuyQty = 2;

// Lấy thống kê doanh thu ban đầu
$initialStats = $orderModel->getStats();
$initialRevenue = (float)$initialStats['revenue'];

echo "1. Trạng thái ban đầu: Sản phẩm #{$productId} ('{$product['name']}'), Tồn kho: {$initialQty}, Doanh thu hiện tại: {$initialRevenue}đ\n";

// 2. Tạo đơn hàng thử nghiệm (Chưa thanh toán: payment_status = pending)
$orderData = [
    'user_id' => null,
    'customer_name' => 'Test Stock User',
    'customer_phone' => '0988888888',
    'customer_email' => 'test_stock@example.com',
    'customer_address' => '123 Đường Test, Hà Nội',
    'notes' => 'Đơn kiểm thử nghiệp vụ tồn kho & doanh thu',
    'total_amount' => $price * $testBuyQty,
    'discount_amount' => 0,
    'shipping_fee' => 0,
    'final_amount' => $price * $testBuyQty,
    'payment_method' => 'sepay',
    'payment_status' => 'pending',
    'order_status' => 'pending'
];

$cartItems = [
    [
        'id' => $productId,
        'name' => $product['name'],
        'price' => $price,
        'quantity' => $testBuyQty,
        'image' => $product['image'] ?? '',
        'color' => ''
    ]
];

$orderCode = $orderModel->createOrder($orderData, $cartItems);
$createdOrder = $orderModel->getByCode($orderCode);

// Kiểm tra tồn kho sau khi tạo đơn
$prodAfterCreate = $productModel->getById($productId);
$qtyAfterCreate = (int)$prodAfterCreate['quantity'];
assert($qtyAfterCreate === ($initialQty - $testBuyQty), "Tồn kho phải bị trừ ngay khi tạo đơn");
echo "-> [PASS] Tồn kho đã được trừ ngay lập tức: {$initialQty} -> {$qtyAfterCreate} (-{$testBuyQty})\n";

// Kiểm tra doanh thu sau khi tạo đơn (CHƯA THANH TOÁN -> DOANH THU KHÔNG ĐƯỢC TĂNG)
$statsAfterCreate = $orderModel->getStats();
$revenueAfterCreate = (float)$statsAfterCreate['revenue'];
assert($revenueAfterCreate === $initialRevenue, "Doanh thu KHÔNG ĐƯỢC tăng khi khách chưa thanh toán");
echo "-> [PASS] Doanh thu không thay đổi khi chưa thanh toán: {$revenueAfterCreate}đ (chính xác bằng {$initialRevenue}đ)\n";

// 3. Khách thanh toán thành công (Xác nhận thanh toán)
$orderModel->updatePaymentStatus($orderCode, 'paid', 'TEST_TX_' . time());
$statsAfterPaid = $orderModel->getStats();
$revenueAfterPaid = (float)$statsAfterPaid['revenue'];
$expectedPaidRevenue = $initialRevenue + ($price * $testBuyQty);
assert($revenueAfterPaid === $expectedPaidRevenue, "Doanh thu phải tăng đúng số tiền đơn sau khi thanh toán");
echo "-> [PASS] Doanh thu đã cập nhật sau khi thanh toán thành công: {$revenueAfterPaid}đ (+ " . ($price * $testBuyQty) . "đ)\n";

// 4. Khách hủy đơn hoặc hoàn trả (Hủy đơn / Hoàn tiền)
$orderModel->cancelOrder((int)$createdOrder['id'], 'Khách test yêu cầu hoàn tiền / hủy đơn');

// Kiểm tra tồn kho sau khi hủy
$prodAfterCancel = $productModel->getById($productId);
$qtyAfterCancel = (int)$prodAfterCancel['quantity'];
assert($qtyAfterCancel === $initialQty, "Tồn kho phải được hoàn trả lại nguyên vẹn như ban đầu");
echo "-> [PASS] Tồn kho đã được hoàn lại đầy đủ vào kho: {$qtyAfterCancel} chiếc (trở lại ban đầu {$initialQty})\n";

// Kiểm tra doanh thu sau khi hủy
$statsAfterCancel = $orderModel->getStats();
$revenueAfterCancel = (float)$statsAfterCancel['revenue'];
assert($revenueAfterCancel === $initialRevenue, "Doanh thu của đơn đã hủy không được tính");
echo "-> [PASS] Doanh thu đã tự động loại bỏ đơn hủy: {$revenueAfterCancel}đ (quay về ban đầu {$initialRevenue}đ)\n";

// 5. Kiểm tra Idempotent (Chống cộng thừa tồn kho nếu gọi hủy/hoàn trả lần 2)
$ordCheck = $orderModel->getById((int)$createdOrder['id']);
echo "Notes trước double restore:\n" . ($ordCheck['notes'] ?? 'NULL') . "\n";

$resDouble = $orderModel->restoreStock((int)$createdOrder['id']);
var_dump($resDouble);

$prodAfterDoubleRestore = $productModel->getById($productId);
$qtyAfterDoubleRestore = (int)$prodAfterDoubleRestore['quantity'];
echo "Qty after double restore: {$qtyAfterDoubleRestore} vs initial: {$initialQty}\n";
assert($qtyAfterDoubleRestore === $initialQty, "Không được hoàn tồn kho lần 2");
echo "-> [PASS] Chống hoàn kho trùng lặp (Idempotent) thành công: kho vẫn giữ nguyên {$qtyAfterDoubleRestore}\n";

// Dọn dẹp đơn test
$db->exec("DELETE FROM order_items WHERE order_id = " . (int)$createdOrder['id']);
$db->exec("DELETE FROM orders WHERE id = " . (int)$createdOrder['id']);

echo "=== TẤT CẢ KIỂM THỬ THÀNH CÔNG 100%! ===\n";
