<?php
ob_start();
ini_set('always_populate_raw_post_data', -1);

$util = include_once __DIR__.'/lib/Util.php';

spl_autoload_register(array($util, 'autoload'));

function array_get($array, $field, $default = '')
{
    if (!isset($array[$field])) {
        return $default;
    }
    return $array[$field];
}
