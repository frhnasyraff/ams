<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Orders extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if (!verifyJWT()) {
        //     errorResponse('missing or invalid token', [], 401);
        // }

        // to remember
        // controllers/orders, views/orders,  js/orders-list
        // xampp php 7.4, uer db
        // filezilla creds
        // db migration: nothing to note, check orderlogs 
        // uer assets count (last commit)
        // search (check from scratch)
        // swm789**  -- rams -- swm123 34.101.122.218
        // uer -- 

        $this->load->helper(array('form', 'file'));
        $this->load->library('form_validation');
    }

    public function getOrdersByDateStatusOrDriver()
    {
        // request validation 
        $this->form_validation->set_rules("status", "status", "required");
        $this->form_validation->set_rules("start_date", "start_date", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $status = $this->input->post('status');
        $start_date = $this->input->post('start_date');
        $new_date = date('Y-m-d', strtotime('+4 hours', strtotime($start_date)));

        $orders = $this->db->select('orders.order_id, orders.order_num, 
        orders.start_date, orders.order_type, orders.second_order_type, orders.status AS order_status, orders.same_bin, companies.company_id, companies.company_name,companies.company_code, 
        company_addresses.company_address_id, company_addresses.address_line_1, company_addresses.person_contact, company_addresses.mobile, company_addresses.latitude, company_addresses.longitude,
        main_service_types.main_service_type_id,main_service_types.main_service_type_name,
        service_types.service_type_id,service_types.service_type_name, workers.worker_id, workers.worker_name, 
        workers.worker_photo, equipments.equipment_id, equipments.equipment_name, 
        equipments.equipment_picture,sos.status,sos.type')
            ->from('orders')
            ->join('sos', 'sos.order_id=orders.order_id', 'LEFT')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
            ->join('main_service_types', 'main_service_types.main_service_type_id=service_types.main_service_type_id', 'LEFT')
            ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
            ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
            ->join('equipments', 'equipments.equipment_id = order_drivers.truck_id', 'LEFT')
            ->where("CASE WHEN service_types.service_type_name = 'Waste Disposal Service - Compactor' THEN orders.start_date = '" . $new_date . "' ELSE orders.start_date = '" . date('Y-m-d', strtotime($start_date)) . "' END")
            ->where('orders.status', $status)
            ->where('companies.status', 0);


        if ($this->input->post('driver_id')) {
            $this->db->where('order_drivers.driver_id', $this->input->post('driver_id'));
        }

        $orders = $this->db->group_by('orders.order_id')->get()->result();

        foreach ($orders as $key => $order) {
            $orders[$key]->worker_photo =  site_url('storage/Driver-' . $order->worker_id . '/' . $order->worker_photo);
            $orders[$key]->equipment_picture = site_url('storage/Truck-') . $order->equipment_id . '/' . $order->equipment_picture;
            $orders[$key]->bin_quantity = $this->db->where("order_id", $order->order_id)->count_all_results("order_equipment_bin_qr_codes");

            if ($order->second_order_type == 'pullback') {
                $orders[$key]->service_type_name  = 'pullback';
            }

            // asset types of current order
            $asset_types = $this->db->select('asset_types.asset_id, asset_types.name')
                ->from('orders')
                ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
                ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
                ->where('orders.order_id', $order->order_id)
                ->get()
                ->result();
            $orders[$key]->asset_types = $asset_types;

            // check if driver params available
            if ($this->input->post('driver_id')) {
                $total = $this->db->where('order_id', $order->order_id)->where('driver_id', $order->worker_id)->where('checkout_time IS NULL')->count_all_results("checkin_checkout");
                $orders[$key]->isCheckIn =  $total > 0 ? true : false;
            }
        }


        successResponse('list orders', $orders);
    }

    public function getOrdersByDriverId()
    {
        // request validation 
        $this->form_validation->set_rules("driver_id", "driver_id", "required|integer");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $driver_id = $this->input->post('driver_id');
        $orders = $this->db->select('orders.order_id, orders.order_num , orders.order_type, orders.status, orders.second_order_type, companies.company_name,companies.company_code, 
        company_addresses.address_line_1,
        company_addresses.person_contact, company_addresses.mobile,
        main_service_types.main_service_type_id,main_service_types.main_service_type_name, 
        service_types.service_type_name, 
        workers.worker_id,  workers.worker_name, workers.worker_photo, equipments.equipment_id, equipments.equipment_name, equipments.equipment_picture')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
            ->join('main_service_types', 'main_service_types.main_service_type_id=service_types.main_service_type_id', 'LEFT')
            ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
            ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
            ->join('equipments', 'equipments.equipment_id = order_drivers.truck_id', 'LEFT')
            ->where('workers.worker_id', $driver_id)
            ->where('companies.status', 0)
            ->get()
            ->result();

        foreach ($orders as $key => $order) {
            $orders[$key]->worker_photo =  site_url('storage/Driver-' . $order->worker_id . '/' . $order->worker_photo);
            $orders[$key]->equipment_picture = site_url('storage/Truck-') . $order->equipment_id . '/' . $order->equipment_picture;
            $orders[$key]->bin_quantity = $this->db->where("order_id", $order->order_id)->count_all_results("order_equipment_bin_qr_codes");
            if ($order->second_order_type == 'pullback') {
                $orders[$key]->service_type_name  = 'pullback';
            }
        }
        successResponse('list orders', $orders);
    }

    public function getOrdersByDriverIdStatus()
    {
        // request validation 
        $this->form_validation->set_rules("driver_id", "driver_id", "required|integer");
        $this->form_validation->set_rules("status", "status", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $driver_id = $this->input->post('driver_id');
        $status = $this->input->post('status');

        $orders = $this->db->select('orders.order_id, orders.order_num , orders.order_type, orders.status, orders.second_order_type, 
        companies.company_name,companies.company_code, company_addresses.address_line_1, 
        company_addresses.person_contact, company_addresses.mobile,
        main_service_types.main_service_type_id,main_service_types.main_service_type_name,
        service_types.service_type_name,workers.worker_id, 
        workers.worker_name, workers.worker_photo, workers.worker_id, equipments.equipment_id, equipments.equipment_name, equipments.equipment_picture')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
            ->join('main_service_types', 'main_service_types.main_service_type_id=service_types.main_service_type_id', 'LEFT')
            ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
            ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
            ->join('equipments', 'equipments.equipment_id = order_drivers.truck_id', 'LEFT')
            ->where('order_drivers.driver_id', $driver_id)
            ->where('orders.status', $status)
            ->where('companies.status', 0)
            ->get()
            ->result();


        foreach ($orders as $key => $order) {
            $orders[$key]->worker_photo =  site_url('storage/Driver-' . $order->worker_id . '/' . $order->worker_photo);
            $orders[$key]->equipment_picture = site_url('storage/Truck-') . $order->equipment_id . '/' . $order->equipment_picture;
            $orders[$key]->bin_quantity = $this->db->where("order_id", $order->order_id)->count_all_results("order_equipment_bin_qr_codes");
            if ($order->second_order_type == 'pullback') {
                $orders[$key]->service_type_name  = 'pullback';
            }
        }

        successResponse('list orders', $orders);
    }

    public function getQRCodesByOrderId()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "order_id", "required|integer");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $id = $this->input->post('order_id');
        $order = $this->db->select('orders.order_id, orders.order_num , orders.order_type, orders.status, orders.second_order_type, companies.company_name,companies.company_code, company_addresses.address_line_1, service_types.service_type_name, workers.worker_id, workers.worker_name, workers.worker_photo, equipments.equipment_id, equipments.equipment_name, equipments.equipment_picture')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
            ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
            ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
            ->join('equipments', 'equipments.equipment_id = order_drivers.truck_id', 'LEFT')
            ->where('orders.order_id', $id)
            ->where('companies.status', 0)
            ->get()
            ->row();

        if ($order) {
            $order->worker_photo =  site_url('storage/Driver-' . $order->worker_id . '/' . $order->worker_photo);
            $order->equipment_picture = site_url('storage/Truck-') . $order->equipment_id . '/' . $order->equipment_picture;
            if ($order->second_order_type == 'pullback') {
                $order->service_type_name  = 'pullback';
            }
        }

        $order_qrcodes = $this->db->select('order_equipment_bin_qr_codes.order_equipment_bin_qr_codes_id, order_equipment_bin_qr_codes.qr_code, asset_types.name')
            ->from('orders')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
            ->where('orders.order_id', $id)
            ->get()
            ->result();

        $data['order'] = $order;
        $data['qr_codes'] = $order_qrcodes;

        successResponse('list orders', $data);
    }

    public function updateOrderStatus()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "order_id", "required|integer");
        $this->form_validation->set_rules("status", "status", "required|integer");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $status = $this->input->post('status');

        if ($status == 1) {
            $field = 'planned_at';
        } else if ($status == 2) {
            $field = 'progress_at';
            $this->db-> set( 'time_start',date('Y-m-d H:i:s'));
            $this->db->where('order_id', $order_id);
            $this->db->update('worker_assignment_history');
            

        } else if ($status == 3) {
            $field = 'completed_at';
            $this->db-> set( 'time_end',date('Y-m-d H:i:s'));
            $this->db->where('order_id', $order_id);
            $this->db->update('worker_assignment_history');
        }

        $this->db->where('order_id', $order_id);
        $this->db->update('orders', [
            'status' => $status,
            $field => date('Y-m-d H:i:s')
        ]);

        successResponse('order status updated', [], 200);
    }

    public function uploadQrCodeByEquipmentBinId()
    {
        // request validation 
        $this->form_validation->set_rules("equipment_bin_qr_code_id", "equipment_bin_qr_code_id", "required");
        $this->form_validation->set_rules('file', 'QR CODE FILE', 'callback_file_check');


        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $equipment_bin_qr_code_id = $this->input->post('equipment_bin_qr_code_id');

        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fileName = randomImageName($extension);

        $this->load->library('upload');
        $config['upload_path'] = './storage/qrcodes/';
        $config['allowed_types'] = 'jpg|png|jpeg|PNG';
        $config['file_name'] = $fileName;
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file')) {
            $errors = $this->upload->display_errors();
            errorResponse('not uploaded', $errors, 409);
        }

        $this->db->where('order_equipment_bin_qr_codes_id', $equipment_bin_qr_code_id);
        $this->db->update('order_equipment_bin_qr_codes', [
            'qr_code' => $fileName,
        ]);

        successResponse('uploaded', [
            'name' => $fileName
        ]);
    }

    public function deleteQrCodeByEquipmentBinId()
    {
        // request validation 
        $this->form_validation->set_rules("equipment_bin_qr_code_id", "equipment_bin_qr_code_id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $equipment_bin_qr_code_id = $this->input->post('equipment_bin_qr_code_id');

        $equipment_bin = $this->db->select('qr_code')
            ->from('order_equipment_bin_qr_codes')
            ->where('order_equipment_bin_qr_codes_id', $equipment_bin_qr_code_id)
            ->get()
            ->row();

        @unlink(base_url('storage/qrcodes/' . $equipment_bin->qr_code));

        $this->db->where("order_equipment_bin_qr_codes_id", $equipment_bin_qr_code_id);
        $this->db->update("order_equipment_bin_qr_codes", ["qr_code" => NULL]);

        successResponse('deleted', []);
    }

    public function getSiteImages()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required|integer");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');

        $orders = $this->db->select('order_site_images.*')
            ->from('order_site_images')
            ->where('order_site_images.order_id', $order_id)
            ->get()
            ->result();

        foreach ($orders as $key => $order) {
            $orders[$key]->image_path = site_url() . 'storage/site/' . $order->order_site_image;
        }

        successResponse('list site images', $orders);
    }

    public function uploadSiteImage()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "order_id", "required|integer");
        $this->form_validation->set_rules("type", "Type", "required");
        $this->form_validation->set_rules('file', 'file', 'callback_file_check');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $type =  $this->input->post('type'); // before, after

        $this->load->library('upload');
        $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        $fileName = randomImageName($extension);
        $config['upload_path'] = './storage/site/';
        $config['allowed_types'] = 'jpg|png|jpeg|PNG';
        $config['file_name'] = $fileName;
        $this->upload->initialize($config);

        if (!$this->upload->do_upload('file')) {
            $error = array('error' => $this->upload->display_errors());
            errorResponse('not uploaded', $error, 409);
        }

        $this->db->set('order_id', $order_id);
        $this->db->set('order_site_image', $fileName);
        $this->db->set('type', $type);
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('order_site_images');

        $data['name'] = $fileName;
        successResponse('uploaded', $data);
    }

    public function deleteSiteImage()
{
    // request validation 
    $this->form_validation->set_rules("order_site_image_id", "order_site_image_id", "required");

    if ($this->form_validation->run() == FALSE) {
        errorResponse('data not validated', $this->form_validation->error_array());
    }

    $order_site_image_id = $this->input->post('order_site_image_id');

    // Fetch the image name based on the given ID
    $order_site_image = $this->db->select('order_site_image')
        ->from('order_site_images')
        ->where('order_site_image_id', $order_site_image_id)
        ->get()
        ->row();

    if (!$order_site_image) {
        errorResponse('Image not found', []);
    }

    // Correct the file path to use absolute server path instead of base_url
    $file_path = FCPATH . 'storage/sites/' . $order_site_image->order_site_image;

    // Check if file exists before attempting to delete
    if (file_exists($file_path)) {
        if (@unlink($file_path)) {
            // File successfully deleted
            log_message('info', 'File deleted: ' . $file_path);
        } else {
            // Handle unlink failure
            log_message('error', 'Failed to delete file: ' . $file_path);
            errorResponse('Failed to delete the file', []);
        }
    } else {
        log_message('error', 'File not found: ' . $file_path);
    }

    // Delete the image record from the database
    $this->db->where("order_site_image_id", $order_site_image_id);
    $this->db->delete("order_site_images");

    successResponse('Image deleted successfully', []);
}


    public function addOrderTipping()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "order_id", "required|integer");
        // $this->form_validation->set_rules("asset_name", "asset_name", "required");
        // $this->form_validation->set_rules("asset_number", "asset_number", "required");
        // $this->form_validation->set_rules("weight", "weight", "required");
        $this->form_validation->set_rules("tipping_fee", "tipping_fee", "numeric");
        // $this->form_validation->set_rules('file', 'file', 'callback_file_check');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $asset_name = $this->input->post('asset_name');
        $asset_number = $this->input->post('asset_number');
        $weight = ($this->input->post('weight') == '') ? null : $this->input->post('weight');
        $tipping_fee = ($this->input->post('tipping_fee') == '') ? null : $this->input->post('tipping_fee');


        $this->load->library('upload');
        if (!empty($_FILES['file']['name'])) {
            $extension = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
            $fileName = randomImageName($extension);
            $config['upload_path'] = './storage/tipping/';
            $config['allowed_types'] = 'jpg|png|jpeg|PNG';
            $config['file_name'] = $fileName;
            $this->upload->initialize($config);

            if (!$this->upload->do_upload('file')) {
                $error = array('error' => $this->upload->display_errors());
                errorResponse('not uploaded', $error, 409);
            }
            $this->db->set('tipping_qr_image', $fileName);
        }

        $this->db->set('order_id', $order_id);
        $this->db->set('asset_name', $asset_name);
        $this->db->set('asset_number', $asset_number);
        $this->db->set('weight', $weight);
        $this->db->set('tipping_fee', $tipping_fee);
        $this->db->set('created_at', date('Y-m-d H:i:s'));

        $this->db->insert('order_tipping');

        successResponse('Successfully Inserted', []);
    }

    public function listScannedQrCode()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required|integer");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $assets = $this->db->select('order_equipment_bin_qr_codes.order_equipment_bin_qr_codes_id, asset_types.name, order_equipment_bin_qr_codes.reg_no')
            ->from('order_equipment_bin_qr_codes')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
            ->where('order_equipment_bin_qr_codes.order_id', $order_id)
            ->where('order_equipment_bin_qr_codes.scanned', 1)
            ->get()
            ->result();

        successResponse('Scanned assets', $assets);
    }

    public function scanQrCode()
    {

        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required|integer");
        $this->form_validation->set_rules("reg_no", "Registration No", "required");
        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $reg_no = $this->input->post('reg_no');

        // if order pull back or not
        $is_pull_back = $this->db->from('orders')->where('orders.order_id', $order_id)->where('second_order_type', 'pullback')->get()->num_rows() > 0 ? true : false;

        // get asset type of registration no
        $asset = $this->db->select('asset_types.name, asset_types.asset_id, equipments_asset.equipment_registration')
            ->from('asset_types')
            ->join('equipments_asset', 'equipments_asset.equipment_type = asset_types.asset_id')
            ->join('order_equipment_bin_qr_codes', 'order_equipment_bin_qr_codes.asset_type_id = asset_types.asset_id')
            ->where('equipments_asset.equipment_registration', $reg_no)
            ->where('order_equipment_bin_qr_codes.order_id', $order_id)
            ->get()
            ->row();

        if (!$asset) {
            errorResponse('Invalid registration number', [], 404);
        }

        //if bin scanned for same order
        $bin_qr_codes = $this->db
            ->from('order_equipment_bin_qr_codes')
            ->where('reg_no', $reg_no)
            ->where('order_id', $order_id)
            ->where('asset_type_id', $asset->asset_id)
            ->get()
            ->row();

        // if bin scanned for an other order    
        $bin_qr_codes_order = $this->db
            ->from('order_equipment_bin_qr_codes')
            ->where('reg_no', $reg_no)
            ->where('order_id <>', $order_id)
            ->get()
            ->row();

        $isInUse = $this->db
            ->from('equipments_asset')
            ->where('equipment_registration', $reg_no)
            ->where('status', 'In use')
            ->get()
            ->row();

        if ($is_pull_back) {
            if ($bin_qr_codes) {
                errorResponse('bin already scanned for an other bin', [], 200);
            }

            // check if this registration no already scanned for specific order and before 12 hours 
            // $last_12_hours = date('Y-m-d H:i:s', strtotime('-12 hours'));

            // if (
            //     $this->db->from('order_equipment_bin_qr_codes')->where('updated_at >', $last_12_hours)
            //     ->where('order_equipment_bin_qr_codes.reg_no', $reg_no)->get()->num_rows() > 0
            // ) {
            //     errorResponse('bin already scanned for an other order', [], 200);
            // }

            // set equipments_asset status to "Available"
            $this->db->where('equipment_registration', $reg_no);
            $this->db->update('equipments_asset', ['equipment_status' => 'Available']);
        } else {

            // check if equipments_asset status is "Sold"
            if ($this->db->where("equipment_registration", $reg_no)->where('equipment_status', 'Sold')->count_all_results("equipments_asset") > 0) {
                errorResponse('This asset has sold', [], 200);
            }
            // check if equipments_asset status is "In use"
            else if ($this->db->where("equipment_registration", $reg_no)->where('equipment_status', 'In use')->count_all_results("equipments_asset") > 0) {

                // if registration number is used by same client having same address then for that client, reg no can be rescanned 

                // used registraton number company 
                $used_company = $this->db->select('order_equipment_bin_qr_codes.order_id, order_equipment_bin_qr_codes.reg_no, orders.company_id, orders.company_address_id')
                    ->from('orders')
                    ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
                    ->where('order_equipment_bin_qr_codes.reg_no', $reg_no)
                    ->order_by('updated_at', 'desc')
                    ->limit(1)
                    ->get()
                    ->row();

                // currenct order company
                $current_company = $this->db->select('orders.company_id, orders.company_address_id')
                    ->from('orders')
                    ->where('orders.order_id', $order_id)
                    ->limit(1)
                    ->get()
                    ->row();

                if (($used_company->company_id == $current_company->company_id) && ($used_company->company_address_id == $current_company->company_address_id)) {
                    // bin mark as scanned
                    $this->markBinScanned($order_id, $asset->asset_id, $reg_no);
                } else {
                    errorResponse('This asset is in use', [], 200);
                }
            }

            // check if this registration no already scanned for another order
            if ($bin_qr_codes_order && $isInUse) {
                errorResponse('bin already scanned for an other order', [], 200);
            }
            // check if this registration no already scanned for same order
            if (($bin_qr_codes || $this->db->from('order_equipment_bin_qr_codes')->where('order_equipment_bin_qr_codes.reg_no', $reg_no)->get()->num_rows() > 0) && $isInUse) {
                errorResponse('bin already scanned for an other bin', [], 200);
            }

            // check if service_type is "Sales Bin" 
            $service_type = $this->db->select('service_types.service_type_name')
                ->from('orders')
                ->join('service_types', 'orders.service_type_id = service_types.service_type_id')
                ->where('orders.order_id', $order_id)
                ->get()
                ->row();

            if ($service_type && $service_type->service_type_name == 'Sales Bin') {
                // set equipments_asset status to "Sold"
                $this->db->where('equipment_registration', $reg_no);
                $this->db->update('equipments_asset', ['equipment_status' => 'Sold']);
            } else {
                // set equipments_asset status to "In use"
                $this->db->where('equipment_registration', $reg_no);
                $this->db->update('equipments_asset', ['equipment_status' => 'In use']);
            }
        }

        // bin mark as scanned
        $this->markBinScanned($order_id, $asset->asset_id, $reg_no);
    }

    public function deleteScannedQrCode()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("reg_no", "Registration Number", "required");

        $order_id = $this->input->post('order_id');
        $reg_no = $this->input->post('reg_no');

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        // qr code mark as scanned
        $this->db->where('order_id', $order_id);
        $this->db->where('reg_no', $reg_no);
        $this->db->update('order_equipment_bin_qr_codes', [
            'reg_no' => NULL,
            'scanned' => 0,
            'updated_at' => NULL,
        ]);

        // set equipment asset status to "Available"
        $this->db->where('equipment_registration', $reg_no);
        $this->db->update('equipments_asset', ['equipment_status' => 'Available']);

        successResponse('bin mark as unscanned ', []);
    }

    // File Validation
    public function file_check($str)
    {
        $allowed_mime_type_arr = array('image/jpeg', 'image/pjpeg', 'image/png', 'image/x-png');
        $mime = get_mime_by_extension($_FILES['file']['name']);
        if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != "") {
            if (in_array($mime, $allowed_mime_type_arr)) {
                return true;
            } else {
                $this->form_validation->set_message('file_check', 'Please select only jpg/png file.');
                return false;
            }
        } else {
            $this->form_validation->set_message('file_check', 'Please choose a file to upload.');
            return false;
        }
    }

    public function changeBinStatus()
    {
        // request validation 
        $this->form_validation->set_rules("status", "Status", "required");
        $this->form_validation->set_rules("reg_no", "Registration Number", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $reg_no = $this->input->post('reg_no');
        $status = $this->input->post('status');

        if (!in_array($status, ['In use', 'Maintenance', 'Standby', 'Available', 'Repair', 'Sold', 'Dispose', 'Scrap'])) {
            errorResponse("bin status should be in ('In use','Maintenance','Standby','Available','Repair','Sold', 'Dispose', 'Scrap')", [], 200);
        }

        // set equipments_asset status to "In use"
        $this->db->where('equipment_registration', $reg_no);
        $this->db->update('equipments_asset', ['equipment_status' => $status]);

        successResponse('bin status changed ', []);
    }

    private function markBinScanned($order_id, $asset_id, $reg_no)
    {
        $this->db
            ->where('scanned', 0)
            ->where('order_equipment_bin_qr_codes.order_id', $order_id)
            ->where('order_equipment_bin_qr_codes.asset_type_id', $asset_id)
            ->limit(1)
            ->update('order_equipment_bin_qr_codes', [
                'scanned' => 1,
                'reg_no' => $reg_no,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

        //asset usage start

        if (!empty($reg_no)) {

            $orders_query = $this->db->select('orders.*, order_drivers.*,companies.*')
                ->from('orders')
                ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
                ->join('companies', 'companies.company_id=orders.company_id')
                ->where('orders.order_id', $order_id)
                ->get(); // Execute the query

            $res = $orders_query->row();
            $address = $res->company_name;
            $start_time = $res->progress_at;
            $end_time = $res->completed_at;
            $queryR = $this->db->select('*')
                ->from('equipments_asset')
                ->like(
                    'equipment_registration',
                    trim($reg_no)
                )
                ->get();
            $equipment = $queryR->row();
            if (!empty($equipment)) {
                $orders_query = $this->db->select('*')
                    ->from('workers')
                    ->where('worker_id', $res->driver_id)
                    ->get(); // Execute the query


                $res3 = $orders_query->row();
                // print_r($res3->ic_number);
                // die;

                if ($start_time != '') {
                    $datetime_obj = new DateTime($start_time);

                    $start_date = $datetime_obj->format('Y-m-d');
                    $start_time = $datetime_obj->format('h:i A');
                    $this->db->set('vh_date', $start_date);
                    $this->db->set('vh_time_start', $start_time);
                }
                if ($end_time != '') {
                    $datetime_obj2 = new DateTime($end_time);

                    $end_date = $datetime_obj2->format('Y-m-d');
                    $end_time = $datetime_obj2->format('h:i A');
                    $this->db->set('vh_date_end', $end_date);
                    $this->db->set('vh_time_end', $end_time);
                }
                $this->db->set('driver_id', $res->driver_id);
                $this->db->set('equipment_id', $equipment->equipment_id);
                $this->db->set('vh_location_end', $address);
                $this->db->set('vh_driver_name_ic_number', $res3->ic_number);
                $this->db->insert('vehicle_history_asset');
            }
        }
        //asset usage end

        successResponse('bin scanned successfully', ['registration' => $reg_no]);
    }

    public function getCompanyCompactorOrderAssets()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("company_id", "Company Id", "required");
        $this->form_validation->set_rules("company_address_id", "Company Addresss Id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        // check if order belongs to same company and address
        $company_order_exists = $this->db->select('1')
            ->from('orders')
            ->where('order_id', $this->input->post('order_id'))
            ->where('company_id', $this->input->post('company_id'))
            ->where('company_address_id', $this->input->post('company_address_id'))
            ->get()
            ->num_rows();

        if ($company_order_exists <= 0) {
            errorResponse('order not belongs to this company or company addresss', []);
        }

        $orders = $this->db->select('compactor_company_address.quantity, compactor_company_address.order_id, asset_types.name, company_addresses.address_line_1')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id')
            ->join('compactor_company_address', 'compactor_company_address.company_id = companies.company_id')
            ->join('company_addresses', 'compactor_company_address.company_address_id=company_addresses.company_address_id')
            ->join('asset_types', 'compactor_company_address.asset_type_id = asset_types.asset_id')
            ->where('orders.order_id', $this->input->post('order_id'))
            ->where('orders.company_id', $this->input->post('company_id'))
            ->where('orders.company_address_id', $this->input->post('company_address_id'))
            ->where('companies.status', 0)
            ->where('compactor_company_address.order_id IS NULL')
            ->get()
            ->result();

        successResponse('Assets types', $orders);
    }

    public function getCompactorAssetsByorderId()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $orders = $this->db->select('compactor_company_address.quantity, asset_types.name, company_addresses.address_line_1')
            ->from('compactor_company_address')
            ->join('companies', 'compactor_company_address.company_id=companies.company_id')
            ->join('company_addresses', 'compactor_company_address.company_address_id=company_addresses.company_address_id')
            ->join('asset_types', 'compactor_company_address.asset_type_id = asset_types.asset_id')
            ->where('compactor_company_address.order_id', $this->input->post('order_id'))
            ->get()
            ->result();

        successResponse('Compactor order assets', $orders);
    }


    public function addCompanyCompactorOrderAssets()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("bin_type_id", "Bin Type Id", "required");
        $this->form_validation->set_rules("quantity", "Quantity", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        // get company_id and company_address id by order id
        $company = $this->db->select('company_addresses.company_address_id, companies.company_id')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id')
            ->where('order_id', $this->input->post('order_id'))
            ->where('companies.status', 0)
            ->get()
            ->row();

        if ($company) {
            $company_id = $company->company_id;
            $address_id = $company->company_address_id;
            $bin_type_id = $this->input->post('bin_type_id');
            $quantity = $this->input->post('quantity');

            $this->db->set('company_id', $company_id);
            $this->db->set('company_address_id', $address_id);
            $this->db->set('asset_type_id', $bin_type_id);
            $this->db->set('quantity', $quantity);
            $this->db->set('order_id', $this->input->post('order_id'));
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('compactor_company_address');


            successResponse('compactor info added successfully', []);
        } else {
            errorResponse('addresss not found for this order', []);
        }
    }


    public function addCompanyQuantityImages()
    {
        // request validation 
        $this->form_validation->set_rules("company_id", "Company Id", "required");
        $this->form_validation->set_rules("quantity", "Quantity", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }


        $data = array();
        $count = count($_FILES['files']['name']);
        for ($i = 0; $i < $count; $i++) {
            if (!empty($_FILES['files']['name'][$i])) {

                $_FILES['file']['name'] = $_FILES['files']['name'][$i];
                $_FILES['file']['type'] = $_FILES['files']['type'][$i];
                $_FILES['file']['tmp_name'] = $_FILES['files']['tmp_name'][$i];
                $_FILES['file']['error'] = $_FILES['files']['error'][$i];
                $_FILES['file']['size'] = $_FILES['files']['size'][$i];

                $extension = pathinfo($_FILES['files']['name'][$i], PATHINFO_EXTENSION);
                $filename = randomImageName($extension);

                $this->load->library('upload');
                $config['upload_path'] = './storage/compayquantity/';
                $config['allowed_types'] = 'jpg|png|jpeg|PNG';
                $config['file_name'] = $filename;
                $this->upload->initialize($config);

                if (!$this->upload->do_upload('file')) {
                    $errors = $this->upload->display_errors();
                    errorResponse('not uploaded', $errors, 422);
                } else {
                    $data[] = $filename;
                }
            }
        }

        // insert company_id and quantity
        $this->db->set('company_id', $this->input->post('company_id'));
        $this->db->set('quantity',  $this->input->post('quantity'));
        $this->db->set('created_at', date('Y-m-d H:i:s'));
        $this->db->insert('company_quantity');
        $insert_id = $this->db->insert_id();

        foreach ($data as $img) {
            $this->db->set('company_quantity_id', $insert_id);
            $this->db->set('image',  $img);
            $this->db->set('created_at', date('Y-m-d H:i:s'));
            $this->db->insert('company_quantity_images');
        }

        successResponse('data inserted', []);
    }


    public function getAssetTypes()
    {
        $asset_types = $this->db->select('*')
            ->from('asset_types')
            ->where('active', 1)
            ->get()
            ->result();

        successResponse('Assets types', $asset_types);
    }
    public function updateBinProcess()
    {

        $sameBin = $this->input->post('same_bin');
        $orderId = $this->input->post('order_id');

        ($sameBin == 'false') ? $sameBin = 0 : $sameBin = 1;

        $this->db->set('same_bin', $sameBin);
        $this->db->where('order_id', $orderId);
        $check = $this->db->update('orders');
        // errorResponse('teri pain di siri', [$sameBin, $orderId], 500);
        successResponse('Same Bin updated', [$sameBin, $orderId]);
    }
}
