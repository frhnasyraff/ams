<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VendorManufacturingNumber extends CI_Controller
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
        $this->load->view('header', ['title' => "Manufacturer Numbers", 'title2' => "Manufacturer Numbers", "styles" => []]);
        $this->load->view('vendor-manufacturing-number', []);
        $this->load->view('footer', ['scripts' => ['design/js/vendor-manufacturing-number.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("vendor_manufacturing_number", ["id", "manufacturer_name", "manufacturer_number"]));
    }

    public function add()
    {


        // $this->db->set('id', $this->input->post('id'));
        $this->db->set('manufacturer_name', $this->input->post('manufacturer_name'));
        $this->db->set('manufacturer_number', $this->input->post('manufacturer_number'));


        try {
            if ($this->db->insert('vendor_manufacturing_number')) {
                redirect("/VendorManufacturingNumber?message=Vendor Manufacturing Number added successfully!");
            }
        } catch (Exception $e) {
            redirect("/VendorManufacturingNumber?error=Adding Manufacturing number failed!");
        }
    }


    public function update()
    {
        if ($this->input->post('id')) {

            $update_data = [
                'manufacturer_name' => $this->input->post('manufacturer_name_edit'),
                'manufacturer_number' => $this->input->post('manufacturer_number_edit')
            ];


            $this->db->where("id", $this->input->post('id'));
            $this->db->update('vendor_manufacturing_number', $update_data);


            redirect("/VendorManufacturingNumber?message=Vendor Manufacturing Number updated successfully!");
        }
    }


    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("vendor_manufacturing_number");
            die(redirect("/VendorManufacturingNumber?message= Vendor Manufacturing Number deleted successfully!"));
        }
    }
}
