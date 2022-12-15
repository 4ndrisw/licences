
<?php

use app\services\licences\LicencesPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Licences extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('licences_model');
    }

    /* Get all licences in case user go on index page */
    public function index($id = '')
    {
        $this->list_licences($id);
    }

    /* List all licences datatables */
    public function list_licences($id = '')
    {
        if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && get_option('allow_staff_view_licences_assigned') == '0') {
            access_denied('licences');
        }

        $isPipeline = $this->session->userdata('licence_pipeline') == 'true';

        $data['licence_statuses'] = $this->licences_model->get_statuses();
        if ($isPipeline && !$this->input->get('status') && !$this->input->get('filter')) {
            $data['title']           = _l('licences_pipeline');
            $data['bodyclass']       = 'licences-pipeline licences-total-manual';
            $data['switch_pipeline'] = false;

            if (is_numeric($id)) {
                $data['licenceid'] = $id;
            } else {
                $data['licenceid'] = $this->session->flashdata('licenceid');
            }

            $this->load->view('admin/licences/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') || $this->input->get('filter') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['licenceid']            = $id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('licences');
            $data['bodyclass']             = 'licences-total-manual';
            $data['licences_years']       = $this->licences_model->get_licences_years();
            $data['licences_sale_agents'] = $this->licences_model->get_sale_agents();

            $this->load->view('admin/licences/manage_table', $data);
        }
    }

    public function table($clientid = '')
    {
        if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && get_option('allow_staff_view_licences_assigned') == '0') {
            ajax_access_denied();
        }
        $this->app->get_table_data(module_views_path('licences', 'admin/tables/table',[
            'clientid' => $clientid,
        ]));
    }

    /* Add new licence or update existing */
    public function licence($id = '')
    {
        if ($this->input->post()) {
            $licence_data = $this->input->post();

            $save_and_send_later = false;
            if (isset($licence_data['save_and_send_later'])) {
                unset($licence_data['save_and_send_later']);
                $save_and_send_later = true;
            }

            if ($id == '') {
                if (!has_permission('licences', '', 'create')) {
                    access_denied('licences');
                }
                $id = $this->licences_model->add($licence_data);

                if ($id) {
                    set_alert('success', _l('added_successfully', _l('licence')));

                    $redUrl = admin_url('licences/list_licences/' . $id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_licence_pipeline_autoload($id) ? $redUrl : admin_url('licences/list_licences/')
                    );
                }
            } else {
                if (!has_permission('licences', '', 'edit')) {
                    access_denied('licences');
                }
                $success = $this->licences_model->update($licence_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('licence')));
                }
                if ($this->set_licence_pipeline_autoload($id)) {
                    redirect(admin_url('licences/list_licences/'));
                } else {
                    redirect(admin_url('licences/list_licences/' . $id));
                }
            }
        }
        if ($id == '') {
            $title = _l('create_new_licence');
        } else {
            $licence = $this->licences_model->get($id);

            if (!$licence || !user_can_view_licence($id)) {
                blank_page(_l('licence_not_found'));
            }

            $data['licence'] = $licence;
            $data['edit']     = true;
            $title            = _l('edit', _l('licence_lowercase'));
        }
        include_once(APP_MODULES_PATH . 'peralatan/models/Jenis_pesawat_model.php');
        $this->load->model('jenis_pesawat_model');

        $data['ajaxItems'] = false;
        if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
            $data['items'] = $this->jenis_pesawat_model->get_grouped();
        } else {
            $data['items']     = [];
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->jenis_pesawat_model->get_groups();

        $data['staff']             = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['title']             = $title;
//        $this->load->view(module_views_path('licences','admin/licences/licence'), $data);
        $this->load->view('admin/licences/licence', $data);
    }
    

    public function get_program_items_table($licence_clientid, $licence_program_id, $licence_inspection_id, $licence_status, $licence_id)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/program_items_table'), [
                'licence_clientid'=>$licence_clientid,
                'licence_program_id'=>$licence_program_id,
                'licence_id'=>$licence_id,
                'licence_status'=>$licence_status,
            ]);
        }
    }

    public function get_inspection_items_table($licence_clientid, $licence_program_id, $licence_inspection_id, $licence_id, $licence_status)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/inspection_items_table'), [
                'licence_clientid' => $licence_clientid,
                'licence_program_id' => $licence_program_id,
                'licence_inspection_id' => $licence_inspection_id,
                'licence_id'=>$licence_id,
                'licence_status'=>$licence_status,
            ]);
        }
    }
    
    public function get_licence_items_table($licence_clientid, $licence_program_id, $licence_inspection_id, $licence_id, $licence_status)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data(module_views_path('licences', 'admin/tables/licence_items_table'), [
                'licence_clientid' => $licence_clientid,
                'licence_program_id' => $licence_program_id,
                'licence_inspection_id' => $licence_inspection_id,
                'licence_id'=>$licence_id,
                'licence_status'=>$licence_status,
            ]);
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('licences', '', 'delete')) {
            $this->licences_model->clear_signature($id);
        }

        redirect(admin_url('licences/list_licences/' . $id));
    }

    public function update_number_settings($id)
    {
        $response = [
            'success' => false,
            'message' => '',
        ];
        if (has_permission('licences', '', 'edit')) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'licences', [
                'prefix' => $this->input->post('prefix'),
            ]);
            if ($this->db->affected_rows() > 0) {
                $response['success'] = true;
                $response['message'] = _l('updated_successfully', _l('licence'));
            }
        }

        echo json_encode($response);
        die;
    }

    public function validate_licence_number()
    {
        $isedit          = $this->input->post('isedit');
        $number          = $this->input->post('number');
        $date            = $this->input->post('date');
        $original_number = $this->input->post('original_number');
        $number          = trim($number);
        $number          = ltrim($number, '0');

        if ($isedit == 'true') {
            if ($number == $original_number) {
                echo json_encode(true);
                die;
            }
        }

        if (total_rows(db_prefix() . 'licences', [
            'YEAR(date)' => date('Y', strtotime(to_sql_date($date))),
            'number' => $number,
        ]) > 0) {
            echo 'false';
        } else {
            echo 'true';
        }
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->licences_model->delete_attachment($id);
        } else {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }
    }

    /* Get all licence data used when user click on licence number in a datatable left side*/
    public function get_licence_data_ajax($id, $to_return = false)
    {
        if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && get_option('allow_staff_view_licences_assigned') == '0') {
            echo _l('access_denied');
            die;
        }

        if (!$id) {
            die('No licence found');
        }

        $licence = $this->licences_model->get($id);

        if (!$licence || !user_can_view_licence($id)) {
            echo _l('licence_not_found');
            die;
        }

        $licence->date       = _d($licence->date);
        $licence->duedate = _d($licence->duedate);
        if ($licence->invoiceid !== null) {
            $this->load->model('invoices_model');
            $licence->invoice = $this->invoices_model->get($licence->invoiceid);
        }

        if ($licence->sent == 0) {
            $template_name = 'licence_send_to_customer';
        } else {
            $template_name = 'licence_send_to_customer_already_sent';
        }

        $data = prepare_mail_preview_data($template_name, $licence->clientid);
        include_once(APP_MODULES_PATH.'programs/models/Programs_model.php');
        $this->load->model('programs_model');
        $program = $this->programs_model->get($licence->program_id);

        include_once(APP_MODULES_PATH.'inspections/models/inspections_model.php');
        $this->load->model('inspections_model');
        $inspection = $this->inspections_model->get($licence->inspection_id);

        $data['activity']          = $this->licences_model->get_licence_activity($id);
        
        switch ($licence->status) {
            case '1':
                $licence->licence_item_info = 'licence_item_proposed';
                break;
            
            default:
                $licence->licence_item_info = 'licence_item_processed';
                break;
        }

        $data['licence']        = $licence;
        //$data['inspection']        = $inspection;
        //$data['program']           = $program;
        $data['members']           = $this->staff_model->get('', ['active' => 1]);
        $data['licence_statuses'] = $this->licences_model->get_statuses();
        $data['totalNotes']        = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'licence']);

        $data['send_later'] = false;
        if ($this->session->has_userdata('send_later')) {
            $data['send_later'] = true;
            $this->session->unset_userdata('send_later');
        }

        if ($to_return == false) {
            $this->load->view('admin/licences/licence_preview_template', $data);
        } else {
            return $this->load->view('admin/licences/licence_preview_template', $data, true);
        }
    }

    public function get_licences_total()
    {
        if ($this->input->post()) {
            $data['totals'] = $this->licences_model->get_licences_total($this->input->post());

            $this->load->model('currencies_model');

            if (!$this->input->post('customer_id')) {
                $multiple_currencies = call_user_func('is_using_multiple_currencies', db_prefix() . 'licences');
            } else {
                $multiple_currencies = call_user_func('is_client_using_multiple_currencies', $this->input->post('customer_id'), db_prefix() . 'licences');
            }

            if ($multiple_currencies) {
                $data['currencies'] = $this->currencies_model->get();
            }

            $data['licences_years'] = $this->licences_model->get_licences_years();

            if (
                count($data['licences_years']) >= 1
                && !\app\services\utilities\Arr::inMultidimensional($data['licences_years'], 'year', date('Y'))
            ) {
                array_unshift($data['licences_years'], ['year' => date('Y')]);
            }

            $data['_currency'] = $data['totals']['currencyid'];
            unset($data['totals']['currencyid']);
            $this->load->view('admin/licences/licences_total_template', $data);
        }
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && user_can_view_licence($rel_id)) {
            $this->misc_model->add_note($this->input->post(), 'licence', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if (user_can_view_licence($id)) {
            $data['notes'] = $this->misc_model->get_notes($id, 'licence');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function mark_action_status($status, $id)
    {
        if (!has_permission('licences', '', 'update_status')) {
            access_denied('licences');
        }

        log_activity(json_encode('1 == status ' . $status . ' id ' . $id));
        $action = $status;
        if($action = 2 || $action = 4){
            $licence = $this->licences_model->get($id);

            if($licence->reference_no == NULL || $licence->reference_no == '' ){
                set_alert('danger', _l('licence_status_changed_fail'));
                log_activity('error 1 reference_no is null or empty');
            }
            else{
                $total_licence_items = total_rows(db_prefix().'program_items',
                  array(
                   'licence_id'=>$id,
                   'surveyor_staff_id <>'=> null,
                  )
                );
                log_activity('total_licence_items ' . json_encode($total_licence_items));
                if($total_licence_items < 1){
                    set_alert('danger', _l('licence_status_changed_fail'));
                    log_activity('error 2 there is no licence_items');

                    if ($this->set_licence_pipeline_autoload($id)) {
                        redirect($_SERVER['HTTP_REFERER']);
                    } else {
                        redirect(admin_url('licences/list_licences/' . $id));
                    }
                }
            }
        }

        log_activity(json_encode('2 == status ' . $status . ' id ' . $id));
        $success = $this->licences_model->mark_action_status($status, $id);

        log_activity(json_encode($success));

        if ($success) {
            set_alert('success', _l('licence_status_changed_success'));
        } else {
            set_alert('danger', _l('licence_status_changed_fail'));
        }

        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/list_licences/' . $id));
        }
    }

    public function send_expiry_reminder($id)
    {
        $canView = user_can_view_licence($id);
        if (!$canView) {
            access_denied('Licences');
        } else {
            if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && $canView == false) {
                access_denied('Licences');
            }
        }

        $success = $this->licences_model->send_expiry_reminder($id);
        if ($success) {
            set_alert('success', _l('sent_expiry_reminder_success'));
        } else {
            set_alert('danger', _l('sent_expiry_reminder_fail'));
        }
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/list_licences/' . $id));
        }
    }

    /* Send licence to email */
    public function send_to_email($id)
    {
        $canView = user_can_view_licence($id);
        if (!$canView) {
            access_denied('licences');
        } else {
            if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && $canView == false) {
                access_denied('licences');
            }
        }

        try {
            $success = $this->licences_model->send_licence_to_client($id, '', $this->input->post('attach_pdf'), $this->input->post('cc'));
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('licence_sent_to_client_success'));
        } else {
            set_alert('danger', _l('licence_sent_to_client_fail'));
        }
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/list_licences/' . $id));
        }
    }

    /* Convert licence to invoice */
    public function convert_to_invoice($id)
    {
        if (!has_permission('invoices', '', 'create')) {
            access_denied('invoices');
        }
        if (!$id) {
            die('No licence found');
        }
        $draft_invoice = false;
        if ($this->input->get('save_as_draft')) {
            $draft_invoice = true;
        }
        $invoiceid = $this->licences_model->convert_to_invoice($id, false, $draft_invoice);
        if ($invoiceid) {
            set_alert('success', _l('licence_convert_to_invoice_successfully'));
            redirect(admin_url('invoices/list_invoices/' . $invoiceid));
        } else {
            if ($this->session->has_userdata('licence_pipeline') && $this->session->userdata('licence_pipeline') == 'true') {
                $this->session->set_flashdata('licenceid', $id);
            }
            if ($this->set_licence_pipeline_autoload($id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('licences/list_licences/' . $id));
            }
        }
    }

    public function copy($id)
    {
        if (!has_permission('licences', '', 'create')) {
            access_denied('licences');
        }
        if (!$id) {
            die('No licence found');
        }
        $new_id = $this->licences_model->copy($id);
        if ($new_id) {
            set_alert('success', _l('licence_copied_successfully'));
            if ($this->set_licence_pipeline_autoload($new_id)) {
                redirect($_SERVER['HTTP_REFERER']);
            } else {
                redirect(admin_url('licences/licence/' . $new_id));
            }
        }
        set_alert('danger', _l('licence_copied_fail'));
        if ($this->set_licence_pipeline_autoload($id)) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('licences/licence/' . $id));
        }
    }

    /* Delete licence */
    public function delete($id)
    {
        if (!has_permission('licences', '', 'delete')) {
            access_denied('licences');
        }
        if (!$id) {
            redirect(admin_url('licences/list_licences'));
        }
        $success = $this->licences_model->delete($id);
        if (is_array($success)) {
            set_alert('warning', _l('is_invoiced_licence_delete_error'));
        } elseif ($success == true) {
            set_alert('success', _l('deleted', _l('licence')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('licence_lowercase')));
        }
        redirect(admin_url('licences/list_licences'));
    }

    public function clear_acceptance_info($id)
    {
        if (is_admin()) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'licences', get_acceptance_info_array(true));
        }

        redirect(admin_url('licences/list_licences/' . $id));
    }

    /* Generates licence PDF and senting to email  */
    public function pdf($id)
    {
        $canView = user_can_view_licence($id);
        if (!$canView) {
            access_denied('Licences');
        } else {
            if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && $canView == false) {
                access_denied('Licences');
            }
        }
        if (!$id) {
            redirect(admin_url('licences/list_licences'));
        }
        $licence        = $this->licences_model->get($id);
        $licence_number = format_licence_number($licence->id);

        try {
            $pdf = licence_pdf($licence);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $fileNameHookData = hooks()->apply_filters('licence_file_name_admin_area', [
                            'file_name' => mb_strtoupper(slug_it($licence_number)) . '.pdf',
                            'licence'  => $licence,
                        ]);

        $pdf->Output($fileNameHookData['file_name'], $type);
    }

    // Pipeline
    public function get_pipeline()
    {
        if (has_permission('licences', '', 'view') || has_permission('licences', '', 'view_own') || get_option('allow_staff_view_licences_assigned') == '1') {
            $data['licence_statuses'] = $this->licences_model->get_statuses();
            $this->load->view('admin/licences/pipeline/pipeline', $data);
        }
    }

    public function pipeline_open($id)
    {
        $canView = user_can_view_licence($id);
        if (!$canView) {
            access_denied('Licences');
        } else {
            if (!has_permission('licences', '', 'view') && !has_permission('licences', '', 'view_own') && $canView == false) {
                access_denied('Licences');
            }
        }

        $data['id']       = $id;
        $data['licence'] = $this->get_licence_data_ajax($id, true);
        $this->load->view('admin/licences/pipeline/licence', $data);
    }

    public function update_pipeline()
    {
        if (has_permission('licences', '', 'edit')) {
            $this->licences_model->update_pipeline($this->input->post());
        }
    }

    public function pipeline($set = 0, $manual = false)
    {
        if ($set == 1) {
            $set = 'true';
        } else {
            $set = 'false';
        }
        $this->session->set_userdata([
            'licence_pipeline' => $set,
        ]);
        if ($manual == false) {
            redirect(admin_url('licences/list_licences'));
        }
    }

    public function pipeline_load_more()
    {
        $status = $this->input->get('status');
        $page   = $this->input->get('page');

        $licences = (new LicencesPipeline($status))
            ->search($this->input->get('search'))
            ->sortBy(
                $this->input->get('sort_by'),
                $this->input->get('sort')
            )
            ->page($page)->get();

        foreach ($licences as $licence) {
            $this->load->view('admin/licences/pipeline/_kanban_card', [
                'licence' => $licence,
                'status'   => $status,
            ]);
        }
    }

    public function set_licence_pipeline_autoload($id)
    {
        if ($id == '') {
            return false;
        }

        if ($this->session->has_userdata('licence_pipeline')
                && $this->session->userdata('licence_pipeline') == 'true') {
            $this->session->set_flashdata('licenceid', $id);

            return true;
        }

        return false;
    }

    public function get_due_date()
    {
        if ($this->input->post()) {
            $date    = $this->input->post('date');
            $duedate = '';
            if (get_option('licence_due_after') != 0) {
                $date    = to_sql_date($date);
                $d       = date('Y-m-d', strtotime('+' . get_option('licence_due_after') . ' DAY', strtotime($date)));
                $duedate = _d($d);
                echo $duedate;
            }
        }
    }

    public function add_licence_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licences_add_licence_item($this->input->post());
        }
    }

    public function remove_licence_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licences_remove_licence_item($this->input->post());
        }
    }
    
    public function process_licence_item()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licences_process_licence_item($this->input->post());
        }
    }

    public function add_licence_item_number()
    {
        if ($this->input->post() && $this->input->is_ajax_request()) {
            $this->licences_model->licences_add_licence_item_number($this->input->post());
        }
    }

    public function load_licence_template($file=''){
        $data['nama_alat'] = 'nama_alat';
        echo 'nama alat = '. 'aaa';
        $this->load->view('admin/licences/licence_template/forklif', $data);
    }
/*
    public function get_licence_item_data($id, $jenis_pesawat_id){

        $licence_item = $this->licences_model->get_licence_items($id);
        $licence = $this->licences_model->get($licence_item->licence_id);
        $_jenis_pesawat = $licence->jenis_pesawat;

        $jenis_pesawat = strtolower(str_replace(' ', '_', $_jenis_pesawat));
        $licence_item_data = $this->licences_model->get_licence_item_data($id, $jenis_pesawat);
        $data['licence'] = $licence;
        $data['licence_item'] = $licence_item;
        $data['licence_item_data'] = $licence_item_data;
        
        //return json_encode($data);

    }
*/

    public function licence_item($licence_item_id, $jenis_pesawat_id){
        
        $licence_item = $this->licences_model->get_licence_items($licence_item_id);
        $licence = $this->licences_model->get($licence_item->licence_id);
        $_jenis_pesawat = $licence_item->jenis_pesawat;
        $jenis_pesawat = strtolower(str_replace(' ', '_', $_jenis_pesawat));
        $licence_item_data = $this->licences_model->get_licence_item_data($licence_item_id, $jenis_pesawat);

        if ($this->input->post()) {
            $equipment_data = $this->input->post();
            //var_dump($licence_item_data);

            $save_and_send_later = false;
            if (isset($licence_data['save_and_send_later'])) {
                unset($licence_data['save_and_send_later']);
                $save_and_send_later = true;
            }


            if ($licence_item_data == NULL) {
                if (!has_permission('licences', '', 'create')) {
                    access_denied('licences');
                }
                $insert_id = $this->licences_model->add_licence_item_data($equipment_data, $jenis_pesawat);

                if ($insert_id) {
                    set_alert('success', _l('added_successfully', _l('licence')));

                    $redUrl = admin_url('licences/list_licences/' . $licence->id);

                    if ($save_and_send_later) {
                        $this->session->set_userdata('send_later', true);
                        // die(redirect($redUrl));
                    }

                    redirect(
                        !$this->set_licence_pipeline_autoload($licence_item_id) ? $redUrl : admin_url('licences/list_licences/')
                    );
                }
            } else {
                if (!has_permission('licences', '', 'edit')) {
                    access_denied('licences');
                }
                $success = $this->licences_model->update_licence_item_data($equipment_data, $jenis_pesawat, $licence_item_data->id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('licence')));
                }
                if ($this->set_licence_pipeline_autoload($licence_item_id)) {
                    redirect(admin_url('licences/list_licences/'));
                } else {
                    redirect(admin_url('licences/list_licences/' . $licence->id));
                }
            }
        }


        /*
         *
         *
         */

        $data = [];
        
        $data['licence'] = $licence;
        $data['licence_item'] = $licence_item;
        $data['licence_item_data'] = $licence_item_data;
        $data['jenis_pesawat'] = $jenis_pesawat;
        $data['surveyor_staff'] = get_surveyor_staff_data($licence_item->surveyor_staff_id);
        //$data['permits']    = get_surveyor_permits_by_category($licence_item->surveyor_id, $licence_item->kelompok_alat);
        $permits    = get_surveyor_permits_by_category($licence_item->surveyor_id, $licence_item->kelompok_alat);
        
        foreach($permits as $legals){
            $permit[$legals->rel_type] = $legals;                          
        }
        $data['permit'] = !empty($permit) ? $permit : [];

        $data['id']      = $licence_item_id;
        $data['title']      = str_replace('_',' ',$licence_item->jenis_pesawat) . ' info';
        $data['jenis_pesawat_id']   = $jenis_pesawat_id;
        $this->load->view('admin/licences/licence_item_template', $data);

    }

    function next_number($key = ''){
        if (!has_permission('licences', '', 'format_number')) {
            access_denied('licences');
        }

        if ($this->input->post()) {
            $next_number = $this->input->post();
            $next_licence_number = $this->licences_model->licences_next_number($next_number);
        }

        $staffid = get_staff_user_id();
        $data['institution_id'] = get_institution_id_by_staff_id($staffid);
        if(is_admin()){
            $data['institution_id'] = $key;
        }
        //$data['inspector_id'] = get_inspector_id_by_staff_id($staffid);
        $data['kelompok_alat'] = get_kelompok_alat();

        $next_numbers = $this->licences_model->get_lincence_institution_next_number($data['institution_id']);
        if(!$next_numbers){
            $data['string'] = 'error next number is not found';
            $this->load->view('blank_page', $data);
            return;
        }
        foreach($next_numbers as $q){
            $institution_next_numbers[$q['category']] = $q['next_number'];
        }
        $data['institution_next_numbers'] = $institution_next_numbers;

        $data['title']      = _l('format_number_form');
        $this->load->view('admin/licences/format_number', $data);
    }

}


