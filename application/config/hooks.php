<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| Hooks
| -------------------------------------------------------------------------
| This file lets you define "hooks" to extend CI without hacking the core
| files.  Please see the user guide for info:
|
|	https://codeigniter.com/user_guide/general/hooks.html
|
*/
// MySQL 8 / Laragon compatibility: remove ONLY_FULL_GROUP_BY for legacy CI queries.
$hook['post_controller_constructor'][] = array(
    'class'    => 'SqlModeHook',
    'function' => 'removeOnlyFullGroupBy',
    'filename' => 'SqlModeHook.php',
    'filepath' => 'hooks',
    'params'   => array()
);
