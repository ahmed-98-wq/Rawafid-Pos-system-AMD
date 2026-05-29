<?php
/* =============================================
   صفحة التقارير والإحصائيات
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
$db       = Database::getInstance();
$from     = Helper::sanitize($_GET['from'] ?? date('Y-m-01'));
$to       = Helper::sanitize($_GET['to']   ?? date('Y-m-d'));

// إجماليات
$totalSales   = $db->fetchOne("SELECT COALESCE(SUM(total),0) as total, COUNT(*) as count FROM invoices WHERE type='sale' AND status!='returned' AND DATE(created_at) BETWEEN ? AND ?", [$from,$to]);
$totalProfit  = (float)$db->fetchColumn("SELECT COALESCE(SUM(ii.total-(ii.quantity*ii.purchase_price)),0) FROM invoice_items ii JOIN invoices i ON ii.invoice_id=i.id WHERE i.type='sale' AND i.status!='returned' AND DATE(i.created_at) BETWEEN ? AND ?", [$from,$to]);
$totalReturns = (float)$db->fetchColumn("SELECT COALESCE(SUM(total),0) FROM invoices WHERE type='return' AND DATE(created_at) BETWEEN ? AND ?", [$from,$to]);

// مبيعات يومية
$dailySales     = $db->fetchAll("SELECT DATE(created_at) as day, COALESCE(SUM(total),0) as total, COUNT(*) as cnt FROM invoices WHERE type='sale' AND status!='returned' AND DATE(created_at) BETWEEN ? AND ? GROUP BY DATE(created_at) ORDER BY day ASC", [$from,$to]);
$topProducts    = $db->fetchAll("SELECT p.name, SUM(ii.quantity) as qty, SUM(ii.total) as revenue, SUM(ii.total-ii.quantity*ii.purchase_price) as profit FROM invoice_items ii JOIN invoices i ON ii.invoice_id=i.id JOIN products p ON ii.product_id=p.id WHERE i.type='sale' AND i.status!='returned' AND DATE(i.created_at) BETWEEN ? AND ? GROUP BY ii.product_id ORDER BY qty DESC LIMIT 10", [$from,$to]);
$paymentMethods = $db->fetchAll("SELECT payment_method, COUNT(*) as cnt, SUM(total) as total FROM invoices WHERE type='sale' AND status!='returned' AND DATE(created_at) BETWEEN ? AND ? GROUP BY payment_method", [$from,$to]);
$recentInvoices = $db->fetchAll("SELECT i.*, c.name as cname, u.full_name as uname FROM invoices i LEFT JOIN customers c ON i.customer_id=c.id LEFT JOIN users u ON i.user_id=u.id WHERE i.type='sale' AND DATE(i.created_at) BETWEEN ? AND ? ORDER BY i.created_at DESC LIMIT 20", [$from,$to]);

$pmNames = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
$stMap   = ['paid'=>['مدفوع','badge-success'],'pending'=>['معلّق','badge-warning'],'partial'=>['جزئي','badge-info'],'returned'=>['مرتجع','badge-danger']];
$profitMargin = ($totalSales['total'] > 0) ? round($totalProfit / $totalSales['total'] * 100, 1) : 0;
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-bar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 13a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v6a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M15 9a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v10a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M9 5a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v14a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M4 20h16" />
</svg> التقارير والإحصائيات</h1>
  <div class="page-actions">
    <button onclick="window.print()" class="btn btn-outline"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
  <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
  <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
</svg> طباعة</button>
    <a href="?page=reports&action=exportExcel&from=<?= $from ?>&to=<?= $to ?>" class="btn btn-outline">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-spreadsheet" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 3v4a1 1 0 0 0 1 1h4" />
  <path d="M5 12v-7a2 2 0 0 1 2 -2h7l5 5v4" />
  <path d="M4 15m0 2a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v3a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2z" />
  <path d="M10 15v7" />
  <path d="M14 15v7" />
  <path d="M4 18h16" />
</svg> تصدير Excel
    </a>
  </div>
</div>

<!-- ======= فلتر التاريخ ======= -->
<div class="card filters-bar">
  <form method="GET" class="filters-form" id="rpt-form">
    <input type="hidden" name="page" value="reports">
    <div class="filter-group">
      <label class="form-label-sm"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
  <path d="M16 3v4" />
  <path d="M8 3v4" />
  <path d="M4 11h16" />
  <path d="M11 15h1" />
  <path d="M12 15v3" />
</svg> من</label>
      <input type="date" name="from" id="rpt-from" value="<?= $from ?>" class="form-input" style="width:150px">
    </div>
    <div class="filter-group">
      <label class="form-label-sm">إلى</label>
      <input type="date" name="to" id="rpt-to" value="<?= $to ?>" class="form-input" style="width:150px">
    </div>
    <div class="filter-group" style="gap:4px">
      <button type="button" onclick="setRange('today')"  class="btn btn-xs btn-outline">اليوم</button>
      <button type="button" onclick="setRange('week')"   class="btn btn-xs btn-outline">الأسبوع</button>
      <button type="button" onclick="setRange('month')"  class="btn btn-xs btn-outline">الشهر</button>
      <button type="button" onclick="setRange('year')"   class="btn btn-xs btn-outline">السنة</button>
    </div>
    <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> عرض</button>
  </form>
</div>

<!-- ======= إحصائيات ======= -->
<div class="stats-grid">
  <div class="stat-card green">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">إجمالي المبيعات</div>
      <div class="stat-value"><?= number_format($totalSales['total'], 2) ?></div>
      <div class="stat-sub"><?= $currency ?> &bull; <?= $totalSales['count'] ?> فاتورة</div>
    </div>
  </div>
  <div class="stat-card blue">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trending-up" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 17l6 -6l4 4l8 -8" />
  <path d="M14 7l7 0l0 7" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">صافي الربح</div>
      <div class="stat-value"><?= number_format($totalProfit, 2) ?></div>
      <div class="stat-sub">هامش <?= $profitMargin ?>%</div>
    </div>
  </div>
  <div class="stat-card orange">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-3 -2l-2 2l-2 -2z" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M13 15l2 0" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">متوسط الفاتورة</div>
      <div class="stat-value"><?= $totalSales['count'] > 0 ? number_format($totalSales['total'] / $totalSales['count'], 2) : '0.00' ?></div>
      <div class="stat-sub"><?= $currency ?></div>
    </div>
  </div>
  <div class="stat-card red" style="cursor:pointer" onclick="window.location.href='?page=returns'">
    <div class="stat-icon"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
</svg></div>
    <div class="stat-body">
      <div class="stat-label">إجمالي المرتجعات</div>
      <div class="stat-value"><?= number_format($totalReturns, 2) ?></div>
      <div class="stat-sub"><?= $currency ?> — <span style="text-decoration:underline">عرض المرتجعات</span></div>
    </div>
  </div>
</div>

<!-- ======= الرسوم البيانية ======= -->
<div class="row-2col">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-line" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 19l16 0" />
  <path d="M4 15l4 -6l4 2l4 -5l4 4" />
</svg> المبيعات اليومية</h3>
    </div>
    <canvas id="daily-chart" style="width:100%;height:180px"></canvas>
  </div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-chart-donut" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 3.2a9 9 0 1 0 10.8 10.8a1 1 0 0 0 -1 -1h-3.8a4 4 0 1 1 -5 -5v-3.8a1 1 0 0 0 -1 -1z" />
  <path d="M15 3.5a9 9 0 0 1 5.5 5.5h-4.5a4 4 0 0 0 -1 -1v-4.5z" />
</svg> طرق الدفع</h3>
    </div>
    <canvas id="pm-chart" style="width:100%;height:180px"></canvas>
  </div>
</div>

<div class="row-2col">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trophy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 21l8 0" />
  <path d="M12 17l0 4" />
  <path d="M7 4l10 0" />
  <path d="M17 4v8a5 5 0 0 1 -10 0v-8" />
  <path d="M5 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M7 6v3c0 2.4 1 4 3 4" />
  <path d="M19 9m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 6v3c0 2.4 -1 4 -3 4" />
</svg> المنتجات الأكثر مبيعاً</h3>
    </div>
    <?php if (empty($topProducts)): ?>
      <div style="text-align:center;padding:24px;color:var(--text-3);font-size:13px">لا توجد بيانات للفترة المحددة</div>
    <?php else: ?>
      <div class="table-wrap" style="max-height: 400px; overflow-y: auto; position: relative;">
        <table class="data-table">
          <thead style="position: sticky; top: 0; z-index: 10;">
            <tr><th>#</th><th>المنتج</th><th>الكمية</th><th>الإيرادات</th><th>الربح</th></tr>
          </thead>
          <tbody>
          <?php foreach ($topProducts as $i => $p):
            $pct = $p['revenue'] > 0 ? round($p['profit']/$p['revenue']*100,1) : 0;
          ?>
          <tr>
            <td><span class="rank-badge rank-<?= min($i+1,3) ?>"><?= $i+1 ?></span></td>
            <td class="fw-bold"><?= Helper::e($p['name']) ?></td>
            <td><?= number_format($p['qty'], 0) ?></td>
            <td><?= number_format($p['revenue'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small></td>
            <td>
              <div class="rpt-profit-row">
                <span class="text-green"><?= number_format($p['profit'], 2) ?></span>
                <div class="mini-bar-wrap"><div class="mini-bar" style="width:<?= min($pct,100) ?>%"></div><small><?= $pct ?>%</small></div>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-3 -2l-2 2l-2 -2z" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M13 15l2 0" />
</svg> آخر الفواتير</h3>
    </div>
    <div class="table-wrap" style="max-height: 400px; overflow-y: auto; position: relative;">
      <table class="data-table">
        <thead style="position: sticky; top: 0; z-index: 10;">
          <tr><th>رقم</th><th>العميل</th><th>المبلغ</th><th>الدفع</th><th>الحالة</th></tr>
        </thead>
        <tbody>
        <?php if (empty($recentInvoices)): ?>
        <tr><td colspan="5" class="empty-td">لا توجد فواتير</td></tr>
        <?php else: foreach ($recentInvoices as $inv):
          $sb = $stMap[$inv['status']] ?? ['—','badge-secondary'];
        ?>
        <tr>
          <td><a href="?page=pos&action=printInvoice&id=<?= $inv['id'] ?>" target="_blank" class="link-btn"><?= Helper::e($inv['invoice_number']) ?></a></td>
          <td style="font-size:12px;color:var(--text-2)"><?= Helper::e($inv['cname'] ?? 'نقدي') ?></td>
          <td class="fw-bold"><?= number_format($inv['total'],2) ?></td>
          <td style="font-size:12px"><?= $pmNames[$inv['payment_method']] ?? '—' ?></td>
          <td><span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span></td>
        </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<style>
.rpt-profit-row{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.form-input-sm{padding:6px 10px;border:1.5px solid var(--input-border);border-radius:6px;font-size:12px;color:var(--text);background:var(--input-bg)}
</style>

<script>
// ---- بناء الرسوم البيانية ----
function buildCharts() {
  const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
  const c1 = isDark ? '#5a9eff' : '#4a8a3c';
  const c2 = isDark ? 'rgba(90,158,255,.25)' : 'rgba(74,138,60,.2)';
  const cArr = isDark
    ? ['#5a9eff','#72c870','#ffca6a','#ff8a88','#a090ff']
    : ['#4a8a3c','#84bc72','#b87000','#b83232','#7040a0'];
  const tc = isDark ? '#4a6888' : '#7a8e72';

  // مبيعات يومية
  (function() {
    const cv = document.getElementById('daily-chart');
    if (!cv) return;
    const ctx = cv.getContext('2d');
    const labels = <?= json_encode(array_map(fn($r) => date('d/m', strtotime($r['day'])), $dailySales)) ?>;
    const data   = <?= json_encode(array_map(fn($r) => (float)$r['total'], $dailySales)) ?>;
    const W = cv.parentElement.offsetWidth, H = 180;
    cv.width = W; cv.height = H;
    if (!data.length) { ctx.fillStyle=tc; ctx.font='13px sans-serif'; ctx.textAlign='center'; ctx.fillText('لا توجد بيانات',W/2,H/2); return; }
    const mx = Math.max(...data,1), n = data.length;
    const bW = Math.max(6, Math.floor((W-60)/n - 5));
    data.forEach((v,i) => {
      const h = Math.max(3, Math.round(v/mx*(H-35)));
      const x = 30 + i*((W-60)/n) + 2, y = H-25-h;
      const g = ctx.createLinearGradient(0,y,0,H-25);
      g.addColorStop(0,c1); g.addColorStop(1,c2);
      ctx.fillStyle = g;
      ctx.beginPath();
      if (ctx.roundRect) ctx.roundRect(x,y,bW,h,[3,3,0,0]); else ctx.rect(x,y,bW,h);
      ctx.fill();
      if (n<=16 && labels[i]) { ctx.fillStyle=tc; ctx.font='9px sans-serif'; ctx.textAlign='center'; ctx.fillText(labels[i],x+bW/2,H-7); }
    });
  })();

  // طرق الدفع — دوناتي
  (function() {
    const cv = document.getElementById('pm-chart');
    if (!cv) return;
    const ctx = cv.getContext('2d');
    const names  = <?= json_encode(array_map(fn($r) => $pmNames[$r['payment_method']] ?? $r['payment_method'], $paymentMethods)) ?>;
    const values = <?= json_encode(array_map(fn($r) => (float)$r['total'], $paymentMethods)) ?>;
    const W = cv.parentElement.offsetWidth, H = 180;
    cv.width = W; cv.height = H;
    if (!values.length) { ctx.fillStyle=tc; ctx.font='13px sans-serif'; ctx.textAlign='center'; ctx.fillText('لا توجد بيانات',W/2,H/2); return; }
    const tot = values.reduce((a,b)=>a+b,0);
    const cx=W*0.36, cy=H*0.5, r=Math.min(cx,cy)-16, ri=r*0.52;
    let angle=-Math.PI/2;
    values.forEach((v,i) => {
      const sl=(v/tot)*2*Math.PI;
      ctx.beginPath(); ctx.moveTo(cx,cy); ctx.arc(cx,cy,r,angle,angle+sl); ctx.closePath();
      ctx.fillStyle=cArr[i%cArr.length]; ctx.fill(); angle+=sl;
    });
    ctx.beginPath(); ctx.arc(cx,cy,ri,0,2*Math.PI);
    ctx.fillStyle = isDark ? '#141e30' : '#ffffff'; ctx.fill();
    names.forEach((l,i) => {
      const y=28+i*24;
      ctx.fillStyle=cArr[i%cArr.length]; ctx.fillRect(W*0.66,y,11,11);
      ctx.fillStyle=tc; ctx.font='11px sans-serif'; ctx.textAlign='right';
      ctx.fillText(l,W-8,y+10);
    });
  })();
}

document.addEventListener('DOMContentLoaded', buildCharts);
// إعادة البناء عند تبديل الثيم
document.getElementById('theme-btn')?.addEventListener('click', () => setTimeout(buildCharts, 50));

function setRange(r) {
  const d = new Date(), fmt = x => x.toISOString().slice(0,10);
  let from, to = fmt(d);
  if      (r==='today') from = to;
  else if (r==='week')  { const w=new Date(d); w.setDate(d.getDate()-6); from=fmt(w); }
  else if (r==='month') from = d.getFullYear()+'-'+String(d.getMonth()+1).padStart(2,'0')+'-01';
  else                  from = d.getFullYear()+'-01-01';
  document.getElementById('rpt-from').value = from;
  document.getElementById('rpt-to').value   = to;
  document.getElementById('rpt-form').submit();
}
</script>
