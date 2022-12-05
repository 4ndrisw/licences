<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('_attachment_sale_id',$licence->id); ?>
<?php echo form_hidden('_attachment_sale_type','licence'); ?>
<div class="col-md-12 no-padding">
   <div class="panel_s">
      <div class="panel-body">
         <div class="horizontal-scrollable-tabs preview-tabs-top">
            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
            <div class="horizontal-tabs">
               <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
                  <li role="presentation" class="active">
                     <a href="#tab_licence" aria-controls="tab_licence" role="tab" data-toggle="tab">
                     <?php echo _l('licence'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_licence_items" onclick="initDataTable('.table-licence_items', admin_url + 'licences/get_licence_items_table/'
                                                                                       + <?php echo $licence->clientid ?> + '/'
                                                                                       + <?php echo $licence->program_id; ?> + '/'
                                                                                       + <?php echo $licence->inspection_id; ?> + '/'
                                                                                       + <?php echo $licence->id ?> + '/'
                                                                                       + <?php echo $licence->status ;?>, undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_licence_items" role="tab" data-toggle="tab">
                     <?php echo _l('licence_items_tab'); ?>
                     <?php
                        $total_licence_items = total_rows(db_prefix().'program_items',
                          array(
                           'licence_id'=>$licence->id,
                           )
                          );
                        if($total_licence_items > 0){
                          echo '<span class="badge">'.$total_licence_items.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  
                  <li role="presentation">
                     <a href="#tab_inspection_items" onclick="initDataTable('.table-inspection_items', admin_url + 'licences/get_inspection_items_table/'
                                                                                       + <?php echo $licence->clientid ?> + '/'
                                                                                       + <?php echo $licence->program_id; ?> + '/'
                                                                                       + <?php echo $licence->inspection_id; ?> + '/'
                                                                                       + <?php echo $licence->id ?> + '/'
                                                                                       + <?php echo $licence->status ;?>, undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_inspection_items" role="tab" data-toggle="tab">
                     <?php echo _l('inspection_items_tab'); ?>
                     <?php
                        $total_inspection_items = total_rows(db_prefix().'program_items',
                          array(
                           'inspection_id'=>$licence->inspection_id,
                           'licence_id'=>NULL,
                           )
                          );
                        if($total_inspection_items > 0){
                          echo '<span class="badge">'.$total_inspection_items.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_program_items" onclick="initDataTable('.table-program_items', admin_url + 'licences/get_program_items_table/'
                                                                                       + <?php echo $licence->clientid ?> + '/'
                                                                                       + <?php echo $licence->program_id; ?> + '/'
                                                                                       + <?php echo $licence->inspection_id; ?> + '/'
                                                                                       + <?php echo $licence->id ?> + '/'
                                                                                       + <?php echo $licence->status ;?>, undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_program_items" role="tab" data-toggle="tab">
                     <?php echo _l('program_items_tab'); ?>
                     <?php
                        $total_program_items = total_rows(db_prefix().'program_items',
                          array(
                           'program_id'=>$licence->program_id,
                           //'licence_id'=>NULL,
                           )
                          );
                        if($total_program_items > 0){
                          echo '<span class="badge">'.$total_program_items.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <?php if(has_permission('licences', '', 'delete')) { ?>
                  <li role="presentation">
                     <a href="#tab_tasks" onclick="init_rel_tasks_table(<?php echo $licence->id; ?>,'licence'); return false;" aria-controls="tab_tasks" role="tab" data-toggle="tab">
                     <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                  <?php } ?>

                  <li role="presentation">
                     <a href="#tab_activity" aria-controls="tab_activity" role="tab" data-toggle="tab">
                     <?php echo _l('licence_view_activity_tooltip'); ?>
                     </a>
                  </li>
                  <li role="presentation">
                     <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $licence->id ;?> + '/' + 'licence', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                     <?php echo _l('licence_reminders'); ?>
                     <?php
                        $total_reminders = total_rows(db_prefix().'reminders',
                          array(
                           'isnotified'=>0,
                           'staff'=>get_staff_user_id(),
                           'rel_type'=>'licence',
                           'rel_id'=>$licence->id
                           )
                          );
                        if($total_reminders > 0){
                          echo '<span class="badge">'.$total_reminders.'</span>';
                        }
                        ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_notes" onclick="get_sales_notes(<?php echo $licence->id; ?>,'licences'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                     <?php echo _l('licence_notes'); ?>
                     <span class="notes-total">
                        <?php if($totalNotes > 0){ ?>
                           <span class="badge"><?php echo $totalNotes; ?></span>
                        <?php } ?>
                     </span>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" title="<?php echo _l('emails_tracking'); ?>" class="tab-separator">
                     <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab">
                     <?php if(!is_mobile()){ ?>
                     <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                     <?php } else { ?>
                     <?php echo _l('emails_tracking'); ?>
                     <?php } ?>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('view_tracking'); ?>" class="tab-separator">
                     <a href="#tab_views" aria-controls="tab_views" role="tab" data-toggle="tab">
                     <?php if(!is_mobile()){ ?>
                     <i class="fa fa-eye"></i>
                     <?php } else { ?>
                     <?php echo _l('view_tracking'); ?>
                     <?php } ?>
                     </a>
                  </li>
                  <li role="presentation" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="tab-separator toggle_view">
                     <a href="#" onclick="small_table_full_view(); return false;">
                     <i class="fa fa-expand"></i></a>
                  </li>
               </ul>
            </div>
         </div>
         <div class="row mtop10">
            <div class="col-md-3">
               <?php echo format_licence_status($licence->status,'mtop5');  ?>
            </div>
            <div class="col-md-9">
               <div class="visible-xs">
                  <div class="mtop10"></div>
               </div>
               <div class="pull-right _buttons">
                  <?php if(staff_can('edit', 'licences')){ ?>
                  <a href="<?php echo admin_url('licences/licence/'.$licence->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('edit_licence_tooltip'); ?>" data-placement="bottom"><i class="fa-solid fa-pen-to-square"></i></a>
                  <?php } ?>
                  <div class="btn-group">
                     <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa-solid fa-file-pdf"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                     <ul class="dropdown-menu dropdown-menu-right">
                        <li class="hidden-xs"><a href="<?php echo admin_url('licences/pdf/'.$licence->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                        <li class="hidden-xs"><a href="<?php echo admin_url('licences/pdf/'.$licence->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                        <li><a href="<?php echo admin_url('licences/pdf/'.$licence->id); ?>"><?php echo _l('download'); ?></a></li>
                        <li>
                           <a href="<?php echo admin_url('licences/pdf/'.$licence->id.'?print=true'); ?>" target="_blank">
                           <?php echo _l('print'); ?>
                           </a>
                        </li>
                     </ul>
                  </div>
                  <?php
                     $_tooltip = _l('licence_sent_to_email_tooltip');
                     $_tooltip_already_send = '';
                     if($licence->sent == 1){
                        $_tooltip_already_send = _l('licence_already_send_to_client_tooltip', time_ago($licence->datesend));
                     }
                     ?>
                  <?php if(!empty($licence->clientid)){ ?>
                  <a href="#" class="licence-send-to-client btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo $_tooltip; ?>" data-placement="bottom"><span data-toggle="tooltip" data-title="<?php echo $_tooltip_already_send; ?>"><i class="fa fa-envelope"></i></span></a>
                  <?php } ?>
                  <div class="btn-group">
                     <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php echo _l('more'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu dropdown-menu-right">
                        <li>
                           <a href="<?php echo site_url('licence/' . $licence->id . '/' .  $licence->hash) ?>" target="_blank">
                           <?php echo _l('view_licence_as_client'); ?>
                           </a>
                        </li>
                        <?php hooks()->do_action('after_licence_view_as_client_link', $licence); ?>
                        <?php if((!empty($licence->duedate) && date('Y-m-d') < $licence->duedate && ($licence->status == 2 || $licence->status == 5)) && is_licences_expiry_reminders_enabled()){ ?>
                        <li>
                           <a href="<?php echo admin_url('licences/send_expiry_reminder/'.$licence->id); ?>">
                           <?php echo _l('send_expiry_reminder'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <li>
                           <a href="#" data-toggle="modal" data-target="#sales_attach_file"><?php echo _l('invoice_attach_file'); ?></a>
                        </li>
                        <?php if (staff_can('create', 'projects') && $licence->program_id == 0) { ?>
                           <li>
                              <a href="<?php echo admin_url("projects/project?via_licence_id={$licence->id}&customer_id={$licence->clientid}") ?>">
                                 <?php echo _l('licence_convert_to_project'); ?>
                              </a>
                           </li>
                        <?php } ?>
                        <?php if($licence->invoiceid == NULL){
                           if(staff_can('edit', 'licences')){
                             foreach($licence_statuses as $status){
                               if($licence->status != $status){ ?>
                        <li>
                           <a href="<?php echo admin_url() . 'licences/mark_action_status/'.$status.'/'.$licence->id; ?>">
                           <?php echo _l('licence_mark_as',format_licence_status($status,'',false)); ?></a>
                        </li>
                        <?php }
                           }
                           ?>
                        <?php } ?>
                        <?php } ?>
                        <?php if(staff_can('create', 'licences')){ ?>
                        <li>
                           <a href="<?php echo admin_url('licences/copy/'.$licence->id); ?>">
                           <?php echo _l('copy_licence'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <?php if(!empty($licence->signature) && staff_can('delete', 'licences')){ ?>
                        <li>
                           <a href="<?php echo admin_url('licences/clear_signature/'.$licence->id); ?>" class="_delete">
                           <?php echo _l('clear_signature'); ?>
                           </a>
                        </li>
                        <?php } ?>
                        <?php if(staff_can('delete', 'licences')){ ?>
                        <?php
                           if((get_option('delete_only_on_last_licence') == 1 && is_last_licence($licence->id)) || (get_option('delete_only_on_last_licence') == 0)){ ?>
                        <li>
                           <a href="<?php echo admin_url('licences/delete/'.$licence->id); ?>" class="text-danger delete-text _delete"><?php echo _l('delete_licence_tooltip'); ?></a>
                        </li>
                        <?php
                           }
                           }
                           ?>
                     </ul>
                  </div>
                  <?php if($licence->invoiceid == NULL){ ?>
                  <?php if(staff_can('create', 'invoices') && !empty($licence->clientid)){ ?>
                  <div class="btn-group pull-right mleft5">
                     <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                     <?php echo _l('licence_convert_to_licence'); ?> <span class="caret"></span>
                     </button>
                     <ul class="dropdown-menu">
                        <li><a href="<?php echo admin_url('licences/convert_to_invoice/'.$licence->id.'?save_as_draft=true'); ?>"><?php echo _l('convert_and_save_as_draft'); ?></a></li>
                        <li class="divider">
                        <li><a href="<?php echo admin_url('licences/convert_to_invoice/'.$licence->id); ?>"><?php echo _l('convert'); ?></a></li>
                        </li>
                     </ul>
                  </div>
                  <?php } ?>
                  <?php } else { ?>
                  <a href="<?php echo admin_url('invoices/list_invoices/'.$licence->invoice->id); ?>" data-placement="bottom" data-toggle="tooltip" title="<?php echo _l('licence_invoiced_date',_dt($licence->invoiced_date)); ?>"class="btn mleft10 btn-info"><?php echo format_invoice_number($licence->invoice->id); ?></a>
                  <?php } ?>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
         <hr class="hr-panel-heading" />
         <div class="tab-content">
            <div role="tabpanel" class="tab-pane ptop10 active" id="tab_licence">
               <?php if(isset($licence->licenced_email) && $licence->licenced_email) { ?>
                     <div class="alert alert-warning">
                        <?php echo _l('invoice_will_be_sent_at', _dt($licence->licenced_email->licenced_at)); ?>
                        <?php if(staff_can('edit', 'licences') || $licence->addedfrom == get_staff_user_id()) { ?>
                           <a href="#"
                           onclick="edit_licence_licenced_email(<?php echo $licence->licenced_email->id; ?>); return false;">
                           <?php echo _l('edit'); ?>
                        </a>
                     <?php } ?>
                  </div>
               <?php } ?>
               <div id="licence-preview">
                  <div class="row">
                     <?php if($licence->status == 4 && !empty($licence->acceptance_firstname) && !empty($licence->acceptance_lastname) && !empty($licence->acceptance_email)){ ?>
                     <div class="col-md-12">
                        <div class="alert alert-info mbot15">
                           <?php echo _l('accepted_identity_info',array(
                              _l('licence_lowercase'),
                              '<b>'.$licence->acceptance_firstname . ' ' . $licence->acceptance_lastname . '</b> (<a href="mailto:'.$licence->acceptance_email.'">'.$licence->acceptance_email.'</a>)',
                              '<b>'. _dt($licence->acceptance_date).'</b>',
                              '<b>'.$licence->acceptance_ip.'</b>'.(is_admin() ? '&nbsp;<a href="'.admin_url('licences/clear_acceptance_info/'.$licence->id).'" class="_delete text-muted" data-toggle="tooltip" data-title="'._l('clear_this_information').'"><i class="fa fa-remove"></i></a>' : '')
                              )); ?>
                        </div>
                     </div>
                     <?php } ?>
                     <?php if($licence->program_id != 0){ ?>
                     <div class="col-md-12">
                        <h4 class="font-medium mbot15"><?php echo _l('related_to_program',array(
                           _l('licence_related'),
                           _l('program_lowercase'),
                           '<a href="'.admin_url('programs/list_programs/'.$licence->program_id).'" target="_blank">' . format_program_number($licence->program_id) . '</a>',
                           )); ?></h4>
                     </div>

                     <div class="col-md-12">
                        <h4 class="font-medium mbot15"><?php echo _l('related_to_inspection',array(
                           _l('licence_related'),
                           _l('inspection_related'),
                           '<a href="'.admin_url('inspections/list_inspections/'.$licence->inspection_id).'" target="_blank">' . format_inspection_number($licence->inspection_id) . '</a>',
                           )); ?></h4>
                     </div>
                     <?php } ?>
                     <div class="col-md-6 col-sm-6">
                        <h4 class="bold">
                           <?php
                              $tags = get_tags_in($licence->id,'licence');
                              if(count($tags) > 0){
                                echo '<i class="fa fa-tag" aria-hidden="true" data-toggle="tooltip" data-title="'.html_escape(implode(', ',$tags)).'"></i>';
                              }
                              ?>
                           <a href="<?php echo admin_url('licences/licence/'.$licence->id); ?>">
                           <span id="licence-number">
                           <?php echo format_licence_number($licence->id); ?>
                           </span>
                           </a>
                        </h4>
                        <address>
                           <?php echo format_organization_info(); ?>
                        </address>
                     </div>
                     <div class="col-sm-6 text-right">
                        <span class="bold"><?php echo _l('licence_to'); ?>:</span>
                        <address>
                           <?php echo format_customer_info($licence, 'licence', 'billing', true); ?>
                        </address>
                        <?php if($licence->include_shipping == 1 && $licence->show_shipping_on_licence == 1){ ?>
                        <span class="bold"><?php echo _l('ship_to'); ?>:</span>
                        <address>
                           <?php echo format_customer_info($licence, 'licence', 'shipping'); ?>
                        </address>
                        <?php } ?>
                        <p class="no-mbot">
                           <span class="bold">
                           <?php echo _l('licence_data_date'); ?>:
                           </span>
                           <?php echo $licence->date; ?>
                        </p>
                        <?php if(!empty($licence->duedate)){ ?>
                        <p class="no-mbot">
                           <span class="bold"><?php echo _l('licence_data_expiry_date'); ?>:</span>
                           <?php echo $licence->duedate; ?>
                        </p>
                        <?php } ?>
                        <?php if(!empty($licence->reference_no)){ ?>
                        <p class="no-mbot">
                           <span class="bold"><?php echo _l('reference_no'); ?>:</span>
                           <?php echo $licence->reference_no; ?>
                        </p>
                        <?php } ?>
                        <?php if($licence->inspector_staff_id != 0 && get_option('show_assigned_on_licences') == 1){ ?>
                        <p class="no-mbot">
                           <span class="bold"><?php echo _l('inspector_staff_string'); ?>:</span>
                           <?php echo get_staff_full_name($licence->inspector_staff_id); ?>
                        </p>
                        <?php } ?>

                        <?php $pdf_custom_fields = get_custom_fields('licence',array('show_on_pdf'=>1));
                           foreach($pdf_custom_fields as $field){
                           $value = get_custom_field_value($licence->id,$field['id'],'licence');
                           if($value == ''){continue;} ?>
                        <p class="no-mbot">
                           <span class="bold"><?php echo $field['name']; ?>: </span>
                           <?php echo $value; ?>
                        </p>
                        <?php } ?>
                     </div>
                  </div>
                  <div class="row">
                     <div class="col-md-12">
                        <div class="table-responsive">
                              <?php
                                 //$items = get_items_table_data($licence, 'licence', 'html', true);
                                 //echo $items->table();
                              ?>
                        </div>
                     </div>

                     <div class="col-md-5 col-md-offset-7">
                     </div>
                     <?php if(count($licence->attachments) > 0){ ?>
                     <div class="clearfix"></div>
                     <hr />
                     <div class="col-md-12">
                        <p class="bold text-muted"><?php echo _l('licence_files'); ?></p>
                     </div>
                     <?php foreach($licence->attachments as $attachment){
                        $attachment_url = site_url('download/file/sales_attachment/'.$attachment['attachment_key']);
                        if(!empty($attachment['external'])){
                          $attachment_url = $attachment['external_link'];
                        }
                        ?>
                     <div class="mbot15 row col-md-12" data-attachment-id="<?php echo $attachment['id']; ?>">
                        <div class="col-md-8">
                           <div class="pull-left"><i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i></div>
                           <a href="<?php echo $attachment_url; ?>" target="_blank"><?php echo $attachment['file_name']; ?></a>
                           <br />
                           <small class="text-muted"> <?php echo $attachment['filetype']; ?></small>
                        </div>
                        <div class="col-md-4 text-right">
                           <?php if($attachment['visible_to_customer'] == 0){
                              $icon = 'fa fa-toggle-off';
                              $tooltip = _l('show_to_customer');
                              } else {
                              $icon = 'fa fa-toggle-on';
                              $tooltip = _l('hide_from_customer');
                              }
                              ?>
                           <a href="#" data-toggle="tooltip" onclick="toggle_file_visibility(<?php echo $attachment['id']; ?>,<?php echo $licence->id; ?>,this); return false;" data-title="<?php echo $tooltip; ?>"><i class="<?php echo $icon; ?>" aria-hidden="true"></i></a>
                           <?php if($attachment['staffid'] == get_staff_user_id() || is_admin()){ ?>
                           <a href="#" class="text-danger" onclick="delete_licence_attachment(<?php echo $attachment['id']; ?>); return false;"><i class="fa fa-times"></i></a>
                           <?php } ?>
                        </div>
                     </div>
                     <?php } ?>
                     <?php } ?>
                     <?php if($licence->clientnote != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('licence_note'); ?></p>
                        <p><?php echo $licence->clientnote; ?></p>
                     </div>
                     <?php } ?>
                     <?php if($licence->terms != ''){ ?>
                     <div class="col-md-12 mtop15">
                        <p class="bold text-muted"><?php echo _l('terms_and_conditions'); ?></p>
                        <p><?php echo $licence->terms; ?></p>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_licence_items">
               <span class="label label-success mbot5 mtop5"><?php echo _l($licence->licence_item_info); ?> </span>
               <hr />
               <?php render_datatable(array( _l( 'licence_items_table'), _l( 'serial_number'), _l( 'unit_number'), _l( 'process')), 'licence_items'); ?>
               <?php echo _l('this_list_has_been_load_from_master_of_equipment'); ?>

            </div>
            <div role="tabpanel" class="tab-pane" id="tab_inspection_items">
               <span class="label label-success mbot5 mtop5"><?php echo _l('inspection_items_proposed'); ?> </span>
               <hr />
               <?php render_datatable(array( _l( 'inspection_items_table'), _l( 'serial_number'), _l( 'unit_number'), _l( 'process')), 'inspection_items'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_program_items">
               <span class="label label-success mbot5 mtop5"><?php echo _l('program_items_proposed'); ?> </span>
               <hr />
               <?php render_datatable(array( _l( 'program_items_table'), _l( 'serial_number'), _l( 'unit_number'), _l( 'process')), 'program_items'); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_tasks">
               <?php init_relation_tasks_table(array('data-new-rel-id'=>$licence->id,'data-new-rel-type'=>'licence')); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" data-toggle="modal" class="btn btn-info" data-target=".reminder-modal-licence-<?php echo $licence->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('licence_set_reminder_title'); ?></a>
               <hr />
               <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$licence->id,'name'=>'licence','members'=>$members,'reminder_title'=>_l('licence_set_reminder_title'))); ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_emails_tracking">
               <?php
                  $this->load->view('admin/includes/emails_tracking',array(
                     'tracked_emails'=>
                     get_tracked_emails($licence->id, 'licence'))
                  );
                  ?>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_notes">
               <?php echo form_open(admin_url('licences/add_note/'.$licence->id),array('id'=>'sales-notes','class'=>'licence-notes-form')); ?>
               <?php echo render_textarea('description'); ?>
               <div class="text-right">
                  <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('licence_add_note'); ?></button>
               </div>
               <?php echo form_close(); ?>
               <hr />
               <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_activity">
               <div class="row">
                  <div class="col-md-12">
                     <div class="activity-feed">
                        <?php foreach($activity as $activity){
                           $_custom_data = false;
                           ?>
                        <div class="feed-item" data-sale-activity-id="<?php echo $activity['id']; ?>">
                           <div class="date">
                              <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($activity['date']); ?>">
                              <?php echo time_ago($activity['date']); ?>
                              </span>
                           </div>
                           <div class="text">
                              <?php if(is_numeric($activity['staffid']) && $activity['staffid'] != 0){ ?>
                              <a href="<?php echo admin_url('profile/'.$activity["staffid"]); ?>">
                              <?php echo staff_profile_image($activity['staffid'],array('staff-profile-xs-image pull-left mright5'));
                                 ?>
                              </a>
                              <?php } ?>
                              <?php
                                 $additional_data = '';
                                 if(!empty($activity['additional_data'])){
                                  $additional_data = unserialize($activity['additional_data']);
                                  $i = 0;
                                  foreach($additional_data as $data){
                                    if(strpos($data,'<original_status>') !== false){
                                      $original_status = get_string_between($data, '<original_status>', '</original_status>');
                                      $additional_data[$i] = format_licence_status($original_status,'',false);
                                    } else if(strpos($data,'<new_status>') !== false){
                                      $new_status = get_string_between($data, '<new_status>', '</new_status>');
                                      $additional_data[$i] = format_licence_status($new_status,'',false);
                                    } else if(strpos($data,'<status>') !== false){
                                      $status = get_string_between($data, '<status>', '</status>');
                                      $additional_data[$i] = format_licence_status($status,'',false);
                                    } else if(strpos($data,'<custom_data>') !== false){
                                      $_custom_data = get_string_between($data, '<custom_data>', '</custom_data>');
                                      unset($additional_data[$i]);
                                    }
                                    $i++;
                                  }
                                 }
                                 $_formatted_activity = _l($activity['description'],$additional_data);
                                 if($_custom_data !== false){
                                 $_formatted_activity .= ' - ' .$_custom_data;
                                 }
                                 if(!empty($activity['full_name'])){
                                 $_formatted_activity = $activity['full_name'] . ' - ' . $_formatted_activity;
                                 }
                                 echo $_formatted_activity;
                                 if(is_admin()){
                                 echo '<a href="#" class="pull-right text-danger" onclick="delete_sale_activity('.$activity['id'].'); return false;"><i class="fa fa-remove"></i></a>';
                                 }
                                 ?>
                           </div>
                        </div>
                        <?php } ?>
                     </div>
                  </div>
               </div>
            </div>
            <div role="tabpanel" class="tab-pane" id="tab_views">
               <?php
                  $views_activity = get_views_tracking('licence',$licence->id);
                  if(count($views_activity) === 0) {
                     echo '<h4 class="no-mbot">'._l('not_viewed_yet',_l('licence_lowercase')).'</h4>';
                  }
                  foreach($views_activity as $activity){ ?>
               <p class="text-success no-margin">
                  <?php echo _l('view_date') . ': ' . _dt($activity['date']); ?>
               </p>
               <p class="text-muted">
                  <?php echo _l('view_ip') . ': ' . $activity['view_ip']; ?>
               </p>
               <hr />
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</div>
<script>
   init_items_sortable(true);
   init_btn_with_tooltips();
   init_datepicker();
   init_selectpicker();
   init_form_reminder();
   init_tabs_scrollable();
   <?php if($send_later) { ?>
      licence_licence_send(<?php echo $licence->id; ?>);
   <?php } ?>
</script>
<?php $this->load->view('admin/licences/licence_send_to_client'); ?>
