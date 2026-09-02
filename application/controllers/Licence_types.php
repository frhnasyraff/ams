<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Licence_types extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_licence_types")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Licence types", "styles" => []]);
        $this->load->view('licence_types', []);
        $this->load->view('footer', ['scripts' => ['design/js/licence_types-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_licence_types")) {

            $query = $this->db->get_where('licence_type', ["licence_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Licence type - " . $info[0]->licence_name]);
                $this->load->view('licence_types-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("licence_types?error=Licence type not found");
            }
        } else {
            redirect("licence_types?error=Licence type not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("licence_type", ["licence_name"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', licence_name, ')') as label, CONCAT(commodity_code, ' - ', licence_name) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("licence_name", $this->input->get("term"))->group_end()->get_where("licence_type", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_licence_types") && $this->input->post('id')) {
            die($this->steve->active_toggle("licence_type", "licence_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("delete_licence_types") && $this->input->post('id')) {
            $this->db->where("commodity_id", intval($this->input->post('id')));
            if ($this->db->delete("licence_type")) {
                redirect("licence_types/index?message=Commodity was deleted successfully.");
            } else {
                redirect("licence_types/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("licence_types/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_licence_types") && $this->input->post('id')) {

            $this->db->set("licence_name", $this->input->post('licence_name'));
            $this->db->where("licence_id", intval($this->input->post('id')));

            if ($this->db->update("licence_type")) {
                $this->logs->add("licence_type", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("licence_types/index?message=Licencetype was updated successfully.");
            } else {
                redirect("licence_types/index?error=Update failed.");
            }
        } else {
            redirect("licence_types/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_licence_types") && $this->input->post('licence_name')) {
            $this->db->set('licence_name', $this->input->post('licence_name'));
            if ($this->db->insert('licence_type')) {
                $this->logs->add("licence_type", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("licence_types?message=Added licence type successfully");
            } else {
                redirect("licence_types?error=Adding licence type failed");
            }
        } else {
            redirect("licence_types?error=No permission to add licence types");
        }
    }
}
