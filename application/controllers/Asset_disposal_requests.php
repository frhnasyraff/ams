<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_disposal_requests extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->library('session');
        $this->load->library('upload');
        $this->load->model('user_model');
        $this->load->model('asset_disposal_requests_model');
        $this->load->model('disposal_methods_model');
        
        if (!$this->user_model->logged_in()) {
            die(redirect('/order_summary?error=Please login to access this page.'));
        }
    }
    
    public function index()
    {
        if (!$this->user_model->has_perm('list_assets')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
        
        $search = $this->input->get('search');
        $status = $this->input->get('status');

        $config['base_url'] = site_url('asset_disposal_requests');
        $config['total_rows'] = $this->asset_disposal_requests_model->count_all($search, $status);
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
        $config['query_string_segment'] = 'page';
        $config['reuse_query_string'] = TRUE;
        $config['first_link'] = '1';
        $config['last_link'] = (string) max(1, (int) ceil($config['total_rows'] / $config['per_page']));
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;

        $assets = $this->db->select('equipment_id, equipment_name, serial_number')
                          ->from('equipments_asset')
                          ->order_by('equipment_name', 'ASC')
                          ->get()
                          ->result();

        $write_off_reasons = $this->db->select('id, write_off_reason')
                                     ->from('write_off_reasons')
                                     ->order_by('write_off_reason', 'ASC')
                                     ->get()
                                     ->result();

        $disposal_methods = $this->db->select('id, disposal_method')
                                    ->from('disposal_methods')
                                    ->order_by('disposal_method', 'ASC')
                                    ->get()
                                    ->result();
        
        $data = [
            'title' => 'Asset Disposal Requests',
            'title2' => 'Asset Disposal Requests',
            'requests' => $this->asset_disposal_requests_model->get_all($config['per_page'], $page, $search, $status),
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'page' => $page,
            'config' => $config,
            'search' => $search,
            'status' => $status,
            'assets' => $assets,
            'write_off_reasons' => $write_off_reasons,
            'disposal_methods' => $disposal_methods,
        ];

        $this->load->view('header', $data);
        $this->load->view('asset-disposal-requests-list', $data);
        $this->load->view('footer', [
            'scripts' => []
        ]);
    }
    
    public function view($id = null)
    {
        if (!$id) {
            show_404();
            return;
        }

        $request = $this->asset_disposal_requests_model->get_by_id($id);
        if (!$request) {
            show_404();
            return;
        }

        $assets = $this->asset_disposal_requests_model->get_active_assets();
        $write_off_reasons = $this->asset_disposal_requests_model->get_active_reasons();
        $disposal_methods = $this->disposal_methods_model->get_all();

        $data = [
            'title' => 'View Asset Disposal Request',
            'edit_id' => $id,
            'request' => $request,
            'assets' => $assets,
            'write_off_reasons' => $write_off_reasons,
            'disposal_methods' => $disposal_methods
        ];

        $this->load->view('header', $data);
        $this->load->view('asset-disposal-requests-list', $data);
        $this->load->view('footer');
    }

    // public function save()
    // {
    //     if (!$this->input->is_ajax_request()) {
    //         die(json_encode(['success' => false, 'message' => 'Invalid request']));
    //     }
        
    //     $id = $this->input->post('id');
    //     $equipment_asset_id = $this->input->post('equipment_asset_id');
    //     $write_off_reason_id = $this->input->post('write_off_reason_id');
    //     $estimated_value = $this->input->post('estimated_value');
    //     $justification = $this->input->post('justification');
    //     $disposal_method_id = $this->input->post('disposal_method_id');

    //     $status = $this->input->post('status');

    //     if (empty($equipment_asset_id)) {
    //         echo json_encode(['success' => false, 'message' => 'Please select an asset', 'field' => 'equipment_asset_id']);
    //         return;
    //     }
    //     if (empty($write_off_reason_id)) {
    //         echo json_encode(['success' => false, 'message' => 'Please select a write-off reason', 'field' => 'write_off_reason_id']);
    //         return;
    //     }
    //     if (empty($justification)) {
    //         echo json_encode(['success' => false, 'message' => 'Justification is required', 'field' => 'justification']);
    //         return;
    //     }
    //     if (empty($disposal_method_id)) {
    //         echo json_encode(['success' => false, 'message' => 'Please select a disposal method', 'field' => 'disposal_method_id']);
    //         return;
    //     }

    //     $data = [
    //         'equipment_asset_id' => $equipment_asset_id,
    //         'write_off_reason_id' => $write_off_reason_id,
    //         'disposal_method_id' => $disposal_method_id,
    //         'estimated_value' => $estimated_value ? floatval($estimated_value) : 0.00,
    //         'justification' => trim($justification),
    //         'status' => 'new',
    //         'created_at' => date('Y-m-d H:i:s'),
    //         'updated_at' => date('Y-m-d H:i:s')
    //     ];

    //     try {
    //         if ($id) {
    //             $result = $this->asset_disposal_requests_model->update($id, $data);
    //             $message = 'Request updated successfully';
    //             $request_id = $id;
    //         } else {
    //             $request_id = $this->asset_disposal_requests_model->create($data);
    //             $result = ($request_id > 0);
    //             $message = 'Request created successfully';
    //         }

    //         if (!empty($_FILES['attachment']['name'])) {
    //             $upload_result = $this->upload_file($request_id);
    //             if ($upload_result['success']) {
    //                 $update_data = ['attachment' => $upload_result['file_path']];
    //                 $this->asset_disposal_requests_model->update($request_id, $update_data);
    //             }
    //         }

    //         if ($result) {
    //             echo json_encode([
    //                 'success' => true,
    //                 'message' => $message,
    //                 'id' => $request_id,
    //                 'redirect' => site_url('asset_disposal_requests')
    //             ]);
    //         } else {
    //             echo json_encode(['success' => false, 'message' => 'Failed to save request.']);
    //         }
    //     } catch (Exception $e) {
    //         echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    //     }
    // }


    public function save_changes()
    {
        if (!$this->input->is_ajax_request()) {
            die(json_encode(['success' => false, 'message' => 'Invalid request']));
        }
        
        $id = $this->input->post('id');
        $equipment_asset_id = $this->input->post('equipment_asset_id');
        $write_off_reason_id = $this->input->post('write_off_reason_id');
        $disposal_method_id = $this->input->post('disposal_method_id');
        $estimated_value = $this->input->post('estimated_value');
        $justification = $this->input->post('justification');
        
        if (empty($equipment_asset_id)) {
            echo json_encode(['success' => false, 'message' => 'Please select an asset', 'field' => 'equipment_asset_id']);
            return;
        }
        if (empty($disposal_method_id)) {
            echo json_encode(['success' => false, 'message' => 'Please select disposal method', 'field' => 'disposal_method_id']);
            return;
        }
        if (empty($justification)) {
            echo json_encode(['success' => false, 'message' => 'Justification is required', 'field' => 'justification']);
            return;
        }
        
        try {
            $data = [
                'equipment_asset_id' => $equipment_asset_id,
                'write_off_reason_id' => $write_off_reason_id,
                'disposal_method_id' => $disposal_method_id,
                'estimated_value' => $estimated_value ? floatval($estimated_value) : 0.00,
                'justification' => trim($justification),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->where('id', $id);
            $result = $this->db->update('asset_disposal_requests', $data);
            
            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Changes saved successfully',
                    'redirect' => site_url('asset_disposals')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save changes']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }

    // public function change_status_ajax_original()
    // {
    //     if (!$this->input->is_ajax_request()) {
    //         die(json_encode(['success' => false, 'message' => 'Invalid request']));
    //     }

    //     $id = $this->input->post('id');
    //     $status = $this->input->post('status');

    //     if (!$id || !$status) {
    //         echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    //         return;
    //     }

    //     try {
    //         // Get current user from session
    //         $current_user = $this->user_model->current_user();
            
    //         if (!$current_user) {
    //             echo json_encode(['success' => false, 'message' => 'User not logged in']);
    //             return;
    //         }
    //         $user_id = $current_user->user_id; 

    //         $request = $this->asset_disposal_requests_model->get_by_id($id);
    //         if (!$request) {
    //             echo json_encode(['success' => false, 'message' => 'Request not found']);
    //             return;
    //         }

    //         // Check permissions
    //         // if (!$this->user_model->has_perm('approve_disposals') && $status == 'approved') {
    //         //     echo json_encode(['success' => false, 'message' => 'No permission to approve']);
    //         //     return;
    //         // }
            
    //         // if (!$this->user_model->has_perm('reject_disposals') && $status == 'rejected') {
    //         //     echo json_encode(['success' => false, 'message' => 'No permission to reject']);
    //         //     return;
    //         // }

    //         // Insert into asset_disposal_status table
    //         $status_data = [

    //             'disposal_method_id' => $request->disposal_method_id ?? null,
    //             'user_id' => $user_id,
    //             'status' => $status,
    //             'created_at' => date('Y-m-d H:i:s')
    //         ];

    //         // First check if table exists
    //         if (!$this->db->table_exists('asset_disposal_status')) {
    //             // Create table if not exists
    //             $this->create_asset_disposal_status_table();
    //         }

    //         $status_inserted = $this->db->insert('asset_disposal_status', $status_data);
            
    //         if (!$status_inserted) {
    //             echo json_encode(['success' => false, 'message' => 'Failed to save status']);
    //             return;
    //         }

    //         // Update main request status
    //         $update_data = [
    //             'status' => $status,
    //             'updated_at' => date('Y-m-d H:i:s')
    //         ];
            
    //         $this->db->where('id', $id);
    //         $request_updated = $this->db->update('asset_disposal_requests', $update_data);
            
    //         // Update asset status if approved
    //         if ($status == 'approved' && $request->equipment_asset_id) {
    //             $this->db->where('equipment_id', $request->equipment_asset_id);
    //             $this->db->update('equipments_asset', [
    //                 'equipment_status' => 'disposed',
    //                 'updated_at' => date('Y-m-d H:i:s')
    //             ]);
    //         }

    //         if ($request_updated) {
    //             echo json_encode([
    //                 'success' => true, 
    //                 'message' => 'Request has been ' . $status . ' successfully'
    //             ]);
    //         } else {
    //             echo json_encode(['success' => false, 'message' => 'Failed to update request status']);
    //         }
    //     } catch (Exception $e) {
    //         echo json_encode([
    //             'success' => false, 
    //             'message' => 'Error: ' . $e->getMessage()
    //         ]);
    //     }
    // }

    public function change_status_ajax()
{
    $id = $this->input->post('id');
    $status = $this->input->post('status');
    $user_id = isset($_SESSION['user']->user_id) ? $_SESSION['user']->user_id : 1;


    if (!$id || !$status) {
        echo json_encode([
            'success' => false,
            'message' => 'Invalid request.'
        ]);
        return;
    }

    // 1. Update main request table
    $this->db->where('id', $id);
    $this->db->update('asset_disposal_requests', [
        'status' => $status,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

    $request = $this->asset_disposal_requests_model->get_by_id($id);

    // 2. Keep an audit trail of every status change.
    $this->db->insert('asset_disposal_status', [
        'request_id' => $id,
        'disposal_method_id' => $request ? $request->disposal_method_id : null,
        'status' => $status,
        'user_id' => $user_id,
        'created_at' => date('Y-m-d H:i:s')
    ]);

    echo json_encode([
        'success' => true,
        'message' => $status === 'approved' 
            ? 'Request approved successfully.' 
            : 'Request rejected successfully.'
    ]);
}


    // private function upload_file($request_id)
    // {
    //     $upload_path = FCPATH . 'uploads/asset_attachments/';
        
    //     if (!is_dir($upload_path)) {
    //         if (!mkdir($upload_path, 0777, TRUE)) {
    //             return [
    //                 'success' => false,
    //                 'message' => 'Failed to create upload directory'
    //             ];
    //         }
    //     }
        
    //     if (!is_writable($upload_path)) {
    //         return [
    //             'success' => false,
    //             'message' => 'Upload directory is not writable'
    //         ];
    //     }
        
    //     $config['upload_path'] = $upload_path;
    //     $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
    //     $config['max_size'] = 5120;
    //     $config['file_name'] = 'asset_disposal_' . $request_id . '_' . time();
    //     $config['overwrite'] = FALSE;
        
    //     $this->upload->initialize($config);
        
    //     if ($this->upload->do_upload('attachment')) {
    //         $upload_data = $this->upload->data();
    //         return [
    //             'success' => true,
    //             'file_path' => 'uploads/asset_attachments/' . $upload_data['file_name'],
    //             'file_name' => $upload_data['file_name']
    //         ];
    //     } else {
    //         return [
    //             'success' => false,
    //             'message' => $this->upload->display_errors()
    //         ];
    //     }
    // }


    public function save()
    {
        if (!$this->input->is_ajax_request()) {
            die(json_encode(['success' => false, 'message' => 'Invalid request']));
        }
        
        // Validation
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('equipment_asset_id', 'Asset', 'required');
        $this->form_validation->set_rules('write_off_reason_id', 'Write-off Reason', 'required');
        $this->form_validation->set_rules('disposal_method_id', 'Disposal Method', 'required');
        $this->form_validation->set_rules('justification', 'Justification', 'required|min_length[10]');
        
        if ($this->form_validation->run() === FALSE) {
            $errors = $this->form_validation->error_array();
            $first_error = array_shift($errors);
            echo json_encode(['success' => false, 'message' => $first_error]);
            return;
        }
        
        $id = $this->input->post('id');
        $equipment_asset_id = $this->input->post('equipment_asset_id');
        $write_off_reason_id = $this->input->post('write_off_reason_id');
        $estimated_value = $this->input->post('estimated_value');
        $justification = $this->input->post('justification');
        $disposal_method_id = $this->input->post('disposal_method_id');
        $requested_status = strtolower((string) $this->input->post('status'));
        $status = in_array($requested_status, ['new', 'draft', 'submitted'], true)
            ? $requested_status
            : 'new';

        // Check if asset exists
        $this->db->where('equipment_id', $equipment_asset_id);
        $asset_exists = $this->db->get('equipments_asset')->row();
        
        if (!$asset_exists) {
            echo json_encode(['success' => false, 'message' => 'Selected asset does not exist']);
            return;
        }

        $data = [
            'equipment_asset_id' => $equipment_asset_id,
            'write_off_reason_id' => $write_off_reason_id,
            'disposal_method_id' => $disposal_method_id,
            'estimated_value' => $estimated_value ? floatval($estimated_value) : 0.00,
            'justification' => trim($justification),
            'status' => $status,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        try {
            if ($id) {
                // Update existing
                $this->db->where('id', $id);
                $result = $this->db->update('asset_disposal_requests', $data);
                $message = 'Request updated successfully';
                $request_id = $id;
            } else {
                // Create new with request number
                $data['request_number'] = $this->asset_disposal_requests_model->generate_request_number();
                $result = $this->db->insert('asset_disposal_requests', $data);
                $request_id = $this->db->insert_id();
                $message = 'Request created successfully';
            }

            // Handle file upload
            if (!empty($_FILES['attachment']['name'])) {
                $upload_result = $this->upload_file($request_id);
                
                if ($upload_result['success']) {
                    $this->db->where('id', $request_id);
                    $this->db->update('asset_disposal_requests', ['attachment' => $upload_result['file_path']]);
                } else {
                    // Log upload error but don't fail the request
                    error_log('Upload failed: ' . $upload_result['message']);
                }
            }

            if ($result) {
                echo json_encode([
                    'success' => true,
                    'message' => $message,
                    'id' => $request_id,
                    'redirect' => site_url('asset_disposals')
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to save request']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    }




    private function upload_file($request_id)
{
    $upload_path = FCPATH . 'uploads/asset_attachments/';
    
    if (!is_dir($upload_path)) {
        if (!mkdir($upload_path, 0777, TRUE)) {
            return [
                'success' => false,
                'message' => 'Failed to create upload directory'
            ];
        }
    }
    
    if (!is_writable($upload_path)) {
        return [
            'success' => false,
            'message' => 'Upload directory is not writable'
        ];
    }
    
    $config['upload_path'] = $upload_path;
    $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx|xls|xlsx';
    $config['max_size'] = 20480; // 20MB in KB (changed from 5120)
    $config['file_name'] = 'asset_disposal_' . $request_id . '_' . time();
    $config['overwrite'] = FALSE;
    
    $this->upload->initialize($config);
    
    if ($this->upload->do_upload('attachment')) {
        $upload_data = $this->upload->data();
        return [
            'success' => true,
            'file_path' => 'uploads/asset_attachments/' . $upload_data['file_name'],
            'file_name' => $upload_data['file_name']
        ];
    } else {
        return [
            'success' => false,
            'message' => $this->upload->display_errors()
        ];
    }
}
    
    public function get_asset_details()
    {
        $asset_id = $this->input->post('asset_id');
        
        if (!$asset_id) {
            echo json_encode(['success' => false]);
            return;
        }

        $this->db->where('equipment_id', $asset_id);
        $asset = $this->db->get('equipments_asset')->row();

        if (!$asset) {
            echo json_encode(['success' => false]);
            return;
        }

        echo json_encode([
            'success' => true,
            'asset_tag' => $asset->asset_tag ?? '',
            'serial_number' => $asset->serial_number ?? '',
            'asset_name' => $asset->equipment_name ?? ''
        ]);
    }
}
