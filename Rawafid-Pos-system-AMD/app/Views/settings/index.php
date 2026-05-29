<?php
/* =============================================
   صفحة الإعدادات
============================================= */
Auth::requirePermission('settings');
$db = Database::getInstance();

// حفظ الإعدادات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Session::verifyCsrf($_POST['csrf_token'] ?? '')) {
        Session::flash('error','طلب غير صالح');
    } else {
        $fields = ['business_name','business_type','currency','currency_code','tax_rate','tax_enabled','low_stock_alert','invoice_prefix','receipt_footer','backup_auto'];
        foreach ($fields as $key) {
            $val = Helper::sanitize($_POST[$key] ?? '');
            $db->query("INSERT INTO settings (setting_key,setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value=?", [$key,$val,$val]);
        }
        if (!empty($_FILES['logo']['name'])) {
            $img = Helper::uploadImage($_FILES['logo'],'logo');
            if ($img) $db->query("INSERT INTO settings (setting_key,setting_value) VALUES ('logo',?) ON DUPLICATE KEY UPDATE setting_value=?",[$img,$img]);
        }
        $settings = Helper::getSettings();
        Session::flash('success','تم حفظ الإعدادات بنجاح ✅');
        Helper::redirect('?page=settings');
    }
}

$s = $settings;
$businessTypes = BUSINESS_TYPES;
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-settings" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10.325 4.317c.426 -1.756 2.924 -1.756 3.35 0a1.724 1.724 0 0 0 2.573 1.066c1.543 -.94 3.31 .826 2.37 2.37a1.724 1.724 0 0 0 1.065 2.572c1.756 .426 1.756 2.924 0 3.35a1.724 1.724 0 0 0 -1.066 2.573c.94 1.543 -.826 3.31 -2.37 2.37a1.724 1.724 0 0 0 -2.572 1.065c-.426 1.756 -2.924 1.756 -3.35 0a1.724 1.724 0 0 0 -2.573 -1.066c-1.543 .94 -3.31 -.826 -2.37 -2.37a1.724 1.724 0 0 0 -1.065 -2.572c-1.756 -.426 -1.756 -2.924 0 -3.35a1.724 1.724 0 0 0 1.066 -2.573c-.94 -1.543 .826 -3.31 2.37 -2.37c.996 .608 2.296 .07 2.572 -1.065z" />
  <path d="M9 12a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
</svg> إعدادات النظام</h1>
</div>

<form method="POST" action="?page=settings" enctype="multipart/form-data" id="settings-form">
  <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">

  <div class="settings-grid">

    <!-- معلومات النشاط -->
    <div class="card">
      <div class="settings-section-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-building-store" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 21l18 0" />
  <path d="M3 7v1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1m0 1a3 3 0 0 0 6 0v-1h-18l2 -4h14l2 4" />
  <path d="M5 21v-10.25" />
  <path d="M19 21v-10.25" />
  <path d="M9 21v-4a2 2 0 0 1 2 -2h2a2 2 0 0 1 2 2v4" />
</svg> معلومات النشاط التجاري</div>
      <div class="form-group">
        <label class="form-label required">اسم النشاط</label>
        <input type="text" name="business_name" class="form-input" value="<?= Helper::e($s['business_name']??'') ?>" required>
      </div>
      <div class="form-group">
        <label class="form-label">نوع النشاط</label>
        <select name="business_type" class="form-select">
          <?php foreach ($businessTypes as $k=>$v): ?>
          <option value="<?= $k ?>" <?= ($s['business_type']??'')===$k?'selected':'' ?>><?= $v ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">الشعار</label>
        <?php if (!empty($s['logo'])): ?>
        <div style="margin-bottom:8px"><img src="<?= BASE_URL ?>/uploads/<?= Helper::e($s['logo']) ?>" style="height:50px;border-radius:8px;border:1px solid var(--border)"></div>
        <?php endif; ?>
        <input type="file" name="logo" class="form-input" accept="image/*">
        <small class="form-hint">PNG أو JPG بدقة 200×200 أو أعلى</small>
      </div>
    </div>

    <!-- الإعدادات المالية -->
    <div class="card">
      <div class="settings-section-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg> الإعدادات المالية</div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">رمز العملة</label>
          <input type="text" name="currency" class="form-input" value="<?= Helper::e($s['currency']??'ج.س') ?>" maxlength="10">
        </div>
        <div class="form-group">
          <label class="form-label">كود العملة</label>
          <input type="text" name="currency_code" class="form-input" value="<?= Helper::e($s['currency_code']??'SDG') ?>" maxlength="5" dir="ltr">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label class="form-label">نسبة الضريبة (%)</label>
          <input type="number" name="tax_rate" class="form-input" step="0.01" min="0" max="100" value="<?= Helper::e($s['tax_rate']??15) ?>">
        </div>
        <div class="form-group">
          <label class="form-label">تفعيل الضريبة</label>
          <select name="tax_enabled" class="form-select">
            <option value="1" <?= ($s['tax_enabled']??1)?'selected':'' ?>>نعم</option>
            <option value="0" <?= !($s['tax_enabled']??1)?'selected':'' ?>>لا</option>
          </select>
        </div>
      </div>
    </div>

    <!-- إعدادات المخزون والفواتير -->
    <div class="card">
      <div class="settings-section-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
  <path d="M16 5.25l-8 4.5" />
</svg> المخزون والفواتير</div>
      <div class="form-group">
        <label class="form-label">حد تنبيه نقص المخزون</label>
        <input type="number" name="low_stock_alert" class="form-input" min="0" value="<?= Helper::e($s['low_stock_alert']??10) ?>">
        <small class="form-hint">تنبيه عند وصول أي منتج لهذه الكمية أو أقل</small>
      </div>
      <div class="form-group">
        <label class="form-label">بادئة رقم الفاتورة</label>
        <input type="text" name="invoice_prefix" class="form-input" value="<?= Helper::e($s['invoice_prefix']??'INV') ?>" maxlength="10" dir="ltr">
        <small class="form-hint">مثال: INV تُنتج INV-00001</small>
      </div>
      <div class="form-group">
        <label class="form-label">نص تذييل الفاتورة</label>
        <textarea name="receipt_footer" class="form-textarea" rows="2"><?= Helper::e($s['receipt_footer']??'') ?></textarea>
      </div>
    </div>

    <!-- النسخ الاحتياطي -->
    <div class="card">
      <div class="settings-section-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 6m-8 0a8 3 0 1 0 16 0a8 3 0 1 0 -16 0" />
  <path d="M4 6v6a8 3 0 0 0 16 0v-6" />
  <path d="M4 12v6a8 3 0 0 0 16 0v-6" />
</svg> النسخ الاحتياطي</div>
      <div class="form-group">
        <label class="form-label">النسخ التلقائي</label>
        <select name="backup_auto" class="form-select">
          <option value="1" <?= ($s['backup_auto']??1)?'selected':'' ?>>مفعّل</option>
          <option value="0" <?= !($s['backup_auto']??1)?'selected':'' ?>>معطّل</option>
        </select>
      </div>
      <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px">
        <a href="?page=settings&action=backup" class="btn btn-outline">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-export" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3" />
  <path d="M4 6v6c0 1.657 3.582 3 8 3c.478 0 .947 -.017 1.402 -.05" />
  <path d="M20 11.5v-5.5" />
  <path d="M12 21c-4.418 0 -8 -1.343 -8 -3v-6" />
  <path d="M16 19h6" />
  <path d="M19 16l3 3l-3 3" />
</svg> تصدير نسخة احتياطية الآن
        </a>
        <label class="btn btn-ghost" style="cursor:pointer;justify-content:center">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-database-import" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 6c0 1.657 3.582 3 8 3s8 -1.343 8 -3s-3.582 -3 -8 -3s-8 1.343 -8 3" />
  <path d="M4 6v6c0 1.657 3.582 3 8 3c.478 0 .947 -.017 1.402 -.05" />
  <path d="M4 12v6c0 1.657 3.582 3 8 3c.18 0 .358 -.003 .534 -.009" />
  <path d="M19 22l-3 -3l3 -3" />
  <path d="M22 19h-6" />
</svg> استعادة من ملف SQL
          <input type="file" name="restore_file" accept=".sql" style="display:none"
                 onchange="if(confirm('هل أنت متأكد من الاستعادة؟ سيتم الكتابة فوق البيانات الحالية!')){this.form.action=\'?page=settings&action=restore\';this.form.submit();}">
        </label>
      </div>
      <div class="stg-info-box">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#7a8e72" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 9h.01" />
  <path d="M11 12h1v4h1" />
</svg>
        النسخة الاحتياطية تحتوي على جميع البيانات بصيغة SQL قابلة للاستعادة.
      </div>
    </div>

  </div>

  <div class="settings-submit">
    <button type="submit" class="btn btn-primary btn-lg">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
  <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 4l0 4l-4 0l0 -4" />
</svg> حفظ جميع الإعدادات
    </button>
  </div>
</form>

<style>
.stg-info-box{background:rgba(90,158,255,.08);border:1px solid rgba(90,158,255,.2);border-radius:8px;padding:10px 12px;font-size:12px;color:var(--text-2);margin-top:10px;display:flex;align-items:flex-start;gap:6px;line-height:1.5}
[data-theme="dark"] .stg-info-box{background:rgba(90,158,255,.06);border-color:rgba(90,158,255,.15)}
</style>
