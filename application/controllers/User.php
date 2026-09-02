<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * User class.
 *
 * @extends CI_Controller
 */
class User extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        if ($this->user_model->logged_in() && $this->user_model->current_user()->user_group == 9) {
            die(redirect("/customer_dashboard"));
        } else if ($this->user_model->logged_in()) {
            die(redirect("/assets_type_dashboard"));
        }
        $this->user_model->logout();

        $this->load->view('login', $data);
    }

    public function reset_password()
    {
        if ($this->input->post("existing_password") && $this->input->post('confirm_password') && $this->input->post('password')) {

            if ($this->input->post('confirm_password') != $this->input->post('password')) {
                redirect("user/settings?error=Your password & re-enter password does not match. Please try again.");
            } else {
                $user = $this->db->get_where('users', ["active" => 1, "session" => get_cookie("Steve_user")])->result();
                if ($user && password_verify($this->input->post("existing_password"), $user[0]->password)) {
                    $this->db->set('password', password_hash($this->input->post('password'), PASSWORD_DEFAULT));
                    $this->db->where("user_id", $user[0]->user_id);
                    if ($this->db->update("users")) {
                        $this->logs->add("users", $user[0]->user_id, "PASSWORD_RESET");
                        redirect("user/settings?message=Your password has been successfully updated.");
                    } else {
                        redirect("user/settings?error=Password reset failed. Please try again.");
                    }
                } else {
                    redirect("user/settings?error=Your existing password is incorrect. Please try again.");
                }
            }
        } else {
            redirect("user/settings?error=Please make sure you filled all fields");
        }
    }

    public function read_message()
    {
        if ($this->input->post('table')) {
            if ($this->db->set('record_id', intval($this->input->post('record')))->set('table_name', $this->input->post('table'))->set('user_id', $_SESSION['user']->user_id)->set("message_timestamp", $this->input->post("t"))->insert("message_views")) {
                die(json_encode(["state" => 1]));
            }
        }
        die(json_encode(["state" => 0]));
    }

    public function upload_picture()
    {
        if ($this->user_model->logged_in()) {
            if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["file"]["tmp_name"];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . "-" . basename($_FILES["file"]["name"]);

                $folder = realpath("storage") . "/User-" . $_SESSION['user']->user_id;

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . "/" . $name)) {
                    $this->db->set("profile_picture", $name);
                    $this->db->where("user_id", $_SESSION['user']->user_id);

                    if ($this->db->update("users")) {
                        $this->logs->add("users", $_SESSION['user']->user_id, "PICTURE_UPLOADED", "A new profile photo was uploaded. attachment " . $name . " was added.");
                    }
                }
            }
        }
    }

    public function settings()
    {
        if ($this->user_model->logged_in()) {
            $this->load->view('header', ['title' => "Settings", 'styles' => ["design/vendor/dropzone/min/dropzone.min.css", 'design/vendor/jquery-wheelcolorpicker/css/wheelcolorpicker.css', 'design/vendor/jquery-fontselect/fontselect.css']]);
            $this->load->view('profile-settings');
            $this->load->view('footer', ['scripts' => ["design/vendor/dropzone/min/dropzone.min.js", 'design/vendor/jquery-wheelcolorpicker/jquery.wheelcolorpicker.min.js', 'design/vendor/jquery-fontselect/jquery.fontselect.js', 'design/js/profile-settings.js']]);
        } else {
            die(redirect("/assets_type_dashboard?error=No permission to view this content."));
        }
    }

    public function set_color()
    {
        $this->db->set("default_color", ($this->input->post("colour") ? $this->input->post("colour") : ''));
        $this->db->where("user_id", $_SESSION['user']->user_id);
        if ($this->db->update("users")) {
            $this->logs->add("users", $_SESSION['user']->user_id, "COLOR_CHANGED", $_POST);
            die(json_encode(["state" => 1]));
        } else {
            die(json_encode(["state" => 0]));
        }
    }

    public function set_font()
    {
        $this->db->set("default_font", ($this->input->post("font") ? $this->input->post("font") : ''));
        $this->db->where("user_id", $_SESSION['user']->user_id);
        if ($this->db->update("users")) {
            $this->logs->add("users", $_SESSION['user']->user_id, "FONT_CHANGED", $_POST);
            die(json_encode(["state" => 1]));
        } else {
            die(json_encode(["state" => 0]));
        }
    }

    public function update()
    {

        $this->db->set("timezone", $this->input->post("timezone"));
        $this->db->where("user_id", $_SESSION['user']->user_id);
        if ($this->db->update("users")) {
            $this->logs->add("users", $_SESSION['user']->user_id, "USER_UPDATED", $_POST);
            redirect("user/settings?message=Settings saved successfully.");
        } else {
            redirect("user/settings?error=Save failed.");
        }
    }

    public function login()
    {

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        if ($username && $password && $this->user_model->login($username, $password)) {
            $session = password_hash(microtime(), PASSWORD_DEFAULT);

            // Get the user's image path from logo_images table
            $this->db->select('image_path')
                ->from('logo_images')
                ->where('image_id', 1);  // Assuming the global logo has id=1 or any other identifier
            $image_data = $this->db->get()->row();
            $image_path = $image_data ? $image_data->image_path : '';  // Default image if no logo is found


            // Store the image_path in session for global use
            $_SESSION['logo_image_path'] = $image_path;

            $this->db->set('session', $session);
            $this->db->set('remember', intval($this->input->post('remember_me')));
            $this->db->where("username", $username);
            if ($this->db->update("users")) {

                $this->input->set_cookie("Steve_user", $session, 60 * 60 * ($this->input->post("remember_me") ? 60 * 8 : 1));
                if (get_cookie("redirect")) {

                    $url = get_cookie("redirect");
                    delete_cookie("redirect");
                    redirect($url);
                } else {

                    $query = $this->db->from('users')->where("username", $username)->where("user_code <>", "DRIVER")->get();
                    $user = $query->result();

                    //  get logged-in user active branches
                    $user_branches = $this->db->select('branch_id')->from('user_branch')
                        ->where('user_id', $user[0]->user_id)
                        ->get()
                        ->result();
                    $user_branch_ids = array();
                    foreach ($user_branches as $user_branch) {
                        array_push($user_branch_ids, $user_branch->branch_id);
                    }
                    $_SESSION['user_active_branches'] = $user_branch_ids;

                    if ($user && $user[0]->user_group == 9) {
                        redirect('/assets_type_dashboard');
                    } else {
                        redirect('/assets_type_dashboard');
                    }
                }
            } else {
                redirect("/?error=Login failed. Please try again.");
            }
        } else if ($this->input->get('session') && $this->user_model->login_session($this->input->get('session'))) {
            $this->input->set_cookie("Steve_user", $this->input->get('session'), 60 * 60 * ($this->input->post("remember_me") ? 60 * 8 : 1));
            if (get_cookie("redirect")) {
                $url = get_cookie("redirect");
                delete_cookie("redirect");
                redirect($url);
            } else {
                if ($this->model->user_model->current_user()->user_group == 9) {
                    redirect('/assets_type_dashboard');
                } else {
                    redirect('/assets_type_dashboard');
                }
            }
        } else {
            redirect("/?error=Username or password is incorrect. Please try again.");
        }
    }

    public function logout()
    {
        $this->user_model->logout();
        die(redirect("/?message=Successfully logged out"));
    }
}
