<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Task_model extends CI_Model
{
    protected $table = 'task_list';
    protected $primary_key = 'id';

    public function __construct()
    {
        parent::__construct();
    }

    // Existing methods
    public function getAll()
    {
        return $this->db->get($this->table)->result();
    }

    public function getById($id)
    {
        return $this->db->get_where($this->table, [$this->primary_key => $id])->row();
    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }

    public function updateTask($id, $data)
    {
        $this->db->where($this->primary_key, $id);
        return $this->db->update($this->table, $data);
    }

    public function delete($id)
    {
        return $this->db->delete($this->table, [$this->primary_key => $id]);
    }

    // 🔥 NEW DATATABLES METHODS
    public function count_all()
    {
        return $this->db->count_all($this->table);
    }

    public function count_filtered($search = '')
    {
        $this->db->from($this->table);
        
        if(!empty($search)) {
            $this->db->like('name', $search);
            $this->db->or_like('frequency_in_days', $search);
        }
        
        return $this->db->count_all_results();
    }

    public function get_datatables($start = 0, $length = 10, $search = '')
    {
        $this->db->from($this->table);
        
        if(!empty($search)) {
            $this->db->like('name', $search);
            $this->db->or_like('frequency_in_days', $search);
        }
        
        $this->db->limit($length, $start);
        $this->db->order_by('id', 'ASC');
        
        return $this->db->get()->result();
    }
}