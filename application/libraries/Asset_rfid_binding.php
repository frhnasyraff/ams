<?php
defined('BASEPATH') or exit('No direct script access allowed');

/** Serializes API bindings so concurrent readers cannot reuse one tag. */
class Asset_rfid_binding
{
    private $db;

    public function __construct($db = null)
    {
        $this->db = $db ?: get_instance()->db;
    }

    public function bind($assetId, $tag)
    {
        $tag = strtoupper(trim((string) $tag));
        if (!ctype_digit((string) $assetId) || (int) $assetId < 1 || $tag === '' || $tag === 'NULL') {
            return [400, 'Valid asset ID and RFID are required', []];
        }
        // One lock per database also protects simultaneous bindings of two
        // different tags to the same asset. Release before response helpers exit.
        $lockName = 'ams:rfid:' . substr(hash('sha256', $this->db->database), 0, 40);
        $lock = $this->db->query('SELECT GET_LOCK(?, 5) AS acquired', [$lockName])->row();
        if (!$lock || (int) $lock->acquired !== 1) {
            return [503, 'Another binding is in progress. Please retry.', []];
        }
        try {
            return $this->saveBinding($assetId, $tag);
        } finally {
            $this->db->query('SELECT RELEASE_LOCK(?)', [$lockName]);
        }
    }

    private function saveBinding($assetId, $tag)
    {
        $asset = $this->db->select('equipment_id, rfid')->from('equipments_asset')
            ->where('equipment_id', $assetId)->get()->row();
        if (!$asset) {
            return [404, 'Asset not found', []];
        }
        // Include inactive assets: a tag remains reserved until explicitly unbound.
        $owner = $this->db->select('equipment_id')->from('equipments_asset')
            ->where('UPPER(TRIM(rfid)) = ' . $this->db->escape($tag), null, false)
            ->where('equipment_id !=', $assetId)->get()->row();
        if ($owner) {
            return [409, 'RFID is already bound to another asset. Use a different tag.', []];
        }
        $current = strtoupper(trim((string) $asset->rfid));
        if ($current !== '' && $current !== 'NULL' && $current !== $tag) {
            return [409, 'This asset already has an RFID. Unbind it before assigning a different tag.', []];
        }
        // Retrying the same binding is safe and does not create a second write.
        if ($current !== $tag) {
            $updated = $this->db->where('equipment_id', $assetId)
                ->update('equipments_asset', ['rfid' => $tag]);
            if (!$updated) {
                return [500, 'RFID could not be saved', []];
            }
        }
        $saved = $this->db->select('equipment_id, rfid')->from('equipments_asset')
            ->where('equipment_id', $assetId)->get()->row();
        if (!$saved || strtoupper(trim((string) $saved->rfid)) !== $tag) {
            return [500, 'RFID binding could not be confirmed', []];
        }
        return [200, 'RFID bound successfully', [
            'status' => true,
            'equipment_id' => (string) $saved->equipment_id,
            'rfid' => $saved->rfid,
        ]];
    }
}