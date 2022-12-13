<?php

defined('BASEPATH') or exit('No direct script access allowed');

$route['licences/licence/(:num)/(:any)'] = 'licence/index/$1/$2';

/**
 * @since 2.0.0
 */
$route['licences/list'] = 'mylicence/list';
$route['licences/show/(:num)/(:any)'] = 'mylicence/show/$1/$2';
$route['licences/display/(:num)/(:num)/(:num)/(:any)'] = 'mylicence/display/$1/$2/$3/$4';
$route['licences/item/(:num)/pdf/(:num)'] = 'mylicence/item_pdf/$1/$2';
$route['licences/item/(:num)/show/(:num)'] = 'mylicence/item_show/$1/$2';

$route['licences/suket/(:num)/(:num)'] = 'mylicence/office/$1/$2';
//$route['licences/office/(:num)/(:any)'] = 'mylicence/office/$1/$2';
//$route['licences/pdf/(:num)'] = 'mylicence/pdf/$1';
//$route['licences/office_pdf/(:num)'] = 'mylicence/office_pdf/$1';
