/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

DROP TABLE IF EXISTS `bank_accounts`;
CREATE TABLE `bank_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `bank_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `branch` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `auto_confirm` tinyint(1) NOT NULL DEFAULT '0',
  `prefix` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'naptien',
  `access_token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `bank_deposits`;
CREATE TABLE `bank_deposits` (
  `transaction_id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `account_number` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `content` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bank` enum('VPBank','TPBank','VietinBank','ACB','BIDV','MBBank','OCB','KienLongBank','MSB') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`transaction_id`),
  KEY `bank_deposits_user_id_foreign` (`user_id`),
  CONSTRAINT `bank_deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `card_deposits`;
CREATE TABLE `card_deposits` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `telco` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` int NOT NULL,
  `received_amount` int NOT NULL,
  `serial` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `pin` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `request_id` bigint NOT NULL,
  `status` enum('success','error','processing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `response` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `card_deposits_user_id_foreign` (`user_id`),
  CONSTRAINT `card_deposits_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `configs`;
CREATE TABLE `configs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `configs_key_unique` (`key`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `discount_code_usages`;
CREATE TABLE `discount_code_usages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `discount_code_id` bigint unsigned NOT NULL,
  `user_id` bigint unsigned NOT NULL,
  `context` enum('account','random_account','service') COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_id` bigint unsigned NOT NULL,
  `original_price` decimal(10,2) NOT NULL,
  `discounted_price` decimal(10,2) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `used_at` timestamp NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `discount_code_usages_discount_code_id_foreign` (`discount_code_id`),
  KEY `discount_code_usages_user_id_foreign` (`user_id`),
  CONSTRAINT `discount_code_usages_discount_code_id_foreign` FOREIGN KEY (`discount_code_id`) REFERENCES `discount_codes` (`id`) ON DELETE CASCADE,
  CONSTRAINT `discount_code_usages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `discount_codes`;
CREATE TABLE `discount_codes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `code` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `discount_type` enum('percentage','fixed_amount') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL DEFAULT '0.00',
  `max_discount_value` decimal(10,2) DEFAULT NULL,
  `min_purchase_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `is_active` enum('1','0') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '1',
  `usage_limit` int DEFAULT NULL,
  `usage_count` int NOT NULL DEFAULT '0',
  `per_user_limit` int DEFAULT NULL,
  `applicable_to` enum('account','random_account','service') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `item_ids` json DEFAULT NULL,
  `expire_date` timestamp NULL DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `discount_codes_code_unique` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `game_accounts`;
CREATE TABLE `game_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `game_category_id` bigint unsigned NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` bigint unsigned NOT NULL,
  `status` enum('available','sold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `buyer_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `thumb` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `images` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `registration_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`),
  KEY `game_accounts_game_category_id_foreign` (`game_category_id`),
  KEY `game_accounts_buyer_id_foreign` (`buyer_id`),
  CONSTRAINT `game_accounts_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `game_accounts_game_category_id_foreign` FOREIGN KEY (`game_category_id`) REFERENCES `game_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `game_categories`;
CREATE TABLE `game_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `game_services`;
CREATE TABLE `game_services` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `type` enum('gold','gem','leveling') COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `game_services_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `lucky_wheel_histories`;
CREATE TABLE `lucky_wheel_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `lucky_wheel_id` bigint unsigned NOT NULL,
  `spin_count` int NOT NULL,
  `total_cost` bigint NOT NULL,
  `reward_type` enum('gold','gem') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reward_amount` int NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `lucky_wheel_histories_user_id_foreign` (`user_id`),
  KEY `lucky_wheel_histories_lucky_wheel_id_foreign` (`lucky_wheel_id`),
  CONSTRAINT `lucky_wheel_histories_lucky_wheel_id_foreign` FOREIGN KEY (`lucky_wheel_id`) REFERENCES `lucky_wheels` (`id`) ON DELETE CASCADE,
  CONSTRAINT `lucky_wheel_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `lucky_wheels`;
CREATE TABLE `lucky_wheels` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `wheel_image` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `rules` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `price_per_spin` bigint NOT NULL,
  `config` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `lucky_wheels_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `money_transactions`;
CREATE TABLE `money_transactions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `type` enum('deposit','withdraw','purchase','refund') COLLATE utf8mb4_unicode_ci NOT NULL,
  `amount` bigint NOT NULL,
  `balance_before` bigint NOT NULL,
  `balance_after` bigint NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `reference_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `money_transactions_user_id_foreign` (`user_id`),
  CONSTRAINT `money_transactions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `money_withdrawal_histories`;
CREATE TABLE `money_withdrawal_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `amount` bigint NOT NULL,
  `user_note` text COLLATE utf8mb4_unicode_ci,
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `status` enum('processing','success','error') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `money_withdrawal_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `money_withdrawal_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `class_favicon` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `password_reset_tokens`;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `purchase_history`;
CREATE TABLE `purchase_history` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `game_account_id` bigint unsigned NOT NULL,
  `amount` bigint NOT NULL,
  `account_details` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `purchase_history_user_id_foreign` (`user_id`),
  KEY `purchase_history_game_account_id_foreign` (`game_account_id`),
  CONSTRAINT `purchase_history_game_account_id_foreign` FOREIGN KEY (`game_account_id`) REFERENCES `game_accounts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `purchase_history_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `random_categories`;
CREATE TABLE `random_categories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `slug` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `random_categories_slug_unique` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `random_category_accounts`;
CREATE TABLE `random_category_accounts` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `random_category_id` bigint unsigned NOT NULL,
  `account_name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `price` bigint NOT NULL,
  `status` enum('available','sold') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'available',
  `server` int NOT NULL,
  `buyer_id` bigint unsigned DEFAULT NULL,
  `note` text COLLATE utf8mb4_unicode_ci,
  `note_buyer` text COLLATE utf8mb4_unicode_ci,
  `thumbnail` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `random_category_accounts_random_category_id_foreign` (`random_category_id`),
  KEY `random_category_accounts_buyer_id_foreign` (`buyer_id`),
  CONSTRAINT `random_category_accounts_buyer_id_foreign` FOREIGN KEY (`buyer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `random_category_accounts_random_category_id_foreign` FOREIGN KEY (`random_category_id`) REFERENCES `random_categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_histories`;
CREATE TABLE `service_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `game_service_id` bigint unsigned NOT NULL,
  `service_package_id` bigint unsigned DEFAULT NULL,
  `game_account` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `game_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server` int NOT NULL,
  `amount` bigint NOT NULL DEFAULT '0',
  `price` bigint NOT NULL,
  `discount_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `discount_amount` decimal(10,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','processing','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `admin_note` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_histories_user_id_foreign` (`user_id`),
  KEY `service_histories_game_service_id_foreign` (`game_service_id`),
  KEY `service_histories_service_package_id_foreign` (`service_package_id`),
  CONSTRAINT `service_histories_game_service_id_foreign` FOREIGN KEY (`game_service_id`) REFERENCES `game_services` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_histories_service_package_id_foreign` FOREIGN KEY (`service_package_id`) REFERENCES `service_packages` (`id`) ON DELETE CASCADE,
  CONSTRAINT `service_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `service_packages`;
CREATE TABLE `service_packages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `game_service_id` bigint unsigned NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `price` bigint NOT NULL,
  `estimated_time` int DEFAULT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `service_packages_game_service_id_foreign` (`game_service_id`),
  CONSTRAINT `service_packages_game_service_id_foreign` FOREIGN KEY (`game_service_id`) REFERENCES `game_services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `google_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `facebook_id` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `role` enum('member','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'member',
  `balance` bigint NOT NULL DEFAULT '0',
  `total_deposited` bigint NOT NULL DEFAULT '0',
  `gold` bigint NOT NULL DEFAULT '0',
  `gem` bigint NOT NULL DEFAULT '0',
  `banned` tinyint(1) NOT NULL DEFAULT '0',
  `ip_address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_username_unique` (`username`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `withdrawal_histories`;
CREATE TABLE `withdrawal_histories` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint unsigned NOT NULL,
  `amount` int NOT NULL,
  `type` enum('gold','gem') COLLATE utf8mb4_unicode_ci NOT NULL,
  `character_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `server` int NOT NULL,
  `user_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `admin_note` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `status` enum('success','error','processing') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'processing',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `withdrawal_histories_user_id_foreign` (`user_id`),
  CONSTRAINT `withdrawal_histories_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `bank_accounts` (`id`, `bank_name`, `account_name`, `account_number`, `branch`, `note`, `is_active`, `auto_confirm`, `prefix`, `access_token`, `created_at`, `updated_at`) VALUES
(1, 'MBBank', 'NGUYEN TRONG KHAI', '0989060084', NULL, NULL, 1, 1, 'firefoxgame', 'MQJZ788ST3EZACPDQSFPGKSEU9HLXDUN4YIFT924PRJWDVHAFNT5JOYRMH26KGE6', '2025-04-16 05:07:57', '2025-04-24 04:16:46');






INSERT INTO `configs` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(1, 'site_name', 'Shop Game Liên Quân Mobile', '2025-04-17 07:54:25', '2025-04-20 02:53:18');
INSERT INTO `configs` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(2, 'site_keywords', 'Mua bán tài khoản game Liên Quân Mobile', '2025-04-17 07:54:25', '2025-04-20 02:53:18');
INSERT INTO `configs` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(3, 'site_description', 'Mua bán tài khoản game Liên Quân Mobile', '2025-04-17 07:54:25', '2025-04-20 02:53:18');
INSERT INTO `configs` (`id`, `key`, `value`, `created_at`, `updated_at`) VALUES
(4, 'address', 'HCM, VN', '2025-04-17 07:54:25', '2025-04-17 07:54:25'),
(5, 'phone', '0989060084', '2025-04-17 07:54:25', '2025-04-20 02:53:18'),
(6, 'email', 'tkhai12386@gmail.com', '2025-04-17 07:54:25', '2025-04-17 07:54:25'),
(7, 'site_logo', '/storage/config/1744851469_104477a915d447cba73aea14bec49d57.png', '2025-04-17 07:54:44', '2025-04-17 07:57:49'),
(8, 'site_logo_footer', '/storage/config/1744851469_104477a915d447cba73aea14bec49d57.png', '2025-04-17 07:54:44', '2025-04-17 07:57:49'),
(9, 'site_banner', '/storage/config/1744851289_88b757133689ac02b9bafb12e73a7497.jpg', '2025-04-17 07:54:44', '2025-04-17 07:54:49'),
(10, 'site_favicon', '/storage/config/1744851469_104477a915d447cba73aea14bec49d57.png', '2025-04-17 07:54:44', '2025-04-17 07:57:49'),
(11, 'site_share_image', '/storage/config/1744851284_88b757133689ac02b9bafb12e73a7497.jpg', '2025-04-17 07:54:44', '2025-04-17 07:54:44');





INSERT INTO `game_accounts` (`id`, `game_category_id`, `username`, `password`, `price`, `status`, `buyer_id`, `note`, `thumb`, `images`, `created_at`, `updated_at`, `registration_type`) VALUES
(2, 1, 'mwnux1370', 'P9135b7m3E@', 30000, 'available', NULL, 'Richer Susano, Slimz thỏ 7 màu', '/storage/accounts/thumbnails/1745441704_a66ff8ef1f13a421f9028b67b7e48dc4.jpg', '[\"\\/storage\\/accounts\\/images\\/1745441704_a66ff8ef1f13a421f9028b67b7e48dc4.jpg\",\"\\/storage\\/accounts\\/images\\/1745441704_42638baa5d476a4eba6e73f3211c92ae.jpg\"]', '2025-04-24 03:55:04', '2025-04-24 03:55:04', 'TTT');


INSERT INTO `game_categories` (`id`, `name`, `slug`, `thumbnail`, `description`, `active`, `created_at`, `updated_at`) VALUES
(1, 'ACC REG', 'acc-reg', '/storage/categories/1744754577_2b12d50e9a0aaf93d42c00fc23a3f5de.jpg', 'KHO ACC REG GIÁ RẺ', 1, '2025-04-16 05:02:57', '2025-04-16 05:02:57');








INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(25, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(26, '2025_03_28_181908_create_users_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(27, '2025_03_28_181914_create_game_categories_table', 1);
INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(28, '2025_03_28_181917_create_game_accounts_table', 1),
(29, '2025_03_28_181929_create_purchase_history_table', 1),
(30, '2025_03_28_181932_create_money_transactions_table', 1),
(31, '2025_03_29_015110_create_card_deposits_table', 1),
(32, '2025_03_29_154334_create_game_services_table', 1),
(33, '2025_03_29_154343_create_service_packages_table', 1),
(34, '2025_03_29_154350_create_service_histories_table', 1),
(35, '2025_03_30_231218_create_configs_table', 1),
(36, '2025_03_31_050014_create_bank_deposits_table', 1),
(37, '2025_03_31_065843_create_bank_accounts_table', 1),
(38, '2025_04_01_031303_create_random_categories_table', 1),
(39, '2025_04_01_031307_create_random_category_accounts_table', 1),
(40, '2025_04_01_035918_create_discount_codes_table', 1),
(41, '2025_04_01_040223_create_discount_code_usages_table', 1),
(42, '2025_04_02_060346_create_lucky_wheels_table', 1),
(43, '2025_04_02_060438_create_lucky_wheel_histories_table', 1),
(44, '2025_04_02_060504_create_withdrawal_histories_table', 1),
(45, '2025_04_04_043941_create_money_withdrawal_histories_table', 1),
(46, '2025_04_05_101214_create_notifications_table', 1),
(47, '2025_04_07_022109_create_password_reset_tokens_table', 1),
(51, '2025_04_16_042148_update_game_accounts_table', 2);

INSERT INTO `money_transactions` (`id`, `user_id`, `type`, `amount`, `balance_before`, `balance_after`, `description`, `reference_id`, `created_at`, `updated_at`) VALUES
(1, 1, 'purchase', 0, 0, 0, 'Mua tài khoản #1', '1', '2025-04-16 05:06:08', '2025-04-16 05:06:08');




















INSERT INTO `users` (`id`, `username`, `password`, `email`, `google_id`, `facebook_id`, `role`, `balance`, `total_deposited`, `gold`, `gem`, `banned`, `ip_address`, `remember_token`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$mvpnS9OYbVH8AZxshLfYeu9t7B5oHN0BrF2rk6HulHsPwfRHYgECq', 'admin@admin.vn', NULL, NULL, 'admin', 0, 0, 0, 0, 0, '127.0.0.1', NULL, NULL, '2025-04-16 05:02:02', '2025-04-16 05:02:02');
INSERT INTO `users` (`id`, `username`, `password`, `email`, `google_id`, `facebook_id`, `role`, `balance`, `total_deposited`, `gold`, `gem`, `banned`, `ip_address`, `remember_token`, `email_verified_at`, `created_at`, `updated_at`) VALUES
(2, 'tkhai12386@gmail.com', '$2y$12$1ewIN397Q0rk6cJOLl0tO.e4Nvx7ljmahN8t5BNEYdz6lG5obX/2W', 'tkhai12386@gmail.com', NULL, NULL, 'member', 0, 0, 0, 0, 0, '127.0.0.1', NULL, NULL, '2025-04-17 06:56:59', '2025-04-17 06:56:59');





/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;