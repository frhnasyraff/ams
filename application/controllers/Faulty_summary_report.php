<?php
defined('BASEPATH') or exit('No direct script access allowed');

class faulty_summary_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Assets & Items Faulty", 'title2' => "Assets & Items Faulty", "styles" => [
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



        $this->load->view('faulty_summary_report', [

            'workers' => $data
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/faulty_summary_report.js'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');
        $performance_record = []; // Initialize an empty array for storing records

        foreach ($ids as $id) {
            $parts = explode('.', $id);
            $idd = trim($parts[0]); // Get the first part (ID)
            $string = isset($parts[1]) ? trim($parts[1], "'") : ''; // Get the second part (type)

            // Check if the string indicates an item_faulty record
            if ($string == "item_faulty") {
                $query = $this->db->select('add_asset_items.*, 
                add_asset_items.item_name as system_name, 
                locations.name as location, 
                states.state_name as state, 
                asset_types.name as type_name,
                fault_type_color_code.fault_type as fault_type')
                    ->from('add_asset_items')
                    ->join('equipments_asset', 'equipments_asset.equipment_id = add_asset_items.asset_id')
                    ->join('locations', 'locations.id = equipments_asset.location_id')
                    ->join('states', 'states.id = equipments_asset.state_id')
                    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
                    ->join('fault_type_color_code', 'fault_type_color_code.id = add_asset_items.faulty_type_id')
                    ->where('add_asset_items.faulty_type_id IS NOT NULL')
                    ->where('add_asset_items.id', $idd); // Single ID for this query

                $result = $query->get()->result();
                if (!empty($result)) {
                    $performance_record = array_merge($performance_record, $result); // Merge results into the array
                }
            } else {
                $query = $this->db->select('equipments_asset.*, 
                equipments_asset.equipment_name as system_name, 
                locations.name as location, 
                states.state_name as state, 
                asset_types.name as type_name,
                fault_type_color_code.fault_type as fault_type')
                    ->from('equipments_asset')
                    ->join('locations', 'locations.id = equipments_asset.location_id')
                    ->join('states', 'states.id = equipments_asset.state_id')
                    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
                    ->join('fault_type_color_code', 'fault_type_color_code.id = equipments_asset.faulty_type_id')
                    ->where('equipments_asset.faulty_type_id IS NOT NULL')
                    ->where('equipments_asset.equipment_id', $idd); // Single ID for this query

                $result = $query->get()->result();
                if (!empty($result)) {
                    $performance_record = array_merge($performance_record, $result); // Merge results into the array
                }
            }
        }

        // Move the download check outside the loop
        if ($download_type == 'pdf') {
            $this->downloadPDF($performance_record, $ids); // Pass data to downloadPDF function
        } else if ($download_type == 'excel') {
            $this->downloadCsv($performance_record); // Pass data to downloadCsv function
        }
    }

    public function downloadPDF($performance_record, $ids)
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
            font-size: 14px;
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
                <th>System Name</th>
                <th>Asset / Item Type</th>
                <th>State</th>
                <th>Location</th>
                <th>Faulty Type</th>
            </tr>
        </thead>
        <tbody>';

        if (empty($performance_record)) {
            die(redirect("/driver_performance/index?error=No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->system_name . '</td>';
            $html .= '<td>' . $record->type_name . '</td>';
            $html .= '<td>' . $record->state . '</td>';
            $html .= '<td>' . $record->location . '</td>';
            $html .= '<td>' . $record->fault_type . '</td>';
            $html .= '</tr>';
        }

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


        $query = $this->db->select(
            'equipments_asset.*, 
        locations.name as location, 
        states.state_name as state, 
        asset_types.name as type_name,
        fault_type_color_code.fault_type as fault_type'
        )
            ->from('equipments_asset')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            ->join('states', 'states.id = equipments_asset.state_id')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
            ->join('fault_type_color_code', 'fault_type_color_code.id = equipments_asset.faulty_type_id')
            ->where('equipments_asset.faulty_type_id IS NOT NULL') // No third parameter
            ->get(); // Directly fetching the result

        $result = $query->result();



        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }


    public function ajax_list_item()
    {


        $query = $this->db->select(
            'add_asset_items.*, 
        locations.name as location, 
        states.state_name as state, 
        asset_types.name as type_name,
        fault_type_color_code.fault_type as fault_type'
        )
            ->from('add_asset_items')
            ->join('equipments_asset', 'equipments_asset.equipment_id = add_asset_items.asset_id')
            ->join('locations', 'locations.id = equipments_asset.location_id')
            ->join('states', 'states.id = equipments_asset.state_id')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
            ->join('fault_type_color_code', 'fault_type_color_code.id = add_asset_items.faulty_type_id')
            ->where('add_asset_items.faulty_type_id IS NOT NULL') // No third parameter
            ->get(); // Directly fetching the result

        $result = $query->result();

        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }
}
