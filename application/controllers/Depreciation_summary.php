<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciation_summary extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('user_model');
        $this->load->model('depreciation_summary_model');
        
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }
    
    public function index($asset_type_id = 'all')
    {
        // Get dynamic data for the summary with filter
        $summary_data = $this->depreciation_summary_model->get_depreciation_summary($asset_type_id);
        
        // Get assets for write-off review with filter
        $write_off_assets = $this->depreciation_summary_model->get_write_off_assets($asset_type_id);
        
        // Get recent transactions with filter
        $recent_transactions = $this->depreciation_summary_model->get_recent_transactions($asset_type_id);
        
        // Get depreciation by category
        $category_data = $this->depreciation_summary_model->get_depreciation_by_category();
        
        // Get all asset types for dropdown
        $asset_types = $this->depreciation_summary_model->get_all_asset_types();
        
        // Get asset type name for display
        $selected_asset_type_name = 'All Assets';
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            foreach ($asset_types as $type) {
                if ($type['id'] == $asset_type_id) {
                    $selected_asset_type_name = $type['name'];
                    break;
                }
            }
        }
        
        $data = [
            'title' => 'Depreciation Summary',
            'title2' => 'Asset Depreciation Summary',
            'summary_data' => $summary_data,
            'write_off_assets' => $write_off_assets,
            'recent_transactions' => $recent_transactions,
            'category_data' => $category_data,
            'asset_types' => $asset_types,
            'selected_asset_type_id' => $asset_type_id,
            'selected_asset_type_name' => $selected_asset_type_name,
            'current_date' => date('F d, Y'),
            'current_year' => date('Y')
        ];
        
        $this->load->view('header', $data);
        $this->load->view('depreciation-summary', $data);
        $this->load->view('footer', [
            'scripts' => [
                'https://code.jquery.com/jquery-3.6.0.min.js',
                'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js',
                'https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js'
            ],
            'styles' => []
        ]);
    }
    
    /**
     * AJAX endpoint for filtered data
     */
    public function get_filtered_data()
    {
        // Allow AJAX requests only
        if (!$this->input->is_ajax_request()) {
            show_404();
        }
        
        $asset_type_id = $this->input->post('asset_type_id') ?: 'all';
        
        // Debug logging
        // error_log("Asset Type ID received: " . $asset_type_id);
        
        $summary_data = $this->depreciation_summary_model->get_depreciation_summary($asset_type_id);
        $write_off_assets = $this->depreciation_summary_model->get_write_off_assets($asset_type_id);
        $recent_transactions = $this->depreciation_summary_model->get_recent_transactions($asset_type_id);
        
        // Ensure all numeric values are actually numbers
        $summary_data = array_map(function($value) {
            return is_numeric($value) ? (float)$value : $value;
        }, $summary_data);
        
        $response = [
            'success' => true,
            'data' => [
                'summary' => $summary_data,
                'write_off_assets' => $write_off_assets,
                'recent_transactions' => $recent_transactions
            ]
        ];
        
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
    
    /**
     * Get detailed report data via AJAX
     */
    public function get_detailed_report($report_type = 'monthly')
    {
        if (!$this->input->is_ajax_request()) {
            die('Direct access not allowed');
        }
        
        $data = $this->depreciation_summary_model->get_detailed_report_data($report_type);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $data
        ]);
    }
    
    /**
     * Export report to PDF
     */
    public function export_pdf()
    {
        // Get data for PDF export
        $summary_data = $this->depreciation_summary_model->get_depreciation_summary();
        $write_off_assets = $this->depreciation_summary_model->get_write_off_assets();
        $recent_transactions = $this->depreciation_summary_model->get_recent_transactions();
        
        // In real implementation, you would generate PDF here
        // This is a placeholder
        echo "PDF export functionality would be implemented here.";
    }
    
    /**
     * Export report to Excel
     */
    public function export_excel()
    {
        // Get data for Excel export
        $summary_data = $this->depreciation_summary_model->get_depreciation_summary();
        $write_off_assets = $this->depreciation_summary_model->get_write_off_assets();
        $recent_transactions = $this->depreciation_summary_model->get_recent_transactions();
        
        // In real implementation, you would generate Excel here
        // This is a placeholder
        echo "Excel export functionality would be implemented here.";
    }
}
