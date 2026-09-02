<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Insurance_companies extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_insurance_companies")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Insurance Companies", "styles" => []]);
        $this->load->view('insurance_companies', []);
        $this->load->view('footer', ['scripts' => ['design/js/insurance_companies-list.js']]);
    }

    public function info()
    {

        if ($this->input->get('id') && $this->user_model->has_perm("edit_insurance_companies")) {
            $query = $this->db->get_where('insurance_companies', ["insurance_company_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Insurance company - " . $info[0]->name]);
                $this->load->view('insurance_companies-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("insurance_companies?error=Insurance company not found");
            }
        } else {
            redirect("insurance_companies?error=Insurance company not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("insurance_companies", ["name", "address"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("id as id, CONCAT(commodity_code, ' (', address, ')') as label, CONCAT(commodity_code, ' - ', address) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("address", $this->input->get("term"))->group_end()->get_where("insurance_companies", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_insurance_companies") && $this->input->post('id')) {
            die($this->steve->active_toggle("insurance_companies", "insurance_company_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_insurance_companies") && $this->input->post('id')) {
            $this->db->where("id", intval($this->input->post('id')));
            if ($this->db->delete("insurance_companies")) {
                redirect("insurance_companies/index?message=Commodity was deleted successfully.");
            } else {
                redirect("insurance_companies/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("insurance_companies/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_insurance_companies") && $this->input->post('id')) {

            $this->db->set("name", $this->input->post('name'));
            $this->db->set("address", $this->input->post('address'));
            $this->db->where("insurance_company_id", intval($this->input->post('id')));

            if ($this->db->update("insurance_companies")) {
                $this->logs->add("insurance_companies", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("insurance_companies/index?message=Insurance company was updated successfully.");
            } else {
                redirect("insurance_companies/index?error=Update failed.");
            }
        } else {
            redirect("insurance_companies/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_insurance_companies") && $this->input->post('name')) {
            $this->db->set('name', $this->input->post('name'));
            $this->db->set('address', $this->input->post('address'));
            if ($this->db->insert('insurance_companies')) {
                $this->logs->add("insurance_companies", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("insurance_companies?message=Added insurance company successfully");
            } else {
                redirect("insurance_companies?error=Adding Insurance company failed");
            }
        } else {
            redirect("insurance_companies?error=No permission to add Insurance company");
        }
    }
}
