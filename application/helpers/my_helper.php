<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function id_decode2($id = 0)
{
    $data = ($id ? $id : $this->input->get('id'));
    if (base64_encode(base64_decode($data, true)) === $data) {
        return intval(str_replace("STeVe-", "", base64_decode($data)));
    } else {
        return intval($data);
    }
}

function id_encode2($id)
{
    return base64_encode("STeVe-" . $id);
}

function sortColumn($column, $tab, $url = 'orders')
{
    $tab = empty($tab) ? 'all' :  $tab;
    return site_url("{$url}?order={$tab}&sort=" . (isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'desc' : 'asc') . "&column=" . $column);
}

function sortColumnn($columnn, $tab, $url = 'orders')
{
    $tab = empty($tab) ? 'all' :  $tab;
    if(isset($_GET['sort']) && $_GET['sort'] == 'asc'){
        return site_url("{$url}?order={$tab}&sort=" . (isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'asc' : '') . "&columnn=" . $columnn);

    }else if(isset($_GET['sort']) && $_GET['sort'] == 'desc'){

        return site_url("{$url}?order={$tab}&sort=" . (isset($_GET['sort']) && $_GET['sort'] == 'desc' ? 'desc' : '') . "&columnn=" . $columnn);
    }
}
function sortColumnNew($column, $url = 'driver_performance')
{
    // $tab = empty($tab) ? 'all' :  $tab;
    return site_url("{$url}?&sort=" . (isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'desc' : 'asc') . "&column=" . $column);
}
function sortColumnCustomerCenter($column, $tab)
{
    $tab = empty($tab) ? 'all' :  $tab;
    return site_url("customer_center?order={$tab}&sort=" . (isset($_GET['sort']) && $_GET['sort'] == 'asc' ? 'desc' : 'asc') . "&column=" . $column);
}

function sortColumnIcon($column)
{
    if ($_GET['column'] == $column) {
        if (isset($_GET['sort']) && $_GET['sort'] == 'asc') {
            return '<i class="fa fa-arrow-up"></i>';
        } else if (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
            return '<i class="fa fa-arrow-down"></i>';
        }
    }
}

function sortColumnIconn($column)
{
    if ($_GET['column'] == $column) {
        if (isset($_GET['sort']) && $_GET['sort'] == 'asc') {
            return '<i class="fa fa-arrow-up"></i>';
        } else if (isset($_GET['sort']) && $_GET['sort'] == 'desc') {
            return '<i class="fa fa-arrow-down"></i>';
        }
    }
}

function statusToText($status)
{
    $txt = '';
    if ($status == 0) {
        $txt = 'New';
    }
    if ($status == 1) {
        $txt = 'Planned';
    }
    if ($status == 2) {
        $txt = 'Progress';
    }
    if ($status == 3) {
        $txt = 'Completed';
    }
    return $txt;
}

function driverCardLink($worker_id)
{
    $query = $_GET;
    $query['driver'] = $worker_id;
    return site_url('daily_summary/index?' . (urldecode(http_build_query($query))));
}

function csvDateFormat($csv_date)
{
    $date = str_replace('/', '-', $csv_date);
    return date('Y-m-d', strtotime($date));
}

function latLonMetersCalculator($lat1, $lon1, $lat2, $lon2)
{
    if (($lat1 == $lat2) && ($lon1 == $lon2)) {
        return 0;
    } else {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $meters = $miles * 1609.34;
        return round($meters, 2);
    }
}


/*************************************
            API METHODS
 ************************************/

function jWTSecretKey()
{
    return '745RamseCreT124@***usr';
}

function successResponse($message, $data, $code = 200)
{
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'status' => true,
        'message' => $message,
        'data' => $data,
    ]);
    die;
}

function errorResponse($message, $errors, $code = 400)
{
    header('Content-Type: application/json');
    http_response_code($code);
    echo json_encode([
        'status' => false,
        'message' => $message,
        'errors' => $errors,
    ]);
    die;
}

function verifyJWT()
{
    $headers = apache_request_headers();
    if (!isset($headers['Authorization'])) {
        return false;
    }
    $token = str_replace('Bearer ', '', $headers['Authorization']);
    try {
        return JWT::decode($token, new Key(jWTSecretKey(), 'HS256'));
    } catch (Exception $e) {
        return false;
    }
}

function randomImageName($extension)
{
    return time() . rand(1111111, 9999999) . '.' . $extension;
}

function isSuperAdmin()
{
    $user = $_SESSION['user'];
    return $user->isSuper ? true : false;
}

function getUserActiveBranchsId()
{
    // Check if the session variable is set
    if(isset($_SESSION['user_active_branches'])) {
        return $_SESSION['user_active_branches'];
       
    } else {
        logout();
    }
}


 function logout()
    {
        delete_cookie("redirect");
        delete_cookie("Steve_user");
        // $this->logs->add("users", $_SESSION['user']->user_id, "LOGOUT", "Logged out manually.", $user[0]->user_id);
        session_destroy();
        redirect('login', 'refresh');
    }