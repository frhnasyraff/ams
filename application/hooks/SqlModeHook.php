<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SqlModeHook
{
    public function removeOnlyFullGroupBy()
    {
        $CI =& get_instance();

        if (!isset($CI->db)) {
            return;
        }

        // Legacy queries in this IMS select non-grouped columns. MySQL 8 enables ONLY_FULL_GROUP_BY by default,
        // so remove only that mode for this request while keeping the rest of the server SQL mode intact.
        $CI->db->query("SET SESSION sql_mode = REPLACE(@@SESSION.sql_mode, 'ONLY_FULL_GROUP_BY', '')");
    }
}
