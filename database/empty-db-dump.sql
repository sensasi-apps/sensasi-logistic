-- --------------------------------------------------------
-- Host:                         localhost
-- Server version:               10.10.2-MariaDB-log - mariadb.org binary distribution
-- Server OS:                    Win64
-- HeidiSQL Version:             12.1.0.6537
-- --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Dumping structure for table senlog_data.failed_jobs
CREATE TABLE IF NOT EXISTS `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.failed_jobs: ~0 rows (approximately)

-- Dumping structure for table senlog_data.manufactures
CREATE TABLE IF NOT EXISTS `manufactures` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) DEFAULT NULL,
  `at` datetime NOT NULL,
  `note` text DEFAULT NULL,
  `material_out_id` bigint(20) unsigned NOT NULL,
  `product_in_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `manufactures_material_out_id_unique` (`material_out_id`),
  UNIQUE KEY `manufactures_product_in_id_unique` (`product_in_id`),
  UNIQUE KEY `manufactures_code_unique` (`code`),
  CONSTRAINT `manufactures_material_out_id_foreign` FOREIGN KEY (`material_out_id`) REFERENCES `material_outs` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `manufactures_product_in_id_foreign` FOREIGN KEY (`product_in_id`) REFERENCES `product_ins` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.manufactures: ~0 rows (approximately)

-- Dumping structure for table senlog_data.materials
CREATE TABLE IF NOT EXISTS `materials` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `tags_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`tags_json`)),
  `unit` varchar(10) NOT NULL,
  `low_qty` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `materials_brand_name_unique` (`brand`,`name`),
  UNIQUE KEY `materials_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.materials: ~0 rows (approximately)

-- Dumping structure for table senlog_data.material_ins
CREATE TABLE IF NOT EXISTS `material_ins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) DEFAULT NULL,
  `at` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_ins_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.material_ins: ~0 rows (approximately)

-- Dumping structure for table senlog_data.material_in_details
CREATE TABLE IF NOT EXISTS `material_in_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_in_id` bigint(20) unsigned NOT NULL,
  `material_id` bigint(20) unsigned NOT NULL,
  `qty` double(8,2) NOT NULL,
  `price` double(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_in_details_material_id_material_in_id_unique` (`material_id`,`material_in_id`),
  KEY `material_in_details_material_in_id_foreign` (`material_in_id`),
  CONSTRAINT `material_in_details_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `material_in_details_material_in_id_foreign` FOREIGN KEY (`material_in_id`) REFERENCES `material_ins` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.material_in_details: ~0 rows (approximately)

-- Dumping structure for view senlog_data.material_in_details_stock_view
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `material_in_details_stock_view` (
	`material_in_detail_id` BIGINT(20) UNSIGNED NOT NULL,
	`qty` DOUBLE(19,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for procedure senlog_data.material_in_details__material_monthly_movements_procedure
DELIMITER //
CREATE PROCEDURE `material_in_details__material_monthly_movements_procedure`(
                IN materialInID int,
                IN materialID int
            )
BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;

                SELECT YEAR(`at`), MONTH(`at`) INTO yearAt, monthAt
                FROM material_ins
                WHERE id = materialInID;

                CALL material_monthly_movements_upsert_in_procedure(materialID, yearAt, monthAt);
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.material_monthly_movements
CREATE TABLE IF NOT EXISTS `material_monthly_movements` (
  `material_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `in` bigint(20) NOT NULL DEFAULT 0,
  `out` bigint(20) NOT NULL DEFAULT 0,
  `avg_in` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_out` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_in_price` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_out_price` double(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`material_id`,`year`,`month`),
  CONSTRAINT `material_monthly_movements_material_id_foreign` FOREIGN KEY (`material_id`) REFERENCES `materials` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.material_monthly_movements: ~0 rows (approximately)

-- Dumping structure for procedure senlog_data.material_monthly_movements_upsert_in_procedure
DELIMITER //
CREATE PROCEDURE `material_monthly_movements_upsert_in_procedure`(
                IN materialID int,
                IN yearAt int,
                IN monthAt int
            )
BEGIN
                INSERT INTO material_monthly_movements
                    (material_id, year, month, `in`, avg_in, avg_in_price)
                SELECT
                    mid.material_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(mid.qty),
                    @avg_qty := AVG(mid.qty),
                    @avg_in_price := AVG(CASE WHEN mid.price > 0 THEN mid.price ELSE NULL END)
                FROM material_ins mi
                JOIN material_in_details mid ON mi.id = mid.material_in_id
                WHERE
                    mid.material_id = materialID AND
                    mid.qty > 0 AND
                    YEAR(mi.at) = yearAt AND
                    MONTH(mi.at) = monthAt
                GROUP BY mid.material_id
                ON DUPLICATE KEY UPDATE `in` = @total_qty, avg_in = @avg_qty, avg_in_price = @avg_in_price;
            END//
DELIMITER ;

-- Dumping structure for procedure senlog_data.material_monthly_movements_upsert_out_procedure
DELIMITER //
CREATE PROCEDURE `material_monthly_movements_upsert_out_procedure`(
                IN materialID int,
                IN yearAt int,
                IN monthAt int
            )
BEGIN
                INSERT INTO material_monthly_movements
                    (material_id, year, month, `out`, avg_out, avg_out_price)
                SELECT
                    mid.material_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(`mod`.qty),
                    @avg_out := AVG(`mod`.qty),
                    @avg_out_price := AVG(CASE WHEN `mid`.price > 0 THEN `mid`.price ELSE NULL END)
                FROM material_in_details AS mid
                JOIN material_out_details AS `mod` ON mid.id = `mod`.material_in_detail_id
                JOIN material_outs AS mo ON `mod`.material_out_id = mo.id
                WHERE
                    mid.material_id = materialID AND
                    YEAR(mo.at) = yearAt AND
                    MONTH(mo.at) = monthAt AND
                    `mod`.qty > 0
                GROUP BY mid.material_id
                ON DUPLICATE KEY UPDATE `out` = @total_qty, avg_out = @avg_out, avg_out_price = @avg_out_price;
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.material_outs
CREATE TABLE IF NOT EXISTS `material_outs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) DEFAULT NULL,
  `at` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_outs_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.material_outs: ~0 rows (approximately)

-- Dumping structure for table senlog_data.material_out_details
CREATE TABLE IF NOT EXISTS `material_out_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `material_in_detail_id` bigint(20) unsigned NOT NULL,
  `material_out_id` bigint(20) unsigned NOT NULL,
  `qty` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `material_out_details_unique` (`material_in_detail_id`,`material_out_id`),
  KEY `material_out_details_material_out_id_foreign` (`material_out_id`),
  CONSTRAINT `material_out_details_material_in_detail_id_foreign` FOREIGN KEY (`material_in_detail_id`) REFERENCES `material_in_details` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `material_out_details_material_out_id_foreign` FOREIGN KEY (`material_out_id`) REFERENCES `material_outs` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.material_out_details: ~0 rows (approximately)

-- Dumping structure for procedure senlog_data.material_out_details__material_monthly_movements_procedure
DELIMITER //
CREATE PROCEDURE `material_out_details__material_monthly_movements_procedure`(
                IN materialOutId int,
                IN materialInDetailId int
            )
BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;
                DECLARE materialID int;

                SELECT YEAR(`mo`.`at`), MONTH(`mo`.`at`), `mid`.material_id INTO yearAt, monthAt, materialID
                FROM material_out_details as `mod`
                LEFT JOIN material_outs AS mo ON `mod`.material_out_id = mo.id
                LEFT JOIN material_in_details AS mid ON `mod`.material_in_detail_id = mid.id
                WHERE `mod`.material_out_id = materialOutId AND `mod`.material_in_detail_id = materialInDetailId;

                CALL material_monthly_movements_upsert_out_procedure(
                    materialID,
                    yearAt,
                    monthAt
                );
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.migrations
CREATE TABLE IF NOT EXISTS `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.migrations: ~23 rows (approximately)
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
	(1, '2014_10_12_000000_create_users_table', 1),
	(2, '2014_10_12_100000_create_password_resets_table', 1),
	(3, '2019_08_19_000000_create_failed_jobs_table', 1),
	(4, '2019_12_14_000001_create_personal_access_tokens_table', 1),
	(5, '2022_10_30_232639_create_permission_tables', 1),
	(6, '2022_11_01_211159_create_materials_table', 1),
	(7, '2022_11_05_110329_create_material_monthly_movements_table', 1),
	(8, '2022_11_06_205447_create_material_ins_table', 1),
	(9, '2022_11_06_210325_create_material_in_details_table', 1),
	(10, '2022_11_14_200911_insert_roles_table', 1),
	(11, '2022_11_20_201514_create_material_outs_table', 1),
	(12, '2022_11_20_201549_create_material_out_details_table', 1),
	(13, '2022_11_22_055729_create_material_in_details_stock_view', 1),
	(14, '2022_11_23_193950_create_products_table', 1),
	(15, '2022_11_23_194009_create_product_monthly_movements_table', 1),
	(16, '2022_11_23_211124_create_product_ins_table', 1),
	(17, '2022_11_23_211150_create_product_in_details_table', 1),
	(18, '2022_11_27_202714_create_product_outs_table', 1),
	(19, '2022_11_27_202850_create_product_out_details_table', 1),
	(20, '2022_11_27_204936_create_product_in_details_stock_vew', 1),
	(21, '2022_11_29_210923_create_manufactures_table', 1),
	(22, '2022_12_23_023622_create_user_activities_table', 1),
	(23, '2022_12_23_025554_add_fa_icon_to_roles_table', 1);

-- Dumping structure for table senlog_data.model_has_permissions
CREATE TABLE IF NOT EXISTS `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.model_has_permissions: ~0 rows (approximately)

-- Dumping structure for table senlog_data.model_has_roles
CREATE TABLE IF NOT EXISTS `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.model_has_roles: ~0 rows (approximately)

-- Dumping structure for table senlog_data.password_resets
CREATE TABLE IF NOT EXISTS `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  KEY `password_resets_email_index` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.password_resets: ~0 rows (approximately)

-- Dumping structure for table senlog_data.permissions
CREATE TABLE IF NOT EXISTS `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.permissions: ~0 rows (approximately)

-- Dumping structure for table senlog_data.personal_access_tokens
CREATE TABLE IF NOT EXISTS `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.personal_access_tokens: ~0 rows (approximately)

-- Dumping structure for table senlog_data.products
CREATE TABLE IF NOT EXISTS `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(10) DEFAULT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `tags_json` varchar(255) DEFAULT NULL,
  `default_price` double(8,2) NOT NULL,
  `low_qty` int(11) DEFAULT NULL,
  `unit` varchar(10) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_brand_name_unique` (`brand`,`name`),
  UNIQUE KEY `products_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.products: ~0 rows (approximately)

-- Dumping structure for table senlog_data.product_ins
CREATE TABLE IF NOT EXISTS `product_ins` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) DEFAULT NULL,
  `at` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_ins_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.product_ins: ~0 rows (approximately)

-- Dumping structure for table senlog_data.product_in_details
CREATE TABLE IF NOT EXISTS `product_in_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_in_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `qty` double(8,2) NOT NULL,
  `price` double(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_in_details_product_id_product_in_id_unique` (`product_id`,`product_in_id`),
  KEY `product_in_details_product_in_id_foreign` (`product_in_id`),
  CONSTRAINT `product_in_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `product_in_details_product_in_id_foreign` FOREIGN KEY (`product_in_id`) REFERENCES `product_ins` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.product_in_details: ~0 rows (approximately)

-- Dumping structure for view senlog_data.product_in_details_stock_view
-- Creating temporary table to overcome VIEW dependency errors
CREATE TABLE `product_in_details_stock_view` (
	`product_in_detail_id` BIGINT(20) UNSIGNED NOT NULL,
	`qty` DOUBLE(19,2) NULL
) ENGINE=MyISAM;

-- Dumping structure for procedure senlog_data.product_in_details__product_monthly_movements_procedure
DELIMITER //
CREATE PROCEDURE `product_in_details__product_monthly_movements_procedure`(
                IN productInID int,
                IN productID int
            )
BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;

                SELECT YEAR(`at`), MONTH(`at`) INTO yearAt, monthAt
                FROM product_ins
                WHERE id = productInID;

                CALL product_monthly_movements_upsert_in_procedure(productID, yearAt, monthAt);
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.product_monthly_movements
CREATE TABLE IF NOT EXISTS `product_monthly_movements` (
  `product_id` bigint(20) unsigned NOT NULL,
  `year` smallint(5) unsigned NOT NULL,
  `month` tinyint(3) unsigned NOT NULL,
  `in` bigint(20) NOT NULL DEFAULT 0,
  `out` bigint(20) NOT NULL DEFAULT 0,
  `avg_in` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_out` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_in_price` double(8,2) NOT NULL DEFAULT 0.00,
  `avg_out_price` double(8,2) NOT NULL DEFAULT 0.00,
  PRIMARY KEY (`product_id`,`year`,`month`),
  CONSTRAINT `product_monthly_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.product_monthly_movements: ~0 rows (approximately)

-- Dumping structure for procedure senlog_data.product_monthly_movements_upsert_in_procedure
DELIMITER //
CREATE PROCEDURE `product_monthly_movements_upsert_in_procedure`(
                IN productID int,
                IN yearAt int,
                IN monthAt int
            )
BEGIN
                INSERT INTO
                    product_monthly_movements (product_id, year, month, `in`, avg_in, avg_in_price)
                SELECT
                    product_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(qty),
                    @avg_qty := AVG(qty),
                    @avg_in_price := AVG(CASE WHEN pid.price > 0 THEN pid.price ELSE NULL END)
                FROM product_ins pi
                LEFT JOIN product_in_details pid ON pi.id = pid.product_in_id
                WHERE
                    pid.product_id = productID AND
                    YEAR(`pi`.at) = yearAt AND
                    MONTH(`pi`.at) = monthAt AND
                    pid.qty > 0
                GROUP BY pid.product_id
                ON DUPLICATE KEY UPDATE `in` = @total_qty, avg_in = @avg_qty, avg_in_price = @avg_in_price;
            END//
DELIMITER ;

-- Dumping structure for procedure senlog_data.product_monthly_movements_upsert_out_procedure
DELIMITER //
CREATE PROCEDURE `product_monthly_movements_upsert_out_procedure`(
            IN productID int,
            IN yearAt int,
            IN monthAt int
        )
BEGIN
                INSERT INTO product_monthly_movements
                    (product_id, year, month, `out`, avg_out, avg_out_price)
                SELECT
                    pid.product_id,
                    yearAt,
                    monthAt,
                    @total_qty := SUM(`pod`.qty),
                    @avg_qty := AVG(`pod`.qty),
                    @avg_out_price := AVG(CASE WHEN `pod`.price > 0 THEN `pod`.price ELSE NULL END)
                FROM product_in_details AS pid
                JOIN product_out_details AS `pod` ON pid.id = `pod`.product_in_detail_id
                JOIN product_outs AS po ON `pod`.product_out_id = po.id
                WHERE
                    pid.product_id = productID AND
                    YEAR(po.at) = yearAt AND
                    MONTH(po.at) = monthAt AND
                    `pod`.qty > 0
                GROUP BY pid.product_id
                ON DUPLICATE KEY UPDATE `out` = @total_qty, avg_out = @avg_qty;
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.product_outs
CREATE TABLE IF NOT EXISTS `product_outs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(15) DEFAULT NULL,
  `at` datetime NOT NULL,
  `type` varchar(255) NOT NULL,
  `note` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_outs_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.product_outs: ~0 rows (approximately)

-- Dumping structure for table senlog_data.product_out_details
CREATE TABLE IF NOT EXISTS `product_out_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_in_detail_id` bigint(20) unsigned NOT NULL,
  `product_out_id` bigint(20) unsigned NOT NULL,
  `qty` double(8,2) NOT NULL,
  `price` double(8,2) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_out_details_unique` (`product_in_detail_id`,`product_out_id`),
  KEY `product_out_details_product_out_id_foreign` (`product_out_id`),
  CONSTRAINT `product_out_details_product_in_detail_id_foreign` FOREIGN KEY (`product_in_detail_id`) REFERENCES `product_in_details` (`id`) ON UPDATE CASCADE,
  CONSTRAINT `product_out_details_product_out_id_foreign` FOREIGN KEY (`product_out_id`) REFERENCES `product_outs` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.product_out_details: ~0 rows (approximately)

-- Dumping structure for procedure senlog_data.product_out_details__product_monthly_movements_procedure
DELIMITER //
CREATE PROCEDURE `product_out_details__product_monthly_movements_procedure`(
                IN productOutId int,
                IN productInDetailId int
            )
BEGIN
                DECLARE yearAt int;
                DECLARE monthAt int;
                DECLARE productID int;

                SELECT YEAR(`po`.`at`), MONTH(`po`.`at`), `pid`.product_id INTO yearAt, monthAt, productID
                FROM product_out_details as `pod`
                LEFT JOIN product_outs AS po ON `pod`.product_out_id = po.id
                LEFT JOIN product_in_details AS pid ON `pod`.product_in_detail_id = pid.id
                WHERE `pod`.product_out_id = productOutId AND `pod`.product_in_detail_id = productInDetailId;

                CALL product_monthly_movements_upsert_out_procedure(
                    productID,
                    yearAt,
                    monthAt
                );
            END//
DELIMITER ;

-- Dumping structure for table senlog_data.roles
CREATE TABLE IF NOT EXISTS `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `fa_icon` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.roles: ~0 rows (approximately)
INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`, `fa_icon`) VALUES
	(1, 'Super Admin', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL),
	(2, 'Admin', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL),
	(3, 'Stackholder', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL),
	(4, 'Manufacture', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL),
	(5, 'Sales', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL),
	(6, 'Warehouse', 'web', '2023-03-13 15:51:09', '2023-03-13 15:51:09', NULL);

-- Dumping structure for table senlog_data.role_has_permissions
CREATE TABLE IF NOT EXISTS `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.role_has_permissions: ~0 rows (approximately)

-- Dumping structure for table senlog_data.users
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_google_id_unique` (`google_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.users: ~0 rows (approximately)

-- Dumping structure for table senlog_data.user_activities
CREATE TABLE IF NOT EXISTS `user_activities` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `action` varchar(255) NOT NULL,
  `model` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) DEFAULT NULL,
  `value` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`value`)),
  `at` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip` varchar(255) DEFAULT NULL,
  `browser` varchar(255) DEFAULT NULL,
  `os` varchar(255) DEFAULT NULL,
  `device` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `user_activities_user_id_foreign` (`user_id`),
  CONSTRAINT `user_activities_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for table senlog_data.user_activities: ~0 rows (approximately)

-- Dumping structure for trigger senlog_data.material_ins_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_ins_after_update_trigger
            AFTER UPDATE
            ON material_ins
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE material_id INT;

                DECLARE cur CURSOR FOR SELECT material_id FROM material_in_details WHERE material_in_id = OLD.id;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO material_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL material_monthly_movements_upsert_in_procedure(material_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL material_monthly_movements_upsert_in_procedure(material_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;

            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_in_details_after_delete_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_in_details_after_delete_trigger
                AFTER DELETE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(OLD.material_in_id, OLD.material_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_in_details_after_insert_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_in_details_after_insert_trigger
                AFTER INSERT
                ON material_in_details
                FOR EACH ROW
            BEGIN
                CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_in_details_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_in_details_after_update_trigger
                AFTER UPDATE
                ON material_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.material_id = OLD.material_id THEN
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
                END IF;

                IF NEW.material_id <> OLD.material_id THEN
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, OLD.material_id);
                    CALL material_in_details__material_monthly_movements_procedure(NEW.material_in_id, NEW.material_id);
                END IF;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_outs_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_outs_after_update_trigger
            AFTER UPDATE
            ON material_outs
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE material_id INT;

                DECLARE cur CURSOR FOR SELECT
                    `mid`.material_id
                FROM material_out_details AS `mod`
                LEFT JOIN material_in_details AS mid ON `mod`.material_in_detail_id = mid.id
                WHERE material_out_id = OLD.id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO material_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL material_monthly_movements_upsert_out_procedure(material_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL material_monthly_movements_upsert_out_procedure(material_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_out_details_after_delete_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_out_details_after_delete_trigger
                AFTER DELETE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(OLD.material_out_id, OLD.material_in_detail_id);

            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_out_details_after_insert_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_out_details_after_insert_trigger
                AFTER INSERT
                ON material_out_details
                FOR EACH ROW
            BEGIN
                CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.material_out_details_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER material_out_details_after_update_trigger
                AFTER UPDATE
                ON material_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.material_in_detail_id = OLD.material_in_detail_id THEN
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
                END IF;

                IF NEW.material_in_detail_id <> OLD.material_in_detail_id THEN
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, OLD.material_in_detail_id);
                    CALL material_out_details__material_monthly_movements_procedure(NEW.material_out_id, NEW.material_in_detail_id);
                END IF;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_ins_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_ins_after_update_trigger
                AFTER UPDATE
                ON product_ins
                FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE product_id INT;

                DECLARE cur CURSOR FOR SELECT product_id FROM product_in_details WHERE product_in_id = OLD.id;
                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO product_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL product_monthly_movements_upsert_in_procedure(product_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL product_monthly_movements_upsert_in_procedure(product_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_in_details_after_delete_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_in_details_after_delete_trigger
                AFTER DELETE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(OLD.product_in_id, OLD.product_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_in_details_after_insert_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_in_details_after_insert_trigger
                AFTER INSERT
                ON product_in_details
                FOR EACH ROW
            BEGIN
                CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_in_details_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_in_details_after_update_trigger
                AFTER UPDATE
                ON product_in_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.product_id = OLD.product_id THEN
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
                END IF;

                IF NEW.product_id <> OLD.product_id THEN
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, OLD.product_id);
                    CALL product_in_details__product_monthly_movements_procedure(NEW.product_in_id, NEW.product_id);
                END IF;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_outs_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_outs_after_update_trigger
            AFTER UPDATE
            ON product_outs
            FOR EACH ROW
            BEGIN
                DECLARE done INT DEFAULT FALSE;
                DECLARE product_id INT;

                DECLARE cur CURSOR FOR SELECT
                    `pid`.product_id
                FROM product_out_details AS `pod`
                LEFT JOIN product_in_details AS pid ON `pod`.product_in_detail_id = pid.id
                WHERE product_out_id = OLD.id;

                DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;

                SET @is_at_changed = YEAR(NEW.at) <> YEAR(OLD.at) OR MONTH(NEW.at) <> MONTH(OLD.at);

                OPEN cur;

                read_loop: LOOP
                    FETCH cur INTO product_id;
                    IF done THEN
                        LEAVE read_loop;
                    END IF;

                    IF @is_at_changed THEN
                        CALL product_monthly_movements_upsert_out_procedure(product_id, YEAR(OLD.at), MONTH(OLD.at));
                        CALL product_monthly_movements_upsert_out_procedure(product_id, YEAR(NEW.at), MONTH(NEW.at));
                    END IF;
                END LOOP;

                CLOSE cur;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_out_details_after_delete_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_out_details_after_delete_trigger
                AFTER DELETE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(OLD.product_out_id, OLD.product_in_detail_id);

            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_out_details_after_insert_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_out_details_after_insert_trigger
                AFTER INSERT
                ON product_out_details
                FOR EACH ROW
            BEGIN
                CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for trigger senlog_data.product_out_details_after_update_trigger
SET @OLDTMP_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';
DELIMITER //
CREATE TRIGGER product_out_details_after_update_trigger
                AFTER UPDATE
                ON product_out_details
                FOR EACH ROW
            BEGIN
                IF NEW.qty <> OLD.qty AND NEW.product_in_detail_id = OLD.product_in_detail_id THEN
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
                END IF;

                IF NEW.product_in_detail_id <> OLD.product_in_detail_id THEN
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, OLD.product_in_detail_id);
                    CALL product_out_details__product_monthly_movements_procedure(NEW.product_out_id, NEW.product_in_detail_id);
                END IF;
            END//
DELIMITER ;
SET SQL_MODE=@OLDTMP_SQL_MODE;

-- Dumping structure for view senlog_data.material_in_details_stock_view
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `material_in_details_stock_view`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `material_in_details_stock_view` AS SELECT
                mid.id as material_in_detail_id,
                `mid`.`qty` - COALESCE(SUM(`mod`.`qty`), 0) as qty
            FROM
                material_in_details AS `mid`
                LEFT JOIN material_out_details AS `mod` ON mid.id = `mod`.material_in_detail_id
                LEFT JOIN material_outs AS `mo` ON `mod`.material_out_id = `mo`.id
                LEFT JOIN material_ins AS `mi` ON `mid`.material_in_id = `mi`.id
            GROUP BY
                mid.id, mid.qty ;

-- Dumping structure for view senlog_data.product_in_details_stock_view
-- Removing temporary table and create final VIEW structure
DROP TABLE IF EXISTS `product_in_details_stock_view`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY DEFINER VIEW `product_in_details_stock_view` AS SELECT
                pid.id as product_in_detail_id,
                `pid`.`qty` - COALESCE(SUM(`pod`.`qty`), 0) as qty
            FROM
                product_in_details AS `pid`
                LEFT JOIN product_out_details AS `pod` ON pid.id = `pod`.product_in_detail_id
                LEFT JOIN product_outs AS `po` ON `pod`.product_out_id = `po`.id
                LEFT JOIN product_ins AS `pi` ON `pid`.product_in_id = `pi`.id
            GROUP BY
                pid.id, pid.qty ;

/*!40103 SET TIME_ZONE=IFNULL(@OLD_TIME_ZONE, 'system') */;
/*!40101 SET SQL_MODE=IFNULL(@OLD_SQL_MODE, '') */;
/*!40014 SET FOREIGN_KEY_CHECKS=IFNULL(@OLD_FOREIGN_KEY_CHECKS, 1) */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40111 SET SQL_NOTES=IFNULL(@OLD_SQL_NOTES, 1) */;
