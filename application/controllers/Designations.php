<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Designations extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_designations")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Designations", 'title2' => "Designations", "styles" => []]);
        $this->load->view('designations');
        $this->load->view('footer', ['scripts' => ['design/js/designations-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_designations")) {

            $query = $this->db->get_where('designations', ["designation_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {

                $this->load->view('header', ['title' => "Designation - " . $info[0]->designation_name, "styles" => []]);
                $this->load->view('designation-info', ['info' => $info[0]]);
                $this->load->view('footer', ['scripts' => ['design/js/DataTables/datatables.js', 'design/js/state-info.js', 'design/js/bootstrap-toggle.min.js', 'design/js/jquery.bootstrap-growl.min.js']]);
            } else {
                redirect("designations?error=Designation not found");
            }
        } else {
            redirect("designations?error=Designation not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("designations", ["designation_name", "description"]));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_designations") && $this->input->post('id')) {
            die($this->steve->active_toggle("designations", "designation_id"));
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_designations") && $this->input->post('id') && $this->input->post('name')) {
            $this->db->set("designation_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("designation_id", intval($this->input->post('id')));
            if ($this->db->update("designations")) {
                $this->logs->add("designations", $this->input->post('id'), "DESIGNATION_UPDATED", $_POST);
                redirect("designations/index?message=Designation was updated successfully.");
            } else {
                redirect("designations/info?id=" . $this->input->post('id') . "&error=Update failed.");
            }
        } else {
            redirect("designations/index?error=No permission or name cannot be blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_designations") && $this->input->post('name')) {
            $this->db->set('designation_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('designations')) {
                $this->logs->add("designations", $this->db->insert_id(), "DESIGNATION_CREATED", $_POST);
                redirect("designations");
            } else {
                redirect("designations?error=Adding designation failed");
            }
        }
    }
}
