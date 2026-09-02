<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
class Maintenance_summary_report extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_assets")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => "Maintenance Summary", 'title2' => "Maintenance Summary", "styles" => [
            'design/css/performance-summary.css'
        ]]);


        // if (!isSuperAdmin()) {
        //     $query->where_in('workers.branch_office_id', getUserActiveBranchsId());
        // } else {
        //     // if user is super admin check if selected any branch
        //     if ($this->input->get('branch') && !empty($this->input->get('branch'))) {
        //         $query->where('workers.branch_office_id', $this->input->get('branch'));
        //     }
        // }



        $this->load->view('maintenance_summary', [

            'workers' => []
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/report-suite.js?v=1',
            'design/js/maintenance_summary.js?v=2'
        ]]);
    }

    

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');

       
        // Fetching performance records
        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        equipment_maintenance_asset.update_date as actual_date, 
        manufacturers.manufacturer_name as manufacturer_name, 
        equipment_maintenance_asset.maintenance_type_id as maintenance_type, 
        maintenance_task_done.task_done as task_done,

        DATE_ADD(COALESCE(MAX(equipment_maintenance_asset.update_date), NOW()), INTERVAL FLOOR(12 / GREATEST(equipments_asset.frequency_day, 1)) Year) as next_maintenance_date')
   
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id')
        ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->where('equipment_maintenance_asset.maintenance_type_id' , 'preventive')
        ->where_in('equipments_asset.equipment_id', $ids)
        ->group_by('equipments_asset.equipment_id');

        $performance_record = $query->get()->result();

      

        
        // Fetching additional items for each asset
        $query = $this->db->select('add_asset_items.*, 
                                    add_asset_items.asset_id as asset_id
                                   ')
            ->from('add_asset_items')
            ->where_in('add_asset_items.asset_id', $ids);

        $performance_record1 = $query->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
            $asset_items[$item->asset_id][] = $item->item_name;
        }

        if ($download_type == 'pdf') {
            $this->downloadPDF($performance_record, $asset_items);
        } else if ($download_type == 'excel') {
            $this->downloadExcelSingle($ids);
        }else{
            $this->downloadExcel();

        }
    }

    public function downloadPDF($performance_record, $asset_items)
    {
        $this->load->library('pdf');
        $html = '<!DOCTYPE html>
        <html>
        <head>
            <style>
            table {
                border-collapse: collapse;
                width: 100%;
            }
    
            th, td {
                text-align: left;
                padding: 8px;
                border: 1px solid #ddd;
                font-size: 14px
            }
    
            th {
                background-color: #f2f2f2;
            }
    
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            </style>
        </head>
        <body>
        <table>
            <thead>
                <tr>
                    <th>Asset Type</th>
                    <th>Registration Number</th>
                    <th>Location</th>
                    <th>Managed By</th>
                    <th>Asset Items</th>
                    <th>Manufacturer </th>
                    <th>Part Number</th>
                    <th>Maintenance Type</th>
                    <th>Planned Maintenance Date</th>
                    <th>Actual Maintenance Date</th>
                    <th>Task Done</th>
                    <th>Status</th>
                    <th>Delay (Days)</th>
                    
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            // die(redirect("/driver_performance/index?error= No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->type_name . '</td>';
            $html .= '<td>' . $record->equipment_registration . '</td>';
            $html .= '<td>' . $record->location . '</td>';
            $html .= '<td>' . $record->equipment_name . '</td>';
            $html .= '<td>' . $record->item_type_name . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';
            $items = isset($asset_items[$record->equipment_id]) ? implode(', ', $asset_items[$record->equipment_id]) : 'N/A';
            $html .= '<td>' . $items . '</td>';
            $html .= '<td>' . $record->maintenance_type . '</td>';
              // Check if required data exists
            if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                $frequency = intval($record->frequency_year);
                if ($frequency > 0) {
                    $nextMaintenance = $this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance);
                    $html .= '<td>' . htmlspecialchars($nextMaintenance) . '</td>';
                } else {
                    $html .= '<td>NA</td>';
                }
            } else {
                $html .= '<td>NA</td>';
            }

            $html .= '<td>' . $record->actual_date . '</td>';
            $html .= '<td>' . $record->task_done . '</td>';
            $html .= '<td>' . $record->equipment_status . '</td>';
            if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                $frequency = intval($record->frequency_year);
            
                if ($frequency > 0) {
                    // Convert next maintenance date to a timestamp
                    $nextMaintenance = strtotime($this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance));
            
                    // Convert actual date to a timestamp
                    $actualDate = !empty($record->actual_date) ? strtotime($record->actual_date) : false;
            
                    if ($nextMaintenance && $actualDate) {
                        // Calculate days remaining
                        $daysRemaining = ceil((  $actualDate - $nextMaintenance) / 86400);
            
                        // If daysRemaining is negative, set it to 0
                        $daysRemaining = max(0, $daysRemaining);
            
                        $html .= '<td>' . $daysRemaining . '</td>';
                    } else {
                        $html .= '<td>NA</td>';
                    }
                }
            } else {
                $html .= '<td>NA</td>';
            }
            

            // Fetch and format additional items

            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'maintenance_summary_report.pdf');
    }


    public function downloadExcelSingle($ids){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       
        foreach(range('A' , 'M') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Asset Type');
        $sheet->setCellValue('B1' , 'Registration Number');
        $sheet->setCellValue('C1' , 'Location');
        $sheet->setCellValue('D1' , 'Managed By');
        $sheet->setCellValue('E1' , 'Asset Items');
        $sheet->setCellValue('F1' , 'Manufacturer');
        $sheet->setCellValue('G1' , 'Part Number');
        $sheet->setCellValue('H1' , 'Maintenance Type');  
        $sheet->setCellValue('I1' , 'Planned Maintenance Date');  
        $sheet->setCellValue('J1' , 'Actual Maintenance Date');  
        $sheet->setCellValue('K1' , 'Task Done');  
        $sheet->setCellValue('L1' , 'Status');  
        $sheet->setCellValue('M1' , 'Delay (Days)');  
       

        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        equipment_maintenance_asset.update_date as actual_date, 
        manufacturers.manufacturer_name as manufacturer_name, 
        equipment_maintenance_asset.maintenance_type_id as maintenance_type, 
        maintenance_task_done.task_done as task_done,

        DATE_ADD(COALESCE(MAX(equipment_maintenance_asset.update_date), NOW()), INTERVAL FLOOR(12 / GREATEST(equipments_asset.frequency_day, 1)) Year) as next_maintenance_date')
   
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id')
        ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->where('equipment_maintenance_asset.maintenance_type_id' , 'preventive')
        ->group_by('equipments_asset.equipment_id')
        ->where_in('equipments_asset.equipment_id', $ids);


        $result = $query->get()->result();
        $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')
        ->from('add_asset_items') ->where_in('add_asset_items.asset_id', $ids);

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach ($result as $record) {
            // Get asset items, if no items exist, set default value ['N/A']
            $items = isset($asset_items[$record->equipment_id]) && !empty($asset_items[$record->equipment_id]) 
                     ? $asset_items[$record->equipment_id] 
                     : ['N/A'];
        
            $itemCount = count($items); // Total rows needed for this asset
        
            // Store starting row index for merging
            $startRow = $x;
            $endRow = $x + $itemCount - 1; // Last row to merge
        
            foreach ($items as $item) {
                // Set values in the respective columns
                $sheet->setCellValue('A' . $x, $record->type_name);
                $sheet->setCellValue('B' . $x, $record->equipment_registration);
                $sheet->setCellValue('C' . $x, $record->location);
                $sheet->setCellValue('D' . $x, $record->equipment_name);
                $sheet->setCellValue('E' . $x, $record->item_type_name);
                $sheet->setCellValue('F' . $x, $record->manufacturer_name);
        
                // Set Asset Item separately in column G
                $sheet->setCellValue('G' . $x, $item);
        
                $sheet->setCellValue('H' . $x, $record->maintenance_type);
        
                // Check and calculate next maintenance date
                if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                    $frequency = intval($record->frequency_year);
                    if ($frequency > 0) {
                        $nextMaintenance = $this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance);
                        $sheet->setCellValue('I' . $x, $nextMaintenance);
                    } else {
                        $sheet->setCellValue('I' . $x, 'NA');
                    }
                } else {
                    $sheet->setCellValue('I' . $x, 'NA');
                }
        
                $sheet->setCellValue('J' . $x, $record->actual_date);
                $sheet->setCellValue('K' . $x, $record->task_done);
                $sheet->setCellValue('L' . $x, $record->equipment_status);
        
                // Calculate Days Remaining
                if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                    $frequency = intval($record->frequency_year);
                
                    if ($frequency > 0) {
                        // Convert next maintenance date to a timestamp
                        $nextMaintenance = strtotime($this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance));
                
                        // Convert actual date to a timestamp
                        $actualDate = !empty($record->actual_date) ? strtotime($record->actual_date) : false;
                
                        if ($nextMaintenance && $actualDate) {
                            // Calculate days remaining
                            $daysRemaining = ceil(($actualDate - $nextMaintenance) / 86400);
                
                            // If daysRemaining is negative, set it to 0
                            $daysRemaining = max(0, $daysRemaining);
                
                            $sheet->setCellValue('M' . $x, $daysRemaining);
                        } else {
                            $sheet->setCellValue('M' . $x, 'NA');
                        }
                    }
                } else {
                    $sheet->setCellValue('M' . $x, 'NA');
                }
        
                // Move to the next row for the next asset item
                $x++;
            }
        
            // Merge only the required columns if there are multiple rows for this asset
            if ($itemCount > 1) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Type Name
                $sheet->mergeCells("B{$startRow}:B{$endRow}"); // Merge Equipment Registration
                $sheet->mergeCells("C{$startRow}:C{$endRow}"); // Merge Location
                $sheet->mergeCells("D{$startRow}:D{$endRow}"); // Merge Equipment Name
            }
        }
        
        $writer= new Xlsx($spreadsheet);
        $fileName = 'maintenance_summary_report.xlsx';
        // $writer->save($fileName); // this is for download in folder

        // for force Download 
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

        try {
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Download Error: ' . $e->getMessage());
        }
    }


    public function downloadExcel(){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       
        foreach(range('A' , 'M') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Asset Type');
        $sheet->setCellValue('B1' , 'Registration Number');
        $sheet->setCellValue('C1' , 'Location');
        $sheet->setCellValue('D1' , 'Managed By');
        $sheet->setCellValue('E1' , 'Asset Items');
        $sheet->setCellValue('F1' , 'Manufacturer');
        $sheet->setCellValue('G1' , 'Part Number');
        $sheet->setCellValue('H1' , 'Maintenance Type');  
        $sheet->setCellValue('I1' , 'Planned Maintenance Date');  
        $sheet->setCellValue('J1' , 'Actual Maintenance Date');  
        $sheet->setCellValue('K1' , 'Task Done');  
        $sheet->setCellValue('L1' , 'Status');  
        $sheet->setCellValue('M1' , 'Delay (Days)');  
       

        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        equipment_maintenance_asset.update_date as actual_date, 
        manufacturers.manufacturer_name as manufacturer_name, 
        equipment_maintenance_asset.maintenance_type_id as maintenance_type, 
        maintenance_task_done.task_done as task_done,

        DATE_ADD(COALESCE(MAX(equipment_maintenance_asset.update_date), NOW()), INTERVAL FLOOR(12 / GREATEST(equipments_asset.frequency_day, 1)) Year) as next_maintenance_date')
   
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id')
        ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->where('equipment_maintenance_asset.maintenance_type_id' , 'preventive')
        ->group_by('equipments_asset.equipment_id');


        $result = $query->get()->result();
        $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')->from('add_asset_items');

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach ($result as $record) {
            // Get asset items, if no items exist, set default value ['N/A']
            $items = isset($asset_items[$record->equipment_id]) && !empty($asset_items[$record->equipment_id]) 
                     ? $asset_items[$record->equipment_id] 
                     : ['N/A'];
        
            $itemCount = count($items); // Total rows needed for this asset
        
            // Store starting row index for merging
            $startRow = $x;
            $endRow = $x + $itemCount - 1; // Last row to merge
        
            foreach ($items as $item) {
                // Set values in the respective columns
                $sheet->setCellValue('A' . $x, $record->type_name);
                $sheet->setCellValue('B' . $x, $record->equipment_registration);
                $sheet->setCellValue('C' . $x, $record->location);
                $sheet->setCellValue('D' . $x, $record->equipment_name);
                $sheet->setCellValue('E' . $x, $record->item_type_name);
                $sheet->setCellValue('F' . $x, $record->manufacturer_name);
        
                // Set Asset Item separately in column G
                $sheet->setCellValue('G' . $x, $item);
        
                $sheet->setCellValue('H' . $x, $record->maintenance_type);
        
                // Check and calculate next maintenance date
                if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                    $frequency = intval($record->frequency_year);
                    if ($frequency > 0) {
                        $nextMaintenance = $this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance);
                        $sheet->setCellValue('I' . $x, $nextMaintenance);
                    } else {
                        $sheet->setCellValue('I' . $x, 'NA');
                    }
                } else {
                    $sheet->setCellValue('I' . $x, 'NA');
                }
        
                $sheet->setCellValue('J' . $x, $record->actual_date);
                $sheet->setCellValue('K' . $x, $record->task_done);
                $sheet->setCellValue('L' . $x, $record->equipment_status);
        
                // Calculate Days Remaining
                if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                    $frequency = intval($record->frequency_year);
                
                    if ($frequency > 0) {
                        // Convert next maintenance date to a timestamp
                        $nextMaintenance = strtotime($this->getCurrentIntervalMaintenanceDate($record->maintenance_date, $frequency, $record->last_maintenance));
                
                        // Convert actual date to a timestamp
                        $actualDate = !empty($record->actual_date) ? strtotime($record->actual_date) : false;
                
                        if ($nextMaintenance && $actualDate) {
                            // Calculate days remaining
                            $daysRemaining = ceil(($actualDate - $nextMaintenance) / 86400);
                
                            // If daysRemaining is negative, set it to 0
                            $daysRemaining = max(0, $daysRemaining);
                
                            $sheet->setCellValue('M' . $x, $daysRemaining);
                        } else {
                            $sheet->setCellValue('M' . $x, 'NA');
                        }
                    }
                } else {
                    $sheet->setCellValue('M' . $x, 'NA');
                }
        
                // Move to the next row for the next asset item
                $x++;
            }
        
            // Merge only the required columns if there are multiple rows for this asset
            if ($itemCount > 1) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Type Name
                $sheet->mergeCells("B{$startRow}:B{$endRow}"); // Merge Equipment Registration
                $sheet->mergeCells("C{$startRow}:C{$endRow}"); // Merge Location
                $sheet->mergeCells("D{$startRow}:D{$endRow}"); // Merge Equipment Name
            }
        }
        
        $writer= new Xlsx($spreadsheet);
        $fileName = 'maintenance_summary_report.xlsx';
        // $writer->save($fileName); // this is for download in folder

        // for force Download 
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

        try {
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Download Error: ' . $e->getMessage());
        }
    }
   

    public function ajax_list()
    {


        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        equipment_maintenance_asset.update_date as actual_date, 
        manufacturers.manufacturer_name as manufacturer_name, 
        equipment_maintenance_asset.maintenance_type_id as maintenance_type, 
        maintenance_task_done.task_done as task_done,

        DATE_ADD(COALESCE(MAX(equipment_maintenance_asset.update_date), NOW()), INTERVAL FLOOR(12 / GREATEST(equipments_asset.frequency_day, 1)) Year) as next_maintenance_date')
   
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id')
        ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->where('equipment_maintenance_asset.maintenance_type_id' , 'preventive')
        ->group_by('equipments_asset.equipment_id');
    
        $result = $query->get()->result();
      

        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }

    public function itemList()
    {
        $equipmentId = $this->input->get('id');
        $query = $this->db->select('*')
            ->from('add_asset_items')
            ->where('asset_id', $equipmentId)
            ->get();
        $data = $query->result();
        header('Content-Type: application/json');
        // Set the content type
        // echo '<pre>';
        // var_dump( $data );
        // Return JSON response
        echo json_encode($data);
    }

    public function getCurrentIntervalMaintenanceDate($startDate, $frequency, $latestMaintenance) {
        $dates = [];
        $interval = 12 / $frequency; // Months between each maintenance
        $start = new DateTime($startDate);
    
        for ($i = 0; $i < $frequency; $i++) {
            $newDate = clone $start;
            $newDate->modify("+".($i * $interval)." months");
            $dates[] = $newDate->format('Y-m-d'); // Format as YYYY-MM-DD
        }
    
        // Convert latestMaintenance to DateTime object
        $latestDate = new DateTime($latestMaintenance);
    
        // Find the current interval maintenance date (latest or equal but not future)
        $currentIntervalDate = $dates[0]; // Default to first maintenance date
        foreach ($dates as $date) {
            $dateObj = new DateTime($date);
            if ($dateObj > $latestDate) {
                break; // Stop when we find a future date
            }
            $currentIntervalDate = $date; // Update to latest valid date
        }
    
        return $currentIntervalDate;
    }
    
}
