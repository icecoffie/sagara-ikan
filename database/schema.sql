-- ============================================================
--  Sagara Resto — Database Schema
--  Engine: MySQL / MariaDB (InnoDB, utf8mb4)
--
--  cPanel/phpMyAdmin: create the database first (via cPanel MySQL
--  Databases), SELECT it, then import this file. The CREATE DATABASE
--  lines below are commented out on purpose so the import works into
--  a database you already selected.
-- ============================================================

-- CREATE DATABASE IF NOT EXISTS `sagara_resto` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE `sagara_resto`;

SET FOREIGN_KEY_CHECKS = 0;

DROP TABLE IF EXISTS `activity_logs`;
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reservations`;
DROP TABLE IF EXISTS `dining_tables`;
DROP TABLE IF EXISTS `menu_items`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `role_permissions`;
DROP TABLE IF EXISTS `permissions`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `roles`;

SET FOREIGN_KEY_CHECKS = 1;

-- ------------------------------------------------------------
--  RBAC: roles, permissions, role_permissions, users
-- ------------------------------------------------------------

CREATE TABLE `roles` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(50) NOT NULL,
    `slug`        VARCHAR(50) NOT NULL,
    `description` VARCHAR(255) DEFAULT NULL,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_roles_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `permissions` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `slug`       VARCHAR(100) NOT NULL,   -- e.g. menu.create
    `resource`   VARCHAR(50)  NOT NULL,   -- e.g. menu
    `action`     VARCHAR(50)  NOT NULL,   -- e.g. create
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_permissions_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `role_permissions` (
    `role_id`       INT UNSIGNED NOT NULL,
    `permission_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`role_id`, `permission_id`),
    KEY `idx_rp_permission` (`permission_id`),
    CONSTRAINT `fk_rp_role` FOREIGN KEY (`role_id`)
        REFERENCES `roles` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_rp_permission` FOREIGN KEY (`permission_id`)
        REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `users` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`       VARCHAR(100) NOT NULL,
    `email`      VARCHAR(150) NOT NULL,
    `password`   VARCHAR(255) NOT NULL,
    `role_id`    INT UNSIGNED NOT NULL,
    `is_active`  TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_users_email` (`email`),
    KEY `idx_users_role` (`role_id`),
    CONSTRAINT `fk_users_role` FOREIGN KEY (`role_id`)
        REFERENCES `roles` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Catalog: categories, menu_items
-- ------------------------------------------------------------

CREATE TABLE `categories` (
    `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`        VARCHAR(100) NOT NULL,
    `slug`        VARCHAR(120) NOT NULL,
    `description` TEXT DEFAULT NULL,
    `is_active`   TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`  TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `menu_items` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `name`         VARCHAR(150) NOT NULL,
    `category_id`  INT UNSIGNED NOT NULL,
    `price`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `description`  TEXT DEFAULT NULL,
    `image`        VARCHAR(255) DEFAULT NULL,
    `is_available` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_menu_category` (`category_id`),
    CONSTRAINT `fk_menu_category` FOREIGN KEY (`category_id`)
        REFERENCES `categories` (`id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Floor: dining_tables, reservations
-- ------------------------------------------------------------

CREATE TABLE `dining_tables` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `table_number` VARCHAR(20) NOT NULL,
    `capacity`     INT UNSIGNED NOT NULL DEFAULT 2,
    `location`     ENUM('indoor','outdoor') NOT NULL DEFAULT 'indoor',
    `status`       ENUM('available','occupied','reserved') NOT NULL DEFAULT 'available',
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_table_number` (`table_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `reservations` (
    `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `customer_name`    VARCHAR(120) NOT NULL,
    `phone`            VARCHAR(30) DEFAULT NULL,
    `table_id`         INT UNSIGNED DEFAULT NULL,
    `reservation_time` DATETIME NOT NULL,
    `party_size`       INT UNSIGNED NOT NULL DEFAULT 1,
    `status`           ENUM('pending','confirmed','cancelled') NOT NULL DEFAULT 'pending',
    `notes`            TEXT DEFAULT NULL,
    `created_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`       TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_res_table` (`table_id`),
    CONSTRAINT `fk_res_table` FOREIGN KEY (`table_id`)
        REFERENCES `dining_tables` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Transactions: orders, order_items
-- ------------------------------------------------------------

CREATE TABLE `orders` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_number` VARCHAR(30) NOT NULL,
    `table_id`     INT UNSIGNED DEFAULT NULL,
    `user_id`      INT UNSIGNED DEFAULT NULL,  -- creator (cashier/waiter), NULL for customer orders
    `source`       ENUM('staff','customer') NOT NULL DEFAULT 'staff',
    `order_type`   ENUM('dine_in','takeaway') NOT NULL DEFAULT 'dine_in',
    `customer_name`  VARCHAR(120) DEFAULT NULL,
    `customer_phone` VARCHAR(30) DEFAULT NULL,
    `status`       ENUM('open','paid','cancelled') NOT NULL DEFAULT 'open',
    `total`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `paid_at`      DATETIME DEFAULT NULL,
    `created_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at`   TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_order_number` (`order_number`),
    KEY `idx_orders_table` (`table_id`),
    KEY `idx_orders_user` (`user_id`),
    KEY `idx_orders_status` (`status`),
    CONSTRAINT `fk_orders_table` FOREIGN KEY (`table_id`)
        REFERENCES `dining_tables` (`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `order_items` (
    `id`           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `order_id`     INT UNSIGNED NOT NULL,
    `menu_item_id` INT UNSIGNED DEFAULT NULL,
    `item_name`    VARCHAR(150) NOT NULL,     -- snapshot of the menu name
    `qty`          INT UNSIGNED NOT NULL DEFAULT 1,
    `price`        DECIMAL(12,2) NOT NULL DEFAULT 0,
    `subtotal`     DECIMAL(12,2) NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`),
    KEY `idx_oi_order` (`order_id`),
    KEY `idx_oi_menu` (`menu_item_id`),
    CONSTRAINT `fk_oi_order` FOREIGN KEY (`order_id`)
        REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_oi_menu` FOREIGN KEY (`menu_item_id`)
        REFERENCES `menu_items` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
--  Audit: activity_logs
-- ------------------------------------------------------------

CREATE TABLE `activity_logs` (
    `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id`    INT UNSIGNED DEFAULT NULL,
    `action`     VARCHAR(100) NOT NULL,
    `detail`     VARCHAR(255) DEFAULT NULL,
    `ip_address` VARCHAR(45) DEFAULT NULL,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_log_user` (`user_id`),
    CONSTRAINT `fk_log_user` FOREIGN KEY (`user_id`)
        REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
