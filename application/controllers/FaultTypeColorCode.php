<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FaultTypeColorCode extends CI_Controller
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

        $this->load->view('header', ['title' => "Fault Colors", 'title2' => "Fault Colors", "styles" => []]);
        $this->load->view('fault-type-color-code', []);
        $this->load->view('footer', ['scripts' => ['design/js/fault-type-color-code.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("fault_type_color_code", ["fault_type", "color"]));
    }

    public function add()
    {
        $this->db->set('color', $this->input->post('color'));
        $this->db->set('fault_type', $this->input->post('fault_type'));

        $this->db->insert('fault_type_color_code');

        // var_dump($this->db->last_query());
        // exit();

        redirect("/FaultTypeColorCode/index?message=Fault Type Color Code added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id_edit')) {

            // Update the color for the selected ID
            $this->db->where('id', $this->input->post('id_edit')); // Adding the where condition for the ID
            $this->db->update('fault_type_color_code', ['color' => $this->input->post('color_edit'), 'fault_type' => $this->input->post('fault_type_edit')]); // Update the color

            // Redirect with a success message
            redirect("/FaultTypeColorCode?message=Fault Type Color Code updated successfully!");
        } else {
            // Redirect if the fault_type was not found
            redirect("/FaultTypeColorCode?message=Fault Type not found!");
        }
    }


    public function delete()
    {
        if ($this->input->get('fault_type')) {
            // Get the fault_type from the URL
            $fault_type = $this->input->get('fault_type');

            // Check if the record exists for the given fault_type
            $record = $this->db->select('id')
                ->from('fault_type_color_code')
                ->where('fault_type', $fault_type)
                ->get()
                ->row();

            if ($record) {
                // If the record exists, delete it
                $this->db->where('fault_type', $fault_type);
                $this->db->delete('fault_type_color_code');

                // Redirect with a success message
                redirect("/FaultTypeColorCode?message=Fault Type Color Code deleted successfully!");
            } else {
                // If the record does not exist, redirect with an error message
                redirect("/FaultTypeColorCode?message=Fault Type not found!");
            }
        } else {
            // Redirect if no fault_type is provided
            redirect("/FaultTypeColorCode?message=No Fault Type provided!");
        }
    }
}
