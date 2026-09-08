-- Run on the AMS database after taking a backup. Safe to run more than once.
-- Adds schema only: no asset dates, statuses, users, or existing type settings are rewritten.
-- NULL maintenance means "not configured", not "maintenance disabled".
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS asset_type_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  asset_type_id INT UNSIGNED NOT NULL,
  item_type_id INT UNSIGNED NOT NULL,
  quantity INT UNSIGNED NOT NULL DEFAULT 1,
  KEY asset_type_items_type_idx (asset_type_id),
  KEY asset_type_items_item_idx (item_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS task_list (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(180) NOT NULL,
  active TINYINT NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS asset_type_tasks (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  asset_type_id INT UNSIGNED NOT NULL,
  task_list_id INT UNSIGNED NOT NULL,
  KEY asset_type_tasks_type_idx (asset_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP PROCEDURE IF EXISTS ams_maintenance_add_column;
DELIMITER $$
CREATE PROCEDURE ams_maintenance_add_column(IN p_table VARCHAR(64), IN p_column VARCHAR(64), IN p_definition TEXT)
BEGIN
  IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table)
     AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column)
  THEN
    SET @ams_maintenance_sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
    PREPARE ams_maintenance_stmt FROM @ams_maintenance_sql;
    EXECUTE ams_maintenance_stmt;
    DEALLOCATE PREPARE ams_maintenance_stmt;
  END IF;
END$$
DELIMITER ;
CALL ams_maintenance_add_column('asset_types', 'maintenance', 'TINYINT NULL DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'calibration', 'TINYINT NOT NULL DEFAULT 0');
CALL ams_maintenance_add_column('asset_types', 'manufacturer', 'VARCHAR(180) DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'vendor_part_number', 'INT UNSIGNED DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'maintenance_frequency_year', 'INT UNSIGNED DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'maintenance_reminder_days', 'INT UNSIGNED DEFAULT NULL');
-- Other fields already read/written by the Asset Type editor.
CALL ams_maintenance_add_column('asset_types', 'depreciation_method_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'useful_life_years', 'INT DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'salvage_value', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'depreciate_value', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_maintenance_add_column('asset_types', 'asset_picture', 'VARCHAR(255) DEFAULT NULL');
CALL ams_maintenance_add_column('item_types', 'manufacturer', 'INT UNSIGNED DEFAULT NULL');
CALL ams_maintenance_add_column('item_types', 'vendor_part_number', 'INT UNSIGNED DEFAULT NULL');
CALL ams_maintenance_add_column('item_types', 'calibration', 'TINYINT NOT NULL DEFAULT 0');
CALL ams_maintenance_add_column('item_types', 'maintenance', 'TINYINT NOT NULL DEFAULT 0');
CALL ams_maintenance_add_column('equipments_asset', 'maintenance_date', 'DATE DEFAULT NULL');
CALL ams_maintenance_add_column('equipments_asset', 'frequency_year', 'INT DEFAULT NULL');
CALL ams_maintenance_add_column('equipments_asset', 'maintenance_reminder_day', 'INT DEFAULT NULL');
DROP PROCEDURE IF EXISTS ams_maintenance_add_column;
