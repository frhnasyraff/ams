<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssetInUse_summary extends CI_Controller
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

        $this->load->view('header', ['title' => "Assets In Use", 'title2' => "Assets In Use", "styles" => [
            'design/css/performance-summary.css'
        ]]);


        // if (!isSuperAdmin()) {
        //     $query->where_in('workers.branch_office_id', getUserActiveBranchsId());
        // } else {
        //     // if user is super admin check if selected any branch
        //     if ($this->input->get('branch') && !empty($this->input->get('branch'))) {
        //         $query->where('workers.branch_office_id', $this->input->get('branch'));
        //     }
        // }



        $this->load->view('assetInUse_summary', [

            'workers' => $data
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/assetInUse_summary.js'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');

        $query = $this->db->select('equipments_asset.*, 
                            states.state_name as state_name, 
                            equipment_types.equipment_type_name as eq_type_name, 
                            manufacturers.manufacturer_name as manufacturer_name,
                            managed_by_add_data.name as ownership') // Assuming 'name' is a column in manufacturers
            ->from('equipments_asset')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            // Legacy schema used locations.state_name; current schema stores state_id on locations.
            ->join('states', 'states.id = locations.state_id', 'left')
            ->join('equipment_types', 'equipment_types.equipment_type_id = equipments_asset.equipment_type')
            ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer')
            ->join('managed_by_add_data', 'managed_by_add_data.id = equipments_asset.ownership')

            ->where('equipments_asset.equipment_status', "In use")
            ->where_in('equipments_asset.equipment_id', $ids);

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
                    <th>Equipment Name</th>
                    <th>Equipment Type</th>
                    <th>Location</th>
                    <th>Managed By</th>
                    <th>Manufacturer</th>
                    <th>Registration</th>
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/driver_performance/index?error= No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->equipment_name . '</td>';
            $html .= '<td>' . $record->eq_type_name . '</td>';
            $html .= '<td>' . $record->state_name . '</td>';
            $html .= '<td>' . $record->ownership . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';
            $html .= '<td>' . $record->equipment_registration . '</td>';

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


        $query = $this->db->select('equipments_asset.*, 
                            states.state_name as state_name, 
                            asset_types.name as eq_type_name, 
                            manufacturers.manufacturer_name as manufacturer_name,
                            managed_by_add_data.name as ownership') // Assuming 'name' is a column in manufacturers
            ->from('equipments_asset')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            // Legacy schema used locations.state_name; current schema stores state_id on locations.
            ->join('states', 'states.id = locations.state_id', 'left')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
            ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer')
            ->join('managed_by_add_data', 'managed_by_add_data.id = equipments_asset.ownership')

            ->where('equipments_asset.equipment_status', "In use");


        $result = $query->get()->result();

        // var_dump($this->db->last_query());
        // exit();


        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }
}

