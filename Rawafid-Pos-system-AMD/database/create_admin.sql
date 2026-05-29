-- إنشاء مستخدم admin الافتراضي
-- كلمة المرور: password
USE pos_system;

INSERT INTO users (username, password, full_name, role, is_active)
VALUES (
    'admin',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'مدير النظام',
    'admin',
    1
) ON DUPLICATE KEY UPDATE
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    is_active = 1;

-- كاشير تجريبي (كلمة المرور: password)
INSERT INTO users (username, password, full_name, role, is_active)
VALUES (
    'cashier1',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'كاشير 1',
    'cashier',
    1
) ON DUPLICATE KEY UPDATE username = username;

-- منتجات تجريبية
INSERT IGNORE INTO categories (id, name) VALUES
(1, 'أدوية'), (2, 'مكملات غذائية'), (3, 'معقمات'), (4, 'مستلزمات طبية');

INSERT IGNORE INTO products (name, barcode, category_id, unit, purchase_price, sale_price, stock_qty, min_stock_alert)
VALUES
('باراسيتامول 500mg', '6001001', 1, 'علبة', 10.00, 15.00, 50, 10),
('أمبروكسول 30mg',   '6001002', 1, 'علبة', 18.00, 28.00, 30, 5),
('فيتامين سي 1000',  '6001003', 2, 'علبة', 30.00, 45.00, 25, 5),
('أوميغا 3',         '6001004', 2, 'علبة', 80.00, 120.00, 40, 10),
('ماء أكسجيني',      '6001005', 3, 'زجاجة', 5.00, 8.00, 100, 20),
('كريم البشرة',      '6001006', 4, 'أنبوب', 40.00, 65.00, 20, 5);
