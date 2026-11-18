<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/SepayGateway.php';

$secretKey = env('SEPAY_SECRET_KEY');
$merchantId = env('SEPAY_MERCHANT_ID');

function testInit(array $fields, string $secretKey): array {
    $signed = [];
    foreach ($fields as $field => $value) {
        if (in_array($field, SepayGateway::SIGNED_FIELDS, true)) {
            $signed[] = $field . '=' . $value;
        }
    }
    $fields['signature'] = base64_encode(hash_hmac('sha256', implode(',', $signed), $secretKey, true));

    $ch = curl_init('https://pay-sandbox.sepay.vn/v1/checkout/init');
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $res = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    return ['status' => $status, 'body' => $res];
}

$txn = 'INV' . time();

// Payload 4: Percent-encoded path
$fields4 = [
    'merchant' => $merchantId,
    'currency' => 'VND',
    'order_amount' => '100000',
    'operation' => 'PURCHASE',
    'order_description' => 'Thanh toan don hang',
    'payment_method' => 'BANK_TRANSFER',
    'order_invoice_number' => $txn,
    'success_url' => 'http://localhost/D%E1%BB%B0%20%C3%81N%201/BanHangOnline/index.php?url=payment/sepay/success&ref=' . $txn,
    'error_url' => 'http://localhost/D%E1%BB%B0%20%C3%81N%201/BanHangOnline/index.php?url=payment/sepay/error&ref=' . $txn,
    'cancel_url' => 'http://localhost/D%E1%BB%B0%20%C3%81N%201/BanHangOnline/index.php?url=payment/sepay/cancel&ref=' . $txn,
];

$r4 = testInit($fields4, $secretKey);
echo "Test 4 (Percent-encoded URL):\n";
if (strpos($r4['body'], 'Yêu cầu không hợp lệ') !== false) {
    echo "  -> RESULT: Yêu cầu không hợp lệ\n";
} else {
    echo "  -> ACCEPTED BY SEPAY! SUCCESS!\n";
}
