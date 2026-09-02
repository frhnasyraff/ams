<?php
defined('BASEPATH') or exit('No direct script access allowed');

class preventive_maintenance extends CI_Controller
{
    public function __construct()
    {

        parent::__construct();

        $this->load->helper('url');
        $this->load->library('pagination');

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm('list_assets')) {

            die(redirect('/order_summary?error=No permission to view this content.'));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => 'Preventive Maintenance', 'title2' => 'MaintenanceDashboard', 'styles' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.css',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.css',
            'design/css/order-summary.css',
            'design/css/order-summary-cards.css',
            'design/css/custom-datatable.css',
            'design/vendor/dropzone/min/dropzone.min.css',
            'design/css/datepicker.css',
            'design/css/assets-type-dashboard.css?v=74',
        ]]);

        $this->load->view('preventive-maintenance', []);

        $this->load->view('footer', ['scripts' => [
            'https://api.mapbox.com/mapbox.js/v3.3.1/mapbox.js',
            'https://api.mapbox.com/mapbox-gl-js/v2.12.0/mapbox-gl.js',
            'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js',
            'https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@0.4.0/dist/chartjs-plugin-datalabels.min.js',
            'design/js/graph-colors.js',
            'design/vendor/dropzone/min/dropzone.min.js',
            'design/js/datepicker.js',
            'design/js/assets-type-dashboard.js',
            'design/js/assets-list.js',
            'design/js/preventive_table_list.js',

        ]]);
    }

    public function preventive_table_list()
    {
        $asset_type_id = $this->input->post("asset_id");
        $table_data = [];

        $data = $this->db->select('
        equipments_asset.*, 
        GROUP_CONCAT(DISTINCT CONCAT(add_asset_items.item_name, " (", 
        IFNULL(add_asset_items.manufacturer_name, "No Manufacturer"), ")") 
        SEPARATOR ", ") AS asset_items, 
        latest_maintenance.update_date AS latest_maintenance_date,
        latest_task.remarks AS latest_remarks,
        asset_types.name AS equipment_type_name,
        store_location.name AS store_location_name,
        GROUP_CONCAT(DISTINCT equipment_maintenance_asset.update_date ORDER BY equipment_maintenance_asset.update_date ASC) AS maintenance_history,
        next_maintenance_date.maintenance_date AS next_maintenance_date
    ')
            ->from("equipments_asset")
            ->join("asset_types", "asset_types.asset_id = equipments_asset.equipment_type", "left")
            ->join("store_location", "store_location.id = equipments_asset.store_location_id", "left")
            ->join("add_asset_items", "add_asset_items.asset_id = equipments_asset.equipment_id", "left")
            ->join("equipment_maintenance_asset", "equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id", "left")
            ->join("next_maintenance_date", "next_maintenance_date.equipment_id = equipments_asset.equipment_id", "left")
            ->join('(
            SELECT ema.*
            FROM equipment_maintenance_asset ema
            JOIN (
                SELECT equipment_id, MAX(created_at) AS max_created_at
                FROM equipment_maintenance_asset
                GROUP BY equipment_id
            ) latest_ema
            ON ema.equipment_id = latest_ema.equipment_id
            AND ema.created_at = latest_ema.max_created_at
            WHERE ema.maintenance_type_id = "preventive"
        ) AS latest_maintenance', "latest_maintenance.equipment_id = equipments_asset.equipment_id", "left")
            ->join('(
            SELECT mtd.*
            FROM maintenance_task_done mtd
            JOIN (
                SELECT equipment_maintenance_id, MAX(created_at) AS max_created_at
                FROM maintenance_task_done
                GROUP BY equipment_maintenance_id
            ) latest_mtd
            ON mtd.equipment_maintenance_id = latest_mtd.equipment_maintenance_id
            AND mtd.created_at = latest_mtd.max_created_at
        ) AS latest_task', "latest_task.equipment_maintenance_id = latest_maintenance.equipment_id", "left")
            ->where("equipments_asset.maintenance_date IS NOT NULL", null, false)
            ->where("equipments_asset.frequency_year IS NOT NULL", null, false)
            ->where("equipments_asset.maintenance_reminder_day IS NOT NULL", null, false)
            ->where("equipments_asset.equipment_type", $asset_type_id)
            ->group_by("equipments_asset.equipment_id")
            ->get()
            ->result();

        foreach ($data as $data) {
            if (empty($data->next_maintenance_date)) {
                continue;
            }

            try {
                $next_maintenance_date = new DateTime($data->next_maintenance_date);
            } catch (Exception $e) {
                continue;
            }

            $currentDate = new DateTime();
            $reminder_days = (int)$data->maintenance_reminder_day;
            $reminder_date = (clone $next_maintenance_date)->modify("-$reminder_days days");

            $status = null;
            $latest_maintenance_date = null;
            $latest_same_as_next = false;

            if (!empty($data->latest_maintenance_date)) {
                try {
                    $latest_maintenance_date = new DateTime($data->latest_maintenance_date);
                    if ($latest_maintenance_date->format("Y-m-d") === $next_maintenance_date->format("Y-m-d")) {
                        $latest_same_as_next = true;
                    }

                    if ($latest_maintenance_date >= $next_maintenance_date) {
                        $status = "complete";
                    } elseif ($next_maintenance_date < $currentDate) {
                        $status = "pending";
                    } elseif ($currentDate >= $reminder_date && $currentDate < $next_maintenance_date) {
                        $status = "Maintenance";
                    }
                } catch (Exception $e) {
                    $status = "pending";
                }
            } else {
                if ($next_maintenance_date >= $currentDate) {
                    $status = "complete";
                } else {
                    $status = "pending";
                }
            }

            // Build items array
            $itemArray = [];
            if (!empty($data->asset_items)) {
                $items = explode(", ", $data->asset_items);
                foreach ($items as $item) {
                    preg_match("/(.*?)\s*\((.*?)\)/", $item, $matches);
                    $itemArray[] = [
                        "item_name"         => $matches[1] ?? $item,
                        "manufacturer_name" => $matches[2] ?? "No Manufacturer",
                    ];
                }
            }

            // Add history records
            if (!empty($data->maintenance_history)) {
                $history_dates = explode(',', $data->maintenance_history);
                $i = 1;
                foreach ($history_dates as $past_date_raw) {
                    $past_date = trim($past_date_raw);
                    if (!empty($past_date)) {
                        $formatted_past = (new DateTime($past_date))->format("Y-m-d");
                        $table_data[] = (object)[
                            "equipment_id"           => $data->equipment_id,
                            "equipment_name"         => $data->equipment_name,
                            "interval_number"        => "Previous #" . $i,
                            "interval"               => $formatted_past,
                            "interval_start_date"    => null,
                            "interval_end_date"      => $formatted_past,
                            "equipment_type_name"    => $data->equipment_type_name,
                            "equipment_registration" => $data->equipment_registration,
                            "store_location_name"    => $data->store_location_name,
                            "items"                  => $itemArray,
                            "maintenance_records"    => $formatted_past,
                            "remarks"                => "Completed",
                            "current_status"         => "complete",
                        ];
                        $i++;
                    }
                }
            }

            // Avoid duplicate: skip if latest == next and status is complete
            if ($status === 'complete'&& $latest_same_as_next) {
                continue;
            }

            // Add upcoming maintenance record
            $table_data[] = (object)[
                "equipment_id"           => $data->equipment_id,
                "equipment_name"         => $data->equipment_name,
                "interval_number"        => "Next Maintenance",
                "interval"               => $next_maintenance_date->format("Y-m-d"),
                "interval_start_date"    => null,
                "interval_end_date"      => $next_maintenance_date->format("Y-m-d"),
                "equipment_type_name"    => $data->equipment_type_name,
                "equipment_registration" => $data->equipment_registration,
                "store_location_name"    => $data->store_location_name,
                "items"                  => $itemArray,
                "maintenance_records"    => $data->latest_maintenance_date ?? "No Date",
                "remarks"                => $data->latest_remarks ?? "No Remarks",
                "current_status"         => $status,
            ];
        }

        echo json_encode(['data' => $table_data]);
        die;
    }

    public function asset_type_picture()
    {
        if ($this->input->post('id')) {
            if ($_FILES['file']['error'] == UPLOAD_ERR_OK) {
                $tmp_name = $_FILES['file']['tmp_name'];
                // basename() may prevent filesystem traversal attacks;
                // further validation/sanitation of the filename may be appropriate
                $prefix = time();
                $name = $prefix . '-' . basename($_FILES['file']['name']);

                $folder = realpath('storage') . '/AssetType-' . $this->input->post('id');

                @mkdir($folder);

                if (move_uploaded_file($tmp_name, $folder . '/' . $name)) {
                    $this->db->set('asset_picture', $name);
                    $this->db->where('asset_id', $this->input->post('id'));

                    if ($this->db->update('asset_types')) {
                        $this->logs->add('ASSETSTYPE', $this->input->post('id'), 'ASSET_TYPE_PHOTO_UPLOADED', 'A new photo was uploaded.');
                    }
                }
            }
        }
    }
}


