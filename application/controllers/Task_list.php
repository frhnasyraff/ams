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
                'design/js/task_list.js?v=4'
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
                    <div class="task-row-actions">
                        <a href="javascript:void(0);" 
                           class="task-action-btn task-action-btn--edit editBtn" 
                           title="Edit task"
                           data-id="'.$task->id.'" 
                           data-name="'.htmlspecialchars($task->name, ENT_QUOTES, 'UTF-8').'" 
                           data-frequency="'.$task->frequency_in_days.'">
                           <i class="fas fa-pen"></i><span>Edit</span>
                        </a>
                        <a href="'.site_url('Task_list/delete/'.$task->id).'" 
                           class="task-action-btn task-action-btn--delete" 
                           title="Delete task"
                           onclick="return confirm(\'Are you sure?\')">
                           <i class="fas fa-trash-alt"></i><span>Delete</span>
                        </a>
                    </div>'
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


