<?php

declare(strict_types=1);

/**
 * Lớp giới hạn tần suất yêu cầu và chống tấn công Brute-Force độc lập với Session ID.
 * Hoạt động bền vững ngay cả khi kẻ tấn công không lưu cookie hoặc xóa session liên tục.
 */
final class RateLimiter
{
    private static function getStorageDir(): string
    {
        $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'nexvora_cache';
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }
        return $dir;
    }

    /**
     * Lấy IP thực của client an toàn, chống giả mạo header X-Forwarded-For.
     * Chỉ tin X-Forwarded-For nếu REMOTE_ADDR nằm trong danh sách TRUSTED_PROXIES cấu hình trong .env.
     */
    public static function getClientIp(): string
    {
        $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

        // Lấy danh sách IP proxy tin cậy từ cấu hình
        $trustedProxiesStr = defined('TRUSTED_PROXIES') ? TRUSTED_PROXIES : (getenv('TRUSTED_PROXIES') ?: '');
        $trustedProxies = array_filter(array_map('trim', explode(',', $trustedProxiesStr)));

        if (!empty($trustedProxies) && in_array($remoteAddr, $trustedProxies, true)) {
            $forwarded = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['HTTP_CF_CONNECTING_IP'] ?? '';
            if (!empty($forwarded)) {
                $ips = explode(',', $forwarded);
                $clientIp = trim($ips[0]);
                if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                    return $clientIp;
                }
            }
        }

        // Mặc định luôn sử dụng REMOTE_ADDR do web server cung cấp (không thể giả mạo bằng HTTP header)
        return filter_var($remoteAddr, FILTER_VALIDATE_IP) ? $remoteAddr : '127.0.0.1';
    }

    private static function getFilePath(string $action, string $key): string
    {
        $hash = md5($action . '_' . $key);
        return self::getStorageDir() . DIRECTORY_SEPARATOR . "rl_{$hash}.json";
    }

    /**
     * Kiểm tra xem mục tiêu có đang bị khóa không
     */
    public static function isLocked(string $action, string $key): ?int
    {
        $file = self::getFilePath($action, $key);
        if (!file_exists($file)) {
            return null;
        }

        $raw = @file_get_contents($file);
        if (!$raw) {
            return null;
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            return null;
        }

        $lockedUntil = (int)($data['locked_until'] ?? 0);
        if ($lockedUntil > time()) {
            return $lockedUntil - time();
        }

        return null;
    }

    /**
     * Ghi nhận 1 lần thực hiện thất bại (Hit).
     * @param string $action Hành động (VD: 'login_account', 'login_ip')
     * @param string $key Định danh (Email hoặc IP)
     * @param int $maxAttempts Số lần thử tối đa trước khi khóa (mặc định 5)
     * @param int $decaySeconds Thời gian khóa (mặc định 900 giây = 15 phút)
     */
    public static function hit(string $action, string $key, int $maxAttempts = 5, int $decaySeconds = 900): array
    {
        $file = self::getFilePath($action, $key);
        $now = time();
        $data = [
            'attempts' => 0,
            'first_hit' => $now,
            'locked_until' => 0
        ];

        if (file_exists($file)) {
            $raw = @file_get_contents($file);
            $parsed = $raw ? json_decode($raw, true) : null;
            if (is_array($parsed)) {
                $data = $parsed;
            }
        }

        // Nếu đang bị khóa
        if (!empty($data['locked_until']) && $data['locked_until'] > $now) {
            return [
                'allowed' => false,
                'retry_after' => (int)($data['locked_until'] - $now),
                'attempts' => (int)($data['attempts'] ?? $maxAttempts),
                'remaining' => 0
            ];
        }

        // Nếu chu kỳ đã hết hạn (> decaySeconds) -> Reset lại
        if (($now - (int)($data['first_hit'] ?? 0)) > $decaySeconds) {
            $data['attempts'] = 0;
            $data['first_hit'] = $now;
            $data['locked_until'] = 0;
        }

        $data['attempts'] = (int)($data['attempts'] ?? 0) + 1;

        if ($data['attempts'] >= $maxAttempts) {
            $data['locked_until'] = $now + $decaySeconds;
            @file_put_contents($file, json_encode($data), LOCK_EX);
            return [
                'allowed' => false,
                'retry_after' => $decaySeconds,
                'attempts' => $data['attempts'],
                'remaining' => 0
            ];
        }

        @file_put_contents($file, json_encode($data), LOCK_EX);

        return [
            'allowed' => true,
            'retry_after' => 0,
            'attempts' => $data['attempts'],
            'remaining' => max(0, $maxAttempts - $data['attempts'])
        ];
    }

    /**
     * Xóa bỏ lịch sử vi phạm khi đăng nhập thành công
     */
    public static function clear(string $action, string $key): void
    {
        $file = self::getFilePath($action, $key);
        if (file_exists($file)) {
            @unlink($file);
        }
    }
}
