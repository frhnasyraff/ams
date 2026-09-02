<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Ticket extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("issue_ticket_view")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $faulty = $this->db->select('*')
            ->from('fault_type_color_code')
            ->get()
            ->result();

        $asset = $this->db->select('equipment_id, equipment_name, equipment_registration, state_id, location_id')
            ->from('equipments_asset')
            ->get()
            ->result();

        $this->load->view('header', ['title' => "Tickets", 'title2' => "list of Tickets", "styles" => [
            "design/css/custom-datatable.css"
        ]]);
        $this->load->view('ticket-list', [
            'asset' => $asset,
            'faulty' => $faulty
        ]);
        $this->load->view('footer', ['scripts' => ['design/js/ticket-list.js']]);
    }

    public function get_asset_details()
    {
        $equipment_name = $this->input->post('equipment_name');

        // Fetch the asset details
        $asset = $this->db->select('*')
            ->from('equipments_asset')
            ->where('equipment_id', $equipment_name)
            ->get()
            ->row();

        if ($asset) {
            $state = $this->db->select('state_name')
                ->from('states')
                ->where('id', $asset->state_id)
                ->get()
                ->row();

            $location = $this->db->select('name')
                ->from('locations')
                ->where('id', $asset->location_id)
                ->get()
                ->row();

            // Fetch all related item names
            $item_types = $this->db->select('item_name, id')
                ->from('add_asset_items')
                ->where('asset_id', $asset->equipment_id)
                ->get()
                ->result();

            $item_names = array_map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                ];
            }, $item_types);

            echo json_encode([
                'state_name' => $state ? $state->state_name : '',
                'name' => $location ? $location->name : '',
                'item_names' => $item_names // Array of item names
            ]);
        } else {
            echo json_encode(['state_name' => '', 'name' => '', 'item_names' => []]);
        }
    }


    public function info()
    {
        if ($this->user_model->has_perm('edit_issue_ticket')) {

            if ($this->input->get('id')) {
                // Get the ticket with equipment name using JOIN
                $this->db->select('ticket.*, equipments_asset.equipment_name');
                $this->db->from('ticket');
                $this->db->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left');
                $this->db->where('ticket.id', $this->steve->id_decode());
                $query = $this->db->get();
                $info = $query->row(); // using row() instead of result() since you're using single record

                if ($info) {
                    // Get all assets for the dropdown
                    $assets = $this->db->select('equipment_id, equipment_name')
                        ->from('equipments_asset')
                        ->get()
                        ->result();

                    $this->load->view('header', ['title' => "Ticket - " . $info->ticket_number]);
                    $this->load->view('ticket-info', ['info' => $info, 'asset' => $assets]);
                    $this->load->view('footer');
                } else {
                    redirect("equipments?error=Ticket not found");
                }
            } else {
                redirect("equipments?error=Ticket not found or you do not have permission to edit.");
            }
        } else {
            redirect('assets?error=Tickets not found or you do not have permission to update tickets.');
        }
    }



    public function delete()
    {
        if ($this->input->get('id') && $this->user_model->has_perm('delete_issue_ticket')) {
            $query = $this->db->get_where('ticket', ["id" => $this->steve->id_decode()]);
            $info = $query->result();

            if ($info) {
                $ticket_id = $info[0]->id;

                // Delete related item_ticket entries
                $this->db->where('ticket_id', $ticket_id);
                $this->db->delete('item_ticket');

                // Delete the main ticket
                $this->db->where('id', $ticket_id);
                if ($this->db->delete('ticket')) {
                    redirect("ticket?message=Ticket deleted successfully");
                } else {
                    log_message('error', 'Failed to delete ticket: ' . $this->db->last_query());
                    redirect("ticket?error=Failed to delete the ticket");
                }
            } else {
                redirect("ticket?error=Ticket not found");
            }
        } else {
            redirect("ticket?error=Ticket not found or you do not have permission to delete.");
        }
    }



    public function ajax_list()
    {
        if ($this->user_model->has_perm('issue_ticket_view')) {
            // Get pagination and sorting parameters
            $start = $this->input->post('start');
            $length = $this->input->post('length');
            $order_column = $this->input->post('order')[0]['column'];
            $order_dir = $this->input->post('order')[0]['dir'];

            // Select the necessary fields from the ticket, equipment, and fault type tables
            $this->db->select('ticket.*, UPPER(equipments_asset.equipment_name) AS equipment_name, UPPER(fault_type_color_code.fault_type) AS fault_type');

            $this->db->from('ticket');
            $this->db->join('equipments_asset', 'ticket.equipment_id = equipments_asset.equipment_id', 'left');
            $this->db->join('fault_type_color_code', 'ticket.fault_type_id = fault_type_color_code.id', 'left');

            // Apply sorting based on DataTables order
            $this->db->order_by($order_column, $order_dir);

            // Limit the number of records based on pagination
            $this->db->limit($length, $start);

            // Execute the query to get the ticket data
            $query = $this->db->get();
            $data = $query->result_array();

            // Get total number of records without filtering (for pagination)
            $this->db->select('COUNT(*) as total');
            $this->db->from('ticket');
            $total_query = $this->db->get();
            $total_records = $total_query->row()->total;

            // Prepare the response
            $response = array(
                "draw" => $_POST['draw'],               // Required by DataTables for handling pagination
                "recordsTotal" => $total_records,       // Total number of records
                "recordsFiltered" => $total_records,    // Same as recordsTotal since no filtering is applied
                "data" => $data                         // Data to be shown in the table
            );

            // Return the response as JSON
            echo json_encode($response);
        } else {
            redirect('assets?error=Tickets not found or you do not have permission to see tickets.');
        }
    }







    public function update()
    {
        if ($this->input->post('id') && $this->user_model->has_perm('edit_issue_ticket')) {
            // Get the ID from the request
            $id = $this->input->post('id');

            $issue_date = $this->input->post('issue_date') ?: date('Y-m-d');


            $asset = $this->input->post('asset_number');


            $fault_type = $this->db->select('id')
                ->from('fault_type_color_code')
                ->where('fault_type', $this->input->post('ticket_fault_type'))
                ->get()
                ->row();
            $date_of_completion = $this->input->post('date_of_completion') ?: null;
            // Set the fields to be updated
            $this->db->set('issue_date', $issue_date .= ' ' . date('H:i:s'));
            $this->db->set('equipment_id', $asset);
            $this->db->set('details_of_issue', strtoupper($this->input->post('details_of_issue')));
            $this->db->set('severity', $this->input->post('severity'));
            $this->db->set('date_of_completion', $date_of_completion ?? null);


            // Add WHERE clause to update only the specific record
            $this->db->where('id', $id);

            // Execute the update query
            if ($this->db->update("ticket")) {
                // Log the update and redirect with success message
                $this->logs->add("ticket", $id, "TICKET_UPDATED", $_POST);
                redirect("ticket/index?message=Ticket was updated successfully.");
            } else {
                // Redirect with error message
                redirect("ticket/index?error=Update failed.");
            }
        } else {
            // Redirect if no ID is provided
            redirect("ticket/index?error=No permission or ID is blank");
        }
    }


    public function add()
    {
        if (!$this->input->post('asset_number') || !$this->user_model->has_perm('issue_ticket_add')) {
            log_message('error', 'Form submission failed: ' . print_r($this->input->post(), true));
            redirect("ticket?error=No permission to add ticket");
            return;
        }
        $this->load->model('asset_logs');

        $asset_number = $this->input->post('asset_number');
        $issue_date = $this->input->post('issue_date') ?: date('Y-m-d');
        $date_part = date('dmY', strtotime($issue_date));

        // Update Equipment Status
        // 1. Get the old status first (for proper logging)
        $old_status = $this->db->select('equipment_status')
            ->from('equipments_asset')
            ->where('equipment_id', $asset_number)
            ->get()
            ->row('equipment_status');

        // 2. Update Equipment Status
        $this->db->where('equipment_id', $asset_number)
            ->update('equipments_asset', ['equipment_status' => 'MAINTENANCE']);

        // 4. Prepare proper log description
        $asset_log_description = "Updated asset: - equipment_status: '{$old_status}' → 'MAINTENANCE'";
        // 5. Insert into asset logs
        $this->asset_logs->add('assets/info', $asset_number, 'Asset_Updated', $asset_log_description);

        // Fetch Last Ticket Number
        $last_ticket = $this->db->select('ticket_number')
            ->order_by('id', 'DESC')
            ->limit(1)
            ->get('ticket')
            ->row();

        if ($last_ticket) {
            // Extract the first three digits (running number) from the last ticket number
            $last_number = (int) substr($last_ticket->ticket_number, 0, 3);
            $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
        } else {
            // Start from '001' if no previous ticket exists
            $new_number = '001';
        }

        // Get the issue date (use today's date if not provided)
        $issue_date = $this->input->post('issue_date') ?: date('Y-m-d');

        // Generate date part in DDMMYYYY format
        $date_part = date('dmY', strtotime($issue_date));

        // Concatenate to form the final ticket number
        $formatted_ticket_number = $new_number . $date_part;

        // Retrieve Fault Type ID
        $fault_type = $this->db->select('id')
            ->from('fault_type_color_code')
            ->where('fault_type', $this->input->post('ticket_fault_type'))
            ->get()
            ->row();

        // Prepare and Insert Ticket
        $ticket_data = [
            'ticket_number' => $formatted_ticket_number,
            'issue_date' => $issue_date .= ' ' . date('H:i:s'),
            'equipment_id' => $asset_number,
            'fault_type_id' => $fault_type->id ?? null,
            'ticket_location' => $this->input->post('ticket_location'),
            'ticket_state' => $this->input->post('ticket_state'),
            'details_of_issue' => $this->input->post('details_of_issue'),
            'severity' => $this->input->post('severity'),
            'date_of_completion' => $this->input->post('date_of_completion')
        ];

        if ($this->db->insert('ticket', $ticket_data)) {
            $ticket_id = $this->db->insert_id();
            $this->logs->add("ticket", $ticket_id, "TICKET_ADDED", $_POST);

            // Fetch Item Status ID for 'MAINTENANCE'
            $item_status = $this->db->get_where('item_status', ['name' => 'MAINTENANCE'])->row();

            if (!$item_status) {
                log_message('error', 'MAINTENANCE status not found.');
                redirect("ticket?error=MAINTENANCE status not found");
                return;
            }

            $item_status_id = $item_status->id;
            $item_names = $this->input->post('item_name');
            $item_fault_types = $this->input->post('item_fault_type');

            if (is_array($item_names) && is_array($item_fault_types)) {
                foreach ($item_names as $index => $item_name) {
                    $this->db->where('id', $item_name)
                        ->set('item_status_id', $item_status_id)
                        ->update('add_asset_items');

                    $item_fault = $this->db->select('id')
                        ->where('fault_type', $item_fault_types[$index] ?? '')
                        ->get('fault_type_color_code')
                        ->row();

                    // Fetch last item ticket number
                    $last_item_ticket = $this->db->select('number')
                        ->order_by('id', 'DESC')
                        ->limit(1)
                        ->get('item_ticket')
                        ->row();

                    if ($last_item_ticket) {
                        // Extract the first three digits (running number)
                        $last_item_number = (int) substr($last_item_ticket->number, 0, 3);
                        $new_item_number = str_pad($last_item_number + 1, 3, '0', STR_PAD_LEFT);
                    } else {
                        $new_item_number = '001'; // Start from '001' if no previous ticket exists
                    }

                    // Generate item ticket number in the same format
                    $formatted_item_number = $new_item_number . $date_part;



                    $item_ticket_data = [
                        'ticket_id' => $ticket_id,
                        'number' => $formatted_item_number,
                        'issue_date' => $issue_date,
                        'item_id' => $item_name,
                        'equipment_id' => $asset_number,
                        'fault_type_id' => $item_fault->id ?? null,
                        'location' => $this->input->post('ticket_location'),
                        'state' => $this->input->post('ticket_state'),
                        'details_of_issue' => $this->input->post('details_of_issue'),
                        'severity' => $this->input->post('severity'),
                        'date_of_completion' => $this->input->post('date_of_completion')
                    ];

                    if (!$this->db->insert('item_ticket', $item_ticket_data)) {
                        log_message('error', 'Item Ticket Insert Failed: ' . json_encode($this->db->error()));
                    }
                }
            }
            redirect("ticket?message=Added ticket successfully");
        } else {
            log_message('error', 'Database insert failed: ' . json_encode($this->db->error()));
            redirect("ticket?error=Database insert failed");
        }
    }

    public function itemList()
    {
        $equipmentId = $this->input->get('id');

        $query = $this->db->select("item_ticket.*, 
                            fault_type_color_code.fault_type, 
                            equipments_asset.equipment_name, 
                            add_asset_items.item_name")
            ->from("item_ticket")
            ->join("fault_type_color_code", "fault_type_color_code.id = item_ticket.fault_type_id", "left")
            ->join("equipments_asset", "equipments_asset.equipment_id = item_ticket.equipment_id", "left")
            ->join("add_asset_items", "add_asset_items.id = item_ticket.item_id", "left")
            ->where("item_ticket.equipment_id", $equipmentId)
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
