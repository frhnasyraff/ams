<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Templates extends CI_Controller
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
        $this->load->view('header', ['title' => "User groups", "styles" => []]);
        //$this->load->view('user-groups', []);
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

    public function branch_ajax()
    {
        if ($this->input->post('id')) {

            $result = $this->db->order_by("branch_template_id", "desc")->get_where('branch_template', ["template_id" => $this->input->post('id'), "branch_id" => $this->input->post('branch_id')], [0, 1])->result();

            if ($result) {
                die(json_encode(["state" => 1, "content" => $result[0]->template_html]));
            } else {
                $result = $this->db->get_where('templates', ["template_id" => $this->input->post('id'), "template_group" => "branch"])->result();
                if ($result) {
                    die(json_encode(["state" => 1, "content" => $result[0]->template_html]));
                }
            }
        }
        die(json_encode(["state" => 0]));
    }

    public function country_ajax()
    {
        if ($this->input->post('id')) {

            $result = $this->db->order_by("country_template_id", "desc")->get_where('country_template', ["template_id" => $this->input->post('id'), "country_id" => $this->input->post('country_id'), "branch_id" => $_SESSION['user']->active_branch], [0, 1])->result();

            if ($result) {
                die(json_encode(["state" => 1, "content" => $result[0]->template_html]));
            } else {
                $result = $this->db->get_where('templates', ["template_id" => $this->input->post('id'), "template_group" => "country"])->result();
                if ($result) {
                    die(json_encode(["state" => 1, "content" => $result[0]->template_html]));
                }
            }
        }
        die(json_encode(["state" => 0]));
    }

    public function parse_branch_ajax()
    {
        if ($this->input->post('id') && $this->input->post('html')) {
            $html = $this->input->post('html');
            $html = $this->steve->parse_branch_template($this->input->post("id"), $html);
        }
        die(json_encode(["state" => 1, "content" => $html]));
    }

    public function save_branch()
    {
        if ($this->input->post('branch_id') && $this->input->post('template_id')) {
            if ($this->db->replace('branch_template', ["branch_id" => intval($this->input->post('branch_id')), "template_id" => intval($this->input->post('template_id')), "template_html" => $this->input->post('content')])) {
                redirect("companies/branch_info?id=" . $this->steve->id_encode($this->input->post('branch_id')) . "&message=Branch template was updated successfully.");
            }
        }
        redirect("companies/branch_info?id=" . $this->steve->id_encode($this->input->post("branch_id")) . "&error=Adding template failed");
    }

    public function save_country()
    {
        if ($this->input->post('country_id') && $this->input->post('template_id')) {
            if ($this->db->replace('country_template', ["country_id" => intval($this->input->post('country_id')), "branch_id" => $_SESSION['user']->active_branch, "template_id" => intval($this->input->post('template_id')), "template_html" => $this->input->post('content')])) {
                redirect("countries/info?id=" . $this->steve->id_encode($this->input->post('country_id')) . "&message=Country template was updated successfully.");
            }
        }
        redirect("countries/info?id=" . $this->steve->id_encode($this->input->post("country_id")) . "&error=Adding template failed");
    }
}
