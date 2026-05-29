<?php
class Auth {
    public static function isLoggedIn(): bool {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }

    public static function user(): array|null {
        return $_SESSION['user'] ?? null;
    }

    public static function id(): int|null {
        return $_SESSION['user_id'] ?? null;
    }

    public static function role(): string {
        return $_SESSION['user']['role'] ?? 'cashier';
    }

    public static function can(string $permission): bool {
        $role = self::role();
        $permissions = ROLE_PERMISSIONS[$role] ?? [];
        return in_array($permission, $permissions);
    }

    public static function requirePermission(string $permission): void {
        if (!self::can($permission)) {
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
                http_response_code(403);
                echo json_encode(['success' => false, 'message' => 'غير مصرح لك بهذه العملية']);
                exit;
            }
            Helper::redirect('?page=dashboard&error=unauthorized');
        }
    }

    public static function login(array $user): void {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user']    = $user;
        $_SESSION['login_time'] = time();
        // تحديث وقت آخر دخول
        $db = Database::getInstance();
        $db->update('users', ['last_login' => date('Y-m-d H:i:s')], 'id = ?', [$user['id']]);
    }

    public static function logout(): void {
        session_unset();
        session_destroy();
    }

    public static function attempt(string $username, string $password): array|false {
        $db   = Database::getInstance();
        $user = $db->fetchOne(
            "SELECT * FROM users WHERE username = ? AND is_active = 1",
            [$username]
        );
        if ($user && password_verify($password, $user['password'])) {
            unset($user['password']);
            return $user;
        }
        return false;
    }
}
