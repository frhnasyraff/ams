<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_disposal_requests_model extends CI_Model
{
    private $table = 'asset_disposal_requests';
    
    public function __construct()
    {
        parent::__construct();

    }
    
    /**
     * Generate unique request number
     */
    public function generate_request_number()
    {
        $prefix = 'ADR-' . date('Y-m-');
        $this->db->like('request_number', $prefix, 'after');
        $this->db->order_by('request_number', 'DESC');
        $this->db->limit(1);
        $query = $this->db->get($this->table);
        
        if ($query->num_rows() > 0) {
            $last = $query->row()->request_number;
            $last_num = intval(substr($last, -3));
            $new_num = str_pad($last_num + 1, 3, '0', STR_PAD_LEFT);
        } else {
            $new_num = '001';
        }
        
        return $prefix . $new_num;
    }
        
    /**
     * Get disposal request by ID
     */
    public function get_by_id($id)
    {
        $this->db->select('adr.*, 
                          ea.equipment_name, 
                          ea.equipment_id as asset_tag,
                          ea.serial_number,
                          ea.equipment_type,
                          ea.equipment_status,
                          ea.purchase_date,
                          wor.write_off_reason');
        $this->db->from($this->table . ' adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');
        $this->db->join('write_off_reasons wor', 'wor.id = adr.write_off_reason_id', 'left');
        $this->db->where('adr.id', $id);
        $query = $this->db->get();
        return $query->row();
    }
    
    /**
     * Count all disposal requests
     */
    public function count_all($search = null, $status = null)
    {
        $this->db->from($this->table . ' adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');
        
        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('adr.request_number', $search);
            $this->db->or_like('ea.equipment_name', $search);
            $this->db->or_like('ea.equipment_id', $search);
            $this->db->group_end();
        }
        
        if (!empty($status)) {
            $this->db->where('adr.status', $status);
        }
        
        return $this->db->count_all_results();
    }
    
    /**
     * Create new disposal request
     */
    public function create($data)
    {
        $data['request_number'] = $this->generate_request_number();
        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }
    
    /**
     * Update disposal request
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    
    /**
     * Delete disposal request
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
    
    /**
     * Change status
     */
    public function change_status($id, $status, $reviewer_id = null)
    {
        $data = [
            'status' => $status,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    
    /**
     * Get active assets for dropdown
     */

public function get_active_assets()
{
    try {
        // First, let's check if table exists
        $table_exists = $this->db->table_exists('equipments_asset');
        if (!$table_exists) {
            error_log("Table 'equipments_asset' does not exist");
            return [];
        }
        
        // Get all assets regardless of status
        $this->db->select('equipment_id, equipment_name, serial_number');
        $this->db->from('equipments_asset');
        $this->db->order_by('equipment_name', 'ASC');
        $query = $this->db->get();
        
        $result = $query->result();
        error_log("Assets found: " . count($result));
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in get_active_assets: " . $e->getMessage());
        return [];
    }
}

public function get_active_reasons()
{
    try {
        // Check if table exists
        $table_exists = $this->db->table_exists('write_off_reasons');
        if (!$table_exists) {
            error_log("Table 'write_off_reasons' does not exist");
            return [];
        }
        
        $this->db->select('id, write_off_reason');
        $this->db->from('write_off_reasons');
        $this->db->order_by('write_off_reason', 'ASC');
        $query = $this->db->get();
        
        $result = $query->result();
        error_log("Write-off reasons found: " . count($result));
        
        return $result;
    } catch (Exception $e) {
        error_log("Error in get_active_reasons: " . $e->getMessage());
        return [];
    }
}
    
    /**
     * Get requests by user
     */
    public function get_by_user($user_id, $limit = null, $offset = null)
    {
        $this->db->order_by('created_at', 'DESC');
        
        if ($limit !== null && $offset !== null) {
            $this->db->limit($limit, $offset);
        }
        
        $query = $this->db->get($this->table);
        return $query->result();
    }
    
    /**
     * Check if asset already has pending disposal request
     */
    public function has_pending_request($asset_id)
    {
        $this->db->where('equipment_asset_id', $asset_id);
        $this->db->where_in('status', ['draft', 'submitted', 'under_review']);
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }
    
    /**
     * Update attachment path
     */
    public function update_attachment($id, $attachment_path)
    {
        $data = [
            'attachment' => $attachment_path,
            'updated_at' => date('Y-m-d H:i:s')
        ];
        
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }


public function get_available_assets()
{
    $this->db->select('equipment_id, equipment_name, serial_number');
    $this->db->from('equipments_asset');
    $this->db->order_by('equipment_name', 'ASC');
    $query = $this->db->get();
    return $query->result();
}

public function get_all($limit = null, $offset = null, $search = null, $status = null)
{
    $this->db->select('adr.*, ea.equipment_name, ea.serial_number');
    $this->db->from($this->table . ' adr');
    $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');

    if (!empty($search)) {
        $this->db->group_start();
        $this->db->like('adr.request_number', $search);
        $this->db->or_like('ea.equipment_name', $search);
        $this->db->or_like('ea.equipment_id', $search);
        $this->db->group_end();
    }

    if (!empty($status) && $status !== 'all') {
        $this->db->where('adr.status', $status);
    }

    $this->db->order_by('adr.created_at', 'DESC');
    if ($limit !== null) {
        $this->db->limit($limit, (int) $offset);
    }

    return $this->db->get()->result();
}


}
