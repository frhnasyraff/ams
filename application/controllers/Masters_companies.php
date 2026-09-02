<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Masters_companies extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_masters_companies")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Companies", 'title2' => "list of Companies", "styles" => []]);
        $this->load->view('masters_companies', []);
        $this->load->view('footer', ['scripts' => ['design/js/masters_companies.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_masters_companies")) {
            $query = $this->db->get_where('masters_companies', ["company_id " => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Company- " . $info[0]->masters_companies]);
                $this->load->view('masters_companies-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("masters_companies?error=Company not found");
            }
        } else {
            redirect("masters_companies?error= Company not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("masters_companies", ["company_id ", "registration_id"]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("masters_companies", "asc")
            ->select("company_id as id, CONCAT(company_name, ' (', registrationid, ')') as label, CONCAT(company_name, ' - ', registration_id) as value")
            ->group_start()
            ->like("company_name", $this->input->get("term"))
            ->or_like("registration_id", $this->input->get("term"))
            ->or_like("contact_person", $this->input->get("term"))
            ->or_like("contact_email", $this->input->get("term"))
            ->or_like("business_type", $this->input->get("term"))
            ->group_end()
            ->get_where("masters_companies", ["active" => 1])
            ->result();
        die(json_encode($info));
    }

    public function state_ajax()
    {
        die($this->steve->active_toggle("masters_companies", "company_id"));
    }

    public function delete()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("delete_masters_companies")) {
            $this->db->where("company_id", intval($this->input->post('id')));
            if ($this->db->delete("masters_companies")) {
                redirect("masters_companies/index?message=Company was deleted successfully.");
            } else {
                redirect("masters_companies/index?error=Company deletion failed.");
            }
        } else {
            redirect("masters_companies?error= Company not deleted or you do not have permission to delete.");
        }
    }

    public function update()
    {
        if ($this->input->post('id') && $this->user_model->has_perm("edit_masters_companies")) {
            $this->db->set('registration_id ', $this->input->post('registration_id'));
            $this->db->set('company_name', $this->input->post('company_name'));
            $this->db->set('contact_person', $this->input->post('contact_person'));
            $this->db->set('contact_email', $this->input->post('contact_email'));
            $this->db->set('business_type ', $this->input->post('business_type'));
            $this->db->where("company_id", intval($this->input->post('id')));

            if ($this->db->update("masters_companies")) {
                $this->logs->add("masters_companies", $this->input->post('id'), "MASTERS_COMPANIES_UPDATED", $_POST);
                redirect("masters_companies/index?message=Company was updated successfully.");
            } else {
                redirect("masters_companies/index?error=Update failed.");
            }
        } else {
            redirect("masters_companies?error= Company not found or you do not have permission to edit.");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_masters_companies")) {
            $this->db->set('registration_id ', $this->input->post('registration_id'));
            $this->db->set('company_name', $this->input->post('company_name'));
            $this->db->set('contact_person', $this->input->post('contact_person'));
            $this->db->set('contact_email', $this->input->post('contact_email'));
            $this->db->set('business_type ', $this->input->post('business_type'));
            if ($this->db->insert('masters_companies')) {
                $this->logs->add("masters_companies", $this->db->insert_id(), "MASTERS_COMPANIES_CREATED", $_POST);
                redirect("masters_companies?message=Adding company sucessfully");
            } else {
                redirect("masters_companies?error=Adding company failed");
            }
        } else {
            redirect("masters_companies?error= You do not have permission to add.");
        }
    }
}
