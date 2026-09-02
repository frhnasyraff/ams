<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Land_field extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_masters_companies")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Land Field", 'title2' => "Land Field Location", "styles" => []]);
        $branch_office = $this->db->select('branch_id, branch_name')->from('branch_office')->where('active', 1)->get()->result();
        $this->load->view('land_field', [
            'branch_office' => $branch_office
        ]);
        $this->load->view('footer', ['scripts' => ['design/js/land-field.js']]);
    }

    public function info()
    {
        if ($this->input->get('id')) {
            $query = $this->db->get_where('land_field_location', ["land_field_id" => $this->steve->id_decode()]);
            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Land Field- " . $info[0]->location_name]);
                $branch_office = $this->db->select('branch_id, branch_name')->from('branch_office')->where('active', 1)->get()->result();
                $this->load->view('land_field-info', [
                    'info' => $info[0],
                    'branch_office' => $branch_office
                ]);
                $this->load->view('footer');
            } else {
                redirect("land_field?error=Location not found");
            }
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("land_field_location", ["land_field_id", "location_name", "address", "latitude", "longitude"]));
    }

    public function add()
    {
        $this->db->set('location_name ', $this->input->post('location_name'));
        $this->db->set('branch_id', $this->input->post('branch_id'));
        $this->db->set('address', $this->input->post('address'));
        $this->db->set('latitude', $this->input->post('latitude'));
        $this->db->set('longitude ', $this->input->post('longitude'));
        $this->db->set('created_at ', date('Y-m-d H:i:s'));
        if ($this->db->insert('land_field_location')) {
            redirect("land_field?message=Adding Land Field Location sucessfully");
        } else {
            redirect("land_field?errorAdding Land Field Location failed");
        }
    }


    public function update()
    {
        if ($this->input->post('id')) {
            $this->db->set('location_name ', $this->input->post('location_name'));
            $this->db->set('branch_id', $this->input->post('branch_id'));
            $this->db->set('address', $this->input->post('address'));
            $this->db->set('latitude', $this->input->post('latitude'));
            $this->db->set('longitude ', $this->input->post('longitude'));
            $this->db->where("land_field_id", intval($this->input->post('id')));

            if ($this->db->update("land_field_location")) {
                redirect("land_field/index?message=Land Field was updated successfully.");
            } else {
                redirect("land_field/index?error=Update failed.");
            }
        }
    }

    public function delete()
    {
        $this->db->where("land_field_id", intval($this->input->get('id')));
        if ($this->db->delete("land_field_location")) {
            redirect("land_field/index?message=Field location was deleted successfully.");
        } else {
            redirect("land_field/index?error=Field location deletion failed.");
        }
    }
}
