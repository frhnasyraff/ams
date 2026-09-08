-- Explicitly approved: Add Maintenance access for the active Admin role only.
-- Does not grant edit/delete/all permissions, modify memberships, or change asset data.
-- Existing grants (including grants for other roles) remain unchanged. Safe to repeat.
DROP PROCEDURE IF EXISTS ams_asset_maintenance_admin_access;
DELIMITER $$
CREATE PROCEDURE ams_asset_maintenance_admin_access()
BEGIN
    DECLARE admin_count INT DEFAULT 0;
    DECLARE admin_id INT;
    DECLARE permission_count INT DEFAULT 0;
    DECLARE permission_id INT;
    DECLARE category_count INT DEFAULT 0;
    DECLARE category_id INT;
    DECLARE transactional_tables INT DEFAULT 0;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;

    SELECT COUNT(*), MIN(role_id) INTO admin_count, admin_id FROM roles
      WHERE LOWER(TRIM(role_name)) IN ('admin', 'administrator') AND active = 1;
    IF admin_count <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Expected one active Admin/Administrator role. No maintenance access granted.';
    END IF;

    -- Refuse a partial repair when the permission tables cannot roll back together.
    SELECT COUNT(*) INTO transactional_tables FROM information_schema.TABLES
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME IN ('permissions', 'permission_categories', 'role_permissions')
        AND ENGINE = 'InnoDB';
    IF transactional_tables <> 3 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Permission tables must exist and use InnoDB. No maintenance access granted.';
    END IF;

    START TRANSACTION;
    SELECT COUNT(*), MIN(perm_id) INTO permission_count, permission_id FROM permissions
      WHERE perm_name = 'add_maintenance_log_asset';
    IF permission_count > 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Duplicate add_maintenance_log_asset permission definitions. No grants applied.';
    END IF;

    IF permission_count = 0 THEN
        SELECT COUNT(*), MIN(perm_cat_id) INTO category_count, category_id
          FROM permission_categories WHERE perm_cat_name = 'Asset Maintenance';
        IF category_count > 1 THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Duplicate Asset Maintenance permission categories. No grants applied.';
        END IF;
        IF category_count = 0 THEN
            INSERT INTO permission_categories (perm_cat_name) VALUES ('Asset Maintenance');
            SET category_id = LAST_INSERT_ID();
        END IF;

        INSERT INTO permissions (perm_name, perm_cat_id, `system`)
          VALUES ('add_maintenance_log_asset', category_id, 1);
        SET permission_id = LAST_INSERT_ID();
    END IF;

    INSERT INTO role_permissions (role_id, perm_id)
      SELECT admin_id, permission_id WHERE NOT EXISTS (
        SELECT 1 FROM role_permissions WHERE role_id = admin_id AND perm_id = permission_id
      );
    COMMIT;

    SELECT r.role_id, r.role_name, p.perm_name FROM roles r
      JOIN role_permissions rp ON rp.role_id = r.role_id
      JOIN permissions p ON p.perm_id = rp.perm_id
      WHERE r.role_id = admin_id AND p.perm_id = permission_id;
END$$
DELIMITER ;
CALL ams_asset_maintenance_admin_access();
DROP PROCEDURE IF EXISTS ams_asset_maintenance_admin_access;
