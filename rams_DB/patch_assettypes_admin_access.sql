-- Explicitly requested Admin access repair. Do NOT apply to arbitrary roles.
-- Resolves one active role named exactly Admin or Administrator; ambiguous/missing roles abort.
-- Existing grants for other roles and all asset data are left unchanged. Safe to repeat.
DROP PROCEDURE IF EXISTS ams_assettypes_admin_access;
DELIMITER $$
CREATE PROCEDURE ams_assettypes_admin_access()
BEGIN
    DECLARE admin_count INT DEFAULT 0;
    DECLARE admin_id INT;
    DECLARE category_id INT;
    DECLARE category_count INT;
    DECLARE EXIT HANDLER FOR SQLEXCEPTION BEGIN ROLLBACK; RESIGNAL; END;

    SELECT COUNT(*), MIN(role_id) INTO admin_count, admin_id FROM roles
      WHERE LOWER(TRIM(role_name)) IN ('admin', 'administrator') AND active = 1;
    IF admin_count <> 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Expected one active Admin/Administrator role. Check roles table; no grants applied.';
    END IF;

    START TRANSACTION;
    SELECT COUNT(*), MIN(perm_cat_id) INTO category_count, category_id
      FROM permission_categories WHERE perm_cat_name = 'Asset Types';
    IF category_count > 1 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'Duplicate Asset Types permission categories. Resolve duplicates before applying this patch.';
    END IF;
    IF category_count = 0 THEN
        INSERT INTO permission_categories (perm_cat_name) VALUES ('Asset Types');
        SET category_id = LAST_INSERT_ID();
    END IF;

    INSERT INTO permissions (perm_name, perm_cat_id, `system`)
    SELECT wanted.perm_name, category_id, 1 FROM (
        SELECT 'list_assettypes' AS perm_name UNION ALL
        SELECT 'add_assettypes' UNION ALL SELECT 'edit_assettypes' UNION ALL
        SELECT 'list_admin' UNION ALL SELECT 'list_masters'
    ) wanted WHERE NOT EXISTS (SELECT 1 FROM permissions p WHERE p.perm_name = wanted.perm_name);

    INSERT INTO role_permissions (role_id, perm_id)
    SELECT admin_id, p.perm_id FROM permissions p
      WHERE p.perm_name IN ('list_assettypes','add_assettypes','edit_assettypes','list_admin','list_masters')
      AND NOT EXISTS (SELECT 1 FROM role_permissions rp WHERE rp.role_id = admin_id AND rp.perm_id = p.perm_id);
    COMMIT;

    SELECT r.role_id, r.role_name, p.perm_name FROM roles r
      JOIN role_permissions rp ON rp.role_id = r.role_id
      JOIN permissions p ON p.perm_id = rp.perm_id
      WHERE r.role_id = admin_id
      AND p.perm_name IN ('list_assettypes','add_assettypes','edit_assettypes','list_admin','list_masters')
      ORDER BY p.perm_name;
END$$
DELIMITER ;
CALL ams_assettypes_admin_access();
DROP PROCEDURE IF EXISTS ams_assettypes_admin_access;
