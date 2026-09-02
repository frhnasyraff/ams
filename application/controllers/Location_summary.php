<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Location_summary extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->helper('url');
        $this->load->library('pagination');

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {

            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $states = $this->db->select('*')->from('states')->get()->result();

        $itemsStateCount = $this->db->select('states.id, states.state_name, COUNT(add_asset_items.id) as total_items')
            ->from('states')
            ->join('equipments_asset', 'states.id = equipments_asset.state_id')
            ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
            ->group_by('states.id, states.state_name') // Grouping by state id and name
            ->get()
            ->result();

        $totalItemsRecords = count($itemsStateCount);
        $totalItemsCount = array_sum(array_column($itemsStateCount, 'total_items'));

        $stateCount = $this->db->select('states.id, states.state_name, COUNT(equipments_asset.equipment_id) as total_assets')
            ->from('states')
            ->join('equipments_asset', 'states.id = equipments_asset.state_id', 'left')
            ->group_by('states.id, states.state_name') // Grouping by state id and name
            ->get()
            ->result();

        $totalCount = $this->db->where('equipments_asset.state_id !=', null)->where('equipments_asset.location_id !=', null)->count_all_results('equipments_asset');

        // Location Summary redesign metrics. Legacy totalCount above is kept for compatibility.
        $totalAssetsAll = $this->db->count_all_results('equipments_asset');
        $unassignedAssets = $this->db
            ->group_start()
                ->where('state_id IS NULL', null, false)
                ->or_where('location_id IS NULL', null, false)
            ->group_end()
            ->count_all_results('equipments_asset');
        $activeLocations = $this->db
            ->select('COUNT(DISTINCT location_id) AS total', false)
            ->where('location_id IS NOT NULL', null, false)
            ->get('equipments_asset')
            ->row()
            ->total;
        $statesWithAssets = 0;
        foreach ($stateCount as $stateRow) {
            if ((int) $stateRow->total_assets > 0) {
                $statesWithAssets++;
            }
        }

        $this->load->view('header', ['title' => 'Location Summary', 'title2' => 'Location Summary', 'styles' => [
            'design/css/order-summary-cards.css',
            'design/css/location_summary.css',
        ]]);


        $this->load->view('location_summary', [
            'stateCount' => $stateCount,
            'itemsStateCount' => $itemsStateCount,
            'states' => $states,
            'totalItemsRecords' => $totalItemsRecords,

            'totalCount' => $totalCount,
            'totalItemsCount' => $totalItemsCount,
            'totalAssetsAll' => $totalAssetsAll,
            'unassignedAssets' => $unassignedAssets,
            'activeLocations' => $activeLocations,
            'statesWithAssets' => $statesWithAssets

            // 'equipment_types_data' => $equipment_types_data

        ]);
        $this->load->view('footer', ['scripts' => [

            // Load the local Chart.js runtime before plugins and location_summary.js.
            'design/vendor/chart.js/Chart.min.js',

            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.4.0/dist/chartjs-plugin-datalabels.min.js',

            'design/js/location_summary.js?v=5'
        ]]);
    }

    //ajax

    // public function getCustomerComplaintByMonth()
    // {

    //     $customerMonth = $this->input->get('customerSelectedMonth');
    //     $date = date_parse($customerMonth);

    //     if ($customerMonth != null) {

    //         $customerComplaintsByLocation = $this->db->select('COUNT(branch_id) as customer_complaints, branch_office.branch_name, branch_office.branch_code')
    //             ->from('branch_office')
    //             ->join('company_addresses', 'company_addresses.branch_office_id = branch_office.branch_id', 'LEFT')
    //             ->join('companies', 'companies.company_id = company_addresses.company_id', 'LEFT')
    //             ->join('orders', 'orders.company_id = companies.company_id', 'LEFT')
    //             ->like('orders.remarks_updated_at', date('Y-0' . $date['month']))
    //             ->where('branch_office.active', '1')
    //             ->group_by('branch_office.branch_id')
    //             ->get()
    //             ->result_object();
    //     }
    //     echo json_encode([
    //         'customerComplaintsByLocation' => $customerComplaintsByLocation ? $customerComplaintsByLocation : [],
    //     ]);
    //     die();
    // }
    // ajax

    // public function getAssetsSummary()
    // {

    //     if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    //         die('invaid request');
    //     }

    //     // $in_use_query =  $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $in_use_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $in_use = $in_use_query->where( 'equipment_status', 'In use' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $available_query =  $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $available_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $available = $available_query->where( 'equipment_status', 'Available' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $maintenance_query = $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $maintenance_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $maintenance = $maintenance_query->where( 'equipment_status', 'Maintenance' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $decommission_query = $this->db->select( 'equipment_id' )->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $decommission_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $decommission = $decommission_query->where( 'equipment_status', 'Decommission' )
    //     //     ->get()
    //     //     ->num_rows();

    //     $query =  $this->db->select('equipments_asset.equipment_type, asset_type_color.color, asset_types.name, COUNT(*) as in_use_count')
    //         ->from('equipments_asset')
    //         ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id')
    //         ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id')
    //         ->where('equipments_asset.equipment_status', 'In use')
    //         ->group_by('equipments_asset.equipment_type, 
    //     asset_type_color.color, 
    //     asset_types.name')
    //         ->get();
    //     $equipment_types_data = $query->result();

    //     $total_query = $this->db->select('COUNT(*) as total_count')
    //         ->from('equipments_asset')
    //         ->where('equipments_asset.equipment_status', 'In use')
    //         ->get();
    //     $total_count = $total_query->row()->total_count;

    //     $data = [
    //         'equipment_types' => $equipment_types_data,
    //         'total' => $total_count
    //         // 'total' => $in_use + $available + $maintenance + $decommission
    //     ];
    //     print_r(json_encode($data));
    //     die;
    // }

    // public function getAssetsMaintenance()
    // {

    //     if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    //         die('invaid request');
    //     }

    //     // $in_use_query =  $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $in_use_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $in_use = $in_use_query->where( 'equipment_status', 'In use' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $available_query =  $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $available_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $available = $available_query->where( 'equipment_status', 'Available' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $maintenance_query = $this->db->select( 'equipment_id' )
    //     //     ->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $maintenance_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $maintenance = $maintenance_query->where( 'equipment_status', 'Maintenance' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $decommission_query = $this->db->select( 'equipment_id' )->from( 'equipments_asset' );
    //     // if ( !isSuperAdmin() ) {
    //     //     $decommission_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     // }
    //     // $decommission = $decommission_query->where( 'equipment_status', 'Decommission' )
    //     //     ->get()
    //     //     ->num_rows();

    //     // $totalAssets = $this->db->count_all( 'equipments_asset' );
    //     // $totalAssetsInUse = $this->db->where( 'equipment_status=', 'In use' )->count_all_results( 'equipments_asset' );
    //     // $totalAssetsInMaintenance = $this->db->where( 'equipment_status=', 'Maintenance' )->count_all_results( 'equipments_asset' );

    //     // $query =  $this->db->select( 'equipment_maintenance_asset.maintenance_type_id, maintenance_type_color_code.color, maintenance_type_color_code.maintenance_type, COUNT(*) as in_use_count' )
    //     // ->from( 'equipment_maintenance_asset' )
    //     // ->join( 'maintenance_type_color_code', 'equipment_maintenance_asset.maintenance_type_id = maintenance_type_color_code.id' )
    //     // // ->where( 'equipments_asset.equipment_status', 'Maintenance' )
    //     // ->group_by( 'equipments_asset.maintenance_type_id' )
    //     // ->get();

    //     // $this->db->select( 'equipment_maintenance_asset.maintenance_type_id, maintenance_type_color_code.color, maintenance_type_color_code.maintenance_type, COUNT(*) as in_use_count' )
    //     // ->from( 'equipment_maintenance_asset' )
    //     // ->join( 'maintenance_type_color_code', 'equipment_maintenance_asset.maintenance_type_id = maintenance_type_color_code.id' )
    //     // ->group_by( 'equipment_maintenance_asset.maintenance_type_id' ) // Fixed alias for group_by
    //     // ->get();

    //     //    $query = $this->db->select( 'equipment_maintenance_asset.maintenance_type_id, 
    //     //            maintenance_type_color_code.color, 
    //     //            maintenance_type_color_code.maintenance_type, 
    //     //            COUNT(equipment_maintenance_asset.maintenance_type_id) as in_use_count' )
    //     //  ->from( 'equipment_maintenance_asset' )
    //     //  ->join( 'maintenance_type_color_code', 'equipment_maintenance_asset.maintenance_type_id = maintenance_type_color_code.id' )
    //     //  ->group_by( 'equipment_maintenance_asset.maintenance_type_id' ) // Group by all non-aggregated fields
    //     //  ->get();

    //     $query = $this->db->select('equipment_maintenance_asset.maintenance_type_id, 
    //                maintenance_type_color_code.color, 
    //                maintenance_type_color_code.maintenance_type, 
    //                COUNT(equipment_maintenance_asset.maintenance_type_id) as in_use_count')
    //         ->from('equipment_maintenance_asset')
    //         ->join('maintenance_type_color_code', 'equipment_maintenance_asset.maintenance_type_id = maintenance_type_color_code.id')
    //         ->group_by([
    //             'equipment_maintenance_asset.maintenance_type_id',
    //             'maintenance_type_color_code.color',
    //             'maintenance_type_color_code.maintenance_type'
    //         ])
    //         ->get();

    //     $equipment_types_data = $query->result();

    //     $total_query = $this->db->select('COUNT(*) as total_count')
    //         ->from('equipment_maintenance_asset')
    //         ->get();
    //     $total_count = $total_query->row()->total_count;

    //     $data = [
    //         'equipment_types' => $equipment_types_data,
    //         'total' => $total_count
    //         // 'total' => $in_use + $available + $maintenance + $decommission
    //     ];
    //     print_r(json_encode($data));
    //     die;
    // }

    // public function getAssetsQuantity() {

    //     if ( empty( $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] ) || $_SERVER[ 'HTTP_X_REQUESTED_WITH' ] != 'XMLHttpRequest' ) {
    //         die( 'invaid request' );
    //     }

    //     $in_use_query =  $this->db->select( 'equipment_id' )
    //         ->from( 'equipments_asset' );
    //     if ( !isSuperAdmin() ) {
    //         $in_use_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     }
    //     $in_use = $in_use_query->where( 'equipment_status', 'In use' )
    //         ->get()
    //         ->num_rows();

    //     $available_query =  $this->db->select( 'equipment_id' )
    //         ->from( 'equipments_asset' );
    //     if ( !isSuperAdmin() ) {
    //         $available_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     }
    //     $available = $available_query->where( 'equipment_status', 'Available' )
    //         ->get()
    //         ->num_rows();

    //     $maintenance_query = $this->db->select( 'equipment_id' )
    //         ->from( 'equipments_asset' );
    //     if ( !isSuperAdmin() ) {
    //         $maintenance_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     }
    //     $maintenance = $maintenance_query->where( 'equipment_status', 'Maintenance' )
    //         ->get()
    //         ->num_rows();

    //     $decommission_query = $this->db->select( 'equipment_id' )->from( 'equipments_asset' );
    //     if ( !isSuperAdmin() ) {
    //         $decommission_query->where_in( 'equipments_asset.branch_office_id', getUserActiveBranchsId() );
    //     }
    //     $decommission = $decommission_query->where( 'equipment_status', 'Decommission' )
    //         ->get()
    //         ->num_rows();
    //         $totalAssets = $this->db->count_all( 'equipments_asset' );
    //     $data = [
    //         'in_use' => $in_use,
    //         'available' => $available,
    //         'maintenance' => $maintenance,
    //         'decommission' => $decommission,
    //         'total' => $totalAssets
    //         // 'total' => $in_use + $available + $maintenance + $decommission
    // ];
    //     print_r( json_encode( $data ) );
    //     die;
    // }

    // public function getAssetsQuantity()
    // {

    //     if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    //         die('Invalid request');
    //     }

    //     // Build a base query for the join
    //     //   $query =  $this->db->select( 'equipments_asset.equipment_type, asset_type_color.color, asset_types.name, COUNT(equipments_asset.equipment_id) as in_use_count' )
    //     //     ->from( 'equipments_asset' )
    //     //     ->join( 'asset_types', 'equipments_asset.equipment_type = asset_types.asset_id' )
    //     //     ->join( 'asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id' )
    //     //     ->where_in( 'equipments_asset.equipment_status', [ 'In use', 'Available', 'Maintenance', 'Standby', 'Repair', 'Sold', 'Dispose', 'Scrap' ] )
    //     //     ->group_by( 'equipments_asset.equipment_type' )
    //     //     ->get();

    //     $query = $this->db->select('equipments_asset.equipment_type, asset_type_color.color, asset_types.name, COUNT(equipments_asset.equipment_id) as in_use_count')
    //         ->from('equipments_asset')
    //         ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id')
    //         ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id')
    //         ->group_by('  equipments_asset.equipment_type, 
    // asset_type_color.color, 
    // asset_types.name')
    //         ->get();
    //     $equipment_types_data = $query->result();

    //     if (!isSuperAdmin()) {
    //         $query->where_in('equipments_asset.branch_office_id', getUserActiveBranchsId());
    //     }

    //     // Fetch total assets
    //     $totalAssets = $this->db->count_all('equipments_asset');

    //     // Prepare response data
    //     $data = [

    //         'total' => $totalAssets,
    //         'equipment_types' => $equipment_types_data // Include equipment type data
    //     ];

    //     // Return the response as JSON
    //     echo json_encode($data);
    //     die;
    // }

    // public function getAssetsLocation()
    // {

    //     if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    //         die('invaid request');
    //     }

    //     // $location_data = $query->result();

    //     // $query = $this->db->select( 'state_name, colour, COUNT(*) as in_use_count' )
    //     // ->from( 'locations' )
    //     // ->group_by( [ 'state_name', 'colour' ] )
    //     // ->get();

    //     $query =  $this->db->select('equipments_asset.location_id, locations.colour, locations.state_name, COUNT(*) as in_use_count')
    //         ->from('equipments_asset')
    //         ->join('locations', 'equipments_asset.location_id = locations.id')
    //         // ->where( 'equipments_asset.equipment_status', 'In use' )
    //         ->group_by('equipments_asset.location_id')
    //         ->get();
    //     $equipment_types_data = $query->result();

    //     $location_data = $query->result();

    //     $locations = $this->db->count_all('locations');

    //     // $totalAssetsInUse = $this->db->where( 'equipment_status=', 'In use' )->count_all_results( 'equipments_asset' );
    //     // $totalAssetsInMaintenance = $this->db->where( 'equipment_status=', 'Maintenance' )->count_all_results( 'equipments_asset' );

    //     $data = [
    //         // 'in_use' => $in_use,
    //         // 'available' => $available,
    //         'locations' => $location_data,
    //         // 'decommission' => $decommission,
    //         'total' => $locations,
    //     ];
    //     print_r(json_encode($data));
    //     die;
    // }

    // public function home_table_data()
    // {
    //     $result = $this->db->select([
    //         'equipments_asset.equipment_name',
    //         'states.state_name as state_name',
    //         'equipments_asset.equipment_status'
    //     ])
    //         ->from('equipments_asset')
    //         ->join('states', 'states.id = equipments_asset.state_id')
    //         ->get()
    //         ->result_array();

    //     echo json_encode(['data' => $result]);
    //     exit;
    // }

    public function ajax_list()
    {
        $filterValue = $this->input->get('filter');
        $type = $this->input->get('type'); // Use 'type' to differentiate between ajax_list and item_info queries
        log_message('debug', 'Filter value: ' . $filterValue . ', Type: ' . $type);

        if ($type === 'asset') {
            // Query for assets
            $this->db->select('
                equipments_asset.*,
                locations.name as location_name,
                asset_types.name as asset_type,
                vendor_part_number.part_number as vendor
            ');
            $this->db->from('equipments_asset')
                ->join('states', 'states.id = equipments_asset.state_id', 'left')
                ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
                ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
                ->join('vendor_part_number', 'vendor_part_number.id = equipments_asset.vendor_part_number_id', 'left')
                ->where('locations.name IS NOT NULL')
                ->where('locations.name !=', '');
        } elseif ($type === 'item') {
            // Query for items
            $this->db->select('
                add_asset_items.*,
                equipments_asset.*,
                item_types.name as item_type_name,
                locations.name as location_name,
                states.state_name,
                item_status.name
            ');
            $this->db->from('add_asset_items')
                ->join('equipments_asset', 'equipments_asset.equipment_id = add_asset_items.asset_id', 'left')
                ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
                ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
                ->join('states', 'states.id = equipments_asset.state_id', 'left')
                ->where('locations.name IS NOT NULL')
                ->where('locations.name !=', '');
        } else {
            // Invalid type provided
            echo json_encode(['error' => 'Invalid type provided']);
            return;
        }

        // Apply filter if it exists
        if ($filterValue) {
            $this->db->where('states.state_name', $filterValue);
            log_message('debug', 'Filtering by state_name: ' . $filterValue);
        }

        // Execute the query
        $query = $this->db->get();
        $data = $query->result();

        // DataTables expects a data array. Empty results are valid, not an API error.
        // Legacy behavior returned: echo json_encode(['error' => 'No data found']);
        echo json_encode(['data' => $data]);
    }
}



