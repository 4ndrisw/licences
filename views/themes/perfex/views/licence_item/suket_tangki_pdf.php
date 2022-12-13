<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions = $pdf->getPageDimensions();

// set auto page breaks
$pdf->SetAutoPageBreak(true, 5);
$pdf->SetFont('dejavusans');


$inspection = $suket->inspection;
$equipment = $suket->equipment[0];
$licence_item = $suket->licence_items[0];

$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$inspection = $suket->inspection;
$inspection_date = _d($inspection->date);
$client = $suket->client;
$client_company = $client->company;
$client_address = $client->address;

$equipment = $suket->equipment[0];
$equipment_jenis_pesawat = isset($equipment['jenis_pesawat']) ? $equipment['jenis_pesawat'] : '';
$equipment_lokasi = isset($equipment['lokasi']) ? $equipment['lokasi'] : '';
$equipment_nama_pesawat = isset($equipment['nama_pesawat']) ? $equipment['nama_pesawat'] : '';
$equipment_pabrik_pembuat = isset($equipment['pabrik_pembuat']) ? $equipment['pabrik_pembuat'] : '';
$equipment_tempat_pembuatan = isset($equipment['tempat_pembuatan']) ? $equipment['tempat_pembuatan'] : '';
$equipment_tahun_pembuatan = isset($equipment['tahun_pembuatan']) ? $equipment['tahun_pembuatan'] : '';

$equipment_nomor_seri = isset($equipment['nomor_seri']) ? $equipment['nomor_seri'] : '';
$equipment_nomor_unit = isset($equipment['nomor_unit']) ? $equipment['nomor_unit'] : '';
$equipment_kapasitas = isset($equipment['kapasitas']) ? $equipment['kapasitas'] : '';
$equipment_jenis_pemeriksaan = isset($equipment['jenis_pemeriksaan']) ? $equipment['jenis_pemeriksaan'] : '';
$equipment_jenis_bejana = isset($equipment['jenis_bejana']) ? $equipment['jenis_bejana'] : '';
$office_dinas = $suket->office->dinas;
$regulasi = explode(' -- ', $equipment['regulasi']);
$equipment_regulasi = '';
$equipment_regulasi .= '<ol class="regulasi">'; 

$tanggal_pemeriksaan = tanggal_pemeriksaan($inspection->date);
$tanggal_suket = tanggal_suket($licence_item->tanggal_suket);
$expired = tanggal_suket($licence_item->expired);

$nomor_suket = $licence_item->nomor_suket;

$text = '<div style="text-align:center;"><strong>';
$text .= 'SURAT KETERANGAN' . '<br>';
$text .= '<span style="text-decoration: underline;">HASIL PEMERIKSAAN DAN PENGUJIAN' .'</span><br>';
$text .= 'Nomor : ' . $nomor_suket;
$text .= '</strong></div>';

$pdf->setFontSize('12');
$pdf->ln(20);
$pdf->writeHTML($text, true, 0, true, true);

$pdf->setFontSize('10');

$text = 'Berdasarkan hasil pemeriksaan dan pengujian yang dilakukan oleh ';
$text .= get_option('invoice_company_name');
$text .= ' pada tanggal ' .$tanggal_pemeriksaan. ' terhadap ' .$equipment_nama_pesawat. ' dapat diterangkan bahwa :' . "\r\n";

$pdf->SetLeftMargin(24);
$pdf->SetRightMargin(24);
$pdf->ln(4);
$pdf->Write(0, $text, '', 0, 'J', true, 0, false, false, 0);

foreach($regulasi as $row){
    $equipment_regulasi .= '<li style="margin-left:70;">' .$row. '</li>'; 
}

$equipment_regulasi .= '</ol>'; 

//var_dump($office_short_name);

$html = <<<EOD
<style>
    tr > ol {
    margin-left: 76px;
    }
</style>
<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="width:20;">A.</td>
        <td style="width:280;">Data Umum Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Jenis objek K3 yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_nama_pesawat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Nama Perusahaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$client_company</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">3. Alamat Perusahaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$client_address</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. Lokasi objek yang diuji</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_lokasi</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">B.</td>
        <td style="width:280;">Data Teknis Objek Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Pabrik pembuat</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_pabrik_pembuat</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Tempat / tahun pembuatan</td>
        <td style="width:10;">:</td>
        <td style="width:420;"> $equipment_tempat_pembuatan / $equipment_tahun_pembuatan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">3. Bentuk</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jenis_bejana</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">4. No. Serie / No. Unit</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_nomor_seri / $equipment_nomor_unit</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">5. Kapasitas</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_kapasitas</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">6. Jenis pemeriksaan</td>
        <td style="width:10;">:</td>
        <td style="width:420;">$equipment_jenis_pemeriksaan</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">C.</td>
        <td style="width:450;">Hasil Pemeriksaan dan Pengujian</td>
        <td style="width:10;"></td>
        <td style="width:0;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">1. Pemeriksaan Dokumen</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;">2. Pengujian</td>
        <td style="width:10;">:</td>
        <td style="width:220;">Baik</td>
        <td style="width:200;">(Terlampir)</td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:280;"></td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;">D.</td>
        <td style="width:280;">Kesimpulan</td>
        <td style="width:10;"></td>
        <td style="width:420;"></td>
    </tr>
    <tr>
        <td style="width:20;"></td>
        <td style="width:650;">Berdasarkan hasil pemeriksaan dan pengujian tersebut <strong>$equipment_nama_pesawat</strong></td>
        <td style="width:5;"></td>
        <td style="width:5;"></td>
    </tr>
</table>
EOD;

// Print text using writeHTMLCell()
//$pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);
//$pdf->writeHTML($html, true, false, false, false, '');

// store old margin values
$margins = $pdf->getMargins();

// set new left margin
$pdf->SetLeftMargin(24);

// output the HTML content
// restore the left margin
$pdf->SetLeftMargin($margins['left']);

$pdf->ln(2);
$pdf->writeHTML($html, true, 0, true, true);
$blank_line ="\r\n";

$pdf->Write(0, $blank_line, '', 0, 'J', true, 0, false, false, 0);


$text = '<div style="text-align:center;"><strong>';
$text .= 'MEMENUHI' ."<br />";
$text .= 'PERSYARATAN KESELAMATAN DAN KESEHATAN KERJA';
$text .= '</strong></div>';
$pdf->writeHTML($text, true, 0, true, true);

$pdf->ln(4);

$text = '<div style="text-align:justify;">';
$text .= 'Demikian surat keterangan ini dibuat dengan sebenarnya agar dapat digunakan sebagaimana mestinya dan berlaku sepanjang objek pengujian tidak dilakukan perubahan dan / atau sampai dilakukan pengujian selanjutnya paling lambat tanggal ';
$text .= '<strong>'. $expired .'</strong>.';
$text .= '</div>';
$pdf->SetLeftMargin(20);
$pdf->SetRightMargin(20);
$pdf->writeHTML($text, true, 0, true, true);

$pdf->ln(4);

$left_info = '<div style="text-align:center;">';
$left_info .= "<br />";
$left_info .= 'Mengetahui,' .'<br />';
$left_info .= 'Kepala ' . $suket->office->dinas . '<br />';
$left_info .= $suket->office->province;
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= "<br />";
$left_info .= '<span style="text-decoration: underline;"><strong>' . $licence_item->kepala_dinas_nama . '</strong></span>';
$left_info .= "<br />";
$left_info .= '<strong>' . $licence_item->kepala_dinas_nip .'</strong>';
$left_info .= '</div>';


$right_info = '<div style="text-align:center;">';
$right_info .= "Serang, $tanggal_suket" .'<br />';
$right_info .= "<br />";
$right_info .= 'Yang Melakukan Evaluasi,' .'<br />';
$right_info .= 'Pengawas Ketenagakerjaan' .'<br />';
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= "<br />";
$right_info .= '<span style="text-decoration: underline;"><strong>' . $licence_item->pengawas_nama . '</strong></span>';
$right_info .= "<br />";
$right_info .= '<strong>' . $licence_item->pengawas_nip . '</strong>';
$right_info .= '</div>';



$html = <<<EOD

<table cellspacing="1" cellpadding="1" border="0">
    <tr>
        <td style="text-align:center;">$left_info</td>
        <td style="text-align:center;">$right_info</td>
    </tr>
</table>
EOD;

$pdf->writeHTML($html, true, 0, true, true);

