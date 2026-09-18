<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ai_insights extends CI_Controller
{
    private $apiUrl = 'http://localhost:8002/api/assets';

    public function __construct()
    {
        parent::__construct();
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {
        $viewData = [
            'query_url' => site_url('ai_insights/query'),
            'status_url' => site_url('ai_insights/status'),
            'embed' => $this->input->get('embed') === '1',
        ];

        if ($viewData['embed']) {
            return $this->load->view('ai-insights-embed', $viewData);
        }

        $this->load->view('header', [
            'title' => 'AI Insights',
            'title2' => 'AI Insights',
            'styles' => ['design/css/ai-insights.css?v=3'],
        ]);
        $this->load->view('ai-insights', $viewData);
        $this->load->view('footer', [
            'scripts' => [
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js',
                'design/js/ai-insights.js?v=3',
            ],
        ]);
    }

    public function status()
    {
        return $this->json($this->callAiBackend('GET', '/status'));
    }

    public function query()
    {
        $rawInput = file_get_contents('php://input');
        $request = json_decode($rawInput, true);
        if (!is_array($request)) {
            return $this->json(['error' => true, 'message' => 'Invalid JSON payload'], 422);
        }

        $message = trim((string) ($request['message'] ?? ''));
        if ($message === '') {
            return $this->json(['error' => true, 'message' => 'Message is required'], 422);
        }
        if (mb_strlen($message) > 600) {
            return $this->json(['error' => true, 'message' => 'Message is too long. Keep it under 600 characters.'], 422);
        }

        $dataSource = $this->detectDataSource($message);
        $payload = [
            'message' => $message,
            'data_source' => $dataSource,
            'data' => $this->buildInsightData($dataSource),
            'context' => [
                'user_id' => $this->user_model->current_user()->user_id ?? null,
                'generated_at' => date('c'),
            ],
        ];

        return $this->json($this->callAiBackend('POST', '/chat', $payload));
    }

    private function detectDataSource($message)
    {
        $text = strtolower($message);
        if (preg_match('/disposal|dispose|disposed|write[ -]?off|writeoff|scrap/', $text)) return 'disposals';
        if (preg_match('/ticket|fault|faulty|issue|breakdown/', $text)) return 'tickets';
        if (preg_match('/maintenance|preventive|corrective|pending|overdue|complete|repair/', $text)) return 'maintenance';
        if (preg_match('/component|item|items|part/', $text)) return 'components';
        if (preg_match('/type|category|asset type/', $text)) return 'asset_types';
        if (preg_match('/location|store|site|where|branch/', $text)) return 'locations';
        if (preg_match('/status|serviceable|unserviceable|available|store/', $text)) return 'assets';
        return 'overall';
    }

    private function buildInsightData($dataSource)
    {
        $data = [
            'summary' => $this->summaryMetrics(),
            'asset_status_counts' => $this->assetStatusCounts(),
            'asset_type_counts' => $this->assetTypeCounts(),
            'asset_location_counts' => $this->assetLocationCounts(),
            'component_status_counts' => $this->componentStatusCounts(),
            'maintenance_status_counts' => $this->maintenanceStatusCounts(),
            'maintenance_monthly_counts' => $this->maintenanceMonthlyCounts(),
            'maintenance_type_counts' => $this->maintenanceTypeCounts(),
            'ticket_fault_counts' => $this->ticketFaultCounts(),
            'disposal_status_counts' => $this->disposalStatusCounts(),
            'write_off_reason_counts' => $this->writeOffReasonCounts(),
            'top_maintenance_assets' => $this->topMaintenanceAssets(),
            'recent_records' => $this->recentRecords($dataSource),
        ];
        return $data;
    }

    private function summaryMetrics()
    {
        $totalAssets = $this->tableExists('equipments_asset') ? $this->db->count_all_results('equipments_asset') : 0;
        $totalComponents = $this->tableExists('add_asset_items') ? $this->db->count_all_results('add_asset_items') : 0;
        $totalMaintenance = $this->tableExists('equipment_maintenance_asset') ? $this->db->count_all_results('equipment_maintenance_asset') : 0;
        $totalTickets = $this->tableExists('ticket') ? $this->db->count_all_results('ticket') : 0;
        $totalDisposals = $this->tableExists('asset_disposals') ? $this->db->count_all_results('asset_disposals') : 0;
        $openMaintenance = 0;
        if ($this->tableExists('equipment_maintenance_asset')) {
            $openMaintenance += $this->db->where('LOWER(REPLACE(final_status, "_", " ")) IN ("pending", "in progress", "in maintenance")', null, false)->count_all_results('equipment_maintenance_asset');
        }
        if ($this->tableExists('logs_item_maintenance')) {
            $openMaintenance += $this->db->where('LOWER(REPLACE(final_status, "_", " ")) IN ("pending", "in progress", "in maintenance")', null, false)->count_all_results('logs_item_maintenance');
        }
        return [
            'total_assets' => (int) $totalAssets,
            'total_components' => (int) $totalComponents,
            'total_maintenance' => (int) $totalMaintenance,
            'open_maintenance' => (int) $openMaintenance,
            'total_tickets' => (int) $totalTickets,
            'total_disposals' => (int) $totalDisposals,
        ];
    }

    private function assetStatusCounts()
    {
        if (!$this->tableExists('equipments_asset')) return [];
        return $this->labelValueRows($this->db->select('COALESCE(NULLIF(equipment_status, ""), "Unknown") AS label, COUNT(*) AS value', false)
            ->from('equipments_asset')
            ->group_by('equipment_status')
            ->order_by('value', 'DESC')
            ->limit(12)
            ->get()->result_array());
    }

    private function assetTypeCounts()
    {
        if (!$this->tableExists('equipments_asset')) return [];
        if ($this->tableExists('asset_types')) {
            $this->db->select('COALESCE(asset_types.name, CONCAT("Type ", equipments_asset.equipment_type), "Unknown") AS label, COUNT(*) AS value', false);
            $this->db->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left');
        } else {
            $this->db->select('COALESCE(NULLIF(equipments_asset.equipment_type, ""), "Unknown") AS label, COUNT(*) AS value', false);
        }
        return $this->labelValueRows($this->db->from('equipments_asset')->group_by('label')->order_by('value', 'DESC')->limit(12)->get()->result_array());
    }

    private function assetLocationCounts()
    {
        if (!$this->tableExists('equipments_asset')) return [];
        $locationParts = [];
        $this->db->from('equipments_asset');
        if ($this->tableExists('store_location') && $this->db->field_exists('store_location_id', 'equipments_asset')) {
            $this->db->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left');
            if ($this->db->field_exists('name', 'store_location')) $locationParts[] = 'store_location.name';
        }
        if ($this->tableExists('locations') && $this->db->field_exists('location_id', 'equipments_asset')) {
            $this->db->join('locations', 'locations.id = equipments_asset.location_id', 'left');
            if ($this->db->field_exists('location_name', 'locations')) $locationParts[] = 'locations.location_name';
            if ($this->db->field_exists('name', 'locations')) $locationParts[] = 'locations.name';
        }
        $labelExpr = $locationParts ? 'COALESCE(' . implode(', ', $locationParts) . ', "No location")' : '"No location"';
        $this->db->select($labelExpr . ' AS label, COUNT(DISTINCT equipments_asset.equipment_id) AS value', false);
        return $this->labelValueRows($this->db->group_by('label')->order_by('value', 'DESC')->limit(12)->get()->result_array());
    }

    private function componentStatusCounts()
    {
        if (!$this->tableExists('add_asset_items')) return [];
        $this->db->from('add_asset_items');
        if ($this->tableExists('item_status')) {
            $this->db->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left');
            $this->db->select('COALESCE(item_status.name, "Unknown") AS label, COUNT(*) AS value', false);
        } elseif ($this->db->field_exists('item_status_id', 'add_asset_items')) {
            $this->db->select('COALESCE(NULLIF(add_asset_items.item_status_id, ""), "Unknown") AS label, COUNT(*) AS value', false);
        } else {
            $this->db->select('"Unknown" AS label, COUNT(*) AS value', false);
        }
        return $this->labelValueRows($this->db->group_by('label')->order_by('value', 'DESC')->limit(12)->get()->result_array());
    }

    private function maintenanceStatusCounts()
    {
        $rows = [];
        if ($this->tableExists('equipment_maintenance_asset')) {
            $rows = array_merge($rows, $this->db->select('COALESCE(NULLIF(final_status, ""), "Unknown") AS label, COUNT(*) AS value', false)
                ->from('equipment_maintenance_asset')
                ->group_by('final_status')
                ->get()->result_array());
        }
        if ($this->tableExists('logs_item_maintenance')) {
            $rows = array_merge($rows, $this->db->select('COALESCE(NULLIF(final_status, ""), "Unknown") AS label, COUNT(*) AS value', false)
                ->from('logs_item_maintenance')
                ->group_by('final_status')
                ->get()->result_array());
        }
        return $this->combineLabelRows($rows);
    }

    private function maintenanceMonthlyCounts()
    {
        if (!$this->tableExists('equipment_maintenance_asset')) return [];
        $dateExpr = $this->maintenanceDateExpression();
        if ($dateExpr === '') return [];
        return $this->labelValueRows($this->db->select('DATE_FORMAT(' . $dateExpr . ', "%Y-%m") AS label, COUNT(*) AS value', false)
            ->from('equipment_maintenance_asset')
            ->where($dateExpr . ' IS NOT NULL', null, false)
            ->group_by('label')
            ->order_by('label', 'ASC')
            ->limit(12)
            ->get()->result_array());
    }

    private function maintenanceTypeCounts()
    {
        if (!$this->tableExists('equipment_maintenance_asset')) return [];
        $this->db->from('equipment_maintenance_asset');
        if ($this->tableExists('maintenance_type_color_code')) {
            $this->db->join('maintenance_type_color_code', 'maintenance_type_color_code.id = equipment_maintenance_asset.maintenance_type_id', 'left');
            $this->db->select('COALESCE(maintenance_type_color_code.maintenance_type, equipment_maintenance_asset.maintenance_type_id, "Unknown") AS label, COUNT(*) AS value', false);
        } else {
            $this->db->select('COALESCE(NULLIF(equipment_maintenance_asset.maintenance_type_id, ""), "Unknown") AS label, COUNT(*) AS value', false);
        }
        return $this->labelValueRows($this->db->group_by('label')->order_by('value', 'DESC')->limit(12)->get()->result_array());
    }

    private function ticketFaultCounts()
    {
        $rows = [];
        if ($this->tableExists('ticket')) {
            $field = $this->firstExistingField('ticket', ['ticket_fault_type', 'fault_type', 'faulty_type', 'ticket_status']);
            if ($field !== '') {
                $rows = array_merge($rows, $this->countByField('ticket', $field));
            }
        }
        if ($this->tableExists('item_ticket')) {
            $field = $this->firstExistingField('item_ticket', ['fault_type', 'faulty_type', 'ticket_status']);
            if ($field !== '') {
                $rows = array_merge($rows, $this->countByField('item_ticket', $field));
            }
        }
        return $this->combineLabelRows($rows);
    }

    private function disposalStatusCounts()
    {
        $rows = [];
        foreach (['asset_disposals', 'asset_disposal_requests'] as $table) {
            if (!$this->tableExists($table)) continue;
            $field = $this->firstExistingField($table, ['status', 'approval_status', 'request_status', 'disposal_status']);
            if ($field !== '') {
                $rows = array_merge($rows, $this->countByField($table, $field));
            }
        }
        return $this->combineLabelRows($rows);
    }

    private function writeOffReasonCounts()
    {
        if (!$this->tableExists('asset_disposal_requests')) return [];
        $this->db->from('asset_disposal_requests');
        if ($this->tableExists('write_off_reasons') && $this->db->field_exists('write_off_reason_id', 'asset_disposal_requests')) {
            $this->db->join('write_off_reasons', 'write_off_reasons.id = asset_disposal_requests.write_off_reason_id', 'left');
            $this->db->select('COALESCE(write_off_reasons.write_off_reason, "Unknown") AS label, COUNT(*) AS value', false);
        } elseif ($this->db->field_exists('write_off_reason_id', 'asset_disposal_requests')) {
            $this->db->select('COALESCE(NULLIF(write_off_reason_id, ""), "Unknown") AS label, COUNT(*) AS value', false);
        } else {
            return [];
        }
        return $this->labelValueRows($this->db->group_by('label')->order_by('value', 'DESC')->limit(12)->get()->result_array());
    }

    private function topMaintenanceAssets()
    {
        if (!$this->tableExists('equipment_maintenance_asset')) return [];
        $this->db->from('equipment_maintenance_asset');
        if ($this->tableExists('equipments_asset')) {
            $this->db->join('equipments_asset', 'equipments_asset.equipment_id = equipment_maintenance_asset.equipment_id', 'left');
            $this->db->select('COALESCE(equipments_asset.equipment_name, CONCAT("Asset ", equipment_maintenance_asset.equipment_id)) AS label, COUNT(*) AS value', false);
        } else {
            $this->db->select('COALESCE(NULLIF(equipment_id, ""), "Unknown") AS label, COUNT(*) AS value', false);
        }
        return $this->labelValueRows($this->db->group_by('label')->order_by('value', 'DESC')->limit(10)->get()->result_array());
    }

    private function recentRecords($dataSource)
    {
        if ($dataSource === 'maintenance' && $this->tableExists('equipment_maintenance_asset')) {
            $dateExpr = $this->maintenanceDateExpression();
            $dateSelect = $dateExpr !== '' ? $dateExpr . ' AS date' : 'NULL AS date';
            return $this->db->select('equipment_maintenance_id AS id, equipment_id, final_status, maintenance_notes AS remarks, ' . $dateSelect, false)
                ->from('equipment_maintenance_asset')
                ->order_by('date', 'DESC')
                ->limit(15)
                ->get()->result_array();
        }
        if ($dataSource === 'components' && $this->tableExists('add_asset_items')) {
            return $this->db->select('id, asset_id, item_name, serial_number')
                ->from('add_asset_items')
                ->order_by('id', 'DESC')
                ->limit(15)
                ->get()->result_array();
        }
        if ($dataSource === 'tickets' && $this->tableExists('ticket')) {
            $fields = $this->selectExistingFields('ticket', ['ticket_id', 'ticket_number', 'equipment_id', 'ticket_fault_type', 'fault_type', 'ticket_location', 'created_at']);
            return $this->db->select($fields ?: '*')->from('ticket')->order_by($fields && in_array('ticket_id', $fields, true) ? 'ticket_id' : '1', 'DESC')->limit(15)->get()->result_array();
        }
        if ($dataSource === 'disposals' && $this->tableExists('asset_disposal_requests')) {
            $fields = $this->selectExistingFields('asset_disposal_requests', ['id', 'asset_id', 'equipment_id', 'status', 'approval_status', 'write_off_reason_id', 'created_at']);
            return $this->db->select($fields ?: '*')->from('asset_disposal_requests')->order_by($fields && in_array('id', $fields, true) ? 'id' : '1', 'DESC')->limit(15)->get()->result_array();
        }
        if ($this->tableExists('equipments_asset')) {
            return $this->db->select('equipment_id, equipment_name, equipment_registration, equipment_status, serial_number')
                ->from('equipments_asset')
                ->order_by('equipment_id', 'DESC')
                ->limit(15)
                ->get()->result_array();
        }
        return [];
    }

    private function maintenanceDateExpression()
    {
        $fields = [];
        foreach (['update_date', 'maintenance_date', 'created_at'] as $field) {
            if ($this->db->field_exists($field, 'equipment_maintenance_asset')) {
                $fields[] = $field;
            }
        }
        if (!$fields) return '';
        return count($fields) === 1 ? $fields[0] : 'COALESCE(' . implode(', ', $fields) . ')';
    }

    private function countByField($table, $field)
    {
        return $this->labelValueRows($this->db->select('COALESCE(NULLIF(' . $field . ', ""), "Unknown") AS label, COUNT(*) AS value', false)
            ->from($table)
            ->group_by($field)
            ->order_by('value', 'DESC')
            ->limit(12)
            ->get()->result_array());
    }

    private function firstExistingField($table, $fields)
    {
        foreach ($fields as $field) {
            if ($this->db->field_exists($field, $table)) return $field;
        }
        return '';
    }

    private function selectExistingFields($table, $fields)
    {
        $out = [];
        foreach ($fields as $field) {
            if ($this->db->field_exists($field, $table)) $out[] = $field;
        }
        return $out;
    }

    private function callAiBackend($method, $path, $payload = null)
    {
        $ch = curl_init($this->apiUrl . $path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 35);
        if ($method === 'POST') {
            $json = json_encode($payload);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Content-Length: ' . strlen($json)]);
        }
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        curl_close($ch);
        if ($error) {
            return ['error' => true, 'message' => 'Failed to connect to AI backend: ' . $error];
        }
        $data = json_decode($response, true);
        if ($httpCode < 200 || $httpCode >= 300 || !is_array($data)) {
            return ['error' => true, 'message' => 'AI backend error', 'details' => $response, 'http_code' => $httpCode];
        }
        if (isset($data['response']) && is_string($data['response'])) {
            $data['response'] = mb_substr($data['response'], 0, 4000);
        }
        if (isset($data['chart']) && is_array($data['chart'])) {
            $data['chart'] = $this->sanitizeChart($data['chart']);
        }
        return $data;
    }

    private function sanitizeChart($chart)
    {
        $allowedTypes = ['bar', 'line', 'doughnut', 'pie', 'table'];
        $type = in_array(($chart['type'] ?? ''), $allowedTypes, true) ? $chart['type'] : 'bar';
        $out = [
            'type' => $type,
            'title' => mb_substr((string) ($chart['title'] ?? 'AI Graph'), 0, 120),
        ];
        if ($type === 'table') {
            $columns = array_slice(array_map('strval', $chart['columns'] ?? []), 0, 8);
            $rows = array_slice(is_array($chart['rows'] ?? null) ? $chart['rows'] : [], 0, 25);
            $out['columns'] = $columns;
            $out['rows'] = array_map(function ($row) use ($columns) {
                $clean = [];
                foreach ($columns as $column) {
                    $clean[$column] = mb_substr((string) ($row[$column] ?? ''), 0, 180);
                }
                return $clean;
            }, $rows);
            return $out;
        }
        $out['labels'] = array_slice(array_map(function ($label) {
            return mb_substr((string) $label, 0, 80);
        }, $chart['labels'] ?? []), 0, 20);
        $datasets = is_array($chart['datasets'] ?? null) ? array_slice($chart['datasets'], 0, 4) : [];
        $out['datasets'] = array_map(function ($dataset) {
            return [
                'label' => mb_substr((string) ($dataset['label'] ?? 'Value'), 0, 80),
                'data' => array_slice(array_map('intval', $dataset['data'] ?? []), 0, 20),
            ];
        }, $datasets);
        return $out;
    }

    private function tableExists($table)
    {
        return $this->db->table_exists($table);
    }

    private function labelValueRows($rows)
    {
        return array_map(function ($row) {
            return ['label' => (string) ($row['label'] ?? 'Unknown'), 'value' => (int) ($row['value'] ?? 0)];
        }, $rows ?: []);
    }

    private function combineLabelRows($rows)
    {
        $combined = [];
        foreach ($rows as $row) {
            $label = trim((string) ($row['label'] ?? 'Unknown')) ?: 'Unknown';
            $combined[$label] = ($combined[$label] ?? 0) + (int) ($row['value'] ?? 0);
        }
        arsort($combined);
        $out = [];
        foreach ($combined as $label => $value) {
            $out[] = ['label' => $label, 'value' => $value];
        }
        return array_slice($out, 0, 12);
    }

    private function json($data, $status = 200)
    {
        return $this->output->set_status_header($status)->set_content_type('application/json')->set_output(json_encode($data));
    }
}

