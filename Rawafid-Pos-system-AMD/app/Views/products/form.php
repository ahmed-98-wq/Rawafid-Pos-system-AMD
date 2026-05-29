<?php
/* =============================================
   نموذج إضافة / تعديل منتج
   $product = null عند الإضافة
   $product = [...] عند التعديل
============================================= */
$isEdit   = isset($product) && !empty($product);
$currency = $settings['currency'] ?? 'ج.س';
$p        = $product ?? [];
$formTitle = $isEdit ? 'تعديل المنتج' : 'إضافة منتج جديد';
$formIcon  = $isEdit ? 'ti-edit' : 'ti-plus';
?>

<!-- ======= Page Header ======= -->
<div class="page-header">
  <h1 class="page-title">
    <svg class="icon icon-tabler <?= $formIcon ?> icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> <?= $formTitle ?>
  </h1>
  <div class="page-actions">
    <a href="?page=products" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-right" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l14 0" />
  <path d="M13 18l6 -6" />
  <path d="M13 6l6 6" />
</svg> العودة للقائمة
    </a>
  </div>
</div>

<!-- ======= Form ======= -->
<form method="POST"
      action="?page=products&action=<?= $isEdit ? 'update' : 'store' ?>"
      enctype="multipart/form-data"
      id="product-form"
      novalidate>

  <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
  <?php if ($isEdit): ?>
  <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
  <?php endif; ?>

  <div class="pf-grid">

    <!-- ========= العمود الأيمن ========= -->
    <div class="pf-col">

      <!-- بطاقة: المعلومات الأساسية -->
      <div class="card pf-card">
        <div class="pf-section-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 9h.01" />
  <path d="M11 12h1v4h1" />
</svg> المعلومات الأساسية
        </div>

        <div class="form-group">
          <label class="form-label required">اسم المنتج</label>
          <input type="text"
                 name="name"
                 id="prod-name"
                 class="form-input pf-input-lg"
                 value="<?= Helper::e($p['name'] ?? '') ?>"
                 placeholder="أدخل اسم المنتج بوضوح..."
                 required
                 autofocus>
          <div class="field-err" id="err-name"></div>
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">التصنيف</label>
            <select name="category_id" class="form-select">
              <option value="">— بدون تصنيف —</option>
              <?php foreach (($categories ?? []) as $c): ?>
              <option value="<?= $c['id'] ?>"
                <?= (int)($p['category_id'] ?? 0) === (int)$c['id'] ? 'selected' : '' ?>>
                <?= Helper::e($c['name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">وحدة القياس</label>
            <select name="unit" class="form-select">
              <?php
              $units = ['قطعة','علبة','كرتون','كيس','زجاجة','لتر','كيلو','غرام','متر','درزن','باكيت','أنبوب','أمبول'];
              $selUnit = $p['unit'] ?? 'قطعة';
              foreach ($units as $u): ?>
              <option value="<?= $u ?>" <?= $selUnit === $u ? 'selected' : '' ?>><?= $u ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">الباركود</label>
          <div class="barcode-wrap">
            <input type="text"
                   name="barcode"
                   id="prod-barcode"
                   class="form-input mono"
                   value="<?= Helper::e($p['barcode'] ?? '') ?>"
                   placeholder="اسكن أو أدخل الباركود يدوياً">
            <button type="button" class="barcode-gen-btn" onclick="generateBarcode()" title="توليد باركود تلقائي">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-barcode" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7v-1a2 2 0 0 1 2 -2h2" />
  <path d="M4 17v1a2 2 0 0 0 2 2h2" />
  <path d="M16 4h2a2 2 0 0 1 2 2v1" />
  <path d="M16 20h2a2 2 0 0 0 2 -2v-1" />
  <path d="M5 11h1v2h-1z" />
  <path d="M10 11l1 0v2l-1 0z" />
  <path d="M14 11h1v2h-1z" />
  <path d="M19 11h1v2h-1z" />
</svg> توليد
            </button>
          </div>
          <small class="form-hint">اتركه فارغاً إذا لم يكن للمنتج باركود</small>
        </div>

        <div class="form-group">
          <label class="form-label">الوصف</label>
          <textarea name="description"
                    class="form-textarea"
                    rows="3"
                    placeholder="وصف مختصر للمنتج (اختياري)..."><?= Helper::e($p['description'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- بطاقة: المخزون والتواريخ -->
      <div class="card pf-card">
        <div class="pf-section-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
  <path d="M16 5.25l-8 4.5" />
</svg> المخزون والتواريخ
        </div>

        <div class="form-row">
          <div class="form-group">
            <label class="form-label">
              <?= $isEdit ? 'الكمية الحالية' : 'الكمية الابتدائية' ?>
            </label>
            <?php if ($isEdit): ?>
            <!-- عند التعديل: عرض الكمية فقط مع رابط لتعديلها -->
            <div class="qty-display-wrap">
              <span class="qty-display <?= (float)($p['stock_qty'] ?? 0) <= 0 ? 'qty-out' : ((float)($p['stock_qty'] ?? 0) <= (float)($p['min_stock_alert'] ?? 5) ? 'qty-low' : 'qty-ok') ?>">
                <?= number_format($p['stock_qty'] ?? 0, 2) ?> <?= Helper::e($p['unit'] ?? '') ?>
              </span>
              <small class="form-hint" style="display:block;margin-top:4px">
                لتعديل الكمية → <a href="?page=inventory" style="color:var(--olive-500)">صفحة المخزون</a>
              </small>
            </div>
            <?php else: ?>
            <input type="number"
                   name="stock_qty"
                   class="form-input"
                   step="0.001"
                   min="0"
                   value="<?= $p['stock_qty'] ?? 0 ?>"
                   placeholder="0">
            <small class="form-hint">الكمية الموجودة حالياً عند الإضافة</small>
            <?php endif; ?>
          </div>

          <div class="form-group">
            <label class="form-label">حد التنبيه (أقل كمية)</label>
            <input type="number"
                   name="min_stock_alert"
                   class="form-input"
                   step="0.001"
                   min="0"
                   value="<?= $p['min_stock_alert'] ?? 5 ?>"
                   placeholder="5">
            <small class="form-hint">يُنبَّه عند وصول المخزون لهذا الحد</small>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">تاريخ انتهاء الصلاحية</label>
          <input type="date"
                 name="expiry_date"
                 class="form-input"
                 value="<?= $p['expiry_date'] ?? '' ?>"
                 min="<?= date('Y-m-d') ?>">
          <small class="form-hint">اتركه فارغاً للمنتجات التي لا تنتهي صلاحيتها</small>
        </div>
      </div>

    </div><!-- end right col -->

    <!-- ========= العمود الأيسر ========= -->
    <div class="pf-col">

      <!-- بطاقة: الأسعار -->
      <div class="card pf-card">
        <div class="pf-section-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg> الأسعار
        </div>

        <div class="form-group">
          <label class="form-label">سعر الشراء (<?= $currency ?>)</label>
          <div class="price-input-wrap">
            <span class="price-prefix"><?= $currency ?></span>
            <input type="number"
                   name="purchase_price"
                   id="purchase-price"
                   class="form-input price-input"
                   step="0.01"
                   min="0"
                   value="<?= $p['purchase_price'] ?? 0 ?>"
                   placeholder="0.00"
                   oninput="calcProfit()">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label required">سعر البيع (<?= $currency ?>)</label>
          <div class="price-input-wrap">
            <span class="price-prefix"><?= $currency ?></span>
            <input type="number"
                   name="sale_price"
                   id="sale-price"
                   class="form-input price-input"
                   step="0.01"
                   min="0.01"
                   value="<?= $p['sale_price'] ?? '' ?>"
                   placeholder="0.00"
                   required
                   oninput="calcProfit()">
          </div>
          <div class="field-err" id="err-price"></div>
        </div>

        <!-- عرض هامش الربح -->
        <div id="profit-box" class="profit-preview" style="display:none">
          <div class="pp-row">
            <span>هامش الربح</span>
            <strong id="pp-margin">—</strong>
          </div>
          <div class="pp-row">
            <span>نسبة الربح</span>
            <strong id="pp-pct">—</strong>
          </div>
          <div class="profit-bar-wrap">
            <div class="profit-bar" id="pp-bar" style="width:0%"></div>
          </div>
        </div>
      </div>

      <!-- بطاقة: صورة المنتج -->
      <div class="card pf-card">
        <div class="pf-section-title">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-photo" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M15 8h.01" />
  <path d="M3 6a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v12a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3v-12z" />
  <path d="M3 16l5 -5c.928 -.893 2.072 -.893 3 0l5 5" />
  <path d="M14 14l1 -1c.928 -.893 2.072 -.893 3 0l3 3" />
</svg> صورة المنتج
        </div>

        <!-- معاينة الصورة الحالية -->
        <div id="img-preview-wrap" style="<?= !empty($p['image']) ? '' : 'display:none' ?>;margin-bottom:10px;text-align:center">
          <img id="img-preview"
               src="<?= !empty($p['image']) ? BASE_URL . '/uploads/' . Helper::e($p['image']) : '' ?>"
               alt="صورة المنتج"
               style="max-height:120px;border-radius:8px;border:1px solid var(--border)">
          <div style="font-size:11px;color:var(--text-3);margin-top:4px">الصورة الحالية</div>
        </div>

        <!-- منطقة رفع الصورة -->
        <div class="upload-area" id="upload-area" onclick="document.getElementById('img-input').click()">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cloud-upload" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 18a4.6 4.4 0 0 1 0 -9a5 4.5 0 0 1 11 2h1a3.5 3.5 0 0 1 0 7h-1" />
  <path d="M9 15l3 -3l3 3" />
  <path d="M12 12l0 9" />
</svg>
          <p><?= !empty($p['image']) ? 'انقر لتغيير الصورة' : 'انقر لرفع صورة' ?></p>
          <small>JPG, PNG, WebP — حتى 5 ميجابايت</small>
        </div>
        <input type="file"
               name="image"
               id="img-input"
               accept="image/jpeg,image/png,image/webp,image/gif"
               style="display:none"
               onchange="previewImage(this)">
      </div>

      <!-- بطاقة: أزرار الحفظ -->
      <div class="card pf-card">
        <button type="submit" class="btn btn-primary btn-block btn-submit-main" id="submit-btn">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg>
        <!-- <i class="ti ti-<?= $isEdit ? 'device-floppy' : 'plus' ?>"></i> -->
          <?= $isEdit ? 'حفظ التعديلات' : 'إضافة المنتج' ?>
        </button>

        <?php if ($isEdit): ?>
        <a href="?page=products&action=create" class="btn btn-outline btn-block" style="margin-top:8px">
           <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إضافة منتج جديد آخر
        </a>
        <?php endif; ?>

        <a href="?page=products" class="btn btn-ghost btn-block" style="margin-top:6px">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> إلغاء
        </a>
      </div>

    </div><!-- end left col -->
  </div><!-- end grid -->
</form>

<!-- ======= Styles ======= -->
<style>
.pf-grid{display:grid;grid-template-columns:1fr 1fr;gap:16px;align-items:start}
.pf-col{display:flex;flex-direction:column;gap:14px}
.pf-card{padding:18px}
.pf-section-title{font-size:12px;font-weight:700;color:var(--olive-600);margin-bottom:14px;padding-bottom:10px;border-bottom:1px solid var(--border);display:flex;align-items:center;gap:7px;text-transform:uppercase;letter-spacing:.4px}
.pf-input-lg{font-size:15px;padding:11px 14px;font-weight:500}
.field-err{font-size:11px;color:#a32d2d;margin-top:3px;display:none}
.field-err.show{display:block}
/* Barcode */
.barcode-wrap{display:flex;gap:6px}
.barcode-wrap .form-input{flex:1;font-family:'Courier New',monospace;letter-spacing:.5px}
.barcode-gen-btn{padding:0 12px;border:1.5px solid var(--border);border-radius:8px;background:var(--bg);color:var(--olive-600);cursor:pointer;font-size:12px;font-weight:600;display:flex;align-items:center;gap:5px;white-space:nowrap;transition:.15s}
.barcode-gen-btn:hover{background:var(--olive-50);border-color:var(--olive-300)}
/* Price */
.price-input-wrap{display:flex;align-items:center}
.price-prefix{background:var(--bg);border:1.5px solid var(--border);border-left:none;border-radius:8px 0 0 8px;padding:9px 10px;font-size:12px;color:var(--text-2);white-space:nowrap;order:2}
.price-input{border-radius:0 8px 8px 0 !important;order:1;flex:1}
/* Profit */
.profit-preview{background:var(--bg);border-radius:8px;padding:12px;margin-top:-4px;border:1px solid var(--border)}
.pp-row{display:flex;justify-content:space-between;font-size:12px;color:var(--text-2);padding:2px 0}
.profit-bar-wrap{height:5px;background:var(--border);border-radius:3px;margin-top:8px;overflow:hidden}
.profit-bar{height:5px;background:var(--olive-500);border-radius:3px;transition:width .4s}
/* Qty display */
.qty-display-wrap{padding:4px 0}
.qty-display{display:inline-block;padding:5px 12px;border-radius:7px;font-size:14px;font-weight:700}
.qty-ok{background:#eaf3de;color:#3b6d11}
.qty-low{background:#faeeda;color:#854f0b}
.qty-out{background:#fcebeb;color:#a32d2d}
/* Upload */
.upload-area{border:2px dashed var(--border);border-radius:9px;padding:22px;text-align:center;cursor:pointer;color:var(--text-3);transition:.2s}
.upload-area:hover{border-color:var(--olive-400);color:var(--olive-500);background:var(--olive-50)}
.upload-area i{font-size:30px;display:block;margin-bottom:6px}
.upload-area p{font-size:13px;font-weight:500}
.upload-area small{font-size:11px}
/* Submit */
.btn-submit-main{padding:13px;font-size:15px;justify-content:center}
@media(max-width:900px){.pf-grid{grid-template-columns:1fr}}
</style>

<!-- ======= JavaScript ======= -->
<script>
// ---- حساب هامش الربح ----
function calcProfit() {
  const buy  = parseFloat(document.getElementById('purchase-price').value) || 0;
  const sell = parseFloat(document.getElementById('sale-price').value)    || 0;
  const box  = document.getElementById('profit-box');

  if (buy > 0 && sell > 0) {
    const margin = sell - buy;
    const pct    = Math.round((margin / buy) * 100);
    const clr    = margin >= 0 ? 'var(--olive-600)' : '#a32d2d';

    document.getElementById('pp-margin').textContent = margin.toFixed(2) + ' <?= $currency ?>';
    document.getElementById('pp-margin').style.color = clr;
    document.getElementById('pp-pct').textContent    = pct + '%';
    document.getElementById('pp-pct').style.color    = clr;
    document.getElementById('pp-bar').style.width    = Math.min(Math.max(pct, 0), 100) + '%';
    document.getElementById('pp-bar').style.background = clr;
    box.style.display = 'block';
  } else {
    box.style.display = 'none';
  }
}

// ---- توليد باركود ----
function generateBarcode() {
  const ts  = Date.now().toString();
  const code = ts.slice(-10);
  document.getElementById('prod-barcode').value = code;
  // تأثير بصري بسيط
  const input = document.getElementById('prod-barcode');
  input.style.borderColor = 'var(--olive-400)';
  input.style.background  = 'var(--olive-50)';
  setTimeout(function() {
    input.style.borderColor = '';
    input.style.background  = '';
  }, 1000);
}

// ---- معاينة الصورة ----
function previewImage(input) {
  if (input.files && input.files[0]) {
    const file   = input.files[0];
    const reader = new FileReader();
    reader.onload = function(e) {
      const prev = document.getElementById('img-preview');
      const wrap = document.getElementById('img-preview-wrap');
      prev.src          = e.target.result;
      wrap.style.display = 'block';
      // تغيير نص منطقة الرفع
      const area = document.getElementById('upload-area');
      area.querySelector('p').textContent = file.name;
    };
    reader.readAsDataURL(file);
  }
}

// Drag & Drop للصورة
const uploadArea = document.getElementById('upload-area');
uploadArea.addEventListener('dragover', function(e) {
  e.preventDefault();
  this.style.borderColor = 'var(--olive-500)';
  this.style.background  = 'var(--olive-50)';
});
uploadArea.addEventListener('dragleave', function() {
  this.style.borderColor = '';
  this.style.background  = '';
});
uploadArea.addEventListener('drop', function(e) {
  e.preventDefault();
  this.style.borderColor = '';
  this.style.background  = '';
  const file = e.dataTransfer.files[0];
  if (file && file.type.startsWith('image/')) {
    const input = document.getElementById('img-input');
    const dt    = new DataTransfer();
    dt.items.add(file);
    input.files = dt.files;
    previewImage(input);
  }
});

// ---- التحقق من الصحة ----
document.getElementById('product-form').addEventListener('submit', function(e) {
  let valid = true;

  const name  = document.getElementById('prod-name').value.trim();
  const price = parseFloat(document.getElementById('sale-price').value);
  const errN  = document.getElementById('err-name');
  const errP  = document.getElementById('err-price');

  // إخفاء الأخطاء القديمة
  errN.classList.remove('show');
  errP.classList.remove('show');

  if (!name) {
    errN.textContent = 'يرجى إدخال اسم المنتج';
    errN.classList.add('show');
    document.getElementById('prod-name').focus();
    valid = false;
  }

  if (!price || price <= 0) {
    errP.textContent = 'يرجى إدخال سعر بيع صحيح أكبر من صفر';
    errP.classList.add('show');
    if (valid) document.getElementById('sale-price').focus();
    valid = false;
  }

  if (!valid) {
    e.preventDefault();
    return;
  }

  // تعطيل زر الإرسال لمنع التكرار
  const btn = document.getElementById('submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> جاري الحفظ...';
});

// تشغيل حساب الربح عند تحميل الصفحة (للتعديل)
calcProfit();
</script>
