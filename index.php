<?php
/**
 * ================================================
 * نظام المبيعات المتكامل - نقطة الدخول الرئيسية
 * ================================================
 */

define('ROOT', __DIR__);

require_once ROOT . '/config/config.php';
require_once ROOT . '/config/Database.php';
require_once ROOT . '/app/Helpers/Auth.php';
require_once ROOT . '/app/Helpers/Session.php';
require_once ROOT . '/app/Helpers/Helper.php';

// بدء الجلسة
Session::start();

// ترميز الخرج
header('Content-Type: text/html; charset=utf-8');

// قراءة المسار الحالي
$page   = isset($_GET['page'])   ? trim(preg_replace('/[^a-z_]/', '', $_GET['page']))   : 'dashboard';
$action = isset($_GET['action']) ? trim(preg_replace('/[^a-zA-Z_]/', '', $_GET['action'])) : 'index';

// الصفحات العامة لا تحتاج تسجيل دخول
$publicPages = ['login', 'logout'];

// التحقق من تسجيل الدخول
if (!in_array($page, $publicPages) && !Auth::isLoggedIn()) {
    Helper::redirect('?page=login');
}

// توجيه الصفحة للـ Controller المناسب
$controllerMap = [
    'dashboard'  => 'DashboardController',
    'pos'        => 'PosController',
    'products'   => 'ProductController',
    'customers'  => 'CustomerController',
    'suppliers'  => 'SupplierController',
    'reports'    => 'ReportController',
    'inventory'  => 'InventoryController',
    'settings'   => 'SettingsController',
    'users'      => 'UserController',
    'purchases'  => 'PurchaseController',
    'returns'    => 'ReturnController',
    'login'      => 'AuthController',
    'logout'     => 'AuthController',
];

if (!isset($controllerMap[$page])) {
    Helper::redirect('?page=dashboard');
}

$allowedActions = [
    'dashboard' => ['index'],
    'pos'       => ['index', 'getProducts', 'searchProducts', 'saveInvoice', 'printInvoice'],
    'products'  => ['index', 'create', 'store', 'edit', 'update', 'delete', 'adjustStock'],
    'customers' => ['index', 'store', 'update', 'delete', 'getOne', 'getStats', 'search', 'payment', 'statement'],
    'suppliers' => ['index', 'store', 'update', 'delete', 'getOne', 'payment'],
    'reports'   => ['index', 'exportExcel', 'salesDetail', 'topCustomers'],
    'inventory' => ['index', 'movements', 'bulkAdjust', 'export'],
    'settings'  => ['index', 'backup', 'restore'],
    'users'     => ['index', 'store', 'update', 'toggle', 'getOne', 'resetPassword'],
    'purchases' => ['index', 'store', 'delete', 'view'],
    'returns'   => ['index', 'search', 'store', 'printReturn', 'getInvoice'],
    'login'     => ['login'],
    'logout'    => ['logout'],
];

if (!in_array($action, $allowedActions[$page] ?? ['index'], true)) {
    $action = 'index';
}

if (
    ($page === 'pos' && $action === 'printInvoice' && empty($_GET['id'])) ||
    ($page === 'returns' && $action === 'printReturn' && empty($_GET['id'])) ||
    ($page === 'products' && $action === 'edit' && empty($_GET['id'])) ||
    ($page === 'purchases' && $action === 'view' && empty($_GET['id']))
) {
    $action = 'index';
}

$controllerName = $controllerMap[$page];
$controllerFile = ROOT . "/app/Controllers/{$controllerName}.php";

if (!file_exists($controllerFile)) {
    Helper::redirect('?page=dashboard');
}

require_once $controllerFile;
$controller = new $controllerName();

// تحديد الـ action المطلوب
if ($page === 'logout') {
    $action = 'logout';
} elseif ($page === 'login') {
    $action = 'login';
}

// تنفيذ الـ action
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    $controller->index();
}
