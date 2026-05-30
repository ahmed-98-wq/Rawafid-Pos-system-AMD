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
<div class="u-style-92">
  <div class="card u-style-93">
    <div class="u-style-124"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14l-4 -4l4 -4" />
  <path d="M5 10h11a4 4 0 0 1 0 8h-1" />
</svg></div>
    <div><div class="u-style-95">عدد المرتجعات</div><div class="u-style-125"><?= number_format($stats['total_count'] ?? 0) ?></div></div>
  </div>
  <div class="card u-style-93">
    <div class="u-style-126"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-currency-dollar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M16.7 8a3 3 0 0 0 -2.7 -2h-4a3 3 0 0 0 0 6h4a3 3 0 0 1 0 6h-4a3 3 0 0 1 -2.7 -2" />
  <path d="M12 3v3m0 12v3" />
</svg></div>
    <div><div class="u-style-95">قيمة المرتجعات</div><div class="u-style-127"><?= number_format($stats['total_amount'] ?? 0, 2) ?></div><div class="u-style-4"><?= $currency ?></div></div>
  </div>
  <div class="card u-style-93">
    <div class="u-style-128"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-calendar" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7a2 2 0 0 1 2 -2h12a2 2 0 0 1 2 2v12a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12z" />
  <path d="M16 3v4" />
  <path d="M8 3v4" />
  <path d="M4 11h16" />
</svg></div>
    <div><div class="u-style-95">فترة العرض</div><div class="u-style-129"><?= $from ?> → <?= $to ?></div></div>
  </div>
</div>

<!-- فلاتر -->
<div class="card filters-bar">
  <form method="GET" class="filters-form">
    <input type="hidden" name="page" value="returns">
    <div class="u-style-130">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg>
      <input type="text" name="search" value="<?= Helper::e($search ?? '') ?>" placeholder="بحث برقم المرتجع أو اسم العميل..." class="form-input" >
    </div>
    <div class="filter-group">
      <label class="cell-soft">من</label>
      <input type="date" name="from" value="<?= $from ?>" class="form-input w-140">
    </div>
    <div class="filter-group">
      <label class="cell-soft">إلى</label>
      <input type="date" name="to" value="<?= $to ?>" class="form-input w-140">
    </div>
    <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="16" height="16" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg> بحث</button>
  </form>
</div>

<div class="card card-flush">
  <div class="table-wrap table-scroll">
    <table class="data-table">
      <thead class="sticky-head">
        <tr>
          <th>رقم المرتجع</th>
          <th>الفاتورة الأصلية</th>
          <th>العميل</th>
          <th>المبلغ المُرجَع</th>
          <th>السبب</th>
          <th>الكاشير</th>
          <th>التاريخ</th>
          <th class="w-80">إجراءات</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($returns)): ?>
        <tr><td colspan="8" class="empty-td">
          <div class="empty-td">
            <i class="ti ti-arrow-back-up u-style-131"></i>
            لا توجد مرتجعات في هذه الفترة
            <br>
            <a href="?page=returns&action=search" class="btn btn-primary btn-sm mt-3">
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
          <td class="fw-bold text-danger"><?= Helper::e($r['invoice_number']) ?></td>
          <td>
            <span class="u-style-132">
              <?= Helper::e($originalNum) ?>
            </span>
          </td>
          <td class="cell-soft"><?= Helper::e($r['customer_name'] ?? 'نقدي') ?></td>
          <td class="fw-bold text-danger">
            <?= number_format($r['total'], 2) ?> <small class="text-muted"><?= $currency ?></small>
          </td>
          <td class="u-style-133">
            <?= $reason ? Helper::e(mb_substr($reason, 0, 40)) . (mb_strlen($reason) > 40 ? '...' : '') : '—' ?>
          </td>
          <td class="cell-muted"><?= Helper::e($r['cashier_name'] ?? '') ?></td>
          <td class="cell-muted"><?= Helper::formatDate($r['created_at'], 'd/m/Y H:i') ?></td>
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
  <div class="pagination u-style-89">
    <span class="u-style-9">
      عرض <?= (($pageNum-1)*$perPage)+1 ?>–<?= min($pageNum*$perPage, $total) ?> من <?= $total ?>
    </span>
    <?php for ($i=1; $i<=$totalPages; $i++): ?>
    <a href="?page=returns&p=<?= $i ?>&from=<?= $from ?>&to=<?= $to ?>&search=<?= urlencode($search??'') ?>"
       class="page-btn <?= ($pageNum??1)===$i?'active':'' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
  <?php endif; ?>
</div>
