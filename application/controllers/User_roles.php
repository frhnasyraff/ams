<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_roles extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        
        if (!$this->user_model->has_perm("list_user_roles")) {
            die(redirect("/"));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "User Roles",'title2' => "User Roles", "styles" => []]);
        $this->load->view('user-roles', ['summary' => $this->status_summary('roles')]);
        $this->load->view('footer', ['scripts' => ['design/js/user-roles-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_user_roles")) {

            $query = $this->db->get_where('roles', ["role_id" => $this->steve->id_decode()]);

            $info = $query->result();

            $users_in_role = [];

            foreach ($this->db->where("role_id", $info[0]->role_id)->get("user_role")->result() as $role) {
                $users_in_role[] = $role->user_id;
            }

            if ($info) {
                $this->load->view('header', ['title' => "Manage User Role", 'title2' => "Manage User Role", "styles" => ['design/css/multi-select.css']]);
                $this->load->view('user-role-info', ['info' => $info[0], "users" => $this->db->where("active", 1)->get("users")->result(), "role_users" => $users_in_role, "role_permissions" => $this->steve->get_role_permissions($info[0]->role_id)]);
                $this->load->view('footer', ['scripts' => ['design/js/jquery.multi-select.js', 'design/js/user-role-info.js?v=2']]);
            } else {
                redirect("user_roles?error=User role not found");
            }
        } else {
            redirect("user_roles?error=User role not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        $conditions = [];
        $status = $this->input->post('status_filter');
        if ($status !== null && $status !== '') {
            $conditions[] = ['roles.active', intval($status)];
        }

        $response = json_decode($this->steve->datatables_mysql(
            "roles",
            ["roles.role_name", "roles.description", "roles.active"],
            $conditions,
            [],
            ["roles.role_id", "roles.role_name", "roles.description", "roles.active"]
        ), true);
        $response['summary'] = $this->status_summary('roles');

        die(json_encode($response));
    }

    private function status_summary($table)
    {
        $summary = $this->db
            ->select('COUNT(*) AS total, SUM(active = 1) AS active, SUM(active = 0) AS inactive', false)
            ->get($table)
            ->row();

        return [
            'total' => intval($summary->total ?? 0),
            'active' => intval($summary->active ?? 0),
            'inactive' => intval($summary->inactive ?? 0)
        ];
    }

    public function assign_users()
    {
        if ($this->user_model->has_perm("assign_users") && $this->input->post('id')) {
            $role_id = $this->input->post('id');
            $this->db->delete('user_role', array('role_id' => $role_id));

            foreach (($this->input->post('users') ?: []) as $user) {
                $this->db->set('user_id', $user)->set('role_id', $role_id);
                $this->db->insert('user_role');
                $this->logs->add("users", $user, "ROLE_ASSIGNED", $_POST);
            }
            redirect("user_roles/info?id=" . $this->input->post('id') . "&message=User association saved successfully");
        }
    }

    public function assign_permissions()
    {
        if ($this->input->post('id')) {
            $role_id = $this->input->post('id');

            $this->db->delete('role_permissions', ['role_id' => $role_id]);

            foreach (($this->input->post('permissions') ?: []) as $perm) {
                $this->db->set('perm_id', intval($perm))->set('role_id', $role_id);
                $this->db->insert('role_permissions');
            }
            $this->logs->add("roles", $role_id, "PERMISSIONS_UPDATED", $_POST);
            redirect("user_roles/info?id=" . $this->input->post('id') . "&message=Permissions saved successfully");
        }
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_user_roles") && $this->input->post('id')) {
            die($this->steve->active_toggle("roles", "role_id"));
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_user_roles") && $this->input->post('id') && $this->input->post('name')) {
            $this->db->set("role_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("role_id", intval($this->input->post('id')));
            if ($this->db->update("roles")) {
                $this->logs->add("roles", $this->input->post('id'), "ROLE_UPDATED", $_POST);
                redirect("user_roles/index?message=User role was updated successfully.");
            } else {
                redirect("user_roles/index?error=Update failed.");
            }
        } else {
            redirect("user_roles/info?id=" . $this->input->post('id') . "&error=No user role or name cannot be blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_user_roles") && $this->input->post('name')) {
            $this->db->set('role_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('roles')) {
                $this->logs->add("roles", $this->db->insert_id(), "ROLE_CREATED", $_POST);
                redirect("user_roles");
            } else {
                redirect("user_roles?error=Adding user role failed");
            }
        } else {
            redirect("user_roles?error=No permission or some fields are missing.");
        }
    }
}
