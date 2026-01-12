<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'warehouse/items';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// module routes (opsional, HMVC biasanya auto-resolve)
$route['items'] = 'warehouse/items';
$route['items/(:num)'] = 'warehouse/items/view/$1';
$route['inbound/scan'] = 'warehouse/inbound/scan';
$route['outbound/scan'] = 'warehouse/outbound/scan';
