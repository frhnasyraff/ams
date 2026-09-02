<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Write_off_reasons_model extends CI_Model
{
    private $table = 'write_off_reasons';
    
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get all write-off reasons
     */
    public function get_all($limit = null, $offset = null, $search = null, $status = null)
    {
        $this->db->from($this->table);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('write_off_reason', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        $this->db->order_by('write_off_reason', 'ASC');

        if ($limit !== null) {
            $this->db->limit($limit, $offset);
        }

        return $this->db->get()->result();
    }
    
    /**
     * Get write-off reason by ID
     */
    public function get_by_id($id)
    {
        $this->db->where('id', $id);
        $query = $this->db->get($this->table);
        return $query->row();
    }
    
    /**
     * Count all write-off reasons
     */
    public function count_all($search = null, $status = null)
    {
        $this->db->from($this->table);

        if (!empty($search)) {
            $this->db->group_start();
            $this->db->like('write_off_reason', $search);
            $this->db->or_like('description', $search);
            $this->db->group_end();
        }

        if (!empty($status)) {
            $this->db->where('status', $status);
        }

        return $this->db->count_all_results();
    }
    
    /**
     * Create new write-off reason
     */
    public function create($data)
    {
        // Remove created_by
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

    
    /**
     * Update write-off reason
     */
    public function update($id, $data)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, $data);
    }
    
    /**
     * Delete write-off reason
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        return $this->db->delete($this->table);
    }
    
    /**
     * Change status (active/inactive)
     */
    public function change_status($id, $status)
    {
        $this->db->where('id', $id);
        return $this->db->update($this->table, ['status' => $status]);
    }
    
    /**
     * Get active write-off reasons for dropdown
     */
    public function get_active_reasons()
    {
        $this->db->select('id, write_off_reason');
        $this->db->from($this->table);
        $this->db->where('status', 'active');
        $this->db->order_by('write_off_reason', 'ASC');
        $query = $this->db->get();
        return $query->result();
    }
    
    /**
     * Check if write-off reason already exists
     */
    public function exists($write_off_reason, $exclude_id = null)
    {
        $this->db->where('write_off_reason', $write_off_reason);
        
        if ($exclude_id) {
            $this->db->where('id !=', $exclude_id);
        }
        
        $query = $this->db->get($this->table);
        return $query->num_rows() > 0;
    }
}