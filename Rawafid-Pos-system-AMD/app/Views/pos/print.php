<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>فاتورة <?= Helper::e($invoice['invoice_number']) ?></title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Courier New',Courier,monospace;font-size:12px;background:#fff;color:#000;direction:rtl}
.receipt{width:80mm;margin:0 auto;padding:6px 8px}
.r-header{text-align:center;padding-bottom:8px;border-bottom:1px dashed #000;margin-bottom:8px}
.r-logo{font-size:22px;font-weight:700;margin-bottom:3px}
.r-sub{font-size:10px;color:#555}
.r-meta{font-size:11px;margin-bottom:8px}
.r-meta table{width:100%}
.r-meta td{padding:2px 0;vertical-align:top}
.r-meta td:last-child{text-align:left}
.r-items{width:100%;border-collapse:collapse;margin-bottom:8px}
.r-items th{border-top:1px solid #000;border-bottom:1px solid #000;padding:4px 2px;font-size:10px;text-align:right}
.r-items td{padding:4px 2px;font-size:11px;border-bottom:1px dashed #ddd}
.r-items tr:last-child td{border-bottom:1px solid #000}
.r-totals{font-size:12px;margin-bottom:8px}
.r-totals table{width:100%}
.r-totals td{padding:2px 0}
.r-totals td:last-child{text-align:left;font-weight:700}
.r-grand td{font-size:14px;font-weight:700;border-top:1px solid #000;padding-top:4px}
.r-footer{text-align:center;border-top:1px dashed #000;padding-top:8px;font-size:10px;color:#555;line-height:1.6}
.r-barcode{text-align:center;letter-spacing:3px;font-size:15px;margin:6px 0}
.no-print{text-align:center;margin:16px;display:flex;gap:8px;justify-content:center}
.btn-p{padding:9px 20px;border:none;border-radius:7px;cursor:pointer;font-size:13px;font-weight:600}
.btn-primary{background:#3a7030;color:white}
.btn-ghost{background:#eee;color:#333}
@media print{
  body{margin:0}.no-print{display:none!important}
  @page{margin:0;size:80mm auto}
}
</style>
</head>
<body>
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
    <img src="<?= BASE_URL ?>/uploads/<?= Helper::e($settings['logo']) ?>" style="height:40px;margin-bottom:4px;display:block;margin:0 auto 4px"><br>
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
        <th style="text-align:center">الكمية</th>
        <th style="text-align:left">السعر</th>
        <th style="text-align:left">الإجمالي</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($items as $item): ?>
      <tr>
        <td><?= Helper::e($item['product_name']) ?></td>
        <td style="text-align:center"><?= number_format($item['quantity'],0) ?></td>
        <td style="text-align:left"><?= number_format($item['unit_price'],2) ?></td>
        <td style="text-align:left"><?= number_format($item['total'],2) ?></td>
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
    <div style="margin-top:4px"><?= date('d/m/Y H:i:s') ?></div>
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
