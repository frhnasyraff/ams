<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Common_csv_upload extends CI_Controller
{
    public function index()
    {
        $this->load->view('header', ['title' => "Equipment Asset Csv", 'title2' => "Equipment Asset", "styles" => []]);

        $this->load->view('common-csv.php', []);
        $this->load->view('footer', ['scripts' => []]);
    }

    function uploadEquipmentAssets()
    {
        if (isset($_FILES['file'])) {
            if ($_FILES['file']['error'] == 0) {
                $ext = pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
                if ($ext == 'csv') {

                    $csv = fopen($_FILES['file']['tmp_name'], 'r') or die("can't open file");
                    $i = 0;
                    while ($row = fgetcsv($csv, 1024)) {
                        $i++;
                        if ($i == 1) {
                            continue;
                        }

                        $asset_name = $row[0];
                        $registration_no = $row[1];
                        $purchase_date = $row[2];
                        $asset_manufacturer = $row[3];
                        $asset_type = $row[4];
                        $branch_office_address = $row[5];
                        $ownership = $row[6];

                        // get manufacturer id by name 
                        $manufacturer_id = NULL;
                        $manufacturer = $this->db->select('manufacturer_id')->from('manufacturers')->where('manufacturer_name', $asset_manufacturer)->get()->row();
                        if ($manufacturer) {
                            $manufacturer_id = $manufacturer->manufacturer_id;
                        }

                        // get branch id by name 
                        $branch_id = 0;
                        $branch = $this->db->select('branch_id')->from('branch_office')->where('branch_name', $branch_office_address)->get()->row();
                        if ($branch) {
                            $branch_id = $branch->branch_id;
                        }

                        // get asset_types id by name
                        $asset_type_id = 0;
                        $asset_type_row = $this->db->select('asset_id')->from('asset_types')->where('name', $asset_type)->get()->row();
                        if ($asset_type_row) {
                            $asset_type_id = $asset_type_row->asset_id;
                        }

                        $this->db->set('equipment_registration', $registration_no);
                        $this->db->set('equipment_name', $asset_name);
                        $this->db->set('purchase_date', date('Y-m-d', strtotime($purchase_date)));
                        $this->db->set('equipment_manufacturer', $manufacturer_id);
                        $this->db->set('ownership', $ownership);
                        $this->db->set('branch_office_id', $branch_id);
                        $this->db->set('equipment_type', $asset_type_id);

                        $this->db->insert("equipments_asset");
                    }
                    die(redirect("common_csv_upload/index?message=Equipment assets imported Successfully"));
                }
            }
        }
    }
}
