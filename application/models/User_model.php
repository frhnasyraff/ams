<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User_model class.
 *
 * @extends CI_Model
 */
class User_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function login($username, $password)
    {
        $user = $this->db->select("*")->from('users')->where("username", $username)->where("user_code <>", "DRIVER")->get()->result();

        if ($user) {
            $this->logs->add("users", $user[0]->user_id, "LOGIN_ATTEMPT", "Login attempted", $user[0]->user_id);
            if (password_verify($password, $user[0]->password)) {
                $this->logs->add("users", $user[0]->user_id, "LOGIN_SUCCESS", "Login successful", $user[0]->user_id);
                return true;
            }
        } else {
            return false;
        }
    }
    public function customer_login($username, $password)
    {
        $query = $this->db->select('*')->from('users')->where("username", $username)->where("user_code <>", "DRIVER")->get();

        $user = $query->result();

        if ($user && $user[0]->user_group == 9) {
            $this->logs->add("users", $user[0]->user_id, "LOGIN_ATTEMPT", "Login attempted", $user[0]->user_id);
            if (password_verify($password, $user[0]->password)) {
                $this->logs->add("users", $user[0]->user_id, "LOGIN_SUCCESS", "Login successful", $user[0]->user_id);
                return true;
            }
        } else {
            return false;
        }
    }

    public function current_user()
    {
        $user = $this->db->get_where('users', ["active" => 1, "session" => get_cookie("Steve_user")])->result()[0];
        return $user;
    }
    public function current_user_company()
    {
        $user = $this->db->get_where('users', ["active" => 1, "session" => get_cookie("Steve_user")])->result()[0];
        return $user->company_id;
    }
    public function get_admin_ids()
    {
        $adminList = $this->db->select('user_id')
            ->from('users')
            ->where("user_group", 1)
            ->get()
            ->result_array();

        $adminIdArray = [];
        foreach ($adminList as $k => $id) {
            $adminIdArray[$k] = $id['user_id'];
        }
        return $adminIdArray;
    }


    public function login_session($session)
    {
        $query = $this->db->get_where('users', ["session" => $session]);

        $user = $query->result();

        if ($user) {
            $this->logs->add("users", $user[0]->user_id, "SESSION_LOGIN_SUCCESS", "Session login successful", $user[0]->user_id);
            return true;
        } else {
            return false;
        }
    }

    public function has_perm($perm)
    {
        // if (!$_SESSION['permissions']) {
        $_SESSION["permissions"] = $this->permissions();
        // }
        return in_array($perm, $_SESSION['permissions']);
    }

    public function logged_in()
    {
        if (get_cookie("Steve_user")) {
            $user = $this->db->join("companies", "companies.company_id = users.company_id", "left")->get_where('users', ["active" => 1, "session" => get_cookie("Steve_user")])->result();
            if ($user) {
                $_SESSION['user'] = $user[0];
                $this->input->set_cookie("Steve_user", $user[0]->session, 60 * 60 * ($user[0]->remember ? 6 : 1));
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function get_user_roles($id)
    {
        $results = $this->db->get_where('user_role', ["user_id" => intval($id)])->result();
        $roles = [];
        foreach ($results as $r) {
            $roles[] = intval($r->role_id);
        }
        if (!count($roles)) {

            $this->logout();
            if ($results[0]->role_id == 4) {
                die(redirect("/customer_login?error=Your account is not configured with any user roles. Please contact HQ."));
            } else {
                die(redirect("/?error=Your account is not configured with any user roles. Please contact HQ."));
            }
        }
        return $roles;
    }

    public function permissions()
    {
        if (get_cookie("Steve_user")) {
            $user = $this->db->get_where('users', ["active" => 1, "session" => get_cookie("Steve_user")])->result();
            if ($user) {

                $roles = $this->get_user_roles($user[0]->user_id);
                $_SESSION['full_name'] = $user[0]->full_name;

                $permissions = $this->db->where_in("role_id", $roles)->join('permissions', 'permissions.perm_id = role_permissions.perm_id', 'left')->get("role_permissions")->result();
                $response = [];
                foreach ($permissions as $permission) {
                    $response[] = $permission->perm_name;
                }

                $user_permissions = $this->db->join('permissions', 'permissions.perm_id = user_permissions.perm_id', 'left')->get_where("user_permissions", ["user_id" => $_SESSION['user']->user_id])->result();

                // var_dump($this->db->last_query());
                // exit();
                foreach ($user_permissions as $permission) {
                    $response[] = $permission->perm_name;
                }
                return $response;
            } else {
                redirect("/?error=You have logged in from a different device. Please login again.");
            }
        } else {
            redirect("/?error=You have been logged out.");
        }
    }

    public function logout()
    {
        delete_cookie("redirect");
        delete_cookie("Steve_user");
        $this->logs->add("users", $_SESSION['user']->user_id, "LOGOUT", "Logged out manually.", $user[0]->user_id);
        session_destroy();
    }
    public function get_orders_data()
    {
        $orders = $this->db->select("orders.*")
            ->from('orders')
            ->join('companies', 'companies.company_id=orders.company_id', 'LEFT')
            ->get()
            ->result();
        return $orders;
    }
}
