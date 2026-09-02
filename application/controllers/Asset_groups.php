<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_groups extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_equipment_groups")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Asset Groups", 'title2' => "Asset Groups", "styles" => [
            "design/css/custom-datatable.css"
        ]]);
        $this->load->view('asset-groups-list', []);
        $this->load->view('footer', ['scripts' => ['design/js/asset-groups-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_equipment_groups")) {

            $query = $this->db->get_where('equipment_groups_asset', ["equipment_group_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $equipments = $this->db->join("equipments_asset", "equipments_asset.equipment_id = equipment_group.equipment_id", "left")->get_where('equipment_group', ["equipment_group_id" => $this->steve->id_decode()])->result();

                $this->load->view('header', ['title' => "Asset group - " . $info[0]->equipment_group_name]);
                $this->load->view('asset-group-info', ['info' => $info[0], "equipments" => $equipments]);
                $this->load->view('footer');
            } else {
                redirect("equipments?error=Asset group not found");
            }
        } else {
            redirect("equipments?error=Asset group not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("equipment_groups_asset", ["equipment_group_name", "equipment_group_code"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("operation_types", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_equipment_groups") && $this->input->post('id')) {
            die($this->steve->active_toggle("equipment_groups_asset", "equipment_group_id"));
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_equipment_groups") && $this->input->post('id')) {
            $this->db->set("equipment_group_name", $this->input->post('name'));
            $this->db->set('equipment_group_code', $this->input->post('code'));
            $this->db->set('equipment_group_notes', $this->input->post('notes'));
            $this->db->where("equipment_group_id", intval($this->input->post('id')));

            if ($this->db->update("equipment_groups_asset")) {
                $this->logs->add("equipment_groups_asset", $this->input->post('id'), "ASSET_GROUP_UPDATED", $_POST);
                redirect("asset_groups/index?message=Asset group was updated successfully.");
            } else {
                redirect("asset_groups/index?error=Update failed.");
            }
        } else {
            redirect("asset_groups/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_equipment_groups") && $this->input->post('name')) {
            $this->db->set('equipment_group_name', $this->input->post('name'));
            $this->db->set('equipment_group_code', $this->input->post('code'));
            $this->db->set('equipment_group_notes', $this->input->post('notes'));
            if ($this->db->insert('equipment_groups_asset')) {
                $this->logs->add("equipment_groups_asset", $this->db->insert_id(), "ASSET_GROUP_ADDED", $_POST);
                redirect("asset_groups?message=Added Asset group successfully");
            } else {
                // var_dump($this->db->last_query());
                // exit();
                redirect("asset_groups?error=Adding Asset group failed");
            }
        } else {
            redirect("asset_groups?error=No permission to add Asset group");
        }
    }
}
