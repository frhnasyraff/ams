<?php
defined('BASEPATH') or exit('No direct script access allowed');
require 'vendor/autoload.php';
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Faulty_item_list_report extends CI_Controller
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

        $this->load->view('header', ['title' => "Faulty Item List", 'title2' => "Faulty Item List", "styles" => [
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



        $this->load->view('faulty_item_list');
        $this->load->view('footer', ['scripts' => [
            'design/js/report-suite.js?v=1',
            'design/js/faulty_item_list.js?v=2'
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
        item_ticket.issue_date as faulty_date,
        manufacturers.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id' , 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer','left')   
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
                    <th>Asset Registration Number</th>
                    <th>Location</th>
                    <th>Date Installed</th>
                    <th>Date Faulty</th>
                    <th>Managed By</th>
                    <th>Asset Items</th>
                    <th>Manufacturer Name</th>
                    <th>Part Number</th>
                    <th>Location</th>
                    
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
            $html .= '<td>' . $record->faulty_date . '</td>';
            $html .= '<td>' . $record->equipment_name . '</td>';
            $html .= '<td>' . $record->item_type_name . '</td>';
            $html .= '<td>' . $record->manufacturer_name . '</td>';
            // $items = isset($asset_items[$record->equipment_id]) ? implode(', ', $asset_items[$record->equipment_id]) : 'N/A';
            // $html .= '<td>' . $items . '</td>';
            $html .= '<td>' . $record->part_number . '</td>';
            $html .= '<td>' . $record->store_location . '</td>';
            ;

            // Fetch and format additional items

            $html .= '</tr>';
        }

        $html .= '<tbody>';
        $html .= '</table>';
        $html .= '</body>';
        $html .= '</html>';

        $this->pdf->createPDF($html, date('Ymd') . 'faulty_item_list.pdf');
    }

   

    public function ajax_list()
    {


        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        item_ticket.issue_date as faulty_date,
        manufacturers.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id' , 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer','left')
        ->group_by('equipments_asset.equipment_id');
   
   
        $result = $query->get()->result();   
        

        $data = [
            "data" => $result
        ];

        echo json_encode($data);
    }

    public function downloadExcelSingle($ids){
        
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
       
        foreach(range('A' , 'J') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
    $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Asset Type');
       
        $sheet->setCellValue('B1' , 'Asset Registration Number');
        $sheet->setCellValue('C1' , 'Location');
        $sheet->setCellValue('D1' , 'Date Installed');
        $sheet->setCellValue('E1' , 'Date Faulty');
        $sheet->setCellValue('F1' , 'Managed By');
        $sheet->setCellValue('G1' , 'Asset Items');
        $sheet->setCellValue('H1' , 'Manufacturer Name');
        $sheet->setCellValue('I1' , 'Part Number');
        $sheet->setCellValue('J1' , 'Location');  
       
        
        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        item_ticket.issue_date as faulty_date,
        manufacturers.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id' , 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer','left')
        ->group_by('equipments_asset.equipment_id')
        ->where_in('equipments_asset.equipment_id', $ids);
   
        $result = $query->get()->result_array();

        $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')
        ->from('add_asset_items')->where_in('add_asset_items.asset_id', $ids);

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach($result as $row){
            $sheet->setCellValue('A'.$x, $row['type_name']);
            $sheet->setCellValue('B'.$x, $row['equipment_registration']);
            $sheet->setCellValue('C'.$x, $row['location']);
            $sheet->setCellValue('D'.$x, $row['date_installed']);
            $sheet->setCellValue('E'.$x, $row['faulty_date']);
            $sheet->setCellValue('F'.$x, $row['equipment_name']);
            $sheet->setCellValue('G'.$x, $row['item_type_name']);
            $sheet->setCellValue('H'.$x, $row['manufacturer_name']);
            // $items = isset($asset_items[$row['equipment_id']]) ? implode(', ', $asset_items[$row['equipment_id']]) : 'N/A';
            // $sheet->setCellValue('I'.$x, $items);
            $sheet->setCellValue('I'.$x, $row['part_number']);
            $sheet->setCellValue('J'.$x, $row['store_location']);
            $x++;

        }
        $writer= new Xlsx($spreadsheet);
        $fileName = 'faulty_item_list.xlsx';
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
       
        foreach(range('A' , 'J') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        // Apply bold formatting to the first row (A1:J1)
    $spreadsheet->getActiveSheet()->getStyle('A1:J1')->getFont()->setBold(true);
        $sheet->setCellValue('A1' , 'Asset Type');
       
        $sheet->setCellValue('B1' , 'Asset Registration Number');
        $sheet->setCellValue('C1' , 'Location');
        $sheet->setCellValue('D1' , 'Date Installed');
        $sheet->setCellValue('E1' , 'Date Faulty');
        $sheet->setCellValue('F1' , 'Managed By');
        $sheet->setCellValue('G1' , 'Asset Items');
        $sheet->setCellValue('H1' , 'Manufacturer Name');
        $sheet->setCellValue('I1' , 'Part Number');
        $sheet->setCellValue('J1' , 'Location');  
       
        
        $query = $this->db->select('equipments_asset.*, 
        locations.name as location, 
        asset_types.name as type_name, 
        item_types.name as item_type_name, 
        add_asset_items.item_name as part_number, 
        store_location.name as store_location, 
        item_ticket.issue_date as faulty_date,
        manufacturers.manufacturer_name as manufacturer_name, ')
        ->from('equipments_asset')
        ->join('add_asset_items', 'add_asset_items.asset_id = equipments_asset.equipment_id', 'left')
        ->join('item_ticket', 'item_ticket.item_id = add_asset_items.id' , 'left')
        ->join('store_location', 'store_location.id = equipments_asset.store_location_id', 'left')
        ->join('item_types', 'item_types.id = add_asset_items.item_type_id', 'left')
        ->join('locations', 'locations.id = equipments_asset.location_id', 'left')
        ->join('asset_types', 'asset_types.asset_id = equipments_asset.equipment_type', 'left')
        ->join('manufacturers', 'manufacturers.manufacturer_id = equipments_asset.equipment_manufacturer','left')
        ->group_by('equipments_asset.equipment_id');
   
        $result = $query->get()->result_array();

        $query1 = $this->db->select('add_asset_items.*, add_asset_items.asset_id as asset_id')
        ->from('add_asset_items');

        $performance_record1 = $query1->get()->result();

        // Group items by asset_id
        $asset_items = [];
        foreach ($performance_record1 as $item) {
        $asset_items[$item->asset_id][] = $item->item_name;
        }

        $x = 2; //start from row 2
        foreach($result as $row){
            $sheet->setCellValue('A'.$x, $row['type_name']);
            $sheet->setCellValue('B'.$x, $row['equipment_registration']);
            $sheet->setCellValue('C'.$x, $row['location']);
            $sheet->setCellValue('D'.$x, $row['date_installed']);
            $sheet->setCellValue('E'.$x, $row['faulty_date']);
            $sheet->setCellValue('F'.$x, $row['equipment_name']);
            $sheet->setCellValue('G'.$x, $row['item_type_name']);
            $sheet->setCellValue('H'.$x, $row['manufacturer_name']);
            // $items = isset($asset_items[$row['equipment_id']]) ? implode(', ', $asset_items[$row['equipment_id']]) : 'N/A';
            // $sheet->setCellValue('I'.$x, $items);
            $sheet->setCellValue('I'.$x, $row['part_number']);
            $sheet->setCellValue('J'.$x, $row['store_location']);
            $x++;

        }
        $writer= new Xlsx($spreadsheet);
        $fileName = 'faulty_item_list.xlsx';
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

    public function exportExcel(){
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach(range('A' , 'F') as $columID){
            $spreadsheet->getActiveSheet()->getColumnDimension($columID)->setAutosize(true);
        }
        $sheet->setCellValue('A1' , 'ID');
        $sheet->setCellValue('B1' , 'Name');
        $sheet->setCellValue('C1' , 'Email');
        $sheet->setCellValue('D1' , 'Mobile');
        $sheet->setCellValue('E1' , 'City');
        $sheet->setCellValue('F1' , 'Country');
        
        $users = $this->db->query("SELECT * FROM users")->result_array();
        $x = 2; //start from row 2
        foreach($users as $row){
            $sheet->setCellValue('A'.$x, $row['id']);
            $sheet->setCellValue('B'.$x, $row['id']);
            $sheet->setCellValue('C'.$x, $row['id']);
            $sheet->setCellValue('D'.$x, $row['id']);
            $sheet->setCellValue('E'.$x, $row['id']);
            $sheet->setCellValue('F'.$x, $row['id']);
            $x++;
        }

        $writer= new Xlsx($spreadsheet);
        $fileName = 'users_details_export.xlsx';
        // $writer->save($fileName); // this is for download in folder

        // for force Download 
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Conent-Disposition: attachment; filename="'.$fileName.'"');
        $writer->save('php://output');

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
}
