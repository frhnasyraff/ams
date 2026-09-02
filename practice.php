<?php

// SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));

// $info = $this->db->get_where('companies', ["company_id" => 1])->result();
// $orders = $this->db->get('orders')->result();
// $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));


// $getRes = $this->db->get("equipments_asset");
// if ($getRes->num_rows() > 0) {
//     $this->db->where('equipment_id', $this->steve->id_decode($this->input->get('id')));
//     if ($this->db->update('equipments_asset', ['qr_code' => 1])) {
//         redirect("assets/info?id=" . ($this->input->get('id')) . "&message=QR Code has been generated successfully...#nav-qr");
//     } else {
//         redirect("assets?error=Sorry QR code could not be generated at this moment, try again later.");
//     }
// }

// // 

// $document_query  = $this->db->select('incident_request_attachment_id')
// ->from('incident_requests_attachments')
// ->order_by('incident_requests_attachments.timestamp','asc')
// ->limit(1) 

// ->where("incident_requests_attachments.incident_request_id", $this->input->post('id'))
// ->where("incident_requests_attachments.deleted", 0)
// ->get();

// if($document_query->num_rows() > 0)


// // requests
// $this->input->get('id') // get request
// $this->input->post('id') //post request

// // get rows count
// $getRes = $this->db->get("equipments_asset");
// if ($getRes->num_rows() > 0) {
// }

// // count

// "workers" => $this->db->where("active = 1")->count_all_results("workers"),


// // where clause

// // delete data
// $this->db->where("company_id", $this->input->post("id"));
// $this->db->delete("company_prices");

// // select specific
// $info = $this->db->select("company_address_id as id, address_line_1 as label, location_id as value, companies.company_id, company_name")
// ->group_start()
// ->like("location_id", $this->input->get("term"))
// ->or_like("address_line_1", $this->input->get("term"))
// ->or_like("address_line_2", $this->input->get("term"))
// ->or_like("address_city", $this->input->get("term"))
// ->group_end()
// ->join("companies", "companies.company_id = company_addresses.company_id", "right")
// ->get_where("company_addresses", ($this->input->get("company") ? ["company_addresses.company_id" => $this->input->get("company")] : []))->result();

// die(json_encode($info));

// // query
// $info = $this->db->select('t1.commodity_code, SUM(t2.tonnage) as total_tonnage')
//         ->from('commodities as t1')        
//         ->join('service_request_operations as t2', 't1.commodity_id = t2.commodity_id', 'LEFT')  
//         ->where('t1.commodity_code is NOT NULL')
//         ->where('t2.commodity_id is NOT NULL')
//         ->where('MONTH(t2.t_start)', date('m'))
//         ->where('YEAR(t2.t_start)', date('Y'))
//         ->group_by('t2.commodity_id')
//         ->order_by("t2.t_start", 'DESC')
//         ->limit($last, $start)  
//         ->get();

// $array_data = $info->result_array();


// $this->db->set('equipment_id', $equipment_id)->set('equipment_group_id', $role);
// $this->db->insert('equipment_group_asset');
// $insert_id = $this->db->insert_id();

// redirect("commodities/index?message=Commodity was updated successfully.");;

// form

// action="<?=site_url("users/add");
?> method="post"

<!-- // pass data to view file -->
<!-- // $this->load->view('company-info', ['info' => $info[0], "addresses" => $addresses, "prices" => $prices]); -->




<!-- ->join('companies', 'orders.company_id=companies.company_id', 'LEFT') -->
<!-- ->join('company_addresses', 'orders.company_address_id=company_addresses.company_address_id', 'LEFT') -->



<!-- $asset = $this->db->select('order_equipment_bin_qr_codes.asset_type_id, order_equipment_bin_qr_codes.scanned, order_equipment_bin_qr_codes.created_at')
            ->from('order_equipment_bin_qr_codes')
            ->join('asset_types', 'asset_types.asset_id = order_equipment_bin_qr_codes.asset_type_id')
            ->join('equipments_asset', 'equipments_asset.equipment_id = asset_types.asset_id') -->