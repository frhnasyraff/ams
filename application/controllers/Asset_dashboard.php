<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_dashboard extends CI_Controller
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

        $this->load->view('header', ['title' => 'GENERAL', 'title2' => 'GENERAL', 'styles' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
            'design/css/asset-dashboard.css?v=2',
        ]]);

        // $total_assets = $faulty_assets = $assets_in_maintenance = $total_locations = null;


        $total_assets = $this->db->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id')->count_all_results('equipments_asset');
        // $total_locations = $this->db->count_all_results('locations');

        $this->db->select('states.id AS state_id, MIN(locations.colour) AS colour, states.state_name, COUNT(DISTINCT equipments_asset.equipment_id) AS in_use_count');
        $this->db->from('equipments_asset');
        $this->db->join('locations', 'equipments_asset.location_id = locations.id');
        // Legacy: $this->db->join('states', 'locations.state_name = states.state_name');
        $this->db->join('states', 'locations.state_id = states.id', 'left');
        $this->db->group_by('states.id, states.state_name');
        $query = $this->db->get();

        $location_data = $query->result();
        $total_locations = $this->db
        ->select('COUNT(DISTINCT states.id) as total')
        ->from('states')
        ->join('equipments_asset', 'states.id = equipments_asset.state_id', 'inner')
        ->get()
        ->row()
        ->total;

        $assets_unseviceable = $this->db->where('equipment_status', 'UNSERVICEABLE')->count_all_results('equipments_asset');

        // $faulty_assets = $this->db->select(
        //     'equipments_asset.*, 
        //         locations.name as location, 
        //         states.state_name as state, 
        //         asset_types.name as type_name,
        //         fault_type_color_code.fault_type as fault_type'
        // )
        //     ->from('equipments_asset')
        //     ->join('locations', 'locations.id = equipments_asset.location_id')
        //     ->join('states', 'states.id = equipments_asset.state_id')
        //     ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
        //     ->join('fault_type_color_code', 'fault_type_color_code.id = equipments_asset.faulty_type_id')
        //     ->where('equipments_asset.faulty_type_id IS NOT NULL')
        //     ->get()
        //     ->result();



        // $assets_in_maintenance = $this->db->select('
        //     equipment_maintenance_asset.*, 
        //     maintenance_type_color_code.maintenance_type AS type_name, 
        //     equipments_asset.equipment_name
        // ')
        //     ->from('equipments_asset') // Start from equipments_asset
        //     ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left') // Left join so missing relations are included
        //     ->join('maintenance_type_color_code', 'maintenance_type_color_code.id = equipment_maintenance_asset.maintenance_type_id', 'left')
        //     ->where('equipments_asset.equipment_status', 'Maintenance')
        //     ->where('equipments_asset.faulty_type_id IS NULL', null, false) // Corrected NULL check
        //     ->get()
        //     ->result();

    
        $assets_in_maintenance = $this->db->where('equipment_status', 'MAINTENANCE')->count_all_results('equipments_asset');
        $assets_in_available = $this->db->where('equipment_status', 'AVAILABLE')->count_all_results('equipments_asset');
        $assets_in_store = $this->db->where('equipment_status', 'STORE')->count_all_results('equipments_asset');


        $assets_seviceable = $this->db->where('equipment_status', 'SERVICEABLE')->count_all_results('equipments_asset');


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
            } elseif ($statusColor->status_name == "locations") {
                $location_color = $statusColor->status_color;
            } elseif ($statusColor->status_name == "total") {
                $total_color = $statusColor->status_color;
            }
        }



        $this->load->view('asset-dashboard', [
            'total_assets' => $total_assets,
            'assets_unseviceable' => $assets_unseviceable,
            'assets_seviceable' => $assets_seviceable,
            'assets_in_maintenance' => $assets_in_maintenance,
            'total_locations' => $total_locations,
            'assets_in_available' => $assets_in_available,
            'assets_in_store' => $assets_in_store,
            'maintenance_color' => $maintenance_color,
            'in_use_color' => $in_use_color,
            'faulty_type_color' => $faulty_type_color,
            'location_color' => $location_color,
            'total_color' => $total_color,

        ]);

        $this->load->view('footer', ['scripts' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
            // 'design/js/init-map.js',
            // 'design/js/dashboard_map.js',
            'design/js/asset-dashboard.js',
             'design/js/equipment_asset_map.js'
        ]]);

        // Helper function to get color by status name

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
        echo json_encode([
            'addresses' => $addresses,
        ]);
        die;
    }

    public function assetLocationPointer()
    {
        // Set header to JSON
        header('Content-Type: application/json');

        

        $query = $this->db->select(
            "
            l.state_name, 
            l.lat, 
            l.long, 
            l.name as location_name,
            at.name as asset_type,
            ea.equipment_name as asset_name, 
            f.fault_type as faulty_status,
            f.color as faulty_color,
            dsc.status_color,
            CASE 
                WHEN ea.faulty_type_id IS NOT NULL THEN 'faulty'
                ELSE ea.equipment_status 
            END AS equipment_status" // Override equipment status if faulty_type_id is set
        )
            ->from('equipments_asset AS ea')
            ->join('locations AS l', 'ea.location_id = l.id')
            ->join('fault_type_color_code AS f', 'ea.faulty_type_id = f.id', 'left')
            ->join('asset_types AS at', 'ea.equipment_type = at.asset_id', 'left')
            ->join('dashboard_status_colors AS dsc', 'ea.equipment_status = dsc.status_name', 'left') // Join to get status color
            ->where('ea.equipment_type !=', null)
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
                'asset_type' => $order->asset_type,
                'asset_name' => $order->asset_name,
                'status_color' => $order->status_color,
                // Add status color to the response
            ]);
        }

        // Encode data to JSON and output
        echo json_encode(['states' => $states]);
    }
}



