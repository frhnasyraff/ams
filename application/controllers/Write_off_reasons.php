<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Write_off_reasons extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('user_model');
        $this->load->model('write_off_reasons_model');
        
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_masters')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }
    
    public function index()
    {
        // Pagination configuration
        $config['base_url'] = site_url('write_off_reasons');
        $config['total_rows'] = $this->write_off_reasons_model->count_all();
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
        $config['query_string_segment'] = 'page';
        
        $config['first_link'] = '1';
        $config['last_link'] = (string) max(1, (int) ceil($config['total_rows'] / $config['per_page']));
        
        $this->pagination->initialize($config);
        
        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;
        
        $data = [
            'title' => 'Write Off Reasons',
            'title2' => 'Write Off Reasons',
            'write_off_reasons' => $this->write_off_reasons_model->get_all($config['per_page'], $page),
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'page' => $page,
            'config' => $config
        ];
        
        $this->load->view('header', $data);
        $this->load->view('write-off-reasons', $data);
        $this->load->view('footer', [
            'scripts' => ['design/js/write-off-reasons.js']
        ]);
    }
    
    /**
     * AJAX: Create or update write-off reason
     */
    public function save()
    {
        if (!$this->input->is_ajax_request()) {
            die('Invalid request');
        }
        
        $id = $this->input->post('id');
        $write_off_reason = trim($this->input->post('write_off_reason'));
        $description = trim($this->input->post('description'));
        $status = $this->input->post('status');
        
        // Validation
        if (empty($write_off_reason)) {
            echo json_encode(['success' => false, 'message' => 'Write-off reason is required', 'field' => 'write_off_reason']);
            return;
        }
        
        // Check if already exists
        if ($this->write_off_reasons_model->exists($write_off_reason, $id)) {
            echo json_encode(['success' => false, 'message' => 'Write-off reason already exists', 'field' => 'write_off_reason']);
            return;
        }
        
        $data = [
            'write_off_reason' => $write_off_reason,
            'description' => $description,
            'status' => $status
        ];
        
        if ($id) {
            // Update
            $result = $this->write_off_reasons_model->update($id, $data);
            $message = 'Write-off reason updated successfully';
        } else {
            // Create
            $result = $this->write_off_reasons_model->create($data);
            $message = 'Write-off reason created successfully';
        }
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => $message]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save write-off reason']);
        }
    }
    
    /**
     * AJAX: Delete write-off reason
     */
    public function delete()
    {
        if (!$this->input->is_ajax_request()) {
            die('Invalid request');
        }
        
        $id = $this->input->post('id');
        
        if (empty($id)) {
            echo json_encode(['success' => false, 'message' => 'ID is required']);
            return;
        }
        
        // Check if reason exists
        $reason = $this->write_off_reasons_model->get_by_id($id);
        if (!$reason) {
            echo json_encode(['success' => false, 'message' => 'Write-off reason not found']);
            return;
        }
        
        // Delete
        $result = $this->write_off_reasons_model->delete($id);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Write-off reason deleted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete write-off reason']);
        }
    }
    
    /**
     * AJAX: Change status
     */
    public function change_status()
    {
        if (!$this->input->is_ajax_request()) {
            die('Invalid request');
        }
        
        $id = $this->input->post('id');
        $status = $this->input->post('status');
        
        if (empty($id) || empty($status)) {
            echo json_encode(['success' => false, 'message' => 'ID and status are required']);
            return;
        }
        
        $result = $this->write_off_reasons_model->change_status($id, $status);
        
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update status']);
        }
    }

    public function get_ajax_list()
    {
        if (!$this->input->is_ajax_request()) {
            show_404();
        }

        $search = $this->input->get('search');
        $status = $this->input->get('status');

        $data = $this->write_off_reasons_model->get_all(null, null, $search, $status);

        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }

}
