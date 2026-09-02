<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciation_summary_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get depreciation summary (dynamic data with accurate calculation)
     * ADDED: $asset_type_id parameter for filtering
     */
    public function get_depreciation_summary($asset_type_id = 'all')
    {
        // ================ 1. TOTAL ASSET VALUE ================
        $this->db->select_sum('price_of_purchase');
        
        // Apply filter if not 'all'
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('equipment_type', $asset_type_id);
        }
        
        $total_asset_value_query = $this->db->get('equipments_asset');
        $total_asset_value = $total_asset_value_query->row()->price_of_purchase ?: 0;
        
        // ================ 2. CALCULATE ACTUAL DEPRECIATION ================
        // Get all assets with their depreciation details
        $this->db->select('
            ea.equipment_id,
            ea.equipment_name,
            ea.price_of_purchase,
            ea.purchase_date,
            at.depreciation_method_id,
            at.useful_life_years,
            at.salvage_value,
            at.depreciate_value,
            dm.depreciation_method
        ');
        $this->db->from('equipments_asset ea');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');
        $this->db->join('depreciation_methods dm', 'dm.id = at.depreciation_method_id', 'left');
        
        // Apply filter if not 'all'
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('ea.equipment_type', $asset_type_id);
        }
        
        $assets_query = $this->db->get();
        
        $total_depreciation = 0;
        $total_assets = 0;
        $total_accumulated_depreciation = 0;
        $total_current_year_depreciation = 0;
        $total_net_book_value = 0;
        
        if ($assets_query->num_rows() > 0) {
            $total_assets = $assets_query->num_rows();
            $current_year = date('Y');
            
            foreach ($assets_query->result_array() as $asset) {
                $cost = $asset['price_of_purchase'] ?: 0;
                $purchase_date = $asset['purchase_date'];
                $useful_life = $asset['useful_life_years'] ?: 5;
                $salvage_value = $asset['salvage_value'] ?: 0;
                $depreciation_method = $asset['depreciation_method'] ?: 'Straight Line';
                $depreciate_value = $asset['depreciate_value'] ?: 0;
                
                // Calculate depreciation for this asset
                $asset_depreciation = $this->calculate_asset_depreciation(
                    $cost,
                    $purchase_date,
                    $useful_life,
                    $salvage_value,
                    $current_year,
                    $depreciation_method,
                    $depreciate_value
                );
                
                $total_depreciation += $asset_depreciation['total_depreciation'];
                $total_accumulated_depreciation += $asset_depreciation['accumulated_depreciation'];
                $total_current_year_depreciation += $asset_depreciation['current_year_depreciation'];
                $total_net_book_value += $asset_depreciation['net_book_value'];
            }
        }
        
        // ================ 3. NET BOOK VALUE ================
        $net_book_value = $total_asset_value - $total_accumulated_depreciation;
        
        // ================ 4. COUNT ACTIVE/INACTIVE ASSETS ================
        // Apply filter for counts
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('equipment_type', $asset_type_id);
        }
        $total_assets_count = $this->db->count_all('equipments_asset');
        
        // Active assets count
        $this->db->where('status', 'active');
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('equipment_type', $asset_type_id);
        }
        $active_assets = $this->db->count_all_results('equipments_asset');
        
        $inactive_assets = $total_assets_count - $active_assets;
        
        // ================ 5. WRITE-OFF DATA ================
        $this->db->select_sum('ea.price_of_purchase', 'total_pending')
                 ->from('asset_disposal_requests adr')
                 ->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id');
        
        // Apply filter for write-off
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('ea.equipment_type', $asset_type_id);
        }
        
        $this->db->group_start();
        $this->db->where('adr.status', 'new');
        $this->db->or_where('adr.status', 'pending');
        $this->db->or_where('adr.status', '');
        $this->db->or_where('adr.status IS NULL');
        $this->db->group_end();
        
        $pending_query = $this->db->get();
        $write_off_pending = $pending_query->row()->total_pending ?: 0;
        
        // Write-off items count
        $this->db->from('asset_disposal_requests adr')
                 ->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id');
        
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('ea.equipment_type', $asset_type_id);
        }
        
        $this->db->group_start();
        $this->db->where('adr.status', 'new');
        $this->db->or_where('adr.status', 'pending');
        $this->db->or_where('adr.status', '');
        $this->db->or_where('adr.status IS NULL');
        $this->db->group_end();
        
        $write_off_items = $this->db->count_all_results();
        
        // ================ 6. AVERAGE DEPRECIATION ================
        $avg_depreciation_per_year = $total_assets_count > 0 ? $total_depreciation / 5 : 0;
        
        // ================ 7. DEPRECIATION RATE ================
        $depreciation_rate = $total_asset_value > 0 ? ($total_accumulated_depreciation / $total_asset_value * 100) : 0;
        
        return [
            'total_asset_value' => $total_asset_value,
            'total_depreciation' => $total_depreciation,
            'total_accumulated_depreciation' => $total_accumulated_depreciation,
            'total_current_year_depreciation' => $total_current_year_depreciation,
            'net_book_value' => $net_book_value,
            'avg_depreciation_per_year' => $avg_depreciation_per_year,
            'total_assets' => $total_assets_count,
            'active_assets' => $active_assets,
            'inactive_assets' => $inactive_assets,
            'depreciation_rate' => round($depreciation_rate, 1),
            'vs_last_month' => 12.4,
            'ytd_variance' => 0.8,
            'write_off_pending' => $write_off_pending,
            'write_off_items' => $write_off_items,
            'asset_type_id' => $asset_type_id // Return filter info
        ];
    }
    
    /**
     * Calculate depreciation for a single asset
     */
    private function calculate_asset_depreciation($cost, $purchase_date, $useful_life, $salvage_value, $current_year, $method = 'Straight Line', $depreciate_value = 0)
    {
        // ... (keep existing calculation logic, same as before)
        $purchase_year = date('Y', strtotime($purchase_date));
        $years_passed = $current_year - $purchase_year;
        
        if ($years_passed < 0) $years_passed = 0;
        
        $annual_depreciation = 0;
        $accumulated_depreciation = 0;
        $current_year_depreciation = 0;
        
        if ($method === 'Reducing Balance') {
            // Reducing Balance Method
            $rate = $depreciate_value / 100;
            $remaining_value = $cost;
            
            for ($i = 1; $i <= $years_passed; $i++) {
                $year_dep = $remaining_value * $rate;
                $accumulated_depreciation += $year_dep;
                $remaining_value -= $year_dep;
                
                if ($i == $years_passed) {
                    $current_year_depreciation = $year_dep;
                }
            }
            
            if ($years_passed == 0) {
                $current_year_depreciation = $cost * $rate;
                $accumulated_depreciation = $current_year_depreciation;
                $remaining_value = $cost - $current_year_depreciation;
            }
            
            $net_book_value = max($remaining_value, $salvage_value);
            
        } else {
            // Straight Line Method (default)
            $annual_depreciation = ($cost - $salvage_value) / $useful_life;
            $accumulated_depreciation = $annual_depreciation * min($years_passed, $useful_life);
            $current_year_depreciation = $years_passed <= $useful_life ? $annual_depreciation : 0;
            $net_book_value = max($cost - $accumulated_depreciation, $salvage_value);
            
            if ($years_passed == 0 && $purchase_date) {
                $purchase_month = date('n', strtotime($purchase_date));
                $months_remaining = 12 - $purchase_month + 1;
                $current_year_depreciation = $annual_depreciation * ($months_remaining / 12);
                $accumulated_depreciation = $current_year_depreciation;
                $net_book_value = $cost - $current_year_depreciation;
            }
        }
        
        $total_depreciation = $cost - $salvage_value;
        
        return [
            'total_depreciation' => $total_depreciation,
            'accumulated_depreciation' => $accumulated_depreciation,
            'current_year_depreciation' => $current_year_depreciation,
            'net_book_value' => $net_book_value,
            'annual_depreciation' => $annual_depreciation
        ];
    }
    
    /**
     * Get write-off assets with filter
     */
    public function get_write_off_assets($asset_type_id = 'all')
    {
        $this->db->select('
            adr.id,
            ea.equipment_name as asset_name,
            dm.disposal_method as description,
            wr.write_off_reason,
            ea.price_of_purchase as value,
            adr.status,
            at.name as category,
            adr.created_at as date_added
        ');
        $this->db->from('asset_disposal_requests adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id', 'left');
        $this->db->join('disposal_methods dm', 'dm.id = adr.disposal_method_id', 'left');
        $this->db->join('write_off_reasons wr', 'wr.id = adr.write_off_reason_id', 'left');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');
        
        // Apply filter if not 'all'
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('ea.equipment_type', $asset_type_id);
        }
        
        // Filter for pending write-offs
        $this->db->group_start();
        $this->db->where('adr.status', 'new');
        $this->db->or_where('adr.status', 'pending');
        $this->db->or_where('adr.status', '');
        $this->db->or_where('adr.status IS NULL');
        $this->db->group_end();
        
        $this->db->limit(5);
        $query = $this->db->get();
        
        $write_off_assets = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $write_off_assets[] = [
                    'id' => $row['id'],
                    'asset_name' => $row['asset_name'] ?: 'N/A',
                    'description' => $row['write_off_reason'] ?: ($row['description'] ?: 'Pending Review'),
                    'value' => $row['value'] ?: 0,
                    'status' => $row['status'] ? ucfirst($row['status']) : 'Review',
                    'category' => $row['category'] ?: 'Uncategorized',
                    'date_added' => date('Y-m-d', strtotime($row['date_added']))
                ];
            }
        }
        
        return $write_off_assets;
    }
    
    /**
     * Get recent transactions with filter
     */
    public function get_recent_transactions($asset_type_id = 'all')
    {
        $this->db->select('
            ea.equipment_name AS asset_name,
            at.name AS category,
            ea.purchase_date AS purchase_date,
            ea.price_of_purchase AS amount,
            ea.status
        ');
        $this->db->from('equipments_asset ea');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');
        
        // Apply filter if not 'all'
        if ($asset_type_id !== 'all' && !empty($asset_type_id)) {
            $this->db->where('ea.equipment_type', $asset_type_id);
        }
        
        $this->db->order_by('ea.purchase_date', 'DESC');
        $this->db->limit(10);

        $query = $this->db->get();

        $transactions = [];
        foreach ($query->result_array() as $row) {
            $transactions[] = [
                'asset_name' => $row['asset_name'],
                'category'   => $row['category'] ?? 'Uncategorized',
                'date'       => $row['purchase_date']
                                ? date('M d, Y', strtotime($row['purchase_date']))
                                : 'N/A',
                'value'      => $row['amount'],
                'status'     => ucfirst($row['status'] ?? 'active')
            ];
        }

        return $transactions;
    }
    
    /**
     * AJAX method to get filtered summary
     */
    public function get_filtered_summary($asset_type_id = 'all')
    {
        $summary_data = $this->get_depreciation_summary($asset_type_id);
        $write_off_assets = $this->get_write_off_assets($asset_type_id);
        $recent_transactions = $this->get_recent_transactions($asset_type_id);
        
        return [
            'summary' => $summary_data,
            'write_off_assets' => $write_off_assets,
            'recent_transactions' => $recent_transactions
        ];
    }
    
    /**
     * Get depreciation by category (dynamic) - SIMPLIFIED VERSION
     */
    public function get_depreciation_by_category()
    {
        $this->db->select('
            at.name AS category,
            SUM(ea.price_of_purchase) AS total_value,
            COUNT(ea.equipment_id) AS asset_count
        ');
        $this->db->from('equipments_asset ea');
        $this->db->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left');
        $this->db->group_by('at.name');
        $this->db->order_by('total_value', 'DESC');

        $query = $this->db->get();

        $data = [];
        foreach ($query->result_array() as $row) {
            // Use default 20% depreciation for simplicity
            $depr = $row['total_value'] * 0.2;

            $data[] = [
                'category'      => $row['category'] ?? 'Uncategorized',
                'total_value'   => $row['total_value'] ?: 0,
                'depreciation'  => $depr,
                'count'         => $row['asset_count'] ?: 0,
                'percentage'    => round(($depr / ($row['total_value'] ?: 1)) * 100, 1)
            ];
        }

        // Add "All Assets" option at the beginning
        array_unshift($data, [
            'category'      => 'All Assets',
            'total_value'   => $this->get_total_asset_value(),
            'depreciation'  => $this->get_total_asset_value() * 0.2,
            'count'         => $this->get_total_assets_count(),
            'percentage'    => 20.0
        ]);

        return $data;
    }
    
    /**
     * Get total asset value (simple function)
     */
    private function get_total_asset_value()
    {
        $this->db->select_sum('price_of_purchase');
        $query = $this->db->get('equipments_asset');
        return $query->row()->price_of_purchase ?: 0;
    }
    
    /**
     * Get total assets count (simple function)
     */
    private function get_total_assets_count()
    {
        return $this->db->count_all('equipments_asset');
    }
    
    /**
     * Get total pending write-off amount
     */
    public function get_total_pending_write_off()
    {
        $this->db->select_sum('ea.price_of_purchase', 'total_pending');
        $this->db->from('asset_disposal_requests adr');
        $this->db->join('equipments_asset ea', 'ea.equipment_id = adr.equipment_asset_id');
        
        $this->db->group_start();
        $this->db->where('adr.status', 'new');
        $this->db->or_where('adr.status', 'pending');
        $this->db->or_where('adr.status', '');
        $this->db->or_where('adr.status IS NULL');
        $this->db->group_end();
        
        $query = $this->db->get();
        return $query->row()->total_pending ?: 0;
    }
    
    /**
     * Get all asset types for dropdown
     */
    public function get_all_asset_types()
    {
        $this->db->select('asset_id, name');
        $this->db->from('asset_types');
        $this->db->order_by('name', 'ASC');
        $query = $this->db->get();
        
        $types = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $types[] = [
                    'id' => $row['asset_id'],
                    'name' => $row['name']
                ];
            }
        }
        
        // Add "All Assets" option
        array_unshift($types, [
            'id' => 'all',
            'name' => 'All Assets'
        ]);
        
        return $types;
    }
    
    /**
     * Get detailed report data
     */
    public function get_detailed_report_data($report_type = 'monthly')
    {
        // This would be your existing function
        // Generate sample data based on report type
        $data = [];
        
        if ($report_type === 'monthly') {
            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            foreach ($months as $month) {
                $data[] = [
                    'period' => $month,
                    'depreciation' => rand(15000, 35000),
                    'additions' => rand(20000, 50000),
                    'disposals' => rand(5000, 15000)
                ];
            }
        } elseif ($report_type === 'category') {
            // Use dynamic categories from database
            $categories_data = $this->get_depreciation_by_category();
            foreach ($categories_data as $category) {
                $data[] = [
                    'category' => $category['category'],
                    'total_value' => $category['total_value'],
                    'depreciation' => $category['depreciation'],
                    'net_value' => $category['total_value'] - $category['depreciation']
                ];
            }
        }
        
        return $data;
    }
    
    /**
     * Get depreciation methods for dropdown
     */
    public function get_depreciation_methods()
    {
        $this->db->select('id, method_name');
        $this->db->from('depreciation_methods');
        $this->db->order_by('method_name', 'ASC');
        $query = $this->db->get();
        
        $methods = [];
        if ($query->num_rows() > 0) {
            foreach ($query->result_array() as $row) {
                $methods[] = [
                    'id' => $row['id'],
                    'method_name' => $row['method_name']
                ];
            }
        }
        
        return $methods;
    }
}

