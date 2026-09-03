<?php

declare(strict_types=1);

/**
 * Minimal deployment seed for AMS.
 *
 * Run after importing rams_DB/rams_import_safe.sql:
 *   php rams_DB/seed_minimal_deploy_data.php
 *
 * Optional DB env vars:
 *   AMS_DB_HOST, AMS_DB_USER, AMS_DB_PASS, AMS_DB_NAME
 *   or DB_HOST, DB_USER, DB_PASS, DB_NAME
 *
 * Admin login:
 *   username: admin
 *   password: Admin@2026
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$host = getenv('AMS_DB_HOST') ?: getenv('DB_HOST') ?: '127.0.0.1';
$user = getenv('AMS_DB_USER') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('AMS_DB_PASS') ?: getenv('DB_PASS') ?: '';
$name = getenv('AMS_DB_NAME') ?: getenv('DB_NAME') ?: 'rams';

$db = new mysqli($host, $user, $pass, $name);
$db->set_charset('utf8mb4');

const DEMO_MARKER = '[AMS-MINIMAL-DEMO]';
const ADMIN_PASSWORD = 'Admin@2026';

function ident(string $name): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        throw new InvalidArgumentException("Unsafe SQL identifier: {$name}");
    }

    return "`{$name}`";
}

function tableExists(mysqli $db, string $table): bool
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.TABLES WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ?');
    $stmt->bind_param('s', $table);
    $stmt->execute();
    return (int) $stmt->get_result()->fetch_row()[0] > 0;
}

function columnExists(mysqli $db, string $table, string $column): bool
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?');
    $stmt->bind_param('ss', $table, $column);
    $stmt->execute();
    return (int) $stmt->get_result()->fetch_row()[0] > 0;
}

function ensureColumn(mysqli $db, string $table, string $column, string $definition): void
{
    if (tableExists($db, $table) && !columnExists($db, $table, $column)) {
        $db->query('ALTER TABLE ' . ident($table) . ' ADD COLUMN ' . ident($column) . ' ' . $definition);
    }
}

function ensureIndex(mysqli $db, string $table, string $index, string $columns, bool $unique = false): void
{
    $stmt = $db->prepare('SELECT COUNT(*) FROM information_schema.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND INDEX_NAME = ?');
    $stmt->bind_param('ss', $table, $index);
    $stmt->execute();
    if ((int) $stmt->get_result()->fetch_row()[0] === 0) {
        $db->query('ALTER TABLE ' . ident($table) . ' ADD ' . ($unique ? 'UNIQUE ' : '') . 'KEY ' . ident($index) . ' (' . $columns . ')');
    }
}

function upsert(mysqli $db, string $table, string $idColumn, array $match, array $data = []): int
{
    $where = implode(' AND ', array_map(static fn ($column) => ident($column) . ' = ?', array_keys($match)));
    $stmt = $db->prepare('SELECT ' . ident($idColumn) . ' FROM ' . ident($table) . " WHERE {$where} LIMIT 1");
    $stmt->execute(array_values($match));
    $existing = $stmt->get_result()->fetch_assoc();

    if ($existing) {
        if ($data) {
            $set = implode(', ', array_map(static fn ($column) => ident($column) . ' = ?', array_keys($data)));
            $update = $db->prepare('UPDATE ' . ident($table) . " SET {$set} WHERE " . ident($idColumn) . ' = ?');
            $update->execute([...array_values($data), (int) $existing[$idColumn]]);
        }
        return (int) $existing[$idColumn];
    }

    $row = array_merge($match, $data);
    $columns = implode(', ', array_map('ident', array_keys($row)));
    $placeholders = implode(', ', array_fill(0, count($row), '?'));
    $insert = $db->prepare('INSERT INTO ' . ident($table) . " ({$columns}) VALUES ({$placeholders})");
    $insert->execute(array_values($row));

    return (int) $db->insert_id;
}

function ensureLink(mysqli $db, string $table, array $match): void
{
    $where = implode(' AND ', array_map(static fn ($column) => ident($column) . ' = ?', array_keys($match)));
    $stmt = $db->prepare('SELECT 1 FROM ' . ident($table) . " WHERE {$where} LIMIT 1");
    $stmt->execute(array_values($match));
    if ($stmt->get_result()->fetch_row()) {
        return;
    }

    $columns = implode(', ', array_map('ident', array_keys($match)));
    $placeholders = implode(', ', array_fill(0, count($match), '?'));
    $insert = $db->prepare('INSERT INTO ' . ident($table) . " ({$columns}) VALUES ({$placeholders})");
    $insert->execute(array_values($match));
}

function scalar(mysqli $db, string $sql): int
{
    return (int) ($db->query($sql)->fetch_row()[0] ?? 0);
}

function datePlus(string $date, string $modifier, string $format = 'Y-m-d'): string
{
    return (new DateTimeImmutable($date))->modify($modifier)->format($format);
}

$db->begin_transaction();

try {
    $db->query("CREATE TABLE IF NOT EXISTS branch_office (
        branch_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        branch_code VARCHAR(30) NOT NULL,
        branch_name VARCHAR(255) NOT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (branch_id),
        UNIQUE KEY branch_code_unique (branch_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS states (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        state_name VARCHAR(120) NOT NULL,
        colour VARCHAR(20) DEFAULT '#36caff',
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY state_name_unique (state_name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS locations (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS asset_types (
        asset_id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(120) NOT NULL,
        short_code VARCHAR(20) DEFAULT NULL,
        colour VARCHAR(20) DEFAULT '#36caff',
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (asset_id),
        UNIQUE KEY asset_type_name_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS store_location (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(120) NOT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY store_location_name_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS managed_by_add_data (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(160) NOT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY managed_by_name_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS item_status (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(80) NOT NULL,
        colour VARCHAR(20) DEFAULT '#36caff',
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY item_status_name_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS item_types (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(160) NOT NULL,
        manufacturer INT UNSIGNED DEFAULT NULL,
        vendor_part_number INT UNSIGNED DEFAULT NULL,
        calibration INT(1) NOT NULL DEFAULT 0,
        maintenance INT(1) NOT NULL DEFAULT 1,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY item_type_name_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS fault_type_color_code (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        fault_type VARCHAR(160) NOT NULL,
        color VARCHAR(20) DEFAULT '#f59e0b',
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY fault_type_unique (fault_type)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS dashboard_status_colors (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        name VARCHAR(80) NOT NULL,
        color VARCHAR(20) NOT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY dashboard_status_unique (name)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS vendor_part_number (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        part_number VARCHAR(100) NOT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY vendor_part_unique (part_number)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS add_asset_items (
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
        item_picture VARCHAR(200) DEFAULT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        UNIQUE KEY item_qr_unique (items_qr_code)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS ticket (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS item_ticket (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        ticket_id INT UNSIGNED DEFAULT NULL,
        equipment_id INT UNSIGNED DEFAULT NULL,
        item_id INT UNSIGNED DEFAULT NULL,
        issue_date DATE DEFAULT NULL,
        fault_type_id INT UNSIGNED DEFAULT NULL,
        description TEXT,
        status VARCHAR(80) DEFAULT 'Open',
        date_of_completion DATE DEFAULT NULL,
        location VARCHAR(180) DEFAULT NULL,
        state VARCHAR(120) DEFAULT NULL,
        active INT(1) NOT NULL DEFAULT 1,
        PRIMARY KEY (id),
        KEY item_ticket_item_idx (item_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS maintenance_task_done (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        equipment_maintenance_id INT UNSIGNED DEFAULT NULL,
        task_done VARCHAR(255) DEFAULT NULL,
        remarks TEXT,
        active INT(1) NOT NULL DEFAULT 1,
        created_at DATETIME DEFAULT NULL,
        updated_at DATETIME DEFAULT NULL,
        PRIMARY KEY (id),
        KEY maintenance_task_done_maintenance_idx (equipment_maintenance_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    $db->query("CREATE TABLE IF NOT EXISTS next_maintenance_date (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT,
        equipment_id INT UNSIGNED NOT NULL,
        maintenance_date DATE DEFAULT NULL,
        frequency_year INT DEFAULT 1,
        maintenance_reminder_day INT DEFAULT 21,
        PRIMARY KEY (id),
        UNIQUE KEY next_maintenance_equipment_unique (equipment_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

    foreach (['equipments_asset', 'equipments'] as $table) {
        ensureColumn($db, $table, 'serial_number', 'VARCHAR(120) DEFAULT NULL');
        ensureColumn($db, $table, 'rfid', 'VARCHAR(120) DEFAULT NULL');
        ensureColumn($db, $table, 'calibration_date', 'DATE DEFAULT NULL');
        ensureColumn($db, $table, 'frequency_day', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'reminder_day', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'maintenance_date', 'DATE DEFAULT NULL');
        ensureColumn($db, $table, 'date_installed', 'DATE DEFAULT NULL');
        ensureColumn($db, $table, 'ownership', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'location_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'faulty_type_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'state_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'vendor_part_number_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'store_location_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'useful_life_years', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'salvage_value', 'DECIMAL(12,2) DEFAULT NULL');
        ensureColumn($db, $table, 'frequency_year', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'maintenance_reminder_day', 'INT DEFAULT NULL');
        ensureColumn($db, $table, 'price_of_purchase', 'DECIMAL(12,2) DEFAULT NULL');
        ensureColumn($db, $table, 'purchase_origin', 'VARCHAR(255) DEFAULT NULL');
        ensureColumn($db, $table, 'company_name', 'VARCHAR(180) DEFAULT NULL');
        ensureColumn($db, $table, 'person_in_contact', 'VARCHAR(180) DEFAULT NULL');
        ensureColumn($db, $table, 'contact_number', 'VARCHAR(80) DEFAULT NULL');
        ensureColumn($db, $table, 'purchased_by', 'VARCHAR(180) DEFAULT NULL');
        ensureColumn($db, $table, 'purchase_price', 'DECIMAL(12,2) DEFAULT NULL');
        ensureColumn($db, $table, 'branch_office_id', 'INT UNSIGNED DEFAULT NULL');
        ensureColumn($db, $table, 'invoice', 'VARCHAR(120) DEFAULT NULL');
    }

    if (tableExists($db, 'equipment_maintenance_asset')) {
        ensureColumn($db, 'equipment_maintenance_asset', 'ticket_number', 'VARCHAR(80) DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'maintenance_type_id', 'VARCHAR(40) DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'faulty_type', 'VARCHAR(160) DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'final_status', 'VARCHAR(80) DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'created_at', 'DATETIME DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'update_date', 'DATETIME DEFAULT NULL');
        ensureColumn($db, 'equipment_maintenance_asset', 'updated_at', 'DATETIME DEFAULT NULL');
        ensureIndex($db, 'equipment_maintenance_asset', 'ema_ticket_number_idx', '`ticket_number`');
    }

    $branches = [
        ['HQ', 'Head Office Kuala Lumpur'], ['JHR', 'Johor Operations'],
        ['SGR', 'Selangor Operations'], ['PNG', 'Penang Operations'],
    ];
    $branchIds = [];
    foreach ($branches as [$code, $label]) {
        $branchIds[] = upsert($db, 'branch_office', 'branch_id', ['branch_code' => $code], ['branch_name' => $label, 'active' => 1]);
    }

    $states = [
        ['JOHOR', '#36caff', 'Johor Bahru', '1.4927', '103.7414'],
        ['SELANGOR', '#35d6a0', 'Shah Alam', '3.0738', '101.5183'],
        ['PULAU PINANG', '#a47aff', 'George Town', '5.4141', '100.3288'],
        ['KEDAH', '#f5b942', 'Alor Setar', '6.1248', '100.3678'],
        ['PERAK', '#f16f79', 'Ipoh', '4.5975', '101.0901'],
        ['WILAYAH PERSEKUTUAN KUALA LUMPUR', '#2f80ff', 'Kuala Lumpur', '3.1390', '101.6869'],
    ];
    $locationIds = [];
    foreach ($states as [$stateName, $colour, $city, $lat, $long]) {
        $stateId = upsert($db, 'states', 'id', ['state_name' => $stateName], ['colour' => $colour, 'active' => 1]);
        $locationIds[] = upsert($db, 'locations', 'id', ['name' => $city], [
            'state_id' => $stateId,
            'address' => $city . ', ' . $stateName,
            'lat' => $lat,
            'long' => $long,
            'colour' => $colour,
            'active' => 1,
        ]);
    }

    $assetTypes = [
        ['Arm Roll RORO', 'RORO', '#36caff'], ['Box Van', 'BV', '#35d6a0'],
        ['Compactor', 'CPT', '#a47aff'], ['Waste Bin', 'WB', '#f16f79'],
        ['Water Jetter', 'WJ', '#f5b942'], ['Cleaning Machinery', 'CM', '#2f80ff'],
    ];
    $assetTypeIds = [];
    foreach ($assetTypes as [$typeName, $short, $colour]) {
        $assetTypeIds[] = upsert($db, 'asset_types', 'asset_id', ['name' => $typeName], [
            'short_code' => $short,
            'colour' => $colour,
            'active' => 1,
        ]);
    }

    foreach (['Stor Utama', 'Stor Operasi', 'Stor Penyelenggaraan'] as $storeName) {
        upsert($db, 'store_location', 'id', ['name' => $storeName], ['active' => 1]);
    }
    foreach (['Operations', 'Maintenance', 'Asset Control'] as $managedBy) {
        upsert($db, 'managed_by_add_data', 'id', ['name' => $managedBy], ['active' => 1]);
    }

    foreach ([['SERVICEABLE', '#35d6a0'], ['UNSERVICEABLE', '#f16f79'], ['MAINTENANCE', '#f5b942'], ['STORE', '#a47aff'], ['AVAILABLE', '#36caff']] as [$status, $colour]) {
        upsert($db, 'item_status', 'id', ['name' => $status], ['colour' => $colour, 'active' => 1]);
        upsert($db, 'dashboard_status_colors', 'id', ['name' => $status], ['color' => $colour, 'active' => 1]);
    }

    $faultIds = [];
    foreach ([['Hydraulic leak', '#f5b942'], ['Electrical fault', '#f16f79'], ['Routine inspection', '#36caff']] as [$fault, $colour]) {
        $faultIds[] = upsert($db, 'fault_type_color_code', 'id', ['fault_type' => $fault], ['color' => $colour, 'active' => 1]);
    }

    $manufacturerIds = [];
    if (tableExists($db, 'manufacturers')) {
        foreach (['Toyota', 'Volvo', 'Mercedes', 'Nissan'] as $maker) {
            $manufacturerIds[] = upsert($db, 'manufacturers', 'manufacturer_id', ['manufacturer_name' => $maker], ['description' => DEMO_MARKER, 'active' => 1]);
        }
    }
    if (!$manufacturerIds) {
        $manufacturerIds = [null];
    }

    $itemTypeIds = [];
    foreach (['Hydraulic Pump', 'Brake Assembly', 'GPS Tracker', 'Control Panel'] as $itemType) {
        $itemTypeIds[] = upsert($db, 'item_types', 'id', ['name' => $itemType], ['manufacturer' => $manufacturerIds[0], 'calibration' => 0, 'maintenance' => 1, 'active' => 1]);
    }

    $adminId = upsert($db, 'users', 'user_id', ['username' => 'admin'], [
        'full_name' => 'Admin',
        'user_code' => 'ADMIN',
        'email' => 'admin@ams.local',
        'password' => password_hash(ADMIN_PASSWORD, PASSWORD_BCRYPT),
        'active_branch' => $branchIds[0],
        'company_id' => 1,
        'designation' => 1,
        'user_group' => 1,
        'address_country' => 'MY',
        'timezone' => 'Asia/Kuala_Lumpur',
        'mobile' => 1,
        'active' => 1,
    ]);

    if (tableExists($db, 'roles')) {
        upsert($db, 'roles', 'role_id', ['role_id' => 1], ['role_name' => 'Administrator', 'description' => 'Full system access', 'active' => 1]);
    }
    if (tableExists($db, 'user_role')) {
        ensureLink($db, 'user_role', ['user_id' => $adminId, 'role_id' => 1]);
    }
    if (tableExists($db, 'role_permissions') && tableExists($db, 'permissions')) {
        $permissionRows = $db->query('SELECT perm_id FROM permissions WHERE active = 1')->fetch_all(MYSQLI_ASSOC);
        foreach ($permissionRows as $row) {
            ensureLink($db, 'role_permissions', ['role_id' => 1, 'perm_id' => (int) $row['perm_id']]);
        }
    }

    foreach ([['asset.manager', 'Asset Manager', 'AM'], ['maintenance.user', 'Maintenance User', 'MU']] as [$username, $fullName, $code]) {
        $userId = upsert($db, 'users', 'user_id', ['username' => $username], [
            'full_name' => $fullName,
            'user_code' => $code,
            'email' => str_replace('.', '-', $username) . '@ams.local',
            'password' => password_hash('Demo@2026', PASSWORD_BCRYPT),
            'active_branch' => $branchIds[1],
            'user_group' => 1,
            'address_country' => 'MY',
            'timezone' => 'Asia/Kuala_Lumpur',
            'active' => 1,
        ]);
        if (tableExists($db, 'user_role')) {
            ensureLink($db, 'user_role', ['user_id' => $userId, 'role_id' => 1]);
        }
    }

    $assetIds = [];
    $statuses = ['In use', 'In use', 'Maintenance', 'Standby'];
    for ($i = 1; $i <= 24; $i++) {
        $typeId = $assetTypeIds[($i - 1) % count($assetTypeIds)];
        $locationId = $locationIds[($i - 1) % count($locationIds)];
        $makerId = $manufacturerIds[($i - 1) % count($manufacturerIds)];
        $registration = sprintf('AMS-%03d', $i);
        $assetId = upsert($db, 'equipments_asset', 'equipment_id', ['equipment_registration' => $registration], [
            'equipment_name' => sprintf('Asset Unit %03d', $i),
            'equipment_manufacturer' => $makerId,
            'equipment_type' => $typeId,
            'equipment_status' => $statuses[$i % count($statuses)],
            'current_mileage' => 10000 + ($i * 500),
            'service_every_mileage' => 10000,
            'next_service_mileage' => 20000 + ($i * 500),
            'last_service_date' => datePlus('2026-01-01', '+' . ($i * 5) . ' days'),
            'service_interval_weeks' => 12,
            'next_service_date' => datePlus('2026-09-01', '+' . ($i * 4) . ' days'),
            'worked_days' => 120 + $i,
            'equipment_notes' => DEMO_MARKER . ' Minimal deployment asset.',
            'equipment_safe_load' => '1000KG',
            'active' => 1,
            'purchase_date' => datePlus('2023-01-01', '+' . ($i * 20) . ' days'),
            'serial_number' => sprintf('SN-AMS-%05d', $i),
            'date_installed' => datePlus('2023-03-01', '+' . ($i * 10) . ' days'),
            'location_id' => $locationId,
            'state_id' => (($i - 1) % count($states)) + 1,
            'store_location_id' => (($i - 1) % 3) + 1,
            'branch_office_id' => $branchIds[$i % count($branchIds)],
            'useful_life_years' => 8,
            'salvage_value' => 2500,
            'price_of_purchase' => 50000 + ($i * 1000),
            'purchase_price' => 50000 + ($i * 1000),
            'company_name' => 'AMS Demo Operations',
            'purchased_by' => 'Procurement',
            'invoice' => sprintf('INV-AMS-%04d', $i),
        ]);
        $assetIds[] = $assetId;
        upsert($db, 'next_maintenance_date', 'id', ['equipment_id' => $assetId], ['maintenance_date' => datePlus('2026-09-01', '+' . ($i * 4) . ' days'), 'frequency_year' => 1, 'maintenance_reminder_day' => 21]);
    }

    $itemStatuses = ['SERVICEABLE', 'SERVICEABLE', 'MAINTENANCE', 'STORE', 'UNSERVICEABLE'];
    $itemIds = [];
    for ($i = 1; $i <= 36; $i++) {
        $status = $itemStatuses[$i % count($itemStatuses)];
        $itemIds[] = upsert($db, 'add_asset_items', 'id', ['items_qr_code' => sprintf('CMP-AMS-%04d', $i)], [
            'asset_id' => $assetIds[($i - 1) % count($assetIds)],
            'item_name' => sprintf('%s %02d', ['Hydraulic Pump', 'Brake Assembly', 'GPS Tracker', 'Control Panel'][$i % 4], $i),
            'vendor_part_number' => sprintf('VP-AMS-%04d', $i),
            'manufacturer_name' => ['Toyota', 'Volvo', 'Mercedes', 'Nissan'][$i % 4],
            'manufacturer_part_number' => sprintf('MP-AMS-%04d', $i),
            'manufacturer_drawing_number' => sprintf('DWG-AMS-%04d', $i),
            'item_status' => $status,
            'item_status_id' => (($i % 5) + 1),
            'item_type_id' => $itemTypeIds[$i % count($itemTypeIds)],
            'faulty_type_id' => $faultIds[$i % count($faultIds)],
            'store_location_id' => (($i - 1) % 3) + 1,
            'active' => 1,
        ]);
    }

    for ($i = 1; $i <= 18; $i++) {
        $assetId = $assetIds[($i - 1) % count($assetIds)];
        $ticketNumber = sprintf('TCK-AMS-%04d', $i);
        $status = $i % 3 === 0 ? 'complete' : ($i % 3 === 1 ? 'in_progress' : 'IN-MAINTENANCE');
        upsert($db, 'ticket', 'id', ['ticket_number' => $ticketNumber], [
            'equipment_id' => $assetId,
            'asset_number' => $assetId,
            'issue_date' => datePlus('2026-08-01', '+' . $i . ' days'),
            'fault_type_id' => $faultIds[$i % count($faultIds)],
            'description' => DEMO_MARKER . ' Ticket seeded for deployment testing.',
            'status' => $status === 'complete' ? 'Closed' : 'Open',
            'active' => 1,
        ]);
        $maintenanceId = upsert($db, 'equipment_maintenance_asset', 'equipment_maintenance_id', ['ticket_number' => $ticketNumber], [
            'equipment_id' => $assetId,
            'maintenance_date' => datePlus('2026-08-01', '+' . $i . ' days'),
            'in_out' => $status === 'complete' ? 'Out of maintenance' : 'In maintenance',
            'maintenance_mileage' => 12000 + ($i * 600),
            'maintenance_notes' => DEMO_MARKER . ' Maintenance record seeded.',
            'maintenance_type_id' => $i % 4 === 0 ? 'preventive' : 'corrective',
            'faulty_type' => ['Hydraulic leak', 'Electrical fault', 'Routine inspection'][$i % 3],
            'final_status' => $status,
            'created_at' => datePlus('2026-08-01', '+' . $i . ' days', 'Y-m-d H:i:s'),
            'update_date' => datePlus('2026-08-03', '+' . $i . ' days', 'Y-m-d H:i:s'),
            'updated_at' => datePlus('2026-08-03', '+' . $i . ' days', 'Y-m-d H:i:s'),
        ]);
        upsert($db, 'maintenance_task_done', 'id', ['equipment_maintenance_id' => $maintenanceId, 'task_done' => 'Inspect and service asset'], [
            'remarks' => 'Seeded maintenance task for report testing.',
            'active' => 1,
            'created_at' => datePlus('2026-08-03', '+' . $i . ' days', 'Y-m-d H:i:s'),
            'updated_at' => datePlus('2026-08-03', '+' . $i . ' days', 'Y-m-d H:i:s'),
        ]);
    }

    $db->commit();

    echo "Minimal deployment seed completed.\n\n";
    echo "Admin username: admin\n";
    echo "Admin password: " . ADMIN_PASSWORD . "\n\n";
    foreach (['states', 'locations', 'branch_office', 'asset_types', 'equipments_asset', 'add_asset_items', 'ticket'] as $table) {
        echo str_pad($table, 22) . scalar($db, 'SELECT COUNT(*) FROM ' . ident($table)) . " rows\n";
    }
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, 'Minimal seeder failed and was rolled back: ' . $exception->getMessage() . "\n");
    exit(1);
}