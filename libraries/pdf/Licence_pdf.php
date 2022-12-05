<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(LIBSPATH . 'pdf/App_pdf.php');

class Licence_pdf extends App_pdf
{
    protected $licence;

    private $licence_number;

    public function __construct($licence, $tag = '')
    {
        $this->load_language($licence->clientid);

        $licence                = hooks()->apply_filters('licence_html_pdf_data', $licence);
        $GLOBALS['licence_pdf'] = $licence;

        parent::__construct();

        $this->tag             = $tag;
        $this->licence        = $licence;
        $this->licence_number = format_licence_number($this->licence->id);

        $this->SetTitle($this->licence_number);
    }

    public function prepare()
    {

        $this->set_view_vars([
            'status'          => $this->licence->status,
            'licence_number' => $this->licence_number,
            'licence'        => $this->licence,
        ]);

        return $this->build();
    }

    protected function type()
    {
        return 'licence';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_licencepdf.php';
        $actualPath = module_views_path('licences','themes/' . active_clients_theme() . '/views/licences/licencepdf.php');

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
