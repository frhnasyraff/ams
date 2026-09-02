<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Bin_performance extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_assets")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => "Bin Performance", 'title2' => "BIn Performance", "styles" => [
            'design/css/performance-summary.css'
        ]]);

        (isset($_GET['offset']) && $_GET['offset'] > -1) ? $tableOffset = $_GET['offset'] : $tableOffset = 0;
        isset($_GET['limit']) ? $limit = $_GET['limit'] : $limit = 10;

        $query = $this->db->select('orders.order_id, orders.start_date, service_types.service_type_name,
        companies.company_id,
        companies.company_name,
        company_addresses.address_line_1,
        asset_types.asset_id, asset_types.name as asset_type_name, order_equipment_bin_qr_codes.created_at, 
        COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id');
        // ->where('service_types.service_type_name', 'Buy Bin')


        if (!isSuperAdmin()) {
            $query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());
        }

        if ($this->input->get('month') && !empty($this->input->get('month'))) {
            $query->where('monthname(orders.start_date)', $this->input->get('month'));
        }
        if ($this->input->get('year') && !empty($this->input->get('year'))) {
            $query->where('YEAR(orders.start_date)', $this->input->get('year'));
        }



        $currentTab = null;

        // get specific selected orders based on status
        if ($this->input->get('year')) {
            $query->where('YEAR(orders.start_date)', $this->input->get('year'));


            $currentTab = 'Year';
        }
        if ($this->input->get('month') && !empty($this->input->get('month'))) {
            $query->where('monthname(orders.start_date)', $this->input->get('month'));
            $currentTab = 'month';
        }

        $bins = $query
            ->group_by(['companies.company_id', 'orders.start_date'])
            ->limit($limit)
            ->offset($tableOffset)

            ->limit(1)->get()->result();


        $this->load->view('bin-performance', [
            'bins' => $bins
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/bin-performance.js'
        ]]);
    }



    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        // $performance_record = $this->input->post('record');
        $ids = $this->input->post('record');

        $bins = $this->db->select('orders.order_id, orders.start_date, service_types.service_type_name,
            companies.company_id,
            companies.company_name,
            company_addresses.address_line_1,
            asset_types.asset_id, asset_types.name as asset_type_name, order_equipment_bin_qr_codes.created_at, 
            COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
            ->where_in('orders.order_id', $ids);

        if (!isSuperAdmin()) {
            $bins->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());
        }


        $performance_record = $bins
            ->group_by(['companies.company_id', 'orders.start_date'])

            ->get()
            ->result();





        if ($download_type == 'pdf') {
            $this->downloadPDF($performance_record);
        } else if ($download_type == 'excel') {
            $this->downloadCsv($performance_record);
        }
    }

    public function downloadPDF($performance_record)
    {
        $this->load->library('pdf');
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
    
            th, td {
                text-align: left;
                padding: 8px;
                border: 1px solid #ddd;
                font-size: 14px
            }
    
            th {
                background-color: #f2f2f2;
            }
    
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            </style>
        </head>
        <body>
        <table>
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Asset Type</th>
                    <th>Service Date</th>
                    <th>Total Qty Sold</th>
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/asset_performance/index?error= No data to download."));
        }



        foreach ($performance_record as $record) {


            // $bin_performance = json_decode(urldecode($record), true);

            $html .= '<tr>';
            $html .= '<td>' . $record->company_name . '</td>';
            // $html .= '<td>' . $bin_performance['company_name'] . '</td>';
            $html .= '<td>' . $record->asset_type_name . '</td>';
            // $html .= '<td>' . $bin_performance['asset_type_name'] . '</td>';
            // $html .= '<td>' . $record->service_date . '</td>';
            $html .= '<td>' . $record->start_date . '</td>';
            // $html .= '<td>' . $bin_performance['service_date'] . '</td>';
            $html .= '<td>' .  $record->total_qr_codes . '</td>';
            // $html .= '<td>' .  $bin_performance['total_qr_codes'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'bin-performance-report.pdf');
    }

    function downloadCsv($performance_record)
    {

        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/truck_performance/index?error= No data to download."));
        }
        $file = fopen('php://output', 'w');
        fputcsv($file, [
            'Customer Name',
            'Asset Type',
            'Service Date',
            'Total Qty Sold'
        ]);

        foreach ($performance_record as $record) {
            // $bin_performance = json_decode(urldecode($record), true);

            fputcsv($file, [
                // $bin_performance['company_name'] ,
                // $bin_performance['asset_type_name'],
                // $bin_performance['service_date'] ,
                // $bin_performance['total_qr_codes']
                $record->company_name,
                $record->asset_type_name,
                $record->start_date,
                $record->total_qr_codes
            ]);
        }

        $name = 'bin-performance-report.csv';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        die;
    }


    public function get_bin_data()
    {
        $query = $this->db->select('orders.order_id, orders.start_date, service_types.service_type_name,
            companies.company_id,
            companies.company_name,
            company_addresses.address_line_1,
            asset_types.asset_id, asset_types.name as asset_type_name, order_equipment_bin_qr_codes.created_at, 
            COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id')
            ->join('service_types', 'service_types.service_type_id=orders.service_type_id')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id');

        if (!isSuperAdmin()) {
            $query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());
        }

        // if (isset($_GET['year'])) {
        //     $year = $_GET['year'];
        //     $is_filter_active = true;
        // }else{
        //     $year = 2024;
        // }
        // $query->where('YEAR(orders.start_date)', $year);

        if ($this->input->get('month') && !empty($this->input->get('month'))) {
            $query->where('monthname(orders.start_date)', $this->input->get('month'));
        } else {
            // Default to the year 2024 if year is not provided
            $currentMonth = date('F'); // Get current month name
            $query->where('monthname(orders.start_date)', $currentMonth);
        }

        if ($this->input->get('year') && !empty($this->input->get('year'))) {
            $query->where('YEAR(orders.start_date)', $this->input->get('year'));
        } else {
            // Default to the year 2024 if year is not provided
            $query->where('YEAR(orders.start_date)', 2024);
        }

        $bins = $query
            ->group_by(['companies.company_id', 'orders.start_date'])

            ->get()
            ->result();

        // var_dump($this->db->last_query());
        // exit();


        //    var_dump(json_encode([
        //     "data" => $bins
        //    ])); 
        die(json_encode([

            "data" => $bins
        ]));
    }

    public function get_bin_data_html()
    {
        if ($this->input->get('orderId')) {
            $bins = $this->db->select('companies.company_id, companies.company_name, orders.start_date, COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes')
                ->from('orders')
                ->join('companies', 'orders.company_id = companies.company_id')
                ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
                ->where('orders.order_id', $this->input->get('orderId'))
                ->group_by(['companies.company_id', 'orders.start_date'])
                ->get()
                ->result();



            $html = '';
            foreach ($bins as $bin) {
                $bin_qr_codes = $this->db->select('order_equipment_bin_qr_codes.reg_no')
                    ->from('order_equipment_bin_qr_codes')
                    ->join('orders', 'order_equipment_bin_qr_codes.order_id = orders.order_id')
                    ->join('companies', 'orders.company_id=companies.company_id')
                    ->where('companies.company_id', $bin->company_id)
                    ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
                    ->where('orders.start_date', $bin->start_date)
                    ->get()
                    ->result();




                foreach ($bin_qr_codes as $j => $b) {
                    $html .= "<div class='input'>
                                <label for=''> " . ($j + 1) . ") Asset Regno</label>
                                <div class='bin_qr_code'>
                                    <div class='qr_code'>
                                        <input type='text' class='field' value='" . $b->reg_no . "' readonly>
                                    </div>
                                </div>
                            </div>";
                }
            }

            print_r($html);
            die;
        }
    }
}
