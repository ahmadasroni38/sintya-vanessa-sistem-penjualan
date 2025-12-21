/*
SQLyog Community v13.2.0 (64 bit)
MySQL - 10.6.22-MariaDB-0ubuntu0.22.04.1 : Database - db_ta_sintiya_2025
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`db_ta_sintiya_2025` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci */;

USE `db_ta_sintiya_2025`;

/*Table structure for table `account_balance_histories` */

DROP TABLE IF EXISTS `account_balance_histories`;

CREATE TABLE `account_balance_histories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chart_of_account_id` bigint(20) unsigned NOT NULL,
  `balance` decimal(15,2) NOT NULL,
  `debit_total` decimal(15,2) NOT NULL,
  `credit_total` decimal(15,2) NOT NULL,
  `period_start` date NOT NULL,
  `period_end` date NOT NULL,
  `calculated_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_balance_histories_account_period` (`chart_of_account_id`,`period_start`,`period_end`),
  KEY `idx_balance_histories_account_period_start` (`chart_of_account_id`,`period_start`),
  KEY `idx_balance_histories_account_period_end` (`chart_of_account_id`,`period_end`),
  KEY `idx_balance_histories_period_start` (`period_start`),
  KEY `idx_balance_histories_period_end` (`period_end`),
  KEY `idx_balance_histories_calculated_by` (`calculated_by`),
  KEY `idx_balance_histories_created_at` (`created_at`),
  CONSTRAINT `account_balance_histories_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `chart_of_account_audits` */

DROP TABLE IF EXISTS `chart_of_account_audits`;

CREATE TABLE `chart_of_account_audits` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `chart_of_account_id` bigint(20) unsigned NOT NULL,
  `event_type` varchar(255) NOT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_audits_account_event` (`chart_of_account_id`,`event_type`),
  KEY `idx_audits_user_event` (`user_id`,`event_type`),
  KEY `idx_audits_created_at` (`created_at`),
  KEY `idx_audits_account_created` (`chart_of_account_id`,`created_at`),
  CONSTRAINT `chart_of_account_audits_chart_of_account_id_foreign` FOREIGN KEY (`chart_of_account_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chart_of_account_audits_chk_1` CHECK (json_valid(`old_values`)),
  CONSTRAINT `chart_of_account_audits_chk_2` CHECK (json_valid(`new_values`))
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `chart_of_accounts` */

DROP TABLE IF EXISTS `chart_of_accounts`;

CREATE TABLE `chart_of_accounts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `account_code` varchar(20) NOT NULL,
  `account_name` varchar(255) NOT NULL,
  `account_type` enum('asset','liability','equity','revenue','expense') NOT NULL,
  `normal_balance` enum('debit','credit') NOT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `level` int(11) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `description` text DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL COMMENT 'Additional account metadata',
  `opening_balance` decimal(15,2) NOT NULL DEFAULT 0.00,
  `current_balance` decimal(15,2) NOT NULL DEFAULT 0.00 COMMENT 'Cached balance for performance',
  `balance_updated_at` timestamp NULL DEFAULT NULL COMMENT 'Last time balance was calculated',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` varchar(255) DEFAULT NULL COMMENT 'User who created the account',
  `updated_by` varchar(255) DEFAULT NULL COMMENT 'User who last updated the account',
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `chart_of_accounts_account_code_unique` (`account_code`),
  KEY `idx_chart_accounts_code_active` (`account_code`,`is_active`),
  KEY `idx_chart_accounts_type_active` (`account_type`,`is_active`),
  KEY `idx_chart_accounts_parent_level` (`parent_id`,`level`),
  KEY `idx_chart_accounts_balance_updated` (`balance_updated_at`),
  KEY `idx_chart_accounts_created_by` (`created_by`),
  KEY `idx_chart_accounts_updated_by` (`updated_by`),
  FULLTEXT KEY `idx_chart_accounts_name_fulltext` (`account_name`),
  CONSTRAINT `chart_of_accounts_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `chart_of_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `chart_of_accounts_chk_1` CHECK (json_valid(`metadata`)),
  CONSTRAINT `check_current_balance` CHECK (`current_balance` >= 0),
  CONSTRAINT `check_level_range` CHECK (`level` >= 1 and `level` <= 5),
  CONSTRAINT `check_opening_balance` CHECK (`opening_balance` >= 0)
) ENGINE=InnoDB AUTO_INCREMENT=42 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `customers` */

DROP TABLE IF EXISTS `customers`;

CREATE TABLE `customers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `customer_code` varchar(20) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `customers_customer_code_unique` (`customer_code`),
  KEY `customers_status_customer_code_index` (`status`,`customer_code`),
  KEY `customers_customer_name_index` (`customer_name`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `failed_jobs` */

DROP TABLE IF EXISTS `failed_jobs`;

CREATE TABLE `failed_jobs` (
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

/*Table structure for table `journal_entries` */

DROP TABLE IF EXISTS `journal_entries`;

CREATE TABLE `journal_entries` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `entry_number` varchar(255) NOT NULL,
  `entry_date` date NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `description` text NOT NULL,
  `entry_type` enum('general','adjustment','closing','opening') NOT NULL DEFAULT 'general',
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `total_debit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_credit` decimal(15,2) NOT NULL DEFAULT 0.00,
  `currency` varchar(3) NOT NULL DEFAULT 'IDR',
  `exchange_rate` decimal(10,4) NOT NULL DEFAULT 1.0000,
  `created_by` bigint(20) unsigned NOT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `updated_by` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `journal_entries_entry_number_unique` (`entry_number`),
  KEY `journal_entries_posted_by_foreign` (`posted_by`),
  KEY `journal_entries_approved_by_foreign` (`approved_by`),
  KEY `journal_entries_updated_by_foreign` (`updated_by`),
  KEY `journal_entries_entry_date_status_index` (`entry_date`,`status`),
  KEY `journal_entries_entry_type_status_index` (`entry_type`,`status`),
  KEY `journal_entries_created_by_status_index` (`created_by`,`status`),
  CONSTRAINT `journal_entries_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entries_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entries_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `journal_entry_approvals` */

DROP TABLE IF EXISTS `journal_entry_approvals`;

CREATE TABLE `journal_entry_approvals` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `notes` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_approvals_journal_entry_id_status_index` (`journal_entry_id`,`status`),
  KEY `journal_entry_approvals_user_id_status_index` (`user_id`,`status`),
  KEY `journal_entry_approvals_status_created_at_index` (`status`,`created_at`),
  CONSTRAINT `journal_entry_approvals_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_approvals_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `journal_entry_attachments` */

DROP TABLE IF EXISTS `journal_entry_attachments`;

CREATE TABLE `journal_entry_attachments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint(20) unsigned NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `file_size` bigint(20) unsigned NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_attachments_journal_entry_id_index` (`journal_entry_id`),
  KEY `journal_entry_attachments_uploaded_by_index` (`uploaded_by`),
  KEY `journal_entry_attachments_mime_type_index` (`mime_type`),
  CONSTRAINT `journal_entry_attachments_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `journal_entry_details` */

DROP TABLE IF EXISTS `journal_entry_details`;

CREATE TABLE `journal_entry_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint(20) unsigned NOT NULL,
  `account_id` bigint(20) unsigned NOT NULL,
  `transaction_type` enum('debit','credit') NOT NULL,
  `amount` decimal(15,2) NOT NULL,
  `debit_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `credit_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `quantity` decimal(15,4) DEFAULT NULL,
  `unit_price` decimal(15,2) DEFAULT NULL,
  `tax_rate` decimal(5,2) DEFAULT NULL,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `reconciliation_id` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `department_id` bigint(20) unsigned DEFAULT NULL,
  `project_id` bigint(20) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `journal_entry_details_department_id_foreign` (`department_id`),
  KEY `journal_entry_details_journal_entry_id_account_id_index` (`journal_entry_id`,`account_id`),
  KEY `journal_entry_details_account_id_transaction_type_index` (`account_id`,`transaction_type`),
  KEY `journal_entry_details_reconciliation_id_index` (`reconciliation_id`),
  CONSTRAINT `journal_entry_details_account_id_foreign` FOREIGN KEY (`account_id`) REFERENCES `chart_of_accounts` (`id`),
  CONSTRAINT `journal_entry_details_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `journal_entry_details_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `journal_entry_revisions` */

DROP TABLE IF EXISTS `journal_entry_revisions`;

CREATE TABLE `journal_entry_revisions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `journal_entry_id` bigint(20) unsigned NOT NULL,
  `revision_number` int(11) NOT NULL,
  `changes` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `revised_by` bigint(20) unsigned NOT NULL,
  `revision_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `journal_entry_revisions_journal_entry_id_revision_number_index` (`journal_entry_id`,`revision_number`),
  KEY `journal_entry_revisions_revised_by_index` (`revised_by`),
  KEY `journal_entry_revisions_created_at_index` (`created_at`),
  CONSTRAINT `journal_entry_revisions_journal_entry_id_foreign` FOREIGN KEY (`journal_entry_id`) REFERENCES `journal_entries` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_revisions_revised_by_foreign` FOREIGN KEY (`revised_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `journal_entry_revisions_chk_1` CHECK (json_valid(`changes`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `locations` */

DROP TABLE IF EXISTS `locations`;

CREATE TABLE `locations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `code` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `state` varchar(255) DEFAULT NULL,
  `country` varchar(255) DEFAULT NULL,
  `postal_code` varchar(255) DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `color` varchar(7) NOT NULL DEFAULT '#10B981',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `locations_code_unique` (`code`),
  KEY `locations_is_active_index` (`is_active`),
  KEY `locations_parent_id_index` (`parent_id`),
  KEY `locations_name_is_active_index` (`name`,`is_active`),
  KEY `locations_city_is_active_index` (`city`,`is_active`),
  KEY `locations_country_is_active_index` (`country`,`is_active`),
  CONSTRAINT `locations_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `locations_chk_1` CHECK (json_valid(`metadata`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `migrations` */

DROP TABLE IF EXISTS `migrations`;

CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `password_reset_otps` */

DROP TABLE IF EXISTS `password_reset_otps`;

CREATE TABLE `password_reset_otps` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `otp` varchar(60) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `used_at` timestamp NULL DEFAULT NULL,
  `attempts` tinyint(4) NOT NULL DEFAULT 0,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `password_reset_otps_email_expires_at_index` (`email`,`expires_at`),
  KEY `password_reset_otps_email_index` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `password_reset_tokens` */

DROP TABLE IF EXISTS `password_reset_tokens`;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `group` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_unique` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `personal_access_tokens` */

DROP TABLE IF EXISTS `personal_access_tokens`;

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) unsigned NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `product_categories` */

DROP TABLE IF EXISTS `product_categories`;

CREATE TABLE `product_categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `product_categories_code_unique` (`code`),
  KEY `product_categories_code_index` (`code`),
  KEY `product_categories_parent_id_index` (`parent_id`),
  KEY `product_categories_is_active_index` (`is_active`),
  CONSTRAINT `product_categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `product_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `products` */

DROP TABLE IF EXISTS `products`;

CREATE TABLE `products` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_code` varchar(50) NOT NULL,
  `product_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `product_type` enum('raw_material','finished_goods','consumable') NOT NULL DEFAULT 'raw_material',
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `unit_id` bigint(20) unsigned NOT NULL,
  `purchase_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `selling_price` decimal(15,2) DEFAULT NULL,
  `minimum_stock` int(11) NOT NULL DEFAULT 0,
  `maximum_stock` int(11) NOT NULL DEFAULT 0,
  `location_id` bigint(20) unsigned DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `products_product_code_unique` (`product_code`),
  KEY `products_location_id_foreign` (`location_id`),
  KEY `products_product_code_index` (`product_code`),
  KEY `products_product_name_index` (`product_name`),
  KEY `products_product_type_index` (`product_type`),
  KEY `products_category_id_index` (`category_id`),
  KEY `products_unit_id_index` (`unit_id`),
  KEY `products_is_active_index` (`is_active`),
  KEY `products_created_at_index` (`created_at`),
  KEY `products_product_type_is_active_index` (`product_type`,`is_active`),
  CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `product_categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE SET NULL,
  CONSTRAINT `products_unit_id_foreign` FOREIGN KEY (`unit_id`) REFERENCES `units` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `role_permissions` */

DROP TABLE IF EXISTS `role_permissions`;

CREATE TABLE `role_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `role_permissions_role_id_permission_id_unique` (`role_id`,`permission_id`),
  KEY `role_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `role_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=94 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `display_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `permissions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_unique` (`name`),
  CONSTRAINT `roles_chk_1` CHECK (json_valid(`permissions`))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `sale_details` */

DROP TABLE IF EXISTS `sale_details`;

CREATE TABLE `sale_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `sale_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_price` decimal(15,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sale_details_product_id_foreign` (`product_id`),
  KEY `sale_details_sale_id_product_id_index` (`sale_id`,`product_id`),
  CONSTRAINT `sale_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sale_details_sale_id_foreign` FOREIGN KEY (`sale_id`) REFERENCES `sales` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `sales` */

DROP TABLE IF EXISTS `sales`;

CREATE TABLE `sales` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(20) NOT NULL,
  `transaction_date` date NOT NULL,
  `customer_id` bigint(20) unsigned DEFAULT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `subtotal` decimal(15,2) NOT NULL DEFAULT 0.00,
  `tax_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `paid_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `change_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `payment_method` enum('cash','transfer','credit') NOT NULL DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sales_transaction_number_unique` (`transaction_number`),
  KEY `sales_location_id_foreign` (`location_id`),
  KEY `sales_created_by_foreign` (`created_by`),
  KEY `sales_posted_by_foreign` (`posted_by`),
  KEY `sales_status_transaction_date_index` (`status`,`transaction_date`),
  KEY `sales_customer_id_status_index` (`customer_id`,`status`),
  KEY `sales_transaction_number_index` (`transaction_number`),
  CONSTRAINT `sales_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `sales_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `sales_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `settings` */

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `logo_sistem` varchar(255) DEFAULT NULL,
  `nama_sistem` varchar(255) NOT NULL,
  `deskripsi_sistem` text NOT NULL,
  `nama_perusahaan` varchar(255) NOT NULL,
  `alamat_lengkap` text NOT NULL,
  `email_perusahaan` varchar(255) NOT NULL,
  `nomor_telepon` varchar(255) NOT NULL,
  `footer_text` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_adjustment_details` */

DROP TABLE IF EXISTS `stock_adjustment_details`;

CREATE TABLE `stock_adjustment_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_adjustment_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `system_quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `actual_quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `difference_quantity` decimal(15,2) NOT NULL DEFAULT 0.00,
  `adjustment_type` enum('increase','decrease') NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_adjustment_details_product_id_foreign` (`product_id`),
  KEY `stock_adjustment_details_stock_adjustment_id_product_id_index` (`stock_adjustment_id`,`product_id`),
  CONSTRAINT `stock_adjustment_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_adjustment_details_stock_adjustment_id_foreign` FOREIGN KEY (`stock_adjustment_id`) REFERENCES `stock_adjustments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_adjustments` */

DROP TABLE IF EXISTS `stock_adjustments`;

CREATE TABLE `stock_adjustments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `adjustment_number` varchar(255) NOT NULL,
  `adjustment_date` date NOT NULL,
  `description` text DEFAULT NULL,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `product_id` bigint(20) unsigned DEFAULT NULL,
  `system_quantity` decimal(15,2) DEFAULT NULL,
  `actual_quantity` decimal(15,2) DEFAULT NULL,
  `difference_quantity` decimal(15,2) DEFAULT NULL,
  `adjustment_type` enum('increase','decrease') DEFAULT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_adjustments_adjustment_number_unique` (`adjustment_number`),
  KEY `stock_adjustments_location_id_foreign` (`location_id`),
  KEY `stock_adjustments_created_by_foreign` (`created_by`),
  KEY `stock_adjustments_approved_by_foreign` (`approved_by`),
  CONSTRAINT `stock_adjustments_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_adjustments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_adjustments_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_balances` */

DROP TABLE IF EXISTS `stock_balances`;

CREATE TABLE `stock_balances` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `current_balance` decimal(10,2) NOT NULL DEFAULT 0.00,
  `minimum_stock` decimal(10,2) DEFAULT NULL,
  `maximum_stock` decimal(10,2) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'in_stock',
  `last_transaction_date` date DEFAULT NULL,
  `last_transaction_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_balances_location_id_foreign` (`location_id`),
  KEY `stock_balances_product_id_location_id_index` (`product_id`,`location_id`),
  KEY `stock_balances_status_index` (`status`),
  CONSTRAINT `stock_balances_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_balances_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_cards` */

DROP TABLE IF EXISTS `stock_cards`;

CREATE TABLE `stock_cards` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `product_id` bigint(20) unsigned NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `transaction_date` date NOT NULL,
  `transaction_type` varchar(255) NOT NULL,
  `reference_id` bigint(20) unsigned DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `quantity_in` int(11) NOT NULL DEFAULT 0,
  `quantity_out` int(11) NOT NULL DEFAULT 0,
  `balance` int(11) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_cards_product_id_location_id_transaction_date_index` (`product_id`,`location_id`,`transaction_date`),
  KEY `stock_cards_location_id_transaction_date_index` (`location_id`,`transaction_date`),
  KEY `stock_cards_transaction_type_transaction_date_index` (`transaction_type`,`transaction_date`),
  KEY `stock_cards_transaction_date_index` (`transaction_date`),
  KEY `stock_cards_reference_number_index` (`reference_number`),
  CONSTRAINT `stock_cards_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `stock_cards_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_in` */

DROP TABLE IF EXISTS `stock_in`;

CREATE TABLE `stock_in` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `supplier_name` varchar(255) DEFAULT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','posted','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `posted_by` bigint(20) unsigned DEFAULT NULL,
  `posted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_in_transaction_number_unique` (`transaction_number`),
  KEY `stock_in_location_id_foreign` (`location_id`),
  KEY `stock_in_created_by_foreign` (`created_by`),
  KEY `stock_in_posted_by_foreign` (`posted_by`),
  CONSTRAINT `stock_in_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_in_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `stock_in_posted_by_foreign` FOREIGN KEY (`posted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_in_details` */

DROP TABLE IF EXISTS `stock_in_details`;

CREATE TABLE `stock_in_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_in_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `unit_price` decimal(15,2) NOT NULL,
  `total_price` decimal(15,2) NOT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_in_details_stock_in_id_index` (`stock_in_id`),
  KEY `stock_in_details_product_id_index` (`product_id`),
  CONSTRAINT `stock_in_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_in_details_stock_in_id_foreign` FOREIGN KEY (`stock_in_id`) REFERENCES `stock_in` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_mutation_details` */

DROP TABLE IF EXISTS `stock_mutation_details`;

CREATE TABLE `stock_mutation_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_mutation_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `quantity` decimal(15,2) NOT NULL,
  `available_stock` decimal(15,2) NOT NULL DEFAULT 0.00,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_mutation_details_stock_mutation_id_index` (`stock_mutation_id`),
  KEY `stock_mutation_details_product_id_index` (`product_id`),
  CONSTRAINT `stock_mutation_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_mutation_details_stock_mutation_id_foreign` FOREIGN KEY (`stock_mutation_id`) REFERENCES `stock_mutations` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_mutations` */

DROP TABLE IF EXISTS `stock_mutations`;

CREATE TABLE `stock_mutations` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `transaction_number` varchar(255) NOT NULL,
  `transaction_date` date NOT NULL,
  `from_location_id` bigint(20) unsigned NOT NULL,
  `to_location_id` bigint(20) unsigned NOT NULL,
  `reference_number` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','pending','approved','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `submitted_by` bigint(20) unsigned DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) unsigned DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_mutations_transaction_number_unique` (`transaction_number`),
  KEY `stock_mutations_created_by_foreign` (`created_by`),
  KEY `stock_mutations_submitted_by_foreign` (`submitted_by`),
  KEY `stock_mutations_approved_by_foreign` (`approved_by`),
  KEY `stock_mutations_completed_by_foreign` (`completed_by`),
  KEY `stock_mutations_transaction_number_index` (`transaction_number`),
  KEY `stock_mutations_from_location_id_index` (`from_location_id`),
  KEY `stock_mutations_to_location_id_index` (`to_location_id`),
  KEY `stock_mutations_status_index` (`status`),
  KEY `stock_mutations_transaction_date_index` (`transaction_date`),
  CONSTRAINT `stock_mutations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_mutations_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_mutations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_mutations_from_location_id_foreign` FOREIGN KEY (`from_location_id`) REFERENCES `locations` (`id`),
  CONSTRAINT `stock_mutations_submitted_by_foreign` FOREIGN KEY (`submitted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_mutations_to_location_id_foreign` FOREIGN KEY (`to_location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_opname_details` */

DROP TABLE IF EXISTS `stock_opname_details`;

CREATE TABLE `stock_opname_details` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `stock_opname_id` bigint(20) unsigned NOT NULL,
  `product_id` bigint(20) unsigned NOT NULL,
  `system_quantity` int(11) NOT NULL,
  `physical_quantity` int(11) NOT NULL,
  `difference_quantity` int(11) NOT NULL,
  `adjustment_type` enum('increase','decrease') DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `counted_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `stock_opname_details_stock_opname_id_foreign` (`stock_opname_id`),
  KEY `stock_opname_details_product_id_foreign` (`product_id`),
  KEY `stock_opname_details_counted_by_foreign` (`counted_by`),
  CONSTRAINT `stock_opname_details_counted_by_foreign` FOREIGN KEY (`counted_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_opname_details_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`),
  CONSTRAINT `stock_opname_details_stock_opname_id_foreign` FOREIGN KEY (`stock_opname_id`) REFERENCES `stock_opnames` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `stock_opnames` */

DROP TABLE IF EXISTS `stock_opnames`;

CREATE TABLE `stock_opnames` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `opname_number` varchar(255) NOT NULL,
  `opname_date` date NOT NULL,
  `location_id` bigint(20) unsigned NOT NULL,
  `total_items` int(11) NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` enum('draft','in_progress','completed','cancelled') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) unsigned NOT NULL,
  `completed_by` bigint(20) unsigned DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `stock_opnames_opname_number_unique` (`opname_number`),
  KEY `stock_opnames_location_id_foreign` (`location_id`),
  KEY `stock_opnames_created_by_foreign` (`created_by`),
  KEY `stock_opnames_completed_by_foreign` (`completed_by`),
  CONSTRAINT `stock_opnames_completed_by_foreign` FOREIGN KEY (`completed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `stock_opnames_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `stock_opnames_location_id_foreign` FOREIGN KEY (`location_id`) REFERENCES `locations` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `units` */

DROP TABLE IF EXISTS `units`;

CREATE TABLE `units` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `symbol` varchar(10) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `units_code_unique` (`code`),
  KEY `units_code_index` (`code`),
  KEY `units_is_active_index` (`is_active`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_permissions` */

DROP TABLE IF EXISTS `user_permissions`;

CREATE TABLE `user_permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `permission_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_permissions_user_id_permission_id_unique` (`user_id`,`permission_id`),
  KEY `user_permissions_permission_id_foreign` (`permission_id`),
  CONSTRAINT `user_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_permissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `user_roles` */

DROP TABLE IF EXISTS `user_roles`;

CREATE TABLE `user_roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `assigned_at` datetime NOT NULL DEFAULT current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_roles_user_id_role_id_unique` (`user_id`,`role_id`),
  KEY `user_roles_role_id_foreign` (`role_id`),
  KEY `user_roles_user_id_is_active_index` (`user_id`,`is_active`),
  CONSTRAINT `user_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_roles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `date_of_birth` date DEFAULT NULL,
  `phone_number` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `avatar_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

/* Trigger structure for table `chart_of_accounts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `check_chart_of_accounts_circular_reference` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `check_chart_of_accounts_circular_reference` BEFORE INSERT ON `chart_of_accounts` FOR EACH ROW BEGIN
                DECLARE ancestor_id INT;
                DECLARE ancestor_code VARCHAR(20);

                SET ancestor_id = NEW.parent_id;

                WHILE ancestor_id IS NOT NULL DO
                    SELECT account_code INTO ancestor_code FROM chart_of_accounts WHERE id = ancestor_id;

                    IF ancestor_id = NEW.id THEN
                        SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Circular reference detected in account hierarchy';
                    END IF;

                    SELECT parent_id INTO ancestor_id FROM chart_of_accounts WHERE id = ancestor_id;
                END WHILE;
            END */$$


DELIMITER ;

/* Trigger structure for table `chart_of_accounts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `set_chart_of_accounts_created_by` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `set_chart_of_accounts_created_by` BEFORE INSERT ON `chart_of_accounts` FOR EACH ROW BEGIN
                SET NEW.created_by = COALESCE(@current_user_id, 'system');
                SET NEW.updated_by = COALESCE(@current_user_id, 'system');
            END */$$


DELIMITER ;

/* Trigger structure for table `chart_of_accounts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `check_chart_of_accounts_circular_reference_update` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `check_chart_of_accounts_circular_reference_update` BEFORE UPDATE ON `chart_of_accounts` FOR EACH ROW BEGIN
                DECLARE ancestor_id INT;
                DECLARE ancestor_code VARCHAR(20);

                IF NEW.parent_id IS NOT NULL AND NEW.parent_id != OLD.parent_id THEN
                    SET ancestor_id = NEW.parent_id;

                    WHILE ancestor_id IS NOT NULL DO
                        SELECT account_code INTO ancestor_code FROM chart_of_accounts WHERE id = ancestor_id;

                        IF ancestor_id = NEW.id THEN
                            SIGNAL SQLSTATE '45000'
                            SET MESSAGE_TEXT = 'Circular reference detected in account hierarchy';
                        END IF;

                        SELECT parent_id INTO ancestor_id FROM chart_of_accounts WHERE id = ancestor_id;
                    END WHILE;
                END IF;
            END */$$


DELIMITER ;

/* Trigger structure for table `chart_of_accounts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `update_chart_of_accounts_balance_timestamp` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `update_chart_of_accounts_balance_timestamp` BEFORE UPDATE ON `chart_of_accounts` FOR EACH ROW BEGIN
                IF NEW.opening_balance != OLD.opening_balance THEN
                    SET NEW.balance_updated_at = CURRENT_TIMESTAMP();
                END IF;
            END */$$


DELIMITER ;

/* Trigger structure for table `chart_of_accounts` */

DELIMITER $$

/*!50003 DROP TRIGGER*//*!50032 IF EXISTS */ /*!50003 `set_chart_of_accounts_updated_by` */$$

/*!50003 CREATE */ /*!50017 DEFINER = 'root'@'localhost' */ /*!50003 TRIGGER `set_chart_of_accounts_updated_by` BEFORE UPDATE ON `chart_of_accounts` FOR EACH ROW BEGIN
                SET NEW.updated_by = COALESCE(@current_user_id, 'system');
            END */$$


DELIMITER ;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
