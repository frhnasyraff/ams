<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard extends CI_Controller
{

    public function __construct()
    {
        parent::__construct();

        if (!$this->user_model->logged_in()) {
            die(redirect("/"));
        } else {
            if ($this->user_model->current_user()->user_group != 1) {
                show_error('You do not have permission to view this content');
            }
        }
    }

    public function index()
    {

        $this->load->view('header', ['title' => "Dashboard", 'title2' => "WORKERS DEPLOYED", "styles" => ["design/css/datepicker.css"]]);

        $incident_requests = $this->db->order_by('incident_type_id', 'desc')->get_where('incident_requests')->result();

        $this->load->view('dashboard', [
            "workers_s1" => $this->db->where("shift = 1")->where("operation_date = CURDATE()")->count_all_results("vessel_visit_workers"),
            "workers_s2" => $this->db->where("shift = 2")->where("operation_date = CURDATE()")->count_all_results("vessel_visit_workers"),
            "workers" => $this->db->where("active = 1")->count_all_results("workers"),
            "equipments_s1" => $this->db->where("shift = 1")->where("operation_date = CURDATE()")->count_all_results("vessel_visit_equipments"),
            "equipments_s2" => $this->db->where("shift = 2")->where("operation_date = CURDATE()")->count_all_results("vessel_visit_equipments"),
            "equipments" => $this->db->where("active = 1")->count_all_results("equipments") - count($this->db->select("distinct(equipment_id)")->where("operation_date = CURDATE()")->get("vessel_visit_equipments")->result()),
            "incident_requests" => $incident_requests
        ]);
        $this->load->view('footer', ['scripts' => ['https://www.gstatic.com/charts/loader.js', "design/js/datepicker.js", "design/vendor/moment.js-2.24.0/moment.min.js", 'design/js/jquery.bootstrap-growl.min.js', 'design/js/dashboard.js', 'design/js/dashboard-overview.js?v=1']]);
    }
    public function getincidentinfo()
    {
        $this->input->post('id');

        $document_query  = $this->db->select('incident_request_attachment_id')
            ->from('incident_requests_attachments')
            ->order_by('incident_requests_attachments.timestamp', 'asc')
            ->limit(1)
            ->where("incident_requests_attachments.incident_request_id", $this->input->post('id'))
            ->where("incident_requests_attachments.deleted", 0)
            ->get();

        if ($document_query->num_rows() > 0) {
            $document_id = $document_query->result()[0];
            $info = $this->db->from("incident_requests")
                ->join("incident_types", "incident_types.incident_type_id = incident_requests.incident_type_id", "left")
                ->join("worker_locations", "worker_locations.worker_location_id  = incident_requests.location_id", "left")
                ->join("incident_requests_attachments", "incident_requests_attachments.incident_request_id = incident_requests.incident_request_id 
                                    and incident_requests_attachments.incident_request_attachment_id = $document_id->incident_request_attachment_id")
                ->join("vessel_visits", "incident_requests.vessel_visit_id = vessel_visits.vessel_visit_id", "left")
                ->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id", "left")

                ->where("incident_requests.incident_request_id", $this->input->post('id'))
                ->get();
        } else {
            $info = $this->db->from("incident_requests")
                ->join("incident_types", "incident_types.incident_type_id = incident_requests.incident_type_id", "left")
                ->join("worker_locations", "worker_locations.worker_location_id  = incident_requests.location_id", "left")
                ->join("vessel_visits", "incident_requests.vessel_visit_id = vessel_visits.vessel_visit_id", "left")
                ->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id", "left")

                ->where("incident_requests.incident_request_id", $this->input->post('id'))
                ->get();
        }

        //echo $this->db->last_query();
        $reponse = [];
        if ($info) {
            $reponse['status'] = true;
            $reponse['data'] = $info->result()[0];
        } else {
            $reponse['status'] = false;
            $reponse['data'] = $info->result()[0];
        }

        echo json_encode($reponse);
    }

    public function getdelayreport()
    {
        /* $vistids_data  = $this->db->select('service_request_operation_tally.vessel_visit_id, vessels.vessel_name')
        ->from('service_request_operation_tally')
        ->join("vessel_visits", "vessel_visits.vessel_visit_id = service_request_operation_tally.vessel_visit_id")
        ->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id")
        ->order_by('service_request_operation_tally.vessel_visit_id','desc')
        ->group_by('service_request_operation_tally.vessel_visit_id')
        ->where("service_request_operation_tally.deleted", 0)
        ->get()->result(); */

        $vistids_data = $this->db->select('vessel_visits.vessel_visit_id, vessels.vessel_name, vessel_visits.visit_eta')
            ->from("vessel_visits")
            ->join("vessels", "vessels.vessel_id = vessel_visits.vessel_id")
            ->order_by('vessel_visits.visit_eta', 'desc')
            ->group_by('vessel_visits.vessel_visit_id')
            ->where("vessel_visits.deleted", 0)
            ->get()
            ->result();

        $table = "<table id='getdelaydatatable' class='table table-bordered table-striped table-sm '><thead><tr><th>Vessel Name</th><th>ETA</th><th>Vessel Delay</th><th>H1</th><th>H2</th><th>H3</th><th>H4</th><th>H5</th></tr></thead><tbody class='small'>";
        foreach ($vistids_data as $visit) {
            $visitid = $visit->vessel_visit_id;
            $visit_eta = $visit->visit_eta;
            $vesselname = $visit->vessel_name;
            $encoded_vist_id = $this->steve->id_encode($visitid);
            /* $vesseldelayinfo  = $this->db->select('vessel_hatch_id, delay_minutes')
                            ->from('service_request_operation_tally')
                            ->order_by('service_request_operation_tally.delay_minutes','desc')
                            ->where("service_request_operation_tally.deleted", 0)
                            //->where("delay_minutes is not null")
                            ->where("service_request_operation_tally.vessel_visit_id = $visitid")
                            ->get()->result(); */
            $hatchdelayinfo  = $this->db->select('vessel_hatch_id')
                ->select_sum('delay_minutes')
                ->from('service_request_operation_tally')
                ->order_by('delay_minutes', 'desc')
                ->group_by('vessel_hatch_id')
                ->where("deleted", 0)
                ->where("vessel_hatch_id is not NULL")
                //->where("delay_minutes is not null")
                ->where("vessel_visit_id = $visitid")
                ->get()->result();

            //echo $this->db->last_query()."<br/>";


            $vesseldelayinfo  = $this->db->select('vessel_hatch_id')
                ->select_sum('delay_minutes')
                ->from('service_request_operation_tally')
                ->order_by('delay_minutes', 'desc')
                ->group_by('vessel_hatch_id')
                ->where("deleted", 0)
                ->where("vessel_hatch_id is NULL")
                //->where("delay_minutes is not null")
                ->where("vessel_visit_id = $visitid")
                ->get()->result();



            $vessel_delay = $vesseldelayinfo[0]->delay_minutes;
            $vessel_delay_hours = $this->convertToHoursMins($vessel_delay, '%02dh%02dm');
            $hatches_delay = "";
            $counter = 0;


            for ($i = 0; $i < 5; $i++) {
                $data = $hatchdelayinfo[$i];
                if ($i >= sizeof($hatchdelayinfo) || is_null($data->delay_minutes)) {
                    $hatches_delay    = $hatches_delay . "<td>NA</td>";
                } else {
                    $hatches_delay    = $hatches_delay . "<td>" . $this->convertToHoursMins($data->delay_minutes, '%02dh%02dm') . "</td>";
                    //$vessel_delay = (int)$vessel_delay + (int)$data->delay_minutes;
                }
            }
            $table = $table .  "<tr><td><a target='_blank' href='vessel_visits/performance?id=$encoded_vist_id'>$vesselname - $visitid</a></td><td>$visit_eta</td><td>" . $vessel_delay_hours . "</td>" . $hatches_delay . "</tr>";
        }

        $table = $table .  "</tbody></table>";

        echo $table;
    }
    private function convertToHoursMins($time, $format = '%02d:%02d')
    {
        if ($time < 1) {
            return sprintf($format, 0, 0);
        }
        $hours = floor($time / 60);
        $minutes = ($time % 60);
        return sprintf($format, $hours, $minutes);
    }
}
