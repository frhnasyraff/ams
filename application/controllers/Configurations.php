<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Configurations extends CI_Controller {

    public function __construct() {
		parent::__construct();
		
		// Admin only
			if (!$this->user_model->logged_in()) {
			if ($this->input->is_ajax_request()) {
				header('HTTP/1.1 500 Internal Server Ayyoh');
			die(json_encode(["redirect" => "/"])); 
			}
				die(redirect("/"));
		}
	}
    
	public function index()
	{
		$this->load->view('header', ['title' => "Configurations", "styles" => []]);
		$this->load->view('configurations', ['config' => $this->pollstar->get_configs()]);
		$this->load->view('footer', ['scripts' => ['design/js/configurations.js', 'design/js/bootstrap-toggle.min.js', 'design/js/jquery.bootstrap-growl.min.js']]);
	}
	
    public function toggle_config() {
      $resp = $this->API->POST("admin/configs/update", $_POST);

			if ($resp && $resp->state) {
          die(json_encode($resp));
      }
    }
	
		public function user_state_ajax() {
		$resp = $this->API->POST("admin/users/update", $_POST);
    if ($resp) {
			die(json_encode($resp));
		}
	}
}
