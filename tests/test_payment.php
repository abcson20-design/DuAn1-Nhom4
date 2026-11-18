<?php

declare(strict_types=1);

/**
 * Bộ kiểm thử toàn diện Cổng thanh toán SePay cho NEXVORA
 * Đối chiếu 100% các tiêu chuẩn từ dự án Base Enterprise (D:\base (1)\base\tests\run.php)
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/SepayGateway.php';
require_once __DIR__ . '/../services/PaymentService.php';
require_once __DIR__ . '/../models/Payment.php';
require_once __DIR__ . '/../models/Order.php';

class SimpleTester
{
    private int $passed = 0;
    private int $failed = 0;

    public function test(string $name, callable $fn): void
    {
        try {
            $fn();
            $this->passed++;
            echo "  ✓ {$name}\n";
        } catch (Throwable $e) {
            $this->failed++;
            echo "  ✗ {$name}\n";
            echo "    " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
        }
    }

    public function summary(): void
    {
        echo "\n----------------------------------------\n";
        echo "KẾT QUẢ: {$this->passed} passed, {$this->failed} failed.\n";
        echo "----------------------------------------\n";
        if ($this->failed > 0) {
            exit(1);
        }
    }
}

$T = new SimpleTester();
echo "BẮT ĐẦU KIỂM THỬ CỔNG THANH TOÁN SEPAY (NEXVORA)\n\n";

// TEST 1: Cấu hình và URL
$T->test('SePay Gateway: URL Sandbox và Production chính xác', function () {
    $gw = new SepayGateway([
        'enabled' => true,
        'env' => 'sandbox',
        'merchant_id' => 'TEST_MERCHANT',
        'secret_key' => 'TEST_SECRET',
    ]);
    if ($gw->checkoutUrl() !== 'https://pay-sandbox.sepay.vn/v1/checkout/init') {
        throw new Exception('Checkout URL sandbox sai');
    }
    if ($gw->apiUrl() !== 'https://pgapi-sandbox.sepay.vn/v1') {
        throw new Exception('API URL sandbox sai');
    }

    $gwProd = new SepayGateway([
        'enabled' => true,
        'env' => 'production',
        'merchant_id' => 'TEST_MERCHANT',
        'secret_key' => 'TEST_SECRET',
    ]);
    if ($gwProd->checkoutUrl() !== 'https://pay.sepay.vn/v1/checkout/init') {
        throw new Exception('Checkout URL production sai');
    }
});

// TEST 2: Chữ ký HMAC-SHA256
$T->test('SePay Gateway: Thuật toán ký HMAC-SHA256 đúng chuẩn SDK SePay chính thức', function () {
    $secret = 'test_secret_key_123';
    $fields = [
        'merchant' => 'SP-TEST',
        'currency' => 'VND',
        'order_amount' => '100000',
        'operation' => 'PURCHASE',
        'order_description' => 'Test Don Hang',
        'order_invoice_number' => 'INV123456',
        'success_url' => 'https://example.com/success',
        'error_url' => 'https://example.com/error',
        'cancel_url' => 'https://example.com/cancel',
    ];

    $gw = new SepayGateway(['secret_key' => $secret]);
    $sig = $gw->sign($fields);

    // Tính tay để kiểm chứng
    $signedParts = [];
    foreach ($fields as $k => $v) {
        if (in_array($k, SepayGateway::SIGNED_FIELDS, true)) {
            $signedParts[] = $k . '=' . $v;
        }
    }
    $expected = base64_encode(hash_hmac('sha256', implode(',', $signedParts), $secret, true));

    if ($sig !== $expected) {
        throw new Exception("Chữ ký không khớp: got {$sig}, expected {$expected}");
    }
});

// TEST 3: Parse số tiền
$T->test('SePay Gateway: parseAmount chuyển số nguyên VND chính xác không qua float', function () {
    if (SepayGateway::parseAmount(50000) !== 50000) throw new Exception('parse int fail');
    if (SepayGateway::parseAmount('50000') !== 50000) throw new Exception('parse string fail');
    if (SepayGateway::parseAmount('50000.00') !== 50000) throw new Exception('parse .00 fail');
    if (SepayGateway::parseAmount('50000.50') !== null) throw new Exception('tiền có số lẻ không được chấp nhận');
    if (SepayGateway::parseAmount('abc') !== null) throw new Exception('chữ không được chấp nhận');
});

// TEST 4: Xác thực khoá IPN
$T->test('SePay Gateway: verifyIpnKey chống timing attack, đúng nhận, sai từ chối', function () {
    $gw = new SepayGateway(['ipn_secret' => 'my_secret_token_2026']);
    if (!$gw->verifyIpnKeyInstance('my_secret_token_2026')) throw new Exception('Khoá đúng bị từ chối');
    if ($gw->verifyIpnKeyInstance('wrong_key')) throw new Exception('Khoá sai lại được nhận');
    if ($gw->verifyIpnKeyInstance(null)) throw new Exception('Khoá null lại được nhận');
    if ($gw->verifyIpnKeyInstance('')) throw new Exception('Khoá rỗng lại được nhận');
});

// TEST 5: Luồng PaymentService (start, IPN, reconcile)
$T->test('PaymentService: start() tạo bản ghi pending và form checkout đã ký', function () {
    $pdo = Database::getConnection();
    
    // Tạo 1 đơn hàng test trong DB
    $testCode = 'TEST_ORD_' . time() . '_' . random_int(100, 999);
    $stmt = $pdo->prepare("INSERT INTO orders (order_code, total_amount, final_amount, payment_method, payment_status, order_status, created_at) VALUES (?, 250000, 250000, 'sepay', 'pending', 'pending', NOW())");
    $stmt->execute([$testCode]);
    $orderId = (int)$pdo->lastInsertId();

    $order = [
        'id' => $orderId,
        'order_code' => $testCode,
        'final_amount' => 250000,
        'payment_method' => 'sepay',
        'payment_status' => 'pending',
        'order_status' => 'pending',
    ];

    $config = [
        'enabled' => true,
        'env' => 'sandbox',
        'merchant_id' => 'SP-TEST-1234',
        'secret_key' => 'spsk_test_5678',
        'ipn_secret' => 'ipn_key_test',
        'reconcile_on_return' => true,
    ];
    $gw = new SepayGateway($config);
    $service = new PaymentService($gw);

    $checkout = $service->start($order, 'BANK_TRANSFER');
    if (empty($checkout['action']) || empty($checkout['fields']['signature'])) {
        throw new Exception('Checkout form thiếu action hoặc signature');
    }
    if ($checkout['fields']['order_amount'] !== '250000') {
        throw new Exception('Số tiền trong checkout không khớp: ' . $checkout['fields']['order_amount']);
    }
    if ($checkout['fields']['payment_method'] !== 'BANK_TRANSFER') {
        throw new Exception('Hình thức thanh toán con không khớp');
    }

    $paymentModel = new Payment();
    $payment = $paymentModel->findByTxnRef($checkout['fields']['order_invoice_number']);
    if (!$payment || $payment['status'] !== 'pending') {
        throw new Exception('Bản ghi payments chưa được lưu pending');
    }

    // IPN: Test sai khoá
    $res401 = $service->handleSepayIpn('sai_khoa', []);
    if ($res401['status'] !== 401) throw new Exception('IPN sai khoá không trả về 401');

    // IPN: Test sai số tiền
    $badAmountBody = [
        'notification_type' => 'ORDER_PAID',
        'order' => [
            'order_invoice_number' => $payment['txn_ref'],
            'order_amount' => '1000.00',
        ],
    ];
    $res422 = $service->handleSepayIpn('ipn_key_test', $badAmountBody);
    if ($res422['status'] !== 422) throw new Exception('IPN sai số tiền không trả về 422');

    // IPN: Test thành công chuẩn
    $okBody = [
        'notification_type' => 'ORDER_PAID',
        'order' => [
            'order_invoice_number' => $payment['txn_ref'],
            'order_amount' => '250000.00',
        ],
        'transaction' => [
            'transaction_id' => 'TXN_TEST_' . time(),
            'payment_method' => 'BANK_TRANSFER',
        ],
    ];
    $res200 = $service->handleSepayIpn('ipn_key_test', $okBody);
    if ($res200['status'] !== 200) throw new Exception('IPN hợp lệ không trả về 200: ' . json_encode($res200));

    // Kiểm tra đơn đã cập nhật paid
    $orderModel = new Order();
    $freshOrder = $orderModel->getByCode($testCode);
    if ($freshOrder['payment_status'] !== 'paid') {
        throw new Exception('Đơn hàng chưa chuyển sang paid');
    }

    // IPN lặp lại: Tính Idempotent
    $resRepeat = $service->handleSepayIpn('ipn_key_test', $okBody);
    if ($resRepeat['status'] !== 200) throw new Exception('IPN lặp lại không trả về 200');

    // Dọn dẹp dữ liệu test
    $pdo->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$orderId]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
});

// TEST 6: Đối soát qua Mock API SePay
$T->test('PaymentService: reconcileSepay() gọi API đối soát thành công và idempotent', function () {
    $pdo = Database::getConnection();
    
    $testCode = 'TEST_RECON_' . time() . '_' . random_int(100, 999);
    $stmt = $pdo->prepare("INSERT INTO orders (order_code, total_amount, final_amount, payment_method, payment_status, order_status, created_at) VALUES (?, 500000, 500000, 'sepay', 'pending', 'pending', NOW())");
    $stmt->execute([$testCode]);
    $orderId = (int)$pdo->lastInsertId();

    $paymentModel = new Payment();
    $txnRef = $paymentModel->createPayment($orderId, $testCode, 500000, 'sepay');
    $payment = $paymentModel->findByTxnRef($txnRef);

    // Giả lập SePay API trả về CAPTURED
    $mockTransport = function (string $method, string $url, array $headers, ?string $body) use ($txnRef): array {
        return [
            'status' => 200,
            'body' => json_encode([
                'data' => [
                    'order' => [
                        'id' => 'SP_ORD_999',
                        'order_status' => 'CAPTURED',
                        'order_amount' => '500000.00',
                        'order_invoice_number' => $txnRef,
                        'payment_method' => 'BANK_TRANSFER',
                    ]
                ]
            ]),
        ];
    };

    $config = [
        'enabled' => true,
        'env' => 'sandbox',
        'merchant_id' => 'SP-TEST-1234',
        'secret_key' => 'spsk_test_5678',
        'ipn_secret' => 'ipn_key_test',
        'reconcile_on_return' => true,
    ];
    $gw = new SepayGateway($config, $mockTransport);
    $service = new PaymentService($gw);

    $reconcile = $service->reconcileSepay($payment);
    if (!$reconcile['updated'] || !$reconcile['paid']) {
        throw new Exception('Đối soát không cập nhật thành công: ' . $reconcile['message']);
    }

    $orderModel = new Order();
    $freshOrder = $orderModel->getByCode($testCode);
    if ($freshOrder['payment_status'] !== 'paid') {
        throw new Exception('Đơn hàng chưa chuyển paid sau đối soát');
    }

    // Lần 2 gọi đối soát lại => không cập nhật lần nữa (idempotent)
    $paymentAfter = $paymentModel->findByTxnRef($txnRef);
    $reconcile2 = $service->reconcileSepay($paymentAfter);
    if ($reconcile2['updated']) {
        throw new Exception('Lần 2 đối soát lại bị cập nhật trùng lặp');
    }

    // Dọn dẹp
    $pdo->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$orderId]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
});

// TEST 7: Chống hồi sinh đơn đã huỷ
$T->test('PaymentService: Chống hồi sinh đơn đã huỷ (Bảo vệ hoàn tiền)', function () {
    $pdo = Database::getConnection();
    
    $testCode = 'TEST_CANCEL_' . time() . '_' . random_int(100, 999);
    $stmt = $pdo->prepare("INSERT INTO orders (order_code, total_amount, final_amount, payment_method, payment_status, order_status, created_at) VALUES (?, 150000, 150000, 'sepay', 'pending', 'cancelled', NOW())");
    $stmt->execute([$testCode]);
    $orderId = (int)$pdo->lastInsertId();

    $paymentModel = new Payment();
    $txnRef = $paymentModel->createPayment($orderId, $testCode, 150000, 'sepay');
    $payment = $paymentModel->findByTxnRef($txnRef);

    $config = [
        'enabled' => true,
        'env' => 'sandbox',
        'merchant_id' => 'SP-TEST-1234',
        'secret_key' => 'spsk_test_5678',
        'ipn_secret' => 'ipn_key_test',
        'reconcile_on_return' => true,
    ];
    $gw = new SepayGateway($config);
    $service = new PaymentService($gw);

    $body = [
        'notification_type' => 'ORDER_PAID',
        'order' => [
            'order_invoice_number' => $txnRef,
            'order_amount' => '150000.00',
        ],
        'transaction' => [
            'transaction_id' => 'TXN_CANCEL_TEST',
            'payment_method' => 'BANK_TRANSFER',
        ],
    ];

    $res = $service->handleSepayIpn('ipn_key_test', $body);
    if ($res['status'] !== 409) {
        throw new Exception('Đơn huỷ nhận tiền phải trả 409 Conflict để xử lý đối soát: got ' . $res['status']);
    }

    $orderModel = new Order();
    $fresh = $orderModel->getByCode($testCode);
    if ($fresh['order_status'] !== 'cancelled') {
        throw new Exception('Đơn hàng bị hồi sinh trạng thái thành công!');
    }
    if ($fresh['payment_status'] !== 'refunded') {
        throw new Exception('Đơn hàng không được đánh dấu refunded');
    }

    // Dọn dẹp
    $pdo->prepare("DELETE FROM payments WHERE order_id = ?")->execute([$orderId]);
    $pdo->prepare("DELETE FROM orders WHERE id = ?")->execute([$orderId]);
});

$T->summary();
