<?php
// ============================================================
// main.php — الـ Layout الرئيسي
// المتغيرات $page و $action متاحة من index.php عبر global
// ============================================================

// ضمان وجود المتغيرات الأساسية دائماً
if (!isset($page))     $page     = $_GET['page']   ?? 'dashboard';
if (!isset($action))   $action   = $_GET['action']  ?? 'index';
if (!isset($settings)) $settings = Helper::getSettings();

$pageTitles = [
    'dashboard' => 'لوحة التحكم',
    'pos'       => 'نقطة البيع',
    'products'  => 'المنتجات',
    'customers' => 'العملاء',
    'suppliers' => 'الموردون',
    'reports'   => 'التقارير',
    'inventory' => 'المخزون',
    'settings'  => 'الإعدادات',
    'users'     => 'المستخدمون',
    'purchases' => 'المشتريات',
    'returns'   => 'المرتجعات',
];

$businessIcons = [
    'pharmacy'    => '💊',
    'grocery'     => '🛒',
    'shoes'       => '👟',
    'warehouse'   => '📦',
    'distributor' => '🚚',
    'agency'      => '🏢',
];
$btype     = $settings['business_type'] ?? 'general';
$bicon     = $businessIcons[$btype] ?? '🏪';
$bname     = $settings['business_name'] ?? APP_NAME;
$curPage   = $pageTitles[$page] ?? 'لوحة التحكم';

// جلب عدد المخزون المنخفض للـ sidebar والـ topbar
try {
    $db2           = Database::getInstance();
    $lowStockCount = (int)$db2->fetchColumn(
        "SELECT COUNT(*) FROM products WHERE stock_qty <= min_stock_alert AND is_active = 1"
    );
} catch (Exception $e) {
    $lowStockCount = 0;
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl" id="html-root">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($curPage . ' — ' . $bname, ENT_QUOTES, 'UTF-8') ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<!-- Tabler Icons - يعمل بالإنترنت، أو انسخ الملفات محلياً -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
<style>
/* Fallback لو الإنترنت غير متاح - أيقونات نصية بسيطة */
.ti::before { font-style: normal; }
</style>
</head>
<body>

<!-- Page Loader -->
<div id="page-loader" class="page-loader">
  <div class="loader-spinner"></div>
</div>

<!-- Toast Container -->
<div id="toast-container" class="toast-container"></div>

<!-- App Wrapper -->
<div class="app-wrapper">

  <!-- ============ SIDEBAR ============ -->
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">
      <div class="logo-icon"><?= $bicon ?></div>
      <div class="logo-text">
        <div class="logo-name"><?= htmlspecialchars($bname, ENT_QUOTES, 'UTF-8') ?></div>
        <div class="logo-version">v<?= APP_VERSION ?></div>
      </div>
    </div>

    <nav class="sidebar-nav">

      <div class="nav-group-label">الرئيسية</div>

      <?php if (Auth::can('dashboard')): ?>
      <a href="?page=dashboard" class="nav-item <?= $page === 'dashboard' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
  <path d="M5 16h4a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" />
  <path d="M14 4h4a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" />
  <path d="M14 13h4a1 1 0 0 1 1 1v7a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-7a1 1 0 0 1 1 -1" />
</svg><span>لوحة التحكم</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('pos')): ?>
      <a href="?page=pos" class="nav-item <?= $page === 'pos' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash-register" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16 15h-8v3h8v-3z" />
  <path d="M16 10h-8v3h8v-3z" />
  <path d="M5 18h14a1 1 0 0 0 1 -1v-12a1 1 0 0 0 -1 -1h-14a1 1 0 0 0 -1 1v12a1 1 0 0 0 1 1z" />
  <path d="M8 10v-3h8v3" />
  <path d="M12 4v3" />
</svg><span>نقطة البيع</span>
        <span class="nav-badge">POS</span>
      </a>
      <?php endif; ?>

      <div class="nav-group-label">الإدارة</div>

      <?php if (Auth::can('products')): ?>
      <a href="?page=products" class="nav-item <?= $page === 'products' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
  <path d="M2 13.5v5.5l5 3" />
  <path d="M7 16.5l5 -3" />
  <path d="M12 13.5l5 3l5 -3l-5 -3z" />
  <path d="M17 16.5v5.5l5 -3v-5.5" />
  <path d="M17 16.5l-5 -3" />
  <path d="M12 7.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
  <path d="M7 4.5v5.5l5 3" />
  <path d="M12 7.5l5 -3" />
</svg><span>المنتجات</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('inventory')): ?>
      <a href="?page=inventory" class="nav-item <?= $page === 'inventory' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
</svg><span>المخزون</span>
        <?php if ($lowStockCount > 0): ?>
        <span class="nav-badge danger"><?= $lowStockCount ?></span>
        <?php endif; ?>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('customers')): ?>
      <a href="?page=customers" class="nav-item <?= $page === 'customers' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
</svg><span>العملاء</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('suppliers')): ?>
      <a href="?page=suppliers" class="nav-item <?= $page === 'suppliers' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h10v-6h-8m0 -5h5l3 5" />
</svg><span>الموردون</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('purchases')): ?>
      <a href="?page=purchases" class="nav-item <?= $page === 'purchases' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17h-11v-14h-2" />
  <path d="M6 5l14 1l-1 7h-13" />
</svg><span>المشتريات</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('pos')): ?>
      <a href="?page=returns" class="nav-item <?= $page === 'returns' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
</svg><span>المرتجعات</span>
      </a>
      <?php endif; ?>

      <div class="nav-group-label">التقارير</div>

      <?php if (Auth::can('reports')): ?>
      <a href="?page=reports" class="nav-item <?= $page === 'reports' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M9 8m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M15 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M4 20l16 0" />
</svg><span>التقارير</span>
      </a>
      <?php endif; ?>

      <div class="nav-group-label">النظام</div>

      <?php if (Auth::can('settings')): ?>
      <a href="?page=settings" class="nav-item <?= $page === 'settings' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c1 .608 2.296 .07 2.572 -1.065z" />
  <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
</svg><span>الإعدادات</span>
      </a>
      <?php endif; ?>

      <?php if (Auth::can('manage_users')): ?>
      <a href="?page=users" class="nav-item <?= $page === 'users' ? 'active' : '' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-lock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
  <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
  <path d="M12 12l0 2" />
</svg><span>المستخدمون</span>
      </a>
      <?php endif; ?>

    </nav>

    <!-- User Info -->
    <div class="sidebar-footer">
      <div class="user-info">
        <div class="user-avatar-sm">
          <?= mb_substr(Auth::user()['full_name'] ?? 'م', 0, 1) ?>
        </div>
        <div style="flex:1;overflow:hidden">
          <div class="user-name-sm">
            <?= htmlspecialchars(Auth::user()['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>
          </div>
          <div class="user-role-sm">
            <?= ['admin'=>'مدير النظام','cashier'=>'كاشير','warehouse'=>'أمين مخزن','supervisor'=>'مشرف'][Auth::role()] ?? Auth::role() ?>
          </div>
        </div>
        <a href="?page=logout" class="logout-btn" title="تسجيل الخروج">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-logout" width="26" height="26" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" />
  <path d="M9 12h12l-3 -3" />
  <path d="M18 15l3 -3" />
</svg>
        </a>
      </div>
    </div>
  </aside>
  <!-- ============ END SIDEBAR ============ -->

  <!-- ============ MAIN CONTENT ============ -->
  <div class="main-content">

    <!-- Top Bar -->
    <header class="topbar">
      <div class="topbar-right">
        <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="قائمة">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-menu-2" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 6l16 0" />
  <path d="M4 12l16 0" />
  <path d="M4 18l16 0" />
</svg>
        </button>
        <nav class="breadcrumb" aria-label="موقعك الحالي">
          <a href="?page=dashboard" style="color:var(--text-3);text-decoration:none;font-size:13px">الرئيسية</a>
          <?php if ($page !== 'dashboard'): ?>
          <span style="color:var(--text-3);margin:0 6px">/</span>
          <span style="color:var(--text);font-weight:600;font-size:13px"><?= htmlspecialchars($curPage, ENT_QUOTES, 'UTF-8') ?></span>
          <?php endif; ?>
        </nav>
      </div>

      <div class="topbar-left">
        <!-- Dark Mode Toggle -->
        <button class="topbar-btn" onclick="toggleTheme()" id="theme-btn" title="الوضع الليلي / النهاري">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-moon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3c.132 0 .263 0 .393 .007a7.5 7.5 0 0 0 7.92 12.446a9 9 0 1 1 -8.313 -12.454z" />
</svg>
        </button>

        <!-- Notifications -->
        <div class="topbar-btn" onclick="toggleNotifications()" style="cursor:pointer;position:relative" title="التنبيهات">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
  <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
</svg>
          <?php if ($lowStockCount > 0): ?>
          <span class="notif-badge"><?= $lowStockCount ?></span>
          <?php endif; ?>
        </div>

        <!-- User Avatar -->
        <div class="user-avatar" title="<?= htmlspecialchars(Auth::user()['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
          <?= mb_substr(Auth::user()['full_name'] ?? 'م', 0, 1) ?>
        </div>
      </div>
    </header>

    <!-- Flash Messages -->
    <?php if ($msg = Session::getFlash('success')): ?>
    <div class="alert alert-success" style="margin:12px 20px 0">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M9 12l2 2l4 -4" />
</svg> <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>
    <?php if ($msg = Session::getFlash('error')): ?>
    <div class="alert alert-danger" style="margin:12px 20px 0">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 8v4" />
  <path d="M12 16h.01" />
</svg> <?= htmlspecialchars($msg, ENT_QUOTES, 'UTF-8') ?>
    </div>
    <?php endif; ?>

    <!-- ============ PAGE CONTENT ============ -->
    <main class="page-content">
      <?php
      $viewMap = [
          'dashboard' => 'dashboard/index.php',
          'pos'       => 'pos/index.php',
          'products'  => 'products/index.php',
          'customers' => 'customers/index.php',
          'suppliers' => 'suppliers/index.php',
          'reports'   => 'reports/index.php',
          'inventory' => 'inventory/index.php',
          'settings'  => 'settings/index.php',
          'users'     => 'users/index.php',
          'purchases' => 'purchases/index.php',
          'returns'   => 'returns/index.php',
      ];

      // تحديد مسار الـ View
      $baseView = $viewMap[$page] ?? 'dashboard/index.php';

      // المرتجعات لها views خاصة حسب الـ action
      if ($page === 'returns') {
          if ($action === 'search') {
              $viewPath = ROOT . '/app/Views/returns/search.php';
          } elseif ($action === 'printReturn') {
              // الطباعة تستخدم layout خاص بدون sidebar
              $viewPath = ROOT . '/app/Views/returns/print.php';
          } else {
              $viewPath = ROOT . '/app/Views/returns/index.php';
          }
      } elseif ($action === 'create' || $action === 'edit') {
          $viewPath = ROOT . '/app/Views/' . str_replace('index.php', 'form.php', $baseView);
      } else {
          $viewPath = ROOT . '/app/Views/' . $baseView;
      }

      if (file_exists($viewPath)) {
          require $viewPath;
      } else {
          echo '<div class="empty-state">
                  <i class="ti ti-tools" style="font-size:48px;color:var(--text-3);display:block;margin-bottom:12px"></i>
                  <p style="color:var(--text-3)">هذه الصفحة غير موجودة: ' . htmlspecialchars($viewPath) . '</p>
                </div>';
      }
      ?>
    </main>
    <!-- ============ END PAGE CONTENT ============ -->

  </div>
  <!-- ============ END MAIN CONTENT ============ -->

</div><!-- end app-wrapper -->

<!-- ============ NOTIFICATIONS PANEL ============ -->
<div class="notif-panel" id="notif-panel">
  <div class="notif-header">
    <span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-bell" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 5a2 2 0 1 1 4 0a7 7 0 0 1 4 6v3a4 4 0 0 0 2 3h-16a4 4 0 0 0 2 -3v-3a7 7 0 0 1 4 -6" />
  <path d="M9 17v1a3 3 0 0 0 6 0v-1" />
</svg> تنبيهات المخزون</span>
    <button onclick="toggleNotifications()" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:18px">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg>
    </button>
  </div>
  <div class="notif-list">
    <?php
    try {
        $alertProducts = $db2->fetchAll(
            "SELECT name, stock_qty, min_stock_alert, unit
             FROM products
             WHERE stock_qty <= min_stock_alert AND is_active = 1
             ORDER BY stock_qty ASC
             LIMIT 10"
        );
        if (!empty($alertProducts)):
            foreach ($alertProducts as $ap): ?>
        <div class="notif-item <?= $ap['stock_qty'] <= 0 ? 'danger' : 'warning' ?>">
          <i class="ti ti-<?= $ap['stock_qty'] <= 0 ? 'circle-x' : 'alert-triangle' ?>"></i>
          <div>
            <div class="notif-title"><?= htmlspecialchars($ap['name'], ENT_QUOTES, 'UTF-8') ?></div>
            <div class="notif-text">
              <?= $ap['stock_qty'] <= 0
                  ? 'نفذ المخزون تماماً'
                  : 'متبقي: ' . number_format($ap['stock_qty'], 0) . ' ' . htmlspecialchars($ap['unit'] ?? '', ENT_QUOTES, 'UTF-8') ?>
            </div>
          </div>
        </div>
        <?php endforeach;
        else: ?>
        <div class="notif-empty">
          <svg xmlns="http://www.w3.org/2000/svg" style="color:#3b6d11" class="icon icon-tabler icon-tabler-circle-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M9 12l2 2l4 -4" />
</svg> المخزون في مستوى جيد
        </div>
        <?php endif;
    } catch (Exception $e) {
        echo '<div class="notif-empty">تعذر تحميل التنبيهات</div>';
    }
    ?>
  </div>
</div>
<div class="overlay" id="overlay" onclick="closeAll()"></div>

<script src="<?= BASE_URL ?>/assets/js/app.js"></script>
</body>
</html>
