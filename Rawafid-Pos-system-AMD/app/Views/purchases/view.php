<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>فاتورة شراء - <?= Helper::e($purchase['purchase_number']) ?></title>
<link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/app.css">
<style>
body{background:var(--bg);padding:20px}
.pv-wrap{max-width:700px;margin:0 auto;background:var(--surface);border-radius:12px;border:1px solid var(--border);overflow:hidden}
.pv-header{background:linear-gradient(135deg,var(--olive-600),var(--olive-400));padding:24px;color:white;display:flex;justify-content:space-between;align-items:flex-start}
[data-theme="dark"] .pv-header{background:linear-gradient(135deg,#1a3460,#2563eb)}
.pv-title{font-size:20px;font-weight:700}
.pv-num{font-size:13px;opacity:.8;margin-top:4px}
.pv-meta{background:var(--surface-2,var(--bg));padding:16px 20px;display:grid;grid-template-columns:repeat(3,1fr);gap:12px;border-bottom:1px solid var(--border)}
.pv-meta-item label{font-size:11px;color:var(--text-3);display:block;margin-bottom:3px}
.pv-meta-item strong{font-size:13px;color:var(--text)}
.pv-table-wrap{padding:16px 20px}
.pv-items{width:100%;border-collapse:collapse;font-size:13px}
.pv-items th{background:var(--bg);color:var(--text-2);font-size:12px;padding:8px 10px;text-align:right;border-bottom:1px solid var(--border)}
.pv-items td{padding:9px 10px;border-bottom:1px solid var(--border-2,rgba(0,0,0,.05));color:var(--text);vertical-align:middle}
.pv-items tr:last-child td{border-bottom:none}
.pv-totals{background:var(--bg);border-radius:8px;padding:14px 16px;margin:0 20px 20px;display:flex;flex-direction:column;gap:5px}
.pv-tot-row{display:flex;justify-content:space-between;font-size:13px;color:var(--text-2)}
.pv-tot-row.final{font-size:16px;font-weight:700;color:var(--text);border-top:1px solid var(--border);padding-top:8px;margin-top:3px}
.no-print{padding:16px 20px;border-top:1px solid var(--border);display:flex;gap:10px;justify-content:flex-end;background:var(--surface)}
@media print{.no-print{display:none!important} body{padding:0} .pv-wrap{box-shadow:none;border:none} @page{margin:10mm}}
</style>
</head>
<body>
<?php
$currency = $settings['currency'] ?? 'ج.س';
$stMap = ['paid'=>['مدفوع','#3a7030'],'pending'=>['معلّق','#b87000'],'partial'=>['جزئي','#1a50a8']];
$st = $stMap[$purchase['status']] ?? ['—','var(--text-3)'];
$pmMap = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
?>
<div class="pv-wrap">
  <!-- Header -->
  <div class="pv-header">
    <div>
      <div class="pv-title">فاتورة شراء</div>
      <div class="pv-num"><?= Helper::e($purchase['purchase_number']) ?></div>
    </div>
    <div style="text-align:left">
      <div style="font-size:13px;opacity:.85"><?= Helper::e($settings['business_name'] ?? '') ?></div>
      <div style="font-size:12px;opacity:.7;margin-top:3px"><?= Helper::formatDate($purchase['created_at'], 'd/m/Y H:i') ?></div>
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
      <strong style="color:<?= $st[1] ?>"><?= $st[0] ?></strong>
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
          <td style="color:var(--text-3);font-size:11px"><?= $i+1 ?></td>
          <td style="font-weight:600"><?= Helper::e($item['product_name']) ?></td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::e($item['unit'] ?? 'قطعة') ?></td>
          <td><?= number_format($item['quantity'], 2) ?></td>
          <td><?= number_format($item['unit_price'], 2) ?> <?= $currency ?></td>
          <td style="font-weight:700"><?= number_format($item['total'], 2) ?> <?= $currency ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <!-- Totals -->
  <div class="pv-totals">
    <div class="pv-tot-row"><span>الإجمالي</span><span><?= number_format($purchase['total'],2) ?> <?= $currency ?></span></div>
    <div class="pv-tot-row"><span>المدفوع</span><span style="color:#3a7030"><?= number_format($purchase['paid_amount'],2) ?> <?= $currency ?></span></div>
    <?php if($purchase['total'] - $purchase['paid_amount'] > 0): ?>
    <div class="pv-tot-row" style="color:#b83232"><span>المتبقي</span><span><?= number_format($purchase['total']-$purchase['paid_amount'],2) ?> <?= $currency ?></span></div>
    <?php endif; ?>
    <div class="pv-tot-row final"><span>الإجمالي الكلي</span><span><?= number_format($purchase['total'],2) ?> <?= $currency ?></span></div>
  </div>

  <?php if($purchase['notes']): ?>
  <div style="padding:0 20px 16px;font-size:12px;color:var(--text-2)">
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
