<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>فاتورة <?= Helper::e($invoice['invoice_number']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= APP_VERSION ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tabler-icons.min.css?v=<?= APP_VERSION ?>">
</head>
<body class="receipt-page pos-print-page">
<?php
$currency = $settings['currency'] ?? 'ج.س';
$pmNames  = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
$stNames  = ['paid'=>'مدفوع','pending'=>'معلّق','partial'=>'جزئي','returned'=>'مرتجع'];
$businessTypes = BUSINESS_TYPES;
?>

<div class="receipt">
  <!-- Header -->
  <div class="r-header">
    <?php if(!empty($settings['logo'])): ?>
    <img src="<?= BASE_URL ?>/uploads/<?= Helper::e($settings['logo']) ?>" class="receipt-logo-img"><br>
    <?php endif; ?>
    <div class="r-logo"><?= Helper::e($settings['business_name'] ?? '') ?></div>
    <?php $btype = $settings['business_type'] ?? '';
    if ($btype && isset($businessTypes[$btype])): ?>
    <div class="r-sub"><?= $businessTypes[$btype] ?></div>
    <?php endif; ?>
    <div class="r-sub">فاتورة مبيعات</div>
  </div>

  <!-- Meta Info -->
  <div class="r-meta">
    <table>
      <tr><td>رقم الفاتورة</td><td><?= Helper::e($invoice['invoice_number']) ?></td></tr>
      <tr><td>التاريخ</td><td><?= Helper::formatDate($invoice['created_at'],'d/m/Y H:i') ?></td></tr>
      <tr><td>الكاشير</td><td><?= Helper::e($invoice['cashier_name'] ?? '') ?></td></tr>
      <?php if(!empty($invoice['customer_name']) && $invoice['customer_name'] !== 'عميل نقدي'): ?>
      <tr><td>العميل</td><td><?= Helper::e($invoice['customer_name']) ?></td></tr>
      <?php endif; ?>
      <tr><td>طريقة الدفع</td><td><?= $pmNames[$invoice['payment_method']] ?? '—' ?></td></tr>
    </table>
  </div>

  <!-- Items -->
  <table class="r-items">
    <thead>
      <tr>
        <th>الصنف</th>
        <th class="text-center">الكمية</th>
        <th class="text-left">السعر</th>
        <th class="text-left">الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($items as $item): ?>
      <tr>
        <td><?= Helper::e($item['product_name']) ?></td>
        <td class="text-center"><?= number_format($item['quantity'],0) ?></td>
        <td class="text-left"><?= number_format($item['unit_price'],2) ?></td>
        <td class="text-left"><?= number_format($item['total'],2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Totals -->
  <div class="r-totals">
    <table>
      <tr><td>المجموع الفرعي</td><td><?= number_format($invoice['subtotal'],2) ?> <?= $currency ?></td></tr>
      <?php if($invoice['discount_amount'] > 0): ?>
      <tr><td>الخصم</td><td>-<?= number_format($invoice['discount_amount'],2) ?> <?= $currency ?></td></tr>
      <?php endif; ?>
      <?php if($invoice['tax_amount'] > 0): ?>
      <tr><td>ضريبة (<?= $invoice['tax_rate'] ?>%)</td><td><?= number_format($invoice['tax_amount'],2) ?> <?= $currency ?></td></tr>
      <?php endif; ?>
      <tr class="r-grand"><td>الإجمالي</td><td><?= number_format($invoice['total'],2) ?> <?= $currency ?></td></tr>
      <?php if($invoice['payment_method'] === 'cash'): ?>
      <tr><td>المدفوع</td><td><?= number_format($invoice['paid_amount'],2) ?> <?= $currency ?></td></tr>
      <?php if($invoice['change_amount'] > 0): ?>
      <tr><td>الباقي</td><td><?= number_format($invoice['change_amount'],2) ?> <?= $currency ?></td></tr>
      <?php endif; ?>
      <?php endif; ?>
    </table>
  </div>

  <!-- Barcode -->
  <div class="r-barcode"><?= Helper::e($invoice['invoice_number']) ?></div>

  <!-- Footer -->
  <div class="r-footer">
    <div><?= Helper::e($settings['receipt_footer'] ?? 'شكراً لتعاملكم معنا') ?></div>
    <div class="u-style-83"><?= date('d/m/Y H:i:s') ?></div>
  </div>
</div>

<div class="no-print">
  <button onclick="window.print()" class="btn-p btn-primary">🖨️ طباعة</button>
  <button onclick="window.close()" class="btn-p btn-ghost">✖ إغلاق</button>
</div>

<script>
window.addEventListener('load', function() { window.print(); });
</script>
</body>
</html>
