<?php

defined('BASEPATH') or exit('No direct script access allowed');

use Firebase\JWT\JWT;

class Driver extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        // if (!verifyJWT()) {
        //     errorResponse('missing or invalid token', [], 401);
        // }
        $this->load->library('form_validation');
    }

    public function updatePassword()
    {
        // request validation 
        $this->form_validation->set_rules("user_id", "User ID", "required");
        $this->form_validation->set_rules("old_password", "Old Password", "required");
        $this->form_validation->set_rules("new_password", "New Password", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $user_id = $this->input->post('user_id');
        $old_password = $this->input->post('old_password');
        $new_password = $this->input->post('new_password');

        // validate old password
        $query = $this->db->from('users')->where('user_id', $user_id)->where('user_code', 'DRIVER')->get();
        if ($query->num_rows() > 0) {
            $user = $query->row();
            if (password_verify($old_password, $user->password)) {
                // update password
                $this->db->where('user_id', $user_id)->where('user_code', 'DRIVER');
                $this->db->update('users', [
                    'password' => password_hash($new_password, PASSWORD_DEFAULT)
                ]);
                $this->db->where('user_id', $user_id);
                $this->db->update('worker_user', ['password_updated' => 1]);

                successResponse('Password updated successfully', [], 200);
            } else {
                errorResponse('invalid credentials', [], 401);
            }
        } else {
            errorResponse('invalid credentials', [], 401);
        }
    }

    public function freeCheckIn()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        $this->form_validation->set_rules("latitude", "Latitude", "required");
        $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        $check_in_latitude = $this->input->post('latitude');
        $check_in_longitude = $this->input->post('longitude');

        // check in
        $this->db->set('order_id', $order_id);
        $this->db->set('driver_id', $driver_id);
        $this->db->set('latitude', $check_in_latitude);
        $this->db->set('longitude', $check_in_longitude);
        $this->db->set('checkin_time', date('Y-m-d H:i:s'));
        $this->db->insert('checkin_checkout');

        successResponse('check-in successfully', [], 200);
    }

    public function checkIn()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        $this->form_validation->set_rules("latitude", "Latitude", "required");
        $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        $check_in_latitude = $this->input->post('latitude');
        $check_in_longitude = $this->input->post('longitude');

        // validate location by lat and lon 
        $this->checkMeters($order_id, $check_in_latitude, $check_in_longitude);

        // check in
        $this->db->set('order_id', $order_id);
        $this->db->set('driver_id', $driver_id);
        $this->db->set('latitude', $check_in_latitude);
        $this->db->set('longitude', $check_in_longitude);
        $this->db->set('checkin_time', date('Y-m-d H:i:s'));
        $this->db->insert('checkin_checkout');

        successResponse('check-in successfully', [], 200);
    }

    public function checkOut()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        $this->form_validation->set_rules("latitude", "Latitude", "required");
        $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        $check_in_latitude = $this->input->post('latitude');
        $check_in_longitude = $this->input->post('longitude');


        // validate location by lat and lon 
        $this->checkMeters($order_id, $check_in_latitude, $check_in_longitude);

        // check if check in before checkout
        $row = $this->db->select('*')->from('checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->get()->row();
        if (!empty($row->checkin_time)) {
            // check out
            $this->db->where('order_id', $order_id)->where('driver_id', $driver_id);
            $this->db->update('checkin_checkout', ['checkout_time' => date('Y-m-d H:i:s')]);

            // update order completed_at time
            $this->db->where('order_id', $order_id);
            $this->db->update('orders', ['completed_at' => date('Y-m-d H:i:s')]);


            successResponse('check-out successfully', [], 200);
        }
        errorResponse('not checked-in before', [], 200);
    }

    public function checkOutFree()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        // $this->form_validation->set_rules("latitude", "Latitude", "required");
        // $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        // $check_in_latitude = $this->input->post('latitude');
        // $check_in_longitude = $this->input->post('longitude');


        // validate location by lat and lon 
        // $this->checkMeters($order_id, $check_in_latitude, $check_in_longitude);

        // check if check in before checkout
        $row = $this->db->select('*')->from('checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->get()->row();
        if (!empty($row->checkin_time)) {
            // check out
            $this->db->where('order_id', $order_id)->where('driver_id', $driver_id);
            $this->db->update('checkin_checkout', ['checkout_time' => date('Y-m-d H:i:s')]);

            // update order completed_at time
            $this->db->where('order_id', $order_id);
            $this->db->update('orders', ['completed_at' => date('Y-m-d H:i:s')]);


            successResponse('check-out successfully', [], 200);
        }
        errorResponse('not checked-in before', [], 200);
    }

    public function isCheckIn()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');

        $checkin_qry = $this->db->from('checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->order_by('checkin_time', 'desc')->where('checkin_time IS NOT NULL')->get();
        $checkout_qry = $this->db->from('checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->order_by('checkout_time', 'desc')->where('checkout_time IS NOT NULL')->get();

        successResponse('check in status', [
            'isCheckIn' => $checkin_qry->num_rows() > 0 ? true : false,
            'isCheckOut' => $checkout_qry->num_rows() > 0 ? true : false
        ], 200);
    }

    public function checkMeters($order_id, $check_in_latitude, $check_in_longitude)
    {
        // get company address latitude, longitude
        $company_addresses = $this->db->select('company_addresses.latitude, company_addresses.longitude')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->where('orders.order_id', $order_id)
            ->get()
            ->row();

        $company_latitude = $company_addresses->latitude ?? 0;
        $company_longitude = $company_addresses->longitude ?? 0;

        // get driver and company address location-difference in meters
        $meters = latLonMetersCalculator($check_in_latitude, $check_in_longitude, $company_latitude, $company_longitude);
        if ($meters > 4000) {

            $this->db->set('order_id', $order_id);
            $this->db->set('checkin_lat', $check_in_latitude);
            $this->db->set('checkin_lon', $check_in_longitude);
            $this->db->set('lat', $company_latitude);
            $this->db->set('lon', $company_longitude);
            $this->db->set('checkin_lat', $check_in_latitude);
            $this->db->set('api_name', 'checkMeters');

            $this->db->insert('coverage_logs');
            errorResponse('Out of coverage', [], 200);
        }
    }

    // field check in, check out 
    public function fieldCheckIn()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        $this->form_validation->set_rules("latitude", "Latitude", "required");
        $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }

        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        $check_in_latitude = $this->input->post('latitude');
        $check_in_longitude = $this->input->post('longitude');

        // validate location by lat and lon 
        $field_id = $this->checkFieldMeters($check_in_latitude, $check_in_longitude);

        // check in
        $this->db->set('order_id', $order_id);
        $this->db->set('driver_id', $driver_id);
        $this->db->set('field_id', $field_id);
        $this->db->set('latitude', $check_in_latitude);
        $this->db->set('longitude', $check_in_longitude);
        $this->db->set('checkin_time', date('Y-m-d H:i:s'));
        $this->db->insert('field_checkin_checkout');

        successResponse('field check-in successfully', [], 200);
    }

    public function fieldCheckOut()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");
        $this->form_validation->set_rules("latitude", "Latitude", "required");
        $this->form_validation->set_rules("longitude", "Longitude", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');
        $check_in_latitude = $this->input->post('latitude');
        $check_in_longitude = $this->input->post('longitude');

        // validate location by lat and lon for specific field
        if (true) {
            // check if check_in before checkout
            $row = $this->db->select('*')->from('field_checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->get()->row();
            if (!empty($row->checkin_time)) {
                // check out
                $this->db->where('order_id', $order_id)->where('driver_id', $driver_id);
                $this->db->update('field_checkin_checkout', ['checkout_time' => date('Y-m-d H:i:s')]);
                successResponse('check-out successfully', [], 200);
            }
            errorResponse('not checked-in before', [], 200);
        }
    }

    public function isFieldCheckIn()
    {
        // request validation 
        $this->form_validation->set_rules("order_id", "Order Id", "required");
        $this->form_validation->set_rules("driver_id", "Driver Id", "required");

        if ($this->form_validation->run() == FALSE) {
            errorResponse('data not validated', $this->form_validation->error_array());
        }
        $order_id = $this->input->post('order_id');
        $driver_id = $this->input->post('driver_id');

        $checkin_qry = $this->db->from('field_checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->order_by('checkin_time', 'desc')->where('checkin_time IS NOT NULL')->get();
        $checkout_qry = $this->db->from('field_checkin_checkout')->where('order_id', $order_id)->where('driver_id', $driver_id)->order_by('checkout_time', 'desc')->where('checkout_time IS NOT NULL')->get();

        successResponse('check in status', [
            'isCheckIn' => $checkin_qry->num_rows() > 0 ? true : false,
            'isCheckOut' => $checkout_qry->num_rows() > 0 ? true : false
        ], 200);
    }

    public function checkFieldMeters($check_in_latitude, $check_in_longitude)
    {
        $field_id = null;
        // get land field address latitude, longitude
        $land_fields = $this->db->select('*')
            ->from('land_field_location')
            ->get()
            ->result();

        $distance_in_range = false;
        foreach ($land_fields as $land) {
            $latitude = $land->latitude ?? 0;
            $longitude = $land->longitude ?? 0;
            // get land field address and driver checked-in addresss, location-difference in meters
            $meters = latLonMetersCalculator($check_in_latitude, $check_in_longitude, $latitude, $longitude);
            if ($meters <= 4000) {
                $distance_in_range = true;
                $field_id = $land->land_field_id;
                break;
            }
        }
        if ($distance_in_range == false) {

            // $this->db->set('order_id', $order_id);
            $this->db->set('checkin_lat', $check_in_latitude);
            $this->db->set('checkin_lon', $check_in_longitude);
            $this->db->set('lat', $latitude);
            $this->db->set('lon', $longitude);
            $this->db->set('checkin_lat', $check_in_latitude);
            $this->db->set('api_name', 'checkFieldMeters');

            $this->db->insert('coverage_logs');

            errorResponse('out of coverage', [], 200);
        }

        return $field_id;
    }

    public function checkFieldMetersByFieldId($order_id, $driver_id, $check_in_latitude, $check_in_longitude)
    {
        // get field by order id and  driver id from "field_checkin_checkout" table
        $field_checkin_checkout = $this->db->select('field_id')
            ->from('field_checkin_checkout')
            ->where('order_id', $order_id)
            ->where('driver_id', $driver_id)
            ->order_by('check_in_id', 'desc')
            ->get()
            ->row();

        if ($field_checkin_checkout) {
            // get land_field_location
            $land_field_location = $this->db->select('*')
                ->from('land_field_location')
                ->where('land_field_id', $field_checkin_checkout->field_id)
                ->get()
                ->row();

            $field_latitude = $land_field_location->latitude ?? 0;
            $field_longitude = $land_field_location->longitude ?? 0;

            // get driver and company address location-difference in meters
            $meters = latLonMetersCalculator($check_in_latitude, $check_in_longitude, $field_latitude, $field_longitude);
            if ($meters > 4000) {

                $this->db->set('order_id', $order_id);
                $this->db->set('checkin_lat', $check_in_latitude);
                $this->db->set('checkin_lon', $check_in_longitude);
                $this->db->set('lat', $field_latitude);
                $this->db->set('lon', $field_longitude);
                $this->db->set('checkin_lat', $check_in_latitude);
                $this->db->set('api_name', 'checkFieldMetersByFieldId');

                $this->db->insert('coverage_logs');

                errorResponse('out of coverage', [], 200);
            }

            return $field_checkin_checkout->field_id;
        }
    }

    public function getOrdersByDriver()
    {
        $driverId = $this->input->post('driverId', true);
        if (isset($driverId) && $driverId != "") {
            $this->db->select('o.start_date');
            $this->db->from('orders o');
            $this->db->join('order_drivers od', 'o.order_id = od.order_id');
            $this->db->where('od.driver_id', $driverId);
            $this->db->order_by('o.start_date', 'ASC');
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $result = $query->result_array();
                $startDates = array();
                foreach ($result as $row) {
                    $startDates[] = $row['start_date'];
                }
                successResponse('Orders by driver', $startDates, 200);
            } else {
                errorResponse('No orders found for driver', [], 404);
            }
        } else {
            errorResponse('data not validated', array("driverId" => "The Driver Id field is required."));
        }
    }
}