<?php
/**
 * دالة لعرض أيقونة Tabler بتنسيق SVG مباشر
 */
function render_svg_icon($name, $size = 16, $color = 'currentColor') {
    // مصفوفة تحتوي على مسارات SVG الأساسية للأيقونات التي طلبتها
    $icons = [
        'check' => '<path d="M5 12l5 5l10 -10" />',
        'ban'   => '<path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M5.7 5.7l12.6 12.6" />'
    ];

    $path = isset($icons[$name]) ? $icons[$name] : '';

    echo '<svg xmlns="http://www.w3.org/2000/svg" width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" stroke-width="2" stroke="'.$color.'" fill="none" stroke-linecap="round" stroke-linejoin="round">' . 
         '<path stroke="none" d="M0 0h24v24H0z" fill="none"/>' . 
         $path . 
         '</svg>';
}
?>
<?php
/* =============================================
   إدارة المستخدمين والصلاحيات
============================================= */
Auth::requirePermission('manage_users');
$db    = Database::getInstance();
$users = $db->fetchAll("SELECT * FROM users ORDER BY role, created_at DESC");
$roles = ['admin'=>'مدير النظام','supervisor'=>'مشرف','cashier'=>'كاشير','warehouse'=>'أمين مخزن'];
$roleBadges = ['admin'=>'badge-success','supervisor'=>'badge-info','cashier'=>'badge-warning','warehouse'=>'badge-secondary'];
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shield-lock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3a12 12 0 0 0 8.5 3a12 12 0 0 1 -8.5 15a12 12 0 0 1 -8.5 -15a12 12 0 0 0 8.5 -3" />
  <path d="M12 11m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
  <path d="M12 12l0 3" />
</svg> إدارة المستخدمين</h1>
  <button class="btn btn-primary" onclick="openUserModal()">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
  <path d="M16 19h6" />
  <path d="M19 16v6" />
  <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
</svg> إضافة مستخدم
  </button>
</div>

<!-- إحصائيات -->
<div class="usr-stats">
  <?php
  $rCounts = array_count_values(array_column($users,'role'));
  foreach ($roles as $k=>$v):
    $cnt = $rCounts[$k] ?? 0;
  ?>
  <div class="usr-stat-card">
    <div class="usr-stat-val"><?= $cnt ?></div>
    <div class="usr-stat-lbl"><?= $v ?></div>
  </div>
  <?php endforeach; ?>
</div>

<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>#</th><th>المستخدم</th><th>اسم الدخول</th><th>الدور</th>
          <th>الصلاحيات الأساسية</th><th>آخر دخول</th><th>الحالة</th><th style="width:90px">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u):
          $perms     = ROLE_PERMISSIONS[$u['role']] ?? [];
          $permLabel = implode('، ', array_slice(array_map(fn($p) => [
            'dashboard'=>'لوحة','pos'=>'بيع','products'=>'منتجات',
            'customers'=>'عملاء','reports'=>'تقارير','inventory'=>'مخزون',
            'settings'=>'إعدادات','manage_users'=>'مستخدمين','purchases'=>'مشتريات'
          ][$p] ?? '', $perms), 0, 4));
        ?>
        <tr>
          <td style="color:var(--text-3);font-size:12px"><?= $u['id'] ?></td>
          <td>
            <div style="display:flex;align-items:center;gap:9px">
              <div class="usr-avatar" style="background:<?= ['admin'=>'linear-gradient(135deg,#3a7030,#84bc72)','cashier'=>'linear-gradient(135deg,#1a4a90,#5a9eff)','warehouse'=>'linear-gradient(135deg,#8a5800,#ffca6a)','supervisor'=>'linear-gradient(135deg,#5040a8,#a090ff)'][$u['role']] ?? 'var(--bg)' ?>">
                <?= mb_strtoupper(mb_substr($u['full_name'],0,1)) ?>
              </div>
              <div>
                <div style="font-weight:600;font-size:13px"><?= Helper::e($u['full_name']) ?></div>
                <?php if ($u['email']): ?>
                <div style="font-size:11px;color:var(--text-3)"><?= Helper::e($u['email']) ?></div>
                <?php endif; ?>
              </div>
            </div>
          </td>
          <td class="mono" style="font-size:12px"><?= Helper::e($u['username']) ?></td>
          <td>
            <span class="badge <?= $roleBadges[$u['role']] ?? 'badge-secondary' ?>">
              <?= $roles[$u['role']] ?? $u['role'] ?>
            </span>
          </td>
          <td style="font-size:11px;color:var(--text-3);max-width:180px"><?= $permLabel ?>...</td>
          <td style="font-size:12px;color:var(--text-3)">
            <?= $u['last_login'] ? Helper::formatDate($u['last_login'],'d/m/Y H:i') : '<span style="color:var(--text-4)">لم يدخل بعد</span>' ?>
          </td>
          <td>
            <span class="badge <?= $u['is_active'] ? 'badge-success' : 'badge-secondary' ?>">
              <?= $u['is_active'] ? 'نشط' : 'معطّل' ?>
            </span>
          </td>
          <td>
            <div class="action-btns">
              <button onclick="openUserModal(<?= $u['id'] ?>)" class="action-btn edit" title="تعديل">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 7h-1a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-1" />
  <path d="M20.385 6.585a2.1 2.1 0 0 0 -2.97 0l-9.915 9.915a2 2 0 0 0 -.5 1.5l0 2l2 -0l9.915 -9.915a2.1 2.1 0 0 0 0 -2.97z" />
  <path d="M16 5l3 3" />
</svg>
              </button>
              <?php if ($u['id'] !== Auth::id()): ?>
              <a href="?page=users&action=toggle&id=<?= $u['id'] ?>"
                 class="action-btn <?= $u['is_active'] ? 'delete' : 'view' ?>"
                 title="<?= $u['is_active'] ? 'تعطيل' : 'تفعيل' ?>"
                 onclick="return confirm('<?= $u['is_active'] ? 'تعطيل' : 'تفعيل' ?> هذا المستخدم؟')">
                <div class="status-icon">
    <?php render_svg_icon($u['is_active'] ? 'check' : 'ban', 16, $u['is_active'] ? '#30a449' : '#e03131'); ?>
</div>
                 <!-- <i class="ti ti-<?= $u['is_active'] ? 'ban' : 'check' ?>"></i> -->
              </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- صلاحيات تفصيلية -->
<div class="card">
  <div class="card-header">
    <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-lock" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 13a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2v-6z" />
  <path d="M11 16a1 1 0 1 0 2 0a1 1 0 0 0 -2 0" />
  <path d="M8 11v-4a4 4 0 1 1 8 0v4" />
</svg> جدول الصلاحيات</h3>
  </div>
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr>
          <th>الصلاحية</th>
          <th style="text-align:center">مدير</th>
          <th style="text-align:center">مشرف</th>
          <th style="text-align:center">كاشير</th>
          <th style="text-align:center">مخزن</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $permList = [
          'dashboard' => 'لوحة التحكم','pos' => 'نقطة البيع','products' => 'المنتجات',
          'inventory' => 'المخزون','customers' => 'العملاء','suppliers' => 'الموردون',
          'purchases' => 'المشتريات','reports' => 'التقارير',
          'settings' => 'الإعدادات','manage_users' => 'إدارة المستخدمين',
          'backup' => 'النسخ الاحتياطي','delete_invoices' => 'حذف الفواتير',
        ];
        foreach ($permList as $pkey => $plabel):
        ?>
        <tr>
          <td><?= $plabel ?></td>
          <?php foreach (['admin','supervisor','cashier','warehouse'] as $role): ?>
          <td style="text-align:center">
            <?= in_array($pkey, ROLE_PERMISSIONS[$role] ?? [])
              ? '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#4bcd34" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M9 12l2 2l4 -4" />
</svg>'
              : '<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#b83232" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M10 10l4 4m0 -4l-4 4" />
</svg>' ?>
          </td>
          <?php endforeach; ?>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: إضافة/تعديل مستخدم -->
<div class="modal-overlay" id="user-modal" style="display:none">
  <div class="modal-box" style="max-width:460px">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div class="usr-avatar" id="um-avatar" style="width:38px;height:38px;font-size:16px;background:linear-gradient(135deg,#3a7030,#84bc72)">م</div>
        <div>
          <h3 id="um-title">إضافة مستخدم</h3>
          <p id="um-sub" style="font-size:11px;color:var(--text-3)">إدخال بيانات المستخدم الجديد</p>
        </div>
      </div>
      <button onclick="closeUserModal()" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:20px"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="?page=users&action=store" id="user-form">
      <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
      <input type="hidden" name="id" id="um-id">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label required">الاسم الكامل</label>
          <input type="text" name="full_name" id="um-name" class="form-input" required oninput="updateUmAvatar(this.value)">
        </div>
        <div class="form-group">
          <label class="form-label required">اسم الدخول</label>
          <input type="text" name="username" id="um-username" class="form-input" required autocomplete="off">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label" id="um-pass-label">كلمة المرور (6 أحرف على الأقل)</label>
        <div style="position:relative">
          <input type="password" name="password" id="um-pass" class="form-input" placeholder="••••••••" autocomplete="new-password">
          <button type="button" onclick="toggleUmPass()" style="position:absolute;left:10px;top:50%;transform:translateY(-50%);background:none;border:none;cursor:pointer;color:var(--text-3);font-size:16px">
            <svg class="icon icon-tabler icon-tabler-eye" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 12a2 2 0 1 0 4 0a2 2 0 0 0 -4 0" />
  <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
</svg>
          </button>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">الدور</label>
          <select name="role" id="um-role" class="form-select" onchange="updateUmAvatar(document.getElementById('um-name').value)">
            <?php foreach ($roles as $k=>$v): ?>
            <option value="<?= $k ?>"><?= $v ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">الهاتف</label>
          <input type="text" name="phone" id="um-phone" class="form-input" placeholder="اختياري">
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">البريد الإلكتروني</label>
        <input type="email" name="email" id="um-email" class="form-input" dir="ltr" placeholder="اختياري">
      </div>
      <div style="display:flex;justify-content:flex-end;gap:8px;margin-top:16px">
        <button type="button" onclick="closeUserModal()" class="btn btn-ghost">إلغاء</button>
        <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
  <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 4l0 4l-4 0l0 -4" />
</svg> حفظ</button>
      </div>
    </form>
  </div>
</div>

<style>
.usr-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:10px;margin-bottom:16px}
.usr-stat-card{background:var(--surface);border:1px solid var(--border);border-radius:9px;padding:14px;text-align:center}
.usr-stat-val{font-size:24px;font-weight:700;color:var(--text)}
.usr-stat-lbl{font-size:11px;color:var(--text-2);margin-top:2px}
.usr-avatar{width:32px;height:32px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-size:12px;font-weight:700;flex-shrink:0}
</style>

<script>
async function openUserModal(id=null) {
  resetUm();
  if (id) {
    document.getElementById('um-title').textContent = 'تعديل بيانات المستخدم';
    document.getElementById('um-sub').textContent   = 'اترك كلمة المرور فارغة للإبقاء على القديمة';
    document.getElementById('um-pass-label').textContent = 'كلمة المرور الجديدة (اتركها فارغة للإبقاء على القديمة)';
    document.getElementById('user-form').action = '?page=users&action=update';
    try {
      const r = await fetch('?page=users&action=getOne&id='+id);
      const d = await r.json();
      const u = d.user; if (!u) return;
      document.getElementById('um-id').value       = u.id;
      document.getElementById('um-name').value     = u.full_name||'';
      document.getElementById('um-username').value = u.username||'';
      document.getElementById('um-phone').value    = u.phone||'';
      document.getElementById('um-email').value    = u.email||'';
      document.getElementById('um-role').value     = u.role||'cashier';
      updateUmAvatar(u.full_name||'');
    } catch(e){}
  }
  document.getElementById('user-modal').style.display='flex';
  setTimeout(()=>document.getElementById('um-name').focus(),150);
}
function closeUserModal(){document.getElementById('user-modal').style.display='none';}
function resetUm(){
  document.getElementById('um-title').textContent='إضافة مستخدم';
  document.getElementById('um-sub').textContent='إدخال بيانات المستخدم الجديد';
  document.getElementById('um-pass-label').textContent='كلمة المرور (6 أحرف على الأقل)';
  document.getElementById('user-form').action='?page=users&action=store';
  document.getElementById('um-id').value='';
  ['um-name','um-username','um-pass','um-phone','um-email'].forEach(i=>{const e=document.getElementById(i);if(e)e.value='';});
  document.getElementById('um-role').value='cashier';
  document.getElementById('um-avatar').textContent='م';
}
function updateUmAvatar(name){
  const el=document.getElementById('um-avatar');
  el.textContent=name?name.charAt(0).toUpperCase():'م';
  const roleColors={admin:'linear-gradient(135deg,#3a7030,#84bc72)',cashier:'linear-gradient(135deg,#1a4a90,#5a9eff)',warehouse:'linear-gradient(135deg,#8a5800,#ffca6a)',supervisor:'linear-gradient(135deg,#5040a8,#a090ff)'};
  const role=document.getElementById('um-role').value;
  el.style.background=roleColors[role]||'var(--bg)';
}
function toggleUmPass(){
  const inp=document.getElementById('um-pass');
  const ico=document.getElementById('um-pass-ico');
  if(inp.type==='password'){inp.type='text';ico.className='ti ti-eye-off';}
  else{inp.type='password';ico.className='ti ti-eye';}
}
</script>
