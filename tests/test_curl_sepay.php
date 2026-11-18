<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../services/SepayGateway.php';

$gw = new SepayGateway();
$payment = [
    'txn_ref' => 'TEST' . time(),
    'amount' => 100000,
    'order_info' => 'Thanh toan don TEST',
    'method' => 'BANK_TRANSFER'
];
$checkout = $gw->createCheckoutInstance($payment);
echo "FIELDS:\n";
print_r($checkout['fields']);

$ch = curl_init($checkout['action']);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($checkout['fields']));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
$res = curl_exec($ch);
$code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$effectiveUrl = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
echo "\nHTTP CODE: {$code}\n";
echo "EFFECTIVE URL: {$effectiveUrl}\n";
file_put_contents(__DIR__ . '/sepay_response.html', $res);
echo "Saved response to sepay_response.html (" . strlen($res) . " bytes)\n";
