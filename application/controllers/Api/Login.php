<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->database();
    }

    public function doLogin()
    {
        // Request validation
        $this->form_validation->set_rules('username', 'Username', 'required');
        $this->form_validation->set_rules('password', 'Password', 'required');

        if ($this->form_validation->run() == FALSE) {
            $response = array(
                'status' => false,
                'message' => 'data not validated',
                'errors' => $this->form_validation->error_array()
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode($response));
        }

        $username = $this->input->post('username');
        $password = $this->input->post('password');

        // Query to check if the user exists
        $query = $this->db->from('users')
            ->where('username', $username)
            ->get();

        if ($query->num_rows() > 0) {
            $user = $query->row();

            // Verify the password
            if (password_verify($password, $user->password)) {

                // Check if the user can login via mobile
                if (is_null($user->mobile)) {
                    $response = array(
                        'status' => false,
                        'message' => 'Not available for mobile login'
                    );
                    return $this->output
                        ->set_content_type('application/json')
                        ->set_status_header(401)
                        ->set_output(json_encode($response));
                }

                // Successful login response
                $response = array(
                    'status' => true,
                    'message' => 'User logged in successfully'
                );
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(200)
                    ->set_output(json_encode($response));
            } else {
                // Invalid password response
                $response = array(
                    'status' => false,
                    'message' => 'Invalid credentials'
                );
                return $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(401)
                    ->set_output(json_encode($response));
            }
        } else {
            // User not found response
            $response = array(
                'status' => false,
                'message' => 'Invalid credentials'
            );
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode($response));
        }
    }

    // In your controller, e.g., EquipmentAssetController.php

    public function getAllAssets()
{
    $page = $this->input->post('page') ?: 1;
    $perPage = $this->input->post('asset_per_page') ?: 10;
    $search = trim($this->input->post('search', TRUE));
    $equipmentType = $this->input->post('equipment_type');
    $equipmentGroup = $this->input->post('equipment_group');

    $offset = ($page - 1) * $perPage;

    // ----- SELECT Columns -----
    $select = 'equipments_asset.*,
            asset_types.name AS asset_type_name, 
            locations.name AS location_name, 
            states.state_name AS state_name';

    // ----- Count Total Records -----
    $this->db->from('equipments_asset');
    $totalRecords = $this->db->count_all_results();

    // ----- Base Query -----
    $this->db->select($select);
    $this->db->from('equipments_asset');
    $this->db->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left');
    $this->db->join('locations', 'locations.id = equipments_asset.location_id', 'left');
    $this->db->join('states', 'states.id = equipments_asset.state_id', 'left');

    // ----- Apply Filters -----
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('equipments_asset.equipment_name', $search);
        $this->db->or_like('asset_types.name', $search);
        $this->db->or_like('locations.name', $search);
        $this->db->or_like('states.state_name', $search);
        $this->db->or_like('equipments_asset.equipment_status', $search);
        $this->db->or_like('equipments_asset.rfid', $search); // ADDED: RFID search filter
        $this->db->group_end();
    }

    if (!empty($equipmentType)) {
        $this->db->where('equipments_asset.equipment_type', $equipmentType);
    }

    if (!empty($equipmentGroup)) {
        $this->db->where('equipments_asset.equipment_status', $equipmentGroup);
    }

    // ----- Clone for Filtered Count -----
    $filteredQuery = clone $this->db;
    $filteredRecords = $filteredQuery->count_all_results('', FALSE);

    // ----- Reset the Query Builder -----
    if (method_exists($this->db, 'reset_query')) {
        $this->db->reset_query(); // CI 3.1.9+
    } else {
        $this->db->_reset_select(); // Fallback for older CI versions
    }

    // ----- Rebuild the Query Again -----
    $this->db->select($select);
    $this->db->from('equipments_asset');
    $this->db->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left');
    $this->db->join('locations', 'locations.id = equipments_asset.location_id', 'left');
    $this->db->join('states', 'states.id = equipments_asset.state_id', 'left');

    // ----- Reapply Filters -----
    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('equipments_asset.equipment_name', $search);
        $this->db->or_like('asset_types.name', $search);
        $this->db->or_like('locations.name', $search);
        $this->db->or_like('states.state_name', $search);
        $this->db->or_like('equipments_asset.equipment_status', $search);
        $this->db->or_like('equipments_asset.rfid', $search); // ADDED: RFID search filter
        $this->db->group_end();
    }

    if (!empty($equipmentType)) {
        $this->db->where('equipments_asset.equipment_type', $equipmentType);
    }

    if (!empty($equipmentGroup)) {
        $this->db->where('equipments_asset.equipment_status', $equipmentGroup);
    }

    // ----- Get All Matching Data (No Pagination Applied in Result) -----
    $query = $this->db->get();
    $result = $query->result();

    // ----- Prepare Response -----
    echo json_encode($result);

        // If you need metadata too, uncomment below:
        /*
    echo json_encode([
        'current_page' => (int)$page,
        'per_page' => (int)$perPage,
        'recordsTotal' => $totalRecords,
        'recordsFiltered' => $filteredRecords,
        'data' => $result
    ]);
    */
}


    public function getAssetById($assetId)
    {
        // Validate the asset ID
        if (empty($assetId) || !is_numeric($assetId)) {
            // Return a 400 Bad Request if the asset ID is invalid
            $this->output->set_status_header(400);
            echo json_encode(['error' => 'Invalid asset ID']);
            return;
        }

        // Fetch the asset details from 'equipments_asset' table
        $this->db->select(
            'equipments_asset.equipment_id,
             equipments_asset.equipment_registration,
             equipments_asset.equipment_name,
             equipments_asset.rfid,
             equipments_asset.calibration_date,
             equipments_asset.frequency_day,
             equipments_asset.reminder_day,
             equipments_asset.status,
             equipments_asset.manufacturer_drwing_number,
             equipments_asset.equipment_picture,
             equipments_asset.maintenance_date,
             equipments_asset.purchase_date,
             equipments_asset.date_installed,
             managed_by_add_data.name AS managed_by,
             store_location.name AS store_location_name,
             locations.name AS location_name, 
             fault_type_color_code.fault_type AS faulty_type_name, 
             states.state_name as state_name, 
             asset_types.name AS asset_type,
             vendor_part_number.part_number as vendor_part_number,
             vendor_manufacturing_number.manufacturer_name AS manufacturer_name'

        );
        $this->db->from('equipments_asset');
        $this->db->join('locations', 'equipments_asset.location_id = locations.id', 'left');
        $this->db->join('fault_type_color_code', 'equipments_asset.faulty_type_id = fault_type_color_code.id', 'left');
        $this->db->join('states', 'equipments_asset.state_id = states.id', 'left');
        $this->db->join('vendor_part_number', 'equipments_asset.vendor_part_number_id = vendor_part_number.id', 'left');
        $this->db->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left');
        $this->db->join('vendor_manufacturing_number', 'equipments_asset.equipment_manufacturer = vendor_manufacturing_number.id', 'left');
        $this->db->join('managed_by_add_data', 'equipments_asset.ownership = managed_by_add_data.id', 'left');
        $this->db->join('store_location', 'equipments_asset.store_location_id = store_location.id', 'left');
        $this->db->where('equipments_asset.equipment_id', $assetId);
        $query = $this->db->get();

        // Check if the asset exists
        if ($query->num_rows() === 0) {
            // Return a 404 Not Found if the asset is not found
            $this->output->set_status_header(404);
            echo json_encode(['error' => 'Asset not found']);
            return;
        }

        // Get asset data
        $asset = $query->row();


        $this->db->select(
            'add_asset_items.id,
             add_asset_items.item_name,
             add_asset_items.vendor_part_number,
             add_asset_items.manufacturer_name,
             add_asset_items.manufacturer_part_number,
             add_asset_items.manufacturer_drawing_number,
             add_asset_items.items_qr_code,
             add_asset_items.item_status,
             add_asset_items.calibration_date,
             add_asset_items.frequency_day,
             add_asset_items.reminder_day,
             add_asset_items.item_picture,
             item_status.name AS item_status,
             equipments_asset.equipment_name AS asset_name,
             item_types.name AS item_type'

        );
        $this->db->from('add_asset_items');
        $this->db->join('item_status', 'add_asset_items.item_status_id = item_status.id', 'left');
        $this->db->join('item_types', 'add_asset_items.item_type_id = item_types.id', 'left');
        $this->db->join('equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left');
        $this->db->where('asset_id', $assetId);
        $itemsQuery = $this->db->get();
        // Fetch all items related to this asset from the 'add_asset_items' table
        // $this->db->select('*');
        // $this->db->from('add_asset_items');
        // $this->db->where('asset_id', $assetId);
        // $itemsQuery = $this->db->get();

        // Get the associated items
        $items = $itemsQuery->result();

        // Return the asset and related items as a JSON response
        $response = [
            'asset' => $asset,
            'items' => $items
        ];

        echo json_encode($response);
    }

    public function count()
    {
        // Count total assets
        $totalAssets = $this->db->count_all('equipments_asset');

        // Count assets in use
        $totalAssetsInUse = $this->db->where('equipment_status', 'SERVICEABLE')->count_all_results('equipments_asset');

        // Count assets in maintenance
        // Fetch all unique equipment types

        $totalAssetsInMaintenance = null;
        $equipment_types = $this->db->select('asset_id')
            ->from('asset_types')
            ->get()
            ->result();

        $totalCorrective = 0;
        $totalPreventive = 0;
        $current_date = date('Y-m-d');
        $default_frequency_year = 2;

        foreach ($equipment_types as $type) {
            // Count corrective maintenance assets
            $corrective = $this->db->select('COUNT(DISTINCT equipments_asset.equipment_id) as corrective_count')
                ->from('equipments_asset')
                ->join(
                    '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                  FROM equipment_maintenance_asset 
                  WHERE maintenance_type_id = "corrective" AND final_status != "complete"
                  GROUP BY equipment_id
                ) t2',
                    'equipments_asset.equipment_id = t2.equipment_id',
                    'left'
                )
                ->where('equipments_asset.equipment_type', $type->asset_id)
                ->where('equipments_asset.equipment_status', 'MAINTENANCE')
                ->get()
                ->row()
                ->corrective_count ?? 0;

            // Count preventive maintenance assets
            $preventive = $this->db->select('COUNT(*) as preventive_count')
                ->from('equipments_asset ea')
                ->join(
                    '(SELECT equipment_id, maintenance_date, frequency_year 
                  FROM equipments_asset 
                  WHERE frequency_year IS NOT NULL AND maintenance_date IS NOT NULL
                ) e_freq',
                    'ea.equipment_id = e_freq.equipment_id',
                    'inner'
                )
                ->join(
                    '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                  FROM equipment_maintenance_asset 
                  WHERE maintenance_type_id = "preventive"
                  GROUP BY equipment_id
                ) t2',
                    'ea.equipment_id = t2.equipment_id',
                    'left'
                )
                ->where('ea.equipment_type', $type->asset_id)
                ->where('(t2.final_status IS NULL OR t2.final_status != "complete")')
                ->where("TIMESTAMPDIFF(MONTH, e_freq.maintenance_date, '$current_date') >= (12 / COALESCE(e_freq.frequency_year, $default_frequency_year))")
                ->get()
                ->row()
                ->preventive_count ?? 0;

            $totalCorrective += (int)$corrective;
            $totalPreventive += (int)$preventive;
        }

        $totalAssetsInMaintenance = $totalCorrective + $totalPreventive;

        // Count total locations
        $totalLocations = $this->db->count_all_results('locations');
        $this->db->reset_query();

        // Count total items
        $this->db->select('COUNT(id) as total_items');
        $total_items = $this->db->get('add_asset_items')->row()->total_items;
        $this->db->reset_query();

        // Count active items
        $this->db->from('add_asset_items');
        $this->db->where('item_status_id !=', null);
        $activeItemCount = $this->db->count_all_results();
        $this->db->reset_query();

        // Count items in maintenance
        $this->db->from('add_asset_items');
        $this->db->where('item_status', 'maintinence');
        $MaintinenceItemCount = $this->db->count_all_results();
        $this->db->reset_query();

        // Count faulty items
        $faultyItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'UNSERVICEABLE')
            ->count_all_results();

        // Count items with store location
        $storelocationItemCount = $this->db
            ->select('COUNT(DISTINCT add_asset_items.store_location_id) as total_locations')
            ->from('add_asset_items')
            ->join('store_location', 'store_location.id = add_asset_items.store_location_id', 'inner')
            ->get()
            ->row()
            ->total_locations;

        // Prepare response data
        $data = [
            'total_items' => $total_items,
            'activeItemCount' => $activeItemCount,
            'MaintinenceItemCount' => $MaintinenceItemCount,
            'faultyItemCount' => $faultyItemCount,
            'storelocationItemCount' => $storelocationItemCount,
            'totalAssets' => $totalAssets,
            'totalAssetsInUse' => $totalAssetsInUse,
            'totalAssetsInMaintenance' => $totalAssetsInMaintenance,
            'totalLocations' => $totalLocations
        ];

        echo json_encode($data);
    }

    public function graphData()
    {
        // Enable error reporting for debugging purposes
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);

        // Helper function to handle query errors

        function checkQueryError($db)
        {
            $error = $db->error();
            if ($error['code'] != 0) {
                die('Database error: ' . $error['message']);
            }
        }

        // 1. Fetch data for 'In use' equipment with type details
        // Step 1: Get all asset types and their associated color
        $this->db->select('asset_types.asset_id, asset_types.name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_type_color.asset_type_id = asset_types.asset_id', 'left');
        $asset_types_query = $this->db->get();
        $asset_types = $asset_types_query->result();

        // Step 2: Get all Serviceable equipment
        $this->db->select('equipment_type')
            ->from('equipments_asset')
            ->where('equipment_status', 'Serviceable');

        $serviceable_query = $this->db->get();
        $serviceable_equipments = $serviceable_query->result();

        // Step 3: Count serviceable equipment per asset type
        $equipment_counts = [];
        foreach ($serviceable_equipments as $equipment) {
            $type_id = $equipment->equipment_type;
            if (!isset($equipment_counts[$type_id])) {
                $equipment_counts[$type_id] = 0;
            }
            $equipment_counts[$type_id]++;
        }

        // Step 4: Merge counts into asset types
        $result = [];
        $total = 0;
        foreach ($asset_types as $type) {
            $count = isset($equipment_counts[$type->asset_id]) ? $equipment_counts[$type->asset_id] : 0;
            if ($count == 0) {
                continue; // Skip this type if in_use_count is 0
            }
            $total += $count;

            $result[] = (object)[
                'equipment_type' => $type->asset_id,
                'name' => $type->name,
                'color' => $type->color,
                'in_use_count' => $count
            ];
        }
        // 2. Fetch maintenance data
        // $queryMaintenance = $this->db->select('equipment_maintenance_asset.maintenance_type_id, maintenance_type_color_code.color, maintenance_type_color_code.maintenance_type, COUNT(equipment_maintenance_asset.maintenance_type_id) as in_use_count')
        // Fetch all unique equipment types
        $equipment_types = $this->db->select('asset_id, name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id', 'left')
            ->get()
            ->result();

        $data = [];
        $total = null;

        foreach ($equipment_types as $type) {
            // Total assets of the current equipment type
            $total_assets = $this->db->select('COUNT(*) as total')
                ->from('equipments_asset')
                ->where('equipment_type', $type->asset_id)
                ->get()
                ->row()
                ->total;

            if ($total_assets > 0) {

                // Assets with maintenance_type_id = corrective
                $corrective_maintenance = $this->db->select('COUNT(DISTINCT equipments_asset.equipment_id) as corrective_count')
                    ->from('equipments_asset')
                    ->join(
                        '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                        FROM equipment_maintenance_asset 
                        WHERE maintenance_type_id = "corrective" AND final_status != "complete"
                        GROUP BY equipment_id
                    ) t2',
                        'equipments_asset.equipment_id = t2.equipment_id',
                        'left'
                    )
                    ->where('equipments_asset.equipment_type', $type->asset_id)
                    ->where('equipments_asset.equipment_status', 'MAINTENANCE')
                    ->get()
                    ->row()
                    ->corrective_count ?? 0; // Ensure it defaults to 0 if NULL

                // Assets with maintenance_type_id = preventive
                $default_frequency_year = 2; // Default frequency in years
                $current_date = date('Y-m-d');

                $preventive_maintenance = $this->db->select('COUNT(*) as preventive_count')
                    ->from('equipments_asset ea')
                    ->join(
                        '(SELECT equipment_id, maintenance_date, frequency_year, maintenance_reminder_day 
                        FROM equipments_asset 
                        WHERE frequency_year IS NOT NULL AND maintenance_date IS NOT NULL
                    ) e_freq',
                        'ea.equipment_id = e_freq.equipment_id',
                        'inner'
                    )
                    ->join(
                        '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                        FROM equipment_maintenance_asset 
                        WHERE maintenance_type_id = "preventive"
                        GROUP BY equipment_id
                    ) t2',
                        'ea.equipment_id = t2.equipment_id',
                        'left'
                    )
                    ->where('ea.equipment_type', $type->asset_id)
                    ->where('(t2.final_status IS NULL OR t2.final_status != "complete")')
                    ->where("TIMESTAMPDIFF(MONTH, e_freq.maintenance_date, '$current_date') >= (12 / COALESCE(e_freq.frequency_year, $default_frequency_year))")
                    ->get()
                    ->row()
                    ->preventive_count ?? 0; // Ensure it defaults to 0 if NULL

                // **Ignore this data if both maintenance counts are 0**
                if ($corrective_maintenance > 0 || $preventive_maintenance > 0) {
                    $in_use_count = $corrective_maintenance + $preventive_maintenance;
                    $total += $corrective_maintenance + $preventive_maintenance;
                    $data[] = [
                        'maintenance_type' => $type->name,
                        'color' => $type->color,
                        'in_use_count' => $in_use_count,
                        'maintenance_type_id' =>  $type->asset_id,


                    ];
                }
            }
        }
        // 3. Fetch data for faulty equipment
        $this->db->select('asset_types.asset_id, asset_types.name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_type_color.asset_type_id = asset_types.asset_id', 'left');
        $asset_types_query = $this->db->get();
        $asset_types = $asset_types_query->result();

        // Step 2: Get all UnServiceable equipment
        $this->db->select('equipment_type')
            ->from('equipments_asset')
            ->where('equipment_status', 'UnServiceable');


        $unserviceable_query = $this->db->get();
        $unserviceable_equipments = $unserviceable_query->result();

        // Step 3: Count unserviceable equipment per asset type
        $equipment_counts = [];
        foreach ($unserviceable_equipments as $equipment) {
            $type_id = $equipment->equipment_type;
            if (!isset($equipment_counts[$type_id])) {
                $equipment_counts[$type_id] = 0;
            }
            $equipment_counts[$type_id]++;
        }

        // Step 4: Merge counts into asset types
        $result = [];
        $total = 0;
        foreach ($asset_types as $type) {
            $count = isset($equipment_counts[$type->asset_id]) ? $equipment_counts[$type->asset_id] : 0;
            if ($count == 0) {
                continue; // Skip this type if in_use_count is 0
            }
            $total += $count;

            $result[] = (object)[
                'fault_type' => $type->name,
                'color' => $type->color,
                'faulty_count' => $count
            ];
        }

        // 4. Fetch location data
        $this->db->select("COALESCE(s.state_name, 'Unassigned') AS name, COALESCE(MIN(l.colour), '#64748B') AS colour, COUNT(DISTINCT ea.equipment_id) AS in_use_count", false);
        $this->db->from('equipments_asset ea');
        $this->db->join('locations l', 'l.id = ea.location_id', 'left');
        $this->db->join('states s', 's.id = l.state_id', 'left');
        $this->db->group_by('s.id, s.state_name');
        $query = $this->db->get();

        $location_data = $query->result();
        // Prepare and output the final response
        $data = [
            'in_use' => [
                'details' => $result,
                'total' => $total
            ],
            'maintenance' => [
                'details' => $data,
                'total' => $total
            ],
            'faulty' => $result,
            'locations' => $location_data
        ];

        echo json_encode($data);
    }

    public function assetInUse()
    {

        // Step 1: Get all asset types and their associated color
        $this->db->select('asset_types.asset_id, asset_types.name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_type_color.asset_type_id = asset_types.asset_id', 'left');
        $asset_types_query = $this->db->get();
        $asset_types = $asset_types_query->result();

        // Step 2: Get all Serviceable equipment
        $this->db->select('equipment_type')
            ->from('equipments_asset')
            ->where('equipment_status', 'Serviceable');

        $serviceable_query = $this->db->get();
        $serviceable_equipments = $serviceable_query->result();

        // Step 3: Count serviceable equipment per asset type
        $equipment_counts = [];
        foreach ($serviceable_equipments as $equipment) {
            $type_id = $equipment->equipment_type;
            if (!isset($equipment_counts[$type_id])) {
                $equipment_counts[$type_id] = 0;
            }
            $equipment_counts[$type_id]++;
        }

        // Step 4: Merge counts into asset types
        $result = [];
        $total = 0;
        foreach ($asset_types as $type) {
            $count = isset($equipment_counts[$type->asset_id]) ? $equipment_counts[$type->asset_id] : 0;
            if ($count == 0) {
                continue; // Skip this type if in_use_count is 0
            }
            $total += $count;

            $result[] = (object)[
                'asset_type' => $type->name,
                'color' => $type->color,
                'in_use_count' => $count
            ];
        }

        $data = [
            'In Use' => $result,
        ];

        echo json_encode($data);
    }

    public function assetMaintenance()
    {
        // Fetch all unique equipment types
        $equipment_types = $this->db->select('asset_id, name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id', 'left')
            ->get()
            ->result();

        $data = [];

        foreach ($equipment_types as $type) {
            // Total assets of the current equipment type
            $total_assets = $this->db->select('COUNT(*) as total')
                ->from('equipments_asset')
                ->where('equipment_type', $type->asset_id)
                ->get()
                ->row()
                ->total;

            if ($total_assets > 0) {

                // Assets with maintenance_type_id = corrective
                $corrective_maintenance = $this->db->select('COUNT(DISTINCT equipments_asset.equipment_id) as corrective_count')
                    ->from('equipments_asset')
                    ->join(
                        '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                        FROM equipment_maintenance_asset 
                        WHERE maintenance_type_id = "corrective" AND final_status != "complete"
                        GROUP BY equipment_id
                    ) t2',
                        'equipments_asset.equipment_id = t2.equipment_id',
                        'left'
                    )
                    ->where('equipments_asset.equipment_type', $type->asset_id)
                    ->where('equipments_asset.equipment_status', 'MAINTENANCE')
                    ->get()
                    ->row()
                    ->corrective_count ?? 0; // Ensure it defaults to 0 if NULL

                // Assets with maintenance_type_id = preventive
                $default_frequency_year = 2; // Default frequency in years
                $current_date = date('Y-m-d');

                $preventive_maintenance = $this->db->select('COUNT(*) as preventive_count')
                    ->from('equipments_asset ea')
                    ->join(
                        '(SELECT equipment_id, maintenance_date, frequency_year, maintenance_reminder_day 
                        FROM equipments_asset 
                        WHERE frequency_year IS NOT NULL AND maintenance_date IS NOT NULL
                    ) e_freq',
                        'ea.equipment_id = e_freq.equipment_id',
                        'inner'
                    )
                    ->join(
                        '(SELECT equipment_id, final_status, MAX(created_at) AS latest_created_at 
                        FROM equipment_maintenance_asset 
                        WHERE maintenance_type_id = "preventive"
                        GROUP BY equipment_id
                    ) t2',
                        'ea.equipment_id = t2.equipment_id',
                        'left'
                    )
                    ->where('ea.equipment_type', $type->asset_id)
                    ->where('(t2.final_status IS NULL OR t2.final_status != "complete")')
                    ->where("TIMESTAMPDIFF(MONTH, e_freq.maintenance_date, '$current_date') >= (12 / COALESCE(e_freq.frequency_year, $default_frequency_year))")
                    ->get()
                    ->row()
                    ->preventive_count ?? 0; // Ensure it defaults to 0 if NULL

                // **Ignore this data if both maintenance counts are 0**
                if ($corrective_maintenance > 0 || $preventive_maintenance > 0) {
                    $in_use_count = $corrective_maintenance + $preventive_maintenance;
                    $data[] = [
                        'maintenance_type' => $type->name,
                        'color' => $type->color,
                        'in_use_count' => $in_use_count,


                    ];
                }
            }
        }

        $data = [
            'asset Maintenance' => $data,
        ];

        echo json_encode($data);
    }

    public function totalAssets()
    {
        $query = $this->db->select(' asset_type_color.color, asset_types.name AS asset_type, COUNT(*) as in_use_count')
            ->from('equipments_asset')
            ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left')
            ->join('asset_type_color', 'asset_types.asset_id = asset_type_color.asset_type_id', 'left')
            ->group_by('equipments_asset.equipment_type, asset_type_color.color, asset_types.name')
            ->get();
        $equipment_types_data = $query->result();

        // Prepare response data
        $data = [

            'equipment_types' => $equipment_types_data // Include equipment type data
        ];

        echo json_encode($data);
    }

    public function faultyAsset()
    {
        // Step 1: Get all asset types and their color codes
        $this->db->select('asset_types.asset_id, asset_types.name, asset_type_color.color')
            ->from('asset_types')
            ->join('asset_type_color', 'asset_type_color.asset_type_id = asset_types.asset_id', 'left');
        $asset_types_query = $this->db->get();
        $asset_types = $asset_types_query->result();

        // Step 2: Get all UnServiceable equipment
        $this->db->select('equipment_type')
            ->from('equipments_asset')
            ->where('equipment_status', 'UnServiceable');


        $unserviceable_query = $this->db->get();
        $unserviceable_equipments = $unserviceable_query->result();

        // Step 3: Count unserviceable equipment per asset type
        $equipment_counts = [];
        foreach ($unserviceable_equipments as $equipment) {
            $type_id = $equipment->equipment_type;
            if (!isset($equipment_counts[$type_id])) {
                $equipment_counts[$type_id] = 0;
            }
            $equipment_counts[$type_id]++;
        }

        // Step 4: Merge counts into asset types
        $result = [];
        $total = 0;
        foreach ($asset_types as $type) {
            $count = isset($equipment_counts[$type->asset_id]) ? $equipment_counts[$type->asset_id] : 0;
            if ($count == 0) {
                continue; // Skip this type if in_use_count is 0
            }
            $total += $count;

            $result[] = (object)[
                'fault_type' => $type->name,
                'color' => $type->color,
                'in_use_count' => $count
            ];
        }


        // Prepare response data
        $data = [

            'equipment_types' => $result // Include equipment type data
        ];

        echo json_encode($data);
    }

    public function totalLocations()
    {

        $this->db->select("COALESCE(s.state_name, 'Unassigned') AS state_name, COALESCE(MIN(l.colour), '#64748B') AS colour, COUNT(DISTINCT ea.equipment_id) AS in_use_count", false);
        $this->db->from('equipments_asset ea');
        $this->db->join('locations l', 'l.id = ea.location_id', 'left');
        $this->db->join('states s', 's.id = l.state_id', 'left');
        $this->db->group_by('s.id, s.state_name');
        $query = $this->db->get();

        $location_data = $query->result();

        $data = [

            'locations' => $location_data,

        ];

        echo json_encode($data);
    }
}
