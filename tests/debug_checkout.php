<?php

declare(strict_types=1);

ini_set('display_errors', '1');
error_reporting(E_ALL);

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../controllers/CheckoutController.php';

$pdo = Database::getConnection();
$prod = $pdo->query("SELECT id, name, price, quantity FROM products WHERE deleted_at IS NULL AND quantity > 0 LIMIT 1")->fetch(PDO::FETCH_ASSOC);
echo "Sản phẩm test: " . print_r($prod, true) . "\n";

$_SESSION['user'] = [
    'id' => 1,
    'email' => 'admin@gmail.com',
    'fullname' => 'Admin User',
    'phone' => '0987654321',
    'address' => 'Ha Noi'
];
$_SESSION['cart'] = [
    (string)$prod['id'] => [
        'id' => (int)$prod['id'],
        'name' => $prod['name'],
        'price' => (float)$prod['price'],
        'quantity' => 1,
        'image' => '',
        'category_name' => 'Phone'
    ]
];

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'customer_name' => 'Le Van Son',
    'customer_phone' => '0987654321',
    'customer_email' => 'son@gmail.com',
    'customer_address' => 'Thanh Hoa',
    'payment_method' => 'sepay',
    'sepay_method' => 'BANK_TRANSFER',
    '_csrf_token' => Csrf::token(),
];

class TestCheckoutController extends CheckoutController {
    protected function redirect(string $url): void {
        echo "REDIRECT TO: " . $url . "\n";
        if (!empty($_SESSION['checkout_error'])) {
            echo "CHECKOUT ERROR: " . $_SESSION['checkout_error'] . "\n";
        }
    }

    protected function render(string $viewPath, array $data = [], string $layout = 'layouts/header'): void {
        echo "RENDER VIEW: " . $viewPath . "\n";
        echo "DATA: " . json_encode(array_keys($data)) . "\n";
        parent::render($viewPath, $data, $layout);
    }
}

try {
    $c = new TestCheckoutController();
    $c->process();
} catch (Throwable $e) {
    echo "EXCEPTION: " . $e->getMessage() . " at " . $e->getFile() . ":" . $e->getLine() . "\n";
    echo $e->getTraceAsString() . "\n";
}
