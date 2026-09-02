<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Task Types", 'title2' => "Task Types", "styles" => []]);
        $this->load->view('task', []);
        $this->load->view('footer', ['scripts' => ['design/js/task.js']]);
    }

    public function ajax_list()
    {
        die($this->steve->datatables_mysql("task", ["id", "name"]));
    }

    public function add()
    {


        // $this->db->set('id', $this->input->post('id'));
        $this->db->set('name', $this->input->post('name'));



        try {
            if ($this->db->insert('task')) {
                redirect("/Task?message=Task added successfully!");
            }
        } catch (Exception $e) {
            redirect("/Task?error=Adding Task failed!");
        }
    }


    public function update()
    {
        if ($this->input->post('id')) {

            $update_data = [
                'name' => $this->input->post('name_edit'),

            ];


            $this->db->where("id", $this->input->post('id'));
            $this->db->update('task', $update_data);


            redirect("/Task?message=Task updated successfully!");
        }
    }


    public function delete()
    {
        if ($this->input->get('id')) {
            $this->db->where("id", $this->input->get('id'));
            $this->db->delete("task");
            die(redirect("/Task?message= Task deleted successfully!"));
        }
    }
}
