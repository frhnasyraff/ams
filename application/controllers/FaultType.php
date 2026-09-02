<?php
defined('BASEPATH') or exit('No direct script access allowed');

class FaultType extends CI_Controller
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
        $this->load->view('header', ['title' => "Fault Type", 'title2' => "Fault Type", "styles" => []]);
        $this->load->view('fault-type', []);
        $this->load->view('footer', ['scripts' => ['design/js/fault-type.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("fault_type", ["id", "name"]));
    }

    public function add()
    {

        $this->db->set('name', $this->input->post('name'));

        $this->db->insert('fault_type');

        // Redirect with a success message
        redirect("/FaultType/index?message=Fault Type added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id')) {
            // Prepare the data for updating
            $data = [
                'name' => $this->input->post('name_edit')
            ];

            // Perform the update query
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('fault_type', $data);

            // Redirect with a success message
            redirect("/FaultType?message=Fault Type updated successfully!");
        }
    }



    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("fault_type");
            die(redirect("/FaultType?message= Fault Type deleted successfully!"));
        }
    }
}
