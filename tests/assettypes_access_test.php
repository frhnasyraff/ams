<?php
// Controller guard regression tests: no real session, database or permission grants.
define('BASEPATH', __DIR__);
class AccessTestUser {
    public function logged_in() { return $GLOBALS['case'] !== 'signed_out'; }
    public function has_perm($permission) {
        $allowed = ['list' => ['list_assettypes'], 'edit' => ['list_assettypes','edit_assettypes']];
        return in_array($permission, $allowed[$GLOBALS['case']] ?? [], true);
    }
}
class AccessTestRouter { public function fetch_method() { return $GLOBALS['case'] === 'api_denied' ? 'ajax_list' : 'index'; } }
class AccessTestLoader {
    public function library($name) {}
    public function view($name, $data = []) { $GLOBALS['views'][] = $name; }
}
class AccessTestOutput {
    private $status = 200; private $body = '';
    public function set_status_header($status) { $this->status = $status; return $this; }
    public function set_content_type($type) { return $this; }
    public function set_header($header) { return $this; }
    public function set_output($body) { $this->body = $body; return $this; }
    public function _display() { echo json_encode(['status' => $this->status, 'body' => $this->body, 'views' => $GLOBALS['views'] ?? []]); }
}
class CI_Controller {
    public $user_model; public $router; public $load; public $output;
    public function __construct() { $this->user_model = new AccessTestUser(); $this->router = new AccessTestRouter(); $this->load = new AccessTestLoader(); $this->output = new AccessTestOutput(); }
}
function redirect($url) { echo json_encode(['redirect' => $url]); }
if (isset($argv[1])) {
    $GLOBALS['case'] = $argv[1];
    require __DIR__ . '/../application/controllers/Assettypes.php';
    new Assettypes();
    echo json_encode(['status' => 200, 'views' => $GLOBALS['views'] ?? []]);
    exit;
}
foreach (['denied','api_denied','signed_out','list','edit'] as $case) {
    $response = shell_exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg(__FILE__) . ' ' . escapeshellarg($case));
    $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
    if ($case === 'denied') {
        $ok = $data['status'] === 403 && $data['views'] === ['header','assettypes-access','footer'];
    } elseif ($case === 'api_denied') {
        $ok = $data['status'] === 403 && json_decode($data['body'], true)['status'] === false && !$data['views'];
    } elseif ($case === 'signed_out') {
        $ok = isset($data['redirect']) && !str_contains($data['redirect'], 'order_summary');
    } else { $ok = $data['status'] === 200 && !$data['views']; }
    if (!$ok) { throw new RuntimeException("Failed access case: $case"); }
    echo "PASS: $case\n";
}
echo "All Asset Types access guards passed.\n";
