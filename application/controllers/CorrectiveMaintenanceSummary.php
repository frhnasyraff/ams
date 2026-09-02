<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CorrectiveMaintenanceSummary extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {


        $this->load->view('header', ['title' => 'Corrective Maintenance Summary', 'title2' => 'Corrective Maintenance Summary', 'styles' => [
            'design/css/performance-summary.css',
            'design/css/custom-datatable.css'
        ]]);

        $this->load->view('corrective-maintenance-summary', []);

        $this->load->view('footer', ['scripts' => [
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js',
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.4.0/dist/chartjs-plugin-datalabels.min.js',
            'design/js/graph-colors.js',
            'design/js/corrective-maintenance-summary.js'
        ]]);
    }

    public function ajax_list()
    {
        // Fetch asset types
        $asset_types = $this->db->select('asset_id, name')->from('asset_types')->get()->result_array();

        // Fetch all fault types (for headings and chart) - fetch them just once.
        $fault_types = $this->db->select('fault_type')->from('fault_type_color_code')->get()->result_array();
        $fault_types_array = array_column($fault_types, 'fault_type'); // For DataTables columns

        $data = [];
        $chart_data = []; // Array to store chart data

        foreach ($asset_types as $asset_type) {
            $row = ['asset_type' => $asset_type['name']];

            // Initialize counts for all fault types to 0 for each asset type
            foreach ($fault_types_array as $fault_type) {
                $row[$fault_type] = 0;  // Initialize counts to 0
            }

            // Optimized query to get counts directly using GROUP BY
            $ticket_counts = $this->db->select('ft.fault_type, COUNT(*) as ticket_count')
                ->from('ticket t')
                ->join('equipments_asset ea', 'ea.equipment_id = t.equipment_id')
                ->join('fault_type_color_code ft', 'ft.id = t.fault_type_id')
                ->where('ea.equipment_type', $asset_type['asset_id'])
                ->group_by('ft.fault_type')
                ->get()
                ->result_array();

            // Update counts from the grouped query and prepare chart data
            foreach ($ticket_counts as $count_data) {
                $fault_type = $count_data['fault_type'];
                $count = (int)$count_data['ticket_count'];
                $row[$fault_type] = $count; // Ensure integer

                // Add to chart data:  fault_type => total count across all asset types
                if (isset($chart_data[$fault_type])) {
                    $chart_data[$fault_type] += $count;
                } else {
                    $chart_data[$fault_type] = $count;
                }
            }

            $data[] = $row;
        }

        // Prepare chart data for JSON response (convert to array of objects)
        $chart_data_array = [];
        foreach ($chart_data as $fault_type => $count) {
            $chart_data_array[] = ['label' => $fault_type, 'value' => $count];
        }

        echo json_encode([
            'data' => $data,
            'fault_types' => $fault_types_array, // For DataTables
            'chart_data' => $chart_data_array // For the chart
        ]);
    }


    public function ajax_list_items()
    {
        // Fetch asset types
        $asset_types = $this->db->select('asset_id, name')->from('asset_types')->get()->result_array();

        // Fetch all fault types (for headings and chart) - fetch them just once.
        $fault_types = $this->db->select('fault_type')->from('fault_type_color_code')->get()->result_array();
        $fault_types_array = array_column($fault_types, 'fault_type'); // For DataTables columns

        $data = [];
        $chart_data = []; // Array to store chart data

        foreach ($asset_types as $asset_type) {
            $row = ['asset_type' => $asset_type['name']];

            // Initialize counts for all fault types to 0 for each asset type
            foreach ($fault_types_array as $fault_type) {
                $row[$fault_type] = 0;  // Initialize counts to 0
            }

            // Optimized query to get counts directly using GROUP BY
            $ticket_counts = $this->db->select('ft.fault_type, COUNT(*) as ticket_count')
                ->from('item_ticket it')
                ->join('add_asset_items ai', 'ai.id = it.item_id') // Get asset item
                ->join('equipments_asset ea', 'ea.equipment_id = ai.asset_id') // Get asset
                ->join('fault_type_color_code ft', 'ft.id = it.fault_type_id') // Get fault type
                ->where('ea.equipment_type', $asset_type['asset_id']) // Filter by asset type
                ->group_by('ft.fault_type')
                ->get()
                ->result_array();

            // Update counts from the grouped query and prepare chart data
            foreach ($ticket_counts as $count_data) {
                $fault_type = $count_data['fault_type'];
                $count = (int)$count_data['ticket_count'];
                $row[$fault_type] = $count; // Ensure integer

                // Add to chart data: fault_type => total count across all asset types
                if (isset($chart_data[$fault_type])) {
                    $chart_data[$fault_type] += $count;
                } else {
                    $chart_data[$fault_type] = $count;
                }
            }

            $data[] = $row;
        }

        // Prepare chart data for JSON response (convert to array of objects)
        $chart_data_array = [];
        foreach ($chart_data as $fault_type => $count) {
            $chart_data_array[] = ['label' => $fault_type, 'value' => $count];
        }

        echo json_encode([
            'data' => $data,
            'fault_types' => $fault_types_array, // For DataTables
            'chart_data' => $chart_data_array // For the chart
        ]);
    }
}
