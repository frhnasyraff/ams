<?php

declare(strict_types=1);

/**
 * Realistic, fully-linked demo dataset for the Steve asset-management module.
 *
 * Safe to run repeatedly: every generated record has a stable natural key and
 * is updated in place on subsequent runs. Existing user records are preserved.
 * Demo login password: Demo@2026
 */

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$db = new mysqli('127.0.0.1', 'root', '', 'rams');
$db->set_charset('utf8mb4');

const DEMO_MARKER = '[STEVE-DEMO-2026]';
const DEMO_PASSWORD_HASH = '$2y$10$m00cEJRg797Iz3m7QipXAOpj7fn73IWK975CB2VWZjakwkIswYXp6';

function ident(string $name): string
{
    if (!preg_match('/^[A-Za-z0-9_]+$/', $name)) {
        throw new InvalidArgumentException("Unsafe SQL identifier: {$name}");
    }

    return "`{$name}`";
}

function upsert(mysqli $db, string $table, string $idColumn, array $match, array $data = []): int
{
    $where = implode(' AND ', array_map(
        static fn (string $column): string => ident($column) . ' = ?',
        array_keys($match)
    ));
    $select = 'SELECT ' . ident($idColumn) . ' FROM ' . ident($table) . " WHERE {$where} LIMIT 1";
    $statement = $db->prepare($select);
    $statement->execute(array_values($match));
    $existing = $statement->get_result()->fetch_assoc();

    if ($existing !== null) {
        if ($data !== []) {
            $set = implode(', ', array_map(
                static fn (string $column): string => ident($column) . ' = ?',
                array_keys($data)
            ));
            $update = $db->prepare(
                'UPDATE ' . ident($table) . " SET {$set} WHERE " . ident($idColumn) . ' = ?'
            );
            $update->execute([...array_values($data), (int) $existing[$idColumn]]);
        }

        return (int) $existing[$idColumn];
    }

    $row = array_merge($match, $data);
    $columns = implode(', ', array_map('ident', array_keys($row)));
    $placeholders = implode(', ', array_fill(0, count($row), '?'));
    $insert = $db->prepare(
        'INSERT INTO ' . ident($table) . " ({$columns}) VALUES ({$placeholders})"
    );
    $insert->execute(array_values($row));

    return (int) $db->insert_id;
}

function ensureLink(mysqli $db, string $table, array $match, array $data = []): void
{
    $where = implode(' AND ', array_map(
        static fn (string $column): string => ident($column) . ' = ?',
        array_keys($match)
    ));
    $statement = $db->prepare('SELECT 1 FROM ' . ident($table) . " WHERE {$where} LIMIT 1");
    $statement->execute(array_values($match));

    if ($statement->get_result()->fetch_row() !== null) {
        return;
    }

    $row = array_merge($match, $data);
    $columns = implode(', ', array_map('ident', array_keys($row)));
    $placeholders = implode(', ', array_fill(0, count($row), '?'));
    $insert = $db->prepare('INSERT INTO ' . ident($table) . " ({$columns}) VALUES ({$placeholders})");
    $insert->execute(array_values($row));
}

function rows(mysqli $db, string $sql): array
{
    return $db->query($sql)->fetch_all(MYSQLI_ASSOC);
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
    // ---------------------------------------------------------------------
    // Master data used by forms, filters, status badges and report joins.
    // ---------------------------------------------------------------------
    $branches = [
        ['HQ', 'Ibu Pejabat Kuala Lumpur'],
        ['SGR', 'Operasi Selangor'],
        ['JHR', 'Operasi Johor'],
        ['PNG', 'Operasi Pulau Pinang'],
        ['PRK', 'Operasi Perak'],
        ['KDH', 'Operasi Kedah'],
        ['PHG', 'Operasi Pahang'],
        ['SBH', 'Operasi Sabah'],
        ['SWK', 'Operasi Sarawak'],
        ['TRG', 'Operasi Terengganu'],
    ];
    $branchIds = [];
    foreach ($branches as [$code, $name]) {
        $branchIds[] = upsert($db, 'branch_office', 'branch_id', ['branch_code' => $code], [
            'branch_name' => $name,
            'active' => 1,
        ]);
    }

    $stateIds = [];
    foreach (rows($db, 'SELECT id, state_name FROM states WHERE active = 1 ORDER BY id') as $state) {
        $stateIds[$state['state_name']] = (int) $state['id'];
    }

    $sites = [
        ['Depot Bukit Raja', 'SELANGOR', '3.0801', '101.4988'],
        ['Workshop Shah Alam', 'SELANGOR', '3.0738', '101.5183'],
        ['Depot Pasir Gudang', 'JOHOR', '1.4703', '103.9029'],
        ['Workshop Senai', 'JOHOR', '1.6006', '103.6419'],
        ['Depot Bayan Lepas', 'PULAU PINANG', '5.2974', '100.2769'],
        ['Depot Ipoh Selatan', 'PERAK', '4.5670', '101.0820'],
        ['Depot Sungai Petani', 'KEDAH', '5.6470', '100.4877'],
        ['Depot Kuantan', 'PAHANG', '3.8077', '103.3260'],
        ['Depot Kota Kinabalu', 'SABAH', '5.9804', '116.0735'],
        ['Depot Kuching', 'SARAWAK', '1.5533', '110.3592'],
        ['Depot Kuala Terengganu', 'TERENGGANU', '5.3296', '103.1370'],
        ['Pusat Servis Putrajaya', 'WILAYAH PERSEKUTUAN PUTRAJAYA', '2.9264', '101.6964'],
        ['Depot Kota Bharu', 'KELANTAN', '6.1254', '102.2381'],
        ['Depot Melaka Tengah', 'MELAKA', '2.1896', '102.2501'],
        ['Depot Seremban', 'NEGERI SEMBILAN', '2.7258', '101.9424'],
        ['Depot Kangar', 'PERLIS', '6.4414', '100.1986'],
        ['Pusat Operasi Kuala Lumpur', 'WILAYAH PERSEKUTUAN KUALA LUMPUR', '3.1390', '101.6869'],
        ['Depot Labuan', 'WILAYAH PERSEKUTUAN LABUAN', '5.2831', '115.2308'],
    ];
    $locationIds = [];
    $locationState = [];
    foreach ($sites as $index => [$name, $stateName, $lat, $long]) {
        $stateId = $stateIds[$stateName];
        $locationId = upsert($db, 'locations', 'id', ['name' => $name], [
            'state_id' => $stateId,
            'address' => "Kawasan Operasi {$name}, Malaysia",
            'lat' => $lat,
            'long' => $long,
            'colour' => ['#26c6da', '#42a5f5', '#66bb6a', '#ab47bc'][$index % 4],
            'active' => 1,
        ]);
        $locationIds[] = $locationId;
        $locationState[$locationId] = $stateId;
    }

    $faults = [
        ['Hydraulic Leak', '#ef4444'],
        ['Electrical Fault', '#f59e0b'],
        ['Engine Overheating', '#f97316'],
        ['Brake System Fault', '#dc2626'],
        ['Tyre or Wheel Damage', '#eab308'],
        ['Sensor Malfunction', '#8b5cf6'],
        ['Structural Damage', '#be123c'],
        ['Scheduled Inspection Finding', '#0ea5e9'],
    ];
    $faultIds = [];
    foreach ($faults as [$name, $colour]) {
        $faultIds[] = upsert($db, 'fault_type_color_code', 'id', ['fault_type' => $name], [
            'color' => $colour,
            'active' => 1,
        ]);
    }

    $taskNames = [
        'Inspect hydraulic hoses', 'Replace engine oil', 'Replace oil filter',
        'Check brake pressure', 'Inspect electrical harness', 'Calibrate sensor',
        'Lubricate moving joints', 'Check tyre pressure', 'Inspect safety interlock',
        'Test emergency stop', 'Clean cooling system', 'Tighten mounting bolts',
        'Road test and verification', 'Update service label', 'Final safety inspection',
    ];
    $taskIds = [];
    foreach ($taskNames as $name) {
        $taskIds[] = upsert($db, 'task', 'id', ['name' => $name], ['active' => 1]);
    }

    $taskLists = [
        ['Daily operator inspection', 1], ['Weekly safety inspection', 7],
        ['Monthly lubrication service', 30], ['Quarterly preventive service', 90],
        ['Semi-annual hydraulic inspection', 180], ['Annual statutory inspection', 365],
        ['Brake service checklist', 120], ['Electrical system checklist', 180],
        ['Body and chassis inspection', 365], ['Post-repair quality check', 0],
    ];
    $taskListIds = [];
    foreach ($taskLists as [$name, $days]) {
        $taskListIds[] = upsert($db, 'task_list', 'id', ['name' => $name], [
            'frequency_in_days' => $days,
            'active' => 1,
        ]);
    }

    $dashboardColours = [
        ['SERVICEABLE', '#22c55e'], ['UNSERVICEABLE', '#f43f5e'],
        ['MAINTENANCE', '#f59e0b'], ['STORE', '#8b5cf6'],
        ['AVAILABLE', '#0ea5e9'], ['IN USE', '#a855f7'],
    ];
    foreach ($dashboardColours as [$name, $colour]) {
        upsert($db, 'dashboard_status_colors', 'id', ['name' => $name], [
            'color' => $colour,
            'active' => 1,
        ]);
    }

    $partIds = [];
    $drawingIds = [];
    for ($i = 1; $i <= 24; $i++) {
        $partIds[] = upsert($db, 'vendor_part_number', 'id', [
            'part_number' => sprintf('VP-MY-%04d', $i),
        ], ['active' => 1]);
        $drawingIds[] = upsert($db, 'vendor_manufacturing_drawing_number', 'id', [
            'drawing_number' => sprintf('DWG-MY-%03d-R%02d', (int) ceil($i / 3), (($i - 1) % 3) + 1),
        ], ['active' => 1]);
    }

    $manufacturerRows = rows($db, 'SELECT manufacturer_id, manufacturer_name FROM manufacturers ORDER BY manufacturer_id');
    $manufacturerIds = array_map(static fn (array $row): int => (int) $row['manufacturer_id'], $manufacturerRows);
    $manufacturerNames = [];
    foreach ($manufacturerRows as $row) {
        $manufacturerNames[(int) $row['manufacturer_id']] = $row['manufacturer_name'];
    }
    foreach ($manufacturerRows as $manufacturerIndex => $manufacturer) {
        for ($model = 1; $model <= 3; $model++) {
            upsert($db, 'vendor_manufacturing_number', 'id', [
                'manufacturer_number' => sprintf('%s-%d%02d', strtoupper(substr($manufacturer['manufacturer_name'], 0, 3)), 20 + $manufacturerIndex, $model),
            ], [
                'manufacturer_name' => $manufacturer['manufacturer_name'],
                'active' => 1,
            ]);
        }
    }

    $componentTypes = [
        'Hydraulic Pump', 'Control Panel', 'GPS Tracker', 'Safety Camera',
        'Hydraulic Cylinder', 'Compactor Blade', 'Brake Assembly', 'Tyre Set',
        'Battery Pack', 'Water Pump', 'Pressure Sensor', 'Lighting Module',
    ];
    $itemTypeIds = [];
    foreach ($componentTypes as $index => $name) {
        $itemTypeIds[] = upsert($db, 'item_types', 'id', ['name' => $name], [
            'manufacturer' => $manufacturerIds[$index % count($manufacturerIds)],
            'vendor_part_number' => $partIds[$index % count($partIds)],
            'calibration' => in_array($name, ['GPS Tracker', 'Pressure Sensor'], true) ? 1 : 0,
            'maintenance' => 1,
            'active' => 1,
        ]);
    }

    $consumableNames = [
        ['Engine Oil 15W-40', 480.00, 120.00], ['Hydraulic Oil ISO 46', 720.00, 180.00],
        ['Multipurpose Grease', 160.00, 40.00], ['Coolant Premix', 240.00, 60.00],
        ['Oil Filter', 95.00, 25.00], ['Air Filter', 80.00, 20.00],
        ['Brake Fluid DOT 4', 60.00, 15.00], ['Cleaning Solvent', 110.00, 30.00],
    ];
    $consumableIds = [];
    foreach ($consumableNames as [$name, $stock, $replenishment]) {
        $consumableIds[] = upsert($db, 'consumables', 'consumable_id', ['consumable_name' => $name], [
            'consumable_notes' => DEMO_MARKER . ' Standard workshop stock',
            'consumable_stock' => $stock,
            'consumable_replenishment' => $replenishment,
            'active' => 1,
        ]);
    }

    // ---------------------------------------------------------------------
    // Demo users and roles. All accounts share the documented demo password.
    // ---------------------------------------------------------------------
    $demoUsers = [
        ['aiman.hakim', 'Aiman Hakim', 'AH', 'Asset Administrator'],
        ['nur.aisyah', 'Nur Aisyah', 'NA', 'Maintenance Planner'],
        ['daniel.lee', 'Daniel Lee', 'DL', 'Fleet Supervisor'],
        ['kavitha.raj', 'Kavitha Raj', 'KR', 'Inventory Controller'],
        ['farid.zakaria', 'Farid Zakaria', 'FZ', 'Workshop Coordinator'],
        ['lim.jia.wei', 'Lim Jia Wei', 'LJW', 'Operations Executive'],
        ['siti.hajar', 'Siti Hajar', 'SH', 'Compliance Officer'],
        ['jason.tan', 'Jason Tan', 'JT', 'Procurement Executive'],
        ['azlan.ismail', 'Azlan Ismail', 'AI', 'Regional Manager'],
        ['priya.nair', 'Priya Nair', 'PN', 'Finance Executive'],
        ['hafiz.rahman', 'Hafiz Rahman', 'HR', 'Technician'],
        ['melissa.wong', 'Melissa Wong', 'MW', 'System Auditor'],
        ['demo.viewer', 'Demo Viewer', 'DV', 'Read-only Reviewer'],
        ['demo.inactive', 'Demo Inactive Account', 'DI', 'Former Contractor'],
    ];
    $userIds = [];
    foreach ($demoUsers as $index => [$username, $fullName, $code, $jobTitle]) {
        $userId = upsert($db, 'users', 'user_id', ['username' => $username], [
            'full_name' => $fullName,
            'user_code' => $code,
            'email' => str_replace('.', '-', $username) . '@demo.steve.local',
            'password' => DEMO_PASSWORD_HASH,
            'active_branch' => $branchIds[$index % count($branchIds)],
            'company_id' => (($index % 7) + 1),
            'address_line_1' => 'Demo Operations Office',
            'address_line_2' => $jobTitle,
            'address_zip' => sprintf('%05d', 40000 + ($index * 100)),
            'address_city' => ['Shah Alam', 'Johor Bahru', 'George Town', 'Ipoh'][$index % 4],
            'address_state' => ['Selangor', 'Johor', 'Pulau Pinang', 'Perak'][$index % 4],
            'address_country' => 'MY',
            'phone' => sprintf('+6038000%04d', 1100 + $index),
            'timezone' => 'Asia/Kuala_Lumpur',
            'active' => $username === 'demo.inactive' ? 0 : 1,
        ]);
        $userIds[] = $userId;
        ensureLink($db, 'user_role', ['user_id' => $userId, 'role_id' => [15, 8, 7, 16, 17][$index % 5]]);
    }

    // ---------------------------------------------------------------------
    // Fleet assets distributed across Malaysia, with financial metadata.
    // ---------------------------------------------------------------------
    $assetTypes = rows($db, 'SELECT asset_id, name FROM asset_types WHERE active = 1 ORDER BY asset_id');
    $assetStatuses = ['SERVICEABLE', 'SERVICEABLE', 'In use', 'AVAILABLE', 'STORE', 'MAINTENANCE', 'UNSERVICEABLE', 'In use', 'SERVICEABLE', 'SERVICEABLE'];
    $assetIds = [];
    $assetNames = [];
    for ($i = 1; $i <= 60; $i++) {
        $type = $assetTypes[($i - 1) % count($assetTypes)];
        $typeId = (int) $type['asset_id'];
        $locationId = $locationIds[($i - 1) % count($locationIds)];
        $manufacturerId = $manufacturerIds[($i - 1) % count($manufacturerIds)];
        $status = $assetStatuses[($i - 1) % count($assetStatuses)];
        $purchaseYear = 2018 + (($i - 1) % 8);
        $purchasePrice = 38000 + (($typeId * 18500) + ($i * 1250));
        $short = strtoupper(preg_replace('/[^A-Za-z]/', '', $type['name']));
        $short = substr($short, 0, 3);
        $assetName = sprintf('%s-%s-%03d', $short, ['CTR', 'NTH', 'STH', 'EST'][$i % 4], $i);
        $assetId = upsert($db, 'equipments_asset', 'equipment_id', [
            'equipment_registration' => sprintf('DEMO-MY-%04d', $i),
        ], [
            'serial_number' => sprintf('SN26%02d%05d', $typeId, $i),
            'equipment_name' => $assetName,
            'equipment_manufacturer' => $manufacturerId,
            'equipment_type' => $typeId,
            'equipment_status' => $status,
            'current_mileage' => 18000 + ($i * 1375),
            'service_every_mileage' => 10000,
            'next_service_mileage' => 28000 + ($i * 1375),
            'last_service_date' => datePlus('2026-01-05', '+' . (($i * 3) % 180) . ' days'),
            'service_interval_weeks' => [4, 8, 12, 24][$i % 4],
            'next_service_date' => datePlus('2026-09-01', '+' . (($i * 5) % 150) . ' days'),
            'worked_days' => 240 + ($i * 7),
            'equipment_notes' => DEMO_MARKER . ' Operational fleet record with complete service history.',
            'equipment_safe_load' => 1000 + ($typeId * 500),
            'active' => $i > 56 ? 0 : 1,
            'purchase_date' => sprintf('%d-%02d-%02d', $purchaseYear, (($i - 1) % 12) + 1, (($i * 3) % 25) + 1),
            'rfid' => sprintf('RFID-DEMO-%05d', $i),
            'calibration_date' => datePlus('2026-02-01', '+' . (($i * 2) % 150) . ' days'),
            'frequency_day' => [30, 60, 90, 180][$i % 4],
            'reminder_day' => [7, 14, 21][$i % 3],
            'maintenance_date' => datePlus('2026-09-15', '+' . (($i * 4) % 120) . ' days'),
            'date_installed' => sprintf('%d-%02d-15', $purchaseYear, (($i + 2) % 12) + 1),
            'ownership' => (($i - 1) % 7) + 1,
            'location_id' => $locationId,
            'faulty_type_id' => in_array($status, ['UNSERVICEABLE', 'MAINTENANCE'], true) ? $faultIds[$i % count($faultIds)] : 1,
            'state_id' => $locationState[$locationId],
            'vendor_part_number_id' => $partIds[$i % count($partIds)],
            'store_location_id' => ($i % 6) + 1,
            'useful_life_years' => 8 + ($i % 5),
            'salvage_value' => round($purchasePrice * 0.10, 2),
            'frequency_year' => 1,
            'maintenance_reminder_day' => 21,
            'price_of_purchase' => $purchasePrice,
            'purchase_origin' => 'Authorised Malaysian distributor',
            'company_name' => ['Bytespace Fleet Services', 'Metro Environmental Services', 'Northern Municipal Operations'][$i % 3],
            'person_in_contact' => 'Demo Fleet Contact',
            'contact_number' => '+60380009999',
            'purchased_by' => 'Procurement Department',
            'purchase_price' => $purchasePrice,
            'branch_office_id' => $branchIds[$i % count($branchIds)],
            'invoice' => sprintf('INV-DEMO-2026-%04d', $i),
        ]);
        $assetIds[] = $assetId;
        $assetNames[$assetId] = $assetName;

        upsert($db, 'next_maintenance_date', 'id', ['equipment_id' => $assetId], [
            'maintenance_date' => datePlus('2026-09-01', '+' . (($i * 5) % 150) . ' days'),
            'frequency_year' => 1,
            'maintenance_reminder_day' => 21,
        ]);

        for ($reading = 1; $reading <= 4; $reading++) {
            $readingDate = datePlus('2026-01-10', '+' . (($reading - 1) * 60 + ($i % 25)) . ' days');
            upsert($db, 'equipment_mileage_asset', 'equipment_mileage_id', [
                'equipment_id' => $assetId,
                'date_recorded' => $readingDate,
            ], ['mileage' => 15000 + ($i * 1375) + ($reading * 850)]);
        }

        for ($usage = 0; $usage < 2; $usage++) {
            $usageDate = datePlus('2026-02-01', '+' . (($usage * 120) + ($i % 28)) . ' days');
            upsert($db, 'equipment_consumables_asset', 'equipment_consumable_id', [
                'equipment_id' => $assetId,
                'consumable_id' => $consumableIds[($i + $usage) % count($consumableIds)],
                'date_recorded' => $usageDate,
            ], ['quantity' => 2 + (($i + $usage) % 9)]);
        }
    }

    // ---------------------------------------------------------------------
    // Components attached to assets, including serviceable and faulty items.
    // ---------------------------------------------------------------------
    $itemStatuses = rows($db, 'SELECT id, name FROM item_status ORDER BY id');
    $itemStatusByName = [];
    foreach ($itemStatuses as $row) {
        $itemStatusByName[strtoupper($row['name'])] = (int) $row['id'];
    }
    $componentStatuses = ['SERVICEABLE', 'SERVICEABLE', 'STORE', 'MAINTENANCE', 'UNSERVICEABLE', 'SERVICEABLE'];
    $itemIds = [];
    for ($i = 1; $i <= 180; $i++) {
        $assetId = $assetIds[($i - 1) % count($assetIds)];
        $typeIndex = ($i - 1) % count($componentTypes);
        $status = $componentStatuses[($i - 1) % count($componentStatuses)];
        $manufacturerId = $manufacturerIds[$i % count($manufacturerIds)];
        $itemIds[] = upsert($db, 'add_asset_items', 'id', [
            'items_qr_code' => sprintf('CMP-DEMO-%05d', $i),
        ], [
            'asset_id' => $assetId,
            'item_name' => sprintf('%s %03d', $componentTypes[$typeIndex], $i),
            'vendor_part_number' => sprintf('VP-MY-%04d', (($i - 1) % 24) + 1),
            'manufacturer_name' => $manufacturerNames[$manufacturerId],
            'manufacturer_part_number' => sprintf('MP-%02d-%05d', $manufacturerId, $i),
            'manufacturer_drawing_number' => sprintf('DWG-MY-%03d-R%02d', (($i - 1) % 8) + 1, (($i - 1) % 3) + 1),
            'item_status' => $status,
            'item_status_id' => $itemStatusByName[$status] ?? 1,
            'item_type_id' => $itemTypeIds[$typeIndex],
            'faulty_type_id' => in_array($status, ['UNSERVICEABLE', 'MAINTENANCE'], true) ? $faultIds[$i % count($faultIds)] : 1,
            'store_location_id' => ($i % 6) + 1,
            'calibration_date' => datePlus('2026-03-01', '+' . ($i % 150) . ' days'),
            'frequency_day' => [30, 90, 180, 365][$i % 4],
            'reminder_day' => [7, 14, 30][$i % 3],
            'maintenance_date' => datePlus('2026-09-10', '+' . ($i % 120) . ' days'),
            'active' => $i > 174 ? 0 : 1,
        ]);
    }

    // ---------------------------------------------------------------------
    // Corrective/preventive tickets and their linked maintenance outcomes.
    // ---------------------------------------------------------------------
    for ($i = 1; $i <= 84; $i++) {
        $assetId = $assetIds[($i - 1) % count($assetIds)];
        $ticketNumber = sprintf('CM-DEMO-2026-%04d', $i);
        $issueDate = datePlus('2026-01-02', '+' . (($i * 3) % 238) . ' days');
        $finalStatus = match ($i % 5) {
            0, 1 => 'complete',
            2, 3 => 'in_progress',
            default => 'IN-MAINTENANCE',
        };
        upsert($db, 'ticket', 'id', ['ticket_number' => $ticketNumber], [
            'equipment_id' => $assetId,
            'asset_number' => $assetId,
            'issue_date' => $issueDate,
            'fault_type_id' => $faultIds[$i % count($faultIds)],
            'description' => DEMO_MARKER . ' ' . $faults[$i % count($faults)][0] . ' reported during operator inspection.',
            'status' => $finalStatus === 'complete' ? 'Closed' : ($finalStatus === 'in_progress' ? 'In Progress' : 'Open'),
            'active' => 1,
        ]);
        $completionDate = datePlus($issueDate, '+' . (($i % 8) + 1) . ' days', 'Y-m-d H:i:s');
        $maintenanceId = upsert($db, 'equipment_maintenance_asset', 'equipment_maintenance_id', [
            'ticket_number' => $ticketNumber,
        ], [
            'equipment_id' => $assetId,
            'maintenance_date' => $issueDate,
            'in_out' => $finalStatus === 'complete' ? 'Out of maintenance' : 'In maintenance',
            'maintenance_mileage' => 19000 + ($i * 740),
            'maintenance_notes' => DEMO_MARKER . ' Diagnosis, repair and safety verification recorded.',
            'maintenance_type_id' => $i % 4 === 0 ? 'preventive' : 'corrective',
            'faulty_type' => $faults[$i % count($faults)][0],
            'final_status' => $finalStatus,
            'created_at' => $issueDate . ' 08:30:00',
            'update_date' => $completionDate,
            'updated_at' => $completionDate,
        ]);
        upsert($db, 'maintenance_task_done', 'id', [
            'equipment_maintenance_id' => $maintenanceId,
            'task_done' => $taskNames[$i % count($taskNames)],
        ], [
            'remarks' => $finalStatus === 'complete' ? 'Repair verified and asset released for operation.' : 'Work is progressing according to workshop schedule.',
            'active' => 1,
            'created_at' => $completionDate,
            'updated_at' => $completionDate,
        ]);
    }

    // Numeric maintenance type IDs are retained for the legacy maintenance report join.
    foreach ($assetIds as $index => $assetId) {
        if (($index % 10) !== 5) {
            continue;
        }
        $ticketNumber = sprintf('MS-DEMO-2026-%04d', $index + 1);
        $maintenanceId = upsert($db, 'equipment_maintenance_asset', 'equipment_maintenance_id', ['ticket_number' => $ticketNumber], [
            'equipment_id' => $assetId,
            'maintenance_date' => datePlus('2026-08-01', '+' . $index . ' days'),
            'in_out' => 'In maintenance',
            'maintenance_mileage' => 28000 + ($index * 950),
            'maintenance_notes' => DEMO_MARKER . ' Scheduled workshop maintenance.',
            'maintenance_type_id' => (string) (($index % 2) + 1),
            'faulty_type' => $faults[$index % count($faults)][0],
            'final_status' => 'in_progress',
            'created_at' => datePlus('2026-08-01', '+' . $index . ' days', 'Y-m-d H:i:s'),
            'update_date' => datePlus('2026-08-01', '+' . ($index + 2) . ' days', 'Y-m-d H:i:s'),
        ]);
        upsert($db, 'maintenance_task_done', 'id', [
            'equipment_maintenance_id' => $maintenanceId,
            'task_done' => 'Scheduled preventive inspection',
        ], [
            'remarks' => 'Inspection and replacement parts are in progress.',
            'active' => 1,
        ]);
    }

    // Component fault tickets and maintenance logs.
    for ($i = 1; $i <= 72; $i++) {
        $itemId = $itemIds[($i * 2) % count($itemIds)];
        $issueDate = datePlus('2026-01-06', '+' . (($i * 3) % 230) . ' days');
        $finalStatus = $i % 3 === 0 ? 'COMPLETE' : ($i % 3 === 1 ? 'IN PROGRESS' : 'PENDING');
        $itemTicketId = upsert($db, 'item_ticket', 'id', [
            'item_id' => $itemId,
            'issue_date' => $issueDate,
        ], [
            'fault_type_id' => $faultIds[$i % count($faultIds)],
            'description' => DEMO_MARKER . ' Component inspection identified ' . strtolower($faults[$i % count($faults)][0]) . '.',
            'status' => $finalStatus,
            'date_of_completion' => $finalStatus === 'COMPLETE' ? datePlus($issueDate, '+' . (($i % 6) + 1) . ' days') : null,
            'active' => 1,
        ]);
        $itemMaintenanceId = upsert($db, 'logs_item_maintenance', 'id', [
            'item_ticket_id' => $itemTicketId,
        ], [
            'update_date' => datePlus($issueDate, '+2 days', 'Y-m-d H:i:s'),
            'created_at' => $issueDate . ' 09:15:00',
            'updated_at' => datePlus($issueDate, '+2 days', 'Y-m-d H:i:s'),
            'final_status' => $finalStatus,
            'notes' => DEMO_MARKER . ' Component repair workflow updated.',
            'active' => 1,
        ]);
        upsert($db, 'logs_item_maintenance_task_done', 'id', [
            'item_maintenance_id' => $itemMaintenanceId,
            'task_id' => $taskIds[$i % count($taskIds)],
        ], [
            'maintenance_task_id' => $taskListIds[$i % count($taskListIds)],
            'status' => $finalStatus,
            'active' => 1,
            'created_at' => datePlus($issueDate, '+1 day', 'Y-m-d H:i:s'),
        ]);
    }

    // ---------------------------------------------------------------------
    // Disposal lifecycle records with status history for review pages.
    // ---------------------------------------------------------------------
    $writeOffReasonIds = array_map(
        static fn (array $row): int => (int) $row['id'],
        rows($db, 'SELECT id FROM write_off_reasons WHERE active = 1 ORDER BY id')
    );
    $disposalMethodIds = array_map(
        static fn (array $row): int => (int) $row['id'],
        rows($db, 'SELECT id FROM disposal_methods WHERE active = 1 ORDER BY id')
    );
    $disposalStatuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected', 'completed'];
    for ($i = 1; $i <= 24; $i++) {
        $status = $disposalStatuses[($i - 1) % count($disposalStatuses)];
        $created = datePlus('2026-02-01', '+' . ($i * 7) . ' days', 'Y-m-d H:i:s');
        $requestId = upsert($db, 'asset_disposal_requests', 'id', [
            'request_number' => sprintf('ADR-DEMO-26-%03d', $i),
        ], [
            'equipment_asset_id' => $assetIds[(35 + $i) % count($assetIds)],
            'write_off_reason_id' => $writeOffReasonIds[$i % count($writeOffReasonIds)],
            'disposal_method_id' => $disposalMethodIds[$i % count($disposalMethodIds)],
            'estimated_value' => 1800 + ($i * 275),
            'justification' => DEMO_MARKER . ' Assessment confirms disposal is more economical than continued repair.',
            'status' => $status,
            'created_at' => $created,
            'updated_at' => datePlus($created, '+3 days', 'Y-m-d H:i:s'),
        ]);
        upsert($db, 'asset_disposal_status', 'id', [
            'request_id' => $requestId,
            'status' => 'submitted',
        ], [
            'disposal_method_id' => $disposalMethodIds[$i % count($disposalMethodIds)],
            'user_id' => $userIds[$i % count($userIds)],
            'created_at' => $created,
        ]);
        if ($status !== 'draft' && $status !== 'submitted') {
            upsert($db, 'asset_disposal_status', 'id', [
                'request_id' => $requestId,
                'status' => $status,
            ], [
                'disposal_method_id' => $disposalMethodIds[$i % count($disposalMethodIds)],
                'user_id' => $userIds[($i + 1) % count($userIds)],
                'created_at' => datePlus($created, '+3 days', 'Y-m-d H:i:s'),
            ]);
        }
    }

    // ---------------------------------------------------------------------
    // Orders, workers and deployment history used by the home dashboard.
    // ---------------------------------------------------------------------
    $workerIds = [];
    $workerNames = [
        'Muhammad Irfan', 'Nadia Zulkifli', 'Chong Wei Ming', 'Arun Kumar',
        'Faizal Hamid', 'Nurul Iman', 'Goh Kai Wen', 'Shafiq Razak',
        'Rina Abdullah', 'Dev Anand', 'Amirul Syafiq', 'Tan Mei Ling',
    ];
    foreach ($workerNames as $index => $name) {
        $workerIds[] = upsert($db, 'workers', 'worker_id', [
            'ic_number' => sprintf('DEMO-DRV-%03d', $index + 1),
        ], [
            'worker_name' => $name,
            'worker_order' => $index + 10,
            'worker_type' => $index % 4 === 0 ? 'van-driver' : 'contract-monthly',
            'address' => DEMO_MARKER . ' Operations staff demo record',
            'worker_resource_type' => 'Fleet Operations',
            'joining_date' => sprintf('%d-%02d-01', 2019 + ($index % 6), ($index % 12) + 1),
            'contact_number' => sprintf('+6038100%04d', 2100 + $index),
            'shift_1' => '08:00-17:00',
            'max_ot_hours' => 20,
            'worker_notes' => DEMO_MARKER . ' Trained fleet operator.',
            'last_worked' => datePlus('2026-08-30', '-' . ($index % 7) . ' days'),
            'active' => 1,
        ]);
    }

    $legacyEquipmentIds = array_map(
        static fn (array $row): int => (int) $row['equipment_id'],
        rows($db, 'SELECT equipment_id FROM equipments WHERE active = 1 ORDER BY equipment_id')
    );
    for ($i = 1; $i <= 36; $i++) {
        $workerId = $workerIds[($i - 1) % count($workerIds)];
        $workerName = $workerNames[($i - 1) % count($workerNames)];
        $assetId = $assetIds[($i - 1) % count($assetIds)];
        $tripDate = datePlus('2026-07-01', '+' . (($i - 1) % 58) . ' days');
        upsert($db, 'vehicle_history_asset', 'vh_id', [
            'vh_date' => $tripDate,
            'equipment_id' => $assetId,
            'driver_id' => $workerId,
        ], [
            'vh_date_end' => $tripDate,
            'vh_time_start' => sprintf('%02d:00', 6 + ($i % 4)),
            'vh_time_end' => sprintf('%02d:30', 15 + ($i % 4)),
            'vh_location_start' => $sites[($i - 1) % count($sites)][0],
            'vh_location_end' => $sites[$i % count($sites)][0],
            'vh_driver_name_ic_number' => $workerName . ' (DEMO)',
        ]);
        if ($legacyEquipmentIds !== []) {
            upsert($db, 'vehicle_history', 'vh_id', [
                'vh_date' => $tripDate,
                'equipment_id' => $legacyEquipmentIds[($i - 1) % count($legacyEquipmentIds)],
                'driver_id' => $workerId,
            ], [
                'vh_time_start' => sprintf('%02d:00', 7 + ($i % 3)),
                'vh_time_end' => sprintf('%02d:30', 16 + ($i % 3)),
                'vh_location_start' => $sites[($i - 1) % count($sites)][0],
                'vh_location_end' => $sites[$i % count($sites)][0],
                'vh_driver_name_ic_number' => $workerName . ' (DEMO)',
            ]);
        }
    }

    for ($i = 1; $i <= 48; $i++) {
        $created = datePlus('2026-01-03', '+' . (($i * 5) % 235) . ' days', 'Y-m-d H:i:s');
        upsert($db, 'orders', 'id', ['order_number' => sprintf('ORD-DEMO-26-%04d', $i)], [
            'equipment_id' => $legacyEquipmentIds === [] ? null : $legacyEquipmentIds[$i % count($legacyEquipmentIds)],
            'asset_id' => $assetIds[$i % count($assetIds)],
            'status' => ['Scheduled', 'In Progress', 'Completed', 'Completed'][$i % 4],
            'active' => 1,
            'created_at' => $created,
            'remarks_updated_at' => datePlus($created, '+2 days', 'Y-m-d H:i:s'),
            'company_id' => ($i % 7) + 1,
        ]);
    }

    // ---------------------------------------------------------------------
    // Time-series status events for Performance/MTBF charts and audit logs.
    // ---------------------------------------------------------------------
    $statusCycle = ['SERVICEABLE', 'SERVICEABLE', 'MAINTENANCE', 'SERVICEABLE', 'UNSERVICEABLE', 'SERVICEABLE', 'SERVICEABLE', 'MAINTENANCE'];
    foreach (array_slice($assetIds, 0, 36) as $assetIndex => $assetId) {
        for ($month = 1; $month <= 8; $month++) {
            $newStatus = $statusCycle[($month + $assetIndex) % count($statusCycle)];
            $oldStatus = $statusCycle[($month + $assetIndex - 1 + count($statusCycle)) % count($statusCycle)];
            $timestamp = sprintf('2026-%02d-%02d %02d:%02d:00', $month, 8 + ($assetIndex % 18), 8 + ($assetIndex % 9), ($assetIndex * 7) % 60);
            upsert($db, 'asset_logs', 'log_id', [
                'log_item_table' => 'equipments_asset',
                'log_item_id' => $assetId,
                'log_code' => 'Asset_Updated',
                'timestamp' => $timestamp,
            ], [
                'log_user_id' => $userIds[$assetIndex % count($userIds)],
                'log_description' => "Status changed from '{$oldStatus}' → '{$newStatus}' (legacy â†’ '{$newStatus}'). " . DEMO_MARKER,
                'log_ip' => '127.0.0.1',
            ]);
        }
    }

    foreach (array_slice($itemIds, 0, 72) as $itemIndex => $itemId) {
        for ($month = 1; $month <= 6; $month++) {
            $newStatus = $statusCycle[($month + $itemIndex + 2) % count($statusCycle)];
            $oldStatus = $statusCycle[($month + $itemIndex + 1) % count($statusCycle)];
            $timestamp = sprintf('2026-%02d-%02d %02d:%02d:00', $month + 2, 7 + ($itemIndex % 19), 7 + ($itemIndex % 10), ($itemIndex * 5) % 60);
            upsert($db, 'asset_logs', 'log_id', [
                'log_item_table' => 'add_asset_items',
                'log_item_id' => $itemId,
                'log_code' => 'Component_Updated',
                'timestamp' => $timestamp,
            ], [
                'log_user_id' => $userIds[$itemIndex % count($userIds)],
                'log_description' => "Component status changed from '{$oldStatus}' → '{$newStatus}' (legacy â†’ '{$newStatus}'). " . DEMO_MARKER,
                'log_ip' => '127.0.0.1',
            ]);
        }
    }

    $auditCodes = [
        ['ASSET_UPDATED', 'Assets', 'Asset record updated'],
        ['TICKET_CREATED', 'Tickets', 'Maintenance ticket created'],
        ['MAINTENANCE_UPDATED', 'Maintenance', 'Maintenance progress recorded'],
        ['DISPOSAL_REVIEWED', 'Disposal Requests', 'Disposal request reviewed'],
        ['USER_LOGIN', 'Users', 'User signed in'],
        ['ROLE_ASSIGNED', 'Roles', 'Role assigned to user'],
        ['COMPONENT_UPDATED', 'Components', 'Component information updated'],
        ['REPORT_EXPORTED', 'Reports', 'Operational report exported'],
    ];
    for ($i = 1; $i <= 160; $i++) {
        [$code, $table, $description] = $auditCodes[$i % count($auditCodes)];
        $timestamp = datePlus('2026-05-01 07:30:00', '+' . ($i * 17) . ' hours', 'Y-m-d H:i:s');
        upsert($db, 'logs', 'log_id', [
            'log_code' => $code . '_DEMO_' . sprintf('%03d', $i),
        ], [
            'log_user_id' => $userIds[$i % count($userIds)],
            'log_item_table' => $table,
            'log_item_id' => $i,
            'log_description' => DEMO_MARKER . ' ' . $description . ' for linked demo record #' . $i . '.',
            'log_ip' => '127.0.0.1',
            'timestamp' => $timestamp,
        ]);
    }

    $db->commit();

    $summaryTables = [
        'equipments_asset', 'add_asset_items', 'ticket', 'equipment_maintenance_asset',
        'item_ticket', 'asset_disposal_requests', 'orders', 'users', 'workers',
        'asset_logs', 'logs', 'locations', 'branch_office', 'task', 'task_list',
    ];
    echo "Realistic demo data seeded successfully.\n\n";
    foreach ($summaryTables as $table) {
        printf("%-32s %6d rows\n", $table, scalar($db, 'SELECT COUNT(*) FROM ' . ident($table)));
    }
    echo "\nDemo accounts: 14\nDemo password: Demo@2026\n";
} catch (Throwable $exception) {
    $db->rollback();
    fwrite(STDERR, "Seeder failed and was rolled back: {$exception->getMessage()}\n");
    exit(1);
}
