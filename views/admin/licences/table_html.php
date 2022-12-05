<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
   _l('licence_dt_table_heading_number'),
   array(
      'name'=>_l('licence_dt_table_heading_client'),
      'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
   ),
   _l('surveyors'),
   _l('inspector'),
   array(
      'name'=>_l('invoice_licence_year'),
      'th_attrs'=>array('class'=>'not_visible')
   ),
   _l('inspector_staff'),
   _l('licence_dt_table_heading_date'),
   _l('reference_no'),
   _l('licence_dt_table_heading_status'));

$table_data = hooks()->apply_filters('licences_table_columns', $table_data);

render_datatable($table_data, isset($class) ? $class : 'licences');