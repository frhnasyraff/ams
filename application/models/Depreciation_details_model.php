<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Depreciation_details_model extends CI_Model
{
    public function get_asset_types()
    {
        return $this->db
            ->select('asset_id, name')
            ->from('asset_types')
            ->where('active', 1)
            ->get()->result_array();
    }

    public function get_asset_summary($asset_type_id)
    {
        $result = $this->db
            ->select('name')
            ->from('asset_types')
            ->where('asset_id', $asset_type_id)
            ->get()->row_array();
        
        return $result ? $result : ['name' => 'Unknown Asset'];
    }
    
    public function get_first_asset($asset_type_id)
    {
        $result = $this->db
            ->select('equipment_id, purchase_date, price_of_purchase')
            ->from('equipments_asset')
            ->where('equipment_type', $asset_type_id)
            ->order_by('purchase_date', 'asc')
            ->limit(1)
            ->get()->row_array();
        
        return $result ? $result : [
            'equipment_id' => 'N/A',
            'purchase_date' => date('Y-m-d'),
            'price_of_purchase' => 0
        ];
    }

    public function get_depreciation_params($asset_type_id)
    {
        $result = $this->db
            ->select('
                COALESCE(dm.depreciation_method, "Straight-Line") as depreciation_method,
                COALESCE(at.useful_life_years, 5) as useful_life_years,
                COALESCE(at.salvage_value, 0) as salvage_value,
                COALESCE(at.depreciate_value, 0) as depreciate_value
            ')
            ->from('asset_types at')
            ->join('depreciation_methods dm', 'dm.id = at.depreciation_method_id', 'left')
            ->where('at.asset_id', $asset_type_id)
            ->get()->row_array();
        
        return $result ? $result : [
            'depreciation_method' => 'Straight-Line',
            'useful_life_years' => 5,
            'salvage_value' => 0,
            'depreciate_value' => 0
        ];
    }
    
    public function calculate_current_book_value($cost, $params, $purchase_date)
    {
        $cost = floatval($cost);
        
        if ($cost <= 0) {
            return 0;
        }
        
        $method = $params['depreciation_method'] ?? 'Straight-Line';
        $purchase_date = new DateTime($purchase_date);
        $current_date = new DateTime();
        
        // Check if asset is not purchased yet
        if ($purchase_date > $current_date) {
            return $cost;
        }
        
        if ($method === 'Straight-Line') {
            return $this->calculate_straight_line_book_value($cost, $params, $purchase_date, $current_date);
        } else {
            // For Reducing Balance/Research Balance
            return $this->calculate_reducing_balance_book_value($cost, $params, $purchase_date, $current_date);
        }
    }
    
    private function calculate_straight_line_book_value($cost, $params, $purchase_date, $current_date)
    {
        $salvage_value = floatval($params['salvage_value'] ?? 0);
        $useful_life_years = intval($params['useful_life_years'] ?? 1);
        
        if ($useful_life_years <= 0) {
            return max($cost, $salvage_value);
        }
        
        // Calculate annual depreciation
        $annual_depreciation = ($cost - $salvage_value) / $useful_life_years;
        
        // Calculate months passed since purchase
        $interval = $purchase_date->diff($current_date);
        $total_months_passed = ($interval->y * 12) + $interval->m;
        
        // Adjust for days (if more than 15 days, count as a full month)
        if ($interval->d > 15) {
            $total_months_passed += 1;
        }
        
        // Calculate total depreciation
        $monthly_depreciation = $annual_depreciation / 12;
        $total_depreciation = $monthly_depreciation * $total_months_passed;
        
        // Calculate book value
        $book_value = $cost - $total_depreciation;
        
        // Ensure book value doesn't go below salvage value
        return max(round($book_value, 2), $salvage_value);
    }

    public function get_asset_by_id($asset_id)
{
    $result = $this->db
        ->select('ea.*, at.name as equipment_type_name')
        ->from('equipments_asset ea')
        ->join('asset_types at', 'at.asset_id = ea.equipment_type', 'left')
        ->where('ea.equipment_id', $asset_id)
        ->get()->row_array();
    
    return $result ? $result : [
        'equipment_id' => 'N/A',
        'equipment_name' => 'Unknown Asset',
        'purchase_date' => date('Y-m-d'),
        'price_of_purchase' => 0,
        'equipment_type' => null
    ];
}
    
    private function calculate_reducing_balance_book_value($cost, $params, $purchase_date, $current_date)
    {
        $depreciate_value = floatval($params['depreciate_value'] ?? 0);
        
        if ($depreciate_value <= 0) {
            return $cost;
        }
        
        // Calculate months passed
        $interval = $purchase_date->diff($current_date);
        $total_months_passed = ($interval->y * 12) + $interval->m;
        
        // For reducing balance, depreciation is applied at the end of each year
        $years_passed = floor($total_months_passed / 12);
        
        $book_value = $cost;
        
        for ($i = 0; $i < $years_passed; $i++) {
            $depreciation = $book_value * ($depreciate_value / 100);
            $book_value -= $depreciation;
        }
        
        return round($book_value, 2);
    }
    
    public function calculate_schedule($asset, $params)
    {
        if (empty($asset) || empty($params)) {
            return [];
        }
        
        $cost = floatval($asset['price_of_purchase'] ?? 0);
        $purchase_date = new DateTime($asset['purchase_date'] ?? date('Y-m-d'));
        $purchase_year = (int)$purchase_date->format('Y');
        $purchase_month = (int)$purchase_date->format('n'); // 1-12
        
        $method = $params['depreciation_method'] ?? 'Straight Line';
        $salvage_value = floatval($params['salvage_value'] ?? 0);
        $useful_life_years = intval($params['useful_life_years'] ?? 5);
        $depreciate_value = floatval($params['depreciate_value'] ?? 0);
        
        $current_date = new DateTime();
        $current_year = (int)$current_date->format('Y');
        
        $rows = [];
        $accumulated_depreciation = 0;
        $book_value = $cost;
        
        $method = strtolower(str_replace('-', ' ', $params['depreciation_method'] ?? 'straight line'));

        if ($method === 'straight line') {

            $salvage_value = floatval($params['salvage_value']);
            $useful_life_years = intval($params['useful_life_years']);

            $total_months = $useful_life_years * 12;
            $monthly_dep = ($cost - $salvage_value) / $total_months;

            $current = clone $purchase_date;
            $end = new DateTime(); // today

            $book_value = $cost;
            $accumulated = 0;
            $yearly = [];

            while ($current <= $end && $book_value > $salvage_value) {

                $year = $current->format('Y');

                if (!isset($yearly[$year])) {
                    $yearly[$year] = [
                        'year' => $year,
                        'beginning' => $book_value,
                        'depreciation' => 0,
                        'accumulated' => 0,
                        'ending' => 0
                    ];
                }

                $dep = min($monthly_dep, $book_value - $salvage_value);

                $yearly[$year]['depreciation'] += $dep;
                $accumulated += $dep;
                $book_value -= $dep;

                $yearly[$year]['accumulated'] = $accumulated;
                $yearly[$year]['ending'] = $book_value;

                $current->modify('+1 month');
            }

            foreach ($yearly as $row) {
                $rows[] = [
                    'year' => $row['year'],
                    'beginning' => round($row['beginning'], 2),
                    'depreciation' => round($row['depreciation'], 2),
                    'accumulated' => round($row['accumulated'], 2),
                    'ending' => round($row['ending'], 2),
                ];
            }
        } else {
            // For Reducing Balance/Research Balance Method
            $year_start = $purchase_year;
            
            for ($year = $year_start; $year <= min($purchase_year + $useful_life_years - 1, $current_year); $year++) {
                $beginning_value = $book_value;
                
                // Calculate depreciation for this year
                $annual_depreciation = $beginning_value * ($depreciate_value / 100);
                
                // For first year, adjust if purchased mid-year
                if ($year == $purchase_year && $purchase_month > 1) {
                    $months_in_year = 12 - $purchase_month + 1;
                    $annual_depreciation = $annual_depreciation * ($months_in_year / 12);
                }
                
                $accumulated_depreciation += $annual_depreciation;
                $ending_value = $beginning_value - $annual_depreciation;
                
                $rows[] = [
                    'year' => $year,
                    'beginning' => round($beginning_value, 2),
                    'depreciation' => round(-$annual_depreciation, 2),
                    'accumulated' => round(-$accumulated_depreciation, 2),
                    'ending' => round($ending_value, 2)
                ];
                
                $book_value = $ending_value;
                
                // Stop if book value is too low
                if ($book_value < ($cost * 0.05)) { // Stop at 5% of original cost
                    break;
                }
            }
        }
        
        return $rows;
    }
}

