<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assets_type_dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_equipments')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        // Store fetched data in the $data array to pass to the view

        // Load views with $data
        $this->load->view('header', [
            'title' => 'Assets Dashboard',
            'title2' => 'List of Assets',
            'styles' => [
                'design/vendor/dropzone/min/dropzone.min.css',
                'design/css/datepicker.css',
                'design/css/assets-type-dashboard.css?v=card-ref3',
                
            ]
        ]);

        $this->load->view('assets-type-dashboard',[ ]);
       

        $this->load->view('footer', [
            'scripts' => [
                'design/vendor/dropzone/min/dropzone.min.js',
                'design/js/datepicker.js',
                'design/js/assets-type-dashboard.js',
                'design/js/assets-list.js',
                'design/js/dashboard-upload-pills.js'

            ]
        ]);
    }

    public function asset_type_picture()
    {
        if ($this->input->post('id')) {
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']['tmp_name'];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . '-' . basename($_FILES['file']['name']);

                $folder = realpath('storage') . '/AssetType-' . $this->input->post('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . '/' . $name)) {
                    $this->db->set('asset_picture', $name);
                    $this->db->where('asset_id', $this->input->post('id'));

                    if ($this->db->update('asset_types')) {
                        $this->logs->add('ASSETSTYPE', $this->input->post('id'), 'ASSET_TYPE_PHOTO_UPLOADED', 'A new photo was uploaded.');
                    }
                }
            }
        }
    }

}
