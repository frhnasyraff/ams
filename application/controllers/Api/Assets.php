<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Assets extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if (!verifyJWT()) {
        //     errorResponse('missing or invalid token', [], 401);
        // }

        // to remember
        // controllers/orders, views/orders,  js/orders-list
        // xampp php 7.4, uer db
        // filezilla creds
        // db migration: nothing to note, check orderlogs 
        // uer assets count (last commit)
        // search (check from scratch)
        // swm789**  -- rams -- swm123 34.101.122.218
        // uer -- 

        $this->load->helper(array('form', 'file'));
        $this->load->library('form_validation');
    }

    public function register_asset_rfid()
    {
        $this->form_validation->set_rules("asset_id", "Asset ID", "required");
        $this->form_validation->set_rules("rfid", "RFID", "required");

        if ($this->form_validation->run() == FALSE) {
            return errorResponse('Validation failed', $this->form_validation->error_array());
        }

        $this->load->library('asset_rfid_binding');
        try {
            list($code, $message, $data) = $this->asset_rfid_binding->bind(
                $this->input->post('asset_id'), $this->input->post('rfid')
            );
        } catch (Throwable $error) {
            log_message('error', 'RFID binding failed: ' . $error->getMessage());
            return errorResponse('RFID binding could not be confirmed. Refresh before retrying.', [], 500);
        }
        if ($code !== 200) {
            return errorResponse($message, [], $code);
        }
        return successResponse($message, $data);
    }
    public function get_asset_types()
    {
        $result = $this->db->select('asset_id, name')
            ->from('asset_types')
            ->get()
            ->result();

        if (empty($result)) {
            return errorResponse('No asset types found', []);
        }

        return successResponse('Asset types fetched successfully', $result);

    }


    public function store_equipment()
    {
        $json = file_get_contents('php://input');
        $postData = json_decode($json, true);

        if ($postData) {
            $_POST = $postData; // overwrite POST array so form_validation works
        }

        $this->form_validation->set_rules("equipment_registration", "Equipment Registration", "required");
        $this->form_validation->set_rules("equipment_name", "Equipment Name", "required");
        $this->form_validation->set_rules("equipment_type", "Equipment Type", "required");

        if ($this->form_validation->run() == FALSE) {
            return errorResponse('Validation failed', $this->form_validation->error_array());
        }

        $data = [
            'equipment_registration' => $this->input->post('equipment_registration'),
            'equipment_name'         => $this->input->post('equipment_name'),
            'equipment_status'       => 'AVAILABLE',
            'equipment_type'         => $this->input->post('equipment_type'),
        ];

        $insert = $this->db->insert('equipments_asset', $data);

        if ($insert) {
            return successResponse('Equipment added successfully', [
                'status' => true,
                'data'   => $data
            ]);
        } else {
            return errorResponse('Failed to add equipment', []);
        }
    }







    // Mobile Side Api 

public function get_pending_maintenance()
{
    if (!$this->user_model->logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Login required']);
        return;
    }

    try {
        $this->db->select('
            ema.equipment_maintenance_id,
            ema.equipment_id,
            ema.maintenance_type_id,
            ema.final_status,
            ema.created_at,
            ema.updated_at,
            ea.equipment_name,
            ea.equipment_registration,
            ast.name as equipment_type_name,
            sl.name as store_location_name
        ');
        $this->db->from('equipment_maintenance_asset ema');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = ema.equipment_id', 'left');
        $this->db->join('asset_types ast', 'ast.asset_id = ea.equipment_type', 'left');
        $this->db->join('store_location sl', 'sl.id = ea.store_location_id', 'left');
        $this->db->where('ema.final_status', 'pending');
        $this->db->order_by('ema.created_at', 'DESC');

        $pending_maintenance = $this->db->get()->result();

        echo json_encode([
            'success' => true,
            'data' => $pending_maintenance,
            'count' => count($pending_maintenance)
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

public function get_maintenance_tasks($maintenance_id)
{

    try {
        $this->db->select('
            emt.id as task_id,
            emt.equipment_maintenance_id,
            emt.equipment_id,
            emt.task_list_id,
            emt.cost,
            emt.user_id,
            emt.file_path,
            emt.status,
            emt.created_at,
            emt.updated_at,
            tl.name as task_name,
            u.full_name as assigned_user_name,
            u.username,
            ea.equipment_name
        ');
        $this->db->from('equipment_maintenance_tasks emt');
        $this->db->join('task_list tl', 'tl.id = emt.task_list_id', 'left');
        $this->db->join('users u', 'u.user_id = emt.user_id', 'left');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = emt.equipment_id', 'left');
        $this->db->where('emt.equipment_maintenance_id', $maintenance_id);
        $this->db->order_by('tl.name', 'ASC');

        $tasks = $this->db->get()->result();

        // Format response for mobile
        $formatted_tasks = [];
        foreach ($tasks as $task) {
            $formatted_tasks[] = [
                'task_id' => $task->task_id,
                'task_name' => $task->task_name,
                'assigned_user' => $task->assigned_user_name ? $task->assigned_user_name . ' (' . $task->username . ')' : 'Not Assigned',
                'cost' => $task->cost ? "₹" . number_format(floatval($task->cost), 2) : '--',
                'file_path' => $task->file_path,
                'status' => $task->status,
                'equipment_name' => $task->equipment_name,
                'created_at' => $task->created_at,
                'updated_at' => $task->updated_at
            ];
        }

        echo json_encode([
            'success' => true,
            'data' => $formatted_tasks,
            'count' => count($formatted_tasks)
        ]);

    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

public function mobile_update_task()
{

    header('Content-Type: application/json');

    $input = json_decode(file_get_contents('php://input'), true);
    
    $task_id = $input['task_id'] ?? null;
    $equipment_id = $input['equipment_id'] ?? null;
    $maintenance_id = $input['maintenance_id'] ?? null;
    $task_list_id = $input['task_list_id'] ?? null;
    $cost = $input['cost'] ?? null;
    $user_id = $input['user_id'] ?? null;
    $status = $input['status'] ?? null;
    $base64_image = $input['image'] ?? null;

    try {
        // ✅ VALIDATE REQUIRED FIELDS
        if (empty($task_list_id)) {
            throw new Exception('Task List ID is required');
        }

        if (empty($equipment_id)) {
            throw new Exception('Equipment ID is required');
        }

        // ✅ PREPARE UPDATE DATA
        $update_data = [
            'equipment_maintenance_id' => $maintenance_id,
            'equipment_id' => $equipment_id,
            'task_list_id' => $task_list_id,
            'cost' => !empty($cost) ? floatval($cost) : 0.00,
            'user_id' => !empty($user_id) ? $user_id : null,
            'status' => !empty($status) ? $status : 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // ✅ BASE64 IMAGE UPLOAD HANDLING
        if (!empty($base64_image)) {
            $upload_path = './uploads/maintenance_tasks/';
            
            if (!is_dir($upload_path)) {
                mkdir($upload_path, 0777, true);
            }

            // Base64 decode and save image
            $image_data = base64_decode($base64_image);
            $file_name = 'mobile_task_' . time() . '_' . rand(1000, 9999) . '.jpg';
            $file_path = $upload_path . $file_name;

            if (file_put_contents($file_path, $image_data)) {
                $update_data['file_path'] = 'uploads/maintenance_tasks/' . $file_name;
            }
        }

        // ✅ TASK UPDATE/INSERT
        if ($task_id == 'new' || empty($task_id)) {
            $update_data['created_at'] = date('Y-m-d H:i:s');
            $updated = $this->db->insert('equipment_maintenance_tasks', $update_data);
            $message = 'Task created successfully!';
            $new_task_id = $this->db->insert_id();
        } else {
            $this->db->where('id', $task_id);
            $updated = $this->db->update('equipment_maintenance_tasks', $update_data);
            $message = 'Task updated successfully!';
            $new_task_id = $task_id;
        }

        if ($updated) {
            // ✅ CHECK IF ALL TASKS COMPLETED
            if ($this->checkAllTasksComplete($equipment_id, $maintenance_id)) {
                $this->db->where('equipment_maintenance_id', $maintenance_id);
                $this->db->update('equipment_maintenance_asset', [
                    'final_status' => 'complete',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                $this->updateNextMaintenanceDate($equipment_id, $maintenance_id);
            }

            echo json_encode([
                'success' => true, 
                'message' => $message,
                'task_id' => $new_task_id,
                'maintenance_id' => $maintenance_id
            ]);
        } else {
            $db_error = $this->db->error();
            throw new Exception('Database operation failed: ' . $db_error['message']);
        }

    } catch (Exception $e) {
        error_log("💥 MOBILE_UPDATE_TASK ERROR: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}


private function checkAllTasksComplete($equipment_id, $maintenance_id)
{
    $this->db->select('COUNT(*) as total_tasks, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks');
    $this->db->from('equipment_maintenance_tasks');
    $this->db->where('equipment_id', $equipment_id);
    $this->db->where('equipment_maintenance_id', $maintenance_id);
    
    $result = $this->db->get()->row();
    
    // Agar sab tasks complete hain to true return karo
    return ($result->total_tasks > 0 && $result->total_tasks == $result->completed_tasks);
}


private function updateNextMaintenanceDate($equipment_id, $maintenance_id)
{
    try {
        $this->db->where('equipment_maintenance_id', $maintenance_id);
        
        $maintenance_details = $this->db->select('*')
            ->from('equipment_maintenance_asset')
            ->get()
            ->row();
            
        if (!$maintenance_details) {
            error_log("❌ Maintenance details not found for ID: " . $maintenance_id);
            return false;
        }
        
        if ($maintenance_details->maintenance_type_id == 'preventive') {
            
            // ✅ FREQUENCY YEAR SET KAREN (Default 6 months)
            $frequency_year = $this->input->post('frequency_year') ?: "6";
            
            error_log("📅 Frequency Year: " . $frequency_year);
            
            $interval_duration_days = round(365.25 / $frequency_year);
            error_log("📅 Interval Days: " . $interval_duration_days);
            
            $current_date = $maintenance_details->update_date ?: date('Y-m-d H:i:s');
            error_log("📅 Current Date: " . $current_date);
            
            $dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $current_date);
            
            if ($dateObject) {
                // ✅ MONTHLY MAINTENANCE KE LIYE BHI DATE SET KAREN (NULL NA KAREN)
                $nextDateObject = clone $dateObject;
                $next_maintenance_date = $nextDateObject->modify("+$interval_duration_days days")->format('Y-m-d');
                
                $this->db->set('equipment_id', $equipment_id);
                $this->db->set('maintenance_date', $next_maintenance_date);
                $this->db->where("equipment_id", intval($equipment_id));
                $this->db->update('next_maintenance_date');
                
                error_log("✅ Updated next_maintenance_date to: " . $next_maintenance_date);
                
                // ✅ NEXT MAINTENANCE RECORD CREATE KARNA HAI TO YAHAN CODE ADD KAREN
            }
        } else {
            error_log("ℹ️ Maintenance type is not preventive, skipping next maintenance date calculation");
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("💥 Error in updateNextMaintenanceDate: " . $e->getMessage());
        return false;
    }
}



public function get_all_users()
{
    header('Content-Type: application/json');

    try {
        // ✅ Required Columns from 'users' table
        $this->db->select('user_id, username, full_name, email');
        $this->db->from('users');
        $this->db->order_by('full_name', 'ASC');

        $users = $this->db->get()->result();

        if (!empty($users)) {
            $formatted_users = [];

            foreach ($users as $u) {
                $formatted_users[] = [
                    'user_id'      => $u->user_id,
                    'username'     => $u->username,
                    'full_name'    => $u->full_name,
                    'email'        => $u->email,
                    'display_name' => $u->full_name . ' (' . $u->username . ')'
                ];
            }

            echo json_encode([
                'success' => true,
                'data'    => $formatted_users,
                'count'   => count($formatted_users)
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No users found in the system.'
            ]);
        }
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

public function maintenance_alerts()
{
    if (!verifyJWT()) {
        return errorResponse('missing or invalid token', [], 401);
    }

    $alerts = [];
    if ($this->db->table_exists('equipments_asset') && $this->db->table_exists('next_maintenance_date')) {
        $alerts = array_merge($alerts, $this->mobileScheduledMaintenanceAlerts(false));
    }
    if ($this->db->table_exists('add_asset_items')) {
        $alerts = array_merge($alerts, $this->mobileScheduledMaintenanceAlerts(true));
    }
    $alerts = array_merge($alerts, $this->mobilePendingMaintenanceAlerts());

    usort($alerts, function ($a, $b) {
        if ($a['sort_weight'] === $b['sort_weight']) {
            return strcmp((string) $a['due_date'], (string) $b['due_date']);
        }
        return $a['sort_weight'] <=> $b['sort_weight'];
    });

    return successResponse('Maintenance alerts fetched successfully', [
        'count' => count($alerts),
        'alerts' => $alerts,
    ]);
}

public function update_maintenance_status()
{
    if (!verifyJWT()) {
        return errorResponse('missing or invalid token', [], 401);
    }

    $rawInput = file_get_contents('php://input');
    $input = json_decode($rawInput, true);
    if (!is_array($input)) {
        return errorResponse('Invalid JSON payload', [], 422);
    }
    $entityType = strtolower(trim($input['entity_type'] ?? ''));
    $recordId = (int) ($input['record_id'] ?? 0);
    $status = strtolower(trim($input['status'] ?? ''));
    $remarks = trim((string) ($input['remarks'] ?? ''));
    $tasks = isset($input['tasks']) && is_array($input['tasks']) ? $input['tasks'] : [];
    $image = trim((string) ($input['image'] ?? ''));

    if (!in_array($entityType, ['asset', 'component'], true) || $recordId <= 0) {
        return errorResponse('Invalid maintenance record', [], 422);
    }
    if (!in_array($status, ['pending', 'in_progress', 'complete'], true)) {
        return errorResponse('Invalid maintenance status', [], 422);
    }
    if ($status === 'complete' && $remarks === '') {
        return errorResponse('Remarks are required before completing maintenance', [], 422);
    }
    if ($entityType === 'component' && $image !== '') {
        return errorResponse('Photo attachment is only supported for asset maintenance', [], 422);
    }

    if ($entityType === 'component') {
        if (!$this->db->where('id', $recordId)->count_all_results('logs_item_maintenance')) {
            return errorResponse('Maintenance record not found', [], 404);
        }
        $storedStatus = ['pending' => 'PENDING', 'in_progress' => 'IN PROGRESS', 'complete' => 'COMPLETE'][$status];
        $this->db->trans_start();
        $this->db->where('id', $recordId);
        $updated = $this->db->update('logs_item_maintenance', [
            'final_status' => $storedStatus,
            'notes' => $remarks,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    } else {
        if (!$this->db->where('equipment_maintenance_id', $recordId)->count_all_results('equipment_maintenance_asset')) {
            return errorResponse('Maintenance record not found', [], 404);
        }
        $assetUpdate = [
            'final_status' => $status,
            'maintenance_notes' => $remarks,
            'updated_at' => date('Y-m-d H:i:s'),
        ];
        if ($image !== '') {
            try {
                $assetUpdate['maintenance_files'] = $this->saveMobileMaintenanceImage($image);
            } catch (Exception $e) {
                return errorResponse($e->getMessage(), [], 422);
            }
        }
        $this->db->trans_start();
        $this->db->where('equipment_maintenance_id', $recordId);
        $updated = $this->db->update('equipment_maintenance_asset', $assetUpdate);
    }

    $this->updateMobileMaintenanceTasks($entityType, $recordId, $tasks, $status === 'complete');
    $this->db->trans_complete();

    if (!$updated || $this->db->trans_status() === false) {
        return errorResponse('Maintenance update failed', $this->db->error(), 500);
    }

    return successResponse($status === 'complete' ? 'Maintenance completed successfully' : 'Maintenance updated successfully', [
        'record_id' => $recordId,
        'entity_type' => $entityType,
        'status' => $status,
        'completed' => $status === 'complete',
    ]);
}

private function saveMobileMaintenanceImage($image)
{
    $image = trim($image);
    if (preg_match('/^data:image\/(jpeg|jpg|png);base64,/', $image, $matches)) {
        $image = substr($image, strpos($image, ',') + 1);
    }
    $image = preg_replace('/\s+/', '', $image);

    $binary = base64_decode($image, true);
    if ($binary === false || strlen($binary) > 5 * 1024 * 1024) {
        throw new Exception('Invalid maintenance image');
    }
    $imageInfo = @getimagesizefromstring($binary);
    if ($imageInfo === false || empty($imageInfo['mime'])) {
        throw new Exception('Maintenance image must be a valid image file');
    }
    $extensions = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    if (!isset($extensions[$imageInfo['mime']])) {
        throw new Exception('Maintenance image must be JPG or PNG');
    }
    $extension = $extensions[$imageInfo['mime']];

    $uploadPath = './uploads/maintenance_tasks/';
    if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
        throw new Exception('Unable to create maintenance upload folder');
    }
    if (!is_writable($uploadPath)) {
        throw new Exception('Maintenance upload folder is not writable');
    }
    $fileName = 'mobile_maintenance_' . time() . '_' . bin2hex(random_bytes(6)) . '.' . $extension;
    $filePath = $uploadPath . $fileName;
    if (file_put_contents($filePath, $binary) === false) {
        throw new Exception('Unable to save maintenance image');
    }

    return 'uploads/maintenance_tasks/' . $fileName;
}

private function updateMobileMaintenanceTasks($entityType, $recordId, $tasks, $markComplete = false)
{
    $statusMap = ['pending' => 'PENDING', 'in_progress' => 'IN PROGRESS', 'complete' => 'COMPLETE'];
    foreach ($tasks as $task) {
        $taskId = (int) ($task['task_id'] ?? 0);
        if ($taskId <= 0) {
            continue;
        }

        if ($entityType === 'component') {
            $taskStatus = strtolower(str_replace([' ', '-'], '_', trim((string) ($task['status'] ?? ''))));
            if ($markComplete) {
                $taskStatus = 'complete';
            }
            if (!isset($statusMap[$taskStatus])) {
                continue;
            }
            $this->db->where('id', $taskId)
                ->where('item_maintenance_id', $recordId)
                ->update('logs_item_maintenance_task_done', [
                    'status' => $statusMap[$taskStatus],
                ]);
        } else {
            $taskRemarks = trim((string) ($task['remarks'] ?? ''));
            $this->db->where('id', $taskId)
                ->where('equipment_maintenance_id', $recordId)
                ->update('maintenance_task_done', [
                    'remarks' => $taskRemarks,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }
}

private function mobileScheduledMaintenanceAlerts($component = false)
{
    $today = new DateTime('today');
    if ($component) {
        $serial = $this->db->field_exists('serial_number', 'add_asset_items') ? 'item.serial_number' : "''";
        $reminder = $this->db->field_exists('maintenance_reminder_day', 'add_asset_items') ? 'item.maintenance_reminder_day' : '0';
        $this->db->select('item.id AS entity_id, item.asset_id AS mobile_asset_id, item.item_name AS title, ' . $serial . ' AS tag, ' . $serial . ' AS serial_number, ' . $reminder . ' AS maintenance_reminder_day, sl.name AS location, item.maintenance_date AS next_maintenance_date')
            ->from('add_asset_items item')
            ->join('store_location sl', 'sl.id = item.store_location_id', 'left')
            ->where('item.maintenance_date IS NOT NULL', null, false);
    } else {
        $this->db->select('asset.equipment_id AS entity_id, asset.equipment_id AS mobile_asset_id, asset.equipment_name AS title, asset.equipment_registration AS tag, asset.serial_number, asset.maintenance_reminder_day, at.name AS asset_type_name, sl.name AS location, nmd.maintenance_date AS next_maintenance_date')
            ->from('equipments_asset asset')
            ->join('asset_types at', 'at.asset_id = asset.equipment_type', 'left')
            ->join('store_location sl', 'sl.id = asset.store_location_id', 'left')
            ->join('next_maintenance_date nmd', 'nmd.equipment_id = asset.equipment_id', 'left')
            ->where('nmd.maintenance_date IS NOT NULL', null, false)
            ->where('asset.maintenance_reminder_day IS NOT NULL', null, false);
    }

    $rows = $this->db->get()->result();
    $alerts = [];
    foreach ($rows as $row) {
        $date = substr((string) $row->next_maintenance_date, 0, 10);
        $dueDate = DateTime::createFromFormat('!Y-m-d', $date);
        if (!$dueDate || $dueDate->format('Y-m-d') !== $date || $date === '0000-00-00') continue;
        $reminderDate = (clone $dueDate)->modify('-' . max(0, (int) $row->maintenance_reminder_day) . ' days');
        if ($today < $reminderDate) continue;

        $daysDiff = (int) $today->diff($dueDate)->format('%r%a');
        $isOverdue = $dueDate < $today;
        $alerts[] = [
            'id' => ($component ? 'component' : 'asset') . '-maintenance-' . $row->entity_id,
            'record_id' => '',
            'entity_type' => $component ? 'component' : 'asset',
            'mobile_asset_id' => (string) $row->mobile_asset_id,
            'type' => $isOverdue ? 'overdue' : 'due_soon',
            'status_label' => $isOverdue ? 'Overdue' : 'Due soon',
            'title' => trim((string) $row->title) ?: ($component ? 'Unnamed component' : 'Unnamed asset'),
            'subtitle' => trim((string) $row->tag) ?: trim((string) $row->serial_number) ?: (string) $row->entity_id,
            'asset_type' => $component ? 'Component' : ($row->asset_type_name ?: 'Asset'),
            'location' => $row->location ?: 'No location',
            'due_date' => $dueDate->format('Y-m-d'),
            'days_text' => $this->mobileDaysText($daysDiff),
            'maintenance_type' => 'Preventive',
            'work_status' => 'Scheduled',
            'remarks' => '',
            'tasks' => [],
            'can_update' => false,
            'sort_weight' => $isOverdue ? 0 : 1,
        ];
    }
    return $alerts;
}

private function mobilePendingMaintenanceAlerts()
{
    $alerts = [];
    foreach ([false, true] as $component) {
        $table = $component ? 'logs_item_maintenance' : 'equipment_maintenance_asset';
        if (!$this->db->table_exists($table)) continue;

        if ($component) {
            $rows = $this->db->select('maintenance.id AS record_id, item.id AS entity_id, item.asset_id AS mobile_asset_id, item.item_name AS title, sl.name AS location, maintenance.update_date AS record_date, maintenance.final_status, maintenance.notes AS remarks')
                ->from('logs_item_maintenance maintenance')
                ->join('item_ticket ticket', 'ticket.id = maintenance.item_ticket_id')
                ->join('add_asset_items item', 'item.id = ticket.item_id')
                ->join('store_location sl', 'sl.id = item.store_location_id', 'left')
                ->where('NOT EXISTS (SELECT 1 FROM logs_item_maintenance newer WHERE newer.item_ticket_id = maintenance.item_ticket_id AND newer.active = 1 AND (newer.update_date > maintenance.update_date OR (newer.update_date = maintenance.update_date AND newer.id > maintenance.id)))', null, false)
                ->where('maintenance.active', 1)
                ->get()->result();
        } else {
            $rows = $this->db->select('maintenance.equipment_maintenance_id AS record_id, asset.equipment_id AS entity_id, asset.equipment_id AS mobile_asset_id, asset.equipment_name AS title, asset.equipment_registration AS tag, sl.name AS location, COALESCE(maintenance.update_date, maintenance.maintenance_date) AS record_date, maintenance.final_status, maintenance.maintenance_notes AS remarks, mt.maintenance_type')
                ->from('equipment_maintenance_asset maintenance')
                ->join('equipments_asset asset', 'asset.equipment_id = maintenance.equipment_id')
                ->join('store_location sl', 'sl.id = asset.store_location_id', 'left')
                ->join('maintenance_type_color_code mt', 'mt.id = maintenance.maintenance_type_id', 'left')
                ->get()->result();
        }

        $openRows = array_filter($rows, function ($row) {
            $status = strtolower(str_replace(['_', '-'], ' ', trim((string) $row->final_status)));
            return in_array($status, ['pending', 'in progress', 'in maintenance'], true);
        });
        if (!$openRows) continue;

        $tasks = $this->mobileTasksForRows($openRows, $component);
        foreach ($openRows as $row) {
            $status = ucwords(strtolower(str_replace(['_', '-'], ' ', $row->final_status)));
            $alerts[] = [
                'id' => ($component ? 'component' : 'asset') . '-pending-' . $row->record_id,
                'record_id' => (string) $row->record_id,
                'entity_type' => $component ? 'component' : 'asset',
                'mobile_asset_id' => (string) $row->mobile_asset_id,
                'type' => 'pending',
                'status_label' => 'Pending',
                'title' => $row->title ?: ($component ? 'Unnamed component' : 'Unnamed asset'),
                'subtitle' => ($row->tag ?? '') ?: (string) $row->entity_id,
                'asset_type' => $component ? 'Component' : 'Asset',
                'location' => $row->location ?: 'No location',
                'due_date' => substr((string) $row->record_date, 0, 10),
                'days_text' => $status,
                'maintenance_type' => $component ? 'Corrective' : ($row->maintenance_type ?: 'Not specified'),
                'work_status' => $status,
                'remarks' => $row->remarks ?: '',
                'tasks' => $tasks[$row->record_id] ?? [],
                'can_update' => true,
                'sort_weight' => 1,
            ];
        }
    }
    return $alerts;
}

private function mobileTasksForRows($rows, $component)
{
    $table = $component ? 'logs_item_maintenance_task_done' : 'maintenance_task_done';
    if (!$this->db->table_exists($table)) return [];

    $ids = array_map(function ($row) { return $row->record_id; }, $rows);
    if (!$ids) return [];

    if ($component) {
        $taskRows = $this->db->select('task_done.id AS task_id, task_done.item_maintenance_id AS record_id, task.name, task_done.status, "" AS remarks')
            ->from('logs_item_maintenance_task_done task_done')
            ->join('task task', 'task.id = task_done.task_id', 'left')
            ->where_in('task_done.item_maintenance_id', $ids)
            ->where('task_done.active', 1)
            ->get()->result();
    } else {
        $taskRows = $this->db->select('id AS task_id, equipment_maintenance_id AS record_id, task_done AS name, remarks, "" AS status')
            ->from('maintenance_task_done')
            ->where_in('equipment_maintenance_id', $ids)
            ->where('active', 1)
            ->get()->result();
    }

    $tasks = [];
    foreach ($taskRows as $task) {
        $tasks[$task->record_id][] = [
            'name' => $task->name ?: 'Maintenance task',
            'task_id' => (string) $task->task_id,
            'status' => $task->status,
            'remarks' => $task->remarks,
        ];
    }
    return $tasks;
}

private function mobileDaysText($daysDiff)
{
    if ($daysDiff < 0) return abs($daysDiff) . ' day(s) overdue';
    if ($daysDiff === 0) return 'Due today';
    return 'Due in ' . $daysDiff . ' day(s)';
}


}
