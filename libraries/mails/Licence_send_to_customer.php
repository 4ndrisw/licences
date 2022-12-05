<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Licence_send_to_customer extends App_mail_template
{
    protected $for = 'customer';

    protected $licence;

    protected $contact;

    public $slug = 'licence-send-to-client';

    public $rel_type = 'licence';

    public function __construct($licence, $contact, $cc = '')
    {
        parent::__construct();

        $this->licence = $licence;
        $this->contact = $contact;
        $this->cc      = $cc;
    }

    public function build()
    {
        if ($this->ci->input->post('email_attachments')) {
            $_other_attachments = $this->ci->input->post('email_attachments');
            foreach ($_other_attachments as $attachment) {
                $_attachment = $this->ci->licences_model->get_attachments($this->licence->id, $attachment);
                $this->add_attachment([
                                'attachment' => get_upload_path_by_type('licence') . $this->licence->id . '/' . $_attachment->file_name,
                                'filename'   => $_attachment->file_name,
                                'type'       => $_attachment->filetype,
                                'read'       => true,
                            ]);
            }
        }

        $this->to($this->contact->email)
        ->set_rel_id($this->licence->id)
        ->set_merge_fields('client_merge_fields', $this->licence->clientid, $this->contact->id)
        ->set_merge_fields('licence_merge_fields', $this->licence->id);
    }
}
