<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Log_viewer extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("view_logs")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function ajax_list()
    {
        $joins = [
            ["users", "users.user_id = logs.log_user_id", "left"]
        ];

        $select = "logs.*, users.full_name, users.username";
        $conditions = [];

        $module = trim((string) $this->input->post('module_filter'));
        $activity = trim((string) $this->input->post('activity_filter'));
        $period = trim((string) $this->input->post('period_filter'));

        if ($module !== '') {
            $conditions[] = ['logs.log_item_table', $module];
        }

        if ($activity !== '') {
            $conditions[] = ['logs.log_code', $activity];
        }

        if ($period === 'today') {
            $conditions[] = ['logs.timestamp >=', date('Y-m-d 00:00:00')];
        } elseif ($period === '7days') {
            $conditions[] = ['logs.timestamp >=', date('Y-m-d H:i:s', strtotime('-7 days'))];
        } elseif ($period === '30days') {
            $conditions[] = ['logs.timestamp >=', date('Y-m-d H:i:s', strtotime('-30 days'))];
        }

        $searchable = [
            "logs.timestamp",
            "logs.log_item_table",
            "logs.log_code",
            "logs.log_description",
            "logs.log_ip",
            "users.full_name",
            "users.username"
        ];

        $response = json_decode($this->steve->datatables_mysql("logs", $searchable, $conditions, $joins, $select), true);
        $response['summary'] = $this->summary();

        exit(json_encode($response));
    }

    public function index()
    {
        $modules = $this->db
            ->select('log_item_table')
            ->distinct()
            ->order_by('log_item_table', 'asc')
            ->get('logs')
            ->result();

        $activities = $this->db
            ->select('log_code')
            ->distinct()
            ->order_by('log_code', 'asc')
            ->get('logs')
            ->result();

        $this->load->view('header', ['title' => "System Audit Log", 'title2' => "System Audit Log", "styles" => []]);
        $this->load->view('logs', [
            'summary' => $this->summary(),
            'modules' => $modules,
            'activities' => $activities
        ]);
        $this->load->view('footer', ['scripts' => ['design/js/logs-list.js?2']]);
    }

    private function summary()
    {
        $userCount = $this->db
            ->select('COUNT(DISTINCT log_user_id) AS total', false)
            ->get('logs')
            ->row();
        $moduleCount = $this->db
            ->select('COUNT(DISTINCT log_item_table) AS total', false)
            ->get('logs')
            ->row();

        return [
            'total' => intval($this->db->count_all('logs')),
            'today' => intval($this->db->where('timestamp >=', date('Y-m-d 00:00:00'))->count_all_results('logs')),
            'users' => intval($userCount->total ?? 0),
            'modules' => intval($moduleCount->total ?? 0)
        ];
    }
}
