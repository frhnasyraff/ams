<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Wastage_types extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_wastage_types")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Wastage types", "styles" => []]);
        $this->load->view('wastage_types', []);
        $this->load->view('footer', ['scripts' => ['design/js/wastage_types-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_wastage_types")) {

            $query = $this->db->get_where('wastage_types', ["wastage_type_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Wastage type - " . $info[0]->wastage_type_name]);
                $this->load->view('wastage_type-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("wastage_types?error=Wastage type not found");
            }
        } else {
            redirect("wastage_types?error=Wastage type not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("wastage_types", ["wastage_type_name", "description"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("wastage_types", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_wastage_types") && $this->input->post('id')) {
            die($this->steve->active_toggle("wastage_types", "wastage_type_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_wastage_types") && $this->input->post('id')) {
            $this->db->where("commodity_id", intval($this->input->post('id')));
            if ($this->db->delete("wastage_types")) {
                redirect("wastage_types/index?message=Commodity was deleted successfully.");
            } else {
                redirect("wastage_types/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("wastage_types/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_wastage_types") && $this->input->post('id')) {

            $this->db->set("wastage_type_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("wastage_type_id", intval($this->input->post('id')));

            if ($this->db->update("wastage_types")) {
                $this->logs->add("wastage_types", $this->input->post('id'), "OPERATION_TYPE_UPDATED", $_POST);
                redirect("wastage_types/index?message=Wastage type was updated successfully.");
            } else {
                redirect("wastage_types/index?error=Update failed.");
            }
        } else {
            redirect("wastage_types/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_wastage_types") && $this->input->post('name')) {
            $this->db->set('wastage_type_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('wastage_types')) {
                $this->logs->add("wastage_types", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
                redirect("wastage_types?message=Added wastage type successfully");
            } else {
                redirect("wastage_types?error=Adding Wastage type failed");
            }
        } else {
            redirect("wastage_types?error=No permission to add Wastage types");
        }
    }
}
