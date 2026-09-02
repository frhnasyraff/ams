<?php
defined('BASEPATH') or exit('No direct script access allowed');

class AssetMaintenance_summary extends CI_Controller
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

        $this->load->view('header', ['title' => "Assets In Maintenance", 'title2' => "Assets In Maintenance", "styles" => [
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



        $this->load->view('assetMaintenance_summary', [

            'workers' => $data
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/AssetMaintenance_summary.js'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');

        $ids = $this->input->post('record');

        $query = $this->db->select('equipment_maintenance_asset.*, 
        maintenance_type_color_code.maintenance_type as type_name, 
        equipments_asset.equipment_name,

       ') // Assuming 'name' is a column in manufacturers
            ->from('equipment_maintenance_asset')
            ->join('maintenance_type_color_code', 'maintenance_type_color_code.id = equipment_maintenance_asset.maintenance_type_id')
            ->join('equipments_asset', 'equipments_asset.equipment_id = equipment_maintenance_asset.equipment_id')
            ->where('equipments_asset.equipment_status', 'Maintenance')
            ->where_in('equipment_maintenance_asset.equipment_maintenance_id', $ids);

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
                    <th>Maintenance Cost</th>
                    <th>Maintenance Date</th>
                   
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            die(redirect("/driver_performance/index?error= No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->equipment_name . '</td>';
            $html .= '<td>' . $record->type_name . '</td>';
            $html .= '<td>' . $record->maintenance_cost . '</td>';
            $html .= '<td>' . $record->maintenance_date . '</td>';

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
            'Equipment Name',
            'Equipment Type',
            'Maintenance Cost',
            'Maintenance Date',

        ]);

        foreach ($performance_record as $record) {
            $worker = json_decode(urldecode($record), true);
            fputcsv($file, [
                $worker['equipment_name'],
                $worker['equipment_name'],
                $worker['maintenance_cost'],
                $worker['maintenance_date']

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


        // $query = $this->db->select('equipments_asset.*, 
        //                     locations.state_name as state_name, 
        //                     asset_types.name as eq_type_name, 
        //                     manufacturers.manufacturer_name as manufacturer_name,
        //                     managed_by_add_data.name as ownership') // Assuming 'name' is a column in manufacturers
        // ->from('equipments_asset')
        // ->join('locations', 'locations.id = equipments_asset.location_id')
        // ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
        // ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer')
        // ->join('managed_by_add_data', 'managed_by_add_data.id = equipments_asset.ownership')

        // ->where('equipments_asset.equipment_status', "In use");

        $query = $this->db->select('equipment_maintenance_asset.*, 
        maintenance_type_color_code.maintenance_type as type_name, 
        equipments_asset.equipment_name,

       ') // Assuming 'name' is a column in manufacturers
            ->from('equipment_maintenance_asset')
            ->join('maintenance_type_color_code', 'maintenance_type_color_code.id = equipment_maintenance_asset.maintenance_type_id')
            ->join('equipments_asset', 'equipments_asset.equipment_id = equipment_maintenance_asset.equipment_id')
            ->where('equipments_asset.equipment_status', 'Maintenance');



        $result = $query->get()->result();





        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }
}
