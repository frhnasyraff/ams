<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_performance extends CI_Controller
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
        $this->load->view('header', ['title' => "Asset Performance", 'title2' => "Asset Performance", "styles" => [
            'design/css/performance-summary.css'
        ]]);

        $limit = $this->input->get('limit') ? intval($this->input->get('limit')) : 10;
        $offset = $this->input->get('offset') ? intval($this->input->get('offset')) : 0;

        $query = $this->db->select('orders.order_id, orders.start_date, companies.company_id, companies.company_name, company_addresses.company_address_id,
        company_addresses.address_line_1, 
        asset_types.asset_id, asset_types.name as asset_type_name, 
        COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes, order_equipment_bin_qr_codes.created_at')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id');


        if (!isSuperAdmin()) {
            $query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());
        }

        if ($this->input->get('date')) {
            $query->where('DATE(order_equipment_bin_qr_codes.created_at)', date('Y-m-d', strtotime($this->input->get('date'))));
        }
        if ($this->input->get('month') && !empty($this->input->get('month'))) {
            $query->where('monthname(order_equipment_bin_qr_codes.created_at)', $this->input->get('month'));
        }
        if ($this->input->get('year') && !empty($this->input->get('year'))) {
            $query->where('YEAR(order_equipment_bin_qr_codes.created_at)', $this->input->get('year'));
        }

        // sort based on columns selected
        if (isset($_GET['sort']) && isset($_GET['column'])) {
            $column = $_GET['column'];
            $sort = $_GET['sort'];

            if ($column == 'asset_type') {
                $column = 'asset_type_name';
            } else if ($column == 'total_qr_codes') {
                $column = 'total_qr_codes';
            } else if ($column == 'date_deployed') {
                $column = 'order_equipment_bin_qr_codes.created_at';
            } else if ($column == 'customer_name') {
                $column = 'companies.company_name';
            } else if ($column == 'customer_location') {
                $column = 'company_addresses.address_line_1';
            }

            $query->order_by($column, $sort);
        } else {
            $query->order_by('orders.company_address_id');
        }

        // Add limit and offset for pagination
        $query->limit($limit, $offset);

        // Execute query and get results
        $assets = $query->group_by('orders.company_address_id')->get()->result();

        // Count total records for pagination
        $total_records = $this->db->count_all_results();

        $this->load->view('asset-performance', [
            'assets' => $assets,
            'limit' => $limit,
            'offset' => $offset,
            'total_records' => $total_records
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/asset-performance.js'
        ]]);
    }


    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $filters = $this->input->post('filters');

        $query = $this->db->select('orders.order_id, orders.start_date, companies.company_id, companies.company_name, company_addresses.company_address_id,
    company_addresses.address_line_1, 
    asset_types.asset_id, asset_types.name as asset_type_name, 
    COUNT(order_equipment_bin_qr_codes.asset_type_id) as total_qr_codes, order_equipment_bin_qr_codes.created_at')
            ->from('orders')
            ->join('companies', 'orders.company_id=companies.company_id', 'LEFT')
            ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT')
            ->join('order_equipment_bin_qr_codes', 'orders.order_id = order_equipment_bin_qr_codes.order_id')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id');

        if (!isSuperAdmin()) {
            $query->where_in('company_addresses.branch_office_id', getUserActiveBranchsId());
        }

        if (!empty($filters['date'])) {
            $query->where('DATE(order_equipment_bin_qr_codes.created_at)', date('Y-m-d', strtotime($filters['date'])));
        }
        if (!empty($filters['month'])) {
            $query->where('monthname(order_equipment_bin_qr_codes.created_at)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $query->where('YEAR(order_equipment_bin_qr_codes.created_at)', $filters['year']);
        }

        $assets = $query->group_by('orders.company_address_id')->get()->result_array();

        if ($download_type == 'pdf') {
            $this->downloadPDF($assets);
        } else if ($download_type == 'excel') {
            $this->downloadCsv($assets);
        }
    }

    public function downloadPDF($assets)
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
                <th>Asset Type</th>
                <th>Quantity</th>
                <th>Date Deployed</th>
                <th>Customer Name</th>
                <th>Customer Location</th>
            </tr>
        </thead>
        <tbody>';

        foreach ($assets as $asset) {
            $html .= '<tr>';
            $html .= '<td>' . $asset['asset_type_name'] . '</td>';
            $html .= '<td>' . $asset['total_qr_codes'] . '</td>';
            $html .= '<td>' . $asset['created_at'] . '</td>';
            $html .= '<td>' . $asset['company_name'] . '</td>';
            $html .= '<td>' . $asset['address_line_1'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'asset-performance-report.pdf');
    }

    public function downloadCsv($assets)
    {
        $file = fopen('php://output', 'w');
        fputcsv($file, [
            'Asset Type',
            'Quantity',
            'Registration',
            'Date Deployed',
            'Customer Name',
            'Customer Location'
        ]);

        foreach ($assets as $asset) {
            fputcsv($file, [
                $asset['asset_type_name'],
                $asset['total_qr_codes'],
                $asset['created_at'],
                $asset['company_name'],
                $asset['address_line_1']
            ]);
        }

        $name = 'asset-performance-report.csv';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        fclose($file);
        exit;
    }
}
