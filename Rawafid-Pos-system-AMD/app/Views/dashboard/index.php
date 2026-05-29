<?php
function get_status_icon($stock_qty) {
    $color = ($stock_qty <= 0) ? '#b83232' : '#d97706'; // أحمر للنفاد، برتقالي للتنبيه
    
    if ($stock_qty <= 0) {
        // SVG الخاص بـ circle-x
        return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="'.$color.'" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M10 10l4 4m0 -4l-4 4" /></svg>';
    } else {
        // SVG الخاص بـ alert-circle
        return '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="'.$color.'" fill="none" stroke-linecap="round" stroke-linejoin="round"><path stroke="none" d="M0 0h24v24H0z" fill="none"/><path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" /><path d="M12 8l0 4" /><path d="M12 16l.01 0" /></svg>';
    }
}
?>
<?php
/* =============================================
   لوحة التحكم الرئيسية
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-dashboard" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 4h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-6a1 1 0 0 1 1 -1" />
  <path d="M5 16h4a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" />
  <path d="M14 4h4a1 1 0 0 1 1 1v3a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-3a1 1 0 0 1 1 -1" />
  <path d="M14 13h4a1 1 0 0 1 1 1v7a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1v-7a1 1 0 0 1 1 -1" />
</svg> لوحة التحكم</h1>
  <div class="page-actions">
    <span class="db-date-badge">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
  <path d="M16 3v4" />
  <path d="M8 3v4" />
  <path d="M4 11h16" />
  <path d="M11 15h1" />
  <path d="M12 15v3" />
</svg> <?= date('d/m/Y — l', time()) ?>
    </span>
  </div>
</div>

<!-- ======= بطاقات الإحصائيات ======= -->
<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3v18" />
  <path d="M16.5 7.5a3.5 3.5 0 0 0 -3.5 -3.5h-2a3.5 3.5 0 0 0 0 7h2a3.5 3.5 0 0 1 0 7h-2a3.5 3.5 0 0 1 -3.5 -3.5" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">مبيعات اليوم</div>
      <div class="stat-value"><?= number_format($todaySales['total'] ?? 0, 0) ?></div>
      <div class="stat-sub"><?= $currency ?> &bull; <?= $todaySales['count'] ?? 0 ?> فاتورة</div>
    </div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-line" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 19l16 0" />
  <path d="M4 15l4 -4l4 4l8 -8" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">مبيعات الشهر</div>
      <div class="stat-value"><?= number_format($monthSales['total'] ?? 0, 0) ?></div>
      <div class="stat-sub"><?= $currency ?></div>
    </div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">عملاء جدد اليوم</div>
      <div class="stat-value"><?= $newCustomers ?? 0 ?></div>
      <div class="stat-sub">عميل</div>
    </div>
  </div>
  <div class="stat-card <?= ($lowStockCount ?? 0) > 0 ? 'red' : 'green' ?>">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 9v4" />
  <path d="M12 17h.01" />
  <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">تنبيهات المخزون</div>
      <div class="stat-value"><?= $lowStockCount ?? 0 ?></div>
      <div class="stat-sub"><?= ($outOfStock ?? 0) ?> نفذ تماماً</div>
    </div>
  </div>
</div>

<!-- ======= الرسوم البيانية + الأكثر مبيعاً ======= -->
<div class="row-2col">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M4 20l16 0" />
</svg> مبيعات آخر 7 أيام</h3>
    </div>
    <div style="position:relative;height:160px">
      <canvas id="weekly-chart" style="width:100%;height:160px"></canvas>
    </div>
  </div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trophy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 21l8 0" />
  <path d="M12 17l0 4" />
  <path d="M7 4l10 0" />
  <path d="M17 4v8a5 5 0 0 1 -10 0v-8" />
  <path d="M5 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M7 7v4" />
  <path d="M19 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 7v4" />
</svg> الأكثر مبيعاً هذا الشهر</h3>
      <a href="?page=reports" class="btn btn-sm btn-outline">كل التقارير</a>
    </div>
    <?php if (empty($topProducts)): ?>
    <div class="db-empty-small"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-off" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 4v2m0 4v10" />
  <path d="M12 12v8" />
  <path d="M16 16v4" />
  <path d="M3 3l18 18" />
</svg> لا توجد مبيعات هذا الشهر</div>
    <?php else: ?>
    <div class="db-top-list">
      <?php foreach ($topProducts as $i => $p): ?>
      <div class="db-top-item">
        <span class="db-rank rank-<?= min($i+1,3) ?>"><?= $i+1 ?></span>
        <span class="db-top-name"><?= Helper::e($p['name']) ?></span>
        <span class="db-top-qty"><?= number_format($p['total_qty'], 0) ?></span>
        <span class="db-top-rev"><?= number_format($p['total_revenue'], 0) ?> <small><?= $currency ?></small></span>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endif; ?>
  </div>
</div>

<!-- ======= تنبيهات المخزون + آخر الفواتير ======= -->
<div class="row-2col">
  <!-- تنبيهات المخزون -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title" style="color:<?= ($lowStockCount??0) > 0 ? '#b83232' : 'inherit' ?>">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-triangle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 9v4" />
  <path d="M12 17h.01" />
  <path d="M5 19h14a2 2 0 0 0 1.84 -2.75l-7.1 -12.25a2 2 0 0 0 -3.5 0l-7.1 12.25a2 2 0 0 0 1.75 2.75" />
</svg> تنبيهات المخزون
      </h3>
      <a href="?page=inventory" class="btn btn-sm btn-outline">إدارة المخزون</a>
    </div>
    
    <?php if (empty($lowStockProducts)): ?>
      <div class="db-empty-small success"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-circle-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M9 12l2 2l4 -4" />
</svg> المخزون في مستوى جيد</div>
    <?php else: ?>
      <!-- إضافة السكرول هنا بتحديد ارتفاع (مثلاً 300px) -->
      <div class="db-alerts-list" style="max-height: 300px; overflow-y: auto;">
        <?php foreach ($lowStockProducts as $p): ?>
        <div class="db-alert-row <?= $p['stock_qty'] <= 0 ? 'db-al-out' : 'db-al-low' ?>">
          <td>
            <?php echo get_status_icon($p['stock_qty']); ?>
        </td>
        <!-- <i class="ti ti-<?= $p['stock_qty'] <= 0 ? 'circle-x' : 'alert-circle' ?>"></i> -->
          <span class="db-al-name"><?= Helper::e($p['name']) ?></span>
          <span class="db-al-qty">
            <?= $p['stock_qty'] <= 0 ? 'نفذ' : number_format($p['stock_qty'], 0).' '.Helper::e($p['unit']) ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- آخر الفواتير -->
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M13 15l2 0" />
</svg> آخر الفواتير</h3>
      <a href="?page=reports" class="btn btn-sm btn-outline">عرض الكل</a>
    </div>
    
    <!-- إضافة السكرول هنا بتحديد ارتفاع (مثلاً 300px) -->
    <div class="table-wrap" style="max-height: 300px; overflow-y: auto;">
      <table class="data-table">
        <thead>
          <tr><th>رقم الفاتورة</th><th>العميل</th><th>المبلغ</th><th>الحالة</th></tr>
        </thead>
        <tbody>
          <?php if (empty($recentInvoices)): ?>
          <tr><td colspan="4" class="empty-td">لا توجد فواتير بعد</td></tr>
          <?php else: ?>
          <?php foreach ($recentInvoices as $inv):
            $stMap   = ['paid'=>['مدفوع','badge-success'],'pending'=>['معلّق','badge-warning'],'partial'=>['جزئي','badge-info'],'returned'=>['مرتجع','badge-danger']];
            $stBadge = $stMap[$inv['status']] ?? ['—','badge-secondary'];
          ?>
          <tr>
            <td>
              <a href="?page=pos&action=printInvoice&id=<?= $inv['id'] ?>" target="_blank" class="link-btn">
                <?= Helper::e($inv['invoice_number']) ?>
              </a>
            </td>
            <td class="db-cname"><?= Helper::e($inv['customer_name'] ?? 'نقدي') ?></td>
            <td class="fw-bold"><?= number_format($inv['total'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
            <td><span class="badge <?= $stBadge[1] ?>"><?= $stBadge[0] ?></span></td>
          </tr>
          <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<!-- ======= اختصارات سريعة ======= -->
<div class="db-shortcuts">
  <a href="?page=pos"              class="db-shortcut blue"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash-register" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16 15h-10a1 1 0 0 1 -1 -1v-8a1 1 0 0 1 1 -1h10a1 1 0 0 1 1 1v8a1 1 0 0 1 -1 1z" />
  <path d="M7 10h1v1h-1z" />
  <path d="M11 10h1v1h-1z" />
  <path d="M11 14h1v1h-1z" />
  <path d="M15 10h1v1h-1z" />
  <path d="M7 14h1v1h-1z" />
  <path d="M4 19h16a1 1 0 0 0 1 -1v-1a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v1a1 1 0 0 0 1 1z" />
  <path d="M16 8h-3v3h3v-3z" />
</svg><span>نقطة البيع</span><small>F1</small></a>
  <a href="?page=products&action=create" class="db-shortcut green"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg><span>منتج جديد</span></a>
  <a href="?page=customers"        class="db-shortcut orange"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
</svg><span>العملاء</span></a>
  <a href="?page=inventory"        class="db-shortcut red"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-box" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
</svg><span>المخزون</span></a>
  <a href="?page=returns&action=search" class="db-shortcut red"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M4 20l16 0" />
</svg><span>مرتجع</span></a>
  <a href="?page=reports"          class="db-shortcut purple"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-shopping-cart" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 19m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17h-11v-14h-2" />
  <path d="M6 5l14 1l-1 7h-13" />
</svg><span>التقارير</span></a>
</div>

<style>
.db-date-badge{background:var(--surface);border:1px solid var(--border);border-radius:7px;padding:6px 12px;font-size:12px;color:var(--text-2);display:flex;align-items:center;gap:6px}
.db-top-list{display:flex;flex-direction:column;gap:6px}
.db-top-item{display:flex;align-items:center;gap:8px;padding:5px 0;border-bottom:1px solid var(--border-2);font-size:12px}
.db-top-item:last-child{border-bottom:none}
.db-rank{width:22px;height:22px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:11px;font-weight:700;background:var(--bg);flex-shrink:0}
.rank-1{background:#fff4dd;color:#8a5800}
.rank-2{background:#dde8ee;color:#1a50a8}
.rank-3{background:#f0e4d4;color:#6a3010}
[data-theme="dark"] .rank-1{background:rgba(138,88,0,.2);color:#ffca6a}
[data-theme="dark"] .rank-2{background:rgba(26,80,168,.2);color:#5a9eff}
[data-theme="dark"] .rank-3{background:rgba(106,48,16,.2);color:#ffb07a}
.db-top-name{flex:1;color:var(--text);font-weight:500}
.db-top-qty{color:var(--text-3);min-width:36px;text-align:center}
.db-top-rev{font-weight:700;color:var(--olive-500)}
[data-theme="dark"] .db-top-rev{color:#5a9eff}
.db-empty-small{text-align:center;padding:18px;color:var(--text-3);font-size:12px}
.db-empty-small.success{color:#3a7030}
[data-theme="dark"] .db-empty-small.success{color:#72c870}
.db-alerts-list{display:flex;flex-direction:column;gap:5px}
.db-alert-row{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:7px;font-size:12px}
.db-al-out{background:var(--expired-bg);color:var(--expired-text)}
.db-al-low{background:var(--warn-bg);color:var(--warn-text)}
.db-al-name{flex:1;font-weight:600}
.db-al-qty{font-weight:700}
.db-cname{font-size:12px;color:var(--text-2)}
/* اختصارات */
.db-shortcuts{display:grid;grid-template-columns:repeat(6,1fr);gap:10px;margin-top:4px}
.db-shortcut{display:flex;flex-direction:column;align-items:center;justify-content:center;gap:6px;padding:16px 8px;border-radius:10px;text-decoration:none;transition:all var(--transition);border:1.5px solid var(--border);background:var(--surface);font-size:12px;font-weight:600;color:var(--text-2)}
.db-shortcut:hover{transform:translateY(-2px);box-shadow:var(--shadow-md)}
.db-shortcut i{font-size:24px}
.db-shortcut small{font-size:10px;opacity:.6;font-weight:400}
.db-shortcut.blue:hover{border-color:#5a9eff;color:#1a50a8;background:#e6f0ff}
.db-shortcut.green:hover{border-color:var(--olive-400);color:var(--olive-600);background:var(--olive-50)}
.db-shortcut.orange:hover{border-color:#f5d48a;color:#8a5800;background:#fff4dd}
.db-shortcut.red:hover{border-color:#f5c0c0;color:#b03030;background:#feeaea}
.db-shortcut.purple:hover{border-color:#c0b8f8;color:#5040a8;background:#eeebff}
.db-shortcut.teal:hover{border-color:#9ad4c8;color:#1a6b5a;background:#e6f5f0}
[data-theme="dark"] .db-shortcut.blue:hover{background:rgba(90,158,255,.1);color:#5a9eff;border-color:rgba(90,158,255,.3)}
[data-theme="dark"] .db-shortcut.green:hover{background:rgba(58,112,48,.1);color:#72c870;border-color:rgba(114,200,112,.3)}
[data-theme="dark"] .db-shortcut.orange:hover{background:rgba(138,88,0,.1);color:#ffca6a;border-color:rgba(255,202,106,.3)}
[data-theme="dark"] .db-shortcut.red:hover{background:rgba(176,48,48,.1);color:#ff8a88;border-color:rgba(255,138,136,.3)}
[data-theme="dark"] .db-shortcut.purple:hover{background:rgba(80,64,168,.1);color:#a090ff;border-color:rgba(160,144,255,.3)}
[data-theme="dark"] .db-shortcut.teal:hover{background:rgba(26,107,90,.1);color:#60c8b8;border-color:rgba(96,200,184,.3)}
@media(max-width:900px){.db-shortcuts{grid-template-columns:repeat(3,1fr)}}
</style>

<script>
(function() {
  const canvas = document.getElementById('weekly-chart');
  if (!canvas) return;
  const ctx  = canvas.getContext('2d');
  const data = <?= json_encode(array_map(fn($r) => (float)$r['total'], $weeklySales ?? [])) ?>;
  const days = <?= json_encode(array_map(fn($r) => date('D', strtotime($r['day'])), $weeklySales ?? [])) ?>;
  const W    = canvas.parentElement.offsetWidth;
  const H    = 160;
  canvas.width  = W;
  canvas.height = H;

  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const barClr = isDark ? '#5a9eff' : '#4a8a3c';
  const barClr2 = isDark ? 'rgba(90,158,255,.3)' : 'rgba(74,138,60,.25)';
  const txtClr = isDark ? '#4a6888' : '#7a8e72';

  if (!data.length) {
    ctx.fillStyle = txtClr;
    ctx.font = '13px sans-serif';
    ctx.textAlign = 'center';
    ctx.fillText('لا توجد بيانات', W/2, H/2);
    return;
  }
  const maxV = Math.max(...data, 1);
  const n    = data.length;
  const gap  = 6;
  const bW   = Math.floor((W - 40 - gap * (n-1)) / n);
  data.forEach((v, i) => {
    const h = Math.max(4, Math.round(v / maxV * (H - 30)));
    const x = 20 + i * (bW + gap);
    const y = H - 20 - h;
    const g = ctx.createLinearGradient(0, y, 0, H-20);
    g.addColorStop(0, barClr);
    g.addColorStop(1, barClr2);
    ctx.fillStyle = g;
    ctx.beginPath();
    if (ctx.roundRect) ctx.roundRect(x, y, bW, h, [4,4,0,0]);
    else ctx.rect(x, y, bW, h);
    ctx.fill();
    if (days[i] && n <= 14) {
      ctx.fillStyle = txtClr;
      ctx.font = '9px sans-serif';
      ctx.textAlign = 'center';
      ctx.fillText(days[i].slice(0,3), x + bW/2, H - 5);
    }
  });
})();
</script>
