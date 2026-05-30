<?php
/* ===========================================
   صفحة إدارة العملاء
=========================================== */
$currency = $settings['currency'] ?? 'ج.س';
$db       = Database::getInstance();
$search   = Helper::sanitize($_GET['search'] ?? '');
$filter   = $_GET['filter'] ?? 'all';
$page_num = max(1, (int)($_GET['p'] ?? 1));
$perPage  = 15;
$offset   = ($page_num - 1) * $perPage;

$where  = "WHERE 1=1"; $params = [];
if ($search) {
    $where   .= " AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)";
    $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%";
}
if ($filter === 'debt') { $where .= " AND c.balance > 0"; }

$total     = $db->fetchColumn("SELECT COUNT(*) FROM customers c $where", $params);
$customers = $db->fetchAll(
    "SELECT c.*,
            COALESCE((SELECT SUM(i.total) FROM invoices i WHERE i.customer_id=c.id AND i.type='sale' AND i.status!='returned'),0) as total_purchases,
            COALESCE((SELECT COUNT(*) FROM invoices i WHERE i.customer_id=c.id AND i.type='sale'),0) as invoice_count,
            (SELECT MAX(i.created_at) FROM invoices i WHERE i.customer_id=c.id) as last_invoice
     FROM customers c $where ORDER BY c.created_at DESC LIMIT $perPage OFFSET $offset",
    $params
);
$totalPages  = (int)ceil($total / $perPage);
$globalStats = $db->fetchOne(
    "SELECT COUNT(*) as total_customers, COALESCE(SUM(balance),0) as total_debt,
            COUNT(CASE WHEN balance > 0 THEN 1 END) as debtors_count
     FROM customers"
);
?>

<!-- Header -->
<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
</svg> إدارة العملاء</h1>
  <div class="page-actions">
    <button class="btn btn-outline" onclick="exportCustomers()"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-download" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 17v2a2 2 0 0 0 2 2h12a2 2 0 0 0 2 -2v-2" />
  <path d="M7 11l5 5l5 -5" />
  <path d="M12 4l0 12" />
</svg> تصدير</button>
    <button class="btn btn-primary" onclick="openCustomerModal()"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
  <path d="M16 19h6" />
  <path d="M19 16v6" />
  <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
</svg> إضافة عميل</button>
  </div>
</div>

<!-- Stats -->
<div class="c-stats">
  <div class="c-stat blue"><div class="cs-ico"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-users" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7m-4 0a4 4 0 1 0 8 0a4 4 0 1 0 -8 0" />
  <path d="M3 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
  <path d="M16 3.13a4 4 0 0 1 0 7.75" />
  <path d="M21 21v-2a4 4 0 0 0 -3 -3.85" />
</svg></div>
    <div><div class="cs-v"><?= number_format($globalStats['total_customers']) ?></div><div class="cs-l">إجمالي العملاء</div></div></div>
  <div class="c-stat red"><div class="cs-ico"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M20.866 10.45a9 9 0 1 0 -7.815 10.488" />
  <path d="M12 7v5l1.5 1.5" />
  <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
  <path d="M19 21v1m0 -8v1" />
</svg></div>
    <div><div class="cs-v"><?= number_format($globalStats['total_debt'],2) ?></div><div class="cs-l">إجمالي الديون (<?= $currency ?>)</div></div></div>
  <div class="c-stat orange"><div class="cs-ico"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 8v4" />
  <path d="M12 16h.01" />
</svg></div>
    <div><div class="cs-v"><?= number_format($globalStats['debtors_count']) ?></div><div class="cs-l">عملاء لديهم ديون</div></div></div>
</div>

<!-- Filters -->
<div class="card filters-bar">
  <form method="GET" class="filters-form" id="filter-form">
    <input type="hidden" name="page" value="customers">
    <div class="filter-tabs">
      <a href="?page=customers&filter=all<?= $search?"&search=".urlencode($search):"" ?>" class="filter-tab <?= $filter==='all'?'active':'' ?>">الكل <span class="tab-count"><?= $globalStats['total_customers'] ?></span></a>
      <a href="?page=customers&filter=debt<?= $search?"&search=".urlencode($search):"" ?>" class="filter-tab red <?= $filter==='debt'?'active':'' ?>">لديهم ديون <span class="tab-count"><?= $globalStats['debtors_count'] ?></span></a>
    </div>
    <div class="s-wrap">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg>
      <input type="text" name="search" value="<?= Helper::e($search) ?>" placeholder="بحث بالاسم أو الهاتف..." class="form-input si" oninput="dbSearch(this)">
      <?php if($search): ?><a href="?page=customers&filter=<?=$filter?>" class="s-clear"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></a><?php endif; ?>
    </div>
  </form>
</div>

<!-- Table -->
<div class="card card-flush">
  <div class="table-wrap">
    <table class="data-table" id="ct">
      <thead><tr>
        <th>العميل</th><th>الهاتف</th><th>المشتريات</th><th>الفواتير</th><th>الرصيد</th><th>آخر فاتورة</th><th class="u-style-1">إجراءات</th>
      </tr></thead>
      <tbody>
      <?php if(empty($customers)): ?>
        <tr><td colspan="7" class="empty-td">
          <div class="empty-medium">
            <i class="ti ti-users-off u-style-2"></i>
            <?= $search ? "لا نتائج لـ «".Helper::e($search)."»" : "لا يوجد عملاء بعد" ?>
            <?php if(!$search): ?><br><button class="btn btn-primary btn-sm mt-3" onclick="openCustomerModal()"><i class="ti ti-plus"></i> أضف عميل</button><?php endif; ?>
          </div>
        </td></tr>
      <?php else: foreach($customers as $c): ?>
        <tr>
          <td>
            <div class="modal-title-group">
              <div class="c-avatar <?= cColorClass($c['name']) ?>"><?= mb_strtoupper(mb_substr($c['name'],0,1)) ?></div>
              <div>
                <div class="u-style-3"><?= Helper::e($c['name']) ?></div>
                <?php if($c['address']): ?><div class="u-style-4"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
  <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
</svg> <?= Helper::e(mb_substr($c['address'],0,28)) ?><?= mb_strlen($c['address'])>28?'...':'' ?></div><?php endif; ?>
              </div>
            </div>
          </td>
          <td><?= $c['phone'] ? '<a href="tel:'.Helper::e($c['phone']).'" class="u-style-5"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
</svg> '.Helper::e($c['phone']).'</a>' : '<span class="text-muted">—</span>' ?></td>
          <td class="fw-bold"><?= number_format($c['total_purchases'],2) ?> <small class="text-muted"><?= $currency ?></small></td>
          <td class="text-center"><span class="u-style-6"><?= $c['invoice_count'] ?></span></td>
          <td>
            <?php if($c['balance']>0): ?>
              <div class="u-style-7">
                <span class="badge badge-danger"><?= number_format($c['balance'],2) ?> <?= $currency ?></span>
                <button onclick="openPayModal(<?=$c['id']?>,'<?=Helper::e($c['name'])?>',<?=$c['balance']?>)" class="icon-btn text-success" title="تسجيل دفعة"><i class="ti ti-cash"></i></button>
              </div>
            <?php else: ?>
              <span class="badge badge-success"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg> مسدّد</span>
            <?php endif; ?>
          </td>
          <td class="cell-muted"><?= $c['last_invoice'] ? Helper::formatDate($c['last_invoice'],'d/m/Y') : '—' ?></td>
          <td>
            <div class="action-btns">
              <button onclick="openStatement(<?=$c['id']?>)" class="action-btn view" title="كشف الحساب"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-file-invoice" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M14 3v4a1 1 0 0 0 1 1h4" />
  <path d="M17 21h-10a2 2 0 0 1 -2 -2v-14a2 2 0 0 1 2 -2h7l5 5v11a2 2 0 0 1 -2 2z" />
  <path d="M9 7l1 0" />
  <path d="M9 13l6 0" />
  <path d="M13 17l2 0" />
</svg></button>
              <button onclick="openCustomerModal(<?=$c['id']?>)" class="action-btn edit" title="تعديل"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
  <path d="M16 5l3 3" />
</svg></button>
              <button onclick="delCustomer(<?=$c['id']?>,'<?=Helper::e($c['name'])?>',<?=$c['balance']?>)" class="action-btn delete" title="حذف"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7l16 0" />
  <path d="M10 11l0 6" />
  <path d="M14 11l0 6" />
  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
</svg></button>
            </div>
          </td>
        </tr>
      <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
  <?php if($totalPages>1): ?>
  <div class="pagination u-style-8">
    <span class="u-style-9">عرض <?=($offset+1)?>–<?=min($offset+$perPage,$total)?> من <?=$total?></span>
    <?php for($i=1;$i<=$totalPages;$i++): ?><a href="?page=customers&p=<?=$i?>&filter=<?=$filter?>&search=<?=urlencode($search)?>" class="page-btn <?=$page_num===$i?'active':''?>"><?=$i?></a><?php endfor; ?>
  </div>
  <?php endif; ?>
</div>

<!-- ===================== MODAL: إضافة / تعديل ===================== -->
<div class="modal-overlay is-hidden" id="cust-modal">
  <div class="modal-box cm-box">
    <div class="modal-header">
      <div class="flex items-center gap-3">
        <div class="cm-ico" id="cm-ico">👤</div>
        <div><h3 id="cm-title">إضافة عميل جديد</h3><p id="cm-sub" class="u-style-10">أدخل بيانات العميل</p></div>
      </div>
      <button onclick="closeCustModal()" class="icon-btn"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button>
    </div>

    <form method="POST" action="?page=customers&action=store" id="cust-form" novalidate>
      <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
      <input type="hidden" name="id" id="c-id">

      <!-- Tabs -->
      <div class="cm-tabs">
        <button type="button" class="cm-tab active" onclick="cTab('basic',this)"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
  <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
</svg> الأساسية</button>
        <button type="button" class="cm-tab" onclick="cTab('finance',this)"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-wallet" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M17 8v-3a1 1 0 0 0 -1 -1h-10a2 2 0 0 0 0 4h12a1 1 0 0 1 1 1v3m0 4v3a1 1 0 0 1 -1 1h-12a2 2 0 0 1 -2 -2v-12" />
  <path d="M20 12v4h-4a2 2 0 0 1 0 -4h4z" />
</svg> المالية</button>
        <button type="button" class="cm-tab" onclick="cTab('notes',this)"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-notes" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M9 15l4 0" />
</svg> ملاحظات</button>
      </div>

      <!-- Tab: Basic -->
      <div class="cm-panel active" id="t-basic">
        <div class="form-group">
          <label class="form-label required"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 7a4 4 0 1 0 0 8a4 4 0 0 0 0 -8z" />
  <path d="M6 21v-2a4 4 0 0 1 4 -4h4a4 4 0 0 1 4 4v2" />
</svg> اسم العميل</label>
          <input type="text" name="name" id="c-name" class="form-input u-style-11" placeholder="الاسم الكامل" required oninput="updateCmIco(this.value)">
          <div id="e-name" class="u-style-12"></div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-phone" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 4h4l2 5l-2.5 1.5a11 11 0 0 0 5 5l1.5 -2.5l5 2v4a2 2 0 0 1 -2 2a16 16 0 0 1 -15 -15a2 2 0 0 1 2 -2" />
</svg> الهاتف</label>
            <input type="tel" name="phone" id="c-phone" class="form-input" placeholder="09XXXXXXXX">
          </div>
          <div class="form-group">
            <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-mail" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 7a2 2 0 0 1 2 -2h14a2 2 0 0 1 2 2v10a2 2 0 0 1 -2 2h-14a2 2 0 0 1 -2 -2v-10z" />
  <path d="M3 7l9 6l9 -6" />
</svg> البريد</label>
            <input type="email" name="email" id="c-email" class="form-input" placeholder="email@example.com" dir="ltr">
          </div>
        </div>
        <div class="form-group">
          <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-map-pin" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 11a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
  <path d="M17.657 16.657l-4.243 4.243a2 2 0 0 1 -2.827 0l-4.244 -4.243a8 8 0 1 1 11.314 0z" />
</svg> العنوان</label>
          <input type="text" name="address" id="c-address" class="form-input" placeholder="المدينة، الحي، الشارع">
        </div>
      </div>

      <!-- Tab: Finance -->
      <div class="cm-panel" id="t-finance">
        <!-- بيانات مالية عند التعديل -->
        <div id="fin-info" class="u-style-13">
          <div class="u-style-14"><span>إجمالي المشتريات</span><strong id="fi-total">—</strong></div>
          <div class="u-style-14"><span>عدد الفواتير</span><strong id="fi-count">—</strong></div>
          <div class="u-style-15"><span>الرصيد المستحق</span><strong id="fi-bal" class="u-style-16">—</strong></div>
        </div>

        <div class="form-group">
          <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-credit-card" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
  <path d="M3 10l18 0" />
  <path d="M7 15l2 0" />
  <path d="M11 15l3 0" />
</svg> حد الائتمان (<?= $currency ?>)</label>
          <input type="number" name="credit_limit" id="c-credit" class="form-input" min="0" step="0.01" placeholder="0 = غير محدود">
          <small class="form-hint">الحد الأقصى للبيع بالآجل. 0 = غير محدود</small>
        </div>

        <!-- عند التعديل: تسوية رصيد -->
        <div id="adj-section" class="is-hidden">
          <div class="u-style-17">
            <div class="u-style-18">الرصيد الحالي</div>
            <div id="cur-bal-disp" class="u-style-19">—</div>
          </div>
          <label class="u-style-20">
            <input type="checkbox" name="adjust_balance" id="adj-cb" onchange="toggleAdj(this.checked)" class="check-accent">
            تعديل الرصيد يدوياً
          </label>
          <div id="adj-inputs" class="is-hidden">
            <div class="form-group">
              <label class="form-label">الرصيد الجديد (<?= $currency ?>)</label>
              <input type="number" name="new_balance" id="c-newbal" class="form-input" min="0" step="0.01" placeholder="0.00">
              <small class="form-hint u-style-21">⚠️ سيتم تعديل الرصيد مباشرة</small>
            </div>
          </div>
        </div>

        <!-- عند الإضافة: رصيد افتتاحي -->
        <div id="opening-section">
          <div class="form-group">
            <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-coins" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 14c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" />
  <path d="M9 14v4c0 1.656 2.686 3 6 3s6 -1.344 6 -3v-4" />
  <path d="M3 6c0 1.657 2.686 3 6 3s6 -1.343 6 -3s-2.686 -3 -6 -3s-6 1.343 -6 3z" />
  <path d="M3 6v10c0 1.657 2.686 3 6 3c.469 0 .926 -.027 1.36 -.078" />
  <path d="M15 10v4c0 .542 -.29 1.05 -.792 1.455" />
</svg> رصيد افتتاحي (<?= $currency ?>)</label>
            <input type="number" name="opening_balance" id="c-opening" class="form-input" min="0" step="0.01" placeholder="0.00">
            <small class="form-hint">رصيد مدين قديم قبل استخدام النظام</small>
          </div>
        </div>
      </div>

      <!-- Tab: Notes -->
      <div class="cm-panel" id="t-notes">
        <div class="form-group">
          <label class="form-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-notes" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 3m0 2a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v14a2 2 0 0 1 -2 2h-10a2 2 0 0 1 -2 -2z" />
  <path d="M9 7l6 0" />
  <path d="M9 11l6 0" />
  <path d="M9 15l4 0" />
</svg> ملاحظات</label>
          <textarea name="notes" id="c-notes" class="form-textarea" rows="6" placeholder="شروط خاصة، تفضيلات، أولويات..."></textarea>
        </div>
        <div class="u-style-22">
          <span class="u-style-4">إضافة سريعة:</span>
          <?php foreach(['عميل VIP','لا يُباع بالآجل','يطلب فاتورة رسمية','توصيل للمنزل','خصم خاص'] as $n): ?>
          <button type="button" onclick="addNote('<?= $n ?>')" class="quick-chip"><?= $n ?></button>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Footer -->
      <div class="u-style-23">
        <button type="button" onclick="closeCustModal()" class="btn btn-ghost"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> إلغاء</button>
        <button type="submit" class="btn btn-primary" id="c-submit"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
  <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 4l0 4l-6 0l0 -4" />
</svg> <span id="c-submit-lbl">حفظ العميل</span></button>
      </div>
    </form>
  </div>
</div>

<!-- ===================== MODAL: دفعة ===================== -->
<div class="modal-overlay is-hidden" id="pay-modal">
  <div class="modal-box u-style-24">
    <div class="modal-header">
      <div class="modal-title-group">
        <div class="cm-ico">💳</div>
        <div><h3>تسجيل دفعة</h3><p id="pm-name" class="cell-muted">—</p></div>
      </div>
      <button onclick="closePayModal()" class="icon-btn"><i class="ti ti-x"></i></button>
    </div>
    <form method="POST" action="?page=customers&action=payment">
      <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
      <input type="hidden" name="customer_id" id="pm-id">
      <div class="u-style-25">
        <div class="u-style-26"><span>الرصيد المستحق</span><strong id="pm-debt" class="u-style-16">—</strong></div>
      </div>
      <div class="form-group">
        <label class="form-label required">مبلغ الدفعة (<?= $currency ?>)</label>
        <input type="number" name="payment_amount" id="pm-amount" class="form-input u-style-27" step="0.01" min="0.01" required placeholder="0.00" oninput="calcRem()">
      </div>
      <div id="rem-box" class="u-style-28">
        <div class="u-style-26"><span>المتبقي بعد الدفعة</span><strong id="rem-val">—</strong></div>
      </div>
      <div class="flex gap-2 mb-3">
        <button type="button" onclick="setFull()" class="u-style-29">دفع الكل</button>
        <button type="button" onclick="setHalf()" class="u-style-29">نصف المبلغ</button>
      </div>
      <div class="u-style-30">
        <button type="button" onclick="closePayModal()" class="btn btn-ghost">إلغاء</button>
        <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 10a3 3 0 1 0 6 0a3 3 0 0 0 -6 0" />
  <path d="M21 11v-3a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v8a2 2 0 0 0 2 2h4" />
  <path d="M19 12v6" />
  <path d="M16 15h6" />
</svg> تأكيد الدفعة</button>
      </div>
    </form>
  </div>
</div>

<!-- Styles -->
<script>
const CUR = '<?= $currency ?>';
let debt = 0;

function cColor(n) {
  const c=['var(--color-hex-4e6b2b)','var(--color-hex-185fa5)','var(--color-hex-854f0b)','var(--color-hex-534ab7)','var(--color-hex-1a6b6b)','var(--color-hex-6b1a1a)'];
  let h=0; for(let i=0;i<(n||'').length;i++) h=(n.charCodeAt(i)+((h<<5)-h));
  return c[Math.abs(h)%c.length];
}

let st;
function dbSearch(el) { clearTimeout(st); st=setTimeout(()=>document.getElementById('filter-form').submit(),600); }

async function openCustomerModal(id=null) {
  resetForm();
  if (id) {
    document.getElementById('cm-title').textContent    = 'تعديل بيانات العميل';
    document.getElementById('cm-sub').textContent      = 'تعديل بيانات عميل موجود';
    document.getElementById('c-submit-lbl').textContent = 'حفظ التعديلات';
    document.getElementById('opening-section').style.display = 'none';
    document.getElementById('adj-section').style.display    = 'block';
    try {
      const r = await fetch('?page=customers&action=getOne&id='+id);
      const d = await r.json();
      const c = d.customer; if(!c) return;
      document.getElementById('c-id').value      = c.id;
      document.getElementById('c-name').value    = c.name||'';
      document.getElementById('c-phone').value   = c.phone||'';
      document.getElementById('c-email').value   = c.email||'';
      document.getElementById('c-address').value = c.address||'';
      document.getElementById('c-credit').value  = c.credit_limit||0;
      document.getElementById('c-notes').value   = c.notes||'';
      document.getElementById('c-newbal').value  = parseFloat(c.balance||0).toFixed(2);
      updateCmIco(c.name);
      const bal = parseFloat(c.balance||0);
      document.getElementById('cur-bal-disp').textContent = bal.toFixed(2)+' '+CUR;
      document.getElementById('cur-bal-disp').style.color = bal>0?'var(--danger)':'var(--success)';
      document.getElementById('fin-info').style.display = 'flex';
      document.getElementById('fin-info').style.flexDirection = 'column';
      document.getElementById('fi-bal').textContent = bal.toFixed(2)+' '+CUR;
      document.getElementById('fi-bal').style.color = bal>0?'var(--danger)':'var(--success)';
    } catch(e){}
  } else {
    document.getElementById('opening-section').style.display = 'block';
    document.getElementById('adj-section').style.display    = 'none';
    document.getElementById('fin-info').style.display       = 'none';
  }
  document.getElementById('cust-modal').style.display = 'flex';
  setTimeout(()=>document.getElementById('c-name').focus(),150);
}

function closeCustModal() { document.getElementById('cust-modal').style.display='none'; }

function resetForm() {
  document.getElementById('cm-title').textContent    = 'إضافة عميل جديد';
  document.getElementById('cm-sub').textContent      = 'أدخل بيانات العميل';
  document.getElementById('c-submit-lbl').textContent = 'حفظ العميل';
  document.getElementById('c-id').value='';
  ['c-name','c-phone','c-email','c-address','c-notes'].forEach(i=>{const e=document.getElementById(i);if(e)e.value='';});
  document.getElementById('c-credit').value=''; document.getElementById('c-opening').value='';
  document.getElementById('cm-ico').textContent='👤';
  document.getElementById('adj-cb').checked=false;
  document.getElementById('adj-inputs').style.display='none';
  cTab('basic',document.querySelector('.cm-tab'));
}

function updateCmIco(v) {
  const el=document.getElementById('cm-ico');
  el.textContent = v ? v.charAt(0).toUpperCase() : '👤';
}

function cTab(id,btn) {
  document.querySelectorAll('.cm-panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.cm-tab').forEach(b=>b.classList.remove('active'));
  document.getElementById('t-'+id).classList.add('active');
  if(btn) btn.classList.add('active');
}

function toggleAdj(on) { document.getElementById('adj-inputs').style.display=on?'block':'none'; }
function addNote(t) { const el=document.getElementById('c-notes'); el.value=el.value?el.value+'\n'+t:t; }

function openPayModal(id,name,d) {
  debt=parseFloat(d)||0;
  document.getElementById('pm-id').value=id;
  document.getElementById('pm-name').textContent=name;
  document.getElementById('pm-debt').textContent=debt.toFixed(2)+' '+CUR;
  document.getElementById('pm-amount').value='';
  document.getElementById('rem-box').style.display='none';
  document.getElementById('pay-modal').style.display='flex';
  setTimeout(()=>document.getElementById('pm-amount').focus(),150);
}
function closePayModal() { document.getElementById('pay-modal').style.display='none'; }
function calcRem() {
  const p=parseFloat(document.getElementById('pm-amount').value)||0;
  const r=Math.max(0,debt-p);
  document.getElementById('rem-val').textContent=r.toFixed(2)+' '+CUR;
  document.getElementById('rem-val').style.color=r===0?'var(--success)':(r<debt?'var(--warning)':'var(--danger)');
  document.getElementById('rem-box').style.display=p>0?'block':'none';
}
function setFull() { document.getElementById('pm-amount').value=debt.toFixed(2); calcRem(); }
function setHalf() { document.getElementById('pm-amount').value=(debt/2).toFixed(2); calcRem(); }

function openStatement(id) { window.open('?page=customers&action=statement&id='+id,'_blank'); }

function delCustomer(id,name,balance) {
  if(parseFloat(balance)>0){ alert('⚠️ لا يمكن حذف "'+name+'" لأن لديه رصيداً مستحقاً.'); return; }
  if(confirm('حذف العميل "'+name+'"؟ لا يمكن التراجع.')) { window.location.href='?page=customers&action=delete&id='+id; }
}

function exportCustomers() {
  const rows=document.querySelectorAll('#ct tbody tr');
  const heads=document.querySelectorAll('#ct thead th');
  const bom='\uFEFF';
  const h=Array.from(heads).map(e=>'"'+e.innerText.trim()+'"').join(',');
  const b=Array.from(rows).map(r=>Array.from(r.querySelectorAll('td')).map(td=>'"'+td.innerText.trim().replace(/"/g,'""')+'"').join(',')).join('\n');
  const blob=new Blob([bom+h+'\n'+b],{type:'text/csv;charset=utf-8;'});
  const a=document.createElement('a'); a.href=URL.createObjectURL(blob); a.download='customers_<?= date("Y-m-d") ?>.csv'; a.click();
}

document.getElementById('cust-form').addEventListener('submit',function(e){
  const n=document.getElementById('c-name').value.trim();
  const er=document.getElementById('e-name');
  if(!n){ e.preventDefault(); er.style.display='block'; er.textContent='يرجى إدخال اسم العميل'; cTab('basic',document.querySelector('.cm-tab')); document.getElementById('c-name').focus(); }
  else { er.style.display='none'; }
});
</script>

<?php
// Helper function for avatar color
function cColorClass(string $name): string {
    $hash = 0;
    for ($i = 0; $i < mb_strlen($name); $i++) {
        $hash = mb_ord(mb_substr($name, $i, 1)) + (($hash << 5) - $hash);
    }
    return 'avatar-color-' . (abs($hash) % 7);
}
?>
