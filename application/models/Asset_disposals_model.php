<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_disposals_model extends CI_Model
{
    private $table = 'asset_disposal_requests';

    public function __construct()
    {
        parent::__construct();
    }

    public function get_all($limit = null, $offset = null, $search = null, $status = null)
    {
        $this->db->select('
            adr.*,
            ea.equipment_id,
            ea.equipment_name,
            at.name AS equipment_type_name,
            dm.disposal_method AS disposal_method_name
        ');
        $this->db->from($this->table . ' adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');
        $this->db->join('disposal_methods dm', 'dm.id = adr.disposal_method_id', 'left');  

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ea.equipment_id', $search);
            $this->db->or_like('ea.equipment_name', $search);
            $this->db->or_like('at.name', $search);
            $this->db->or_like('dm.disposal_method', $search); // agar search me disposal_method bhi include karna ho
            $this->db->group_end();
        }

        if (!empty($status) && $status != 'all') {
            $this->db->where('adr.status', $status);
        }

        $this->db->order_by('adr.created_at', 'DESC');

        if ($limit !== null && $offset !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }


    public function count_all($search = null, $status = null)
    {
        $this->db->from($this->table . ' adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('ea.equipment_id', $search);
            $this->db->or_like('ea.equipment_name', $search);
            $this->db->or_like('at.name', $search);
            $this->db->group_end();
        }

        if (!empty($status) && $status != 'all') {
            $this->db->where('adr.status', $status);
        }

        return $this->db->count_all_results();
    }
}
?>
