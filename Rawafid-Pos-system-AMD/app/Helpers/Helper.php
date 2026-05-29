<?php
class Helper {
    public static function redirect(string $url): void {
        header("Location: {$url}");
        exit;
    }

    public static function formatMoney(float $amount, string $currency = null): string {
        if ($currency === null) {
            $db = Database::getInstance();
            $currency = $db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'currency'") ?: 'ج.س';
        }
        return number_format($amount, 2) . ' ' . $currency;
    }

    public static function formatDate(string $date, string $format = 'd/m/Y H:i'): string {
        return date($format, strtotime($date));
    }

    public static function generateInvoiceNumber(): string {
        $db = Database::getInstance();
        $prefix  = $db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'invoice_prefix'") ?: 'INV';
        $counter = (int)($db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = 'invoice_counter'") ?: 1);
        $db->query("UPDATE settings SET setting_value = ? WHERE setting_key = 'invoice_counter'", [$counter + 1]);
        return $prefix . '-' . str_pad($counter, 5, '0', STR_PAD_LEFT);
    }

    public static function generatePurchaseNumber(): string {
        return 'PUR-' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);
    }

    public static function sanitize(string $str): string {
        return htmlspecialchars(strip_tags(trim($str)), ENT_QUOTES, 'UTF-8');
    }

    public static function isAjax(): bool {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    public static function jsonResponse(array $data): void {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    public static function uploadImage(array $file, string $subDir = 'products'): string|false {
        $allowed  = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $maxSize  = MAX_UPLOAD_SIZE;
        if (!in_array($file['type'], $allowed)) return false;
        if ($file['size'] > $maxSize) return false;
        $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $ext;
        $destDir  = UPLOAD_PATH . $subDir . '/';
        if (!is_dir($destDir)) mkdir($destDir, 0755, true);
        if (move_uploaded_file($file['tmp_name'], $destDir . $filename)) {
            return $subDir . '/' . $filename;
        }
        return false;
    }

    public static function getSettings(): array {
        $db   = Database::getInstance();
        $rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
        $out  = [];
        foreach ($rows as $row) {
            $out[$row['setting_key']] = $row['setting_value'];
        }
        return $out;
    }

    public static function e(mixed $val): string {
        return htmlspecialchars((string)$val, ENT_QUOTES, 'UTF-8');
    }

    public static function getSetting(string $key, mixed $default = ''): mixed {
        $db = Database::getInstance();
        $v  = $db->fetchColumn("SELECT setting_value FROM settings WHERE setting_key = ?", [$key]);
        return $v !== false ? $v : $default;
    }
}
