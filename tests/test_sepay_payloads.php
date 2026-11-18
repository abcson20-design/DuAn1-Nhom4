<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/SepayGateway.php';

$secretKey = env('SEPAY_SECRET_KEY');
$merchantId = env('SEPAY_MERCHANT_ID');

function testInit(array $fields, string $secretKey): array {
    // Generate signature
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

// Payload 1: Clean URL without accents or C:
$fields1 = [
    'merchant' => $merchantId,
    'currency' => 'VND',
    'order_amount' => '100000',
    'operation' => 'PURCHASE',
    'order_description' => 'Thanh toan don hang',
    'payment_method' => 'BANK_TRANSFER',
    'order_invoice_number' => $txn,
    'success_url' => 'https://nexvora.com/payment/sepay/success?ref=' . $txn,
    'error_url' => 'https://nexvora.com/payment/sepay/error?ref=' . $txn,
    'cancel_url' => 'https://nexvora.com/payment/sepay/cancel?ref=' . $txn,
];

$r1 = testInit($fields1, $secretKey);
echo "Test 1 (Clean URL, with payment_method BANK_TRANSFER):\n";
if (strpos($r1['body'], 'Yêu cầu không hợp lệ') !== false) {
    echo "  -> RESULT: Yêu cầu không hợp lệ\n";
} else {
    echo "  -> SUCCESS! Found valid page!\n";
}

// Payload 3: With Vietnamese URL (DỰ ÁN 1)
$fields3 = $fields1;
$fields3['success_url'] = 'http://localhost/DỰ ÁN 1/BanHangOnline/index.php?url=payment/sepay/success?ref=' . $txn;
$r3 = testInit($fields3, $secretKey);
echo "Test 3 (With DỰ ÁN 1 in success_url):\n";
if (strpos($r3['body'], 'Yêu cầu không hợp lệ') !== false) {
    echo "  -> RESULT: Yêu cầu không hợp lệ (REJECTED by SePay!)\n";
} else {
    echo "  -> ACCEPTED\n";
}

// Check if any error text in r1
if (preg_match('/<div class=\"alert alert-danger[^\"]*\">(.*?)<\/div>/is', $r1['body'], $m)) {
    echo "Alert in r1: " . trim($m[1]) . "\n";
}
