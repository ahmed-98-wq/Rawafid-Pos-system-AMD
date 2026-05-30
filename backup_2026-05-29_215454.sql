-- POS System Backup
-- 2026-05-29 21:54:54
SET NAMES utf8mb4;

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) DEFAULT NULL,
  `module` varchar(50) DEFAULT NULL,
  `details` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `categories_ibfk_1` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` VALUES ('1', 'أدوية', 'الأدوية والمستحضرات الصيدلانية', NULL, '2026-05-16 11:23:17');
INSERT INTO `categories` VALUES ('2', 'مكملات غذائية', 'الفيتامينات والمكملات', NULL, '2026-05-16 11:23:17');
INSERT INTO `categories` VALUES ('3', 'معقمات ومطهرات', 'مواد التعقيم والتطهير', NULL, '2026-05-16 11:23:17');
INSERT INTO `categories` VALUES ('4', 'مستلزمات طبية', 'المستلزمات والأجهزة الطبية', NULL, '2026-05-16 11:23:17');

DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `credit_limit` decimal(12,2) DEFAULT 0.00,
  `balance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_phone` (`phone`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `customers` VALUES ('1', 'عميل نقدي1', '0926311330', 'mogotigchic@gmail.com', 'نيالا', 'عميل VIP', '50000.00', '0.00', '2026-05-16 11:23:17');

DROP TABLE IF EXISTS `invoice_items`;
CREATE TABLE `invoice_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `purchase_price` decimal(12,2) DEFAULT 0.00,
  `discount_percent` decimal(5,2) DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `product_id` (`product_id`),
  KEY `idx_invoice` (`invoice_id`),
  CONSTRAINT `invoice_items_ibfk_1` FOREIGN KEY (`invoice_id`) REFERENCES `invoices` (`id`) ON DELETE CASCADE,
  CONSTRAINT `invoice_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoice_items` VALUES ('1', '1', '1', 'امكسيسيلين', '10.000', '2000.00', '1200.00', '0.00', '20000.00');
INSERT INTO `invoice_items` VALUES ('2', '2', '1', 'امكسيسيلين', '10.000', '2000.00', '1200.00', '0.00', '20000.00');
INSERT INTO `invoice_items` VALUES ('3', '3', '2', 'اموكلان', '3.000', '3000.00', '2000.00', '0.00', '9000.00');
INSERT INTO `invoice_items` VALUES ('4', '4', '3', 'جنتمايسين', '2.000', '2200.00', '1000.00', '0.00', '4400.00');
INSERT INTO `invoice_items` VALUES ('5', '5', '5', 'Amiprazole', '3.000', '6000.00', '3000.00', '0.00', '18000.00');
INSERT INTO `invoice_items` VALUES ('6', '6', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('7', '7', '5', 'Amiprazole', '2.000', '6000.00', '3000.00', '0.00', '12000.00');
INSERT INTO `invoice_items` VALUES ('8', '8', '6', 'ringer drip', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('9', '8', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('10', '9', '2', 'اموكلان', '2.000', '3000.00', '2000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('11', '10', '3', 'جنتمايسين', '8.000', '2200.00', '1000.00', '0.00', '17600.00');
INSERT INTO `invoice_items` VALUES ('12', '11', '1', 'امكسيسيلين', '3.000', '2000.00', '1200.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('13', '12', '6', 'ringer drip', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('14', '12', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('15', '13', '2', 'اموكلان', '1.000', '3000.00', '2000.00', '0.00', '3000.00');
INSERT INTO `invoice_items` VALUES ('16', '14', '4', 'Ketoconazole', '2.000', '10000.00', '6000.00', '0.00', '20000.00');
INSERT INTO `invoice_items` VALUES ('17', '15', '6', 'ringer drip', '2.000', '5000.00', '1500.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('18', '16', '2', 'اموكلان', '2.000', '3000.00', '2000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('19', '17', '2', 'اموكلان', '1.000', '3000.00', '2000.00', '0.00', '3000.00');
INSERT INTO `invoice_items` VALUES ('20', '18', '2', 'اموكلان', '1.000', '3000.00', '2000.00', '0.00', '3000.00');
INSERT INTO `invoice_items` VALUES ('21', '19', '2', 'اموكلان', '1.000', '3000.00', '2000.00', '0.00', '3000.00');
INSERT INTO `invoice_items` VALUES ('22', '19', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('23', '20', '6', 'ringer drip', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('24', '21', '2', 'اموكلان', '5.000', '3000.00', '2000.00', '0.00', '15000.00');
INSERT INTO `invoice_items` VALUES ('25', '22', '1', 'امكسيسيلين', '3.000', '2000.00', '1200.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('26', '23', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '3.000', '6500.00', '3000.00', '0.00', '19500.00');
INSERT INTO `invoice_items` VALUES ('27', '23', '4', 'Ketoconazole', '2.000', '10000.00', '6000.00', '0.00', '20000.00');
INSERT INTO `invoice_items` VALUES ('28', '23', '5', 'Amiprazole', '2.000', '6000.00', '3000.00', '0.00', '12000.00');
INSERT INTO `invoice_items` VALUES ('29', '23', '8', '7 Furosemide(Frusemide) 40mg Tablet', '2.000', '5000.00', '2000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('30', '23', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('31', '23', '6', 'ringer drip', '2.000', '5000.00', '1500.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('32', '24', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('33', '24', '11', 'Carvedilol', '1.000', '6000.00', '1500.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('34', '24', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('35', '24', '6', 'ringer drip', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('36', '24', '10', 'Spironolactone', '1.000', '8500.00', '4000.00', '0.00', '8500.00');
INSERT INTO `invoice_items` VALUES ('37', '25', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('38', '26', '11', 'Carvedilol', '1.000', '6000.00', '1500.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('39', '26', '8', '7 Furosemide(Frusemide) 40mg Tablet', '1.000', '5000.00', '2000.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('40', '27', '12', 'Propranolol hydrochloride', '1.000', '15000.00', '7000.00', '0.00', '15000.00');
INSERT INTO `invoice_items` VALUES ('41', '28', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('42', '29', '8', '7 Furosemide(Frusemide) 40mg Tablet', '1.000', '5000.00', '2000.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('43', '30', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('44', '31', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '5.000', '6500.00', '3000.00', '0.00', '32500.00');
INSERT INTO `invoice_items` VALUES ('45', '32', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '7.000', '6500.00', '3000.00', '0.00', '45500.00');
INSERT INTO `invoice_items` VALUES ('46', '33', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '4.000', '6500.00', '3000.00', '0.00', '26000.00');
INSERT INTO `invoice_items` VALUES ('47', '34', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('48', '35', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('49', '35', '10', 'Spironolactone', '1.000', '8500.00', '4000.00', '0.00', '8500.00');
INSERT INTO `invoice_items` VALUES ('50', '36', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('51', '36', '6', 'ringer drip', '2.000', '5000.00', '1500.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('52', '37', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('53', '38', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('54', '39', '12', 'Propranolol hydrochloride', '2.000', '15000.00', '7000.00', '0.00', '30000.00');
INSERT INTO `invoice_items` VALUES ('55', '39', '13', 'Mogo T', '1.000', '2000.00', '1000.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('56', '40', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('57', '40', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('58', '41', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('59', '41', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('60', '42', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('61', '42', '11', 'Carvedilol', '1.000', '6000.00', '1500.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('62', '42', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('63', '42', '8', '7 Furosemide(Frusemide) 40mg Tablet', '1.000', '5000.00', '2000.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('64', '42', '9', 'Hydrochlorothiazide 50mg scroed Tablet', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('65', '43', '8', '7 Furosemide(Frusemide) 40mg Tablet', '1.000', '5000.00', '2000.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('66', '43', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');
INSERT INTO `invoice_items` VALUES ('67', '44', '10', 'Spironolactone', '1.000', '8500.00', '4000.00', '0.00', '8500.00');
INSERT INTO `invoice_items` VALUES ('68', '44', '18', 'Shalspirin CV', '1.000', '7000.00', '3000.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('69', '44', '24', 'OMEGA-3 (1000mg)', '1.000', '8000.00', '3500.00', '0.00', '8000.00');
INSERT INTO `invoice_items` VALUES ('70', '45', '26', 'Clotrimazole', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('71', '45', '19', 'Climepiride', '1.000', '4000.00', '2000.00', '0.00', '4000.00');
INSERT INTO `invoice_items` VALUES ('72', '45', '22', 'CEFIUM 400mg', '1.000', '6000.00', '2000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('73', '46', '6', 'ringer drip', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('74', '46', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('75', '46', '18', 'Shalspirin CV', '1.000', '7000.00', '3000.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('76', '46', '10', 'Spironolactone', '1.000', '8500.00', '4000.00', '0.00', '8500.00');
INSERT INTO `invoice_items` VALUES ('77', '46', '30', 'Voltanext', '1.000', '4999.96', '1500.00', '0.00', '4999.96');
INSERT INTO `invoice_items` VALUES ('78', '47', '31', 'zinconext', '1.000', '4000.00', '1500.00', '0.00', '4000.00');
INSERT INTO `invoice_items` VALUES ('79', '48', '1', 'امكسيسيلين', '1.000', '2000.00', '1200.00', '0.00', '2000.00');
INSERT INTO `invoice_items` VALUES ('80', '49', '17', 'Zinnia F', '1.000', '5500.00', '2000.00', '0.00', '5500.00');
INSERT INTO `invoice_items` VALUES ('81', '50', '21', 'Dydrogesterone', '1.000', '4000.00', '1000.00', '0.00', '4000.00');
INSERT INTO `invoice_items` VALUES ('82', '51', '18', 'Shalspirin CV', '1.000', '7000.00', '3000.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('83', '52', '1', 'امكسيسيلين', '3.000', '2000.00', '1200.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('84', '53', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('85', '54', '26', 'Clotrimazole', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('86', '55', '26', 'Clotrimazole', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('87', '56', '22', 'CEFIUM 400mg', '1.000', '6000.00', '2000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('88', '57', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('89', '58', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('90', '58', '18', 'Shalspirin CV', '1.000', '7000.00', '3000.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('91', '59', '8', 'Furosemide', '1.000', '5000.00', '2000.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('92', '59', '23', 'ZEcuf 100ml', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('93', '60', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('94', '61', '27', 'Snake Venom Antiserum IP', '1.000', '5500.00', '2000.00', '0.00', '5500.00');
INSERT INTO `invoice_items` VALUES ('95', '62', '20', 'Itraconazole', '1.000', '3500.00', '1500.00', '0.00', '3500.00');
INSERT INTO `invoice_items` VALUES ('96', '62', '23', 'ZEcuf 100ml', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('97', '63', '24', 'OMEGA-3 (1000mg)', '1.000', '8000.00', '3500.00', '0.00', '8000.00');
INSERT INTO `invoice_items` VALUES ('98', '63', '12', 'Propranolol hydrochloride', '1.000', '15000.00', '7000.00', '0.00', '15000.00');
INSERT INTO `invoice_items` VALUES ('99', '64', '5', 'Amiprazole', '1.000', '6000.00', '3000.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('100', '64', '11', 'Carvedilol', '1.000', '6000.00', '1500.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('101', '65', '26', 'Clotrimazole', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('102', '65', '21', 'Dydrogesterone', '1.000', '4000.00', '1000.00', '0.00', '4000.00');
INSERT INTO `invoice_items` VALUES ('103', '65', '16', 'XECUF', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('104', '65', '23', 'ZEcuf 100ml', '1.000', '5000.00', '1500.00', '0.00', '5000.00');
INSERT INTO `invoice_items` VALUES ('105', '66', '24', 'OMEGA-3 (1000mg)', '1.000', '8000.00', '3500.00', '0.00', '8000.00');
INSERT INTO `invoice_items` VALUES ('106', '66', '16', 'XECUF', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('107', '66', '29', 'Rufenac 50', '1.000', '6000.00', '2500.00', '0.00', '6000.00');
INSERT INTO `invoice_items` VALUES ('108', '66', '28', 'Metukulopram', '2.000', '10000.00', '4000.00', '0.00', '20000.00');
INSERT INTO `invoice_items` VALUES ('109', '67', '4', 'Ketoconazole', '1.000', '10000.00', '6000.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('110', '67', '9', 'Hydrochlorothiazide', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('111', '68', '4', 'Ketoconazole', '1.000', '10000.00', '0.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('112', '68', '9', 'Hydrochlorothiazide', '1.000', '6500.00', '0.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('113', '69', '3', 'جنتمايسين', '2.000', '2200.00', '1000.00', '0.00', '4400.00');
INSERT INTO `invoice_items` VALUES ('114', '69', '15', 'Ketoconazole2', '1.000', '8000.00', '1800.00', '0.00', '8000.00');
INSERT INTO `invoice_items` VALUES ('115', '70', '4', 'Ketoconazole', '1.000', '10000.00', '0.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('116', '71', '4', 'Ketoconazole', '1.000', '10000.00', '0.00', '0.00', '10000.00');
INSERT INTO `invoice_items` VALUES ('117', '72', '17', 'Zinnia F', '1.000', '5500.00', '0.00', '0.00', '5500.00');
INSERT INTO `invoice_items` VALUES ('118', '73', '9', 'Hydrochlorothiazide', '1.000', '6500.00', '3000.00', '0.00', '6500.00');
INSERT INTO `invoice_items` VALUES ('119', '74', '26', 'Clotrimazole', '1.000', '7000.00', '2500.00', '0.00', '7000.00');
INSERT INTO `invoice_items` VALUES ('120', '74', '19', 'Climepiride', '1.000', '4000.00', '2000.00', '0.00', '4000.00');
INSERT INTO `invoice_items` VALUES ('121', '75', '14', 'erty', '1.000', '25000.00', '10000.00', '0.00', '25000.00');

DROP TABLE IF EXISTS `invoices`;
CREATE TABLE `invoices` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `invoice_number` varchar(20) NOT NULL,
  `type` enum('sale','return','purchase') DEFAULT 'sale',
  `customer_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `subtotal` decimal(12,2) NOT NULL,
  `discount_type` enum('fixed','percent') DEFAULT 'fixed',
  `discount_value` decimal(12,2) DEFAULT 0.00,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `tax_rate` decimal(5,2) DEFAULT 15.00,
  `tax_amount` decimal(12,2) DEFAULT 0.00,
  `total` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) DEFAULT 0.00,
  `change_amount` decimal(12,2) DEFAULT 0.00,
  `payment_method` enum('cash','card','transfer','credit') DEFAULT 'cash',
  `status` enum('paid','pending','partial','returned') DEFAULT 'paid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `invoice_number` (`invoice_number`),
  KEY `user_id` (`user_id`),
  KEY `idx_invoice_number` (`invoice_number`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_customer` (`customer_id`),
  CONSTRAINT `invoices_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `invoices_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=76 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `invoices` VALUES ('1', 'ِAMD-00001', 'sale', NULL, '4', '20000.00', 'fixed', '10.00', '10.00', '15.00', '0.00', '19990.00', '19990.00', '0.00', 'cash', 'paid', '', '2026-05-17 16:49:12');
INSERT INTO `invoices` VALUES ('2', 'ِAMD-00002', 'sale', NULL, '4', '20000.00', 'fixed', '10.00', '10.00', '15.00', '0.00', '19990.00', '19990.00', '0.00', 'cash', 'paid', '', '2026-05-17 16:49:41');
INSERT INTO `invoices` VALUES ('3', 'ِAMD-00003', 'sale', NULL, '4', '9000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '9000.00', '9000.00', '0.00', 'cash', 'paid', '', '2026-05-17 19:05:22');
INSERT INTO `invoices` VALUES ('4', 'ِAMD-00004', 'sale', NULL, '4', '4400.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '4400.00', '4400.00', '0.00', 'cash', 'paid', '', '2026-05-17 19:07:58');
INSERT INTO `invoices` VALUES ('5', 'ِAMD-00005', 'sale', NULL, '4', '18000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '18000.00', '18000.00', '0.00', 'cash', 'paid', '', '2026-05-18 12:32:18');
INSERT INTO `invoices` VALUES ('6', 'ِAMD-00006', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '7000.00', '1000.00', 'cash', 'paid', '', '2026-05-18 12:35:43');
INSERT INTO `invoices` VALUES ('7', 'ِAMD-00007', 'sale', NULL, '4', '12000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '12000.00', '50000.00', '38000.00', 'cash', 'paid', '', '2026-05-18 12:36:55');
INSERT INTO `invoices` VALUES ('8', 'ِAMD-00008', 'sale', NULL, '4', '7000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '7000.00', '7000.00', '0.00', 'cash', 'paid', '', '2026-05-18 21:22:59');
INSERT INTO `invoices` VALUES ('9', 'ِAMD-00009', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:43:01');
INSERT INTO `invoices` VALUES ('10', 'ِAMD-00010', 'sale', NULL, '4', '17600.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '17600.00', '17600.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:43:39');
INSERT INTO `invoices` VALUES ('11', 'ِAMD-00011', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:44:04');
INSERT INTO `invoices` VALUES ('12', 'ِAMD-00012', 'sale', NULL, '4', '11000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '11000.00', '11000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:44:14');
INSERT INTO `invoices` VALUES ('13', 'ِAMD-00013', 'sale', NULL, '4', '3000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '3000.00', '3000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:44:27');
INSERT INTO `invoices` VALUES ('14', 'ِAMD-00014', 'sale', NULL, '4', '20000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '20000.00', '20000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:45:49');
INSERT INTO `invoices` VALUES ('15', 'ِAMD-00015', 'sale', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'paid', '', '2026-05-19 09:46:19');
INSERT INTO `invoices` VALUES ('16', 'ِAMD-00016', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-19 10:29:47');
INSERT INTO `invoices` VALUES ('17', 'ِAMD-00017', 'sale', NULL, '4', '3000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '3000.00', '50000.00', '47000.00', 'cash', 'paid', '', '2026-05-19 11:38:12');
INSERT INTO `invoices` VALUES ('18', 'ِAMD-00018', 'sale', NULL, '4', '3000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '3000.00', '3000.00', '0.00', 'cash', 'paid', '', '2026-05-19 11:40:28');
INSERT INTO `invoices` VALUES ('19', 'ِAMD-00019', 'sale', NULL, '4', '5000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '5000.00', '5000.00', '0.00', 'cash', 'paid', '', '2026-05-19 11:40:57');
INSERT INTO `invoices` VALUES ('20', 'ِAMD-00020', 'sale', NULL, '4', '5000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '5000.00', '5000.00', '0.00', 'cash', 'paid', '', '2026-05-19 11:42:51');
INSERT INTO `invoices` VALUES ('21', 'ِAMD-00021', 'sale', NULL, '4', '15000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '15000.00', '15000.00', '0.00', 'cash', 'paid', '', '2026-05-19 11:53:50');
INSERT INTO `invoices` VALUES ('22', 'ِAMD-00022', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-20 11:41:05');
INSERT INTO `invoices` VALUES ('23', 'ِAMD-00023', 'sale', NULL, '4', '73500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '73500.00', '73500.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:15:20');
INSERT INTO `invoices` VALUES ('24', 'ِAMD-00024', 'sale', NULL, '4', '32000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '32000.00', '32000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:25:32');
INSERT INTO `invoices` VALUES ('25', 'ِAMD-00025', 'sale', NULL, '4', '6500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6500.00', '6500.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:31');
INSERT INTO `invoices` VALUES ('26', 'ِAMD-00026', 'sale', NULL, '4', '11000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '11000.00', '11000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:39');
INSERT INTO `invoices` VALUES ('27', 'ِAMD-00027', 'sale', NULL, '4', '15000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '15000.00', '15000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:41');
INSERT INTO `invoices` VALUES ('28', 'ِAMD-00028', 'sale', NULL, '4', '2000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '2000.00', '2000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:49');
INSERT INTO `invoices` VALUES ('29', 'ِAMD-00029', 'sale', NULL, '4', '5000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '5000.00', '5000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:55');
INSERT INTO `invoices` VALUES ('30', 'ِAMD-00030', 'sale', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'paid', '', '2026-05-20 14:31:57');
INSERT INTO `invoices` VALUES ('31', 'ِAMD-00031', 'sale', NULL, '4', '32500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '32500.00', '32500.00', '0.00', 'cash', 'paid', '', '2026-05-20 16:48:08');
INSERT INTO `invoices` VALUES ('32', 'ِAMD-00032', 'sale', NULL, '4', '45500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '45500.00', '45500.00', '0.00', 'cash', 'paid', '', '2026-05-20 16:48:15');
INSERT INTO `invoices` VALUES ('33', 'ِAMD-00033', 'sale', NULL, '4', '26000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '26000.00', '26000.00', '0.00', 'cash', 'paid', '', '2026-05-20 16:48:21');
INSERT INTO `invoices` VALUES ('34', 'ِAMD-00034', 'sale', NULL, '4', '6500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6500.00', '10000.00', '3500.00', 'cash', 'paid', '', '2026-05-21 09:25:51');
INSERT INTO `invoices` VALUES ('35', 'ِAMD-00035', 'sale', NULL, '4', '33500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '33500.00', '50000.00', '16500.00', 'cash', 'paid', '', '2026-05-21 09:27:17');
INSERT INTO `invoices` VALUES ('36', 'ِAMD-00036', 'sale', NULL, '4', '12000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '12000.00', '12000.00', '0.00', 'cash', 'paid', '', '2026-05-21 09:59:13');
INSERT INTO `invoices` VALUES ('37', 'ِAMD-00037', 'sale', NULL, '4', '25000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '25000.00', '25000.00', '0.00', 'cash', 'paid', '', '2026-05-21 09:59:55');
INSERT INTO `invoices` VALUES ('38', 'ِAMD-00038', 'sale', NULL, '4', '25000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '25000.00', '25000.00', '0.00', 'cash', 'paid', '', '2026-05-21 10:03:20');
INSERT INTO `invoices` VALUES ('39', 'ِAMD-00039', 'sale', NULL, '4', '32000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '32000.00', '32000.00', '0.00', 'cash', 'paid', '', '2026-05-21 10:06:22');
INSERT INTO `invoices` VALUES ('40', 'ِAMD-00040', 'sale', NULL, '4', '31500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '31500.00', '31500.00', '0.00', 'cash', 'paid', '', '2026-05-21 17:29:05');
INSERT INTO `invoices` VALUES ('41', 'ِAMD-00041', 'sale', '1', '4', '31500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '31500.00', '31500.00', '0.00', 'cash', 'paid', '', '2026-05-21 17:36:08');
INSERT INTO `invoices` VALUES ('42', 'ِAMD-00042', 'sale', NULL, '4', '48500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '48500.00', '48500.00', '0.00', 'cash', 'paid', '', '2026-05-23 11:08:57');
INSERT INTO `invoices` VALUES ('43', 'RAW-00043', 'sale', NULL, '4', '30000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '30000.00', '30000.00', '0.00', 'cash', 'paid', '', '2026-05-23 13:50:36');
INSERT INTO `invoices` VALUES ('44', 'RAW-00044', 'sale', NULL, '4', '23500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '23500.00', '23500.00', '0.00', 'cash', 'paid', '', '2026-05-23 14:36:20');
INSERT INTO `invoices` VALUES ('45', 'RAW-00045', 'sale', NULL, '4', '17000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '17000.00', '17000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:21:41');
INSERT INTO `invoices` VALUES ('46', 'RAW-00046', 'sale', NULL, '4', '31499.96', 'fixed', '0.00', '0.00', '15.00', '0.00', '31499.96', '31499.96', '0.00', 'cash', 'paid', '', '2026-05-24 08:22:06');
INSERT INTO `invoices` VALUES ('47', 'RAW-00047', 'sale', NULL, '4', '4000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '4000.00', '4000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:22:10');
INSERT INTO `invoices` VALUES ('48', 'RAW-00048', 'sale', NULL, '4', '2000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '2000.00', '2000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:22:13');
INSERT INTO `invoices` VALUES ('49', 'RAW-00049', 'sale', NULL, '4', '5500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '5500.00', '5500.00', '0.00', 'cash', 'returned', '', '2026-05-24 08:22:18');
INSERT INTO `invoices` VALUES ('50', 'RAW-00050', 'sale', NULL, '4', '4000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '4000.00', '4000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:22:24');
INSERT INTO `invoices` VALUES ('51', 'RAW-00051', 'sale', NULL, '4', '7000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '7000.00', '7000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:23:06');
INSERT INTO `invoices` VALUES ('52', 'RAW-00052', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:23:13');
INSERT INTO `invoices` VALUES ('53', 'RAW-00053', 'sale', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:24:52');
INSERT INTO `invoices` VALUES ('54', 'RAW-00054', 'sale', NULL, '4', '7000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '7000.00', '7000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:37:17');
INSERT INTO `invoices` VALUES ('55', 'RAW-00055', 'sale', NULL, '4', '7000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '7000.00', '7000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:41:55');
INSERT INTO `invoices` VALUES ('56', 'RAW-00056', 'sale', NULL, '4', '6000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6000.00', '6000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:42:06');
INSERT INTO `invoices` VALUES ('57', 'RAW-00057', 'sale', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'returned', '', '2026-05-24 08:45:30');
INSERT INTO `invoices` VALUES ('58', 'RAW-00058', 'sale', NULL, '4', '17000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '17000.00', '17000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:52:43');
INSERT INTO `invoices` VALUES ('59', 'RAW-00059', 'sale', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'paid', '', '2026-05-24 08:55:18');
INSERT INTO `invoices` VALUES ('60', 'RAW-00060', 'sale', '1', '4', '10000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '10000.00', '10000.00', '0.00', 'credit', 'returned', '', '2026-05-24 08:58:09');
INSERT INTO `invoices` VALUES ('61', 'RAW-00061', 'sale', NULL, '4', '5500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '5500.00', '5500.00', '0.00', 'cash', 'paid', '', '2026-05-24 09:05:49');
INSERT INTO `invoices` VALUES ('62', 'RAW-00062', 'sale', NULL, '4', '8500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '8500.00', '8500.00', '0.00', 'cash', 'paid', '', '2026-05-24 09:08:06');
INSERT INTO `invoices` VALUES ('63', 'RAW-00063', 'sale', NULL, '4', '23000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '23000.00', '23000.00', '0.00', 'cash', 'paid', '', '2026-05-25 16:54:57');
INSERT INTO `invoices` VALUES ('64', 'RAW-00064', 'sale', NULL, '4', '12000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '12000.00', '12000.00', '0.00', 'cash', 'paid', '', '2026-05-25 16:55:29');
INSERT INTO `invoices` VALUES ('65', 'RAW-00065', 'sale', NULL, '4', '23000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '23000.00', '23000.00', '0.00', 'cash', 'paid', '', '2026-05-25 16:55:46');
INSERT INTO `invoices` VALUES ('66', 'RAW-00066', 'sale', NULL, '4', '41000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '41000.00', '50000.00', '9000.00', 'cash', 'paid', '', '2026-05-25 21:23:19');
INSERT INTO `invoices` VALUES ('67', 'RAW-00067', 'sale', '1', '4', '16500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '16500.00', '16500.00', '0.00', 'transfer', 'returned', '', '2026-05-25 21:36:34');
INSERT INTO `invoices` VALUES ('68', 'RET-D958013B', 'return', '1', '4', '16500.00', 'fixed', '0.00', '0.00', '0.00', '0.00', '16500.00', '16500.00', '0.00', 'transfer', 'returned', 'مرتجع من: RAW-00067 | REF:67 | السبب: طلب العميل', '2026-05-27 01:24:06');
INSERT INTO `invoices` VALUES ('69', 'RAW-00068', 'sale', NULL, '4', '12400.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '12400.00', '12400.00', '0.00', 'card', 'paid', '', '2026-05-27 01:28:42');
INSERT INTO `invoices` VALUES ('70', 'RET-E7B134A7', 'return', '1', '4', '10000.00', 'fixed', '0.00', '0.00', '0.00', '0.00', '10000.00', '10000.00', '0.00', 'credit', 'returned', 'مرتجع من: RAW-00060 | REF:60 | السبب: لم يدفع في الزمن المحدد', '2026-05-27 01:33:15');
INSERT INTO `invoices` VALUES ('71', 'RET-9203375D', 'return', NULL, '4', '10000.00', 'fixed', '0.00', '0.00', '0.00', '0.00', '10000.00', '10000.00', '0.00', 'cash', 'returned', 'مرتجع من: RAW-00057 | REF:57 | السبب: خطأ في الفاتورة', '2026-05-27 01:36:24');
INSERT INTO `invoices` VALUES ('72', 'RET-2644D574', 'return', NULL, '4', '5500.00', 'fixed', '0.00', '0.00', '0.00', '0.00', '5500.00', '5500.00', '0.00', 'cash', 'returned', 'مرتجع من: RAW-00049 | REF:49 | السبب: منتج غير مطابق للمواصفات', '2026-05-27 01:41:12');
INSERT INTO `invoices` VALUES ('73', 'RAW-00069', 'sale', NULL, '4', '6500.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '6500.00', '6500.00', '0.00', 'cash', 'paid', '', '2026-05-27 10:05:32');
INSERT INTO `invoices` VALUES ('74', 'RAW-00070', 'sale', NULL, '4', '11000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '11000.00', '11000.00', '0.00', 'cash', 'paid', '', '2026-05-29 20:17:58');
INSERT INTO `invoices` VALUES ('75', 'RAW-00071', 'sale', NULL, '4', '25000.00', 'fixed', '0.00', '0.00', '15.00', '0.00', '25000.00', '25000.00', '0.00', 'cash', 'paid', '', '2026-05-29 20:18:01');

DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(200) NOT NULL,
  `barcode` varchar(50) DEFAULT NULL,
  `category_id` int(11) DEFAULT NULL,
  `unit` varchar(50) DEFAULT 'قطعة',
  `purchase_price` decimal(12,2) DEFAULT 0.00,
  `sale_price` decimal(12,2) NOT NULL,
  `stock_qty` decimal(12,3) DEFAULT 0.000,
  `min_stock_alert` decimal(12,3) DEFAULT 5.000,
  `max_stock` decimal(12,3) DEFAULT 1000.000,
  `expiry_date` date DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `barcode` (`barcode`),
  KEY `created_by` (`created_by`),
  KEY `idx_barcode` (`barcode`),
  KEY `idx_name` (`name`),
  KEY `idx_category` (`category_id`),
  CONSTRAINT `products_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `products` VALUES ('1', 'امكسيسيلين', '9029149799', '1', 'قطعة', '1200.00', '2000.00', '65.000', '15.000', '1000.000', '2026-06-17', 'products/6a19edfcd6e30.png', 'مضاد حيوي', '1', '4', '2026-05-17 16:47:16', '2026-05-29 21:50:20');
INSERT INTO `products` VALUES ('2', 'اموكلان', '9029614441', '1', 'علبة', '2000.00', '3000.00', '34.000', '10.000', '1000.000', '2026-05-17', 'products/6a19ee0c615f1.png', 'مسكن الم', '1', '4', '2026-05-17 16:54:26', '2026-05-29 21:50:36');
INSERT INTO `products` VALUES ('3', 'جنتمايسين', '9029771227', '3', 'قطعة', '1000.00', '2200.00', '28.000', '10.000', '1000.000', '2026-06-17', 'products/6a19ee1dcf577.png', 'معقم ومطهر', '1', '4', '2026-05-17 16:57:12', '2026-05-29 21:50:53');
INSERT INTO `products` VALUES ('4', 'Ketoconazole', '9080611951', '3', 'علبة', '6000.00', '10000.00', '23.000', '10.000', '1000.000', '2026-09-18', 'products/6a19ee34d61b7.jpg', 'Shampoo BP 2% w/v', '1', '4', '2026-05-18 07:05:42', '2026-05-29 21:51:16');
INSERT INTO `products` VALUES ('5', 'Amiprazole', '9081466014', '1', 'قطعة', '3000.00', '6000.00', '20.000', '10.000', '1000.000', '2026-08-18', 'products/6a19ee42bade5.jpg', 'uryuue', '1', '4', '2026-05-18 07:18:29', '2026-05-29 21:51:30');
INSERT INTO `products` VALUES ('6', 'ringer drip', '9081554976', '1', 'زجاجة', '1500.00', '5000.00', '29.000', '10.000', '1000.000', '2028-07-18', 'products/6a19ee4eb313d.jpg', 'rtyui', '1', '4', '2026-05-18 07:20:00', '2026-05-29 21:51:42');
INSERT INTO `products` VALUES ('7', 'Digoxin', '9278650696', '1', 'قطعة', '1000.00', '3500.00', '0.000', '5.000', '1000.000', '2027-11-24', 'products/6a19ed7f4b13b.jpg', 'Digoxin 250 mcg scored Tablet', '1', '4', '2026-05-20 14:05:04', '2026-05-29 21:48:15');
INSERT INTO `products` VALUES ('8', 'Furosemide', '9278730265', '1', 'قطعة', '2000.00', '5000.00', '17.000', '10.000', '1000.000', '2028-10-20', 'products/6a19ee5d1b9fd.jpg', '7 Furosemide(Frusemide) 40mg Tablet', '1', '4', '2026-05-20 14:06:04', '2026-05-29 21:51:57');
INSERT INTO `products` VALUES ('9', 'Hydrochlorothiazide', '9278786071', '1', 'قطعة', '3000.00', '6500.00', '14.000', '10.000', '1000.000', '2027-05-20', 'products/6a19ee69a3994.jpg', 'Hydrochlorothiazide 50mg scroed Tablet', '1', '4', '2026-05-20 14:06:56', '2026-05-29 21:52:09');
INSERT INTO `products` VALUES ('10', 'Spironolactone', '9279373197', '1', 'قطعة', '4000.00', '8500.00', '16.000', '9.997', '1000.000', '2026-11-30', 'products/6a19ee78b9863.png', '25mg Tablet', '1', '4', '2026-05-20 14:16:48', '2026-05-29 21:52:24');
INSERT INTO `products` VALUES ('11', 'Carvedilol', '9279459469', '1', 'قطعة', '1500.00', '6000.00', '12.000', '10.000', '1000.000', '2027-12-20', 'products/6a19ee86580c5.png', 'Carvedilol 6.25mg Tablet', '1', '4', '2026-05-20 14:18:10', '2026-05-29 21:52:38');
INSERT INTO `products` VALUES ('12', 'Propranolol hydrochloride', '9280154396', '1', 'أمبول', '7000.00', '15000.00', '16.000', '10.000', '1000.000', '2028-06-20', 'products/6a19ee944fc8c.png', '1mg/ml slow IV (1ml Ampoule )', '1', '4', '2026-05-20 14:29:49', '2026-05-29 21:52:52');
INSERT INTO `products` VALUES ('13', 'Mogo T', '9282264086', '4', 'كيس', '1000.00', '2000.00', '9.000', '10.000', '1000.000', '2028-06-20', 'products/6a19eea3ec4cf.jpg', 'غفقثص', '1', '4', '2026-05-20 15:04:48', '2026-05-29 21:53:07');
INSERT INTO `products` VALUES ('14', 'erty', '9288384110', '2', 'علبة', '10000.00', '25000.00', '32.000', '10.000', '1000.000', '2028-03-13', 'products/6a19edefa4490.png', 'ertyu', '1', '4', '2026-05-20 16:47:14', '2026-05-29 21:50:07');
INSERT INTO `products` VALUES ('15', 'Ketoconazole2', '9352754134', '1', 'قطعة', '1800.00', '8000.00', '14.000', '10.000', '1000.000', '2026-07-21', 'products/6a19ede23b530.png', 'werty', '1', '4', '2026-05-21 10:41:28', '2026-05-29 21:49:54');
INSERT INTO `products` VALUES ('16', 'XECUF', '9537251684', '1', 'زجاجة', '2500.00', '7000.00', '13.000', '10.000', '1000.000', '2027-11-23', 'products/6a19ecf11d0eb.jpg', '', '1', '4', '2026-05-23 13:55:03', '2026-05-29 21:45:53');
INSERT INTO `products` VALUES ('17', 'Zinnia F', '9537345019', '1', 'قطعة', '2000.00', '5500.00', '15.000', '10.000', '1000.000', '2028-01-23', 'products/6a19ed0c5fddf.jpg', '', '1', '4', '2026-05-23 13:56:20', '2026-05-29 21:46:20');
INSERT INTO `products` VALUES ('18', 'Shalspirin CV', '9537417939', '1', 'قطعة', '3000.00', '7000.00', '11.000', '10.000', '1000.000', '2027-12-23', 'products/6a19edd8160f7.png', '', '1', '4', '2026-05-23 13:57:26', '2026-05-29 21:49:44');
INSERT INTO `products` VALUES ('19', 'Climepiride', '9537485931', '1', 'قطعة', '2000.00', '4000.00', '14.000', '10.000', '1000.000', '2028-07-23', 'products/6a19ed44f39c2.jpg', '', '1', '4', '2026-05-23 13:58:46', '2026-05-29 21:47:16');
INSERT INTO `products` VALUES ('20', 'Itraconazole', '9537566172', '1', 'قطعة', '1500.00', '3500.00', '18.000', '10.000', '1000.000', '2026-11-23', 'products/6a19edcc8e8c0.png', '', '1', '4', '2026-05-23 14:00:01', '2026-05-29 21:49:32');
INSERT INTO `products` VALUES ('21', 'Dydrogesterone', '9537637482', '1', 'قطعة', '1000.00', '4000.00', '18.000', '10.000', '1000.000', '2027-12-05', 'products/6a19edc1c375a.png', '', '1', '4', '2026-05-23 14:01:19', '2026-05-29 21:49:21');
INSERT INTO `products` VALUES ('22', 'CEFIUM 400mg', '9537792362', '1', 'قطعة', '2000.00', '6000.00', '23.000', '10.000', '1000.000', '2029-11-23', 'products/6a19ecd7dc074.jpg', '', '1', '4', '2026-05-23 14:03:50', '2026-05-29 21:45:27');
INSERT INTO `products` VALUES ('23', 'ZEcuf 100ml', '9539024990', '1', 'زجاجة', '1500.00', '5000.00', '17.000', '10.000', '1000.000', '2027-11-23', 'products/6a19ec9378c91.jpg', '', '1', '4', '2026-05-23 14:24:13', '2026-05-29 21:44:19');
INSERT INTO `products` VALUES ('24', 'OMEGA-3 (1000mg)', '9539129990', '2', 'زجاجة', '3500.00', '8000.00', '27.000', '10.000', '1000.000', '2028-02-23', 'products/6a19ec7e865dc.jpg', '', '1', '4', '2026-05-23 14:26:10', '2026-05-29 21:43:58');
INSERT INTO `products` VALUES ('25', 'FAMY-POP (0.03mg)', '9539243990', '1', 'قطعة', '3000.00', '9500.00', '50.000', '10.000', '1000.000', '2029-10-23', 'products/6a19ec6ef0a92.jpg', '', '1', '4', '2026-05-23 14:28:11', '2026-05-29 21:43:42');
INSERT INTO `products` VALUES ('26', 'Clotrimazole', '9543029041', '3', 'قطعة', '2500.00', '7000.00', '35.000', '10.000', '1000.000', '2028-11-23', 'products/6a19ec55d2706.jpg', 'Clotrimazole Vaginal 100mg', '1', '4', '2026-05-23 15:31:12', '2026-05-29 21:43:17');
INSERT INTO `products` VALUES ('27', 'Snake Venom Antiserum IP', '9543196304', '1', 'قطعة', '2000.00', '5500.00', '9.000', '10.000', '1000.000', '2027-11-23', 'products/6a19ec281575f.jpg', '', '1', '4', '2026-05-23 15:33:55', '2026-05-29 21:42:32');
INSERT INTO `products` VALUES ('28', 'Metukulopram', '9543313160', '1', 'علبة', '4000.00', '10000.00', '98.000', '20.000', '1000.000', '2028-01-01', 'products/6a19edb52ffee.png', 'محلول للحقن العضلي والوريدي 10mg-2ml', '1', '4', '2026-05-23 15:37:29', '2026-05-29 21:49:09');
INSERT INTO `products` VALUES ('29', 'Rufenac 50', '9602558624', '1', 'قطعة', '2500.00', '6000.00', '19.000', '10.000', '1000.000', '2027-07-24', 'products/6a19ebc9bebaa.jpg', '', '1', '4', '2026-05-24 08:03:13', '2026-05-29 21:40:57');
INSERT INTO `products` VALUES ('30', 'Voltanext', '9602631489', '1', 'قطعة', '1500.00', '4999.96', '29.000', '10.000', '1000.000', '2027-06-24', 'products/6a19ebb8d80ef.jpg', 'Diclofenac Sodium Bp Tablets 100mg', '1', '4', '2026-05-24 08:05:35', '2026-05-29 21:40:40');
INSERT INTO `products` VALUES ('31', 'zinconext', '9603564893', '1', 'علبة', '1500.00', '4000.00', '19.000', '10.000', '1000.000', '2028-11-24', 'products/6a19eba8aaebe.jpg', '60ml', '1', '4', '2026-05-24 08:20:32', '2026-05-29 21:40:24');
INSERT INTO `products` VALUES ('32', 'Daprofen', '9605594846', '2', 'قطعة', '2000.00', '6000.00', '0.000', '5.000', '1000.000', '2027-06-24', 'products/6a19eb913090f.jpg', '60ml', '1', '4', '2026-05-24 08:21:26', '2026-05-29 21:40:01');
INSERT INTO `products` VALUES ('33', 'Ketoconazole', '9737193594', '4', 'أمبول', '3000.00', '6500.00', '50.000', '10.000', '1000.000', '2027-11-25', 'products/6a19edaa4c704.png', '', '1', '4', '2026-05-25 21:27:43', '2026-05-29 21:48:58');

DROP TABLE IF EXISTS `purchase_items`;
CREATE TABLE `purchase_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `unit_price` decimal(12,2) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_id` (`purchase_id`),
  KEY `product_id` (`product_id`),
  CONSTRAINT `purchase_items_ibfk_1` FOREIGN KEY (`purchase_id`) REFERENCES `purchases` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_items_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchase_items` VALUES ('1', '1', '5', '13.000', '3000.00', '39000.00');

DROP TABLE IF EXISTS `purchases`;
CREATE TABLE `purchases` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `purchase_number` varchar(20) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `total` decimal(12,2) NOT NULL,
  `paid_amount` decimal(12,2) DEFAULT 0.00,
  `status` enum('paid','pending','partial') DEFAULT 'paid',
  `notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `purchase_number` (`purchase_number`),
  KEY `supplier_id` (`supplier_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `purchases_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `purchases_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `purchases` VALUES ('1', 'PUR-28119', '1', '4', '39000.00', '39000.00', 'paid', '', '2026-05-18 11:37:22');

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=119 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `settings` VALUES ('1', 'business_name', 'Rawafid-Pos', '2026-05-23 13:47:48');
INSERT INTO `settings` VALUES ('2', 'business_type', 'pharmacy', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('3', 'currency', 'ج.س', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('4', 'currency_code', 'SDG', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('5', 'tax_rate', '15', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('6', 'tax_enabled', '0', '2026-05-17 11:16:16');
INSERT INTO `settings` VALUES ('7', 'low_stock_alert', '10', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('8', 'invoice_prefix', 'RAW', '2026-05-23 13:48:38');
INSERT INTO `settings` VALUES ('9', 'invoice_counter', '72', '2026-05-29 20:18:01');
INSERT INTO `settings` VALUES ('10', 'theme_color', 'olive', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('11', 'dark_mode', '0', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('12', 'receipt_footer', 'شكراً لتعاملكم معنا', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('13', 'backup_auto', '1', '2026-05-16 11:23:16');
INSERT INTO `settings` VALUES ('24', 'logo', 'logo/6a162d5f70aff.png', '2026-05-27 01:31:43');

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE `stock_movements` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `type` enum('in','out','return','adjustment') NOT NULL,
  `quantity` decimal(12,3) NOT NULL,
  `before_qty` decimal(12,3) DEFAULT NULL,
  `after_qty` decimal(12,3) DEFAULT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `idx_product` (`product_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `stock_movements_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_movements_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=159 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `stock_movements` VALUES ('1', '1', 'in', '1000.000', '0.000', '1000.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-17 16:47:16');
INSERT INTO `stock_movements` VALUES ('2', '1', 'out', '10.000', '1000.000', '990.000', 'invoice', '1', NULL, '4', '2026-05-17 16:49:12');
INSERT INTO `stock_movements` VALUES ('3', '1', 'out', '10.000', '990.000', '980.000', 'invoice', '2', NULL, '4', '2026-05-17 16:49:41');
INSERT INTO `stock_movements` VALUES ('4', '2', 'in', '50.000', '0.000', '50.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-17 16:54:26');
INSERT INTO `stock_movements` VALUES ('5', '3', 'in', '10.000', '0.000', '10.000', 'manual', NULL, 'زيادة', '4', '2026-05-17 16:57:33');
INSERT INTO `stock_movements` VALUES ('6', '2', 'out', '3.000', '50.000', '47.000', 'invoice', '3', NULL, '4', '2026-05-17 19:05:22');
INSERT INTO `stock_movements` VALUES ('7', '3', 'out', '2.000', '10.000', '8.000', 'invoice', '4', NULL, '4', '2026-05-17 19:07:58');
INSERT INTO `stock_movements` VALUES ('8', '4', 'in', '30.000', '0.000', '30.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-18 07:05:42');
INSERT INTO `stock_movements` VALUES ('9', '5', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-18 07:18:29');
INSERT INTO `stock_movements` VALUES ('10', '6', 'in', '40.000', '0.000', '40.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-18 07:20:00');
INSERT INTO `stock_movements` VALUES ('11', '5', 'in', '13.000', '20.000', '33.000', 'purchase', '1', NULL, '4', '2026-05-18 11:37:22');
INSERT INTO `stock_movements` VALUES ('12', '5', 'out', '3.000', '33.000', '30.000', 'invoice', '5', NULL, '4', '2026-05-18 12:32:18');
INSERT INTO `stock_movements` VALUES ('13', '5', 'out', '1.000', '30.000', '29.000', 'invoice', '6', NULL, '4', '2026-05-18 12:35:43');
INSERT INTO `stock_movements` VALUES ('14', '5', 'out', '2.000', '29.000', '27.000', 'invoice', '7', NULL, '4', '2026-05-18 12:36:55');
INSERT INTO `stock_movements` VALUES ('15', '1', 'out', '900.000', '980.000', '80.000', 'manual', NULL, 'خصم', '4', '2026-05-18 15:36:32');
INSERT INTO `stock_movements` VALUES ('16', '6', 'out', '1.000', '40.000', '39.000', 'invoice', '8', NULL, '4', '2026-05-18 21:22:59');
INSERT INTO `stock_movements` VALUES ('17', '1', 'out', '1.000', '80.000', '79.000', 'invoice', '8', NULL, '4', '2026-05-18 21:22:59');
INSERT INTO `stock_movements` VALUES ('18', '2', 'out', '2.000', '47.000', '45.000', 'invoice', '9', NULL, '4', '2026-05-19 09:43:01');
INSERT INTO `stock_movements` VALUES ('19', '3', 'out', '8.000', '8.000', '0.000', 'invoice', '10', NULL, '4', '2026-05-19 09:43:39');
INSERT INTO `stock_movements` VALUES ('20', '1', 'out', '3.000', '79.000', '76.000', 'invoice', '11', NULL, '4', '2026-05-19 09:44:04');
INSERT INTO `stock_movements` VALUES ('21', '6', 'out', '1.000', '39.000', '38.000', 'invoice', '12', NULL, '4', '2026-05-19 09:44:14');
INSERT INTO `stock_movements` VALUES ('22', '5', 'out', '1.000', '27.000', '26.000', 'invoice', '12', NULL, '4', '2026-05-19 09:44:14');
INSERT INTO `stock_movements` VALUES ('23', '2', 'out', '1.000', '45.000', '44.000', 'invoice', '13', NULL, '4', '2026-05-19 09:44:27');
INSERT INTO `stock_movements` VALUES ('24', '4', 'out', '2.000', '30.000', '28.000', 'invoice', '14', NULL, '4', '2026-05-19 09:45:49');
INSERT INTO `stock_movements` VALUES ('25', '6', 'out', '2.000', '38.000', '36.000', 'invoice', '15', NULL, '4', '2026-05-19 09:46:19');
INSERT INTO `stock_movements` VALUES ('26', '2', 'out', '2.000', '44.000', '42.000', 'invoice', '16', NULL, '4', '2026-05-19 10:29:47');
INSERT INTO `stock_movements` VALUES ('27', '2', 'out', '1.000', '42.000', '41.000', 'invoice', '17', NULL, '4', '2026-05-19 11:38:12');
INSERT INTO `stock_movements` VALUES ('28', '2', 'out', '1.000', '41.000', '40.000', 'invoice', '18', NULL, '4', '2026-05-19 11:40:28');
INSERT INTO `stock_movements` VALUES ('29', '2', 'out', '1.000', '40.000', '39.000', 'invoice', '19', NULL, '4', '2026-05-19 11:40:57');
INSERT INTO `stock_movements` VALUES ('30', '1', 'out', '1.000', '76.000', '75.000', 'invoice', '19', NULL, '4', '2026-05-19 11:40:57');
INSERT INTO `stock_movements` VALUES ('31', '6', 'out', '1.000', '36.000', '35.000', 'invoice', '20', NULL, '4', '2026-05-19 11:42:51');
INSERT INTO `stock_movements` VALUES ('32', '2', 'out', '5.000', '39.000', '34.000', 'invoice', '21', NULL, '4', '2026-05-19 11:53:50');
INSERT INTO `stock_movements` VALUES ('33', '1', 'out', '3.000', '75.000', '72.000', 'invoice', '22', NULL, '4', '2026-05-20 11:41:06');
INSERT INTO `stock_movements` VALUES ('34', '8', 'in', '12.000', '0.000', '12.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 14:06:04');
INSERT INTO `stock_movements` VALUES ('35', '9', 'in', '30.000', '0.000', '30.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 14:06:56');
INSERT INTO `stock_movements` VALUES ('36', '9', 'out', '3.000', '30.000', '27.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('37', '4', 'out', '2.000', '28.000', '26.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('38', '5', 'out', '2.000', '26.000', '24.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('39', '8', 'out', '2.000', '12.000', '10.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('40', '1', 'out', '1.000', '72.000', '71.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('41', '6', 'out', '2.000', '35.000', '33.000', 'invoice', '23', NULL, '4', '2026-05-20 14:15:20');
INSERT INTO `stock_movements` VALUES ('42', '10', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 14:16:48');
INSERT INTO `stock_movements` VALUES ('43', '11', 'in', '12.000', '0.000', '12.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 14:18:10');
INSERT INTO `stock_movements` VALUES ('44', '9', 'out', '1.000', '27.000', '26.000', 'invoice', '24', NULL, '4', '2026-05-20 14:25:32');
INSERT INTO `stock_movements` VALUES ('45', '11', 'out', '1.000', '12.000', '11.000', 'invoice', '24', NULL, '4', '2026-05-20 14:25:32');
INSERT INTO `stock_movements` VALUES ('46', '5', 'out', '1.000', '24.000', '23.000', 'invoice', '24', NULL, '4', '2026-05-20 14:25:32');
INSERT INTO `stock_movements` VALUES ('47', '6', 'out', '1.000', '33.000', '32.000', 'invoice', '24', NULL, '4', '2026-05-20 14:25:32');
INSERT INTO `stock_movements` VALUES ('48', '10', 'out', '1.000', '20.000', '19.000', 'invoice', '24', NULL, '4', '2026-05-20 14:25:32');
INSERT INTO `stock_movements` VALUES ('49', '12', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 14:29:49');
INSERT INTO `stock_movements` VALUES ('50', '9', 'out', '1.000', '26.000', '25.000', 'invoice', '25', NULL, '4', '2026-05-20 14:31:31');
INSERT INTO `stock_movements` VALUES ('51', '11', 'out', '1.000', '11.000', '10.000', 'invoice', '26', NULL, '4', '2026-05-20 14:31:39');
INSERT INTO `stock_movements` VALUES ('52', '8', 'out', '1.000', '10.000', '9.000', 'invoice', '26', NULL, '4', '2026-05-20 14:31:39');
INSERT INTO `stock_movements` VALUES ('53', '12', 'out', '1.000', '20.000', '19.000', 'invoice', '27', NULL, '4', '2026-05-20 14:31:41');
INSERT INTO `stock_movements` VALUES ('54', '1', 'out', '1.000', '71.000', '70.000', 'invoice', '28', NULL, '4', '2026-05-20 14:31:49');
INSERT INTO `stock_movements` VALUES ('55', '8', 'out', '1.000', '9.000', '8.000', 'invoice', '29', NULL, '4', '2026-05-20 14:31:55');
INSERT INTO `stock_movements` VALUES ('56', '4', 'out', '1.000', '26.000', '25.000', 'invoice', '30', NULL, '4', '2026-05-20 14:31:57');
INSERT INTO `stock_movements` VALUES ('57', '13', 'in', '10.000', '0.000', '10.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 15:04:48');
INSERT INTO `stock_movements` VALUES ('58', '14', 'in', '40.000', '0.000', '40.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-20 16:47:14');
INSERT INTO `stock_movements` VALUES ('59', '9', 'out', '5.000', '25.000', '20.000', 'invoice', '31', NULL, '4', '2026-05-20 16:48:08');
INSERT INTO `stock_movements` VALUES ('60', '9', 'out', '7.000', '20.000', '13.000', 'invoice', '32', NULL, '4', '2026-05-20 16:48:15');
INSERT INTO `stock_movements` VALUES ('61', '9', 'out', '4.000', '13.000', '9.000', 'invoice', '33', NULL, '4', '2026-05-20 16:48:21');
INSERT INTO `stock_movements` VALUES ('62', '9', 'in', '10.000', '9.000', '19.000', 'manual', NULL, 'بضاعة جديدة', '4', '2026-05-20 17:09:23');
INSERT INTO `stock_movements` VALUES ('63', '8', 'in', '12.000', '8.000', '20.000', 'manual', NULL, 'بضاعة جديدة', '4', '2026-05-20 17:10:37');
INSERT INTO `stock_movements` VALUES ('64', '9', 'out', '1.000', '19.000', '18.000', 'invoice', '34', NULL, '4', '2026-05-21 09:25:51');
INSERT INTO `stock_movements` VALUES ('65', '14', 'out', '1.000', '40.000', '39.000', 'invoice', '35', NULL, '4', '2026-05-21 09:27:17');
INSERT INTO `stock_movements` VALUES ('66', '10', 'out', '1.000', '19.000', '18.000', 'invoice', '35', NULL, '4', '2026-05-21 09:27:17');
INSERT INTO `stock_movements` VALUES ('67', '1', 'out', '1.000', '70.000', '69.000', 'invoice', '36', NULL, '4', '2026-05-21 09:59:13');
INSERT INTO `stock_movements` VALUES ('68', '6', 'out', '2.000', '32.000', '30.000', 'invoice', '36', NULL, '4', '2026-05-21 09:59:13');
INSERT INTO `stock_movements` VALUES ('69', '14', 'out', '1.000', '39.000', '38.000', 'invoice', '37', NULL, '4', '2026-05-21 09:59:55');
INSERT INTO `stock_movements` VALUES ('70', '14', 'out', '1.000', '38.000', '37.000', 'invoice', '38', NULL, '4', '2026-05-21 10:03:20');
INSERT INTO `stock_movements` VALUES ('71', '12', 'out', '2.000', '19.000', '17.000', 'invoice', '39', NULL, '4', '2026-05-21 10:06:22');
INSERT INTO `stock_movements` VALUES ('72', '13', 'out', '1.000', '10.000', '9.000', 'invoice', '39', NULL, '4', '2026-05-21 10:06:22');
INSERT INTO `stock_movements` VALUES ('73', '15', 'in', '15.000', '0.000', '15.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-21 10:41:28');
INSERT INTO `stock_movements` VALUES ('74', '14', 'out', '1.000', '37.000', '36.000', 'invoice', '40', NULL, '4', '2026-05-21 17:29:05');
INSERT INTO `stock_movements` VALUES ('75', '9', 'out', '1.000', '18.000', '17.000', 'invoice', '40', NULL, '4', '2026-05-21 17:29:05');
INSERT INTO `stock_movements` VALUES ('76', '9', 'out', '1.000', '17.000', '16.000', 'invoice', '41', NULL, '4', '2026-05-21 17:36:08');
INSERT INTO `stock_movements` VALUES ('77', '14', 'out', '1.000', '36.000', '35.000', 'invoice', '41', NULL, '4', '2026-05-21 17:36:08');
INSERT INTO `stock_movements` VALUES ('78', '14', 'out', '1.000', '35.000', '34.000', 'invoice', '42', NULL, '4', '2026-05-23 11:08:57');
INSERT INTO `stock_movements` VALUES ('79', '11', 'out', '1.000', '10.000', '9.000', 'invoice', '42', NULL, '4', '2026-05-23 11:08:57');
INSERT INTO `stock_movements` VALUES ('80', '5', 'out', '1.000', '23.000', '22.000', 'invoice', '42', NULL, '4', '2026-05-23 11:08:57');
INSERT INTO `stock_movements` VALUES ('81', '8', 'out', '1.000', '20.000', '19.000', 'invoice', '42', NULL, '4', '2026-05-23 11:08:57');
INSERT INTO `stock_movements` VALUES ('82', '9', 'out', '1.000', '16.000', '15.000', 'invoice', '42', NULL, '4', '2026-05-23 11:08:57');
INSERT INTO `stock_movements` VALUES ('83', '8', 'out', '1.000', '19.000', '18.000', 'invoice', '43', NULL, '4', '2026-05-23 13:50:36');
INSERT INTO `stock_movements` VALUES ('84', '14', 'out', '1.000', '34.000', '33.000', 'invoice', '43', NULL, '4', '2026-05-23 13:50:36');
INSERT INTO `stock_movements` VALUES ('85', '16', 'in', '15.000', '0.000', '15.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 13:55:03');
INSERT INTO `stock_movements` VALUES ('86', '17', 'in', '15.000', '0.000', '15.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 13:56:20');
INSERT INTO `stock_movements` VALUES ('87', '18', 'in', '15.000', '0.000', '15.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 13:57:26');
INSERT INTO `stock_movements` VALUES ('88', '19', 'in', '16.000', '0.000', '16.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 13:58:46');
INSERT INTO `stock_movements` VALUES ('89', '20', 'in', '19.000', '0.000', '19.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:00:01');
INSERT INTO `stock_movements` VALUES ('90', '21', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:01:19');
INSERT INTO `stock_movements` VALUES ('91', '22', 'in', '25.000', '0.000', '25.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:03:50');
INSERT INTO `stock_movements` VALUES ('92', '23', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:24:13');
INSERT INTO `stock_movements` VALUES ('93', '24', 'in', '30.000', '0.000', '30.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:26:10');
INSERT INTO `stock_movements` VALUES ('94', '25', 'in', '50.000', '0.000', '50.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 14:28:11');
INSERT INTO `stock_movements` VALUES ('95', '10', 'out', '1.000', '18.000', '17.000', 'invoice', '44', NULL, '4', '2026-05-23 14:36:20');
INSERT INTO `stock_movements` VALUES ('96', '18', 'out', '1.000', '15.000', '14.000', 'invoice', '44', NULL, '4', '2026-05-23 14:36:20');
INSERT INTO `stock_movements` VALUES ('97', '24', 'out', '1.000', '30.000', '29.000', 'invoice', '44', NULL, '4', '2026-05-23 14:36:20');
INSERT INTO `stock_movements` VALUES ('98', '26', 'in', '40.000', '0.000', '40.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 15:31:12');
INSERT INTO `stock_movements` VALUES ('99', '27', 'in', '10.000', '0.000', '10.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 15:33:55');
INSERT INTO `stock_movements` VALUES ('100', '28', 'in', '100.000', '0.000', '100.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-23 15:37:29');
INSERT INTO `stock_movements` VALUES ('101', '29', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-24 08:03:13');
INSERT INTO `stock_movements` VALUES ('102', '30', 'in', '30.000', '0.000', '30.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-24 08:05:35');
INSERT INTO `stock_movements` VALUES ('103', '31', 'in', '20.000', '0.000', '20.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-24 08:20:32');
INSERT INTO `stock_movements` VALUES ('104', '26', 'out', '1.000', '40.000', '39.000', 'invoice', '45', NULL, '4', '2026-05-24 08:21:41');
INSERT INTO `stock_movements` VALUES ('105', '19', 'out', '1.000', '16.000', '15.000', 'invoice', '45', NULL, '4', '2026-05-24 08:21:41');
INSERT INTO `stock_movements` VALUES ('106', '22', 'out', '1.000', '25.000', '24.000', 'invoice', '45', NULL, '4', '2026-05-24 08:21:41');
INSERT INTO `stock_movements` VALUES ('107', '6', 'out', '1.000', '30.000', '29.000', 'invoice', '46', NULL, '4', '2026-05-24 08:22:06');
INSERT INTO `stock_movements` VALUES ('108', '5', 'out', '1.000', '22.000', '21.000', 'invoice', '46', NULL, '4', '2026-05-24 08:22:06');
INSERT INTO `stock_movements` VALUES ('109', '18', 'out', '1.000', '14.000', '13.000', 'invoice', '46', NULL, '4', '2026-05-24 08:22:06');
INSERT INTO `stock_movements` VALUES ('110', '10', 'out', '1.000', '17.000', '16.000', 'invoice', '46', NULL, '4', '2026-05-24 08:22:06');
INSERT INTO `stock_movements` VALUES ('111', '30', 'out', '1.000', '30.000', '29.000', 'invoice', '46', NULL, '4', '2026-05-24 08:22:06');
INSERT INTO `stock_movements` VALUES ('112', '31', 'out', '1.000', '20.000', '19.000', 'invoice', '47', NULL, '4', '2026-05-24 08:22:10');
INSERT INTO `stock_movements` VALUES ('113', '1', 'out', '1.000', '69.000', '68.000', 'invoice', '48', NULL, '4', '2026-05-24 08:22:13');
INSERT INTO `stock_movements` VALUES ('114', '17', 'out', '1.000', '15.000', '14.000', 'invoice', '49', NULL, '4', '2026-05-24 08:22:18');
INSERT INTO `stock_movements` VALUES ('115', '21', 'out', '1.000', '20.000', '19.000', 'invoice', '50', NULL, '4', '2026-05-24 08:22:24');
INSERT INTO `stock_movements` VALUES ('116', '18', 'out', '1.000', '13.000', '12.000', 'invoice', '51', NULL, '4', '2026-05-24 08:23:06');
INSERT INTO `stock_movements` VALUES ('117', '1', 'out', '3.000', '68.000', '65.000', 'invoice', '52', NULL, '4', '2026-05-24 08:23:13');
INSERT INTO `stock_movements` VALUES ('118', '4', 'out', '1.000', '25.000', '24.000', 'invoice', '53', NULL, '4', '2026-05-24 08:24:52');
INSERT INTO `stock_movements` VALUES ('119', '26', 'out', '1.000', '39.000', '38.000', 'invoice', '54', NULL, '4', '2026-05-24 08:37:17');
INSERT INTO `stock_movements` VALUES ('120', '26', 'out', '1.000', '38.000', '37.000', 'invoice', '55', NULL, '4', '2026-05-24 08:41:55');
INSERT INTO `stock_movements` VALUES ('121', '22', 'out', '1.000', '24.000', '23.000', 'invoice', '56', NULL, '4', '2026-05-24 08:42:06');
INSERT INTO `stock_movements` VALUES ('122', '4', 'out', '1.000', '24.000', '23.000', 'invoice', '57', NULL, '4', '2026-05-24 08:45:30');
INSERT INTO `stock_movements` VALUES ('123', '4', 'out', '1.000', '23.000', '22.000', 'invoice', '58', NULL, '4', '2026-05-24 08:52:43');
INSERT INTO `stock_movements` VALUES ('124', '18', 'out', '1.000', '12.000', '11.000', 'invoice', '58', NULL, '4', '2026-05-24 08:52:43');
INSERT INTO `stock_movements` VALUES ('125', '8', 'out', '1.000', '18.000', '17.000', 'invoice', '59', NULL, '4', '2026-05-24 08:55:18');
INSERT INTO `stock_movements` VALUES ('126', '23', 'out', '1.000', '20.000', '19.000', 'invoice', '59', NULL, '4', '2026-05-24 08:55:18');
INSERT INTO `stock_movements` VALUES ('127', '4', 'out', '1.000', '22.000', '21.000', 'invoice', '60', NULL, '4', '2026-05-24 08:58:09');
INSERT INTO `stock_movements` VALUES ('128', '27', 'out', '1.000', '10.000', '9.000', 'invoice', '61', NULL, '4', '2026-05-24 09:05:49');
INSERT INTO `stock_movements` VALUES ('129', '20', 'out', '1.000', '19.000', '18.000', 'invoice', '62', NULL, '4', '2026-05-24 09:08:06');
INSERT INTO `stock_movements` VALUES ('130', '23', 'out', '1.000', '19.000', '18.000', 'invoice', '62', NULL, '4', '2026-05-24 09:08:06');
INSERT INTO `stock_movements` VALUES ('131', '24', 'out', '1.000', '29.000', '28.000', 'invoice', '63', NULL, '4', '2026-05-25 16:54:57');
INSERT INTO `stock_movements` VALUES ('132', '12', 'out', '1.000', '17.000', '16.000', 'invoice', '63', NULL, '4', '2026-05-25 16:54:57');
INSERT INTO `stock_movements` VALUES ('133', '5', 'out', '1.000', '21.000', '20.000', 'invoice', '64', NULL, '4', '2026-05-25 16:55:29');
INSERT INTO `stock_movements` VALUES ('134', '11', 'out', '1.000', '9.000', '8.000', 'invoice', '64', NULL, '4', '2026-05-25 16:55:29');
INSERT INTO `stock_movements` VALUES ('135', '26', 'out', '1.000', '37.000', '36.000', 'invoice', '65', NULL, '4', '2026-05-25 16:55:46');
INSERT INTO `stock_movements` VALUES ('136', '21', 'out', '1.000', '19.000', '18.000', 'invoice', '65', NULL, '4', '2026-05-25 16:55:46');
INSERT INTO `stock_movements` VALUES ('137', '16', 'out', '1.000', '15.000', '14.000', 'invoice', '65', NULL, '4', '2026-05-25 16:55:46');
INSERT INTO `stock_movements` VALUES ('138', '23', 'out', '1.000', '18.000', '17.000', 'invoice', '65', NULL, '4', '2026-05-25 16:55:46');
INSERT INTO `stock_movements` VALUES ('139', '24', 'out', '1.000', '28.000', '27.000', 'invoice', '66', NULL, '4', '2026-05-25 21:23:19');
INSERT INTO `stock_movements` VALUES ('140', '16', 'out', '1.000', '14.000', '13.000', 'invoice', '66', NULL, '4', '2026-05-25 21:23:19');
INSERT INTO `stock_movements` VALUES ('141', '29', 'out', '1.000', '20.000', '19.000', 'invoice', '66', NULL, '4', '2026-05-25 21:23:19');
INSERT INTO `stock_movements` VALUES ('142', '28', 'out', '2.000', '100.000', '98.000', 'invoice', '66', NULL, '4', '2026-05-25 21:23:19');
INSERT INTO `stock_movements` VALUES ('143', '33', 'in', '50.000', '0.000', '50.000', 'initial', NULL, 'كمية افتتاحية عند إضافة المنتج', '4', '2026-05-25 21:27:43');
INSERT INTO `stock_movements` VALUES ('144', '3', 'in', '30.000', '0.000', '30.000', 'manual', NULL, '', '4', '2026-05-25 21:28:38');
INSERT INTO `stock_movements` VALUES ('145', '11', 'in', '4.000', '8.000', '12.000', 'manual', NULL, '', '4', '2026-05-25 21:29:02');
INSERT INTO `stock_movements` VALUES ('146', '4', 'out', '1.000', '21.000', '20.000', 'invoice', '67', NULL, '4', '2026-05-25 21:36:34');
INSERT INTO `stock_movements` VALUES ('147', '9', 'out', '1.000', '15.000', '14.000', 'invoice', '67', NULL, '4', '2026-05-25 21:36:34');
INSERT INTO `stock_movements` VALUES ('148', '4', 'return', '1.000', '20.000', '21.000', 'return', '68', 'مرتجع من فاتورة RAW-00067', '4', '2026-05-27 01:24:06');
INSERT INTO `stock_movements` VALUES ('149', '9', 'return', '1.000', '14.000', '15.000', 'return', '68', 'مرتجع من فاتورة RAW-00067', '4', '2026-05-27 01:24:06');
INSERT INTO `stock_movements` VALUES ('150', '3', 'out', '2.000', '30.000', '28.000', 'invoice', '69', NULL, '4', '2026-05-27 01:28:42');
INSERT INTO `stock_movements` VALUES ('151', '15', 'out', '1.000', '15.000', '14.000', 'invoice', '69', NULL, '4', '2026-05-27 01:28:42');
INSERT INTO `stock_movements` VALUES ('152', '4', 'return', '1.000', '21.000', '22.000', 'return', '70', 'مرتجع من فاتورة RAW-00060', '4', '2026-05-27 01:33:15');
INSERT INTO `stock_movements` VALUES ('153', '4', 'return', '1.000', '22.000', '23.000', 'return', '71', 'مرتجع من فاتورة RAW-00057', '4', '2026-05-27 01:36:24');
INSERT INTO `stock_movements` VALUES ('154', '17', 'return', '1.000', '14.000', '15.000', 'return', '72', 'مرتجع من فاتورة RAW-00049', '4', '2026-05-27 01:41:12');
INSERT INTO `stock_movements` VALUES ('155', '9', 'out', '1.000', '15.000', '14.000', 'invoice', '73', NULL, '4', '2026-05-27 10:05:32');
INSERT INTO `stock_movements` VALUES ('156', '26', 'out', '1.000', '36.000', '35.000', 'invoice', '74', NULL, '4', '2026-05-29 20:17:58');
INSERT INTO `stock_movements` VALUES ('157', '19', 'out', '1.000', '15.000', '14.000', 'invoice', '74', NULL, '4', '2026-05-29 20:17:58');
INSERT INTO `stock_movements` VALUES ('158', '14', 'out', '1.000', '33.000', '32.000', 'invoice', '75', NULL, '4', '2026-05-29 20:18:01');

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE `suppliers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `balance` decimal(12,2) DEFAULT 0.00,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `suppliers` VALUES ('1', 'Mogo Tigchic', '0937624', 'modfguioiuytr@gmail.com', 'iuytrew', NULL, '0.00', '2026-05-18 11:36:15');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `role` enum('admin','cashier','warehouse','supervisor') DEFAULT 'cashier',
  `email` varchar(100) DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `last_login` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` VALUES ('2', 'cashier1', '$2y$10$2DT75B2lQUrVnP2JTKE9ROPdIrxgyLvJWlQXL755BFi6.sgBbk.GW', 'كاشير 1', 'cashier', '', '', '0', '2026-05-27 10:30:39', '2026-05-16 11:23:17');
INSERT INTO `users` VALUES ('4', 'admin', '$2y$10$jMbrj04RC2laTc6BddNgce4llS0lA1jX95MIP972XxbYeantP6AUS', 'مدير النظام', 'admin', NULL, NULL, '1', '2026-05-29 20:17:24', '2026-05-16 11:31:17');

