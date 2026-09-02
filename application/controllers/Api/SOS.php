<?php
defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;

class SOS extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if (!verifyJWT()) {
        //     errorResponse('missing or invalid token', [], 401);
        // }
        $this->load->library('form_validation');
    }


    public function addSOS()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required|integer");
        $this->form_validation->set_rules("type", "type", "required");
        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $type = $this->input->post('type');

        $this->db->set('order_id', $order_id);
        $this->db->set('type', $type);
        $this->db->set('status', 0);
        $this->db->set('created_at', date('Y-m-d'));
        $this->db->insert('sos');

        //change truck status to Repair
        // if($type=='Truck Breakdown' || $type=='Pupspakom Inspection'){

            $orderDriver = $this->db->select('truck_id')->from('order_drivers')->where('order_id', $order_id)->get()->row();
            $this->db->set('equipment_status');
            $this->db->where('equipment_id', $orderDriver->truck_id);
            $this->db->update('equipments', [
                'equipment_status' => 'Repair'
            ]);
            
        // }        

        successResponse('SOS added', [] , 200);
    }
}
