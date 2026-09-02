<?php
defined('BASEPATH') or exit('No direct script access allowed');
use NumberToWords\NumberToWords;

/**
 * User_model class.
 *
 * @extends CI_Model
 */
class MYPDF extends TCPDF
{
    //Page header
    public function Header()
    {
        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);

        $this->SetTopMargin(35);

        $this->SetFont('times', '', 12);
        $this->Image(realpath("resources/gss-logo.png"), 5, 5, 22 * 1.25, 21 * 1.25);

        $this->writeHTMLCell(220, 20, 0, 5, '<h1 align="center">GSS PORT SERVICES SDN. BHD. <small>(1241767-K)</small></h1>', 0, 0, false, true, 'center');
        $this->writeHTMLCell(220, 20, 0, 13, '<div align="center">Sublot 52 & 53 (Survey Lot 201 & 202), Samalaju Central,<br />Block 1, Kemena Land District, Samalaju Industrial Park,<br />97000 Bintulu, Sarawak, Malaysia.</div>', 0, 0, false, true, 'center');

        $this->SetAutoPageBreak($auto_page_break, $bMargin);

        // set the starting point for the page content
        $this->setPageMark();
    }
}

class Documents extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function new_pdf($title, $draft = 0, $bl = null)
    {
        $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        $pdf->SetCreator("Steve");
        $pdf->SetAuthor('GSS');
        $pdf->SetTitle($title);

        $pdf->SetSubject($title . ' PDF');
        $pdf->SetKeywords('Steve, ' . $title);

        $pdf->SetHeaderMargin(0);

        $pdf->setPrintHeader(true);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetMargins(PDF_MARGIN_LEFT / 2, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT / 2);

        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER / 2);
        $pdf->setPrintFooter(true);
        $pdf->SetAutoPageBreak(true, PDF_MARGIN_BOTTOM / 4);
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
        $pdf->AddPage();

        return $pdf;
    }

    public function generate_invoice_pdf($vessel_visit_id, $company_id, $items, $disposal_prices = [], $disposal_weights = [])
    {

//        $service_operations

        $pdf = $this->new_pdf("Invoice");

        $company_address = $this->db->join("companies", "companies.company_id = company_addresses.company_id", "left")->get_where("company_addresses", ["company_addresses.company_id" => $company_id, "finance" => 1])->result();

        $company_address = $company_address[0];

        $vessel_visit = $this->db->get_where('vessel_visits', ["vessel_visit_id" => $vessel_visit_id])->result();

        $pdf->SetFont('times', '', 12);

        $pdf->writeHTMLCell(210, 8, 0, 32, '<h2 align="center">INVOICE</h2>', 0, 0, false, true, 'center');

        $today = date("d-m-Y");
        $pdf->SetFont('times', '', 11);

        $address = $this->steve->make_address($company_address);
        $invoice_number = $this->db->select("max(invoice_id) as invoice_number")->get_where("invoices")->result();
        $invoice_number[0]->invoice_number++;
        $invoice_number = "GSP" . date("y-m") . '-' . sprintf('%06d', $invoice_number[0]->invoice_number);

        $voyage = $vessel_visit[0]->visit_voyage;

        $tbl = <<<EOD
&nbsp;<br /><table cellspacing="0" cellpadding="3" style="border: 0px solid white;" border="0" width="100%">
<tr>
<td align="left" width="55%" rowspan="5"><br />Billing Address<br /><strong>$company_address->company_name</strong><br />$address</td>
<td></td>
<td></td>
<td></td>
</tr>
<tr>
<td align="left" width="21%">INVOICE NO.</td>
<td align="left" width="2%">:</td>
<td width="22%">$invoice_number</td>
</tr>
<tr>
<td align="left" width="21%">INVOICE DATE</td>
<td align="left" width="2%">:</td>
<td width="22%">$today</td>
</tr>
<tr>
<td align="left" width="21%">VOYAGE</td>
<td align="left" width="2%">:</td>
<td width="22%">$voyage</td>
</tr>
<tr>
<td align="left" width="21%">PAYMENT TERM</td>
<td align="left" width="2%">:</td>
<td width="22%">30 DAYS</td>
</tr>
</table>
EOD;

        $pdf->writeHTML($tbl);

        $pdf->Line(7, $pdf->getY(), 203, $pdf->getY(), array('width' => 0.25));
        $pdf->Line(7, 97, 203, 97, array('width' => 0.25));
        $pdf->SetXY(7, 87);

        $costs = '';
        $total = 0;

        foreach ($items as $bill) {
            $bill = json_decode($bill);
            if ($bill->type == "disposal") {
                $bill->price = $disposal_prices[$bill->id];
            } else if ($bill->type == "disposal_weight") {
                $bill->price = $disposal_weights[$bill->id];
            }
            
            $costs .= '<tr><td align="left" width="50%">' . $bill->title . '</td><td align="left" width="10%">' . $bill->units . '</td><td align="center" width="8%">' . $bill->unit . '</td><td align="center" width="15%">' . number_format($bill->price, 2) . '</td><td align="right" width="17%">' . number_format($bill->units * $bill->price, 2) . '</td></tr>';
            $total += round($bill->units * $bill->price, 2);
        }

        $tbl = '<table cellspacing="0" cellpadding="3" style="border: 0px solid white;" width="100%" border="0"><tr><td align="left" width="50%">DESCRIPTION</td><td align="left" width="10%">QTY</td><td align="left" width="10%">UOM</td><td align="left" width="15%">PRICE/UNIT</td><td align="left" width="15%">AMOUNT (RM)</td></tr><tr><td>&nbsp;</td></tr><tr><td>BEING COST OF:~</td></tr>' . $costs . '</table>';
        $pdf->writeHTML($tbl);

        $numberToWords = new NumberToWords();
        $currencyTransformer = $numberToWords->getNumberTransformer('en');

        $pdf->setY(215);
        $pdf->Line(7, $pdf->getY(), 203, $pdf->getY(), array('width' => 0.25));

        $pdf->writeHTML('RINGGIT MALAYSIA : ' . strtoupper($currencyTransformer->toWords($total)) . ' ONLY');

        $pdf->Line(7, $pdf->getY(), 203, $pdf->getY(), array('width' => 0.25));

        $pdf->writeHTML('<table><tr><td width="50%">Payment Terms<br />30 DAYS</td><td align="right" width="40%">Total Payable<br />' . number_format($total, 2) . '</td></tr></table>');

        $pdf->Line(7, $pdf->getY(), 203, $pdf->getY(), array('width' => 0.25));

        $pdf->writeHTML('<br /><div align="right">Authorised Signature<br /><strong>GSS PORT SERVICES SDN BHD (1241767-K)</strong></div>');

        $pdf->setY(272);
        $pdf->Line(7, $pdf->getY(), 203, $pdf->getY(), array('width' => 0.25));
        $pdf->SetFont('times', '', 8);

        $pdf->writeHTML('TERMS AND CONDITIONS :<br />
a) Any discrepancies pertaining to this invoice should be lodged within 7 days from date of receipt of this invoice.<br />
b) Payment by cheque be crossed and made payable to GSS PORT SERVICES SDN BHD - PIB Bank A/C No. 3814922516 <br />c) We reserve the right to charge interest at the rate of 1.5% per month on overdue invoice.<br />
d) Ship NOT responsible for breakage, leakage, shortage & weight of the contents.');

        $filename = 'Invoice-' . $invoice_number . '.pdf';

        $pdf->Output(realpath("storage") . '/' . $filename, 'F');

        if (file_exists(realpath("storage") . '/' . $filename)) {
            $this->db->set("invoice_number", $invoice_number);
            $this->db->set("value", $total);
            $this->db->set("company_id", $company_id);
            $this->db->set("vessel_visit_id", $vessel_visit_id);

            $this->db->set("filename", $filename);
            if ($this->db->insert("invoices")) {

                $invoiceid = $this->db->insert_id();

                foreach ($items as $bill) {
                    $bill = json_decode($bill);
                    if ($bill->type == "commodity") {
                        $this->db->reset_query();
                        $this->db->set("invoice_id", $invoiceid);
                        $this->db->set("price", $bill->price);
                        $this->db->where("service_request_operation_id", $bill->id);
                        $this->db->update("service_request_operations");
                    } else if ($bill->type == "delay") {
                        $this->db->reset_query();
                        $this->db->set("tally_invoice_id", $invoiceid);
                        $this->db->set("tally_price", $bill->price * $bill->units);
                        $this->db->where("service_request_operation_tally_id", $bill->id);
                        $this->db->update("service_request_operation_tally");
                    } else if ($bill->type == "gears") {
                        $this->db->reset_query();
                        $this->db->set("invoice_id", $invoiceid);
                        $this->db->set("price", $bill->price * $bill->units);
                        $this->db->where("vessel_visit_gear_id", $bill->id);
                        $this->db->update("vessel_visit_gears");
                    } else if ($bill->type == "equipments") {
                        $this->db->reset_query();
                        $this->db->set("invoice_id", $invoiceid);
                        $this->db->set("price", $bill->price * $bill->units);
                        $this->db->where("vessel_visit_equipment_id", $bill->id);
                        $this->db->update("vessel_visit_equipments");
                    } else if ($bill->type == "workers") {
                        $this->db->reset_query();
                        $this->db->set("invoice_id", $invoiceid);
                        $this->db->set("price", $bill->price * $bill->units);
                        $this->db->where("vessel_visit_worker_id", $bill->id);
                        $this->db->update("vessel_visit_workers");
                    } else if ($bill->type == "work_meal") {
                        $this->db->reset_query();
                        $this->db->set("work_meal_invoice_id", $invoiceid);
                        $this->db->set("work_meal_price", $bill->price * $bill->units);
                        $this->db->where("service_request_id", $bill->id);
                        $this->db->update("service_requests");
                    } else if ($bill->type == "disposal") {
                        $this->db->reset_query();
                        $this->db->set("disposal_invoice_id", $invoiceid);
                        $this->db->set("disposal_price", ($disposal_prices[$bill->id] ? $disposal_prices[$bill->id] : 0));
                        $this->db->where("service_request_disposal_id", $bill->id);
                        $this->db->update("service_request_disposals");
                    }
                }

                $this->db->set("invoice_id", $this->db->insert_id());
                $this->db->where_in("service_voucher_id", $service_voucher_ids);
                if ($this->db->update("service_vouchers")) {
                    $this->logs->add("vessel_visits", $ssrs[0]->vessel_visit_id, "INVOICE_GENERATED");
                    return true;
                }
            }
        }
    }

    public function generate_service_voucher_pdf($vessel_visit_id, $shift, $date, $company_id)
    {

        $pdf = $this->new_pdf("Service voucher");

        $ssrs = $this->db->join("companies", "companies.company_id = service_requests.company_id", "left")->join("vessel_visits", "vessel_visits.vessel_visit_id = service_requests.vessel_visit_id", "left")->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id", "left")->join("cargo_types", "cargo_types.cargo_type_id = service_requests.cargo_type", "left")->join("port_wharfs", "port_wharfs.port_wharf_id = vessel_visits.port_wharf_id", "left")->join("ports", "ports.port_id = port_wharfs.port_id", "left")->where_in("service_request_status", ['approved'])->get_where("service_requests", ["service_requests.deleted" => 0, "service_requests.vessel_visit_id" => $vessel_visit_id, "service_requests.not_chargeable" => 0, "service_requests.company_id" => $company_id])->result();

        foreach ($ssrs as $ssr) {
            $ssrids[] = $ssr->service_request_id;
        }

        $remarks = [];

        $operations = $this->db->where_in("service_request_operations.service_request_id", $ssrids)->where("date('" . $date . "') between t_start and t_end")->join("commodities", "commodities.commodity_id = service_request_operations.commodity_id", "left")->join("operation_types", "operation_types.operation_type_id = service_request_operations.operation_type", "left")->join("vessel_hatches", "vessel_hatches.vessel_hatch_id = service_request_operations.vessel_hatch_id", "left")->join("service_requests", "service_request_operations.service_request_id = service_requests.service_request_id", "left")->get_where("service_request_operations", ["service_request_operations.deleted" => 0])->result();

        foreach ($operations as $operation) {
            $remarks[$operation->vessel_hatch_id . "-" . $operation->operation_type_id][] = $operation;
        }

        $vessel_visit_workers = $this->db->join("workers", "workers.worker_id = vessel_visit_workers.worker_id", "left")->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")->get_where('vessel_visit_workers', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

        $worker_sets = [];

        foreach ($vessel_visit_workers as $workers) {
            $worker_sets[strtoupper($workers->resource_type_name)]++;
        }

        $vessel_visit_equipments = $this->db->join("equipments", "equipments.equipment_id = vessel_visit_equipments.equipment_id", "left")->join("equipment_types", "equipment_types.equipment_type_id = equipments.equipment_type", "left")->get_where('vessel_visit_equipments', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

        $equipment_sets = [];

        foreach ($vessel_visit_equipments as $equipment) {
            $equipment_sets[strtoupper($equipment->equipment_type_name)]++;
        }

        $vessel_visit_gears = $this->db->join("gears", "gears.gear_id = vessel_visit_gears.gear_id", "left")->join("gear_types", "gear_types.gear_type_id = gears.gear_type", "left")->get_where('vessel_visit_gears', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

        foreach ($vessel_visit_gears as $gear) {
            $equipment_sets[strtoupper($gear->gear_type_name)]++;
        }

        $pdf->SetFont('times', '', 12);

        $pdf->writeHTMLCell(210, 8, 0, 32, '<h2 align="center">SERVICE VOUCHER</h2>', 0, 0, false, true, 'center');

        $ssr = $ssrs[0];
        $today = date("d-m-Y");
        $op_date = date("d-m-Y", strtotime($date));

        $tbl = <<<EOD
&nbsp;<br /><table cellspacing="0" cellpadding="3" style="border: 1px solid black;" width="100%">
<tr>
<td align="left" width="15%"> Company</td>
<td align="left" width="2%">:</td>
<td width="43%" style="font-family: helvetica; color: #6B829B">$ssr->company_name </td>
<td align="left" width="16%"> SV No.</td>
<td align="left" width="2%">:</td>
<td width="22%" style="font-family: helvetica; color: #6B829B">SV$ssr->vessel_visit_id-$company_id-$date-$shift</td>
</tr>
<tr>
<td align="left" width="15%"> Vessel</td>
<td align="left" width="2%">:</td>
<td width="43%" style="font-family: helvetica; color: #6B829B">$ssr->vessel_name</td>
<td align="left" width="16%"> SV Date.</td>
<td align="left" width="2%">:</td>
<td width="22%" style="font-family: helvetica; color: #6B829B">$today</td>
</tr>
<tr>
<td align="left" width="15%"> Visit SCN</td>
<td align="left" width="2%">:</td>
<td width="43%" style="font-family: helvetica; color: #6B829B">$ssr->visit_scn</td>
<td align="left" width="16%"> Operation Date.</td>
<td align="left" width="2%">:</td>
<td width="22%" style="font-family: helvetica; color: #6B829B">$op_date</td>
</tr>
</table>
EOD;

        $pdf->writeHTML($tbl);

        $tbl = '<table cellspacing="0" cellpadding="3" width="100%" border="0" style="color: #6B829B; font-family: helvetica;"><tr><td width="10%"></td><td width="75%"></td><td width="15%"></td></tr>';

        $i = 0;

        foreach ($worker_sets as $worker => $count) {
            $i++;
            $tbl .= '<tr><td> &nbsp; &nbsp; ' . $i . ')</td><td> &nbsp; ' . $worker . '</td><td> &nbsp;  &nbsp; ' . $count . ' MPW</td></tr>';
        }
        foreach ($equipment_sets as $equipment => $count) {
            $i++;
            $tbl .= '<tr><td> &nbsp; &nbsp; ' . $i . ')</td><td> &nbsp; ' . $equipment . '</td><td> &nbsp;  &nbsp; ' . $count . ' UNIT' . ($count > 1 ? 'S' : '') . '</td></tr>';
        }

        $tbl .= '</table>';

        $pdf->writeHTMLCell(197, 50, 6, 80, $tbl, 0, 0, false, true, 'center');

        $pdf->SetXY(7, 72);

        $tbl = '<table cellspacing="0" cellpadding="3" style="border: 1px solid black;" width="100%" border="1"><tr bgcolor="black" color="white"><td align="center" width="10%"><strong>ITEM</strong></td><td align="center" width="75%"><strong>DESCRIPTION</strong></td><td align="center" width="15%"><strong>UNIT</strong></td></tr><tr><td></td><td align="center"><strong>SHIFT : ' . $shift . ' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; TIME : ' . ($shift == "1" ? "07:00 - 19:00" : "19:00 - 07:00") . '</strong></td><td></td></tr><tr><td style="height: 450px;"></td><td></td><td></td></tr></table>';
        $pdf->writeHTML($tbl);

        $tbl = '<table cellspacing="0" cellpadding="3" style="border: 1px solid black;" width="100%" border="1"><tr><td style="height: 85px;"><strong>&nbsp;&nbsp;REMARKS:</strong><br /><small style="font-family: helvetica; color: #6B829B">';

        if (count($remarks)) {
            foreach ($remarks as $hatch) {
                $tbl .= '<strong>' . $operation->service_request_number . ' -
' . $operation->hatch_name . ' - ' . $operation->operation_type_name . '</strong> - ';

                foreach ($hatch as $operation) {

                    if ($operation->commodity_code) {
                        $tbl .= $operation->commodity_code;
                    }
                    if ($operation->tonnage && $operation->quantity) {
                        $tbl .= ' (' . $operation->tonnage . 'MT. ' . $operation->quantity . ' units)';
                    } else {
                        if ($operation->tonnage) {
                            $tbl .= ' (' . $operation->tonnage . ' MT.) ';
                        }
                        if ($operation->quantity) {
                            $tbl .= ' (' . $operation->quantity . ' units)';
                        }
                    }
                    $tbl .= '; ';

                }
                $tbl .= '<br />';
            }
        }

        $tbl .= '</small></td></tr></table>';

        $pdf->writeHTML($tbl);

        $tbl = '<table cellspacing="0" cellpadding="3" style="border: 1px solid black;" width="100%" border="1"><tr><td width="50%"><strong>&nbsp;&nbsp;ISSUED BY:</strong><br /> <br />&nbsp;&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp; Name&nbsp;: _&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp; Date&nbsp;&nbsp;&nbsp;: _&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp;</td><td width="50%"><strong>&nbsp;&nbsp;ACKNOWLEDGED & RECEIVED BY:</strong><br /> <br />&nbsp;&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp; Name&nbsp;: _&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp; Date&nbsp;&nbsp;&nbsp;: _&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_&nbsp;_<br />&nbsp;</td></tr></table>';

        $pdf->writeHTML($tbl);

        $filename = 'SV' . $ssr->vessel_visit_id . '-' . $company_id . "-" . $date . '-' . $shift . '.pdf';

        $pdf->Output(realpath("storage") . '/' . $filename, 'F');

        if (file_exists(realpath("storage") . '/' . $filename)) {

            $this->db->set("vessel_visit_id", $vessel_visit_id);
            $this->db->set("shift", $shift);
            $this->db->set("company_id", $company_id);
            $this->db->set("operation_date", $date);
            $this->db->set("filename", $filename);
            if ($this->db->insert("service_vouchers")) {
                $this->logs->add("vessel_visits", $vessel_visit_id, "SV_GENERATED", "Date " . $date . " shift " . $shift);
                return true;
            }

        }
    }

    public function generate_deployment_plan_pdf($vessel_visit_id, $date)
    {

        $pdf = $this->new_pdf("Deployment plan");

        $ssrs = $this->db->join("companies", "companies.company_id = service_requests.company_id", "left")->join("vessel_visits", "vessel_visits.vessel_visit_id = service_requests.vessel_visit_id", "left")->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id", "left")->join("cargo_types", "cargo_types.cargo_type_id = service_requests.cargo_type", "left")->join("port_wharfs", "port_wharfs.port_wharf_id = vessel_visits.port_wharf_id", "left")->join("ports", "ports.port_id = port_wharfs.port_id", "left")->where_in("service_request_status", ['approved'])->get_where("service_requests", ["service_requests.deleted" => 0, "service_requests.vessel_visit_id" => $vessel_visit_id])->result();

        $ssrids = [];

        if ($ssrs) {
            foreach ($ssrs as $ssr) {
                $ssrids[] = $ssr->service_request_id;
                
            }

            $remarks = [];

            $operations = $this->db->where_in("service_request_operations.service_request_id", $ssrids)->where("date('" . $date . "') between t_start and t_end")->join("commodities", "commodities.commodity_id = service_request_operations.commodity_id", "left")->join("operation_types", "operation_types.operation_type_id = service_request_operations.operation_type", "left")->join("vessel_hatches", "vessel_hatches.vessel_hatch_id = service_request_operations.vessel_hatch_id", "left")->join("service_requests", "service_request_operations.service_request_id = service_requests.service_request_id", "left")->get_where("service_request_operations", ["service_request_operations.deleted" => 0])->result();

            $commodities = [];
            $gangs = '';

            foreach ($operations as $operation) {
                $commodities[$operation->commodity_id] = $operation->commodity_code;
            }
            for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                $gangs .= '<td style="border: 1px solid black;" align="center"><strong>GANG ' . $i . '</strong></td>';
            }

            $vessel_visit_workers = $this->db->join("workers", "workers.worker_id = vessel_visit_workers.worker_id", "left")->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")->get_where('vessel_visit_workers', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date])->result();

            foreach ([1, 2] as $shift) {

                $vessel_visit_workers = $this->db->join("workers", "workers.worker_id = vessel_visit_workers.worker_id", "left")->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")->get_where('vessel_visit_workers', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

                $vessel_visit_equipments = $this->db->join("equipments", "equipments.equipment_id = vessel_visit_equipments.equipment_id", "left")->join("equipment_types", "equipment_types.equipment_type_id = equipments.equipment_type", "left")->get_where('vessel_visit_equipments', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

                $vessel_visit_gears = $this->db->join("gears", "gears.gear_id = vessel_visit_gears.gear_id", "left")->join("gear_types", "gear_types.gear_type_id = gears.gear_type", "left")->get_where('vessel_visit_gears', ["vessel_visit_id" => $vessel_visit_id, "operation_date" => $date, "shift" => $shift])->result();

                $worker_sets = [];
                $equipment_sets = [];
                $worker_group_sets = [];
                $equipment_group_sets = [];

                foreach ($vessel_visit_workers as $workers) {
                    if (!$worker_group_sets[$workers->gang]) {
                        $worker_group_sets[$workers->gang] = [];
                    }

                    if ($workers->worker_resource_type_override) {
                        $resource_type = $this->steve->resource_type($workers->worker_resource_type_override);
                        $workers->resource_type_name = $resource_type->resource_type_name;
                        $workers->worker_name = $workers->worker_name . ' (SUB)';
                    }
                    
                    if (!$worker_group_sets[$workers->gang][$workers->resource_type_name]) {
                        $worker_group_sets[$workers->gang][$workers->resource_type_name] = [];
                    }
                    if (!$this->steve->worker_absent($workers->worker_id, $workers->operation_date)) {
                        $worker_group_sets[$workers->gang][$workers->resource_type_name][] = $workers->worker_name;
                    }

                    sort($worker_group_sets[$workers->gang][$workers->resource_type_name]);

                    $worker_sets[$workers->resource_type_name]++;
                }

                foreach ($vessel_visit_equipments as $equipments) {
                    if (!$equipment_group_sets[$equipments->gang]) {
                        $equipment_group_sets[$equipments->gang] = [];
                    }

                    if (!$equipment_group_sets[$equipments->gang][$equipments->equipment_type_name]) {
                        $equipment_group_sets[$equipments->gang][$equipments->equipment_type_name] = [];
                    }

                    $equipment_group_sets[$equipments->gang][$equipments->equipment_type_name][] = $equipments->equipment_name . " - " . $equipments->equipment_registration;

                    sort($equipment_group_sets[$equipments->gang][$equipments->equipment_type_name]);

                    $equipment_sets[$equipments->equipment_type_name]++;
                }

                arsort($worker_sets);
                arsort($equipment_sets);

                $pdf->SetFont('helvetica', '', 8);

                $y = 35 + (130 * ($shift - 1));

                if ($pdf->getY() > $y) {
                    $pdf->AddPage();
                    $y = $pdf->getY();
                }

                $pdf->writeHTMLCell(100, 8, 6, $y, implode("/", $commodities), 0, 0, false, true);
                
                $pdf->writeHTMLCell(100, 8, 50, $y, $ssr->work_meals ?' Working Through Meals : <strong><span class="text-success"> Yes</span>' : '<span class="text-danger">No </span></strong>', 0, 0, false, true);
                
                $pdf->writeHTMLCell(210, 8, 20, $y, '<div align="center">VESSEL: <strong>' . $ssr->vessel_name . '</strong> </div>', 0, 0, false, true, 'center');

                $pdf->writeHTMLCell(200, 8, 0, $y, '<div align="right">' . date('d-m-Y', strtotime($date)) . '</strong> - Shift ' . $shift . '</div>', 0, 0, false, true, 'right');

                $ssr = $ssrs[0];
                $today = date("d-m-Y");
                $op_date = date("d-m-Y", strtotime($date));

                $worker_rows = [];
                $equipment_rows = [];
                $pdf->SetFont('helvetica', '', 6);

                foreach ($worker_sets as $worker_set_name => $count) {

                    $total_count = 0;

                    $row_html = '<tr>';
                    for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                        $row_html .= '<td style="border: 1px solid black;" align="center"><strong>' . strtoupper($worker_set_name) . '</strong></td>';
                        if ($worker_group_sets[$i][$worker_set_name] && $total_count < count($worker_group_sets[$i][$worker_set_name])) {
                            $total_count = count($worker_group_sets[$i][$worker_set_name]);
                        }
                    }

                    $row_html .= '</tr>';

                    for ($j = 1; $j <= $total_count; $j++) {
                        $row_html .= '<tr>';
                        for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                            $row_html .= '<td style="border: 1px solid black;" align="left"> ' . $j . ') ' . $worker_group_sets[$i][$worker_set_name][$j - 1] . '</td>';
                        }
                        $row_html .= '</tr>';
                    }

                    $row_html .= '<tr>';
                    for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                        $row_html .= '<td style="border: 1px solid black;" align="center">&nbsp;</td>';
                    }
                    $row_html .= '</tr>';

                    $worker_rows[] = $row_html;
                }
                $worker_rows = implode("\n", $worker_rows);

                foreach ($equipment_sets as $equipment_set_name => $count) {

                    $total_count = 0;

                    $row_html = '<tr>';
                    for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                        $row_html .= '<td style="border: 1px solid black;" align="center"><strong>' . strtoupper($equipment_set_name) . '</strong></td>';
                        if ($equipment_group_sets[$i] && $equipment_group_sets[$i][$equipment_set_name] && $total_count < count($equipment_group_sets[$i][$equipment_set_name])) {
                            $total_count = count($equipment_group_sets[$i][$equipment_set_name]);
                        }
                    }

                    $row_html .= '</tr>';

                    for ($j = 1; $j <= $total_count; $j++) {
                        $row_html .= '<tr>';
                        for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                            $row_html .= '<td style="border: 1px solid black;" align="left"> ' . $j . ') ' . $equipment_group_sets[$i][$equipment_set_name][$j - 1] . '</td>';
                        }
                        $row_html .= '</tr>';
                    }

                    $row_html .= '<tr>';
                    for ($i = 1; $i <= $ssr->number_gangs; $i++) {
                        $row_html .= '<td style="border: 1px solid black;" align="center">&nbsp;</td>';
                    }
                    $row_html .= '</tr>';

                    $equipment_rows[] = $row_html;
                }
                $equipment_rows = implode("\n", $equipment_rows);

                $tbl = <<<EOD
        &nbsp;<br />&nbsp;<br /><table cellspacing="0" cellpadding="1" style="border: 1px solid black;" width="100%">
<tr>$gangs<td style="border: 1px solid black;" align="center"><strong>REMARKS</strong></td>'
</tr>
$worker_rows
$equipment_rows
</table>
EOD;

                $pdf->writeHTML($tbl);
            }

            $filename = 'DP' . $ssr->vessel_visit_id . '-' . $date . '.pdf';

            $pdf->Output($filename, 'I');

            die;

            if (file_exists(realpath("storage") . '/' . $filename)) {

                $this->db->set("vessel_visit_id", $vessel_visit_id);
                $this->db->set("shift", $shift);
                $this->db->set("company_id", $company_id);
                $this->db->set("operation_date", $date);
                $this->db->set("filename", $filename);
                if ($this->db->insert("service_vouchers")) {
                    $this->logs->add("vessel_visits", $vessel_visit_id, "SV_GENERATED", "Date " . $date . " shift " . $shift);
                    return true;
                }

            }
        }
    }

}
