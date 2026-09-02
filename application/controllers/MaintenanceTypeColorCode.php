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
        $this->load->view('footer', ['scripts' => ['design/js/maintenance-type-color-code.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("maintenance_type_color_code", ["maintenance_type", "color"]));
    }

    public function add()
    {
        $this->db->set('color', $this->input->post('color'));

        $this->db->set('maintenance_type', $this->input->post('maintenance_type'));

        $this->db->insert('maintenance_type_color_code');

        redirect("/MaintenanceTypeColorCode/index?message=Maintenance Type Color Code added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id_edit')) {


            // Update the color for the selected ID
            $this->db->where('id', $this->input->post('id_edit')); // Adding the where condition for the ID
            $this->db->update('maintenance_type_color_code', ['color' => $this->input->post('color_edit'), 'maintenance_type' => $this->input->post('maintenance_type_edit')]); // Update the color

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
