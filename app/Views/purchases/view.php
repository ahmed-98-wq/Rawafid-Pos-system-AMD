<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>فاتورة شراء - <?= Helper::e($purchase['purchase_number']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= APP_VERSION ?>">
</head>
<body class="document-page purchase-view-page">
<?php
$currency = $settings['currency'] ?? 'ج.س';
$stMap = ['paid'=>['مدفوع','text-success'],'pending'=>['معلّق','text-warning'],'partial'=>['جزئي','text-info']];
$st = $stMap[$purchase['status']] ?? ['—','text-muted'];
$pmMap = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
?>
<div class="pv-wrap">
  <!-- Header -->
  <div class="pv-header">
    <div>
      <div class="pv-title">فاتورة شراء</div>
      <div class="pv-num"><?= Helper::e($purchase['purchase_number']) ?></div>
    </div>
    <div class="text-left">
      <div class="u-style-118"><?= Helper::e($settings['business_name'] ?? '') ?></div>
      <div class="u-style-119"><?= Helper::formatDate($purchase['created_at'], 'd/m/Y H:i') ?></div>
    </div>
  </div>

  <!-- Meta -->
  <div class="pv-meta">
    <div class="pv-meta-item">
      <label>المورد</label>
      <strong><?= Helper::e($purchase['supplier_name'] ?? 'غير محدد') ?></strong>
    </div>
    <div class="pv-meta-item">
      <label>المستخدم</label>
      <strong><?= Helper::e($purchase['user_name'] ?? '') ?></strong>
    </div>
    <div class="pv-meta-item">
      <label>الحالة</label>
      <strong class="<?= $st[1] ?>"><?= $st[0] ?></strong>
    </div>
  </div>

  <!-- Items -->
  <div class="pv-table-wrap">
    <table class="pv-items">
      <thead>
        <tr><th>#</th><th>المنتج</th><th>الوحدة</th><th>الكمية</th><th>سعر الوحدة</th><th>الإجمالي</th></tr>
      </thead>
      <tbody>
        <?php foreach ($items as $i => $item): ?>
        <tr>
          <td class="u-style-57"><?= $i+1 ?></td>
          <td class="fw-600"><?= Helper::e($item['product_name']) ?></td>
          <td class="cell-muted"><?= Helper::e($item['unit'] ?? 'قطعة') ?></td>
          <td><?= number_format($item['quantity'], 2) ?></td>
          <td><?= number_format($item['unit_price'], 2) ?> <?= $currency ?></td>
          <td class="fw-700"><?= number_format($item['total'], 2) ?> <?= $currency ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="pv-totals">
    <div class="pv-tot-row"><span>الإجمالي</span><span><?= number_format($purchase['total'],2) ?> <?= $currency ?></span></div>
    <div class="pv-tot-row"><span>المدفوع</span><span class="u-style-34"><?= number_format($purchase['paid_amount'],2) ?> <?= $currency ?></span></div>
    <?php if($purchase['total'] - $purchase['paid_amount'] > 0): ?>
    <div class="pv-tot-row text-danger"><span>المتبقي</span><span><?= number_format($purchase['total']-$purchase['paid_amount'],2) ?> <?= $currency ?></span></div>
    <?php endif; ?>
    <div class="pv-tot-row final"><span>الإجمالي الكلي</span><span><?= number_format($purchase['total'],2) ?> <?= $currency ?></span></div>
  </div>

  <?php if($purchase['notes']): ?>
  <div class="u-style-120">
    <strong>ملاحظات:</strong> <?= Helper::e($purchase['notes']) ?>
  </div>
  <?php endif; ?>

  <div class="no-print">
    <button onclick="window.print()" class="btn btn-primary"><i class="ti ti-printer"></i> طباعة</button>
    <button onclick="window.close()" class="btn btn-ghost"><i class="ti ti-x"></i> إغلاق</button>
  </div>
</div>
<script>
if(localStorage.getItem('pos_theme')==='dark') document.documentElement.setAttribute('data-theme','dark');
</script>
</body>
</html>
