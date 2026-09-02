<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Rebundling_colours extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_rebundling_colours")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Rebundling colours", "styles" => []]);
        $this->load->view('rebundling_colours', []);
        $this->load->view('footer', ['scripts' => ['design/js/rebundling_colours-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_rebundling_colours")) {

            $query = $this->db->get_where('rebundling_colours', ["rebundling_colour_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Rebundling colour - " . $info[0]->rebundling_colour_name]);
                $this->load->view('rebundling_colour-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("rebundling_colours?error=Rebundling colour not found");
            }
        } else {
            redirect("rebundling_colours?error=Rebundling colour not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("rebundling_colours", ["rebundling_colour_name", "description"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("rebundling_colours", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_rebundling_colours") && $this->input->post('id')) {
            die($this->steve->active_toggle("rebundling_colours", "rebundling_colour_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_rebundling_colours") && $this->input->post('id')) {
            $this->db->where("commodity_id", intval($this->input->post('id')));
            if ($this->db->delete("rebundling_colours")) {
                redirect("rebundling_colours/index?message=Commodity was deleted successfully.");
            } else {
                redirect("rebundling_colours/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("rebundling_colours/index?error=No commodity or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_rebundling_colours") && $this->input->post('id')) {
            $this->db->set("rebundling_colour_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("rebundling_colour_id", intval($this->input->post('id')));

            if ($this->db->update("rebundling_colours")) {
                $this->logs->add("rebundling_colours", $this->input->post('id'), "COLOUR_UPDATED", $_POST);
                redirect("rebundling_colours/index?message=Rebundling colour was updated successfully.");
            } else {
                redirect("rebundling_colours/index?error=Update failed.");
            }
        } else {
            redirect("rebundling_colours/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_rebundling_colours") && $this->input->post('name')) {
            $this->db->set('rebundling_colour_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('rebundling_colours')) {
                $this->logs->add("rebundling_colours", $this->db->insert_id(), "rebundling_colour_CREATED", $_POST);
                redirect("rebundling_colours?message=Added Rebundling colour successfully");
            } else {
                redirect("rebundling_colours?error=Adding Rebundling colour failed");
            }
        } else {
            redirect("rebundling_colours?error=No permission to add Rebundling colours");
        }
    }
}
