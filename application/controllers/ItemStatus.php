<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ItemStatus extends CI_Controller
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
        $this->load->view('header', ['title' => "Component Statuses", 'title2' => "Component Statuses", "styles" => []]);
        $this->load->view('item-status', []);
        $this->load->view('footer', ['scripts' => ['design/js/item-status.js']]);
    }

    public function ajax_list()
    {
        $query = $this->db->select('*')->from('item_status')->get()->result();

        echo json_encode([
            'data' => $query,
        ]);
    }

    public function add()
    {

        $this->db->set('name', $this->input->post('name'));

        $this->db->insert('item_status');

        // Redirect with a success message
        redirect("/ItemStatus/index?message=Asset Status added successfully!");
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
            $this->db->update('item_status', $data);

            // Redirect with a success message
            redirect("/ItemStatus?message=Asset Status updated successfully!");
        }
    }



    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("item_status");
            die(redirect("/ItemStatus?message= Asset Status deleted successfully!"));
        }
    }
}
