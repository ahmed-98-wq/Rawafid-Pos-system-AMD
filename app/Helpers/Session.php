<?php
class Session {
    public static function start(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_name(SESSION_NAME);
            session_set_cookie_params([
                'lifetime' => SESSION_LIFETIME,
                'path'     => '/',
                'httponly' => true,
                'samesite' => 'Lax',
            ]);
            session_start();
        }
    }

    public static function set(string $key, mixed $value): void {
        $_SESSION[$key] = $value;
    }

    public static function get(string $key, mixed $default = null): mixed {
        return $_SESSION[$key] ?? $default;
    }

    public static function has(string $key): bool {
        return isset($_SESSION[$key]);
    }

    public static function remove(string $key): void {
        unset($_SESSION[$key]);
    }

    public static function flash(string $key, mixed $value): void {
        $_SESSION['_flash'][$key] = $value;
    }

    public static function getFlash(string $key): mixed {
        $value = $_SESSION['_flash'][$key] ?? null;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }

    public static function csrf(): string {
        if (empty($_SESSION[CSRF_TOKEN_KEY])) {
            $_SESSION[CSRF_TOKEN_KEY] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_KEY];
    }

    public static function verifyCsrf(string $token): bool {
        return isset($_SESSION[CSRF_TOKEN_KEY]) && hash_equals($_SESSION[CSRF_TOKEN_KEY], $token);
    }
}
