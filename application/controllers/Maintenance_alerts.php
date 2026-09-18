<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Maintenance_alerts extends CI_Controller
{
    public function index()
    {
        $this->list();
    }

    public function list()
    {
        if (!$this->user_model->logged_in()) {
            return $this->json([
                'success' => false,
                'count' => 0,
                'alerts' => [],
                'message' => 'Unauthorized',
            ], 401);
        }

        if (!$this->user_model->has_perm('list_equipments') || !$this->user_model->has_perm('edit_equipments')) {
            return $this->json(['success' => false, 'count' => 0, 'alerts' => [], 'message' => 'Forbidden'], 403);
        }

        $alerts = [];

        if ($this->db->table_exists('equipments_asset') && $this->db->table_exists('next_maintenance_date')) {
            $alerts = array_merge($alerts, $this->upcomingPreventiveAlerts());
        }
        if ($this->db->table_exists('add_asset_items')) {
            $alerts = array_merge($alerts, $this->upcomingPreventiveAlerts(true));
        }

        $alerts = array_merge($alerts, $this->pendingAlerts());

        usort($alerts, function ($a, $b) {
            if ($a['sort_weight'] === $b['sort_weight']) {
                return strcmp((string) $a['due_date'], (string) $b['due_date']);
            }
            return $a['sort_weight'] <=> $b['sort_weight'];
        });

        $this->json([
            'success' => true,
            'count' => count($alerts),
            'alerts' => $alerts,
        ]);
    }

    private function upcomingPreventiveAlerts($component = false)
    {
        $today = new DateTime('today');
        if ($component) {
            // Older component schemas have no serial or advance-reminder setting.
            $serial = $this->db->field_exists('serial_number', 'add_asset_items')
                ? 'ea.serial_number' : "''";
            $reminder = $this->db->field_exists('maintenance_reminder_day', 'add_asset_items')
                ? 'ea.maintenance_reminder_day' : '0';
            $this->db->select('ea.id AS equipment_id, ea.item_name AS equipment_name,
                ' . $serial . ' AS equipment_registration, ' . $serial . ' AS serial_number,
                ' . $reminder . ' AS maintenance_reminder_day, sl.name AS location_name,
                ea.maintenance_date AS next_maintenance_date')
                ->from('add_asset_items ea')
                ->join('store_location sl', 'sl.id = ea.store_location_id', 'left')
                ->where('ea.maintenance_date IS NOT NULL', null, false);
        } else {
            $this->db->select('
                ea.equipment_id,
                ea.equipment_name,
                ea.equipment_registration,
                ea.serial_number,
                ea.maintenance_reminder_day,
                at.name AS asset_type_name,
                sl.name AS location_name,
                nmd.maintenance_date AS next_maintenance_date
            ')
            ->from('equipments_asset ea')
            ->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left')
            ->join('store_location sl', 'sl.id = ea.store_location_id', 'left')
            ->join('next_maintenance_date nmd', 'nmd.equipment_id = ea.equipment_id', 'left')
            ->where('nmd.maintenance_date IS NOT NULL', null, false)
            ->where('ea.maintenance_reminder_day IS NOT NULL', null, false);
        }
        $rows = $this->db
            ->get()
            ->result();

        $alerts = [];

        foreach ($rows as $row) {
            $date = substr((string) $row->next_maintenance_date, 0, 10);
            $dueDate = DateTime::createFromFormat('!Y-m-d', $date);
            if (!$dueDate || $dueDate->format('Y-m-d') !== $date || $date === '0000-00-00') {
                continue;
            }

            $reminderDays = max(0, (int) $row->maintenance_reminder_day);
            $reminderDate = (clone $dueDate)->modify('-' . $reminderDays . ' days');

            if ($today < $reminderDate) {
                continue;
            }

            $daysDiff = (int) $today->diff($dueDate)->format('%r%a');
            $isOverdue = $dueDate < $today;
            $assetName = trim((string) $row->equipment_name) ?: 'Unnamed asset';
            $assetTag = trim((string) $row->equipment_registration) ?: trim((string) $row->serial_number) ?: (string) $row->equipment_id;
            $encodedId = $this->steve->id_encode($row->equipment_id);

            $alerts[] = [
                'id' => ($component ? 'component' : 'asset') . '-maintenance-' . $row->equipment_id,
                'entity_type' => $component ? 'component' : 'asset',
                'maintenance_type' => 'Preventive',
                'work_status' => 'Scheduled',
                'remarks' => '',
                'tasks' => [],
                'type' => $isOverdue ? 'overdue' : 'due_soon',
                'status_label' => $isOverdue ? 'Overdue' : 'Due soon',
                'title' => $assetName,
                'subtitle' => $assetTag,
                'asset_type' => $component ? 'Component' : ($row->asset_type_name ?: 'Asset'),
                'location' => $row->location_name ?: 'No location',
                'due_date' => $dueDate->format('Y-m-d'),
                'days_text' => $this->formatDaysText($daysDiff),
                'detail_url' => site_url(($component ? 'items' : 'assets') . '/info?id=' . rawurlencode($encodedId)),
                'sort_weight' => $isOverdue ? 0 : 1,
            ];
        }

        return $alerts;
    }

    private function pendingAlerts()
    {
        $alerts = [];
        foreach ([false, true] as $component) {
            $table = $component ? 'logs_item_maintenance' : 'equipment_maintenance_asset';
            if (!$this->db->table_exists($table)) {
                continue;
            }
            if ($component) {
                $rows = $this->db->select('m.id AS record_id, i.id AS entity_id,
                    i.item_name AS title, sl.name AS location, m.update_date AS record_date,
                    m.final_status, m.notes AS remarks')
                    ->from('logs_item_maintenance m')
                    ->join('item_ticket ticket', 'ticket.id = m.item_ticket_id')
                    ->join('add_asset_items i', 'i.id = ticket.item_id')
                    ->join('store_location sl', 'sl.id = i.store_location_id', 'left')
                    ->where('NOT EXISTS (SELECT 1 FROM logs_item_maintenance newer
                        WHERE newer.item_ticket_id = m.item_ticket_id AND newer.active = 1
                        AND (newer.update_date > m.update_date OR
                        (newer.update_date = m.update_date AND newer.id > m.id)))', null, false)
                    ->where('m.active', 1)->get()->result();
            } else {
                $rows = $this->db->select('m.equipment_maintenance_id AS record_id,
                    ea.equipment_id AS entity_id, ea.equipment_name AS title,
                    ea.equipment_registration AS tag, sl.name AS location,
                    COALESCE(m.update_date, m.maintenance_date) AS record_date,
                    m.final_status, m.maintenance_notes AS remarks, mt.maintenance_type')
                    ->from('equipment_maintenance_asset m')
                    ->join('equipments_asset ea', 'ea.equipment_id = m.equipment_id')
                    ->join('store_location sl', 'sl.id = ea.store_location_id', 'left')
                    ->join('maintenance_type_color_code mt', 'mt.id = m.maintenance_type_id', 'left')
                    ->get()->result();
            }
            $openRows = array_filter($rows, function ($row) {
                $status = strtolower(str_replace(['_', '-'], ' ', trim($row->final_status)));
                return in_array($status, ['pending', 'in progress', 'in maintenance'], true);
            });
            if (!$openRows) {
                continue;
            }
            $tasks = [];
            $ids = array_map(function ($row) { return $row->record_id; }, $openRows);
            $taskTable = $component ? 'logs_item_maintenance_task_done' : 'maintenance_task_done';
            if ($this->db->table_exists($taskTable)) {
                if ($component) {
                    $taskRows = $this->db->select('td.item_maintenance_id AS record_id,
                        t.name, td.status, "" AS remarks')
                        ->from('logs_item_maintenance_task_done td')
                        ->join('task t', 't.id = td.task_id', 'left')
                        ->where_in('td.item_maintenance_id', $ids)
                        ->where('td.active', 1)->get()->result();
                } else {
                    $taskRows = $this->db->select('equipment_maintenance_id AS record_id,
                        task_done AS name, remarks, "" AS status')
                        ->from('maintenance_task_done')
                        ->where_in('equipment_maintenance_id', $ids)
                        ->where('active', 1)->get()->result();
                }
                foreach ($taskRows as $task) {
                    $tasks[$task->record_id][] = [
                        'name' => $task->name ?: 'Maintenance task',
                        'status' => $task->status,
                        'remarks' => $task->remarks,
                    ];
                }
            }
            foreach ($openRows as $row) {
                $status = ucwords(strtolower(str_replace(['_', '-'], ' ', $row->final_status)));
                $alerts[] = [
                    'id' => ($component ? 'component' : 'asset') . '-pending-' . $row->record_id,
                    'entity_type' => $component ? 'component' : 'asset',
                    'type' => 'pending',
                    'status_label' => 'Pending',
                    'title' => $row->title,
                    'subtitle' => ($row->tag ?? '') ?: (string) $row->entity_id,
                    'asset_type' => $component ? 'Component' : 'Asset',
                    'location' => $row->location ?: 'No location',
                    'due_date' => substr((string) $row->record_date, 0, 10),
                    'days_text' => $status,
                    'maintenance_type' => $component ? 'Corrective' : ($row->maintenance_type ?: 'Not specified'),
                    'work_status' => $status,
                    'remarks' => $row->remarks ?: '',
                    'tasks' => $tasks[$row->record_id] ?? [],
                    'detail_url' => site_url(($component ? 'items' : 'assets') . '/info?id=' . rawurlencode($this->steve->id_encode($row->entity_id))),
                    'sort_weight' => 1,
                ];
            }
        }
        return $alerts;
    }

    private function formatDaysText($daysDiff)
    {
        if ($daysDiff < 0) {
            return abs($daysDiff) . ' day(s) overdue';
        }

        if ($daysDiff === 0) {
            return 'Due today';
        }

        return 'Due in ' . $daysDiff . ' day(s)';
    }

    private function json($payload, $status = 200)
    {
        $this->output
            ->set_header('Cache-Control: no-store')
            ->set_status_header($status)
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
