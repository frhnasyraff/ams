<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Users extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('email');
        $this->load->config('email');

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_users")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "User Accounts", 'title2' => "User Accounts", "styles" => ["design/vendor/jquery-ui-1.12.1/jquery-ui.min.css"]]);

        $this->load->view('users', [
            'summary' => $this->status_summary('users')
        ]);

        $this->load->view('footer', ['scripts' => ["design/vendor/jquery-ui-1.12.1/jquery-ui.min.js", 'design/js/users-list.js']]);
    }

    public function upload_picture()
    {
        if ($this->input->post('id') && $this->user_model->has_perm("edit_users")) {

            if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["file"]["tmp_name"];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . "-" . basename($_FILES["file"]["name"]);

                $folder = realpath("storage") . "/User-" . $this->input->post('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . "/" . $name)) {
                    $this->db->set("profile_picture", $name);
                    $this->db->where("user_id", $this->input->post("id"));

                    if ($this->db->update("users")) {
                        $this->logs->add("users", $this->input->post('id'), "PICTURE_UPLOADED", "A new profile photo was uploaded. attachment " . $name . " was added.");
                    }
                }
            }
        }
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_users")) {

            $query = $this->db->get_where('users', ["user_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if (!$info) {
                redirect("users?error=User not found");
                return;
            }

            $user_in_roles = [];

            foreach ($this->db->where("user_id", intval($info[0]->user_id))->get("user_role")->result() as $role) {
                $user_in_roles[] = $role->role_id;
            }

            $user_permission_overrides = [];

            foreach ($this->db->where("user_id", intval($info[0]->user_id))->get("user_permissions")->result() as $permission) {
                $user_permission_overrides[] = $permission->perm_id;
            }

            $branches = $this->db->select("*")->from("branch_office")->where('active', 1)->get()->result();
            $user_branches = $this->db->select('branch_id')->where("user_id", $this->steve->id_decode())->get("user_branch")->result();
            $selected_branch_ids = array();
            foreach ($user_branches as $branch) {
                array_push($selected_branch_ids, $branch->branch_id);
            }

            if ($info) {
                $this->load->view('header', ['title' => "User - " . $info[0]->full_name . " (" . $info[0]->username . ")", "styles" => ['design/css/multi-select.css', "design/vendor/jquery-ui-1.12.1/jquery-ui.min.css", "design/vendor/dropzone/min/dropzone.min.css"]]);
                $this->load->view('user-info', ['info' => $info[0], "branches" => $branches, "selected_branch_ids" =>  $selected_branch_ids, "user_in_roles" => $user_in_roles, "role_permissions" => (count($user_in_roles) ? $this->steve->get_role_permissions($user_in_roles) : null), "user_permission_overrides" => $user_permission_overrides, "supports_email_reminders" => $this->db->field_exists('email_check', 'users')]);
                $this->load->view('footer', ['scripts' => ["design/vendor/dropzone/min/dropzone.min.js",  "design/vendor/jquery-ui-1.12.1/jquery-ui.min.js", 'design/js/jquery.multi-select.js', 'design/js/user-info.js']]);
            } else {
                redirect("users?error=User not found");
            }
        } else {
            redirect("users?error=User not found or you do not have permission to edit.");
        }
    }

    public function assign_permissions()
    {
        if ($this->user_model->has_perm("user_permissions_override")) {
            if ($this->input->post('id')) {
                $user_id = $this->input->post('id');

                $this->db->delete('user_permissions', ['user_id' => $user_id]);

                foreach ((array) $this->input->post('permissions') as $perm) {
                    $this->db->set('perm_id', intval($perm))->set('user_id', $user_id);
                    $this->db->insert('user_permissions');
                }
                $this->logs->add("users", $user_id, "PERMISSION_OVERRIDE_UPDATED", $_POST);
                redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=Permission override(s) saved successfully#nav-permissions");
            }
        } else {
            redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&error=No permission to save override.");
        }
    }

    public function ajax_list()
    {
        $conditions = [];
        $status = $this->input->post('status_filter');
        if ($status !== null && $status !== '') {
            $conditions[] = ['users.active', intval($status)];
        }

        $response = json_decode($this->steve->datatables_mysql(
            "users",
            ["users.user_code", "users.username", "users.full_name", "users.email", "users.active"],
            $conditions,
            [],
            ["users.user_id", "users.user_code", "users.username", "users.full_name", "users.email", "users.active"]
        ), true);
        $response['summary'] = $this->status_summary('users');

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

    public function assign_roles()
    {
        if ($this->user_model->has_perm("assign_user_roles") && $this->input->post('id')) {
            $user_id = intval($this->input->post('id'));
            $this->db->delete('user_role', array('user_id' => $user_id));

            foreach ((array) $this->input->post('roles') as $role) {
                $this->db->set('user_id', $user_id)->set('role_id', $role);
                $this->db->insert('user_role');
            }
            $this->logs->add("users", $user_id, "ROLES_UPDATED", $_POST);
            redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=Role(s) association saved successfully#nav-association");
        }
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_users") && $this->input->post('id')) {
            die($this->steve->active_toggle("users", "user_id"));
        }
    }

    public function reset_password()
    {
        if ($this->user_model->has_perm("edit_users") && $this->input->post('id') && $this->input->post('confirm_password') && $this->input->post('password')) {

            if ($this->input->post('confirm_password') != $this->input->post('password')) {
                redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&error=Your passwords do not match. Please try again.");
            } else {
                $this->db->set('password', password_hash($this->input->post('password'), PASSWORD_DEFAULT));
                $this->db->where("user_id", intval($this->input->post('id')));
                if ($this->db->update("users")) {
                    $this->logs->add("users", $this->input->post("id"), "PASSWORD_CHANGED");
                    redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=The password has been successfully updated.");
                } else {
                    redirect("users/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&error=Password reset failed. Please try again.");
                }
            }
        } else {
            redirect("users?error=Please make sure you filled all fields");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_users") && $this->input->post('username') && $this->input->post('email') && $this->input->post('password')) {
            $this->db->set('username', $this->input->post('username'));
            $this->db->set('password', password_hash($this->input->post('password'), PASSWORD_DEFAULT));
            $this->db->set('email', $this->input->post('email'));
            $this->db->set('user_code', $this->input->post('user_code'));
            $this->db->set('full_name', $this->input->post('full_name'));
            $this->db->set('designation', intval($this->input->post('designation')));
            $this->db->set('user_group', intval($this->input->post('user_group')));
            // $this->db->set('user_group', 9);
            $this->db->set('address_line_1', $this->input->post('address_line_1'));
            $this->db->set('address_line_2', $this->input->post('address_line_2'));
            $this->db->set('address_zip', $this->input->post('address_zip'));
            $this->db->set('address_city', $this->input->post('address_city'));
            $this->db->set('address_state', $this->input->post('address_state'));
            $this->db->set('address_country', $this->input->post('address_country'));
            $this->db->set('mobile', $this->input->post('mobile'));
            $this->db->set('phone', $this->input->post('phone'));
            $comp_id = 0;
            if ($this->input->post('company_id') && $this->input->post('company_id') !== null && $this->input->post('company_id') != "") {
                $comp_id = $this->input->post('company_id');
            }


            $this->db->set('company_id', $comp_id);

            unset($_POST['password']);

            if ($this->db->insert('users')) {

                $inserted_user_id  = $this->db->insert_id();

                // add user in user roles table
                $addedUser = $this->db->get_where('users', ["user_id" => $inserted_user_id])->result();
                $this->db->set('user_id', $addedUser[0]->user_id);
                $this->db->set('role_id', 4);
                $this->db->insert('user_role');
                // $this->logs->add("users", $inserted_user_id, "USER_CREATED", $_POST);
                redirect("users/?message=User has been Added successfully.");
            } else {
                $error = $this->db->error();
                if ($error['code'] == 1062) {
                    redirect("users?error=Another account already exists for this username.");
                } else {
                    redirect("users?error=Adding user failed");
                }
            }
        } else {
            redirect("users?error=No permission or some fields are missing.");
        }
    }

    public function update()
    {


        if ($this->user_model->has_perm("edit_users") && $this->input->post('username') && $this->input->post('email')) {
            $checkboxValue = $this->input->post('email_checkbox');
            $this->db->set('username', $this->input->post('username'));
            $this->db->set('email', $this->input->post('email'));
            $this->db->set('user_code', $this->input->post('user_code'));
            $this->db->set('full_name', $this->input->post('full_name'));
            $this->db->set('designation', intval($this->input->post('designation')));
            $this->db->set('user_group', intval($this->input->post('user_group')));
            $this->db->set('address_line_1', $this->input->post('address_line_1'));
            $this->db->set('address_line_2', $this->input->post('address_line_2'));
            $this->db->set('address_zip', $this->input->post('address_zip'));
            $this->db->set('address_city', $this->input->post('address_city'));
            $this->db->set('address_state', $this->input->post('address_state'));
            $this->db->set('address_country', $this->input->post('address_country'));
            $this->db->set('mobile', $this->input->post('mobile'));
            $this->db->set('phone', $this->input->post('phone'));
            if ($this->db->field_exists('email_check', 'users')) {
                $this->db->set('email_check', $checkboxValue ? '1' : '0');
            }
            $this->db->set('company_id', $this->input->post('company_id') ? $this->input->post('company_id') : null);

            $this->db->where("user_id", intval($this->input->post('id')));

            if ($this->db->update('users')) {
                $this->logs->add("users", $this->input->post("id"), "USER_UPDATED", $_POST);

                // update user branch
                if (is_array($this->input->post('branch_ids'))) {

                    // remove old user branch
                    $this->db->where("user_id", intval($this->input->post('id')));
                    $this->db->delete("user_branch");

                    // add new user branch
                    foreach ($this->input->post('branch_ids') as $branch_id) {
                        $this->db
                            ->set('user_id', intval($this->input->post('id')))
                            ->set('branch_id', $branch_id);
                        $this->db->insert('user_branch');
                    }
                }

                redirect("users/index?message=User was updated successfully.");
            } else {
                redirect("users/info?id=" . $this->input->post('id') . "&error=Update failed.");
            }
        } else {
            redirect("users/index?error=No permission or incomplete fields.");
        }
    }


    public function emailSend()
    {

        $current_date = date('Y-m-d');
        $expiringAssetsCount = 0;
        $expiringItemsCount = 0;

        $asset_calibration_data = $this->db->select('equipments_asset.*')
            ->from('equipments_asset')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        $item_calibration_data = $this->db->select('add_asset_items.*')
            ->from('add_asset_items')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        // Asset calibration loop
        foreach ($asset_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringAssetsCount++;
            }
        }

        // Item calibration loop
        foreach ($item_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            // Debugging output to check date compariso

            if ($current_date == $reminderDate->format('Y-m-d')) {
                $expiringItemsCount++;
            }
        }



        // ==========================================


        $data = $this->db->select('users.*')
            ->from('users')
            ->where('active', 1);

        if ($this->db->field_exists('email_check', 'users')) {
            $data->where('email_check', '1');
        }

        $data = $data->get()->result();

        // Load email library
        $this->load->library('email');

        foreach ($data as $user) {

            // Email configuration
            $this->email->from('ranasuleman014@gmail.com', 'Techuick IT Solution');

            // $this->email->to('ranasuleman014@gmail.com'); // User's email from the database
            $this->email->to($user->email); // User's email from the database

            $this->email->subject('Asset / Itemes Calibration');

            $message = $this->load->view('email_template', [
                'expiringAssetsCount' => $expiringAssetsCount,
                'expiringItemsCount' => $expiringItemsCount
            ], TRUE);
            $this->email->message($message);

            // Send email and check for success
            if (!$this->email->send()) {
                log_message('error', 'Email not sent to: ' . $user->email);
                log_message('info', 'Email sent to: ' . $user->email);
            } else {
                log_message('info', 'Email sent to: ' . $user->email);
            }

            // Clear email settings for the next iteration
            $this->email->clear();
        }
    }
}
