<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_disposals extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('user_model');
        $this->load->model('Asset_disposals_model');

        if (!$this->user_model->logged_in()) {
            redirect('login');
        }
    }

    public function index()
    {
        $search = $this->input->get('search');
        $status = $this->input->get('status');

        $config['base_url'] = site_url('asset_disposals');
        $config['total_rows'] = $this->Asset_disposals_model->count_all($search, $status);
        $config['per_page'] = 10;
        $config['uri_segment'] = 2;
        $config['reuse_query_string'] = TRUE;
        $config['first_link'] = '1';
        $config['last_link'] = (string) max(1, (int) ceil($config['total_rows'] / $config['per_page']));

        $this->pagination->initialize($config);

        $page = ($this->uri->segment(2)) ? $this->uri->segment(2) : 0;

        $data = [
            'title' => 'Disposals List',
            'title2' => 'Disposals List',
            'disposals' => $this->Asset_disposals_model->get_all($config['per_page'], $page, $search, $status),
            'pagination' => $this->pagination->create_links(),
            'total_rows' => $config['total_rows'],
            'page' => $page,
            'search' => $search,
            'status' => $status,
        ];

        $this->load->view('header', $data);
        $this->load->view('asset-disposals-list', $data);
        $this->load->view('footer');
    }
}
?>
