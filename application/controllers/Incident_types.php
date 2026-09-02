<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Incident_types extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_incident_types")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Incident Types", "styles" => []]);
        $this->load->view('incident_types', []);
        $this->load->view('footer', ['scripts' => ['design/js/incident_types-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_incident_types")) {
            $query = $this->db->get_where('incident_types', ["incident_type_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Incident Type - " . $info[0]->incident_type]);
                $this->load->view('incident_type-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("incident_types?error=Incident type not found");
            }
        } else {
            redirect("incident_types?error= Incident type not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("incident_types", ["incident_type", "description"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("incident_type", "asc")->select("incident_type_id as id, CONCAT(incident_type, ' (', description, ')') as label, CONCAT(incident_type, ' - ', description) as value")->group_start()->like("incident_type", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("incident_types", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->input->post('id') && $this->user_model->has_perm("list_incident_types")) {
            die($this->steve->active_toggle("incident_types", "incident_type_id"));
        }
    }

    public function delete()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("delete_incident_types")) {
            $this->db->where("incident_type_id", intval($this->input->post('id')));
            if ($this->db->delete("incident_types")) {
                redirect("incident_types/index?message=Commodity was deleted successfully.");
            } else {
                redirect("incident_types/index?error=Commodity deletion failed.");
            }
        } else {
            redirect("incident_types?error= Incident type not found or you do not have permission to delete.");
        }
    }

    public function update()
    {
        if ($this->input->post('id') && $this->user_model->has_perm("edit_incident_types")) {

            $this->db->set("incident_type", $this->input->post('incident_type'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("incident_type_id", intval($this->input->post('id')));

            if ($this->db->update("incident_types")) {
                $this->logs->add("incident_types", $this->input->post('id'), "INCIDENT_TYPE_UPDATED", $_POST);
                redirect("incident_types/index?message=Incident type was updated successfully.");
            } else {
                redirect("incident_types/index?error=Update failed.");
            }
        } else {
            redirect("incident_types?error= Incident type not found or you do not have permission to update.");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_incident_types") && $this->input->post('incident_type')) {

            $this->db->set('incident_type', $this->input->post('incident_type'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('incident_types')) {
                $this->logs->add("incident_types", $this->db->insert_id(), "INCIDENT_TYPE_CREATED", $_POST);
                redirect("incident_types?message=Added incident type successfully");
            } else {
                redirect("incident_types?error=Adding incident type failed");
            }
        } else {
            redirect("incident_types?error=No permission to add Incident types");
        }
    }
}
