<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_type extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_users')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
        $this->load->library('form_validation');
    }

    public function index()
    {

        $manufacturer_name = $this->db->select('*')
            ->from('vendor_manufacturing_number')
            ->get()
            ->result();

        $part_numbers = $this->db->select('id , part_number')
            ->from('vendor_part_number')
            ->get()
            ->result();

        $this->load->view('header', ['title' => 'Component Types', 'title2' => 'Component Types']);

        $this->load->view('item_type', [
            'manufacturer_name' => $manufacturer_name,
            'part_numbers' => $part_numbers
        ]);

        $this->load->view('footer', ['scripts' => ['design/js/item_type.js']]);
    }

    public function ajax_list()
{
    $this->db->select('item_types.*, vendor_part_number.part_number, vendor_manufacturing_number.manufacturer_name');
    $this->db->from('item_types');
    $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number', 'left');
    $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer', 'left');
    $query = $this->db->get();
    $data = $query->result();

    echo json_encode([
        'data' => $data,
    ]);
}


    public function add()
    {
        $this->form_validation->set_rules('name', 'Name', 'required|regex_match[/^[a-zA-Z0-9\s\-_\.]+$/]');
        $calibration  = $this->input->post('calibration');
        $maintenance  = $this->input->post('maintenance');
        $manufacturer  = $this->input->post('manufacturer');
        $vendor_part_number  = $this->input->post('vendor_part_number');
        if (empty($calibration) && $calibration == null) {
            $calibration = '0';
        } else {
            $calibration  = $this->input->post('calibration');
        }

        if (empty($maintenance) && $maintenance == null) {
            $maintenance = '0';
        } else {
            $maintenance  = $this->input->post('maintenance');
        }

        if ($this->form_validation->run() === TRUE) {
            // Validation passed, proceed with inserting data
            $data = array(
                'name'   => $this->input->post('name'),
                'calibration'   => $calibration,
                'maintenance'   => $maintenance,
                'manufacturer'   => $manufacturer,
                'vendor_part_number'   => $vendor_part_number,
            );

            // Insert data into the database
            if ($this->db->insert('item_types', $data)) {
                // Specify the table name
                $this->session->set_flashdata('success', 'Items Types added successfully!');
            } else {

                $this->session->set_flashdata('error', 'Error while adding Items Types.');
            }
            redirect('item_type');
            // Adjust the redirect as needed
        } else {

            // Validation failed, reload the form or show errors
            $this->session->set_flashdata('error', validation_errors());
            redirect('item_type');
            // Adjust the redirect as needed
        }
    }

    public function update()
    {
        $id = $this->input->post('id');
        $calibration  = $this->input->post('calibration');
        $maintenance  = $this->input->post('maintenance');
        $manufacturer  = $this->input->post('manufacturer');
        $vendor_part_number  = $this->input->post('vendor_part_number');

        if (empty($calibration) && $calibration == null) {
            $calibration = '0';
        } else {
            $calibration  = $this->input->post('calibration');
        }

        if (empty($maintenance) && $maintenance == null) {
            $maintenance = '0';
        } else {
            $maintenance  = $this->input->post('maintenance');
        }

        $data = [
            'name' => $this->input->post('name'),
            'calibration' =>  $calibration,
            'maintenance' =>  $maintenance,
            'manufacturer' =>  $manufacturer,
            'vendor_part_number' =>  $vendor_part_number,
        ];

        $this->db->where('id', $id);
        if ($this->db->update('item_types', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'Item Types updated successfully.']);
        } else {

            echo json_encode(['status' => 'error', 'message' => 'Error updating Items Types.']);
        }
    }

    public function delete()
    {
        // Get the ID from the POST request
        $id = $this->input->post('id');

        // Check if ID is valid ( you can add more validation here if needed )
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            return;
        }

        // Perform the deletion
        $this->db->where('id', $id);
        if ($this->db->delete('item_types')) {
            // Deletion was successful
            echo json_encode(['status' => 'success', 'message' => 'Items Types deleted successfully.']);
        } else {
            // Deletion failed
            echo json_encode(['status' => 'error', 'message' => 'Error deleting Items Types.']);
        }
    }

    public function get_data()
    {
        $id = $this->input->get('id');

        $this->db->where('id', $id);
        $query = $this->db->get('item_types');

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->row()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data not found.']);
        }
    }

    public function item_calibration()
    {
        if (isset($_POST['asset_id'])) {
            $assetId = $_POST['asset_id'];

            // Query the database for a single result
            $data = $this->db->select('*')
                ->from('item_types')
                ->where('id', $assetId)
                ->get()
                ->row();
            // Use ->row() for a single result

            // Return JSON response
            header('Content-Type: application/json');
            if ($data) {
                echo json_encode([
                    'calibration' => $data->calibration,
                    'maintenance' => $data->maintenance
                ]);
            } else {
                echo json_encode([
                    'calibration' => 0,
                    'maintenance' => 0
                ]);
            }
        }
    }
}
