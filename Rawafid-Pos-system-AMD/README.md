# نظام المبيعات والإدارة المتكامل
## دليل التثبيت والتشغيل

---

## المتطلبات
- XAMPP (PHP 8.0+، MySQL 5.7+)
- متصفح حديث (Chrome، Firefox، Edge)
- نظام Windows / Mac / Linux

---

## خطوات التثبيت

### 1. نسخ المشروع
انسخ مجلد `pos_system` إلى:
```
C:\xampp\htdocs\pos_system
```

### 2. تشغيل XAMPP
افتح XAMPP Control Panel وشغّل:
- ✅ Apache
- ✅ MySQL

### 3. إنشاء قاعدة البيانات
افتح المتصفح واذهب إلى:
```
http://localhost/phpmyadmin
```
ثم:
1. اضغط **New** لإنشاء قاعدة بيانات جديدة
2. اكتب الاسم: `pos_system`
3. اختر `utf8mb4_unicode_ci`
4. اضغط **Create**
5. انقر فوق قاعدة البيانات الجديدة
6. اختر تبويب **Import**
7. اختر الملف: `database/schema.sql`
8. اضغط **Go**

### 4. إعداد المستخدم الافتراضي
افتح phpMyAdmin وشغّل هذا الاستعلام في قاعدة `pos_system`:

```sql
INSERT INTO users (username, password, full_name, role, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'مدير النظام',
    'admin',
    1
);
```
> كلمة المرور الافتراضية: `password`

أو شغّل ملف `database/create_admin.sql` المرفق.

### 5. تشغيل النظام
افتح المتصفح واذهب إلى:
```
http://localhost/pos_system
```

---

## بيانات الدخول الافتراضية
| الحقل | القيمة |
|-------|--------|
| اسم المستخدم | `admin` |
| كلمة المرور | `password` |

> **مهم:** غيّر كلمة المرور فور تسجيل الدخول الأول من الإعدادات

---

## هيكل المشروع
```
pos_system/
├── index.php              # نقطة الدخول الرئيسية (Router)
├── config/
│   ├── config.php         # إعدادات النظام
│   └── Database.php       # اتصال قاعدة البيانات
├── app/
│   ├── Controllers/       # المتحكمات
│   │   ├── AuthController.php
│   │   ├── DashboardController.php
│   │   ├── PosController.php
│   │   ├── ProductController.php
│   │   ├── CustomerController.php
│   │   ├── SupplierController.php
│   │   ├── ReportController.php
│   │   ├── InventoryController.php
│   │   ├── SettingsController.php
│   │   ├── UserController.php
│   │   └── PurchaseController.php
│   ├── Views/             # الواجهات
│   │   ├── layouts/main.php
│   │   ├── auth/login.php
│   │   ├── dashboard/index.php
│   │   ├── pos/index.php
│   │   ├── pos/print.php
│   │   ├── products/index.php
│   │   ├── products/form.php
│   │   ├── customers/index.php
│   │   ├── customers/statement.php
│   │   ├── suppliers/index.php
│   │   ├── reports/index.php
│   │   ├── inventory/index.php
│   │   ├── settings/index.php
│   │   ├── users/index.php
│   │   └── purchases/index.php
│   └── Helpers/
│       ├── Auth.php
│       ├── Session.php
│       └── Helper.php
├── assets/
│   ├── css/app.css        # الأنماط الرئيسية
│   ├── js/app.js          # JavaScript الرئيسي
│   ├── fonts/             # الخطوط المحلية
│   └── icons/             # Tabler Icons المحلية
├── database/
│   ├── schema.sql         # هيكل قاعدة البيانات
│   └── create_admin.sql   # إنشاء مستخدم admin
├── uploads/               # الصور المرفوعة
└── README.md              # هذا الملف
```

---

## تحميل Tabler Icons محلياً
1. اذهب إلى: https://tabler.io/icons
2. حمّل الحزمة
3. انسخ ملف `tabler-icons.min.css` إلى `assets/css/`
4. انسخ مجلد الخطوط إلى `assets/fonts/`

أو استخدم CDN مؤقتاً بإضافة في `<head>`:
```html
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
```

---

## الأدوار والصلاحيات
| الدور | الصلاحيات |
|-------|-----------|
| admin | كامل الصلاحيات |
| supervisor | مشرف - جميع العمليات ما عدا إدارة المستخدمين |
| cashier | البيع والعملاء فقط |
| warehouse | المنتجات والمخزون والمشتريات |

---

## الميزات الرئيسية
- ✅ لوحة تحكم مع إحصائيات وتحليلات
- ✅ نقطة بيع سريعة (POS) مع دعم الباركود
- ✅ إدارة منتجات مع صور وتصنيفات
- ✅ تنبيهات المخزون وتواريخ الانتهاء
- ✅ إدارة العملاء وكشف الحساب
- ✅ إدارة الموردين والمشتريات
- ✅ تقارير مبيعات مع رسوم بيانية
- ✅ تصدير CSV للتقارير
- ✅ طباعة فواتير حرارية
- ✅ نظام صلاحيات متعدد الأدوار
- ✅ الوضع الليلي والنهاري
- ✅ نسخ احتياطي واستعادة قاعدة البيانات
- ✅ يعمل 100% بدون إنترنت

---

## الدعم والتطوير
النظام مبني بـ:
- **PHP 8+** — Backend
- **MySQL** — قاعدة البيانات
- **CSS مخصص** — تصميم زيتي احترافي
- **JavaScript Vanilla** — بدون frameworks خارجية
- **Tabler Icons** — أيقونات محلية
