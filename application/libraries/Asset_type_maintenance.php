<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** Read-only metadata for asset forms. Optional component data must not hide maintenance settings. */
class Asset_type_maintenance
{
    private $db;
    public function __construct($params = [])
    {
        $this->db = $params['db'] ?? get_instance()->db;
    }

    public static function can_lookup($masterAccess, $assetListAccess, $assetAddAccess, $assetEditAccess)
    {
        return $masterAccess || ($assetListAccess && ($assetAddAccess || $assetEditAccess));
    }

    public static function defaults($frequency, $reminder)
    {
        $result = [];
        foreach (['maintenance_frequency_year' => [$frequency, 1, 365],
                  'maintenance_reminder_days' => [$reminder, 0, 3650]] as $field => $rule) {
            [$value, $min, $max] = $rule;
            if ($value === null || $value === '') {
                $result[$field] = null;
            } elseif (filter_var($value, FILTER_VALIDATE_INT) === false || (int) $value < $min || (int) $value > $max) {
                throw new InvalidArgumentException('Maintenance frequency must be 1-365 services per year; reminder must be 0-3650 days.');
            } else {
                $result[$field] = (int) $value;
            }
        }
        return $result;
    }

    public function lookup($id)
    {
        if (filter_var($id, FILTER_VALIDATE_INT) === false || (int) $id < 1) {
            throw new InvalidArgumentException('Select a valid asset type.');
        }
        if (!$this->db->table_exists('asset_types') || !$this->db->field_exists('maintenance', 'asset_types')) {
            throw new RuntimeException('Asset Type maintenance schema is missing. Apply patch_asset_type_maintenance.sql.', 503);
        }
        $type = $this->db->get_where('asset_types', ['asset_id' => (int) $id])->row();
        if (!$type) {
            throw new RuntimeException('Asset type not found.', 404);
        }
        $result = [
            'status' => true,
            'asset_id' => (int) $id,
            'maintenance' => $type->maintenance === null ? null : (int) $type->maintenance,
            'calibration' => (int) ($type->calibration ?? 0),
            'manufacturer' => $type->manufacturer ?? null,
            'vpn' => $type->vendor_part_number ?? null,
            'maintenance_frequency_year' => $type->maintenance_frequency_year ?? null,
            'maintenance_reminder_days' => $type->maintenance_reminder_days ?? null,
            'items' => [],
            'warnings' => [],
        ];
        // The settings above are usable even when the deployment lacks optional component tables.
        if (!$this->db->table_exists('asset_type_items') || !$this->db->table_exists('item_types')) {
            $result['warnings'][] = 'Component templates are unavailable. Apply patch_asset_type_maintenance.sql.';
            return $result;
        }
        $links = $this->db->get_where('asset_type_items', ['asset_type_id' => (int) $id])->result();
        if (!$links) {
            return $result;
        }
        $ids = array_values(array_unique(array_map(function ($link) { return (int) $link->item_type_id; }, $links)));
        $types = $this->db->where_in('id', $ids)->get('item_types')->result();
        $byId = [];
        foreach ($types as $item) { $byId[(int) $item->id] = $item; }
        $manufacturers = $this->names('vendor_manufacturing_number', 'manufacturer_name', $types, 'manufacturer');
        $parts = $this->names('vendor_part_number', 'part_number', $types, 'vendor_part_number');
        foreach ($links as $link) {
            $item = $byId[(int) $link->item_type_id] ?? null;
            if (!$item) { continue; }
            $result['items'][] = [
                'item_type_id' => (int) $link->item_type_id,
                'qty' => (int) $link->quantity,
                'manufacturer' => $manufacturers[(int) ($item->manufacturer ?? 0)] ?? null,
                'vendor_part_number' => $parts[(int) ($item->vendor_part_number ?? 0)] ?? null,
                'calibration' => (int) ($item->calibration ?? 0),
                'maintenance' => (int) ($item->maintenance ?? 0),
            ];
        }
        return $result;
    }

    private function names($table, $field, $items, $linkField)
    {
        $ids = array_values(array_filter(array_unique(array_map(function ($item) use ($linkField) {
            return (int) ($item->$linkField ?? 0);
        }, $items))));
        if (!$ids || !$this->db->table_exists($table) || !$this->db->field_exists($field, $table)) { return []; }
        $rows = $this->db->select('id, ' . $field)->where_in('id', $ids)->get($table)->result();
        $result = [];
        foreach ($rows as $row) { $result[(int) $row->id] = $row->$field; }
        return $result;
    }
}
