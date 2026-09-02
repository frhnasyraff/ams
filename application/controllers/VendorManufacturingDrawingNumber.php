<?php
defined('BASEPATH') or exit('No direct script access allowed');

class VendorManufacturingDrawingNumber extends CI_Controller
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
        $this->load->view('header', ['title' => "Drawing Numbers", 'title2' => "Drawing Numbers", "styles" => []]);
        $this->load->view('vendor-manufacturer-drawing-number', []);
        $this->load->view('footer', ['scripts' => ['design/js/vendor-manufacturer-drawing-number.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("vendor_manufacturing_drawing_number", ["id", "drawing_number"]));
    }

    public function add()
    {


        $this->db->set('id', $this->input->post('id'));
        $this->db->set('drawing_number', $this->input->post('drawing_number'));

        $this->db->insert('vendor_manufacturing_drawing_number');

        // Redirect with a success message
        redirect("/VendorManufacturingDrawingNumber/index?message=Vendor Manufacturing Drawing Number added successfully!");
    }


    public function update()
    {
        if ($this->input->post('id')) {
            // Prepare the data for updating
            $data = [
                'drawing_number' => $this->input->post('drawing_number_edit')
            ];

            // Perform the update query
            $this->db->where('id', $this->input->post('id'));
            $this->db->update('vendor_manufacturing_drawing_number', $data);

            // Redirect with a success message
            redirect("/VendorManufacturingDrawingNumber?message=Vendor Manufacturing Drawing Number updated successfully!");
        }
    }



    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("vendor_manufacturing_drawing_number");
            die(redirect("/VendorManufacturingDrawingNumber?message= Vendor Manufacturing Drawing Number deleted successfully!"));
        }
    }
}
