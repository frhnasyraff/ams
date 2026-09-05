<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_summary_dashboard  extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->helper('url');
        $this->load->library('pagination');

        if (!$this->user_model->logged_in()) {

            die(redirect('/'));
        }
    }

    public function index()
    {

        $alertMessage = '';
        $itemalertMessage = '';
        $asset_maintenanceAlertMessage = '';
        $item_maintenanceAlertMessage = '';
        $current_date = date('Y-m-d');
        $expiringAssetsCount = 0;
        $expiringItemsCount = 0;

        $asset_calibration_data = $this->db->select('equipments_asset.*')
            ->from('equipments_asset')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        $item_calibration_data = $this->db->select('add_asset_items.*')
            ->from('add_asset_items')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        // Asset calibration loop
        foreach ($asset_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringAssetsCount++;
            }
        }

        // Item calibration loop
        foreach ($item_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            // Ensure $frequency_day is a valid number
            $frequency_day = is_numeric($frequency_day) ? (int)$frequency_day : 0;
            $reminder_day = is_numeric($reminder_day) ? (int)$reminder_day : 0;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            // Debugging output to check date compariso

            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringItemsCount++;
            }
        }




        $overdue_assets_count = 0; // Counter for overdue maintenance

        // Fetch latest maintenance data from the database
        $asset_maintenance_data = $this->db->select('
        equipments_asset.*, 
        latest_maintenance.update_date AS latest_maintenance_date,
        latest_maintenance.maintenance_type_id AS latest_maintenance_type,
        latest_task.remarks AS latest_remarks,
        latest_maintenance.final_status AS latest_final_status
        ')
            ->from('equipments_asset')

            // Join latest equipment_maintenance_asset based on latest created_at timestamp
            ->join('(SELECT ema.* 
        FROM equipment_maintenance_asset ema 
        JOIN (SELECT equipment_id, MAX(created_at) AS max_created_at 
              FROM equipment_maintenance_asset 
              GROUP BY equipment_id) latest_ema 
        ON ema.equipment_id = latest_ema.equipment_id 
        AND ema.created_at = latest_ema.max_created_at
        ) AS latest_maintenance', 'latest_maintenance.equipment_id = equipments_asset.equipment_id', 'left')

            // Join latest maintenance_task_done based on latest created_at timestamp
            ->join('(SELECT mtd.* 
        FROM maintenance_task_done mtd 
        JOIN (SELECT equipment_maintenance_id, MAX(created_at) AS max_created_at 
              FROM maintenance_task_done 
              GROUP BY equipment_maintenance_id) latest_mtd 
        ON mtd.equipment_maintenance_id = latest_mtd.equipment_maintenance_id 
        AND mtd.created_at = latest_mtd.max_created_at
        ) AS latest_task', 'latest_task.equipment_maintenance_id = latest_maintenance.equipment_maintenance_id', 'left')

            // Filters for valid maintenance data
            ->where('equipments_asset.maintenance_date IS NOT NULL', null, false)
            ->where('equipments_asset.frequency_year IS NOT NULL', null, false)
            ->where('equipments_asset.maintenance_reminder_day IS NOT NULL', null, false)
            ->group_by('equipments_asset.equipment_id')
            ->get()
            ->result();

        $current_date = new DateTime();

        foreach ($asset_maintenance_data as $data) {
            $maintenance_date = new DateTime($data->maintenance_date);
            $frequency_per_year = !empty($data->frequency_year) ? (int)$data->frequency_year : 2;
            $reminder_days = !empty($data->maintenance_reminder_day) ? (int)$data->maintenance_reminder_day : 30;
            $interval_duration_months = 12 / $frequency_per_year;
            $is_maintenance_done = false;
            $found_current_interval = false;

            for ($i = 0; $i < $frequency_per_year; $i++) {
                $interval_start = (clone $maintenance_date)->modify('+' . ($interval_duration_months * $i) . ' months');
                $interval_end = (clone $interval_start)->modify('+' . $interval_duration_months . ' months')->modify('-1 day');
                $reminder_date = (clone $interval_end)->modify('-' . $reminder_days . ' days');

                if ($current_date >= $interval_start && $current_date <= $interval_end) {
                    $found_current_interval = true;

                    // Check latest maintenance status
                    if ($data->latest_maintenance_type === 'preventive') {
                        if ($data->latest_final_status === 'complete') {
                            $is_maintenance_done = true;
                        }
                    }

                    if (!$is_maintenance_done && $current_date >= $reminder_date) {
                        $overdue_assets_count++;
                    }

                    break;
                }
            }

            // If no maintenance data exists, consider it overdue
            if (!$found_current_interval) {
                $overdue_assets_count++;
            }
        }


        if ($overdue_assets_count > 0) {
            // Generate alert for the specific equipment
            $asset_maintenanceAlertMessage = "{$overdue_assets_count}";
        }





        // Initialize the counter for overdue items
        $overdue_items_count = 0;
        $item_maintenanceAlertMessage = '';

        // Get the current date
        $current_date = date('Y-m-d');

        // Fetch items with issue_date and date_of_completion
        $items = $this->db->select('item_ticket.id, item_ticket.issue_date, item_ticket.date_of_completion')
            ->from('item_ticket')
            ->where('item_ticket.issue_date <=', $current_date) // Items issued before or equal to today
            ->where('item_ticket.date_of_completion >=', $current_date) // Items not yet completed
            ->get()
            ->result();

        if (!empty($items)) {
            $item_ids = array_column($items, 'id'); // Extract item IDs for filtering

            // Get the latest maintenance logs for the relevant items
            $maintenance_logs = $this->db->select('logs_item_maintenance.item_ticket_id, logs_item_maintenance.final_status')
                ->from('logs_item_maintenance')
                ->where_in('logs_item_maintenance.item_ticket_id', $item_ids)
                ->where('logs_item_maintenance.created_at = (
            SELECT MAX(lim2.created_at) 
            FROM logs_item_maintenance AS lim2 
            WHERE lim2.item_ticket_id = logs_item_maintenance.item_ticket_id
        )', null, false) // Fetch latest maintenance log per item
                ->get()
                ->result();

            // Convert logs into an associative array for faster lookup
            $maintenance_status = [];
            foreach ($maintenance_logs as $log) {
                $maintenance_status[$log->item_ticket_id] = $log->final_status;
            }

            // Count overdue items where maintenance is missing or incomplete
            foreach ($items as $item) {
                if (!isset($maintenance_status[$item->id]) || is_null($maintenance_status[$item->id])) {
                    $overdue_items_count++;
                }
            }
        }

        // Generate alert if there are overdue items
        if ($overdue_items_count > 0) {
            $item_maintenanceAlertMessage .= "Item Maintenance Alert: <a href='Assets_Item_maintenance#nav-item'>&nbsp;&nbsp;&nbsp; {$overdue_items_count} </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;!";
        }


        // Append alerts for both assets and items
        if ($expiringAssetsCount > 0) {
            $alertMessage .= "{$expiringAssetsCount}";
        }

        if ($expiringItemsCount > 0) {
            $itemalertMessage .= "{$expiringItemsCount}";
        }

        $selectedMonth = date('F', strtotime('m'));
        $months = array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
        $customerComplaintsByLocation = $this->db->select('COUNT(branch_id) as customer_complaints, branch_office.branch_name, branch_office.branch_code')
            ->from('branch_office')
            ->join('company_addresses', 'company_addresses.branch_office_id = branch_office.branch_id', 'LEFT')
            ->join('companies', 'companies.company_id = company_addresses.company_id', 'LEFT')
            ->join('orders', 'orders.company_id = companies.company_id', 'LEFT')
            ->like('orders.remarks_updated_at', date('Y-m'))
            ->where('branch_office.active', '1')
            ->group_by('branch_office.branch_id')
            ->get()
            ->result_object();

        $assestsAvailability = $this->db->select('COUNT(branch_id) as assets_available, branch_office.branch_name, branch_office.branch_code')
            ->from('branch_office')
            ->join('equipments_asset', 'equipments_asset.branch_office_id = branch_office.branch_id', 'LEFT')
            ->where('equipments_asset.equipment_status', 'Available')
            ->group_by('branch_office.branch_id')
            ->get()
            ->result_object();

        // $totalAssets = $this->db->where( 'equipment_status!=', null )->count_all_results( 'equipments_asset' );
        $totalAssets = $this->db->count_all('equipments_asset');

        $totalAssetsServiceable = $this->db->where('equipment_status', 'SERVICEABLE')->count_all_results('equipments_asset');
        $UnServiceable_assets = $this->db->where('equipment_status', 'UNSERVICEABLE')->count_all_results('equipments_asset');
        $totalAssetsInMaintenance = $this->db->where('equipment_status', 'MAINTENANCE')->count_all_results('equipments_asset');

        // // Fetch all unique equipment types
        // $equipment_types = $this->db->select('asset_id, name, asset_type_color.color')
        //     ->from('asset_types')
        //     ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id', 'left')
        //     ->get()
        //     ->result();

        // $totalAssetsInMaintenance = null;
        // foreach ($equipment_types as $type) {
        //     // Total assets of the current equipment type
        //     $total_assets = $this->db->select('COUNT(*) as total')
        //         ->from('equipments_asset')
        //         ->where('equipment_type', $type->asset_id)
        //         ->get()
        //         ->row()
        //         ->total;

        //     if ($total_assets > 0) {

        //         // Assets with maintenance_type_id = corrective
        //         $corrective_maintenance = $this->db->select('COUNT(DISTINCT equipments_asset.equipment_id) as corrective_count')
        //             ->from('equipments_asset')
        //             ->join(
        //                 '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
        //             FROM equipment_maintenance_asset 
        //             WHERE maintenance_type_id = "corrective" AND final_status != "complete"
        //             GROUP BY equipment_id
        //         ) t2',
        //                 'equipments_asset.equipment_id = t2.equipment_id',
        //                 'left'
        //             )
        //             ->where('equipments_asset.equipment_type', $type->asset_id)
        //             ->where('equipments_asset.equipment_status', 'MAINTENANCE')
        //             ->get()
        //             ->row()
        //             ->corrective_count ?? 0; // Ensure it defaults to 0 if NULL

        //         // Assets with maintenance_type_id = preventive
        //         $default_frequency_year = 2; // Default frequency in years
        //         $current_date = date('Y-m-d');

        //         $preventive_maintenance = $this->db->select('COUNT(*) as preventive_count')
        //             ->from('equipments_asset ea')
        //             ->join(
        //                 '(SELECT equipment_id, maintenance_date, frequency_year, maintenance_reminder_day 
        //             FROM equipments_asset 
        //             WHERE frequency_year IS NOT NULL AND maintenance_date IS NOT NULL
        //         ) e_freq',
        //                 'ea.equipment_id = e_freq.equipment_id',
        //                 'inner'
        //             )
        //             ->join(
        //                 '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
        //             FROM equipment_maintenance_asset 
        //             WHERE maintenance_type_id = "preventive"
        //             GROUP BY equipment_id
        //         ) t2',
        //                 'ea.equipment_id = t2.equipment_id',
        //                 'left'
        //             )
        //             ->where('ea.equipment_type', $type->asset_id)
        //             ->where('(t2.final_status IS NULL OR t2.final_status != "complete")')
        //             ->where("TIMESTAMPDIFF(MONTH, e_freq.maintenance_date, '$current_date') >= (12 / COALESCE(e_freq.frequency_year, $default_frequency_year))")
        //             ->get()
        //             ->row()
        //             ->preventive_count ?? 0; // Ensure it defaults to 0 if NULL

        //         // **Ignore this data if both maintenance counts are 0**
        //         if ($corrective_maintenance > 0 || $preventive_maintenance > 0) {

        //             $totalAssetsInMaintenance = $corrective_maintenance + $preventive_maintenance;
        //         }
        //     }
        // }

        $this->db->from('equipments_asset');
        $this->db->where('faulty_type_id !=', null);
        $faultyItemCount = $this->db->count_all_results();

        $this->db->select('states.id AS state_id, MIN(locations.colour) AS colour, states.state_name, COUNT(DISTINCT equipments_asset.equipment_id) AS in_use_count');
        $this->db->from('equipments_asset');
        $this->db->join('locations', 'equipments_asset.location_id = locations.id');
        // Legacy: $this->db->join('states', 'locations.state_name = states.state_name');
        $this->db->join('states', 'locations.state_id = states.id', 'left');
        $this->db->group_by('states.id, states.state_name');
        $query = $this->db->get();

        $location_data = $query->result();

        // Calculate total in_use_count
        // $totalLocations = array_sum(array_column($location_data, 'in_use_count'));
        // $totalLocations = $this->db
        //     ->select('COUNT(DISTINCT equipments_asset.location_id) as total_locations')
        //     ->from('equipments_asset')
        //     ->join('locations', 'locations.id = equipments_asset.location_id', 'inner')
        //     ->get()
        //     ->row()
        //     ->total_locations;

        $totalLocations = $this->db
            ->select('COUNT(DISTINCT states.id) as total')
            ->from('states')
            ->join('equipments_asset', 'states.id = equipments_asset.state_id', 'inner')
            ->get()
            ->row()
            ->total;



        // die();

        // Assit Quantity graph list
        // $query =  $this->db->select( 'equipments_asset.equipment_type, equipment_types.equipment_type_colour, equipment_types.equipment_type_name, COUNT(*) as in_use_count' )
        // ->from( 'equipments_asset' )
        // ->join( 'equipment_types', 'equipments_asset.equipment_type = equipment_types.equipment_type_id' )
        // ->where_in( 'equipments_asset.equipment_status', [ 'In use', 'Available', 'Maintenance', 'Standby', 'Repair', 'Sold', 'Dispose', 'Scrap' ] )
        // ->group_by( 'equipments_asset.equipment_type' )
        // ->get();
        // $equipment_types_data = $query->result();

        // var_dump( $equipment_types_data )

        // Total items count

        $total_items = $this->db->count_all('add_asset_items');


        $ServiceableCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'SERVICEABLE')
            ->count_all_results();




        $MaintinenceItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'MAINTENANCE')
            ->count_all_results();

        $UnserviceableCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'UNSERVICEABLE')
            ->count_all_results();

       $storelocationItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'STORE')
            ->count_all_results();

        // Fetch tickets and their latest status from maintenance along with asset details
        $data = $this->db->select('ticket.*, 
        equipments_asset.*, 
        COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") AS final_status, 
        latest_maintenance_asset.update_date AS update_date,
        latest_maintenance_asset.maintenance_type_id AS maintenance_type,
        latest_maintenance_asset.faulty_type,
        latest_task_done.task_done AS task_done,
        latest_task_done.remarks AS remarks')
            ->from('ticket')
            // Legacy: ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
            ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.asset_number', 'left')

            // Fetch the latest maintenance record per ticket
            ->join(
                '(SELECT * FROM (
            SELECT t1.*, 
                   ROW_NUMBER() OVER (PARTITION BY t1.ticket_number ORDER BY t1.created_at DESC) AS rn
            FROM equipment_maintenance_asset t1
        ) latest WHERE latest.rn = 1) AS latest_maintenance_asset',
                'latest_maintenance_asset.ticket_number = ticket.ticket_number',
                'left'
            )

            // Fetch the latest task done per maintenance record
            ->join(
                '(SELECT * FROM (
            SELECT t1.*, 
                   ROW_NUMBER() OVER (PARTITION BY t1.equipment_maintenance_id ORDER BY t1.created_at DESC) AS rn
            FROM maintenance_task_done t1
        ) latest WHERE latest.rn = 1) AS latest_task_done',
                'latest_task_done.equipment_maintenance_id = latest_maintenance_asset.equipment_maintenance_id',
                'left'
            )

            ->get()
            ->result();

        $status_counts = [
            'in_progress' => 0,
            'in_maintenance' => 0
        ];

        foreach ($data as $row) {
            $status = trim(strtolower($row->final_status)); // Normalize casing and remove spaces

            if ($status === 'in_progress') {
                $status_counts['in_progress']++;
            } elseif ($status === 'in-maintenance') {
                $status_counts['in_maintenance']++;
            }
        }




        $this->load->view('header', ['title' => 'Asset Summary', 'title2' => 'OrderSummary', 'styles' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
            'design/css/order-summary.css',
            'design/css/order-summary-cards.css',
            'design/css/order-summaryMaintenance.css',
        ]]);

        $this->load->view('asset-summary-dashboard', [
            'total_items' => $total_items,
            'ServiceableCount' => $ServiceableCount,
            'MaintinenceItemCount' => $MaintinenceItemCount,
            'UnserviceableCount' => $UnserviceableCount,
            'storelocationItemCount' => $storelocationItemCount,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'customerComplaintsByLocation' => $customerComplaintsByLocation,
            'UnServiceable_assets' => $UnServiceable_assets,
            'faulty_assets' => $UnServiceable_assets,
            'assestsAvailability' => $assestsAvailability,
            'totalAssets' => $totalAssets,
            'totalAssetsServiceable' => $totalAssetsServiceable,
            'totalAssetsInMaintenance' => $totalAssetsInMaintenance,
            'totalLocations' => $totalLocations,
            'alertMessage' => $alertMessage,
            'itemalertMessage' => $itemalertMessage,
            'asset_maintenanceAlertMessage' => $asset_maintenanceAlertMessage,
            'item_maintenanceAlertMessage' => $item_maintenanceAlertMessage

        ]);
        $this->load->view('footer', ['scripts' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js',
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.4.0/dist/chartjs-plugin-datalabels.min.js',
            'design/js/graph-colors.js',
            'design/js/summary-chart-canvas-fix.js',
            'design/js/order-summary.js?v=4',
            'design/js/order-summaryMaintenance.js?v=4',
            'design/js/store-summary.js?v=4',
            'design/js/order-summaryLocation.js?v=4',
            'design/js/order-summaryQuantity.js?v=4',

            'design/js/order-summaryFaulty.js?v=4',

            'design/js/equipment_asset_map.js?v=2'
        ]]);
    } 
}


