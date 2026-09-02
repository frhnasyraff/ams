<?php

defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if (!verifyJWT()) {
        //     errorResponse('missing or invalid token', [], 401);
        // }
        $this->load->library('form_validation');
    }

    public function getUserById()
    {
        // request validation 
        $this->form_validation->set_rules("id", "id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $id = $this->input->post('id');
        $user = $this->db->from('users')->where('user_id', $id)->get()->row();
        if ($user) {
            $data['id'] = $user->user_id;
            $data['username'] = $user->username;
            $data['email'] = $user->email;
            $data['user_code'] = $user->user_code;
            $data['photo'] = site_url('storage/User-' . $user->user_id . '/' . $user->profile_picture);

            // get worker_photo
            if ($user->user_code == 'DRIVER') {
                $user = $this->db->from('users')->where('user_id', $user->user_id)->where('user_code', 'DRIVER')->get()->row();
                if ($user) {
                    $worker_user = $this->db->select('*')->from('worker_user')->where('user_id', $user->user_id)->get()->row();
                    $worker = $this->db->select('*')->from('workers')->where('worker_id', $worker_user->worker_id)->get()->row();
                    $data['photo'] = site_url('storage/Driver-' . $worker->worker_id . '/' . $worker->worker_photo);
                }
            }
            successResponse('User Detail', $data, 200);
        }
        errorResponse('User not found', [], 404);
    }
}
