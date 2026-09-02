<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Asset_depreciation_model extends CI_Model
{
    /**
     * ASSET TYPES + COUNT
     */
    public function get_asset_types_with_count()
    {
        $this->db->select('asset_id, name');
        $types = $this->db->get('asset_types')->result_array();

        foreach ($types as &$type) {
            $this->db->where('equipment_type', $type['asset_id']);
            $type['asset_count'] = $this->db->count_all_results('equipments_asset');
        }

        return $types;
    }

    /**
     * ASSET TYPE INFO
     */
    public function get_asset_type_info($asset_type_id)
    {
        return $this->db
            ->select('at.*, dm.depreciation_method as depreciation_method')
            ->from('asset_types at')
            ->join('depreciation_methods dm', 'dm.id = at.depreciation_method_id', 'left')
            ->where('at.asset_id', $asset_type_id)
            ->get()
            ->row_array();
    }

    /**
     * ASSETS BY TYPE
     */
    public function get_assets_by_type($asset_type_id)
    {
        return $this->db
            ->select('
                ea.*,
                at.useful_life_years,
                at.salvage_value,
                at.depreciate_value,
                at.depreciation_method_id,
                dm.depreciation_method
            ')
            ->from('equipments_asset ea')
            ->join('asset_types at', 'ea.equipment_type = at.asset_id')
            ->join('depreciation_methods dm', 'dm.id = at.depreciation_method_id', 'left')
            ->where('ea.equipment_type', $asset_type_id)
            ->get()
            ->result_array();
    }

public function calculate_depreciation($cost, $purchase_date, $life_years, $salvage, $selectedYear = null,
    $depreciation_method = 'Straight Line',
    $depreciate_value = null)
{
    $cost = (float)$cost;
    $salvage = (float)$salvage;
    $life = max((int)$life_years, 1);
    
    // System date and selected year
    $systemDate = new DateTime();
    $systemYear = (int)$systemDate->format('Y');
    $currentYear = $selectedYear ? (int)$selectedYear : $systemYear;
    $currentMonth = (int)$systemDate->format('n');
    
    $purchase = new DateTime($purchase_date);
    $purchaseYear = (int)$purchase->format('Y');
    $purchaseMonth = (int)$purchase->format('n');
    
    // Monthly array initialize
    $monthlyArray = array_fill(0, 12, 0);
    
    // Check which depreciation method to use
    if ($depreciation_method == 'Reducing Balance') {
        $ratePercent = (float)$depreciate_value;
        $rate = $ratePercent / 100;

        $yearsPassed = max(0, $currentYear - $purchaseYear);

        $accumulated = 0;
        $remainingValue = $cost;

        // Calculate depreciation for each year up to the current year
        for ($i = 1; $i <= $yearsPassed; $i++) {
            $yearDep = $remainingValue * $rate;
            $accumulated += $yearDep;
            $remainingValue -= $yearDep;
        }

        // Current year depreciation for monthly calculation
        $currentYearDep = $remainingValue * $rate;
        $monthlyDep = $currentYearDep / 12;
        
        // For selected year, show monthly depreciation
        $startMonth = ($currentYear == $purchaseYear) ? $purchaseMonth : 1;
        $endMonth = ($currentYear == $purchaseYear + $life - 1) ? $purchaseMonth - 1 : 12;
        if ($endMonth < 1) $endMonth = 12;

        for ($m = $startMonth; $m <= $endMonth; $m++) {
            $monthlyArray[$m-1] = $monthlyDep;
        }

        $netBook = max(0, $remainingValue - $currentYearDep);
        
    } else {
        // STRAIGHT LINE METHOD (default)
        $annualDep = ($cost - $salvage) / $life;
        $monthlyDep = $annualDep / 12;
        
        // Depreciation end date
        $depEndDate = clone $purchase;
        $depEndDate->add(new DateInterval('P' . $life . 'Y'));
        $depEndDate->sub(new DateInterval('P1D'));
        
        $depEndYear = (int)$depEndDate->format('Y');
        $depEndMonth = (int)$depEndDate->format('n');
        
        // Calculate TOTAL accumulated depreciation up to current year
        $accumulated = 0;
        $currentYearDep = 0;
        
        // Loop through each year from purchase year to current year
        for ($year = $purchaseYear; $year <= $currentYear; $year++) {
            // Skip if year is after depreciation end
            if ($year > $depEndYear) break;
            
            // Calculate months for this year
            $startMonth = ($year == $purchaseYear) ? $purchaseMonth : 1;
            $endMonth = ($year == $depEndYear) ? $depEndMonth : 12;
            
            // Only calculate for months that have passed in current year
            if ($year == $currentYear) {
                // For current year, only show months that have passed (or all if selected year is in past)
                $monthsToShow = $endMonth;
                if ($currentYear == $systemYear) {
                    $monthsToShow = min($currentMonth, $endMonth);
                }
                
                for ($m = $startMonth; $m <= $monthsToShow; $m++) {
                    $monthlyArray[$m-1] = $monthlyDep;
                    $currentYearDep += $monthlyDep;
                    $accumulated += $monthlyDep;
                }
            } else {
                // For previous years, add full year's depreciation
                $monthsThisYear = ($endMonth - $startMonth + 1);
                $accumulated += ($monthlyDep * $monthsThisYear);
            }
        }
        
        $netBook = max($salvage, $cost - $accumulated);
    }
    
    return [
        'cost' => round($cost, 2),
        'annual' => round($annualDep ?? 0, 2),
        'monthly' => round($monthlyDep ?? 0, 2),
        'monthly_array' => array_map(fn($v) => round($v, 2), $monthlyArray),
        'accumulated' => round($accumulated ?? 0, 2),
        'current_year' => round($currentYearDep ?? 0, 2),
        'net_book' => round($netBook ?? $cost, 2),
    ];
}

    /**
     * GET DEPRECIATION METHODS
     */
    public function get_depreciation_methods()
    {
        return $this->db
            ->select('id, depreciation_method as method_name')
            ->get('depreciation_methods')
            ->result_array();
    }

    /**
     * UPDATE POLICY
     */
    public function update_asset_type_policy($asset_type_id, $data)
    {
        return $this->db
            ->where('asset_id', $asset_type_id)
            ->update('asset_types', $data);
    }

    /**
     * DISPOSAL METHODS (legacy - remove if not needed)
     */
    public function get_disposal_methods()
    {
        return $this->db->get('disposal_methods')->result_array();
    }
}
