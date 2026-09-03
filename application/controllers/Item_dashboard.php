<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_equipment_groups')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => 'Item Dashboard', 'title2' => 'Item Dashboard', 'styles' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
            'design/css/asset-dashboard.css?v=2',
        ]]);

        // $total_items = $faulty_item = $items_in_maintenance = $total_locations = $item_in_use = null;
        

            // $this->db->limit( PHP_INT_MAX, 1 );
            // $total_assets = $this->db->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id')->count_all_results('equipments_asset');
            $total_items = $this->db->count_all('add_asset_items');


            // $warehouse_assets = $this->db->where( 'equipment_status', 'Available' )->join( 'asset_types', 'equipments_asset.equipment_type = asset_types.asset_id' )->count_all_results( 'equipments_asset' );           
            // $total_locations = $this->db->count_all_results('locations');

            

            $total_locations = $this->db
    ->select('COUNT(DISTINCT states.id) as total')
    ->from('states')
    ->join('equipments_asset', 'states.id = equipments_asset.state_id', 'inner')
    ->join('add_asset_items', 'equipments_asset.equipment_id = add_asset_items.asset_id', 'inner')
    ->get()
    ->row()
    ->total;

            $unserviceable_item = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'UNSERVICEABLE')
            ->count_all_results();


            $items_in_maintenance = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'MAINTENANCE')
            ->count_all_results();

            $item_serviceable = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'SERVICEABLE')
            ->count_all_results();
        
            $item_available = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'AVAILABLE')
            ->count_all_results();

            $item_store = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'STORE')
            ->count_all_results();


        // $assets_deployed_query = $this->db->select( 'DISTINCT(order_equipment_bin_qr_codes.reg_no)' )
        //     ->from( 'orders' )
        //     ->join( 'order_equipment_bin_qr_codes', 'order_equipment_bin_qr_codes.order_id = orders.order_id' )
        //     ->join( 'asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id' )
        //     ->join( 'equipments_asset', 'equipments_asset.equipment_registration = order_equipment_bin_qr_codes.reg_no' )
        //     ->where( 'order_equipment_bin_qr_codes.scanned', 1 )
        //     ->where( 'order_equipment_bin_qr_codes.reg_no IS NOT NULL' );

        // if ( !isSuperAdmin() ) {
        //     $assets_deployed_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
        // }

        // $assets_deployed = $assets_deployed_query->where( 'orders.second_order_type IS NULL' )
        //     ->get()
        //     ->num_rows();

        // assets that are expected to be pulled back in 7 days for roro bin


        // }


        $maintenance_color = $in_use_color = $faulty_type_color = $total_color = $location_color = "";

        $dashboardStatusColors = $this->db->select('dashboard_status_colors.*')
            ->from('dashboard_status_colors')
            ->get()
            ->result();

        foreach ($dashboardStatusColors as $statusColor) {

            if ($statusColor->status_name == "MAINTENANCE") {
                $maintenance_color = $statusColor->status_color;
            } elseif ($statusColor->status_name == "IN USE") {
                $in_use_color = $statusColor->status_color;
            } elseif ($statusColor->status_name == "FAULTY") {
                $faulty_type_color = $statusColor->status_color;
            }
        }


        $this->load->view('item-dashboard', [
            'total_items' => $total_items,
            'unserviceable_item' => $unserviceable_item,
            'item_serviceable' => $item_serviceable,
            'items_in_maintenance' => $items_in_maintenance,
            'total_locations' => $total_locations,
            'item_available' => $item_available,
            'item_store' => $item_store,
            'maintenance_color' => $maintenance_color,
            'in_use_color' => $in_use_color,
            'faulty_type_color' => $faulty_type_color,

        ]);

        $this->load->view('footer', ['scripts' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
            // 'design/js/init-map.js',
            'design/js/items_map.js',
            'design/js/asset-dashboard.js'
        ]]);
    }

    public function assetLocation()
    {

        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('invaid request');
        }
        // $query = "WITH RankedData AS (
        //     SELECT qc.*, ROW_NUMBER() OVER (PARTITION BY qc.reg_no ORDER BY qc.created_at DESC) AS rn
        //     FROM order_equipment_bin_qr_codes qc
        //     WHERE qc.scanned = 1 AND qc.reg_no IS NOT NULL
        // )
        // SELECT company_addresses.*, countries.code as country_code
        // FROM orders
        // LEFT JOIN companies ON orders.company_id = companies.company_id
        // LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
        // LEFT JOIN countries on countries.code = company_addresses.address_country
        // LEFT JOIN RankedData ON RankedData.order_id = orders.order_id
        // WHERE orders.second_order_type IS NULL AND RankedData.rn = 1";

        $query = "SELECT company_addresses.*, countries.code as country_code
        FROM orders
        LEFT JOIN companies ON orders.company_id = companies.company_id
        LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
        LEFT JOIN countries on countries.code = company_addresses.address_country
        LEFT JOIN (
            SELECT qc.*, 
                @rn := IF(@prev_reg_no = qc.reg_no, @rn + 1, 1) AS rn,
                @prev_reg_no := qc.reg_no
            FROM (SELECT * FROM order_equipment_bin_qr_codes WHERE scanned = 1 AND reg_no IS NOT NULL ORDER BY reg_no, created_at DESC) qc
            CROSS JOIN (SELECT @rn := 0, @prev_reg_no := NULL) vars
        ) RankedData ON RankedData.order_id = orders.order_id
        WHERE orders.second_order_type IS NULL AND RankedData.rn = 1";



        if (!isSuperAdmin()) {
            $query = "WITH RankedData AS (
                SELECT qc.*, ROW_NUMBER() OVER (PARTITION BY qc.reg_no ORDER BY qc.created_at DESC) AS rn
                FROM order_equipment_bin_qr_codes qc
                WHERE qc.scanned = 1 AND qc.reg_no IS NOT NULL
            )
            SELECT company_addresses.*, countries.code as country_code
            FROM orders
            LEFT JOIN companies ON orders.company_id = companies.company_id
            LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
            LEFT JOIN countries on countries.code = company_addresses.address_country
            LEFT JOIN RankedData ON RankedData.order_id = orders.order_id
            WHERE orders.second_order_type IS NULL AND RankedData.rn = 1
            AND company_addresses.branch_office_id in (" . implode(',', getUserActiveBranchsId()) . ')';
        }

        $plannedOrdersData = $this->db->query($query)->result();
        // var_dump("work here");
        // exit();

        $addresses = [];
        foreach ($plannedOrdersData as $order) {
            array_push($addresses, [
                'country_code' => $order->country_code,
                'state' => $order->address_state,
                'city' => $order->address_city,
                'address' => $order->address_line_1,
                'latitude' => $order->latitude,
                'longitude' => $order->longitude,


            ]);
        }
        echo "<pre>";
        var_dump($addresses);


        print_r(json_encode([
            'addresses' => $addresses,
        ]));
        die;
    }



    public function itemLocationPointer()
    {
        // Set header to JSON
        header('Content-Type: application/json');

        $query = $this->db->select(
            '
            l.state_name, 
            l.lat, 
            l.long, 
            asi.item_name,
            l.name as location_name,
            it.name as item_type,
            ea.equipment_status, 
            f.fault_type as faulty_status,
            ds.status_color' // Add status_color from dashboard_status_colors
        )
            ->from('add_asset_items AS asi')
            ->join('equipments_asset AS ea', 'ea.equipment_id = asi.asset_id')
            ->join('locations AS l', 'ea.location_id = l.id')
            ->join('item_types AS it', 'asi.item_type_id = it.id', 'left')
            ->join('fault_type_color_code AS f', 'ea.faulty_type_id = f.id', 'left') // Left join to handle cases where faulty_type_id might be null
            ->join('dashboard_status_colors AS ds', 'ea.equipment_status = ds.status_name', 'left') // Join to get status_color
            ->get();

        // Get the result as an associative array
        $data = $query->result();

        $states = [];
        foreach ($data as $order) {
            array_push($states, [
                'state_name' => $order->state_name,
                'longitude' => $order->long,
                'latitude' => $order->lat,
                'status' => $order->equipment_status,
                'faulty_status' => $order->faulty_status,
                'location_name' => $order->location_name,
                'item_type' => $order->item_type,
                'item_name' => $order->item_name,
                'status_color' => $order->status_color // Include status_color in response
            ]);
        }

        // Encode data to JSON and output
        echo json_encode(['states' => $states]);
    }
}


