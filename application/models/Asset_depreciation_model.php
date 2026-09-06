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
    $cost = max(0, (float)$cost);
    $salvage = min($cost, max(0, (float)$salvage));
    $life = max(1, (int)$life_years);
    $year = (int)($selectedYear ?: date('Y'));
    $purchase = new DateTime($purchase_date);
    $purchaseMonth = (int)$purchase->format('Y') * 12 + (int)$purchase->format('n') - 1;
    $endMonth = $year * 12 + ($year === (int)date('Y') ? (int)date('n') : 12) - 1;
    $months = max(0, min($life * 12, $endMonth - $purchaseMonth + 1));
    $monthlyArray = array_fill(0, 12, 0.0);
    $remaining = $cost;
    $annual = ($cost - $salvage) / $life;
    $rate = min(1, max(0, (float)$depreciate_value / 100));
    $annualBalance = $cost;
    for ($i = 0; $i < $months; $i++) {
        if ($i % 12 === 0) $annualBalance = $remaining;
        $amount = $depreciation_method === 'Reducing Balance' ? $annualBalance * $rate / 12 : $annual / 12;
        $amount = round(min(max(0, $remaining - $salvage), $amount), 2);
        if ($depreciation_method !== 'Reducing Balance' && $i === $life * 12 - 1) $amount = round($remaining - $salvage, 2);
        $remaining = max($salvage, $remaining - $amount);
        $absoluteMonth = $purchaseMonth + $i;
        if (intdiv($absoluteMonth, 12) === $year) $monthlyArray[$absoluteMonth % 12] = $amount;
    }
    return [
        'cost' => round($cost, 2),
        'annual' => round($depreciation_method === 'Reducing Balance' ? $annualBalance * $rate : $annual, 2),
        'monthly' => round($depreciation_method === 'Reducing Balance' ? $annualBalance * $rate / 12 : $annual / 12, 2),
        'monthly_array' => $monthlyArray,
        'accumulated' => round($cost - $remaining, 2),
        'current_year' => round(array_sum($monthlyArray), 2),
        'net_book' => $endMonth < $purchaseMonth ? 0 : round($remaining, 2),
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
