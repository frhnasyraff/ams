<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task_list extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('Task_model');
    }

    public function index()
    {
        $data['title'] = "Task List";

        $this->load->view('header', [
            'title' => "Maintenance Tasks", 
            'title2' => "Maintenance Tasks", 
            "styles" => []
        ]);
        $this->load->view('task-list', $data);
        $this->load->view('footer', [
            'scripts' => [
                'design/js/task_list.js'
            ]
        ]);
    }

    public function ajax_list()
    {
        // DataTables ke liye data return karo
        $draw = $this->input->post('draw');
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $search = $this->input->post('search')['value'];

        // Total records
        $totalRecords = $this->Task_model->count_all();

        // Total records with filter
        $totalFiltered = $this->Task_model->count_filtered($search);

        // Data fetch karo
        $tasks = $this->Task_model->get_datatables($start, $length, $search);

        $data = array();
        foreach ($tasks as $task) {
            $data[] = array(
                "id" => $task->id,
                "name" => $task->name,
                "frequency_in_days" => $task->frequency_in_days,
                "action" => '
                    <a href="javascript:void(0);" 
                       class="btn btn-sm btn-primary editBtn" 
                       data-id="'.$task->id.'" 
                       data-name="'.$task->name.'" 
                       data-frequency="'.$task->frequency_in_days.'">
                       Edit
                    </a>
                    <a href="'.site_url('Task_list/delete/'.$task->id).'" 
                       class="btn btn-sm btn-danger" 
                       onclick="return confirm(\'Are you sure?\')">
                       Delete
                    </a>'
            );
        }

        $output = array(
            "draw" => intval($draw),
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $totalFiltered,
            "data" => $data
        );

        echo json_encode($output);
    }

    public function add()
    {
        $data = [
            'name' => $this->input->post('name'),
            'frequency_in_days' => $this->input->post('frequency_in_days')
        ];
        $this->Task_model->insert($data);
        
        // AJAX request check
        if($this->input->is_ajax_request()) {
            echo json_encode(['success' => true]);
        } else {
            redirect('Task_list');
        }
    }

    public function edit($id)
    {
        $task = $this->Task_model->getById($id);
        echo json_encode($task);
    }

    public function update()
    {
        $id = $this->input->post('id');
        $data = [
            'name' => $this->input->post('name_edit'),
            'frequency_in_days' => $this->input->post('frequency_edit')
        ];
        $this->Task_model->updateTask($id, $data);
        
        // AJAX request check
        if($this->input->is_ajax_request()) {
            echo json_encode(['success' => true]);
        } else {
            redirect('Task_list');
        }
    }

    public function delete($id)
    {
        $this->Task_model->delete($id);
        redirect('Task_list');
    }
}
