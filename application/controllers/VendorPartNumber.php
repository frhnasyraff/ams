<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VendorPartNumber extends CI_Controller
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
        $this->load->view('header', ['title' => "Vendor Part Numbers", 'title2' => "Vendor Part Numbers", "styles" => []]);
        $this->load->view('vendor-part-number', []);
        $this->load->view('footer', ['scripts' => ['design/js/vendor_part_number.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("vendor_part_number", ["id", "part_number"]));
    }

    public function add()
    {

        // Insert the asset type color
        $this->db->set('id', $this->input->post('id'));
        $this->db->set('part_number', $this->input->post('part_number'));  // Use the actual part_number field

        // Perform the insertion into 'vendor_part_number'
        $this->db->insert('vendor_part_number');

        // Redirect with a success message
        redirect("/VendorPartNumber/index?message=Vendor Part Number added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id')) {
            $this->db->where("id", $this->input->post('id'));
            $this->db->update('vendor_part_number', ['part_number' => $this->input->post('part_number_edit')]);
            die(redirect("/VendorPartNumber?message= Vendor Part Number updated successfully!"));
        }
    }

    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("vendor_part_number");
            die(redirect("/VendorPartNumber?message= Vendor Part Number deleted successfully!"));
        }
    }
}
