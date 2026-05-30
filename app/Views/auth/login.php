<!DOCTYPE html>
<html lang="ar" dir="rtl" id="html-root">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول — <?= Helper::e($settings['business_name'] ?? APP_NAME) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= APP_VERSION ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tabler-icons.min.css?v=<?= APP_VERSION ?>">
</head>
<body class="login-page">
<div class="particles" id="particles"></div>

<div class="login-card">
  <div class="login-logo">
    <?php
    $icons = ['pharmacy'=>'💊','grocery'=>'🛒','shoes'=>'👟','warehouse'=>'📦','distributor'=>'🚚','agency'=>'🏢'];
    $type  = $settings['business_type'] ?? 'general';
    echo $icons[$type] ?? '🏪';
    ?>
  </div>
  <div class="login-title"><?= Helper::e($settings['business_name'] ?? APP_NAME) ?></div>
  <div class="login-sub">نظام إدارة المبيعات المتكامل</div>

  <?php if($error): ?>
  <div class="login-error"><i class="ti ti-alert-circle"></i> <?= Helper::e($error) ?></div>
  <?php endif; ?>

  <form method="POST" action="?page=login" autocomplete="off">
    <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">

    <div class="login-field">
      <label for="username"><i class="ti ti-user"></i> اسم المستخدم</label>
      <input class="login-input" type="text" id="username" name="username"
             placeholder="أدخل اسم المستخدم"
             value="<?= Helper::e($_COOKIE['remember_user'] ?? '') ?>"
             required autofocus>
    </div>

    <div class="login-field">
      <label for="password"><i class="ti ti-lock"></i> كلمة المرور</label>
      <div class="pass-wrap">
        <input class="login-input" type="password" id="password" name="password"
               placeholder="أدخل كلمة المرور" required>
        <button type="button" class="pass-toggle" onclick="togglePass()" id="pass-btn">
          <i class="ti ti-eye" id="pass-icon"></i>
        </button>
      </div>
    </div>

    <div class="remember-row">
      <input type="checkbox" id="remember" name="remember" <?= isset($_COOKIE['remember_user']) ? 'checked' : '' ?>>
      <label for="remember">تذكرني</label>
    </div>

    <button type="submit" class="login-btn">
      <i class="ti ti-login"></i> دخول النظام
    </button>
  </form>

  <div class="login-footer">
    🔒 النظام يعمل بالكامل بدون إنترنت &nbsp;|&nbsp; XAMPP Local
  </div>
</div>

<script>
function togglePass() {
  const inp = document.getElementById('password');
  const ico = document.getElementById('pass-icon');
  if (inp.type === 'password') {
    inp.type = 'text';
    ico.className = 'ti ti-eye-off';
  } else {
    inp.type = 'password';
    ico.className = 'ti ti-eye';
  }
}
// Particles
const p = document.getElementById('particles');
for (let i = 0; i < 20; i++) {
  const el = document.createElement('div');
  el.className = 'particle';
  el.style.cssText = `left:${Math.random()*100}%;animation-duration:${8+Math.random()*12}s;animation-delay:${Math.random()*10}s;width:${2+Math.random()*4}px;height:${2+Math.random()*4}px;opacity:${0.1+Math.random()*.3}`;
  p.appendChild(el);
}
// Dark mode persist
if (localStorage.getItem('dark') === '1') document.getElementById('html-root').setAttribute('data-theme','dark');
</script>
</body>
</html>
