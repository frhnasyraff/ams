<?php
defined('BASEPATH') or exit('No direct script access allowed');

class MaintenenceType extends CI_Controller
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
        $this->load->view('header', ['title' => "Maintenence Type", 'title2' => "Maintenence Type", "styles" => []]);
        $this->load->view('maintenence-type', []);
        $this->load->view('footer', ['scripts' => ['design/js/maintenence-type.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("maintenence_type", ["id", "name"]));
    }

    public function add()
    {

        $this->db->set('name', $this->input->post('name'));

        $this->db->insert('maintenence_type');

        // Redirect with a success message
        redirect("/MaintenenceType/index?message=MaintenencevType added successfully!");
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
            $this->db->update('maintenence_type', $data);

            // Redirect with a success message
            redirect("/MaintenenceType?message=MaintenencevType updated successfully!");
        }
    }



    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("maintenence_type");
            die(redirect("/MaintenenceType?message= MaintenencevType deleted successfully!"));
        }
    }
}
