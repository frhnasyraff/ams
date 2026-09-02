<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_detail extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_equipment_groups")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        if ($this->input->get('type')) {
            $type = $this->input->get('type');
            $this->load->view('header', ['title' => $type, 'title2' => $type, "styles" => [
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
                "design/css/asset-dashboard.css",
            ]]);

            $assets = array();

            if ($type == 'deployed') {
                $query1 = "WITH RankedData AS (
                                SELECT qc.*, ROW_NUMBER() OVER (PARTITION BY qc.reg_no ORDER BY qc.created_at DESC) AS rn
                                FROM order_equipment_bin_qr_codes qc
                                WHERE qc.scanned = 1 AND qc.reg_no IS NOT NULL
                            )
                            SELECT orders.order_id, asset_types.name, equipments_asset.equipment_name, 
                                   equipments_asset.equipment_registration, company_addresses.address_line_1, companies.company_name
                            FROM orders
                            LEFT JOIN companies ON orders.company_id = companies.company_id
                            LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
                            LEFT JOIN RankedData ON RankedData.order_id = orders.order_id
                            LEFT JOIN asset_types ON asset_types.asset_id = RankedData.asset_type_id
                            LEFT JOIN equipments_asset ON equipments_asset.equipment_registration = RankedData.reg_no
                            WHERE orders.second_order_type IS NULL AND RankedData.rn = 1 and equipments_asset.equipment_status = 'In use'";

                $assets = $this->db->query($query1)->result();
                $assets_deployed = $this->db->where('equipment_status', 'In use')->count_all_results('equipments_asset');
            } elseif ($type == 'warehouse') {
                $assets = $this->db->select('asset_types.name, equipments_asset.equipment_name, equipments_asset.equipment_registration, equipments_asset.equipment_status')
                    ->from('asset_types')
                    ->join('equipments_asset', 'equipments_asset.equipment_type = asset_types.asset_id')
                    ->where('equipments_asset.equipment_status', 'Available')
                    ->get()
                    ->result();
            } else if ($type == 'maintenance') {
                $assets = $this->db->select('asset_types.name, equipments_asset.equipment_name, equipments_asset.equipment_registration, branch_office.branch_name, branch_office.branch_code')
                    ->from('asset_types')
                    ->join('equipments_asset', 'equipments_asset.equipment_type = asset_types.asset_id')
                    ->join('branch_office', 'branch_office.branch_id = equipments_asset.branch_office_id')
                    ->where('equipments_asset.equipment_status', 'Maintenance')
                    ->get()
                    ->result();
            }



            $this->load->view('asset-detail', [
                'assets' => $assets,
                'type' => $type,
                'assets_deployed' => $assets_deployed
            ]);

            $this->load->view('footer', ['scripts' => [
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
                'design/js/helper.js',
                'design/js/init-map.js',
                'design/js/asset-detail.js'
            ]]);
        }
    }


    public function assetDeployedLocation()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('invaid request');
        }

        $query1 = "WITH RankedData AS (
            SELECT qc.*, ROW_NUMBER() OVER (PARTITION BY qc.reg_no ORDER BY qc.created_at DESC) AS rn
            FROM order_equipment_bin_qr_codes qc
            WHERE qc.scanned = 1 AND qc.reg_no IS NOT NULL
        )
        SELECT company_addresses.*, countries.code as country_code,equipments_asset.*, asset_types.name
        FROM orders
        LEFT JOIN companies ON orders.company_id = companies.company_id
        LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
        LEFT JOIN countries ON countries.code=company_addresses.address_country
        LEFT JOIN RankedData ON RankedData.order_id = orders.order_id
        LEFT JOIN asset_types ON asset_types.asset_id = RankedData.asset_type_id
        LEFT JOIN equipments_asset ON equipments_asset.equipment_registration = RankedData.reg_no
        WHERE orders.second_order_type IS NULL AND RankedData.rn = 1 and equipments_asset.equipment_status = 'In use'";
        //     $query1 ="SELECT 
        //     company_addresses.*, 
        //     countries.code AS country_code,
        //     equipments_asset.*, 
        //     asset_types.name
        // FROM 
        //     orders
        //     LEFT JOIN companies ON orders.company_id = companies.company_id
        //     LEFT JOIN company_addresses ON orders.company_address_id = company_addresses.company_address_id
        //     LEFT JOIN countries ON countries.code = company_addresses.address_country
        //     LEFT JOIN (
        //         SELECT 
        //             qc.*, 
        //             @rn := IF(@prev_reg_no = qc.reg_no, @rn + 1, 1) AS rn,
        //             @prev_reg_no := qc.reg_no
        //         FROM 
        //             (SELECT * FROM order_equipment_bin_qr_codes WHERE scanned = 1 AND reg_no IS NOT NULL ORDER BY reg_no, created_at DESC) qc
        //             CROSS JOIN (SELECT @rn := 0, @prev_reg_no := NULL) vars
        //     ) AS RankedData ON RankedData.order_id = orders.order_id
        //     LEFT JOIN asset_types ON asset_types.asset_id = RankedData.asset_type_id
        //     LEFT JOIN equipments_asset ON equipments_asset.equipment_registration = RankedData.reg_no
        // WHERE 
        //     orders.second_order_type IS NULL AND 
        //     RankedData.rn = 1 AND 
        //     equipments_asset.equipment_status = 'In use'
        // ";
        $orders = $this->db->query($query1)->result();

        $addresses = [];
        foreach ($orders as $order) {
            array_push($addresses, [
                'country_code' => $order->address_country,
                'state' => $order->address_state,
                'city' => $order->address_city,
                'address' => $order->address_line_1 . ', ' . $order->address_line_2,
                'asset_name' => $order->equipment_name,
                'asset_number' => $order->equipment_registration,
                'asset_type' => $order->name
            ]);
        }
        print_r(json_encode([
            'addresses' => $addresses,
        ]));
        die;
    }
    public function assetWarehouseLocation()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('invaid request');
        }

        $orders = $this->db->select("branch_office.*, 'MY' as country_code,equipments_asset.*, asset_types.name")
            ->from('equipments_asset')
            ->join('branch_office', 'branch_office.branch_id=equipments_asset.branch_office_id', 'LEFT')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'LEFT')
            ->where('equipments_asset.equipment_status', 'Available')
            ->get()
            ->result();

        $addresses = [];
        foreach ($orders as $order) {
            array_push($addresses, [
                'country_code' => $order->country_code,
                'state' => $order->address_state,
                'city' => $order->address_city,
                'address' => $order->branch_address,
                'asset_name' => $order->equipment_name,
                'asset_number' => $order->equipment_registration,
                'asset_type' => $order->name
            ]);
        }
        print_r(json_encode([
            'addresses' => $addresses,
        ]));
        die;
    }
}
