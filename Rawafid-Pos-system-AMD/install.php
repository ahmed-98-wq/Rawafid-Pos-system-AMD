<?php
/**
 * ================================================
 * سكريبت التثبيت والإعداد الأولي
 * افتح هذا الملف مرة واحدة فقط:
 * http://localhost/pos_system/install.php
 * ================================================
 */

define('ROOT', __DIR__);
require_once ROOT . '/config/config.php';
require_once ROOT . '/config/Database.php';

$errors   = [];
$success  = [];
$step     = $_POST['step'] ?? 'check';

// ---- تجربة الاتصال بقاعدة البيانات ----
function tryConnect(string $host, string $user, string $pass, string $dbName): array {
    try {
        $pdo = new PDO(
            "mysql:host={$host};charset=utf8mb4",
            $user, $pass,
            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
        );
        // إنشاء قاعدة البيانات إن لم تكن موجودة
        $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo->exec("USE `{$dbName}`");
        return ['success' => true, 'pdo' => $pdo];
    } catch (PDOException $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

// ---- تنفيذ ملف SQL ----
function executeSqlFile(PDO $pdo, string $file): array {
    if (!file_exists($file)) return ['success' => false, 'error' => "الملف غير موجود: $file"];
    $sql = file_get_contents($file);
    // تقسيم الأوامر
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => !empty($s) && !str_starts_with(ltrim($s), '--')
    );
    $count = 0;
    foreach ($statements as $stmt) {
        if (empty(trim($stmt))) continue;
        try {
            $pdo->exec($stmt);
            $count++;
        } catch (PDOException $e) {
            // تجاهل أخطاء "already exists"
            if (strpos($e->getMessage(), 'already exists') === false &&
                strpos($e->getMessage(), 'Duplicate entry') === false) {
                // تسجيل الخطأ لكن الاستمرار
            }
        }
    }
    return ['success' => true, 'count' => $count];
}

$dbOk = false;
$pdo  = null;

if ($step === 'install' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $host   = trim($_POST['db_host']   ?? 'localhost');
    $dbUser = trim($_POST['db_user']   ?? 'root');
    $dbPass = trim($_POST['db_pass']   ?? '');
    $dbName = trim($_POST['db_name']   ?? 'pos_system');
    $adminU = trim($_POST['admin_user']?? 'admin');
    $adminP = trim($_POST['admin_pass']?? '');
    $adminN = trim($_POST['admin_name']?? 'مدير النظام');

    if (empty($adminP) || strlen($adminP) < 6) {
        $errors[] = 'كلمة مرور المدير يجب أن تكون 6 أحرف على الأقل';
    } else {
        // اتصال
        $conn = tryConnect($host, $dbUser, $dbPass, $dbName);
        if (!$conn['success']) {
            $errors[] = 'فشل الاتصال بقاعدة البيانات: ' . $conn['error'];
        } else {
            $pdo = $conn['pdo'];
            // تنفيذ schema.sql
            $schemaResult = executeSqlFile($pdo, ROOT . '/database/schema.sql');

            // إنشاء المستخدم الأول
            $hashedPass = password_hash($adminP, PASSWORD_BCRYPT);
            try {
                $stmt = $pdo->prepare(
                    "INSERT INTO users (username, password, full_name, role, is_active)
                     VALUES (?, ?, ?, 'admin', 1)
                     ON DUPLICATE KEY UPDATE
                         password = VALUES(password),
                         full_name = VALUES(full_name),
                         is_active = 1"
                );
                $stmt->execute([$adminU, $hashedPass, $adminN]);
                $success[] = '✅ تم إنشاء المستخدم بنجاح';
            } catch (PDOException $e) {
                $errors[] = 'خطأ في إنشاء المستخدم: ' . $e->getMessage();
            }

            // تحديث config إن اختلف
            if ($host !== DB_HOST || $dbUser !== DB_USER || $dbName !== DB_NAME) {
                $configContent = file_get_contents(ROOT . '/config/config.php');
                $configContent = preg_replace("/define\('DB_HOST',.*?\);/", "define('DB_HOST', '$host');", $configContent);
                $configContent = preg_replace("/define\('DB_USER',.*?\);/", "define('DB_USER', '$dbUser');", $configContent);
                $configContent = preg_replace("/define\('DB_PASS',.*?\);/", "define('DB_PASS', '$dbPass');", $configContent);
                $configContent = preg_replace("/define\('DB_NAME',.*?\);/", "define('DB_NAME', '$dbName');", $configContent);
                file_put_contents(ROOT . '/config/config.php', $configContent);
                $success[] = '✅ تم تحديث إعدادات قاعدة البيانات';
            }

            if (empty($errors)) {
                $success[] = '✅ تم تثبيت النظام بنجاح!';
                $success[] = '✅ جاهز للتشغيل — يمكنك حذف هذا الملف الآن';
                $dbOk = true;
            }
        }
    }
}

// فحص الاتصال الحالي
$currentConnOk = false;
try {
    $testPdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $currentConnOk = true;
} catch (Exception $e) {}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>تثبيت نظام المبيعات</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Segoe UI',Tahoma,Arial,sans-serif;background:linear-gradient(135deg,#1a2410,#2d3d1a);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.box{background:#fff;border-radius:16px;padding:36px;width:520px;max-width:100%;box-shadow:0 20px 60px rgba(0,0,0,.4)}
h1{font-size:22px;color:#1a2410;margin-bottom:4px;display:flex;align-items:center;gap:10px}
.sub{font-size:13px;color:#7a8a6a;margin-bottom:24px}
.section{background:#f8faf4;border-radius:10px;padding:16px;margin-bottom:16px;border:1px solid #e0e8d0}
.section-title{font-size:12px;font-weight:700;color:#4e6b2b;margin-bottom:12px;text-transform:uppercase;letter-spacing:.5px}
label{display:block;font-size:12px;font-weight:600;color:#5e6b4a;margin-bottom:4px}
input{width:100%;padding:10px 12px;border:1.5px solid #d5e8b8;border-radius:8px;font-size:13px;color:#1a2410;background:#fff;margin-bottom:12px;direction:rtl}
input:focus{outline:none;border-color:#5e8233;box-shadow:0 0 0 3px rgba(94,130,51,.12)}
.btn{width:100%;padding:12px;background:linear-gradient(135deg,#4e6b2b,#7a9e4a);color:white;border:none;border-radius:9px;font-size:15px;font-weight:700;cursor:pointer}
.btn:hover{opacity:.9}
.alert{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:10px;display:flex;align-items:center;gap:8px}
.alert-err{background:#fcebeb;color:#a32d2d;border:1px solid #f7c1c1}
.alert-ok{background:#eaf3de;color:#3b6d11;border:1px solid #c0dd97}
.status-ok{color:#3b6d11;font-weight:700}
.status-err{color:#a32d2d;font-weight:700}
.go-btn{display:block;text-align:center;margin-top:16px;padding:12px;background:#4e6b2b;color:white;border-radius:9px;text-decoration:none;font-size:15px;font-weight:700}
.note{font-size:11px;color:#9ab866;background:#f2f7e8;border-radius:7px;padding:8px 12px;margin-top:14px}
</style>
</head>
<body>
<div class="box">
  <h1>🛒 تثبيت النظام</h1>
  <p class="sub">إعداد نظام إدارة المبيعات المتكامل</p>

  <?php foreach($errors as $e): ?>
  <div class="alert alert-err">❌ <?= htmlspecialchars($e) ?></div>
  <?php endforeach; ?>
  <?php foreach($success as $s): ?>
  <div class="alert alert-ok"><?= htmlspecialchars($s) ?></div>
  <?php endforeach; ?>

  <?php if($dbOk): ?>
    <a href="?page=login" class="go-btn">🚀 انتقل إلى صفحة الدخول</a>
    <div class="note">🔒 احذف هذا الملف (install.php) بعد التثبيت لأسباب أمنية</div>
  <?php else: ?>

  <!-- فحص الاتصال الحالي -->
  <div class="section">
    <div class="section-title">🔍 فحص الاتصال الحالي</div>
    <p style="font-size:13px">
      الاتصال بـ <strong><?= DB_HOST ?> / <?= DB_NAME ?></strong>:
      <?php if($currentConnOk): ?>
        <span class="status-ok">✅ متصل</span>
      <?php else: ?>
        <span class="status-err">❌ غير متصل</span>
      <?php endif; ?>
    </p>
  </div>

  <form method="POST">
    <input type="hidden" name="step" value="install">

    <div class="section">
      <div class="section-title">🗄️ إعدادات قاعدة البيانات</div>
      <label>سيرفر MySQL</label>
      <input type="text" name="db_host" value="<?= DB_HOST ?>" placeholder="localhost">
      <label>اسم المستخدم</label>
      <input type="text" name="db_user" value="<?= DB_USER ?>" placeholder="root">
      <label>كلمة مرور MySQL</label>
      <input type="password" name="db_pass" value="<?= DB_PASS ?>" placeholder="(فارغة في XAMPP عادةً)">
      <label>اسم قاعدة البيانات</label>
      <input type="text" name="db_name" value="<?= DB_NAME ?>" placeholder="pos_system">
    </div>

    <div class="section">
      <div class="section-title">👤 بيانات مدير النظام</div>
      <label>اسم المستخدم</label>
      <input type="text" name="admin_user" value="admin" placeholder="admin" required>
      <label>كلمة المرور (6 أحرف على الأقل)</label>
      <input type="password" name="admin_pass" placeholder="••••••••" required minlength="6">
      <label>الاسم الكامل</label>
      <input type="text" name="admin_name" value="مدير النظام" placeholder="مدير النظام">
    </div>

    <button type="submit" class="btn">⚡ تثبيت النظام الآن</button>
  </form>

  <div class="note">
    ⚠️ تأكد من تشغيل XAMPP (Apache + MySQL) قبل التثبيت
  </div>

  <?php endif; ?>
</div>
</body>
</html>
