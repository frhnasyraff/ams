<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Items_Ticket extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("issue_item_ticket_view")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $faulty = $this->db->select('*')
            ->from('fault_type_color_code')
            ->get()
            ->result();

        $asset = $this->db->select('*')
            ->from('equipments_asset')
            ->get()
            ->result();


        $this->load->view('header', ['title' => "Items Tickets", 'title2' => "list of Items Tickets", "styles" => [
            "design/css/custom-datatable.css"
        ]]);
        $this->load->view('items-ticket-list', [
            'asset' => $asset,
            'faulty' => $faulty
        ]);
        $this->load->view('footer', ['scripts' => ['design/js/items-ticket-list.js']]);
    }

    public function get_asset_details()
    {
        $equipment_id = $this->input->post('equipment_id');

        if (empty($equipment_id)) {
            echo json_encode(['error' => 'No equipment id provided']);
            return;
        }


        // Fetch other asset details
        $asset = $this->db->select('state_id, location_id')
            ->from('equipments_asset')
            ->where('equipment_id', $equipment_id)
            ->get()
            ->row();

        if ($asset && $equipment_id) {
            // Fetch state name
            $state = $this->db->select('state_name')
                ->from('states')
                ->where('id', $asset->state_id)
                ->get()
                ->row();

            // Fetch location name
            $location = $this->db->select('name')
                ->from('locations')
                ->where('id', $asset->location_id)
                ->get()
                ->row();

            // Fetch all related item names
            $item_types = $this->db->select('item_name, id')
                ->from('add_asset_items')
                ->where('asset_id', $equipment_id)
                ->get()
                ->result();



            // Prepare item names as an array
            $item_names = array_map(function ($item) {
                return [
                    'id' => $item->id,
                    'item_name' => $item->item_name,
                ];
            }, $item_types);

            // Return data as JSON
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
        if ($this->user_model->has_perm('edit_item_ticket')) {
            // Fetch asset data
            $asset = $this->db->select('*')
                ->from('equipments_asset')
                ->get()
                ->result();

            $item = $this->db->select('*')
                ->from('add_asset_items')
                ->get()
                ->result();

            $faulty = $this->db->select('*')
                ->from('fault_type_color_code')
                ->get()
                ->result();

            if ($this->input->get('id')) {
                // Decode the ID and fetch the ticket data
                $decoded_id = $this->steve->id_decode($this->input->get('id'));

                $query = $this->db->get_where('item_ticket', ["id" => $decoded_id]);
                $info = $query->row(); // Fetch a single row for clarity

                if ($info) {
                    // Load views with processed data
                    $this->load->view('header', ['title' => "Ticket - " . $info->number]);
                    $this->load->view('items-ticket-info', [
                        'info' => $info,
                        'asset' => $asset,
                        'faulty' => $faulty,
                        'item' => $item
                    ]);
                    $this->load->view('footer');
                } else {
                    redirect("equipments?error=Ticket not found");
                }
            } else {
                redirect("equipments?error=Ticket not found or you do not have permission to edit.");
            }
        } else {
            redirect('items_ticket?error=Tickets not found or you do not have permission to update tickets.');
        }
    }


    public function delete()
    {

        if ($this->input->get('id') && $this->user_model->has_perm('edit_item_ticket')) {
            $query = $this->db->get_where('item_ticket', ["id" => $this->steve->id_decode()]);


            $info = $query->result();

            if ($info) {
                // Perform the delete operation
                $this->db->where('id', $info[0]->id);
                if ($this->db->delete('item_ticket')) {

                    redirect("items_ticket?message=Ticket deleted successfully");
                } else {
                    // Log any database error
                    log_message('error', 'Failed to delete ticket: ' . $this->db->last_query());
                    redirect("items_ticket?error=Failed to delete the ticket");
                }
            } else {
                redirect("items_ticket?error=Ticket not found");
            }
        } else {
            redirect("items_ticket?error=Ticket not found or you do not have permission to delete.");
        }
    }


    public function ajax_list()
    {
        if ($this->user_model->has_perm('issue_item_ticket_view')) {
            // With all JOINs
            $this->db->select("item_ticket.*, 
                   fault_type_color_code.fault_type, 
                   equipments_asset.equipment_name, 
                   add_asset_items.item_name");
            $this->db->from("item_ticket");
            $this->db->join("fault_type_color_code", "fault_type_color_code.id = item_ticket.fault_type_id", "left");
            $this->db->join("equipments_asset", "equipments_asset.equipment_id = item_ticket.equipment_id", "left");
            $this->db->join("add_asset_items", "add_asset_items.id = item_ticket.item_id", "left");

            $query = $this->db->get();


            if (!$query) {
                // Output SQL error
                print_r($this->db->error());
                die();
            }

            // Return data in JSON format
            echo json_encode([
                "data" => $query->result()
            ]);
        } else {
            redirect("items_ticket?error=Ticket not found or you do not have permission.");
        }
    }



    public function update()
    {
        if ($this->input->post('id') && $this->user_model->has_perm('edit_item_ticket')) {
            // Get the ID from the request
            $id = $this->input->post('id');

            $issue_date = $this->input->post('issue_date') ?: date('Y-m-d');


            // Set the fields to be updated
            $this->db->set('number', $this->input->post('number'));
            $this->db->set('issue_date', $issue_date);
            $this->db->set('item_id', $this->input->post('item_type'));
            $this->db->set('equipment_id', $this->input->post('asset_number'));
            $this->db->set('fault_type_id', $this->input->post('fault_type'));
            $this->db->set('location', $this->input->post('location'));
            $this->db->set('state', $this->input->post('state'));
            $this->db->set('details_of_issue', $this->input->post('details_of_issue'));
            $this->db->set('severity', $this->input->post('severity'));
            $this->db->set('date_of_completion', $this->input->post('date_of_completion'));

            // Add WHERE clause to update only the specific record
            $this->db->where('id', $id);

            // Execute the update query
            if ($this->db->update("item_ticket")) {
                // Log the update and redirect with success message
                $this->logs->add("item_ticket", $id, "UPDATED", $_POST);
                redirect("items_ticket/index?message=Asset group was updated successfully.");
            } else {
                // Redirect with error message
                redirect("items_ticket/index?error=Update failed.");
            }
        } else {
            // Redirect if no ID is provided
            redirect("items_ticket/index?error=No permission or ID is blank");
        }
    }


    public function add()
    {

        if ($this->input->post('asset_number') && $this->user_model->has_perm('edit_item_ticket')) {


            // Fetch the last ticket number from the database
            $last_ticket = $this->db->select('number')
                ->order_by('id', 'DESC') // Assuming 'id' is the primary key
                ->limit(1)
                ->get('item_ticket')
                ->row();


            if ($last_ticket) {
                // Extract the numeric part of the last ticket number
                $last_number = (int)substr($last_ticket->number, 0, 3);
                $new_number = str_pad($last_number + 1, 3, '0', STR_PAD_LEFT);
            } else {
                // If no previous ticket exists, start with 001
                $new_number = '001';
            }

            $number_prefix = $new_number; // Set the ticket prefix
            $issue_date = $this->input->post('issue_date') ?: date('Y-m-d');
            $date_part = date('dmY', strtotime($issue_date));
            $formatted_number = $number_prefix . $date_part;


            $this->db->set('number', $formatted_number);
            $this->db->set('issue_date', $issue_date);
            $this->db->set('item_id', $this->input->post('item_type'));
            $this->db->set('equipment_id', $this->input->post('asset_number'));
            $this->db->set('fault_type_id', $this->input->post('fault_type'));
            $this->db->set('location', $this->input->post('location'));
            $this->db->set('state', $this->input->post('state'));
            $this->db->set('details_of_issue', $this->input->post('details_of_issue'));
            $this->db->set('severity', $this->input->post('severity'));
            $this->db->set('date_of_completion', $this->input->post('date_of_completion'));


            if ($this->db->insert('item_ticket')) {
                $this->logs->add("item_ticket", $this->db->insert_id(), "ADDED", $_POST);
                redirect("items_ticket?message=Added ticket successfully");
            } else {
                log_message('error', 'Database insert failed: ' . $this->db->last_query());
                $error = $this->db->error();
                log_message('error', 'Database error: ' . $error['message']);
                redirect("items_ticket?error=Database insert failed");
            }
        } else {
            log_message('error', 'Form submission failed: ' . print_r($this->input->post(), true));
            redirect("items_ticket?error=No permission to add ticket");
        }
    }
}
