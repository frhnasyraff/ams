<?php
defined('BASEPATH') or exit('No direct script access allowed');

class StoreLocation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_masters')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }

        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('header', [
            'title' => 'Store Locations',
            'title2' => 'Store Locations'
        ]);

        $this->load->view('store-location', [
            'states' => $this->db->order_by('state_name', 'asc')->get('states')->result()
        ]);

        $this->load->view('footer', ['scripts' => ['design/js/store-location.js']]);
    }

    public function ajax_list()
    {
        $rows = $this->db
            ->select('id, name, active')
            ->order_by('name', 'asc')
            ->get('store_location')
            ->result();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(['data' => $rows]));
    }

    public function add()
    {
        $this->form_validation->set_rules('name', 'Store location name', 'trim|required|max_length[255]');

        if (!$this->form_validation->run()) {
            $this->session->set_flashdata('error', validation_errors());
            redirect('StoreLocation');
            return;
        }

        $created = $this->db->insert('store_location', [
            'name' => $this->input->post('name', true),
            'active' => 1
        ]);

        $this->session->set_flashdata(
            $created ? 'success' : 'error',
            $created ? 'Store location added successfully.' : 'Unable to add the store location.'
        );
        redirect('StoreLocation');
    }

    public function get_data()
    {
        $row = $this->db->get_where('store_location', ['id' => intval($this->input->get('id'))])->row();

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($row
                ? ['status' => 'success', 'data' => $row]
                : ['status' => 'error', 'message' => 'Store location not found.']));
    }

    public function update()
    {
        $this->form_validation->set_rules('id', 'Store location', 'required|integer');
        $this->form_validation->set_rules('name', 'Store location name', 'trim|required|max_length[255]');

        if (!$this->form_validation->run()) {
            $this->json(['status' => 'error', 'message' => strip_tags(validation_errors())]);
            return;
        }

        $updated = $this->db
            ->where('id', intval($this->input->post('id')))
            ->update('store_location', ['name' => $this->input->post('name', true)]);

        $this->json($updated
            ? ['status' => 'success', 'message' => 'Store location updated successfully.']
            : ['status' => 'error', 'message' => 'Unable to update the store location.']);
    }

    public function delete()
    {
        $id = intval($this->input->post('id'));
        if (!$id) {
            $this->json(['status' => 'error', 'message' => 'Invalid store location.']);
            return;
        }

        $deleted = $this->db->where('id', $id)->delete('store_location');
        $this->json($deleted
            ? ['status' => 'success', 'message' => 'Store location deleted successfully.']
            : ['status' => 'error', 'message' => 'Unable to delete the store location.']);
    }

    private function json($payload)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($payload));
    }
}
