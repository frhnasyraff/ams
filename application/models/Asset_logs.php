<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Asset_logs model class.
 *
 * Handles logging specifically to the asset_logs table.
 *
 * @extends CI_Model
 */
class Asset_logs extends CI_Model
{
    /**
     * Constructor
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add a log entry to the asset_logs table.
     *
     * @param string $table         The table being referenced (e.g., 'assets/info')
     * @param int    $id            ID of the item being logged
     * @param string $code          Log event code (e.g., 'asset_update')
     * @param string|array $description Description of the event (string or array)
     * @param int|string $user_id   Optional user ID (defaults to logged-in user)
     */
    public function add($table, $id, $code, $description = '', $user_id = '')
    {
        if (!$user_id && isset($_SESSION['user']) && isset($_SESSION['user']->user_id)) {
            $user_id = $_SESSION['user']->user_id;
        }
        if (!$user_id) {
            log_message('error', 'Asset_logs: user_id is null, aborting insert.');
            return false; // Or handle as needed
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

            $this->db->insert("asset_logs");
            $this->db->reset_query();
        }
    }
}
