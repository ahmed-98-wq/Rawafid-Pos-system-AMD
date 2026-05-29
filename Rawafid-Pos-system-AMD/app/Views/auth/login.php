<!DOCTYPE html>
<html lang="ar" dir="rtl" id="html-root">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>تسجيل الدخول — <?= Helper::e($settings['business_name'] ?? APP_NAME) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.19.0/dist/tabler-icons.min.css">
<style>
body { background: linear-gradient(135deg, #1a2410 0%, #2d3d1a 50%, #1a2410 100%); min-height: 100vh; display:flex; align-items:center; justify-content:center; }
.login-card { background: #fff; border-radius: 20px; padding: 44px 40px; width: 380px; max-width: 95vw; box-shadow: 0 20px 60px rgba(0,0,0,0.4); }
.login-logo { width: 64px; height: 64px; border-radius: 16px; background: linear-gradient(135deg, #4e6b2b, #9ab866); margin: 0 auto 20px; display:flex; align-items:center; justify-content:center; font-size: 32px; }
.login-title { font-size: 22px; font-weight: 700; color: #1a2410; text-align:center; margin-bottom: 4px; }
.login-sub { font-size: 13px; color: #7a8a6a; text-align:center; margin-bottom: 28px; }
.login-field { margin-bottom: 14px; }
.login-field label { display:block; font-size: 12px; font-weight: 600; color: #4e6b2b; margin-bottom: 5px; }
.login-input { width:100%; padding: 12px 14px; border: 1.5px solid #d5e8b8; border-radius: 10px; font-size: 14px; color: #1a2410; background: #f8faf4; direction: rtl; transition: .2s; box-sizing: border-box; }
.login-input:focus { outline:none; border-color: #5e8233; background: #fff; box-shadow: 0 0 0 3px rgba(94,130,51,0.15); }
.pass-wrap { position: relative; }
.pass-toggle { position:absolute; left:12px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:#7a9e4a; font-size:16px; padding:0; }
.remember-row { display:flex; align-items:center; gap:8px; margin-bottom: 20px; font-size: 13px; color: #5e6b4a; }
.remember-row input[type=checkbox] { accent-color: #5e8233; width: 15px; height: 15px; }
.login-btn { width:100%; padding: 13px; background: linear-gradient(135deg, #4e6b2b, #7a9e4a); color: white; border: none; border-radius: 10px; font-size: 15px; font-weight: 700; cursor: pointer; transition: .2s; letter-spacing: .5px; }
.login-btn:hover { opacity: .92; transform: translateY(-1px); }
.login-btn:active { transform: translateY(0); }
.login-error { background: #fcebeb; color: #a32d2d; border: 1px solid #f09595; border-radius: 9px; padding: 10px 14px; font-size: 13px; margin-bottom: 16px; display:flex; align-items:center; gap:8px; }
.login-footer { text-align:center; margin-top: 20px; font-size: 11px; color: #9ab866; background: #f2f7e8; border-radius: 8px; padding: 9px; }
.particles { position:fixed; top:0; left:0; width:100%; height:100%; pointer-events:none; overflow:hidden; }
.particle { position:absolute; width:3px; height:3px; background:rgba(154,184,102,.25); border-radius:50%; animation: float linear infinite; }
@keyframes float { 0%{transform:translateY(100vh) rotate(0deg);opacity:0} 10%{opacity:1} 90%{opacity:.5} 100%{transform:translateY(-100px) rotate(720deg);opacity:0} }
</style>
</head>
<body>
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
