<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssetTypesColors extends CI_Controller
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
        $assets = $this->db->select('name, active')
            ->from('asset_types')
            ->where('active', '1')
            ->get()
            ->result();
        $this->load->view('header', ['title' => "Asset Type Colors", 'title2' => "Asset Type Colors", "styles" => []]);
        $this->load->view('asset-type-colors', ['assets' => $assets]);
        $this->load->view('footer', ['scripts' => ['design/js/asset-type-colors-list.js']]);
    }

    public function ajax_list()
    {
        $query = $this->db->select('asset_type_color.*, 
        asset_types.name, 
        asset_type_color.color')
            ->from('asset_type_color')
            ->join('asset_types', 'asset_type_color.asset_type_id = asset_types.asset_id')
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
                'name' => $item->name,
                'color' => $item->color,
                'action' => '', // JS handles this
            ];
        }

        // Wrap the data array in a 'data' key for DataTables
        echo json_encode(['data' => $data]);
    }



    public function add()
    {
        // Fetch the asset_id by name from the 'asset_types' table
        $asset = $this->db->select('asset_id', 'name')
            ->from('asset_types')
            ->where('name', $this->input->post('asset-type-name'))
            ->get()
            ->row();  // Use row() if you're expecting a single record

        // Check if the asset was found
        if ($asset) {
            // Insert the asset type color
            $this->db->set('color', $this->input->post('asset-type-color'));
            // $this->db->set('name', $this->input->post('asset-type-name'));
            $this->db->set('asset_type_id', $asset->asset_id);  // Use the actual asset_id field

            // Perform the insertion into 'asset_type_color'
            $this->db->insert('asset_type_color');


            // Redirect with a success message
            redirect("/AssetTypesColors/index?message=Asset Types Color added successfully!");
        } else {
            // Handle the case where the asset type name doesn't exist
            redirect("/AssetTypesColors/index?message=Asset Type not found!");
        }
    }


    public function update()
    {
        if ($this->input->post('asset_type_color_id')) {
            $this->db->where("id", $this->input->post('asset_type_color_id'));
            $this->db->update('asset_type_color', ['color' => $this->input->post('asset_type_color_edit')]);
            die(redirect("/AssetTypesColors?message= Asset Types Color updated successfully!"));
        }
    }

    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("asset_type_color");
            die(redirect("/AssetTypesColors?message= Asset Types Color deleted successfully!"));
        }
    }
}
