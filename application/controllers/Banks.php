<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Banks extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_banks")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Banks", "styles" => []]);
        $this->load->view('banks', []);
        $this->load->view('footer', ['scripts' => ['design/js/banks-list.js']]);
    }

    public function info()
    {

        if ($this->input->get('id') && $this->user_model->has_perm("edit_banks")) {
            $query = $this->db->get_where('banks', ["bank_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Bank - " . $info[0]->name]);
                $this->load->view('banks-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("banks?error=Bank not found");
            }
        } else {
            redirect("banks?error=Bank not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("banks", ["name"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("id as id, CONCAT(commodity_code, ' (', name, ')') as label, CONCAT(commodity_code, ' - ', name) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("name", $this->input->get("term"))->group_end()->get_where("banks", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("banks") && $this->input->post('id')) {
            die($this->steve->active_toggle("banks", "bank_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_banks") && $this->input->post('id')) {
            $this->db->where("id", intval($this->input->post('id')));
            if ($this->db->delete("banks")) {
                redirect("banks/index?message=Commodity was deleted successfully.");
            } else {
                redirect("banks/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("banks/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_banks") && $this->input->post('id')) {

            $this->db->set("name", $this->input->post('name'));
            $this->db->where("bank_id", intval($this->input->post('id')));

            if ($this->db->update("banks")) {
                $this->logs->add("banks", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("banks/index?message=Bank was updated successfully.");
            } else {
                redirect("banks/index?error=Update failed.");
            }
        } else {
            redirect("banks/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_banks") && $this->input->post('name')) {
            $this->db->set('name', $this->input->post('name'));
            if ($this->db->insert('banks')) {
                $this->logs->add("banks", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("banks?message=Added Bank successfully");
            } else {
                redirect("banks?error=Adding Bank failed");
            }
        } else {
            redirect("banks?error=No permission to add bank");
        }
    }
}
