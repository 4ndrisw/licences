<?php

use app\services\AbstractKanban;
use app\services\licences\LicencesPipeline;

defined('BASEPATH') or exit('No direct script access allowed');

class Licences_model extends App_Model
{
    private $statuses;

    private $shipping_fields = ['shipping_street', 'shipping_city', 'shipping_city', 'shipping_state', 'shipping_zip', 'shipping_country'];

    public function __construct()
    {
        parent::__construct();

        $this->statuses = hooks()->apply_filters('before_set_licence_statuses', [
            1, //draft
            2, //propose
            5, //expired
            3, //decline
            4, //accept
            6, //process
            7, //release
        ]);
    }

    /**
     * Get unique sale agent for licences / Used for filters
     * @return array
     */
    public function get_sale_agents()
    {
        return $this->db->query("SELECT DISTINCT(sale_agent) as sale_agent, CONCAT(firstname, ' ', lastname) as full_name FROM " . db_prefix() . 'licences JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid=' . db_prefix() . 'licences.sale_agent WHERE sale_agent != 0')->result_array();
    }

    /**
     * Get licence/s
     * @param mixed $id licence id
     * @param array $where perform where
     * @return mixed
     */
    public function get($id = '', $where = [])
    {
        $this->db->select('*,' . db_prefix() . 'currencies.id as currencyid, ' . db_prefix() . 'licences.id as id, ' . db_prefix() . 'currencies.name as currency_name');
        $this->db->from(db_prefix() . 'licences');
        $this->db->join(db_prefix() . 'currencies', db_prefix() . 'currencies.id = ' . db_prefix() . 'licences.currency', 'left');
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'licences.id', $id);
            $licence = $this->db->get()->row();
            if ($licence) {
                $licence->attachments                           = $this->get_attachments($id);
                $licence->visible_attachments_to_customer_found = false;

                foreach ($licence->attachments as $attachment) {
                    if ($attachment['visible_to_customer'] == 1) {
                        $licence->visible_attachments_to_customer_found = true;

                        break;
                    }
                }

                $licence->items = get_items_by_type('licence', $id);

                if ($licence->program_id != 0) {
                    $this->load->model('projects_model');
                    $licence->project_data = $this->projects_model->get($licence->program_id);
                }

                $licence->client = $this->clients_model->get($licence->clientid);

                if (!$licence->client) {
                    $licence->client          = new stdClass();
                    $licence->client->company = $licence->deleted_customer_name;
                }

                $this->load->model('email_schedule_model');
                $licence->licenced_email = $this->email_schedule_model->get($id, 'licence');
            }

            return $licence;
        }
        $this->db->order_by('number,YEAR(date)', 'desc');

        return $this->db->get()->result_array();
    }

    /**
     * Get licence statuses
     * @return array
     */
    public function get_statuses()
    {
        return $this->statuses;
    }

    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $licence = $this->db->get(db_prefix() . 'licences')->row();

        if ($licence) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'licences', ['signature' => null]);

            if (!empty($licence->signature)) {
                unlink(get_upload_path_by_type('licence') . $id . '/' . $licence->signature);
            }

            return true;
        }

        return false;
    }

    /**
     * Convert licence to invoice
     * @param mixed $id licence id
     * @return mixed     New invoice ID
     */
    public function convert_to_invoice($id, $client = false, $draft_invoice = false)
    {
        // Recurring invoice date is okey lets convert it to new invoice
        $_licence = $this->get($id);

        $new_invoice_data = [];
        if ($draft_invoice == true) {
            $new_invoice_data['save_as_draft'] = true;
        }
        $new_invoice_data['clientid']   = $_licence->clientid;
        $new_invoice_data['program_id'] = $_licence->program_id;
        $new_invoice_data['number']     = get_option('next_invoice_number');
        $new_invoice_data['date']       = _d(date('Y-m-d'));
        $new_invoice_data['duedate']    = _d(date('Y-m-d'));
        if (get_option('invoice_due_after') != 0) {
            $new_invoice_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('invoice_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }
        $new_invoice_data['show_quantity_as'] = $_licence->show_quantity_as;
        $new_invoice_data['currency']         = $_licence->currency;
        $new_invoice_data['subtotal']         = $_licence->subtotal;
        $new_invoice_data['total']            = $_licence->total;
        $new_invoice_data['adjustment']       = $_licence->adjustment;
        $new_invoice_data['discount_percent'] = $_licence->discount_percent;
        $new_invoice_data['discount_total']   = $_licence->discount_total;
        $new_invoice_data['discount_type']    = $_licence->discount_type;
        $new_invoice_data['sale_agent']       = $_licence->sale_agent;
        // Since version 1.0.6
        $new_invoice_data['billing_street']   = clear_textarea_breaks($_licence->billing_street);
        $new_invoice_data['billing_city']     = $_licence->billing_city;
        $new_invoice_data['billing_state']    = $_licence->billing_state;
        $new_invoice_data['billing_zip']      = $_licence->billing_zip;
        $new_invoice_data['billing_country']  = $_licence->billing_country;
        $new_invoice_data['shipping_street']  = clear_textarea_breaks($_licence->shipping_street);
        $new_invoice_data['shipping_city']    = $_licence->shipping_city;
        $new_invoice_data['shipping_state']   = $_licence->shipping_state;
        $new_invoice_data['shipping_zip']     = $_licence->shipping_zip;
        $new_invoice_data['shipping_country'] = $_licence->shipping_country;

        if ($_licence->include_shipping == 1) {
            $new_invoice_data['include_shipping'] = 1;
        }

        $new_invoice_data['show_shipping_on_invoice'] = $_licence->show_shipping_on_licence;
        $new_invoice_data['terms']                    = get_option('predefined_terms_invoice');
        $new_invoice_data['clientnote']               = get_option('predefined_clientnote_invoice');
        // Set to unpaid status automatically
        $new_invoice_data['status']    = 1;
        $new_invoice_data['adminnote'] = '';

        $this->load->model('payment_modes_model');
        $modes = $this->payment_modes_model->get('', [
            'expenses_only !=' => 1,
        ]);
        $temp_modes = [];
        foreach ($modes as $mode) {
            if ($mode['selected_by_default'] == 0) {
                continue;
            }
            $temp_modes[] = $mode['id'];
        }
        $new_invoice_data['allowed_payment_modes'] = $temp_modes;
        $new_invoice_data['newitems']              = [];
        $custom_fields_items                       = get_custom_fields('items');
        $key                                       = 1;
        foreach ($_licence->items as $item) {
            $new_invoice_data['newitems'][$key]['description']      = $item['description'];
            $new_invoice_data['newitems'][$key]['long_description'] = clear_textarea_breaks($item['long_description']);
            $new_invoice_data['newitems'][$key]['qty']              = $item['qty'];
            $new_invoice_data['newitems'][$key]['unit']             = $item['unit'];
            $new_invoice_data['newitems'][$key]['taxname']          = [];
            $taxes                                                  = get_licence_item_taxes($item['id']);
            foreach ($taxes as $tax) {
                // tax name is in format TAX1|10.00
                array_push($new_invoice_data['newitems'][$key]['taxname'], $tax['taxname']);
            }
            $new_invoice_data['newitems'][$key]['rate']  = $item['rate'];
            $new_invoice_data['newitems'][$key]['order'] = $item['item_order'];
            foreach ($custom_fields_items as $cf) {
                $new_invoice_data['newitems'][$key]['custom_fields']['items'][$cf['id']] = get_custom_field_value($item['id'], $cf['id'], 'items', false);

                if (!defined('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST')) {
                    define('COPY_CUSTOM_FIELDS_LIKE_HANDLE_POST', true);
                }
            }
            $key++;
        }
        $this->load->model('invoices_model');
        $id = $this->invoices_model->add($new_invoice_data);
        if ($id) {
            // Customer accepted the licence and is auto converted to invoice
            if (!is_staff_logged_in()) {
                $this->db->where('rel_type', 'invoice');
                $this->db->where('rel_id', $id);
                $this->db->delete(db_prefix() . 'sales_activity');
                $this->invoices_model->log_invoice_activity($id, 'invoice_activity_auto_converted_from_licence', true, serialize([
                    '<a href="' . admin_url('licences/list_licences/' . $_licence->id) . '">' . format_licence_number($_licence->id) . '</a>',
                ]));
            }
            // For all cases update addefrom and sale agent from the invoice
            // May happen staff is not logged in and these values to be 0
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'invoices', [
                'addedfrom'  => $_licence->addedfrom,
                'sale_agent' => $_licence->sale_agent,
            ]);

            // Update licence with the new invoice data and set to status accepted
            $this->db->where('id', $_licence->id);
            $this->db->update(db_prefix() . 'licences', [
                'invoiced_date' => date('Y-m-d H:i:s'),
                'invoiceid'     => $id,
                'status'        => 4,
            ]);


            if (is_custom_fields_smart_transfer_enabled()) {
                $this->db->where('fieldto', 'licence');
                $this->db->where('active', 1);
                $cfLicences = $this->db->get(db_prefix() . 'customfields')->result_array();
                foreach ($cfLicences as $field) {
                    $tmpSlug = explode('_', $field['slug'], 2);
                    if (isset($tmpSlug[1])) {
                        $this->db->where('fieldto', 'invoice');

                        $this->db->group_start();
                        $this->db->like('slug', 'invoice_' . $tmpSlug[1], 'after');
                        $this->db->where('type', $field['type']);
                        $this->db->where('options', $field['options']);
                        $this->db->where('active', 1);
                        $this->db->group_end();

                        // $this->db->where('slug LIKE "invoice_' . $tmpSlug[1] . '%" AND type="' . $field['type'] . '" AND options="' . $field['options'] . '" AND active=1');
                        $cfTransfer = $this->db->get(db_prefix() . 'customfields')->result_array();

                        // Don't make mistakes
                        // Only valid if 1 result returned
                        // + if field names similarity is equal or more then CUSTOM_FIELD_TRANSFER_SIMILARITY%
                        if (count($cfTransfer) == 1 && ((similarity($field['name'], $cfTransfer[0]['name']) * 100) >= CUSTOM_FIELD_TRANSFER_SIMILARITY)) {
                            $value = get_custom_field_value($_licence->id, $field['id'], 'licence', false);

                            if ($value == '') {
                                continue;
                            }

                            $this->db->insert(db_prefix() . 'customfieldsvalues', [
                                'relid'   => $id,
                                'fieldid' => $cfTransfer[0]['id'],
                                'fieldto' => 'invoice',
                                'value'   => $value,
                            ]);
                        }
                    }
                }
            }

            if ($client == false) {
                $this->log_licence_activity($_licence->id, 'licence_activity_converted', false, serialize([
                    '<a href="' . admin_url('invoices/list_invoices/' . $id) . '">' . format_invoice_number($id) . '</a>',
                ]));
            }

            hooks()->do_action('licence_converted_to_invoice', ['invoice_id' => $id, 'licence_id' => $_licence->id]);
        }

        return $id;
    }

    /**
     * Copy licence
     * @param mixed $id licence id to copy
     * @return mixed
     */
    public function copy($id)
    {
        $_licence                       = $this->get($id);
        $new_licence_data               = [];
        $new_licence_data['clientid']   = $_licence->clientid;
        $new_licence_data['program_id'] = $_licence->program_id;
        $new_licence_data['number']     = get_option('next_licence_number');
        $new_licence_data['date']       = _d(date('Y-m-d'));
        $new_licence_data['duedate'] = null;

        if ($_licence->duedate && get_option('licence_due_after') != 0) {
            $new_licence_data['duedate'] = _d(date('Y-m-d', strtotime('+' . get_option('licence_due_after') . ' DAY', strtotime(date('Y-m-d')))));
        }

        $new_licence_data['show_quantity_as'] = $_licence->show_quantity_as;
        //$new_licence_data['currency']         = $_licence->currency;
        //$new_licence_data['subtotal']         = $_licence->subtotal;
        //$new_licence_data['total']            = $_licence->total;
        $new_licence_data['adminnote']        = $_licence->adminnote;
        $new_licence_data['adjustment']       = $_licence->adjustment;
        //$new_licence_data['discount_percent'] = $_licence->discount_percent;
        //$new_licence_data['discount_total']   = $_licence->discount_total;
        //$new_licence_data['discount_type']    = $_licence->discount_type;
        $new_licence_data['terms']            = $_licence->terms;
        $new_licence_data['sale_agent']       = $_licence->sale_agent;
        $new_licence_data['reference_no']     = $_licence->reference_no;
        // Since version 1.0.6
        $new_licence_data['billing_street']   = clear_textarea_breaks($_licence->billing_street);
        $new_licence_data['billing_city']     = $_licence->billing_city;
        $new_licence_data['billing_state']    = $_licence->billing_state;
        $new_licence_data['billing_zip']      = $_licence->billing_zip;
        $new_licence_data['billing_country']  = $_licence->billing_country;
        $new_licence_data['shipping_street']  = clear_textarea_breaks($_licence->shipping_street);
        $new_licence_data['shipping_city']    = $_licence->shipping_city;
        $new_licence_data['shipping_state']   = $_licence->shipping_state;
        $new_licence_data['shipping_zip']     = $_licence->shipping_zip;
        $new_licence_data['shipping_country'] = $_licence->shipping_country;
        if ($_licence->include_shipping == 1) {
            $new_licence_data['include_shipping'] = $_licence->include_shipping;
        }
        $new_licence_data['show_shipping_on_licence'] = $_licence->show_shipping_on_licence;
        // Set to unpaid status automatically
        $new_licence_data['status']     = 1;
        $new_licence_data['clientnote'] = $_licence->clientnote;
        $new_licence_data['adminnote']  = '';
        $new_licence_data['newitems']   = [];

        //
        // get_rest_licence_items here
        //

        echo '<pre>';
        var_dump($_licence);
        echo '----------------- <br />';


        var_dump($_licence->items);
        echo '</pre>';



        $id = $this->add($new_licence_data);

        if ($id) {
            $key                             = 1;
            $_licence->items = $this->get_rest_licence_items($_licence);
            
            foreach ($_licence->items as $item) {
                $this->db->where('id', $item['id']);    
                $this->db->update(db_prefix() . 'program_items', [
                    'licence_id'   => $id,
                ]);

                $key++;
            }

            log_activity('Copied Licence ' . format_licence_number($_licence->id));

            return $id;
        }

        return false;
    }
    /**
     * Performs rest of program items for licences 
     * @param array $data
     * @return array
     */
    public function get_rest_licence_items($licence)
    {
        $this->db->where('clientid',$licence->clientid);
        $this->db->where('program_id',$licence->program_id);
        $this->db->where('licence_id', null);
        $items = $this->db->get(db_prefix(). 'program_items')->result_array();
        return $items;
    }


    /**
     * Performs licences totals status
     * @param array $data
     * @return array
     */
    public function get_licences_total($data)
    {
        $statuses            = $this->get_statuses();
        $has_permission_view = has_permission('licences', '', 'view');
        $this->load->model('currencies_model');
        if (isset($data['currency'])) {
            $currencyid = $data['currency'];
        } elseif (isset($data['customer_id']) && $data['customer_id'] != '') {
            $currencyid = $this->clients_model->get_customer_default_currency($data['customer_id']);
            if ($currencyid == 0) {
                $currencyid = $this->currencies_model->get_base_currency()->id;
            }
        } elseif (isset($data['program_id']) && $data['program_id'] != '') {
            $this->load->model('projects_model');
            $currencyid = $this->projects_model->get_currency($data['program_id'])->id;
        } else {
            $currencyid = $this->currencies_model->get_base_currency()->id;
        }

        $currency = get_currency($currencyid);
        $where    = '';
        if (isset($data['customer_id']) && $data['customer_id'] != '') {
            $where = ' AND clientid=' . $data['customer_id'];
        }

        if (isset($data['program_id']) && $data['program_id'] != '') {
            $where .= ' AND program_id=' . $data['program_id'];
        }

        if (!$has_permission_view) {
            $where .= ' AND ' . get_licences_where_sql_for_staff(get_staff_user_id());
        }

        $sql = 'SELECT';
        foreach ($statuses as $licence_status) {
            $sql .= '(SELECT SUM(total) FROM ' . db_prefix() . 'licences WHERE status=' . $licence_status;
            $sql .= ' AND currency =' . $this->db->escape_str($currencyid);
            if (isset($data['years']) && count($data['years']) > 0) {
                $sql .= ' AND YEAR(date) IN (' . implode(', ', array_map(function ($year) {
                    return get_instance()->db->escape_str($year);
                }, $data['years'])) . ')';
            } else {
                $sql .= ' AND YEAR(date) = ' . date('Y');
            }
            $sql .= $where;
            $sql .= ') as "' . $licence_status . '",';
        }

        $sql     = substr($sql, 0, -1);
        $result  = $this->db->query($sql)->result_array();
        $_result = [];
        $i       = 1;
        foreach ($result as $key => $val) {
            foreach ($val as $status => $total) {
                $_result[$i]['total']         = $total;
                $_result[$i]['symbol']        = $currency->symbol;
                $_result[$i]['currency_name'] = $currency->name;
                $_result[$i]['status']        = $status;
                $i++;
            }
        }
        $_result['currencyid'] = $currencyid;

        return $_result;
    }

    /**
     * Insert new licence to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, licence ID if succes
     */
    public function add($data)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $data['prefix'] = get_option('licence_prefix');

        $data['number_format'] = get_option('licence_number_format');

        $save_and_send = isset($data['save_and_send']);

        $licenceRequestID = false;
        if (isset($data['licence_request_id'])) {
            $licenceRequestID = $data['licence_request_id'];
            unset($data['licence_request_id']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        $data['hash'] = app_generate_hash();
        $tags         = isset($data['tags']) ? $data['tags'] : '';

        $items = [];
        if (isset($data['newitems'])) {
            $items = $data['newitems'];
            unset($data['newitems']);
        }

        $data = $this->map_shipping_columns($data);

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $hook = hooks()->apply_filters('before_licence_added', [
            'data'  => $data,
            'items' => $items,
        ]);

        $data  = $hook['data'];
        $items = $hook['items'];

        $this->db->insert(db_prefix() . 'licences', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update next licence number in settings
            $this->db->where('name', 'next_licence_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');

            if ($licenceRequestID !== false && $licenceRequestID != '') {
                $this->load->model('licence_request_model');
                $completedStatus = $this->licence_request_model->get_status_by_flag('completed');
                $this->licence_request_model->update_request_status([
                    'requestid' => $licenceRequestID,
                    'status'    => $completedStatus->id,
                ]);
            }

            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }

            handle_tags_save($tags, $insert_id, 'licence');

            foreach ($items as $key => $item) {
                if ($itemid = add_new_sales_item_post($item, $insert_id, 'licence')) {
                    _maybe_insert_post_item_tax($itemid, $item, $insert_id, 'licence');
                }
            }

            update_sales_total_tax_column($insert_id, 'licence', db_prefix() . 'licences');
            $this->log_licence_activity($insert_id, 'licence_activity_created');

            hooks()->do_action('after_licence_added', $insert_id);

            if ($save_and_send === true) {
                $this->send_licence_to_client($insert_id, '', true, '', true);
            }

            return $insert_id;
        }

        return false;
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_licence_item($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'program_items')->row();
    }

    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_licence_items($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'program_items')->row();
    }
    /**
     * Get item by id
     * @param mixed $id item id
     * @return object
     */
    public function get_licence_item_data($licence_item_id, $jenis_pesawat)
    {
        $this->db->where('inspection_item_id', $licence_item_id);
        return $this->db->get(db_prefix() . $jenis_pesawat)->row();
    }

    /**
     * Update licence data
     * @param array $data licence data
     * @param mixed $id licenceid
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['number'] = trim($data['number']);

        $original_licence = $this->get($id);

        $original_status = $original_licence->status;

        $original_number = $original_licence->number;

        $original_number_formatted = format_licence_number($id);

        $save_and_send = isset($data['save_and_send']);

        $items = [];
        if (isset($data['items'])) {
            $items = $data['items'];
            unset($data['items']);
        }

        $newitems = [];
        if (isset($data['newitems'])) {
            $newitems = $data['newitems'];
            unset($data['newitems']);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'licence')) {
                $affectedRows++;
            }
        }

        $data['billing_street'] = trim($data['billing_street']);
        $data['billing_street'] = nl2br($data['billing_street']);

        $data['shipping_street'] = trim($data['shipping_street']);
        $data['shipping_street'] = nl2br($data['shipping_street']);

        $data = $this->map_shipping_columns($data);

        $hook = hooks()->apply_filters('before_licence_updated', [
            'data'          => $data,
            'items'         => $items,
            'newitems'      => $newitems,
            'removed_items' => isset($data['removed_items']) ? $data['removed_items'] : [],
        ], $id);

        $data                  = $hook['data'];
        $items                 = $hook['items'];
        $newitems              = $hook['newitems'];
        $data['removed_items'] = $hook['removed_items'];

        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            $original_item = $this->get_licence_item($remove_item_id);
            if (handle_removed_sales_item_post($remove_item_id, 'licence')) {
                $affectedRows++;
                $this->log_licence_activity($id, 'invoice_licence_activity_removed_item', false, serialize([
                    $original_item->description,
                ]));
            }
        }

        unset($data['removed_items']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', $data);

        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $this->log_licence_activity($original_licence->id, 'not_licence_status_updated', false, serialize([
                    '<original_status>' . $original_status . '</original_status>',
                    '<new_status>' . $data['status'] . '</new_status>',
                ]));
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            if ($original_number != $data['number']) {
                $this->log_licence_activity($original_licence->id, 'licence_activity_number_changed', false, serialize([
                    $original_number_formatted,
                    format_licence_number($original_licence->id),
                ]));
            }
            $affectedRows++;
        }

        foreach ($items as $key => $item) {
            $original_item = $this->get_licence_item($item['itemid']);

            if (update_sales_item_post($item['itemid'], $item, 'item_order')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'unit')) {
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'rate')) {
                $this->log_licence_activity($id, 'invoice_licence_activity_updated_item_rate', false, serialize([
                    $original_item->rate,
                    $item['rate'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'qty')) {
                $this->log_licence_activity($id, 'invoice_licence_activity_updated_qty_item', false, serialize([
                    $item['description'],
                    $original_item->qty,
                    $item['qty'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'description')) {
                $this->log_licence_activity($id, 'invoice_licence_activity_updated_item_short_description', false, serialize([
                    $original_item->description,
                    $item['description'],
                ]));
                $affectedRows++;
            }

            if (update_sales_item_post($item['itemid'], $item, 'long_description')) {
                $this->log_licence_activity($id, 'invoice_licence_activity_updated_item_long_description', false, serialize([
                    $original_item->long_description,
                    $item['long_description'],
                ]));
                $affectedRows++;
            }

            if (isset($item['custom_fields'])) {
                if (handle_custom_fields_post($item['itemid'], $item['custom_fields'])) {
                    $affectedRows++;
                }
            }

            if (!isset($item['taxname']) || (isset($item['taxname']) && count($item['taxname']) == 0)) {
                if (delete_taxes_from_item($item['itemid'], 'licence')) {
                    $affectedRows++;
                }
            } else {
                $item_taxes        = get_licence_item_taxes($item['itemid']);
                $_item_taxes_names = [];
                foreach ($item_taxes as $_item_tax) {
                    array_push($_item_taxes_names, $_item_tax['taxname']);
                }

                $i = 0;
                foreach ($_item_taxes_names as $_item_tax) {
                    if (!in_array($_item_tax, $item['taxname'])) {
                        $this->db->where('id', $item_taxes[$i]['id'])
                            ->delete(db_prefix() . 'item_tax');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                    $i++;
                }
                if (_maybe_insert_post_item_tax($item['itemid'], $item, $id, 'licence')) {
                    $affectedRows++;
                }
            }
        }

        foreach ($newitems as $key => $item) {
            if ($new_item_added = add_new_sales_item_post($item, $id, 'licence')) {
                _maybe_insert_post_item_tax($new_item_added, $item, $id, 'licence');
                $this->log_licence_activity($id, 'invoice_licence_activity_added_item', false, serialize([
                    $item['description'],
                ]));
                $affectedRows++;
            }
        }

        if ($affectedRows > 0) {
            update_sales_total_tax_column($id, 'licence', db_prefix() . 'licences');
        }

        if ($save_and_send === true) {
            $this->send_licence_to_client($id, '', true, '', true);
        }

        if ($affectedRows > 0) {
            hooks()->do_action('after_licence_updated', $id);

            return true;
        }

        return false;
    }

    public function mark_action_status($action, $id, $client = false)
    {
        $date_status_column = get_date_status_column($action);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'status' => $action,
            'date_' . $date_status_column => date('Y-m-d H:i:s')
        ]);
        
        $notifiedUsers = [];

        if ($this->db->affected_rows() > 0) {
            $licence = $this->get($id);
            if ($client == true) {
                $this->db->where('staffid', $licence->addedfrom);
                $this->db->or_where('staffid', $licence->sale_agent);
                $staff_licence = $this->db->get(db_prefix() . 'staff')->result_array();

                $invoiceid = false;
                $invoiced  = false;

                $contact_id = !is_client_logged_in()
                    ? get_primary_contact_user_id($licence->clientid)
                    : get_contact_user_id();

                if ($action == 4) {
                    if (get_option('licence_auto_convert_to_invoice_on_client_accept') == 1) {
                        $invoiceid = $this->convert_to_invoice($id, true);
                        $this->load->model('invoices_model');
                        if ($invoiceid) {
                            $invoiced = true;
                            $invoice  = $this->invoices_model->get($invoiceid);
                            $this->log_licence_activity($id, 'licence_activity_client_accepted_and_converted', true, serialize([
                                '<a href="' . admin_url('invoices/list_invoices/' . $invoiceid) . '">' . format_invoice_number($invoice->id) . '</a>',
                            ]));
                        }
                    } else {
                        $this->log_licence_activity($id, 'licence_activity_client_accepted', true);
                    }

                    // Send thank you email to all contacts with permission licences
                    $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'licence_emails' => 1]);

                    foreach ($contacts as $contact) {
                        send_mail_template('licence_accepted_to_customer', $licence, $contact);
                    }

                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_licence_customer_accepted',
                            'link'            => 'licences/list_licences/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }

                        send_mail_template('licence_accepted_to_staff', $licence, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    hooks()->do_action('licence_accepted', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                } elseif ($action == 3) {
                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_licence_customer_declined',
                            'link'            => 'licences/list_licences/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        // Send staff email notification that customer declined licence
                        send_mail_template('licence_declined_to_staff', $licence, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    $this->log_licence_activity($id, 'licence_activity_client_declined', true);
                    hooks()->do_action('licence_declined', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                } elseif ($action == 6) {
                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_licence_customer_processed',
                            'link'            => 'licences/list_licences/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        // Send staff email notification that customer processed licence
                        send_mail_template('licence_processed_to_staff', $licence, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    $this->log_licence_activity($id, 'licence_activity_client_processed', true);
                    hooks()->do_action('licence_processed', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                } elseif ($action == 7) {
                    foreach ($staff_licence as $member) {
                        $notified = add_notification([
                            'fromcompany'     => true,
                            'touserid'        => $member['staffid'],
                            'description'     => 'not_licence_customer_released',
                            'link'            => 'licences/list_licences/' . $id,
                            'additional_data' => serialize([
                                format_licence_number($licence->id),
                            ]),
                        ]);

                        if ($notified) {
                            array_push($notifiedUsers, $member['staffid']);
                        }
                        // Send staff email notification that customer released licence
                        send_mail_template('licence_released_to_staff', $licence, $member['email'], $contact_id);
                    }

                    pusher_trigger_notification($notifiedUsers);
                    $this->log_licence_activity($id, 'licence_activity_client_released', true);
                    hooks()->do_action('licence_released', $id);

                    return [
                        'invoiced'  => $invoiced,
                        'invoiceid' => $invoiceid,
                    ];
                }
            } else {
                if ($action == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
                // Admin marked licence
                $this->log_licence_activity($id, 'licence_activity_marked', false, serialize([
                    '<status>' . $action . '</status>',
                ]));

                return true;
            }
        }

        return false;
    }

    /**
     * Get licence attachments
     * @param mixed $licence_id
     * @param string $id attachment id
     * @return mixed
     */
    public function get_attachments($licence_id, $id = '')
    {
        // If is passed id get return only 1 attachment
        if (is_numeric($id)) {
            $this->db->where('id', $id);
        } else {
            $this->db->where('rel_id', $licence_id);
        }
        $this->db->where('rel_type', 'licence');
        $result = $this->db->get(db_prefix() . 'files');
        if (is_numeric($id)) {
            return $result->row();
        }

        return $result->result_array();
    }

    /**
     *  Delete licence attachment
     * @param mixed $id attachmentid
     * @return  boolean
     */
    public function delete_attachment($id)
    {
        $attachment = $this->get_attachments('', $id);
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('licence') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Licence Attachment Deleted [LicenceID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('licence') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('licence') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('licence') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Delete licence items and all connections
     * @param mixed $id licenceid
     * @return boolean
     */
    public function delete($id, $simpleDelete = false)
    {
        if (get_option('delete_only_on_last_licence') == 1 && $simpleDelete == false) {
            if (!is_last_licence($id)) {
                return false;
            }
        }
        $licence = $this->get($id);
        if (!is_null($licence->invoiceid) && $simpleDelete == false) {
            return [
                'is_invoiced_licence_delete_error' => true,
            ];
        }
        
        hooks()->do_action('before_licence_deleted', $id);

        $number = format_licence_number($id);

        $this->clear_signature($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'licences');

        if ($this->db->affected_rows() > 0) {
            if (!is_null($licence->short_link)) {
                app_archive_short_link($licence->short_link);
            }

            if (get_option('licence_number_decrement_on_delete') == 1 && $simpleDelete == false) {
                $current_next_licence_number = get_option('next_licence_number');
                if ($current_next_licence_number > 1) {
                    // Decrement next licence number to
                    $this->db->where('name', 'next_licence_number');
                    $this->db->set('value', 'value-1', false);
                    $this->db->update(db_prefix() . 'options');
                }
            }

            if (total_rows(db_prefix() . 'proposals', [
                    'licence_id' => $id,
                ]) > 0) {
                $this->db->where('licence_id', $id);
                $licence = $this->db->get(db_prefix() . 'proposals')->row();
                $this->db->where('id', $licence->id);
                $this->db->update(db_prefix() . 'proposals', [
                    'licence_id'    => null,
                    'date_converted' => null,
                ]);
            }

            delete_tracked_emails($id, 'licence');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'notes');

            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'views_tracking');

            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'reminders');

            /*
            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $this->db->delete(db_prefix() . 'taggables');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'itemable');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'item_tax');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete(db_prefix() . 'sales_activity');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'licence');
            $this->db->delete(db_prefix() . 'customfieldsvalues');
            */
            $attachments = $this->get_attachments($id);
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'licence');
            $this->db->delete('licenced_emails');

            // Get related tasks
            $this->db->where('rel_type', 'licence');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            if ($simpleDelete == false) {
                log_activity('Licences Deleted [Number: ' . $number . ']');
            }

            return true;
        }

        return false;
    }

    /**
     * Set licence to sent when email is successfuly sended to client
     * @param mixed $id licenceid
     */
    public function set_licence_sent($id, $emails_sent = [])
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'sent'     => 1,
            'datesend' => date('Y-m-d H:i:s'),
        ]);

        $this->log_licence_activity($id, 'invoice_licence_activity_sent_to_client', false, serialize([
            '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
        ]));

        // Update licence status to sent
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'status' => 2,
        ]);

        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'licence');
        $this->db->delete('licenced_emails');
    }

    /**
     * Send expiration reminder to customer
     * @param mixed $id licence id
     * @return boolean
     */
    public function send_expiry_reminder($id)
    {
        $licence        = $this->get($id);
        $licence_number = format_licence_number($licence->id);
        set_mailing_constant();
        $pdf              = licence_pdf($licence);
        $attach           = $pdf->Output($licence_number . '.pdf', 'S');
        $emails_sent      = [];
        $sms_sent         = false;
        $sms_reminder_log = [];

        // For all cases update this to prevent sending multiple reminders eq on fail
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'licences', [
            'is_expiry_notified' => 1,
        ]);

        $contacts = $this->clients_model->get_contacts($licence->clientid, ['active' => 1, 'licence_emails' => 1]);

        foreach ($contacts as $contact) {
            $template = mail_template('licence_expiration_reminder', $licence, $contact);

            $merge_fields = $template->get_merge_fields();

            $template->add_attachment([
                'attachment' => $attach,
                'filename'   => str_replace('/', '-', $licence_number . '.pdf'),
                'type'       => 'application/pdf',
            ]);

            if ($template->send()) {
                array_push($emails_sent, $contact['email']);
            }

            if (can_send_sms_based_on_creation_date($licence->datecreated)
                && $this->app_sms->trigger(SMS_TRIGGER_ESTIMATE_EXP_REMINDER, $contact['phonenumber'], $merge_fields)) {
                $sms_sent = true;
                array_push($sms_reminder_log, $contact['firstname'] . ' (' . $contact['phonenumber'] . ')');
            }
        }

        if (count($emails_sent) > 0 || $sms_sent) {
            if (count($emails_sent) > 0) {
                $this->log_licence_activity($id, 'not_expiry_reminder_sent', false, serialize([
                    '<custom_data>' . implode(', ', $emails_sent) . '</custom_data>',
                ]));
            }

            if ($sms_sent) {
                $this->log_licence_activity($id, 'sms_reminder_sent_to', false, serialize([
                    implode(', ', $sms_reminder_log),
                ]));
            }

            return true;
        }

        return false;
    }

    /**
     * Send licence to client
     * @param mixed $id licenceid
     * @param string $template email template to sent
     * @param boolean $attachpdf attach licence pdf or not
     * @return boolean
     */
    public function send_licence_to_client($id, $template_name = '', $attachpdf = true, $cc = '', $manually = false)
    {
        $licence = $this->get($id);

        if ($template_name == '') {
            $template_name = $licence->sent == 0 ?
                'licence_send_to_customer' :
                'licence_send_to_customer_already_sent';
        }

        $licence_number = format_licence_number($licence->id);

        $emails_sent = [];
        $send_to     = [];

        // Manually is used when sending the licence via add/edit area button Save & Send
        if (!DEFINED('CRON') && $manually === false) {
            $send_to = $this->input->post('sent_to');
        } elseif (isset($GLOBALS['licenced_email_contacts'])) {
            $send_to = $GLOBALS['licenced_email_contacts'];
        } else {
            $contacts = $this->clients_model->get_contacts(
                $licence->clientid,
                ['active' => 1, 'licence_emails' => 1]
            );

            foreach ($contacts as $contact) {
                array_push($send_to, $contact['id']);
            }
        }

        $status_auto_updated = false;
        $status_now          = $licence->status;

        if (is_array($send_to) && count($send_to) > 0) {
            $i = 0;

            // Auto update status to sent in case when user sends the licence is with status draft
            if ($status_now == 1) {
                $this->db->where('id', $licence->id);
                $this->db->update(db_prefix() . 'licences', [
                    'status' => 2,
                ]);
                $status_auto_updated = true;
            }

            if ($attachpdf) {
                $_pdf_licence = $this->get($licence->id);
                set_mailing_constant();
                $pdf = licence_pdf($_pdf_licence);

                $attach = $pdf->Output($licence_number . '.pdf', 'S');
            }

            foreach ($send_to as $contact_id) {
                if ($contact_id != '') {
                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $contact = $this->clients_model->get_contact($contact_id);

                    if (!$contact) {
                        continue;
                    }

                    $template = mail_template($template_name, $licence, $contact, $cc);

                    if ($attachpdf) {
                        $hook = hooks()->apply_filters('send_licence_to_customer_file_name', [
                            'file_name' => str_replace('/', '-', $licence_number . '.pdf'),
                            'licence'  => $_pdf_licence,
                        ]);

                        $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => $hook['file_name'],
                            'type'       => 'application/pdf',
                        ]);
                    }

                    if ($template->send()) {
                        array_push($emails_sent, $contact->email);
                    }
                }
                $i++;
            }
        } else {
            return false;
        }

        if (count($emails_sent) > 0) {
            $this->set_licence_sent($id, $emails_sent);
            hooks()->do_action('licence_sent', $id);

            return true;
        }

        if ($status_auto_updated) {
            // Licence not send to customer but the status was previously updated to sent now we need to revert back to draft
            $this->db->where('id', $licence->id);
            $this->db->update(db_prefix() . 'licences', [
                'status' => 1,
            ]);
        }

        return false;
    }

    /**
     * All licence activity
     * @param mixed $id licenceid
     * @return array
     */
    public function get_licence_activity($id)
    {
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'licence');
        $this->db->order_by('date', 'desc');

        return $this->db->get(db_prefix() . 'sales_activity')->result_array();
    }

    /**
     * Log licence activity to database
     * @param mixed $id licenceid
     * @param string $description activity description
     */
    public function log_licence_activity($id, $description = '', $client = false, $additional_data = '')
    {
        $staffid   = get_staff_user_id();
        $full_name = get_staff_full_name(get_staff_user_id());
        if (DEFINED('CRON')) {
            $staffid   = '[CRON]';
            $full_name = '[CRON]';
        } elseif ($client == true) {
            $staffid   = null;
            $full_name = '';
        }

        $this->db->insert(db_prefix() . 'sales_activity', [
            'description'     => $description,
            'date'            => date('Y-m-d H:i:s'),
            'rel_id'          => $id,
            'rel_type'        => 'licence',
            'staffid'         => $staffid,
            'full_name'       => $full_name,
            'additional_data' => $additional_data,
        ]);
    }

    /**
     * Updates pipeline order when drag and drop
     * @param mixe $data $_POST data
     * @return void
     */
    public function update_pipeline($data)
    {
        $this->mark_action_status($data['status'], $data['licenceid']);
        AbstractKanban::updateOrder($data['order'], 'pipeline_order', 'licences', $data['status']);
    }

    /**
     * Get licence unique year for filtering
     * @return array
     */
    public function get_licences_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(date)) as year FROM ' . db_prefix() . 'licences ORDER BY year DESC')->result_array();
    }

    private function map_shipping_columns($data)
    {
        if (!isset($data['include_shipping'])) {
            foreach ($this->shipping_fields as $_s_field) {
                if (isset($data[$_s_field])) {
                    $data[$_s_field] = null;
                }
            }
            $data['show_shipping_on_licence'] = 1;
            $data['include_shipping']          = 0;
        } else {
            $data['include_shipping'] = 1;
            // set by default for the next time to be checked
            if (isset($data['show_shipping_on_licence']) && ($data['show_shipping_on_licence'] == 1 || $data['show_shipping_on_licence'] == 'on')) {
                $data['show_shipping_on_licence'] = 1;
            } else {
                $data['show_shipping_on_licence'] = 0;
            }
        }

        return $data;
    }

    public function do_kanban_query($status, $search = '', $page = 1, $sort = [], $count = false)
    {
        _deprecated_function('Licences_model::do_kanban_query', '2.9.2', 'LicencesPipeline class');

        $kanBan = (new LicencesPipeline($status))
            ->search($search)
            ->page($page)
            ->sortBy($sort['sort'] ?? null, $sort['sort_by'] ?? null);

        if ($count) {
            return $kanBan->countAll();
        }

        return $kanBan->get();
    }

    public function licences_add_licence_item($data){
        $data['licence_addfrom'] = get_staff_user_id();
        $id = $data['id'];
        $licence_id = $data['licence_id'];

        $item = $this->get_licence_item($data['id']);

        include_once(APP_MODULES_PATH . 'institutions/models/Institutions_model.php');
        $this->load->model('institutions_model');
        $institution = $this->institutions_model->get($item->institution_id);

        unset($data['id']);
        unset($data['inspection_id']);
        unset($data['licence_id']);

        $inspector_staff_nama = get_staff_full_name($item->inspector_staff_id);
        $inspector_staff_nip = get_staff_nip($item->inspector_staff_id);
        $kepala_dinas_nama = get_staff_full_name($institution->head_id);
        $kepala_dinas_nip = get_staff_nip($institution->head_id);

        $data['inspector_staff_nama'] = $inspector_staff_nama;
        $data['inspector_staff_nip'] = $inspector_staff_nip;
        $data['kepala_dinas_nama'] = $kepala_dinas_nama;
        $data['kepala_dinas_nip'] = $kepala_dinas_nip;

        $this->db->set('licence_id', $licence_id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'program_items', $data);
    }


    public function licences_remove_licence_item($data){
        $this->db->set('licence_id', null);
        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'program_items', $data);
    }

    public function licences_process_licence_item($data){
        $this->db->set('status', 6);
        $this->db->where('id', $data['id']);
        $this->db->update(db_prefix() . 'program_items', $data);
    }

    public function licences_add_licence_item_number($data){
        $data['suket_addfrom'] = get_staff_user_id();
        $item = $this->get_licence_item($data['id']);

        include_once(APP_MODULES_PATH . 'institutions/models/Institutions_model.php');
        $this->load->model('institutions_model');
        $institution = $this->institutions_model->get($item->institution_id);
        
        $item->institution =$institution;
        $tanggal_penerbitan = date('Y-m-d');

        $number = get_institution_next_number($item->institution_id, $item->kelompok_alat);
        
        //$output = new stdClass;
        
        $data['tanggal_penerbitan'] = $tanggal_penerbitan;
        //$data['tanggal_kadaluarsa'] =  
        $data['jenis_pesawat_id'] = $item->jenis_pesawat_id;
        $data['institution_head_id'] = $item->institution->head_id;
        
        $masa_berlaku = '+1';
        $program_id = $data['id'];
        unset($data['id']);
        $this->db->set('tanggal_penerbitan', date('Y-m-d'));
        $this->db->set('tanggal_suket', date('Y-m-d'));
        $this->db->set('tanggal_kadaluarsa', date('Y-m-d', strtotime($masa_berlaku .' years')));
        
                
        $this->db->set('nomor_suket', $number);
        $this->db->set('institution_head_id', $item->institution->head_id);


        $this->db->where('id', $program_id);
        
        $this->db->update(db_prefix() . 'program_items');

        //if ($success) {
            $this->db->reset_query();

            // Update next licence number in settings
            $this->db->where('institution_id', $item->institution_id);
            $this->db->where('category', $item->kelompok_alat);

            $this->db->set('next_number', $number+1, false);
            $this->db->update(db_prefix() . 'lincence_institution_next_number');
        //}

    }

    /**
     * Insert new licence to database
     * @param array $data invoiec data
     * @return mixed - false if not insert, licence ID if succes
     */
    public function add_licence_item_data($data, $jenis_pesawat)
    {
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedfrom'] = get_staff_user_id();

        $save_and_send = isset($data['save_and_send']);


        $this->db->insert(db_prefix() . $jenis_pesawat, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            // Update next licence number in settings
            $this->db->where('name', 'next_licence_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update(db_prefix() . 'options');


            $this->log_licence_activity($insert_id, 'licence_item_data_activity_created');

            hooks()->do_action('after_licence_item_added', $insert_id);

            return $insert_id;
        }

        return false;
    }

    /**
     * Update licence data
     * @param array $data licence data
     * @param mixed $id licenceid
     * @return boolean
     */
    public function update_licence_item_data($data, $jenis_pesawat, $id)
    {
        $affectedRows = 0;

        $origin = $this->get_licence_item_data($id, $jenis_pesawat);

        $save_and_send = isset($data['save_and_send']);
        $data['removed_items'] = ['remove']; 
        // Delete items checked to be removed from database
        foreach ($data['removed_items'] as $remove_item_id) {
            $original_item = $this->get_licence_item($remove_item_id);
            if (handle_removed_sales_item_post($remove_item_id, 'licence')) {
                $affectedRows++;
                $this->log_licence_activity($id, 'invoice_licence_activity_removed_item', false, serialize([
                    $original_item->description,
                ]));
            }
        }

        unset($data['removed_items']);

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . $jenis_pesawat, $data);

        if ($this->db->affected_rows() > 0) {
            // Check for status change
            if ($original_status != $data['status']) {
                $this->log_licence_activity($origin->id, 'not_licence_status_updated', false, serialize([
                    '<original_status>' . $original_status . '</original_status>',
                    '<new_status>' . $data['status'] . '</new_status>',
                ]));
                if ($data['status'] == 2) {
                    $this->db->where('id', $id);
                    $this->db->update(db_prefix() . 'licences', ['sent' => 1, 'datesend' => date('Y-m-d H:i:s')]);
                }
            }
            if ($original_number != $data['number']) {
                $this->log_licence_activity($origin->id, 'licence_activity_number_changed', false, serialize([
                    $original_number_formatted,
                    format_licence_number($origin->id),
                ]));
            }
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            hooks()->do_action('after_licence_item_data_updated', $id);

            return true;
        }

        return false;
    }

    public function licences_next_number($next_numbers){

        foreach($next_numbers as $loop){
            $numbers = $loop;
            foreach($numbers as $number ){
                $this->db->where('institution_id', $number['institution_id']);
                $this->db->where('category', $number['category']);
                $exist = (bool)$this->db->get(db_prefix() . 'lincence_institution_next_number')->row();
                
                if(!$exist){
                    $this->db->reset_query();
                    $this->db->insert(db_prefix() . 'lincence_institution_next_number', $number);
                }
                $this->db->where('institution_id', $number['institution_id']);
                $this->db->where('category', $number['category']);
                $this->db->set('next_number', $number['next_number']);
                $this->db->update(db_prefix() . 'lincence_institution_next_number', $number);
            }
        }
        hooks()->do_action('after_licence_next_number_updated');
    }

    public function get_lincence_institution_next_number($institution_id){
        $this->db->where('institution_id', $institution_id);
        return $this->db->get(db_prefix() .'lincence_institution_next_number')->result_array();
    } 

    public function get_surveyor_staff($staff_id)
    {
        $this->db->where('staffid', $staff_id);
        $this->db->select(['firstname','lastname', 'skp_number', 'skp_datestart', 'skp_dateend']);
        return $this->db->get(db_prefix() . 'staff')->row();
    }

}

