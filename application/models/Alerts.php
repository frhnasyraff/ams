<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Common class.
 *
 * @extends CI_Model
 */
class Alerts extends CI_Model
{

    /**
     * __construct function.
     *
     * @access public
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function add($column, $id, $message, $branch_id, $permission, $status)
    {
        if ($branch_id) {
            $this->db->reset_query();
            $users = $this->db->join("user_role", "user_role.user_id = users.user_id", "left")->join("role_permissions", "role_permissions.role_id = user_role.role_id", "left")->join("permissions", "permissions.perm_id = role_permissions.perm_id", "left")->get_where("users", ['users.active' => 1, 'permissions.perm_name' => $permission])->result();

            $recipients = [];

            if (count($users)) {
                foreach ($users as $user) {
                    if ($user->user_id != $_SESSION['user']->user_id) {
                        $recipients[] = $user->user_id;
                    }
                }
            }

            if (count($recipients)) {
                $this->db->reset_query();
                $this->db->set("alert_" . $column, $id);
                $this->db->set('recipients', implode(",", $recipients));
                $this->db->set("record_status", $status);
                $this->db->set("message", $message);
                $this->db->set("branch_id", $branch_id);
                $this->db->set("active", 1);
                $this->db->insert("alerts");
                $this->db->reset_query();
                return;
            }
        }
    }

    public function set_done($alert_id)
    {
        $this->db->reset_query();
        $this->db->set("active", 0);
        $this->db->where("alert_id", $alert_id);
        return $this->db->update("alerts");
    }

    function list($user_id, $branch_id)
    {

        // var_dump($branch_id);die();        
        // $alerts = $this->db->order_by("alert_timestamp", "desc")->where("FIND_IN_SET(" . $user_id . ", `alerts`.`recipients`)")->get_where("alerts", ['alerts.active' => 1, 'alerts.branch_id' => $branch_id])->result();
        // join("bookings", "alerts.alert_booking_id = bookings.booking_id and bookings.status = alerts.record_status", "left")->join("quotations", "alerts.alert_quotation_id = quotations.quotation_id and quotations.status = alerts.record_status", "left")->join("bills_of_lading", "alerts.alert_bills_of_lading_id = bills_of_lading.bills_of_lading_id and bills_of_lading.bl_status = alerts.record_status", "left")->

        $alerts = $this->db->order_by("alert_timestamp", "desc")->where("FIND_IN_SET(" . $user_id . ", `alerts`.`recipients`)")->get_where('alerts', ['alerts.active' => 1])->result();
        // $alertsCount = $this->db->order_by("alert_timestamp", "desc")->where("FIND_IN_SET(" . $user_id . ", `alerts`.`recipients`)")->get_where('alerts', ['alerts.active' => 1, 'alerts.checked' => 0])->result();
        $alertsCount = $this->db->order_by("alert_timestamp", "desc")->where("FIND_IN_SET(" . $user_id . ", `alerts`.`recipients`)")->get_where('alerts', ['alerts.active' => 1])->result();
        // var_dump($alerts);die();

        $response = [];
        $key = 0;
        if (count($alerts)) {
            foreach ($alerts as $alert) {

                if ($alert->type == 0) {
                    $alert->color = "danger";
                    $alert->icon = "scroll";
                    $alert->title = 'Order Cancelled';
                    $alert->url = site_url("/customer_center?order=notificationOrder&type=cancel&id=" . $alert->order_id);
                } else if ($alert->type == 1) {
                    $alert->color = "primary";
                    $alert->icon = "receipt";
                    $alert->title = 'Remark Added';
                    $alert->url = site_url("/customer_center?order=notificationOrder&type=remark&id=" . $alert->order_id);
                } else if ($alert->type == 2) {
                    $alert->color = "secondary";
                    $alert->icon = "receipt";
                    $alert->title = 'Feedback Added';
                    $alert->url = site_url("/customer_center?order=notificationOrder&type=feedback&id=" . $alert->order_id);
                }
                /*
                if ($alert->quotation_id) {
                    $alert->color = "primary";
                    $alert->icon = "dollar-sign";
                    $alert->title = $alert->quotation_number;
                    $alert->url = site_url("quotations/info?id=" . $this->steve->id_encode($alert->alert_quotation_id));
                } else if ($alert->bills_of_lading_id && $alert->bills_of_lading_number) {
                    $alert->color = "danger";
                    $alert->icon = "scroll";
                    $alert->title = $alert->bills_of_lading_number;
                    $alert->url = site_url("bills_of_lading/info?id=" . $this->steve->id_encode($alert->alert_bills_of_lading_id));
                } else if ($alert->booking_number) {
                    $alert->color = "secondary";
                    $alert->icon = "receipt";
                    $alert->title = $alert->booking_number;
                    $alert->url = site_url("bookings/info?id=" . $this->steve->id_encode($alert->alert_booking_id));
                }*/
                if ($alert->url) {
                    $response['data'][$key] = $alert;
                    $key++;
                } else {
                    $this->set_done($alert->alert_id);
                }
            }
        }
        // var_dump($response['data']);die();
        $response['alertsCount'] = $alertsCount;
        return $response;
    }

    private function get_remarks_table($url, $table, $user_id, $color, $icon)
    {
        $results = [];
        $related = $this->db->select("distinct(" . $table . "_id) as " . $table . "_id, " . $table . "_remarks.t_updated")->order_by($table . "_remarks.t_updated", "asc")->where($table . "_remarks.t_updated > NOW() - INTERVAL 365 DAY")->get_where($table . "_remarks", [$table . "_remarks.user_id" => $user_id])->result();

        $searches = [];
        foreach ($related as $data) {
            $searches[$data->{$table . "_id"}] = $data->t_updated;
        }

        $this->db->reset_query();

        foreach ($searches as $search_id => $t) {
            $this->db->or_group_start()->where($table . "_remarks." . $table . "_id = " . $search_id)->where($table . "_remarks.t_updated > '" . $t . "'")->where($table . "_remarks.user_id != " . $user_id)->group_end();
        }

        $remarks = $this->db->select("users.full_name, users.user_id, " . $table . "_remarks.remark, " . $table . "_remarks.t_updated, " . $url . "." . $table . "_id, " . $url . "." . $table . "_number as record_number, message_views.t_viewed")->join("users", "users.user_id = " . $table . "_remarks.user_id", "left")->join("message_views", "message_views.table_name = '" . $table . "' and message_views.record_id = " . $table . "_remarks." . $table . "_id and message_views.t_viewed > " . $table . "_remarks.t_updated and message_views.user_id = " . $_SESSION['user']->user_id, "left")->join($url, $url . "." . $table . "_id = " . $table . "_remarks." . $table . "_id")->get_where($table . "_remarks", [])->result();

        //        print_r(end($this->db->queries));
        //  print_r($remarks);

        foreach ($remarks as $info) {
            //          print_r($info);
            if (!$info->t_viewed) {
                $info->color = $color;
                $info->icon = $icon;
                $info->url = ($url . "/info?id=" . $this->steve->id_encode($info->{$table . "_id"}));
                $info->table_name = $table;
                $info->record_id = $info->{$table . "_id"};
                $results[] = $info;
            }
        }
        return $results;
    }

    public function list_remarks($user_id, $branch_id)
    {
        function sort_by_date($a, $b)
        {
            if ($a->t_updated == $b->t_updated) {
                return 0;
            }
            return ($a->t_updated > $b->t_updated) ? -1 : 1;
        }

        $results = array_merge(
            $this->get_remarks_table("service_requests", "service_request", $user_id, "primary", "people-carry")
            //            $this->get_remarks_table("notices_of_arrival", "notices_of_arrival", $user_id, "warning", "truck-loading"),
        );

        usort($results, "sort_by_date");

        return $results;
    }
}
