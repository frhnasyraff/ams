<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Common class.
 *
 * @extends CI_Model
 */
class Logs extends CI_Model
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

    public function add($table, $id, $code, $description = '', $user_id = '')
    {
        if (!$user_id && $_SESSION['user']) {
            $user_id = $_SESSION['user']->user_id;
        }
        if ($user_id) {
            
            $this->db->reset_query();
            
            $this->db->set("log_item_table", $table);
            $this->db->set("log_item_id", intval($id));
            $this->db->set("log_code", $code);
            if (is_array($description)) {
                $this->db->set("log_description", json_encode($description));
            } else {
                $this->db->set("log_description", $description);
            }
            $this->db->set("log_user_id", $user_id);
            $this->db->set("log_ip", $_SERVER['REMOTE_ADDR']);
            
            $this->db->insert("logs");
            $this->db->reset_query();
            return;
        }
    }

    public function seen_by_others($item, $item_id, $code, $mins = 5)
    {
return $this->db->order_by("timestamp", "desc")->limit(1,0)->join("users", "users.user_id = logs.log_user_id", "left")->where("timestamp > DATE_SUB(NOW(), INTERVAL $mins MINUTE)")->where("log_user_id != " . $_SESSION['user']->user_id)->get_where("logs", ["log_code" => $code, "log_item_id" => $item_id, "log_item_table" => $item])->result();
        }
}
