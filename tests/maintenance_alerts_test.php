<?php
define('BASEPATH', __DIR__);
class CI_Controller {}
function site_url($path) { return 'http://localhost/assets_IT-usman/' . $path; }
require __DIR__ . '/../application/controllers/Maintenance_alerts.php';
function check($ok, $message) {
    if (!$ok) { throw new RuntimeException($message); }
    echo "PASS: $message\n";
}
class AlertUser {
    public $authenticated = true;
    public $allowed = true;
    public function logged_in() { return $this->authenticated; }
    public function has_perm($permission) { return $this->allowed; }
}
class AlertOutput {
    public $status;
    public $data;
    public function set_status_header($value) { $this->status = $value; return $this; }
    public function set_header($value) { return $this; }
    public function set_content_type($value) { return $this; }
    public function set_output($value) { $this->data = json_decode($value, true); return $this; }
}
class AlertDb {
    public function field_exists($field, $table) { return false; }
    public $rows = [];
    private $table;
    public function table_exists($table) { return true; }
    public function select($fields) { return $this; }
    public function from($table) { $this->table = $table; return $this; }
    public function join(...$args) { return $this; }
    public function where(...$args) { return $this; }
    public function where_in(...$args) { return $this; }
    public function get() { return new AlertRows($this->rows[$this->table] ?? []); }
}
class AlertRows {
    private $rows;
    public function __construct($rows) { $this->rows = $rows; }
    public function result() { return $this->rows; }
}
class AlertIds { public function id_encode($id) { return 'encoded+' . $id; } }
#[AllowDynamicProperties]
class TestAlerts extends Maintenance_alerts {}
$controller = new TestAlerts();
$controller->user_model = new AlertUser();
$controller->output = new AlertOutput();
$controller->db = new AlertDb();
$controller->steve = new AlertIds();
$controller->user_model->authenticated = false;
$controller->list();
check($controller->output->status === 401, 'Unauthenticated request rejected');
$controller->user_model->authenticated = true;
$controller->user_model->allowed = false;
$controller->list();
check($controller->output->status === 403, 'Missing detail permission rejected');
$controller->user_model->allowed = true;
function fixture($id, $date, $reminder = 7) {
    return (object) ['equipment_id' => $id, 'equipment_name' => 'Pump <test>', 'equipment_registration' => 'P-1',
        'serial_number' => 'S1', 'maintenance_reminder_day' => $reminder, 'asset_type_name' => 'Pump',
        'location_name' => 'Workshop', 'next_maintenance_date' => $date];
}
$controller->db->rows['equipments_asset ea'] = [
    fixture(1, date('Y-m-d', strtotime('-1 day'))),
    fixture(2, date('Y-m-d'), 0),
    fixture(3, date('Y-m-d', strtotime('+7 days'))),
    fixture(4, date('Y-m-d', strtotime('+8 days'))),
    fixture(5, '0000-00-00'), fixture(6, '2026-02-31'), fixture(7, '')
];
$controller->db->rows['add_asset_items ea'] = [fixture(9, date('Y-m-d'))];
$controller->list();
$data = $controller->output->data;
check($data['count'] === 4, 'Only valid dates within reminder window included');
check($data['alerts'][0]['type'] === 'overdue', 'Overdue alerts sort first');
check($data['alerts'][1]['days_text'] === 'Due today', 'Zero reminder includes due today');
$component = array_values(array_filter($data['alerts'], function ($row) { return $row['entity_type'] === 'component'; }))[0];
check(strpos($component['detail_url'], 'items/info?id=encoded%2B9') !== false, 'Component uses encoded component detail link');
check(strpos($data['alerts'][0]['detail_url'], 'assets/info?id=encoded%2B1') !== false, 'Asset uses encoded asset detail link');
$controller->db->rows['equipments_asset ea'] = array_map(function ($id) { return fixture($id, date('Y-m-d')); }, range(1, 225));
$controller->list();
check($controller->output->data['count'] === 226, 'Count and list include records beyond previous limits');
$controller->db->rows = [];
$controller->list();
check($controller->output->data['count'] === 0 && $controller->output->data['alerts'] === [], 'Empty result is valid');
$controller->db->rows['equipment_maintenance_asset m'] = array_map(function ($status) {
    return (object) ['record_id' => $status, 'entity_id' => 1, 'title' => 'Pump',
        'tag' => 'P1', 'location' => 'Store', 'record_date' => '2026-09-17',
        'final_status' => $status, 'remarks' => 'Inspect seal', 'maintenance_type' => 'Preventive'];
}, ['pending', 'in_progress', 'IN-MAINTENANCE', 'complete', 'cancelled']);
$controller->db->rows['maintenance_task_done'] = [(object) [
    'record_id' => 'pending', 'name' => 'Check seal', 'status' => '', 'remarks' => 'Replace if worn'
]];
$controller->list();
$pending = $controller->output->data['alerts'];
check(count($pending) === 3, 'Pending includes open statuses and excludes complete/cancelled');
$record = array_values(array_filter($pending, function ($row) { return $row['work_status'] === 'Pending'; }))[0];
check($record['maintenance_type'] === 'Preventive' && $record['remarks'] === 'Inspect seal',
    'Popup receives maintenance type and remarks');
check($record['tasks'][0]['name'] === 'Check seal' && $record['tasks'][0]['remarks'] === 'Replace if worn',
    'Tasks and remarks attach to their maintenance record');
