-- ============================================================
-- ShortNews License Server — Database Setup
-- Supports multiple CodeCanyon products.
-- Create a SEPARATE database: shortnews_licenses
-- Import this on YOUR server (not the buyer's server)
-- ============================================================

CREATE TABLE IF NOT EXISTS `activations` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL COMMENT 'Product identifier, e.g. shortnews, jobapp',
  `purchase_code` varchar(40) NOT NULL,
  `buyer_username` varchar(100) DEFAULT NULL,
  `domain` varchar(255) NOT NULL,
  `activated_at` timestamp DEFAULT current_timestamp(),
  `last_checked_at` timestamp DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_activation` (`purchase_code`, `product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `activation_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` varchar(50) NOT NULL,
  `purchase_code` varchar(40) NOT NULL,
  `domain` varchar(255) NOT NULL,
  `action` varchar(30) NOT NULL DEFAULT 'activate',
  `ip_address` varchar(64) DEFAULT NULL,
  `created_at` timestamp DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_code` (`purchase_code`),
  KEY `idx_product` (`product_id`),
  KEY `idx_domain` (`domain`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
