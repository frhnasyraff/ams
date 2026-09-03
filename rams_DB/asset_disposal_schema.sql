-- Asset disposal workflow tables required by the disposal and depreciation pages.
-- Safe to run more than once.

CREATE TABLE IF NOT EXISTS `write_off_reasons` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `write_off_reason` varchar(255) NOT NULL,
  `active` tinyint NOT NULL DEFAULT 1,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_write_off_reason` (`write_off_reason`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT IGNORE INTO `write_off_reasons` (`write_off_reason`, `active`) VALUES
  ('Damaged Beyond Repair', 1),
  ('Obsolete', 1),
  ('Lost or Missing', 1),
  ('Uneconomical to Repair', 1),
  ('End of Useful Life', 1);

CREATE TABLE IF NOT EXISTS `asset_disposal_requests` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_number` varchar(30) NOT NULL,
  `equipment_asset_id` int unsigned NOT NULL,
  `write_off_reason_id` int unsigned DEFAULT NULL,
  `disposal_method_id` int unsigned DEFAULT NULL,
  `estimated_value` decimal(12,2) NOT NULL DEFAULT 0.00,
  `justification` text,
  `attachment` varchar(255) DEFAULT NULL,
  `status` varchar(30) NOT NULL DEFAULT 'new',
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_disposal_request_number` (`request_number`),
  KEY `idx_disposal_asset` (`equipment_asset_id`),
  KEY `idx_disposal_status` (`status`),
  KEY `idx_disposal_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `asset_disposal_status` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `request_id` int unsigned NOT NULL,
  `disposal_method_id` int unsigned DEFAULT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `status` varchar(30) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_disposal_status_request` (`request_id`),
  KEY `idx_disposal_status_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
