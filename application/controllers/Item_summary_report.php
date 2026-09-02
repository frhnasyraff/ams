<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Item_summary_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Item Summary", 'title2' => "Item Summary", "styles" => [
            'design/css/performance-summary.css'
        ]]);


        if (!isSuperAdmin()) {
            $query->where_in('workers.branch_office_id', getUserActiveBranchsId());
        } else {
            // if user is super admin check if selected any branch
            if ($this->input->get('branch') && !empty($this->input->get('branch'))) {
                $query->where('workers.branch_office_id', $this->input->get('branch'));
            }
        }



        $this->load->view('item_summary_report', [

            'workers' => $data
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/item_summary_report.js'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');


        $query = $this->db->select('add_asset_items.*, 
                            locations.name as location, 
                            states.state_name as state, 
                            item_types.name as item_type, 
                           ') // Assuming 'name' is a column in manufacturers
            ->from('add_asset_items')
            ->join('equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            ->join('states', 'states.id = equipments_asset.state_id')
            ->join('item_types', 'item_types.id = add_asset_items.item_type_id')
            ->where_in('add_asset_items.id', $ids);

        $performance_record = $query->get()->result();


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
                    
                    <th>Item Type</th>
                    <th>System Name</th>
                    <th>State</th>
                    <th>Location</th>
                    <th>Manufacturer Name</th>
                  
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/driver_performance/index?error= No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->item_type . '</td>';
            $html .= '<td>' . $record->item_name . '</td>';
            $html .= '<td>' . $record->state . '</td>';
            $html .= '<td>' . $record->location . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';

            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'driver-performance-report.pdf');
    }

    function downloadCsv($performance_record)
    {

        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/driver_performance/index?error= No data to download."));
        }
        $file = fopen('php://output', 'w');
        fputcsv($file, [
            'Worker Name',
            'Worker Type',
            'WD-RORO',
            'WD-C',
            'Rent Bin',
            'Sales Bin',
            'Pullback',
            'Completed',
            'Missed',
            'Total'
        ]);

        foreach ($performance_record as $record) {
            $worker = json_decode(urldecode($record), true);
            fputcsv($file, [
                $worker['worker_name'],
                $worker['worker_type'],
                $worker['wd_roro'],
                $worker['wd_c_roro'],
                $worker['rent_bins'],
                $worker['sales_bins'],
                $worker['pullbacks'],
                $worker['completed'],
                $worker['missed'],
                $worker['total']
            ]);
        }

        $name = 'driver-performance-report.csv';
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Cache-Control: private', false);
        header('Content-Disposition: attachment; filename="' . basename($name) . '"');
        header('Content-Transfer-Encoding: binary');
        header('Connection: close');
        die;
    }

    public function ajax_list()
    {


        $query = $this->db->select('add_asset_items.*, 
                            locations.name as location, 
                            states.state_name as state, 
                            item_types.name as item_type, 
                           ') // Assuming 'name' is a column in manufacturers
            ->from('add_asset_items')
            ->join('equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            ->join('states', 'states.id = equipments_asset.state_id')
            ->join('item_types', 'item_types.id = add_asset_items.item_type_id');


        $result = $query->get()->result();


        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }
}
