<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Finance extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in() || !$this->user_model->has_perm("list_finance_documents")) {
            die(redirect("/order_summary?error=No permission to view this content."));
        }
    }

    public function service_vouchers()
    {
        if ($this->user_model->has_perm("list_service_vouchers")) {

            $vessel_visits = $this->db->select("DISTINCT(vessel_visit_workers.vessel_visit_id), vessel_visit_workers.shift, vessel_visit_workers.operation_date, vessel_name")->join("vessel_visits", "vessel_visits.vessel_visit_id = vessel_visit_workers.vessel_visit_id", "left")->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id", "left")->where("vessel_visit_workers.operation_date <  CURDATE() and vessel_visit_workers.operation_date > DATE_ADD(CURDATE(), INTERVAL -30 DAY)")->get_where("vessel_visit_workers")->result();

            $sv_queue = [];

            foreach ($vessel_visits as $vessel_visit) {

                $companies = $this->db->select("distinct(service_requests.company_id), company_name")->join("companies", "companies.company_id = service_requests.company_id")->get_where("service_requests", ["service_requests.deleted" => 0, "service_requests.vessel_visit_id" => $vessel_visit->vessel_visit_id])->result();

                foreach ($companies as $company) {
                    if (!$this->db->get_where("service_vouchers", ["service_vouchers.vessel_visit_id" => $vessel_visit->vessel_visit_id, "service_vouchers.shift" => $vessel_visit->shift, "service_vouchers.company_id" => $company->company_id, "service_vouchers.operation_date" => $vessel_visit->operation_date])->result()) {

                        $arr = $vessel_visit;
                        $arr->company = $company->company_id;
                        $arr->company_name = $company->company_name;
                        $sv_queue[] = $arr;
                    }
                }
            }

            $this->load->view('header', ['title' => "Service vouchers"]);
            $this->load->view('service-vouchers', ["queue" => $sv_queue]);
            $this->load->view('footer', ['scripts' => ['design/js/service-vouchers.js']]);
        }
    }

    public function invoices()
    {
        if ($this->user_model->has_perm("list_invoices")) {

            $pending = $this->db->join("companies", "companies.company_id = service_vouchers.company_id")->join("vessel_visits", "vessel_visits.vessel_visit_id = service_vouchers.vessel_visit_id")->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id")->where("service_vouchers.t_created > DATE_ADD(CURDATE(), INTERVAL -90 DAY)")->get_where("service_vouchers", ["service_vouchers.invoice_id" => null])->result();

            $pending_svs = [];
            foreach ($pending as $sv) {
                $pending_svs[$sv->company_id][] = $sv;
            }

            $this->load->view('header', ['title' => "Invoices", "styles" => ["design/css/multi-select.css"]]);
            $this->load->view('invoices', ["pending" => $pending_svs]);
            $this->load->view('footer', ['scripts' => ['design/js/jquery.multi-select.js', 'design/js/invoices.js']]);
        }
    }

    public function generate_service_voucher()
    {
        if ($this->user_model->has_perm("generate_service_vouchers") && $this->input->get("vessel_visit_id") && $this->input->get("shift") && $this->input->get("date")) {
            if (!$this->db->get_where("service_vouchers", ["service_vouchers.vessel_visit_id" => $this->steve->id_decode($this->input->get("vessel_visit_id")), "service_vouchers.shift" => $this->input->get("shift"), "service_vouchers.company_id" => $this->steve->id_decode($this->input->get("company")), "service_vouchers.operation_date" => $this->input->get("date")])->result()) {
                $this->documents->generate_service_voucher_pdf($this->steve->id_decode($this->input->get("vessel_visit_id")), $this->input->get("shift"), $this->input->get("date"), $this->steve->id_decode($this->input->get("company")));
                die(redirect("/finance/service_vouchers?message=Service voucher generated."));
            } else {
                die(redirect("/finance/service_vouchers?error=Service voucher already generated."));
            }
        } else {
            die(redirect("/finance/service_vouchers?error=No permission to generate service vouchers."));
        }
    }

    public function generate_invoice_from_sv()
    {
        // Renaming generate_invoice to generate_invoice_from_sv. Not using any more.
        if ($this->user_model->has_perm("generate_invoices")) {
            if ($this->input->post("service_vouchers") && count($this->input->post("service_vouchers"))) {
                $ssrs = $this->db->join("service_requests", "service_requests.vessel_visit_id = service_vouchers.vessel_visit_id")->join("vessel_visits", "vessel_visits.vessel_visit_id = service_vouchers.vessel_visit_id")->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id")->join("companies", "companies.company_id = service_requests.company_id", "left")->where_in("service_vouchers.service_voucher_id", $this->input->post("service_vouchers"))->where("service_vouchers.company_id = service_requests.company_id")->get_where("service_vouchers", ["service_requests.not_chargeable" => 0])->result();

                $this->documents->generate_invoice_pdf($ssrs, $this->input->post("service_vouchers"), $ssrs[0]->company_id);

                die(redirect("/finance/invoices?message=Invoice generated."));
            }
        } else {
            die(redirect("/finance/invoices?error=No permission to generate invoices."));
        }
    }

    public function payroll_spreadsheets()
    {
        if ($this->user_model->has_perm("list_payroll_list")) {
            $worker_groups = $this->db->where("payroll_start is not NULL")->get_where('worker_groups', ["active" => 1])->result();
            $worker_teams = $this->db->where("payroll_start is NULL")->get_where('worker_groups', ["active" => 1])->result();

            $this->load->view('header', ['title' => "Payroll spreadsheets"]);
            $this->load->view('payroll-spreadsheets', ["worker_groups" => $worker_groups, "worker_teams" => $worker_teams]);
            $this->load->view('footer', ['scripts' => ['design/js/payroll.js']]);
        }
    }

    public function payroll()
    {
        if ($this->user_model->has_perm("list_payroll_list")) {
            $worker_groups = $this->db->get_where('worker_groups', ["active" => 1])->result();
            $worker_groups_operations = $this->steve->worker_groups_operations();
            $this->load->view('header', ['title' => "Payroll", 'title2' => "Payroll Dashboard", "styles" => ["design/vendor/daterangepicker/daterangepicker.css"]]);
            $this->load->view('payroll', ["worker_groups" => $worker_groups, "worker_groups_operations" => $worker_groups_operations]);
            $this->load->view('footer', ['scripts' => ["design/vendor/moment.js-2.24.0/moment.min.js", "design/vendor/countUp.min.js", "design/vendor/daterangepicker/daterangepicker.js", 'design/js/payroll.js']]);
        }
    }

    public function payroll_ajax()
    {
        if (!$_POST['worker_group_id']) {
            $worked_days = $this->db->select("SUM(pay_hours) as pay_hours, SUM(lop_hours) as lop_hours, SUM(ot_hours) as ot_hours, SUM(pay_amount) as pay_amount, SUM(lop_amount) as lop_amount, SUM(ot_amount) as ot_amount")->where("availability_date >= '" . $_POST['start'] . "'")->where("availability_date <= '" . $_POST['end'] . "'")->get_where("worker_availability", ["attendance_processed" => 1])->result();
        } else {
            $worked_days = $this->db->select("SUM(pay_hours) as pay_hours, SUM(lop_hours) as lop_hours, SUM(ot_hours) as ot_hours, SUM(pay_amount) as pay_amount, SUM(lop_amount) as lop_amount, SUM(ot_amount) as ot_amount")->where("availability_date >= '" . $_POST['start'] . "'")->where("availability_date <= '" . $_POST['end'] . "'")->where("worker_group = '" . $_POST['worker_group_id'] . "'")->get_where("worker_availability", ["attendance_processed" => 1])->result();
        }
        if ($worked_days) {
            die(json_encode($worked_days[0]));
        }
    }

    public function service_vouchers_ajax_list()
    {
        $search = [];

        if ($_SESSION['user']->company_id) {
            $search[] = ["service_vouchers.company_id", $_SESSION['user']->company_id];
        }

        if ($this->input->post("vessel_visit")) {
            $search[] = ["service_vouchers.vessel_visit_id", $this->input->post("vessel_visit")];
        }
        die($this->steve->datatables_mysql("service_vouchers", ["operation_date", "filename", "visit_scn", "remarks", "company_name", "vessel_name"], $search, [["vessel_visits", "vessel_visits.vessel_visit_id = service_vouchers.vessel_visit_id"], ["companies", "companies.company_id = service_vouchers.company_id"], ["vessels", "vessels.vessel_id = vessel_visits.vessel_id"]], "visit_scn, vessel_visits.vessel_visit_id, shift, filename, operation_date, vessel_name, remarks, service_voucher_id, company_name, service_vouchers.t_created"));
    }

    public function delete_service_voucher()
    {
        if ($this->user_model->has_perm("delete_service_vouchers") && $this->input->get('id')) {
            $this->db->where("service_voucher_id", intval($this->input->get('id')));
            if ($this->db->delete("service_vouchers")) {
                redirect("finance/service_vouchers?message=Service voucher was deleted successfully.");
            } else {
                redirect("finance/service_vouchers?error=Service voucher deletion failed.");
            }
        } else {
            redirect("finance/service_vouchers?error=No service voucher or ID is blank");
        }
    }

    public function delete_invoice()
    {
        if ($this->user_model->has_perm("delete_invoices") && $this->input->get('id')) {
            $this->db->where("invoice_id", intval($this->input->get('id')));
            if ($this->db->delete("invoices")) {

                $this->db->reset_query();
                $this->db->set("invoice_id", null);
                $this->db->where("invoice_id", intval($this->input->get('id')));
                $this->db->update("service_request_operations");

                $this->db->reset_query();
                $this->db->set("invoice_id", null);
                $this->db->where("invoice_id", intval($this->input->get('id')));
                $this->db->update("service_request_disposals");

                $this->db->reset_query();
                $this->db->set("tally_invoice_id", null);
                $this->db->where("tally_invoice_id", intval($this->input->get('id')));
                $this->db->update("service_request_operation_tally");

                $this->db->reset_query();
                $this->db->set("invoice_id", null);
                $this->db->where("invoice_id", intval($this->input->get('id')));
                $this->db->update("vessel_visit_workers");

                $this->db->reset_query();
                $this->db->set("invoice_id", null);
                $this->db->where("invoice_id", intval($this->input->get('id')));
                $this->db->update("vessel_visit_equipments");

                $this->db->reset_query();
                $this->db->set("invoice_id", null);
                $this->db->where("invoice_id", intval($this->input->get('id')));
                $this->db->update("vessel_visit_gears");

                $this->db->reset_query();
                $this->db->set("work_meal_invoice_id", null);
                $this->db->where("work_meal_invoice_id", intval($this->input->get('id')));
                $this->db->update("service_requests");

                redirect("finance/invoices?message=Invoice was deleted successfully.");
            } else {
                redirect("finance/invoices?error=Invoice deletion failed.");
            }
        } else {
            redirect("finance/invoices?error=No invoice or ID is blank");
        }
    }

    public function invoices_ajax_list()
    {
        $search = [];

        if ($_SESSION['user']->company_id) {
            $search[] = ["invoices.company_id", $_SESSION['user']->company_id];
        }

        if ($this->input->post("vessel_visit")) {
            $search[] = ["vessel_visit_id", $this->input->post("vessel_visit")];
        }
        die($this->steve->datatables_mysql("invoices", ["invoice_number", "filename"], $search, [["companies", "companies.company_id = invoices.company_id"]], "invoice_id, company_name, invoice_number, filename, value, t_created"));
    }

    public function detailed_payroll_spreadsheet()
    {

        if ($this->input->get("team") && $this->user_model->has_perm("download_payroll")) {

            $worker_team_id = $this->input->get("team");
            $start = $this->input->get("start");

            $end = date("Y-m-d", strtotime($start . " +1 months -1 days"));
            // echo $this->db->last_query(); exit();    
            $workerids = [];
            $workers = $this->db->select("distinct(worker_id)")
                ->where("worker_availability.availability_date >= '" . $start . "'")
                ->where("worker_availability.work_end IS NOT NULL")
                ->where("worker_availability.availability_date <= '" . $end . "'")
                ->get_where('worker_availability', ["deleted" => 0, "worker_availability.worker_group" => $this->input->get("team")])
                ->result();
            foreach ($workers as $worker) {

                // echo $this->db->last_query(); exit();
                $workerids[] = $worker->worker_id;
            }

            if (count($workerids)) {
                $this->db->reset_query();
                $workers = $this->db->order_by("worker_type", "asc")
                    ->order_by("worker_name", "asc")
                    ->order_by("tenure_date", "desc")
                    ->select("*, workers.worker_id as worker_id_main")
                    ->join("worker_group", "worker_group.worker_id = workers.worker_id", "left")
                    ->join("worker_groups", "worker_groups.worker_group_id =$worker_team_id", "left")
                    ->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")
                    ->join("worker_tenure", "worker_tenure.worker_id = workers.worker_id", "left")
                    ->where("workers.worker_id IS NOT NULL")
                    ->where("(tenure_action IS NULL or tenure_action = 'joined')")
                    ->group_by("workers.worker_id")
                    ->where_in("workers.worker_id", $workerids)->get('workers')

                    ->result();
                //  echo $this->db->last_query();
                // die();
                $payroll_group = $this->db->select("payroll_start")
                    ->join("worker_groups", "worker_groups.worker_group_id = worker_group.worker_group_id")
                    ->where("worker_groups.payroll_start IS NOT NULL")
                    ->where("worker_group.worker_id=$worker->worker_id")
                    ->get('worker_group')
                    ->result()[0];

                $worker_start = date("Y-m-" . $payroll_group->payroll_start, strtotime($start));
                $worker_end = date("Y-m-d", strtotime($worker_start . " +1 months -1 days"));


                $worked_days = $this->db->where("availability_date >= '" . $worker_start . "'")->where("availability_date <= '" . $worker_end . "'")->where("worker_group = '" . $worker_team_id . "'")->get_where("worker_availability", ["attendance_processed" => 1])->result();
                // $worked_days = $this->db->where("availability_date >= '" . $start . "'")->where("availability_date <= '" . $end . "'")->get_where("worker_availability", ["attendance_processed" => 1])->result();

                //echo $this->db->last_query(); exit();

                $workers_attendance = [];
                $daily_attendance = [];
                $worker_pays = [];

                foreach ($worked_days as $day) {
                    $workers_attendance[$day->worker_id][$day->availability_date] = $day;
                    $daily_attendance[$day->availability_date][$day->worker_id] = $day;
                }

                $spreadsheet = new Spreadsheet();

                $spreadsheet->getProperties()
                    ->setCreator("Steve")
                    ->setLastModifiedBy("Steve")
                    ->setTitle("Steve payroll")
                    ->setSubject("Steve")
                    ->setDescription(
                        "Payroll report"
                    )
                    ->setKeywords("Detaailed payroll report")
                    ->setCategory("Reports");

                $spreadsheet->removeSheetByIndex(0);
                $r = 1;
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Workers'));

                $sheet = $spreadsheet->getSheetByName('Workers');
                //$start_month = strtotime(date("Y-m-1") . " -" .  " months");
                // $start_month = date_format($start, 'm');
                $start_month = date("F", strtotime($start));
                // echo $start_month;die();
                $sheet->setCellValue('B1', 'ATTENDANCE - ' . $start_month);

                $sheet->setCellValue('B2', 'IC Number');
                $sheet->setCellValue('C2', 'Name');
                $sheet->setCellValue('D2', 'Group');
                $sheet->setCellValue('E2', 'Type');
                $sheet->setCellValue('F2', 'Skill');
                $sheet->setCellValue('G2', 'Date joined');

                $sheet->setCellValue('H2', 'Pay days');
                $sheet->setCellValue('I2', 'Standby days');
                $sheet->setCellValue('J2', 'Leave days');
                $sheet->setCellValue('K2', 'Rest days');
                $sheet->setCellValue('L2', 'Public holidays');
                $sheet->setCellValue('M2', 'OT hours');
                $sheet->setCellValue('N2', 'WTM');
                $sheet->setCellValue('O2', 'Pay hours');
                $sheet->setCellValue('P2', 'Basic pay');
                $sheet->setCellValue('Q2', 'Overtime');
                $sheet->setCellValue('R2', 'Pay loss');
                $sheet->setCellValue('S2', 'Total');

                $sheet->mergeCells('B1:S1');
                $sheet->getStyle('B1:S1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $alphabets = range('A', 'Z');

                $workers_done = [];

                $id = 2;

                $workers_info = [];

                foreach ($workers as $worker) {
                    if (!$workers_done[$worker->worker_id_main]) {
                        $id++;
                        $sheet->setCellValue('B' . $id, $worker->ic_number);
                        $sheet->setCellValue('C' . $id, $worker->worker_name);
                        $sheet->getCell('B' . $id)->getHyperlink()->setUrl("sheet://'" . substr($worker->worker_name, 0, 31) . "'!A1");
                        $sheet->getCell('C' . $id)->getHyperlink()->setUrl("sheet://'" . substr($worker->worker_name, 0, 31) . "'!A1");
                        $sheet->setCellValue('D' . $id, $worker->worker_group_code);
                        $sheet->setCellValue('E' . $id, strtoupper(str_replace("-", " ", $worker->worker_type)));
                        $sheet->setCellValue('F' . $id, $worker->resource_type_short_code);
                        $sheet->setCellValue('G' . $id, $worker->tenure_date);

                        $workers_done[$worker->worker_id_main] = [$id, $worker->worker_type];

                        $workers_info[$worker->worker_id_main] = $worker;
                        $worker_pays[$worker->worker_id_main] = ["standby_days" => 0, "publicholidays" => 0, "pay_days" => 0, "leave_days" => 0, "rest_days" => 0, "work_through_meals" => 0, "pay_hours" => 0, "lop_hours" => 0, "ot_hours" => 0, "pay_amount" => 0, "lop_amount" => 0, "ot_amount" => 0];
                    }
                }

                $end_date = new DateTime($end);
                for ($i = 0; $i <= $end_date->diff(new DateTime($start))->format("%a"); $i++) {
                    $currentDate = date("Y-m-d", strtotime($start . " + " . $i . " days"));
                    $is_public_holiday = $this->db->get_where("public_holidays", ["public_holiday_date" => $currentDate, "active" => 1])->num_rows();
                    // log_message('error', $this->db->last_query());
                    //$is_public_holiday = $is_public_holiday++;
                    // log_message('error', $is_public_holiday);
                    foreach ($workers_done as $worker_id_main => $data) {
                        $row = $data[0];
                        $worker_type = $data[1];
                        //log_message('error', $data[1]);
                        if ($daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main]) {
                            // log_message("Error", "test :".json_encode($daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main]));
                            $day = $daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main];
                            if ($is_public_holiday && $worker_type == 'contract-monthly') {
                                $worker_pays[$worker_id_main]["publicholidays"]++;
                                // log_message("error", "publicholidays days: ". $worker_pays[$worker_id_main]['publicholidays']);

                            }
                            if ($day->worker_attendance != 'P' && $day->worker_attendance != 'RD') {

                                $worker_pays[$worker_id_main]["leave_days"]++;
                            } else {
                                if ($day->work_standby) {
                                    $worker_pays[$worker_id_main]["standby_days"]++;
                                } else {
                                    $worker_pays[$worker_id_main]["pay_days"]++;
                                }
                                if (!$day->work_standby) {
                                    $worker_pays[$worker_id_main]["work_through_meals"]++;
                                }
                                if ($day->worker_attendance != 'P' && $day->worker_attendance = 'RD') {
                                    $worker_pays[$worker_id_main]["rest_days"]++;
                                }

                                $worker_pays[$worker_id_main]["pay_hours"] += $day->pay_hours;
                                $worker_pays[$worker_id_main]["lop_hours"] += $day->lop_hours;
                                $worker_pays[$worker_id_main]["ot_hours"] += $day->ot_hours;
                                $worker_pays[$worker_id_main]["pay_amount"] += $day->pay_amount;
                                $worker_pays[$worker_id_main]["lop_amount"] += $day->lop_amount;
                                $worker_pays[$worker_id_main]["ot_amount"] += $day->ot_amount;
                            }
                        } else {
                            if ($is_public_holiday && $worker_type == "contract-monthly") {
                                $worker_pays[$worker_id_main]["publicholidays"]++;
                            }
                        }
                    }
                }

                $sheet->getStyle('B1:R2')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                $sheet->getStyle("B1:S" . $row)->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

                foreach (range("B", "S") as $alphabet) {
                    $sheet->getColumnDimension($alphabet)->setAutoSize(true);
                }

                $id = 2;

                foreach ($workers_done as $worker_id_main => $row) {

                    $id++;
                    $sheet->setCellValue('H' . $id, $worker_pays[$worker_id_main]['pay_days']);
                    $sheet->setCellValue('I' . $id, $worker_pays[$worker_id_main]['standby_days']);
                    $sheet->setCellValue('J' . $id, $worker_pays[$worker_id_main]['leave_days']);
                    $sheet->setCellValue('K' . $id, $worker_pays[$worker_id_main]['rest_days']);
                    $sheet->setCellValue('L' . $id, $worker_pays[$worker_id_main]['publicholidays']);
                    $sheet->setCellValue('M' . $id, $worker_pays[$worker_id_main]['ot_hours']);
                    $sheet->setCellValue('N' . $id, $worker_pays[$worker_id_main]['work_through_meals']);

                    $sheet->setCellValue('O' . $id, $worker_pays[$worker_id_main]['pay_hours']);
                    $sheet->setCellValue('P' . $id, $worker_pays[$worker_id_main]['pay_amount']);
                    $sheet->setCellValue('Q' . $id, $worker_pays[$worker_id_main]['ot_amount']);
                    $sheet->setCellValue('R' . $id, $worker_pays[$worker_id_main]['lop_amount']);
                    $sheet->setCellValue('S' . $id, $worker_pays[$worker_id_main]['ot_amount'] + $worker_pays[$worker_id_main]['pay_amount'] - $worker_pays[$worker_id_main]['lop_amount']);

                    if (!$summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code] = ['total' => 0, 'working' => 0, 'standby' => 0, 'total_ops' => 0, 'total_pay' => 0, 'total_ot' => 0];
                    }

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total']++;

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['working'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['standby'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $worker_pays[$worker_id_main]['pay_amount'];

                    if ($workers_info[$worker_id_main]->extra_allowance) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $workers_info[$worker_id_main]->extra_allowance;
                    }

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ot'] += $worker_pays[$worker_id_main]['ot_hours'];
                }

                $spreadsheet->getActiveSheet()->setAutoFilter('B2:S' . $id);
                foreach (range('B', 'S') as $a) {
                    $sheet->getColumnDimension($a)->setAutoSize(true);
                }

                foreach ($workers as $worker) {
                    if ($workers_done[$worker->worker_id_main]) {
                        $payroll_group = $this->db->select("payroll_start")
                            ->join("worker_groups", "worker_groups.worker_group_id = worker_group.worker_group_id")
                            ->where("worker_groups.payroll_start IS NOT NULL")
                            ->where("worker_group.worker_id=$worker->worker_id_main")
                            ->get('worker_group')
                            ->result()[0];
                        //echo $this->db->last_query(); exit();


                        $worker_start = date("Y-m-" . $payroll_group->payroll_start, strtotime($start));
                        $worker_end = date("Y-m-d", strtotime($worker_start . " +1 months -1 days"));
                        $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, substr($worker->worker_name, 0, 31)));

                        $sheet = $spreadsheet->getSheetByName(substr($worker->worker_name, 0, 31));

                        $sheet->setCellValue('B1', 'ATTENDANCE - ' . $worker->worker_name . " - " . $worker_start . " - " . $worker_end);

                        $sheet->setCellValue('B2', 'Date');
                        $sheet->setCellValue('C2', 'Attendance');
                        $sheet->setCellValue('D2', 'WD or WE');

                        $sheet->setCellValue('E2', 'Start');
                        $sheet->setCellValue('F2', 'End');
                        $sheet->setCellValue('G2', 'Standby?');
                        $sheet->setCellValue('H2', 'Vesselname');

                        $sheet->setCellValue('I2', 'OT hours');
                        $sheet->setCellValue('J2', 'WTM');
                        $sheet->setCellValue('K2', 'Pay hours');
                        $sheet->setCellValue('L2', 'Basic pay');
                        $sheet->setCellValue('M2', 'OT & PH pay');
                        $sheet->setCellValue('N2', 'RD pay');
                        $sheet->setCellValue('O2', 'WTM pay');
                        $sheet->setCellValue('P2', 'Pay loss');
                        $sheet->setCellValue('Q2', 'Total');
                        $sheet->setCellValue('R2', 'Remarks');
                        $row = 2;
                        log_message("error", "start work: " . $worker_start . "end work :" . $worker_end);
                        log_message("error", "worked id:" . $worker->worker_id_main);

                        for ($i = 0; $i <= (new DateTime($worker_end))->diff(new DateTime($worker_start))->format("%a"); $i++) {
                            $row++;

                            $sheet->setCellValue("B" . $row, date("Y-m-d", strtotime($worker_start . " + " . $i . " days")));

                            if ($daily_attendance[date("Y-m-d", strtotime($worker_start . " + " . $i . " days"))][$worker->worker_id_main]) {
                                $day = $daily_attendance[date("Y-m-d", strtotime($worker_start . " + " . $i . " days"))][$worker->worker_id_main];

                                $sheet->setCellValue("C" . $row, $day->worker_attendance);
                                $date =  date("Y-m-d", strtotime($worker_start . " + " . $i . " days"));
                                $dayName = (date('l', strtotime($date)));
                                log_message("error", "date:" . $date . "dayname: " . $dayName);
                                // echo $dayName ; exit();
                                //if(($dayName) == "6" || ($dayName) == "0") 

                                $sheet->setCellValue("D" . $row, $dayName);
                                //else
                                //  $sheet->setCellValue("D" . $row,"WD" );

                                $sheet->setCellValue("E" . $row, date("H:i", strtotime($day->work_start)));
                                $sheet->setCellValue("F" . $row, date("H:i", strtotime($day->work_end)));

                                $sheet->setCellValue("G" . $row, $day->work_standby ? "Y" : "N");
                                $sheet->setCellValue("H" . $row, $day->vessel_name);

                                $sheet->setCellValue("I" . $row, $day->ot_hours);

                                $sheet->setCellValue("J" . $row, $day->work_through_meals);
                                $sheet->setCellValue("K" . $row, $day->pay_hours);
                                $sheet->setCellValue("L" . $row, $day->pay_amount);
                                $sheet->setCellValue("M" . $row, $day->ot_amount + $day->ph_pay + $day->ph_ot);
                                $sheet->setCellValue("N" . $row, $day->rd_pay + $day->rd_ot);
                                $sheet->setCellValue("O" . $row, $day->work_through_meals_pay);
                                $sheet->setCellValue("P" . $row, $day->lop_amount);
                                $sheet->setCellValue("Q" . $row, $day->ot_amount + $day->ph_pay + $day->ph_ot + $day->pay_amount + $day->rd_pay + $day->rd_ot - $day->lop_amount);
                                $sheet->setCellValue("R" . $row, $day->remarks);
                            }
                        }

                        $sheet->getStyle('B1:R' . $row)->applyFromArray([
                            'borders' => [
                                'outline' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                                ],
                                'inside' => [
                                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                                ],
                            ],
                        ]);

                        $sheet->mergeCells('B1:R1');
                        $sheet->getStyle('B1:R1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        foreach (range('B', 'R') as $a) {
                            $sheet->getColumnDimension($a)->setAutoSize(true);
                        }
                    }
                }

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

                header('Content-Disposition: attachment;filename="Steve - Detailed payroll - ' . $worker_team_id . " - " . $start . " - " . $end . '.xlsx"');

                header('Cache-Control: max-age=0');
                $writer->save('php://output');

                die;
            } else {
                die("No attendance found for this month.");
            }
        } else {
            die("Spreadsheet export failed. Please try a different time period.");
        }
    }

    public function payroll_spreadsheet()
    {

        if ($this->input->get("start") && $this->input->get("group") && $this->user_model->has_perm("download_payroll")) {

            $start = $this->input->get("start");
            $end = date("Y-m-d", strtotime($start . " +1 months -1 days"));

            $workers = $this->db->select("distinct(worker_id)")
                ->where("worker_availability.availability_date >= '" . $start . "'")
                ->where("worker_availability.work_end IS NOT NULL")
                ->where("worker_availability.availability_date <= '" . $end . "'")
                ->get_where('worker_availability', ["deleted" => 0, "worker_availability.worker_group" => $this->input->get("group")])
                ->result();
            $workerids = [];
            foreach ($workers as $worker) {
                $workerids[] = $worker->worker_id;
            }

            if (!$workerids) {
                foreach ($this->db->select("distinct(worker_availability.worker_id)")->join("worker_group", "worker_group.worker_id = worker_availability.worker_id")->where("worker_availability.availability_date >= '" . $start . "'")->where("worker_availability.work_end IS NOT NULL")->where("worker_availability.availability_date <= '" . $end . "'")->get_where('worker_availability', ["deleted" => 0, "worker_group.worker_group_id" => $this->input->get("group")])->result() as $worker) {
                    $workerids[] = $worker->worker_id;
                }
            }

            if ($workerids) {
                $workers = $this->db->order_by("worker_type", "asc")
                    ->order_by("tenure_date", "desc")
                    ->select("*, workers.worker_id as worker_id_main")
                    ->join("worker_group", "worker_group.worker_id = workers.worker_id", "left")
                    ->join("worker_groups", "worker_groups.worker_group_id = worker_group.worker_group_id", "left")
                    ->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")
                    ->join("resource_type_rates", "resource_type_rates.resource_type_id = workers.worker_resource_type and resource_type_rates.employment_type = workers.worker_type", "left")
                    ->join("worker_tenure", "worker_tenure.worker_id = workers.worker_id", "left")
                    ->where("workers.worker_id IS NOT NULL")
                    ->where("(tenure_action IS NULL or tenure_action = 'joined')")
                    ->where_in("workers.worker_id", $workerids)
                    ->get('workers')->result();
                $worked_days = $this->db->where("availability_date >= '" . $worker->payment_effective . "'")
                    ->where("availability_date >= '" . $start . "'")
                    ->where("availability_date <= '" . $end . "'")
                    ->where("availability_date <= '" . $end . "'")
                    ->where_in("worker_availability.worker_id", $workerids)
                    ->get_where("worker_availability", ["attendance_processed" => 1])
                    ->result();

                $group = $this->db->get_where("worker_groups", ["worker_group_id" => $this->input->get("group")])->result();

                $workers_attendance = [];
                $daily_attendance = [];
                $worker_pays = [];

                foreach ($worked_days as $day) {
                    $workers_attendance[$day->worker_id][$day->availability_date] = $day;
                    $daily_attendance[$day->availability_date][$day->worker_id] = $day;
                }

                $spreadsheet = new Spreadsheet();

                $spreadsheet->getProperties()
                    ->setCreator("Steve")
                    ->setLastModifiedBy("Steve")
                    ->setTitle("Steve payroll")
                    ->setSubject("Steve")
                    ->setDescription(
                        "Payroll report"
                    )
                    ->setKeywords("Payroll report")
                    ->setCategory("Reports");

                $spreadsheet->removeSheetByIndex(0);
                $r = 1;
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Attendance'));
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Payroll'));
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Summary'));

                $sheet = $spreadsheet->getSheetByName('Attendance');

                $sheet->setCellValue('B1', 'ATTENDANCE - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->setCellValue('B2', 'Basic details');
                $sheet->setCellValue('H2', 'Date - Day & month');

                $sheet->mergeCells('B2:G2');

                $sheet->setCellValue('B3', 'IC Number');
                $sheet->setCellValue('C3', 'Name');
                $sheet->setCellValue('D3', 'Group');
                $sheet->setCellValue('E3', 'Type');
                $sheet->setCellValue('F3', 'Skill');
                $sheet->setCellValue('G3', 'Date joined');

                $alphabets = range('A', 'Z');

                $workers_done = [];

                $id = 3;

                $workers_info = [];

                foreach ($workers as $worker) {
                    if (!$workers_done[$worker->worker_id_main]) {
                        $id++;
                        $sheet->setCellValue('B' . $id, $worker->ic_number);
                        $sheet->setCellValue('C' . $id, $worker->worker_name);
                        $sheet->setCellValue('D' . $id, $worker->worker_group_code);
                        $sheet->setCellValue('E' . $id, strtoupper(str_replace("-", " ", $worker->worker_type)));
                        $sheet->setCellValue('F' . $id, $worker->resource_type_short_code);
                        $sheet->setCellValue('G' . $id, $worker->tenure_date);

                        $workers_done[$worker->worker_id_main] = [$id, $worker->worker_type];

                        $sheet->mergeCells('B' . $id . ':B' . ($id + 1));
                        $sheet->mergeCells('C' . $id . ':C' . ($id + 1));
                        $sheet->mergeCells('D' . $id . ':D' . ($id + 1));
                        $sheet->mergeCells('E' . $id . ':E' . ($id + 1));
                        $sheet->mergeCells('F' . $id . ':F' . ($id + 1));
                        $sheet->mergeCells('G' . $id . ':G' . ($id + 1));

                        $id++;

                        if ($worker->monthly_allowance) {
                            $worker_allowance = $this->db->get_where("worker_allowances", ["worker_id" => $worker->worker_id_main, "month" => date("Y-m", strtotime($start))])->result();
                            if ($worker_allowance) {
                                $worker->extra_allowance = $worker_allowance[0]->allowance_amount;
                            } else {
                                $worker->extra_allowance = $worker->monthly_allowance;
                                $this->db->reset_query();
                                $this->db->set("worker_id", $worker->worker_id_main);
                                $this->db->set("allowance_amount", $worker->monthly_allowance);
                                $this->db->set("month", date("Y-m", strtotime($start)));
                                $this->db->insert("worker_allowances");
                            }
                        }

                        $workers_info[$worker->worker_id_main] = $worker;
                        $worker_pays[$worker->worker_id_main] = ["ph_ot" => 0, "ph_pay" => 0, "rd_pay" => 0, "rd_ot" => 0, "work_through_meals" => 0, "publicholidays" => 0, "restdays" => 0, "standby_days" => 0, "pay_days" => 0, "pay_hours" => 0, "lop_hours" => 0, "ot_hours" => 0, "pay_amount" => 0, "lop_amount" => 0, "ot_amount" => 0, "worked_rest_days" => 0, "worked_public_holidays" => 0, "worked_work_through_meals" => 0];
                    }
                }

                $last_alphabet = 'A';

                $end_date = new DateTime($end);
                // log_message("error", json_encode($workers_done));
                for ($i = 0; $i <= $end_date->diff(new DateTime($start))->format("%a"); $i++) {
                    $last_alphabet = ($i > 19 ? "A" . $alphabets[$i - 20] : $alphabets[$i + 6]);
                    $currentDate = date("Y-m-d", strtotime($start . " + " . $i . " days"));


                    $sheet->setCellValue($last_alphabet . '3', date("d-M", strtotime($start . " + " . $i . " days")));
                    $is_public_holiday = $this->db->get_where("public_holidays", ["public_holiday_date" => $currentDate, "active" => 1])->num_rows();

                    $sheet->getColumnDimension($last_alphabet)->setAutoSize(true);

                    foreach ($workers_done as $worker_id_main => $data) {
                        $row = $data[0];
                        $worker_type = $data[1];
                        if ($daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main]) {
                            $day = $daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main];

                            if ($day->work_start) {
                                $sheet->setCellValue($last_alphabet . $row, date("H:i", strtotime($day->work_start)));
                            } else {
                                $sheet->setCellValue($last_alphabet . $row, $day->worker_attendance);
                            }
                            if ($day->work_end) {
                                $sheet->setCellValue($last_alphabet . ($row + 1), date("H:i", strtotime($day->work_end)));
                            } else {
                                $sheet->setCellValue($last_alphabet . ($row + 1), $day->worker_attendance);
                            }

                            if ($day->worker_attendance != 'P' && ($day->work_start)) {
                                $worker_pays[$worker_id_main]["leave_days"]++;
                            }

                            $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

                            if ($day->worker_attendance == 'RD' && $worker_type == "contract-monthly") {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('ff0000');
                            } else if ($is_public_holiday && ($worker_type == 'contract-monthly')) {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('fff830');
                            } else {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setARGB($day->work_standby ? 'FF73FDFF' : 'FFF79646');
                            }

                            if ($day->worker_shift) {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFont()->getColor()->setARGB($day->worker_shift == 2 ? 'FF008F00' : 'FF941751');
                            }

                            if ($day->work_through_meals) {
                                if (!$day->work_standby) {
                                    $worker_pays[$worker_id_main]["worked_work_through_meals"]++;
                                }
                                $worker_pays[$worker_id_main]["work_through_meals"]++;
                                // $worker_pays[$worker_id_main]["ot_hours"] ++;
                            }

                            if ($worker_type == 'contract-monthly') {
                                if ($is_public_holiday) {
                                    if ($day->work_start && $day->work_end) {
                                        $worker_pays[$worker_id_main]["worked_public_holidays"]++;
                                    }
                                    $worker_pays[$worker_id_main]["publicholidays"]++;
                                } else if ($day->worker_attendance == 'RD') {
                                    if ($day->work_start && $day->work_end) {
                                        $worker_pays[$worker_id_main]["worked_rest_days"]++;
                                    }
                                    $worker_pays[$worker_id_main]["restdays"]++;
                                }
                            }

                            if ($day->work_standby) {
                                $worker_pays[$worker_id_main]["standby_days"]++;
                            }
                            //if(!$is_public_holiday && $day->worker_attendance != 'RD' && !$day->work_standby) {
                            else {
                                $worker_pays[$worker_id_main]["pay_days"]++;
                            }
                            $worker_pays[$worker_id_main]["pay_hours"] += $day->pay_hours;
                            $worker_pays[$worker_id_main]["lop_hours"] += $day->lop_hours;
                            $worker_pays[$worker_id_main]["ot_hours"] += $day->ot_hours;
                            $worker_pays[$worker_id_main]["pay_amount"] += $day->pay_amount;
                            $worker_pays[$worker_id_main]["lop_amount"] += $day->lop_amount;
                            $worker_pays[$worker_id_main]["ot_amount"] += $day->ot_amount;
                            $worker_pays[$worker_id_main]["ph_pay"] += $day->ph_pay;
                            $worker_pays[$worker_id_main]["ph_ot"] += $day->ph_ot;
                            $worker_pays[$worker_id_main]["rd_pay"] += $day->rd_pay;
                            $worker_pays[$worker_id_main]["rd_ot"] += $day->rd_ot;
                            $worker_pays[$worker_id_main]["work_through_meals_pay"] += $day->work_through_meals_pay;
                        } else {
                            if ($is_public_holiday && $worker_type == "contract-monthly") {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('fff830');
                                $sheet->setCellValue($last_alphabet . $row, "PH");
                                $sheet->setCellValue($last_alphabet . ($row + 1), "PH");
                            } else {
                                $sheet->setCellValue($last_alphabet . $row, "-");
                                $sheet->setCellValue($last_alphabet . ($row + 1), "-");
                            }
                        }
                    }
                }

                $sheet->getStyle('B2:' . $last_alphabet . '3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach ($workers_done as $worker_id_main => $data) {
                    $row = $data[0];
                    $sheet->getStyle('B' . $row . ":" . $last_alphabet . ($row + 1))->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                            'inside' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                            ],
                        ],
                    ]);
                }

                $sheet->getStyle("B" . ($row + 3) . ":B" . ($row + 4))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

                $sheet->setCellValue("B" . ($row + 3), "In Operation");
                $sheet->getStyle("B" . ($row + 3))->getFill()->getStartColor()->setARGB('FFF79646');

                $sheet->setCellValue("B" . ($row + 4), "On standby");
                $sheet->getStyle("B" . ($row + 4))->getFill()->getStartColor()->setARGB('FF73FDFF');

                $sheet->setCellValue("B" . ($row + 5), "Shift 1");
                $sheet->getStyle("B" . ($row + 5))->getFont()->getColor()->setARGB('FF941751');

                $sheet->setCellValue("B" . ($row + 6), "Shift 2");
                $sheet->getStyle("B" . ($row + 6))->getFont()->getColor()->setARGB('FF008F00');

                $sheet->getStyle("B" . ($row + 7))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->setCellValue("B" . ($row + 7), "Public Holiday");
                $sheet->getStyle("B" . ($row + 7))->getFill()->getStartColor()->setRGB('fff830');
                $sheet->getStyle("B" . ($row + 8))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->setCellValue("B" . ($row + 8), "RD");
                $sheet->getStyle("B" . ($row + 8))->getFill()->getStartColor()->setRGB('ff0000');

                foreach (range("B", "G") as $alphabet) {
                    $sheet->getColumnDimension($alphabet)->setAutoSize(true);
                }

                $sheet->mergeCells('H2:' . $last_alphabet . '2');
                $sheet->mergeCells('B1:' . $last_alphabet . '1');
                $sheet->getStyle('B1:' . $last_alphabet . '3')->getFont()->setBold(true);
                $sheet->getStyle('B1:' . $last_alphabet . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Payroll

                $sheet = $spreadsheet->getSheetByName('Payroll');

                $sheet->setCellValue('B1', 'PAYROLL - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->mergeCells('B1:P1');

                $sheet->setCellValue('B2', 'Basic details');

                $sheet->mergeCells('B2:F2');

                $sheet->setCellValue('B3', 'IC Number');
                $sheet->setCellValue('C3', 'Name');
                $sheet->setCellValue('D3', 'Group');
                $sheet->setCellValue('E3', 'Type');
                $sheet->setCellValue('F3', 'Skill');
                $sheet->setCellValue('G3', 'Date joined');

                $sheet->setCellValue('H3', 'Pay days');
                $sheet->setCellValue('I3', 'Standby days');
                $sheet->setCellValue('J3', 'Rest days');
                $sheet->setCellValue('K3', 'Public Holidays');

                $sheet->setCellValue('L3', 'Work Through Meals');
                $sheet->setCellValue('M3', 'Leave days');
                $sheet->setCellValue('N3', 'OT hours');
                $sheet->setCellValue('O3', 'Pay hours');
                $sheet->setCellValue('P3', 'Pay amount');
                $sheet->setCellValue('Q3', 'Public Holiday Pay');
                $sheet->setCellValue('R3', 'Public Holiday OT');
                $sheet->setCellValue('S3', 'Rest Day Pay');
                $sheet->setCellValue('T3', 'Rest Day Ot');
                $sheet->setCellValue('U3', 'Work Through Meals Pay');

                $sheet->setCellValue('V3', 'Overtime');
                $sheet->setCellValue('W3', 'Allowance');
                $sheet->setCellValue('X3', 'Pay loss');
                $sheet->setCellValue('Y3', 'Total');
                $sheet->getStyle("B1:Y3")->getFont()->setBold(true);
                $sheet->getStyle('B1:Y3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $sheet->getStyle('B2:Y3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach (range('B', 'T') as $a) {
                    $sheet->getColumnDimension($a)->setAutoSize(true);
                }

                $summary = [];

                $id = 3;


                foreach ($workers_done as $worker_id_main => $row) {
                    log_message("error", "================================================");
                    $id++;
                    $sheet->setCellValue('B' . $id, $workers_info[$worker_id_main]->ic_number);
                    $sheet->setCellValue('C' . $id, $workers_info[$worker_id_main]->worker_name);
                    $sheet->setCellValue('D' . $id, $workers_info[$worker_id_main]->worker_group_code);
                    $sheet->setCellValue('E' . $id, strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type)));
                    $sheet->setCellValue('F' . $id, $workers_info[$worker_id_main]->resource_type_short_code);
                    $sheet->setCellValue('G' . $id, $workers_info[$worker_id_main]->tenure_date);

                    $sheet->setCellValue('H' . $id, $worker_pays[$worker_id_main]['pay_days']);
                    $sheet->setCellValue('I' . $id, $worker_pays[$worker_id_main]['standby_days']);
                    $sheet->setCellValue('J' . $id, $worker_pays[$worker_id_main]['restdays']);
                    $sheet->setCellValue('K' . $id, $worker_pays[$worker_id_main]['publicholidays']);
                    $sheet->setCellValue('L' . $id, $worker_pays[$worker_id_main]['worked_work_through_meals']);
                    $sheet->setCellValue('M' . $id, $worker_pays[$worker_id_main]['leave_days']);
                    $sheet->setCellValue('N' . $id, $worker_pays[$worker_id_main]['ot_hours']);
                    $sheet->setCellValue('O' . $id, $worker_pays[$worker_id_main]['pay_hours']);
                    if ($workers_info[$worker_id_main]->worker_type == "contract-monthly") {
                        //log_message("error", json_encode($workers_info[$worker_id_main]));
                        $work_rate = $workers_info[$worker_id_main]->work_rate;
                        if ($workers_info[$worker_id_main]->work_rate_override && $workers_info[$worker_id_main]->work_rate_override > 0) {
                            $work_rate = $workers_info[$worker_id_main]->work_rate_override;
                        }
                        $pay_amount = (float)$work_rate * 26;
                        /* log_message("error", "pay_amount: ". $pay_amount);
                        log_message("error", "rest days: ". $worker_pays[$worker_id_main]['worked_rest_days']);
                        log_message("error", "publicholidays days: ". $worker_pays[$worker_id_main]['publicholidays']);
                        if((int)$worker_pays[$worker_id_main]['worked_rest_days'] > 0)
                        {
                            log_message("error", "rs pay". ((int)$worker_pays[$worker_id_main]['worked_rest_days'] * (float)$work_rate * 2));
                            $pay_amount = $pay_amount + ((int)$worker_pays[$worker_id_main]['worked_rest_days'] * (float)$work_rate * 2);
                        }
                        log_message("error", "RS pay_amount: ". $pay_amount);
                        if((int)$worker_pays[$worker_id_main]['worked_public_holidays'] > 0)
                        {
                            $pay_amount = $pay_amount + ((int)$worker_pays[$worker_id_main]['worked_public_holidays'] * (float)$work_rate * 3);
                        }
                        log_message("error", "final pay_amount: ". $pay_amount);
                        */
                        $worker_pays[$worker_id_main]['pay_amount'] = $pay_amount;
                        $sheet->setCellValue('P' . $id, $pay_amount);
                        log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    } else {
                        $sheet->setCellValue('P' . $id, $worker_pays[$worker_id_main]['pay_amount']);
                        log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    }


                    $sheet->setCellValue('Q' . $id, $worker_pays[$worker_id_main]['ph_pay']);
                    $sheet->setCellValue('R' . $id, $worker_pays[$worker_id_main]['ph_ot']);
                    $sheet->setCellValue('S' . $id, $worker_pays[$worker_id_main]['rd_pay']);
                    $sheet->setCellValue('T' . $id, $worker_pays[$worker_id_main]['rd_ot']);
                    $sheet->setCellValue('U' . $id, $worker_pays[$worker_id_main]['work_through_meals_pay']);

                    $sheet->setCellValue('V' . $id, $worker_pays[$worker_id_main]['ot_amount']);



                    if (!$workers_info[$worker_id_main]->extra_allowance) {
                        $workers_info[$worker_id_main]->extra_allowance = 0;
                    }
                    $sheet->setCellValue('W' . $id, $workers_info[$worker_id_main]->extra_allowance);

                    $sheet->setCellValue('X' . $id, $worker_pays[$worker_id_main]['lop_amount']);

                    log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    log_message("error", "ot_amount" . $worker_pays[$worker_id_main]['ot_amount']);

                    log_message("error", "extra_allowance" . $workers_info[$worker_id_main]->extra_allowance);

                    log_message("error", "lop_amount" . $worker_pays[$worker_id_main]['lop_amount']);


                    log_message("error", "================================================");
                    $sheet->setCellValue('Y' . $id, $worker_pays[$worker_id_main]['pay_amount'] + $worker_pays[$worker_id_main]['ph_pay'] + $worker_pays[$worker_id_main]['ph_ot'] + $worker_pays[$worker_id_main]['rd_pay'] + $worker_pays[$worker_id_main]['rd_ot'] + $worker_pays[$worker_id_main]['work_through_meals_pay'] + $worker_pays[$worker_id_main]['ot_amount'] + $workers_info[$worker_id_main]->extra_allowance - $worker_pays[$worker_id_main]['lop_amount']);

                    if (!$summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code] = ['total' => 0, 'working' => 0, 'standby' => 0, 'total_ops' => 0, 'total_pay' => 0, 'total_ot' => 0];
                    }
                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total']++;

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['working'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['standby'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $worker_pays[$worker_id_main]['pay_amount'];

                    if ($workers_info[$worker_id_main]->extra_allowance) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $workers_info[$worker_id_main]->extra_allowance;
                    }

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ot'] += $worker_pays[$worker_id_main]['ot_hours'];
                }

                $sheet = $spreadsheet->getSheetByName('Summary');

                $sheet->setCellValue('B1', 'SUMMARY - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->mergeCells('B1:J1');

                $sheet->setCellValue('B2', 'Employment Type');

                $sheet->setCellValue('C2', 'Skill');
                $sheet->setCellValue('D2', 'Total staff');

                $sheet->setCellValue('E2', 'Total pay days');
                $sheet->setCellValue('F2', 'Total standby days');
                $sheet->setCellValue('G2', 'Total operations');
                $sheet->setCellValue('H2', 'Total pay (RM)');
                $sheet->setCellValue('I2', 'Total OT (hours)');

                $sheet->getStyle("B1:I2")->getFont()->setBold(true);
                $sheet->getStyle('B1:I2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('B2:I2')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach (range('B', 'I') as $a) {
                    $sheet->getColumnDimension($a)->setAutoSize(true);
                }
                $id = 2;

                foreach ($summary as $type => $resources) {
                    $id++;
                    $sheet->setCellValue('B' . $id, $type);
                    foreach ($resources as $resource_type => $totals) {
                        $sheet->setCellValue('C' . $id, $resource_type);
                        $sheet->setCellValue('D' . $id, $totals['total']);
                        $sheet->setCellValue('E' . $id, $totals['working']);
                        $sheet->setCellValue('F' . $id, $totals['standby']);
                        $sheet->setCellValue('G' . $id, $totals['total_ops']);
                        $sheet->setCellValue('H' . $id, $totals['total_pay']);
                        $sheet->setCellValue('I' . $id, $totals['total_ot']);
                        $id++;
                    }
                }

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

                header('Content-Disposition: attachment;filename="Steve - Payroll - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end . '.xlsx"');

                header('Cache-Control: max-age=0');
                $writer->save('php://output');

                die;
            } else {
                die("Spreadsheet export failed. No workers found to export.");
            }
        } else {
            die("Spreadsheet export failed. Please try a different time period.");
        }
    }
    public function operationgroup_spreadsheet()
    {

        if ($this->input->get("start") && $this->input->get("end") &&  $this->user_model->has_perm("download_payroll")) {

            $start = $this->input->get("start");
            $end = $this->input->get("end");
            $worker_group_id = $this->input->get("worker_group_id");

            if ($worker_group_id == "" || $worker_group_id == null) {
                $workers = $this->db->select("distinct(worker_id)")
                    ->where("worker_availability.availability_date >= '" . $start . "'")
                    ->where("worker_availability.work_end IS NOT NULL")
                    ->where("worker_availability.availability_date <= '" . $end . "'")
                    ->get_where('worker_availability', ["deleted" => 0])
                    ->result();
            } else {
                $workers = $this->db->select("distinct(worker_id)")
                    ->where("worker_availability.availability_date >= '" . $start . "'")
                    ->where("worker_availability.work_end IS NOT NULL")
                    ->where("worker_availability.availability_date <= '" . $end . "'")
                    ->get_where('worker_availability', ["deleted" => 0, "worker_availability.worker_group" => $worker_group_id])
                    ->result();
            }
            $workerids = [];
            foreach ($workers as $worker) {
                $workerids[] = $worker->worker_id;
            }

            if (!$workerids) {
                foreach ($this->db->select("distinct(worker_availability.worker_id)")->join("worker_group", "worker_group.worker_id = worker_availability.worker_id")->where("worker_availability.availability_date >= '" . $start . "'")->where("worker_availability.work_end IS NOT NULL")->where("worker_availability.availability_date <= '" . $end . "'")->get_where('worker_availability', ["deleted" => 0, "worker_group.worker_group_id" => $this->input->get("group")])->result() as $worker) {
                    $workerids[] = $worker->worker_id;
                }
            }

            if ($workerids) {
                $workers = $this->db->order_by("worker_type", "asc")
                    ->order_by("tenure_date", "desc")
                    ->select("*, workers.worker_id as worker_id_main")
                    ->join("worker_group", "worker_group.worker_id = workers.worker_id", "left")
                    ->join("worker_groups", "worker_groups.worker_group_id = worker_group.worker_group_id", "left")
                    ->join("resource_types", "resource_types.resource_type_id = workers.worker_resource_type", "left")
                    ->join("resource_type_rates", "resource_type_rates.resource_type_id = workers.worker_resource_type and resource_type_rates.employment_type = workers.worker_type", "left")
                    ->join("worker_tenure", "worker_tenure.worker_id = workers.worker_id", "left")
                    ->where("workers.worker_id IS NOT NULL")
                    ->where("(tenure_action IS NULL or tenure_action = 'joined')")
                    ->where_in("workers.worker_id", $workerids)
                    ->get('workers')->result();
                $worked_days = $this->db->where("availability_date >= '" . $worker->payment_effective . "'")
                    ->where("availability_date >= '" . $start . "'")
                    ->where("availability_date <= '" . $end . "'")
                    ->where("availability_date <= '" . $end . "'")
                    ->where_in("worker_availability.worker_id", $workerids)
                    ->get_where("worker_availability", ["attendance_processed" => 1])
                    ->result();

                $group = $this->db->get_where("worker_groups", ["worker_group_id" => $this->input->get("group")])->result();

                $workers_attendance = [];
                $daily_attendance = [];
                $worker_pays = [];

                foreach ($worked_days as $day) {
                    $workers_attendance[$day->worker_id][$day->availability_date] = $day;
                    $daily_attendance[$day->availability_date][$day->worker_id] = $day;
                }

                $spreadsheet = new Spreadsheet();

                $spreadsheet->getProperties()
                    ->setCreator("Steve")
                    ->setLastModifiedBy("Steve")
                    ->setTitle("Steve payroll")
                    ->setSubject("Steve")
                    ->setDescription(
                        "Payroll report"
                    )
                    ->setKeywords("Payroll report")
                    ->setCategory("Reports");

                $spreadsheet->removeSheetByIndex(0);
                $r = 1;
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Attendance'));
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Payroll'));
                $spreadsheet->addSheet(new \PhpOffice\PhpSpreadsheet\Worksheet\Worksheet($spreadsheet, 'Summary'));

                $sheet = $spreadsheet->getSheetByName('Attendance');

                $sheet->setCellValue('B1', 'ATTENDANCE - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->setCellValue('B2', 'Basic details');
                $sheet->setCellValue('H2', 'Date - Day & month');

                $sheet->mergeCells('B2:G2');

                $sheet->setCellValue('B3', 'IC Number');
                $sheet->setCellValue('C3', 'Name');
                $sheet->setCellValue('D3', 'Group');
                $sheet->setCellValue('E3', 'Type');
                $sheet->setCellValue('F3', 'Skill');
                $sheet->setCellValue('G3', 'Date joined');

                $alphabets = range('A', 'Z');

                $workers_done = [];

                $id = 3;

                $workers_info = [];

                foreach ($workers as $worker) {
                    if (!$workers_done[$worker->worker_id_main]) {
                        $id++;
                        $sheet->setCellValue('B' . $id, $worker->ic_number);
                        $sheet->setCellValue('C' . $id, $worker->worker_name);
                        $sheet->setCellValue('D' . $id, $worker->worker_group_code);
                        $sheet->setCellValue('E' . $id, strtoupper(str_replace("-", " ", $worker->worker_type)));
                        $sheet->setCellValue('F' . $id, $worker->resource_type_short_code);
                        $sheet->setCellValue('G' . $id, $worker->tenure_date);

                        $workers_done[$worker->worker_id_main] = [$id, $worker->worker_type];

                        $sheet->mergeCells('B' . $id . ':B' . ($id + 1));
                        $sheet->mergeCells('C' . $id . ':C' . ($id + 1));
                        $sheet->mergeCells('D' . $id . ':D' . ($id + 1));
                        $sheet->mergeCells('E' . $id . ':E' . ($id + 1));
                        $sheet->mergeCells('F' . $id . ':F' . ($id + 1));
                        $sheet->mergeCells('G' . $id . ':G' . ($id + 1));

                        $id++;

                        if ($worker->monthly_allowance) {
                            $worker_allowance = $this->db->get_where("worker_allowances", ["worker_id" => $worker->worker_id_main, "month" => date("Y-m", strtotime($start))])->result();
                            if ($worker_allowance) {
                                $worker->extra_allowance = $worker_allowance[0]->allowance_amount;
                            } else {
                                $worker->extra_allowance = $worker->monthly_allowance;
                                $this->db->reset_query();
                                $this->db->set("worker_id", $worker->worker_id_main);
                                $this->db->set("allowance_amount", $worker->monthly_allowance);
                                $this->db->set("month", date("Y-m", strtotime($start)));
                                $this->db->insert("worker_allowances");
                            }
                        }

                        $workers_info[$worker->worker_id_main] = $worker;
                        $worker_pays[$worker->worker_id_main] = ["ph_ot" => 0, "ph_pay" => 0, "rd_pay" => 0, "rd_ot" => 0, "work_through_meals" => 0, "publicholidays" => 0, "restdays" => 0, "standby_days" => 0, "pay_days" => 0, "pay_hours" => 0, "lop_hours" => 0, "ot_hours" => 0, "pay_amount" => 0, "lop_amount" => 0, "ot_amount" => 0, "worked_rest_days" => 0, "worked_public_holidays" => 0, "worked_work_through_meals" => 0];
                    }
                }

                $last_alphabet = 'A';

                $end_date = new DateTime($end);

                for ($i = 0; $i <= $end_date->diff(new DateTime($start))->format("%a"); $i++) {
                    $last_alphabet = ($i > 19 ? "A" . $alphabets[$i - 20] : $alphabets[$i + 6]);
                    $currentDate = date("Y-m-d", strtotime($start . " + " . $i . " days"));


                    $sheet->setCellValue($last_alphabet . '3', date("d-M", strtotime($start . " + " . $i . " days")));
                    $is_public_holiday = $this->db->get_where("public_holidays", ["public_holiday_date" => $currentDate, "active" => 1])->num_rows();

                    $sheet->getColumnDimension($last_alphabet)->setAutoSize(true);

                    foreach ($workers_done as $worker_id_main => $data) {
                        $row = $data[0];
                        $worker_type = $data[1];
                        if ($daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main]) {
                            $day = $daily_attendance[date("Y-m-d", strtotime($start . " + " . $i . " days"))][$worker_id_main];

                            if ($day->work_start) {
                                $sheet->setCellValue($last_alphabet . $row, date("H:i", strtotime($day->work_start)));
                            } else {
                                $sheet->setCellValue($last_alphabet . $row, $day->worker_attendance);
                            }
                            if ($day->work_end) {
                                $sheet->setCellValue($last_alphabet . ($row + 1), date("H:i", strtotime($day->work_end)));
                            } else {
                                $sheet->setCellValue($last_alphabet . ($row + 1), $day->worker_attendance);
                            }

                            if ($day->worker_attendance != 'P' && ($day->work_start)) {
                                $worker_pays[$worker_id_main]["leave_days"]++;
                            }

                            $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

                            if ($day->worker_attendance == 'RD' && $worker_type == "contract-monthly") {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('ff0000');
                            } else if ($is_public_holiday && ($worker_type == 'contract-monthly')) {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('fff830');
                            } else {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setARGB($day->work_standby ? 'FF73FDFF' : 'FFF79646');
                            }

                            if ($day->worker_shift) {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFont()->getColor()->setARGB($day->worker_shift == 2 ? 'FF008F00' : 'FF941751');
                            }

                            if ($day->work_through_meals) {
                                if (!$day->work_standby) {
                                    $worker_pays[$worker_id_main]["worked_work_through_meals"]++;
                                }
                                $worker_pays[$worker_id_main]["work_through_meals"]++;
                                // $worker_pays[$worker_id_main]["ot_hours"] ++;
                            }

                            if ($worker_type == 'contract-monthly') {
                                if ($is_public_holiday) {
                                    if ($day->work_start && $day->work_end) {
                                        $worker_pays[$worker_id_main]["worked_public_holidays"]++;
                                    }
                                    $worker_pays[$worker_id_main]["publicholidays"]++;
                                } else if ($day->worker_attendance == 'RD') {
                                    if ($day->work_start && $day->work_end) {
                                        $worker_pays[$worker_id_main]["worked_rest_days"]++;
                                    }
                                    $worker_pays[$worker_id_main]["restdays"]++;
                                }
                            }

                            if ($day->work_standby) {
                                $worker_pays[$worker_id_main]["standby_days"]++;
                            }
                            //if(!$is_public_holiday && $day->worker_attendance != 'RD' && !$day->work_standby) {
                            else {
                                $worker_pays[$worker_id_main]["pay_days"]++;
                            }
                            $worker_pays[$worker_id_main]["pay_hours"] += $day->pay_hours;
                            $worker_pays[$worker_id_main]["lop_hours"] += $day->lop_hours;
                            $worker_pays[$worker_id_main]["ot_hours"] += $day->ot_hours;
                            $worker_pays[$worker_id_main]["pay_amount"] += $day->pay_amount;
                            $worker_pays[$worker_id_main]["lop_amount"] += $day->lop_amount;
                            $worker_pays[$worker_id_main]["ot_amount"] += $day->ot_amount;
                            $worker_pays[$worker_id_main]["ph_pay"] += $day->ph_pay;
                            $worker_pays[$worker_id_main]["ph_ot"] += $day->ph_ot;
                            $worker_pays[$worker_id_main]["rd_pay"] += $day->rd_pay;
                            $worker_pays[$worker_id_main]["rd_ot"] += $day->rd_ot;
                            $worker_pays[$worker_id_main]["work_through_meals_pay"] += $day->work_through_meals_pay;
                        } else {
                            if ($is_public_holiday && $worker_type == "contract-monthly") {
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                                $sheet->getStyle($last_alphabet . $row . ":" . $last_alphabet . ($row + 1))->getFill()->getStartColor()->setRGB('fff830');
                                $sheet->setCellValue($last_alphabet . $row, "PH");
                                $sheet->setCellValue($last_alphabet . ($row + 1), "PH");
                            } else {
                                $sheet->setCellValue($last_alphabet . $row, "-");
                                $sheet->setCellValue($last_alphabet . ($row + 1), "-");
                            }
                        }
                    }
                }

                $sheet->getStyle('B2:' . $last_alphabet . '3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach ($workers_done as $worker_id_main => $data) {
                    $row = $data[0];
                    $sheet->getStyle('B' . $row . ":" . $last_alphabet . ($row + 1))->applyFromArray([
                        'borders' => [
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            ],
                            'inside' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                            ],
                        ],
                    ]);
                }

                $sheet->getStyle("B" . ($row + 3) . ":B" . ($row + 4))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);

                $sheet->setCellValue("B" . ($row + 3), "In Operation");
                $sheet->getStyle("B" . ($row + 3))->getFill()->getStartColor()->setARGB('FFF79646');

                $sheet->setCellValue("B" . ($row + 4), "On standby");
                $sheet->getStyle("B" . ($row + 4))->getFill()->getStartColor()->setARGB('FF73FDFF');

                $sheet->setCellValue("B" . ($row + 5), "Shift 1");
                $sheet->getStyle("B" . ($row + 5))->getFont()->getColor()->setARGB('FF941751');

                $sheet->setCellValue("B" . ($row + 6), "Shift 2");
                $sheet->getStyle("B" . ($row + 6))->getFont()->getColor()->setARGB('FF008F00');

                $sheet->getStyle("B" . ($row + 7))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->setCellValue("B" . ($row + 7), "Public Holiday");
                $sheet->getStyle("B" . ($row + 7))->getFill()->getStartColor()->setRGB('fff830');
                $sheet->getStyle("B" . ($row + 8))->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID);
                $sheet->setCellValue("B" . ($row + 8), "RD");
                $sheet->getStyle("B" . ($row + 8))->getFill()->getStartColor()->setRGB('ff0000');

                foreach (range("B", "G") as $alphabet) {
                    $sheet->getColumnDimension($alphabet)->setAutoSize(true);
                }

                $sheet->mergeCells('H2:' . $last_alphabet . '2');
                $sheet->mergeCells('B1:' . $last_alphabet . '1');
                $sheet->getStyle('B1:' . $last_alphabet . '3')->getFont()->setBold(true);
                $sheet->getStyle('B1:' . $last_alphabet . '3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // Payroll

                $sheet = $spreadsheet->getSheetByName('Payroll');

                $sheet->setCellValue('B1', 'PAYROLL - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->mergeCells('B1:P1');

                $sheet->setCellValue('B2', 'Basic details');

                $sheet->mergeCells('B2:F2');

                $sheet->setCellValue('B3', 'IC Number');
                $sheet->setCellValue('C3', 'Name');
                $sheet->setCellValue('D3', 'Group');
                $sheet->setCellValue('E3', 'Type');
                $sheet->setCellValue('F3', 'Skill');
                $sheet->setCellValue('G3', 'Date joined');

                $sheet->setCellValue('H3', 'Pay days');
                $sheet->setCellValue('I3', 'Standby days');
                $sheet->setCellValue('J3', 'Rest days');
                $sheet->setCellValue('K3', 'Public Holidays');

                $sheet->setCellValue('L3', 'Work Through Meals');
                $sheet->setCellValue('M3', 'Leave days');
                $sheet->setCellValue('N3', 'OT hours');
                $sheet->setCellValue('O3', 'Pay hours');
                $sheet->setCellValue('P3', 'Pay amount');
                $sheet->setCellValue('Q3', 'Public Holiday Pay');
                $sheet->setCellValue('R3', 'Public Holiday OT');
                $sheet->setCellValue('S3', 'Rest Day Pay');
                $sheet->setCellValue('T3', 'Rest Day Ot');
                $sheet->setCellValue('U3', 'Work Through Meals Pay');

                $sheet->setCellValue('V3', 'Overtime');
                $sheet->setCellValue('W3', 'Allowance');
                $sheet->setCellValue('X3', 'Pay loss');
                $sheet->setCellValue('Y3', 'Total');
                $sheet->getStyle("B1:Y3")->getFont()->setBold(true);
                $sheet->getStyle('B1:Y3')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                $sheet->getStyle('B2:Y3')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach (range('B', 'T') as $a) {
                    $sheet->getColumnDimension($a)->setAutoSize(true);
                }

                $summary = [];

                $id = 3;


                foreach ($workers_done as $worker_id_main => $row) {
                    log_message("error", "================================================");
                    $id++;
                    $sheet->setCellValue('B' . $id, $workers_info[$worker_id_main]->ic_number);
                    $sheet->setCellValue('C' . $id, $workers_info[$worker_id_main]->worker_name);
                    $sheet->setCellValue('D' . $id, $workers_info[$worker_id_main]->worker_group_code);
                    $sheet->setCellValue('E' . $id, strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type)));
                    $sheet->setCellValue('F' . $id, $workers_info[$worker_id_main]->resource_type_short_code);
                    $sheet->setCellValue('G' . $id, $workers_info[$worker_id_main]->tenure_date);

                    $sheet->setCellValue('H' . $id, $worker_pays[$worker_id_main]['pay_days']);
                    $sheet->setCellValue('I' . $id, $worker_pays[$worker_id_main]['standby_days']);
                    $sheet->setCellValue('J' . $id, $worker_pays[$worker_id_main]['restdays']);
                    $sheet->setCellValue('K' . $id, $worker_pays[$worker_id_main]['publicholidays']);
                    $sheet->setCellValue('L' . $id, $worker_pays[$worker_id_main]['worked_work_through_meals']);
                    $sheet->setCellValue('M' . $id, $worker_pays[$worker_id_main]['leave_days']);
                    $sheet->setCellValue('N' . $id, $worker_pays[$worker_id_main]['ot_hours']);
                    $sheet->setCellValue('O' . $id, $worker_pays[$worker_id_main]['pay_hours']);
                    if ($workers_info[$worker_id_main]->worker_type == "contract-monthly") {
                        //log_message("error", json_encode($workers_info[$worker_id_main]));
                        $work_rate = $workers_info[$worker_id_main]->work_rate;
                        if ($workers_info[$worker_id_main]->work_rate_override && $workers_info[$worker_id_main]->work_rate_override > 0) {
                            $work_rate = $workers_info[$worker_id_main]->work_rate_override;
                        }
                        $pay_amount = (float)$work_rate * 26;
                        /* log_message("error", "pay_amount: ". $pay_amount);
                        log_message("error", "rest days: ". $worker_pays[$worker_id_main]['worked_rest_days']);
                        log_message("error", "publicholidays days: ". $worker_pays[$worker_id_main]['publicholidays']);
                        if((int)$worker_pays[$worker_id_main]['worked_rest_days'] > 0)
                        {
                            log_message("error", "rs pay". ((int)$worker_pays[$worker_id_main]['worked_rest_days'] * (float)$work_rate * 2));
                            $pay_amount = $pay_amount + ((int)$worker_pays[$worker_id_main]['worked_rest_days'] * (float)$work_rate * 2);
                        }
                        log_message("error", "RS pay_amount: ". $pay_amount);
                        if((int)$worker_pays[$worker_id_main]['worked_public_holidays'] > 0)
                        {
                            $pay_amount = $pay_amount + ((int)$worker_pays[$worker_id_main]['worked_public_holidays'] * (float)$work_rate * 3);
                        }
                        log_message("error", "final pay_amount: ". $pay_amount);
                        */
                        $worker_pays[$worker_id_main]['pay_amount'] = $pay_amount;
                        $sheet->setCellValue('P' . $id, $pay_amount);
                        log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    } else {
                        $sheet->setCellValue('P' . $id, $worker_pays[$worker_id_main]['pay_amount']);
                        log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    }


                    $sheet->setCellValue('Q' . $id, $worker_pays[$worker_id_main]['ph_pay']);
                    $sheet->setCellValue('R' . $id, $worker_pays[$worker_id_main]['ph_ot']);
                    $sheet->setCellValue('S' . $id, $worker_pays[$worker_id_main]['rd_pay']);
                    $sheet->setCellValue('T' . $id, $worker_pays[$worker_id_main]['rd_ot']);
                    $sheet->setCellValue('U' . $id, $worker_pays[$worker_id_main]['work_through_meals_pay']);

                    $sheet->setCellValue('V' . $id, $worker_pays[$worker_id_main]['ot_amount']);



                    if (!$workers_info[$worker_id_main]->extra_allowance) {
                        $workers_info[$worker_id_main]->extra_allowance = 0;
                    }
                    $sheet->setCellValue('W' . $id, $workers_info[$worker_id_main]->extra_allowance);

                    $sheet->setCellValue('X' . $id, $worker_pays[$worker_id_main]['lop_amount']);

                    log_message("error", "pay_amount" . $worker_pays[$worker_id_main]['pay_amount']);
                    log_message("error", "ot_amount" . $worker_pays[$worker_id_main]['ot_amount']);

                    log_message("error", "extra_allowance" . $workers_info[$worker_id_main]->extra_allowance);

                    log_message("error", "lop_amount" . $worker_pays[$worker_id_main]['lop_amount']);


                    log_message("error", "================================================");
                    $sheet->setCellValue('Y' . $id, $worker_pays[$worker_id_main]['pay_amount'] + $worker_pays[$worker_id_main]['ph_pay'] + $worker_pays[$worker_id_main]['ph_ot'] + $worker_pays[$worker_id_main]['rd_pay'] + $worker_pays[$worker_id_main]['rd_ot'] + $worker_pays[$worker_id_main]['work_through_meals_pay'] + $worker_pays[$worker_id_main]['ot_amount'] + $workers_info[$worker_id_main]->extra_allowance - $worker_pays[$worker_id_main]['lop_amount']);

                    if (!$summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code] = ['total' => 0, 'working' => 0, 'standby' => 0, 'total_ops' => 0, 'total_pay' => 0, 'total_ot' => 0];
                    }
                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total']++;

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['working'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['standby'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['pay_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ops'] += $worker_pays[$worker_id_main]['standby_days'];

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $worker_pays[$worker_id_main]['pay_amount'];

                    if ($workers_info[$worker_id_main]->extra_allowance) {
                        $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_pay'] += $workers_info[$worker_id_main]->extra_allowance;
                    }

                    $summary[strtoupper(str_replace("-", " ", $workers_info[$worker_id_main]->worker_type))][$workers_info[$worker_id_main]->resource_type_short_code]['total_ot'] += $worker_pays[$worker_id_main]['ot_hours'];
                }

                $sheet = $spreadsheet->getSheetByName('Summary');

                $sheet->setCellValue('B1', 'SUMMARY - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end);

                $sheet->mergeCells('B1:J1');

                $sheet->setCellValue('B2', 'Employment Type');

                $sheet->setCellValue('C2', 'Skill');
                $sheet->setCellValue('D2', 'Total staff');

                $sheet->setCellValue('E2', 'Total pay days');
                $sheet->setCellValue('F2', 'Total standby days');
                $sheet->setCellValue('G2', 'Total operations');
                $sheet->setCellValue('H2', 'Total pay (RM)');
                $sheet->setCellValue('I2', 'Total OT (hours)');

                $sheet->getStyle("B1:I2")->getFont()->setBold(true);
                $sheet->getStyle('B1:I2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                $sheet->getStyle('B2:I2')->applyFromArray([
                    'borders' => [
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                        'inside' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_DOTTED,
                        ],
                    ],
                ]);

                foreach (range('B', 'I') as $a) {
                    $sheet->getColumnDimension($a)->setAutoSize(true);
                }
                $id = 2;

                foreach ($summary as $type => $resources) {
                    $id++;
                    $sheet->setCellValue('B' . $id, $type);
                    foreach ($resources as $resource_type => $totals) {
                        $sheet->setCellValue('C' . $id, $resource_type);
                        $sheet->setCellValue('D' . $id, $totals['total']);
                        $sheet->setCellValue('E' . $id, $totals['working']);
                        $sheet->setCellValue('F' . $id, $totals['standby']);
                        $sheet->setCellValue('G' . $id, $totals['total_ops']);
                        $sheet->setCellValue('H' . $id, $totals['total_pay']);
                        $sheet->setCellValue('I' . $id, $totals['total_ot']);
                        $id++;
                    }
                }

                $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');

                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');

                header('Content-Disposition: attachment;filename="Steve - Payroll - ' . strtoupper($group[0]->worker_group_name) . ' - ' . $start . " - " . $end . '.xlsx"');

                header('Cache-Control: max-age=0');
                $writer->save('php://output');

                die;
            } else {
                die("Spreadsheet export failed. No workers found to export.");
            }
        } else {
            die("Spreadsheet export failed. Please try a different time period.");
        }
    }
}
