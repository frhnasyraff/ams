<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Asset_summary_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Assets Summary", 'title2' => "Assets Summary", "styles" => [
            'design/css/asset_summary_report.css'
        ]]);


        // if (!isSuperAdmin()) {
        //     $query->where_in('workers.branch_office_id', getUserActiveBranchsId());
        // } else {
        //     // if user is super admin check if selected any branch
        //     if ($this->input->get('branch') && !empty($this->input->get('branch'))) {
        //         $query->where('workers.branch_office_id', $this->input->get('branch'));
        //     }
        // }



        $this->load->view('asset_summary_report', [

            'workers' => $data
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/asset_summary_report.js'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');

        $totalCount = $this->db->from('equipments_asset')
            ->count_all_results();

        $query = $this->db->select('location_id')
            ->from('equipments_asset')
            ->where('equipments_asset.location_id >', 0)
            ->group_by('location_id')
            ->get();
        $totalLocationCount = $query->num_rows();



        $totalInUseCount = $this->db->from('equipments_asset')
            ->where('equipments_asset.equipment_status', 'In use')
            ->count_all_results();

        $totalFaultyCount = $this->db->from('equipments_asset')
            ->where('equipments_asset.faulty_type_id >', '0')
            ->count_all_results();

        $query = $this->db->from('equipment_maintenance_asset');
        $totalmaintenanceCount = $this->db->count_all_results();




        $data = [
            "total" => $totalCount,
            "in_use" => $totalInUseCount,
            "maintenance" => $totalmaintenanceCount,
            "location" => $totalLocationCount,
            "faulty" => $totalFaultyCount
        ];


        $performance_record = $data;




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
                    <th>Asset Total</th>
                    <th>Asset Total Location</th>
                    <th>Asset In Use</th>
                    <th>Asset Faulty</th>
                    <th>Asset Maintenance</th>
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record)) {
            die(redirect("/driver_performance/index?error= No data to download."));
        }

        // Add summary data to the table
        $html .= '<tr>';
        $html .= '<td>' . $performance_record['total'] . '</td>';
        $html .= '<td>' . $performance_record['location'] . '</td>';
        $html .= '<td>' . $performance_record['in_use'] . '</td>';
        $html .= '<td>' . $performance_record['faulty'] . '</td>';
        $html .= '<td>' . $performance_record['maintenance'] . '</td>';
        $html .= '</tr>';

        $html .= '</tbody>';
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


        $totalCount = $this->db->from('equipments_asset')
            ->count_all_results();

        $totalInUseCount = $this->db->from('equipments_asset')
            ->where('equipments_asset.equipment_status', 'In use')
            ->count_all_results();

        $query = $this->db->from('equipment_maintenance_asset');
        $totalmaintenanceCount = $this->db->count_all_results();

        $totalFaultyCount = $this->db->from('equipments_asset')
            ->where('equipments_asset.faulty_type_id >', '0')
            ->count_all_results();

        $query = $this->db->select('location_id')
            ->from('equipments_asset')
            ->where('equipments_asset.location_id >', 0)
            ->group_by('location_id')
            ->get();
        $totalLocationCount = $query->num_rows();


        $data = [
            "total" => $totalCount,
            "in_use" => $totalInUseCount,
            "maintenance" => $totalmaintenanceCount,
            "location" => $totalLocationCount,
            "faulty" => $totalFaultyCount
        ];

        echo json_encode($data);
    }
}
