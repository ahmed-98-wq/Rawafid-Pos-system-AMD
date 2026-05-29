<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>كشف حساب - <?= Helper::e($customer['name']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<style>
body{background:var(--bg);padding:20px}
.st-wrap{max-width:800px;margin:0 auto}
.st-header{background:var(--surface);border:1px solid var(--border);border-radius:12px;padding:20px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:flex-start}
.st-biz{font-size:18px;font-weight:700;color:var(--text)}
.st-biz-sub{font-size:12px;color:var(--text-3);margin-top:3px}
.st-cust{text-align:left}
.st-cust-name{font-size:16px;font-weight:700;color:var(--text)}
.st-stats{display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px}
.st-stat{background:var(--surface);border:1px solid var(--border);border-radius:9px;padding:14px;text-align:center}
.st-stat-val{font-size:20px;font-weight:700;color:var(--text)}
.st-stat-lbl{font-size:11px;color:var(--text-2);margin-top:2px}
.filter-bar{background:var(--surface);border:1px solid var(--border);border-radius:9px;padding:12px 16px;margin-bottom:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.st-table{background:var(--surface);border:1px solid var(--border);border-radius:10px;overflow:hidden;margin-bottom:16px}
@media print{.no-print{display:none!important} body{padding:0} @page{margin:12mm}}
</style>
</head>
<body>
<?php
$currency = $settings['currency'] ?? 'ج.س';
$pmNames  = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
$stMap    = ['paid'=>['مدفوع','badge-success'],'pending'=>['معلّق','badge-warning'],'partial'=>['جزئي','badge-info'],'returned'=>['مرتجع','badge-danger']];
?>

<!-- أزرار التحكم -->
<div class="no-print" style="display:flex;gap:10px;margin-bottom:14px">
  <button onclick="window.print()" class="btn btn-primary"><i class="ti ti-printer"></i> طباعة</button>
  <button onclick="window.close()" class="btn btn-ghost"><i class="ti ti-x"></i> إغلاق</button>
</div>

<!-- رأس الكشف -->
<div class="st-wrap">
  <div class="st-header">
    <div>
      <div class="st-biz"><?= Helper::e($settings['business_name'] ?? '') ?></div>
      <div class="st-biz-sub">كشف حساب عميل — تاريخ الطباعة: <?= date('d/m/Y H:i') ?></div>
    </div>
    <div class="st-cust">
      <div class="st-cust-name"><?= Helper::e($customer['name']) ?></div>
      <?php if($customer['phone']): ?>
      <div style="font-size:12px;color:var(--text-3);margin-top:3px"><i class="ti ti-phone"></i> <?= Helper::e($customer['phone']) ?></div>
      <?php endif; ?>
      <?php if($customer['address']): ?>
      <div style="font-size:12px;color:var(--text-3);margin-top:2px"><i class="ti ti-map-pin"></i> <?= Helper::e($customer['address']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- فلتر التاريخ -->
  <div class="filter-bar no-print">
    <form method="GET" style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
      <input type="hidden" name="page"   value="customers">
      <input type="hidden" name="action" value="statement">
      <input type="hidden" name="id"     value="<?= (int)$customer['id'] ?>">
      <label style="font-size:12px;color:var(--text-2)">من</label>
      <input type="date" name="from" value="<?= $from ?>" class="form-input" style="width:145px">
      <label style="font-size:12px;color:var(--text-2)">إلى</label>
      <input type="date" name="to"   value="<?= $to ?>"   class="form-input" style="width:145px">
      <button type="submit" class="btn btn-primary btn-sm"><i class="ti ti-search"></i> عرض</button>
    </form>
  </div>

  <!-- إحصائيات -->
  <div class="st-stats">
    <div class="st-stat">
      <div class="st-stat-val"><?= number_format($stats['total_sales'] ?? 0, 2) ?></div>
      <div class="st-stat-lbl">إجمالي المشتريات (<?= $currency ?>)</div>
    </div>
    <div class="st-stat">
      <div class="st-stat-val"><?= number_format($stats['total_paid'] ?? 0, 2) ?></div>
      <div class="st-stat-lbl">إجمالي المدفوع (<?= $currency ?>)</div>
    </div>
    <div class="st-stat" style="<?= (float)$customer['balance'] > 0 ? 'border-color:#f5c0c0;' : '' ?>">
      <div class="st-stat-val" style="color:<?= (float)$customer['balance'] > 0 ? '#b83232' : '#3a7030' ?>">
        <?= number_format($customer['balance'] ?? 0, 2) ?>
      </div>
      <div class="st-stat-lbl">الرصيد المستحق (<?= $currency ?>)</div>
    </div>
  </div>

  <!-- جدول الفواتير -->
  <div class="st-table">
    <table class="data-table">
      <thead>
        <tr>
          <th>رقم الفاتورة</th><th>التاريخ</th><th>الكاشير</th>
          <th>الإجمالي</th><th>المدفوع</th><th>المتبقي</th>
          <th>طريقة الدفع</th><th>الحالة</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($invoices)): ?>
        <tr><td colspan="8" class="empty-td">لا توجد فواتير في هذه الفترة</td></tr>
        <?php else: ?>
        <?php
        $runningBalance = 0;
        foreach ($invoices as $inv):
          $remaining = (float)$inv['total'] - (float)$inv['paid_amount'];
          $runningBalance += $remaining;
          $sb = $stMap[$inv['status']] ?? ['—','badge-secondary'];
        ?>
        <tr>
          <td class="fw-bold"><a href="?page=pos&action=printInvoice&id=<?= $inv['id'] ?>" target="_blank" class="link-btn"><?= Helper::e($inv['invoice_number']) ?></a></td>
          <td style="font-size:12px"><?= Helper::formatDate($inv['created_at'],'d/m/Y H:i') ?></td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::e($inv['cashier_name'] ?? '') ?></td>
          <td class="fw-bold"><?= number_format($inv['total'],2) ?></td>
          <td style="color:#3a7030"><?= number_format($inv['paid_amount'],2) ?></td>
          <td><?= $remaining > 0 ? '<span style="color:#b83232;font-weight:700">'.number_format($remaining,2).'</span>' : '<span style="color:#3a7030;font-size:12px">✓</span>' ?></td>
          <td style="font-size:12px"><?= $pmNames[$inv['payment_method']] ?? '—' ?></td>
          <td><span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span></td>
        </tr>
        <?php endforeach; ?>
        <!-- صف الإجمالي -->
        <tr style="background:var(--bg);font-weight:700">
          <td colspan="3" style="text-align:left;font-size:13px;padding:10px 12px">الإجمالي</td>
          <td><?= number_format($stats['total_sales'] ?? 0, 2) ?></td>
          <td style="color:#3a7030"><?= number_format($stats['total_paid'] ?? 0, 2) ?></td>
          <td style="color:#b83232"><?= number_format($customer['balance'] ?? 0, 2) ?></td>
          <td colspan="2"></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ملاحظات العميل -->
  <?php if($customer['notes']): ?>
  <div class="card" style="font-size:13px;color:var(--text-2)">
    <strong>ملاحظات:</strong> <?= Helper::e($customer['notes']) ?>
  </div>
  <?php endif; ?>

  <!-- توقيع -->
  <div style="display:grid;grid-template-columns:1fr 1fr;gap:30px;margin-top:20px;padding:16px;background:var(--surface);border:1px solid var(--border);border-radius:9px" class="print-only" style="display:none">
    <div style="text-align:center">
      <div style="height:50px;border-bottom:1px solid var(--border);margin-bottom:8px"></div>
      <div style="font-size:12px;color:var(--text-3)">توقيع الموظف</div>
    </div>
    <div style="text-align:center">
      <div style="height:50px;border-bottom:1px solid var(--border);margin-bottom:8px"></div>
      <div style="font-size:12px;color:var(--text-3)">توقيع العميل</div>
    </div>
  </div>
</div>

<script>
if (localStorage.getItem('pos_theme') === 'dark')
  document.documentElement.setAttribute('data-theme','dark');
</script>
</body>
</html>
