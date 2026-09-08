<?php
defined('BASEPATH') or exit('No direct script access allowed');

function ams_asset_status_names()
{
    return ['SERVICEABLE', 'UNSERVICEABLE', 'MAINTENANCE', 'STORE', 'AVAILABLE'];
}

function ams_normalize_asset_status($value)
{
    $status = strtoupper(trim((string) $value));
    if (in_array($status, ams_asset_status_names(), true)) return $status;
    $legacy = [
        'IN USE' => 'SERVICEABLE', 'INUSE' => 'SERVICEABLE',
        'STANDBY' => 'AVAILABLE', 'IN STORE' => 'STORE',
        'REPAIR' => 'UNSERVICEABLE', 'FAULTY' => 'UNSERVICEABLE',
    ];
    // Unknown readiness must not imply that an asset is ready for use.
    return $legacy[$status] ?? 'UNSERVICEABLE';
}