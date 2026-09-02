<?php
defined('BASEPATH') or exit('No direct script access allowed');

class States extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_users')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
        $this->load->library('form_validation');
    }

    public function index()
    {
        $this->load->view('header', ['title' => 'States', 'title2' => 'States']);

        $countries = $this->db->select('*')->from('countries')->get()->result();
        $this->load->view('states', [
            'countries' => $countries
        ]);

        $this->load->view('footer', ['scripts' => ['design/js/states.js']]);
    }

    public function upload_picture()
    {
        if ($this->input->post('id') && $this->user_model->has_perm('edit_users')) {

            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']['tmp_name'];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . '-' . basename($_FILES['file']['name']);

                $folder = realpath('storage') . '/User-' . $this->input->post('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . '/' . $name)) {
                    $this->db->set('profile_picture', $name);
                    $this->db->where('user_id', $this->input->post('id'));

                    if ($this->db->update('users')) {
                        $this->logs->add('users', $this->input->post('id'), 'PICTURE_UPLOADED', 'A new profile photo was uploaded. attachment ' . $name . ' was added.');
                    }
                }
            }
        }
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm('edit_users')) {

            $query = $this->db->get_where('users', ['user_id' => $this->steve->id_decode()]);

            $info = $query->result();

            $user_in_roles = [];

            foreach ($this->db->where('user_id', intval($info[0]->user_id))->get('user_role')->result() as $role) {
                $user_in_roles[] = $role->role_id;
            }

            $user_permission_overrides = [];

            foreach ($this->db->where('user_id', intval($info[0]->user_id))->get('user_permissions')->result() as $permission) {
                $user_permission_overrides[] = $permission->perm_id;
            }

            $branches = $this->db->select('*')->from('branch_office')->where('active', 1)->get()->result();
            $user_branches = $this->db->select('branch_id')->where('user_id', $this->steve->id_decode())->get('user_branch')->result();
            $selected_branch_ids = array();
            foreach ($user_branches as $branch) {
                array_push($selected_branch_ids, $branch->branch_id);
            }

            if ($info) {
                $this->load->view('header', ['title' => 'User - ' . $info[0]->full_name . ' (' . $info[0]->username . ')', 'styles' => ['design/css/multi-select.css', 'design/vendor/jquery-ui-1.12.1/jquery-ui.min.css', 'design/vendor/dropzone/min/dropzone.min.css']]);
                $this->load->view('user-info', ['info' => $info[0], 'branches' => $branches, 'selected_branch_ids' =>  $selected_branch_ids, 'user_in_roles' => $user_in_roles, 'role_permissions' => (count($user_in_roles) ? $this->steve->get_role_permissions($user_in_roles) : null), 'user_permission_overrides' => $user_permission_overrides]);
                $this->load->view('footer', ['scripts' => ['design/vendor/dropzone/min/dropzone.min.js',  'design/vendor/jquery-ui-1.12.1/jquery-ui.min.js', 'design/js/jquery.multi-select.js', 'design/js/user-info.js']]);
            } else {
                redirect('users?error=User not found');
            }
        } else {
            redirect('users?error=User not found or you do not have permission to edit.');
        }
    }

    public function assign_permissions()
    {
        if ($this->user_model->has_perm('user_permissions_override')) {
            if ($this->input->post('id')) {
                $user_id = $this->input->post('id');

                $this->db->delete('user_permissions', ['user_id' => $user_id]);

                foreach ($this->input->post('permissions') as $perm) {
                    $this->db->set('perm_id', intval($perm))->set('user_id', $user_id);
                    $this->db->insert('user_permissions');
                }
                $this->logs->add('users', $user_id, 'PERMISSION_OVERRIDE_UPDATED', $_POST);
                redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&message=Permission override(s) saved successfully#nav-permissions');
            }
        } else {
            redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&error=No permission to save override.');
        }
    }

    public function ajax_list()
    {
        $query = $this->db->get('states');
        $data = $query->result();
        echo json_encode([
            'data' => $data,
        ]);
    }

    public function assign_roles()
    {
        if ($this->user_model->has_perm('assign_user_roles') && $this->input->post('id')) {
            $user_id = intval($this->input->post('id'));
            $this->db->delete('user_role', array('user_id' => $user_id));

            foreach ($this->input->post('roles') as $role) {
                $this->db->set('user_id', $user_id)->set('role_id', $role);
                $this->db->insert('user_role');
            }
            $this->logs->add('users', $user_id, 'ROLES_UPDATED', $_POST);
            redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&message=Role(s) association saved successfully');
        }
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm('edit_users') && $this->input->post('id')) {
            die($this->steve->active_toggle('users', 'user_id'));
        }
    }

    public function reset_password()
    {
        if ($this->user_model->has_perm('edit_users') && $this->input->post('id') && $this->input->post('confirm_password') && $this->input->post('password')) {

            if ($this->input->post('confirm_password') != $this->input->post('password')) {
                redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&error=Your passwords do not match. Please try again.');
            } else {
                $this->db->set('password', password_hash($this->input->post('password'), PASSWORD_DEFAULT));
                $this->db->where('user_id', intval($this->input->post('id')));
                if ($this->db->update('users')) {
                    $this->logs->add('users', $this->input->post('id'), 'PASSWORD_CHANGED');
                    redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&message=The password has been successfully updated.');
                } else {
                    redirect('users/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&error=Password reset failed. Please try again.');
                }
            }
        } else {
            redirect('users?error=Please make sure you filled all fields');
        }
    }

    public function add()
    {
        $this->form_validation->set_rules('state_name', 'State Name', 'required|regex_match[/^[a-zA-Z0-9\s]+$/]');

        if ($this->form_validation->run() === TRUE) {
            // Validation passed, proceed with inserting data
            $data = array(
                'state_name'   => $this->input->post('state_name'),
            );

            // Insert data into the database
            if ($this->db->insert('states', $data)) {
                // Specify the table name
                $this->session->set_flashdata('success', 'State added successfully!');
            } else {
                $this->session->set_flashdata('error', 'Error while adding State.');
            }
            redirect('states');
            // Adjust the redirect as needed
        } else {
            // Validation failed, reload the form or show errors
            $this->session->set_flashdata('error', validation_errors());
            redirect('states');
            // Adjust the redirect as needed
        }
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = [
            'state_name' => $this->input->post('state_name'),
        ];

        $this->db->where('id', $id);
        if ($this->db->update('states', $data)) {
            echo json_encode(['status' => 'success', 'message' => 'State updated successfully.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Error updating State.']);
        }
    }

    public function delete()
    {
        // Get the ID from the POST request
        $id = $this->input->post('id');

        // Check if ID is valid ( you can add more validation here if needed )
        if (!$id) {
            echo json_encode(['status' => 'error', 'message' => 'Invalid ID.']);
            return;
        }

        // Perform the deletion
        $this->db->where('id', $id);
        if ($this->db->delete('states')) {
            // Deletion was successful
            echo json_encode(['status' => 'success', 'message' => 'State deleted successfully.']);
        } else {
            // Deletion failed
            echo json_encode(['status' => 'error', 'message' => 'Error deleting State.']);
        }
    }

    public function get_data()
    {
        $id = $this->input->get('id');


        $this->db->where('id', $id);
        $query = $this->db->get('states');

        if ($query->num_rows() > 0) {
            echo json_encode(['status' => 'success', 'data' => $query->row()]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Data not found.']);
        }
    }
}
