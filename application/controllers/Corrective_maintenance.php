<?php
defined('BASEPATH') or exit('No direct script access allowed');

class corrective_maintenance extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->helper('url');
        $this->load->library('pagination');

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {

            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => 'Corrective Maintenance', 'title2' => 'Corrective Maintenance', 'styles' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
            'design/css/order-summary.css',
            'design/css/order-summary-cards.css',
            'design/css/custom-datatable.css'
        ]]);

        $this->load->view('corrective-maintenance', []);

        $this->load->view('footer', ['scripts' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js',
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.4.0/dist/chartjs-plugin-datalabels.min.js',
            'design/js/graph-colors.js',

            'design/js/corrective-summary.js',
            'design/js/corrective-maintenance-summary.js',
            'design/js/corrective_table_list.js'
        ]]);
    }







    public function corrective_table_list()
    {
        $maintenance_count = 0;
        $complete_count = 0;
        $progress_count = 0;
        $summary = [];

        // Fetch only 'complete' tickets and their latest status with asset details
        $data = $this->db->select('ticket.*, 
        equipments_asset.*, 
        asset_types.name AS asset_type_name,
        COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") AS final_status, 
        latest_maintenance_asset.update_date AS update_date,
        latest_maintenance_asset.maintenance_type_id AS maintenance_type,
        latest_maintenance_asset.faulty_type,
        latest_task_done.task_done AS task_done,
        latest_task_done.remarks AS remarks')
            ->from('ticket')
            ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')

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

            ->where('COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") =', 'complete') // <-- Only get 'complete' rows
            ->get()
            ->result();

        // Process data for counts
        foreach ($data as $row) {
            $complete_count++; // Only 'complete' rows returned
        }

        // Prepare summary for chart
        $summary = [
            'total_corrective_count' => $complete_count,
            'corrective_complete_count' => $complete_count,
            // 'corrective_in_progress_count' => $progress_count,
            // 'corrective_in_maintenance' => $maintenance_count
        ];

        // Return data as JSON
        echo json_encode(['data' => $data, 'summary' => $summary]);
        die();
    }


    public function corrective_table_list_all_status()
    {
        $maintenance_count = 0;
        $complete_count = 0;
        $progress_count = 0;
        $summary = [];

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
            ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')

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
            ->where('COALESCE(latest_maintenance_asset.final_status, "IN-MAINTENANCE") !=', 'complete')
            ->get()
            ->result();


        // Process data for counts
        foreach ($data as $row) {
            // if ($row->final_status === 'complete') {
            //     $complete_count++;
            // } else
            if ($row->final_status === 'in_progress') {
                $progress_count++;
            } elseif ($row->final_status === 'IN-MAINTENANCE') {
                $maintenance_count++;
            }
        }

        // Prepare summary for chart
        $summary = [
            'total_corrective_count' => $progress_count + $maintenance_count,
            // 'corrective_complete_count' => $complete_count,
            'corrective_in_progress_count' => $progress_count,
            'corrective_in_maintenance' => $maintenance_count
        ];

        // Return data as JSON for DataTable and Chart
        echo json_encode(['data' => $data, 'summary' => $summary]);
        die();
    }
}
