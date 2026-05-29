<?php
/* =============================================
   صفحة إدارة المنتجات
   $page, $action, $products, $categories,
   $search, $cat, $currentPage, $totalPages,
   $total, $settings — متاحة من الـ Controller
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
?>

<!-- ======= Page Header ======= -->
<div class="page-header">
  <h1 class="page-title">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 16.5l-5 -3l5 -3l5 3V22l-5 -3z" />
  <path d="M2 13.5V19l5 3" />
  <path d="M7 16.5l5 -3" />
  <path d="M12 12l5 -3l5 3v5.5l-5 3l-5 -3z" />
  <path d="M12 15l5 3" />
  <path d="M17 12v5.5" />
  <path d="M7 7.5l5 -3l5 3v5.5l-5 3l-5 -3z" />
  <path d="M7 13V7.5" />
  <path d="M7 7.5l5 3" />
</svg> إدارة المنتجات
  </h1>
  <div class="page-actions">
    <button class="btn btn-outline" onclick="exportProducts()">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
  <path d="M7 11l5 5l5 -5" />
  <path d="M12 4l0 12" />
</svg> تصدير CSV
    </button>
    <a href="?page=products&action=create" class="btn btn-primary">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إضافة منتج جديد
    </a>
  </div>
</div>

<!-- ======= Stats Bar ======= -->
<?php
$db2        = Database::getInstance();
$totalProds = (int)$db2->fetchColumn("SELECT COUNT(*) FROM products WHERE is_active=1");
$lowStock   = (int)$db2->fetchColumn("SELECT COUNT(*) FROM products WHERE stock_qty <= min_stock_alert AND stock_qty > 0 AND is_active=1");
$outStock   = (int)$db2->fetchColumn("SELECT COUNT(*) FROM products WHERE stock_qty <= 0 AND is_active=1");
$totalValue = (float)$db2->fetchColumn("SELECT COALESCE(SUM(stock_qty * purchase_price),0) FROM products WHERE is_active=1");
?>
<div class="prod-stats">
  <div class="ps-card">
    <div class="ps-ico blue"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-packages" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 16.5l-5 -3l5 -3l5 3V22l-5 -3z" />
  <path d="M2 13.5V19l5 3" />
  <path d="M7 16.5l5 -3" />
  <path d="M12 12l5 -3l5 3v5.5l-5 3l-5 -3z" />
  <path d="M12 15l5 3" />
  <path d="M17 12v5.5" />
  <path d="M7 7.5l5 -3l5 3v5.5l-5 3l-5 -3z" />
  <path d="M7 13V7.5" />
  <path d="M7 7.5l5 3" />
</svg></div>
    <div><div class="ps-val"><?= number_format($totalProds) ?></div><div class="ps-lbl">إجمالي المنتجات</div></div>
  </div>
  <div class="ps-card">
    <div class="ps-ico green"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div><div class="ps-val"><?= number_format($totalValue, 0) ?></div><div class="ps-lbl">قيمة المخزون (<?= $currency ?>)</div></div>
  </div>
  <div class="ps-card <?= $lowStock > 0 ? 'warn-card' : '' ?>">
    <div class="ps-ico orange"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 8v4" />
  <path d="M12 16h.01" />
</svg></div>
    <div><div class="ps-val"><?= number_format($lowStock) ?></div><div class="ps-lbl">مخزون منخفض</div></div>
  </div>
  <div class="ps-card <?= $outStock > 0 ? 'danger-card' : '' ?>">
    <div class="ps-ico red"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M10 10l4 4m0 -4l-4 4" />
</svg></div>
    <div><div class="ps-val"><?= number_format($outStock) ?></div><div class="ps-lbl">نفذ المخزون</div></div>
  </div>
</div>

<!-- ======= Filters ======= -->
<div class="card filters-bar" style="margin-bottom:14px">
  <form method="GET" class="filters-form" id="filter-form">
    <input type="hidden" name="page" value="products">
    <!-- بحث -->
    <div class="prod-search-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg>
      <input type="text" name="search" id="prod-search"
             value="<?= Helper::e($search ?? '') ?>"
             placeholder="بحث بالاسم أو الباركود أو الوصف..."
             class="form-input prod-search-input"
             oninput="debounceSubmit()">
      <?php if (!empty($search)): ?>
      <a href="?page=products" class="search-clear-btn"><i class="ti ti-x"></i></a>
      <?php endif; ?>
    </div>
    <!-- تصنيف -->
    <select name="cat" class="form-select" style="width:180px" onchange="this.form.submit()">
      <option value="">📂 كل التصنيفات</option>
      <?php foreach (($categories ?? []) as $c): ?>
      <option value="<?= $c['id'] ?>" <?= (($cat ?? 0) == $c['id']) ? 'selected' : '' ?>>
        <?= Helper::e($c['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> بحث</button>
    <?php if (!empty($search) || !empty($cat)): ?>
    <a href="?page=products" class="btn btn-ghost"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M10 10l4 4m0 -4l-4 4" />
</svg> مسح</a>
    <?php endif; ?>
  </form>
</div>

<div class="card" style="padding:0; overflow:hidden">
  <div class="table-wrap" style="max-height: 600px; overflow-y: auto; position: relative;">
    <table class="data-table" id="products-table">
      <thead style="position: sticky; top: 0; z-index: 10;">
        <tr>
          <th style="width:40px">#</th>
          <th>المنتج</th>
          <th>التصنيف</th>
          <th>سعر الشراء</th>
          <th>سعر البيع</th>
          <th>المخزون</th>
          <th>الباركود</th>
          <th>الصلاحية</th>
          <th>الحالة</th>
          <th style="width:100px">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($products)): ?>
        <tr>
          <td colspan="10">
            <div class="prod-empty">
              <div class="prod-empty-icon">📦</div>
              <h3>لا توجد منتجات</h3>
              <p><?= !empty($search) ? "لا نتائج لـ «" . Helper::e($search) . "»" : "لم تُضَف أي منتجات بعد" ?></p>
              <?php if (empty($search)): ?>
              <a href="?page=products&action=create" class="btn btn-primary">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> أضف أول منتج
              </a>
              <?php endif; ?>
            </div>
          </td>
        </tr>
        <?php else: ?>
        <?php foreach ($products as $p): ?>
        <?php
          $stockClass = $p['stock_qty'] <= 0
            ? 'stock-out'
            : ($p['stock_qty'] <= $p['min_stock_alert'] ? 'stock-low' : 'stock-ok');
          $hasExpiry  = !empty($p['expiry_date']);
          $expirySoon = $hasExpiry && strtotime($p['expiry_date']) < strtotime('+60 days');
          $expired    = $hasExpiry && strtotime($p['expiry_date']) < time();
          $profit     = $p['sale_price'] - $p['purchase_price'];
          $profitPct  = $p['purchase_price'] > 0 ? round($profit / $p['purchase_price'] * 100, 1) : 0;
        ?>
        <tr class="prod-row" data-id="<?= $p['id'] ?>">
          <td class="text-sm-muted"><?= $p['id'] ?></td>

          <td>
            <div class="prod-name-cell">
              <?php if (!empty($p['image']) && file_exists(BASE_PATH . '/uploads/' . $p['image'])): ?>
              <img src="<?= BASE_URL ?>/uploads/<?= Helper::e($p['image']) ?>"
                   class="prod-thumb" alt="<?= Helper::e($p['name']) ?>">
              <?php else: ?>
              <div class="prod-thumb-ph"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M8.2 11.5l7.6 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
</svg></div>
              <?php endif; ?>
              <div>
                <div class="prod-name-text"><?= Helper::e($p['name']) ?></div>
                <?php if (!empty($p['description'])): ?>
                <div class="prod-desc"><?= Helper::e(mb_substr($p['description'], 0, 40)) ?><?= mb_strlen($p['description']) > 40 ? '...' : '' ?></div>
                <?php endif; ?>
                <?php if ($profitPct > 0): ?>
                <div class="prod-profit-badge">ربح <?= $profitPct ?>%</div>
                <?php endif; ?>
              </div>
            </div>
          </td>

          <td class="text-sm-muted"><?= Helper::e($p['category_name'] ?? '—') ?></td>

          <td class="text-sm-muted"><?= number_format($p['purchase_price'], 2) ?> <small><?= $currency ?></small></td>

          <td class="fw-bold text-green">
            <?= number_format($p['sale_price'], 2) ?> <small class="text-sm-muted"><?= $currency ?></small>
          </td>

          <td>
            <span class="stock-badge <?= $stockClass ?>">
              <?= $p['stock_qty'] <= 0 ? 'نفذ' : number_format($p['stock_qty'], 0) . ' ' . Helper::e($p['unit']) ?>
            </span>
          </td>

          <td class="mono text-sm-muted"><?= Helper::e($p['barcode'] ?? '—') ?></td>

          <td>
            <?php if ($hasExpiry): ?>
            <span class="expiry-tag <?= $expired ? 'exp-expired' : ($expirySoon ? 'exp-soon' : '') ?>">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar-time" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M11.795 21h-6.795a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v4" />
  <path d="M18 18m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M15 3v4" />
  <path d="M7 3v4" />
  <path d="M3 11h16" />
  <path d="M18 16.496v1.504l1 1" />
</svg>
              <?= Helper::formatDate($p['expiry_date'], 'd/m/Y') ?>
              <?php if ($expired): ?><br><small>منتهية!</small><?php endif; ?>
            </span>
            <?php else: ?>
            <span class="text-sm-muted">—</span>
            <?php endif; ?>
          </td>

          <td>
            <span class="badge <?= $p['is_active'] ? 'badge-success' : 'badge-secondary' ?>">
              <?= $p['is_active'] ? 'نشط' : 'معطّل' ?>
            </span>
          </td>

          <td>
            <div class="action-btns">
              <a href="?page=products&action=edit&id=<?= $p['id'] ?>"
                 class="action-btn edit" title="تعديل المنتج">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
  <path d="M16 5l3 3" />
</svg>
              </a>
              <button onclick="quickAdjust(<?= $p['id'] ?>, '<?= Helper::e(addslashes($p['name'])) ?>', <?= $p['stock_qty'] ?>)"
                      class="action-btn view" title="تعديل المخزون">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-adjustments-horizontal" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 6m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M4 6l6 0" />
  <path d="M16 6l4 0" />
  <path d="M5 12m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M7 12l14 0" />
  <path d="M19 18m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M4 18l11 0" />
  <path d="M21 18l-2 0" />
</svg>
              </button>
              <a href="?page=products&action=delete&id=<?= $p['id'] ?>"
                 class="action-btn delete" title="حذف المنتج"
                 onclick="return confirm('حذف المنتج «<?= Helper::e(addslashes($p['name'])) ?>»؟')">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7l16 0" />
  <path d="M10 11l0 6" />
  <path d="M14 11l0 6" />
  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
</svg>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

  <!-- Pagination -->
  <?php if (($totalPages ?? 1) > 1): ?>
  <div class="pagination" style="padding:12px 16px;border-top:1px solid var(--border)">
    <span style="color:var(--text-3);font-size:12px;margin-left:auto">
      عرض <?= (($currentPage-1)*20)+1 ?>–<?= min($currentPage*20, $total) ?> من <?= $total ?> منتج
    </span>
    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
    <a href="?page=products&p=<?= $i ?>&search=<?= urlencode($search ?? '') ?>&cat=<?= $cat ?? '' ?>"
       class="page-btn <?= ($currentPage ?? 1) === $i ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ======= Modal: تعديل المخزون ======= -->
<div class="modal-overlay" id="adjust-modal" style="display:none">
  <div class="modal-box" style="max-width:420px">
    <div class="modal-header">
      <div style="display:flex;align-items:center;gap:10px">
        <div style="width:38px;height:38px;border-radius:9px;background:linear-gradient(135deg,#4e6b2b,#9ab866);display:flex;align-items:center;justify-content:center;font-size:18px">📦</div>
        <div>
          <h3>تعديل المخزون</h3>
          <p id="adj-name" style="font-size:12px;color:var(--text-3)">—</p>
        </div>
      </div>
      <button onclick="closeAdjust()" style="background:none;border:none;cursor:pointer;color:var(--text-3);font-size:20px"><i class="ti ti-x"></i></button>
    </div>

    <div style="background:var(--bg);border-radius:8px;padding:12px;margin-bottom:14px;display:flex;justify-content:space-between;align-items:center">
      <span style="font-size:13px;color:var(--text-2)">المخزون الحالي</span>
      <strong id="adj-current-display" style="font-size:18px;color:var(--text)">—</strong>
    </div>

    <div style="display:flex;gap:6px;margin-bottom:14px">
      <button type="button" class="adj-type-btn active" data-type="in" onclick="selectType(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إضافة
      </button>
      <button type="button" class="adj-type-btn" data-type="out" onclick="selectType(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-minus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l14 0" />
</svg> خصم
      </button>
      <button type="button" class="adj-type-btn" data-type="adjustment" onclick="selectType(this)">
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
</svg> تسوية
      </button>
    </div>

    <div class="form-group">
      <label class="form-label" id="adj-qty-label">الكمية المُضافة</label>
      <input type="number" id="adj-qty" class="form-input" style="font-size:16px;padding:11px 14px"
             step="0.001" min="0" placeholder="0" oninput="calcNewQty()">
    </div>

    <div id="new-qty-preview" style="display:none;background:var(--olive-50);border:1px solid var(--olive-200);border-radius:8px;padding:10px;margin-bottom:14px;display:none">
      <div style="display:flex;justify-content:space-between;font-size:13px">
        <span style="color:var(--text-2)">المخزون الجديد سيكون</span>
        <strong id="new-qty-val" style="color:var(--olive-600)">—</strong>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">سبب التعديل (اختياري)</label>
      <input type="text" id="adj-notes" class="form-input" placeholder="مثال: بضاعة جديدة، تلف، جرد...">
    </div>

    <div style="display:flex;gap:8px;justify-content:flex-end;margin-top:16px">
      <button type="button" onclick="closeAdjust()" class="btn btn-ghost">إلغاء</button>
      <button type="button" onclick="saveAdjust()" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg> حفظ التعديل
      </button>
    </div>
  </div>
</div>

<!-- ======= Styles ======= -->
<style>
/* Stats */
.prod-stats{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:16px}
.ps-card{background:var(--surface);border:1px solid var(--border);border-radius:10px;padding:14px 16px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow)}
.ps-card.warn-card{border-color:rgba(133,79,11,.2)}
.ps-card.danger-card{border-color:rgba(163,45,45,.2)}
.ps-ico{width:42px;height:42px;border-radius:9px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.ps-ico.blue{background:#e6f1fb;color:#185fa5}
.ps-ico.green{background:#eaf3de;color:#3b6d11}
.ps-ico.orange{background:#faeeda;color:#854f0b}
.ps-ico.red{background:#fcebeb;color:#a32d2d}
.ps-val{font-size:20px;font-weight:700;color:var(--text);line-height:1.1}
.ps-lbl{font-size:11px;color:var(--text-2);margin-top:2px}
/* Search */
.prod-search-wrap{position:relative;flex:1;min-width:220px;display:flex;align-items:center}
.prod-search-wrap > i{position:absolute;right:10px;color:var(--text-3);font-size:15px;pointer-events:none}
.prod-search-input{padding-right:34px;flex:1}
.search-clear-btn{position:absolute;left:8px;color:var(--text-3);font-size:14px;cursor:pointer;text-decoration:none}
/* Product name cell */
.prod-name-cell{display:flex;align-items:center;gap:10px}
.prod-thumb{width:38px;height:38px;border-radius:7px;object-fit:cover;border:1px solid var(--border);flex-shrink:0}
.prod-thumb-ph{width:38px;height:38px;border-radius:7px;background:var(--bg);display:flex;align-items:center;justify-content:center;color:var(--text-3);font-size:18px;flex-shrink:0;border:1px solid var(--border)}
.prod-name-text{font-size:13px;font-weight:600;color:var(--text)}
.prod-desc{font-size:11px;color:var(--text-3);margin-top:2px}
.prod-profit-badge{display:inline-block;background:#eaf3de;color:#3b6d11;font-size:10px;font-weight:700;padding:1px 6px;border-radius:8px;margin-top:3px}
/* Text helpers */
.text-sm-muted{font-size:12px;color:var(--text-3)}
.text-green{color:#3b6d11}
/* Expiry */
.expiry-tag{font-size:11px;display:flex;align-items:center;gap:3px;color:var(--text-3)}
.exp-soon{color:#854f0b;font-weight:600}
.exp-expired{color:#a32d2d;font-weight:700}
/* Empty state */
.prod-empty{text-align:center;padding:48px 20px;color:var(--text-3)}
.prod-empty-icon{font-size:52px;margin-bottom:12px;opacity:.5}
.prod-empty h3{font-size:16px;font-weight:600;margin-bottom:6px;color:var(--text-2)}
.prod-empty p{font-size:13px;margin-bottom:16px}
/* Adjust modal */
.adj-type-btn{flex:1;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;background:none;color:var(--text-2);cursor:pointer;font-size:12px;font-weight:600;display:flex;align-items:center;justify-content:center;gap:5px;transition:.15s}
.adj-type-btn:hover{border-color:var(--olive-400);color:var(--olive-600)}
.adj-type-btn.active{background:var(--olive-600);border-color:var(--olive-600);color:white}
@media(max-width:900px){.prod-stats{grid-template-columns:repeat(2,1fr)}}
</style>

<!-- ======= JavaScript ======= -->
<script>
// Debounce للبحث
let searchTimer;
function debounceSubmit() {
  clearTimeout(searchTimer);
  searchTimer = setTimeout(function() {
    document.getElementById('filter-form').submit();
  }, 700);
}

// ---- تعديل المخزون ----
let adjProductId   = null;
let adjCurrentQty  = 0;
let adjType        = 'in';

function quickAdjust(id, name, qty) {
  adjProductId  = id;
  adjCurrentQty = parseFloat(qty) || 0;
  adjType       = 'in';
  document.getElementById('adj-name').textContent            = name;
  document.getElementById('adj-current-display').textContent = adjCurrentQty.toFixed(2);
  document.getElementById('adj-qty').value                   = '';
  document.getElementById('adj-notes').value                 = '';
  document.getElementById('new-qty-preview').style.display   = 'none';
  // reset type buttons
  document.querySelectorAll('.adj-type-btn').forEach(b => b.classList.remove('active'));
  document.querySelector('[data-type="in"]').classList.add('active');
  document.getElementById('adj-qty-label').textContent = 'الكمية المُضافة';
  document.getElementById('adjust-modal').style.display = 'flex';
  setTimeout(function() { document.getElementById('adj-qty').focus(); }, 150);
}

function closeAdjust() {
  document.getElementById('adjust-modal').style.display = 'none';
}

function selectType(btn) {
  document.querySelectorAll('.adj-type-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  adjType = btn.getAttribute('data-type');
  const labels = { in:'الكمية المُضافة', out:'الكمية المخصومة', adjustment:'الكمية الجديدة (تسوية)' };
  document.getElementById('adj-qty-label').textContent = labels[adjType] || 'الكمية';
  calcNewQty();
}

function calcNewQty() {
  const q   = parseFloat(document.getElementById('adj-qty').value) || 0;
  const pre = document.getElementById('new-qty-preview');
  const val = document.getElementById('new-qty-val');
  if (q <= 0) { pre.style.display = 'none'; return; }
  let newQty;
  if (adjType === 'adjustment') newQty = q;
  else if (adjType === 'out')   newQty = Math.max(0, adjCurrentQty - q);
  else                          newQty = adjCurrentQty + q;
  val.textContent = newQty.toFixed(2);
  val.style.color = newQty <= 0 ? '#a32d2d' : '#3b6d11';
  pre.style.display = 'block';
}

async function saveAdjust() {
  const qty   = parseFloat(document.getElementById('adj-qty').value);
  const notes = document.getElementById('adj-notes').value;
  if (!adjProductId || isNaN(qty) || qty < 0) {
    alert('يرجى إدخال كمية صحيحة');
    return;
  }
  const btn = document.querySelector('#adjust-modal .btn-primary');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> جاري الحفظ...';
  try {
    const res  = await fetch('?page=products&action=adjustStock', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: adjProductId, quantity: qty, type: adjType, notes })
    });
    const data = await res.json();
    if (data.success) {
      showToast('تم تحديث المخزون بنجاح', 'success');
      closeAdjust();
      setTimeout(function() { location.reload(); }, 800);
    } else {
      alert(data.message || 'حدث خطأ');
    }
  } catch(e) {
    alert('خطأ في الاتصال');
  }
  btn.disabled = false;
  btn.innerHTML = '<i class="ti ti-check"></i> حفظ التعديل';
}

// ---- تصدير CSV ----
function exportProducts() {
  const table = document.getElementById('products-table');
  if (!table) return;
  const rows  = Array.from(table.querySelectorAll('tr'));
  const csv   = rows.map(function(row) {
    return Array.from(row.querySelectorAll('th,td'))
      .map(function(cell) { return '"' + cell.innerText.trim().replace(/"/g,'""') + '"'; })
      .join(',');
  }).join('\n');
  const blob = new Blob(['\uFEFF' + csv], { type: 'text/csv;charset=utf-8;' });
  const a    = document.createElement('a');
  a.href     = URL.createObjectURL(blob);
  a.download = 'products_<?= date("Y-m-d") ?>.csv';
  a.click();
}
</script>
