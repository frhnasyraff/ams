<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Permissions extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_permissions")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Permissions", 'title2' => "Permissions", "styles" => []]);
        $this->load->view('permissions', []);
        $this->load->view('footer', ['scripts' => ['design/js/permissions-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_permissions")) {

            $query = $this->db->get_where('permissions', ["perm_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {

                $this->load->view('header', ['title' => "User group - " . $info[0]->perm_name, "styles" => []]);
                $this->load->view('permission-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("permissions?error=Permission not found");
            }
        } else {
            redirect("permissions?error=Permission not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("permissions", ["perm_name"], [], [["permission_categories", "permission_categories.perm_cat_id = permissions.perm_cat_id"]]));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_permissions") && $this->input->post('id')) {
            die($this->steve->active_toggle("permissions", "perm_id"));
        }
    }

    public function delete()
    {
        if ($this->user_model->has_perm("edit_permissions") && $this->input->post('id')) {
            $this->db->where("perm_id", intval($this->input->post('id')));
            if ($this->db->delete("permissions")) {
                redirect("permissions/index?message=Permission was deleted successfully.");
            } else {
                redirect("permissions/index?error=Permission deletion failed.");
            }
        } else {
            redirect("permissions/index?error=No permission or ID is blank");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_permissions") && $this->input->post('id')) {
            if ($this->input->post('name')) {
                $this->db->set("perm_name", $this->input->post('name'));
            }
            $this->db->set("perm_cat_id", intval($this->input->post('category')));
            $this->db->where("perm_id", intval($this->input->post('id')));

            if ($this->db->update("permissions")) {
                $this->logs->add("permissions", $this->input->post('id'), "PERMISSION_UPDATED", $_POST);
                redirect("permissions/index?message=Permission was updated successfully.");
            } else {
                redirect("permissions/index?error=Update failed.");
            }
        } else {
            redirect("permissions/index?error=No permission or ID is blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_permissions") && $this->input->post('name')) {
            $this->db->set('perm_name', $this->input->post('name'));
            $this->db->set('perm_cat_id', intval($this->input->post('category')));
            if ($this->db->insert('permissions')) {
                $this->logs->add("permissions", $this->db->insert_id(), "PERMISSION_CREATED", $_POST);
                redirect("permissions");
            } else {
                redirect("permissions?error=Adding permission failed. The permission may already exist.");
            }
        }
    }
}
