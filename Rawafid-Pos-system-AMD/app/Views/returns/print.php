<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>إيصال مرتجع - <?= Helper::e($invoice['invoice_number']) ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',Courier,monospace;font-size:12px;background:#fff;color:#000;direction:rtl}
.receipt{width:80mm;margin:0 auto;padding:6px 8px}
.r-header{text-align:center;padding-bottom:8px;border-bottom:2px dashed #000;margin-bottom:8px}
.r-logo{font-size:20px;font-weight:700;margin-bottom:3px}
.r-type{font-size:14px;font-weight:700;background:#000;color:#fff;padding:3px 12px;display:inline-block;margin:4px 0;letter-spacing:1px}
.r-sub{font-size:10px;color:#555}
.r-meta{font-size:11px;margin-bottom:8px}
.r-meta table{width:100%}
.r-meta td{padding:2px 0;vertical-align:top}
.r-meta td:last-child{text-align:left;font-weight:600}
.r-items{width:100%;border-collapse:collapse;margin-bottom:8px}
.r-items th{border-top:1px solid #000;border-bottom:1px solid #000;padding:4px 2px;font-size:10px;text-align:right}
.r-items td{padding:4px 2px;font-size:11px;border-bottom:1px dashed #ccc}
.r-items tr:last-child td{border-bottom:1px solid #000}
.r-total{font-size:14px;font-weight:700;text-align:center;border:2px solid #000;padding:6px;margin:6px 0}
.r-footer{text-align:center;border-top:1px dashed #000;padding-top:8px;font-size:10px;color:#555;line-height:1.7}
.r-original{font-size:10px;background:#f5f5f5;border:1px solid #ccc;padding:4px 6px;margin-bottom:6px;border-radius:3px}
.no-print{text-align:center;margin:16px;display:flex;gap:8px;justify-content:center}
.btn-p{padding:9px 22px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600}
.btn-red{background:#b83232;color:white}
.btn-ghost{background:#eee;color:#333}
@media print{.no-print{display:none!important} body{margin:0} @page{margin:0;size:80mm auto}}
</style>
</head>
<body>
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
    <img src="<?= BASE_URL ?>/uploads/<?= Helper::e($settings['logo']) ?>" style="height:36px;display:block;margin:0 auto 4px"><br>
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
        <th style="text-align:center">الكمية</th>
        <th style="text-align:left">الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($items as $item): ?>
      <tr>
        <td><?= Helper::e($item['product_name']) ?></td>
        <td style="text-align:center"><?= number_format($item['quantity'], 0) ?></td>
        <td style="text-align:left;font-weight:700"><?= number_format($item['total'], 2) ?></td>
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
    <div style="margin-top:4px"><?= date('d/m/Y H:i:s') ?></div>
    <div style="margin-top:4px"><?= Helper::e($settings['receipt_footer'] ?? 'شكراً لتعاملكم معنا') ?></div>
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
