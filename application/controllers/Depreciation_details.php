<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciation_details extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper(['url']);
        $this->load->library(['session']);
        $this->load->model('Depreciation_details_model', 'ddm');
        $this->load->model('user_model');

        if (!$this->user_model->logged_in()) {
            redirect('/order_summary?error=Please login first');
        }
    }

    public function index($encoded_asset_id = null)
    {
        if (!$this->user_model->has_perm('list_assets')) {
            redirect('/order_summary?error=No permission');
        }

        // MANUAL DECODING (temporary fix)
        $asset_id = null;
        if ($encoded_asset_id) {
            // Try to decode manually
            $decoded = base64_decode($encoded_asset_id);
            if ($decoded && strpos($decoded, 'STeVe-') === 0) {
                $asset_id = intval(str_replace("STeVe-", "", $decoded));
            } else {
                // If decoding fails, try to use as-is (might be direct ID)
                $asset_id = intval($encoded_asset_id);
            }
        }

        if (!$asset_id) {
            show_error('Invalid asset ID');
        }

        // Rest of your code...
        $data['title'] = 'Depreciation Details';
        $data['asset'] = [];
        $data['params'] = [];
        $data['rows'] = [];


        // Load data for specific asset if ID is provided
        if ($asset_id) {
            $this->load->model('asset_logs');
            
            // Get asset details directly by equipment_id
            $asset = $this->ddm->get_asset_by_id($asset_id);
            
            if ($asset) {
                // Get depreciation parameters for this asset type
                $params = $this->ddm->get_depreciation_params($asset['equipment_type']);
                
                // Calculate schedule for this specific asset
                $schedule = $this->ddm->calculate_schedule($asset, $params);
                
                $current_book_value = 0;
                if (!empty($schedule)) {
                    $last_row = end($schedule);
                    $current_book_value = $last_row['ending'];
                }
                
                // Format data for view
                $data['asset'] = [
                    'name' => $asset['equipment_name'] ?? 'Unknown Asset',
                    'asset_id' => $asset['equipment_id'] ?? 'N/A',
                    'category' => $asset['equipment_type_name'] ?? 'Unknown',
                    'acquisition_date' => isset($asset['purchase_date']) ? 
                        date('M d, Y', strtotime($asset['purchase_date'])) : 'N/A',
                    'cost' => $asset['price_of_purchase'] ?? 0,
                    'method' => ($params['depreciation_method'] === 'Straight Line')
                        ? 'Straight Line'
                        : 'Reducing Balance',
                    'useful_life' => ($params['depreciation_method'] ?? 'Straight-Line') === 'Straight-Line' 
                        ? ($params['useful_life_years'] ?? 0) . ' Years' 
                        : ($params['depreciate_value'] ?? 0) . '%',
                    'salvage_value' => $params['salvage_value'] ?? 0,
                    'depreciate_value' => $params['depreciate_value'] ?? 0,
                    'current_book' => $current_book_value
                ];
                
                $data['params'] = $params;
                $data['rows'] = $schedule;
                $data['title'] = 'Depreciation Details - ' . $asset['equipment_name'];
                
                // Set asset ID for export
                // $data['export_asset_id'] = $asset['equipment_type'];
                $data['export_asset_id'] = $this->steve->id_encode($asset['equipment_id']);
            }
        }

        $this->load->view('header', $data);
        $this->load->view('depreciation_details', $data);
        $this->load->view('footer');
    }

    public function export_csv($encoded_asset_id = null)
    {
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=depreciation_details_' . date('Y-m-d') . '.csv');

        $output = fopen('php://output', 'w');

        fputcsv($output, [
            'Year',
            'Beginning Value',
            'Depreciation',
            'Accumulated Depreciation',
            'Ending Value'
        ]);

        if ($encoded_asset_id) {
            $asset_id = $this->steve->id_decode($encoded_asset_id);
            $asset = $this->ddm->get_asset_by_id($asset_id);
            
            if ($asset) {
                $params = $this->ddm->get_depreciation_params($asset['equipment_type']);
                $schedule = $this->ddm->calculate_schedule($asset, $params);
                
                foreach ($schedule as $row) {
                    fputcsv($output, [
                        $row['year'],
                        number_format($row['beginning'], 2),
                        number_format($row['depreciation'], 2),
                        number_format($row['accumulated'], 2),
                        number_format($row['ending'], 2)
                    ]);
                }
            }
        }

        fclose($output);
    }
}
