<?php

declare(strict_types=1);

/**
 * Tích hợp SePay Payment Gateway (checkout v1) - Chuẩn Enterprise giống dự án Base.
 * Docs: https://developer.sepay.vn/en/cong-thanh-toan/bat-dau
 * Thuật toán ký chép đúng theo SDK chính thức (sepay/sepay-pg cho PHP, sepay-pg-node cho Node.js).
 *
 * Luồng:
 *   1. createCheckout()  -> trang in form ẩn, trình duyệt POST sang /v1/checkout/init của SePay
 *   2. SePay gọi IPN URL (server-to-server, POST JSON + header X-Secret-Key) -> cập nhật DB (nguồn sự thật)
 *   3. SePay redirect khách về success_url / error_url / cancel_url -> chỉ để hiển thị, KHÔNG có chữ ký
 *   4. retrieveOrder() gọi API SePay để đối soát khi không nhận được IPN
 */
final class SepayGateway
{
    public const CHECKOUT_URLS = [
        'sandbox' => 'https://pay-sandbox.sepay.vn/v1/checkout/init',
        'production' => 'https://pay.sepay.vn/v1/checkout/init',
    ];

    public const API_URLS = [
        'sandbox' => 'https://pgapi-sandbox.sepay.vn/v1',
        'production' => 'https://pgapi.sepay.vn/v1',
    ];

    /** Phương thức chọn trước (payment_method). Rỗng = để khách chọn trên trang SePay */
    public const PAYMENT_METHODS = [
        '' => 'Chọn trên trang SePay',
        'BANK_TRANSFER' => 'Chuyển khoản ngân hàng (quét VietQR)',
        'NAPAS_BANK_TRANSFER' => 'Quét QR Napas 24/7',
        'CARD' => 'Thẻ quốc tế / nội địa',
    ];

    /** Trạng thái đơn đã thu tiền theo chuẩn SePay */
    public const PAID_ORDER_STATUS = 'CAPTURED';

    /**
     * Trường được ký, chép từ SignatureGenerator của SDK chính thức.
     * Chữ ký lấy các trường theo THỨ TỰ XUẤT HIỆN trong form, không phải thứ tự danh sách này.
     */
    public const SIGNED_FIELDS = [
        'merchant', 'env', 'operation', 'payment_method', 'order_amount', 'currency', 'order_invoice_number',
        'order_description', 'customer_id', 'agreement_id', 'agreement_name', 'agreement_type',
        'agreement_payment_frequency', 'agreement_amount_per_payment', 'success_url', 'error_url', 'cancel_url',
    ];

    /** @var (Closure(string, string, array<string, string>, ?string): array{status: int, body: string})|null */
    private ?Closure $transport = null;

    /** @var array<string, mixed> */
    private array $config;

    /** @var Closure|null Mock transport dùng cho static testing */
    private static ?Closure $staticTransport = null;

    public function __construct(?array $config = null, ?Closure $transport = null)
    {
        if ($config === null) {
            $paymentConfig = self::loadConfig();
            $this->config = $paymentConfig['gateways']['sepay'] ?? [];
        } else {
            $this->config = $config;
        }

        $this->transport = $transport ?? Closure::fromCallable([self::class, 'curlTransport']);
    }

    public static function loadConfig(): array
    {
        static $cfg = null;
        if ($cfg === null) {
            $file = __DIR__ . '/../config/payment.php';
            if (file_exists($file)) {
                $cfg = require $file;
            } else {
                $cfg = [
                    'gateways' => [
                        'sepay' => [
                            'enabled' => (bool) env('SEPAY_ENABLED', true),
                            'env' => (string) env('SEPAY_ENV', 'sandbox'),
                            'merchant_id' => (string) env('SEPAY_MERCHANT_ID', ''),
                            'secret_key' => (string) env('SEPAY_SECRET_KEY', ''),
                            'ipn_secret' => (string) env('SEPAY_IPN_SECRET', ''),
                            'reconcile_on_return' => (bool) env('SEPAY_RECONCILE_ON_RETURN', true),
                        ],
                    ],
                ];
            }
        }
        return $cfg;
    }

    public function withTransport(Closure $transport): self
    {
        return new self($this->config, $transport);
    }

    public static function setStaticTransport(?Closure $transport): void
    {
        self::$staticTransport = $transport;
    }

    public function code(): string
    {
        return 'sepay';
    }

    public function name(): string
    {
        return 'SePay';
    }

    public function description(): string
    {
        return 'Thanh toán qua SePay: quét VietQR chuyển khoản, QR Napas, thẻ.';
    }

    public static function isConfigured(): bool
    {
        $cfg = self::loadConfig();
        $sepay = $cfg['gateways']['sepay'] ?? [];
        return ($sepay['merchant_id'] ?? '') !== '' && ($sepay['secret_key'] ?? '') !== '';
    }

    public static function isEnabled(): bool
    {
        $cfg = self::loadConfig();
        $sepay = $cfg['gateways']['sepay'] ?? [];
        return (bool) ($sepay['enabled'] ?? false) && self::isConfigured();
    }

    public function isOnline(): bool
    {
        return true;
    }

    public function config(string $key, mixed $default = null): mixed
    {
        return $this->config[$key] ?? $default;
    }

    public function getEnv(): string
    {
        return ($this->config['env'] ?? self::env()) === 'production' ? 'production' : 'sandbox';
    }

    public static function env(): string
    {
        $cfg = self::loadConfig();
        $sepay = $cfg['gateways']['sepay'] ?? [];
        return ($sepay['env'] ?? 'sandbox') === 'production' ? 'production' : 'sandbox';
    }

    public function checkoutUrl(): string
    {
        return self::CHECKOUT_URLS[$this->getEnv()];
    }

    public static function getCheckoutUrl(): string
    {
        return self::CHECKOUT_URLS[self::env()];
    }

    public function apiUrl(): string
    {
        return self::API_URLS[$this->getEnv()];
    }

    public static function getApiUrl(): string
    {
        return self::API_URLS[self::env()];
    }

    /**
     * Tạo form checkout chuyển sang SePay (hỗ trợ cả instance call lẫn static call).
     */
    public function createCheckoutInstance(array $payment): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('SePay chưa cấu hình SEPAY_MERCHANT_ID / SEPAY_SECRET_KEY trong .env');
        }

        $ref = rawurlencode((string)$payment['txn_ref']);

        // Thứ tự giống prepareFormFields() của SDK PHP. Đổi thứ tự là chữ ký sai.
        $fields = [
            'merchant' => (string) $this->config['merchant_id'],
            'currency' => 'VND',
            'order_amount' => (string) (int) $payment['amount'], // Số nguyên VND, KHÔNG nhân 100
            'operation' => 'PURCHASE',
            'order_description' => self::sanitizeDescription((string)($payment['order_info'] ?? 'Thanh toan don')),
        ];

        if (!empty($payment['method'])) {
            $fields['payment_method'] = (string) $payment['method'];
        }

        $fields['order_invoice_number'] = (string)$payment['txn_ref'];
        
        $successUrl = $payment['success_url'] ?? (rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/success');
        $errorUrl = $payment['error_url'] ?? (rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/error');
        $cancelUrl = $payment['cancel_url'] ?? (rtrim(BASE_URL, '/') . '/index.php?url=payment/sepay/cancel');

        $fullSuccess = $successUrl . (str_contains($successUrl, '?') ? '&' : '?') . 'ref=' . $ref;
        $fullError = $errorUrl . (str_contains($errorUrl, '?') ? '&' : '?') . 'ref=' . $ref;
        $fullCancel = $cancelUrl . (str_contains($cancelUrl, '?') ? '&' : '?') . 'ref=' . $ref;

        // Chuẩn hóa mã hóa URL (chuyển đổi khoảng trắng & ký tự có dấu như 'DỰ ÁN 1' thành mã an toàn RFC 3986)
        $fields['success_url'] = self::encodeUrl($fullSuccess);
        $fields['error_url'] = self::encodeUrl($fullError);
        $fields['cancel_url'] = self::encodeUrl($fullCancel);

        $fields['signature'] = $this->sign($fields);

        return ['action' => $this->checkoutUrl(), 'fields' => $fields];
    }

    /**
     * Chuẩn hoá và mã hoá URL an toàn cho Cổng thanh toán (xử lý khoảng trắng và tiếng Việt)
     */
    public static function encodeUrl(string $url): string
    {
        $parts = parse_url($url);
        if ($parts === false) {
            return $url;
        }

        $scheme   = isset($parts['scheme']) ? $parts['scheme'] . '://' : '';
        $host     = $parts['host'] ?? '';
        $port     = isset($parts['port']) ? ':' . $parts['port'] : '';
        $user     = $parts['user'] ?? '';
        $pass     = isset($parts['pass']) ? ':' . $parts['pass'] : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = $parts['path'] ?? '';
        $query    = isset($parts['query']) ? '?' . $parts['query'] : '';
        $fragment = isset($parts['fragment']) ? '#' . $parts['fragment'] : '';

        if ($path !== '') {
            $segments = explode('/', $path);
            $encodedSegments = array_map(function ($seg) {
                return rawurlencode(rawurldecode($seg));
            }, $segments);
            $path = implode('/', $encodedSegments);
        }

        return "$scheme$user$pass$host$port$path$query$fragment";
    }

    /**
     * Static wrapper cho createCheckoutInstance
     */
    public static function createCheckout(array $payment): array
    {
        $instance = new self();
        return $instance->createCheckoutInstance($payment);
    }

    /**
     * HMAC-SHA256 (base64) trên chuỗi "field=value" nối bằng dấu phẩy, giống SDK chính thức.
     *
     * @param array<string, string> $fields
     */
    public function sign(array $fields, ?string $secretKey = null): string
    {
        $key = $secretKey ?? (string)($this->config['secret_key'] ?? '');
        $signed = [];
        foreach ($fields as $field => $value) {
            if (in_array($field, self::SIGNED_FIELDS, true)) {
                $signed[] = $field . '=' . $value;
            }
        }
        return base64_encode(hash_hmac('sha256', implode(',', $signed), $key, true));
    }

    /**
     * IPN hợp lệ khi header X-Secret-Key trùng khoá IPN ta đặt trên dashboard SePay.
     */
    public function verifyIpnKeyInstance(?string $given): bool
    {
        $expected = (string) ($this->config['ipn_secret'] ?? '');
        return $expected !== '' && is_string($given) && hash_equals($expected, $given);
    }

    public static function verifyIpnKey(?string $given): bool
    {
        $instance = new self();
        return $instance->verifyIpnKeyInstance($given);
    }

    /**
     * Đối soát: hỏi SePay chi tiết đơn theo mã hoá đơn (GET /v1/order/detail/{invoice}, Basic Auth).
     *
     * @return array<string, mixed> JSON SePay trả về
     */
    public function retrieveOrderInstance(string $invoiceNumber): array
    {
        if (!$this->isConfigured()) {
            throw new RuntimeException('SePay chưa cấu hình SEPAY_MERCHANT_ID / SEPAY_SECRET_KEY trong .env');
        }

        $url = $this->apiUrl() . '/order/detail/' . rawurlencode($invoiceNumber);
        $auth = base64_encode($this->config['merchant_id'] . ':' . $this->config['secret_key']);

        $transport = self::$staticTransport ?? $this->transport ?? Closure::fromCallable([self::class, 'curlTransport']);
        $response = $transport('GET', $url, [
            'Authorization' => 'Basic ' . $auth,
            'Content-Type' => 'application/json',
        ], null);

        $json = json_decode($response['body'], true);
        if ($response['status'] >= 400) {
            $detail = is_array($json) ? (string) ($json['message'] ?? $json['error'] ?? '') : '';
            throw new RuntimeException('Gọi API SePay thất bại: HTTP ' . $response['status'] . ($detail !== '' ? ' — ' . $detail : ''));
        }
        if (!is_array($json)) {
            throw new RuntimeException('Phản hồi API SePay không hợp lệ.');
        }
        return $json;
    }

    public static function retrieveOrder(string $invoiceNumber): array
    {
        $instance = new self();
        return $instance->retrieveOrderInstance($invoiceNumber);
    }

    /**
     * Rút phần thông tin đơn từ phản hồi API.
     * Chấp nhận cả dạng {"data": {...}} lẫn {"data": {"order": {...}}}.
     *
     * @param array<string, mixed> $response
     * @return array<string, mixed>
     */
    public static function orderFromResponse(array $response): array
    {
        $data = is_array($response['data'] ?? null) ? $response['data'] : $response;
        return is_array($data['order'] ?? null) ? $data['order'] : $data;
    }

    /**
     * SePay gửi số tiền dạng chuỗi ("50000.00"). Đổi sang số nguyên VND mà không qua float.
     * Trả null nếu có phần lẻ khác 0 hoặc không phải số. (Chuẩn 100% Base Shop).
     */
    public static function parseAmount(mixed $value): ?int
    {
        if (is_int($value)) {
            return $value;
        }
        if (!is_string($value) || preg_match('/^\s*(\d+)(?:\.0+)?\s*$/', $value, $m) !== 1) {
            return null;
        }
        return (int) $m[1];
    }

    /** Mô tả không dấu, không ký tự đặc biệt cho chắc chắn hiển thị đúng ở mọi ngân hàng. */
    public static function sanitizeDescription(string $text): string
    {
        $ascii = self::toAscii($text);
        $ascii = trim((string) preg_replace('/[^A-Za-z0-9 ]+/', ' ', $ascii));
        $ascii = (string) preg_replace('/\s+/', ' ', $ascii);
        return $ascii === '' ? 'Thanh toan don hang' : substr($ascii, 0, 255);
    }

    private static function toAscii(string $text): string
    {
        $map = [
            'a' => 'áàảãạăắằẳẵặâấầẩẫậ', 'd' => 'đ', 'e' => 'éèẻẽẹêếềểễệ', 'i' => 'íìỉĩị',
            'o' => 'óòỏõọôốồổỗộơớờởỡợ', 'u' => 'úùủũụưứừửữự', 'y' => 'ýỳỷỹỵ',
        ];
        $search = $replace = [];
        foreach ($map as $ascii => $chars) {
            foreach (mb_str_split($chars) as $char) {
                $search[] = $char;
                $search[] = mb_strtoupper($char, 'UTF-8');
                $replace[] = $ascii;
                $replace[] = strtoupper($ascii);
            }
        }
        return str_replace($search, $replace, $text);
    }

    /**
     * @param array<string, string> $headers
     * @return array{status: int, body: string}
     */
    private static function curlTransport(string $method, string $url, array $headers, ?string $body): array
    {
        if (!function_exists('curl_init')) {
            throw new RuntimeException('Cần bật extension curl trong php.ini để gọi API SePay.');
        }
        $lines = [];
        foreach ($headers as $name => $value) {
            $lines[] = $name . ': ' . $value;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_HTTPHEADER => $lines,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
        ]);
        if ($body !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $body);
        }
        $result = curl_exec($ch);
        $error = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($result === false) {
            throw new RuntimeException('Không kết nối được SePay: ' . $error);
        }
        return ['status' => $status, 'body' => (string) $result];
    }
}
