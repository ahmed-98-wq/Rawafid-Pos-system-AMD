<?php
/* =============================================
   صفحة إدارة المخزون
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
$db       = Database::getInstance();
$filter   = Helper::sanitize($_GET['filter'] ?? 'all');
$search   = Helper::sanitize($_GET['search'] ?? '');

// بناء الاستعلام
$where = "WHERE p.is_active = 1"; $params = [];
if ($filter === 'low') $where .= " AND p.stock_qty > 0 AND p.stock_qty <= p.min_stock_alert";
if ($filter === 'out') $where .= " AND p.stock_qty <= 0";
if ($filter === 'exp') $where .= " AND p.expiry_date IS NOT NULL AND p.expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)";
if ($filter === 'expired') $where .= " AND p.expiry_date IS NOT NULL AND p.expiry_date < CURDATE()";
if ($search) { $where .= " AND p.name LIKE ?"; $params[] = "%$search%"; }

$products = $db->fetchAll(
    "SELECT p.*, c.name as category_name,
            DATEDIFF(p.expiry_date, CURDATE()) as days_left
     FROM products p
     LEFT JOIN categories c ON p.category_id = c.id
     $where ORDER BY p.stock_qty ASC",
    $params
);

$stats = $db->fetchOne(
    "SELECT COUNT(*) as total,
            COALESCE(SUM(stock_qty * purchase_price), 0) as value,
            SUM(CASE WHEN stock_qty <= 0 THEN 1 ELSE 0 END) as out_count,
            SUM(CASE WHEN stock_qty > 0 AND stock_qty <= min_stock_alert THEN 1 ELSE 0 END) as low_count,
            SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date < CURDATE() THEN 1 ELSE 0 END) as expired_count,
            SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 60 DAY) THEN 1 ELSE 0 END) as expiring_count
     FROM products WHERE is_active = 1"
);
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
  <path d="M16 5.25l-8 4.5" />
</svg> إدارة المخزون</h1>
  <div class="page-actions">
    <a href="?page=inventory&action=export" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 3v4a1 1 0 0 0 1 1h4" />
  <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
  <path d="M8 11h8v7h-8z" />
  <path d="M8 15h8" />
  <path d="M11 11v7" />
</svg> تصدير CSV
    </a>
    <button class="btn btn-primary" onclick="openAdjModal()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 10a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M6 4v4" />
  <path d="M6 12v8" />
  <path d="M10 16a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M12 4v10" />
  <path d="M12 18v2" />
  <path d="M16 7a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M18 4v1" />
  <path d="M18 9v11" />
</svg> تعديل مخزون
    </button>
  </div>
</div>

<!-- إحصائيات -->
<div class="stats-grid">
  <div class="stat-card">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 16.5l-5 -3l5 -3l5 3v5.5l-5 3z" />
  <path d="M2 13.5v5.5l5 3" />
  <path d="M7 16.5l5 -3" />
  <path d="M12 12l5 -3l5 3v5.5l-5 3z" />
  <path d="M17 15l5 -3" />
  <path d="M17 15v5.5l5 3" />
  <path d="M7 10.5l5 -3l5 3v5.5l-5 3z" />
  <path d="M7 10.5v5.5l5 3" />
  <path d="M12 16l5 -3" />
</svg></div>
    <div class="stat-body"><div class="stat-label">إجمالي الأصناف</div>
    <div class="stat-value"><?= number_format($stats['total']) ?></div></div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div class="stat-body"><div class="stat-label">قيمة المخزون</div>
    <div class="stat-value"><?= number_format($stats['value'], 0) ?></div>
    <div class="stat-sub"><?= $currency ?></div></div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 8v4" />
  <path d="M12 16h.01" />
</svg></div>
    <div class="stat-body"><div class="stat-label">منخفض المخزون</div>
    <div class="stat-value"><?= $stats['low_count'] ?></div></div>
  </div>
  <div class="stat-card red">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M10 10l4 4m0 -4l-4 4" />
</svg></div>
    <div class="stat-body"><div class="stat-label">نفذ / منتهي</div>
    <div class="stat-value"><?= (int)$stats['out_count'] + (int)$stats['expired_count'] ?></div></div>
  </div>
</div>

<!-- فلاتر -->
<div class="card filters-bar">
  <form method="GET" class="filters-form" id="inv-filter-form">
    <input type="hidden" name="page" value="inventory">
    <div class="filter-tabs">
      <?php
      $tabs = [
        ['all','الكل','',0],
        ['low','منخفض','orange',$stats['low_count']],
        ['out','نفذ','red',$stats['out_count']],
        ['expired','منتهي','red',$stats['expired_count']],
        ['exp','قريب الانتهاء','orange',$stats['expiring_count']],
      ];
      foreach($tabs as [$fv,$fl,$fc,$fc2]):
      ?>
      <a href="?page=inventory&filter=<?= $fv ?><?= $search?"&search=".urlencode($search):'' ?>"
         class="filter-tab <?= $fc ?> <?= $filter===$fv?'active':'' ?>">
        <?= $fl ?>
        <?php if($fc2 > 0): ?><span class="tab-count"><?= $fc2 ?></span><?php endif; ?>
      </a>
      <?php endforeach; ?>
    </div>
    <div style="position:relative;flex:1;min-width:180px;display:flex;align-items:center">
      <i class="ti ti-search" style="position:absolute;right:10px;color:var(--text-3);font-size:14px;pointer-events:none"></i>
      <input type="text" name="search" value="<?= Helper::e($search) ?>"
             placeholder="بحث بالاسم..." class="form-input" style="padding-right:32px;flex:1"
             oninput="clearTimeout(ist);ist=setTimeout(()=>this.form.submit(),600)">
    </div>
    <a href="?page=inventory&filter=<?= $filter ?>" class="btn btn-ghost btn-sm"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> مسح</a>
  </form>
</div>

<div class="card" style="padding:0; overflow:hidden">
  <div class="table-wrap" style="max-height: 500px; overflow-y: auto; position: relative;">
    <table class="data-table">
      <thead style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
        <tr>
          <th>المنتج</th><th>التصنيف</th><th>الكمية</th><th>حد التنبيه</th>
          <th>الوحدة</th><th>سعر الشراء</th><th>قيمة المخزون</th><th>الصلاحية</th><th style="width:80px">تعديل</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
        <tr><td colspan="9" class="empty-td">لا توجد منتجات بهذا الفلتر</td></tr>
        <?php else: ?>
        <?php foreach ($products as $p):
          $dl = $p['days_left'];
          $isExpired = $p['expiry_date'] && $dl < 0;
          $isSoon    = $p['expiry_date'] && $dl >= 0 && $dl <= 60;
          $stockCls  = $p['stock_qty'] <= 0 ? 'stock-out' : ($p['stock_qty'] <= $p['min_stock_alert'] ? 'stock-low' : 'stock-ok');
        ?>
        <tr style="<?= $isExpired ? 'background:var(--expired-bg)' : '' ?>">
          <td>
            <div style="display:flex;align-items:center;gap:8px">
              <?php if($isExpired): ?><span title="منتهي الصلاحية" style="font-size:16px">⛔</span>
              <?php elseif($isSoon): ?><span title="قريب الانتهاء" style="font-size:14px">⏳</span>
              <?php endif; ?>
              <span style="font-weight:600;<?= $isExpired ? 'color:var(--expired-text)' : '' ?>"><?= Helper::e($p['name']) ?></span>
            </div>
          </td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::e($p['category_name'] ?? '—') ?></td>
          <td><span class="stock-badge <?= $stockCls ?>"><?= number_format($p['stock_qty'], 2) ?></span></td>
          <td style="font-size:12px;color:var(--text-3)"><?= number_format($p['min_stock_alert'], 0) ?></td>
          <td style="font-size:12px"><?= Helper::e($p['unit']) ?></td>
          <td style="font-size:12px"><?= number_format($p['purchase_price'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
          <td style="font-size:12px;font-weight:600"><?= number_format($p['stock_qty'] * $p['purchase_price'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
          <td>
            <?php if ($p['expiry_date']): ?>
            <span class="expiry-tag <?= $isExpired ? 'exp-expired' : ($isSoon ? 'exp-soon' : '') ?>">
              <?php if ($isExpired): ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-octagon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12.804 2.168l6.028 6.028a1 1 0 0 1 0 1.414l-6.028 6.028a1 1 0 0 1 -1.414 0l-6.028 -6.028a1 1 0 0 1 0 -1.414l6.028 -6.028a1 1 0 0 1 1.414 0z" />
  <path d="M12 8v4" />
  <path d="M12 16h.01" />
</svg> منتهي (<?= abs($dl) ?> يوم)
              <?php elseif ($dl <= 7): ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 7v5l3 3" />
</svg> <?= $dl ?> أيام!
              <?php elseif ($dl <= 30): ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 7v5l3 3" />
</svg> <?= $dl ?> يوم
              <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
  <path d="M16 3v4" />
  <path d="M8 3v4" />
  <path d="M4 11h16" />
  <path d="M11 15h1" />
  <path d="M12 15v3" />
</svg> <?= Helper::formatDate($p['expiry_date'], 'd/m/Y') ?>
              <?php endif; ?>
            </span>
            <?php else: ?>
            <span style="color:var(--text-4);font-size:12px">—</span>
            <?php endif; ?>
          </td>
          <td>
            <button onclick="openAdjModal(<?= $p['id'] ?>,'<?= Helper::e(addslashes($p['name'])) ?>',<?= $p['stock_qty'] ?>)"
                    class="action-btn edit" title="تعديل المخزون">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-horizontal" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M4 6l8 0" />
  <path d="M16 6l4 0" />
  <path d="M8 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M4 12l2 0" />
  <path d="M10 12l10 0" />
  <path d="M17 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M4 18l11 0" />
  <path d="M19 18l1 -0" />
</svg>
            </button>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal: تعديل المخزون -->
<div class="modal-overlay" id="adj-modal" style="display:none">
  <div class="modal-box" style="max-width:420px">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,var(--olive-600),var(--olive-300));display:flex;align-items:center;justify-content:center;font-size:18px">📦</div>
        <div><h3>تعديل المخزون</h3><p id="adj-prod-name" style="font-size:12px;color:var(--text-3)">اختر منتجاً</p></div>
      </div>
      <button onclick="closeAdj()" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:20px"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button>
    </div>

    <!-- البحث عن منتج (عند الفتح بدون ID) -->
    <div id="adj-search-wrap" style="margin-bottom:12px;display:none">
      <input type="text" id="adj-search" class="form-input" placeholder="ابحث عن منتج..." oninput="adjSearch(this.value)">
      <div id="adj-search-results" style="max-height:160px;overflow-y:auto;border:1px solid var(--border);border-radius:8px;margin-top:6px;display:none"></div>
    </div>

    <div id="adj-current-box" style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:12px;display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:13px;color:var(--text-2)">المخزون الحالي</span>
      <strong id="adj-cur-val" style="font-size:18px;color:var(--text)">—</strong>
    </div>

    <div style="display:flex;gap:5px;margin-bottom:14px">
      <?php foreach(['in'=>['إضافة','ti-plus'],'out'=>['خصم','ti-minus'],'adjustment'=>['تسوية','ti-adjustments']] as $tv=>[$tl,$ti]): ?>
      <button type="button" class="adj-type-btn <?= $tv==='in'?'active':'' ?>" data-type="<?= $tv ?>" onclick="setAdjType(this)">
        <i class="ti <?= $ti ?>"></i> <?= $tl ?>
      </button>
      <?php endforeach; ?>
    </div>

    <div class="form-group">
      <label class="form-label" id="adj-qty-label">الكمية المُضافة</label>
      <input type="number" id="adj-qty" class="form-input" style="font-size:16px;padding:11px" step="0.001" min="0" placeholder="0" oninput="calcAdjPreview()">
    </div>

    <div id="adj-preview" style="display:none;background:var(--olive-50);border:1px solid var(--border);border-radius:8px;padding:10px;margin-bottom:12px">
      <div style="display:flex;justify-content:space-between;font-size:13px">
        <span style="color:var(--text-2)">المخزون الجديد</span>
        <strong id="adj-new-val" style="color:var(--olive-600)">—</strong>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">سبب التعديل</label>
      <input type="text" id="adj-notes" class="form-input" placeholder="بضاعة جديدة، تلف، جرد صنفي...">
    </div>

    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px">
      <button onclick="closeAdj()" class="btn btn-ghost">إلغاء</button>
      <button onclick="saveAdj()" class="btn btn-primary" id="adj-save-btn">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg> حفظ
      </button>
    </div>
  </div>
</div>

<style>
.adj-type-btn{flex:1;padding:8px;border:1.5px solid var(--border);border-radius:8px;background:none;color:var(--text-2);cursor:pointer;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:5px;transition:.15s}
.adj-type-btn:hover{border-color:var(--olive-400);color:var(--olive-600)}
.adj-type-btn.active{background:var(--olive-600);border-color:var(--olive-600);color:white}
[data-theme="dark"] .adj-type-btn.active{background:#2563eb;border-color:#2563eb}
</style>

<script>
let adjProdId = null, adjCurQty = 0, adjType = 'in';
let ist;

function openAdjModal(id=null, name=null, qty=null) {
  adjProdId = id; adjCurQty = parseFloat(qty) || 0;
  document.getElementById('adj-prod-name').textContent = name || 'اختر منتجاً';
  document.getElementById('adj-cur-val').textContent   = adjCurQty.toFixed(2);
  document.getElementById('adj-qty').value             = '';
  document.getElementById('adj-notes').value           = '';
  document.getElementById('adj-preview').style.display = 'none';
  document.getElementById('adj-search-wrap').style.display = id ? 'none' : 'block';
  document.querySelectorAll('.adj-type-btn').forEach(b => b.classList.remove('active'));
  document.querySelector('[data-type="in"]').classList.add('active');
  adjType = 'in';
  document.getElementById('adj-qty-label').textContent = 'الكمية المُضافة';
  document.getElementById('adj-modal').style.display = 'flex';
  setTimeout(() => (id ? document.getElementById('adj-qty') : document.getElementById('adj-search')).focus(), 150);
}
function closeAdj() { document.getElementById('adj-modal').style.display = 'none'; }
function setAdjType(btn) {
  document.querySelectorAll('.adj-type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active'); adjType = btn.dataset.type;
  const lbls = {in:'الكمية المُضافة',out:'الكمية المخصومة',adjustment:'الكمية الجديدة (تسوية)'};
  document.getElementById('adj-qty-label').textContent = lbls[adjType];
  calcAdjPreview();
}
function calcAdjPreview() {
  const q = parseFloat(document.getElementById('adj-qty').value) || 0;
  const p = document.getElementById('adj-preview');
  if (q <= 0) { p.style.display='none'; return; }
  const nq = adjType==='adjustment' ? q : adjType==='out' ? Math.max(0,adjCurQty-q) : adjCurQty+q;
  document.getElementById('adj-new-val').textContent = nq.toFixed(2);
  document.getElementById('adj-new-val').style.color = nq<=0 ? '#b83232' : (nq<adjCurQty && adjType!=='adjustment' ? '#b87000' : 'var(--olive-600)');
  p.style.display='block';
}
async function adjSearch(q) {
  if (!q || q.length < 2) { document.getElementById('adj-search-results').style.display='none'; return; }
  const r = await fetch('?page=pos&action=getProducts');
  const d = await r.json();
  const f = (d.products||[]).filter(p => p.name.toLowerCase().includes(q.toLowerCase()) || (p.barcode||'').includes(q)).slice(0,8);
  const el = document.getElementById('adj-search-results');
  el.innerHTML = f.map(p => `<div onclick="selectAdjProd(${p.id},'${p.name.replace(/'/g,"\\'")}',${p.stock_qty})" style="padding:8px 12px;cursor:pointer;font-size:13px;border-bottom:1px solid var(--border-2)">${p.name} <span style="color:var(--text-3);font-size:11px">(${p.stock_qty} ${p.unit||''})</span></div>`).join('') || '<div style="padding:8px 12px;color:var(--text-3);font-size:12px">لا نتائج</div>';
  el.style.display='block';
}
function selectAdjProd(id,name,qty) {
  adjProdId=id; adjCurQty=parseFloat(qty)||0;
  document.getElementById('adj-prod-name').textContent=name;
  document.getElementById('adj-cur-val').textContent=adjCurQty.toFixed(2);
  document.getElementById('adj-search-results').style.display='none';
  document.getElementById('adj-qty').focus();
}
async function saveAdj() {
  const qty = parseFloat(document.getElementById('adj-qty').value);
  if (!adjProdId || isNaN(qty) || qty < 0) { showToast('يرجى تحديد منتج وإدخال كمية','error'); return; }
  const btn = document.getElementById('adj-save-btn');
  btn.disabled=true; btn.innerHTML='<i class="ti ti-loader ti-spin"></i> جاري الحفظ...';
  try {
    const r = await fetch('?page=products&action=adjustStock',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({product_id:adjProdId,quantity:qty,type:adjType,notes:document.getElementById('adj-notes').value})});
    const d = await r.json();
    if (d.success) { showToast('تم تحديث المخزون','success'); closeAdj(); setTimeout(()=>location.reload(),700); }
    else showToast(d.message||'حدث خطأ','error');
  } catch(e){ showToast('خطأ في الاتصال','error'); }
  btn.disabled=false; btn.innerHTML='<i class="ti ti-check"></i> حفظ';
}
</script>
