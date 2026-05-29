<?php
class SettingsController {
    private Database $db;
    public function __construct() { $this->db = Database::getInstance(); }

    public function index(): void {
        Auth::requirePermission('settings');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
                Session::flash('error', 'طلب غير صالح');
            } else {
                $fields = ['business_name','business_type','currency','currency_code','tax_rate','tax_enabled','low_stock_alert','invoice_prefix','receipt_footer','backup_auto'];
                foreach ($fields as $key) {
                    $val = Helper::sanitize($_POST[$key] ?? '');
                    $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?", [$key, $val, $val]);
                }
                if (!empty($_FILES['logo']['name'])) {
                    $img = Helper::uploadImage($_FILES['logo'], 'logo');
                    if ($img) $this->db->query("INSERT INTO settings (setting_key, setting_value) VALUES ('logo',?) ON DUPLICATE KEY UPDATE setting_value=?", [$img, $img]);
                }
                Session::flash('success', 'تم حفظ الإعدادات بنجاح');
                Helper::redirect('?page=settings');
            }
        }
        $settings = Helper::getSettings();
        // متغيرات الـ Layout
        if (!isset($page))   $page   = 'settings';
        if (!isset($action)) $action = 'index';
        require ROOT . '/app/Views/layouts/main.php';
    }

    public function backup(): void {
        Auth::requirePermission('backup');
        $tables = $this->db->fetchAll("SHOW TABLES");
        $sql = "-- POS System Backup\n-- " . date('Y-m-d H:i:s') . "\nSET NAMES utf8mb4;\n\n";
        foreach ($tables as $tableRow) {
            $table = reset($tableRow);
            $create = $this->db->fetchOne("SHOW CREATE TABLE `$table`");
            $sql .= "DROP TABLE IF EXISTS `$table`;\n" . end($create) . ";\n\n";
            $rows = $this->db->fetchAll("SELECT * FROM `$table`");
            foreach ($rows as $row) {
                $vals = array_map(fn($v) => $v === null ? 'NULL' : "'" . addslashes((string)$v) . "'", array_values($row));
                $sql .= "INSERT INTO `$table` VALUES (" . implode(', ', $vals) . ");\n";
            }
            $sql .= "\n";
        }
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="backup_' . date('Y-m-d_His') . '.sql"');
        echo $sql;
        exit;
    }

    public function restore(): void {
        Auth::requirePermission('backup');
        if (!empty($_FILES['restore_file']['tmp_name'])) {
            $sql = file_get_contents($_FILES['restore_file']['tmp_name']);
            $this->db->getConn()->exec($sql);
            Session::flash('success', 'تمت استعادة النسخة الاحتياطية بنجاح');
        }
        Helper::redirect('?page=settings');
    }
}
