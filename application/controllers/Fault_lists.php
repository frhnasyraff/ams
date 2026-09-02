<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Fault_lists extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_fault_lists")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Fault Lists", "styles" => []]);
        $this->load->view('fault_lists', []);
        $this->load->view('footer', ['scripts' => ['design/js/fault_lists-list.js']]);
    }

    public function info()
    {

        if ($this->input->get('id') && $this->user_model->has_perm("edit_fault_lists")) {
            $query = $this->db->get_where('fault_lists', ["fault_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Fault Lists - " . $info[0]->fault_name]);
                $this->load->view('fault_lists-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("fault_lists?error=Fault lists not found");
            }
        } else {
            redirect("fault_lists?error=Fault lists not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("fault_lists", ["fault_name"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("id as id, CONCAT(commodity_code, ' (', fault_name, ')') as label, CONCAT(commodity_code, ' - ', fault_name) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("fault_name", $this->input->get("term"))->group_end()->get_where("fault_lists", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("fault_lists") && $this->input->post('id')) {
            die($this->steve->active_toggle("fault_lists", "fault_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_fault_lists") && $this->input->post('id')) {
            $this->db->where("id", intval($this->input->post('id')));
            if ($this->db->delete("fault_lists")) {
                redirect("fault_lists/index?message=Commodity was deleted successfully.");
            } else {
                redirect("fault_lists/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("fault_lists/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_fault_lists") && $this->input->post('id')) {

            $this->db->set("fault_name", $this->input->post('fault_name'));
            $this->db->where("fault_id", intval($this->input->post('id')));

            if ($this->db->update("fault_lists")) {
                $this->logs->add("fault_lists", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("fault_lists/index?message=Fault lists was updated successfully.");
            } else {
                redirect("fault_lists/index?error=Update failed.");
            }
        } else {
            redirect("fault_lists/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_fault_lists") && $this->input->post('fault_name')) {
            $this->db->set('fault_name', $this->input->post('fault_name'));
            if ($this->db->insert('fault_lists')) {
                $this->logs->add("fault_lists", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("fault_lists?message=Added Fault lists successfully");
            } else {
                redirect("fault_lists?error=Adding Fault lists failed");
            }
        } else {
            redirect("fault_lists?error=No permission to add Fault list");
        }
    }
}
