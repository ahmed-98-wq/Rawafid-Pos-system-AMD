<?php
/* =============================================
   صفحة قائمة المرتجعات
============================================= */
$currency = $settings['currency'] ?? 'ج.س';
$stMap    = ['returned'=>['مرتجع','badge-danger'],'paid'=>['مدفوع','badge-success']];
$pmNames  = ['cash'=>'نقدي','card'=>'بطاقة','transfer'=>'تحويل','credit'=>'آجل'];
?>

<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
          <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
          <path d="M9 14l-4 -4l4 -4" />
          <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
        </svg> المرتجعات</h1>
  <a href="?page=returns&action=search" class="btn btn-primary">
    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إرجاع فاتورة
  </a>
</div>

<!-- إحصائيات -->
<div style="display:grid;grid-template-columns:repeat(3,1fr);gap:12px;margin-bottom:16px">
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:42px;height:42px;border-radius:9px;background:var(--expired-bg,#feeaea);color:var(--expired-text,#b83232);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">عدد المرتجعات</div><div style="font-size:22px;font-weight:700;color:var(--text)"><?= number_format($stats['total_count'] ?? 0) ?></div></div>
  </div>
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:42px;height:42px;border-radius:9px;background:#feeaea;color:#b83232;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">قيمة المرتجعات</div><div style="font-size:22px;font-weight:700;color:#b83232"><?= number_format($stats['total_amount'] ?? 0, 2) ?></div><div style="font-size:11px;color:var(--text-3)"><?= $currency ?></div></div>
  </div>
  <div class="card" style="padding:14px;display:flex;align-items:center;gap:12px">
    <div style="width:42px;height:42px;border-radius:9px;background:#e6f0ff;color:#1a50a8;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
  <path d="M16 3v4" />
  <path d="M8 3v4" />
  <path d="M4 11h16" />
</svg></div>
    <div><div style="font-size:11px;color:var(--text-2)">فترة العرض</div><div style="font-size:13px;font-weight:600;color:var(--text)"><?= $from ?> → <?= $to ?></div></div>
  </div>
</div>

<!-- فلاتر -->
<div class="card filters-bar">
  <form method="GET" class="filters-form">
    <input type="hidden" name="page" value="returns">
    <div style="position:relative;flex:1;min-width:200px;display:flex;align-items:center">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg>
      <input type="text" name="search" value="<?= Helper::e($search ?? '') ?>" placeholder="بحث برقم المرتجع أو اسم العميل..." class="form-input" style="padding-right:32px;flex:1">
    </div>
    <div class="filter-group">
      <label style="font-size:12px;color:var(--text-2)">من</label>
      <input type="date" name="from" value="<?= $from ?>" class="form-input" style="width:140px">
    </div>
    <div class="filter-group">
      <label style="font-size:12px;color:var(--text-2)">إلى</label>
      <input type="date" name="to" value="<?= $to ?>" class="form-input" style="width:140px">
    </div>
    <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> بحث</button>
  </form>
</div>

<div class="card" style="padding:0; overflow:hidden">
  <div class="table-wrap" style="max-height: 500px; overflow-y: auto; position: relative;">
    <table class="data-table">
      <thead style="position: sticky; top: 0; z-index: 10; box-shadow: 0 2px 2px -1px rgba(0,0,0,0.1);">
        <tr>
          <th>رقم المرتجع</th>
          <th>الفاتورة الأصلية</th>
          <th>العميل</th>
          <th>المبلغ المُرجَع</th>
          <th>السبب</th>
          <th>الكاشير</th>
          <th>التاريخ</th>
          <th style="width:80px">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($returns)): ?>
        <tr><td colspan="8" class="empty-td">
          <div style="text-align:center;padding:32px;color:var(--text-3)">
            <i class="ti ti-arrow-back-up" style="font-size:42px;display:block;margin-bottom:10px;opacity:.3"></i>
            لا توجد مرتجعات في هذه الفترة
            <br>
            <a href="?page=returns&action=search" class="btn btn-primary btn-sm" style="margin-top:12px">
              <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إرجاع فاتورة
            </a>
          </div>
        </td></tr>
        <?php else: ?>
        <?php foreach ($returns as $r):
          // استخراج رقم الفاتورة الأصلية من الملاحظات
          preg_match('/مرتجع من: ([^\s|]+)/', $r['notes'] ?? '', $m);
          $originalNum = $m[1] ?? '—';
          // استخراج السبب
          preg_match('/السبب: (.+)$/', $r['notes'] ?? '', $sm);
          $reason = trim($sm[1] ?? '');
        ?>
        <tr>
          <td class="fw-bold" style="color:#b83232"><?= Helper::e($r['invoice_number']) ?></td>
          <td>
            <span style="font-size:12px;background:var(--bg);border:1px solid var(--border);padding:2px 8px;border-radius:6px;font-family:monospace">
              <?= Helper::e($originalNum) ?>
            </span>
          </td>
          <td style="font-size:12px;color:var(--text-2)"><?= Helper::e($r['customer_name'] ?? 'نقدي') ?></td>
          <td class="fw-bold" style="color:#b83232">
            <?= number_format($r['total'], 2) ?> <small style="color:var(--text-3)"><?= $currency ?></small>
          </td>
          <td style="font-size:12px;color:var(--text-3);max-width:150px">
            <?= $reason ? Helper::e(mb_substr($reason, 0, 40)) . (mb_strlen($reason) > 40 ? '...' : '') : '—' ?>
          </td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::e($r['cashier_name'] ?? '') ?></td>
          <td style="font-size:12px;color:var(--text-3)"><?= Helper::formatDate($r['created_at'], 'd/m/Y H:i') ?></td>
          <td>
            <div class="action-btns">
              <a href="?page=returns&action=printReturn&id=<?= $r['id'] ?>" target="_blank"
                 class="action-btn view" title="طباعة إيصال المرتجع">
                <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
  <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
  <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
</svg>
              </a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <?php if (($totalPages ?? 1) > 1): ?>
  <div class="pagination" style="padding:12px 16px;border-top:1px solid var(--border)">
    <span style="color:var(--text-3);font-size:12px;margin-left:auto">
      عرض <?= (($pageNum-1)*$perPage)+1 ?>–<?= min($pageNum*$perPage, $total) ?> من <?= $total ?>
    </span>
    <?php for ($i=1; $i<=$totalPages; $i++): ?>
    <a href="?page=returns&p=<?= $i ?>&from=<?= $from ?>&to=<?= $to ?>&search=<?= urlencode($search??'') ?>"
       class="page-btn <?= ($pageNum??1)===$i?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
