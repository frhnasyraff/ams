<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Locations extends CI_Controller
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
        $this->load->view('header', ['title' => 'Locations', 'title2' => 'Locations']);

        $countries = $this->db->select('*')->from('countries')->get()->result();
        $states = $this->db->select('*')->from('states')->get()->result();
        $this->load->view('locations', [
            'countries' => $countries,
            'states' => $states
        ]);

        $this->load->view('footer', ['scripts' => ['design/js/locations.js']]);
    }


    public function ajax_list()
    {
        $filterValue = $this->input->get('filter');

        // Initialize the query for the locations table
        // Legacy query:
        // $this->db->select('*');
        // $this->db->from('locations');
        $this->db->select('locations.*, states.state_name');
        $this->db->from('locations');
        $this->db->join('states', 'states.id = locations.state_id', 'left');

        // Apply the filter if it exists
        if ($filterValue) {
            // Legacy: $this->db->where('state_name', $filterValue);
            $this->db->where('states.state_name', $filterValue);
        }



        $query = $this->db->get();
        $data = $query->result();

        // Return the results as JSON
        echo json_encode([
            'data' => $data,
        ]);
    }


    public function state_ajax()
    {
        if ($this->user_model->has_perm('edit_users') && $this->input->post('id')) {
            die($this->steve->active_toggle('users', 'user_id'));
        }
    }


    public function add()
    {
        $this->form_validation->set_rules('country_name', 'Country Name');
        // Legacy: $this->form_validation->set_rules('state_name', 'State Name', 'required|regex_match[/^[a-zA-Z0-9\s]+$/]');
        $this->form_validation->set_rules('state_id', 'State', 'required|integer');
        $this->form_validation->set_rules('name', 'Name', 'required|regex_match[/^[a-zA-Z0-9\s]+$/]');
        $this->form_validation->set_rules('colour', 'Colour Code');
        $this->form_validation->set_rules('address', 'Address', 'required|regex_match[/^[a-zA-Z0-9\s\-_\.]+$/]');
        $this->form_validation->set_rules('long', 'Long', 'required');
        $this->form_validation->set_rules('lat', 'lat', 'required');

        if ($this->form_validation->run() === TRUE) {
            // Validation passed, proceed with inserting data
            $data = array(
                // 'country_name' => $this->input->post( 'country_name' ),
                // Legacy: 'state_name' => $this->input->post('state_name'),
                'state_id'   => $this->input->post('state_id'),
                'name'   => $this->input->post('name'),
                'colour'   => $this->input->post('colour'),
                'address'   => $this->input->post('address'),
                'long'   => $this->input->post('long'),
                'lat'   => $this->input->post('lat')
            );

            // Insert data into the database
            if ($this->db->insert('locations', $data)) {
                // Specify the table name
                $this->session->set_flashdata('success', 'Location added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Error while adding location.');
            }
            redirect('locations');
            // Adjust the redirect as needed
        } else {
            // Validation failed, reload the form or show errors
            $this->session->set_flashdata('error', validation_errors());
            redirect('locations');
            // Adjust the redirect as needed
        }
    }

    public function update()
    {
        $id = $this->input->post('id');
        $this->form_validation->set_rules('country_name', 'Country Name');
        // Legacy: $this->form_validation->set_rules('state_name', 'State Name', 'required|regex_match[/^[a-zA-Z0-9\s]+$/]');
        $this->form_validation->set_rules('state_id', 'State', 'required|integer');
        $this->form_validation->set_rules('name', 'Name', 'required|regex_match[/^[a-zA-Z0-9\s]+$/]');
        $this->form_validation->set_rules('colour', 'Colour Code');
        $this->form_validation->set_rules('address', 'Address', 'required|regex_match[/^[a-zA-Z0-9\s\-_\.]+$/]');
        $this->form_validation->set_rules('long', 'Long', 'required');
        $this->form_validation->set_rules('lat', 'lat', 'required');
        if ($this->form_validation->run() === TRUE) {
            $data = array(
                // 'country_name' => $this->input->post( 'country_name' ),
                // Legacy: 'state_name' => $this->input->post('state_name'),
                'state_id'   => $this->input->post('state_id'),
                'name'   => $this->input->post('name'),
                'colour'   => $this->input->post('colour'),
                'address'   => $this->input->post('address'),
                'long'   => $this->input->post('long'),
                'lat'   => $this->input->post('lat')
            );

            $this->db->where('id', $id);
            if ($this->db->update('locations', $data)) {
                echo json_encode(['status' => 'success', 'message' => 'Location updated successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Error updating location.']);
            }
        } else {
            // Validation failed, reload the form or show errors
            $this->session->set_flashdata('error', validation_errors());
            redirect('locations');
            // Adjust the redirect as needed
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
        if ($this->db->delete('locations')) {
            // Deletion was successful
            echo json_encode(['status' => 'success', 'message' => 'Item deleted successfully.']);
        } else {
            // Deletion failed
            echo json_encode(['status' => 'error', 'message' => 'Error deleting item.']);
        }
    }

    public function get_data()
    {
        $id = $this->input->get('id');

        $this->db->where('id', $id);
        $query = $this->db->get('locations');

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->row()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data not found.']);
        }
    }
}
