<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>إيصال مرتجع - <?= Helper::e($invoice['invoice_number']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css?v=<?= APP_VERSION ?>">
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/tabler-icons.min.css?v=<?= APP_VERSION ?>">
</head>
<body class="receipt-page return-print-page">
<?php
$currency = $settings['currency'] ?? 'ج.س';
// استخراج رقم الفاتورة الأصلية
preg_match('/مرتجع من: ([^\s|]+)/', $invoice['notes'] ?? '', $m);
$originalNum = $m[1] ?? '—';
preg_match('/السبب: (.+)$/', $invoice['notes'] ?? '', $sm);
$reason = trim($sm[1] ?? '');
?>

<div class="receipt">
  <!-- Header -->
  <div class="r-header">
    <?php if(!empty($settings['logo'])): ?>
    <img src="<?= BASE_URL ?>/uploads/<?= Helper::e($settings['logo']) ?>" class="receipt-logo-img-sm"><br>
    <?php endif; ?>
    <div class="r-logo"><?= Helper::e($settings['business_name'] ?? '') ?></div>
    <div class="r-type">⬅ إيصال مرتجع</div>
  </div>

  <!-- Meta -->
  <div class="r-meta">
    <table>
      <tr><td>رقم المرتجع</td><td><?= Helper::e($invoice['invoice_number']) ?></td></tr>
      <tr><td>الفاتورة الأصلية</td><td><?= Helper::e($originalNum) ?></td></tr>
      <tr><td>التاريخ</td><td><?= Helper::formatDate($invoice['created_at'], 'd/m/Y H:i') ?></td></tr>
      <tr><td>الكاشير</td><td><?= Helper::e($invoice['cashier_name'] ?? '') ?></td></tr>
      <?php if (!empty($invoice['customer_name']) && $invoice['customer_name'] !== 'عميل نقدي'): ?>
      <tr><td>العميل</td><td><?= Helper::e($invoice['customer_name']) ?></td></tr>
      <?php endif; ?>
    </table>
  </div>

  <?php if ($reason): ?>
  <div class="r-original">السبب: <?= Helper::e($reason) ?></div>
  <?php endif; ?>

  <!-- Items -->
  <table class="r-items">
    <thead>
      <tr>
        <th>الصنف المُرجَع</th>
        <th class="text-center">الكمية</th>
        <th class="text-left">الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= Helper::e($item['product_name']) ?></td>
        <td class="text-center"><?= number_format($item['quantity'], 0) ?></td>
        <td class="u-style-134"><?= number_format($item['total'], 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <!-- Total -->
  <div class="r-total">
    المبلغ المُسترَد: <?= number_format($invoice['total'], 2) ?> <?= $currency ?>
  </div>

  <!-- Footer -->
  <div class="r-footer">
    <div>تم تنفيذ الإرجاع بنجاح</div>
    <div>وتمت إعادة المخزون تلقائياً</div>
    <div class="u-style-83"><?= date('d/m/Y H:i:s') ?></div>
    <div class="u-style-83"><?= Helper::e($settings['receipt_footer'] ?? 'شكراً لتعاملكم معنا') ?></div>
  </div>
</div>

<div class="no-print">
  <button onclick="window.print()" class="btn-p btn-red">🖨️ طباعة الإيصال</button>
  <button onclick="window.location.href='?page=returns&action=search'" class="btn-p btn-ghost">إرجاع آخر</button>
  <button onclick="window.location.href='?page=returns'" class="btn-p btn-ghost">القائمة</button>
</div>

<script>window.addEventListener('load', () => window.print());</script>
</body>
</html>
