<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DisposalMethod extends CI_Controller {

    public function __construct()
    {
        parent::__construct();
        $this->load->model('Disposal_methods_model', 'dm');
        $this->load->helper(['url','form']);
        $this->load->library('session');
    }

    // LIST + CREATE + EDIT (one page)
    public function index($mode = 'list', $id = null)
    {
        $data = [];
        $data['mode'] = $mode;

        if ($mode === 'edit' && $id !== null) {
            $data['method'] = $this->dm->get($id);
        }

        $data['methods'] = $this->dm->get_all();

        $this->load->view('header', [
            'title' => 'Disposal Methods',
            'title2' => 'Disposal Methods'
        ]);
        $this->load->view('disposal_methods_list', $data);
        $this->load->view('footer');
    }

    // CREATE or UPDATE
    public function save()
    {
        $id = $this->input->post('id');

        $data = [
            'disposal_method' => $this->input->post('disposal_method'),
            'description'     => $this->input->post('description'),
            'updated_at'      => date('Y-m-d H:i:s')
        ];

        if ($id) {
            $this->dm->update($id, $data);
            $this->session->set_flashdata('success', 'Disposal Method updated successfully.');
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $this->dm->insert($data);
            $this->session->set_flashdata('success', 'Disposal Method added successfully.');
        }

        redirect('DisposalMethod');
    }

    // DELETE
    public function delete($id)
    {
        $this->dm->delete($id);
        $this->session->set_flashdata('success', 'Disposal Method deleted successfully.');
        redirect('DisposalMethod');
    }
}
?>
