<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_depreciation extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->helper(['url']);
        $this->load->library(['session']);
        $this->load->model([
            'user_model',
            'Asset_depreciation_model' => 'deprModel'
        ]);

        if (!$this->user_model->logged_in()) {
            redirect('/order_summary?error=Please login first');
        }
    }

    /**
     * MAIN PAGE
     */
    public function index()
    {
        if (!$this->user_model->has_perm('list_assets')) {
            redirect('/order_summary?error=No permission');
        }

        $data = [
            'title' => 'Asset Depreciation Management',
            'asset_types' => $this->deprModel->get_asset_types_with_count(),
            'depreciation_methods' => $this->deprModel->get_depreciation_methods() // Changed from disposal_methods
        ];

        $this->load->view('header', $data);
        $this->load->view('asset_depreciation', $data);
        $this->load->view('footer');
    }

    /**
     * AJAX – ASSET TYPE DETAIL + SUMMARY
     */
public function get_asset_type_details()
{
    $asset_type_id = $this->input->get('asset_type_id');
    $year = $this->input->get('year') ?? date('Y');

    if (!$asset_type_id) {
        echo json_encode(['success' => false, 'message' => 'Asset type missing']);
        return;
    }

    $assets = $this->deprModel->get_assets_by_type($asset_type_id);
    $assetType = $this->deprModel->get_asset_type_info($asset_type_id);

    $summary = [
        'total_cost' => 0,
        'total_accumulated_depreciation' => 0,
        'total_current_year_depreciation' => 0,
        'total_net_book_value' => 0,
        'total_acc_impairment' => 0
    ];

    $assetDetails = [];

    foreach ($assets as $asset) {
        // Pass depreciation method to calculation
        $calc = $this->deprModel->calculate_depreciation(
            $asset['price_of_purchase'],
            $asset['purchase_date'],
            $asset['useful_life_years'],
            $asset['salvage_value'],
            $year,
            $asset['depreciation_method'],
            $asset['depreciate_value']
        );

        $summary['total_cost'] += $calc['cost'];
        $summary['total_accumulated_depreciation'] += $calc['accumulated'];
        $summary['total_current_year_depreciation'] += $calc['current_year'];
        $summary['total_net_book_value'] += $calc['net_book'];

        $assetDetails[] = [
            'asset' => $asset,
            'depreciation' => $calc
        ];
    }

    // ACC IMPAIRMENT RULE
    $summary['total_acc_impairment'] = 
        max(0, $summary['total_net_book_value'] - $summary['total_cost']);

    echo json_encode([
        'success' => true,
        'asset_type' => $assetType,
        'summary' => $summary,
        'assets' => $assetDetails
    ]);
}

    /**
     * AJAX – UPDATE DEPRECIATION POLICY
     */
    public function update_depreciation_policy()
    {
        $asset_type_id = $this->input->post('asset_type_id');

        $data = [
            'depreciation_method_id' => $this->input->post('depreciation_method_id'),
            'useful_life_years' => $this->input->post('useful_life_years'),
            'salvage_value' => $this->input->post('salvage_value'),
            'depreciate_value'       => $this->input->post('depreciate_value')
        ];

        $update = $this->deprModel->update_asset_type_policy($asset_type_id, $data);

        echo json_encode([
            'success' => $update,
            'message' => $update ? 'Policy updated' : 'Update failed'
        ]);
    }
}
