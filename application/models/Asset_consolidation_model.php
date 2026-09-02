<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_consolidation_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    
    /**
     * Get duplicate assets with matching criteria
     */
// public function get_duplicate_assets($filters = [])
// {
//     // First get ALL active assets without filters
//     $this->db->select('ea.*, at.name as asset_type_name, l.name as location_name');
//     $this->db->from('equipments_asset ea');
//     $this->db->join('asset_types at', 'ea.equipment_type = at.asset_id', 'left');
//     $this->db->join('locations l', 'ea.location_id = l.id', 'left');
//     $this->db->where('ea.active', 1);
//     $this->db->order_by('ea.equipment_name', 'ASC');
//     $query = $this->db->get();
//     $all_assets = $query->result_array();
    
//     // First create groups from ALL assets
//     $all_groups = $this->group_similar_assets_new($all_assets);
    
//     // Now filter groups based on user filters
//     $filtered_groups = [];
    
//     foreach ($all_groups as $group) {
//         $filtered_group = [];
        
//         foreach ($group as $asset) {
//             // Check if asset passes all filters
//             if ($this->passes_additional_filters($asset, $filters)) {
//                 $filtered_group[] = $asset;
//             }
//         }
        
//         // Only add group if at least 2 assets pass the filters
//         if (count($filtered_group) >= 2) {
//             $filtered_groups[] = $filtered_group;
//         }
//     }
    
//     return $filtered_groups;
// }

public function get_duplicate_assets($filters = [])
{
    // First get ALL active assets WITHOUT any filters
    $this->db->select('ea.*, at.name as asset_type_name, l.name as location_name');
    $this->db->from('equipments_asset ea');
    $this->db->join('asset_types at', 'ea.equipment_type = at.asset_id', 'left');
    $this->db->join('locations l', 'ea.location_id = l.id', 'left');
    $this->db->where('ea.active', 1);
    $this->db->order_by('ea.equipment_name', 'ASC');
    $query = $this->db->get();
    $all_assets = $query->result_array();
    
    // First create groups from ALL assets (without filters)
    $all_groups = $this->group_similar_assets_new($all_assets);
    
    // Now, we will return a flat array of assets that belong to groups where at least one asset passes the filters
    $flat_assets = [];
    foreach ($all_groups as $group_index => $group) {
        if (count($group) < 2) {
            continue; // Skip groups with only 1 asset
        }
        
        // Check if at least one asset in the group passes the filters
        $has_passing_asset = false;
        foreach ($group as $asset) {
            if ($this->passes_additional_filters($asset, $filters)) {
                $has_passing_asset = true;
                break;
            }
        }
        
        // If at least one asset passes, add all assets of the group to the flat array
        if ($has_passing_asset) {
            foreach ($group as $asset) {
                $asset['confidence'] = $asset['confidence'] ?? 0;
                $asset['group_index'] = $group_index;
                $asset['group_name'] = $asset['equipment_name'] . ' (Group ' . ($group_index + 1) . ')';
                $flat_assets[] = $asset;
            }
        }
    }
    
    return $flat_assets;
}

private function group_similar_assets_new($assets)
{
    $groups = [];
    $processed_ids = [];
    
    if (empty($assets) || count($assets) < 2) {
        return $groups;
    }
    
    foreach ($assets as $i => $asset) {
        $asset_id = $asset['equipment_id'] ?? $asset['id'] ?? null;
        
        if (!$asset_id || in_array($asset_id, $processed_ids)) {
            continue;
        }
        
        $group = [$asset];
        $processed_ids[] = $asset_id;
        
        foreach ($assets as $j => $compare_asset) {
            if ($i == $j) {
                continue;
            }
            
            $compare_id = $compare_asset['equipment_id'] ?? $compare_asset['id'] ?? null;
            
            if (!$compare_id || in_array($compare_id, $processed_ids)) {
                continue;
            }
            
            $confidence = $this->calculate_confidence($asset, $compare_asset);
            
            if ($confidence >= 70) {
                $compare_asset['confidence'] = $confidence;
                $group[] = $compare_asset;
                $processed_ids[] = $compare_id;
            }
        }
        
        if (count($group) > 1) {
            if (count($group) > 1) {
                $group[0]['confidence'] = isset($group[1]['confidence']) ? $group[1]['confidence'] : 0;
            }
            $groups[] = $group;
        }
    }
    
    return $groups;
}


private function group_similar_assets_with_filters($assets, $filters)
{
    $groups = [];
    $processed_ids = [];
    
    if (empty($assets) || count($assets) < 2) {
        return $groups;
    }
    
    foreach ($assets as $i => $asset) {
        $asset_id = $asset['equipment_id'] ?? $asset['id'] ?? null;
        
        if (!$asset_id || in_array($asset_id, $processed_ids)) {
            continue;
        }
        
        $group = [$asset];
        $processed_ids[] = $asset_id;
        
        // Find similar assets based on NEW criteria
        foreach ($assets as $j => $compare_asset) {
            if ($i == $j) {
                continue;
            }
            
            $compare_id = $compare_asset['equipment_id'] ?? $compare_asset['id'] ?? null;
            
            if (!$compare_id || in_array($compare_id, $processed_ids)) {
                continue;
            }
            
            // Calculate confidence percentage
            $confidence = $this->calculate_confidence($asset, $compare_asset);
            
            // REMOVE THE FILTER CHECK FROM HERE
            // $passes_filters = $this->passes_additional_filters($compare_asset, $filters);
            
            if ($confidence >= 70) { // Remove the && $passes_filters condition
                // Add confidence to asset data for display
                $compare_asset['confidence'] = $confidence;
                $group[] = $compare_asset;
                $processed_ids[] = $compare_id;
            }
        }
        
        if (count($group) > 1) {
            // Add confidence to first asset as well
            $group[0]['confidence'] = isset($group[1]['confidence']) ? $group[1]['confidence'] : 0;
            $groups[] = $group;
        }
    }
    
    return $groups;
}

        private function passes_additional_filters($asset, $filters)
    {
        // If no filters applied, return true
        if (empty($filters['search']) && empty($filters['asset_type']) && 
            empty($filters['location_id']) && empty($filters['date_from']) && empty($filters['date_to'])) {
            return true;
        }
        
        $passes = true;
        
        // Check search filter
        if (!empty($filters['search'])) {
            $search = strtolower($filters['search']);
            $asset_name = strtolower($asset['equipment_name'] ?? '');
            $asset_reg = strtolower($asset['equipment_registration'] ?? '');
            $asset_serial = strtolower($asset['serial_number'] ?? '');
            $asset_type_name = strtolower($asset['asset_type_name'] ?? '');
            $asset_location = strtolower($asset['location_name'] ?? '');
            
            if (strpos($asset_name, $search) === false && 
                strpos($asset_reg, $search) === false && 
                strpos($asset_serial, $search) === false && 
                strpos($asset_type_name, $search) === false && 
                strpos($asset_location, $search) === false) {
                $passes = false;
            }
        }
        
        // Check asset type filter
        if (!empty($filters['asset_type']) && isset($asset['equipment_type'])) {
            if ($asset['equipment_type'] != $filters['asset_type']) {
                $passes = false;
            }
        }
        
        // Check location filter
        if (!empty($filters['location_id']) && isset($asset['location_id'])) {
            if ($asset['location_id'] != $filters['location_id']) {
                $passes = false;
            }
        }
        
        // Check date range filter
        if (!empty($filters['date_from']) && isset($asset['purchase_date'])) {
            $purchase_date = strtotime($asset['purchase_date']);
            $date_from = strtotime($filters['date_from']);
            if ($purchase_date < $date_from) {
                $passes = false;
            }
        }
        
        if (!empty($filters['date_to']) && isset($asset['purchase_date'])) {
            $purchase_date = strtotime($asset['purchase_date']);
            $date_to = strtotime($filters['date_to']);
            if ($purchase_date > $date_to) {
                $passes = false;
            }
        }
        
        return $passes;
    }

    private function calculate_confidence($asset1, $asset2)
    {
        $total_fields = 0;
        $matched_fields = 0;
        
        // List of fields to compare (exact match required)
        $exact_match_fields = [
            'equipment_registration',
            'serial_number', 
            'location_id',
            'equipment_type',
            'price_of_purchase',
            'purchase_date',
            'active',
            'date_installed'
        ];
        
        // 1. Equipment Name - LIKE match (partial)
        $total_fields++;
        $name1 = strtolower(trim($asset1['equipment_name'] ?? ''));
        $name2 = strtolower(trim($asset2['equipment_name'] ?? ''));
        
        if (!empty($name1) && !empty($name2)) {
            // Check if one contains the other (partial match)
            if (strpos($name1, $name2) !== false || strpos($name2, $name1) !== false) {
                $matched_fields++;
            } else {
                // Also check similarity percentage
                similar_text($name1, $name2, $similarity);
                if ($similarity >= 60) {
                    $matched_fields++;
                }
            }
        }
        
        // 2. Exact match fields
        foreach ($exact_match_fields as $field) {
            $val1 = $asset1[$field] ?? null;
            $val2 = $asset2[$field] ?? null;
            
            // Skip if both are null/empty
            if (empty($val1) && empty($val2)) {
                continue;
            }
            
            $total_fields++;
            
            // Convert to string for comparison
            $val1_str = (string) $val1;
            $val2_str = (string) $val2;
            
            // For dates, normalize format
            if (in_array($field, ['purchase_date', 'date_installed']) && !empty($val1) && !empty($val2)) {
                $val1_str = date('Y-m-d', strtotime($val1));
                $val2_str = date('Y-m-d', strtotime($val2));
            }
            
            // Exact match comparison
            if ($val1_str === $val2_str) {
                $matched_fields++;
            }
        }
        
        // Calculate confidence percentage
        if ($total_fields > 0) {
            $confidence = ($matched_fields / $total_fields) * 100;
            return round($confidence);
        }
        
        return 0;
    }

    
    /**
     * Group similar assets based on criteria
     */
    private function group_similar_assets($assets)
    {
        $groups = [];
        $processed_ids = [];
        
        if (empty($assets) || count($assets) < 2) {
            return $groups;
        }
        
        foreach ($assets as $i => $asset) {
            $asset_id = $asset['equipment_id'] ?? $asset['id'] ?? null;
            
            if (!$asset_id || in_array($asset_id, $processed_ids)) {
                continue;
            }
            
            $group = [$asset];
            $processed_ids[] = $asset_id;
            
            // Find similar assets
            foreach ($assets as $j => $compare_asset) {
                if ($i == $j) {
                    continue;
                }
                
                $compare_id = $compare_asset['equipment_id'] ?? $compare_asset['id'] ?? null;
                
                if (!$compare_id || in_array($compare_id, $processed_ids)) {
                    continue;
                }
                
                if ($this->is_similar_asset($asset, $compare_asset)) {
                    $group[] = $compare_asset;
                    $processed_ids[] = $compare_id;
                }
            }
            
            if (count($group) > 1) {
                $groups[] = $group;
            }
        }
        
        return $groups;
    }
    
    /**
     * Check if two assets are similar - UPDATED WITH LOCATION
     */
    private function is_similar_asset($asset1, $asset2)
    {
        $similarity_score = 0;
        
        // 1. Check equipment name similarity (case insensitive)
        $name1 = strtolower(trim($asset1['equipment_name'] ?? ''));
        $name2 = strtolower(trim($asset2['equipment_name'] ?? ''));
        
        if (!empty($name1) && !empty($name2)) {
            similar_text($name1, $name2, $name_percent);
            
            if ($name_percent >= 60) {
                $similarity_score += $name_percent;
            }
        }
        
        // 2. Check if names are exactly the same (case insensitive)
        if ($name1 === $name2 && !empty($name1)) {
            $similarity_score += 50;
        }
        
        // 3. Check if names contain each other (partial match)
        if (!empty($name1) && !empty($name2)) {
            if (strpos($name1, $name2) !== false || strpos($name2, $name1) !== false) {
                $similarity_score += 40;
            }
        }
        
        // 4. Check if serial numbers match (exact or partial)
        $serial1 = strtolower(trim($asset1['serial_number'] ?? ''));
        $serial2 = strtolower(trim($asset2['serial_number'] ?? ''));
        
        if (!empty($serial1) && !empty($serial2)) {
            if ($serial1 === $serial2) {
                $similarity_score += 50;
            } elseif (strpos($serial1, $serial2) !== false || strpos($serial2, $serial1) !== false) {
                $similarity_score += 30;
            }
        }
        
        // 5. Check if registration numbers match (exact or partial)
        $reg1 = strtolower(trim($asset1['equipment_registration'] ?? ''));
        $reg2 = strtolower(trim($asset2['equipment_registration'] ?? ''));
        
        if (!empty($reg1) && !empty($reg2)) {
            if ($reg1 === $reg2) {
                $similarity_score += 40;
            } elseif (strpos($reg1, $reg2) !== false || strpos($reg2, $reg1) !== false) {
                $similarity_score += 20;
            }
        }
        
        // 6. Same asset type
        if (isset($asset1['equipment_type']) && isset($asset2['equipment_type'])) {
            if ($asset1['equipment_type'] == $asset2['equipment_type']) {
                $similarity_score += 20;
            }
        }
        
        // 7. Same location (NEW)
        if (isset($asset1['location_id']) && isset($asset2['location_id'])) {
            if ($asset1['location_id'] == $asset2['location_id']) {
                $similarity_score += 15;
            }
        }
        
        // 8. Check purchase dates similarity (within 1 year)
        if (!empty($asset1['purchase_date']) && !empty($asset2['purchase_date'])) {
            $date1 = strtotime($asset1['purchase_date']);
            $date2 = strtotime($asset2['purchase_date']);
            
            if ($date1 && $date2) {
                $diff_days = abs($date1 - $date2) / (60 * 60 * 24);
                if ($diff_days < 365) {
                    $similarity_score += 10;
                }
            }
        }
        
        // 9. Check price similarity (within 20%)
        if (!empty($asset1['price_of_purchase']) && !empty($asset2['price_of_purchase'])) {
            $price1 = floatval($asset1['price_of_purchase']);
            $price2 = floatval($asset2['price_of_purchase']);
            
            if ($price1 > 0 && $price2 > 0) {
                $price_diff = abs($price1 - $price2) / max($price1, $price2) * 100;
                if ($price_diff < 20) {
                    $similarity_score += 10;
                }
            }
        }
        
        // 10. Same equipment status
        if (isset($asset1['equipment_status']) && isset($asset2['equipment_status'])) {
            if ($asset1['equipment_status'] == $asset2['equipment_status']) {
                $similarity_score += 5;
            }
        }
        
        // Threshold for similarity - lowered to 40 for better detection
        return $similarity_score >= 40;
    }
    
    /**
     * Get all asset types for filter
     */
    public function get_asset_types()
    {
        $this->db->select('asset_id as id, name');
        $this->db->from('asset_types');
        $this->db->where('active', 1);
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }
    
    /**
     * Get all locations for filter
     */
    public function get_locations()
    {
        $this->db->select('id, name');
        $this->db->from('locations');
        $this->db->order_by('name', 'ASC');
        return $this->db->get()->result_array();
    }
    
    /**
     * Get asset by ID
     */
    public function get_asset_by_id($id)
    {
        $this->db->select('ea.*, at.name as asset_type_name, l.name as location_name');
        $this->db->from('equipments_asset ea');
        $this->db->join('asset_types at', 'ea.equipment_type = at.asset_id', 'left');
        $this->db->join('locations l', 'ea.location_id = l.id', 'left');
        $this->db->where('ea.equipment_id', $id);
        return $this->db->get()->row_array();
    }
    
    /**
     * Merge assets
     */
    public function merge_assets($primary_id, $merge_ids, $final_data)
    {
        $this->db->trans_start();
        
        // Update primary asset with final values
        $this->db->where('equipment_id', $primary_id);
        $update_data = [
            'equipment_name' => $final_data['equipment_name'] ?? '',
            'equipment_registration' => $final_data['equipment_registration'] ?? '',
            'serial_number' => $final_data['serial_number'] ?? '',
            'purchase_date' => $final_data['purchase_date'] ?? NULL,
            'price_of_purchase' => $final_data['price_of_purchase'] ?? 0,
            'equipment_type' => $final_data['equipment_type'] ?? NULL,
            'location_id' => $final_data['location_id'] ?? NULL,
            'date_installed' => $final_data['date_installed'] ?? NULL,
            't_updated' => date('Y-m-d H:i:s')
        ];
        
        $this->db->update('equipments_asset', $update_data);
        
        // Mark other assets as inactive (active = 0)
        if (!empty($merge_ids) && is_array($merge_ids)) {
            $this->db->where_in('equipment_id', $merge_ids);
            $this->db->update('equipments_asset', [
                'active' => 0,
                't_updated' => date('Y-m-d H:i:s')
            ]);
        }
        
        $this->db->trans_complete();
        
        return $this->db->trans_status();
    }
}
