<?php
$currency = $settings['currency'] ?? 'ج.س';
$db = Database::getInstance();
$search = Helper::sanitize($_GET['search'] ?? '');
$where = "WHERE 1=1"; $params = [];
if ($search) { $where .= " AND (name LIKE ? OR phone LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$suppliers = $db->fetchAll("SELECT * FROM suppliers $where ORDER BY created_at DESC", $params);
?>
<div class="page-header">
  <h1 class="page-title"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-truck" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M17 17m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M5 17h-2v-11a1 1 0 0 1 1 -1h9v12m-4 0h6m4 0h1v-6h-4l-3 -5h-3v7" />
</svg> الموردون</h1>
  <button class="btn btn-primary" onclick="document.getElementById('supplier-modal').style.display='flex'"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> إضافة مورد</button>
</div>
<div class="card filters-bar">
  <form method="GET" class="filters-form">
    <input type="hidden" name="page" value="suppliers">
    <input type="text" name="search" value="<?= Helper::e($search) ?>" placeholder="🔍 بحث..." class="form-input">
    <button type="submit" class="btn btn-outline"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg></button>
  </form>
</div>
<div class="card">
  <div class="table-wrap">
    <table class="data-table">
      <thead><tr><th>#</th><th>اسم المورد</th><th>الهاتف</th><th>البريد</th><th>الرصيد المستحق</th><th>إجراءات</th></tr></thead>
      <tbody>
        <?php if(empty($suppliers)): ?>
        <tr><td colspan="6" class="empty-td">لا يوجد موردون</td></tr>
        <?php else: foreach($suppliers as $s): ?>
        <tr>
          <td><?= $s['id'] ?></td>
          <td class="fw-bold"><?= Helper::e($s['name']) ?></td>
          <td class="mono"><?= Helper::e($s['phone']??'—') ?></td>
          <td><?= Helper::e($s['email']??'—') ?></td>
          <td><?= $s['balance']>0 ? '<span class="badge badge-danger">'.number_format($s['balance'],2)." $currency</span>" : '<span class="badge badge-success">لا يوجد</span>' ?></td>
          <td><button class="action-btn edit" onclick="editSupplier(<?= $s['id'] ?>)"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3" />
  <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3" />
  <path d="M16 5l3 3" />
</svg></button></td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<!-- Modal -->
<div class="modal-overlay" id="supplier-modal" style="display:none">
  <div class="modal-box">
    <div class="modal-header"><h3 id="sup-modal-title">إضافة مورد</h3><button onclick="document.getElementById('supplier-modal').style.display='none'"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button></div>
    <form method="POST" action="?page=suppliers&action=store" id="supplier-form">
      <input type="hidden" name="csrf_token" value="<?= Session::csrf() ?>">
      <input type="hidden" name="id" id="sup-id">
      <div class="form-group"><label class="form-label required">الاسم</label><input type="text" name="name" id="sup-name" class="form-input" required></div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">الهاتف</label><input type="text" name="phone" id="sup-phone" class="form-input"></div>
        <div class="form-group"><label class="form-label">البريد</label><input type="email" name="email" id="sup-email" class="form-input"></div>
      </div>
      <div class="form-group"><label class="form-label">العنوان</label><textarea name="address" id="sup-address" class="form-textarea" rows="2"></textarea></div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
  <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 4l0 4l-6 0l0 -4" />
</svg> حفظ</button>
        <button type="button" onclick="document.getElementById('supplier-modal').style.display='none'" class="btn btn-ghost">إلغاء</button>
      </div>
    </form>
  </div>
</div>
<script>
function editSupplier(id) {
  fetch(`?page=suppliers&action=getOne&id=${id}`).then(r=>r.json()).then(d=>{
    if(!d.supplier) return;
    const s = d.supplier;
    document.getElementById('sup-modal-title').textContent = 'تعديل بيانات المورد';
    document.getElementById('supplier-form').action = '?page=suppliers&action=update';
    document.getElementById('sup-id').value = s.id;
    document.getElementById('sup-name').value = s.name||'';
    document.getElementById('sup-phone').value = s.phone||'';
    document.getElementById('sup-email').value = s.email||'';
    document.getElementById('sup-address').value = s.address||'';
    document.getElementById('supplier-modal').style.display = 'flex';
  });
}
</script>
