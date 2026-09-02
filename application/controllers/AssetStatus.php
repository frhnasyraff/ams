<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssetStatus extends CI_Controller
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
        $this->load->view('header', ['title' => "Asset Statuses", 'title2' => "Asset Statuses", "styles" => []]);
        $this->load->view('asset-status', []);
        $this->load->view('footer', ['scripts' => ['design/js/asset-status.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("asset_status", ["id", "name"]));
    }

    public function add()
    {

        $this->db->set('name', $this->input->post('name'));

        $this->db->insert('asset_status');

        // Redirect with a success message
        redirect("/AssetStatus/index?message=Asset Status added successfully!");
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
            $this->db->update('asset_status', $data);

            // Redirect with a success message
            redirect("/AssetStatus?message=Asset Status updated successfully!");
        }
    }



    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("asset_status");
            die(redirect("/AssetStatus?message= Asset Status deleted successfully!"));
        }
    }
}
