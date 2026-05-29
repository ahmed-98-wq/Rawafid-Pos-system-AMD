<?php
/* =============================================
   صفحة البحث عن فاتورة وتنفيذ المرتجع
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
$pmNames  = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
</svg> إرجاع فاتورة</h1>
  <a href="?page=returns" class="btn btn-outline">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-list" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 6l11 0" />
  <path d="M9 12l11 0" />
  <path d="M9 18l11 0" />
  <path d="M5 6l0 .01" />
  <path d="M5 12l0 .01" />
  <path d="M5 18l0 .01" />
</svg> قائمة المرتجعات
  </a>
</div>

<!-- ======= خطوة 1: البحث عن الفاتورة ======= -->
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> البحث عن الفاتورة</h3>
  </div>
  <form method="GET" action="" id="search-form">
    <input type="hidden" name="page"   value="returns">
    <input type="hidden" name="action" value="search">
    <div style="display:flex;gap:10px;align-items:flex-end;flex-wrap:wrap">
      <div style="flex:1;min-width:260px">
        <label class="form-label">رقم الفاتورة</label>
        <div style="position:relative;display:flex;align-items:center">
<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21l14 0" />
  <path d="M5 21l0 -18l2 0l2 0l2 0l2 0l2 0l2 0l2 0l0 18" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M9 15l6 0" />
</svg>
          <!-- <i class="ti ti-receipt" style="position:absolute;right:10px;color:var(--text-3);font-size:15px;pointer-events:none"></i> -->
          <input type="text" name="q" id="inv-search-input"
                 value="<?= Helper::e($_GET['q'] ?? '') ?>"
                 class="form-input" style="padding-right:34px;font-size:15px"
                 placeholder="أدخل رقم الفاتورة (مثال: INV-00001)"
                 autofocus required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary" style="padding:10px 20px">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> بحث
      </button>
    </div>
  </form>
</div>

<?php if (!empty($error)): ?>
<!-- رسالة خطأ -->
<div style="background:var(--expired-bg,#feeaea);border:1px solid var(--expired-border,#f5c0c0);border-radius:10px;padding:16px 20px;margin-bottom:16px;display:flex;align-items:center;gap:12px">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 8l0 4" />
  <path d="M12 16l.01 0" />
</svg>
<!-- <i class="ti ti-alert-circle" style="font-size:24px;color:#b83232;flex-shrink:0"></i> -->
  <div>
    <div style="font-weight:700;color:#b83232;font-size:14px">الفاتورة غير موجودة</div>
    <div style="font-size:13px;color:var(--text-2);margin-top:2px"><?= Helper::e($error) ?></div>
  </div>
</div>
<?php endif; ?>

<?php if ($invoice): ?>
<!-- ======= تفاصيل الفاتورة الأصلية ======= -->
<div class="ret-invoice-info">
  <div class="rii-header">
    <div>
      <div class="rii-num"><?= Helper::e($invoice['invoice_number']) ?></div>
      <div class="rii-date"><?= Helper::formatDate($invoice['created_at'], 'd/m/Y H:i') ?></div>
    </div>
    <div class="rii-meta-grid">
      <div class="rii-meta-item">
        <span class="rii-meta-lbl">العميل</span>
        <span class="rii-meta-val"><?= Helper::e($invoice['customer_name'] ?? 'نقدي') ?></span>
      </div>
      <div class="rii-meta-item">
        <span class="rii-meta-lbl">طريقة الدفع</span>
        <span class="rii-meta-val"><?= $pmNames[$invoice['payment_method']] ?? '—' ?></span>
      </div>
      <div class="rii-meta-item">
        <span class="rii-meta-lbl">إجمالي الفاتورة</span>
        <span class="rii-meta-val ret-total"><?= number_format($invoice['total'], 2) ?> <?= $currency ?></span>
      </div>
    </div>
  </div>
</div>

<!-- ======= خطوة 2: تحديد الأصناف المُرجَعة ======= -->
<form method="POST" action="?page=returns&action=store" id="return-form">
  <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
  <input type="hidden" name="original_invoice_id" value="<?= (int)$invoice['id'] ?>">

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
</svg> أصناف الفاتورة
      </h3>
      <div style="display:flex;gap:8px">
        <button type="button" onclick="selectAll()" class="btn btn-xs btn-outline">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check-all" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 12l5 5l10 -10" />
  <path d="M2 12l5 5m5 -5l5 -5" />
</svg> تحديد الكل
        </button>
        <button type="button" onclick="clearAll()" class="btn btn-xs btn-ghost">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> إلغاء الكل
        </button>
      </div>
    </div>

    <div class="table-wrap">
      <table class="data-table" id="items-table">
        <thead>
          <tr>
            <th style="width:36px"><input type="checkbox" id="check-all" onchange="toggleAll(this)" style="accent-color:var(--olive-500)"></th>
            <th>المنتج</th>
            <th style="text-align:center">الكمية الأصلية</th>
            <th style="text-align:center">تم إرجاعه</th>
            <th style="text-align:center">الكمية المُرجَعة</th>
            <th style="text-align:left">سعر الوحدة</th>
            <th style="text-align:left">المبلغ المُسترَد</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($items as $item):
            $maxReturn = $item['quantity'] - ($item['already_returned'] ?? 0);
            $canReturn = $maxReturn > 0;
          ?>
          <tr class="item-row <?= !$canReturn ? 'already-returned-row' : '' ?>" data-item-id="<?= $item['id'] ?>">
            <td>
              <?php if ($canReturn): ?>
              <input type="checkbox" class="item-check" data-id="<?= $item['id'] ?>"
                     onchange="toggleItem(this)" style="accent-color:var(--olive-500)">
              <?php else: ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="#30a449" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg>
                <!-- <i class="ti ti-check" style="color:var(--text-4);font-size:15px"></i> -->
              <?php endif; ?>
            </td>
            <td>
              <div style="font-weight:600;font-size:13px;color:var(--text)"><?= Helper::e($item['product_name']) ?></div>
              <div style="font-size:11px;color:var(--text-3)"><?= Helper::e($item['unit'] ?? 'قطعة') ?></div>
            </td>
            <td style="text-align:center;font-weight:600"><?= number_format($item['quantity'], 0) ?></td>
            <td style="text-align:center">
              <?php if (($item['already_returned'] ?? 0) > 0): ?>
              <span style="color:#b83232;font-weight:700;font-size:12px"><?= number_format($item['already_returned'], 0) ?></span>
              <?php else: ?>
              <span style="color:var(--text-4);font-size:12px">—</span>
              <?php endif; ?>
            </td>
            <td style="text-align:center">
              <?php if ($canReturn): ?>
              <div style="display:flex;align-items:center;gap:4px;justify-content:center">
                <button type="button" class="qty-btn" onclick="changeRetQty(<?= $item['id'] ?>, -1)">−</button>
                <input type="number"
                       name="return_items[<?= $item['id'] ?>][qty]"
                       id="qty-<?= $item['id'] ?>"
                       class="ret-qty-inp"
                       value="0"
                       min="0"
                       max="<?= $maxReturn ?>"
                       step="1"
                       oninput="calcLine(<?= $item['id'] ?>, <?= $item['unit_price'] ?>)"
                       disabled>
                <button type="button" class="qty-btn" onclick="changeRetQty(<?= $item['id'] ?>, 1)">+</button>
                <small style="color:var(--text-3);font-size:10px">/ <?= number_format($maxReturn, 0) ?></small>
              </div>
              <?php else: ?>
              <span class="badge badge-secondary" style="font-size:11px">مُرجَع بالكامل</span>
              <?php endif; ?>
            </td>
            <td style="text-align:left;font-size:13px">
              <?= number_format($item['unit_price'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small>
            </td>
            <td style="text-align:left">
              <span id="line-total-<?= $item['id'] ?>"
                    style="font-weight:700;color:#b83232;font-size:13px">
                0.00 <?= $currency ?>
              </span>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- ======= ملخص المرتجع ======= -->
  <div class="ret-summary-box" id="summary-box" style="display:none">
    <div class="rsb-header">
      <i class="ti ti-calculator"></i> ملخص المرتجع
    </div>
    <div class="rsb-rows">
      <div class="rsb-row"><span>عدد الأصناف</span><strong id="sum-items">0</strong></div>
      <div class="rsb-row"><span>إجمالي الكميات</span><strong id="sum-qty">0</strong></div>
      <div class="rsb-row rsb-total"><span>المبلغ المُسترَد</span><strong id="sum-total">0.00 <?= $currency ?></strong></div>
    </div>
    <?php if ($invoice['payment_method'] === 'cash'): ?>
    <div class="rsb-note">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 9m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v6a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
  <path d="M14 14a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 9v-2a2 2 0 0 0 -2 -2h-10a2 2 0 0 0 -2 2v6a2 2 0 0 0 2 2h2" />
</svg> سيتم استرداد المبلغ نقداً للعميل
    </div>
    <?php elseif ($invoice['payment_method'] === 'credit'): ?>
    <div class="rsb-note">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-dollar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M20.923 13.045a9 9 0 1 0 -8.85 8.955" />
  <path d="M12 7v5l2 2" />
  <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
  <path d="M19 21v1m0 -8v1" />
</svg> سيتم خصم المبلغ من رصيد العميل
    </div>
    <?php endif; ?>
  </div>

  <!-- ======= سبب الإرجاع ======= -->
  <div class="card" style="margin-top:14px">
    <div class="form-group" style="margin-bottom:0">
      <label class="form-label required">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 20l1.3 -3.9a9 9 0 1 1 3.4 2.9l-4.7 1" />
</svg> سبب الإرجاع
      </label>
      <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:10px">
        <?php foreach(['منتج تالف أو معيب','منتج غير مطابق للمواصفات','خطأ في الفاتورة','طلب العميل','منتج منتهي الصلاحية','أخرى'] as $r): ?>
        <button type="button" class="reason-chip" onclick="setReason('<?= $r ?>')">
          <?= $r ?>
        </button>
        <?php endforeach; ?>
      </div>
      <textarea name="reason" id="reason-input" class="form-textarea" rows="2"
                placeholder="اذكر سبب الإرجاع بوضوح..." required></textarea>
    </div>
  </div>

  <!-- أزرار التأكيد -->
  <div class="ret-action-btns">
    <a href="?page=returns&action=search" class="btn btn-ghost">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> إلغاء
    </a>
    <button type="submit" class="btn-confirm-return" id="confirm-btn" disabled>
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
</svg>
      <span id="confirm-btn-text">تأكيد الإرجاع</span>
    </button>
  </div>

</form>
<?php elseif (!empty($_GET['q'])): ?>
<!-- لم يُعثر على فاتورة وليس هناك خطأ محدد -->
<div style="text-align:center;padding:40px;color:var(--text-3)">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search-off" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5.428 5.428a7 7 0 1 0 9.144 9.144m1.401 -2.603a7 7 0 0 0 -7.942 -7.942" />
  <path d="M21 21l-6 -6" />
  <path d="M3 3l18 18" />
</svg>
<!-- <i class="ti ti-search-off" style="font-size:48px;display:block;margin-bottom:12px;opacity:.35"></i> -->
  <p>لم يتم العثور على نتائج</p>
</div>
<?php else: ?>
<!-- الحالة الأولى - لا يوجد بحث -->
<div style="text-align:center;padding:48px;color:var(--text-3)">
  <div style="font-size:64px;margin-bottom:16px;opacity:.3">🔍</div>
  <h3 style="font-size:16px;color:var(--text-2);margin-bottom:8px">ابحث عن الفاتورة</h3>
  <p style="font-size:13px">أدخل رقم الفاتورة التي تريد إرجاعها في حقل البحث أعلاه</p>
</div>
<?php endif; ?>

<!-- ======= Styles ======= -->
<style>
.ret-invoice-info{background:var(--surface);border:2px solid var(--olive-200);border-radius:12px;overflow:hidden;margin-bottom:16px}
[data-theme="dark"] .ret-invoice-info{border-color:rgba(37,99,235,.3)}
.rii-header{background:linear-gradient(135deg,var(--olive-600),var(--olive-400));padding:16px 20px;color:white;display:flex;justify-content:space-between;align-items:flex-start;gap:16px;flex-wrap:wrap}
[data-theme="dark"] .rii-header{background:linear-gradient(135deg,#1a3460,#2563eb)}
.rii-num{font-size:18px;font-weight:700}
.rii-date{font-size:12px;opacity:.8;margin-top:3px}
.rii-meta-grid{display:flex;gap:20px;flex-wrap:wrap}
.rii-meta-item{display:flex;flex-direction:column;gap:2px;min-width:100px}
.rii-meta-lbl{font-size:10px;opacity:.7;text-transform:uppercase;letter-spacing:.5px}
.rii-meta-val{font-size:13px;font-weight:700}
.ret-total{font-size:15px!important}

.already-returned-row{opacity:.5}
.ret-qty-inp{width:48px;text-align:center;border:1.5px solid var(--border);border-radius:7px;font-size:13px;padding:4px;background:var(--input-bg);color:var(--text)}
.ret-qty-inp:disabled{background:var(--bg);cursor:not-allowed}
.ret-qty-inp:not(:disabled):focus{outline:none;border-color:var(--olive-400)}
[data-theme="dark"] .ret-qty-inp:not(:disabled):focus{border-color:#5a9eff}

.ret-summary-box{background:var(--expired-bg,#feeaea);border:1.5px solid var(--expired-border,#f5c0c0);border-radius:10px;padding:16px 20px;margin-top:14px}
[data-theme="dark"] .ret-summary-box{background:rgba(184,50,50,.1);border-color:rgba(255,138,136,.2)}
.rsb-header{font-size:13px;font-weight:700;color:#b83232;margin-bottom:12px;display:flex;align-items:center;gap:7px}
[data-theme="dark"] .rsb-header{color:#ff8a88}
.rsb-rows{display:flex;flex-direction:column;gap:5px}
.rsb-row{display:flex;justify-content:space-between;font-size:13px;color:var(--text-2)}
.rsb-total{font-size:15px;font-weight:700;color:#b83232;border-top:1px solid rgba(184,50,50,.2);margin-top:6px;padding-top:8px}
[data-theme="dark"] .rsb-total{color:#ff8a88}
.rsb-note{background:rgba(184,50,50,.08);border-radius:7px;padding:8px 12px;font-size:12px;color:#b83232;margin-top:10px;display:flex;align-items:center;gap:6px}
[data-theme="dark"] .rsb-note{background:rgba(255,138,136,.08);color:#ff8a88}

.reason-chip{background:var(--bg);border:1px solid var(--border);border-radius:14px;padding:4px 12px;font-size:12px;cursor:pointer;color:var(--text-2);transition:.15s}
.reason-chip:hover{background:var(--olive-50);border-color:var(--olive-300);color:var(--olive-700)}
[data-theme="dark"] .reason-chip:hover{background:rgba(90,158,255,.08);border-color:#5a9eff;color:#5a9eff}

.ret-action-btns{display:flex;justify-content:flex-end;gap:10px;margin-top:16px;padding-top:14px;border-top:1px solid var(--border)}
.btn-confirm-return{padding:12px 24px;background:linear-gradient(135deg,#b83232,#e05252);color:white;border:none;border-radius:10px;font-size:14px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:.2s;box-shadow:0 2px 10px rgba(184,50,50,.3)}
.btn-confirm-return:hover:not(:disabled){opacity:.92;transform:translateY(-1px)}
.btn-confirm-return:disabled{opacity:.45;cursor:not-allowed;transform:none}
</style>

<!-- ======= JavaScript ======= -->
<script>
const CUR = '<?= $currency ?>';
const prices = {};
<?php if ($invoice): ?>
<?php foreach ($items as $item): ?>
prices[<?= $item['id'] ?>] = <?= (float)$item['unit_price'] ?>;
<?php endforeach; ?>
<?php endif; ?>

function toggleAll(cb) {
  document.querySelectorAll('.item-check').forEach(c => {
    c.checked = cb.checked;
    toggleItem(c);
  });
}

function toggleItem(cb) {
  const id   = cb.dataset.id;
  const inp  = document.getElementById('qty-' + id);
  if (!inp) return;
  inp.disabled = !cb.checked;
  if (cb.checked) {
    if (parseInt(inp.value) === 0) {
      inp.value = inp.max;
    }
  } else {
    inp.value = 0;
  }
  calcLine(id, prices[id]);
  updateSummary();
}

function changeRetQty(id, delta) {
  const inp = document.getElementById('qty-' + id);
  if (!inp || inp.disabled) return;
  const max = parseInt(inp.max);
  inp.value = Math.max(0, Math.min(parseInt(inp.value||0) + delta, max));
  calcLine(id, prices[id]);
  updateSummary();
}

function calcLine(id, price) {
  const inp   = document.getElementById('qty-' + id);
  const total = document.getElementById('line-total-' + id);
  if (!inp || !total) return;
  const qty   = parseFloat(inp.value) || 0;
  const amt   = qty * price;
  total.textContent = amt.toFixed(2) + ' ' + CUR;
  total.style.color = qty > 0 ? '#b83232' : 'var(--text-4)';
  updateSummary();
}

function updateSummary() {
  let totalAmt = 0, totalItems = 0, totalQty = 0;

  document.querySelectorAll('.item-check:checked').forEach(cb => {
    const id  = cb.dataset.id;
    const inp = document.getElementById('qty-' + id);
    if (!inp) return;
    const qty = parseFloat(inp.value) || 0;
    if (qty > 0) {
      totalItems++;
      totalQty += qty;
      totalAmt += qty * (prices[id] || 0);
    }
  });

  const box     = document.getElementById('summary-box');
  const btn     = document.getElementById('confirm-btn');
  const btnText = document.getElementById('confirm-btn-text');

  if (totalItems > 0) {
    document.getElementById('sum-items').textContent = totalItems;
    document.getElementById('sum-qty').textContent   = totalQty.toFixed(0);
    document.getElementById('sum-total').textContent = totalAmt.toFixed(2) + ' ' + CUR;
    box.style.display = 'block';
    btn.disabled      = false;
    btnText.textContent = 'تأكيد إرجاع ' + totalAmt.toFixed(2) + ' ' + CUR;
  } else {
    box.style.display = 'none';
    btn.disabled      = true;
    btnText.textContent = 'تأكيد الإرجاع';
  }
}

function selectAll() {
  document.querySelectorAll('.item-check').forEach(c => {
    c.checked = true; toggleItem(c);
  });
  document.getElementById('check-all').checked = true;
}

function clearAll() {
  document.querySelectorAll('.item-check').forEach(c => {
    c.checked = false; toggleItem(c);
  });
  document.getElementById('check-all').checked = false;
}

function setReason(text) {
  document.getElementById('reason-input').value = text;
}

// تأكيد الإرسال
document.getElementById('return-form')?.addEventListener('submit', function(e) {
  const reason = document.getElementById('reason-input').value.trim();
  if (!reason) {
    e.preventDefault();
    document.getElementById('reason-input').focus();
    showToast('يرجى ذكر سبب الإرجاع', 'error');
    return;
  }

  let hasQty = false;
  document.querySelectorAll('.item-check:checked').forEach(cb => {
    const qty = parseFloat(document.getElementById('qty-' + cb.dataset.id)?.value) || 0;
    if (qty > 0) hasQty = true;
  });

  if (!hasQty) {
    e.preventDefault();
    showToast('حدد كمية لمنتج واحد على الأقل', 'error');
    return;
  }

  const btn = document.getElementById('confirm-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> جاري التنفيذ...';
});
</script>
