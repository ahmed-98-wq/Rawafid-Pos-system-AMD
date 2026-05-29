<?php
/* =============================================
   صفحة المشتريات
============================================= */
$currency  = $settings['currency'] ?? 'ج.س';
$db        = Database::getInstance();
$suppliers = $db->fetchAll("SELECT id, name FROM suppliers ORDER BY name");
$products  = $db->fetchAll("SELECT id, name, purchase_price, unit, stock_qty FROM products WHERE is_active=1 ORDER BY name");

$monthTotal = (float)$db->fetchColumn(
    "SELECT COALESCE(SUM(total),0) FROM purchases WHERE DATE_FORMAT(created_at,'%Y-%m')=?",
    [date('Y-m')]
);
$stMap   = ['paid'=>['مدفوع','badge-success'],'pending'=>['معلّق','badge-warning'],'partial'=>['جزئي','badge-info']];
$pmNames = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17h-11v-14h-2" />
  <path d="M6 5l14 1l-1 7h-13" />
</svg> المشتريات</h1>
  <button class="btn btn-primary" onclick="openModal('pur-modal')">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> فاتورة شراء جديدة
  </button>
</div>

<!-- Stats -->
<div class="mini-stats" style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:40px;height:40px;border-radius:9px;background:#e6f0ff;color:#1a50a8;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-3 -2l-2 2l-3 -2z" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M13 15l2 0" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">فواتير هذا الشهر</div><div style="font-size:20px;font-weight:700;color:var(--text)"><?= count($purchases ?? []) ?></div></div>
  </div>
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:40px;height:40px;border-radius:9px;background:#eaf5e0;color:#3a7030;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">إجمالي هذا الشهر</div><div style="font-size:20px;font-weight:700;color:var(--text)"><?= number_format($monthTotal,0) ?> <small style="font-size:12px;color:var(--text-3)"><?= $currency ?></small></div></div>
  </div>
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:40px;height:40px;border-radius:9px;background:#fff4dd;color:#b87000;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h1v-6h-4l-3 -5h-3v7" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">الموردون</div><div style="font-size:20px;font-weight:700;color:var(--text)"><?= count($suppliers) ?></div></div>
  </div>
</div>

<!-- جدول الفواتير -->
<div class="card" style="padding:0;overflow:hidden">
  <div class="table-wrap">
    <table class="data-table">
      <thead>
        <tr><th>رقم الفاتورة</th><th>المورد</th><th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th><th>الحالة</th><th>التاريخ</th><th>المستخدم</th><th style="width:80px">إجراءات</th></tr>
      </thead>
      <tbody>
      <?php if (empty($purchases)): ?>
        <tr><td colspan="9" class="empty-td">
          <div style="text-align:center;padding:30px;color:var(--text-3)">
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart-off" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17h-11v-11" />
  <path d="M9.239 5.231l10.761 .769l-1 7h-2m-4 0h-7" />
  <path d="M3 3l18 18" />
</svg>
          <!-- <i class="ti ti-shopping-cart-off" style="font-size:40px;display:block;margin-bottom:10px;opacity:.35"></i> -->
            لا توجد فواتير مشتريات بعد
            <br><button class="btn btn-primary btn-sm" style="margin-top:10px" onclick="openModal('pur-modal')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> أضف فاتورة</button>
          </div>
        </td></tr>
      <?php else: ?>
      <?php foreach ($purchases as $p):
        $remaining = $p['total'] - $p['paid_amount'];
        $sb = $stMap[$p['status']] ?? ['—','badge-secondary'];
      ?>
        <tr>
          <td class="fw-bold mono"><?= Helper::e($p['purchase_number']) ?></td>
          <td style="font-size:13px"><?= Helper::e($p['supplier_name'] ?? '—') ?></td>
          <td class="fw-bold"><?= number_format($p['total'],2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
          <td style="color:#3a7030;font-weight:600"><?= number_format($p['paid_amount'],2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
          <td><?= $remaining > 0 ? '<span style="color:#b83232;font-weight:700">'.number_format($remaining,2).' '.$currency.'</span>' : '<span style="color:#3a7030;font-size:12px">مسدّد</span>' ?></td>
          <td><span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span></td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::formatDate($p['created_at'],'d/m/Y H:i') ?></td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::e($p['user_name'] ?? '') ?></td>
          <td>
            <div class="action-btns">
              <a href="?page=purchases&action=view&id=<?= $p['id'] ?>" target="_blank" class="action-btn view" title="عرض التفاصيل"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-eye" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 12a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M21 12c-2.4 4 -5.4 6 -9 6c-3.6 0 -6.6 -2 -9 -6c2.4 -4 5.4 -6 9 -6c3.6 0 6.6 2 9 6" />
</svg></a>
              <?php if(Auth::can('admin')): ?>
              <a href="?page=purchases&action=delete&id=<?= $p['id'] ?>" class="action-btn delete" title="حذف" onclick="return confirm('حذف هذه الفاتورة وعكس تأثيرها على المخزون؟')"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7l16 0" />
  <path d="M10 11l0 6" />
  <path d="M14 11l0 6" />
  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
</svg></a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- Modal: فاتورة شراء جديدة -->
<div class="modal-overlay" id="pur-modal" style="display:none">
  <div class="modal-box" style="width:680px;max-width:98vw;max-height:92vh;overflow-y:auto">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,var(--olive-600),var(--olive-300));display:flex;align-items:center;justify-content:center;font-size:18px">🛒</div>
        <div><h3>فاتورة شراء جديدة</h3><p style="font-size:11px;color:var(--text-3)">أضف الأصناف المشتراة</p></div>
      </div>
      <button onclick="closeModal('pur-modal')" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:20px"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button>
    </div>

    <form method="POST" action="?page=purchases&action=store" id="pur-form">
      <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">

      <div class="form-row" style="margin-bottom:12px">
        <div class="form-group">
          <label class="form-label">المورد</label>
          <select name="supplier_id" class="form-select">
            <option value="">— بدون مورد —</option>
            <?php foreach ($suppliers as $s): ?>
            <option value="<?= $s['id'] ?>"><?= Helper::e($s['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">طريقة الدفع</label>
          <select name="payment_method" class="form-select">
            <?php foreach ($pmNames as $k=>$v): ?><option value="<?= $k ?>"><?= $v ?></option><?php endforeach; ?>
          </select>
        </div>
      </div>

      <!-- جدول الأصناف -->
      <div style="border:1px solid var(--border);border-radius:8px;overflow:hidden;margin-bottom:12px">
        <table style="width:100%;border-collapse:collapse;font-size:12px">
          <thead>
            <tr style="background:var(--bg)">
              <th style="padding:8px 10px;text-align:right;color:var(--text-2);border-bottom:1px solid var(--border)">المنتج</th>
              <th style="padding:8px 10px;text-align:center;color:var(--text-2);border-bottom:1px solid var(--border);width:80px">الكمية</th>
              <th style="padding:8px 10px;text-align:center;color:var(--text-2);border-bottom:1px solid var(--border);width:100px">السعر</th>
              <th style="padding:8px 10px;text-align:center;color:var(--text-2);border-bottom:1px solid var(--border);width:90px">الإجمالي</th>
              <th style="padding:8px 10px;border-bottom:1px solid var(--border);width:36px"></th>
            </tr>
          </thead>
          <tbody id="pur-items-body"></tbody>
        </table>
      </div>

      <button type="button" onclick="addPurItem()" class="btn btn-outline btn-sm" style="margin-bottom:14px">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إضافة صنف
      </button>

      <div class="form-row" style="margin-bottom:12px">
        <div class="form-group">
          <label class="form-label">الإجمالي</label>
          <input type="number" name="total" id="pur-total" class="form-input" step="0.01" readonly style="background:var(--bg);font-weight:700;font-size:15px">
        </div>
        <div class="form-group">
          <label class="form-label">المبلغ المدفوع</label>
          <input type="number" name="paid_amount" id="pur-paid" class="form-input" step="0.01" min="0" placeholder="0.00" oninput="calcPurChange()">
        </div>
      </div>

      <div id="pur-remaining-box" style="display:none;background:var(--warn-bg,#fff4dd);border:1px solid var(--warn-border,#f5d48a);border-radius:8px;padding:9px 12px;margin-bottom:12px;font-size:12px;display:flex;justify-content:space-between">
        <span style="color:var(--warn-text,#b87000)">المتبقي للمورد</span>
        <strong id="pur-remaining" style="color:var(--warn-text,#b87000)">—</strong>
      </div>

      <div class="form-group">
        <label class="form-label">ملاحظات</label>
        <textarea name="notes" class="form-textarea" rows="2" placeholder="ملاحظات اختيارية..."></textarea>
      </div>

      <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px;padding-top:14px;border-top:1px solid var(--border)">
        <button type="button" onclick="closeModal('pur-modal')" class="btn btn-ghost">إلغاء</button>
        <button type="submit" class="btn btn-primary" id="pur-submit">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
  <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 4l0 4l-6 0l0 -4" />
</svg> حفظ الفاتورة
        </button>
      </div>
    </form>
  </div>
</div>

<script>
const purProducts = <?= json_encode(array_map(fn($p) => [
  'id'    => $p['id'],
  'name'  => $p['name'],
  'price' => $p['purchase_price'],
  'unit'  => $p['unit'],
  'stock' => $p['stock_qty'],
], $products)) ?>;
const PUR_CUR = '<?= $currency ?>';
let purItemIdx = 0;

// إضافة صف منتج
function addPurItem() {
  purItemIdx++;
  const i = purItemIdx;
  const tr = document.createElement('tr');
  tr.id = 'pur-row-' + i;
  tr.style.borderBottom = '1px solid var(--border-2)';
  tr.innerHTML = `
    <td style="padding:6px 8px">
      <select name="items[${i}][product_id]" class="form-select" style="font-size:12px" onchange="setPurPrice(${i},this)">
        <option value="">اختر منتجاً...</option>
        ${purProducts.map(p=>`<option value="${p.id}" data-price="${p.price}">${p.name} (${p.unit})</option>`).join('')}
      </select>
    </td>
    <td style="padding:6px 5px">
      <input type="number" name="items[${i}][quantity]" class="form-input" style="font-size:12px;padding:7px;text-align:center" min="0.001" step="0.001" placeholder="0" oninput="calcPurTotal()">
    </td>
    <td style="padding:6px 5px">
      <input type="number" name="items[${i}][price]" id="pur-price-${i}" class="form-input" style="font-size:12px;padding:7px;text-align:center" min="0" step="0.01" placeholder="0.00" oninput="calcPurTotal()">
    </td>
    <td style="padding:6px 5px;text-align:center">
      <span id="pur-line-${i}" style="font-weight:700;font-size:12px;color:var(--olive-500)">0.00</span>
    </td>
    <td style="padding:6px 5px;text-align:center">
      <button type="button" onclick="removePurRow(${i})" style="background:#feeaea;border:none;border-radius:5px;padding:5px 7px;cursor:pointer;color:#b83232"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7l16 0" />
  <path d="M10 11l0 6" />
  <path d="M14 11l0 6" />
  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
</svg></button>
    </td>`;
  document.getElementById('pur-items-body').appendChild(tr);
}

function setPurPrice(i, sel) {
  const opt   = sel.options[sel.selectedIndex];
  const price = parseFloat(opt.dataset.price) || 0;
  const priceEl = document.getElementById('pur-price-' + i);
  if (priceEl && price > 0) priceEl.value = price.toFixed(2);
  calcPurTotal();
}

function removePurRow(i) {
  const row = document.getElementById('pur-row-' + i);
  if (row) row.remove();
  calcPurTotal();
}

function calcPurTotal() {
  let total = 0;
  document.querySelectorAll('#pur-items-body tr').forEach(row => {
    const qty = parseFloat(row.querySelector('[name*=quantity]')?.value) || 0;
    const prc = parseFloat(row.querySelector('[name*=price]')?.value)    || 0;
    const line = qty * prc;
    const idx = row.id.replace('pur-row-', '');
    const lineEl = document.getElementById('pur-line-' + idx);
    if (lineEl) lineEl.textContent = line.toFixed(2);
    total += line;
  });
  const totalEl = document.getElementById('pur-total');
  if (totalEl) { totalEl.value = total.toFixed(2); }
  const paidEl = document.getElementById('pur-paid');
  if (paidEl && !paidEl.value) paidEl.value = total.toFixed(2);
  calcPurChange();
}

function calcPurChange() {
  const total = parseFloat(document.getElementById('pur-total')?.value)  || 0;
  const paid  = parseFloat(document.getElementById('pur-paid')?.value)   || 0;
  const rem   = total - paid;
  const box   = document.getElementById('pur-remaining-box');
  const val   = document.getElementById('pur-remaining');
  if (box && val) {
    if (rem > 0.005) {
      val.textContent     = rem.toFixed(2) + ' ' + PUR_CUR;
      box.style.display   = 'flex';
    } else {
      box.style.display   = 'none';
    }
  }
}

// إضافة صف أول تلقائياً
addPurItem();

// منع الإرسال بدون أصناف
document.getElementById('pur-form').addEventListener('submit', function(e) {
  const rows  = document.querySelectorAll('#pur-items-body tr');
  const valid = Array.from(rows).some(r => {
    const sel = r.querySelector('select')?.value;
    const qty = parseFloat(r.querySelector('[name*=quantity]')?.value) || 0;
    return sel && qty > 0;
  });
  if (!valid) { e.preventDefault(); showToast('أضف منتجاً واحداً على الأقل بكمية صحيحة','error'); return; }
  const btn = document.getElementById('pur-submit');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> جاري الحفظ...';
});
</script>
