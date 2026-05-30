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
        <button id="pos-search-clear" onclick="clearSearch()" class="is-hidden">
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
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-alert-octagon" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 9v4" />
  <path d="M12 17h.01" />
  <path d="M9.172 3h5.656a2 2 0 0 1 1.414 .586l4.172 4.172a2 2 0 0 1 .586 1.414v5.656a2 2 0 0 1 -.586 1.414l-4.172 4.172a2 2 0 0 1 -1.414 .586h-5.656a2 2 0 0 1 -1.414 -.586l-4.172 -4.172a2 2 0 0 1 -.586 -1.414v-5.656a2 2 0 0 1 .586 -1.414l4.172 -4.172a2 2 0 0 1 1.414 -.586z" />
</svg>
        <!-- <i class="ti ti-alert-octagon"></i> -->
        <span class="pea-count red"><?= $expiredCount ?> منتهي</span>
        <?php endif; ?>
        <?php if ($soonCount > 0): ?>
          <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-clock u-style-66" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M12 12m-9 0a9 9 0 1 0 18 0a9 9 0 1 0 -18 0" />
  <path d="M12 7v5l3 3" />
</svg>
        <!-- <i class="ti ti-clock u-style-66"></i> -->
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
        <span class="cart-count-badge is-hidden" id="cart-count">0</span>
      </div>
      <div class="flex gap-1">
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
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
    <div id="customer-debt-warn" class="customer-debt-warn is-hidden">
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
      <div class="ct-row discount-ct is-hidden" id="discount-row">
        <span>الخصم</span><span id="s-discount" class="u-style-67"></span>
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
               value="0.00" placeholder="0.00"
               onfocus="clearPaidDefault(this)"
               onblur="restorePaidDefault(this)"
               oninput="calcChange()" class="paid-inp">
      </div>
      <div class="change-row is-hidden" id="change-row">
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
<div class="expiry-panel is-hidden" id="expiry-panel">
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
    <div class="u-style-68">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-check" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M5 12l5 5l10 -10" />
</svg>
      <!-- <i class="ti ti-circle-check u-style-69"></i> -->
      جميع المنتجات في حالة جيدة
    </div>
    <?php endif; ?>
  </div>
</div>
<div class="ep-overlay" id="ep-overlay" onclick="toggleExpiryPanel()"></div>

<!-- ===================== Modal: نجاح البيع ===================== -->
<div class="modal-overlay is-hidden" id="success-modal">
  <div class="modal-box u-style-70">
    <div class="u-style-71">✅</div>
    <h3 class="u-style-72">تمت عملية البيع بنجاح</h3>
    <div id="success-info" class="success-info-box"></div>
    <div class="u-style-73">
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
<div class="modal-overlay is-hidden" id="expired-modal">
  <div class="modal-box u-style-74">
    <div class="u-style-75">⛔</div>
    <h3 class="u-style-76">منتج منتهي الصلاحية!</h3>
    <p id="expired-msg" class="u-style-77"></p>
    <div class="u-style-78">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-info-circle" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M3 12a9 9 0 1 0 18 0a9 9 0 0 0 -18 0" />
  <path d="M12 9h.01" />
  <path d="M11 12h1v4h1" />
</svg>
      لا يمكن بيع منتجات منتهية الصلاحية حفاظاً على سلامة العملاء
    </div>
    <button onclick="document.getElementById('expired-modal').style.display='none'"
            class="btn btn-danger u-style-79">
      <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
  <path stroke="none" d="M0 0h24v24H0z" fill="none"/>
  <path d="M18 6l-12 12" />
  <path d="M6 6l12 12" />
</svg> حسناً، لن أضيفه
    </button>
  </div>
</div>

<!-- ===================== Styles ===================== -->

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
      '<div class="u-style-80">فشل تحميل المنتجات</div>';
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
  if (!bar) return;
  const allButton = bar.querySelector('.pcat-btn');
  if (!allButton) return;
  allButton.classList.add('active');
  bar.replaceChildren(allButton);

  cats.forEach(cat => {
    const b = document.createElement('button');
    b.className   = 'pcat-btn';
    b.innerHTML   = `<svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-tag" width="14" height="14" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
    g.innerHTML = `<div class="u-style-81">
      <i class="ti ti-search-off u-style-82"></i>
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
      : `<div class="ptile-placeholder"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-package" width="18" height="18" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
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
  <div class="ptile-img ${badgeHtml ? 'ptile-img-offset' : ''}">${imgHtml}</div>
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
  if (paidEl && selectedPM === 'cash' && !paidEl.matches(':focus') && !paidEl.dataset.touched) {
    paidEl.value = '0.00';
  }
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

function clearPaidDefault(input) {
  if (!input) return;
  if (!input.dataset.touched && (input.value === '0.00' || input.value === '0')) {
    input.value = '';
  }
}

function restorePaidDefault(input) {
  if (!input) return;
  if (input.value === '') {
    input.dataset.touched = '';
    input.value = '0.00';
  } else {
    input.dataset.touched = '1';
  }
  calcChange();
}

function fmt(n) { return parseFloat(n).toFixed(2) + ' ' + CURRENCY; }

function selectPM(btn) {
  document.querySelectorAll('.pm-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  selectedPM = btn.dataset.method;
  const ps = document.getElementById('paid-section');
  if (ps) ps.style.display = selectedPM === 'cash' ? 'block' : 'none';
  restorePaidDefault(document.getElementById('paid-amount'));
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
  const paidInput = document.getElementById('paid-amount');
  const paidValue = parseFloat(paidInput?.value);
  const paid    = selectedPM === 'cash' ? (Number.isFinite(paidValue) ? paidValue : 0) : tot;

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
  const paidEl = document.getElementById('paid-amount');
  if (paidEl) {
    paidEl.dataset.touched = '';
    paidEl.value = '0.00';
  }
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
