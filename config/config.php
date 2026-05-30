<?php
// ============================================
// إعدادات النظام الرئيسية
// ============================================

define('APP_NAME', 'نظام المبيعات المتكامل');
define('APP_VERSION', '2.0');
define('BASE_PATH', dirname(__DIR__));

// ====================================================
// BASE_URL ديناميكي — يعمل مع أي اسم مجلد تلقائياً
// ====================================================
(function() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host     = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // المسار من جذر الموقع إلى مجلد المشروع
    $scriptDir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // إزالة /index.php من النهاية إن وُجد
    $scriptDir = rtrim($scriptDir, '/');
    define('BASE_URL', $protocol . '://' . $host . $scriptDir);
})();

// إعدادات قاعدة البيانات
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'pos_system');
define('DB_CHARSET', 'utf8mb4');

// إعدادات الجلسة
define('SESSION_NAME', 'pos_session');
define('SESSION_LIFETIME', 28800); // 8 ساعات

// إعدادات الأمان
define('CSRF_TOKEN_KEY', 'csrf_token');

// مسارات الملفات
define('UPLOAD_PATH', BASE_PATH . '/uploads/');
define('UPLOAD_URL', BASE_URL . '/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5 MB

// أنواع النشاط التجاري
define('BUSINESS_TYPES', [
    'pharmacy'     => 'صيدلية',
    'grocery'      => 'بقالة / سوبر ماركت',
    'shoes'        => 'محل أحذية',
    'warehouse'    => 'مخزن / مستودع',
    'distributor'  => 'شركة توزيع',
    'agency'       => 'توكيل تجاري',
    'electronics'  => 'إلكترونيات',
    'clothing'     => 'ملابس وأزياء',
    'general'      => 'تجارة عامة',
]);

// صلاحيات كل دور
define('ROLE_PERMISSIONS', [
    'admin' => [
        'dashboard', 'pos', 'products', 'customers', 'suppliers',
        'reports', 'inventory', 'settings', 'users', 'purchases',
        'returns', 'delete_invoices', 'manage_users', 'backup'
    ],
    'supervisor' => [
        'dashboard', 'pos', 'products', 'customers', 'suppliers',
        'reports', 'inventory', 'purchases', 'returns'
    ],
    'cashier' => [
        'dashboard', 'pos', 'customers', 'reports_basic', 'returns'
    ],
    'warehouse' => [
        'dashboard', 'products', 'inventory', 'purchases', 'suppliers'
    ],
]);
