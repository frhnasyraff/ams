<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Equipment_asset_summary_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Assets Summary", 'title2' => "Assets Summary", "styles" => [
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



        $this->load->view('equipment_asset_summary');
        $this->load->view('footer', ['scripts' => [
            'design/js/report-suite.js?v=1',
            'design/js/equipment_asset_summary.js?v=3'
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
        store_location.name as store_location, 
        GROUP_CONCAT(CONCAT(add_asset_items.vendor_part_number) SEPARATOR ", ") as vendor_part_number, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        add_asset_items.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->group_by('equipments_asset.equipment_id')
        ->where_in('equipments_asset.equipment_id', $ids);

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
                    <th>Date Installed</th>
                    <th>Managed By</th>
                    <th>Manufacturer Name</th>
                    <th>Part Number</th>
                    <th>Status</th>
                    <th>Last Maintenance</th>
                    <th>Next Maintenance</th>
                    <th>Store Location</th>
                    <th>Replacement Date</th>
                    
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
            $html .= '<td>' . $record->date_installed . '</td>';
            $html .= '<td>' . $record->equipment_name . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';
            $html .= '<td>' . $record->vendor_part_number . '</td>';
            // $items = isset($asset_items[$record->equipment_id]) ? implode(', ', $asset_items[$record->equipment_id]) : 'N/A';
            // $html .= '<td>' . $items . '</td>';
            $html .= '<td>' . $record->equipment_status . '</td>';
            $html .= '<td>' . $record->last_maintenance . '</td>';

            if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                $frequency = intval($record->frequency_year);
                if ($frequency > 0) {
                    $nextMaintenance = $this->getMaintenanceDates($record->maintenance_date, $frequency, $record->last_maintenance);
                    $html .= '<td>' . htmlspecialchars($nextMaintenance) . '</td>';
                } else {
                    $html .= '<td>NA</td>';
                }
            } else {
                $html .= '<td>NA</td>';
            }

            $html .= '<td>' . $record->store_location . '</td>';


            $html .= '<td> -- </td>';

            // Fetch and format additional items

            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'asset_summary_report.pdf');
    }

    public function downloadExcel()
    {
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (range('A', 'L') as $columID) {
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
       
        // Apply bold formatting to the first row (A1:J1)
        $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
        $sheet->setCellValue('A1', 'Asset Type');
        $sheet->setCellValue('B1', 'Registration Number');
        $sheet->setCellValue('C1', 'Location');
        $sheet->setCellValue('D1', 'Date Installed');
        $sheet->setCellValue('E1', 'Managed By');
        $sheet->setCellValue('F1', 'Manufacturer Name');
        $sheet->setCellValue('G1', 'Part Number');
        $sheet->setCellValue('H1', 'Status');
        $sheet->setCellValue('I1', 'Last Maintenance');
        $sheet->setCellValue('J1', 'Next Maintenance');
        $sheet->setCellValue('K1', 'Store Location');
        $sheet->setCellValue('L1', 'Replacement Date');

        
        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        store_location.name as store_location, 
        GROUP_CONCAT(add_asset_items.vendor_part_number) as vendor_part_number, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        add_asset_items.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
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
            $startRow = $x;
            $endRow = $x + $itemCount - 1; // Last row to merge

            $vendorParts = explode(',', $record->vendor_part_number);
            $vendorPartCount = count($vendorParts);
            $vendorPartIndex = 0;
           
            foreach ($items as $item) {
                // Set values in the respective columns
                $sheet->setCellValue('A' . $x, $record->type_name);
                $sheet->setCellValue('B' . $x, $record->equipment_registration);
                $sheet->setCellValue('C' . $x, $record->location);
                $sheet->setCellValue('D' . $x, $record->date_installed);
                $sheet->setCellValue('E' . $x, $record->equipment_name);
                $sheet->setCellValue('F' . $x, $record->manufacturer_name);
                // vendor_part_number 
                if ($vendorPartIndex < $vendorPartCount) {
                    $sheet->setCellValue('G' . $x, trim($vendorParts[$vendorPartIndex])); // trim() 
                    $vendorPartIndex++;
                } else {
                    $sheet->setCellValue('G' . $x, ''); // vendor_part_number 
                }        
                // Set Asset Item separately in column G
                // $sheet->setCellValue('G' . $x, $item); 
        
                // Set Store Location
                $sheet->setCellValue('H' . $x, $record->equipment_status);
                $sheet->setCellValue('I' . $x, $record->last_maintenance);
                if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                    $frequency = intval($record->frequency_year);
                    if ($frequency > 0) {
                        $nextMaintenance = $this->getMaintenanceDates($record->maintenance_date, $frequency, $record->last_maintenance);
                        $sheet->setCellValue('J' . $x, $nextMaintenance);
                    } else {
                        $sheet->setCellValue('J' . $x, 'NA');
                    }
                } else {
                    $sheet->setCellValue('J' . $x, 'NA');
                }

                $sheet->setCellValue('K' . $x, $record->store_location);
                $sheet->setCellValue('L' . $x, '-');
        
                // Move to the next row for the next asset item
                $x++;
            }
        
            // Merge only the required columns if there are multiple rows for this asset
            if ($itemCount > 1) {
                $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Type Name
                $sheet->mergeCells("B{$startRow}:B{$endRow}"); // Merge Equipment Registration
                $sheet->mergeCells("C{$startRow}:C{$endRow}"); // Merge Location
                $sheet->mergeCells("D{$startRow}:D{$endRow}"); // Merge Date Installed
                $sheet->mergeCells("E{$startRow}:E{$endRow}"); // Merge Equipment Name
                $sheet->mergeCells("K{$startRow}:K{$endRow}"); // Merge Store Location
                $sheet->mergeCells("L{$startRow}:L{$endRow}"); // Merge Extra Column
            }
        }
        
        
        $writer = new Xlsx($spreadsheet);
        $fileName = 'asset_summary_report.xlsx';
        // $writer->save($fileName); // this is for download in folder

        // for force Download 
        ob_clean();
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');

        try {
            $writer->save('php://output');
            exit();
        } catch (\Exception $e) {
            log_message('error', 'Excel Download Error: ' . $e->getMessage());
        }
    }

    // public function downloadExcelSingle($ids)
    // {
        
    //     $spreadsheet = new Spreadsheet();
    //     $sheet = $spreadsheet->getActiveSheet();

    //     foreach (range('A', 'L') as $columID) {
    //         $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
    //     }
       
    //     // Apply bold formatting to the first row (A1:J1)
    //     $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
    //     $sheet->setCellValue('A1', 'Asset Type');
    //     $sheet->setCellValue('B1', 'Registration Number');
    //     $sheet->setCellValue('C1', 'Location');
    //     $sheet->setCellValue('D1', 'Date Installed');
    //     $sheet->setCellValue('E1', 'Managed By');
    //     $sheet->setCellValue('F1', 'Manufacturer Name');
    //     $sheet->setCellValue('G1', 'Part Number');
    //     $sheet->setCellValue('H1', 'Status');
    //     $sheet->setCellValue('I1', 'Last Maintenance');
    //     $sheet->setCellValue('J1', 'Next Maintenance');
    //     $sheet->setCellValue('K1', 'Store Location');
    //     $sheet->setCellValue('L1', 'Replacement Date');

        
    //     $query = $this->db->select('equipments_asset.*, 
    //     locations.name as location, 
    //     asset_types.name as type_name, 
    //     store_location.name as store_location, 
    //     GROUP_CONCAT(add_asset_items.vendor_part_number) as vendor_part_number, 
    //     MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
    //     add_asset_items.manufacturer_name as manufacturer_name, ')
    //     ->from('equipments_asset')
    //     ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
    //     ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
    //     ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
    //     ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
    //     ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
    //     ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
    //     ->group_by('equipments_asset.equipment_id')

    //     ->where_in('equipments_asset.equipment_id', $ids);

    //     $result = $query->get()->result();
     
    //     $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')
    //     ->from('add_asset_items')
    //     ->where_in('add_asset_items.asset_id', $ids);

    //     $performance_record1 = $query1->get()->result();

    //     // Group items by asset_id
    //     $asset_items = [];
    //     foreach ($performance_record1 as $item) {

    //         $asset_items[$item->asset_id][] = $item->item_name;
    //     }

    //     $x = 2; //start from row 2
    //     foreach ($result as $record) {
    //         // Get asset items, if no items exist, set default value ['N/A']
    //         $items = isset($asset_items[$record->equipment_id]) && !empty($asset_items[$record->equipment_id]) 
    //                  ? $asset_items[$record->equipment_id] 
    //                  : ['N/A'];
        
    //         $itemCount = count($items); // Total rows needed for this asset
        
    //         // Store starting row index for merging
    //         $startRow = $x;
    //         $endRow = $x + $itemCount - 1; // Last row to merge
        
    //         foreach ($items as $item) {
    //             // Set values in the respective columns
    //             $sheet->setCellValue('A' . $x, $record->type_name);
    //             $sheet->setCellValue('B' . $x, $record->equipment_registration);
    //             $sheet->setCellValue('C' . $x, $record->location);
    //             $sheet->setCellValue('D' . $x, $record->date_installed);
    //             $sheet->setCellValue('E' . $x, $record->equipment_name);
    //             $sheet->setCellValue('F' . $x, $record->manufacturer_name);
    //             foreach ($record->vendor_part_number as $i) {
    //                 $sheet->setCellValue('G' . $x, $i);
    //             }

    //             // Set Asset Item separately in column G
    //             // $sheet->setCellValue('G' . $x, $item); 
        
    //             // Set Store Location
    //             $sheet->setCellValue('H' . $x, $record->equipment_status);
    //             $sheet->setCellValue('I' . $x, $record->last_maintenance);
    //             if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
    //                 $frequency = intval($record->frequency_year);
    //                 if ($frequency > 0) {
    //                     $nextMaintenance = $this->getMaintenanceDates($record->maintenance_date, $frequency, $record->last_maintenance);
    //                     $sheet->setCellValue('J' . $x, $nextMaintenance);
    //                 } else {
    //                     $sheet->setCellValue('J' . $x, 'NA');
    //                 }
    //             } else {
    //                 $sheet->setCellValue('J' . $x, 'NA');
    //             }

    //             $sheet->setCellValue('K' . $x, $record->store_location);
    //             $sheet->setCellValue('L' . $x, '-');
        
    //             // Move to the next row for the next asset item
    //             $x++;
    //         }
        
    //         // Merge only the required columns if there are multiple rows for this asset
    //         if ($itemCount > 1) {
    //             $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Type Name
    //             $sheet->mergeCells("B{$startRow}:B{$endRow}"); // Merge Equipment Registration
    //             $sheet->mergeCells("C{$startRow}:C{$endRow}"); // Merge Location
    //             $sheet->mergeCells("D{$startRow}:D{$endRow}"); // Merge Date Installed
    //             $sheet->mergeCells("E{$startRow}:E{$endRow}"); // Merge Equipment Name
    //             $sheet->mergeCells("K{$startRow}:K{$endRow}"); // Merge Store Location
    //             $sheet->mergeCells("L{$startRow}:L{$endRow}"); // Merge Extra Column
    //         }
    //     }
        
        
    //     $writer = new Xlsx($spreadsheet);
    //     $fileName = 'asset_summary_report.xlsx';
    //     // $writer->save($fileName); // this is for download in folder

    //     // for force Download 
    //     ob_clean();
    //     header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    //     header('Content-Disposition: attachment; filename="' . $fileName . '"');

    //     try {
    //         $writer->save('php://output');
    //         exit();
    //     } catch (\Exception $e) {
    //         log_message('error', 'Excel Download Error: ' . $e->getMessage());
    //     }
    // }

    public function downloadExcelSingle($ids)
{
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    foreach (range('A', 'L') as $columID) {
        $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
    }

    // Apply bold formatting to the first row (A1:L1)
    $spreadsheet->getActiveSheet()->getStyle('A1:L1')->getFont()->setBold(true);
    $sheet->setCellValue('A1', 'Asset Type');
    $sheet->setCellValue('B1', 'Registration Number');
    $sheet->setCellValue('C1', 'Location');
    $sheet->setCellValue('D1', 'Date Installed');
    $sheet->setCellValue('E1', 'Managed By');
    $sheet->setCellValue('F1', 'Manufacturer Name');
    $sheet->setCellValue('G1', 'Part Number');
    $sheet->setCellValue('H1', 'Status');
    $sheet->setCellValue('I1', 'Last Maintenance');
    $sheet->setCellValue('J1', 'Next Maintenance');
    $sheet->setCellValue('K1', 'Store Location');
    $sheet->setCellValue('L1', 'Replacement Date');

    $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        store_location.name as store_location, 
        GROUP_CONCAT(add_asset_items.vendor_part_number) as vendor_part_number, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        add_asset_items.manufacturer_name as manufacturer_name')
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
        ->group_by('equipments_asset.equipment_id')
        ->where_in('equipments_asset.equipment_id', $ids);

        


    $result = $query->get()->result();

    $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')
        ->from('add_asset_items')
        ->where_in('add_asset_items.asset_id', $ids);

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

        $vendorParts = explode(',', $record->vendor_part_number);
        $vendorPartCount = count($vendorParts);

        $vendorPartIndex = 0;
        foreach ($items as $item) {
            // Set values in the respective columns
            $sheet->setCellValue('A' . $x, $record->type_name);
            $sheet->setCellValue('B' . $x, $record->equipment_registration);
            $sheet->setCellValue('C' . $x, $record->location);
            $sheet->setCellValue('D' . $x, $record->date_installed);
            $sheet->setCellValue('E' . $x, $record->equipment_name);
            $sheet->setCellValue('F' . $x, $record->manufacturer_name);

            // vendor_part_number 
            if ($vendorPartIndex < $vendorPartCount) {
                $sheet->setCellValue('G' . $x, trim($vendorParts[$vendorPartIndex])); // trim() 
                $vendorPartIndex++;
            } else {
                $sheet->setCellValue('G' . $x, ''); // vendor_part_number 
            }

            $sheet->setCellValue('H' . $x, $record->equipment_status);
            $sheet->setCellValue('I' . $x, $record->last_maintenance);

            if (!empty($record->maintenance_date) && !empty($record->frequency_year) && !empty($record->last_maintenance)) {
                $frequency = intval($record->frequency_year);
                if ($frequency > 0) {
                    $nextMaintenance = $this->getMaintenanceDates($record->maintenance_date, $frequency, $record->last_maintenance);
                    $sheet->setCellValue('J' . $x, $nextMaintenance);
                } else {
                    $sheet->setCellValue('J' . $x, 'NA');
                }
            } else {
                $sheet->setCellValue('J' . $x, 'NA');
            }

            $sheet->setCellValue('K' . $x, $record->store_location);
            $sheet->setCellValue('L' . $x, '-');

            // Move to the next row for the next asset item
            $x++;
        }

        // Merge only the required columns if there are multiple rows for this asset
        if ($itemCount > 1) {
            $sheet->mergeCells("A{$startRow}:A{$endRow}"); // Merge Type Name
            $sheet->mergeCells("B{$startRow}:B{$endRow}"); // Merge Equipment Registration
            $sheet->mergeCells("C{$startRow}:C{$endRow}"); // Merge Location
            $sheet->mergeCells("D{$startRow}:D{$endRow}"); // Merge Date Installed
            $sheet->mergeCells("E{$startRow}:E{$endRow}"); // Merge Equipment Name
            $sheet->mergeCells("K{$startRow}:K{$endRow}"); // Merge Store Location
            $sheet->mergeCells("L{$startRow}:L{$endRow}"); // Merge Extra Column
        }
    }

    $writer = new Xlsx($spreadsheet);
    $fileName = 'asset_summary_report.xlsx';

    // for force Download
    ob_clean();
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $fileName . '"');

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
        store_location.name as store_location, 
        GROUP_CONCAT(CONCAT(add_asset_items.vendor_part_number) SEPARATOR ", ") as vendor_part_number, 
        MAX(equipment_maintenance_asset.update_date) as last_maintenance, 
        add_asset_items.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.equipment_id = equipments_asset.equipment_id', 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
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

    public function getMaintenanceDates($startDate, $frequency, $latestMaintenance)
    {
        $dates = [];
        $interval = 12 / $frequency; // Months between each maintenance
        $start = new DateTime($startDate);

        for ($i = 0; $i < $frequency; $i++) {
            $newDate = clone $start;
            $newDate->modify("+" . ($i * $interval) . " months");
            $dates[] = $newDate->format('Y-m-d'); // Format as YYYY-MM-DD
        }

        // Convert latestMaintenance to DateTime object
        $latestDate = new DateTime($latestMaintenance);

        // Find the next maintenance date after the latest maintenance
        foreach ($dates as $dateStr) {
            $dateObj = new DateTime($dateStr);
            if ($dateObj > $latestDate) {
                return $dateStr;
            }
        }

        // If no future date found in the current year, return the first date of next year
        $nextYearDate = new DateTime($dates[0]);
        $nextYearDate->modify("+1 year");

        return $nextYearDate->format('Y-m-d');
    }
}
