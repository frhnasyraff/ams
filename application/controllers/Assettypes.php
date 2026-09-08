<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assettypes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        $this->load->library('Asset_type_maintenance');
        if (strtolower($this->router->fetch_method()) === 'asset_calibration') {
            if (!$this->user_model->logged_in()) {
                $this->settings_response(['status' => false, 'error' => 'Please log in again.'], 401);
                $this->output->_display();
                exit;
            }
            $allowed = Asset_type_maintenance::can_lookup(
                $this->user_model->has_perm('list_assettypes'),
                $this->user_model->has_perm('list_equipments'),
                $this->user_model->has_perm('add_equipments'),
                $this->user_model->has_perm('edit_equipments')
            );
            if (!$allowed) {
                $this->settings_response(['status' => false, 'error' => 'No permission to read asset type settings.'], 403);
                $this->output->_display();
                exit;
            }
        } elseif (!$this->user_model->logged_in()) {
            die(redirect('/?error=Please log in.'));
        } elseif (!$this->user_model->has_perm('list_assettypes')) {
            $this->output->set_status_header(403);
            if (in_array(strtolower($this->router->fetch_method()), ['index', 'info'], true)) {
                $this->load->view('header', ['title' => 'Asset Types']);
                $this->load->view('assettypes-access');
                $this->load->view('footer', ['scripts' => []]);
            } else {
                $this->settings_response(['status' => false, 'error' => 'Asset Types access required.', 'data' => []], 403);
            }
            $this->output->_display();
            exit;
        }
    }

public function index()
{
    $manufacturer_name = $this->db->select('*')
        ->from('vendor_manufacturing_number')
        ->get()
        ->result();

    $part_numbers = $this->db->select('id , part_number')
        ->from('vendor_part_number')
        ->get()
        ->result();

    $task_lists = $this->db->select('id, name')
        ->from('task_list')
        ->get()
        ->result();

    // Disposal methods fetch करें
    $depreciation_methods = $this->db
        ->select('id, depreciation_method')
        ->from('depreciation_methods')
        ->get()
        ->result();

    $this->load->view('header', ['title' => 'Asset Types', 'styles' => [
        'design/css/custom-datatable.css'
    ]]);
    
    // सारा data एक साथ pass करें
    $this->load->view('assettypes', [
        'manufacturer_name' => $manufacturer_name,
        'part_numbers' => $part_numbers,
        'task_lists' => $task_lists,
        'depreciation_methods' => $depreciation_methods  // यहाँ add किया
    ]);
    
    $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js?v=5']]);
}

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm('edit_assettypes')) {
            $asset_id = $this->steve->id_decode();
            // Fetch asset type details
            $query = $this->db->get_where('asset_types', ['asset_id' => $asset_id]);
            $info = $query->result();


            // Fetch manufacturers and part numbers
            $manufacturer_name = $this->db->select('*')
                ->from('vendor_manufacturing_number')
                ->get()
                ->result();



            $part_numbers = $this->db->select('id, part_number')
                ->from('vendor_part_number')
                ->get()
                ->result();

            // Fetch asset type items (item types and quantities)
            $asset_type_items = $this->db->select('item_type_id, quantity')
                ->from('asset_type_items')
                ->where('asset_type_id', $asset_id)
                ->get()
                ->result();


            $task_lists = $this->db->select('id, name')
                ->from('task_list')
                ->get()
                ->result();

            // 🔥 NEW: Fetch selected tasks for this asset type
            $selected_tasks = $this->db->select('task_list_id')
                ->from('asset_type_tasks')
                ->where('asset_type_id', $asset_id)
                ->get()
                ->result();


            // Convert to simple array of task IDs
            $selected_task_ids = array_map(function($task) {
                return $task->task_list_id;
            }, $selected_tasks);

            $depreciation_methods = $this->db->get('depreciation_methods')->result();

            $this->db->select('item_types.*, manufacturer_name, part_number');
            $this->db->from('item_types');
            $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number');
            $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer');
            $query = $this->db->get();
            $item_types = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => 'Asset Types - ' . $info[0]->name]);
                $this->load->view('assettypes-info', [
                    'info' => $info[0],
                    'manufacturer_name' => $manufacturer_name,
                    'part_numbers' => $part_numbers,
                    'asset_type_items' => $asset_type_items,
                    'item_types' => $item_types,
                    'task_lists' => $task_lists, // 🔥 NEW: Pass task lists to view
                    'selected_task_ids' => $selected_task_ids, // 🔥 NEW: Pass selected task IDs
                    'depreciation_methods' => $depreciation_methods
                ]);
                $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js?v=5']]);
            } else {
                redirect('assettypes?error=Asset type not found');
            }
        } else {
            redirect('assettypes?error=Asset type not found or you do not have permission to edit.');
        }

        //     $this->db->select('item_types.*, manufacturer_name, part_number'); // Select required fields
        //     $this->db->from('item_types'); // Main table
        //     $this->db->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number');
        //     $this->db->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer'); // Join condition
        //     $query = $this->db->get();
        //     $item_types = $query->result();

        //     if ($info) {
        //         $this->load->view('header', ['title' => 'Asset Types - ' . $info[0]->name]);
        //         $this->load->view('assettypes-info', [
        //             'info' => $info[0],
        //             'manufacturer_name' => $manufacturer_name,
        //             'part_numbers' => $part_numbers,
        //             'asset_type_items' => $asset_type_items,
        //             'item_types' => $item_types // Pass the asset type items data to the view
        //         ]);
        //         $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js?v=2']]);
        //     } else {
        //         redirect('assettypes?error=Asset type not found');
        //     }
        // } else {
        //     redirect('assettypes?error=Asset type not found or you do not have permission to edit.');
        // }
    }

    public function ajax_list()
    {
        // Select type identity explicitly: joined tables also contain id/active fields.
        $this->output->set_content_type('application/json')->set_output($this->steve->datatables_mysql(
            'asset_types', ['asset_types.name', 'manufacturer_name', 'part_number'], [],
            [['vendor_manufacturing_number', 'vendor_manufacturing_number.id = asset_types.manufacturer', 'left'],
             ['vendor_part_number', 'vendor_part_number.id = asset_types.vendor_part_number', 'left']],
            'asset_types.*, vendor_manufacturing_number.manufacturer_name, vendor_part_number.part_number'
        ));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by('commodity_code', 'asc')->select("id as id, CONCAT(commodity_code, ' (', name, ')') as label, CONCAT(commodity_code, ' - ', name) as value")->group_start()->like('commodity_code', $this->input->get('term'))->or_like('name', $this->input->get('term'))->group_end()->get_where('asset_types', ['active' => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm('edit_assettypes') && $this->input->post('id')) {
            die($this->steve->active_toggle('asset_types', 'asset_id'));
        }
    }

    public function delete()
    {

        if ($this->user_model->has_perm('Delete') && $this->input->get('id')) {

            $id = intval($this->input->get('id'));
            // First delete related rows from asset_type_color
            $this->db->where('asset_type_id', $id);
            $this->db->delete('asset_type_color');

            // Now delete from asset_types
            $this->db->where('asset_id', $id);
            $this->db->delete('asset_types');

            if ($this->db->affected_rows() > 0) {
                redirect('assettypes/index?message=Asset type was deleted successfully.');
            } else {
                redirect('assettypes/index?error=Asset type deletion failed.');
            }
        } else {
            redirect('assettypes/index?error=No permission or ID is blank');
        }
    }

    // public function update()
    // {
    //     if ($this->user_model->has_perm('edit_assettypes') && $this->input->post('id')) {

    //         // Sanitize inputs
    //         $name = $this->input->post('name', true);
    //         $manufacturer = $this->input->post('manufacturer');
    //         $vendor_part_number = $this->input->post('vendor_part_number');
    //         $calibration = $this->input->post('calibration') ? '1' : '0'; // Check if checkbox is set, default to '0'
    //         $maintenance = $this->input->post('maintenance') ? '1' : '0'; // Check if checkbox is set, default to '0'

    //         // Set database fields for updating
    //         $this->db->set('name', $name);
    //         $this->db->set('calibration', $calibration);
    //         $this->db->set('maintenance', $maintenance);
    //         $this->db->set('vendor_part_number', $vendor_part_number);
    //         $this->db->set('manufacturer', $manufacturer);
    //         $this->db->where('asset_id', intval($this->input->post('id')));

    //         // Update asset_types table
    //         $this->db->update('asset_types');



    //         // Delete previous entries from asset_type_item
    //         $del = $this->db->from('asset_type_items')
    //             ->where('asset_type_id', intval($this->input->post('id')))
    //             ->delete();

    //         if ($del) {
    //             // Insert new records into asset_type_items
    //             $item_types = $this->input->post('item_type');

    //             $quantities = $this->input->post('quantity');
    //             $quantities = array_map('intval', $quantities);


    //             if (!empty($item_types) && !empty($quantities)) {
    //                 foreach ($item_types as $index => $item_type_id) {
    //                     if (isset($quantities[$index])) {
    //                         $this->db->insert('asset_type_items', [
    //                             'asset_type_id' => intval($this->input->post('id')),
    //                             'item_type_id' => intval($item_type_id),
    //                             'quantity' => $quantities[$index]
    //                         ]);
    //                     }
    //                 }
    //                 $this->logs->add('asset_types', $this->input->post('id'), 'OPERATION_TYPE_UPDATED', $_POST);
    //             }


    //             redirect('assettypes/index?message=Asset type was updated successfully.');
    //         } else {

    //             // Handle failure to update asset_types
    //             redirect('assettypes/index?error=Update failed.');
    //         }

    //         // Log and redirect

    //     } else {
    //         redirect('assettypes/index?error=No permission or ID is blank');
    //     }
    // }

public function update()
{
    if ($this->user_model->has_perm('edit_assettypes') && $this->input->post('id')) {

        $asset_id = (int) $this->input->post('id');

        // Basic fields
        $name               = $this->asset_type_name_from_form($asset_id);
        $manufacturer       = $this->input->post('manufacturer');
        $vendor_part_number = $this->input->post('vendor_part_number');
        $calibration        = $this->input->post('calibration') ? '1' : '0';
        $maintenance        = $this->input->post('maintenance') ? '1' : '0';
        $maintenanceDefaults = $this->maintenance_defaults_from_form();

        // Depreciation fields
        $depreciation_method_id = $this->input->post('depreciation_method_id') ?: null;
        $useful_life_years      = $this->input->post('useful_life_years');
        $salvage_value          = $this->input->post('salvage_value');
        $depreciate_value       = $this->input->post('depreciate_value');

        $this->db->trans_start();

        // =======================
        // UPDATE asset_types
        // =======================
        $this->db->set([
            'name'                   => $name,
            'manufacturer'           => $manufacturer,
            'vendor_part_number'     => $vendor_part_number,
            'calibration'            => $calibration,
            'maintenance'            => $maintenance,
            'maintenance_frequency_year' => $maintenanceDefaults['maintenance_frequency_year'],
            'maintenance_reminder_days' => $maintenanceDefaults['maintenance_reminder_days'],
            'depreciation_method_id' => $depreciation_method_id
        ]);

        // 🔥 Depreciation logic
        if (!empty($depreciate_value)) {
            // Reducing Balance
            $this->db->set([
                'depreciate_value'  => $depreciate_value,
                'useful_life_years' => null,
                'salvage_value'     => null
            ]);
        } else {
            // Straight Line
            $this->db->set([
                'useful_life_years' => $useful_life_years === '' ? null : $useful_life_years,
                'salvage_value'     => $salvage_value === '' ? null : $salvage_value,
                'depreciate_value'  => null
            ]);
        }

        $this->save_asset_type_row($asset_id);

        // =======================
        // UPDATE asset_type_items
        // =======================
        $this->db->where('asset_type_id', $asset_id)->delete('asset_type_items');

        $item_types = $this->input->post('item_type');
        $quantities = $this->input->post('quantity');

        if (!empty($item_types) && !empty($quantities)) {
            foreach ($item_types as $i => $item_type_id) {
                if (!empty($quantities[$i])) {
                    $this->db->insert('asset_type_items', [
                        'asset_type_id' => $asset_id,
                        'item_type_id'  => (int) $item_type_id,
                        'quantity'      => (int) $quantities[$i]
                    ]);
                }
            }
        }

        // =======================
        // UPDATE task lists
        // =======================
        if ($this->db->table_exists('asset_type_tasks')) {
            $this->db->where('asset_type_id', $asset_id)->delete('asset_type_tasks');

            $task_lists = $this->input->post('task_lists');
            if (!empty($task_lists)) {
                foreach ($task_lists as $task_id) {
                    $this->db->insert('asset_type_tasks', [
                        'asset_type_id' => $asset_id,
                        'task_list_id'  => (int) $task_id
                    ]);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            redirect('assettypes?error=Update failed');
        }

        $this->logs->add('asset_types', $asset_id, 'OPERATION_TYPE_UPDATED', $_POST);
        redirect('assettypes?message=Asset type updated successfully');
    }

    redirect('assettypes?error=No permission or ID missing');
}



public function add()
{
    if ($this->user_model->has_perm('add_assettypes')) {

        $name               = $this->asset_type_name_from_form();
        $manufacturer       = $this->input->post('manufacturer');
        $vendor_part_number = $this->input->post('vendor_part_number');
        $calibration        = $this->input->post('calibration') ? '1' : '0';
        $maintenance        = $this->input->post('maintenance') ? '1' : '0';
        $maintenanceDefaults = $this->maintenance_defaults_from_form();

        // Depreciation
        $depreciation_method_id = $this->input->post('depreciation_method_id') ?: null;
        $useful_life_years      = $this->input->post('useful_life_years');
        $salvage_value          = $this->input->post('salvage_value');
        $depreciate_value = $this->input->post('depreciate_value');

        $this->db->trans_start();

        // =======================
        // INSERT asset_types
        // =======================
        $this->db->set([
            'name'                   => $name,
            'manufacturer'           => $manufacturer,
            'vendor_part_number'     => $vendor_part_number,
            'calibration'            => $calibration,
            'maintenance'            => $maintenance,
            'maintenance_frequency_year' => $maintenanceDefaults['maintenance_frequency_year'],
            'maintenance_reminder_days' => $maintenanceDefaults['maintenance_reminder_days'],
            'depreciation_method_id' => $depreciation_method_id
        ]);
        

        if (!empty($depreciate_value)) {
            $this->db->set([
                'depreciate_value'  => $depreciate_value,
                'useful_life_years' => null,
                'salvage_value'     => null
            ]);
        } else {
            $this->db->set([
                'useful_life_years' => $useful_life_years === '' ? null : $useful_life_years,
                'salvage_value'     => $salvage_value === '' ? null : $salvage_value,
                'depreciate_value'  => null
            ]);
        }

        $this->save_asset_type_row();
        $asset_type_id = $this->db->insert_id();

        // =======================
        // INSERT asset_type_items
        // =======================
        $item_types = $this->input->post('item_type');
        $quantities = $this->input->post('quantity');

        if (!empty($item_types) && !empty($quantities)) {
            foreach ($item_types as $i => $item_type_id) {
                if (!empty($quantities[$i])) {
                    $this->db->insert('asset_type_items', [
                        'asset_type_id' => $asset_type_id,
                        'item_type_id'  => (int) $item_type_id,
                        'quantity'      => (int) $quantities[$i]
                    ]);
                }
            }
        }

        // =======================
        // INSERT task lists
        // =======================
        $task_lists = $this->input->post('task_lists');
        if (!empty($task_lists)) {
            foreach ($task_lists as $task_id) {
                $this->db->insert('asset_type_tasks', [
                    'asset_type_id' => $asset_type_id,
                    'task_list_id'  => (int) $task_id
                ]);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE) {
            redirect('assettypes?error=Add failed');
        }

        $this->logs->add('asset_types', $asset_type_id, 'OPERATION_TYPE_CREATED', $_POST);
        redirect('assettypes?message=Asset type added successfully');
    }

    redirect('assettypes?error=No permission');
}



    //     public function add()
    //  {
    //         if ( $this->user_model->has_perm( 'add_assettypes' ) && $this->input->post( 'name' ) ) {
    //             $name = $this->input->post( 'name' );
    //             $manufacturer = $this->input->post( 'manufacturer' );
    //             $vendor_part_number = $this->input->post( 'vendor_part_number' );
    //             $calibration  = $this->input->post( 'calibration' );
    //             if ( empty( $calibration ) && $calibration == null ) {
    //                 $calibration = '0';
    //             } else {
    //                 $calibration  = $this->input->post( 'calibration' );
    //             }

    //             // Check if a record with the same name already exists
    //             $existing_record = $this->db->get_where( 'asset_types', array( 'name' => $name ) )->row();

    //             if ( $existing_record ) {
    //                 // Redirect with an error message indicating the name already exists
    //                 redirect( 'assettypes?error=Asset type with the same name already exists' );
    //             } else {
    //                 // Insert the new asset type if it doesn't exist
    //             $this->db->set('name', $name);
    //             $this->db->set('calibration', $calibration);
    //             $this->db->set('manufacturer', $manufacturer);
    //             $this->db->set('vendor_part_number', $vendor_part_number);
    //             $this->db->set('rental_price', $this->input->post('rental_price'));
    //             $this->db->set('selling_price', $this->input->post('selling_price'));
    //             $this->db->set('rental_duration', $this->input->post('rental_duration'));

    //             if ($this->db->insert('asset_types')) {
    //                 $this->logs->add("asset_types", $this->db->insert_id(), "OPERATION_TYPE_CREATED", $_POST);
    //                 redirect("assettypes?message=Added Asset type successfully");
    //             } else {
    //                var_dump($this->db->last_query());
    //                exit();
    //                 redirect("assettypes?error=Adding Asset type failed");
    //             }
    //         }
    //     } else {
    //         redirect("assettypes?error=No permission to add Asset type");
    //     }
    // }

    // public function add()
    // {
    //     if ($this->user_model->has_perm('add_assettypes') && $this->input->post('name')) {
    //         $name = $this->input->post('name');
    //         $manufacturer = $this->input->post('manufacturer');
    //         $vendor_part_number = $this->input->post('vendor_part_number');
    //         $calibration = $this->input->post('calibration') ? $this->input->post('calibration') : '0';
    //         $maintenance = $this->input->post('maintenance') ? $this->input->post('maintenance') : '0';

    //         // Check if a record with the same name already exists
    //         $existing_record = $this->db->get_where('asset_types', ['name' => $name])->row();

    //         if ($existing_record) {
    //             redirect('assettypes?error=Asset type with the same name already exists');
    //         } else {
    //             // Insert the new asset type
    //             $this->db->set('name', $name);
    //             $this->db->set('calibration', $calibration);
    //             $this->db->set('maintenance', $maintenance);
    //             $this->db->set('manufacturer', $manufacturer);
    //             $this->db->set('vendor_part_number', $vendor_part_number);


    //             if ($this->db->insert('asset_types')) {
    //                 $asset_type_id = $this->db->insert_id(); // Get the inserted asset type ID

    //                 // Now, insert multiple asset type items
    //                 $item_types = $this->input->post('item_type');
    //                 $quantities = $this->input->post('quantity');
    //                 $quantities = array_map('intval', $quantities);
    //                 foreach ($item_types as $index => $item_type_id) {
    //                     $this->db->insert('asset_type_items', [
    //                         'asset_type_id' => $asset_type_id,
    //                         'item_type_id' => $item_type_id,
    //                         'quantity' => $quantities[$index]
    //                     ]);
    //                 }

    //                 $this->logs->add("asset_types", $asset_type_id, "OPERATION_TYPE_CREATED", $_POST);
    //                 redirect("assettypes?message=Added Asset type successfully");
    //             } else {
    //                 redirect("assettypes?error=Adding Asset type failed");
    //             }
    //         }
    //     } else {
    //         redirect("assettypes?error=No permission to add Asset type");
    //     }
    // }


    // public function asset_calibration() {
    //     if (isset($_POST['asset_id'])) {
    //         $assetId = $_POST['asset_id'];

    //         // Query the database for a single result
    //         $data = $this->db->select('*')
    //             ->from('asset_types')
    //             ->where('asset_id', $assetId)
    //             ->get()
    //             ->row(); // Use ->row() for a single result

    //         // Return JSON response
    //         header('Content-Type: application/json');
    //         if ($data) {
    //             echo json_encode(['calibration' => $data->calibration , 'maufacturer'=>$data->manufacturer , 'vpn' => $data->vendor_part_number]);
    //         } else {
    //             echo json_encode(['calibration' => 0]);
    //         }
    //     }
    // }

    // public function asset_calibration() {
    //     if (isset($_POST['asset_id'])) {
    //         $assetId = $_POST['asset_id'];

    //         // Sanitize the input (optional, for added security)
    //         $assetId = intval($assetId);

    //         // Query the database for a single result
    //         $data = $this->db->select('*')
    //             ->from('asset_types')
    //             ->where('asset_id', $assetId)
    //             ->get()
    //             ->row(); // Use ->row() for a single result

    //         // Return JSON response
    //         header('Content-Type: application/json');
    //         if ($data) {
    //             echo json_encode([
    //                 'calibration' => $data->calibration,
    //                 'manufacturer' => $data->manufacturer,
    //                 'vpn' => $data->vendor_part_number
    //             ]);
    //         } else {
    //             echo json_encode(['calibration' => 0]);
    //         }
    //     } else {
    //         // Return an error response if asset_id is not provided
    //         header('Content-Type: application/json');
    //         echo json_encode(['error' => 'Asset ID not provided' ] );
    //     }
    //     }


    private function asset_type_name_from_form($asset_id = null)
    {
        $value = $this->input->post('name', true);
        $name = is_string($value) ? trim($value) : '';
        if ($name === '') {
            redirect('assettypes?error=' . rawurlencode('Please enter an Asset Type Name.'));
        }

        // Use the database collation, matching the unique index, and include inactive types.
        $this->db->select('asset_id')->where('name', $name);
        if ($asset_id !== null) {
            $this->db->where('asset_id !=', $asset_id);
        }
        if ($this->db->get('asset_types')->row()) {
            $this->duplicate_asset_type_response();
        }
        return $name;
    }

    private function duplicate_asset_type_response()
    {
        redirect('assettypes?error=' . rawurlencode(
            'An Asset Type with this name already exists. Edit the existing type or use a different name. Check inactive types too.'
        ));
    }

    private function save_asset_type_row($asset_id = null)
    {
        // The unique index remains the final guard for simultaneous submissions.
        $previousDebug = $this->db->db_debug;
        $this->db->db_debug = false;
        $saved = false;
        $errorCode = 0;
        try {
            $saved = $asset_id === null
                ? $this->db->insert('asset_types')
                : $this->db->where('asset_id', $asset_id)->update('asset_types');
            if (!$saved) {
                $errorCode = (int) ($this->db->error()['code'] ?? 0);
            }
        } catch (Throwable $e) {
            $errorCode = (int) $e->getCode();
        } finally {
            $this->db->db_debug = $previousDebug;
        }

        if (!$saved) {
            // Stop before changing component/task links or writing a success log.
            $this->db->trans_rollback();
            if ($errorCode === 1062) {
                $this->duplicate_asset_type_response();
            }
            log_message('error', 'Asset type save failed (database code ' . $errorCode . ').');
            redirect('assettypes?error=' . rawurlencode('Unable to save the Asset Type. Please retry or contact the administrator.'));
        }
    }

    private function settings_response($payload, $status = 200)
    {
        $this->output->set_status_header($status)
            ->set_content_type('application/json')
            ->set_header('Cache-Control: no-store')
            ->set_output(json_encode($payload));
    }

    public function asset_calibration()
    {
        if ($this->input->method(true) !== 'POST') {
            $this->settings_response(['status' => false, 'error' => 'POST required.'], 405);
            return;
        }
        $previousDebug = $this->db->db_debug;
        $this->db->db_debug = false; // Return JSON, never an HTML database error page.
        try {
            $result = $this->asset_type_maintenance->lookup($this->input->post('asset_id'));
            $this->settings_response($result);
        } catch (InvalidArgumentException $e) {
            $this->settings_response(['status' => false, 'error' => $e->getMessage()], 400);
        } catch (Throwable $e) {
            log_message('error', 'Asset type settings lookup failed: ' . $e->getMessage());
            $status = in_array($e->getCode(), [404, 503], true) ? $e->getCode() : 503;
            $message = $status === 404 ? 'Asset type not found.' :
                'Unable to load asset type settings. Ask the administrator to apply patch_asset_type_maintenance.sql, then retry.';
            $this->settings_response(['status' => false, 'error' => $message], $status);
        } finally {
            $this->db->db_debug = $previousDebug;
        }
    }

    private function maintenance_defaults_from_form()
    {
        try {
            return Asset_type_maintenance::defaults(
                $this->input->post('maintenance_frequency_year'),
                $this->input->post('maintenance_reminder_days')
            );
        } catch (InvalidArgumentException $e) {
            show_error($e->getMessage(), 400);
            exit;
        }
    }


    public function getItemTypes()
    {
        $query = $this->db->select('item_types.*, vendor_part_number.part_number, vendor_manufacturing_number.manufacturer_name') // Select required fields
            ->from('item_types') // Main table
            ->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number', 'inner') // Join with vendor_part_number
            ->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer', 'inner') // Join with vendor_manufacturing_number
            ->get()
            ->result_array();
        echo json_encode($query);
    }
}
