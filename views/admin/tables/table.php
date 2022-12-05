<?php

defined('BASEPATH') or exit('No direct script access allowed');

$program_id = $this->ci->input->post('program_id');

$aColumns = [
    db_prefix() . 'licences.number',
    get_sql_select_client_company(),
    db_prefix() . 'licences.surveyor_id',
    db_prefix() . 'licences.inspector_id',
    'YEAR('. db_prefix() .'licences.date) as year',
    db_prefix() . 'licences.inspector_staff_id',
    db_prefix() . 'licences.date',
    db_prefix() . 'licences.reference_no',
    db_prefix() . 'licences.status',
    ];

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'licences.clientid',
    //'LEFT JOIN ' . db_prefix() . 'currencies ON ' . db_prefix() . 'currencies.id = ' . db_prefix() . 'licences.currency',
    'LEFT JOIN ' . db_prefix() . 'programs ON ' . db_prefix() . 'programs.id = ' . db_prefix() . 'licences.program_id',
];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'licences';
/*
$custom_fields = get_table_custom_fields('licence');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'licences.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}
*/
$where  = [];
$filter = [];

if ($this->ci->input->post('not_sent')) {
    array_push($filter, 'OR (sent= 0 AND ' . db_prefix() . 'licences.status NOT IN (2,3,4))');
}
if ($this->ci->input->post('invoiced')) {
    array_push($filter, 'OR inspector_id IS NOT NULL');
}

if ($this->ci->input->post('not_invoiced')) {
    array_push($filter, 'OR inspector_id IS NULL');
}
$statuses  = $this->ci->licences_model->get_statuses();
$statusIds = [];
foreach ($statuses as $status) {
    if ($this->ci->input->post('licences_' . $status)) {
        array_push($statusIds, $status);
    }
}
if (count($statusIds) > 0) {
    array_push($filter, 'AND ' . db_prefix() . 'licences.status IN (' . implode(', ', $statusIds) . ')');
}

$agents    = $this->ci->licences_model->get_sale_agents();
$agentsIds = [];
foreach ($agents as $agent) {
    if ($this->ci->input->post('sale_agent_' . $agent['sale_agent'])) {
        array_push($agentsIds, $agent['sale_agent']);
    }
}
if (count($agentsIds) > 0) {
    array_push($filter, 'AND sale_agent IN (' . implode(', ', $agentsIds) . ')');
}

$years      = $this->ci->licences_model->get_licences_years();
$yearsArray = [];
foreach ($years as $year) {
    if ($this->ci->input->post('year_' . $year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR('. db_prefix() .'licences.date) IN (' . implode(', ', $yearsArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (isset($clientid) && $clientid != '') {
    array_push($where, 'AND ' . db_prefix() . 'licences.clientid=' . $this->ci->db->escape_str($clientid));
}

if ($program_id) {
    array_push($where, 'AND program_id=' . $this->ci->db->escape_str($program_id));
}

if (!has_permission('licences', '', 'view')) {
    $userWhere = 'AND ' . get_licences_where_sql_for_staff(get_staff_user_id());
    array_push($where, $userWhere);
}

$aColumns = hooks()->apply_filters('licences_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
//if (count($custom_fields) > 4) {
//    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
//}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    db_prefix() . 'licences.id',
    db_prefix() . 'licences.clientid',
    db_prefix() . 'licences.inspector_id',
    'program_id',
    db_prefix() . 'licences.deleted_customer_name',
    db_prefix() . 'licences.hash',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $numberOutput = '';
    // If is from client area table or programs area request
    if ((isset($clientid) && is_numeric($clientid)) || $program_id) {
        $numberOutput = '<a href="' . admin_url('licences/list_licences/' . $aRow['id']) . '" target="_blank">' . format_licence_number($aRow['id']) . '</a>';
    } else {
        $numberOutput = '<a href="' . admin_url('licences/list_licences/' . $aRow['id']) . '" onclick="init_licence(' . $aRow['id'] . '); return false;">' . format_licence_number($aRow['id']) . '</a>';
    }

    $numberOutput .= '<div class="row-options">';

    $numberOutput .= '<a href="' . site_url('licence/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';
    if (has_permission('licences', '', 'edit')) {
        $numberOutput .= ' | <a href="' . admin_url('licences/licence/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }
    $numberOutput .= '</div>';

    $row[] = $numberOutput;

    if (empty($aRow['deleted_customer_name'])) {
        $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';
    } else {
        $row[] = $aRow['deleted_customer_name'];
    }

    $row[] = get_surveyor_name_by_id($aRow[db_prefix().'licences.surveyor_id']);

    $inspector = get_surveyor_name_by_id($aRow[db_prefix().'licences.inspector_id']);

    if ($aRow['inspector_id']) {
        $inspector .= '<br /><span class="hide"> - </span><span class="text-success">' . _l('licensed') . '</span>';
    }

    $row[] = $inspector;

    $row[] = $aRow['year'];

    $row[] = get_staff_full_name($aRow[db_prefix().'licences.inspector_staff_id']);

    $row[] = html_date($aRow[db_prefix() . 'licences.date']);

    $row[] = $aRow[db_prefix() . 'licences.reference_no'];

    $row[] = format_licence_status($aRow[db_prefix() . 'licences.status']);

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('licences_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}

echo json_encode($output);
die();