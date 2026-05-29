<?php
/* ==============================================
   نقطة البيع — POS
   مع منع بيع المنتجات منتهية الصلاحية
   وعداد تنازلي للمنتجات قريبة الانتهاء
============================================== */
$currency   = $settings['currency'] ?? 'ج.س';
$taxRate    = (float)($settings['tax_rate']    ?? 15);
$taxEnabled = (int)  ($settings['tax_enabled'] ?? 1);
$db         = Database::getInstance();
$customers  = $db->fetchAll("SELECT id, name, balance FROM customers ORDER BY name");
$today      = date('Y-m-d');

// منتجات قريبة الانتهاء / منتهية للإشعارات
$expiryAlerts = $db->fetchAll(
    "SELECT id, name, expiry_date, stock_qty, unit,
            DATEDIFF(expiry_date, CURDATE()) as days_left
     FROM products
     WHERE is_active = 1
       AND expiry_date IS NOT NULL
       AND expiry_date <= DATE_ADD(CURDATE(), INTERVAL 60 DAY)
     ORDER BY expiry_date ASC
     LIMIT 20"
);
?>

<!-- ===================== POS Layout ===================== -->
<div class="pos-wrap">

  <!-- ====== لوحة المنتجات ====== -->
  <div class="pos-left">

    <!-- شريط البحث والتصنيفات -->
    <div class="pos-toolbar">
      <div class="pos-search-box">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-search pos-search-ico" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M10 10m-7 0a7 7 0 1 0 14 0a7 7 0 1 0 -14 0" />
  <path d="M21 21l-6 -6" />
</svg>
        <input type="text" id="pos-search" class="pos-search-inp"
               placeholder="ابحث بالاسم أو الباركود... (F3)"
               oninput="searchProducts(this.value)" autocomplete="off">
        <button id="pos-search-clear" onclick="clearSearch()" style="display:none">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg>
        </button>
      </div>

      <!-- تنبيه انتهاء الصلاحية في الشريط -->
      <?php if (!empty($expiryAlerts)): ?>
      <?php
        $expiredCount = count(array_filter($expiryAlerts, fn($a) => $a['days_left'] < 0));
        $soonCount    = count(array_filter($expiryAlerts, fn($a) => $a['days_left'] >= 0));
      ?>
      <button class="pos-expiry-alert-btn" onclick="toggleExpiryPanel()" title="تنبيهات الصلاحية">
        <?php if ($expiredCount > 0): ?>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-octagon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round"style=" color:#ff7875">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"style="color:#ff7875"/>
  <path d="M12 9v4" />
  <path d="M12 17h.01" />
  <path d="M9.172 3h5.656a2 2 0 0 1 1.414 .586l4.172 4.172a2 2 0 0 1 .586 1.414v5.656a2 2 0 0 1 -.586 1.414l-4.172 4.172a2 2 0 0 1 -1.414 .586h-5.656a2 2 0 0 1 -1.414 -.586l-4.172 -4.172a2 2 0 0 1 -.586 -1.414v-5.656a2 2 0 0 1 .586 -1.414l4.172 -4.172a2 2 0 0 1 1.414 -.586z" />
</svg>
        <!-- <i class="ti ti-alert-octagon"></i> -->
        <span class="pea-count red"><?= $expiredCount ?> منتهي</span>
        <?php endif; ?>
        <?php if ($soonCount > 0): ?>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round" style="color:#ffb84d">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 7v5l3 3" />
</svg>
        <!-- <i class="ti ti-clock" style="color:#ffb84d"></i> -->
        <span class="pea-count orange"><?= $soonCount ?> قريب</span>
        <?php endif; ?>
      </button>
      <?php endif; ?>
    </div>

    <!-- تصنيفات -->
    <div class="pos-cats-bar" id="pos-cats">
      <button class="pcat-btn active" onclick="filterCat(0, this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-layout-grid" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M14 4m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M4 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
  <path d="M14 14m0 1a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v4a1 1 0 0 1 -1 1h-4a1 1 0 0 1 -1 -1z" />
</svg> الكل
      </button>
    </div>

    <!-- شبكة المنتجات -->
    <div class="pos-grid" id="products-grid">
      <div class="pos-loading">
        <div class="loader-spinner"></div>
        <span>جاري تحميل المنتجات...</span>
      </div>
    </div>
  </div>

  <!-- ====== السلة ====== -->
  <div class="pos-right">

    <!-- رأس السلة -->
    <div class="cart-head">
      <div class="cart-head-title">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-receipt-2" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 21v-16a2 2 0 0 1 2 -2h10a2 2 0 0 1 2 2v16l-3 -2l-2 2l-2 -2l-2 2l-2 -2l-3 2" />
  <path d="M14 8m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M14 8m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M9 11h.01" />
  <path d="M9 15h.01" />
  <path d="M13 15h2" />
</svg>
        <span>الفاتورة الحالية</span>
        <span class="cart-count-badge" id="cart-count" style="display:none">0</span>
      </div>
      <div style="display:flex;gap:4px">
        <button class="cart-ico-btn" onclick="holdInvoice()" title="تعليق الفاتورة">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-pause" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M6 5m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
  <path d="M14 5m0 1a1 1 0 0 1 1 -1h2a1 1 0 0 1 1 1v12a1 1 0 0 1 -1 1h-2a1 1 0 0 1 -1 -1z" />
</svg>
        </button>
        <button class="cart-ico-btn danger" onclick="clearCart()" title="مسح الفاتورة">
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-trash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M4 7l16 0" />
  <path d="M10 11l0 6" />
  <path d="M14 11l0 6" />
  <path d="M5 7l1 12a2 2 0 0 0 2 2h8a2 2 0 0 0 2 -2l1 -12" />
  <path d="M9 7v-3a1 1 0 0 1 1 -1h4a1 1 0 0 1 1 1v3" />
</svg>
        </button>
      </div>
    </div>

    <!-- اختيار العميل -->
    <div class="cart-customer-sel">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#7a8e72" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 10m-3 0a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
  <path d="M6.168 18.849a4 4 0 0 1 3.832 -2.849h4a4 4 0 0 1 3.834 2.855" />
</svg>
      <select id="customer-select" onchange="onCustomerChange(this)">
        <option value="">عميل نقدي</option>
        <?php foreach ($customers as $c): ?>
        <option value="<?= $c['id'] ?>"
                data-balance="<?= $c['balance'] ?>">
          <?= Helper::e($c['name']) ?>
          <?php if ((float)$c['balance'] > 0): ?>
            (دين: <?= number_format($c['balance'], 2) ?>)
          <?php endif; ?>
        </option>
        <?php endforeach; ?>
      </select>
    </div>

    <!-- تحذير رصيد العميل -->
    <div id="customer-debt-warn" class="customer-debt-warn" style="display:none">
      <svg xmlns="[http://www.w3.org/2000/svg](http://www.w3.org/2000/svg)" class="icon icon-tabler icon-tabler-alert-triangle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 9v4" />
  <path d="M12 17h.01" />
  <path d="M10.363 3.591l-8.106 13.534a1.914 1.914 0 0 0 1.636 2.871h1```6.214a1.914 1.914 0 0 0 1.636 -2.87l-8.106 -13.536a1.914 1.914 0 0 0 -3.274 0z" />
</svg>
      <span id="customer-debt-text"></span>
    </div>

    <!-- عناصر السلة -->
    <div class="cart-body" id="cart-items">
      <div class="cart-placeholder">
        <div class="cart-placeholder-ico">🛒</div>
        <p>انقر على منتج لإضافته</p>
        <small>أو ابحث بالاسم / الباركود</small>
      </div>
    </div>

    <!-- خصم -->
    <div class="cart-discount-row">
      <span class="cdr-label"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-discount-2" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M9 15l6 -6" />
  <path d="M9.5 9.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
  <path d="M14.5 14.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
  <path d="M5 7.2a2.2 2.2 0 0 1 2.2 -2.2h9.6a2.2 2.2 0 0 1 2.2 2.2v9.6a2.2 2.2 0 0 1 -2.2 2.2h-9.6a2.2 2.2 0 0 1 -2.2 -2.2z" />
</svg> خصم</span>
      <input type="number" id="discount-val" min="0" step="0.01"
             placeholder="0" oninput="recalc()" class="cdr-input">
      <select id="discount-type" onchange="recalc()" class="cdr-select">
        <option value="fixed"><?= $currency ?></option>
        <option value="percent">%</option>
      </select>
    </div>

    <!-- إجماليات -->
    <div class="cart-totals">
      <div class="ct-row"><span>المجموع الفرعي</span><span id="s-subtotal">0.00 <?= $currency ?></span></div>
      <div class="ct-row discount-ct" id="discount-row" style="display:none">
        <span>الخصم</span><span id="s-discount" style="color:#ff7875"></span>
      </div>
      <?php if ($taxEnabled): ?>
      <div class="ct-row"><span>ضريبة (<?= $taxRate ?>%)</span><span id="s-tax">0.00 <?= $currency ?></span></div>
      <?php endif; ?>
      <div class="ct-row ct-total">
        <span><strong>الإجمالي</strong></span>
        <span id="s-total"><strong>0.00 <?= $currency ?></strong></span>
      </div>
    </div>

    <!-- طرق الدفع -->
    <div class="payment-methods-row">
      <button class="pm-btn active" data-method="cash" onclick="selectPM(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-cash" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7 10a3 3 0 1 0 6 0a3 3 0 1 0 -6 0" />
  <path d="M21 12v-2a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h7" />
  <path d="M19 16m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
  <path d="M22 19l-3 -3" />
</svg><span>نقدي</span>
      </button>
      <button class="pm-btn" data-method="card" onclick="selectPM(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-credit-card" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 5m0 3a3 3 0 0 1 3 -3h12a3 3 0 0 1 3 3v8a3 3 0 0 1 -3 3h-12a3 3 0 0 1 -3 -3z" />
  <path d="M3 10l18 0" />
  <path d="M7 15l.01 0" />
  <path d="M11 15l2 0" />
</svg><span>بطاقة</span>
      </button>
      <button class="pm-btn" data-method="transfer" onclick="selectPM(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-transfer" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M20 10H4l4-4" />
  <path d="M4 14h16l-4 4" />
</svg><span>تحويل</span>
      </button>
      <button class="pm-btn" data-method="credit" onclick="selectPM(this)">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-dollar" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M20.866 10.45a9 9 0 1 0 -7.815 10.488" />
  <path d="M12 7v5l1.5 1.5" />
  <path d="M21 15h-2.5a1.5 1.5 0 0 0 0 3h1a1.5 1.5 0 0 1 0 3h-2.5" />
  <path d="M19 13v1" />
  <path d="M19 21v1" />
</svg><span>آجل</span>
      </button>
    </div>

    <!-- المبلغ المدفوع (نقدي فقط) -->
    <div class="paid-section" id="paid-section">
      <div class="paid-row">
        <label class="paid-lbl">المدفوع</label>
        <input type="number" id="paid-amount" step="0.01" min="0"
               placeholder="0.00" oninput="calcChange()" class="paid-inp">
      </div>
      <div class="change-row" id="change-row" style="display:none">
        <span class="change-lbl">الباقي للعميل</span>
        <span id="change-amount" class="change-val">0.00 <?= $currency ?></span>
      </div>
    </div>

    <!-- زر البيع -->
    <button class="btn-complete-sale" onclick="processSale()" id="pay-btn">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg>
      <span>إتمام البيع</span>
    </button>

  </div>
</div>

<!-- ===================== لوحة تنبيهات الصلاحية ===================== -->
<div class="expiry-panel" id="expiry-panel" style="display:none">
  <div class="ep-header">
    <span><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock-exclamation" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M20.986 12.51a9 9 0 1 0 -5.71 8.348" />
  <path d="M12 7v5l2 2" />
  <path d="M19 16v3" />
  <path d="M19 22v.01" />
</svg> تنبيهات الصلاحية</span>
    <button onclick="toggleExpiryPanel()"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button>
  </div>
  <div class="ep-body">
    <?php foreach ($expiryAlerts as $a):
      $dl = (int)$a['days_left'];
      $isExp = $dl < 0;
      $cls = $isExp ? 'ep-item-expired' : ($dl <= 7 ? 'ep-item-critical' : ($dl <= 30 ? 'ep-item-warn' : 'ep-item-soon'));
      $label = $isExp
        ? 'منتهي منذ ' . abs($dl) . ' يوم'
        : ($dl === 0 ? 'ينتهي اليوم!' : ($dl === 1 ? 'ينتهي غداً!' : 'ينتهي خلال ' . $dl . ' يوم'));
    ?>
    <div class="ep-item <?= $cls ?>">
      <div class="ep-item-icon">
        <?= $isExp ? '⛔' : ($dl <= 7 ? '🚨' : ($dl <= 30 ? '⚠️' : '⏳')) ?>
      </div>
      <div class="ep-item-info">
        <div class="ep-item-name"><?= Helper::e($a['name']) ?></div>
        <div class="ep-item-meta">
          <span><?= $label ?></span>
          <span><?= Helper::formatDate($a['expiry_date'], 'd/m/Y') ?></span>
          <span>المخزون: <?= number_format($a['stock_qty'], 0) ?> <?= Helper::e($a['unit']) ?></span>
        </div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($expiryAlerts)): ?>
    <div style="text-align:center;padding:20px;color:var(--text-3)">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg>
      <!-- <i class="ti ti-circle-check" style="font-size:32px;display:block;margin-bottom:8px"></i> -->
      جميع المنتجات في حالة جيدة
    </div>
    <?php endif; ?>
  </div>
</div>
<div class="ep-overlay" id="ep-overlay" onclick="toggleExpiryPanel()"></div>

<!-- ===================== Modal: نجاح البيع ===================== -->
<div class="modal-overlay" id="success-modal" style="display:none">
  <div class="modal-box" style="max-width:400px;text-align:center">
    <div style="font-size:58px;margin-bottom:10px">✅</div>
    <h3 style="font-size:18px;font-weight:700;color:var(--text);margin-bottom:6px">تمت عملية البيع بنجاح</h3>
    <div id="success-info" class="success-info-box"></div>
    <div style="display:flex;gap:8px;justify-content:center;margin-top:18px">
      <button onclick="printAndClose()" class="btn btn-primary">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-printer" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M17 17h2a2 2 0 0 0 2 -2v-4a2 2 0 0 0 -2 -2h-14a2 2 0 0 0 -2 2v4a2 2 0 0 0 2 2h2" />
  <path d="M17 9v-4a2 2 0 0 0 -2 -2h-6a2 2 0 0 0 -2 2v4" />
  <path d="M7 13m0 2a2 2 0 0 1 2 -2h6a2 2 0 0 1 2 2v4a2 2 0 0 1 -2 2h-6a2 2 0 0 1 -2 -2z" />
</svg> طباعة الفاتورة
      </button>
      <button onclick="newSale()" class="btn btn-outline">
        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-plus" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 5l0 14" />
  <path d="M5 12l14 0" />
</svg> بيع جديد
      </button>
    </div>
  </div>
</div>

<!-- ===================== Modal: منتج منتهي الصلاحية ===================== -->
<div class="modal-overlay" id="expired-modal" style="display:none">
  <div class="modal-box" style="max-width:380px;text-align:center;padding:32px">
    <div style="font-size:60px;margin-bottom:12px">⛔</div>
    <h3 style="color:#ff7875;font-size:17px;margin-bottom:8px">منتج منتهي الصلاحية!</h3>
    <p id="expired-msg" style="font-size:13px;color:var(--text-2);line-height:1.6;margin-bottom:20px"></p>
    <div style="background:var(--expired-bg,#1e1010);border:1px solid var(--expired-border,#3a1010);border-radius:8px;padding:10px;margin-bottom:18px;font-size:12px;color:var(--expired-text,#ff7875)">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 9h.01" />
  <path d="M11 12h1v4h1" />
</svg>
      لا يمكن بيع منتجات منتهية الصلاحية حفاظاً على سلامة العملاء
    </div>
    <button onclick="document.getElementById('expired-modal').style.display='none'"
            class="btn btn-danger" style="width:100%;padding:11px;font-size:14px">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> حسناً، لن أضيفه
    </button>
  </div>
</div>

<!-- ===================== Styles ===================== -->
<style>
/* ===== POS Layout ===== */
.pos-wrap {
  display: grid;
  grid-template-columns: 1fr 350px;
  gap: 0;
  height: 100vh;
  height: calc(100vh - var(--topbar-h) - 30px);
  overflow: hidden;
  margin: 15px;
}
.pos-left {
  display: flex; flex-direction: column; overflow: auto;
  padding: 0px 0px 0px 4px;
  background: var(--bg);
}
.pos-right {
  background: var(--surface);
  border-right: 1px solid var(--border);
  display: flex; flex-direction: column;
  overflow: hidden;
  padding: 12px;
}

/* ===== Toolbar ===== */
.pos-toolbar {
  display: flex; gap: 8px; align-items: center;
  margin-bottom: 8px; flex-shrink: 0;
}
.pos-search-box {
  flex: 1; position: relative; display: flex; align-items: center;
}
.pos-search-ico {
  position: absolute; right: 10px; color: var(--text-3); font-size: 15px; pointer-events: none;
}
.pos-search-inp {
  width: 100%; padding: 9px 34px 9px 32px;
  border: 1.5px solid var(--input-border); border-radius: 9px;
  font-size: 13px; background: var(--surface); color: var(--text);
  transition: var(--transition);
}
.pos-search-inp:focus {
  outline: none; border-color: var(--olive-400);
  box-shadow: 0 0 0 3px rgba(78,133,64,.12);
}
[data-theme="dark"] .pos-search-inp:focus {
  border-color: #63a2ff; box-shadow: 0 0 0 3px rgba(99,162,255,.15);
}
#pos-search-clear {
  position: absolute; left: 8px; background: none; border: none;
  cursor: pointer; color: var(--text-3); font-size: 14px;
}
.pos-expiry-alert-btn {
  display: flex; align-items: center; gap: 5px; flex-shrink: 0;
  background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 8px; padding: 6px 10px; cursor: pointer;
  font-size: 12px; transition: var(--transition);
}
.pos-expiry-alert-btn:hover { border-color: #ffb84d; }
.pea-count { font-weight: 100; font-size: 1px; }
.pea-count.red { color: #ff7875; }
.pea-count.orange { color: #ffb84d; }

/* ===== Categories ===== */
.pos-cats-bar {
  display: flex; gap: 5px; flex-wrap: nowrap; overflow-x: auto;
  margin-bottom: 10px; flex-shrink: 0; padding-bottom: 4px;
  scrollbar-width: thin;
}
.pcat-btn {
  display: flex; align-items: center; gap: 5px;
  padding: 5px 12px; border-radius: 16px; flex-shrink: 0;
  border: 1.5px solid var(--border); background: var(--surface);
  color: var(--text-2); font-size: 12px; font-weight: 600; cursor: pointer;
  transition: var(--transition);
}
.pcat-btn:hover { border-color: var(--olive-300); color: var(--olive-600); }
.pcat-btn.active {
  background: var(--olive-600); border-color: var(--olive-600); color: white;
}
[data-theme="dark"] .pcat-btn.active { background: #2563eb; border-color: #2563eb; }

/* ===== Products Grid ===== */
.pos-grid {
  /* flex: 1; overflow-y: auto; overflow-x: auto; */
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(80px, 1fr));
  gap: 8px;
  align-content: start;
  padding: 2px;
}
.pos-loading {
  grid-column: 1/-1; display: flex; flex-direction: column;
  align-items: center; justify-content: center; padding: 40px;
  color: var(--text-3); gap: 10px; font-size: 10px;
}

/* ===== Product Tile ===== */
.ptile {
  background: var(--surface); border: 1.5px solid var(--border);
  border-radius: 10px; padding: 10px; cursor: pointer;
  transition: all var(--transition); text-align: center;
  position: relative; overflow: hidden; user-select: none;
}
.ptile:hover:not(.ptile-disabled) {
  border-color: var(--olive-400); transform: translateY(-2px);
  box-shadow: 0 4px 16px rgba(17, 146, 163, 0.15);
}
[data-theme="dark"] .ptile:hover:not(.ptile-disabled) {
  border-color: #63a2ff; box-shadow: 0 4px 16px rgba(99,162,255,.1);
}
/* منتهي الصلاحية */
.ptile-expired {
  background: var(--expired-bg, #1e0e0e);
  border-color: var(--expired-border, #3a1515) !important;
  cursor: not-allowed;
  opacity: .85;
}
.ptile-expired .ptile-name { color: var(--expired-text, #ff7875); }
/* نفذ المخزون */
.ptile-outstock { opacity: .5; cursor: not-allowed; }
/* قريب الانتهاء (≤60 يوم) */
.ptile-expiry-warn {
  border-color: #c07000 !important;
}
[data-theme="dark"] .ptile-expiry-warn { border-color: #ffb84d !important; }

/* شارة "منتهي" */
.ptile-badge-expired {
  position: absolute; top: 0; right: 0; left: 0;
  background: linear-gradient(135deg,#c0392b,#e74c3c);
  color: white; font-size: 10px; font-weight: 700;
  padding: 3px 6px; text-align: center;
  border-radius: 8px 8px 0 0;
}
/* عداد تنازلي */
.ptile-countdown {
  position: absolute; top: 0; right: 0; left: 0;
  font-size: 10px; font-weight: 700; padding: 3px 6px;
  text-align: center; border-radius: 8px 8px 0 0;
}
.countdown-critical { background: #7a1010; color: #ffaaaa; }
.countdown-warn     { background: #7a4a00; color: #ffd080; }
.countdown-soon     { background: #4a5a00; color: #d4e880; }
[data-theme="dark"] .countdown-critical { background: rgba(192,57,43,.35); color: #ffaaaa; }
[data-theme="dark"] .countdown-warn     { background: rgba(192,112,0,.35); color: #ffd080; }
[data-theme="dark"] .countdown-soon     { background: rgba(100,120,0,.3); color: #d4e880; }

.ptile-img {
  width: 52px; height: 52px; margin: 0 auto 6px;
  border-radius: 8px; overflow: hidden; background: var(--bg);
  display: flex; align-items: center; justify-content: center;
}
.ptile-img img { width: 100%; height: 100%; object-fit: cover; }
.ptile-placeholder { color: var(--text-4); font-size: 22px; }
.ptile-name {
  font-size: 8px; font-weight: 600; color: var(--text);
  margin-bottom: 3px; line-height: 1.3;
  display: -webkit-box; -webkit-line-clamp: 2;
  -webkit-box-orient: vertical; overflow: hidden;
}
.ptile-price { font-size: 10px; font-weight: 500; color: var(--olive-500); margin-bottom: 3px; }
[data-theme="dark"] .ptile-price { color: #63ff68b3; }
.ptile-stock {
  font-size: 9px; font-weight: 600; padding: 2px 7px;
  border-radius: 8px; display: inline-block;
}
.ps-ok  { background: rgba(61,107,50,.12); color: var(--olive-600); }
.ps-low { background: rgba(192,112,0,.15); color: #c07000; }
.ps-out { background: rgba(192,57,43,.15); color: #c0392b; }
[data-theme="dark"] .ps-ok  { background: rgba(99,162,255,.1); color: #63a2ff; }
[data-theme="dark"] .ps-low { background: rgba(255,184,77,.1); color: #ffb84d; }
[data-theme="dark"] .ps-out { background: rgba(255,120,117,.1); color: #ff7875; }

/* ===== Cart Right Panel ===== */
.cart-head {
  display: flex; align-items: center; justify-content: space-between;
  margin-bottom: 8px; flex-shrink: 0;
}
.cart-head-title {
  display: flex; align-items: center; gap: 7px;
  font-size: 10px; font-weight: 700; color: var(--text);
}
.cart-count-badge {
  background: var(--olive-600); color: white;
  font-size: 10px; font-weight: 700; padding: 1px 7px;
  border-radius: 10px;
}
[data-theme="dark"] .cart-count-badge { background: #2563eb; }
.cart-ico-btn {
  width: 28px; height: 28px; border-radius: 7px;
  border: 1px solid var(--border); background: none;
  color: var(--text-3); cursor: pointer; font-size: 14px;
  display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
}
.cart-ico-btn:hover { background: var(--bg); color: var(--text); }
.cart-ico-btn.danger:hover { background: rgba(192,57,43,.1); color: #ff7875; }

.cart-customer-sel {
  display: flex; align-items: center; gap: 6px; margin-bottom: 8px; flex-shrink: 0;
}
.cart-customer-sel select {
  flex: 1; padding: 7px 10px;
  border: 1.5px solid var(--input-border); border-radius: 8px;
  font-size: 12px; background: var(--input-bg); color: var(--text);
}
.customer-debt-warn {
  background: rgba(255,184,77,.12); border: 1px solid rgba(255,184,77,.25);
  border-radius: 7px; padding: 6px 10px; margin-bottom: 8px;
  font-size: 11px; color: #ffb84d; display: flex; align-items: center; gap: 5px;
}

/* Cart Body */
.cart-body {
  flex: 1; overflow-y: auto; overflow-x: hidden;
  margin-bottom: 8px;
}
.cart-placeholder {
  height: 100%; display: flex; flex-direction: column;
  align-items: center; justify-content: center;
  color: var(--text-4); text-align: center; gap: 6px;
}
.cart-placeholder-ico { font-size: 42px; opacity: .4; }
.cart-placeholder p { font-size: 13px; color: var(--text-3); }
.cart-placeholder small { font-size: 11px; }

/* Cart Item */
.citem {
  display: flex; align-items: center; gap: 5px;
  padding: 7px 0; border-bottom: 1px solid var(--border-2);
}
.citem:last-child { border-bottom: none; }
.citem-del {
  width: 20px; height: 20px; border: none; background: none;
  color: var(--text-4); cursor: pointer; font-size: 13px; flex-shrink: 0;
  border-radius: 4px; display: flex; align-items: center; justify-content: center;
  transition: var(--transition);
}
.citem-del:hover { color: #ff7875; background: rgba(255,120,117,.1); }
.citem-name { flex: 1; font-size: 11px; font-weight: 600; color: var(--text); line-height: 1.3; }
.citem-qty {
  display: flex; align-items: center; gap: 2px;
}
.qty-btn {
  width: 20px; height: 20px; border: 1px solid var(--border);
  background: var(--bg); border-radius: 5px; cursor: pointer;
  font-size: 13px; color: var(--text); display: flex; align-items: center; justify-content: center;
  transition: var(--transition); flex-shrink: 0;
}
.qty-btn:hover { border-color: var(--olive-400); color: var(--olive-600); }
[data-theme="dark"] .qty-btn:hover { border-color: #63a2ff; color: #63a2ff; }
.qty-inp {
  width: 30px; text-align: center; border: 1px solid var(--border);
  border-radius: 5px; font-size: 12px; padding: 2px 3px;
  background: var(--input-bg); color: var(--text);
}
.citem-price {
  font-size: 12px; font-weight: 700; color: var(--olive-500);
  width: 52px; text-align: left; flex-shrink: 0;
}
[data-theme="dark"] .citem-price { color: #63a2ff; }

/* Discount */
.cart-discount-row {
  display: flex; align-items: center; gap: 5px;
  padding: 6px 0; flex-shrink: 0;
  border-top: 1px solid var(--border-2);
}
.cdr-label { font-size: 12px; color: var(--text-2); display: flex; align-items: center; gap: 4px; flex-shrink: 0; }
.cdr-input {
  width: 60px; padding: 5px 7px; border: 1.5px solid var(--input-border);
  border-radius: 7px; font-size: 12px; background: var(--input-bg); color: var(--text); text-align: center;
}
.cdr-select {
  padding: 5px 6px; border: 1.5px solid var(--input-border);
  border-radius: 7px; font-size: 12px; background: var(--input-bg); color: var(--text);
}

/* Totals */
.cart-totals { padding: 8px 0; flex-shrink: 0; border-top: 1px solid var(--border-2); }
.ct-row {
  display: flex; justify-content: space-between; align-items: center;
  font-size: 12px; color: var(--text-2); padding: 2px 0;
}
.ct-total {
  font-size: 15px; color: var(--text); font-weight: 700;
  border-top: 1px solid var(--border); margin-top: 5px; padding-top: 7px;
}

/* Payment methods */
.payment-methods-row {
  display: grid; grid-template-columns: repeat(4, 1fr); gap: 4px;
  margin: 8px 0; flex-shrink: 0;
}
.pm-btn {
  padding: 7px 4px; border: 1.5px solid var(--border); border-radius: 8px;
  background: none; color: var(--text-2); cursor: pointer;
  font-size: 11px; font-weight: 600;
  display: flex; flex-direction: column; align-items: center; gap: 3px;
  transition: var(--transition);
}
.pm-btn i { font-size: 16px; }
.pm-btn:hover { border-color: var(--olive-300); color: var(--olive-600); background: var(--olive-50); }
.pm-btn.active {
  border-color: var(--olive-500); background: var(--olive-50);
  color: var(--olive-700);
}
[data-theme="dark"] .pm-btn:hover  { border-color: #63a2ff; color: #63a2ff; background: rgba(99,162,255,.07); }
[data-theme="dark"] .pm-btn.active { border-color: #2563eb; background: rgba(37,99,235,.12); color: #63a2ff; }

/* Paid section */
.paid-section { flex-shrink: 0; margin-bottom: 8px; }
.paid-row { display: flex; align-items: center; gap: 8px; }
.paid-lbl { font-size: 12px; color: var(--text-2); white-space: nowrap; }
.paid-inp {
  flex: 1; padding: 8px 10px; border: 1.5px solid var(--input-border);
  border-radius: 8px; font-size: 14px; font-weight: 600;
  background: var(--input-bg); color: var(--text); text-align: left;
}
.paid-inp:focus { outline: none; border-color: var(--olive-400); }
[data-theme="dark"] .paid-inp:focus { border-color: #63a2ff; }
.change-row {
  display: flex; justify-content: space-between; align-items: center;
  margin-top: 5px; padding: 6px 8px;
  background: rgba(61,107,50,.08); border-radius: 7px;
  font-size: 12px;
}
[data-theme="dark"] .change-row { background: rgba(37,99,235,.1); }
.change-lbl { color: var(--text-2); }
.change-val { font-weight: 700; color: var(--olive-600); font-size: 14px; }
[data-theme="dark"] .change-val { color: #63a2ff; }

/* Complete Sale Button */
.btn-complete-sale {
  width: 100%; padding: 12px; flex-shrink: 0;
  background: linear-gradient(135deg, var(--olive-600), var(--olive-500));
  color: white; border: none; border-radius: 10px;
  font-size: 15px; font-weight: 700; cursor: pointer;
  display: flex; align-items: center; justify-content: center; gap: 8px;
  transition: var(--transition);
  box-shadow: 0 2px 12px rgba(61,107,50,.3);
}
.btn-complete-sale:hover { opacity: .93; transform: translateY(-1px); }
.btn-complete-sale:active { transform: translateY(0); }
.btn-complete-sale:disabled { opacity: .5; cursor: not-allowed; transform: none; }
[data-theme="dark"] .btn-complete-sale {
  background: linear-gradient(135deg,#1d4ed8,#2563eb);
  box-shadow: 0 2px 12px rgba(37,99,235,.3);
}

/* Expiry Panel */
.expiry-panel {
  position: fixed; left: 0; top: var(--topbar-h); bottom: 0;
  width: 320px; background: var(--surface); z-index: 200;
  border-right: 1px solid var(--border); display: flex; flex-direction: column;
  box-shadow: 4px 0 24px rgba(0,0,0,.12); overflow: hidden;
}
.ep-header {
  display: flex; justify-content: space-between; align-items: center;
  padding: 14px 16px; border-bottom: 1px solid var(--border);
  font-size: 14px; font-weight: 700; color: var(--text); flex-shrink: 0;
}
.ep-header button { background: none; border: none; cursor: pointer; color: var(--text-3); font-size: 18px; }
.ep-body { flex: 1; overflow-y: auto; padding: 10px; }
.ep-item {
  display: flex; align-items: flex-start; gap: 10px;
  padding: 10px; border-radius: 8px; margin-bottom: 8px;
  border: 1px solid transparent;
}
.ep-item-expired  { background: var(--expired-bg); border-color: var(--expired-border); }
.ep-item-critical { background: rgba(192,57,43,.1); border-color: rgba(192,57,43,.25); }
.ep-item-warn     { background: rgba(192,112,0,.1); border-color: rgba(192,112,0,.25); }
.ep-item-soon     { background: rgba(100,140,0,.08); border-color: rgba(100,140,0,.2); }
.ep-item-icon { font-size: 20px; flex-shrink: 0; margin-top: 1px; }
.ep-item-name { font-size: 13px; font-weight: 600; color: var(--text); margin-bottom: 4px; }
.ep-item-meta { display: flex; gap: 8px; flex-wrap: wrap; }
.ep-item-meta span {
  font-size: 11px; padding: 1px 6px; border-radius: 6px;
  background: rgba(0,0,0,.05); color: var(--text-2);
}
[data-theme="dark"] .ep-item-meta span { background: rgba(255,255,255,.06); }
.ep-overlay {
  display: none; position: fixed; inset: 0; z-index: 199;
  background: rgba(0,0,0,.25);
}

/* Success Info Box */
.success-info-box {
  background: var(--bg); border-radius: 9px; padding: 14px;
  margin: 12px 0; font-size: 13px; color: var(--text-2); text-align: right;
}
.success-info-box p { margin-bottom: 5px; display: flex; justify-content: space-between; }
.success-info-box p:last-child { margin-bottom: 0; }

@media (max-width:900px) {
  .pos-wrap { grid-template-columns: 1fr; }
  .pos-right { display: none; }
}
</style>

<!-- ===================== JavaScript ===================== -->
<script>
const CURRENCY    = '<?= $currency ?>';
const TAX_RATE    = <?= $taxRate ?>;
const TAX_ENABLED = <?= $taxEnabled ? 'true' : 'false' ?>;
const TODAY_STR   = '<?= $today ?>';

let allProducts   = [];
let cart          = [];
let selectedPM    = 'cash';
let lastInvoiceId = null;

// ============================================================
// تحميل المنتجات
// ============================================================
window.addEventListener('DOMContentLoaded', () => loadProducts(0));

async function loadProducts(catId) {
  try {
    const res  = await fetch(`?page=pos&action=getProducts&cat=${catId}`);
    const data = await res.json();
    allProducts = data.products || [];
    renderProducts(allProducts);
    if (catId === 0 && data.categories) renderCats(data.categories);
  } catch(e) {
    document.getElementById('products-grid').innerHTML =
      '<div style="grid-column:1/-1;text-align:center;padding:40px;color:#ff7875;font-size:13px">فشل تحميل المنتجات</div>';
  }
}

// ============================================================
// حساب الأيام للانتهاء
// ============================================================
function daysLeft(dateStr) {
  if (!dateStr) return null;
  const exp = new Date(dateStr); exp.setHours(0,0,0,0);
  const now = new Date(TODAY_STR); now.setHours(0,0,0,0);
  return Math.floor((exp - now) / 86400000);
}
const isExpired     = d => d !== null && d < 0;
const isExpiringSoon = d => d !== null && d >= 0 && d <= 60;

function countdownLabel(days) {
  if (days < 0)  return 'منتهي الصلاحية';
  if (days === 0) return '⚠️ ينتهي اليوم!';
  if (days === 1) return '⚠️ ينتهي غداً!';
  if (days <= 7)  return `⏳ ${days} أيام متبقية`;
  if (days <= 30) return `⏳ ${days} يوم`;
  return `⏳ ${Math.ceil(days/7)} أسبوع`;
}

// ============================================================
// رسم التصنيفات
// ============================================================
function renderCats(cats) {
  const bar = document.getElementById('pos-cats');
  cats.forEach(cat => {
    const b = document.createElement('button');
    b.className   = 'pcat-btn';
    b.innerHTML   = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="#7a8e72" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M7.5 7.5m-1 0a1 1 0 1 0 2 0a1 1 0 1 0 -2 0" />
  <path d="M3 6v5.172a2 2 0 0 0 .586 1.414l7.71 7.71a2 2 0 0 0 2.828 0l6.414 -6.414a2 2 0 0 0 0 -2.828l-7.71 -7.71a2 2 0 0 0 -1.414 -.586h-5.172a2 2 0 0 0 -2 2z" />
</svg> ${escHtml(cat.name)}`;
    b.onclick     = () => { filterCat(cat.id, b); };
    bar.appendChild(b);
  });
}
function filterCat(id, btn) {
  document.querySelectorAll('.pcat-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  const list = id === 0 ? allProducts : allProducts.filter(p => p.category_id == id);
  renderProducts(list);
}

// ============================================================
// رسم بطاقات المنتجات
// ============================================================
function renderProducts(list) {
  const g = document.getElementById('products-grid');
  if (!list.length) {
    g.innerHTML = `<div style="grid-column:1/-1;text-align:center;padding:40px;color:var(--text-3);font-size:7px">
      <i class="ti ti-search-off" style="font-size:10px;display:block;margin-bottom:10px;opacity:.4"></i>
      لا توجد منتجات</div>`;
    return;
  }

  g.innerHTML = list.map(p => {
    const days     = daysLeft(p.expiry_date);
    const expired  = isExpired(days);
    const soon     = !expired && isExpiringSoon(days);
    const outStock = parseFloat(p.stock_qty) <= 0;
    const disabled = expired || outStock;

    // Classes
    let cls = 'ptile';
    if (expired)       cls += ' ptile-expired ptile-disabled';
    else if (outStock) cls += ' ptile-outstock ptile-disabled';
    else if (soon)     cls += ' ptile-expiry-warn';

    // Click handler
    const click = disabled
      ? (expired
          ? `showExpiredModal('${escJs(p.name)}','${p.expiry_date}',${Math.abs(days)})`
          : `showToast('نفذ المخزون!','error')`)
      : `addToCart(${p.id})`;

    // صورة
    const imgHtml = p.image
      ? `<img src="<?= BASE_URL ?>/uploads/${escJs(p.image)}" alt="" loading="lazy">`
      : `<div class="ptile-placeholder"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="#7a8e72" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 3l8 4.5l0 9l-8 4.5l-8 -4.5l0 -9l8 -4.5" />
  <path d="M12 12l8 -4.5" />
  <path d="M12 12l0 9" />
  <path d="M12 12l-8 -4.5" />
  <path d="M16 5.25l-8 4.5" />
</svg></div>`;

    // شارة الصلاحية
    let badgeHtml = '';
    if (expired) {
      badgeHtml = `<div class="ptile-badge-expired">⛔ منتهي الصلاحية</div>`;
    } else if (soon && days !== null) {
      const cd  = days <= 7   ? 'countdown-critical'
                : days <= 30  ? 'countdown-warn'
                :               'countdown-soon';
      badgeHtml = `<div class="ptile-countdown ${cd}">${countdownLabel(days)}</div>`;
    }

    // حالة المخزون
    let stockHtml = '';
    if (outStock)     stockHtml = `<span class="ptile-stock ps-out">نفذ</span>`;
    else if (expired) stockHtml = `<span class="ptile-stock ps-out">منتهي</span>`;
    else {
      const qty    = parseFloat(p.stock_qty);
      const minAlt = parseFloat(p.min_stock_alert || 5);
      const sCls   = qty <= minAlt ? 'ps-low' : 'ps-ok';
      stockHtml    = `<span class="ptile-stock ${sCls}">${qty.toFixed(0)} ${escHtml(p.unit||'')}</span>`;
    }

    return `
<div class="${cls}" onclick="${click}" title="${escHtml(p.name)}">
  ${badgeHtml}
  <div class="ptile-img" style="${badgeHtml?'margin-top:18px':''}">${imgHtml}</div>
  <div class="ptile-name">${escHtml(p.name)}</div>
  <div class="ptile-price">${parseFloat(p.sale_price).toFixed(2)} ${CURRENCY}</div>
  ${stockHtml}
</div>`;
  }).join('');
}

// ============================================================
// البحث
// ============================================================
function searchProducts(q) {
  const clearBtn = document.getElementById('pos-search-clear');
  if (clearBtn) clearBtn.style.display = q ? 'block' : 'none';
  if (!q) { renderProducts(allProducts); return; }
  const lq = q.toLowerCase();
  renderProducts(allProducts.filter(p =>
    p.name.toLowerCase().includes(lq) || (p.barcode||'').includes(q)
  ));
}
function clearSearch() {
  const inp = document.getElementById('pos-search');
  inp.value = '';
  searchProducts('');
  inp.focus();
}

// ============================================================
// تنبيه منتج منتهي الصلاحية
// ============================================================
function showExpiredModal(name, dateStr, daysAgo) {
  const d = new Date(dateStr);
  const fmt = d.toLocaleDateString('ar', {year:'numeric',month:'long',day:'numeric'});
  document.getElementById('expired-msg').innerHTML =
    `المنتج <strong>«${escHtml(name)}»</strong> انتهت صلاحيته بتاريخ <strong>${fmt}</strong>` +
    (daysAgo > 0 ? ` (منذ ${daysAgo} يوم).` : '.');
  document.getElementById('expired-modal').style.display = 'flex';
  // هزّ بصري
  const modal = document.querySelector('#expired-modal .modal-box');
  modal.style.animation = 'shake .4s ease';
  setTimeout(() => { modal.style.animation = ''; }, 400);
}

// ============================================================
// لوحة تنبيهات الصلاحية
// ============================================================
function toggleExpiryPanel() {
  const panel   = document.getElementById('expiry-panel');
  const overlay = document.getElementById('ep-overlay');
  const isOpen  = panel.style.display !== 'none';
  panel.style.display   = isOpen ? 'none' : 'flex';
  overlay.style.display = isOpen ? 'none' : 'block';
}

// ============================================================
// إضافة للسلة
// ============================================================
function addToCart(productId) {
  const p = allProducts.find(x => x.id == productId);
  if (!p) return;

  // ← منع منتهي الصلاحية
  const days = daysLeft(p.expiry_date);
  if (isExpired(days)) {
    showExpiredModal(p.name, p.expiry_date, Math.abs(days));
    return;
  }

  // ← منع نافذ المخزون
  if (parseFloat(p.stock_qty) <= 0) {
    showToast('نفذ مخزون هذا المنتج', 'error');
    return;
  }

  const existing = cart.find(x => x.id == productId);
  if (existing) {
    if (existing.qty < parseFloat(p.stock_qty)) {
      existing.qty++;
    } else {
      showToast('وصلت للحد الأقصى المتاح في المخزون', 'warning');
      return;
    }
  } else {
    cart.push({ ...p, qty: 1 });
  }

  renderCart();
  recalc();
  showToast(`تمت إضافة «${p.name}»`, 'success');
}

// ============================================================
// رسم السلة
// ============================================================
function renderCart() {
  const el = document.getElementById('cart-items');
  const cb = document.getElementById('cart-count');

  // تحديث عداد السلة
  const total = cart.reduce((s, i) => s + i.qty, 0);
  if (cb) {
    cb.textContent    = total;
    cb.style.display  = cart.length ? 'inline-block' : 'none';
  }

  if (!cart.length) {
    el.innerHTML = `<div class="cart-placeholder">
      <div class="cart-placeholder-ico">🛒</div>
      <p>أضف منتجات للبيع</p>
      <small>انقر على أي منتج</small>
    </div>`;
    return;
  }

  el.innerHTML = cart.map((item, i) => `
<div class="citem">
  <button class="citem-del" onclick="removeItem(${i})"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg></button>
  <div class="citem-name">${escHtml(item.name)}</div>
  <div class="citem-qty">
    <button class="qty-btn" onclick="changeQty(${i},-1)">−</button>
    <input type="number" class="qty-inp" value="${item.qty}"
           min="1" max="${item.stock_qty}" onchange="setQty(${i},this.value)">
    <button class="qty-btn" onclick="changeQty(${i},1)">+</button>
  </div>
  <div class="citem-price">${(parseFloat(item.sale_price)*item.qty).toFixed(2)}</div>
</div>`).join('');
}

function removeItem(i) { cart.splice(i, 1); renderCart(); recalc(); }
function changeQty(i, d) {
  const max = parseFloat(allProducts.find(p => p.id == cart[i].id)?.stock_qty || 9999);
  cart[i].qty = Math.max(1, Math.min(cart[i].qty + d, max));
  renderCart(); recalc();
}
function setQty(i, v) {
  cart[i].qty = Math.max(1, parseInt(v) || 1);
  renderCart(); recalc();
}
function clearCart() {
  if (cart.length && !confirm('مسح الفاتورة الحالية؟')) return;
  cart = []; renderCart(); recalc();
}

// ============================================================
// الإجماليات
// ============================================================
function recalc() {
  const sub  = cart.reduce((s, i) => s + parseFloat(i.sale_price) * i.qty, 0);
  const dv   = parseFloat(document.getElementById('discount-val').value) || 0;
  const dt   = document.getElementById('discount-type').value;
  const disc = dt === 'percent' ? sub * dv / 100 : Math.min(dv, sub);
  const aft  = sub - disc;
  const tax  = TAX_ENABLED ? aft * TAX_RATE / 100 : 0;
  const tot  = aft + tax;

  document.getElementById('s-subtotal').textContent = fmt(sub);
  document.getElementById('s-discount').textContent = '−' + fmt(disc);
  document.getElementById('discount-row').style.display = disc > 0 ? 'flex' : 'none';
  const taxEl = document.getElementById('s-tax');
  if (taxEl) taxEl.textContent = fmt(tax);
  document.getElementById('s-total').innerHTML = `<strong>${fmt(tot)}</strong>`;

  const paidEl = document.getElementById('paid-amount');
  if (paidEl && selectedPM === 'cash') paidEl.value = tot.toFixed(2);
  calcChange();
}

function calcChange() {
  const totText = document.getElementById('s-total').innerText || '0';
  const tot  = parseFloat(totText.replace(/[^\d.]/g, '')) || 0;
  const paid = parseFloat(document.getElementById('paid-amount')?.value) || 0;
  const chg  = Math.max(0, paid - tot);
  const el   = document.getElementById('change-amount');
  if (el) el.textContent = fmt(chg);
  const row  = document.getElementById('change-row');
  if (row) row.style.display = paid > 0 ? 'flex' : 'none';
}

function fmt(n) { return parseFloat(n).toFixed(2) + ' ' + CURRENCY; }

function selectPM(btn) {
  document.querySelectorAll('.pm-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selectedPM = btn.dataset.method;
  const ps = document.getElementById('paid-section');
  if (ps) ps.style.display = selectedPM === 'cash' ? 'block' : 'none';
}

function onCustomerChange(sel) {
  const balance = parseFloat(sel.options[sel.selectedIndex]?.dataset.balance || 0);
  const warn    = document.getElementById('customer-debt-warn');
  const txt     = document.getElementById('customer-debt-text');
  if (warn && txt) {
    if (balance > 0) {
      txt.textContent = `لدى هذا العميل دين مستحق: ${balance.toFixed(2)} ${CURRENCY}`;
      warn.style.display = 'flex';
    } else {
      warn.style.display = 'none';
    }
  }
}

// ============================================================
// إتمام البيع
// ============================================================
async function processSale() {
  if (!cart.length) { showToast('السلة فارغة!', 'error'); return; }

  // تحقق أخير: لا منتجات منتهية الصلاحية
  const expItem = cart.find(i => isExpired(daysLeft(i.expiry_date)));
  if (expItem) {
    showExpiredModal(expItem.name, expItem.expiry_date, Math.abs(daysLeft(expItem.expiry_date)));
    return;
  }

  const btn = document.getElementById('pay-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="ti ti-loader ti-spin"></i> <span>جاري المعالجة...</span>';

  const totText = document.getElementById('s-total').innerText || '0';
  const tot     = parseFloat(totText.replace(/[^\d.]/g, '')) || 0;
  const paid    = parseFloat(document.getElementById('paid-amount')?.value) || tot;

  try {
    const res  = await fetch('?page=pos&action=saveInvoice', {
      method:  'POST',
      headers: { 'Content-Type': 'application/json' },
      body:    JSON.stringify({
        items:          cart.map(i => ({ product_id: i.id, qty: i.qty, price: i.sale_price })),
        customer_id:    document.getElementById('customer-select').value || null,
        payment_method: selectedPM,
        discount_value: parseFloat(document.getElementById('discount-val').value) || 0,
        discount_type:  document.getElementById('discount-type').value,
        paid_amount:    paid,
      })
    });
    const data = await res.json();

    if (data.success) {
      lastInvoiceId = data.invoice_id;
      document.getElementById('success-info').innerHTML = `
        <p><span>رقم الفاتورة</span><strong>${data.invoice_number}</strong></p>
        <p><span>الإجمالي</span><strong>${parseFloat(data.total).toFixed(2)} ${CURRENCY}</strong></p>
        <p><span>طريقة الدفع</span><strong>${{cash:'نقدي',card:'بطاقة',transfer:'تحويل',credit:'آجل'}[selectedPM]||''}</strong></p>
        ${data.change > 0 ? `<p><span>الباقي للعميل</span><strong>${parseFloat(data.change).toFixed(2)} ${CURRENCY}</strong></p>` : ''}`;
      document.getElementById('success-modal').style.display = 'flex';
    } else {
      showToast(data.message || 'حدث خطأ أثناء الحفظ', 'error');
    }
  } catch(e) {
    showToast('خطأ في الاتصال بالخادم', 'error');
  }

  btn.disabled = false;
  btn.innerHTML = '<i class="ti ti-check"></i> <span>إتمام البيع</span>';
}

function printAndClose() {
  window.open(`?page=pos&action=printInvoice&id=${lastInvoiceId}`, '_blank');
  newSale();
}
function newSale() {
  document.getElementById('success-modal').style.display = 'none';
  cart = []; renderCart(); recalc();
  loadProducts(0);
  document.getElementById('discount-val').value = '';
  document.getElementById('customer-select').value = '';
  document.getElementById('customer-debt-warn').style.display = 'none';
}
function holdInvoice() { showToast('تم تعليق الفاتورة', 'info'); }

// ============================================================
// Helpers
// ============================================================
function escHtml(s) {
  return String(s||'').replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escJs(s) {
  return String(s||'').replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"');
}

// ← هزّة للـ Modal
const shakeStyle = document.createElement('style');
shakeStyle.textContent = `@keyframes shake{0%,100%{transform:translateX(0)}20%,60%{transform:translateX(-8px)}40%,80%{transform:translateX(8px)}}`;
document.head.appendChild(shakeStyle);

// ← F3 للتركيز على البحث
document.addEventListener('keydown', e => {
  if (e.key === 'F3') { e.preventDefault(); document.getElementById('pos-search').focus(); }
});
</script>
