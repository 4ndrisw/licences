<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Licence_item_pdf extends App_pdf
{
    protected $licence;

    protected $jenis_pesawat;

    private $licence_number;

    public function __construct($licence, $licence_item, $licence_item_data, $surveyor_staff)
    {
        $this->load_language($licence->clientid);

        $licence                = hooks()->apply_filters('licence_html_pdf_data', $licence);
        $GLOBALS['licence_item_pdf'] = $licence;

        parent::__construct();

        $this->equipment      = (array)$licence_item_data;
        $this->licence_item  = $licence_item;
        $this->jenis_pesawat  = $licence_item->jenis_pesawat;
        $this->licence        = $licence;
        $this->client        = get_client($licence_item->clientid);
        $this->institution        = get_client($licence_item->institution_id);
        $this->inspector        = get_client($licence_item->inspector_id);
        $this->surveyor        = get_client($licence_item->surveyor_id);
        $this->licence_number = $this->licence->number;
        $this->licence_item_number = $this->licence_item->number;
        $this->surveyor_staff = $surveyor_staff;

        $this->SetTitle($this->licence_item_number);
    }

    public function prepare()
    {

        $this->set_view_vars([
            'status'         => $this->licence->status,
            'licence'        => $this->licence,
            'licence_item'   => $this->licence_item,
            'jenis_pesawat'  => $this->jenis_pesawat,
            'licence_item_number' => $this->licence_item->number,
            'licence_number' => $this->licence_number,
            'equipment'      => $this->equipment,
            'client'      => $this->client,
            'institution'      => $this->institution,
            'inspector'      => $this->inspector,
            'surveyor'      => $this->surveyor,
            'surveyor_staff'      => $this->surveyor_staff,

        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'licence';
    }

    protected function file_path()
    {
        $filePath = 'my_licencepdf.php';
        $licence_jenis_pesawat = isset($this->jenis_pesawat) ? strtolower($this->jenis_pesawat) : FALSE;

        //log_activity($licence_jenis_pesawat);

        if(isset($this->licence->categories)){
            $filePath = 'licence_'. $this->licence->categories .'_pdf.php';
        }
        //var_dump($licence_jenis_pesawat);

        if(!is_null($licence_jenis_pesawat)){
            $jenis_pesawat_filePath = module_views_path('licences','themes/' . active_clients_theme() . '/views/licence_item/suket_' . $licence_jenis_pesawat .'_pdf.php');
            if (file_exists($jenis_pesawat_filePath)) {
                $filePath = $jenis_pesawat_filePath;
            }
        }
        $customPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/licence_item/suket_' . $licence_jenis_pesawat .'_pdf.php');

        $actualPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/licence_item/licence_item_pdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
