<?php
// php tests/asset_status_test.php -- no database writes.
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/helpers/asset_status_helper.php';
$cases = [
    ['In use', 'SERVICEABLE'], [' Inuse ', 'SERVICEABLE'],
    ['Standby', 'AVAILABLE'], ['available', 'AVAILABLE'],
    ['Maintenance', 'MAINTENANCE'], ['Store', 'STORE'], ['in store', 'STORE'],
    ['Repair', 'UNSERVICEABLE'], ['faulty', 'UNSERVICEABLE'],
    [null, 'UNSERVICEABLE'], ['', 'UNSERVICEABLE'], ['unknown', 'UNSERVICEABLE'],
];
foreach (ams_asset_status_names() as $status) $cases[] = [$status, $status];
$sql = file_get_contents(__DIR__ . '/../rams_DB/patch_asset_statuses.sql');
preg_match_all("/WHEN '([^']+)' THEN '([^']+)'/", $sql, $sqlCases, PREG_SET_ORDER);
$sqlMap = [];
foreach ($sqlCases as $case) $sqlMap[$case[1]] = $case[2];
foreach ($cases as list($input, $expected)) {
    $actual = ams_normalize_asset_status($input);
    if ($actual !== $expected || !in_array($actual, ams_asset_status_names(), true)) throw new RuntimeException('Wrong mapping');
    if (ams_normalize_asset_status($actual) !== $actual) throw new RuntimeException('Not idempotent');
    if (($sqlMap[strtoupper(trim((string) $input))] ?? 'UNSERVICEABLE') !== $actual) throw new RuntimeException('SQL/PHP mapping mismatch');
}
echo 'PASS: ' . count($cases) . " status mappings, idempotence and SQL/PHP consistency\n";