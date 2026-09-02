<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Branch_office_lists extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_branch_office_lists")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Branch Office Lists", "styles" => []]);
        $this->load->view('branch_office_lists', []);
        $this->load->view('footer', ['scripts' => ['design/js/branch_office_lists-list.js']]);
    }

    public function info()
    {

        if ($this->input->get('id') && $this->user_model->has_perm("edit_branch_office_lists")) {
            $query = $this->db->get_where('branch_office', ["branch_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Branch Office Lists - " . $info[0]->branch_name]);
                $this->load->view('branch_office_lists-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("branch_office_lists?error=Branch office lists not found");
            }
        } else {
            redirect("branch_office_lists?error=Branch office lists not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("branch_office", ["branch_name", "branch_address", "branch_code"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("id as id, CONCAT(commodity_code, ' (', branch_address, ')') as label, CONCAT(commodity_code, ' - ', branch_address) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("branch_address", $this->input->get("term"))->group_end()->get_where("branch_office", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("branch_office_lists") && $this->input->post('id')) {
            die($this->steve->active_toggle("branch_office", "branch_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("delete_branch_office_lists") && $this->input->post('id')) {
            $this->db->where("id", intval($this->input->post('id')));
            if ($this->db->delete("branch_office")) {
                redirect("branch_office_lists/index?message=Commodity was deleted successfully.");
            } else {
                redirect("branch_office_lists/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("branch_office_lists/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_branch_office_lists") && $this->input->post('id')) {

            $this->db->set("branch_name", $this->input->post('branch_name'));
            $this->db->set("branch_code", $this->input->post('branch_code'));
            $this->db->set("branch_address", $this->input->post('branch_address'));
            $this->db->where("branch_id", intval($this->input->post('id')));

            if ($this->db->update("branch_office")) {
                // $this->logs->add("branch_office", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("branch_office_lists/index?message=Branch office lists was updated successfully.");
            } else {
                redirect("branch_office_lists/index?error=Update failed.");
            }
        } else {
            redirect("branch_office_lists/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_branch_office_lists") && $this->input->post('branch_name')) {
            $this->db->set('branch_name', $this->input->post('branch_name'));
            $this->db->set('branch_code', $this->input->post('branch_code'));
            $this->db->set('branch_address', $this->input->post('branch_address'));
            if ($this->db->insert('branch_office')) {
                // $this->logs->add("branch_office", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("branch_office_lists?message=Added Branch office lists successfully");
            } else {
                redirect("branch_office_lists?error=Adding Branch Office lists failed");
            }
        } else {
            redirect("branch_office_lists?error=No permission to add Branch office list");
        }
    }
}
