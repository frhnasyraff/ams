-- Run once after pulling; safe to repeat. Does NOT reseed assets or passwords.
-- mysql -u ams_user -p rams < rams_DB/patch_asset_statuses.sql
-- Original values are retained in ams_asset_status_backup_20260908.
SET NAMES utf8mb4;
CREATE TABLE IF NOT EXISTS ams_asset_status_backup_20260908 (
  equipment_id BIGINT NOT NULL PRIMARY KEY,
  original_status VARCHAR(255) NULL,
  backed_up_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
INSERT IGNORE INTO ams_asset_status_backup_20260908 (equipment_id, original_status)
SELECT equipment_id, equipment_status FROM equipments_asset;

-- Legacy dumps use an ENUM that cannot store the five current status values.
ALTER TABLE equipments_asset MODIFY COLUMN equipment_status VARCHAR(80) NULL DEFAULT 'UNSERVICEABLE';

START TRANSACTION;
UPDATE equipments_asset SET equipment_status = CASE UPPER(TRIM(COALESCE(equipment_status, '')))
  WHEN 'SERVICEABLE' THEN 'SERVICEABLE'
  WHEN 'IN USE' THEN 'SERVICEABLE'
  WHEN 'INUSE' THEN 'SERVICEABLE'
  WHEN 'AVAILABLE' THEN 'AVAILABLE'
  WHEN 'STANDBY' THEN 'AVAILABLE'
  WHEN 'MAINTENANCE' THEN 'MAINTENANCE'
  WHEN 'STORE' THEN 'STORE'
  WHEN 'IN STORE' THEN 'STORE'
  ELSE 'UNSERVICEABLE'
END;

-- Preserve existing master rows and IDs; the asset form exposes only these five.
INSERT INTO asset_status (name, colour, active)
SELECT 'SERVICEABLE', '#35d6a0', 1 WHERE NOT EXISTS (SELECT 1 FROM asset_status WHERE name = 'SERVICEABLE');
INSERT INTO asset_status (name, colour, active)
SELECT 'UNSERVICEABLE', '#f16f79', 1 WHERE NOT EXISTS (SELECT 1 FROM asset_status WHERE name = 'UNSERVICEABLE');
INSERT INTO asset_status (name, colour, active)
SELECT 'MAINTENANCE', '#f5b942', 1 WHERE NOT EXISTS (SELECT 1 FROM asset_status WHERE name = 'MAINTENANCE');
INSERT INTO asset_status (name, colour, active)
SELECT 'STORE', '#a47aff', 1 WHERE NOT EXISTS (SELECT 1 FROM asset_status WHERE name = 'STORE');
INSERT INTO asset_status (name, colour, active)
SELECT 'AVAILABLE', '#36caff', 1 WHERE NOT EXISTS (SELECT 1 FROM asset_status WHERE name = 'AVAILABLE');
COMMIT;
SELECT equipment_status, COUNT(*) AS assets FROM equipments_asset GROUP BY equipment_status;