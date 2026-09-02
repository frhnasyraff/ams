<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;

class ReplacedBin extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
    }

    public function list()
    {
        $this->form_validation->set_rules('order_id', 'Order Id', 'required');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');

        // replaced assets
        $replaced_assets = $this->db->select('*')
            ->from('replaced_assets')
            ->where('order_id', $order_id)
            ->get()
            ->result();

        foreach ($replaced_assets as $key => $asset) {
            $equipments_asset = $this->db->select('*')
                ->from('equipments_asset')
                ->where('equipment_registration', $asset->reg_no)
                ->get()
                ->row();
            $replaced_assets[$key]->equipment_name = $equipments_asset->equipment_name;
        }

        successResponse('replaced assets list', $replaced_assets);
    }

    public function insert()
    {
        // request validation 
        $this->form_validation->set_rules('order_id', 'Order Id', 'required');
        $this->form_validation->set_rules('reg_no', 'Registration Number', 'required');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $reg_no = $this->input->post('reg_no');

        // check if order_id and reg no already exits
        $qry = $this->db->select('*')
            ->from('replaced_assets')
            ->where('order_id', $order_id)
            ->where('reg_no', $reg_no)
            ->get();

        if ($qry->num_rows() > 0) {
            errorResponse('already exists', []);
        }

        // mark this registration number equipment as "Available"
        $this->db->where('equipment_registration', $reg_no);
        $this->db->update('equipments_asset', ['equipment_status' => 'Available']);

        $this->db->set('order_id', $order_id);
        $this->db->set('reg_no', $reg_no);
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('replaced_assets');

        successResponse('inserted', []);
    }

    public function delete()
    {
        $this->form_validation->set_rules('order_id', 'Order Id', 'required');
        $this->form_validation->set_rules('reg_no', 'Registration Number', 'required');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $reg_no = $this->input->post('reg_no');

        // // mark this registration number equipment as "In use"
        $this->db->where('equipment_registration', $reg_no);
        $this->db->update('equipments_asset', ['equipment_status' => 'In use']);

        $this->db->where("order_id", $order_id);
        $this->db->where("reg_no", $reg_no);
        $this->db->delete("replaced_assets");


        successResponse('deleted', []);
    }
}
