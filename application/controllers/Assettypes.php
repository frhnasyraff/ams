<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assettypes extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assettypes')) {
            die(redirect('/order_summary?error=No permission to view this content.'));
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
    
    $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js']]);
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
                $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js']]);
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
        //         $this->load->view('footer', ['scripts' => ['design/js/assettypes-list.js']]);
        //     } else {
        //         redirect('assettypes?error=Asset type not found');
        //     }
        // } else {
        //     redirect('assettypes?error=Asset type not found or you do not have permission to edit.');
        // }
    }

    public function ajax_list()
    {
        $search[] = ['asset_types.active', 1];
        die($this->steve->datatables_mysql('asset_types', ['name', 'manufacturer', 'vendor_part_number', 'manufacturer_name', 'part_number', 'rental_price', 'selling_price', 'rental_duration', 'active'], [], [['vendor_manufacturing_number', 'vendor_manufacturing_number.id = asset_types.manufacturer'], ['vendor_part_number', 'vendor_part_number.id = asset_types.vendor_part_number']]));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by('commodity_code', 'asc')->select("id as id, CONCAT(commodity_code, ' (', name, ')') as label, CONCAT(commodity_code, ' - ', name) as value")->group_start()->like('commodity_code', $this->input->get('term'))->or_like('name', $this->input->get('term'))->group_end()->get_where('asset_types', ['active' => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm('assettypes') && $this->input->post('id')) {
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
        $name               = $this->input->post('name', true);
        $manufacturer       = $this->input->post('manufacturer');
        $vendor_part_number = $this->input->post('vendor_part_number');
        $calibration        = $this->input->post('calibration') ? '1' : '0';
        $maintenance        = $this->input->post('maintenance') ? '1' : '0';

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
                'useful_life_years' => $useful_life_years,
                'salvage_value'     => $salvage_value,
                'depreciate_value'  => null
            ]);
        }

        $this->db->where('asset_id', $asset_id);
        $this->db->update('asset_types');

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
    if ($this->user_model->has_perm('add_assettypes') && $this->input->post('name')) {

        $name               = $this->input->post('name', true);
        $manufacturer       = $this->input->post('manufacturer');
        $vendor_part_number = $this->input->post('vendor_part_number');
        $calibration        = $this->input->post('calibration') ? '1' : '0';
        $maintenance        = $this->input->post('maintenance') ? '1' : '0';

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
                'useful_life_years' => $useful_life_years,
                'salvage_value'     => $salvage_value,
                'depreciate_value'  => null
            ]);
        }

        $this->db->insert('asset_types');
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


    public function asset_calibration()
    {
        if (isset($_POST['asset_id'])) {
            $assetId = $_POST['asset_id'];

            // Sanitize the input (optional, for added security)
            $assetId = intval($assetId);

            // Query the database for asset type details
            $data = $this->db->select('*')
                ->from('asset_types')
                ->where('asset_id', $assetId)
                ->get()
                ->row(); // Use ->row() for a single result

            // Query the database for related asset items
            $data1 = $this->db->select('asset_type_items.* , item_types.calibration, item_types.maintenance , vendor_manufacturing_number.manufacturer_name as manufacturer , vendor_part_number.part_number as vendor_part_number')
                ->from('asset_type_items')
                ->join('item_types', 'asset_type_items.item_type_id = item_types.id')
                ->join('vendor_manufacturing_number', 'vendor_manufacturing_number.id = item_types.manufacturer ', 'Left')
                ->join('vendor_part_number', 'vendor_part_number.id = item_types.vendor_part_number', 'Left')
                ->where('asset_type_id', $assetId)
                ->get()
                ->result(); // Use ->result() to get multiple rows

            // Prepare the items data
            $items = [];
            foreach ($data1 as $item) {
                $items[] = [
                    'item_type_id' => $item->item_type_id, // or whatever field you want to include
                    'qty' => $item->quantity,
                    'manufacturer' => $item->manufacturer,
                    'vendor_part_number' => $item->vendor_part_number,
                    'calibration' => $item->calibration,
                    'maintenance' => $item->maintenance
                ];
            }

            // Return JSON response
            header('Content-Type: application/json');
            if ($data) {
                echo json_encode([
                    'calibration' => $data->calibration, // Calibration status
                    'maintenance' => $data->maintenance, // maintenance status
                    'manufacturer' => $data->manufacturer, // Manufacturer
                    'vpn' => $data->vendor_part_number, // Vendor part number
                    'items' => $items // Return the array of items correctly
                ]);
            } else {
                echo json_encode(['calibration' => 0, 'items' => []]);
            }
        } else {
            // Return an error response if asset_id is not provided
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Asset ID not provided']);
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
