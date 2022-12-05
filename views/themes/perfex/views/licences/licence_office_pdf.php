<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<span style="font-weight:bold;font-size:27px;">' . _l('licence_office_pdf_heading') . '</span><br />';
$info_right_column .= '<b style="color:#4e4e4e;"># ' . str_replace("SCH","SCH-UPT",$licence_number) . '</b>';

if (get_option('show_status_on_pdf_ei') == 1) {
    $info_right_column .= '<br /><span style="color:rgb(' . licence_status_color_pdf($status) . ');text-transform:uppercase;">' . format_licence_status($status, '', false) . '</span>';
}

// Add logo
$info_left_column .= pdf_logo_url();
// Write top left logo and right column info/text
pdf_multi_row($info_left_column, $info_right_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(8);

$organization_info = '<div style="color:#424242;">';
    $organization_info .= format_organization_info();
$organization_info .= '</div>';

// Licence to
$licence_info = '<b>' . _l('licence_office_to') . '</b>';
$licence_info .= '<div style="color:#424242;">';
$licence_info .= format_office_info($licence->office, 'licence', 'billing');
$licence_info .= '</div>';

$left_info  = $swap == '1' ? $licence_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $licence_info;

pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// Licence to
$left_info ='<p>' . _l('licence_opening') . ',</p>';
$left_info .= _l('licence_client');
$left_info .= '<div style="color:#424242;">';
$left_info .= format_customer_info($licence, 'licence', 'billing');
$left_info .= '</div>';

$right_info = '';

$pdf->ln(4);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 1) - $dimensions['lm']);

$organization_info = '<strong>'. _l('licence_members') . ': </strong><br />';

$CI = &get_instance();
$CI->load->model('licences_model');
$licence_members = $CI->licences_model->get_licence_members($licence->id,true);
$i=1;
foreach($licence_members as $member){
  $organization_info .=  $i.'. ' .$member['firstname'] .' '. $member['lastname']. '<br />';
  $i++;
}

$licence_info = '<br />' . _l('licence_data_date') . ': ' . _d($licence->date) . '<br />';


if ($licence->program_id != 0 && get_option('show_project_on_licence') == 1) {
    $licence_info .= _l('project') . ': ' . get_project_name_by_id($licence->program_id) . '<br />';
}


$left_info  = $swap == '1' ? $licence_info : $organization_info;
$right_info = $swap == '1' ? $organization_info : $licence_info;

$pdf->ln(4);
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

// The Table
$pdf->Ln(hooks()->apply_filters('pdf_info_and_table_separator', 6));

// The items table
$items = get_licence_items_table_data($licence, 'licence', 'pdf');

$tblhtml = $items->table();

$pdf->writeHTML($tblhtml, true, false, false, false, '');

$pdf->SetFont($font_name, '', $font_size);

$assigned_path = <<<EOF
        <img width="150" height="150" src="$licence->assigned_path">
    EOF;    
$assigned_info = '<div style="text-align:center;">';
    $assigned_info .= get_option('invoice_company_name') . '<br />';
    $assigned_info .= $assigned_path . '<br />';

if ($licence->assigned != 0 && get_option('show_assigned_on_licences') == 1) {
    $assigned_info .= get_staff_full_name($licence->assigned);
}
$assigned_info .= '</div>';

$acceptance_path = <<<EOF
    <img src="$licence->acceptance_path">
EOF;
$client_info = '<div style="text-align:center;">';
    $client_info .= $licence->client_company .'<br />';

if ($licence->signed != 0) {
    $client_info .= _l('licence_signed_by') . ": {$licence->acceptance_firstname} {$licence->acceptance_lastname}" . '<br />';
    $client_info .= _l('licence_signed_date') . ': ' . _dt($licence->acceptance_date_string) . '<br />';
    $client_info .= _l('licence_signed_ip') . ": {$licence->acceptance_ip}" . '<br />';

    $client_info .= $acceptance_path;
    $client_info .= '<br />';
}
$client_info .= '</div>';


$left_info  = $swap == '1' ? $client_info : $assigned_info;
$right_info = $swap == '1' ? $assigned_info : $client_info;
pdf_multi_row($left_info, $right_info, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(2);   
$companyname = get_option('companyname');
$pdf->writeHTMLCell('', '', '', '', _l('licence_crm_info', $companyname), 0, 1, false, true, 'L', true);
