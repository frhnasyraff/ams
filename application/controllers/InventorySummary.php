<?php
defined('BASEPATH') or exit('No direct script access allowed');

class InventorySummary extends CI_Controller
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


        $this->load->view('header', ['title' => 'Inventory Summary', 'title2' => 'Inventory Summary', 'styles' => [
            'design/css/performance-summary.css',

            'design/css/custom-datatable.css'
        ]]);

        $this->load->view('inventory-summary', []);

        $this->load->view('footer', ['scripts' => [


            'design/vendor/chart.js/Chart.min.js',
            'design/js/inventory-summary.js?v=6',
            'design/js/getAssetSummary_table.js?v=7'


        ]]);
    }

    public function getAssetSummary()
    {
        if (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
            die('Invalid request');
        }

        // Current schema implementation. The legacy per-type implementation is retained below.
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->buildAssetSummaryRows(false)));

        // Legacy implementation retained for reference (unreachable).
        // Fetch all unique asset types
        $equipment_types = $this->db->select('asset_id, name')
            ->from('asset_types')
            ->get()
            ->result();

        $data = [];

        foreach ($equipment_types as $type) {
            // Total assets for this type
            $total_assets = $this->db->select('COUNT(*) as total')
                ->from('equipments_asset')
                ->where('equipment_type', $type->asset_id)
                ->get()
                ->row()
                ->total;

            if ($total_assets > 0) {
                // Count assets that are currently in use
                $assets_serviceable = $this->db->select('COUNT(*) as in_use_count')
                    ->from('equipments_asset')
                    ->where('equipment_type', $type->asset_id)
                    ->where('equipment_status', 'SERVICEABLE')
                    ->get()
                    ->row()
                    ->in_use_count;

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
                        'left' // Left join to include assets with no maintenance records
                    )
                    ->where('equipments_asset.equipment_type', $type->asset_id)
                    ->where('equipments_asset.equipment_status', 'MAINTENANCE') // Only count assets in maintenance
                    ->get()
                    ->row()
                    ->corrective_count;


                $today = date('Y-m-d');

                // PREVENTIVE MAINTENANCE STATUS COUNTS
                $preventive_maintenance = $this->db
                    ->select("
                        SUM(
                            CASE 
                                WHEN latest_maintenance.update_date >= nmd.maintenance_date THEN 1
                                ELSE 0
                            END
                        ) AS complete_count,
                        SUM(
                            CASE 
                                WHEN (
                                    (latest_maintenance.update_date IS NULL OR latest_maintenance.update_date < nmd.maintenance_date)
                                    AND nmd.maintenance_date >= '{$today}'
                                ) THEN 1
                                ELSE 0
                            END
                        ) AS in_maintenance_count,
                        SUM(
                            CASE 
                                WHEN (
                                    (latest_maintenance.update_date IS NULL OR latest_maintenance.update_date < nmd.maintenance_date)
                                    AND nmd.maintenance_date < '{$today}'
                                ) THEN 1
                                ELSE 0
                            END
                        ) AS pending_count
                    ", false)
                    ->from('equipments_asset ea')
                    ->join("
                        (
                            SELECT ema1.*
                            FROM equipment_maintenance_asset ema1
                            JOIN (
                                SELECT equipment_id, MAX(created_at) AS max_created
                                FROM equipment_maintenance_asset
                                WHERE maintenance_type_id = 'preventive'
                                GROUP BY equipment_id
                            ) ema2 ON ema1.equipment_id = ema2.equipment_id AND ema1.created_at = ema2.max_created
                        ) AS latest_maintenance", 
                        'latest_maintenance.equipment_id = ea.equipment_id', 'left'
                    )
                    ->join('next_maintenance_date nmd', 'nmd.equipment_id = ea.equipment_id', 'left')
                    ->where('ea.equipment_type', $type->asset_id)
                    ->where('ea.maintenance_date IS NOT NULL', null, false)
                    ->where('ea.frequency_year IS NOT NULL', null, false)
                    ->where('ea.maintenance_reminder_day IS NOT NULL', null, false)
                    ->get()
                    ->row();

                    // Ensure no nulls (in case DB returns null on SUM)
                    $complete_count = (int) ($preventive_maintenance->complete_count ?? 0);
                    $in_maintenance_count = (int) ($preventive_maintenance->in_maintenance_count ?? 0);
                    $pending_count = (int) ($preventive_maintenance->pending_count ?? 0);

                    $preventive_maintenance_total = $complete_count + $in_maintenance_count + $pending_count;

                // Calculate percentages safely
                $in_use_percentage = $total_assets > 0 ? ($assets_serviceable / $total_assets) * 100 : 0;
                $corrective_percentage = $total_assets > 0 ? ($corrective_maintenance / $total_assets) * 100 : 0;
                $preventive_percentage = $total_assets > 0 ? ($preventive_maintenance_total / $total_assets) * 100 : 0;

                // Append data
                $data[] = [
                    'total_assets'           => $total_assets,
                    'equipment_type'         => $type->name,
                    'assets_serviceable'     => $assets_serviceable,
                    'corrective'             => $corrective_maintenance,
                    'preventive'             => $preventive_maintenance_total,
                    'in_use_percentage'      => round($in_use_percentage, 2),
                    'corrective_percentage'  => round($corrective_percentage, 2),
                    'preventive_percentage'  => round($preventive_percentage, 2),
                ];

            }
        }

        // Log the response data for debugging
        error_log("Response Data: " . print_r($data, true));

        // Return JSON response
        echo json_encode($data);
        die;
    }



    public function getAssetSummary_table()
    {
        // Current schema implementation. The legacy per-type implementation is retained below.
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->buildAssetSummaryRows(true)));

        // Legacy implementation retained for reference (unreachable).
        // Fetch all unique equipment types without any filters
        $equipment_types = $this->db->select('asset_id, name')
            ->from('asset_types')
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
                // Total Locations
                $total_locations = $this->db->select('COUNT(DISTINCT location_id) as total_locations')
                    ->from('equipments_asset')
                    ->where('equipment_type', $type->asset_id)
                    ->get()
                    ->row()
                    ->total_locations;



                // Assets where equipment_status = IN USE
                $assets_serviceable = $this->db->select('COUNT(*) as in_use_count')
                    ->from('equipments_asset')
                    ->where('equipment_type', $type->asset_id)
                    ->where('equipment_status', 'SERVICEABLE')
                    ->get()
                    ->row()
                    ->in_use_count;

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
                        'left' // Left join to include assets with no maintenance records
                    )
                    ->where('equipments_asset.equipment_type', $type->asset_id)
                    ->where('equipments_asset.equipment_status', 'MAINTENANCE') // Only count assets in maintenance
                    ->get()
                    ->row()
                    ->corrective_count;




                // Assets with maintenance_type_id = preventive
                
                 // PREVENTIVE MAINTENANCE STATUS COUNTS
                $preventive_maintenance = $this->db
                    ->select("
                        SUM(
                            CASE 
                                WHEN latest_maintenance.update_date >= nmd.maintenance_date THEN 1
                                ELSE 0
                            END
                        ) AS complete_count,
                        SUM(
                            CASE 
                                WHEN (
                                    (latest_maintenance.update_date IS NULL OR latest_maintenance.update_date < nmd.maintenance_date)
                                    AND nmd.maintenance_date >= '{$today}'
                                ) THEN 1
                                ELSE 0
                            END
                        ) AS in_maintenance_count,
                        SUM(
                            CASE 
                                WHEN (
                                    (latest_maintenance.update_date IS NULL OR latest_maintenance.update_date < nmd.maintenance_date)
                                    AND nmd.maintenance_date < '{$today}'
                                ) THEN 1
                                ELSE 0
                            END
                        ) AS pending_count
                    ", false)
                    ->from('equipments_asset ea')
                    ->join("
                        (
                            SELECT ema1.*
                            FROM equipment_maintenance_asset ema1
                            JOIN (
                                SELECT equipment_id, MAX(created_at) AS max_created
                                FROM equipment_maintenance_asset
                                WHERE maintenance_type_id = 'preventive'
                                GROUP BY equipment_id
                            ) ema2 ON ema1.equipment_id = ema2.equipment_id AND ema1.created_at = ema2.max_created
                        ) AS latest_maintenance", 
                        'latest_maintenance.equipment_id = ea.equipment_id', 'left'
                    )
                    ->join('next_maintenance_date nmd', 'nmd.equipment_id = ea.equipment_id', 'left')
                    ->where('ea.equipment_type', $type->asset_id)
                    ->where('ea.maintenance_date IS NOT NULL', null, false)
                    ->where('ea.frequency_year IS NOT NULL', null, false)
                    ->where('ea.maintenance_reminder_day IS NOT NULL', null, false)
                    ->get()
                    ->row();

                    // Ensure no nulls (in case DB returns null on SUM)
                    $complete_count = (int) ($preventive_maintenance->complete_count ?? 0);
                    $in_maintenance_count = (int) ($preventive_maintenance->in_maintenance_count ?? 0);
                    $pending_count = (int) ($preventive_maintenance->pending_count ?? 0);

                    $preventive_maintenance_total = $complete_count + $in_maintenance_count + $pending_count;





                // Total Store (Total assets that are not in use)
                $total_store = $this->db->select('COUNT(*) as in_store')
                    ->from('equipments_asset')
                    ->where('equipment_type', $type->asset_id)
                    ->where('equipment_status', 'STORE')
                    ->get()
                    ->row()
                    ->in_store;

                // Append valid data with additional metrics
                $data[] = [
                    'equipment_type' => $type->name,
                    'total_locations' => $total_locations,
                    'total_quantity' => [
                        'total_assets' => $total_assets,
                    ],
                    'total_assets' => $total_assets,
                    'total_serviceable' => [
                        'assets_serviceable' => $assets_serviceable,
                    ],
                    'total_store' => $total_store,
                    'corrective_maintenance' => [
                        'total_assets' => $corrective_maintenance,
                    ],
                    'preventive_maintenance' => [
                        'total_assets' => $preventive_maintenance_total,
                    ]
                ];
            }
        }

        // Return the JSON response
        echo json_encode($data);
        die;
    }

    public function getItemSummary_table()
    {
        // Fetch all unique equipment types
        $equipment_types = $this->db->select('asset_id, name')
            ->from('asset_types')
            ->get()
            ->result();

        if (empty($equipment_types)) {
            // Handle error if no equipment types are found
            log_message('error', 'Error in Query: ' . $this->db->last_query());
            return json_encode(['error' => 'No equipment types found']);
        }

        $data = [];

        foreach ($equipment_types as $type) {

            // Fetch assets that belong to the current asset type
            $equipments_assets = $this->db->select('*')
                ->from('equipments_asset')
                ->where('equipment_type', $type->asset_id)
                ->get()
                ->result();

            if (empty($equipments_assets)) {
                log_message('error', 'DB Error: No assets found for equipment type ' . $type->name);
                continue; // Skip to the next equipment type if no assets found
            }

            foreach ($equipments_assets as $asset) {
                // Get total items for this asset
                $total_items = $this->db->select('COUNT(*) as total')
                    ->from('add_asset_items')
                    ->where('asset_id', $asset->equipment_id)
                    ->get()
                    ->row();

                if (!$total_items) {
                    log_message('error', 'Error in Query: ' . $this->db->last_query());
                    continue; // Skip if total items query fails
                }

                $total_items_count = $total_items->total ?? 0;

                // Count distinct store locations if there are items
                if ($total_items_count > 0) {
                    $locations = $this->db->select('store_location.id, store_location.name')
                        ->from('add_asset_items')
                        ->join('store_location', 'store_location.id = add_asset_items.store_location_id', 'left')
                        ->where('add_asset_items.asset_id', $asset->equipment_id)
                        ->group_by('store_location.id')
                        ->get()
                        ->result();

                    // Filter out NULL values
                    $location_names = array_values(array_filter(array_map(fn($loc) => $loc->name, $locations), fn($name) => !is_null($name) && $name !== ''));

                    $total_locations_count = count($location_names);


                    // Count items in use
                    $items_in_use = $this->db->select('COUNT(*) as in_use_count')
                        ->from('add_asset_items')
                        ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                        ->where('add_asset_items.asset_id', $asset->equipment_id)
                        ->where('item_status.name', 'IN USE')
                        ->get()
                        ->row();

                    $items_in_use_count = $items_in_use->in_use_count ?? 0;

                    // Get latest corrective maintenance entry count
                    $total_corrective_maintenance = $this->db->select('COUNT(*) as total_corrective_maintenance')
                        ->from('item_ticket')
                        ->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id', 'left')
                        ->join('logs_item_maintenance', 'item_ticket.id = logs_item_maintenance.item_ticket_id', 'left') // Left Join to include NULL cases
                        ->where('item_ticket.equipment_id', $asset->equipment_id)
                        ->group_start() // Open a condition group
                        ->where('logs_item_maintenance.id IS NULL') // No record in logs
                        ->or_where('logs_item_maintenance.final_status !=', 'COMPLETE') // If record exists but final_status is not 'COMPLETE'
                        ->group_end() // Close condition group
                        ->get()
                        ->row()
                        ->total_corrective_maintenance ?? 0;



                    // Count items in store
                    $items_in_store = $this->db->select('COUNT(*) as store_count')
                        ->from('add_asset_items')
                        ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                        ->where('add_asset_items.asset_id', $asset->equipment_id)
                        ->where('item_status.name', 'STORE')
                        ->get()
                        ->row();

                    $items_in_store_count = $items_in_store->store_count ?? 0;

                    // Append valid data
                    $data[] = [

                        'equipment_type' => $type->name,
                        'equipment_name' => $asset->equipment_name,
                        'total_locations' => $total_locations_count,
                        'location_names' => $location_names,
                        'total_quantity' => $total_items_count,
                        'total_in_use' => $items_in_use_count,
                        'total_store' => $items_in_store_count,
                        'corrective_maintenance' => $total_corrective_maintenance,
                    ];
                }
            }
        }

        echo json_encode($data);
        die;
    }

    public function getItemSummary()
    {
        // Current schema implementation. The legacy per-type implementation is retained below.
        return $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($this->buildItemSummaryRows()));

        // Legacy implementation retained for reference (unreachable).
        // Fetch all item types
        $item_types = $this->db->select('id, name')
            ->from('item_types')
            ->get()
            ->result();

        if (empty($item_types)) {
            log_message('error', 'Error in Query: ' . $this->db->last_query());
            return json_encode(['error' => 'No item types found']);
        }

        $data = [];

        foreach ($item_types as $type) {
            $item_type_id = $type->id;
            $type_name = $type->name;

            // Total items of this type
            $total_items_count = $this->db->from('add_asset_items')
                ->where('item_type_id', $item_type_id)
                ->count_all_results();

            // Total locations for this item type
            $total_locations_count = $this->db->select('COUNT(DISTINCT store_location_id) as total_locations')
                ->from('add_asset_items')
                ->where('item_type_id', $item_type_id)
                ->get()
                ->row()
                ->total_locations ?? 0;

            // Items in use
            $items_in_use_count = $this->db->from('add_asset_items')
                ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                ->where('item_type_id', $item_type_id)
                ->where('item_status.name', 'IN USE')
                ->count_all_results();

            // Items in store
            $items_in_store_count = $this->db->from('add_asset_items')
                ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                ->where('item_type_id', $item_type_id)
                ->where('item_status.name', 'STORE')
                ->count_all_results();

            // Items serviceable
            $items_serviceable_count = $this->db->from('add_asset_items')
                ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                ->where('item_type_id', $item_type_id)
                ->where('item_status.name', 'SERVICEABLE')
                ->count_all_results();

            // Items in maintenance
            $items_in_maintenance_count = $this->db->from('add_asset_items')
                ->join('item_status', 'item_status.id = add_asset_items.item_status_id', 'left')
                ->where('item_type_id', $item_type_id)
                ->where('item_status.name', 'MAINTENANCE')
                ->count_all_results();

            // Corrective maintenance (keeping the original query for now, you might need this count separately)
            $total_corrective_maintenance = $this->db->select('COUNT(*) as total_corrective_maintenance')
                ->from('item_ticket')
                ->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id', 'left')
                ->join('logs_item_maintenance', 'item_ticket.id = logs_item_maintenance.item_ticket_id', 'left')
                ->where('add_asset_items.item_type_id', $item_type_id)
                ->group_start()
                ->where('logs_item_maintenance.id IS NULL')
                ->or_where('logs_item_maintenance.final_status !=', 'COMPLETE')
                ->group_end()
                ->get()
                ->row()
                ->total_corrective_maintenance ?? 0;

            // Calculate percentages
            $in_use_percentage = $total_items_count > 0 ? ($items_in_use_count / $total_items_count) * 100 : 0;
            $store_percentage = $total_items_count > 0 ? ($items_in_store_count / $total_items_count) * 100 : 0;
            $corrective_maintenance_percentage = $total_items_count > 0 ? ($total_corrective_maintenance / $total_items_count) * 100 : 0;
            $serviceable_percentage = $total_items_count > 0 ? ($items_serviceable_count / $total_items_count) * 100 : 0;


            // Append data
            $data[] = [
                'items_in_use_count' => $items_in_use_count,
                'items_in_store_count' => $items_in_store_count,
                'items_serviceable_count' => $items_serviceable_count,
                'total_corrective_maintenance' => $total_corrective_maintenance,
                'item_type_id' => $item_type_id,
                'item_type' => $type_name,
                'total_locations' => $total_locations_count,
                'total_quantity' => $total_items_count,
                'in_use_percentage' => round($in_use_percentage, 2),
                'store_percentage' => round($store_percentage, 2),
                'corrective_maintenance_percentage' => round($corrective_maintenance_percentage, 2),
                'serviceable_percentage' => round($serviceable_percentage, 2),
            ];
        }

        echo json_encode($data);
        die;
    }

    /**
     * Build the asset summary with one aggregate query matching the current database schema.
     */
    private function buildAssetSummaryRows($forTable = false)
    {
        $rows = $this->db
            ->select("
                at.asset_id,
                at.name AS equipment_type,
                COUNT(DISTINCT ea.equipment_id) AS total_assets,
                COUNT(DISTINCT CASE WHEN ea.location_id IS NOT NULL THEN ea.location_id END) AS total_locations,
                COUNT(DISTINCT CASE WHEN ea.equipment_status = 'In use' THEN ea.equipment_id END) AS assets_serviceable,
                COUNT(DISTINCT CASE WHEN ea.equipment_status = 'Standby' THEN ea.equipment_id END) AS total_store,
                COUNT(DISTINCT CASE WHEN ema.in_out = 'In maintenance' THEN ea.equipment_id END) AS corrective,
                COUNT(DISTINCT nmd.equipment_id) AS preventive
            ", false)
            ->from('asset_types at')
            ->join('equipments_asset ea', 'ea.equipment_type = at.asset_id', 'left')
            ->join('equipment_maintenance_asset ema', 'ema.equipment_id = ea.equipment_id', 'left')
            ->join('next_maintenance_date nmd', 'nmd.equipment_id = ea.equipment_id', 'left')
            ->group_by(['at.asset_id', 'at.name'])
            ->having('total_assets >', 0)
            ->order_by('at.name', 'asc')
            ->get()
            ->result_array();

        return array_map(function ($row) use ($forTable) {
            $total = (int) $row['total_assets'];
            $serviceable = (int) $row['assets_serviceable'];
            $corrective = (int) $row['corrective'];
            $preventive = (int) $row['preventive'];

            if ($forTable) {
                return [
                    'equipment_type' => $row['equipment_type'],
                    'total_locations' => (int) $row['total_locations'],
                    'total_quantity' => ['total_assets' => $total],
                    'total_assets' => $total,
                    'total_serviceable' => ['assets_serviceable' => $serviceable],
                    'total_store' => (int) $row['total_store'],
                    'corrective_maintenance' => ['total_assets' => $corrective],
                    'preventive_maintenance' => ['total_assets' => $preventive],
                ];
            }

            return [
                'total_assets' => $total,
                'equipment_type' => $row['equipment_type'],
                'assets_serviceable' => $serviceable,
                'total_store' => (int) $row['total_store'],
                'corrective' => $corrective,
                'preventive' => $preventive,
                'in_use_percentage' => $total ? round(($serviceable / $total) * 100, 2) : 0,
                'corrective_percentage' => $total ? round(($corrective / $total) * 100, 2) : 0,
                'preventive_percentage' => $total ? round(($preventive / $total) * 100, 2) : 0,
            ];
        }, $rows);
    }

    /**
     * Build the component summary without the removed logs_item_maintenance table.
     */
    private function buildItemSummaryRows()
    {
        $rows = $this->db
            ->select("
                it.id AS item_type_id,
                it.name AS item_type,
                COUNT(DISTINCT aai.id) AS total_quantity,
                COUNT(DISTINCT CASE WHEN aai.store_location_id IS NOT NULL THEN aai.store_location_id END) AS total_locations,
                COUNT(DISTINCT CASE WHEN ist.name = 'IN USE' THEN aai.id END) AS items_in_use_count,
                COUNT(DISTINCT CASE WHEN ist.name = 'STORE' THEN aai.id END) AS items_in_store_count,
                COUNT(DISTINCT CASE WHEN ist.name = 'SERVICEABLE' THEN aai.id END) AS items_serviceable_count,
                COUNT(DISTINCT CASE WHEN ticket.active = 1 AND (ticket.status IS NULL OR UPPER(ticket.status) != 'COMPLETE') THEN ticket.id END) AS total_corrective_maintenance
            ", false)
            ->from('item_types it')
            ->join('add_asset_items aai', 'aai.item_type_id = it.id', 'left')
            ->join('item_status ist', 'ist.id = aai.item_status_id', 'left')
            ->join('item_ticket ticket', 'ticket.item_id = aai.id', 'left')
            ->group_by(['it.id', 'it.name'])
            ->order_by('it.name', 'asc')
            ->get()
            ->result_array();

        return array_map(function ($row) {
            $total = (int) $row['total_quantity'];
            $inUse = (int) $row['items_in_use_count'];
            $inStore = (int) $row['items_in_store_count'];
            $serviceable = (int) $row['items_serviceable_count'];
            $corrective = (int) $row['total_corrective_maintenance'];

            return [
                'items_in_use_count' => $inUse,
                'items_in_store_count' => $inStore,
                'items_serviceable_count' => $serviceable,
                'total_corrective_maintenance' => $corrective,
                'item_type_id' => (int) $row['item_type_id'],
                'item_type' => $row['item_type'],
                'total_locations' => (int) $row['total_locations'],
                'total_quantity' => $total,
                'in_use_percentage' => $total ? round(($inUse / $total) * 100, 2) : 0,
                'store_percentage' => $total ? round(($inStore / $total) * 100, 2) : 0,
                'corrective_maintenance_percentage' => $total ? round(($corrective / $total) * 100, 2) : 0,
                'serviceable_percentage' => $total ? round(($serviceable / $total) * 100, 2) : 0,
            ];
        }, $rows);
    }
}






