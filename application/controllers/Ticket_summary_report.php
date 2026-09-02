<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


class Ticket_summary_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Ticket Summary", 'title2' => "Ticket Summary", "styles" => [
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



        $this->load->view('ticket_summary', [

            'workers' => []
        ]);
        $this->load->view('footer', ['scripts' => [
            'design/js/report-suite.js?v=1',
            'design/js/ticket_summary.js?v=2'
        ]]);
    }

    public function downloadRecord()
    {
        $download_type = $this->input->post('download_type');
        $ids = $this->input->post('record');

        // Fetching performance records
        $query = $this->db->select('
        ticket.*, 
        locations.name as location, 
        manufacturers.manufacturer_name,
        equipment_maintenance_asset.maintenance_type_id as maintenance_type,
        asset_types.name as asset_type,
        equipments_asset.equipment_registration as registration_number,
        equipments_asset.equipment_name as managed_by,
        equipments_asset.equipment_id,
        add_asset_items.id as item_id, 
        GROUP_CONCAT(CONCAT(add_asset_items.item_name) SEPARATOR ", ") as part_number, 
        equipment_maintenance_asset.ticket_number,
        equipment_maintenance_asset.final_status as status,
        maintenance_task_done.task_done
    ')
    ->from('ticket')
    ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.ticket_number = ticket.ticket_number', 'left')
    ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
    ->join('add_asset_items', 'add_asset_items.asset_id = ticket.equipment_id', 'left')
    ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id', 'left')
    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
    ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
    ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id', 'left')
    ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
    ->group_by('ticket.ticket_number')
    ->where_in('ticket.id', $ids);

        $performance_record = $query->get()->result();

        

        // Fetching additional items for each asset
        $query = $this->db->select('add_asset_items.*, 
                                    add_asset_items.id as asset_id
                                   ')
            ->from('add_asset_items')
            ->where_in('add_asset_items.id', $ids);

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
                    <th>Ticket Date</th>
                    <th>Ticket Number</th>
                    <th>Severity</th>
                    <th>Asset Type</th>
                    <th>Registration Number</th>
                    <th>Location</th>
                    <th>Managed By</th>
                    <th>Manufacturer</th>
                    <th>Part Number</th>
                    <th>Maintenance Type</th>
                    <th>Task Done</th>
                    <th>Completion Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>';


        if (empty($performance_record) || count($performance_record) == 0) {
            // die(redirect("/driver_performance/index?error= No data to download."));
        }

        foreach ($performance_record as $record) {
            $html .= '<tr>';
            $html .= '<td>' . $record->issue_date . '</td>';
            $html .= '<td>' . $record->ticket_number . '</td>';
            $html .= '<td>' . $record->severity . '</td>';
            $html .= '<td>' . $record->asset_type . '</td>';
            $html .= '<td>' . $record->registration_number . '</td>';
            $html .= '<td>' . $record->location . '</td>';
            $html .= '<td>' . $record->managed_by . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';
            // $html .= '<td>' . $record->part_number . '</td>';
            // $items = isset($asset_items[$record->equipment_id]) ? implode(', ', $asset_items[$record->equipment_id]) : 'N/A';
            // $html .= '<td>' . $items . '</td>';
            $html .= '<td>' . $record->part_number . '</td>';
            $html .= '<td>' . $record->maintenance_type . '</td>';
            $html .= '<td>' . $record->task_done . '</td>';
            $html .= '<td>' . $record->date_of_completion . '</td>';
            $html .= '<td>' . $record->status . '</td>';
          

            // Fetch and format additional items

            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'ticket_summary_report.pdf');
    }

    public function downloadExcel(){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       
        foreach(range('A' , 'J') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Ticket Date');
        $sheet->setCellValue('B1' , 'Ticket Number');
        $sheet->setCellValue('C1' , 'Severity');
        $sheet->setCellValue('D1' , 'Asset Type');
        $sheet->setCellValue('E1' , 'Registration Number');
        $sheet->setCellValue('F1' , 'Location');
        $sheet->setCellValue('G1' , 'Managed By');
        $sheet->setCellValue('H1' , 'Manufacturer');
        $sheet->setCellValue('I1' , 'Part Number');
        $sheet->setCellValue('J1' , 'Maintenance Type');  
        $sheet->setCellValue('K1' , 'Task Done');  
        $sheet->setCellValue('L1' , 'Completion Date');  
        $sheet->setCellValue('M1' , 'Status');  

        $query = $this->db->select('
        ticket.*, 
        locations.name as location, 
        manufacturers.manufacturer_name,
        equipment_maintenance_asset.maintenance_type_id as maintenance_type,
        asset_types.name as asset_type,
        equipments_asset.equipment_registration as registration_number,
        equipments_asset.equipment_name as managed_by,
        equipments_asset.equipment_id,
        add_asset_items.id as item_id, 
        GROUP_CONCAT(CONCAT(add_asset_items.item_name) SEPARATOR ", ") as part_number, 
        equipment_maintenance_asset.ticket_number,
        equipment_maintenance_asset.final_status as status,
        maintenance_task_done.task_done
    ')
    ->from('ticket')
    ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.ticket_number = ticket.ticket_number', 'left')
    ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
    ->join('add_asset_items', 'add_asset_items.asset_id = ticket.equipment_id', 'left')
    ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id', 'left')
    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
    ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
    ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id', 'left')
    ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
    ->group_by('ticket.ticket_number');
    $result = $query->get()->result_array();

    $query1 = $this->db->select('add_asset_items.*, add_asset_items.id as asset_id')->from('add_asset_items');

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach($result as $row){
            $sheet->setCellValue('A'.$x, $row['issue_date']);
            $sheet->setCellValue('B'.$x, $row['ticket_number']);
            $sheet->setCellValue('C'.$x, $row['severity']);
            $sheet->setCellValue('D'.$x, $row['asset_type']);
            $sheet->setCellValue('E'.$x, $row['registration_number']);
            $sheet->setCellValue('F'.$x, $row['location']);
            $sheet->setCellValue('G'.$x, $row['managed_by']);
            $sheet->setCellValue('H'.$x, $row['manufacturer_name']);
            // $items = isset($asset_items[$row['equipment_id']]) ? implode(', ', $asset_items[$row['equipment_id']]) : 'N/A';
            // $sheet->setCellValue('I'.$x, $items);
            $sheet->setCellValue('I'.$x, $row['part_number']);
            $sheet->setCellValue('J'.$x, $row['maintenance_type']);
            $sheet->setCellValue('K'.$x, $row['task_done']);
            $sheet->setCellValue('L'.$x, $row['date_of_completion']);
            $sheet->setCellValue('M'.$x, $row['status']);
            $x++;

        }
        $writer= new Xlsx($spreadsheet);
        $fileName = 'ticket_summary_report.xlsx';
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

    public function downloadExcelSingle($ids){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       
        foreach(range('A' , 'J') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
        $spreadsheet->getActiveSheet()->getStyle('A1:M1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Ticket Date');
        $sheet->setCellValue('B1' , 'Ticket Number');
        $sheet->setCellValue('C1' , 'Severity');
        $sheet->setCellValue('D1' , 'Asset Type');
        $sheet->setCellValue('E1' , 'Registration Number');
        $sheet->setCellValue('F1' , 'Location');
        $sheet->setCellValue('G1' , 'Managed By');
        $sheet->setCellValue('H1' , 'Manufacturer');
        $sheet->setCellValue('I1' , 'Part Number');
        $sheet->setCellValue('J1' , 'Maintenance Type');  
        $sheet->setCellValue('K1' , 'Task Done');  
        $sheet->setCellValue('L1' , 'Completion Date');  
        $sheet->setCellValue('M1' , 'Status');  

        $query = $this->db->select('
        ticket.*, 
        locations.name as location, 
        manufacturers.manufacturer_name,
        equipment_maintenance_asset.maintenance_type_id as maintenance_type,
        asset_types.name as asset_type,
        equipments_asset.equipment_registration as registration_number,
        equipments_asset.equipment_name as managed_by,
        equipments_asset.equipment_id,
        add_asset_items.id as item_id, 
        GROUP_CONCAT(CONCAT(add_asset_items.item_name) SEPARATOR ", ") as part_number, 
        equipment_maintenance_asset.ticket_number,
        equipment_maintenance_asset.final_status as status,
        maintenance_task_done.task_done
    ')
    ->from('ticket')
    ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.ticket_number = ticket.ticket_number', 'left')
    ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
    ->join('add_asset_items', 'add_asset_items.asset_id = ticket.equipment_id', 'left')
    ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id', 'left')
    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
    ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
    ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id', 'left')
    ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
    ->group_by('ticket.ticket_number')
    ->where_in('ticket.id', $ids);
    $result = $query->get()->result_array();

    $query1 = $this->db->select('add_asset_items.*, add_asset_items.id as asset_id')
    ->from('add_asset_items')->where_in('add_asset_items.id', $ids);

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach($result as $row){
            $sheet->setCellValue('A'.$x, $row['issue_date']);
            $sheet->setCellValue('B'.$x, $row['ticket_number']);
            $sheet->setCellValue('C'.$x, $row['severity']);
            $sheet->setCellValue('D'.$x, $row['asset_type']);
            $sheet->setCellValue('E'.$x, $row['registration_number']);
            $sheet->setCellValue('F'.$x, $row['location']);
            $sheet->setCellValue('G'.$x, $row['managed_by']);
            $sheet->setCellValue('H'.$x, $row['manufacturer_name']);
            // $items = isset($asset_items[$row['equipment_id']]) ? implode(', ', $asset_items[$row['equipment_id']]) : 'N/A';
            // $sheet->setCellValue('I'.$x, $items);
            $sheet->setCellValue('I'.$x, $row['part_number']);
            $sheet->setCellValue('J'.$x, $row['maintenance_type']);
            $sheet->setCellValue('K'.$x, $row['task_done']);
            $sheet->setCellValue('L'.$x, $row['date_of_completion']);
            $sheet->setCellValue('M'.$x, $row['status']);
            $x++;

        }
        $writer= new Xlsx($spreadsheet);
        $fileName = 'ticket_summary_report.xlsx';
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


        // $query = $this->db->select('ticket.*, 
        //                     locations.name as location, 
        //                     manufacturers.manufacturer_name as manufacturer_name,
        //                     equipment_maintenance_asset.maintenance_type_id as maintenance_type,
        //                     asset_types.name as asset_type,
        //                     equipments_asset.equipment_registration as registration_number,
        //                     equipments_asset.equipment_name as managed_by,
        //                     equipments_asset.equipment_id as equipment_id,
        //                     add_asset_items.id as item_id, 
        //                     equipment_maintenance_asset.ticket_number as ticket_number,
        //                     equipment_maintenance_asset.final_status as status,
        //                     maintenance_task_done.task_done as task_done,
        //                     ticket.id as id,
        //                    ') // Assuming 'name' is a column in manufacturers
        //     ->from('ticket')
        //     ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.ticket_number = ticket.ticket_number' ,'left' )
        //     ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id' ,'left')
        //     ->join('item_ticket', 'item_ticket.ticket_id = ticket.id','left')
        //     ->join('add_asset_items', 'add_asset_items.id = item_ticket.item_id','left')
        //     ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type','left')
        //     ->join('locations', 'locations.id = equipments_asset.location_id' ,'left')
        //     ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id' , 'left')
        //     ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer','left')
        //     ->group_by('ticket.ticket_number');

        $query = $this->db->select('
        ticket.*, 
        locations.name as location, 
        manufacturers.manufacturer_name,
        equipment_maintenance_asset.maintenance_type_id as maintenance_type,
        asset_types.name as asset_type,
        equipments_asset.equipment_registration as registration_number,
        equipments_asset.equipment_name as managed_by,
        equipments_asset.equipment_id,
        add_asset_items.id as item_id, 
        GROUP_CONCAT(CONCAT(add_asset_items.item_name) SEPARATOR ", ") as part_number, 
        equipment_maintenance_asset.ticket_number,
        equipment_maintenance_asset.final_status as status,
        maintenance_task_done.task_done
    ')
    ->from('ticket')
    ->join('equipment_maintenance_asset', 'equipment_maintenance_asset.ticket_number = ticket.ticket_number', 'left')
    ->join('equipments_asset', 'equipments_asset.equipment_id = ticket.equipment_id', 'left')
    ->join('add_asset_items', 'add_asset_items.asset_id = ticket.equipment_id', 'left')
    ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id', 'left')
    ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
    ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
    ->join('maintenance_task_done', 'maintenance_task_done.equipment_maintenance_id = equipment_maintenance_asset.equipment_maintenance_id', 'left')
    ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer', 'left')
    ->group_by('ticket.ticket_number');
          
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
            ->where('id', $equipmentId)
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
