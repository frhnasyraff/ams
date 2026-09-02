<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Equipment_types extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_equipment_types")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Equipment types", "styles" => ['design/vendor/jquery-wheelcolorpicker/css/wheelcolorpicker.css']]);
        $this->load->view('equipment_types', []);
        $this->load->view('footer', ['scripts' => ['design/js/equipment_types-list.js', 'design/js/randomColor.js', 'design/vendor/jquery-wheelcolorpicker/jquery.wheelcolorpicker.min.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_equipment_types")) {

            $query = $this->db->get_where('equipment_types', ["equipment_type_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Equipment type - " . $info[0]->equipment_type_name, 'styles' => ['design/vendor/jquery-wheelcolorpicker/css/wheelcolorpicker.css']]);
                $this->load->view('equipment_type-info', ['info' => $info[0]]);
                $this->load->view('footer', ['scripts' => ['design/js/equipment_types-list.js', 'design/js/randomColor.js', 'design/vendor/jquery-wheelcolorpicker/jquery.wheelcolorpicker.min.js']]);
            } else {
                redirect("equipment_types?error=Equipment type not found");
            }
        } else {
            redirect("equipment_types?error=Equipment type not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("equipment_types", ["equipment_type_name", "description"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("equipment_types", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_equipment_types") && $this->input->post('id')) {
            die($this->steve->active_toggle("equipment_types", "equipment_type_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_equipment_types") && $this->input->post('id')) {
            $this->db->where("commodity_id", intval($this->input->post('id')));
            if ($this->db->delete("equipment_types")) {
                redirect("equipment_types/index?message=Commodity was deleted successfully.");
            } else {
                redirect("equipment_types/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("equipment_types/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        // var_dump('hello');
        // exit();
        if ($this->user_model->has_perm("edit_equipment_types") && $this->input->post('id')) {
            $this->db->set("equipment_type_name", $this->input->post('name'));
            $this->db->set("equipment_type_short_code", $this->input->post('short_code'));
            $this->db->set('equipment_type_cost', $this->input->post('cost') ? $this->input->post('cost') : NULL);
            $this->db->set('equipment_type_fuel_cost', $this->input->post('fuel_cost') ? $this->input->post('fuel_cost') : NULL);
            $this->db->set("equipment_type_colour", $this->input->post('colour'));
            $this->db->set("description", $this->input->post('description'));
            if ($this->input->post('resource_type')) {
                $this->db->set("operator_id", $this->input->post('resource_type'));
            }
            $this->db->where("equipment_type_id", intval($this->input->post('id')));

            if ($this->db->update("equipment_types")) {
                $this->logs->add("equipment_types", $this->input->post('id'), "EQUIPMENT_TYPE_UPDATED", $_POST);
                redirect("equipment_types/index?message=Equipment type was updated successfully.");
            } else {
                redirect("equipment_types/index?error=Update failed.");
            }
        } else {
            redirect("equipment_types/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_equipment_types") && $this->input->post('name')) {
            $this->db->set('equipment_type_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->input->post('resource_type')) {
                $this->db->set("operator_id", $this->input->post('resource_type'));
            }
            $this->db->set("equipment_type_short_code", $this->input->post('short_code'));
            $this->db->set("equipment_type_colour", $this->input->post('colour'));
            if ($this->db->insert('equipment_types')) {
                $this->logs->add("equipment_types", $this->db->insert_id(), "EQUIPMENT_TYPE_CREATED", $_POST);
                redirect("equipment_types?message=Added equipment type successfully");
            } else {

                redirect("equipment_types?error=Adding Equipment type failed");
            }
        } else {
            redirect("equipment_types?error=No permission to add Equipment types");
        }
    }
}
