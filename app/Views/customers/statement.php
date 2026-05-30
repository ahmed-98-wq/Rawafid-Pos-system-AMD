<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>كشف حساب - <?= Helper::e($customer['name']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= APP_VERSION ?>">
</head>
<body class="document-page statement-page">
<?php
$currency = $settings['currency'] ?? 'ج.س';
$pmNames  = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
$stMap    = ['paid'=>['مدفوع','badge-success'],'pending'=>['معلّق','badge-warning'],'partial'=>['جزئي','badge-info'],'returned'=>['مرتجع','badge-danger']];
?>

<!-- أزرار التحكم -->
<div class="no-print u-style-31">
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
      <div class="u-style-32"><i class="ti ti-phone"></i> <?= Helper::e($customer['phone']) ?></div>
      <?php endif; ?>
      <?php if($customer['address']): ?>
      <div class="u-style-10"><i class="ti ti-map-pin"></i> <?= Helper::e($customer['address']) ?></div>
      <?php endif; ?>
    </div>
  </div>

  <!-- فلتر التاريخ -->
  <div class="filter-bar no-print">
    <form method="GET" class="u-style-33">
      <input type="hidden" name="page"   value="customers">
      <input type="hidden" name="action" value="statement">
      <input type="hidden" name="id"     value="<?= (int)$customer['id'] ?>">
      <label class="cell-soft">من</label>
      <input type="date" name="from" value="<?= $from ?>" class="form-input w-145">
      <label class="cell-soft">إلى</label>
      <input type="date" name="to"   value="<?= $to ?>"   class="form-input w-145">
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
    <div class="st-stat <?= (float)$customer['balance'] > 0 ? 'is-danger' : '' ?>">
      <div class="st-stat-val <?= (float)$customer['balance'] > 0 ? 'text-danger' : 'text-success' ?>">
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
          <td class="cell-regular"><?= Helper::formatDate($inv['created_at'],'d/m/Y H:i') ?></td>
          <td class="cell-muted"><?= Helper::e($inv['cashier_name'] ?? '') ?></td>
          <td class="fw-bold"><?= number_format($inv['total'],2) ?></td>
          <td class="u-style-34"><?= number_format($inv['paid_amount'],2) ?></td>
          <td><?= $remaining > 0 ? '<span class="u-style-35">'.number_format($remaining,2).'</span>' : '<span class="u-style-36">✓</span>' ?></td>
          <td class="cell-regular"><?= $pmNames[$inv['payment_method']] ?? '—' ?></td>
          <td><span class="badge <?= $sb[1] ?>"><?= $sb[0] ?></span></td>
        </tr>
        <?php endforeach; ?>
        <!-- صف الإجمالي -->
        <tr class="u-style-37">
          <td colspan="3" class="u-style-38">الإجمالي</td>
          <td><?= number_format($stats['total_sales'] ?? 0, 2) ?></td>
          <td class="u-style-34"><?= number_format($stats['total_paid'] ?? 0, 2) ?></td>
          <td class="text-danger"><?= number_format($customer['balance'] ?? 0, 2) ?></td>
          <td colspan="2"></td>
        </tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- ملاحظات العميل -->
  <?php if($customer['notes']): ?>
  <div class="card cell-soft">
    <strong>ملاحظات:</strong> <?= Helper::e($customer['notes']) ?>
  </div>
  <?php endif; ?>

  <!-- توقيع -->
  <div class="print-only u-style-39 is-hidden">
    <div class="text-center">
      <div class="u-style-40"></div>
      <div class="cell-muted">توقيع الموظف</div>
    </div>
    <div class="text-center">
      <div class="u-style-40"></div>
      <div class="cell-muted">توقيع العميل</div>
    </div>
  </div>
</div>

<script>
if (localStorage.getItem('pos_theme') === 'dark')
  document.documentElement.setAttribute('data-theme','dark');
</script>
</body>
</html>
