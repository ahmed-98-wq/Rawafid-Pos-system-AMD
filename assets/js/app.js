// ================================================
// نظام المبيعات — JavaScript الرئيسي
// ================================================

document.addEventListener('DOMContentLoaded', function () {

  // ---- إخفاء Loader بعد تحميل الصفحة ----
  const loader = document.getElementById('page-loader');
  if (loader) {
    loader.classList.add('hidden');          // إخفاء فوري
    setTimeout(function() {
      loader.style.display = 'none';        // إزالة كاملة
    }, 600);
  }

  // ---- تطبيق الوضع المحفوظ ----
  const savedTheme = localStorage.getItem('pos_theme') || 'light';
  applyTheme(savedTheme);

  // ---- إخفاء رسائل التنبيه تلقائياً ----
  document.querySelectorAll('.alert').forEach(function(el) {
    setTimeout(function() {
      el.style.transition = 'opacity .5s';
      el.style.opacity = '0';
      setTimeout(function() { el.remove(); }, 500);
    }, 4000);
  });

  closeAll();

});

// ================================================================
// Dark Mode
// ================================================================
function toggleTheme() {
  var current = localStorage.getItem('pos_theme') || 'light';
  var next    = current === 'light' ? 'dark' : 'light';
  applyTheme(next);
  localStorage.setItem('pos_theme', next);
}

function applyTheme(theme) {
  if (theme === 'dark') {
    document.documentElement.setAttribute('data-theme', 'dark');
  } else {
    document.documentElement.removeAttribute('data-theme');
  }
  var icon = document.getElementById('theme-icon');
  if (icon) {
    icon.className = theme === 'dark' ? 'ti ti-sun' : 'ti ti-moon';
  }
}

// ================================================================
// Sidebar
// ================================================================
function toggleSidebar() {
  var sidebar = document.getElementById('sidebar');
  var overlay = document.getElementById('overlay');
  if (!sidebar) return;
  sidebar.classList.toggle('open');
  if (overlay) overlay.classList.toggle('show');
}

// ================================================================
// Notifications
// ================================================================
function toggleNotifications() {
  var panel   = document.getElementById('notif-panel');
  var overlay = document.getElementById('overlay');
  if (!panel) return;
  var isOpen = panel.classList.contains('open');
  closeAll();
  if (!isOpen) {
    panel.classList.add('open');
    if (overlay) overlay.classList.add('show');
  }
}

function closeAll() {
  var panel = document.getElementById('notif-panel');
  if (panel) panel.classList.remove('open');
  var overlay = document.getElementById('overlay');
  if (overlay) overlay.classList.remove('show');
  // إغلاق Sidebar في الشاشات الصغيرة
  if (window.innerWidth < 900) {
    var sidebar = document.getElementById('sidebar');
    if (sidebar) sidebar.classList.remove('open');
  }
}

// ================================================================
// Toast Notifications
// ================================================================
function showToast(message, type, duration) {
  type     = type     || 'info';
  duration = duration || 3500;
  var container = document.getElementById('toast-container');
  if (!container) return;
  var icons = { success:'check', error:'x', warning:'alert-triangle', info:'info-circle' };
  var toast = document.createElement('div');
  toast.className = 'toast toast-' + type;
  toast.innerHTML = '<i class="ti ti-' + (icons[type]||'info-circle') + '"></i> ' + message;
  container.appendChild(toast);
  setTimeout(function() { toast.classList.add('show'); }, 20);
  setTimeout(function() {
    toast.classList.remove('show');
    setTimeout(function() { toast.remove(); }, 350);
  }, duration);
}

// ================================================================
// Modal Helpers
// ================================================================
function openModal(id) {
  var m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}
function closeModal(id) {
  var m = document.getElementById(id);
  if (m) m.style.display = 'none';
}

// إغلاق Modal بالنقر على الخلفية
document.addEventListener('click', function(e) {
  if (e.target && e.target.classList && e.target.classList.contains('modal-overlay')) {
    e.target.style.display = 'none';
  }
});

// ================================================================
// Keyboard Shortcuts
// ================================================================
document.addEventListener('keydown', function(e) {
  if (e.key === 'Escape') {
    document.querySelectorAll('.modal-overlay').forEach(function(m) {
      m.style.display = 'none';
    });
    closeAll();
  }
  if (e.key === 'F1') { e.preventDefault(); window.location.href = '?page=pos'; }
  if (e.key === 'F2') { e.preventDefault(); window.location.href = '?page=dashboard'; }
});

// ================================================================
// Export Table to CSV
// ================================================================
function exportTableCSV(tableId, filename) {
  filename = filename || 'export.csv';
  var table = document.getElementById(tableId);
  if (!table) return;
  var rows = Array.from(table.querySelectorAll('tr'));
  var csv  = rows.map(function(row) {
    return Array.from(row.querySelectorAll('th, td'))
      .map(function(cell) { return '"' + cell.innerText.replace(/"/g, '""') + '"'; })
      .join(',');
  }).join('\n');
  var bom  = '\uFEFF';
  var blob = new Blob([bom + csv], { type: 'text/csv;charset=utf-8;' });
  var link = document.createElement('a');
  link.href     = URL.createObjectURL(blob);
  link.download = filename;
  link.click();
}
