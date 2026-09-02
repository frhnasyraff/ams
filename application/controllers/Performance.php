<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Performance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect("/dashboard?error=No permission to view this content."));
        }
    }

    public function index()
    {

        $orderType = $this->input->get('order'); // This fetches the 'order' query parameter
        $config['base_url'] = site_url('Performance/index?order=' . $orderType);

        $totalAssets = $this->db->count_all('equipments_asset');
        $totalComponents = $this->db->count_all('add_asset_items');


        // Load Views
        $this->load->view('header', ['title' => "Performance Overview", 'title2' => "Performance Overview", "styles" => [
            "design/css/performance.css",
            "design/css/custom-datatable.css",
        ]]);

        $this->load->view('performance', [
            'assets_summary_count' => $totalAssets,
            'components_summary_count' => $totalComponents,
            // 'orders' => $orders,
            // 'adhoc_orders_count' => $adhoc_orders_count,
            // 'longterm_orders_count' => $longterm_orders_count
        ]);
        $this->load->view('footer', ['scripts' => [
            'https://cdn.jsdelivr.net/npm/chart.js@4.0.1/dist/chart.umd.min.js',
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels',
            'design/js/performance.js?v=12',
        ]]);
    }

    // public function getOrdersCount()
    // {
    //     $year = date('Y');
    //     if (!empty($_GET['year'])) {
    //         $year = $_GET['year'];
    //     }

    //     $orders_query = $this->db->select('orders.*, equipments.*, companies.company_name, company_addresses.branch_office_id, service_types.service_type_name, workers.worker_id, workers.worker_name')
    //         ->from('orders')
    //         ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
    //         ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
    //         ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
    //         ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
    //         ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
    //         ->join('equipments', 'equipments.equipment_id=order_drivers.truck_id', 'LEFT')
    //         ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id', 'LEFT')
    //         ->join('asset_types', 'order_equipment_bin_qr_codes.asset_type_id = asset_types.asset_id', 'LEFT');

    //     $currentTab = null;
    //     $is_filter_active = false;

    //     // get specific selected orders based on status
    //     if ((empty($_GET['order']) || $_GET['order'] == 'adhoc')) {
    //         $orders_query = $orders_query->where('orders.order_type', 1);
    //         $currentTab = 'adhoc';
    //     } elseif (isset($_GET['order']) && $_GET['order'] == 'longterm') {
    //         $orders_query = $orders_query->where('orders.order_type', 2);
    //         $currentTab = 'longterm';
    //     }

    //     if ($_GET['order'] == 'longterm') {
    //         if (!empty($this->input->post('order_type'))) {
    //             $orders_query->where('orders.billing_type', $this->input->post('order_type'));
    //             $is_filter_active = true;
    //         }
    //     }

    //     if (isset($year)) {
    //         $orders_query->where('YEAR(orders.start_date)', $year);
    //         $is_filter_active = true;
    //     } else {
    //         $orders_query->where('YEAR(orders.start_date)', 2025);
    //     }

    //     // Add asset_type filter
    //     if (isset($_GET['asset_type']) && $_GET['asset_type'] != '') {
    //         $orders_query->where('order_equipment_bin_qr_codes.asset_type_id', $_GET['asset_type']);
    //         $is_filter_active = true;
    //     }

    //     $orders_query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());

    //     $orders_query->where('orders.status', 3)->order_by('order_id', 'desc')
    //         ->group_by('orders.order_id');

    //     $result = $orders_query->get()->result_object();

    //     echo (count($result));
    // }
    // Function to get the total count after filters are applied
    // public function getFilteredOrdersCount($year = '2025')
    // {
    //     // var_dump("");
    //     $orders_query = $this->db->select('COUNT(DISTINCT `orders`.`order_id`) as order_count')
    //         ->from('orders')
    //         ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
    //         ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
    //         ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
    //         ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
    //         ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
    //         ->join('equipments', 'equipments.equipment_id=order_drivers.truck_id', 'LEFT');

    //     $currentTab = null;

    //     // get specific selected orders based on status
    //     if ((empty($_GET['order']) || $_GET['order'] == 'adhoc')) {
    //         $orders_query = $orders_query->where('orders.order_type', 1);
    //         $currentTab = 'adhoc';
    //     } elseif (isset($_GET['order']) && $_GET['order'] == 'longterm') {
    //         $orders_query = $orders_query->where('orders.order_type', 2);
    //         $currentTab = 'longterm';
    //     }

    //     if ($_GET['order'] == 'longterm') {
    //         if (!empty($this->input->post('order_type'))) {
    //             $orders_query->where('orders.billing_type', $this->input->post('order_type'));
    //             $is_filter_active = true;
    //         }
    //     }

    //     if (isset($year)) {
    //         $orders_query->where('YEAR(orders.start_date)', $year);
    //         $is_filter_active = true;
    //     } else {
    //         $orders_query->where('YEAR(orders.start_date)', 2025);
    //     }

    //     if (isset($_GET['start_date'])) {
    //         $orders_query->where('(orders.start_date) >=', date('Y-m-d', strtotime($_GET['start_date'])));
    //         $is_filter_active = true;
    //     }
    //     if (isset($_GET['end_date'])) {
    //         $orders_query->where('(orders.start_date) <=', date('Y-m-d', strtotime($_GET['end_date'])));
    //         $is_filter_active = true;
    //     }

    //     $orders_query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());

    //     // search text
    //     $search_array = explode(',', $_GET['search']);
    //     $orders_query->group_start();
    //     foreach ($search_array as $search) {
    //         $orders_query->or_like('workers.worker_name', ltrim($search));
    //         $orders_query->or_like('companies.company_name', ltrim($search));
    //         $orders_query->or_like('equipments.equipment_name', ltrim($search));
    //         $orders_query->or_like('equipments.equipment_registration', ltrim($search));
    //         $orders_query->or_like('orders.order_num', ltrim($search));
    //         $orders_query->or_like('service_types.service_type_name', ltrim($search));
    //     }
    //     $orders_query->group_end();

    //     $orders_query->where('orders.status', 3);

    //     $result = $orders_query->get()->result_object();

    //     // Apply filters to count query if needed
    //     // return count($result);
    //     return $result[0]->order_count;
    // }

    // Function to get filtered and paginated orders
    // private function getFilteredAndPaginatedOrders($page)
    // {

    //     $orders_query = $this->buildOrdersQuery();
    //     $orders = $orders_query
    //         ->limit(10, $page)
    //         ->order_by('start_date', 'desc')
    //         // ->group_by('orders.order_id')
    //         ->get()
    //         ->result_object();

    //     return $orders;
    // }

    // Function to get the count of orders based on order type
    // private function getOrdersCountByType($orderType)
    // {
    //     $orders_query = $this->db->select('COUNT(DISTINCT `orders`.`order_id`) as order_count')
    //         ->from('orders')
    //         ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
    //         ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
    //         ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
    //         ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
    //         ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
    //         ->join('equipments', 'equipments.equipment_id=order_drivers.truck_id', 'LEFT')
    //         ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id', 'LEFT')
    //         ->join('asset_types', 'order_equipment_bin_qr_codes.asset_type_id = asset_types.asset_id', 'LEFT');

    //     $currentTab = null;
    //     $is_filter_active = false;

    //     // get specific selected orders based on status
    //     if ($orderType == 1) {
    //         $orders_query = $orders_query->where('orders.order_type', 1);
    //         $currentTab = 'adhoc';
    //     } elseif ($orderType == 2) {
    //         $orders_query = $orders_query->where('orders.order_type', 2);
    //         $currentTab = 'longterm';
    //     }

    //     if ($orderType == 2) {
    //         if (!empty($this->input->post('order_type'))) {
    //             $orders_query->where('orders.billing_type', $this->input->post('order_type'));
    //             $is_filter_active = true;
    //         }
    //     }

    //     if (isset($year)) {
    //         $orders_query->where('YEAR(orders.start_date)', $year);
    //         $is_filter_active = true;
    //     } else {
    //         $orders_query->where('YEAR(orders.start_date)', 2025);
    //     }

    //     // Add asset_type filter
    //     if (!empty($this->input->post('asset_type'))) {
    //         $orders_query->where('order_equipment_bin_qr_codes.asset_type_id', $this->input->post('asset_type'));
    //         $is_filter_active = true;
    //     }

    //     $orders_query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());

    //     $orders_query->where('orders.status', 3);

    //     $result = $orders_query->get()->result_object();

    //     return $result[0]->order_count;
    // }

    // Common function to build the initial orders query
    // private function buildOrdersQuery()
    // {
    //     $orders_query = $this->db->select('orders.*, equipments.*, companies.company_name, company_addresses.branch_office_id, service_types.service_type_name, workers.worker_id, workers.worker_name')
    //         ->from('orders')
    //         ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
    //         ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
    //         ->join('service_types', 'service_types.service_type_id=orders.service_type_id', 'LEFT')
    //         ->join('order_drivers', 'order_drivers.order_id = orders.order_id', 'LEFT')
    //         ->join('workers', 'workers.worker_id = order_drivers.driver_id', 'LEFT')
    //         ->join('equipments', 'equipments.equipment_id=order_drivers.truck_id', 'LEFT')
    //         ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id', 'LEFT')
    //         ->join('asset_types', 'order_equipment_bin_qr_codes.asset_type_id = asset_types.asset_id', 'LEFT');

    //     $currentTab = null;

    //     // get specific selected orders based on status
    //     if ((empty($_GET['order']) || $_GET['order'] == 'adhoc')) {
    //         $orders_query = $orders_query->where('orders.order_type', 1);
    //         $currentTab = 'adhoc';
    //     } elseif (isset($_GET['order']) && $_GET['order'] == 'longterm') {
    //         $orders_query = $orders_query->where('orders.order_type', 2);
    //         $currentTab = 'longterm';
    //     }
    //     // search filters
    //     $is_filter_active = false;

    //     if ($_GET['order'] == 'longterm') {
    //         if (!empty($this->input->post('order_type'))) {
    //             $orders_query->where('orders.billing_type', $this->input->post('order_type'));
    //             $is_filter_active = true;
    //         }
    //     }
    //     if (!empty($this->input->post('start_date'))) {
    //         $orders_query->where('(orders.start_date) >=', date('Y-m-d', strtotime($this->input->post('start_date'))));
    //         $is_filter_active = true;
    //     }
    //     if (!empty($this->input->post('end_date'))) {
    //         $orders_query->where('(orders.start_date) <=', date('Y-m-d', strtotime($this->input->post('end_date'))));
    //         $is_filter_active = true;
    //     }

    //     // Add asset_type filter
    //     if (!empty($this->input->post('asset_type'))) {
    //         $orders_query->where('order_equipment_bin_qr_codes.asset_type_id', $this->input->post('asset_type'));
    //         $is_filter_active = true;
    //     }

    //     $orders_query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());

    //     $search_array = explode(',', $this->input->post('search'));
    //     $orders_query->group_start();
    //     foreach ($search_array as $search) {
    //         $orders_query->or_like('workers.worker_name', ltrim($search));
    //         $orders_query->or_like('companies.company_name', ltrim($search));
    //         $orders_query->or_like('equipments.equipment_name', ltrim($search));
    //         $orders_query->or_like('equipments.equipment_registration', ltrim($search));
    //         $orders_query->or_like('orders.order_num', ltrim($search));
    //         $orders_query->or_like('service_types.service_type_name', ltrim($search));
    //     }
    //     $orders_query->group_end();

    //     // sort based on columns select
    //     if (isset($_GET['sort']) && isset($_GET['sort'])) {
    //         $column = $_GET['column'];
    //         $sort = $_GET['sort'];

    //         if ($column == 'order_num') {
    //             $column = 'orders.order_num';
    //         } else if ($column == 'company_name') {
    //             $column = 'companies.company_name';
    //         } else if ($column == 'service_date') {
    //             $column = 'orders.start_date';
    //         } else if ($column == 'service_type') {
    //             $column = 'service_types.service_type_name';
    //         } else if ($column == 'driver') {
    //             $column = 'workers.worker_name';
    //         } else if ($column == 'truck') {
    //             $column = 'equipments.equipment_name';
    //         } else if ($column == 'start_time') {
    //             $column = 'orders.progress_at';
    //         } else if ($column == 'end_time') {
    //             $column = 'orders.completed_at';
    //         }
    //         $orders_query->where('orders.status', 3)->order_by($column, $sort)
    //             ->group_by('orders.order_id');
    //     } else {
    //         $orders_query->where('orders.status', 3)->order_by('order_id', 'desc')
    //             ->group_by('orders.order_id');
    //     }

    //     return $orders_query;
    // }

    // public function getOrderAssets()
    // {
    //     if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
    //         die('invaid request');
    //     }

    //     if ($this->input->get('orderId')) {

    //         $bin_qr_codes = $this->db->select('order_equipment_bin_qr_codes.order_equipment_bin_qr_codes_id, order_equipment_bin_qr_codes.asset_type_id, asset_types.name, order_equipment_bin_qr_codes.qr_code, COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_assets')
    //             ->from('orders')
    //             ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
    //             ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
    //             ->where('orders.order_id', $this->input->get('orderId'))
    //             ->order_by('order_equipment_bin_qr_codes.asset_type_id')
    //             ->group_by('order_equipment_bin_qr_codes.asset_type_id')
    //             ->get()
    //             ->result();

    //         $html = "<form class='form' action='' method='POST'>";
    //         foreach ($bin_qr_codes as $bin) {
    //             $html .= "<div class='input'>";
    //             $html .= "<div class='bin_qr_code'>";
    //             $html .= "<div class='qr_code'>";
    //             $html .= "<input type='text' class='form-control rounded-0 mb-2' value='{$bin->name} : {$bin->total_assets}' tabindex='-1' readonly>";
    //             $html .= "</div>";
    //             $html .= "</div>";
    //             $html .= "</div>";
    //         }
    //         $html .= '</form>';
    //         print_r($html);
    //         die;
    //     }
    // }


    public function getSummaryForAll()
    {
        $currentTab = null;
        $is_filter_active = false;

        // get specific selected orders based on status
        if ((empty($_GET['summary']) || $_GET['summary'] == 'assets')) {

            $currentTab = 'assets';
        } elseif (isset($_GET['summary']) && $_GET['summary'] == 'components') {

            $currentTab = 'components';
        }

        if ($currentTab == 'assets') {

            $year = $this->input->get('year');
            $month_param = $this->input->get('month');

            // Default year to current year if not provided
            if (empty($year)) {
                $year = date('Y');
            }

            // 1. Get the total number of assets. This count remains constant for the entire year's calculations.
            $total_assets = $this->db->select('COUNT(*) as total_count')
                ->from('equipments_asset')
                ->get()
                ->row()
                ->total_count;


            $chart_data = [];
            $start_month = !empty($month_param) ? (int)$month_param : 1;
            $end_month = !empty($month_param) ? (int)$month_param : date('n'); // Default to current month if no month filter

            // Adjust end_month based on the requested year
            if (!empty($month_param)) {
                $end_month = (int)$month_param;
            } else {
                // If no specific month is requested, determine the natural end month for the loop.
                if ($year < date('Y')) {
                    $end_month = 12; // For past years, show all 12 months
                } else if ($year == date('Y')) {
                    $end_month = date('n'); // For the current year, show up to the current month
                } else {
                    $end_month = 12; // For future years, show all 12 months (counts will likely be 0)
                }
            }

            // Loop through each month to calculate serviceability for that month
            for ($i = $start_month; $i <= $end_month; $i++) {

                $end_of_month_date = date('Y-m-t', strtotime("$year-$i-01"));

                $this->db->select('log_item_id, MAX(timestamp) as max_timestamp');
                $this->db->from('asset_logs');
                $this->db->where('log_code', 'Asset_Updated'); // Only consider status update logs
                $this->db->where("DATE(timestamp) <= '$end_of_month_date'"); // Logs up to end of current month
                $this->db->group_by('log_item_id');
                $subquery = $this->db->get_compiled_select(); // Get the SQL string for the subquery

                $this->db->select('COUNT(DISTINCT al_main.log_item_id) as serviceable_count');
                $this->db->from('asset_logs al_main');
                $this->db->join("($subquery) as latest_logs", 'al_main.log_item_id = latest_logs.log_item_id AND al_main.timestamp = latest_logs.max_timestamp', 'inner');

                $this->db->like('al_main.log_description', 'â†’ \'SERVICEABLE\'', 'both');

                // Ensure the main query also respects the current month's end date.
                $this->db->where("DATE(al_main.timestamp) <= '$end_of_month_date'");

                $result = $this->db->get()->row();
                $count = $result ? $result->serviceable_count : 0;

                // Calculate the percentage of serviceable assets for the current month.
                $percentage = $total_assets > 0 ? round(($count / $total_assets) * 100, 2) : 0;

                // Add the monthly data to the chart_data array
                $chart_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)), // Format month number to short name (e.g., 'Jan', 'Feb')
                    'percentage' => $percentage
                ];
            }

            // 4. Faulty corrective maintenance assets (per month)
            $faulty_data = [];

            // Loop through each month to calculate maintenance status for that month
            for ($i = $start_month; $i <= $end_month; $i++) {
                // Define the end date for the current month iteration (e.g., '2025-08-31')
                $end_of_month_date = date('Y-m-t', strtotime("$year-$i-01"));

                $this->db->select('log_item_id, MAX(timestamp) as max_timestamp');
                $this->db->from('asset_logs');
                $this->db->where('log_code', 'Asset_Updated'); // Only consider status update logs
                $this->db->where("DATE(timestamp) <= '$end_of_month_date'"); // Logs up to end of current month
                $this->db->group_by('log_item_id');
                $subquery = $this->db->get_compiled_select(); // Get the SQL string for the subquery

                $this->db->select('COUNT(DISTINCT al_main.log_item_id) as maintenance_count');
                $this->db->from('asset_logs al_main');
                $this->db->join("($subquery) as latest_logs", 'al_main.log_item_id = latest_logs.log_item_id AND al_main.timestamp = latest_logs.max_timestamp', 'inner');

                $this->db->like('al_main.log_description', 'â†’ \'MAINTENANCE\'', 'both');

                // Ensure the main query also respects the current month's end date.
                $this->db->where("DATE(al_main.timestamp) <= '$end_of_month_date'");

                $result = $this->db->get()->row();
                $count = $result ? $result->maintenance_count : 0;

                // Calculate the percentage of assets in maintenance for the current month.
                $percentage = $total_assets > 0 ? round(($count / $total_assets) * 100, 2) : 0;

                // Add the monthly data to the faulty_data array
                $faulty_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)), // Format month number to short name (e.g., 'Jan', 'Feb')
                    'percentage' => $percentage
                ];
            }

            // Loop through each month to calculate average repair time
            $repair_time_data = [];
            for ($i = $start_month; $i <= $end_month; $i++) {
                $this->db->select('t.ticket_number, t.issue_date, ema.update_date')
                    ->from('ticket t')
                    ->join('equipment_maintenance_asset ema', 't.ticket_number = ema.ticket_number', 'inner')
                    ->where('ema.final_status', 'complete')
                    ->where('YEAR(ema.update_date)', $year)
                    ->where('MONTH(ema.update_date)', $i);

                // Execute query
                $results = $this->db->get()->result_array();

                $total_duration_seconds = 0;
                $completed_tickets_count = 0;

                foreach ($results as $row) {
                    $issue_timestamp = strtotime(str_replace('/', '-', $row['issue_date']));
                    $complete_timestamp = strtotime($row['update_date']);

                    if ($issue_timestamp && $complete_timestamp && $complete_timestamp >= $issue_timestamp) {
                        $total_duration_seconds += ($complete_timestamp - $issue_timestamp);
                        $completed_tickets_count++;
                    }
                }

                $average_duration_seconds = $completed_tickets_count > 0
                    ? ($total_duration_seconds / $completed_tickets_count)
                    : 0;
                $days = floor($average_duration_seconds / 86400);
                $hours = round(($average_duration_seconds - ($days * 86400)) / 3600, 2);


                $repair_time_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)),
                    'days' => $days,
                    'hours' => $hours
                ];
            }



            // 6. Final response
            header('Content-Type: application/json');
            echo json_encode([
                'chart_data' => $chart_data,
                'faulty_data' => $faulty_data,
                'repair_time_data' => $repair_time_data
            ]);
        } elseif ($currentTab == 'components') {
            $year = $this->input->get('year');
            $month_param = $this->input->get('month');

            // Default year to current year if not provided
            if (empty($year)) {
                $year = date('Y');
            }

            // 1. Get the total number of components. This count remains constant for the entire year's calculations.
            $total_components = $this->db->select('COUNT(*) as total_count')
                ->from('add_asset_items')
                ->get()
                ->row()
                ->total_count;

            $component_chart_data = []; // For component serviceability percentage
            $component_faulty_data = []; // For component maintenance status percentage
            $component_repair_time_data = []; // For component average repair time

            $start_month = !empty($month_param) ? (int)$month_param : 1;
            $end_month = !empty($month_param) ? (int)$month_param : date('n'); // Default to current month if no month filter

            // Adjust end_month based on the requested year
            if (!empty($month_param)) {
                $end_month = (int)$month_param;
            } else {
                // If no specific month is requested, determine the natural end month for the loop.
                if ($year < date('Y')) {
                    $end_month = 12; // For past years, show all 12 months
                } else if ($year == date('Y')) {
                    $end_month = date('n'); // For the current year, show up to the current month
                } else {
                    $end_month = 12; // For future years, show all 12 months (counts will likely be 0)
                }
            }

            // Loop through each month to calculate all metrics for that month
            for ($i = $start_month; $i <= $end_month; $i++) {
                $end_of_month_date = date('Y-m-t', strtotime("$year-$i-01"));

                // --- Calculate Component Serviceability (component_chart_data) ---
                // Subquery: Find the MAX timestamp for each component's 'Component_Updated' log entry
                $this->db->select('log_item_id, MAX(timestamp) as max_timestamp');
                $this->db->from('asset_logs');
                $this->db->where('log_code', 'Component_Updated'); // Target component updates
                $this->db->where("DATE(timestamp) <= '$end_of_month_date'");
                $this->db->group_by('log_item_id');
                $subquery_serviceable_comp = $this->db->get_compiled_select();

                // Main Query: Join with subquery to get the latest status and count serviceable components
                $this->db->select('COUNT(DISTINCT al_main.log_item_id) as serviceable_count');
                $this->db->from('asset_logs al_main');
                $this->db->join("($subquery_serviceable_comp) as latest_logs", 'al_main.log_item_id = latest_logs.log_item_id AND al_main.timestamp = latest_logs.max_timestamp', 'inner');
                $this->db->like('al_main.log_description', 'â†’ \'SERVICEABLE\'', 'both');
                $this->db->where("DATE(al_main.timestamp) <= '$end_of_month_date'");
                $result_serviceable_comp = $this->db->get()->row();
                $serviceable_count_comp = $result_serviceable_comp ? $result_serviceable_comp->serviceable_count : 0;
                $serviceable_percentage_comp = $total_components > 0 ? round(($serviceable_count_comp / $total_components) * 100, 2) : 0;
                $component_chart_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)),
                    'percentage' => $serviceable_percentage_comp
                ];

                // --- Calculate Component Maintenance Status (component_faulty_data) ---
                // Subquery (can reuse similar structure as serviceable, but for 'MAINTENANCE')
                $this->db->select('log_item_id, MAX(timestamp) as max_timestamp');
                $this->db->from('asset_logs');
                $this->db->where('log_code', 'Component_Updated');
                $this->db->where("DATE(timestamp) <= '$end_of_month_date'");
                $this->db->group_by('log_item_id');
                $subquery_maintenance_comp = $this->db->get_compiled_select();

                // Main Query: Join with subquery to get the latest status and count components in 'MAINTENANCE'
                $this->db->select('COUNT(DISTINCT al_main.log_item_id) as maintenance_count');
                $this->db->from('asset_logs al_main');
                $this->db->join("($subquery_maintenance_comp) as latest_logs", 'al_main.log_item_id = latest_logs.log_item_id AND al_main.timestamp = latest_logs.max_timestamp', 'inner');
                $this->db->like('al_main.log_description', 'â†’ \'MAINTENANCE\'', 'both');
                $this->db->where("DATE(al_main.timestamp) <= '$end_of_month_date'");
                $result_maintenance_comp = $this->db->get()->row();
                $maintenance_count_comp = $result_maintenance_comp ? $result_maintenance_comp->maintenance_count : 0;
                $maintenance_percentage_comp = $total_components > 0 ? round(($maintenance_count_comp / $total_components) * 100, 2) : 0;
                $component_faulty_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)),
                    'percentage' => $maintenance_percentage_comp
                ];


                // NEW/CORRECTED QUERY for component repair time using item_ticket and logs_item_maintenance
                $this->db->select('it.id, it.issue_date, lim.update_date');
                $this->db->from('item_ticket it');
                $this->db->join('logs_item_maintenance lim', 'it.id = lim.item_ticket_id', 'inner'); // join via ID, not number
                $this->db->where('lim.final_status', 'COMPLETE');
                $this->db->where('YEAR(lim.update_date)', $year);
                $this->db->where('MONTH(lim.update_date)', $i);

                // Execute query once and store the result object
                $query = $this->db->get();

                // Get the results array from the $query object
                $results_repair_time_comp = $query->result_array(); // <-- Corrected line here

                $total_duration_seconds_comp = 0;
                $completed_repairs_count_comp = 0;
                foreach ($results_repair_time_comp as $row) {
                    // issue_date is varchar 'DD/MM/YYYY'
                    $issue_timestamp_comp = strtotime(str_replace('/', '-', $row['issue_date']));
                    // update_date is date 'YYYY-MM-DD'
                    $complete_timestamp_comp = strtotime($row['update_date']);

                    if ($issue_timestamp_comp !== false && $complete_timestamp_comp !== false && $complete_timestamp_comp >= $issue_timestamp_comp) {
                        $total_duration_seconds_comp += ($complete_timestamp_comp - $issue_timestamp_comp);
                        $completed_repairs_count_comp++;
                    }
                }
                $average_duration_seconds_comp = $completed_repairs_count_comp > 0
                    ? ($total_duration_seconds_comp / $completed_repairs_count_comp)
                    : 0;
                $days_comp = floor($average_duration_seconds_comp / 86400); // 86400 seconds in a day
                $hours_comp = round(($average_duration_seconds_comp - ($days_comp * 86400)) / 3600, 2); // 3600 seconds in an hour
                $component_repair_time_data[] = [
                    'month' => date('M', mktime(0, 0, 0, $i, 1)),
                    'days' => $days_comp,
                    'hours' => $hours_comp
                ];
            }

            // Final response
            header('Content-Type: application/json');
            echo json_encode([
                'component_chart_data' => $component_chart_data,
                'component_faulty_data' => $component_faulty_data,
                'component_repair_time_data' => $component_repair_time_data // CORRECTED TYPO HERE
            ]);
        }
    }
}



