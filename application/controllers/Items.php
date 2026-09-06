<?php

use NumberToWords\Legacy\Numbers\Words\Locale\Id;

defined('BASEPATH') or exit('No direct script access allowed');

class Items extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_equipments')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $manufacturer_number = $this->db->select('*')
            ->from('vendor_manufacturing_number')
            ->get()
            ->result();

        $equipments = $this->db->select('equipment_id, equipment_name')
            ->from('equipments_asset')
            ->get()
            ->result();

        $part_number = $this->db->select('id , part_number')
            ->from('vendor_part_number')
            ->get()
            ->result_array();

        $drawing_number = $this->db->select('drawing_number')
            ->from('vendor_manufacturing_drawing_number')
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

        $this->db->select('item_types.*, manufacturer_name, part_number'); // Select required fields
        $this->db->from('item_types'); // Main table
        $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number');
        $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer'); // Join condition
        $query = $this->db->get();
        $itemTypes = $query->result();


        $faulty = $this->db->select('*')
            ->from('fault_type_color_code')
            ->get()
            ->result();

        // Store fetched data in the $data array to pass to the view

        // Load views with $data
        $this->load->view('header', [
            'manufacturer_number' => $manufacturer_number,
            'equipments' => $equipments,
            'part_number' => $part_number,
            'drawing_number' => $drawing_number,
            'itemStatus' => $itemStatus,
            'storeLocation' => $storeLocation,
            'itemTypes' => $itemTypes,
            'faulty' => $faulty,
            'title' => 'Components',
            'title2' => 'List of Items',
            'styles' => [
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
                'design/css/datepicker.css',
                'design/css/order-summary-cards.css',
                'design/css/custom-datatable.css'
            ]
        ]);

        $this->load->view('item-list', []);
        // Pass the $data array directly

        $this->load->view('footer', [
            'scripts' => [
                'design/js/datepicker.js',
                'design/vendor/moment.js-2.24.0/moment.min.js',
                'design/js/items-list.js?v=2',
                'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
                'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
                'design/js/helper.js',




            ]
        ]);
    }

    public function update()
    {
        $this->load->model('asset_logs');
        if ($this->user_model->has_perm("add_equipments")) {


            if (empty($this->input->post('item_type'))) {
                redirect("assets?error=Please Fill All Fields");
            }

            $asset_id = $this->input->post('asset_id');
            $item_id = $this->input->post('item_id');

            // Prepare updated data
            $item_data = [
                'asset_id' => $asset_id,
                'item_name' => $this->input->post('item'),
                'serial_number' => $this->input->post('serial_number'),
                'vendor_part_number' => $this->input->post('vendor_part_number') ?: null,
                'manufacturer_name' => $this->input->post('manufacturer_name') ?: null,
                'manufacturer_drawing_number' => $this->input->post('manufacturer_drawing_number') ?: null,
                'manufacturer_part_number' => $this->input->post('manufacturer_part_number') ?: null,
                'item_status_id' => $this->input->post('item_status') ?: null,
                'item_type_id' => $this->input->post('item_type') ?: null,
                'faulty_type_id' => $this->input->post('faulty_type_item') ?: null,
                'store_location_id' => $this->input->post('store_location_item') ?: null,
                'calibration_date' => $this->input->post('calibration_date_item') ?: null,
                'frequency_day' => $this->input->post('frequency_day_item') ?: null,
                'reminder_day' => $this->input->post('reminder_day_item') ?: null,
                'maintenance_date' => $this->input->post('maintenance_date_item') ?: null,
                'frequency_year' => $this->input->post('frequency_year_item') ?: null,
                'maintenance_reminder_day' => $this->input->post('maintenance_reminder_day_item') ?: null,
            ];

            // Get old data for comparison
            $old_data = $this->db->get_where('add_asset_items', ['id' => $item_id])->row_array();

            if (!$old_data) {
                redirect("items?error=Item not found");
            }

            // Update item
            $this->db->where('id', $item_id);
            if ($this->db->update('add_asset_items', $item_data)) {

                // Prepare log description
                $log_description = "Updated component '{$old_data['item_name']}':\n";

                foreach ($item_data as $field => $new_value) {
                    $old_value = $old_data[$field];

                    // Convert item_status_id to name in logs
                    if ($field === 'item_status_id') {
                        if ($old_value) {
                            $status = $this->db->select('name')->get_where('item_status', ['id' => $old_value])->row_array();
                            $old_value = $status['name'] ?? $old_value;
                        }
                        if ($new_value) {
                            $status = $this->db->select('name')->get_where('item_status', ['id' => $new_value])->row_array();
                            $new_value = $status['name'] ?? $new_value;
                        }
                    }

                    if ($old_value != $new_value) {
                        $log_description .= "- {$field}: '{$old_value}' → '{$new_value}'\n";
                    }
                }

                // Insert into asset logs
                $this->asset_logs->add('assets/info', $item_id, 'Component_Updated', $log_description);

                redirect("items?message=Updated Item successfully");
            } else {
                redirect("items?error=Failed to update item");
            }
        } else {
            redirect("items?error=No permission to update Item");
        }
    }

    public function add()
    {
        $this->load->model('asset_logs');
        if ($this->user_model->has_perm("add_equipments")) {

            if (empty($this->input->post('item_type'))) {
                redirect("assets?error=Please Fill All Fields");
            }
            // Get posted item data from the Items section
            $equipment_name = $this->input->post('equipment_name');
            $items = $this->input->post('item');
            $vendor_part_numbers = $this->input->post('vendor_part_number');
            $manufacturer_names = $this->input->post('manufacturer_name');
            $manufacturer_drawing_numbers = $this->input->post('manufacturer_drawing_number');

            $item_types = $this->input->post('item_type');
            $faulty_type_item = $this->input->post('faulty_type_item');
            $item_status = $this->input->post('item_status');
            $store_location_item = $this->input->post('store_location_item');
            $calibration_date_item = $this->input->post('calibration_date_item');
            $frequency_day_item = $this->input->post('frequency_day_item');
            $reminder_day_item = $this->input->post('reminder_day_item');

            $item_pictures = $_FILES['item_picture'];

            // Define the base upload directory
            $base_upload_directory_path = realpath('storage') . '/Asset-item';
            // Corrected the directory name to include a hyphen

            // Check if items data is retrieved
            // Loop through items and prepare item data
            if ($items && is_array($items)) {
                foreach ($items as $index => $item) {
                    $item_picture_path = null;

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
                                $item_picture_path = "Asset-item" . "/" . $file_name; // Added hyphen for consistency
                            } else {
                                log_message('error', 'Failed to move uploaded file: ' . $file_name);
                            }
                        } else {
                            log_message('error', 'File upload error for index ' . $index . ': ' . $item_pictures['error'][$index]);
                        }
                    }

                    // Prepare the data for insertion into the database
                    $item_data = array(
                        'asset_id' => isset($equipment_name[$index]) && $equipment_name[$index] !== '' ? trim($equipment_name[$index]) : '',
                        'item_name' => !empty($item) ? $item : null,
                        'serial_number' => $this->input->post('serial_number')[$index] ?? null,
                        'vendor_part_number' => isset($vendor_part_numbers[$index]) ? trim($vendor_part_numbers[$index]) : null,
                        'manufacturer_name' => isset($manufacturer_names[$index]) ? trim($manufacturer_names[$index]) : null,
                        'manufacturer_drawing_number' => isset($manufacturer_drawing_numbers[$index]) ? trim($manufacturer_drawing_numbers[$index]) : null,
                        'manufacturer_part_number' => isset($manufacturer_part_numbers[$index]) ? trim($manufacturer_part_numbers[$index]) : null,
                        'item_status_id' => isset($item_status[$index]) ? trim($item_status[$index]) : null, // Ensure we get the right index for status
                        'item_type_id' => isset($item_types[$index]) ? trim($item_types[$index]) : null, // Ensure we get the right index for status
                        'faulty_type_id' => isset($faulty_type_item[$index]) && $faulty_type_item[$index] !== '' ? trim($faulty_type_item[$index]) : null,
                        'store_location_id' => isset($store_location_item[$index]) ? trim($store_location_item[$index]) : null,
                        'calibration_date' => isset($calibration_date_item[$index]) && $calibration_date_item[$index] !== '' ? trim($calibration_date_item[$index]) : null,
                        'frequency_day' => isset($frequency_day_item[$index]) && $frequency_day_item[$index] !== '' ? trim($frequency_day_item[$index]) : null,
                        'reminder_day' => isset($reminder_day_item[$index]) && $reminder_day_item[$index] !== '' ? trim($reminder_day_item[$index]) : null,
                        'item_picture' => !empty($item_picture_path) ? $item_picture_path : '',
                    );


                    if ($this->db->insert('add_asset_items', $item_data)) {

                        $item_id = $this->db->insert_id();
                        $item_picture_path = null; // default null

                        // Handle file upload
                        if (isset($item_pictures['name'][$index]) && !empty($item_pictures['name'][$index])) {
                            if ($item_pictures['error'][$index] == UPLOAD_ERR_OK) {
                                $tmp_name = $item_pictures['tmp_name'][$index];
                                $file_name = time() . '-' . basename($item_pictures['name'][$index]);
                                $target_folder = $base_upload_directory_path . '/';

                                // Create folder if it doesn't exist
                                if (!is_dir($target_folder)) {
                                    @mkdir($target_folder, 0777, true);
                                }

                                // Move the uploaded file
                                if (move_uploaded_file($tmp_name, $target_folder . $file_name)) {
                                    $item_picture_path = "Asset-item/" . $file_name;
                                } else {
                                    log_message('error', 'Failed to move uploaded file: ' . $file_name);
                                }
                            } else {
                                log_message('error', 'File upload error for index ' . $index . ': ' . $item_pictures['error'][$index]);
                            }
                        }

                        $picture_data = [
                            'add_asset_items_id' => $item_id,
                            'item_picture' => $item_picture_path ?: null
                        ];

                        if (!empty($picture_data["item_picture"])) {
                            $query = $this->db->insert('item_pictures', $picture_data);
                            if (!$query) {
                                log_message('error', "Error inserting item picture for item ID: {$item_id}");
                            }
                        }

                        // Prepare log description
                        $log_description = "New component '{$item_data['item_name']}': .\n";
                        foreach ($item_data as $field => $value) {
                            // Convert item_status_id to name for log readability
                            if ($field === 'item_status_id' && !empty($value)) {
                                $status = $this->db->select('name')->get_where('item_status', ['id' => $value])->row_array();
                                $value = $status['name'] ?? $value;
                            }
                            $log_description .= "- {$field}: '{$value}'\n";
                        }

                        if (!empty($item_picture_path)) {
                            $log_description .= "- item_picture: '{$item_picture_path}'\n";
                        }

                        // Insert into asset logs
                        $this->asset_logs->add('assets/info', $item_id, 'Component_Updated', $log_description);
                    }
                }
            }

            redirect("items?message=Added Item successfully");
        } else {
            redirect("items?error=No permission to add Asset");
        }
    }

    public function ajax_list()
    {
        // Get filters from POST
        $itemGroup = $this->input->post('item_group_filter'); // e.g., item_status
        $itemType = $this->input->post('item_type_filter');   // e.g., item_type_id

        // Define base query with select and joins
        $this->db->select('
        add_asset_items.*,
        item_status.name AS item_status_name,
        locations.name AS location_name
        ')
            ->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->join('equipments_asset', 'equipments_asset.equipment_id = add_asset_items.asset_id', 'left')
            ->join('locations', 'locations.id = equipments_asset.location_id', 'left');


        // Apply filters
        if ($this->input->post('asset_id')) {
            $this->db->where('add_asset_items.asset_id', $this->input->post('asset_id'));
        }
        if ($this->input->post('item_name')) {
            $this->db->like('add_asset_items.item_name', $this->input->post('item_name'));
        }
        if ($this->input->post('vendor_part_number')) {
            $this->db->like('add_asset_items.vendor_part_number', $this->input->post('vendor_part_number'));
        }
        if ($this->input->post('manufacturer_name')) {
            $this->db->like('add_asset_items.manufacturer_name', $this->input->post('manufacturer_name'));
        }
        if ($itemGroup) {
            $this->db->where('item_status.name', $itemGroup); // Match status name
        }
        if ($itemType) {
            $this->db->where('add_asset_items.item_type_id', $itemType); // Match type ID
        }
        if ($this->input->post('item_status')) {
            $this->db->where('add_asset_items.item_status', $this->input->post('item_status'));
        }
        if ($this->input->post('faulty_type_id')) {
            $this->db->where('add_asset_items.faulty_type_id', $this->input->post('faulty_type_id'));
        }

        // Execute main filtered query
        $query = $this->db->get();
        $result = $query->result_array();

        // Unfiltered total count
        $total_items = $this->db->count_all('add_asset_items');

        // Count status groups
        $ServiceableItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'SERVICEABLE')
            ->count_all_results();

        $MaintinenceItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'MAINTENANCE')
            ->count_all_results();

        $UnServiceableItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'UNSERVICEABLE')
            ->count_all_results();

        $storelocationItemCount = $this->db->from('add_asset_items')
            ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
            ->where('item_status.name', 'STORE')
            ->count_all_results();

        // Output response
        $data = [
            'data' => $result,
            'counts' => [
                'total_items' => $total_items,
                'ServiceableItemCount' => $ServiceableItemCount,
                'UnServiceableItemCount' => $UnServiceableItemCount,
                'MaintinenceItemCount' => $MaintinenceItemCount,
                'storelocationItemCount' => $storelocationItemCount,
            ]
        ];

        echo json_encode($data);
        exit;
    }


    public function itemsLocationPointer()
    {
        header('Content-Type: application/json');

        $item_type  = $this->input->post('item_type_filter');
        $item_group = $this->input->post('item_group_filter');

        $this->db->select("
                    locations.state_name, 
                    locations.lat, 
                    locations.long, 
                    locations.name as location_name,
                    item_types.name as item_type,
                    add_asset_items.item_name as item_name,
                    add_asset_items.item_status,
                    asset_type_color.color
                ")
            ->from('add_asset_items')
            ->join('equipments_asset', 'add_asset_items.asset_id = equipments_asset.equipment_id')
            ->join('locations', 'equipments_asset.location_id = locations.id')
            ->join('item_types', 'add_asset_items.item_type_id = item_types.id', 'left')
            ->join('asset_type_color', 'asset_type_color.asset_type_id = equipments_asset.equipment_type', 'left');

        if (!empty($item_type)) {
            $this->db->where('add_asset_items.item_type_id', $item_type);
        }

        if (!empty($item_group)) {
            $this->db->where('add_asset_items.item_status', $item_group);
        }

        $query = $this->db->get();

        if (!$query) {
            echo json_encode([
                'error' => true,
                'message' => $this->db->error()
            ]);
            return;
        }

        $data = $query->result();

        $states = [];
        foreach ($data as $item) {
            $states[] = [
                'state_name'     => $item->state_name,
                'longitude'      => $item->long,
                'latitude'       => $item->lat,
                'status'         => $item->item_status,
                'location_name'  => $item->location_name,
                'item_type'      => $item->item_type,
                'item_name'      => $item->item_name,
                'color'          => $item->color ?? 'blue'  // fallback
            ];
        }

        echo json_encode(['states' => $states]);
    }


    public function info()
    {

        // Check if 'id' is provided in the GET request and user has permission
        if ($this->input->get('id') && $this->user_model->has_perm('edit_equipments')) {

            $id =  $_GET['id'];


            $idd = $this->steve->id_decode($id);

            $item_id = $this->steve->id_decode($this->input->get('id'));
            // Query for items associated with the asset_id
            $items = $this->db->select('add_asset_items.*, item_types.calibration, item_types.maintenance')
                ->from('add_asset_items')
                ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
                ->where('add_asset_items.id', $item_id)
                ->get()
                ->row();




            // Query for pictures associated with the retrieved items
            $pictures = [];
            if (!empty($items->id)) {
                $pictures = $this->db->select('item_picture, id')
                    ->from('item_pictures')
                    ->where_in('add_asset_items_id', $items->id)
                    ->get()
                    ->result();
            }

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

            $equipments = $this->db->select('equipment_id, equipment_name')
                ->from('equipments_asset')
                ->get()
                ->result();

            // Check if equipment info exists
            if ($items) {
                $user_in_groups = [];

                // Fetch user groups associated with the equipment
                foreach ($this->db->where('equipment_id', intval($items->asset_id ?? 0))->get('equipment_group_asset')->result() as $user) {
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
                    'title' => 'Item - ' . $items->item_name,
                    'styles' => [
                        'design/vendor/dropzone/min/dropzone.min.css',
                        'design/css/multi-select.css',
                        'design/css/datepicker.css',
                        'design/css/custom-select.css'
                    ]
                ]);


                // The route ID identifies a component, not its parent asset.
                $locations = [];
                $parentAsset = null;
                if (!empty($items->asset_id)) {
                    $parentAsset = $this->db->get_where('equipments_asset', ['equipment_id' => $items->asset_id])->row();
                }
                if ($parentAsset && !empty($parentAsset->state_id)) {
                    $locations = $this->db->get_where('locations', ['state_id' => $parentAsset->state_id])->result();
                }
                $ticket = $this->db->select('*')
                    ->from('item_ticket')
                    ->where('item_id', $item_id)
                    ->get()
                    ->result();

                $task = $this->db->select('name')
                    ->from('task')
                    ->get()
                    ->result();

                $this->load->view('item-info', [
                    'user_in_groups' => $user_in_groups,
                    'ticket' => $ticket,
                    'task' => $task,
                    'part_numbers' => $part_numbers,
                    'part_number' => $part_number,
                    'manufacturer_number' => $manufacturer_number,
                    'manufacturer_name' => $manufacturer_name,
                    'locations' => $locations,
                    'managedBys' => $managedBys,
                    'drawing_numbers' => $drawing_numbers,
                    'equipments' => $equipments,
                    'items' => $items,
                    'pictures' => $pictures,
                    'maintenance' => $maintenance,
                    'itemTypes' => $itemTypes,
                    'states' => $states,
                    'assetStatus' => $assetStatus,
                    'faulty' => $faulty,
                    'assetTypes' => $assetTypes,
                    'itemStatus' => $itemStatus,
                    'storeLocation' => $storeLocation
                ]);
                $this->load->view('footer', [
                    'scripts' => [
                        'design/vendor/dropzone/min/dropzone.min.js',
                        'design/js/datepicker.js',
                        'design/js/jquery.multi-select.js',
                        'design/js/assets-list.js',
                        'design/js/item-maintenance-list.js'
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
        // Retrieve the ticket ID from the POST request
        $ticketId = $this->input->post('ticket');
        if (!$ticketId) {
            echo json_encode(['error' => 'Ticket ID not provided']);
            return;
        }

        // Fetch the fault_type_id from item_ticket
        $ticketQuery = $this->db->select('*')
            ->from('item_ticket')
            ->where('id', $ticketId)
            ->get()
            ->result();

        if (empty($ticketQuery)) {
            echo json_encode(['error' => 'No ticket found for the given ID']);
            return;
        }

        // Extract fault_type_id
        $faultTypeId = $ticketQuery[0]->fault_type_id ?? null;
        if (!$faultTypeId) {
            echo json_encode(['error' => 'Fault type ID not found']);
            return;
        }

        // Fetch the fault_type from fault_type_color_code based on fault_type_id
        $faultQuery = $this->db->select('fault_type')
            ->from('fault_type_color_code')
            ->where('id', $faultTypeId)
            ->get();

        if (!$faultQuery || $faultQuery->num_rows() == 0) {
            echo json_encode(['error' => 'No fault type found for this ticket']);
            return;
        }

        // Prepare the result
        $result = [];
        foreach ($faultQuery->result() as $row) {
            $result[] = [
                'value' => $row->fault_type,
                'label' => $row->fault_type,
            ];
        }

        echo json_encode($result);
    }





    public function addMaintenace()
    {
        // Get the equipment ID
        $id = $this->input->post('id');
        if ($this->user_model->has_perm('add_maintenance_log_item')) {




            // Retrieve all the form data
            $updateDates = $this->input->post('update_date');
            $ticket_id = $this->input->post('ticket');
            $taskDones = $this->input->post('task_done');
            $finalStatuses = $this->input->post('final_status');
            $remarks = $this->input->post('remarks');
            $currentTimestamp = date('Y-m-d H:i:s');

            // Prepare data for `logs_item_maintenance` table
            $data = [
                'item_ticket_id' => $ticket_id,
                'update_date' => $this->steve->to_date($updateDates .= ' ' . date('H:i:s')),
                'final_status' => $finalStatuses,
                'created_at' => $currentTimestamp, // Add created_at timestamp
                'updated_at' => $currentTimestamp  // Add updated_at timestamp
            ];

            // Perform the insert operation into `logs_item_maintenance` table
            $this->db->insert('logs_item_maintenance', $data);

            // Get the last inserted ID
            $maintenanceId = $this->db->insert_id();

            // Insert into `logs_item_maintenance_task_done` table
            foreach ($taskDones as $index => $taskDone) {
                if (!empty($taskDone)) {
                    $taskData = [
                        'item_maintenance_id' => $maintenanceId,
                        'task_done' => $taskDone,
                        'remarks' => $remarks[$index] ?? '', // Use corresponding remark
                        'created_at' => $currentTimestamp, // Add created_at timestamp
                        'updated_at' => $currentTimestamp  // Add updated_at timestamp
                    ];
                    $this->db->insert('logs_item_maintenance_task_done', $taskData);
                }
            }

            // Redirect with success message
            redirect('items/info?id=' . $id . '&message=Maintenance added successfully...#nav-new-maintenance');
        } else {
            redirect('items/info?id=' . $id . '&message=you do not have permission to add maintenance.');
        }
    }



    function item_maintenance_ajax_list()
    {

        $query = $this->steve->datatables_mysql(
            'item_ticket',
            [
                'item_ticket.*',
                'logs_item_maintenance.*',
                'logs_item_maintenance_task_done.*'
            ],
            [['item_ticket.item_id', $this->input->post('id')]],
            [
                ['logs_item_maintenance', 'item_ticket.id = logs_item_maintenance.item_ticket_id', 'inner'],
                ['logs_item_maintenance_task_done', 'logs_item_maintenance.id = logs_item_maintenance_task_done.item_maintenance_id', 'inner']
            ]
        );


        // Output query result
        die($query);
    }

    public function logDetails()
    {
        $id = $this->input->get('id');
        $updated_at = $this->input->get('updated_at');

        // Validate ID and updated_at
        if (!$id || !is_numeric($id)) {
            return $this->sendJsonResponse(["error" => "Invalid or missing item maintenance ID"]);
        }
        if (!$updated_at) {
            return $this->sendJsonResponse(["error" => "Missing updated_at timestamp"]);
        }

        // Fetch item ticket details along with the specific maintenance info based on ID & updated_at
        $this->db->select('
        item_ticket.id, 
        item_ticket.number, 
        fault_type_color_code.fault_type,
        logs_item_maintenance.id AS maintenance_id, 
        logs_item_maintenance.final_status, 
        logs_item_maintenance.update_date
    ');
        $this->db->from('item_ticket');
        $this->db->join('fault_type_color_code', 'fault_type_color_code.id = item_ticket.fault_type_id', 'left');
        $this->db->join('logs_item_maintenance', 'logs_item_maintenance.item_ticket_id = item_ticket.id', 'left');
        $this->db->where('item_ticket.id', $id);
        $this->db->where('logs_item_maintenance.updated_at', $updated_at);
        $query = $this->db->get();
        $itemTicketResult = $query->row_array();

        // Check if ticket and maintenance record exist
        if (!$itemTicketResult || empty($itemTicketResult['maintenance_id'])) {
            return $this->sendJsonResponse(["error" => "Item ticket or maintenance details not found"]);
        }

        // Fetch maintenance tasks only for this maintenance record and updated_at
        $tasksResult = $this->db->select('id AS task_id, task_done, remarks')
            ->from('logs_item_maintenance_task_done')
            ->where('item_maintenance_id', $itemTicketResult['maintenance_id'])
            ->where('updated_at', $updated_at)
            ->get()
            ->result_array();

        // Prepare and send response
        return $this->sendJsonResponse([
            'equipment_maintenance' => [
                'id' => $itemTicketResult['id'],
                'update_date' => $itemTicketResult['update_date'],
                'number' => $itemTicketResult['number'],
                'maintenance_id' => $itemTicketResult['maintenance_id'],
                'final_status' => $itemTicketResult['final_status'],
                'fault_type' => $itemTicketResult['fault_type']
            ],
            'maintenance_tasks' => $tasksResult
        ]);
    }


    /**
     * Helper function to send a JSON response
     */
    private function sendJsonResponse($data)
    {
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }






    public function delete()
    {
        $item_id = $this->steve->id_decode($this->input->get('item_id'));
        if ($this->input->get('id') && $this->input->get('item_maintenance_id') && $this->input->get('item_id') && $this->user_model->has_perm('add_maintenance_log_item')) {
            // Decode the ID properly
            $id = $this->steve->id_decode($this->input->get('id'));
            $item_maintenance_id = $this->steve->id_decode($this->input->get('item_maintenance_id'));

            $this->db->trans_start(); // Start transaction

            $this->db->where('id', $id);
            $this->db->delete('logs_item_maintenance_task_done');

            if ($this->db->affected_rows() > 0) {
                $this->db->where('id', $item_maintenance_id);
                $this->db->delete('logs_item_maintenance');
            }

            $this->db->trans_complete(); // Complete transaction

            if ($this->db->trans_status() === FALSE) {
                log_message('error', 'Transaction failed while deleting log for ID: ' . $id);

                redirect('items/info?id=' . $item_id . '&error=Failed to delete the log');
            } else {
                redirect('items/info?id=' . $item_id . '&message=Log deleted successfully');
            }
        } else {
            redirect('items/info?id=' . $item_id . '&error=Log not found or you do not have permission to delete.');
        }
    }



    public function itemsqrgen()
    {
        if ($this->input->get('id')) {

            $id = $this->steve->id_decode($this->input->get('id'));
            if ($id) {

                $data = [
                    'items_qr_code' => 1
                ];

                if ($this->db->where('id', $id)) {
                    // $this->db->where('id', $this->input->get('unique_id'));

                    $this->db->update('add_asset_items', $data);

                    if ($this->db->affected_rows() > 0) {
                        redirect('items/info?id=' . $this->input->get('id') . '&message=QR Code has been generated successfully...#nav-details');
                    } else {
                        redirect('items/?error=Sorry, QR code could not be generated at this moment. Please try again later.');
                    }
                } else {
                    redirect('items/?error=Invalid item ID provided.');
                }
            } else {

                redirect('items/?error=No item ID provided for QR code generation.');
            }
        }
    }

    public function itemsqrdel()
    {

        if ($this->input->get('id')) {

            $asset_id = $this->steve->id_decode($this->input->get('id'));

            $this->db->where('id', $asset_id);
            $getRes = $this->db->get('add_asset_items');

            if ($getRes->num_rows() > 0) {
                $this->db->where('id', $this->input->get('unique_id'));
                if ($this->db->update('add_asset_items', ['items_qr_code' => 0])) {
                    redirect('items/info?id=' . $this->input->get('id') . '&message=QR Code has been deleted successfully...');
                } else {
                    redirect('items?error=Sorry, QR code could not be deleted at this moment. Please try again later.');
                }
            } else {
                redirect('items?error=No matching item found to delete the QR Code.');
            }
        } else {
            redirect('items?error=No item ID provided to delete the QR code.');
        }
    }

    public function deleteItem()
    {
        // Get item ID and asset ID from the request

        if ($this->user_model->has_perm('Delete') && $this->input->get('id')) {

            $itemId = $this->input->get('id');
            $assetId = $this->input->get('assetid');

            // First, delete pictures related to this item
            $this->db->where('add_asset_items_id', $itemId);
            $this->db->delete('item_pictures');
            // No need for an if-statement here

            // Then, delete the asset item itself
            $this->db->where('id', $itemId);
            $deleteStatus = $this->db->delete('add_asset_items');
            // Store result of deletion

            // Check if the deletion was successful and set a message accordingly
            if ($deleteStatus) {
                redirect("items?message=Deleted Item successfully");
            } else {
                redirect("items?message=Error occurred while deleting the record.");
            }

            // Redirect to the assets info page with a success or error message
            redirect('items/info?id=' . $assetId);
        } else {
            redirect('items?error=You do not have permission to delete this item.');
        }
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

    public function assetList()
    {
        $equipmentId = $this->input->get('id');
        $query = $this->db->select('equipments_asset.equipment_name AS asset_name')
            ->from('equipments_asset')
            ->where('equipment_id', $equipmentId)
            ->get();
        $data = $query->result();
        header('Content-Type: application/json');
        // Set the content type
        // echo '<pre>';
        // var_dump( $data );
        // Return JSON response
        echo json_encode($data);
    }
}
