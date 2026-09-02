<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_groups extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_user_groups")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "User Groups", 'title2' => "User Groups", "styles" => []]);
        $this->load->view('user-groups', []);
        $this->load->view('footer', ['scripts' => ['design/js/user-groups-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_user_groups")) {

            $query = $this->db->get_where('user_groups', ["user_group_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {

                $this->load->view('header', ['title' => "User group - " . $info[0]->user_group_name, "styles" => []]);
                $this->load->view('user-group-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("user_groups?error=User group not found");
            }
        } else {
            redirect("user_roles?error=User group not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("user_groups", ["user_group_name", "description"]));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_user_groups") && $this->input->post('id')) {
            die($this->steve->active_toggle("user_groups", "user_group_id"));
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_user_groups") && $this->input->post('id') && $this->input->post('name')) {
            $this->db->set("user_group_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("user_group_id", intval($this->input->post('id')));
            if ($this->db->update("user_groups")) {
                $this->logs->add("user_groups", $this->input->post('id'), "USER_GROUP_UPDATED", $_POST);
                redirect("user_groups/index?message=User group was updated successfully.");
            } else {
                redirect("user_groups/index?error=Update failed.");
            }
        } else {
            redirect("user_groups/info?id=" . $this->input->post('id') . "&error=No permission or name cannot be blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_user_groups") && $this->input->post('name')) {
            $this->db->set('user_group_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('user_groups')) {
                $this->logs->add("user_groups", $this->db->insert_id(), "USER_GROUP_CREATED", $_POST);

                redirect("user_groups/index?message=User group added successfully.");
            } else {

                redirect("user_groups?error=Adding group failed");
            }
        } else {
            redirect("user_groups?error=No permission or some fields are missing.");
        }
    }
}
