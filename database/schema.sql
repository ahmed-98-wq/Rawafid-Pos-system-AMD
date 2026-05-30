-- ================================================
-- نظام المبيعات - قاعدة البيانات الكاملة
-- ================================================

CREATE DATABASE IF NOT EXISTS `pos_system`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `pos_system`;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(100) UNIQUE NOT NULL,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) UNIQUE NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin','cashier','warehouse','supervisor') DEFAULT 'cashier',
  `email` VARCHAR(100),
  `phone` VARCHAR(20),
  `is_active` TINYINT(1) DEFAULT 1,
  `last_login` TIMESTAMP NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `parent_id` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(200) NOT NULL,
  `barcode` VARCHAR(50) UNIQUE,
  `category_id` INT NULL,
  `unit` VARCHAR(50) DEFAULT 'قطعة',
  `purchase_price` DECIMAL(12,2) DEFAULT 0,
  `sale_price` DECIMAL(12,2) NOT NULL,
  `stock_qty` DECIMAL(12,3) DEFAULT 0,
  `min_stock_alert` DECIMAL(12,3) DEFAULT 5,
  `expiry_date` DATE NULL,
  `image` VARCHAR(255),
  `description` TEXT,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_by` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_barcode` (`barcode`),
  INDEX `idx_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `customers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT,
  `notes` TEXT,
  `credit_limit` DECIMAL(12,2) DEFAULT 0,
  `balance` DECIMAL(12,2) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `phone` VARCHAR(20),
  `email` VARCHAR(100),
  `address` TEXT,
  `notes` TEXT,
  `balance` DECIMAL(12,2) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoices` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_number` VARCHAR(20) UNIQUE NOT NULL,
  `type` ENUM('sale','return','purchase') DEFAULT 'sale',
  `customer_id` INT NULL,
  `user_id` INT NOT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `discount_type` ENUM('fixed','percent') DEFAULT 'fixed',
  `discount_value` DECIMAL(12,2) DEFAULT 0,
  `discount_amount` DECIMAL(12,2) DEFAULT 0,
  `tax_rate` DECIMAL(5,2) DEFAULT 15,
  `tax_amount` DECIMAL(12,2) DEFAULT 0,
  `total` DECIMAL(12,2) NOT NULL,
  `paid_amount` DECIMAL(12,2) DEFAULT 0,
  `change_amount` DECIMAL(12,2) DEFAULT 0,
  `payment_method` ENUM('cash','card','transfer','credit') DEFAULT 'cash',
  `status` ENUM('paid','pending','partial','returned') DEFAULT 'paid',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_invoice_number` (`invoice_number`),
  INDEX `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `invoice_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `invoice_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(200) NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `purchase_price` DECIMAL(12,2) DEFAULT 0,
  `discount_percent` DECIMAL(5,2) DEFAULT 0,
  `total` DECIMAL(12,2) NOT NULL,
  INDEX `idx_invoice` (`invoice_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `type` ENUM('in','out','return','adjustment') NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL,
  `before_qty` DECIMAL(12,3),
  `after_qty` DECIMAL(12,3),
  `reference_type` VARCHAR(50),
  `reference_id` INT,
  `notes` TEXT,
  `user_id` INT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX `idx_product` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchases` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_number` VARCHAR(20) UNIQUE NOT NULL,
  `supplier_id` INT NULL,
  `user_id` INT NOT NULL,
  `total` DECIMAL(12,2) NOT NULL,
  `paid_amount` DECIMAL(12,2) DEFAULT 0,
  `status` ENUM('paid','pending','partial') DEFAULT 'paid',
  `notes` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `purchase_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `purchase_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `quantity` DECIMAL(12,3) NOT NULL,
  `unit_price` DECIMAL(12,2) NOT NULL,
  `total` DECIMAL(12,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

-- ================================================
-- الإعدادات الافتراضية
-- ================================================
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('business_name','نظام المبيعات'),('business_type','pharmacy'),
('currency','ج.س'),('currency_code','SDG'),('tax_rate','15'),
('tax_enabled','1'),('low_stock_alert','10'),('invoice_prefix','INV'),
('invoice_counter','1'),('receipt_footer','شكراً لتعاملكم معنا'),('backup_auto','1')
ON DUPLICATE KEY UPDATE `setting_value` = VALUES(`setting_value`);

-- ================================================
-- مستخدم admin — كلمة المرور: admin123
-- ================================================
INSERT INTO `users` (`username`,`password`,`full_name`,`role`,`is_active`) VALUES (
  'admin',
  '$2y$10$TKh8H1.PfQ0A32tXbCDJLOAGK6TDAP.y9TCayEuoTDt0sbfALmZDS',
  'مدير النظام','admin',1
) ON DUPLICATE KEY UPDATE
  `password`='$2y$10$TKh8H1.PfQ0A32tXbCDJLOAGK6TDAP.y9TCayEuoTDt0sbfALmZDS',
  `is_active`=1;

-- تصنيفات
INSERT IGNORE INTO `categories` (`id`,`name`) VALUES
(1,'أدوية'),(2,'مكملات غذائية'),(3,'معقمات'),(4,'مستلزمات طبية');

-- منتجات تجريبية
INSERT IGNORE INTO `products` (`name`,`barcode`,`category_id`,`unit`,`purchase_price`,`sale_price`,`stock_qty`,`min_stock_alert`) VALUES
('باراسيتامول 500mg','6001001',1,'علبة',10,15,50,10),
('فيتامين سي 1000','6001003',2,'علبة',30,45,25,5),
('أوميغا 3','6001004',2,'علبة',80,120,40,10),
('ماء أكسجيني','6001005',3,'زجاجة',5,8,100,20);

-- عميل نقدي
INSERT IGNORE INTO `customers` (`id`,`name`,`phone`) VALUES (1,'عميل نقدي','0000000000');
