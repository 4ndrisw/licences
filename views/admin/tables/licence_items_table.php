<?php

defined('BASEPATH') or exit('No direct script access allowed');

$input = $this->ci->input->post('id');

$aColumns = [
    'nama_pesawat',
    'nomor_seri',
    'nomor_unit',
    '1',
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix().'program_items';

$where        = [
    'AND clientid=' . $licence_clientid,
    ];

array_push($where, 'AND licence_id = ' . $licence_id);

$join = [
//    'JOIN '.db_prefix().'staff ON '.db_prefix().'staff.staffid = '.db_prefix().'reminders.staff',
    ];
$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'id',
    'institution_id',
    'inspector_id',
    'inspector_staff_id',
    'surveyor_id',
    'program_id',
    'inspection_id',
    'clientid',
    'kelompok_alat',
    'jenis_pesawat_id',
    'nomor_suket',
    ]);
$output  = $result['output'];
$rResult = $result['rResult'];
$hash = licence_hash($licence_id);

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];

        if ($aColumns[$i] == 'nama_pesawat') {
            //$_data = '<a href="'. admin_url('licences/licence_item/' . $aRow['id']. '/' . $aRow['jenis_pesawat_id']) .'" onclick="init_licence_items_modal(' . $aRow['id'] .','. $aRow['jenis_pesawat_id'] . '); return false;" >' . $aRow['nama_pesawat'] . '</a>';
            $_data = '<a href="'. admin_url('licences/licence_item/' . $aRow['id']. '/' . $aRow['jenis_pesawat_id']) .'">' . $aRow['nama_pesawat'] . '</a>';
            if(!empty($aRow['nomor_suket'])){
                //$_data .= '<br /><span class="hide"> - </span><span class="text-success">' . $aRow['nomor_suket'] . '</span>';
                $nomor_suket = format_licence_item_number($aRow['inspection_id'], $aRow['kelompok_alat'], $aRow['nomor_suket'], $aRow['id']);
                
                $_data .= '<br /><span class="hide"> - </span><span class="text-success"><a href="'. site_url('licences/item/'.$licence_id.'/pdf/'.$aRow['id']. '?output_type=I') .'">'. $nomor_suket .'</a></span>';
                //$_data .= '<a href="'. site_url('licences/display/'.$licence_id.'/'.$aRow['id']. '/' . $aRow['jenis_pesawat_id'].'/'.$hash) .'">aa</a>';
                //$_data .= ' <span class="hide"> - </span><span class="text-success">' . $aRow['nomor_suket'] . '</span>';
            }
        }
        elseif ($aColumns[$i] == 'kelompok_alat') {
            $row[] = strtoupper($_data);
        }
        elseif ($aColumns[$i] == '1') {

            $_data = '';

            if(is_null($aRow['nomor_suket'])){

                $btn_disable = '' ;

                if($licence_status == 6){
                    $_data = '<a class="btn btn-success '.$btn_disable.'" title = "'._l('add_licence_number').'" href="#" onclick="licences_add_licence_item_number(' . $aRow['id'] . ',' . '); return false;">+</a>';
                }else{
                    $_data = '<a class="btn btn-danger '.$btn_disable.'" title = "'._l('remove_this_item').'" href="#" onclick="licences_remove_licence_item(' . $aRow['id'] . ',' . '); return false;">x</a>';
                }
            }
        }
        $row[] = $_data;
    }
    $row['DT_RowClass'] = 'has-row-options';
    $output['aaData'][] = $row;
}
