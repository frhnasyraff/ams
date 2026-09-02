<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_consolidation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->helper('url');
        $this->load->library('pagination');
        $this->load->model('user_model');
        $this->load->model('asset_consolidation_model');
        
        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }
    
public function index()
{
    // Get filters from POST/GET
    $filters = [
        'search' => $this->input->get('search'),
        'asset_type' => $this->input->get('asset_type'),
        'location_id' => $this->input->get('location_id'),
        'date_from' => $this->input->get('date_from'),
        'date_to' => $this->input->get('date_to')
    ];
    
    // Get duplicate assets
    $duplicate_assets = $this->asset_consolidation_model->get_duplicate_assets($filters);
    
    // Get asset types for filter dropdown
    $asset_types = $this->asset_consolidation_model->get_asset_types();
    
    // Get locations for filter dropdown
    $locations = $this->asset_consolidation_model->get_locations();
    
    // Count groups
    $group_indices = [];
    foreach ($duplicate_assets as $asset) {
        if (isset($asset['group_index'])) {
            $group_indices[$asset['group_index']] = true;
        }
    }
    $total_groups = count($group_indices);
    
    $data = [
        'title' => 'Asset Consolidation',
        'title2' => 'Asset Consolidation',
        'duplicate_assets' => $duplicate_assets,
        'asset_types' => $asset_types,
        'locations' => $locations,
        'filters' => $filters,
        'total_duplicates' => count($duplicate_assets),
        'total_groups' => $total_groups,
        'showing_count' => count($duplicate_assets) . ' assets found' . ($total_groups > 0 ? ' in ' . $total_groups . ' groups' : '')
    ];
    
    $this->load->view('header', $data);
    $this->load->view('asset-consolidation', $data);
    $this->load->view('footer', [
        'scripts' => [
            'https://code.jquery.com/jquery-3.6.0.min.js',
            'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js'
        ],
        'styles' => []
    ]);
}
    
    /**
     * Get asset details for modal
     */
    public function get_asset_details($id)
    {
        // Remove AJAX check or fix it
        $asset = $this->asset_consolidation_model->get_asset_by_id($id);
        
        if ($asset) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $asset
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Asset not found'
            ]);
        }
    }
    
    /**
     * Merge assets
     */
    public function merge_assets()
    {
        // Get POST data
        $primary_id = $this->input->post('primary_id');
        $merge_ids_json = $this->input->post('merge_ids');
        $final_data_json = $this->input->post('final_data');
        
        // Decode JSON data
        $merge_ids = json_decode($merge_ids_json, true);
        $final_data = json_decode($final_data_json, true);
        
        if (empty($primary_id) || empty($final_data)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Invalid data'
            ]);
            return;
        }
        
        // Primary ID ko merge_ids se remove karo (IMPORTANT)
        if (is_array($merge_ids) && ($key = array_search($primary_id, $merge_ids)) !== false) {
            unset($merge_ids[$key]);
        }
        
        $result = $this->asset_consolidation_model->merge_assets(
            $primary_id, 
            $merge_ids, 
            $final_data
        );
        
        if ($result) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Assets merged successfully'
            ]);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Failed to merge assets'
            ]);
        }
    }
}
