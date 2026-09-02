<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oops extends CI_Controller {

    public function __construct() {
		parent::__construct();
		
        	}
    
	public function index()
	{
		if (isset($_GET['data'])) {
			die(redirect("/bookings?data=" . urlencode($_GET['data'])));
		}
		
       $this->output->set_status_header('404'); 
	   $this->load->view('header');
	   $this->load->view('404');
	   $this->load->view('footer');
	}
	public function api_gone()
	{
       $this->output->set_status_header('500'); 
    $this->load->view('500');
	}
    
   
}
