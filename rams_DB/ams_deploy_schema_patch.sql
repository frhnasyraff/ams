-- AMS deployment schema patch
-- Run this after importing rams_DB/rams_import_safe.sql and before seed_minimal_deploy_data.php.
-- Safe to re-run: creates missing tables and adds missing columns only.

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS branch_office (
  branch_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  branch_code VARCHAR(30) NOT NULL,
  branch_name VARCHAR(255) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (branch_id),
  UNIQUE KEY branch_code_unique (branch_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS states (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  state_name VARCHAR(120) NOT NULL,
  colour VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY state_name_unique (state_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS locations (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(180) NOT NULL,
  state_id INT UNSIGNED DEFAULT NULL,
  address VARCHAR(255) DEFAULT NULL,
  lat VARCHAR(40) DEFAULT NULL,
  `long` VARCHAR(40) DEFAULT NULL,
  colour VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY location_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS logo_images (
  image_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  image_name VARCHAR(255) DEFAULT NULL,
  image_path VARCHAR(255) DEFAULT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (image_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asset_types (
  asset_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  short_code VARCHAR(20) DEFAULT NULL,
  colour VARCHAR(20) DEFAULT '#36caff',
  asset_picture VARCHAR(255) DEFAULT NULL,
  depreciation_method_id INT UNSIGNED DEFAULT NULL,
  useful_life_years INT DEFAULT NULL,
  salvage_value DECIMAL(12,2) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (asset_id),
  UNIQUE KEY asset_type_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asset_type_color (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  asset_type_id INT UNSIGNED DEFAULT NULL,
  color VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY asset_type_color_type_unique (asset_type_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asset_status (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  colour VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY asset_status_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS item_status (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) NOT NULL,
  colour VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY item_status_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS store_location (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(120) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY store_location_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS managed_by_add_data (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY managed_by_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fault_type_color_code (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fault_type VARCHAR(160) NOT NULL,
  color VARCHAR(20) DEFAULT '#f59e0b',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY fault_type_unique (fault_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS fault_lists (
  fault_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  fault_name VARCHAR(180) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (fault_id),
  UNIQUE KEY fault_name_unique (fault_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS dashboard_status_colors (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(80) DEFAULT NULL,
  color VARCHAR(20) DEFAULT NULL,
  status_name VARCHAR(80) DEFAULT NULL,
  status_color VARCHAR(20) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY dashboard_status_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS maintenance_type_color_code (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  maintenance_type VARCHAR(120) NOT NULL,
  color VARCHAR(20) DEFAULT '#36caff',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY maintenance_type_unique (maintenance_type)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS depreciation_methods (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  method_name VARCHAR(120) NOT NULL,
  description TEXT DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY depreciation_method_name_unique (method_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS disposal_methods (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  disposal_method VARCHAR(120) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY disposal_method_unique (disposal_method)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS write_off_reasons (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  write_off_reason VARCHAR(180) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY write_off_reason_unique (write_off_reason)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vendor_part_number (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  part_number VARCHAR(100) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY vendor_part_unique (part_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vendor_manufacturing_number (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  manufacturer_name VARCHAR(180) NOT NULL,
  manufacturer_number VARCHAR(120) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY vendor_manufacturer_name_unique (manufacturer_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vendor_manufacturing_drawing_number (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  drawing_number VARCHAR(160) NOT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY vendor_drawing_number_unique (drawing_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS item_types (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  name VARCHAR(160) NOT NULL,
  manufacturer INT UNSIGNED DEFAULT NULL,
  vendor_part_number INT UNSIGNED DEFAULT NULL,
  calibration INT(1) NOT NULL DEFAULT 0,
  maintenance INT(1) NOT NULL DEFAULT 1,
  item_picture VARCHAR(255) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY item_type_name_unique (name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS add_asset_items (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  asset_id INT UNSIGNED DEFAULT NULL,
  item_name VARCHAR(180) DEFAULT NULL,
  vendor_part_number VARCHAR(120) DEFAULT NULL,
  manufacturer_name VARCHAR(160) DEFAULT NULL,
  manufacturer_part_number VARCHAR(120) DEFAULT NULL,
  manufacturer_drawing_number VARCHAR(120) DEFAULT NULL,
  item_status VARCHAR(80) DEFAULT NULL,
  item_status_id INT UNSIGNED DEFAULT NULL,
  item_type_id INT UNSIGNED DEFAULT NULL,
  faulty_type_id INT UNSIGNED DEFAULT NULL,
  store_location_id INT UNSIGNED DEFAULT NULL,
  calibration_date DATE DEFAULT NULL,
  frequency_day INT DEFAULT NULL,
  reminder_day INT DEFAULT NULL,
  maintenance_date DATE DEFAULT NULL,
  items_qr_code VARCHAR(120) DEFAULT NULL,
  item_picture VARCHAR(255) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY item_qr_unique (items_qr_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS item_pictures (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  asset_id INT UNSIGNED DEFAULT NULL,
  item_id INT UNSIGNED DEFAULT NULL,
  item_picture VARCHAR(255) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS ticket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_number VARCHAR(80) NOT NULL,
  equipment_id INT UNSIGNED DEFAULT NULL,
  asset_number INT UNSIGNED DEFAULT NULL,
  issue_date DATE DEFAULT NULL,
  fault_type_id INT UNSIGNED DEFAULT NULL,
  description TEXT,
  status VARCHAR(80) DEFAULT 'Open',
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  UNIQUE KEY ticket_number_unique (ticket_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS item_ticket (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  ticket_number VARCHAR(80) DEFAULT NULL,
  ticket_id INT UNSIGNED DEFAULT NULL,
  item_id INT UNSIGNED DEFAULT NULL,
  equipment_id INT UNSIGNED DEFAULT NULL,
  issue_date DATE DEFAULT NULL,
  maintenance_type_id VARCHAR(40) DEFAULT NULL,
  final_status VARCHAR(80) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (id),
  KEY item_ticket_ticket_number_idx (ticket_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS maintenance_task_done (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  equipment_maintenance_id INT UNSIGNED DEFAULT NULL,
  maintenance_ticket_id INT UNSIGNED DEFAULT NULL,
  task_name VARCHAR(180) DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  KEY maintenance_task_done_maintenance_idx (equipment_maintenance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS next_maintenance_date (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  equipment_id INT UNSIGNED NOT NULL,
  maintenance_date DATE DEFAULT NULL,
  frequency_year INT DEFAULT 1,
  maintenance_reminder_day INT DEFAULT 21,
  PRIMARY KEY (id),
  UNIQUE KEY next_maintenance_equipment_unique (equipment_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asset_disposal_requests (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  request_number VARCHAR(80) DEFAULT NULL,
  asset_id INT UNSIGNED DEFAULT NULL,
  equipment_id INT UNSIGNED DEFAULT NULL,
  write_off_reason_id INT UNSIGNED DEFAULT NULL,
  disposal_method_id INT UNSIGNED DEFAULT NULL,
  requested_by INT UNSIGNED DEFAULT NULL,
  approved_by INT UNSIGNED DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'Pending',
  request_status VARCHAR(50) DEFAULT NULL,
  remarks TEXT DEFAULT NULL,
  attachment VARCHAR(255) DEFAULT NULL,
  request_date DATE DEFAULT NULL,
  disposal_date DATE DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY disposal_request_number_unique (request_number)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS orders (
  order_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  order_num VARCHAR(80) DEFAULT NULL,
  company_id INT UNSIGNED DEFAULT NULL,
  company_address_id INT UNSIGNED DEFAULT NULL,
  service_type_id INT UNSIGNED DEFAULT NULL,
  asset_id INT UNSIGNED DEFAULT NULL,
  status INT DEFAULT 1,
  order_type INT DEFAULT NULL,
  second_order_type INT DEFAULT NULL,
  start_date DATE DEFAULT NULL,
  progress_at DATETIME DEFAULT NULL,
  completed_at DATETIME DEFAULT NULL,
  remarks_updated_at DATETIME DEFAULT NULL,
  active INT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (order_id),
  UNIQUE KEY orders_order_num_unique (order_num)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS asset_logs (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  table_name VARCHAR(120) DEFAULT NULL,
  record_id INT UNSIGNED DEFAULT NULL,
  user_id INT UNSIGNED DEFAULT NULL,
  action VARCHAR(120) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  data TEXT DEFAULT NULL,
  log_item_id INT UNSIGNED DEFAULT NULL,
  log_item_table VARCHAR(120) DEFAULT NULL,
  log_code VARCHAR(120) DEFAULT NULL,
  log_description TEXT DEFAULT NULL,
  log_user_id INT UNSIGNED DEFAULT NULL,
  timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY asset_logs_record_idx (table_name, record_id),
  KEY asset_logs_legacy_idx (log_item_id, log_code, timestamp)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE IF NOT EXISTS task (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  task_id INT UNSIGNED DEFAULT NULL,
  task_name VARCHAR(180) DEFAULT NULL,
  name VARCHAR(180) DEFAULT NULL,
  description TEXT DEFAULT NULL,
  remarks TEXT DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'ACTIVE',
  active INT(1) NOT NULL DEFAULT 1,
  created_at DATETIME DEFAULT NULL,
  updated_at DATETIME DEFAULT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

DROP PROCEDURE IF EXISTS ams_add_column_if_missing;
DELIMITER $$
CREATE PROCEDURE ams_add_column_if_missing(IN p_table VARCHAR(64), IN p_column VARCHAR(64), IN p_definition TEXT)
BEGIN
  IF EXISTS (SELECT 1 FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table)
     AND NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = p_table AND COLUMN_NAME = p_column)
  THEN
    SET @sql = CONCAT('ALTER TABLE `', p_table, '` ADD COLUMN `', p_column, '` ', p_definition);
    PREPARE stmt FROM @sql;
    EXECUTE stmt;
    DEALLOCATE PREPARE stmt;
  END IF;
END$$
DELIMITER ;

CALL ams_add_column_if_missing('users', 'isSuper', 'INT(1) NOT NULL DEFAULT 0');
CALL ams_add_column_if_missing('company_addresses', 'branch_office_id', 'INT UNSIGNED DEFAULT NULL');

CALL ams_add_column_if_missing('asset_types', 'short_code', 'VARCHAR(20) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'colour', 'VARCHAR(20) DEFAULT ''#36caff''');
CALL ams_add_column_if_missing('asset_types', 'asset_picture', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'depreciation_method_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'useful_life_years', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'salvage_value', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'active', 'INT(1) NOT NULL DEFAULT 1');

CALL ams_add_column_if_missing('dashboard_status_colors', 'name', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('dashboard_status_colors', 'color', 'VARCHAR(20) DEFAULT NULL');
CALL ams_add_column_if_missing('dashboard_status_colors', 'status_name', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('dashboard_status_colors', 'status_color', 'VARCHAR(20) DEFAULT NULL');
CALL ams_add_column_if_missing('dashboard_status_colors', 'active', 'INT(1) NOT NULL DEFAULT 1');

CALL ams_add_column_if_missing('item_types', 'manufacturer', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('item_types', 'vendor_part_number', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('item_types', 'calibration', 'INT(1) NOT NULL DEFAULT 0');
CALL ams_add_column_if_missing('item_types', 'maintenance', 'INT(1) NOT NULL DEFAULT 1');
CALL ams_add_column_if_missing('item_types', 'item_picture', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('item_types', 'active', 'INT(1) NOT NULL DEFAULT 1');

CALL ams_add_column_if_missing('add_asset_items', 'asset_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'item_name', 'VARCHAR(180) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'vendor_part_number', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'manufacturer_name', 'VARCHAR(160) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'manufacturer_part_number', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'manufacturer_drawing_number', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'item_status', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'item_status_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'item_type_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'faulty_type_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'store_location_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'calibration_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'frequency_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'reminder_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'maintenance_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'frequency_year', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'maintenance_reminder_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'items_qr_code', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'item_picture', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'active', 'INT(1) NOT NULL DEFAULT 1');

CALL ams_add_column_if_missing('equipments_asset', 'serial_number', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'rfid', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'calibration_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'frequency_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'reminder_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'maintenance_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'date_installed', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'ownership', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'location_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'faulty_type_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'state_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'vendor_part_number_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'store_location_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'useful_life_years', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'salvage_value', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'frequency_year', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'maintenance_reminder_day', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'price_of_purchase', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'purchase_origin', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'company_name', 'VARCHAR(180) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'person_in_contact', 'VARCHAR(180) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'contact_number', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'purchased_by', 'VARCHAR(180) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'purchase_price', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'branch_office_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'invoice', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'invoice_file', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'disposal_method_id', 'INT UNSIGNED DEFAULT NULL');

CALL ams_add_column_if_missing('equipments', 'branch_office_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'location_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'state_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'store_location_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'ownership', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'useful_life_years', 'INT DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'salvage_value', 'DECIMAL(12,2) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments', 'purchase_price', 'DECIMAL(12,2) DEFAULT NULL');

CALL ams_add_column_if_missing('equipment_maintenance_asset', 'ticket_number', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'maintenance_type_id', 'VARCHAR(40) DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'faulty_type', 'VARCHAR(160) DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'final_status', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'created_at', 'DATETIME DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'update_date', 'DATETIME DEFAULT NULL');
CALL ams_add_column_if_missing('equipment_maintenance_asset', 'updated_at', 'DATETIME DEFAULT NULL');

CALL ams_add_column_if_missing('asset_disposal_requests', 'request_number', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'asset_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'equipment_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'write_off_reason_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'disposal_method_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'requested_by', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'approved_by', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'status', 'VARCHAR(50) DEFAULT ''Pending''');
CALL ams_add_column_if_missing('asset_disposal_requests', 'request_status', 'VARCHAR(50) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'remarks', 'TEXT DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'attachment', 'VARCHAR(255) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'request_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'disposal_date', 'DATE DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'active', 'INT(1) NOT NULL DEFAULT 1');
CALL ams_add_column_if_missing('asset_disposal_requests', 'created_at', 'DATETIME DEFAULT CURRENT_TIMESTAMP');
CALL ams_add_column_if_missing('asset_disposal_requests', 'updated_at', 'DATETIME DEFAULT NULL');

CALL ams_add_column_if_missing('asset_logs', 'log_item_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'log_item_table', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'log_code', 'VARCHAR(120) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'log_description', 'TEXT DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'log_user_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'log_ip', 'VARCHAR(45) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_logs', 'timestamp', 'DATETIME DEFAULT CURRENT_TIMESTAMP');

CALL ams_add_column_if_missing('store_location', 'color', 'VARCHAR(20) DEFAULT ''#36caff''');
CALL ams_add_column_if_missing('depreciation_methods', 'depreciation_method', 'VARCHAR(100) DEFAULT NULL');
UPDATE depreciation_methods SET depreciation_method = method_name WHERE depreciation_method IS NULL OR depreciation_method = '';
CALL ams_add_column_if_missing('add_asset_items', 'add_asset_items_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('item_pictures', 'add_asset_items_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'manufacturer_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('add_asset_items', 'part_number_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'depreciation_method', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('equipments_asset', 'depreciation_method_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_types', 'depreciate_value', 'DECIMAL(12,2) DEFAULT 0');
CALL ams_add_column_if_missing('asset_types', 'depreciation_method', 'VARCHAR(80) DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'equipment_asset_id', 'INT UNSIGNED DEFAULT NULL');
CALL ams_add_column_if_missing('asset_disposal_requests', 'status', 'VARCHAR(50) DEFAULT ''pending''');
CALL ams_add_column_if_missing('equipments_asset', 'status', 'VARCHAR(50) DEFAULT ''active''');
CALL ams_add_column_if_missing('add_asset_items', 'status', 'VARCHAR(50) DEFAULT ''active''');
CALL ams_add_column_if_missing('task', 'status', 'VARCHAR(50) DEFAULT ''ACTIVE''');
DROP PROCEDURE IF EXISTS ams_add_column_if_missing;

SET FOREIGN_KEY_CHECKS = 1;



-- Component maintenance history and task details used by Items AJAX.
CREATE TABLE IF NOT EXISTS logs_item_maintenance (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 item_ticket_id INT UNSIGNED NOT NULL,
 update_date DATETIME NULL,
 final_status VARCHAR(100) NULL,
 created_at DATETIME NULL,
 updated_at DATETIME NULL,
 KEY item_ticket_idx (item_ticket_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
CREATE TABLE IF NOT EXISTS logs_item_maintenance_task_done (
 id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 item_maintenance_id INT UNSIGNED NOT NULL,
 task_done TEXT NULL,
 remarks TEXT NULL,
 created_at DATETIME NULL,
 updated_at DATETIME NULL,
 KEY item_maintenance_idx (item_maintenance_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;