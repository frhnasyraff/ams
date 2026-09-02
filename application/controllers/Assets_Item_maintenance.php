<?php
defined("BASEPATH") or exit("No direct script access allowed");

class Assets_Item_maintenance extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('user_model');

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {

            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {
        $overdue_assets_count = 0; // Counter for overdue maintenance
        // Fetch latest maintenance data from the database
        $asset_maintenance_data = $this
            ->db
            ->select('
        equipments_asset.*, 
        latest_maintenance.update_date AS latest_maintenance_date,
        latest_maintenance.maintenance_type_id AS latest_maintenance_type,
        latest_task.remarks AS latest_remarks,
        latest_maintenance.final_status AS latest_final_status
     ')
            ->from("equipments_asset")

            // Join latest equipment_maintenance_asset based on latest created_at timestamp

            ->join('(SELECT ema.* 
        FROM equipment_maintenance_asset ema 
        JOIN (SELECT equipment_id, MAX(created_at) AS max_created_at 
              FROM equipment_maintenance_asset 
              GROUP BY equipment_id) latest_ema 
        ON ema.equipment_id = latest_ema.equipment_id 
        AND ema.created_at = latest_ema.max_created_at
        ) AS latest_maintenance', "latest_maintenance.equipment_id = equipments_asset.equipment_id", "left")

            // Join latest maintenance_task_done based on latest created_at timestamp

            ->join('(SELECT mtd.* 
        FROM maintenance_task_done mtd 
        JOIN (SELECT equipment_maintenance_id, MAX(created_at) AS max_created_at 
              FROM maintenance_task_done 
              GROUP BY equipment_maintenance_id) latest_mtd 
        ON mtd.equipment_maintenance_id = latest_mtd.equipment_maintenance_id 
        AND mtd.created_at = latest_mtd.max_created_at
        ) AS latest_task', "latest_task.equipment_maintenance_id = latest_maintenance.equipment_maintenance_id", "left")

            // Filters for valid maintenance data

            ->where("equipments_asset.maintenance_date IS NOT NULL", null, false)
            ->where("equipments_asset.frequency_year IS NOT NULL", null, false)
            ->where("equipments_asset.maintenance_reminder_day IS NOT NULL", null, false)
            ->group_by("equipments_asset.equipment_id")
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
                $interval_start = (clone $maintenance_date)->modify("+" . $interval_duration_months * $i . " months");
                $interval_end = (clone $interval_start)->modify("+" . $interval_duration_months . " months")->modify("-1 day");
                $reminder_date = (clone $interval_end)->modify("-" . $reminder_days . " days");

                if ($current_date >= $interval_start && $current_date <= $interval_end) {
                    $found_current_interval = true;

                    // Check latest maintenance status
                    if ($data->latest_maintenance_type === "preventive") {
                        if ($data->latest_final_status === "complete") {
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
            $asset_maintenanceAlertMessage = "Asset Maintenance Alert: <a href='Assets_Item_maintenance'>&nbsp;&nbsp;&nbsp; {$overdue_assets_count} </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;!";
        }

        // Initialize the counter for overdue items
        $overdue_items_count = 0;
        $item_maintenanceAlertMessage = "";

        // Get the current date
        $current_date = date("Y-m-d");

        // Current schema: item_ticket does not have issue_date/date_of_completion.
        // Legacy date-window query retained for reference:
        // ->select("item_ticket.id, item_ticket.issue_date, item_ticket.date_of_completion")
        // ->where("item_ticket.issue_date <=", $current_date)
        // ->where("item_ticket.date_of_completion >=", $current_date)
        $items = $this
            ->db
            ->select("item_ticket.id")
            ->from("item_ticket")
            ->where("item_ticket.active", 1)
            ->group_start()
                ->where("item_ticket.status IS NULL", null, false)
                ->or_where_not_in("UPPER(item_ticket.status)", ["COMPLETE", "COMPLETED", "CLOSED"])
            ->group_end()
            ->get()
            ->result();

        if (!empty($items)) {
            $item_ids = array_column($items, "id"); // Extract item IDs for filtering
            // Get the latest maintenance logs for the relevant items
            $maintenance_logs = $this
                ->db
                ->select("logs_item_maintenance.item_ticket_id, logs_item_maintenance.final_status")
                ->from("logs_item_maintenance")
                ->where_in("logs_item_maintenance.item_ticket_id", $item_ids)->where('logs_item_maintenance.created_at = (
            SELECT MAX(lim2.created_at) 
            FROM logs_item_maintenance AS lim2 
            WHERE lim2.item_ticket_id = logs_item_maintenance.item_ticket_id
        )', null, false) // Fetch latest maintenance log per item

                ->get()
                ->result();

            // Convert logs into an associative array for faster lookup
            $maintenance_status = [];
            foreach ($maintenance_logs as $log) {
                $maintenance_status[$log
                    ->item_ticket_id] = $log->final_status;
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

        $this
            ->load
            ->view("header", ["title" => "ASSETS & Components MAINTENANCE", "title2" => "ASSETS & ITEMS MAINTENANCE", "styles" => ["design/css/schedule.css", "design/css/fullcalendar/full-calendar.css",],]);

        $this
            ->load
            ->view("asset-item-maintenance", ["alertMessage" => $asset_maintenanceAlertMessage, "item_maintenanceAlertMessage" => $item_maintenanceAlertMessage,]);

        $this
            ->load
            ->view("footer", ["scripts" => ["design/js/moment.js", "design/js/fullCalendar.js", "design/js/schedule.js?v=2", "design/js/asset-item-maintenance.js?v=2",],]);
    }

    
    //  all events for Fullcalendar
    public function getEvents()
    {
    
        if (
            $this->input->get("filter") === "corrective"
        ) {
            $maintenanceType = 1;
        } elseif ($this->input->get("filter") === "preventive") {
            $maintenanceType = 2;
        }
        // Apply condition in query
        if ($maintenanceType == 1) {
            $data = $this->db->select('
                ticket.*, 
                equipments_asset.*, 
                COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") AS final_status, 
                latest_maintenance_asset.update_date,
                latest_maintenance_asset.maintenance_type_id AS maintenance_type,
                latest_maintenance_asset.faulty_type,
                latest_task_done.task_done AS task_done,
                latest_task_done.remarks AS remarks,
                GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") SEPARATOR ", ") AS asset_items,
                asset_types.name AS equipment_type_name,  
                store_location.name AS store_location_name
            ')
            ->from('ticket')
            ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
            ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left')
            ->join('store_location', 'equipments_asset.store_location_id = store_location.id', 'left')
            ->join('item_ticket', 'item_ticket.equipment_id = ticket.equipment_id', 'left')
            ->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id', 'left')

            // Latest maintenance asset (subquery)
            ->join('(
                SELECT * FROM (
                    SELECT t1.*, 
                        ROW_NUMBER() OVER (PARTITION BY t1.ticket_number ORDER BY t1.created_at DESC) AS rn
                    FROM equipment_maintenance_asset t1
                ) latest 
                WHERE latest.rn = 1
            ) AS latest_maintenance_asset', 'latest_maintenance_asset.ticket_number = ticket.ticket_number', 'left')

            // Latest task done (subquery)
            ->join('(
                SELECT * FROM (
                    SELECT t1.*, 
                        ROW_NUMBER() OVER (PARTITION BY t1.equipment_maintenance_id ORDER BY t1.created_at DESC) AS rn
                    FROM maintenance_task_done t1
                ) latest 
                WHERE latest.rn = 1
            ) AS latest_task_done', 'latest_task_done.equipment_maintenance_id = latest_maintenance_asset.equipment_maintenance_id', 'left')

            // Optional fallback WHERE (example logic, adjust as needed)
            ->group_start()
                ->where('latest_maintenance_asset.update_date IS NOT NULL', null, false)
                ->or_where('ticket.issue_date IS NOT NULL', null, false)
            ->group_end()

            ->group_by('ticket.ticket_number')
            ->get()
            ->result();


            $table_data = [];

            foreach ($data as $row) {
                $itemArray = [];
                if (!empty($row->asset_items)) {
                    $items = explode(", ", $row->asset_items);

                    foreach ($items as $item) {
                        if (preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches)) {
                            $itemArray[] = [
                                "item_name" => trim($matches[1]),
                                "manufacturer_name" => trim($matches[2])
                            ];
                        } else {
                            $itemArray[] = [
                                "item_name" => trim($item),
                                "manufacturer_name" => "No Manufacturer"
                            ];
                        }
                    }
                }

                $table_data[] = (object) [
                    "ticket_number" => $row->ticket_number,
                    "equipment_id" => $row->equipment_id,
                    "equipment_name" => $row->equipment_name,
                    "maintenance_date" => $row->issue_date,
                    "equipment_type_name" => $row->equipment_type_name,
                    "equipment_registration" => $row->equipment_registration,
                    "store_location_name" => $row->store_location_name,
                    "items" => $itemArray,
                    "maintenance_records" => $row->update_date ?? "No Data",
                    "remarks" => $row->remarks ?? "No Remarks",
                    "final_status" => $row->final_status ?? "Unknown",
                ];
            }

            // Initialize formatted arrays before using them
            $formattedinMaintenance = [];
            $formattedInProgress = [];
            $formattedComplete = [];

            foreach ($table_data as $data) {
                // Initialize item list
                $itemDetails = "";
                if (!empty($data->items)) {
                    $count = 1;
                    foreach ($data->items as $item) {
                        $itemName = $item["item_name"] ?? "No Name";
                        $manufacturerName = $item["manufacturer_name"] ?? "No Manufacturer";
                        $itemDetails .= "
                            <div class='items'>{$count}. {$itemName} ({$manufacturerName})</div>
                            ";
                        $count++;
                    }
                } else {
                    $itemDetails = "<p>No Items Found</p>";
                }

                // Organize maintenance statuses properly
                if ($data->final_status === "IN-MAINTENANCE") {
                    $formattedinMaintenance[] = [
                        'id' => count($formattedinMaintenance) + 1,
                        'start' => date('Y-m-d', strtotime($data->maintenance_date)),
                        'data' => $data
                    ];
                } elseif ($data->final_status === "in_progress") { // Ensure case matches database values
                    $formattedInProgress[] = [
                        'id' => count($formattedInProgress) + 1,
                        'start' => date('Y-m-d', strtotime($data->maintenance_records)),
                        'data' => $data
                    ];
                } elseif ($data->final_status === "complete") { // Ensure case matches database values
                    $formattedComplete[] = [
                        'id' => count($formattedComplete) + 1,
                        'start' => date('Y-m-d', strtotime($data->maintenance_records)),
                            'data' => (object)[
                            "ticket_number" => $data->ticket_number,
                            "equipment_id" => $data->equipment_id,
                            "equipment_maintenance_id" => $data->equipment_maintenance_id ?? '150', // âœ… Ensure this has value
                            "equipment_name" => $data->equipment_name,
                            "maintenance_date" => $data->maintenance_date,
                            "equipment_type_name" => $data->equipment_type_name,
                            "equipment_registration" => $data->equipment_registration,
                            "store_location_name" => $data->store_location_name,
                            "items" => $data->items,
                            "maintenance_records" => $data->maintenance_records,
                            "remarks" => $data->remarks,
                            "final_status" => $data->final_status,
                            "details_url" => site_url('Assets_Item_maintenance/task_details/'.$data->equipment_id.'/'.($data->equipment_maintenance_id ?? '150'))
                        ]
                    ];
                }
            }

            // Return JSON response
            die(json_encode([
                'plannedOrders' => $formattedinMaintenance,
                'progressOrders' => $formattedInProgress,
                'completedOrders' => $formattedComplete
            ]));
        }

        if ($maintenanceType == 2) {

            $default_frequency_year = 2;
            $default_reminder_days = 30;
            $currentDate = new DateTime();
            $table_data = [];

            $data = $this->db->select("
                equipments_asset.*,

                equipment_maintenance_asset.equipment_maintenance_id AS equipment_maintenance_id,

                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        add_asset_items.item_name,
                        ' (',
                        IFNULL(add_asset_items.manufacturer_name, 'No Manufacturer'),
                        ')'
                    )
                    SEPARATOR ', '
                ) AS asset_items,

                MAX(equipment_maintenance_asset.update_date) AS latest_maintenance_date,

                equipment_maintenance_asset.maintenance_type_id AS latest_maintenance_type,

                latest_task.remarks AS latest_remarks,

                equipment_maintenance_asset.final_status AS latest_final_status,

                asset_types.name AS equipment_type_name,

                store_location.name AS store_location_name,

                GROUP_CONCAT(
                    DISTINCT CONCAT(
                        equipment_maintenance_asset.equipment_maintenance_id,
                        '||',
                        equipment_maintenance_asset.update_date
                    )
                    ORDER BY equipment_maintenance_asset.update_date ASC
                    SEPARATOR ','
                ) AS maintenance_history
            ", false)
            ->from("equipments_asset")
            ->join("asset_types", "asset_types.asset_id = equipments_asset.equipment_type", "left")
            ->join("store_location", "store_location.id = equipments_asset.store_location_id", "left")
            ->join("add_asset_items", "add_asset_items.asset_id = equipments_asset.equipment_id", "left")
            // âœ… Join only completed maintenance records (for history)
            ->join("equipment_maintenance_asset", "equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id AND equipment_maintenance_asset.final_status = 'complete'", "left")
            ->join('(
                SELECT mtd.*
                FROM maintenance_task_done mtd
                JOIN (
                    SELECT equipment_maintenance_id, MAX(created_at) AS max_created_at
                    FROM maintenance_task_done
                    GROUP BY equipment_maintenance_id
                ) latest_mtd
                    ON mtd.equipment_maintenance_id = latest_mtd.equipment_maintenance_id
                AND mtd.created_at = latest_mtd.max_created_at
            ) AS latest_task', "latest_task.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id", "left")
            ->where("equipments_asset.maintenance_date IS NOT NULL", null, false)
            ->where("equipments_asset.frequency_year IS NOT NULL", null, false)
            ->where("equipments_asset.maintenance_reminder_day IS NOT NULL", null, false)
            ->group_by("equipments_asset.equipment_id")
            ->get()
            ->result();

            foreach ($data as $equipment) {
                // Frequency: times per year â†’ interval in days
                $frequency_per_year = !empty($equipment->frequency_year) ? (int)$equipment->frequency_year : $default_frequency_year;
                $interval_days = round(365 / $frequency_per_year);

                // Calculate next maintenance date
                if (!empty($equipment->latest_maintenance_date)) {
                    try {
                        $latest_date = new DateTime($equipment->latest_maintenance_date);
                        $next_maintenance_date = clone $latest_date;
                        $next_maintenance_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        error_log("âŒ Invalid latest maintenance date for equipment {$equipment->equipment_id}");
                        continue;
                    }
                } else {
                    try {
                        $maintenance_date = new DateTime($equipment->maintenance_date);
                        $next_maintenance_date = clone $maintenance_date;
                        $next_maintenance_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        error_log("âŒ Invalid maintenance_date for equipment {$equipment->equipment_id}");
                        continue;
                    }
                }

                // âœ… NEW: Fetch the maintenance record for the upcoming date
                $upcoming = $this->db->select('equipment_maintenance_id, final_status, update_date')
                    ->from('equipment_maintenance_asset')
                    ->where('equipment_id', $equipment->equipment_id)
                    ->where('DATE(update_date)', $next_maintenance_date->format('Y-m-d'))
                    ->get()
                    ->row();

                // Determine status and correct maintenance ID for upcoming entry
                $upcoming_id = null;
                $status = 'PENDING'; // default

                if ($upcoming) {
                    $upcoming_id = $upcoming->equipment_maintenance_id;
                    // Check tasks for this specific maintenance
                    $taskStatus = $this->getTaskBasedStatus($equipment->equipment_id, $upcoming_id, 'PENDING');
                    $status = ($taskStatus == 'complete') ? 'complete' : 'PENDING';
                } else {
                    // No record for that date â†’ pending
                    $status = 'PENDING';
                    $upcoming_id = null;
                }

                // Process asset items
                $itemArray = [];
                if (!empty($equipment->asset_items)) {
                    $items = explode(", ", $equipment->asset_items);
                    foreach ($items as $item) {
                        preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches);
                        $itemArray[] = [
                            "item_name"         => $matches[1] ?? $item,
                            "manufacturer_name" => $matches[2] ?? "No Manufacturer",
                        ];
                    }
                }

                // ------------------------------
                // 1. Past maintenance history (completed)
                // ------------------------------
                if (!empty($equipment->maintenance_history)) {
                    $history_pairs = explode(',', $equipment->maintenance_history);
                    $i = 1;
                    foreach ($history_pairs as $pair) {
                        $pair = trim($pair);
                        if (empty($pair) || strpos($pair, '||') === false) continue;

                        list($hist_maintenance_id, $past_date) = explode('||', $pair, 2);
                        $past_date = trim($past_date);

                        if (empty($past_date) || $past_date == '0000-00-00' || strtolower($past_date) === 'null') continue;

                        $table_data[] = (object)[
                            "equipment_id"           => $equipment->equipment_id,
                            "equipment_maintenance_id" => $hist_maintenance_id,
                            "equipment_name"         => $equipment->equipment_name,
                            "interval_number"        => "Previous #" . $i,
                            "interval"               => $past_date,
                            "interval_start_date"    => null,
                            "interval_end_date"      => $past_date,
                            "equipment_type_name"    => $equipment->equipment_type_name,
                            "equipment_registration" => $equipment->equipment_registration,
                            "store_location_name"    => $equipment->store_location_name,
                            "items"                  => $itemArray,
                            "maintenance_records"    => $past_date,
                            "remarks"                => "Completed",
                            "final_status"           => "complete",
                        ];
                        $i++;
                    }
                }

                // ------------------------------
                // 2. Next Maintenance (with correct ID and status)
                // ------------------------------
                $table_data[] = (object)[
                    "equipment_id"           => $equipment->equipment_id,
                    "equipment_maintenance_id" => $upcoming_id, // now correct for upcoming
                    "equipment_name"         => $equipment->equipment_name,
                    "interval_number"        => "Next Maintenance",
                    "interval"               => $next_maintenance_date->format("Y-m-d"),
                    "interval_start_date"    => null,
                    "interval_end_date"      => $next_maintenance_date->format("Y-m-d"),
                    "equipment_type_name"    => $equipment->equipment_type_name,
                    "equipment_registration" => $equipment->equipment_registration,
                    "store_location_name"    => $equipment->store_location_name,
                    "items"                  => $itemArray,
                    "maintenance_records"    => $equipment->latest_maintenance_date ?? "No Data",
                    "remarks"                => $equipment->latest_remarks ?? "No Remarks",
                    "final_status"           => $status, // 'PENDING' or 'complete'
                ];
            }

            // ------------------------------
            // Format results into statusâ€‘based arrays (red/blue/green)
            // ------------------------------
            $formattedinMaintenance = []; // Red
            $formattedInProgress = [];    // Blue
            $formattedComplete = [];      // Green

            foreach ($table_data as $key => $order) {
                $start_date = !empty($order->interval_start_date)
                    ? date('Y-m-d', strtotime($order->interval_start_date))
                    : $order->interval;

                $formatted_record = [
                    "id" => $key + 1,
                    "start" => $start_date,
                    "title" => $order->equipment_name ,
                    "data" => $order
                ];

                if ($order->final_status === "PENDING") {
                    $formattedinMaintenance[] = $formatted_record;
                } elseif ($order->final_status === "complete") {
                    $formattedComplete[] = $formatted_record;
                } else {
                    $formattedInProgress[] = $formatted_record;
                }
            }

            // Return JSON
            die(json_encode([
                'plannedOrders' => $formattedinMaintenance,
                'progressOrders' => $formattedInProgress,
                'completedOrders' => $formattedComplete
            ]));
        }
    }


    private function getTaskBasedStatus($equipment_id, $maintenance_id, $fallback_status)
    {
        if (empty($maintenance_id)) {
            return $fallback_status;
        }

        $this->db->select('COUNT(*) as total_tasks, SUM(CASE WHEN status = "completed" THEN 1 ELSE 0 END) as completed_tasks');
        $this->db->from('equipment_maintenance_tasks');
        $this->db->where('equipment_id', $equipment_id);
        $this->db->where('equipment_maintenance_id', $maintenance_id);
        $result = $this->db->get()->row();

        if (!$result || $result->total_tasks == 0) {
            return 'PENDING';   // no tasks â†’ pending
        }

        return ($result->total_tasks == $result->completed_tasks) ? "complete" : "PENDING";
    }


    // get orders for specific data
    public function getDateOrders()
    {
        // if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
        //     die('invaid request');
        // }

        if ($this->input->get('date')) {

            $date = $this->input->get('date');

            $table_data = [];
            if ($this->input->get("filter") === "corrective") {

                $maintenanceType = 1;
            } elseif ($this->input->get("filter") === "preventive") {

                $maintenanceType = 2;
            }
            // Apply condition in query
            if ($maintenanceType == 1) {

                // Start building base query
                $this->db->select('ticket.*, 
                    equipments_asset.*, 
                    COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") AS final_status, 
                    latest_maintenance_asset.update_date AS update_date,
                    latest_maintenance_asset.maintenance_type_id AS maintenance_type,
                    latest_maintenance_asset.faulty_type,
                    latest_task_done.task_done AS task_done,
                    latest_task_done.remarks AS remarks,
                    GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") SEPARATOR ", ") AS asset_items,
                    asset_types.name AS equipment_type_name,  
                    store_location.name AS store_location_name');

                $this->db->from('ticket');
                $this->db->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left');
                $this->db->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left');
                $this->db->join('store_location', 'equipments_asset.store_location_id = store_location.id', 'left');
                $this->db->join('item_ticket', 'item_ticket.equipment_id = ticket.equipment_id', 'left');
                $this->db->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id', 'left');

                $this->db->join('(SELECT * FROM (
                    SELECT t1.*, ROW_NUMBER() OVER (PARTITION BY t1.ticket_number ORDER BY t1.created_at DESC) AS rn
                    FROM equipment_maintenance_asset t1
                ) latest WHERE latest.rn = 1) AS latest_maintenance_asset', 'latest_maintenance_asset.ticket_number = ticket.ticket_number', 'left');

                $this->db->join('(SELECT * FROM (
                    SELECT t1.*, ROW_NUMBER() OVER (PARTITION BY t1.equipment_maintenance_id ORDER BY t1.created_at DESC) AS rn
                    FROM maintenance_task_done t1
                ) latest WHERE latest.rn = 1) AS latest_task_done', 'latest_task_done.equipment_maintenance_id = latest_maintenance_asset.equipment_maintenance_id', 'left');

                // Apply conditional WHERE clause
                $this->db->group_start(); // Open group
                    $this->db->where('latest_maintenance_asset.update_date', $date);
                    $this->db->or_group_start();
                        $this->db->where('latest_maintenance_asset.update_date IS NULL', null, false);
                        $this->db->where('ticket.issue_date', $date);
                    $this->db->group_end();
                $this->db->group_end(); // Close group

                $this->db->group_by('ticket.ticket_number');

                $data = $this->db->get()->result();

                $table_data = [];

                foreach ($data as $row) {
                    $itemArray = [];
                    if (!empty($row->asset_items)) {
                        $items = explode(", ", $row->asset_items); // Ensure correct splitting

                        foreach ($items as $item) {
                            // Extract name and manufacturer from "ItemName (ManufacturerName)"
                            if (preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches)) {
                                $itemArray[] = [
                                    "item_name" => trim($matches[1]),
                                    "manufacturer_name" => trim($matches[2])
                                ];
                            } else {
                                $itemArray[] = [
                                    "item_name" => trim($item),
                                    "manufacturer_name" => "No Manufacturer"
                                ];
                            }
                        }
                    }

                    $table_data[] = (object) [
                        "ticket_number" => $row->ticket_number,
                        "equipment_id" => $row->equipment_id,
                        "equipment_name" => $row->equipment_name,
                        "maintenance_date" => $row->issue_date,
                        "equipment_type_name" => $row->equipment_type_name,
                        "equipment_registration" => $row->equipment_registration,
                        "store_location_name" => $row->store_location_name,
                        "items" => $itemArray, // This should now contain items
                        "maintenance_records" => $row->update_date ?? "No Data",
                        "remarks" => $row->remarks ?? "No Remarks",
                        "final_status" => $row->final_status ?? "Unknown",
                    ];
                }


                $html = "";
                foreach ($table_data as $data) {
                    // Initialize item list
                    $itemDetails = "";
                    if (!empty($data->items)) {
                        $itemDetails .= "<table class='table-bordered item-table'>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Name</th>
                                                    <th>Manufacturer Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                        $count = 1;
                        foreach ($data->items as $item) {
                            $itemName = $item["item_name"] ?? "No Name";
                            $manufacturerName = $item["manufacturer_name"] ?? "No Manufacturer";
                            $itemDetails .= "
                                <tr>
                                    <td>{$count}</td>
                                    <td>{$itemName}</td>
                                    <td>{$manufacturerName}</td>
                                </tr>
                            ";
                            $count++;
                        }

                        $itemDetails .= "</tbody></table>";
                    } else {
                        $itemDetails = "<p>No Items Found</p>";
                    }
                    // Assign status class dynamically
                    $class = "";
                    $status = "";
                    if ($data->final_status == "IN-MAINTENANCE") {
                        $class = "planned";
                        $status = "In-Maintenance";
                    } elseif ($data->final_status === "in_progress") {
                        $class = "progresss";
                        $status = "In-Progress";
                    } elseif ($data->final_status === "complete") {
                        $class = "completed";
                        $status = "Completed";
                    } else {
                        $class = "unknown";
                        $status = "Unknown";
                    }
                    // Build card with dynamic data
                    $cardHtml = "
                        <div class='card schedule-card {$class} mb-3'>
                            <div class='card-body'>
                                <div class='header'>
                                    <div class='left'>
                                        <div class='dots'>
                                            <span class='dot'></span>
                                            <span class='dot'></span>
                                            <span class='dot'></span>
                                        </div>
                                    </div>
                                    <div class='right'>
                                        <p>Status</p>
                                        <span class='status'>{$status}</span>
                                    </div>
                                </div>
                                <div class='content'>
                                    <div class='wrapper'>
                                        <div class='key'>Ticket Number</div>
                                        <div class='value ticket-number'>{$data->ticket_number}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Date</div>
                                        <div class='value reminder-date'>{$data->maintenance_date}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Asset Name</div>
                                        <div class='value equipment-name'>{$data->equipment_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Equipment Type</div>
                                        <div class='value equipment-type-name'>{$data->equipment_type_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Equipment Registration1</div>
                                        <div class='value equipment-registration'>{$data->equipment_registration}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Store Location</div>
                                        <div class='value store-location-name'>{$data->store_location_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Items</div>
                                        
                                                {$itemDetails}
                                           
                                    </div>



                                </div>
                            </div>
                        </div>
                        ";

                    // Replace placeholders
                    $cardHtml = str_replace("{{CLASS}}", $class, $cardHtml);
                    $cardHtml = str_replace("{{STATUS}}", $status, $cardHtml);
                    $cardHtml = str_replace("{{REMINDER_DATE}}", $data->maintenance_records, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_NAME}}", $data->equipment_name, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_TYPE_NAME}}", $data->equipment_type_name, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_REGISTRATION}}", $data->equipment_registration, $cardHtml);
                    $cardHtml = str_replace("{{STORE_LOCATION_NAME}}", $data->store_location_name, $cardHtml);
                    // Append to main HTML
                    $html .= $cardHtml;
                }
                print_r($html);
                die;
            }          elseif ($maintenanceType == 2) {
            $filter_date_str = $date;
            if (empty($filter_date_str)) {
                die;
            }

            $filter_date = new DateTime($filter_date_str);
            $table_data = [];

            // Fetch all equipments with maintenance settings and asset items
            $equipments = $this->db->select('
                    equipments_asset.*,
                    asset_types.name AS equipment_type_name,
                    store_location.name AS store_location_name,
                    GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") SEPARATOR ", ") AS asset_items
                ')
                ->from('equipments_asset')
                ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
                ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
                ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
                ->where('equipments_asset.maintenance_date IS NOT NULL', null, false)
                ->where('equipments_asset.frequency_year IS NOT NULL', null, false)
                ->where('equipments_asset.maintenance_reminder_day IS NOT NULL', null, false)
                ->group_by('equipments_asset.equipment_id')
                ->get()
                ->result();

            foreach ($equipments as $equipment) {
                // Calculate next maintenance date (same as in getEvents)
                $frequency_per_year = !empty($equipment->frequency_year) ? (int)$equipment->frequency_year : 2;
                $interval_days = round(365 / $frequency_per_year);

                // Get the latest maintenance date (if any)
                $latest = $this->db->select('update_date')
                    ->from('equipment_maintenance_asset')
                    ->where('equipment_id', $equipment->equipment_id)
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->row();

                if ($latest && !empty($latest->update_date)) {
                    try {
                        $last_date = new DateTime($latest->update_date);
                        $next_date = clone $last_date;
                        $next_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        continue;
                    }
                } else {
                    try {
                        $maintenance_date = new DateTime($equipment->maintenance_date);
                        $next_date = clone $maintenance_date;
                        $next_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        continue;
                    }
                }

                // Skip if this equipment's next date does not match the filter date
                if ($next_date->format('Y-m-d') != $filter_date->format('Y-m-d')) {
                    continue;
                }

                // Fetch the actual maintenance record for this exact date
                $upcoming = $this->db->select('equipment_maintenance_id, final_status, update_date')
                    ->from('equipment_maintenance_asset')
                    ->where('equipment_id', $equipment->equipment_id)
                    ->where('DATE(update_date)', $next_date->format('Y-m-d'))
                    ->get()
                    ->row();

                $status = 'PENDING';
                $maintenance_id = null;

                if ($upcoming) {
                    $maintenance_id = $upcoming->equipment_maintenance_id;
                    // Check tasks for this specific maintenance
                    $taskStatus = $this->getTaskBasedStatus($equipment->equipment_id, $maintenance_id, 'PENDING');
                    $status = ($taskStatus == 'complete') ? 'complete' : 'PENDING';
                } else {
                    $status = 'PENDING';
                    $maintenance_id = null;
                }

                // Prepare asset items
                $itemArray = [];
                if (!empty($equipment->asset_items)) {
                    $items = explode(", ", $equipment->asset_items);
                    foreach ($items as $item) {
                        preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches);
                        $itemArray[] = [
                            "item_name"         => $matches[1] ?? $item,
                            "manufacturer_name" => $matches[2] ?? "No Manufacturer",
                        ];
                    }
                }

                // Build card data
                $table_data[] = (object)[
                    "equipment_id"              => $equipment->equipment_id,
                    "equipment_maintenance_id"  => $maintenance_id,
                    "equipment_name"            => $equipment->equipment_name,
                    "interval_number"           => "Next Maintenance",
                    "interval"                  => $next_date->format("Y-m-d"),
                    "interval_start_date"       => null,
                    "interval_end_date"         => $next_date->format("Y-m-d"),
                    "equipment_type_name"       => $equipment->equipment_type_name,
                    "equipment_registration"    => $equipment->equipment_registration,
                    "store_location_name"       => $equipment->store_location_name,
                    "items"                     => $itemArray,
                    "maintenance_records"       => $latest->update_date ?? "No Data",
                    "remarks"                   => ($status == 'complete') ? "Completed" : "Pending",
                    "final_status"              => $status, // 'PENDING' or 'complete'
                ];
            }

            // ========== GENERATE HTML CARDS ==========
            $html = "";
            foreach ($table_data as $data) {
                // Build item table
                $itemDetails = "";
                if (!empty($data->items)) {
                    $itemDetails .= "<table class='table-bordered item-table'>
                                        <thead><tr><th>#</th><th>Item Name</th><th>Manufacturer Name</th></tr></thead><tbody>";
                    $count = 1;
                    foreach ($data->items as $item) {
                        $itemName = $item["item_name"] ?? "No Name";
                        $manufacturerName = $item["manufacturer_name"] ?? "No Manufacturer";
                        $itemDetails .= "<tr><td>{$count}</td><td>{$itemName}</td><td>{$manufacturerName}</td></tr>";
                        $count++;
                    }
                    $itemDetails .= "</tbody></table>";
                } else {
                    $itemDetails = "<p>No Items Found</p>";
                }

                // Determine CSS class and status text
                // We want PENDING â†’ red (planned), complete â†’ green (completed)
                if ($data->final_status === "PENDING") {
                    $class = "planned";
                    $statusText = "PENDING";
                } elseif ($data->final_status === "complete") {
                    $class = "completed";
                    $statusText = "Completed";
                } else {
                    $class = "progresss";
                    $statusText = "In-Maintenance";
                }

                // Replan button (only for PENDING)
                $replanButtonHtml = ($data->final_status === "PENDING")
                    ? "<div class='right replan-button-container replan'>
                        <button type='button' class='float-right btn btn-primary btn-sm open-replan-modal'
                            data-bs-toggle='modal' data-bs-target='#replanModal'
                            data-equipment-id='" . htmlspecialchars($data->equipment_id) . "'
                            data-current-due-date='" . htmlspecialchars($data->interval_end_date) . "'>
                            <i class='fas fa-edit'></i>
                        </button>
                    </div>"
                    : "";

                // Card HTML
                $cardHtml = "
                    <div class='card schedule-card {$class} mb-3'>
                        <div class='card-body'>
                            <div class='header'>
                                <div class='left'>
                                    <div class='dots'>
                                        <span class='dot'></span><span class='dot'></span><span class='dot'></span>
                                    </div>
                                </div>
                                <div class='right'>
                                    <p>Status</p>
                                    <span class='status'>{$statusText}</span>
                                </div>
                            </div>
                            <div class='content'>
                                <div class='right'>
                                    {$replanButtonHtml}
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Due Date</div>
                                    <div class='value reminder-date'>{$data->interval_end_date}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Asset Name</div>
                                    <div class='value equipment-name'>{$data->equipment_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Equipment Type</div>
                                    <div class='value equipment-type-name'>{$data->equipment_type_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Equipment Registration</div>
                                    <div class='value equipment-registration'>{$data->equipment_registration}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Store Location</div>
                                    <div class='value store-location-name'>{$data->store_location_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Items</div>
                                    {$itemDetails}
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Actions</div>
                                    <div class='value'>
                                        " . (!empty($data->equipment_maintenance_id) ? "
                                        <a href='" . site_url('Assets_Item_maintenance/task_details/'.$data->equipment_id.'/'.$data->equipment_maintenance_id) . "'
                                            class='btn btn-info btn-sm details-btn'>
                                            <i class='fas fa-list'></i> Details
                                        </a>" : "<span>No Actions</span>") . "
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
                $html .= $cardHtml;
            }

            print_r($html);
            die;
        }
    }
}


    public function getMonthlyOrders()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('invaid request');
        }
        if ($this->input->get("current_year") && $this->input->get("current_month")) {
            $year = $this->input->get("current_year");
            $month = $this->input->get("current_month");
            $table_data = [];
            if (
                $this
                ->input
                ->get("filter") === "corrective"
            ) {
                $maintenanceType = 1;
            } elseif ($this->input->get("filter") === "preventive") {
                $maintenanceType = 2;
            }
            // Apply condition in query
            if ($maintenanceType == 1) {
                $year = $this->input->get("current_year");
                $month = $this->input->get("current_month");

                $data = $this->db->select('ticket.*, 
                        equipments_asset.*, 
                        COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") AS final_status, 
                        latest_maintenance_asset.update_date AS update_date,
                        latest_maintenance_asset.maintenance_type_id AS maintenance_type,
                        latest_maintenance_asset.faulty_type,
                        latest_task_done.task_done AS task_done,
                        latest_task_done.remarks AS remarks,
                        GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") SEPARATOR ", ") AS asset_items,
                        asset_types.name AS equipment_type_name,  
                        store_location.name AS store_location_name')
                    ->from('ticket')
                    ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
                    ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left')
                    ->join('store_location', 'equipments_asset.store_location_id = store_location.id', 'left')
                    ->join('item_ticket', 'item_ticket.equipment_id = ticket.equipment_id', 'left')
                    ->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id', 'left')

                    // Latest maintenance asset subquery
                    ->join('(
                        SELECT * FROM (
                            SELECT t1.*, 
                                ROW_NUMBER() OVER (PARTITION BY t1.ticket_number ORDER BY t1.created_at DESC) AS rn
                            FROM equipment_maintenance_asset t1
                        ) latest 
                        WHERE latest.rn = 1
                    ) AS latest_maintenance_asset', 'latest_maintenance_asset.ticket_number = ticket.ticket_number', 'left')

                    // Latest task done subquery
                    ->join('(
                        SELECT * FROM (
                            SELECT t1.*, 
                                ROW_NUMBER() OVER (PARTITION BY t1.equipment_maintenance_id ORDER BY t1.created_at DESC) AS rn
                            FROM maintenance_task_done t1
                        ) latest 
                        WHERE latest.rn = 1
                    ) AS latest_task_done', 'latest_task_done.equipment_maintenance_id = latest_maintenance_asset.equipment_maintenance_id', 'left')

                    // Filter by date: latest_maintenance_asset.update_date OR fallback to ticket.issue_date
                    ->group_start()
                        ->group_start()
                            ->where('YEAR(latest_maintenance_asset.update_date)', $year)
                            ->where('MONTH(latest_maintenance_asset.update_date)', $month)
                        ->group_end()
                        ->or_group_start()
                            ->where('latest_maintenance_asset.update_date IS NULL', null, false)
                            ->where('YEAR(ticket.issue_date)', $year)
                            ->where('MONTH(ticket.issue_date)', $month)
                        ->group_end()
                    ->group_end()

                    ->group_by('ticket.ticket_number')
                    ->get()
                    ->result();

                $table_data = [];

                foreach ($data as $row) {
                    $itemArray = [];
                    if (!empty($row->asset_items)) {
                        $items = explode(", ", $row->asset_items); // Ensure correct splitting

                        foreach ($items as $item) {
                            // Extract name and manufacturer from "ItemName (ManufacturerName)"
                            if (preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches)) {
                                $itemArray[] = [
                                    "item_name" => trim($matches[1]),
                                    "manufacturer_name" => trim($matches[2])
                                ];
                            } else {
                                $itemArray[] = [
                                    "item_name" => trim($item),
                                    "manufacturer_name" => "No Manufacturer"
                                ];
                            }
                        }
                    }

                    $table_data[] = (object) [
                        "ticket_number" => $row->ticket_number,
                        "equipment_id" => $row->equipment_id,
                        "equipment_name" => $row->equipment_name,
                        "maintenance_date" => $row->issue_date,
                        "equipment_type_name" => $row->equipment_type_name,
                        "equipment_registration" => $row->equipment_registration,
                        "store_location_name" => $row->store_location_name,
                        "items" => $itemArray, // This should now contain items
                        "maintenance_records" => $row->update_date ?? "No Data",
                        "remarks" => $row->remarks ?? "No Remarks",
                        "final_status" => $row->final_status ?? "Unknown",
                    ];
                }


                $html = "";
                foreach ($table_data as $data) {
                    // Initialize item list
                    $itemDetails = "";
                    if (!empty($data->items)) {
                        $itemDetails .= "<table class='table-bordered item-table'>
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Item Name</th>
                                                    <th>Manufacturer Name</th>
                                                </tr>
                                            </thead>
                                            <tbody>";

                        $count = 1;
                        foreach ($data->items as $item) {
                            $itemName = $item["item_name"] ?? "No Name";
                            $manufacturerName = $item["manufacturer_name"] ?? "No Manufacturer";
                            $itemDetails .= "
                                <tr>
                                    <td>{$count}</td>
                                    <td>{$itemName}</td>
                                    <td>{$manufacturerName}</td>
                                </tr>
                            ";
                            $count++;
                        }

                        $itemDetails .= "</tbody></table>";
                    } else {
                        $itemDetails = "<p>No Items Found</p>";

                        

                    }
                    // Assign status class dynamically
                    $class = "";
                    $status = "";
                    if ($data->final_status == "IN-MAINTENANCE") {
                        $class = "planned";
                        $status = "In-Maintenance";
                    } elseif ($data->final_status === "in_progress") {
                        $class = "progresss";
                        $status = "In-Progress";
                    } elseif ($data->final_status === "complete") {
                        $class = "completed";
                        $status = "Completed";
                    } else {
                        $class = "unknown";
                        $status = "Unknown";
                    }
                    // Build card with dynamic data
                    $cardHtml = "
                        <div class='card schedule-card {$class} mb-3'>
                            <div class='card-body'>
                                <div class='header'>
                                    <div class='left'>
                                        <div class='dots'>
                                            <span class='dot'></span>
                                            <span class='dot'></span>
                                            <span class='dot'></span>
                                        </div>
                                    </div>
                                    <div class='right'>
                                        <p>Status</p>
                                        <span class='status'>{$status}</span>
                                    </div>
                                </div>
                                <div class='content'>
                                    <div class='wrapper'>
                                        <div class='key'>Ticket Number</div>
                                        <div class='value ticket-number'>{$data->ticket_number}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Date</div>
                                        <div class='value reminder-date'>{$data->maintenance_date}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Asset Name</div>
                                        <div class='value equipment-name'>{$data->equipment_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Equipment Type</div>
                                        <div class='value equipment-type-name'>{$data->equipment_type_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Equipment Registration3</div>
                                        <div class='value equipment-registration'>{$data->equipment_registration}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Store Location</div>
                                        <div class='value store-location-name'>{$data->store_location_name}</div>
                                    </div>
                                    <div class='wrapper'>
                                        <div class='key'>Items</div>
                                        
                                                {$itemDetails}
                                           
                                    </div>



                                    <div class='wrapper'>
                                        <div class='key'>Actions</div>
                                        <div class='value'>
                                            " . (!empty($data->equipment_maintenance_id) ? "
                                            <a href='".site_url('Assets_Item_maintenance/task_details/'.$data->equipment_id.'/'.$data->equipment_maintenance_id)."'  
                                                class='btn btn-info btn-sm details-btn'>
                                                <i class='fas fa-list'></i> Details
                                            </a>" : "<span>No Actions</span>") . "
                                        </div>
                                    </div>




                                </div>
                            </div>
                        </div>
                        ";

                    // Replace placeholders
                    $cardHtml = str_replace("{{CLASS}}", $class, $cardHtml);
                    $cardHtml = str_replace("{{STATUS}}", $status, $cardHtml);
                    $cardHtml = str_replace("{{TICKET_NUMBER}}", $data->ticket_number, $cardHtml);
                    $cardHtml = str_replace("{{REMINDER_DATE}}", $data->issue_date, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_NAME}}", $data->equipment_name, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_TYPE_NAME}}", $data->equipment_type_name, $cardHtml);
                    $cardHtml = str_replace("{{EQUIPMENT_REGISTRATION}}", $data->equipment_registration, $cardHtml);
                    $cardHtml = str_replace("{{STORE_LOCATION_NAME}}", $data->store_location_name, $cardHtml);
                    // Append to main HTML
                    $html .= $cardHtml;
                }
                print_r($html);
                die;
            } elseif ($maintenanceType == 2) {
            $table_data = [];

            // Fetch all equipments with maintenance settings and asset items
            $equipments = $this->db->select('
                    equipments_asset.*,
                    asset_types.name AS equipment_type_name,
                    store_location.name AS store_location_name,
                    GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") SEPARATOR ", ") AS asset_items
                ')
                ->from('equipments_asset')
                ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
                ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
                ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
                ->where('equipments_asset.maintenance_date IS NOT NULL', null, false)
                ->where('equipments_asset.frequency_year IS NOT NULL', null, false)
                ->where('equipments_asset.maintenance_reminder_day IS NOT NULL', null, false)
                ->group_by('equipments_asset.equipment_id')
                ->get()
                ->result();

            foreach ($equipments as $equipment) {
                // Calculate next maintenance date
                $frequency_per_year = !empty($equipment->frequency_year) ? (int)$equipment->frequency_year : 2;
                $interval_days = round(365 / $frequency_per_year);

                // Get the latest maintenance date
                $latest = $this->db->select('update_date')
                    ->from('equipment_maintenance_asset')
                    ->where('equipment_id', $equipment->equipment_id)
                    ->order_by('created_at', 'DESC')
                    ->limit(1)
                    ->get()
                    ->row();

                if ($latest && !empty($latest->update_date)) {
                    try {
                        $last_date = new DateTime($latest->update_date);
                        $next_date = clone $last_date;
                        $next_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        continue;
                    }
                } else {
                    try {
                        $maintenance_date = new DateTime($equipment->maintenance_date);
                        $next_date = clone $maintenance_date;
                        $next_date->modify("+{$interval_days} days");
                    } catch (Exception $e) {
                        continue;
                    }
                }

                // Check if the next date falls within the requested month/year
                $next_year = $next_date->format('Y');
                $next_month = $next_date->format('m');
                if ($next_year != $year || $next_month != str_pad($month, 2, "0", STR_PAD_LEFT)) {
                    continue; // not in this month
                }

                // Fetch the actual maintenance record for this exact date
                $upcoming = $this->db->select('equipment_maintenance_id, final_status, update_date')
                    ->from('equipment_maintenance_asset')
                    ->where('equipment_id', $equipment->equipment_id)
                    ->where('DATE(update_date)', $next_date->format('Y-m-d'))
                    ->get()
                    ->row();

                $status = 'PENDING';
                $maintenance_id = null;

                if ($upcoming) {
                    $maintenance_id = $upcoming->equipment_maintenance_id;
                    $taskStatus = $this->getTaskBasedStatus($equipment->equipment_id, $maintenance_id, 'PENDING');
                    $status = ($taskStatus == 'complete') ? 'complete' : 'PENDING';
                } else {
                    $status = 'PENDING';
                    $maintenance_id = null;
                }

                // Prepare asset items
                $itemArray = [];
                if (!empty($equipment->asset_items)) {
                    $items = explode(", ", $equipment->asset_items);
                    foreach ($items as $item) {
                        preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches);
                        $itemArray[] = [
                            "item_name"         => $matches[1] ?? $item,
                            "manufacturer_name" => $matches[2] ?? "No Manufacturer",
                        ];
                    }
                }

                // Build card data
                $table_data[] = (object)[
                    "equipment_id"              => $equipment->equipment_id,
                    "equipment_maintenance_id"  => $maintenance_id,
                    "equipment_name"            => $equipment->equipment_name,
                    "interval_number"           => "Next Maintenance",
                    "interval"                  => $next_date->format("Y-m-d"),
                    "interval_start_date"       => null,
                    "interval_end_date"         => $next_date->format("Y-m-d"),
                    "equipment_type_name"       => $equipment->equipment_type_name,
                    "equipment_registration"    => $equipment->equipment_registration,
                    "store_location_name"       => $equipment->store_location_name,
                    "items"                     => $itemArray,
                    "maintenance_records"       => $latest->update_date ?? "No Data",
                    "remarks"                   => ($status == 'complete') ? "Completed" : "Pending",
                    "final_status"              => $status,
                ];
            }

            // ========== GENERATE HTML CARDS (same as getDateOrders) ==========
            $html = "";
            foreach ($table_data as $data) {
                // Build item table
                $itemDetails = "";
                if (!empty($data->items)) {
                    $itemDetails .= "<table class='table-bordered item-table'>
                                        <thead><tr><th>#</th><th>Item Name</th><th>Manufacturer Name</th></tr></thead><tbody>";
                    $count = 1;
                    foreach ($data->items as $item) {
                        $itemName = $item["item_name"] ?? "No Name";
                        $manufacturerName = $item["manufacturer_name"] ?? "No Manufacturer";
                        $itemDetails .= "<tr><td>{$count}</td><td>{$itemName}</td><td>{$manufacturerName}</td></tr>";
                        $count++;
                    }
                    $itemDetails .= "</tbody></table>";
                } else {
                    $itemDetails = "<p>No Items Found</p>";
                }

                // Determine CSS class and status text
                if ($data->final_status === "PENDING") {
                    $class = "planned";
                    $statusText = "PENDING";
                } elseif ($data->final_status === "complete") {
                    $class = "completed";
                    $statusText = "Completed";
                } else {
                    $class = "progresss";
                    $statusText = "In-Maintenance";
                }

                // Replan button (only for PENDING)
                $replanButtonHtml = ($data->final_status === "PENDING")
                    ? "<div class='right replan-button-container replan'>
                        <button type='button' class='float-right btn btn-primary btn-sm open-replan-modal'
                            data-bs-toggle='modal' data-bs-target='#replanModal'
                            data-equipment-id='" . htmlspecialchars($data->equipment_id) . "'
                            data-current-due-date='" . htmlspecialchars($data->interval_end_date) . "'>
                            <i class='fas fa-edit'></i>
                        </button>
                    </div>"
                    : "";

                // Card HTML
                $cardHtml = "
                    <div class='card schedule-card {$class} mb-3'>
                        <div class='card-body'>
                            <div class='header'>
                                <div class='left'>
                                    <div class='dots'>
                                        <span class='dot'></span><span class='dot'></span><span class='dot'></span>
                                    </div>
                                </div>
                                <div class='right'>
                                    <p>Status</p>
                                    <span class='status'>{$statusText}</span>
                                </div>
                            </div>
                            <div class='content'>
                                <div class='right'>
                                    {$replanButtonHtml}
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Due Date</div>
                                    <div class='value reminder-date'>{$data->interval_end_date}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Asset Name</div>
                                    <div class='value equipment-name'>{$data->equipment_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Equipment Type</div>
                                    <div class='value equipment-type-name'>{$data->equipment_type_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Equipment Registration</div>
                                    <div class='value equipment-registration'>{$data->equipment_registration}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Store Location</div>
                                    <div class='value store-location-name'>{$data->store_location_name}</div>
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Items</div>
                                    {$itemDetails}
                                </div>
                                <div class='wrapper'>
                                    <div class='key'>Actions</div>
                                    <div class='value'>
                                        " . (!empty($data->equipment_maintenance_id) ? "
                                        <a href='" . site_url('Assets_Item_maintenance/task_details/'.$data->equipment_id.'/'.$data->equipment_maintenance_id) . "'
                                            class='btn btn-info btn-sm details-btn'>
                                            <i class='fas fa-list'></i> Details
                                        </a>" : "<span>No Actions</span>") . "
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                ";
                $html .= $cardHtml;
            }

            print_r($html);
            die;
        }
    }
}

    public function replan()
    {
        if ($this->input->post('equipment_id')) {
            $id = $this->input->post('equipment_id');


            $this->db->set('maintenance_date', $this->input->post('new_next_maintenance_date'));
            $this->db->where("equipment_id", intval($id));
            $this->db->update('next_maintenance_date');

            redirect('Assets_Item_maintenance/?filter=preventive' . '&message=Maintenance rplaned');
        }
    }



    // task wise asset Maintence

public function task_details($equipment_id, $maintenance_id = null)
{
    if (!$this->user_model->logged_in()) {
        die(redirect('/order_summary?error=No permission to view this content.'));
    }

    try {
        // Equipment details fetch karo - equipments_asset table se
        $equipment = $this->db->select('equipments_asset.*, asset_types.name as equipment_type_name, store_location.name as store_location_name')
            ->from('equipments_asset')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
            ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
            ->where('equipments_asset.equipment_id', $equipment_id)
            ->get()
            ->row();

        if (!$equipment) {
            show_error('Equipment not found with ID: ' . $equipment_id);
        }

        // âœ… STEP 1: Equipment Type se Task List get karo
        $task_lists = $this->db->select('task_list.id, task_list.name, task_list.frequency_in_days')
            ->from('asset_type_tasks')
            ->join('task_list', 'task_list.id = asset_type_tasks.task_list_id')
            ->where('asset_type_tasks.asset_type_id', $equipment->equipment_type)
            ->get()
            ->result();

        error_log("ðŸ”§ Equipment Type: " . $equipment->equipment_type);
        error_log("ðŸ“Š Tasks Found: " . count($task_lists));

        // âœ… STEP 2: Existing maintenance tasks get karo (agar hain to)
        $existing_tasks = [];
        if ($maintenance_id) {
            $existing_tasks = $this->db->select('
                emt.*, 
                tl.name as task_name, 
                u.username, 
                u.full_name'
            )
                ->from('equipment_maintenance_tasks emt')
                ->join('task_list tl', 'tl.id = emt.task_list_id', 'left')
                ->join('users u', 'u.user_id = emt.user_id', 'left')
                ->where('emt.equipment_maintenance_id', $maintenance_id)
                ->where('emt.equipment_id', $equipment_id)
                ->get()
                ->result();
        }

        // âœ… USERS LIST - FOR EDIT MODAL ONLY
        $users = $this->db->select('user_id, username, full_name, email')
            ->from('users')
            ->get()
            ->result();

        // Load views
        $this->load->view('header', [
            'title' => 'Task Details - ' . $equipment->equipment_name,
            'title2' => 'Maintenance Task Details',
            'styles' => [
                'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css',
                'https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap4.min.css'
            ]
        ]);
        
        $this->load->view('maintenance_task_details', [
            'equipment' => $equipment,
            'task_lists' => $task_lists, // âœ… Task lists pass karo
            'existing_tasks' => $existing_tasks,
            'users' => $users,
            'maintenance_id' => $maintenance_id,
            'maintenance_details' => null
        ]);
        
        $this->load->view('footer', [
            'scripts' => [
                'https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js',
                'https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap4.min.js',
                'design/js/maintenance_tasks.js'
            ]
        ]);

    } catch (Exception $e) {
        show_error('Error loading task details: ' . $e->getMessage());
    }
}



public function get_tasks_ajax()
{
    $equipment_id = $this->input->post('equipment_id');
    $maintenance_id = $this->input->post('maintenance_id');

    error_log("ðŸŽ¯ GET_TASKS_AJAX - Equipment: $equipment_id, Maintenance: $maintenance_id");

    try {
        $this->db->select('DISTINCT
            tl.id as task_list_id,
            tl.name as task_name,
            emt.id as emt_id,
            emt.cost,
            emt.user_id,
            emt.file_path,
            emt.status,
            u.full_name,
            u.username
        ', FALSE);
        $this->db->from('asset_type_tasks atl');
        $this->db->join('task_list tl', 'tl.id = atl.task_list_id', 'left');
        $this->db->join('equipment_maintenance_tasks emt',
            'emt.task_list_id = atl.task_list_id 
             AND emt.equipment_id = '.$this->db->escape($equipment_id).' 
             AND emt.equipment_maintenance_id = '.$this->db->escape($maintenance_id),
            'left');
        $this->db->join('users u', 'u.user_id = emt.user_id', 'left');
        $this->db->where('atl.asset_type_id', "(SELECT asset_type_id FROM equipments_asset WHERE equipment_id = ".$this->db->escape($equipment_id)." LIMIT 1)", FALSE);

        $tasks = $this->db->get()->result();

        error_log("ðŸ“Š Found tasks: " . count($tasks));

        $data = [];
        foreach ($tasks as $task) {
            // User
            $assigned_user = (!empty($task->full_name)) 
                ? $task->full_name . ' (' . $task->username . ')' 
                : '--';

            // Cost
            $cost_display = (!empty($task->cost)) 
                ? "â‚¹" . number_format(floatval($task->cost), 2) 
                : '--';

            // File
            $file_link = (!empty($task->file_path)) 
                ? '<a href="' . base_url($task->file_path) . '" target="_blank" class="btn btn-sm btn-outline-primary">View File</a>' 
                : '--';

            // Status
            $status_badge = !empty($task->status) 
                ? $this->getStatusBadge($task->status) 
                : '<span class="badge badge-secondary">Not Started</span>';

            // âœ… ðŸŽ¯ FIXED: HAR TASK KE LIYE EDIT BUTTON SHOW KAREN - CHAHE EMT_ID HO YA NA HO
            $task_data = [
                'id' => $task->emt_id ?: 'new', // âœ… Agar emt_id nahi hai to 'new' set karen
                'task_list_id' => $task->task_list_id,
                'cost' => floatval($task->cost) ?: 0,
                'user_id' => $task->user_id ?: '',
                'file_path' => $task->file_path ?: '',
                'status' => $task->status ?: 'pending'
            ];
            $task_data_json = htmlspecialchars(json_encode($task_data), ENT_QUOTES, 'UTF-8');

            // âœ… HAR ROW KE LIYE EDIT BUTTON
            $actions = '
                <button class="btn btn-sm btn-warning edit-task"
                        data-id="' . ($task->emt_id ?: 'new') . '"
                        data-task=\'' . $task_data_json . '\'>
                    <i class="fas fa-edit"></i> Edit
                </button>';

            $data[] = [
                "task_name"     => $task->task_name ?: 'N/A',
                "assigned_user" => $assigned_user,
                "cost"          => $cost_display,
                "file"          => $file_link,
                "status"        => $status_badge,
                "actions"       => $actions // âœ… Ab har row mein edit button hoga
            ];
        }

        echo json_encode([
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        ]);

    } catch (Exception $e) {
        error_log("ðŸ’¥ GET_TASKS_AJAX ERROR: " . $e->getMessage());
        echo json_encode([
            "draw" => intval($this->input->post('draw')),
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => [],
            "error" => $e->getMessage()
        ]);
    }
}


// âœ… Helper function for status badges
private function getStatusBadge($status)
{
    switch ($status) {
        case 'pending':
            return '<span class="badge badge-warning">Pending</span>';
        case 'in_progress':
            return '<span class="badge badge-info">In Progress</span>';
        case 'completed':
            return '<span class="badge badge-success">Completed</span>';
        default:
            return '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }
}

// Delete single task
public function delete_task()
{
    if (!$this->user_model->logged_in() ) {
        die(json_encode(['success' => false, 'message' => 'Not Login']));
    }

    $task_id = $this->input->post('task_id');
    
    error_log("ðŸ—‘ï¸ Deleting task with ID: " . $task_id);
    
    try {
        // âœ… APNE ACTUAL PRIMARY KEY KE HISAB SE
        // Pehle check karein kya primary key 'id' hai ya 'equipment_maintenance_task_id'
        $table_fields = $this->db->list_fields('equipment_maintenance_tasks');
        error_log("ðŸ“Š Equipment Maintenance Tasks Table Fields: " . print_r($table_fields, true));
        
        if (in_array('id', $table_fields)) {
            $this->db->where('id', $task_id);
        } elseif (in_array('equipment_maintenance_task_id', $table_fields)) {
            $this->db->where('equipment_maintenance_task_id', $task_id);
        } else {
            throw new Exception('Primary key not found in table');
        }
        
        $deleted = $this->db->delete('equipment_maintenance_tasks');
        
        if ($deleted) {
            error_log("âœ… Task deleted successfully");
            echo json_encode(['success' => true, 'message' => 'Task deleted successfully']);
        } else {
            $db_error = $this->db->error();
            error_log("âŒ Failed to delete task: " . print_r($db_error, true));
            echo json_encode(['success' => false, 'message' => 'Failed to delete task: ' . $db_error['message']]);
        }
        
    } catch (Exception $e) {
        error_log("ðŸ’¥ Delete task error: " . $e->getMessage());
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}

public function update_task()
{
    // var_dump('kachuporiya');
    // exit();
    if (!$this->user_model->logged_in()) {
        echo json_encode(['success' => false, 'message' => 'Login required']);
        return;
    }

    error_log("ðŸŽ¯ =========== UPDATE_TASK FUNCTION STARTED ===========");

    $task_id = $this->input->post('task_id');
    $equipment_id = $this->input->post('equipment_id');
    $maintenance_id = $this->input->post('maintenance_id');
    $task_list_id = $this->input->post('task_list_id');
    $cost = $this->input->post('cost');
    $user_id = $this->input->post('user_id');
    $status = $this->input->post('status');

    try {
        // âœ… VALIDATE REQUIRED FIELDS
        if (empty($task_list_id)) {
            throw new Exception('Task List ID is required');
        }

        if (empty($equipment_id)) {
            throw new Exception('Equipment ID is required');
        }

        // âœ… AGAR MAINTENANCE_ID NULL HAI TO NAYI MAINTENANCE RECORD CREATE KAREIN
        if (empty($maintenance_id) || $maintenance_id == 'NULL' || $maintenance_id == '0') {
            error_log("ðŸ†• Creating NEW maintenance record...");
            
            $maintenance_data = [
                'equipment_id' => $equipment_id,
                'maintenance_type_id' => 'preventive',
                'final_status' => 'pending',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            
            $this->db->insert('equipment_maintenance_asset', $maintenance_data);        
            $maintenance_id = $this->db->insert_id();
            
            // âœ… INSERT ALL TASKS FOR THIS MAINTENANCE
            $this->insertMaintenanceTasks($equipment_id, $maintenance_id);
            
            error_log("ðŸ†” New Maintenance Record Created with ID: " . $maintenance_id);
        }

        // âœ… PREPARE UPDATE DATA
        $update_data = [
            'equipment_maintenance_id' => $maintenance_id,
            'equipment_id' => $equipment_id,
            'task_list_id' => $task_list_id,
            'cost' => !empty($cost) ? floatval($cost) : 0.00,
            'user_id' => !empty($user_id) ? $user_id : null,
            'status' => !empty($status) ? $status : 'pending',
            'updated_at' => date('Y-m-d H:i:s')
        ];

        // âœ… FILE UPLOAD HANDLING
        if (!empty($_FILES['file']['name']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
            $config['upload_path'] = './uploads/maintenance_tasks/';
            $config['allowed_types'] = 'jpg|jpeg|png|pdf|doc|docx';
            $config['max_size'] = 2048;
            $config['encrypt_name'] = true;

            if (!is_dir($config['upload_path'])) {
                mkdir($config['upload_path'], 0777, true);
            }

            $this->load->library('upload', $config);
            
            if ($this->upload->do_upload('file')) {
                $upload_data = $this->upload->data();
                $update_data['file_path'] = 'uploads/maintenance_tasks/' . $upload_data['file_name'];
            }
        }

        // âœ… TASK UPDATE/INSERT
        if ($task_id == 'new') {
            $update_data['created_at'] = date('Y-m-d H:i:s');
            $updated = $this->db->insert('equipment_maintenance_tasks', $update_data);
            $message = 'Task created successfully!';
        } else {
            $this->db->where('equipment_maintenance_id', $maintenance_id);
            $this->db->where('equipment_id', $equipment_id);
            $this->db->where('task_list_id', $task_list_id);
            $updated = $this->db->update('equipment_maintenance_tasks', $update_data);
            $message = 'Task updated successfully!';
        }

        if ($updated) {
            
            // âœ… ðŸŽ¯ YAHAN NAYA LOGIC ADD KAREN - CHECK ALL TASKS COMPLETE
            if ($this->checkAllTasksComplete($equipment_id, $maintenance_id)) {
                error_log("âœ… All tasks completed for maintenance ID: " . $maintenance_id);
                
                // âœ… STEP 1: equipment_maintenance_asset mein final_status ko "complete" karo
                // equipment_maintenance_asset table ka primary key equipment_maintenance_id hai
                $this->db->where('equipment_maintenance_id', $maintenance_id);
                $this->db->update('equipment_maintenance_asset', [
                    'final_status' => 'complete',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
                
                error_log("âœ… Updated equipment_maintenance_asset final_status to complete");
                
                // âœ… STEP 2: Next maintenance date calculate karo aur update karo
                $this->updateNextMaintenanceDate($equipment_id, $maintenance_id);
                
            } else {
                error_log("â³ Not all tasks completed yet for maintenance ID: " . $maintenance_id);

                $this->db->where('equipment_maintenance_id', $maintenance_id);
                $this->db->update('equipment_maintenance_asset', [
                    'final_status' => 'pending',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'maintenance_id' => $maintenance_id
            ]);
        } else {
            $db_error = $this->db->error();
            throw new Exception('Database operation failed: ' . $db_error['message']);
        }

    } catch (Exception $e) {
        error_log("ðŸ’¥ UPDATE_TASK ERROR: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage()
        ]);
    }
}

private function checkAllTasksComplete($equipment_id, $maintenance_id)
{
    // Count total tasks for this maintenance
    $total = $this->db->where('equipment_id', $equipment_id)
        ->where('equipment_maintenance_id', $maintenance_id)
        ->count_all_results('equipment_maintenance_tasks');

    // Agar koi task hi nahi hai, toh hum ise complete nahi maan sakte (aap chahe toh return true kar sakte hain)
    if ($total == 0) {
        return false;
    }

    // Count completed tasks
    $completed = $this->db->where('equipment_id', $equipment_id)
        ->where('equipment_maintenance_id', $maintenance_id)
        ->where('status', 'completed')
        ->count_all_results('equipment_maintenance_tasks');

    // Sab tasks complete hain tabhi true
    return ($total == $completed);
}

private function updateNextMaintenanceDate($equipment_id, $maintenance_id)
{
    try {
        $this->db->where('equipment_maintenance_id', $maintenance_id);
        $maintenance_details = $this->db->select('*')
            ->from('equipment_maintenance_asset')
            ->get()
            ->row();
            
        if (!$maintenance_details) {
            error_log("âŒ Maintenance details not found for ID: " . $maintenance_id);
            return false;
        }
        
        if ($maintenance_details->maintenance_type_id == 'preventive') {
            $equipment = $this->db->select('frequency_year')
                ->from('equipments_asset')
                ->where('equipment_id', $equipment_id)
                ->get()
                ->row();

            if (!$equipment || empty($equipment->frequency_year)) {
                error_log("âš ï¸ frequency_year not found for equipment_id: " . $equipment_id);
                return false;
            }

            $frequency_year = (int) $equipment->frequency_year;
            $interval_duration_days = round(365.25 / $frequency_year);
            
            $current_date = $maintenance_details->update_date ?: date('Y-m-d H:i:s');
            $dateObject = DateTime::createFromFormat('Y-m-d H:i:s', $current_date);
            
            if ($dateObject) {
                $nextDateObject = clone $dateObject;
                $next_maintenance_date = $nextDateObject->modify("+$interval_duration_days days")->format('Y-m-d');
                
                // âœ… ONLY update next_maintenance_date table â€“ NO new record insert
                $this->db->set('equipment_id', $equipment_id);
                $this->db->set('maintenance_date', $next_maintenance_date);
                $this->db->where("equipment_id", intval($equipment_id));
                $this->db->update('next_maintenance_date');
                
                error_log("âœ… Updated next_maintenance_date to: " . $next_maintenance_date);

                // âŒ REMOVE the insert block below (commented out)
                /*
                $next_maintenance_data = [
                    'equipment_id'          => $equipment_id,
                    'update_date'           => $next_maintenance_date,
                    'created_at'            => date('Y-m-d H:i:s'),
                    'updated_at'            => date('Y-m-d H:i:s'),
                    'maintenance_type_id'   => 'preventive',
                    'final_status'          => 'pending',
                ];
                $this->db->insert('equipment_maintenance_asset', $next_maintenance_data);
                */
            }
        } else {
            error_log("â„¹ï¸ Maintenance type is not preventive, skipping next maintenance date calculation");
        }
        
        return true;
        
    } catch (Exception $e) {
        error_log("ðŸ’¥ Error in updateNextMaintenanceDate: " . $e->getMessage());
        return false;
    }
}

private function insertMaintenanceTasks($equipment_id, $maintenance_id)
{
    // Get equipment type
    $equipment = $this->db->select('equipment_type')
        ->from('equipments_asset')
        ->where('equipment_id', $equipment_id)
        ->get()
        ->row();

    if (!$equipment) {
        return false;
    }

    // Check if tasks already exist for this maintenance
    $existing = $this->db->where('equipment_maintenance_id', $maintenance_id)
        ->get('equipment_maintenance_tasks')
        ->num_rows();
    if ($existing > 0) {
        return true; // already inserted
    }

    // Fetch all task_list IDs for this asset type
    $tasks = $this->db->select('task_list_id')
        ->from('asset_type_tasks')
        ->where('asset_type_id', $equipment->equipment_type)
        ->get()
        ->result();

    if (empty($tasks)) {
        // No tasks defined â€“ nothing to insert
        return true;
    }

    // Insert each task as pending
    foreach ($tasks as $task) {
        $this->db->insert('equipment_maintenance_tasks', [
            'equipment_maintenance_id' => $maintenance_id,
            'equipment_id'             => $equipment_id,
            'task_list_id'             => $task->task_list_id,
            'status'                   => 'pending',
            'created_at'               => date('Y-m-d H:i:s'),
            'updated_at'               => date('Y-m-d H:i:s')
        ]);
    }

    return true;
}

}

