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

        $assetId = $this->input->post('asset_id');
        $rfid    = $this->input->post('rfid');

        $asset = $this->db->select('*')
            ->from('equipments_asset')
            ->where('equipment_id ', $assetId)
            ->get()
            ->row();

        if (!$asset) {

            return successResponse('Asset not found', [
                'status' => false
            ]);
        }


        $this->db->where('equipment_id ', $assetId)->update('equipments_asset', ['rfid' => $rfid]);

        return successResponse('RFID updated successfully', [
            'status'   => true,
            'equipment_id ' => $assetId,
            'rfid'     => $rfid
        ]);
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


}

