<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ManagedByAddData extends CI_Controller
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
        $this->load->view('header', ['title' => "Managed By", 'title2' => "Managed By", "styles" => []]);
        $this->load->view('managed-by-add-data', []);
        $this->load->view('footer', ['scripts' => ['design/js/managed-by-add-data.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("managed_by_add_data", ["id", "name"]));
    }

    public function add()
    {

        // Insert the asset type color
        $this->db->set('id', $this->input->post('id'));
        $this->db->set('name', $this->input->post('name'));  // Use the actual name field

        // Perform the insertion into 'managed_by_add_data'
        $this->db->insert('managed_by_add_data');

        // Redirect with a success message
        redirect("/ManagedByAddData/index?message=Managed By Add Data added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id')) {
            $this->db->where("id", $this->input->post('id'));
            $this->db->update('managed_by_add_data', ['name' => $this->input->post('name_edit')]);
            die(redirect("/ManagedByAddData?message= Managed By Add Data updated successfully!"));
        }
    }

    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("managed_by_add_data");
            die(redirect("/ManagedByAddData?message= Managed By Add Data deleted successfully!"));
        }
    }
}
