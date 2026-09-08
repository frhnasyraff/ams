<?php
// Run: php tests/asset_rfid_binding_test.php (no server/database writes).
define('BASEPATH', __DIR__);
require __DIR__ . '/../application/libraries/Asset_rfid_binding.php';
class BindingFakeDb {
    public $database = 'test';
    public $rows = [];
    public $writes = 0;
    public $released = false;
    public $lockGranted = true;
    public $failUpdate = false;
    public $throwUpdate = false;
    private $conditions = [];
    public function query($sql, $params) {
        if (strpos($sql, 'RELEASE_LOCK') !== false) $this->released = true;
        return new BindingFakeResult((object) ['acquired' => $this->lockGranted ? 1 : 0]);
    }
    public function select($columns) { return $this; }
    public function from($table) { return $this; }
    public function escape($value) { return "'" . str_replace("'", "''", $value) . "'"; }
    public function where($key, $value = null, $escape = true) { $this->conditions[] = [$key, $value]; return $this; }
    public function get() {
        $match = null;
        foreach ($this->rows as $row) {
            $matches = true;
            foreach ($this->conditions as list($key, $value)) {
                if ($key === 'equipment_id' && (string)$row->equipment_id !== (string)$value) $matches = false;
                if ($key === 'equipment_id !=' && (string)$row->equipment_id === (string)$value) $matches = false;
                if (strpos($key, 'UPPER(TRIM(rfid)) = ') === 0 && 'UPPER(TRIM(rfid)) = ' . $this->escape(strtoupper(trim((string)$row->rfid))) !== $key) $matches = false;
            }
            if ($matches) { $match = clone $row; break; }
        }
        $this->conditions = [];
        return new BindingFakeResult($match);
    }
    public function update($table, $values) {
        $id = $this->conditions[0][1]; $this->conditions = [];
        if ($this->throwUpdate) throw new RuntimeException('simulated DB failure');
        if ($this->failUpdate) return false;
        $this->rows[$id]->rfid = $values['rfid']; $this->writes++; return true;
    }
}
class BindingFakeResult {
    private $value;
    public function __construct($value) { $this->value = $value; }
    public function row() { return $this->value; }
}
function fixture() {
    $db = new BindingFakeDb;
    $db->rows = [1 => (object)['equipment_id'=>1,'rfid'=>null], 2 => (object)['equipment_id'=>2,'rfid'=>' OLD123 ']];
    return $db;
}
function check($ok, $message) { if (!$ok) throw new RuntimeException($message); }
$db = fixture(); $service = new Asset_rfid_binding($db);
check($service->bind('1',' old123 ')[0] === 409, 'existing tag must be rejected case/space-insensitively');
check($db->writes === 0 && $db->released, 'duplicate must not write, lock must release');
$db = fixture(); $service = new Asset_rfid_binding($db);
check($service->bind('1','new123')[0] === 200 && $db->rows[1]->rfid === 'NEW123', 'new tag normalized and saved');
check($service->bind('1',' new123 ')[0] === 200 && $db->writes === 1, 'same binding retry must be idempotent');
check($service->bind('1','different')[0] === 409 && $db->writes === 1, 'no silent replacement');
check($service->bind('99','new')[0] === 404, 'missing asset');
check($service->bind('1',' ')[0] === 400, 'empty tag rejected');
$db = fixture(); $db->lockGranted = false;
check((new Asset_rfid_binding($db))->bind('1','new')[0] === 503 && $db->writes === 0, 'lock failure must fail closed');
$db = fixture(); $db->failUpdate = true;
check((new Asset_rfid_binding($db))->bind('1','new')[0] === 500 && $db->released, 'write failure must not report success');
$db = fixture(); $db->throwUpdate = true;
try { (new Asset_rfid_binding($db))->bind('1','new'); throw new LogicException('expected DB failure'); }
catch (RuntimeException $e) { check($db->released, 'lock must release on exception'); }
echo "PASS: duplicate, normalization, idempotent retry, replacement, missing asset, validation, lock contention, failed write, exception release\n";