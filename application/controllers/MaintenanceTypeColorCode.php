<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaintenanceTypeColorCode extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => "Maintenance Colors", 'title2' => "Maintenance Colors", "styles" => []]);
        $this->load->view('maintenance-type-color-code', []);
        $this->load->view('footer', ['scripts' => ['design/js/maintenance-type-color-code.js?v=5']]);
    }

    public function ajax_list()
    {
        // Map the public color field to color_code, including saved table ordering.
        $search = $this->input->post('search');
        $searchValue = is_array($search) ? trim((string) ($search['value'] ?? '')) : '';
        $order = $this->input->post('order');
        $orderIndex = (int) ($order[0]['column'] ?? 0);
        $orderColumns = [0 => 'maintenance_type', 1 => 'color_code'];
        $orderColumn = $orderColumns[$orderIndex] ?? 'maintenance_type';
        $orderDirection = strtolower((string) ($order[0]['dir'] ?? 'asc')) === 'desc' ? 'desc' : 'asc';
        $start = max(0, (int) $this->input->post('start'));
        $length = (int) ($this->input->post('length') ?? 10);

        $total = $this->db->count_all('maintenance_type_color_code');
        $this->db->from('maintenance_type_color_code');
        if ($searchValue !== '') {
            $this->db->group_start()
                ->like('maintenance_type', $searchValue)
                ->or_like('color_code', $searchValue)
                ->group_end();
        }
        $filtered = $this->db->count_all_results('', false);
        $this->db->select('id, maintenance_type, color_code AS color')
            ->order_by($orderColumn, $orderDirection)
            ->order_by('id', 'asc');
        if ($length !== -1) {
            $this->db->limit(max(1, min($length, 1000)), $start);
        }
        $rows = $this->db->get()->result();

        return $this->output->set_content_type('application/json')->set_output(json_encode([
            'draw' => (int) $this->input->post('draw'),
            'recordsTotal' => $total,
            'recordsFiltered' => $filtered,
            'data' => $rows,
        ]));
    }

    public function add()
    {
        $this->db->set('color_code', $this->input->post('color'));

        $this->db->set('maintenance_type', $this->input->post('maintenance_type'));

        $this->db->insert('maintenance_type_color_code');

        redirect("/MaintenanceTypeColorCode/index?message=Maintenance Type Color Code added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id_edit')) {


            // Update the color for the selected ID
            $this->db->where('id', $this->input->post('id_edit')); // Adding the where condition for the ID
            $this->db->update('maintenance_type_color_code', ['color_code' => $this->input->post('color_edit'), 'maintenance_type' => $this->input->post('maintenance_type_edit')]); // Update the color

            // Redirect with a success message
            redirect("/MaintenanceTypeColorCode?message=Maintenance Type Color Code updated successfully!");
        } else {
            // Redirect if the maintenance_type was not found
            redirect("/MaintenanceTypeColorCode?message=Maintenance Type not found!");
        }
    }


    public function delete()
    {
        if ($this->input->get('maintenance_type')) {
            // Get the maintenance_type from the URL
            $maintenance_type = $this->input->get('maintenance_type');

            // Check if the record exists for the given maintenance_type
            $record = $this->db->select('id')
                ->from('maintenance_type_color_code')
                ->where('maintenance_type', $maintenance_type)
                ->get()
                ->row();

            if ($record) {
                // If the record exists, delete it
                $this->db->where('maintenance_type', $maintenance_type);
                $this->db->delete('maintenance_type_color_code');

                // Redirect with a success message
                redirect("/MaintenanceTypeColorCode?message=Maintenance Type Color Code deleted successfully!");
            } else {
                // If the record does not exist, redirect with an error message
                redirect("/MaintenanceTypeColorCode?message=Maintenance Type not found!");
            }
        } else {
            // Redirect if no maintenance_type is provided
            redirect("/MaintenanceTypeColorCode?message=No Maintenance Type provided!");
        }
    }
}


