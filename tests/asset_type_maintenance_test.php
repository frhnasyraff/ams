<?php
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Asset_type_maintenance.php';

set_error_handler(function ($severity, $message) { throw new ErrorException($message, 0, $severity); });
function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
    echo "PASS: $message\n";
}
function rejects($call, $class, $code) {
    try { $call(); } catch (Throwable $e) {
        check($e instanceof $class && $e->getCode() === $code, "$class ($code)");
        return;
    }
    throw new RuntimeException('Expected failure did not occur');
}
class MaintenanceFakeResult {
    private $rows;
    public function __construct($rows) { $this->rows = array_map(function ($row) { return (object) $row; }, $rows); }
    public function row() { return $this->rows[0] ?? null; }
    public function result() { return $this->rows; }
}
class MaintenanceFakeDb {
    public $tables;
    public $missingMaintenance = false;
    private $filter = [];
    public function __construct($tables) { $this->tables = $tables; }
    public function table_exists($table) { return isset($this->tables[$table]); }
    public function field_exists($field, $table) { return !($table === 'asset_types' && $field === 'maintenance' && $this->missingMaintenance); }
    public function select($fields) { return $this; }
    public function where_in($field, $ids) { $this->filter = [$field, $ids]; return $this; }
    public function get($table) {
        $rows = $this->tables[$table];
        if ($this->filter) {
            [$field, $ids] = $this->filter;
            $rows = array_values(array_filter($rows, function ($row) use ($field, $ids) { return in_array($row[$field], $ids); }));
        }
        $this->filter = [];
        return new MaintenanceFakeResult($rows);
    }
    public function get_where($table, $where) {
        return new MaintenanceFakeResult(array_values(array_filter($this->tables[$table], function ($row) use ($where) {
            foreach ($where as $key => $value) { if ($row[$key] != $value) { return false; } }
            return true;
        })));
    }
}
$db = new MaintenanceFakeDb(['asset_types' => [
    ['asset_id' => 1, 'maintenance' => 1, 'maintenance_frequency_year' => 4, 'maintenance_reminder_days' => 0],
    ['asset_id' => 2, 'maintenance' => 0],
    ['asset_id' => 3, 'maintenance' => null],
]]);
$service = new Asset_type_maintenance(['db' => $db]);
check($service->lookup(1)['maintenance'] === 1, 'Settings remain available without component tables');
check(count($service->lookup(1)['warnings']) === 1, 'Missing optional tables are reported');
check($service->lookup(1)['maintenance_reminder_days'] === 0, 'Zero reminder survives lookup');
check($service->lookup(2)['maintenance'] === 0, 'Disabled type is distinct from unconfigured');
check($service->lookup(3)['maintenance'] === null, 'Unconfigured type remains unknown');
rejects(function () use ($service) { $service->lookup(0); }, InvalidArgumentException::class, 0);
rejects(function () use ($service) { $service->lookup('1 OR 1=1'); }, InvalidArgumentException::class, 0);
rejects(function () use ($service) { $service->lookup(99); }, RuntimeException::class, 404);
$db->missingMaintenance = true;
rejects(function () use ($service) { $service->lookup(1); }, RuntimeException::class, 503);
$db->missingMaintenance = false;
$db->tables['asset_type_items'] = [['asset_type_id' => 1, 'item_type_id' => 10, 'quantity' => 2]];
$db->tables['item_types'] = [['id' => 10, 'manufacturer' => 8, 'vendor_part_number' => 9, 'maintenance' => 1]];
$item = $service->lookup(1)['items'][0];
check($item['qty'] === 2 && $item['manufacturer'] === null, 'Component metadata works without vendor lookup tables');
$db->tables['vendor_manufacturing_number'] = [['id' => 8, 'manufacturer_name' => 'Example']];
$db->tables['vendor_part_number'] = [['id' => 9, 'part_number' => 'PART-9']];
$item = $service->lookup(1)['items'][0];
check($item['manufacturer'] === 'Example' && $item['vendor_part_number'] === 'PART-9', 'Component lookup contract preserved');
check(Asset_type_maintenance::can_lookup(false, true, false, true), 'Asset editor can read settings without master access');
check(!Asset_type_maintenance::can_lookup(false, true, false, false), 'List-only user cannot read edit metadata');
check(!Asset_type_maintenance::can_lookup(false, false, false, false), 'No access remains denied');
check(Asset_type_maintenance::can_lookup(true, false, false, false), 'Master access remains supported');
check(Asset_type_maintenance::defaults('', null) === ['maintenance_frequency_year' => null, 'maintenance_reminder_days' => null], 'Defaults are optional');
check(Asset_type_maintenance::defaults('2', '0') === ['maintenance_frequency_year' => 2, 'maintenance_reminder_days' => 0], 'Frequency and zero reminder validated');
foreach ([['0', '1'], ['1.5', '1'], ['366', '1'], ['2', '-1'], ['2', '3651']] as $invalid) {
    rejects(function () use ($invalid) { Asset_type_maintenance::defaults(...$invalid); }, InvalidArgumentException::class, 0);
}
echo "All maintenance service tests passed.\n";
