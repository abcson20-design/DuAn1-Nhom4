<?php

declare(strict_types=1);

/**
 * Cấu hình các cổng thanh toán - Đồng bộ 100% với dự án Base Enterprise
 * Tài liệu: https://developer.sepay.vn/en/cong-thanh-toan/bat-dau
 */

return [
    'gateways' => [
        'cod' => [
            'enabled' => true,
            'name' => 'Thanh toán khi nhận hàng (Ship COD)',
            'description' => 'Được kiểm tra hàng trước khi thanh toán cho shipper.',
        ],

        // Đăng ký: https://my.sepay.vn → Payment Gateway → Register → chọn Sandbox → lấy MERCHANT ID + SECRET KEY
        'sepay' => [
            'enabled' => (bool) env('SEPAY_ENABLED', true),
            // sandbox | production (mỗi môi trường có bộ khoá riêng)
            'env' => (string) env('SEPAY_ENV', 'sandbox'),
            'merchant_id' => (string) env('SEPAY_MERCHANT_ID', ''),
            // Dùng để ký form checkout và gọi API. Chỉ hiện 1 lần khi tạo trên dashboard.
            'secret_key' => (string) env('SEPAY_SECRET_KEY', ''),
            // Khoá IPN do bạn tự đặt (Payment Gateway → Configuration → IPN, auth type SECRET_KEY).
            // SePay gửi lại trong header X-Secret-Key. Để trống => mọi IPN bị từ chối (401).
            'ipn_secret' => (string) env('SEPAY_IPN_SECRET', ''),
            // localhost không nhận được IPN từ SePay. Bật true khi dev: khi khách về success_url,
            // server tự hỏi API SePay rồi cập nhật đơn (vẫn kiểm tra số tiền, chống trùng).
            // Production: đặt false, chỉ IPN được cập nhật đơn.
            'reconcile_on_return' => (bool) env('SEPAY_RECONCILE_ON_RETURN', true),
            'name' => 'Cổng SePay Trực Tuyến',
            'description' => 'Quét VietQR chuyển khoản tự động, QR Napas 24/7, thẻ.',
        ],
    ],
];
