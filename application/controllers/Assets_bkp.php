<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Assets extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_equipments")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {
        $this->load->view('header', ['title' => "Assets", 'title2' => "list of Assets", "styles" => ["design/css/datepicker.css"]]);
        $this->load->view('asset-list', []);
        $this->load->view('footer', ['scripts' => ["design/js/datepicker.js", "design/vendor/moment.js-2.24.0/moment.min.js", 'design/js/assets-list.js']]);
    }

    public function info()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_equipments")) {

            $query = $this->db->get_where('equipments_asset', ["equipment_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $user_in_groups = [];

                foreach ($this->db->where("equipment_id", intval($info[0]->equipment_id))->get("equipment_group_asset")->result() as $user) {
                    $user_in_groups[] = $user->equipment_group_id;
                }

                $this->load->view('header', ['title' => "Assets - " . $info[0]->equipment_name, "styles" => ["design/vendor/dropzone/min/dropzone.min.css", 'design/css/multi-select.css', "design/css/datepicker.css"]]);
                $this->load->view('asset-info', ['info' => $info[0], "user_in_groups" => $user_in_groups]);
                $this->load->view('footer', ['scripts' => ["design/vendor/dropzone/min/dropzone.min.js", "design/js/datepicker.js", 'design/js/jquery.multi-select.js', 'design/js/assets-list.js']]);
            } else {
                redirect("assets?error=Asset not found");
            }
        } else {
            redirect("assets?error=Asset not found or you do not have permission to edit.");
        }
    }

    public function maintenance()
    {
        if ($this->input->get('id') && $this->user_model->has_perm("edit_equipments")) {

            $query = $this->db->join("equipments_asset", "equipments_asset.equipment_id = equipment_maintenance_asset.equipment_id", "left")->get_where('equipment_maintenance_asset', ["equipment_maintenance_asset.equipment_maintenance_id" => $this->steve->id_decode()]);

            $info = $query->result();

            if ($info) {
                $this->load->view('header', ['title' => "Asset repair - " . $info[0]->equipment_name, "styles" => []]);
                $this->load->view('equipment-maintenance-info', ['info' => $info[0]]);
                $this->load->view('footer', ['scripts' => []]);
            } else {
                redirect("assets?error=Asset maintenance not found");
            }
        } else {
            redirect("assets?error=Asset not found or you do not have permission to edit.");
        }
    }

    public function add_maintance_photo()
    {
        $response = [];

        foreach ($_FILES["file"]['name'] as $id => $file) {
            if ($_FILES['file']["error"][$id] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']["tmp_name"][$id];

                $prefix = time();

                $name = $prefix . "-" . basename($file);

                $folder = realpath("storage") . "/EQ-" . $this->input->get("id");

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . "/" . $name)) {
                    $response[] = $name;
                }
            }
        }
        die(json_encode([files => $response]));
    }

    public function mileage_ajax_list()
    {
        die($this->steve->datatables_mysql("equipment_mileage_asset", ["date_recorded", "mileage"], [["equipment_id", $this->input->post('id')]]));
    }

    public function consumable_ajax_list()
    {
        die($this->steve->datatables_mysql("equipment_consumables_asset", ["date_recorded", "consumable_name"], [["equipment_id", $this->input->post('id')]], [["consumables", "consumables.consumable_id = equipment_consumables_asset.consumable_id"]]));
    }

    public function usage_ajax_list()
    {
        die($this->steve->datatables_mysql(
            "vehicle_history_asset",
            [
                "vh_date",
                "vh_start_time",
                "vh_start_end",
                "vh_location_start",
                "vh_location_end",
                "vh_driver_name_ic_number",
                "worker_name"
            ],
            [["equipment_id", $this->input->post('id')]],
            [["workers", "workers.worker_id = vehicle_history_asset.driver_id"]]
        ));
    }

    public function maintenance_ajax_list()
    {
        die($this->steve->datatables_mysql("equipment_maintenance_asset", ["maintenance_date", "maintenance_notes"], [["equipment_id", $this->input->post('id')]]));
    }

    public function ajax_list()
    {
        $search = [];
        $join = [["equipment_types_asset", "equipment_types_asset.equipment_type_id = equipments_asset.equipment_type"]];
        if ($this->input->post("equipment_type")) {
            $search[] = ["equipments_asset.equipment_type", $this->input->post("equipment_type")];
        }
        if ($this->input->post("equipment_group")) {
            $search[] = ["equipment_group_asset.equipment_group_id", $this->input->post("equipment_group")];
            $join[] = ["equipment_group_asset", "equipment_group_asset.equipment_id = equipments_asset.equipment_id"];
        }

        die($this->steve->datatables_mysql("equipments_asset", ["equipment_name", "equipment_registration"], $search, $join, "equipment_picture,equipment_name, equipment_registration, equipments_asset.equipment_id, equipment_type_short_code, equipment_type_colour, equipment_type_name, equipment_status, current_mileage, next_service_mileage, next_service_date, equipments_asset.active"));
    }

    public function active_ajax_list()
    {
        $search = [["vessel_visit_equipments.operation_date >= CURDATE()"]];
        if ($this->input->post("equipment_type")) {
            $search[] = ["equipments_asset.equipment_type", $this->input->post("equipment_type")];
        }
        die($this->steve->datatables_mysql("vessel_visit_equipments", [], $search, [["equipments_asset", "equipments_asset.equipment_id = vessel_visit_equipments.equipment_id"], ["vessel_visits", "vessel_visits.vessel_visit_id = vessel_visit_equipments.vessel_visit_id"], ['port_wharfs', "port_wharfs.port_wharf_id = vessel_visits.port_wharf_id"]], "port_wharfs.wharf_id, equipments_asset.equipment_name, gang, shift, equipments_asset.equipment_id, operation_date"));
    }

    public function search_ajax()
    {
        $info = $this->db->order_by("commodity_code", "asc")->select("commodity_id as id, CONCAT(commodity_code, ' (', description, ')') as label, CONCAT(commodity_code, ' - ', description) as value")->group_start()->like("commodity_code", $this->input->get("term"))->or_like("description", $this->input->get("term"))->group_end()->get_where("operation_types", ["active" => 1])->result();

        die(json_encode($info));
    }

    public function state_ajax()
    {
        if ($this->user_model->has_perm("edit_equipments") && $this->input->post('id')) {
            die($this->steve->active_toggle("equipments_asset", "equipment_id"));
        }
    }

    public function assign_groups()
    {
        if ($this->user_model->has_perm("assign_equipment_groups") && $this->input->post('id')) {

            $equipment_id = intval($this->input->post('id'));
            $this->db->delete('equipment_group_asset', array('equipment_id' => $equipment_id));

            foreach ($this->input->post('groups') as $role) {
                $this->db->set('equipment_id', $equipment_id)->set('equipment_group_id', $role);
                $this->db->insert('equipment_group_asset');
            }
            $this->logs->add("equipments_asset", $equipment_id, "GROUPS_UPDATED", $_POST);
            redirect("assets/info?id=" . $this->steve->id_encode($this->input->post('id')) . "&message=Group(s) association saved successfully");
        }
    }

    public function update()
    {
        if ($this->user_model->has_perm("edit_equipments") && $this->input->post('id')) {
            $this->db->set('equipment_name', $this->input->post('name'));
            $this->db->set('equipment_registration', $this->input->post('code'));
            if ($this->input->post('equipment_type')) {
                $this->db->set('equipment_type', $this->input->post('equipment_type'));
            }

            if ($this->input->post('equipment_manufacturer')) {
                $this->db->set('equipment_manufacturer', $this->input->post('equipment_manufacturer'));
            }

            if ($this->input->post('purchase_date')) {
                $this->db->set('purchase_date', $this->steve->to_date($this->input->post('purchase_date')));
            }

            if ($this->input->post('equipment_status')) {
                $this->db->set('equipment_status', $this->input->post('equipment_status'));
            }

            $this->db->set('equipment_notes', $this->input->post('notes'));

            $this->db->set('equipment_safe_load', $this->input->post('safe_load'));

            $this->db->where("equipment_id", intval($this->input->post('id')));

            if ($this->db->update("equipments_asset")) {
                $this->logs->add("equipments_asset", $this->input->post('id'), "ASSET_UPDATED", $_POST);
                redirect("assets/index?message=Asset was updated successfully.");
            } else {
                redirect("assets/index?error=Update failed.");
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
                $this->logs->add("equipments_asset", $this->input->post('id'), "MILEAGE_ADDED", $_POST);

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
                $this->logs->add("equipments_asset", $this->input->post('consumable_id'), "CONSUMABLE_ADDED", $_POST);

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
            $this->db->set('equipment_id', $this->input->post('id'));
            $this->db->set('vh_location_start', $this->input->post('vh_location_start'));
            $this->db->set('vh_location_end', $this->input->post('vh_location_end'));

            $driver_Spilt = "0|None";
            $driver_explode = explode('|', $driver_Spilt);
            if ($this->input->post('driver_id') && $this->input->post('driver_id') !== null) {
                $driver_explode = explode('|', $this->input->post('driver_id'));
            }
            $this->db->set('driver_id', $driver_explode[0]);
            $this->db->set('vh_driver_name_ic_number', $driver_explode[1]);

            if ($this->db->insert('vehicle_history_asset')) {
                $this->logs->add("equipments", $this->input->post('vh_id'), "ASSET_USAGE_ADDED", $_POST);

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
                $this->logs->add("equipments_asset", $this->input->post('id'), "SCHEDULED_MAINTENANCE_ADDED", $_POST);
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
                $this->logs->add("equipments", $this->input->post('id'), "MAINTENANCE_ADDED", $_POST);
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
        if ($this->user_model->has_perm("add_equipments") && $this->input->post('name')) {
            $this->db->set('equipment_name', $this->input->post('name'));
            $this->db->set('equipment_registration', $this->input->post('code'));

            if ($this->input->post('equipment_type')) {
                $this->db->set('equipment_type', $this->input->post('equipment_type'));
            }
            if ($this->input->post('equipment_manufacturer')) {
                $this->db->set('equipment_manufacturer', $this->input->post('equipment_manufacturer'));
            }
            $this->db->set('equipment_notes', $this->input->post('notes'));
            $this->db->set('equipment_safe_load', $this->input->post('safe_load'));
            if ($this->input->post('current_mileage')) {
                $this->db->set('current_mileage', $this->input->post('current_mileage'));
            }

            if ($this->input->post('purchase_date')) {
                $this->db->set('purchase_date', $this->steve->to_date($this->input->post('purchase_date')));
            }

            if ($this->input->post('equipment_status')) {
                $this->db->set('equipment_status', $this->input->post('equipment_status'));
            }

            if ($this->input->post('service_every_mileage')) {
                $this->db->set('service_every_mileage', $this->input->post('service_every_mileage'));
            }
            if ($this->input->post('next_service_mileage')) {
                $this->db->set('next_service_mileage', $this->input->post('next_service_mileage'));
            }
            if ($this->input->post('last_service_date')) {
                $this->db->set('last_service_date', $this->steve->to_date($this->input->post('last_service_date')));
            }
            $this->db->set('service_interval_weeks', $this->input->post('service_interval_weeks') ? $this->input->post('service_interval_weeks') : 0);

            if ($this->input->post('next_service_date')) {
                $this->db->set('next_service_date', $this->steve->to_date($this->input->post('next_service_date')));
            }
            if ($this->db->insert('equipments_asset')) {
                $this->logs->add("equipments_asset", $this->db->insert_id(), "ASSET_ADDED", $_POST);
                redirect("assets?message=Added Asset successfully");
            } else {
                redirect("assets?error=Adding Asset failed");
            }
        } else {
            redirect("assets?error=No permission to add Asset");
        }
    }
    public function qrgen()
    {

        if ($this->user_model->has_perm("qr_generator") && $this->input->get('id')) {

            $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));
            $getRes = $this->db->get("equipments_asset");
            if ($getRes->num_rows() > 0) {
                $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));
                if ($this->db->update('equipments_asset', ['qr_code' => 1])) {
                    redirect("assets/info?id=" . ($this->input->get('id')) . "&message=QR Code has been generated successfully...#nav-qr");
                } else {
                    redirect("assets?error=Sorry QR code could not be generated at this moment, try again later.");
                }
            }
        } else {
            redirect("assets?error=No permission to Generate QR Code");
        }
    }
    public function qrdel()
    {

        if ($this->user_model->has_perm("qr_generator") && $this->input->get('id')) {

            $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));
            $getRes = $this->db->get("equipments_asset");
            if ($getRes->num_rows() > 0) {
                $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));
                if ($this->db->update('equipments_asset', ['qr_code' => 0])) {
                    redirect("assets/info?id=" . ($this->input->get('id')) . "&message=QR Code has been Deleted successfully...#nav-qr");
                } else {
                    redirect("assets?error=Sorry QR code could not be Deleted at this moment, try again later.");
                }
            }
        } else {
            redirect("assets?error=No permission to Delete QR Code");
        }
    }

    public function upload_picture()
    {
        if ($this->input->post("id")) {
            if ($_FILES["file"]["error"] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES["file"]["tmp_name"];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . "-" . basename($_FILES["file"]["name"]);

                $folder = realpath("storage") . "/Asset-" . $this->input->post("id");

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . "/" . $name)) {
                    $this->db->set("equipment_picture", $name);
                    $this->db->where("equipment_id", $this->input->post("id"));

                    if ($this->db->update("equipments_asset")) {
                        $this->logs->add("ASSETS", $this->input->post("id"), "ASSET_PHOTO_UPLOADED", "A new photo was uploaded.");
                    }
                }
            }
        }
    }
}
