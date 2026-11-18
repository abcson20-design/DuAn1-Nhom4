<?php

declare(strict_types=1);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/CheckoutController.php';

$_SESSION['user'] = [
    'id' => 1,
    'email' => 'test@gmail.com',
    'fullname' => 'Test User',
    'phone' => '0987654321',
    'address' => 'Ha Noi'
];
$_SESSION['cart'] = [
    '1' => ['id' => 1, 'name' => 'Test Phone', 'price' => 100000, 'quantity' => 1, 'image' => '', 'category_name' => 'Phone']
];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'customer_name' => 'Nguyen Van A',
    'customer_phone' => '0987654321',
    'customer_email' => 'a@gmail.com',
    'customer_address' => '123 Pho Hue',
    'payment_method' => 'sepay',
    'sepay_method' => 'BANK_TRANSFER',
    'csrf_token' => Csrf::token(),
];

try {
    $c = new CheckoutController();
    $c->process();
} catch (Throwable $e) {
    echo 'EXCEPTION: ' . $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() . PHP_EOL . $e->getTraceAsString();
}
