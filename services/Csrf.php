<?php

declare(strict_types=1);

/**
 * Lớp bảo vệ chống tấn công CSRF (Cross-Site Request Forgery).
 * Kế thừa chuẩn bảo mật từ Base Shop.
 */
final class Csrf
{
    private const KEY = '_csrf_token';

    private static function startSessionIfNeeded(): void
    {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'domain' => '',
                'secure' => $isSecure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            @session_start();
        }
    }

    public static function token(): string
    {
        self::startSessionIfNeeded();

        $token = $_SESSION[self::KEY] ?? null;
        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $_SESSION[self::KEY] = $token;
        }

        return $token;
    }

    public static function verify(?string $token): bool
    {
        self::startSessionIfNeeded();

        $expected = $_SESSION[self::KEY] ?? null;
        if (!is_string($expected) || $expected === '') {
            $expected = self::token();
        }

        return is_string($token) && $token !== '' && hash_equals($expected, $token);
    }

    public static function validateRequest(): bool
    {
        $token = $_POST['_csrf_token'] ?? $_GET['csrf_token'] ?? null;

        if (empty($token)) {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_SERVER['HTTP_X_XSRF_TOKEN'] ?? $_SERVER['HTTP_CSRF_TOKEN'] ?? null;
        }

        if (empty($token) && function_exists('getallheaders')) {
            $headers = getallheaders();
            foreach ($headers as $k => $v) {
                if (in_array(strtolower((string)$k), ['x-csrf-token', 'x-xsrf-token', 'csrf-token'], true)) {
                    $token = $v;
                    break;
                }
            }
        }

        if (empty($token)) {
            $rawInput = @file_get_contents('php://input');
            if ($rawInput) {
                $json = json_decode($rawInput, true);
                if (is_array($json) && !empty($json['_csrf_token'])) {
                    $token = (string)$json['_csrf_token'];
                }
            }
        }

        return self::verify($token);
    }

    public static function field(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
