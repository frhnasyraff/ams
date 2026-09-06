<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assets extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_equipments')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $manufacturer_name = $this->db->select('*')
            ->from('vendor_manufacturing_number')
            ->get()
            ->result_array();

        $manufacturer_number = $this->db->select('*')
            ->from('vendor_manufacturing_number')
            ->get()
            ->result();

        $drawing_number = $this->db->select('drawing_number')
            ->from('vendor_manufacturing_drawing_number')
            ->get()
            ->result_array();

        $drawing_numbers = $this->db->select('drawing_number')
            ->from('vendor_manufacturing_drawing_number')
            ->get()
            ->result();

        $part_numbers = $this->db->select('id , part_number')
            ->from('vendor_part_number')
            ->get()
            ->result();

        $part_number = $this->db->select('id , part_number')
            ->from('vendor_part_number')
            ->get()
            ->result_array();

        $locations = $this->db->select('*')
            ->from('locations')
            ->get()
            ->result();

        $managedBys = $this->db->select('*')
            ->from('managed_by_add_data')
            ->get()
            ->result();

        $this->db->select('item_types.*, manufacturer_name, part_number'); // Select required fields
        $this->db->from('item_types'); // Main table
        $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number');
        $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer'); // Join condition
        $query = $this->db->get();
        $itemTypes = $query->result();

        $assetStatus = $this->db->select('*')
            ->from('asset_status')
            ->get()
            ->result();

        $states = $this->db->select('*')
            ->from('states')
            ->get()
            ->result();

        $faulty = $this->db->select('*')
            ->from('fault_type_color_code')
            ->get()
            ->result();

        $itemStatus = $this->db->select('*')
            ->from('item_status')
            ->get()
            ->result();

        $storeLocation = $this->db->select('*')
            ->from('store_location')
            ->get()
            ->result();

        $assetTypes = $this->db->select('*')
            ->from('asset_types')
            ->get()
            ->result();


        $totalLocations = $this->db
            ->select('COUNT(DISTINCT states.id) as total')
            ->from('states')
            ->join('equipments_asset', 'states.id = equipments_asset.state_id', 'inner')
            ->get()
            ->row()
            ->total;

        $disposal_methods = $this->db->select('*')
        ->from('disposal_methods')
        ->get()
        ->result();



        $totalAssets = $this->db->count_all('equipments_asset');

        $totalAssetsServiceable = $this->db->where('equipment_status', 'SERVICEABLE')->count_all_results('equipments_asset');
        $UnServiceable_assets = $this->db->where('equipment_status', 'UNSERVICEABLE')->count_all_results('equipments_asset');
        $totalAssetsInMaintenance = $this->db->where('equipment_status', 'MAINTENANCE')->count_all_results('equipments_asset');
        $totalAssetsStore = $this->db->where('equipment_status', 'STORE')->count_all_results('equipments_asset');


        // Store fetched data in the $data array to pass to the view

        // Load views with $data
        $this->load->view('header', [
            'title' => 'Assets',
            'title2' => 'List of Assets',
            'styles' => [
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
                'design/css/datepicker.css',
                'design/css/order-summary-cards.css',
                'design/css/custom-datatable.css'
            ]
        ]);

        $this->load->view('asset-list', [
            'manufacturer_name' => $manufacturer_name,
            'manufacturer_number' => $manufacturer_number,
            'drawing_number' => $drawing_number,
            'drawing_numbers' => $drawing_numbers,
            'part_numbers' => $part_numbers,
            'part_number' => $part_number,
            'locations' => $locations,
            'managedBys' => $managedBys,
            'itemTypes' => $itemTypes,
            'assetStatus' => $assetStatus,
            'states' => $states,
            'faulty' => $faulty,
            'itemStatus' => $itemStatus,
            'storeLocation' => $storeLocation,
            'totalAssets' => $totalAssets,
            'totalAssetsServiceable' => $totalAssetsServiceable,
            'totalAssetsInMaintenance' => $totalAssetsInMaintenance,
            'totalLocations' => $totalLocations,
            'UnServiceable_assets' => $UnServiceable_assets,
            'totalAssetsStore' => $totalAssetsStore,
            'assetTypes' => $assetTypes,
            'disposal_methods' => $disposal_methods

        ]);
        // Pass the $data array directly

        $this->load->view('footer', [
            'scripts' => [
                'design/js/datepicker.js',
                'design/vendor/moment.js-2.24.0/moment.min.js',
                'design/js/assets-list.js?v=5',
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
                'design/js/helper.js',
                // 'design/js/init-map.js',
                'design/js/asset-detail.js',
                'design/js/equipment_asset_map.js'

            ]
        ]);
    }

    public function ItemInfo()
    {
        if ($this->input->get('id')) {

            $id = $this->input->get('id');
            $query = $this->db->get_where('add_asset_items', ['id' => $id]);
            $info = $query->result();

            $this->load->view('header', ['title' => 'Items Info - ' . $info[0]->item_name, 'styles' => ['design/vendor/dropzone/min/dropzone.min.css', 'design/css/multi-select.css', 'design/css/datepicker.css', 'design/css/custom-select.css']]);
            $this->load->view('item-info', ['info' => $info[0]]);
            $this->load->view('footer', ['scripts' => ['design/vendor/dropzone/min/dropzone.min.js', 'design/js/datepicker.js', 'design/js/jquery.multi-select.js', 'design/js/assets-list.js']]);
        }
    }

    public function info()
    {

        // Check if 'id' is provided in the GET request and user has permission
        if ($this->input->get('id') && $this->user_model->has_perm('edit_equipments')) {

            $id =  $_GET['id'];
            $idd = $this->steve->id_decode($id);

            $asset_id = $this->steve->id_decode($this->input->get('id'));
            $this->session->set_userdata('asset_id', $idd);

            // Query for items associated with the asset_id
            $items = $this->db->select('add_asset_items.* ,item_types.calibration, item_types.maintenance ')
                ->from('add_asset_items')
                ->join('equipments_asset', 'equipments_asset.equipment_id = add_asset_items.asset_id', 'left')
                ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
                ->where('add_asset_items.asset_id', $asset_id)
                ->get()
                ->result();

            // Extract the ids ( item ids ) from $items
            $item_ids = array_column($items, 'id');

            // Query for pictures associated with the retrieved items
            $pictures = [];
            if (!empty($item_ids)) {
                $pictures = $this->db->select('*')
                    ->from('item_pictures')
                    ->where_in('add_asset_items_id', $item_ids)
                    ->get()
                    ->result();
            }

            // Extract the ids ( item ids ) from $items
            $item_ids = array_column($items, 'id');

            // Query for the equipment info
            $query = $this->db->select('ea.*, l.name as location_name')
                ->from('equipments_asset ea')
                ->join('locations l', 'l.id = ea.location_id', 'left')
                ->where('ea.equipment_id', $this->steve->id_decode())
                ->get();

            $info = $query->result(); // single row since equipment_id is unique

            $disposal_methods = $this->db->get('disposal_methods')->result();

            $manufacturer_name = $this->db->select('*')->from('vendor_manufacturing_number')->get()->result_array();
            $drawing_numbers = $this->db->select('drawing_number')->from('vendor_manufacturing_drawing_number')->get()->result();
            $part_numbers = $this->db->select('id , part_number')->from('vendor_part_number')->get()->result();
            $managedBys = $this->db->select('*')->from('managed_by_add_data')->get()->result();
            $maintenance = $this->db->select('*')->from('maintenance_type_color_code')->get()->result();
            $this->db->select('item_types.*, manufacturer_name, part_number'); // Select required fields
            $this->db->from('item_types'); // Main table
            $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number');
            $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer'); // Join condition
            $query = $this->db->get();
            $itemTypes = $query->result();
            $states = $this->db->select('*')->from('states')->get()->result();
            $assetStatus = $this->db->select('*')->from('asset_status')->get()->result();
            $assetTypes = $this->db->select('*')->from('asset_types')->get()->result();
            $faulty = $this->db->select('*')
                ->from('fault_type_color_code')
                ->get()
                ->result();



            $manufacturer_number = $this->db->select('*')
                ->from('vendor_manufacturing_number')
                ->get()
                ->result();

            $part_number = $this->db->select('id , part_number')
                ->from('vendor_part_number')
                ->get()
                ->result_array();

            $itemStatus = $this->db->select('*')
                ->from('item_status')
                ->get()
                ->result();

            $storeLocation = $this->db->select('*')
                ->from('store_location')
                ->get()
                ->result();



            // Check if equipment info exists
            if ($info) {
                $user_in_groups = [];

                // Fetch user groups associated with the equipment
                foreach ($this->db->where('equipment_id', intval($info[0]->equipment_id))->get('equipment_group_asset')->result() as $user) {
                    $user_in_groups[] = $user->equipment_group_id;
                }

                // Check if items were found
                if (empty($items)) {
                    $this->session->set_flashdata('item_error', 'No items found for this asset.');
                }

                // Check if pictures were found
                if (empty($pictures)) {
                    $this->session->set_flashdata('picture_error', 'No pictures found for the items.');
                }

                // Load the views
                $this->load->view('header', [
                    'title' => 'Assets - ' . $info[0]->equipment_name,
                    'styles' => [
                        'design/vendor/dropzone/min/dropzone.min.css',
                        'design/css/multi-select.css',
                        'design/css/datepicker.css',
                        'design/css/custom-select.css'
                    ]
                ]);


                $equipments_assetData = $this->db->select('*')
                    ->from('equipments_asset')
                    ->where('equipment_id', $idd)
                    ->get()
                    ->row_array();


                $stateId =  $equipments_assetData['state_id'];



                $stateData = $this->db->select('*')
                    ->from('states')
                    ->where('id', $stateId)
                    ->get()
                    ->row_array();

                $stateName =  $stateData['state_name'];

                // Fetch locations based on the state ID
                $locations = $this->db->select('*')
                    ->from('locations')
                    // Legacy: ->where('state_name', $stateName)
                    ->where('state_id', $stateId)
                    ->get()
                    ->result();


                $ticket = $this->db->select('*')
                    ->from('ticket')
                    // Legacy: ->where('equipment_id', $this->steve->id_decode())
                    ->where('asset_number', $asset_id)
                    ->get()
                    ->result();

                if (empty($ticket)) {
                    $ticket = [];
                }

                $task = $this->db->select('name')
                    ->from('task')
                    ->get()
                    ->result();

                $this->load->view('asset-info', [
                    'info' => $info[0],
                    'user_in_groups' => $user_in_groups,
                    'part_numbers' => $part_numbers,
                    'part_number' => $part_number,
                    'manufacturer_number' => $manufacturer_number,
                    'manufacturer_name' => $manufacturer_name,
                    'locations' => $locations,
                    'managedBys' => $managedBys,
                    'drawing_numbers' => $drawing_numbers,
                    'items' => $items,
                    'pictures' => $pictures,
                    'maintenance' => $maintenance,
                    'itemTypes' => $itemTypes,
                    'states' => $states,
                    'assetStatus' => $assetStatus,
                    'faulty' => $faulty,
                    'ticket' => $ticket,
                    'assetTypes' => $assetTypes,
                    'itemStatus' => $itemStatus,
                    'task' => $task,
                    'storeLocation' => $storeLocation,
                    'disposal_methods' => $disposal_methods,
                    'delete_invoice_url' => site_url('assets/delete_invoice_simple')
                ]);
                $this->load->view('footer', [
                    'scripts' => [
                        'design/vendor/dropzone/min/dropzone.min.js',
                        'design/js/datepicker.js',
                        'design/js/jquery.multi-select.js',
                        'design/js/asset_logs.js',
                        'design/js/assets-list.js'
                    ]
                ]);
            } else {
                redirect('assets?error=Asset not found');
            }
        } else {
            redirect('assets?error=Asset not found or you do not have permission to edit.');
        }
    }


    public function getFaultyType()
    {
        $ticketNumber = $this->input->post('ticket_number');

        if (!$ticketNumber) {
            echo json_encode(['error' => 'Ticket number not received']);
            return;
        }

        // Fetch fault type ID from the ticket
        $this->db->select('*');
        $this->db->from('ticket');
        $this->db->where('ticket_number', $ticketNumber);

        $query = $this->db->get();
        error_log($this->db->last_query()); // Log query

        if ($query->num_rows() == 0) {
            echo json_encode(['error' => 'No ticket found']);
            return;
        }

        $row = $query->row();
        $id = $row->fault_type_id ?? null;

        if (!$id) {
            echo json_encode(['error' => 'Fault type ID missing']);
            return;
        }

        // Fetch fault type details
        $faulty = $this->db->select('*')
            ->from('fault_type_color_code')
            ->where('id', $id)
            ->get();

        error_log($this->db->last_query()); // Log query

        if ($faulty->num_rows() == 0) {
            echo json_encode(['error' => 'No fault type found']);
            return;
        }

        $faulty_row = $faulty->row();

        $result = [
            [
                'value' => $faulty_row->fault_type,
                'label' => $faulty_row->fault_type,
            ]
        ];

        echo json_encode($result);
    }






    public function maintenance()
    {
        if ($this->input->get('id') && $this->user_model->has_perm('edit_equipments')) {

            $query = $this->db->join('equipments_asset', 'equipments_asset.equipment_id = equipment_maintenance_asset.equipment_id', 'left')->get_where('equipment_maintenance_asset', ['equipment_maintenance_asset.equipment_maintenance_id' => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => 'Asset repair - ' . $info[0]->equipment_name, 'styles' => []]);
                $this->load->view('equipment-maintenance-info', ['info' => $info[0]]);
                $this->load->view('footer', ['scripts' => []]);
            } else {
                redirect('assets?error=Asset maintenance not found');
            }
        } else {
            redirect('assets?error=Asset not found or you do not have permission to edit.');
        }
    }

    public function add_maintance_photo()
    {
        $response = [];

        foreach ($_FILES['file']['name'] as $id => $file) {
            if ($_FILES['file']['error'][$id] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']['tmp_name'][$id];

                $prefix = time();

                $name = $prefix . '-' . basename($file);

                $folder = realpath('storage') . '/EQ-' . $this->input->get('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . '/' . $name)) {
                    $response[] = $name;
                }
            }
        }
        die(json_encode(['files' => $response]));
    }

    public function mileage_ajax_list()
    {
        die($this->steve->datatables_mysql('equipment_mileage_asset', ['date_recorded', 'mileage'], [['equipment_id', $this->input->post('id')]]));
    }

    public function consumable_ajax_list()
    {
        die($this->steve->datatables_mysql('equipment_consumables_asset', ['date_recorded', 'consumable_name'], [['equipment_id', $this->input->post('id')]], [['consumables', 'consumables.consumable_id = equipment_consumables_asset.consumable_id']]));
    }

    public function usage_ajax_list()
    {
        // var_dump( $this->input->post( 'id' ) );
        // die();
        die($this->steve->datatables_mysql(
            'vehicle_history_asset',
            [
                'vh_date',
                'vh_date_end',
                'vh_time_start',
                'vh_time_end',
                'vh_location_start',
                'vh_location_end'
                // 'vh_driver_name_ic_number',
                // 'worker_name'
            ],
            [['equipment_id', $this->input->post('id')]]
            // [['workers', 'workers.worker_id = vehicle_history_asset.driver_id']]
        ));
    }

    public function maintenance_ajax_list()
    {
        die($this->steve->datatables_mysql('equipment_maintenance_asset', ['maintenance_date', 'maintenance_notes'], [['equipment_id', $this->input->post('id')]]));
    }

public function new_maintenance_ajax_list()
{
    // Set headers for JSON response
    header('Content-Type: application/json');
    
    $draw = intval($this->input->post('draw'));
    $start = intval($this->input->post('start'));
    $length = intval($this->input->post('length'));
    $searchValue = $this->input->post('search')['value'] ?? '';
    $equipmentId = $this->input->post('id');

    // Debug logging
    error_log("Maintenance AJAX called: Equipment ID = $equipmentId, Draw = $draw");

    // Validate equipment ID
    if (!$equipmentId) {
        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => 0,
            "recordsFiltered" => 0,
            "data" => []
        ]);
        exit;
    }

    // Total records count
    $this->db->from('equipment_maintenance_asset');
    $this->db->where('equipment_id', $equipmentId);
    $totalRecords = $this->db->count_all_results();

    // Filtered records count (with search)
    $this->db->from('equipment_maintenance_asset ema');
    $this->db->join('maintenance_task_done mtd', 'ema.equipment_maintenance_id = mtd.equipment_maintenance_id', 'left');
    $this->db->where('ema.equipment_id', $equipmentId);
    
    if (!empty($searchValue)) {
        $this->db->group_start();
        $this->db->like('ema.ticket_number', $searchValue);
        $this->db->or_like('ema.faulty_type', $searchValue);
        $this->db->or_like('ema.final_status', $searchValue);
        $this->db->or_like('ema.maintenance_type_id', $searchValue);
        $this->db->or_like('mtd.task_done', $searchValue);
        $this->db->or_like('mtd.remarks', $searchValue);
        $this->db->group_end();
    }
    
    $filteredRecords = $this->db->count_all_results();

    // Get data with pagination
    $this->db->select('
        ema.equipment_maintenance_id,
        ema.update_date,
        ema.maintenance_type_id,
        ema.ticket_number,
        ema.faulty_type,
        ema.final_status,
        ema.created_at,
        GROUP_CONCAT(DISTINCT mtd.task_done SEPARATOR ", ") as task_done,
        GROUP_CONCAT(DISTINCT mtd.remarks SEPARATOR " ||| ") as remarks
    ');
    $this->db->from('equipment_maintenance_asset ema');
    $this->db->join('maintenance_task_done mtd', 'ema.equipment_maintenance_id = mtd.equipment_maintenance_id', 'left');
    $this->db->where('ema.equipment_id', $equipmentId);
    $this->db->group_by('ema.equipment_maintenance_id');
    $this->db->order_by('ema.update_date', 'DESC');
    $this->db->limit($length, $start);

    $query = $this->db->get();
    $result = $query->result_array();

    // Format the data
    $formattedData = [];
    foreach ($result as $row) {
        // Format remarks - limit to 50 characters for table display
        $remarks_display = $row['remarks'] ?? '';
        if (strlen($remarks_display) > 50) {
            $remarks_display = substr($remarks_display, 0, 47) . '...';
        }
        
        $formattedData[] = [
            'equipment_maintenance_id' => $row['equipment_maintenance_id'],
            'update_date' => $row['update_date'] ? date('d/m/Y', strtotime($row['update_date'])) : 'N/A',
            'maintenance_type_id' => ucfirst($row['maintenance_type_id'] ?? 'N/A'),
            'ticket_number' => $row['ticket_number'] ?? 'N/A',
            'faulty_type' => $row['faulty_type'] ?? 'N/A',
            'final_status' => $row['final_status'] ?? 'N/A',
            'created_at' => $row['created_at'] ? date('d/m/Y H:i', strtotime($row['created_at'])) : 'N/A',
            'task_done' => $row['task_done'] ?? 'N/A',
            'remarks' => $remarks_display,
            'remarks_full' => $row['remarks'] ?? '' // Store full remarks for modal
        ];
    }

    $response = [
        "draw" => $draw,
        "recordsTotal" => intval($totalRecords),
        "recordsFiltered" => intval($filteredRecords),
        "data" => $formattedData
    ];

    error_log("Returning " . count($formattedData) . " maintenance records");
    
    echo json_encode($response);
    exit;
}

    public function delete()
    {
        if ($this->input->get('id') && $this->user_model->has_perm('edit_maintenance_log_asset')) {
            // Decode the ID properly
            $decoded_id = $this->steve->id_decode($this->input->get('id'));

            if (!$decoded_id) {
                redirect("assets?error=Invalid ID");
                return;
            }

            // Fetch the record
            $query = $this->db->get_where('equipment_maintenance_asset', ["equipment_maintenance_id" => $decoded_id]);
            $info = $query->row(); // Fetch a single record
            $id = $info->equipment_id;

            if ($info && isset($info->equipment_maintenance_id)) {
                $this->db->trans_start(); // Start transaction

                // Delete related records first (from maintenance_task_done)
                $this->db->where('equipment_maintenance_id', $info->equipment_maintenance_id);
                $this->db->delete('maintenance_task_done');

                if ($this->db->affected_rows() === 0) {
                    log_message('error', "Failed to delete from maintenance_task_done for ID: {$info->equipment_maintenance_id}");
                }

                // Delete the main record (from equipment_maintenance_asset)
                $this->db->where('equipment_maintenance_id', $info->equipment_maintenance_id);
                $this->db->delete('equipment_maintenance_asset');

                if ($this->db->affected_rows() === 0) {
                    log_message('error', "Failed to delete from equipment_maintenance_asset for ID: {$info->equipment_maintenance_id}");
                }

                $this->db->trans_complete(); // Complete transaction

                if ($this->db->trans_status() === FALSE) {
                    log_message('error', 'Transaction failed while deleting log for ID: ' . $info->equipment_maintenance_id);

                    redirect('assets/info?id=' . $this->steve->id_encode($id) . '&error=Failed to delete the log');
                } else {
                    redirect('assets/info?id=' . $this->steve->id_encode($id) . '&message=Log deleted successfully');
                }
            } else {
                redirect('assets/info?id=' . $this->steve->id_encode($id) . '&error=Log not found');
            }
        } else {
            redirect('assets/info?id=' . $this->steve->id_encode($id) . '&error=Log not found or you do not have permission to delete.');
        }
    }




    public function asset_list_ajax_list()
    {
        // Define the join to get the equipment_name from the equipments_asset table
        $join = [
            ['equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left'],

        ];

        // Specify the fields to select from both tables
        $fields = [
            'add_asset_items.id',
            'add_asset_items.asset_id',
            'add_asset_items.item_name',
            'add_asset_items.vendor_part_number',
            'add_asset_items.manufacturer_name',
            'add_asset_items.manufacturer_part_number',
            'add_asset_items.manufacturer_drwing_number',
            'add_asset_items.item_picture',
            'equipments_asset.equipment_name'  // Add the equipment_name from the equipments_asset table
        ];

        // Execute the query using the datatables_mysql function with the join
        die($this->steve->datatables_mysql('add_asset_items', $fields, [], $join));
    }

    public function ajax_list()
    {
        $draw = intval($this->input->post('draw'));
        $start = $this->input->post('start');
        $length = $this->input->post('length');
        $searchValue = $this->input->post('search')['value'];

        // Base query
        $this->db->select('equipments_asset.equipment_picture, 
        equipments_asset.equipment_name, 
        equipments_asset.equipment_registration, 
        equipments_asset.equipment_id, 
        asset_types.name, 
        states.state_name AS state_name, 
        equipments_asset.equipment_status, 
        equipments_asset.current_mileage, 
        equipments_asset.next_service_mileage, 
        equipments_asset.next_service_date, 
        equipments_asset.active');
        $this->db->from('equipments_asset');
        $this->db->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left');
        $this->db->join('locations', 'locations.id = equipments_asset.location_id', 'left');
        // Legacy schema used locations.state_name; current schema stores state_id on locations.
        $this->db->join('states', 'states.id = locations.state_id', 'left');

        // Apply filters
        if ($this->input->post('equipment_type')) {
            $this->db->where('equipments_asset.equipment_type', $this->input->post('equipment_type'));
        }
        if ($this->input->post('equipment_group')) {
            $this->db->where('equipments_asset.equipment_status', $this->input->post('equipment_group'));
        }

        // Apply search filter (global search)
        if (!empty($searchValue)) {
            $this->db->group_start();
            $this->db->like('equipments_asset.equipment_name', $searchValue);
            $this->db->or_like('equipments_asset.equipment_registration', $searchValue);
            $this->db->or_like('equipments_asset.equipment_id', $searchValue);
            $this->db->or_like('asset_types.name', $searchValue);
            // Legacy schema used locations.state_name.
            $this->db->or_like('states.state_name', $searchValue);
            $this->db->or_like('equipments_asset.equipment_status', $searchValue);
            $this->db->group_end();
        }

        // Count filtered
        $filteredQuery = clone $this->db;
        $filteredRecords = $filteredQuery->count_all_results();

        // Apply pagination
        $this->db->limit($length, $start);
        $query = $this->db->get();
        $result = $query->result_array();

        // Count total (no filters applied)
        $this->db->from('equipments_asset');
        $totalRecords = $this->db->count_all_results();

        echo json_encode([
            "draw" => $draw,
            "recordsTotal" => $totalRecords,
            "recordsFiltered" => $filteredRecords,
            "data" => $result
        ]);
        exit;
    }



    public function active_ajax_list()
    {
        $search = [['vessel_visit_equipments.operation_date >= CURDATE()']];
        if ($this->input->post('equipment_type')) {
            $search[] = ['equipments_asset.equipment_type', $this->input->post('equipment_type')];
        }
        die($this->steve->datatables_mysql('vessel_visit_equipments', [], $search, [['equipments_asset', 'equipments_asset.equipment_id = vessel_visit_equipments.equipment_id'], ['vessel_visits', 'vessel_visits.vessel_visit_id = vessel_visit_equipments.vessel_visit_id'], ['port_wharfs', 'port_wharfs.port_wharf_id = vessel_visits.port_wharf_id']], 'port_wharfs.wharf_id, equipments_asset.equipment_name, gang, shift, equipments_asset.equipment_id, operation_date'));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by('commodity_code', 'asc')->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like('commodity_code', $this->input->get('term'))->or_like('description', $this->input->get('term'))->group_end()->get_where('operation_types', ['active' => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm('edit_equipments') && $this->input->post('id')) {
            die($this->steve->active_toggle('equipments_asset', 'equipment_id'));
        }
    }

    public function assign_groups()
    {
        if ($this->user_model->has_perm('assign_equipment_groups') && $this->input->post('id')) {

            $equipment_id = intval($this->input->post('id'));
            $this->db->delete('equipment_group_asset', array('equipment_id' => $equipment_id));

            foreach ($this->input->post('groups') as $role) {
                $this->db->set('equipment_id', $equipment_id)->set('equipment_group_id', $role);
                $this->db->insert('equipment_group_asset');
            }
            $this->logs->add('assets', $equipment_id, 'GROUPS_UPDATED', $_POST);
            redirect('assets/info?id=' . $this->steve->id_encode($this->input->post('id')) . '&message=Group(s) association saved successfully');
        }
    }

    public function update_asset()
    {
        // Check if user has permission and if ID is present
        if ($this->user_model->has_perm('edit_equipments') || $this->input->post('id')) {
            // Validate equipment_status value
            $valid_statuses = array('Inuse', 'Maintenance', 'Available', 'Repair', 'Dispose', 'Scrap');
            $equipment_status = $this->input->post('equipment_status');
            if (!in_array($equipment_status, $valid_statuses)) {
                // Invalid equipment_status value
                echo json_encode(array('success' => false, 'error' => 'Invalid equipment status value'));
                return;
            }

            // Set the data to update
            $data = array(

                'equipment_status' => $equipment_status // Use validated equipment_status

            );

            // Set where condition
            $this->db->where('equipment_id', intval($this->input->post('id')));

            // Perform the update
            if ($this->db->update('equipments_asset', $data)) {
                // Log the update
                $this->logs->add('assets', $this->input->post('id'), 'ASSET_UPDATED', $data);
                // Send response indicating success
                echo json_encode(array('success' => true, 'message' => 'Asset was updated successfully.'));
            } else {
                // Send response indicating failure
                echo json_encode(array('success' => false, 'error' => 'Update failed.'));
            }
        } else {
            // Send response indicating no permission or ID is blank
            echo json_encode(array('success' => false, 'error' => 'No permission or ID is blank'));
        }
    }

    public function update()
    {
        $default_frequency_year = 2;
        $default_reminder_days = 30;

        // Check user permission and ensure 'id' is provided
        if ($this->user_model->has_perm('edit_equipments') && $this->input->post('id')) {
            if (empty($this->input->post('equipment_type'))) {
                redirect("assets?error=Please Fill All Fields");
            }
            $asset_id = $this->input->post('id');
            $this->load->model('asset_logs');



            $invoice_file_name = $existing['invoice'] ?? '';
        
                if (isset($_FILES['invoice']) && $_FILES['invoice']['error'] == UPLOAD_ERR_OK) {
                    $invoice_tmp_name = $_FILES['invoice']['tmp_name'];
                    $invoice_original_name = basename($_FILES['invoice']['name']);
                    $invoice_file_name = time() . "-invoice-" . $invoice_original_name;
                    $target_folder = FCPATH . "uploads/asset_invoice/";
                    
                    // Create folder if not exists
                    if (!is_dir($target_folder)) {
                        mkdir($target_folder, 0777, true);
                    }
                    
                    // Delete old invoice if exists
                    if (!empty($existing['invoice_file'])) {
                        $old_file = $target_folder . $existing['invoice_file'];
                        if (file_exists($old_file)) {
                            unlink($old_file);
                        }
                    }
                    
                    // Move uploaded file
                    move_uploaded_file($invoice_tmp_name, $target_folder . $invoice_file_name);
                }


            // --- Process and Update Items ---
            $items_id = $this->input->post('item_id');
            $items = $this->input->post('item');
            $vendor_part_numbers = $this->input->post('vendor_part_number');
            $manufacturer_names = $this->input->post('manufacturer_name');
            $manufacturer_drwing_numbers = $this->input->post('manufacturer_drawing_number');
            if ($manufacturer_drwing_numbers === null) {
                $manufacturer_drwing_numbers = $this->input->post('manufacturer_drwing_number');
            }
            $drawingColumn = $this->db->field_exists('manufacturer_drawing_number', 'add_asset_items')
                ? 'manufacturer_drawing_number' : 'manufacturer_drwing_number';
            $manufacturer_part_numbers = $this->input->post('manufacturer_part_number');
            $items_status = $this->input->post('item_status');
            $item_types = $this->input->post('item_type');
            $faulty_type_item = $this->input->post('faulty_type_item');
            $store_location_item = $this->input->post('store_location_item');
            $calibration_date_item = $this->input->post('calibration_date_item');
            $frequency_day_item = $this->input->post('frequency_day_item');
            $reminder_day_item = $this->input->post('reminder_day_item');
            $maintenance_date_item = $this->input->post('maintenance_date_item');
            $frequency_year_item = $this->input->post('frequency_day_item') ?: $default_frequency_year;
            $maintenance_reminder_day_item = $this->input->post('reminder_day_item') ?: $default_reminder_days;


            if (is_array($items) && !empty($items)) {
                foreach ($items as $index => $item) {
                    if (!isset($items_id[$index])) {
                        log_message('error', "No item ID found for index $index. Skipping.");
                        continue;
                    }

                    $item_id = $items_id[$index];
                    $item_data = [
                        'asset_id' => $asset_id,
                        'item_name' => trim($item),
                        'vendor_part_number' => isset($vendor_part_numbers[$index]) ? trim($vendor_part_numbers[$index]) : null,
                        'manufacturer_name' => isset($manufacturer_names[$index]) ? trim($manufacturer_names[$index]) : null,
                        $drawingColumn => isset($manufacturer_drwing_numbers[$index]) ? trim($manufacturer_drwing_numbers[$index]) : null,
                        'manufacturer_part_number' => isset($manufacturer_part_numbers[$index]) ? trim($manufacturer_part_numbers[$index]) : null,
                        'item_status_id' => isset($items_status[$index]) ? trim($items_status[$index]) : null,
                        'item_type_id' => isset($item_types[$index]) ? trim($item_types[$index]) : null,
                        'faulty_type_id' => isset($faulty_type_item[$index]) && $faulty_type_item[$index] !== '' ? trim($faulty_type_item[$index]) : null,
                        'store_location_id' => isset($store_location_item[$index]) ? trim($store_location_item[$index]) : null,
                        'calibration_date' => isset($calibration_date_item[$index]) && $calibration_date_item[$index] !== '' ? trim($calibration_date_item[$index]) : null,
                        'frequency_day' => isset($frequency_day_item[$index]) && $frequency_day_item[$index] !== '' ? trim($frequency_day_item[$index]) : null,
                        'reminder_day' => isset($reminder_day_item[$index]) && $reminder_day_item[$index] !== '' ? trim($reminder_day_item[$index]) : null,
                        'maintenance_date' => isset($maintenance_date_item[$index]) && $maintenance_date_item[$index] !== '' ? trim($maintenance_date_item[$index]) : null,
                        'frequency_year' => isset($frequency_year_item[$index]) && $frequency_year_item[$index] !== '' ? trim($frequency_year_item[$index]) : $default_frequency_year,
                        'maintenance_reminder_day' => isset($maintenance_reminder_day_item[$index]) && $maintenance_reminder_day_item[$index] !== '' ? trim($maintenance_reminder_day_item[$index]) : $default_reminder_days,
                    ];

                    // Do not clear an existing drawing number when the field was not submitted.
                    if (!is_array($manufacturer_drwing_numbers) || !array_key_exists($index, $manufacturer_drwing_numbers)) {
                        unset($item_data[$drawingColumn]);
                    }
                    $original_item = $this->db
                        ->select('add_asset_items.*, item_status.name AS item_status_name')
                        ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                        ->get_where('add_asset_items', ['add_asset_items.id' => $item_id])
                        ->row_array();

                    if (!$original_item) {
                        log_message('error', "Item ID $item_id not found in DB.");
                        continue;
                    }

                    $changed_fields = [];
                    foreach ($item_data as $key => $new_value) {
                        $old_value = isset($original_item[$key]) ? trim((string)$original_item[$key]) : '';
                        $new_value = trim((string)$new_value);
                        if ($old_value !== $new_value) {
                            $changed_fields[$key] = ['old' => $old_value, 'new' => $new_value];
                        }
                    }

                    // Convert only item_status_id to readable name
                    if (isset($changed_fields['item_status_id'])) {
                        $old_name = $original_item['item_status_name'] ?? '';
                        $new_name = $this->db
                            ->select('name')
                            ->from('item_status')
                            ->where('id', $changed_fields['item_status_id']['new'])
                            ->get()
                            ->row('name');

                        $changed_fields['item_status'] = [
                            'old' => $old_name ?: '',
                            'new' => $new_name ?: ''
                        ];
                        unset($changed_fields['item_status_id']);
                    }

                    if (empty($changed_fields)) {
                        log_message('info', "No changes for item ID: $item_id. Skipping.");
                        continue;
                    }

                    $this->db->where('id', $item_id)->update('add_asset_items', $item_data);
                    if (!$this->db->affected_rows()) {
                        log_message('error', "Failed to update add_asset_items for item ID: $item_id");
                    }

                    $log_description = "Updated component '{$item_data['item_name']}'\n";
                    foreach ($changed_fields as $field => $values) {
                        $log_description .= "- {$field}: '" . $values['old'] . "' â†’ '" . $values['new'] . "'\n";
                    }

                    $this->asset_logs->add('assets/info', $item_id, 'Component_Updated', $log_description);
                    log_message('info', "Successfully updated and logged item ID: $item_id");
                }
            }


            // --- Process and Update Main Asset ---
            $frequency_year = $this->input->post('frequency_year') ?: $default_frequency_year;
            if ($this->input->post('maintenance_date') && $frequency_year) {
                $maintenance_date_str = $this->input->post('maintenance_date');
                $interval_duration_months = 12 / $frequency_year;
                $next_maintenance_date = date('Y-m-d', strtotime("+$interval_duration_months months", strtotime($maintenance_date_str)));

                $exists = $this->db->where('equipment_id', intval($asset_id))->get('next_maintenance_date')->num_rows() > 0;

                if ($exists) {
                    $this->db->where('equipment_id', intval($asset_id))->update('next_maintenance_date', ['maintenance_date' => $maintenance_date_str]);
                } else {
                    $this->db->insert('next_maintenance_date', ['equipment_id' => $asset_id, 'maintenance_date' => $maintenance_date_str]);
                }
            }

            $existing = $this->db->get_where('equipments_asset', ['equipment_id' => $asset_id])->row_array();
            $equipment_data = [
                'equipment_name' => $this->input->post('name'),
                'serial_number' => $this->input->post('serial_number'),
                'equipment_registration' => $this->input->post('equipment_registration'),
                'equipment_type' => $this->input->post('equipment_type') ?: null,
                'date_installed' => $this->input->post('date_installed') ?: null,
                'equipment_manufacturer' => $this->input->post('equipment_manufacturer') ?: null,
                'purchase_date' => $this->input->post('purchase_date') ?: null,
                'company_name' => $this->input->post('company_name') ?: null,
                'invoice_file' => $invoice_file_name,
                'purchase_date' => $this->input->post('purchase_date') ? $this->steve->to_date($this->input->post('purchase_date')) : null,
                'price_of_purchase' => $this->input->post('price_of_purchase') ?: null,
                'equipment_status' => $this->input->post('equipment_status') ?: null,
                'location_id' => $this->input->post('location_id') ?: null,
                'state_id' => $this->input->post('state_id') ?: null,
                'ownership' => $this->input->post('ownership') ?: null,
                'equipment_notes' => $this->input->post('notes'),
                'equipment_safe_load' => $this->input->post('safe_load'),
                'faulty_type_id' => ($this->input->post('faulty_type') ?: null),
                'store_location_id' => $this->input->post('store_location') ?: null,
                'disposal_method_id' => $this->input->post('disposal_method_id') ?: null,
                'useful_life_years' => $this->input->post('useful_life_years') ?: null,
                'salvage_value' => $this->input->post('salvage_value') ?: null,
                'calibration_date' => $this->input->post('calibration_date') ?: null,
                'frequency_day' => $this->input->post('frequency_day') ?: null,
                'reminder_day' => $this->input->post('reminder_day') ?: null,
                'vendor_part_number_id' => $this->input->post('vendor_part_number_id') ?: null,
                'maintenance_date' => $this->input->post('maintenance_date') ?: null,
                'frequency_year' => $this->input->post('frequency_year') ?: $default_frequency_year,
                'maintenance_reminder_day' => $this->input->post('maintenance_reminder_day') ?: $default_reminder_days,
            ];

            $changed_fields_asset = [];
            foreach ($equipment_data as $key => $new_val) {
                $old_val = $existing[$key] ?? null;
                if ($new_val != $old_val) {
                    $changed_fields_asset[$key] = ['old' => $old_val, 'new' => $new_val];
                }
            }

            if (!empty($changed_fields_asset)) {
                $this->db->where("equipment_id", intval($asset_id))->update("equipments_asset", $equipment_data);
                if ($this->db->affected_rows()) {
                    $log_description_asset = "Updated asset:\n";
                    foreach ($changed_fields_asset as $field => $values) {
                        $log_description_asset .= "- {$field}: '" . $values['old'] . "' â†’ '" . $values['new'] . "'\n";
                    }
                    $this->asset_logs->add("assets/info", $asset_id, "Asset_Updated", $log_description_asset);
                    $this->logs->add("assets/info", $asset_id, "ASSET_UPDATED", $_POST);
                    log_message('info', "Successfully updated and logged main asset ID: $asset_id");
                } else {
                    log_message('error', 'Asset update failed: ' . $this->db->last_query());
                }
            }

            // --- Final Redirect (only after all updates are complete) ---
            redirect("assets/index?message=Asset was updated successfully.");
        } else {
            redirect("assets/index?error=No permission or ID is blank");
        }
    }




    public function updatePurchase()
    {
        if ($this->input->post('date_of_purchase')) {
            $this->db->set('date_of_purchase', $this->steve->to_date($this->input->post('date_of_purchase')));
        }
        if ($this->input->post('price_of_purchase')) {
            $this->db->set('price_of_purchase', $this->input->post('price_of_purchase'));
        }

        if ($this->input->post('purchase_origin')) {
            $this->db->set('purchase_origin', $this->input->post('purchase_origin'));
        }

        if ($this->input->post('contact_number')) {
            $this->db->set('contact_number', $this->input->post('contact_number'));
        }
        if ($this->input->post('person_in_contact')) {
            $this->db->set('person_in_contact', $this->input->post('person_in_contact'));
        }
        if ($this->input->post('status')) {
            $this->db->set('status', $this->input->post('status'));
        }
        if ($this->input->post('purchased_by')) {
            $this->db->set('purchased_by', $this->input->post('purchased_by'));
        }
        if ($this->input->post('purchase_price')) {
            $this->db->set('purchase_price', $this->input->post('purchase_price'));
        }

        $this->db->set('equipment_id', $this->steve->id_decode($this->input->post('equipment_id')));

        if ($this->user_model->has_perm("edit_purchase_history") || $this->user_model->has_perm("add_purchase_history")) {
            if ($this->input->post('id')) {
                $this->db->where("equipment_id ", intval($this->input->post('id')));
                if ($this->db->update("equipments_asset")) {
                    $this->logs->add("assets/info", $this->input->post('id'), "PURCHASE_HISTORY_UPDATED", $_POST);
                    redirect("assets/index?message=Purchase history was updated successfully.");
                } else {
                    redirect("assets/index?error=Update failed.");
                }
            } else {
                if ($this->db->insert("equipments_asset")) {
                    $this->logs->add("assets/info", $this->input->post('id'), "PURCHASE_HISTORY_ADDED", $_POST);
                    redirect("assets/index?message=Purchase history was added successfully.");
                } else {
                    redirect("assets/index?error=Added failed.");
                }
            }
        } else {
            redirect("assets/index?error=No permission or ID is blank");
        }
    }

    public function add_mileage()
    {
        if ($this->input->post('mileage') && $this->input->post('record_date')) {
            $this->db->set('mileage', $this->input->post('mileage'));
            $this->db->set('date_recorded', $this->steve->to_date($this->input->post('record_date')));
            $this->db->set('equipment_id', $this->input->post('id'));

            if ($this->db->insert('equipment_mileage_asset')) {
                $this->logs->add("assets/info", $this->input->post('id'), "MILEAGE_ADDED", $_POST);

                $last = $this->db->limit(1, 0)->order_by("date_recorded", "desc")->get_where("equipment_mileage_asset", ['equipment_id' => $this->input->post('id')])->result();

                $this->db->reset_query();

                $this->db->set("current_mileage", $last[0]->mileage);
                $this->db->where("equipment_id", $last[0]->equipment_id);
                if ($this->db->update('equipments_asset')) {
                    redirect("assets?message=Added mileage successfully");
                } else {
                    redirect("assets?error=Adding mileage failed");
                }
            } else {
                redirect("assets?error=Adding mileage failed");
            }
        } else {
            redirect("assets?error=No permission to add equipment");
        }
    }

    public function add_consumable()
    {
        if ($this->input->post('id') && $this->input->post('consumable_id')) {

            $this->db->set('quantity', $this->input->post('consumable_quantity'));
            $this->db->set('date_recorded', $this->steve->to_date($this->input->post('consumable_date')));
            $this->db->set('equipment_id', $this->input->post('id'));
            $this->db->set('consumable_id', $this->input->post('consumable_id'));

            if ($this->db->insert('equipment_consumables_asset')) {
                $this->logs->add("assets/info", $this->input->post('consumable_id'), "CONSUMABLE_ADDED", $_POST);

                $consumable = $this->db->get_where('consumables', ["consumable_id" => $this->input->post('consumable_id')])->result();

                $this->db->reset_query();

                $this->db->set("consumable_stock", $consumable[0]->consumable_stock - $this->input->post('consumable_quantity'));
                $this->db->where("consumable_id", $this->input->post('consumable_id'));

                if ($this->db->update('consumables')) {
                    redirect("assets/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=Added consumption successfully#nav-consumable");
                } else {
                    redirect("assets?error=Adding consumption failed");
                }
            } else {
                redirect("assets?error=Adding consumption failed");
            }
        } else {
            redirect("assets?error=No permission to add equipment consumption");
        }
    }

    public function add_usage()
    {
        if ($this->input->post('id')) {

            $start_time1 = "";
            $end_time1   = "";
            if ($this->input->post('vh_time_start') && $this->input->post('vh_time_start') !== null && $this->input->post('vh_time_start') != "") {
                $start_time1 =  date('h:ia', strtotime($this->input->post('vh_time_start')));
            }
            if ($this->input->post('vh_time_end') && $this->input->post('vh_time_end') !== null && $this->input->post('vh_time_end') != "") {
                $end_time1 =  date('h:ia', strtotime($this->input->post('vh_time_end')));
            }

            $this->db->set('vh_time_start', $start_time1);
            $this->db->set('vh_time_end', $end_time1);
            $this->db->set('vh_date', $this->input->post('vh_date'));
            $this->db->set('vh_date_end', $this->input->post('vh_date_end'));
            $this->db->set('equipment_id', $this->input->post('id'));
            $this->db->set('vh_location_start', $this->input->post('vh_location_start'));
            $this->db->set('vh_location_end', $this->input->post('vh_location_end'));

            if ($this->db->insert('vehicle_history_asset')) {
                $this->logs->add("assets/info", $this->input->post('vh_id'), "ASSET_USAGE_ADDED", $_POST);

                redirect("assets/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=Added Asset Usage successfully#nav-usage");
            } else {
                redirect("assets?error=Adding Asset Usage failed");
            }
        } else {
            redirect("assets?error=No permission to add Asset Usage history.");
        }
    }

    public function add_scheduled_maintenance()
    {
        if ($this->input->post("id")) {
            if ($this->input->post('next_maintenance_date')) {
                $this->db->set('next_service_date', $this->steve->to_date($this->input->post('next_maintenance_date')));
            }
            if ($this->input->post('next_maintenance_mileage')) {
                $this->db->set('next_service_mileage', $this->input->post('next_maintenance_mileage'));
            }
            $this->db->where("equipment_id", $this->input->post('id'));
            if ($this->db->update('equipments_asset')) {
                $this->logs->add("assets/info", $this->input->post('id'), "SCHEDULED_MAINTENANCE_ADDED", $_POST);
                redirect("assets/?message=Added scheduled maintenance details#nav-maintenance");
            } else {
                redirect("assets?error=No permission to add equipment maintenance");
            }
        }
    }

    public function add_maintenance()
    {
        if ($this->input->post('in_out')) {
            $this->db->set('equipment_id', $this->input->post('id'));
            $this->db->set('in_out', $this->input->post('in_out'));
            $this->db->set('maintenance_date', $this->steve->to_date($this->input->post('maintenance_date')));
            if ($this->input->post('maintenance_mileage')) {
                $this->db->set('maintenance_mileage', $this->input->post('maintenance_mileage'));
            }
            $this->db->set('maintenance_files', $this->input->post('maintenance_files'));
            $this->db->set('maintenance_notes', $this->input->post('maintenance_notes'));

            if ($this->db->insert('equipment_maintenance_asset')) {
                $this->logs->add("assets/info", $this->input->post('id'), "MAINTENANCE_ADDED", $_POST);
                $this->db->reset_query();

                $last_maintenance = $this->db->order_by("maintenance_date", "desc")->limit(1, 0)->get_where("equipment_maintenance_asset", ['equipment_id' => $this->input->post('id')])->result();

                if ($last_maintenance && count($last_maintenance)) {
                    $this->db->reset_query();

                    if ($last_maintenance[0]->in_out == "In maintenance") {
                        $this->db->set("equipment_status", "Maintenance");
                        $this->db->set("active", 0);
                    } else {
                        $this->db->set("equipment_status", "In use");
                        $this->db->set("active", 1);
                    }

                    $this->db->where("equipment_id", $this->input->post('id'));
                    $this->db->update('equipments_asset');
                }
                redirect("assets/info?id=" . $this->steve->id_encode($this->input->post("id")) . "&message=Added maintenance details#nav-maintenance");
            } else {
                redirect("assets/info?id=" . $this->steve->id_encode($this->input->post("id")) . "&error=Adding maintenance failed#nav-maintenance");
            }
        } else {
            redirect("assets?error=No permission to add equipment maintenance");
        }
    }

    public function add()
    {
        $default_frequency_year = 2;
        $default_reminder_days = 30;

        if (!$this->user_model->has_perm("add_equipments")) {
            redirect("assets?error=You don't have permission to add equipment");
            return;
        }

        if (!$this->input->post()) {
            // Direct visits to /assets/add are legacy-invalid; keep the add flow as POST from the modal.
            redirect("assets?error=Please use the New Asset form to add an asset");
            return;
        }

        $this->load->model('asset_logs');


        $invoice_file_name = '';
        if (isset($_FILES['invoice']) && $_FILES['invoice']['error'] == UPLOAD_ERR_OK) {
            $invoice_tmp_name = $_FILES['invoice']['tmp_name'];
            $invoice_file_name = time() . "-invoice-" . basename($_FILES['invoice']['name']);
            $target_folder = realpath("uploads/asset_invoice");
            @mkdir($target_folder, 0777, true);
            
            // Get file extension
            $file_ext = pathinfo($_FILES['invoice']['name'], PATHINFO_EXTENSION);
            $allowed_extensions = array('pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png');
            
            if (in_array(strtolower($file_ext), $allowed_extensions)) {
                move_uploaded_file($invoice_tmp_name, $target_folder . '/' . $invoice_file_name);
            }
        }

        // 1. Prepare and insert main asset data
        $equipment_data = [
            'equipment_name' => $this->input->post('name'),
            'equipment_registration' => $this->input->post('equipment_registration'),
            'date_installed' => date('Y-m-d', strtotime(str_replace('/', '-', $this->input->post('date_installed')))),
            'equipment_type' => $this->input->post('equipment_type') ?: null,
            'equipment_manufacturer' => $this->input->post('equipment_manufacturer') ?: null,
            'equipment_notes' => $this->input->post('notes'),
            'equipment_safe_load' => $this->input->post('safe_load'),
            'current_mileage' => $this->input->post('current_mileage') ?: null,
            'purchase_date' => $this->input->post('purchase_date') ?: date('Y-m-d'),
            'company_name' => $this->input->post('company_name') ?: null,
            'invoice_file' => $invoice_file_name,
            'price_of_purchase' => $this->input->post('price_of_purchase') ?: null,
            'service_every_mileage' => $this->input->post('service_every_mileage') ?: null,
            'next_service_mileage' => $this->input->post('next_service_mileage') ?: null,
            'last_service_date' => $this->input->post('last_service_date') ? $this->steve->to_date($this->input->post('last_service_date')) : null,
            'service_interval_weeks' => $this->input->post('service_interval_weeks') ?: 0,
            'next_service_date' => $this->input->post('next_service_date') ? $this->steve->to_date($this->input->post('next_service_date')) : null,
            'branch_office_id' => $this->input->post('branch_office_id') ?: null,
            'ownership' => $this->input->post('ownership') ?: null,
            'state_id' => $this->input->post('state_id') ?: null,
            'location_id' => $this->input->post('location_id') ?: null,
            'vendor_part_number_id' => $this->input->post('vendor_part_number_id') ?: null,
            'serial_number' => $this->input->post('serial_number') ?: null,
            'manufacturer_drwing_number' => $this->input->post('drawing_number') ?: null,
            'store_location_id' => $this->input->post('store_location') ?: null,
            'disposal_method_id' => $this->input->post('disposal_method_id') ?: null,
            'useful_life_years' => $this->input->post('useful_life_years') ?: null,
            'salvage_value' => $this->input->post('salvage_value') ?: null,
            'calibration_date' => $this->input->post('calibration_date') ?: null,
            'frequency_day' => $this->input->post('frequency_day') ?: null,
            'reminder_day' => $this->input->post('reminder_day') ?: null,
            'maintenance_date' => $this->input->post('maintenance_date') ?: null,
            'frequency_year' => $this->input->post('frequency_year') ?: $default_frequency_year,
            'maintenance_reminder_day' => $this->input->post('maintenance_reminder_day') ?: $default_reminder_days,
            'faulty_type_id' => $this->input->post('faulty_type') ?: null,
            // Legacy used lowercase 'faulty', which does not match the equipment_status enum cleanly.
            'equipment_status' => $this->input->post('faulty_type') ? 'FAULTY' : ($this->input->post('equipment_status') ?: null)
        ];

        if ($this->db->insert('equipments_asset', $equipment_data)) {
            $asset_id = $this->db->insert_id();

            // Log the creation of the new asset
            $asset_log_description = "New asset '{$equipment_data['equipment_name']}':\n";
            foreach ($equipment_data as $key => $value) {
                $asset_log_description .= "- {$key}: {$value}\n";
            }
            $this->asset_logs->add('equipments_asset', $asset_id, 'Asset_Added', $asset_log_description);
            $this->logs->add("assets/info", $asset_id, "ASSET_ADDED", $equipment_data);

            // 2. Handle Maintenance Date
            if ($this->input->post('maintenance_date') && $equipment_data['frequency_year']) {
                $maintenance_date_str = $this->input->post('maintenance_date');
                $interval_duration_months = 12 / $equipment_data['frequency_year'];
                $next_maintenance_date = date('Y-m-d', strtotime("+$interval_duration_months months", strtotime($maintenance_date_str)));
                $this->db->insert('next_maintenance_date', [
                    'equipment_id' => $asset_id,
                    'maintenance_date' => $maintenance_date_str
                ]);
            }

            // 3. Handle Asset Picture Upload
            if (isset($_FILES['equipment_picture']) && $_FILES['equipment_picture']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['equipment_picture']['tmp_name'];
                $file_name = time() . "-" . basename($_FILES['equipment_picture']['name']);
                $target_folder = realpath("storage") . "/Asset-" . $asset_id;
                @mkdir($target_folder, 0777, true);

                if (move_uploaded_file($tmp_name, $target_folder . '/' . $file_name)) {
                    $this->db->where('equipment_id', $asset_id)->update('equipments_asset', ['equipment_picture' => $file_name]);
                }
            }

            // 4. Handle and log items
            $items_names = $this->input->post('item');
            if (!empty($items_names) && is_array($items_names)) {
                $base_upload_directory_path = realpath('storage') . '/Asset-item-' . $asset_id;
                @mkdir($base_upload_directory_path, 0777, true);
                $item_pictures = $_FILES['item_picture'];

                foreach ($items_names as $index => $item_name) {
                    if (empty($item_name)) {
                        continue; // Skip empty item rows
                    }

                    // Prepare item data
                    $item_data = [
                        'asset_id' => $asset_id,
                        'item_name' => trim($item_name),
                        'vendor_part_number' => $this->input->post('vendor_part_number')[$index] ?? null,
                        'manufacturer_name' => $this->input->post('manufacturer_name')[$index] ?? null,
                        'manufacturer_drwing_number' => $this->input->post('manufacturer_drwing_number')[$index] ?? null,
                        'manufacturer_part_number' => $this->input->post('manufacturer_part_number')[$index] ?? null,
                        'item_type_id' => $this->input->post('item_type')[$index] ?? null,
                        'faulty_type_id' => $this->input->post('faulty_type_item')[$index] ?: null,
                        'item_status_id' => $this->input->post('item_status')[$index] ?? null,
                        'store_location_id' => $this->input->post('store_location_item')[$index] ?? null,
                        'calibration_date' => $this->input->post('calibration_date_item')[$index] ?? null,
                        'frequency_day' => $this->input->post('frequency_day_item')[$index] ?? null,
                        'reminder_day' => $this->input->post('reminder_day_item')[$index] ?? null,
                        'maintenance_date' => $this->input->post('maintenance_date_item')[$index] ?? null,
                        'frequency_year' => $this->input->post('frequency_year_item')[$index] ?: $default_frequency_year,
                        'maintenance_reminder_day' => $this->input->post('maintenance_reminder_day_item')[$index] ?: $default_reminder_days,
                    ];

                    // Handle item picture upload
                    if (isset($item_pictures['name'][$index]) && !empty($item_pictures['name'][$index])) {
                        $item_picture_name = time() . "-" . basename($item_pictures['name'][$index]);
                        $item_picture_path = $base_upload_directory_path . '/' . $item_picture_name;
                        if (move_uploaded_file($item_pictures['tmp_name'][$index], $item_picture_path)) {
                            $item_data['item_picture'] = $item_picture_name;
                        }
                    }

                    // Insert item into DB
                    if ($this->db->insert('add_asset_items', $item_data)) {
                        $item_id = $this->db->insert_id();

                        // Fetch readable status name for logging
                        $item_status_name = null;
                        if (!empty($item_data['item_status_id'])) {
                            $status = $this->db->select('name')->get_where('item_status', ['id' => $item_data['item_status_id']])->row_array();
                            $item_status_name = $status['name'] ?? null;
                        }

                        // Build log description
                        $item_log_description = "New component '{$item_data['item_name']}' created for asset ID {$asset_id} with ID: {$item_id}.\n";
                        foreach ($item_data as $key => $value) {
                            $display_value = $value;
                            if ($key === 'item_status_id' && $item_status_name) {
                                $display_value = $item_status_name; // Replace ID with name for logs
                            }
                            $item_log_description .= "- {$key}: 'NULL' â†’ '{$display_value}'\n";
                        }

                        // Save to asset logs
                        $this->asset_logs->add('assets/info', $item_id, 'Component_Updated', $item_log_description);
                    }
                }


                redirect("assets?message=Equipment and Item Added Successfully");
            }

            redirect("assets?message=Equipment Added Successfully");
        } else {
            redirect("assets?error=Adding Asset failed");
        }
    }


public function uploadExcel()
{
    if (!$this->user_model->has_perm("add_equipments")) {
        redirect("assets?error=You don't have permission to upload Excel");
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        redirect("assets?error=Invalid request method");
        return;
    }

    if (!isset($_FILES['excel_file']) || $_FILES['excel_file']['error'] !== UPLOAD_ERR_OK) {
        redirect("assets?error=Please select a valid Excel file");
        return;
    }

    require_once APPPATH . '../vendor/autoload.php';

    $allowed_extensions = ['xlsx', 'xls', 'csv'];
    $ext = strtolower(pathinfo($_FILES['excel_file']['name'], PATHINFO_EXTENSION));

    if (!in_array($ext, $allowed_extensions)) {
        redirect("assets?error=Invalid file type");
        return;
    }

    $upload_dir = FCPATH . 'uploads/excel/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_path = $upload_dir . time() . '_' . uniqid() . '.' . $ext;
    move_uploaded_file($_FILES['excel_file']['tmp_name'], $file_path);

    try {

        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file_path);
        $sheet = $spreadsheet->getSheetByName('ASSETS') ?? $spreadsheet->getActiveSheet();

        $highestRow = $sheet->getHighestDataRow();
        $highestColumn = $sheet->getHighestDataColumn();

        // âœ… Header mapping - FIXED: Use exact case matching
        $headers = $sheet->rangeToArray("A1:{$highestColumn}1", null, true, false)[0];
        $headerMap = [];
        foreach ($headers as $index => $header) {
            // Keep original case for keys to match Excel headers exactly
            $headerMap[trim($header)] = $index;
        }

        // âœ… DEBUG: Check what headers were actually found
        // echo '<pre>Headers found: '; print_r($headers); echo '</pre>';
        // echo '<pre>Header map: '; print_r($headerMap); echo '</pre>'; exit;

        $success = 0;
        $failed = 0;
        $errors = [];

        for ($row = 2; $row <= $highestRow; $row++) {

            $rowData = $sheet->rangeToArray("A{$row}:{$highestColumn}{$row}", null, true, false)[0];
            if (empty(trim(implode('', $rowData)))) continue;

            try {

                // Check if headers exist - use exact case from Excel
                if (!isset($headerMap['equipment_name'])) {
                    throw new Exception("Missing 'equipment_name' column in Excel");
                }
                if (!isset($headerMap['equipment_registration'])) {
                    throw new Exception("Missing 'equipment_registration' column in Excel");
                }

                $equipment_name = trim($rowData[$headerMap['equipment_name']] ?? '');
                $equipment_registration = trim($rowData[$headerMap['equipment_registration']] ?? '');

                // âœ… DEBUG: Check what values are being read
                // echo "Row {$row}: name='{$equipment_name}', reg='{$equipment_registration}'<br>";

                if ($equipment_name === '' || $equipment_registration === '') {
                    $failed++;
                    $errors[] = "Row {$row}: Missing equipment name or registration";
                    continue;
                }

                // Prepare asset data - use exact column names from Excel
                $asset_data = [
                    'equipment_name'        => $equipment_name,
                    'equipment_registration'=> $equipment_registration,
                    'serial_number'         => isset($headerMap['serial_number']) 
                                                ? trim($rowData[$headerMap['serial_number']] ?? '') : '',
                    'equipment_type'        => isset($headerMap['equipment_type']) 
                                                ? intval($rowData[$headerMap['equipment_type']] ?? 0) : 0,
                    'equipment_manufacturer'=> isset($headerMap['equipment_manufacturer']) 
                                                ? intval($rowData[$headerMap['equipment_manufacturer']] ?? 0) : 0,
                    'purchase_date'         => isset($headerMap['purchase_date']) && !empty($rowData[$headerMap['purchase_date']])
                                                ? date('Y-m-d', strtotime($rowData[$headerMap['purchase_date']])) : null,
                    'company_name'          => isset($headerMap['company_name']) 
                                                ? trim($rowData[$headerMap['company_name']] ?? '') : '',
                    'manufacturer_drwing_number' => isset($headerMap['manufacturer_drwing_number']) 
                                                ? trim($rowData[$headerMap['manufacturer_drwing_number']] ?? '') : '',
                    'price_of_purchase'     => isset($headerMap['price_of_purchase']) 
                                                ? floatval($rowData[$headerMap['price_of_purchase']] ?? 0) : 0,
                    'equipment_status'      => isset($headerMap['equipment_status']) 
                                                ? strtoupper(trim($rowData[$headerMap['equipment_status']] ?? 'ACTIVE')) : 'ACTIVE',
                    'state_id'              => isset($headerMap['state_id']) 
                                                ? intval($rowData[$headerMap['state_id']] ?? 0) : 0,
                    'location_id'           => isset($headerMap['location_id']) 
                                                ? intval($rowData[$headerMap['location_id']] ?? 0) : 0,
                    'ownership'             => isset($headerMap['ownership']) 
                                                ? intval($rowData[$headerMap['ownership']] ?? 0) : 0,
                    'store_location_id'     => isset($headerMap['store_location_id']) 
                                                ? intval($rowData[$headerMap['store_location_id']] ?? 0) : 0,
                    'vendor_part_number_id' => isset($headerMap['vendor_part_number_id']) 
                                                ? intval($rowData[$headerMap['vendor_part_number_id']] ?? 0) : 0,
                    'date_installed'        => isset($headerMap['date_installed']) && !empty($rowData[$headerMap['date_installed']])
                                                ? date('Y-m-d', strtotime($rowData[$headerMap['date_installed']])) : null,
                    'worked_days'           => isset($headerMap['worked_days']) 
                                                ? intval($rowData[$headerMap['worked_days']] ?? 0) : 0,
                    'active'                => isset($headerMap['active']) 
                                                ? ($rowData[$headerMap['active']] == 1 || strtolower($rowData[$headerMap['active']]) == 'true' ? 1 : 0) 
                                                : 1,
                ];

                // âœ… DEBUG: Check the data before inserting
                // echo '<pre>Row ' . $row . ' data: '; print_r($asset_data); echo '</pre>';

                // Check if record exists
                $existing = $this->db
                    ->where('equipment_registration', $equipment_registration)
                    ->get('equipments_asset')
                    ->row();

                // âœ… DEBUG: Check database query
                // echo "Checking for existing: {$equipment_registration}<br>";

                if ($existing) {
                    // Update existing record
                    $this->db->where('equipment_id', $existing->equipment_id)
                             ->update('equipments_asset', $asset_data);
                    // echo "Updated existing record<br>";
                } else {
                    // Insert new record
                    $result = $this->db->insert('equipments_asset', $asset_data);
                    // echo "Inserted new record, result: " . ($result ? 'true' : 'false') . "<br>";
                    // echo "Last error: " . $this->db->error()['message'] . "<br>";
                }

                $success++;

            } catch (Exception $e) {
                log_message('error', "Excel Row {$row}: " . $e->getMessage());
                $errors[] = "Row {$row}: " . $e->getMessage();
                $failed++;
            }
        }

        unlink($file_path);
        
        // Add errors to message if any
        $message = "Imported {$success} assets | Failed {$failed}";
        if (!empty($errors)) {
            $message .= " | Errors: " . implode("; ", array_slice($errors, 0, 5));
            if (count($errors) > 5) {
                $message .= " and " . (count($errors) - 5) . " more";
            }
        }
        
        redirect("assets?message=" . urlencode($message));

    } catch (Exception $e) {
        unlink($file_path);
        log_message('error', $e->getMessage());
        redirect("assets?error=Excel processing failed: " . urlencode($e->getMessage()));
    }
}



    public function itemsImagesAdd()
    {
        // Check if the request is POST and files are uploaded
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $this->input->post('id');
            $unique_id = $this->input->post('unique_id');

            // Check if id and unique_id are available
            if ($id && $unique_id) {
                $item_pictures = $_FILES['item_picture'];

                // Define the base upload directory
                $base_upload_directory_path = realpath("storage") . "/Asset-item-" . $id;

                // Check if files are uploaded
                if (isset($item_pictures) && is_array($item_pictures['name']) && count($item_pictures['name']) > 0) {
                    for ($index = 0; $index < count($item_pictures['name']); $index++) {
                        $item_picture_path = null;

                        // Handle file upload
                        if (!empty($item_pictures['name'][$index])) {
                            if ($item_pictures['error'][$index] == UPLOAD_ERR_OK) {
                                $tmp_name = $item_pictures['tmp_name'][$index];
                                $file_name = time() . "-" . basename($item_pictures['name'][$index]);
                                $target_folder = $base_upload_directory_path . "/";

                                // Create folder if it doesn't exist
                                if (!is_dir($target_folder)) {
                                    @mkdir($target_folder, 0777, true);
                                }

                                // Move the uploaded file
                                if (move_uploaded_file($tmp_name, $target_folder . $file_name)) {
                                    $item_picture_path = 'Asset-item-' . $id . '/' . $file_name;
                                } else {
                                    log_message('error', 'Failed to move uploaded file: ' . $file_name);
                                }
                            } else {
                                log_message('error', 'File upload error for index ' . $index . ': ' . $item_pictures['error'][$index]);
                            }
                        }

                        // Prepare the data for insertion into the database
                        $item_data = array(
                            'add_asset_items_id' => $unique_id,
                            'item_picture' => $item_picture_path
                        );

                        // Insert into the database
                        $query = $this->db->insert('item_pictures', $item_data);

                        // Check for database errors
                        if (!$query) {

                            // Log SQL and error message
                            log_message('error', 'SQL Query: ' . $this->db->last_query());
                            $db_error = $this->db->error();
                            log_message('error', 'Error Message: ' . $db_error['message']);
                            log_message('error', 'Error Code: ' . $db_error['code']);
                            echo json_encode(['status' => 'error', 'message' => 'Database error occurred.']);
                            exit;
                        }
                    }

                    // Success response
                    echo json_encode(['status' => 'success', 'message' => 'Images uploaded successfully.']);
                } else {
                    echo json_encode(['status' => 'error', 'message' => 'No files uploaded.']);
                }
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Invalid ID or unique_id.']);
            }
        }
    }

    public function itemsqrgen()
    {
        if ($this->input->get('id')) {

            $asset_id_encoded = $this->input->get('id');
            $asset_id = $this->steve->id_decode($asset_id_encoded);
            $unique_id = $this->input->get('unique_id');

            if ($asset_id) {
                $data = [
                    'items_qr_code' => 1
                ];

                // Apply where conditions
                $this->db->where('asset_id', $asset_id);
                $this->db->where('id', $unique_id);
                $this->db->update('add_asset_items', $data);

                if ($this->db->affected_rows() > 0) {

                    // Get the item name for better logging
                    $this->db->select('item_name');
                    $this->db->where('id', $unique_id);
                    $itemRow = $this->db->get('add_asset_items')->row();

                    $itemName = $itemRow ? $itemRow->item_name : 'Unknown Item';

                    // Log the QR code generation
                    $this->load->model('asset_logs');
                    $this->asset_logs->add(
                        'assets/info',
                        $unique_id,
                        'ITEM_QR_GENERATED',
                        "QR code has been generated for Component: '{$itemName}'"
                    );

                    redirect('assets/info?id=' . $asset_id_encoded . '&message=QR Code has been generated successfully...#nav-details');
                } else {
                    redirect('assets/?error=Sorry, QR code could not be generated at this moment. Please try again later.');
                }
            } else {
                redirect('assets/?error=Invalid asset ID provided.');
            }
        } else {
            redirect('assets/?error=No asset ID provided for QR code generation.');
        }
    }


    public function itemsqrdel()
    {
        if ($this->input->get('id')) {

            $asset_id_encoded = $this->input->get('id');
            $asset_id = $this->steve->id_decode($asset_id_encoded);
            $unique_id = $this->input->get('unique_id');

            // Check if the item exists
            $this->db->where('asset_id', $asset_id);
            $getRes = $this->db->get('add_asset_items');

            if ($getRes->num_rows() > 0) {

                // Update the QR code field to 0 for the specific item
                $this->db->where('id', $unique_id);
                if ($this->db->update('add_asset_items', ['items_qr_code' => 0])) {

                    // Get the item name for better log readability
                    $this->db->select('item_name');
                    $this->db->where('id', $unique_id);
                    $itemRow = $this->db->get('add_asset_items')->row();

                    $itemName = $itemRow ? $itemRow->item_name : 'Unknown Item';

                    // Add log
                    $this->load->model('asset_logs');
                    $this->asset_logs->add(
                        'assets/info',
                        $unique_id,
                        'ITEM_QR_DELETED',
                        "QR code has been deleted for Component: '{$itemName}'"
                    );

                    redirect('assets/info?id=' . $asset_id_encoded . '&message=QR Code has been deleted successfully...');
                } else {
                    redirect('assets?error=Sorry, QR code could not be deleted at this moment. Please try again later.');
                }
            } else {
                redirect('assets?error=No matching asset found to delete the QR Code.');
            }
        } else {
            redirect('assets?error=No asset ID provided to delete the QR code.');
        }
    }


    public function qrgen()
    {
        if ($this->user_model->has_perm('qr_generator') && $this->input->get('id')) {

            // Decode equipment ID
            $equipmentIdEncoded = $this->input->get('id');
            $equipmentId = $this->steve->id_decode($equipmentIdEncoded);

            // Check if equipment exists
            $getRes = $this->db->get_where('equipments_asset', ['equipment_id' => $equipmentId]);
            if ($getRes->num_rows() > 0) {

                // Attempt to set QR code to active (1)
                $this->db->where('equipment_id', $equipmentId);
                if ($this->db->update('equipments_asset', ['qr_code' => 1])) {

                    // Load and insert log
                    $this->load->model('asset_logs');
                    $this->asset_logs->add(
                        'assets/info',
                        $equipmentId,
                        'ASSET_QR_GENERATED',
                        'QR code has been generated for the asset.'
                    );

                    redirect('assets/info?id=' . $equipmentIdEncoded . '&message=QR Code has been generated successfully...#nav-qr');
                } else {
                    redirect('assets?error=Sorry QR Code could not be generated at this moment. Please try again later.');
                }
            } else {
                redirect('assets?error=Asset not found.');
            }
        } else {
            redirect('assets?error=No permission to generate QR Code or missing ID.');
        }
    }


    public function qrdel()
    {
        // Check permission and input
        if ($this->user_model->has_perm('qr_generator') && $this->input->get('id')) {

            // Decode equipment ID
            $equipmentIdEncoded = $this->input->get('id');
            $equipmentId = $this->steve->id_decode($equipmentIdEncoded);

            // Check if equipment exists
            $getRes = $this->db->get_where('equipments_asset', ['equipment_id' => $equipmentId]);
            if ($getRes->num_rows() > 0) {

                // Attempt to update QR code field to 0 (deleted)
                $this->db->where('equipment_id', $equipmentId);
                if ($this->db->update('equipments_asset', ['qr_code' => 0])) {

                    // Log the QR code deletion
                    $this->load->model('asset_logs');
                    $this->asset_logs->add('assets/info', $equipmentId, 'ASSET_QR_DELETED', 'QR code was removed from the asset.');

                    // Redirect with success message
                    redirect('assets/info?id=' . $equipmentIdEncoded . '&message=QR Code has been deleted successfully...#nav-qr');
                } else {
                    // Failed update
                    redirect('assets?error=Sorry, QR Code could not be deleted at this moment. Please try again later.');
                }
            } else {
                redirect('assets?error=Equipment not found.');
            }
        } else {
            redirect('assets?error=No permission to delete QR Code or missing ID.');
        }
    }


    public function upload_picture()
    {
        if ($this->input->post('id')) {
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']['tmp_name'];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . '-' . basename($_FILES['file']['name']);

                $folder = realpath('storage') . '/Asset-' . $this->input->post('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . '/' . $name)) {
                    $this->db->set('equipment_picture', $name);
                    $this->db->where('equipment_id', $this->input->post('id'));

                    if ($this->db->update('equipments_asset')) {
                        $this->logs->add('assets', $this->input->post('id'), 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.');
                        $this->load->model('asset_logs');
                        $this->asset_logs->add('assets/info', $this->input->post('id'), 'ASSET_PHOTO_UPLOADED', 'A new photo was uploaded.');
                    }
                }
            }
        }
    }

    public function delete_asset_picture()
    {
        $this->load->helper('file');
        // Load file helper to delete files

        $input = json_decode(file_get_contents('php://input'), true);
        // Get the JSON input
        $equipmentId = $input['id'] ?? null;
        $pictureName = $input['picture'] ?? null;

        if ($equipmentId && $pictureName) {
            // Delete the picture from the filesystem
            $filePath = 'storage/Asset-' . $equipmentId . '/' . $pictureName;
            if (file_exists($filePath)) {
                unlink($filePath);
                // Delete the file
            }

            // Update the database to remove the picture reference
            $this->db->set('equipment_picture', null);
            $this->db->where('equipment_id', $equipmentId);
            if ($this->db->update('equipments_asset')) {
                // Log the action
                $this->logs->add('assets', $equipmentId, 'ASSET_PHOTO_DELETED', 'A photo was deleted.');
                echo json_encode(['success' => true]);
                return;
            } else {
                echo json_encode(['success' => false, 'message' => 'Database update failed.']);
                return;
            }
        }

        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
    }

    // public function addMaintenace()
    // {
    //     if ($this->user_model->has_perm('add_maintenance_log_asset')) {
    //         // Get the equipment ID
    //         $id = $this->input->post('id');
    //         $encodedId = $this->steve->id_encode($id);

    //         // Retrieve all the form data
    //         $updateDates = $this->input->post('update_date');
    //         $maintenanceTypes = $this->input->post('maintenance_type');
    //         $tickets = $this->input->post('ticket');
    //         $faultyTypes = $this->input->post('faulty_type_maintenance');
    //         $taskDones = $this->input->post('task_done');
    //         $finalStatuses = $this->input->post('final_status');
    //         $remarks = $this->input->post('remarks');

    //         // Get the current timestamp for created_at and updated_at
    //         $currentTimestamp = date('Y-m-d H:i:s');

    //         // Insert into `equipment_maintenance_asset`
    //         $data = [
    //             'equipment_id' => $id,
    //             'update_date' => $this->steve->to_date($updateDates .= ' ' . date('H:i:s')),
    //             'maintenance_type_id' => $maintenanceTypes,
    //             'ticket_number' => $tickets,
    //             'faulty_type' => $faultyTypes,
    //             'final_status' => $finalStatuses,
    //             'created_at' => $currentTimestamp,  // Add created_at timestamp
    //             'updated_at' => $currentTimestamp,  // Add updated_at timestamp
    //         ];

    //         // Insert the data into the `equipment_maintenance_asset` table
    //         $this->db->insert('equipment_maintenance_asset', $data);

    //         // Get the last inserted ID from `equipment_maintenance_asset`
    //         $maintenanceId = $this->db->insert_id();

    //         // Insert into `maintenance_task_done` table
    //         foreach ($taskDones as $index => $taskDone) {
    //             if (!empty($taskDone)) {
    //                 $taskData = [
    //                     'equipment_maintenance_id' => $maintenanceId,
    //                     'task_done' => $taskDone,
    //                     'remarks' => isset($remarks[$index]) ? $remarks[$index] : '',  // Use corresponding remark
    //                     'created_at' => $currentTimestamp,  // Add created_at timestamp
    //                     'updated_at' => $currentTimestamp,  // Add updated_at timestamp
    //                 ];
    //                 $this->db->insert('maintenance_task_done', $taskData);
    //             }
    //         }

    //         if ($maintenanceTypes == "preventive") {

    //             $frequency_year = $this->input->post('frequency_year') ?: "12";



    //             if ($maintenanceTypes == "preventive") {
    //                 $frequency_year = $this->input->post('frequency_year') ?: "12";

    //                 if ($finalStatuses == "complete") {
    //                     if ($frequency_year && $updateDates) {
    //                         // Calculate the interval in days between each maintenance
    //                         $interval_duration_days = round(365.25 / $frequency_year); // more accurate than 365

    //                         $dateObject = DateTime::createFromFormat('d/m/Y H:i:s', $updateDates);
                          
    //                         if ($dateObject) {
    //                             // If the interval is exactly 1 month (roughly 30 days), and maintenance is complete, set to NULL
                              
    //                             if ($interval_duration_days >= 28 && $interval_duration_days <= 31) {
    //                                 $this->db->set('equipment_id', $id);
    //                                 $this->db->set('maintenance_date', null); // explicitly NULL
    //                                 $this->db->where("equipment_id", intval($id));
    //                                 $this->db->update('next_maintenance_date');
    //                             } else {
                                   
    //                                 // Clone the object to avoid modifying the original
    //                                 $nextDateObject = clone $dateObject;
    //                                 $next_maintenance_date = $nextDateObject->modify("+$interval_duration_days days")->format('Y-m-d');

    //                                 $this->db->set('equipment_id', $id);
    //                                 $this->db->set('maintenance_date', $next_maintenance_date);
    //                                 $this->db->where("equipment_id", intval($id));
    //                                 $this->db->update('next_maintenance_date');

    //                                 // yahn pr  equipment_maintenance_asset ka table ma data insert kr raha hon

    //                                 $maintenance_data = [
    //                                     'equipment_id'          => $id,
    //                                     'update_date'      => $next_maintenance_date,
    //                                     'created_at'            => date('Y-m-d H:i:s'),
    //                                     'updated_at'            => date('Y-m-d H:i:s'),
    //                                     'maintenance_type_id'   => 'preventive',
    //                                     'final_status'           => 'pending',
    //                                 ];

    //                                 $this->db->insert('equipment_maintenance_asset', $maintenance_data);
    //                                 $maintenance_id = $this->db->insert_id();

    //                             }
    //                         } else {
    //                             echo "Invalid date format: $updateDates";
    //                         }
    //                     }
    //                 }
    //             }
    //         }



    //         // Redirect with success message
    //         redirect('assets/info?id=' . $encodedId . '&message=Maintenance added successfully...#nav-new-maintenance');
    //     } else {
    //         redirect('assets/info?id=' . $encodedId . '&message=you do not have permission to add maintenance.');
    //     }
    // }

    public function addMaintenace()
{
    // var_dump('kachuporiya');
    // exit();
    if ($this->user_model->has_perm('add_maintenance_log_asset')) {
        // Get the equipment ID
        $id = $this->input->post('id');
        $encodedId = $this->steve->id_encode($id);

        // Retrieve all the form data
        $updateDates = $this->input->post('update_date');
        $maintenanceTypes = $this->input->post('maintenance_type');
        $tickets = $this->input->post('ticket');
        $faultyTypes = $this->input->post('faulty_type_maintenance');
        $taskDones = $this->input->post('task_done');
        $finalStatuses = $this->input->post('final_status');
        $remarks = $this->input->post('remarks');

        // Get the current timestamp for created_at and updated_at
        $currentTimestamp = date('Y-m-d H:i:s');

        // Insert into `equipment_maintenance_asset`
        $data = [
            'equipment_id' => $id,
            'update_date' => $this->steve->to_date($updateDates .= ' ' . date('H:i:s')),
            'maintenance_type_id' => $maintenanceTypes,
            'ticket_number' => $tickets,
            'faulty_type' => $faultyTypes,
            'final_status' => $finalStatuses,
            'created_at' => $currentTimestamp,  // Add created_at timestamp
            'updated_at' => $currentTimestamp,  // Add updated_at timestamp
        ];

        // Insert the data into the `equipment_maintenance_asset` table
        $this->db->insert('equipment_maintenance_asset', $data);

        // Get the last inserted ID from `equipment_maintenance_asset`
        $maintenanceId = $this->db->insert_id();

        // Insert into `maintenance_task_done` table
        foreach ($taskDones as $index => $taskDone) {
            if (!empty($taskDone)) {
                $taskData = [
                    'equipment_maintenance_id' => $maintenanceId,
                    'task_done' => $taskDone,
                    'remarks' => isset($remarks[$index]) ? $remarks[$index] : '',  // Use corresponding remark
                    'created_at' => $currentTimestamp,  // Add created_at timestamp
                    'updated_at' => $currentTimestamp,  // Add updated_at timestamp
                ];
                $this->db->insert('maintenance_task_done', $taskData);
            }
        }

        if ($maintenanceTypes == "preventive") {

            $frequency_year = $this->input->post('frequency_year') ?: "12";

            if ($finalStatuses == "complete") {

                // ==========================================================
                // equipment_maintenance_tasks sirf preventive complete case me
                // ==========================================================
                $allTaskLists = $this->db->get('task_list')->result();

                // echo "<pre>";
                // print_r($this->session->userdata());
                // exit;
                $user = $this->session->userdata('user');
                $loggedInUserId = $user->user_id;

                if (!empty($allTaskLists)) {
                    foreach ($allTaskLists as $taskListRow) {

                        $maintenanceTaskData = [
                            'equipment_maintenance_id' => $maintenanceId,
                            'equipment_id'             => $id,
                            'task_list_id'             => $taskListRow->id,
                            'cost'                     => 0,
                            'user_id'                  => $loggedInUserId,
                            'file_path'                => null,
                            'status'                   => 'completed',
                            'created_at'               => $currentTimestamp,
                            'updated_at'               => $currentTimestamp,
                        ];

                        $this->db->insert('equipment_maintenance_tasks', $maintenanceTaskData);
                    }
                }

                // ========= Preventive logic with Upsert =========
                if ($frequency_year && $updateDates) {

                    $interval_duration_days = round(365.25 / $frequency_year);

                    $dateObject = DateTime::createFromFormat('d/m/Y H:i:s', $updateDates);
                    if (!$dateObject) {
                        echo "Invalid date format: $updateDates";
                        return;
                    }

                    // Determine the next maintenance date (or NULL)
                    $next_maintenance_date = null;
                    if ($interval_duration_days >= 28 && $interval_duration_days <= 31) {
                        // Set to NULL â€“ no next maintenance needed
                        $next_maintenance_date = null;
                    } else {
                        $nextDateObject = clone $dateObject;
                        $next_maintenance_date = $nextDateObject->modify("+{$interval_duration_days} days")->format('Y-m-d');
                    }

                    // ---- Check if a record exists for this equipment ----
                    $this->db->where('equipment_id', intval($id));
                    $existing = $this->db->get('next_maintenance_date')->row();

                    if ($existing) {
                        // Update existing record
                        $this->db->set('maintenance_date', $next_maintenance_date);
                        $this->db->where('equipment_id', intval($id));
                        $this->db->update('next_maintenance_date');
                    } else {
                        // Insert new record
                        $this->db->insert('next_maintenance_date', [
                            'equipment_id'     => $id,
                            'maintenance_date' => $next_maintenance_date
                        ]);
                    }

                    // ---- (Optional) Create a pending maintenance record for the future ----
                    // Only if next_maintenance_date is not null
                    if ($next_maintenance_date !== null) {
                        $maintenance_data = [
                            'equipment_id'        => $id,
                            'update_date'         => $next_maintenance_date,
                            'created_at'          => date('Y-m-d H:i:s'),
                            'updated_at'          => date('Y-m-d H:i:s'),
                            'maintenance_type_id' => 'preventive',
                            'final_status'        => 'pending',
                        ];
                        $this->db->insert('equipment_maintenance_asset', $maintenance_data);
                    }
                }
            }
        }        

        // Redirect with success message
        redirect('assets/info?id=' . $encodedId . '&message=Maintenance added successfully...#nav-new-maintenance');
    } else {
        redirect('assets/info?id=' . $encodedId . '&message=you do not have permission to add maintenance.');
    }
}

public function getMaintenanceDetails()
{
    $maintenanceId = $this->input->post('id');
    
    if (!$maintenanceId) {
        echo json_encode([
            'success' => false,
            'error' => 'Maintenance ID is required'
        ]);
        return;
    }
    
    // Get main maintenance details
    $this->db->select('*');
    $this->db->from('equipment_maintenance_asset');
    $this->db->where('equipment_maintenance_id', $maintenanceId);
    $maintenance = $this->db->get()->row_array();
    
    if (!$maintenance) {
        echo json_encode([
            'success' => false,
            'error' => 'Maintenance record not found'
        ]);
        return;
    }
    
    // Get tasks and remarks
    $this->db->select('*');
    $this->db->from('maintenance_task_done');
    $this->db->where('equipment_maintenance_id', $maintenanceId);
    $tasks = $this->db->get()->result_array();
    
    echo json_encode([
        'success' => true,
        'data' => $maintenance,
        'tasks' => $tasks
    ]);
}

public function updateMaintenance()
{
    $maintenanceId = $this->input->post('maintenance_id');
    $equipmentId = $this->input->post('equipment_id');
    
    // Update main maintenance record
    $maintenanceData = [
        'update_date' => $this->steve->to_date($this->input->post('update_date')),
        'maintenance_type_id' => $this->input->post('maintenance_type'),
        'final_status' => $this->input->post('final_status'),
        'updated_at' => date('Y-m-d H:i:s')
    ];
    
    $this->db->where('equipment_maintenance_id', $maintenanceId);
    $this->db->update('equipment_maintenance_asset', $maintenanceData);
    
    // Delete existing tasks
    $this->db->where('equipment_maintenance_id', $maintenanceId);
    $this->db->delete('maintenance_task_done');
    
    // Insert updated tasks with remarks
    $taskDones = $this->input->post('task_done');
    $remarks = $this->input->post('remarks');
    
    foreach ($taskDones as $index => $taskDone) {
        if (!empty($taskDone)) {
            $taskData = [
                'equipment_maintenance_id' => $maintenanceId,
                'task_done' => $taskDone,
                'remarks' => isset($remarks[$index]) ? $remarks[$index] : '',
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $this->db->insert('maintenance_task_done', $taskData);
        }
    }
    
    // Log the update
    $this->load->model('asset_logs');
    $this->asset_logs->add('assets/info', $equipmentId, 'MAINTENANCE_UPDATED', 
        "Maintenance record #$maintenanceId was updated");
    
    echo json_encode([
        'success' => true,
        'message' => 'Maintenance updated successfully'
    ]);
}



    // CSV UPLOAD

    function uploadCSV()
    {
        if (isset($_FILES['file'])) {
            if ($_FILES['file']['error'] == 0) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {
                    $csv = fopen($_FILES['file']['tmp_name'], 'r') or die("can't open file");
                    $i = 0;
                    while ($row = fgetcsv($csv, 1024)) {
                        $i++;
                        if ($i == 1) {
                            if ($row[0] != 'Asset Name' || $row[1] != 'Asset Number' || $row[2] != 'Asset Manufacturer') {
                                die(redirect('assets/index?error=Not a valid assets csv file'));
                            }
                            continue;
                        }

                        $asset_name = !empty($row[0]) ? $row[0] : NULL;
                        $asset_number = !empty($row[1]) ? $row[1] : NULL;
                        $asset_manufacturer = !empty($row[2]) ? $row[2] : NULL;
                        $asset_type = !empty($row[3]) ? $row[3] : NULL;
                        $branch = !empty($row[4]) ? $row[4] : NULL;
                        $status = !empty($row[5]) ? $row[5] : NULL;
                        $notes = !empty($row[6]) ? $row[6] : NULL;
                        $safe_load = !empty($row[7]) ? $row[7] : NULL;

                        if (!empty($status))
                            if (!in_array($status, ['In use', 'Maintenance', 'Standby', 'Available', 'Repair', 'Dispose', 'Scrap'])) {
                                die(redirect("assets/index?error=Equipment status should be in (In use, Maintenance, Standby , Available, Repair, 'Dispose', 'Scrap') in row " . ($i - 1)));
                            }

                        // check if registration number exists
                        $total_rows = $this->db->select('equipment_id')->from('equipments_asset')
                            ->where('equipment_registration', $asset_number)
                            ->get()
                            ->num_rows();

                        // get manufacturer_id from manufacturer_name
                        $manufacturer = $this->db->select('manufacturer_id')->from('manufacturers')->where('manufacturer_name', $asset_manufacturer)->get()->row();

                        // get brach_id from branch_code
                        $branch = $this->db->select('branch_id')->from('branch_office')->where('branch_code', $branch)->get()->row();

                        // get asset_type_id from asset_type_name
                        $asset_type = $this->db->select('asset_id')->from('asset_types')->where('name', $asset_type)->get()->row();

                        if ($total_rows > 0) {
                            $this->db->where('equipment_registration', $asset_number);
                            $this->db->update('equipments_asset', [
                                'equipment_name' => $asset_name,
                                'equipment_registration' => $asset_number,
                                'equipment_manufacturer' => !empty($manufacturer) ? $manufacturer->manufacturer_id : NULL,
                                'equipment_type' => !empty($asset_type) ? $asset_type->asset_id : NULL,
                                'equipment_status' => $status,
                                'equipment_notes' => $notes,
                                'equipment_safe_Load' => $safe_load,
                                'branch_office_id' => !empty($branch) ? $branch->branch_id : 0,
                            ]);
                        } else {
                            $this->db->set('equipment_name', $asset_name);
                            $this->db->set('equipment_registration', $asset_number);
                            $this->db->set('equipment_manufacturer', !empty($manufacturer) ? $manufacturer->manufacturer_id : NULL);
                            $this->db->set('equipment_type', !empty($asset_type) ? $asset_type->asset_id : NULL);
                            $this->db->set('equipment_status', $status);
                            $this->db->set('equipment_notes', $notes);
                            $this->db->set('equipment_safe_Load', $safe_load);
                            $this->db->set('branch_office_id', !empty($branch) ? $branch->branch_id : 0);
                            $this->db->insert('equipments_asset');
                        }
                    }
                    die(redirect('assets/index?message=Assets Imported Successfully'));
                }
            }
        }
    }

    // function downloadCsv()
    // {
    //     $file = fopen('php://output', 'w');

    //     fputcsv($file, [
    //         'Asset Name',
    //         'Asset Number',
    //         'Asset Manufacturer',
    //         'Asset Type',
    //         'Branch',
    //         'Equipment Status',
    //         'Notes',
    //         'Safe Load'
    //     ]);

    //     $name = 'asset-header.csv';
    //     header('Pragma: public');
    //     header('Expires: 0');
    //     header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    //     header('Cache-Control: private', false);
    //     header('Content-Disposition: attachment; filename="' . basename($name) . '"');
    //     header('Content-Transfer-Encoding: binary');
    //     header('Connection: close');
    //     die;
    // }

    public function itemList()
    {
        $equipmentId = $this->input->get('id');

        $query = $this->db->select('add_asset_items.*, item_status.name AS status, item_types.name AS item_type_name')
            ->from('add_asset_items')
            ->join('item_types', 'add_asset_items.item_type_id = item_types.id', 'left')
            ->join('item_status', 'add_asset_items.item_status_id = item_status.id', 'left')
            ->where('add_asset_items.asset_id', $equipmentId)
            ->get();

        $data = $query->result();

        header('Content-Type: application/json');
        echo json_encode($data);
    }


    public function logDetails()
    {
        $equipmentmaintenanceid = $this->input->get('id');


        if (!$equipmentmaintenanceid) {
            echo json_encode(["error" => "Invalid or missing equipment maintenance ID"]);
            return;
        }

        // Fetch data from equipment_maintenance_asset
        $query = $this->db->select('ema.*')  // Selecting all columns from equipment_maintenance_asset
            ->from('equipment_maintenance_asset as ema')
            ->where('ema.equipment_maintenance_id', $equipmentmaintenanceid)
            ->get();

        $equipmentData = $query->row();  // Since it's one record, use row() to get single object

        if (!$equipmentData) {
            echo json_encode(["error" => "No data found for the given equipment maintenance ID"]);
            return;
        }

        // Fetch related maintenance_task_done records
        $query = $this->db->select('mtd.*')  // Selecting all columns from maintenance_task_done
            ->from('maintenance_task_done as mtd')
            ->where('mtd.equipment_maintenance_id', $equipmentmaintenanceid)
            ->get();

        $maintenanceTasks = $query->result();  // Multiple tasks may be returned

        // Structure the data with equipment and related tasks
        $response = [
            'equipment_maintenance' => $equipmentData,
            'maintenance_tasks' => $maintenanceTasks
        ];

        header('Content-Type: application/json');
        echo json_encode($response);
    }



    public function generateQrPDF()
    {

        if ($this->input->post('equipment_ids')) {
            $equipment_ids = $this->input->post('equipment_ids');
            $equipments = $this->db->select(
                '
        equipments_asset.equipment_name, 
        equipments_asset.equipment_registration, 
        equipments_asset.equipment_type, 
        asset_types.name'
            )
                ->from('equipments_asset')
                ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
                ->where_in('equipments_asset.equipment_id', $equipment_ids)
                ->get()
                ->result();
            $this->load->view('asset-qrcodes', [
                'equipments' => $equipments
            ]);
        }
    }

    public function printRFID()
    {
        if ($this->input->post('equipment_ids')) {
            $equipment_ids = $this->input->post('equipment_ids');
            $equipments = $this->db->select(
                '
        equipments_asset.equipment_name, 
        equipments_asset.rfid, 
        equipments_asset.date_installed, 
        asset_types.name'
            )
                ->from('equipments_asset')
                ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
                ->where_in('equipments_asset.equipment_id', $equipment_ids)
                ->get()
                ->result();
            $this->load->view('asset-rfid', [
                'equipments' => $equipments
            ]);
        }
    }


    public function rfid_del()
    {
        if ($this->input->get('id')) {

            $equipmentIdEncoded = $this->input->get('id');
            $equipmentId = $this->steve->id_decode($equipmentIdEncoded);

            // Check if equipment exists
            $getRes = $this->db->get_where('equipments_asset', ['equipment_id' => $equipmentId]);
            if ($getRes->num_rows() > 0) {

                // Attempt to update RFID field to NULL
                $this->db->where('equipment_id', $equipmentId);
                if ($this->db->update('equipments_asset', ['rfid' => null])) {

                    // Log the RFID deletion
                    $this->load->model('asset_logs');
                    $this->asset_logs->add('assets/info', $equipmentId, 'ASSET_RFID_DELETED', 'RFID was removed from the asset.');

                    // Redirect with success message
                    redirect('assets/info?id=' . $equipmentIdEncoded . '&message=RFID has been deleted successfully...#nav-rfid');
                } else {
                    // Failed update
                    redirect('assets?error=Sorry, RFID could not be deleted at this moment. Please try again later.');
                }
            } else {
                redirect('assets?error=Equipment not found.');
            }
        } else {
            redirect('assets?error=No asset ID provided to delete the RFID.');
        }
    }

    public function assetLocationPointer()
    {
        header('Content-Type: application/json');

        // Retrieve filters from the request
        $equipment_type = $this->input->post('equipment_type');
        $equipment_group = $this->input->post('equipment_group');

        $this->db->select('
            states.state_name AS state_name,
            locations.lat,
            locations.long,
            locations.name as location_name,
            asset_types.name as asset_type,
            equipments_asset.equipment_name as asset_name,
            asset_type_color.color,
            equipments_asset.equipment_status
        ')
            ->from('equipments_asset')
            ->join('locations', 'equipments_asset.location_id = locations.id')
            // Legacy schema used locations.state_name; current schema stores state_id on locations.
            ->join('states', 'states.id = locations.state_id', 'left')
            ->join('asset_types', 'equipments_asset.equipment_type = asset_types.asset_id', 'left')
            ->join('asset_type_color', 'asset_type_color.asset_type_id  = equipments_asset.equipment_type', 'left');


        // Apply filters
        if ($equipment_type) {
            $this->db->where('equipment_type', $equipment_type);
        }
        if ($equipment_group) {
            $this->db->where('equipment_status', $equipment_group);
        }

        $query = $this->db->get();
        $data = $query->result();
        $states = [];
        foreach ($data as $order) {
            array_push($states, [
                'state_name' => $order->state_name,
                'longitude' => $order->long,
                'latitude' => $order->lat,
                'status' => $order->equipment_status,
                'color' => $order->color,
                'location_name' => $order->location_name,
                'asset_type' => $order->asset_type,
                'asset_name' => $order->asset_name,
            ]);
        }

        echo json_encode(['states' => $states]);
    }


    public function delete_picture()
    {
        // Get the JSON input data
        $input = json_decode(file_get_contents('php://input'), true);

        if (isset($input['id'])) {
            $pictureId = $input['id'];

            // Prepare and execute the delete query
            $this->db->where('id', $pictureId);
            $result = $this->db->delete('item_pictures');
            // Delete from item_pictures table

            if ($result) {
                // Successfully deleted
                echo json_encode(['success' => true]);
            } else {
                // Deletion failed
                echo json_encode(['success' => false, 'message' => 'Failed to delete picture.']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request.']);
        }
    }

    public function deleteItem()
    {
        // Get item ID and asset ID from the request
        $itemId = $this->input->get('id');
        $assetId = $this->input->get('assetid');

        // Fetch the item name before deleting
        $item = $this->db->select('item_name')->from('add_asset_items')->where('id', $itemId)->get()->row();

        if (!$item) {
            // Item not found
            log_message('error', "Attempted to delete non-existent item with ID $itemId");
            redirect('assets/info?id=' . $assetId);
            return;
        }

        $itemName = $item->item_name;

        // Delete pictures related to this item
        $this->db->where('add_asset_items_id', $itemId);
        $this->db->delete('item_pictures');

        // Delete the asset item itself
        $this->db->where('id', $itemId);
        $deleteStatus = $this->db->delete('add_asset_items');

        if ($deleteStatus) {
            $this->load->model('asset_logs');

            // Log using item name instead of just ID
            $log_description = "Deleted item: {$itemName}";
            $this->asset_logs->add("assets/info", $itemId, "ASSET_ITEM_DELETED", $log_description);

            $message = 'Record has been deleted successfully.';
        } else {
            $message = 'Error occurred while deleting the record.';
        }

        // Redirect to the assets info page with a success or error message
        redirect('assets/info?id=' . $assetId);
    }

    public function addAssetItems()
    {
        $asset_id = $this->input->post('asset_id');

        // Get posted item data from the Items section
        $items = $this->input->post('item');
        $vendor_part_numbers = $this->input->post('vendor_part_number');
        $manufacturer_names = $this->input->post('manufacturer_name');
        $manufacturer_drwing_numbers = $this->input->post('manufacturer_drwing_number');
        $manufacturer_part_numbers = $this->input->post('manufacturer_part_number');
        $item_types = $this->input->post('item_type');
        $faulty_item = $this->input->post('faulty_type_item');
        $item_status = $this->input->post('item_status');
        $store_location_item = $this->input->post('store_location_item');
        $calibration_date_item = $this->input->post('calibration_date_item');
        $frequency_day_item = $this->input->post('frequency_day_item');
        $reminder_day_item = $this->input->post('reminder_day_item');

        $maintenance_date_item = $this->input->post('maintenance_date_item');
        $frequency_year_item = $this->input->post('frequency_year_item');
        $maintenance_reminder_day_item = $this->input->post('maintenance_reminder_day_item');

        // Check if arrays have only empty values
        $all_vendor_parts_empty = is_array($vendor_part_numbers) && empty(array_filter($vendor_part_numbers, 'strlen'));
        $all_manufacturer_names_empty = is_array($manufacturer_names) && empty(array_filter($manufacturer_names, 'strlen'));
        $all_item_types_empty = is_array($item_types) && empty(array_filter($item_types, 'strlen'));

        // Redirect if any of the required fields are empty
        if ($all_vendor_parts_empty || $all_manufacturer_names_empty || $all_item_types_empty) {
            redirect('assets/info?id=' . $this->steve->id_encode($asset_id) . '&error=Please Fill All Required Fields...');
        }

        // Handle file uploads (example)
        $item_pictures = $_FILES['item_picture'];

        // Define the base upload directory
        $base_upload_directory_path = realpath('storage') . '/Asset-item-' . $asset_id;
        if (!is_dir($base_upload_directory_path)) {
            mkdir($base_upload_directory_path, 0755, true); // Create directory if it doesn't exist
        }

        // Check if items data is retrieved and loop through them
        if ($items && is_array($items)) {
            foreach ($items as $index => $item) {
                $item_picture_path = null;

                // Handle file upload for each item
                if (isset($item_pictures['tmp_name'][$index]) && !empty($item_pictures['tmp_name'][$index])) {
                    $upload_path = $base_upload_directory_path . '/' . basename($item_pictures['name'][$index]);
                    move_uploaded_file($item_pictures['tmp_name'][$index], $upload_path);
                    $item_picture_path = $upload_path; // Save the path for database use
                }

                // Prepare the data for insertion into the database
                $item_data = array(
                    'asset_id' => $asset_id,
                    'item_name' => $item,
                    'vendor_part_number' => isset($vendor_part_numbers[$index]) ? trim($vendor_part_numbers[$index]) : null,
                    'manufacturer_name' => isset($manufacturer_names[$index]) ? trim($manufacturer_names[$index]) : null,
                    'manufacturer_drwing_number' => isset($manufacturer_drwing_numbers[$index]) ? trim($manufacturer_drwing_numbers[$index]) : null,
                    'manufacturer_part_number' => isset($manufacturer_part_numbers[$index]) ? trim($manufacturer_part_numbers[$index]) : null,
                    'item_type_id' => isset($item_types[$index]) ? trim($item_types[$index]) : null,
                    'store_location_id' => isset($store_location_item[$index]) ? trim($store_location_item[$index]) : null,
                    'item_status_id' => isset($item_status[$index]) ? trim($item_status[$index]) : null,
                    'faulty_type_id' => isset($faulty_item[$index]) && trim($faulty_item[$index]) !== '' ? trim($faulty_item[$index]) : null,
                    'calibration_date' => isset($calibration_date_item[$index]) ? trim($calibration_date_item[$index]) : null,
                    'frequency_day' => isset($frequency_day_item[$index]) ? trim($frequency_day_item[$index]) : null,
                    'reminder_day' => isset($reminder_day_item[$index]) ? trim($reminder_day_item[$index]) : null,
                    'maintenance_date' => isset($maintenance_date_item[$index]) ? trim($maintenance_date_item[$index]) : null,
                    'frequency_year' => isset($frequency_year_item[$index]) ? trim($frequency_year_item[$index]) : null,
                    'maintenance_reminder_day' => isset($maintenance_reminder_day_item[$index]) ? trim($maintenance_reminder_day_item[$index]) : null,
                    'item_picture' => $item_picture_path, // Add the path of the uploaded image
                );

                // Insert the $item_data into the database or process it further
                // $this->db->insert('asset_items', $item_data);
                if ($this->db->insert('add_asset_items', $item_data)) {


                    $item_id = $this->db->insert_id();
                    $this->load->model('asset_logs');
                    $this->asset_logs->add("assets/info", $item_id, "Component_Updated", $_POST);
                    // Handle file upload
                    if (isset($item_pictures['name'][$index]) && !empty($item_pictures['name'][$index])) {
                        // Fixed to check for 'name' instead of 'tmpp_name'
                        if ($item_pictures['error'][$index] == UPLOAD_ERR_OK) {
                            $tmp_name = $item_pictures['tmp_name'][$index];
                            // Corrected to use 'tmp_name'
                            $file_name = time() . '-' . basename($item_pictures['name'][$index]);
                            $target_folder = $base_upload_directory_path . '/';

                            // Create folder if it doesn't exist
                            if (!is_dir($target_folder)) {
                                @mkdir($target_folder, 0777, true);
                            }

                            // Move the uploaded file
                            if (move_uploaded_file($tmp_name, $target_folder . $file_name)) {
                                $item_picture_path = "Asset-item-" . $asset_id . "/" . $file_name; // Added hyphen for consistency
                            } else {
                                log_message('error', 'Failed to move uploaded file: ' . $file_name);
                            }
                        } else {
                            log_message('error', 'File upload error for index ' . $index . ': ' . $item_pictures['error'][$index]);
                        }
                    }
                    $picture_data = array(
                        'add_asset_items_id' => $item_id,
                        'item_picture' => !empty($item_picture_path) ? $item_picture_path : null
                    );
                    $query = $this->db->insert('item_pictures', $picture_data);
                    if ($query) {
                        $this->asset_logs->add("assets/info", $item_id, "Component_Picture_Added", $_POST);
                    }
                } else {
                    log_message('error', 'Failed to insert asset item for asset ID ' . $asset_id . ': ' . $this->db->error()['message']);
                    redirect('assets/info?id=' .  $this->steve->id_encode($asset_id) . '&error=Failed to add item. Please try again.');
                    return;
                }
            }
        }
        redirect('assets/info?id=' .  $this->steve->id_encode($asset_id) . '&message=items added successfully...');
    }


    public function locationDropdown()
    {
        $stateId = $this->input->post('state_id');

		$this->output->set_content_type('application/json');

		if (!$stateId || !ctype_digit((string) $stateId)) {
			return $this->output
				->set_status_header(400)
				->set_output(json_encode(['locations' => [], 'error' => 'Invalid state ID.']));
		}

        // get State Name From Id 
        $stateData = $this->db->select('state_name')
            ->from('states')
            ->where('id', $stateId)
            ->get()
            ->row_array();

		if (!$stateData) {
			return $this->output
				->set_status_header(404)
				->set_output(json_encode(['locations' => [], 'error' => 'State not found.']));
		}

        // Fetch locations based on the state ID
        $locations = $this->db->select('*')
            ->from('locations')
			->where('state_id', $stateId)
			->where('active', 1)
			->order_by('name', 'asc')
            ->get()
            ->result_array();
        // Return the response as JSON
		return $this->output->set_output(json_encode(['locations' => $locations]));
    }


    public function deleteAsset()
    {
        $assetId = $this->input->get('id');

        // Validate asset ID
        if (!$assetId) {
            redirect('assets/index?error=Asset ID is required for deletion.');
            return;
        }

        // Get all related item IDs
        $this->db->select('id');
        $this->db->from('add_asset_items');
        $this->db->where('asset_id', $assetId);
        $query = $this->db->get();
        $itemIds = array_column($query->result_array(), 'id');

        // Delete related pictures (if any)
        if (!empty($itemIds)) {
            $this->db->where_in('add_asset_items_id', $itemIds);
            $this->db->delete('item_pictures');
        }

        // Delete related items
        $this->db->where('asset_id', $assetId);
        $this->db->delete('add_asset_items');

        // Delete the asset itself
        $this->db->where('equipment_id', $assetId);
        $deleteStatus = $this->db->delete('equipments_asset');

        if ($deleteStatus) {
            $this->load->model('asset_logs');
            $this->asset_logs->add("assets/info", $asset_id, "Asset_Deleted", $_POST);
        }
        // Set message and redirect
        $message = $deleteStatus
            ? 'Asset and its related items deleted successfully.'
            : 'Error occurred while deleting the asset.';

        redirect('assets/index?message=' . urlencode($message));
    }

    public function asset_logs_ajax_list()
    {
        $asset_id = $this->session->userdata('asset_id');

        if (!$asset_id) {
            show_error('Asset ID not found in session.');
        }

        $joins = [
            ["users", "users.user_id = asset_logs.log_user_id", "left"],
            ["equipments_asset", "equipments_asset.equipment_id = asset_logs.log_item_id", "left"],
        ];

        $select = "asset_logs.*, users.full_name";

        $searchable = ["log_item_table", "log_code", "users.full_name", "log_description"];

        $conditions = [
            ["equipments_asset.equipment_id", $asset_id]
        ];

        exit($this->steve->datatables_mysql("asset_logs", $searchable, $conditions, $joins, $select));
    }


public function delete_invoice_simple()
{
    header('Content-Type: application/json');
    
    // // Check permission
    // if (!$this->user_model->has_perm("edit_equipments")) {
    //     echo json_encode(['success' => false, 'message' => 'No permission to delete invoice']);
    //     return;
    // }

    // Get data - check both POST and raw JSON
    $asset_id = null;
    $invoice_file = null;
    
    // Try to get from POST first
    if ($this->input->post('id')) {
        $asset_id = $this->input->post('id');
        $invoice_file = $this->input->post('invoice_file');
    } 
    // Try to get from raw JSON input
    else {
        $raw_input = json_decode(file_get_contents('php://input'), true);
        if ($raw_input) {
            $asset_id = isset($raw_input['id']) ? $raw_input['id'] : null;
            $invoice_file = isset($raw_input['invoice_file']) ? $raw_input['invoice_file'] : null;
        }
    }

    // Debug logging
    error_log("DELETE INVOICE: Asset ID = $asset_id, File = $invoice_file");
    
    if (!$asset_id || !$invoice_file) {
        error_log("DELETE INVOICE ERROR: Missing parameters");
        echo json_encode(['success' => false, 'message' => 'Missing parameters']);
        return;
    }

    try {
        // Start transaction
        $this->db->trans_start();
        
        // First, get current value for logging
        $current_record = $this->db->select('invoice_file')
            ->where('equipment_id', $asset_id)
            ->get('equipments_asset')
            ->row();
            
        error_log("CURRENT INVOICE FILE: " . ($current_record->invoice_file ?: 'NULL'));
        
        // Update database - set to NULL
        $this->db->set('invoice_file', null);
        $this->db->where('equipment_id', $asset_id);
        $update_result = $this->db->update('equipments_asset');
        
        // Check if update was successful
        if (!$update_result) {
            $error = $this->db->error();
            error_log("DATABASE UPDATE ERROR: " . $error['message']);
            throw new Exception('Database update failed: ' . $error['message']);
        }
        
        // Check affected rows
        $affected_rows = $this->db->affected_rows();
        error_log("AFFECTED ROWS: $affected_rows");
        
        // Delete physical file
        $file_path = FCPATH . 'uploads/asset_invoice/' . $invoice_file;
        error_log("FILE PATH: $file_path");
        
        if (file_exists($file_path)) {
            if (unlink($file_path)) {
                error_log("FILE DELETED SUCCESSFULLY");
            } else {
                error_log("FILE DELETE FAILED - but database updated");
                // Continue even if file delete fails
            }
        } else {
            error_log("FILE NOT FOUND - but database updated");
        }
        
        // Verify the update
        $this->db->select('invoice_file');
        $this->db->where('equipment_id', $asset_id);
        $verify = $this->db->get('equipments_asset')->row();
        
        error_log("VERIFICATION - Current invoice_file after update: " . ($verify->invoice_file ?: 'NULL'));
        
        // Log the action
        $this->load->model('asset_logs');
        $this->asset_logs->add('assets/info', $asset_id, 'Invoice_Deleted', "Invoice file '$invoice_file' was deleted.");
        
        // Commit transaction
        $this->db->trans_complete();
        
        if ($this->db->trans_status() === FALSE) {
            throw new Exception('Transaction failed');
        }
        
        // Return success
        echo json_encode([
            'success' => true, 
            'message' => 'Invoice deleted successfully',
            'asset_id' => $asset_id,
            'invoice_file' => $invoice_file
        ]);
        
    } catch (Exception $e) {
        // Rollback transaction
        $this->db->trans_rollback();
        
        error_log("DELETE INVOICE EXCEPTION: " . $e->getMessage());
        echo json_encode([
            'success' => false, 
            'message' => 'Error: ' . $e->getMessage(),
            'debug' => [
                'asset_id' => $asset_id,
                'invoice_file' => $invoice_file
            ]
        ]);
    }
}
}
