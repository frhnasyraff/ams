<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Worker_locations extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_worker_locations")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Worker Locations", 'title2' => "Worker Locations", "styles" => []]);
        $this->load->view('worker-locations', []);
        $this->load->view('footer', ['scripts' => ['design/js/worker-locations-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_worker_locations")) {

            $query = $this->db->get_where('worker_locations', ["worker_location_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {

                $this->load->view('header', ['title' => "Worker location - " . $info[0]->worker_location_name, "styles" => []]);
                $this->load->view('worker-location-info', ['info' => $info[0]]);
                $this->load->view('footer');
            } else {
                redirect("worker_locations?error=Worker location not found");
            }
        } else {
            redirect("worker_locations?error=Worker location not found or you do not have permission to edit.");
        }
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("worker_locations", ["worker_location_name", "description"]));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_worker_locations") && $this->input->post('id')) {
            die($this->steve->active_toggle("worker_locations", "worker_location_id"));
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_worker_locations") && $this->input->post('id') && $this->input->post('name')) {
            $this->db->set("worker_location_name", $this->input->post('name'));
            $this->db->set("description", $this->input->post('description'));
            $this->db->where("worker_location_id", intval($this->input->post('id')));
            if ($this->db->update("worker_locations")) {
                $this->logs->add("worker_locations", $this->input->post('id'), "WORKER_LOCATION_UPDATED", $_POST);
                redirect("worker_locations/index?message=Worker location was updated successfully.");
            } else {
                redirect("worker_locations/index?error=Update failed.");
            }
        } else {
            redirect("worker_locations/info?id=" . $this->input->post('id') . "&error=No permission or name cannot be blank");
        }
    }

    public function add()
    {
        if ($this->user_model->has_perm("add_worker_locations") && $this->input->post('name')) {
            $this->db->set('worker_location_name', $this->input->post('name'));
            $this->db->set('description', $this->input->post('description'));
            if ($this->db->insert('worker_locations')) {
                $this->logs->add("worker_locations", $this->db->insert_id(), "WORKER_LOCATION_CREATED", $_POST);
                redirect("worker_locations");
            } else {
                redirect("worker_locations?error=Adding group failed");
            }
        } else {
            redirect("worker_locations?error=No permission or some fields are missing.");
        }
    }
}
