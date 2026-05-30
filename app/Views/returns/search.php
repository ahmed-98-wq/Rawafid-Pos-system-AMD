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
<div class="card mb-4">
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
    <div class="flex flex-wrap gap-3 items-end">
      <div class="u-style-135">
        <label class="form-label">رقم الفاتورة</label>
        <div class="u-style-136">
<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21l14 0" />
  <path d="M5 21l0 -18l2 0l2 0l2 0l2 0l2 0l2 0l2 0l0 18" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M9 15l6 0" />
</svg>
          <!-- <i class="ti ti-receipt u-style-137"></i> -->
          <input type="text" name="q" id="inv-search-input"
                 value="<?= Helper::e($_GET['q'] ?? '') ?>"
                 class="form-input input-search-lg"
                 placeholder="أدخل رقم الفاتورة (مثال: INV-00001)"
                 autofocus required>
        </div>
      </div>
      <button type="submit" class="btn btn-primary u-style-138">
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
<div class="u-style-139">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 8l0 4" />
  <path d="M12 16l.01 0" />
</svg>
<!-- <i class="ti ti-alert-circle u-style-140"></i> -->
  <div>
    <div class="u-style-141">الفاتورة غير موجودة</div>
    <div class="u-style-142"><?= Helper::e($error) ?></div>
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
      <div class="flex gap-2">
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
            <th class="u-style-143"><input type="checkbox" id="check-all" onchange="toggleAll(this)" class="check-accent"></th>
            <th>المنتج</th>
            <th class="text-center">الكمية الأصلية</th>
            <th class="text-center">تم إرجاعه</th>
            <th class="text-center">الكمية المُرجَعة</th>
            <th class="text-left">سعر الوحدة</th>
            <th class="text-left">المبلغ المُسترَد</th>
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
                     onchange="toggleItem(this)" class="check-accent">
              <?php else: ?>
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check text-success" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg>
                <!-- <i class="ti ti-check u-style-144"></i> -->
              <?php endif; ?>
            </td>
            <td>
              <div class="u-style-3"><?= Helper::e($item['product_name']) ?></div>
              <div class="u-style-4"><?= Helper::e($item['unit'] ?? 'قطعة') ?></div>
            </td>
            <td class="u-style-145"><?= number_format($item['quantity'], 0) ?></td>
            <td class="text-center">
              <?php if (($item['already_returned'] ?? 0) > 0): ?>
              <span class="u-style-146"><?= number_format($item['already_returned'], 0) ?></span>
              <?php else: ?>
              <span class="u-style-48">—</span>
              <?php endif; ?>
            </td>
            <td class="text-center">
              <?php if ($canReturn): ?>
              <div class="u-style-147">
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
                <small class="u-style-148">/ <?= number_format($maxReturn, 0) ?></small>
              </div>
              <?php else: ?>
              <span class="badge badge-secondary u-style-149">مُرجَع بالكامل</span>
              <?php endif; ?>
            </td>
            <td class="u-style-150">
              <?= number_format($item['unit_price'], 2) ?> <small class="text-muted"><?= $currency ?></small>
            </td>
            <td class="text-left">
              <span id="line-total-<?= $item['id'] ?>"
                    class="line-total text-danger">
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
  <div class="ret-summary-box is-hidden" id="summary-box">
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
  <div class="card mt-3">
    <div class="form-group mb-0">
      <label class="form-label required">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-message-circle" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 20l1.3 -3.9a9 9 0 1 1 3.4 2.9l-4.7 1" />
</svg> سبب الإرجاع
      </label>
      <div class="u-style-151">
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
<div class="empty-large">
  <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search-off" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5.428 5.428a7 7 0 1 0 9.144 9.144m1.401 -2.603a7 7 0 0 0 -7.942 -7.942" />
  <path d="M21 21l-6 -6" />
  <path d="M3 3l18 18" />
</svg>
<!-- <i class="ti ti-search-off u-style-152"></i> -->
  <p>لم يتم العثور على نتائج</p>
</div>
<?php else: ?>
<!-- الحالة الأولى - لا يوجد بحث -->
<div class="empty-state">
  <div class="u-style-153">🔍</div>
  <h3 class="u-style-154">ابحث عن الفاتورة</h3>
  <p class="u-style-99">أدخل رقم الفاتورة التي تريد إرجاعها في حقل البحث أعلاه</p>
</div>
<?php endif; ?>

<!-- ======= Styles ======= -->
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
  total.style.color = qty > 0 ? 'var(--danger)' : 'var(--text-4)';
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
