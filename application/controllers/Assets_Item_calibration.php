<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assets_Item_calibration extends CI_Controller
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
        $alertMessage = '';
        $itemalertMessage = '';
        $current_date = date('Y-m-d');
        $expiringAssetsCount = 0;
        $expiringItemsCount = 0;

        $asset_calibration_data = $this->db->select('equipments_asset.*')
            ->from('equipments_asset')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        $item_calibration_data = $this->db->select('add_asset_items.*')
            ->from('add_asset_items')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        // Asset calibration loop
        foreach ($asset_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringAssetsCount++;
            }
        }

        // Item calibration loop
        foreach ($item_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            // Ensure $frequency_day is a valid number
            $frequency_day = is_numeric($frequency_day) ? (int)$frequency_day : 0;
            $reminder_day = is_numeric($reminder_day) ? (int)$reminder_day : 0;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            // Debugging output to check date compariso

            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringItemsCount++;
            }
        }

        // Append alerts for both assets and items
        if ($expiringAssetsCount > 0) {
            $alertMessage .= "Asset Calibration Alert: <a href='Assets_Item_calibration/index'>&nbsp;&nbsp;&nbsp; {$expiringAssetsCount} </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;!";
        }

        if ($expiringItemsCount > 0) {
            $itemalertMessage .= "Item Calibration Alert: <a href='Assets_Item_calibration/index'>&nbsp;&nbsp;&nbsp; {$expiringItemsCount} </a> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;!";
        }

        $this->load->view('header', ['title' => 'Assets & Component Calibration', 'title2' => 'Assets & Items Calibration', 'styles' => [
            'design/css/performance-summary.css'
        ]]);

        $this->load->view('asset-item-calibration', [

            'alertMessage' => $alertMessage,
            'itemalertMessage' => $itemalertMessage,
            // 'equipment_types_data' => $equipment_types_data

        ]);

        $this->load->view('footer', ['scripts' => [
            'design/js/asset-item-calibration.js?v=2'
        ]]);
    }

    public function ajax_list()
    {

        $expiringAssets = [];
        // Array to hold expiring assets
        $current_date = date('Y-m-d');

        $asset_calibration_data = $this->db->select('equipments_asset.* , asset_types.name as type_name')
            ->from('equipments_asset')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
            ->where('calibration_date !=', null)
            ->where('frequency_day !=', null)
            ->where('reminder_day !=', null)
            ->get()->result();

        // Asset calibration loop
        foreach ($asset_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            // Check if asset is expiring
            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringAssets[] = $d;
                // Add expiring asset to array
            }
        }

        $data = [
            'data' => $expiringAssets // Send only expiring assets to the DataTable
        ];

        echo json_encode($data);
    }

    public function item_ajax_list()
    {

        $expiringAssets = [];
        // Array to hold expiring assets
        $current_date = date('Y-m-d');

        $asset_calibration_data = $this->db->select('add_asset_items.*,add_asset_items.id as equipment_id , add_asset_items.item_name as equipment_name ,asset_types.name as type_name')
            ->from('add_asset_items')
            ->join('equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id')
            ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type')
            ->where('add_asset_items.calibration_date !=', null)
            ->where('add_asset_items.frequency_day !=', null)
            ->where('add_asset_items.reminder_day !=', null)
            ->get()->result();

        // Asset calibration loop
        foreach ($asset_calibration_data as $d) {
            $calibration_date = $d->calibration_date;
            $frequency_day = $d->frequency_day;
            $reminder_day = $d->reminder_day;

            // Ensure $frequency_day is a valid number
            $frequency_day = is_numeric($frequency_day) ? (int)$frequency_day : 0;
            $reminder_day = is_numeric($reminder_day) ? (int)$reminder_day : 0;

            $selectedDate = new DateTime($calibration_date);
            $calibrationDate = clone $selectedDate;
            $calibrationDate->modify("+{$frequency_day} days");

            $reminderDate = clone $calibrationDate;
            $reminderDate->modify("-{$reminder_day} days");

            // Check if asset is expiring
            if ($current_date >= $reminderDate->format('Y-m-d')) {
                $expiringAssets[] = $d;
                // Add expiring asset to array
            }
        }

        $data = [
            'data' => $expiringAssets // Send only expiring assets to the DataTable
        ];

        echo json_encode($data);
    }

    public function get_calibration_asset($id)
    {

        // Find the calibration record based on equipment ID
        $calibration = $this->db->select('calibration_date')
            ->from('equipments_asset')
            ->where('equipment_id', $id)
            ->get()
            ->row_array();
        // Use row_array() to get a single record

        // Prepare the response data
        if ($calibration) {
            // Send the calibration data back in the expected format
            $data = [
                'calibration_date' => $calibration['calibration_date'], // Directly return the calibration date
            ];
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'Calibration data not found.']);
        }
    }

    public function get_calibration_item($id)
    {
        $calibration = $this->db->select('calibration_date')
            ->from('add_asset_items')
            ->where('id', $id)
            ->get()
            ->row_array();
        // Use row_array() to get a single record

        // Prepare the response data
        if ($calibration) {
            // Send the calibration data back in the expected format
            $data = [
                'calibration_date' => $calibration['calibration_date'], // Directly return the calibration date
            ];
            echo json_encode($data);
        } else {
            echo json_encode(['message' => 'Calibration data not found.']);
        }
    }

    public function update_calibration()
    {
        $calibration_date = $this->input->post('calibration_date');
        $id = $this->input->post('id');
        $type = $this->input->post('type');
        if ($type == 'equipment') {

            $this->db->set('calibration_date', $calibration_date);
            $this->db->where('equipment_id', $id);
            $updated = $this->db->update('equipments_asset');
        } elseif ($type == 'item') {
            $this->db->set('calibration_date', $calibration_date);
            $this->db->where('id', $id);
            $updated = $this->db->update('add_asset_items');
        }
        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Calibration updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update calibration data.']);
        }
    }
}
