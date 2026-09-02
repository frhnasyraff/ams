<?php
defined('BASEPATH') or exit('No direct script access allowed');

class DashboardStatusColor extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => 'Dashboard Status Colors', 'title2' => 'Dashboard Status Colors', 'styles' => []]);
        $this->load->view('dashboard-status-colors');
        $this->load->view('footer', ['scripts' => ['design/js/dashboard-status-color.js']]);
    }

    public function ajax_list()
    {
        $query = $this->db->select('dashboard_status_colors.*')
            ->from('dashboard_status_colors')
            ->get();

        if (!$query) {
            echo json_encode(['error' => 'Database query failed']);
            return;
        }

        $list = $query->result();

        $data = [];

        foreach ($list as $item) {
            $data[] = [
                'id' => $item->id,
                'name' => $item->status_name,
                'color' => $item->status_color,
                'action' => '', // JS handles this
            ];
        }

        // Wrap the data array in a 'data' key for DataTables
        echo json_encode(['data' => $data]);
    }

    public function add()
    {

        $this->db->set('status_color', $this->input->post('status_color'));
        // $this->db->set( 'name', $this->input->post( 'asset-type-name' ) );
        $this->db->set('status_name', $this->input->post('status_name'));
        // Use the actual asset_id field
        if ($this->db->insert('dashboard_status_colors')) {
            redirect('/DashboardStatusColor/index?message=Asset Types Color added successfully!');
        } else {

            // Handle the case where the asset type name doesn't exist
            redirect("/DashboardStatusColor/index?message=Asset Type not found!");
        }
    }


    public function update()
    {
        if ($this->input->post('edit_id')) {

            $this->db->where("id", $this->input->post('edit_id'));
            if ($this->db->update('dashboard_status_colors', ['status_name' => $this->input->post('status_name'), 'status_color' => $this->input->post('status_color')])) {
            } else {
                var_dump($this->db->last_query());
                exit();
            }
            die(redirect("/DashboardStatusColor?message= Asset Types Color updated successfully!"));
        }
    }

    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete('dashboard_status_colors');
            die(redirect('/DashboardStatusColor?message= Asset Types Color deleted successfully!'));
        }
    }
}
