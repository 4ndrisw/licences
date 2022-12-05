<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="mtop15 preview-top-wrapper">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="licence-html-logo">
               <?php echo get_dark_company_logo(); ?>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   <div class="top" data-sticky data-sticky-class="preview-sticky-header">
      <div class="container preview-sticky-container">
         <div class="row">
            <div class="col-md-12">
               <div class="col-md-3">
                  <h3 class="bold no-mtop licence-html-number no-mbot">
                     <span class="sticky-visible hide">
                     <?php echo format_licence_number($licence->id); ?>
                     </span>
                  </h3>
                  <h4 class="licence-html-status mtop7">
                     <?php echo format_licence_status($licence->status,'',true); ?>
                  </h4>
               </div>
               <div class="col-md-9">         
                  <?php
                     // Is not accepted, declined and expired
                     if ($licence->status != 4 && $licence->status != 3 && $licence->status != 5) {
                       $can_be_accepted = true;
                       if($identity_confirmation_enabled == '0'){
                         echo form_open($this->uri->uri_string(), array('class'=>'pull-right mtop7 action-button'));
                         echo form_hidden('licence_action', 4);
                         echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_licence').'</button>';
                         echo form_close();
                       } else {
                         echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_licence').'</button>';
                       }
                     } else if($licence->status == 3){
                       if (($licence->duedate >= date('Y-m-d') || !$licence->duedate) && $licence->status != 5) {
                         $can_be_accepted = true;
                         if($identity_confirmation_enabled == '0'){
                           echo form_open($this->uri->uri_string(),array('class'=>'pull-right mtop7 action-button'));
                           echo form_hidden('licence_action', 4);
                           echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-success action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_licence').'</button>';
                           echo form_close();
                         } else {
                           echo '<button type="button" id="accept_action" class="btn btn-success mright5 mtop7 pull-right action-button accept"><i class="fa fa-check"></i> '._l('clients_accept_licence').'</button>';
                         }
                       }
                     }
                     // Is not accepted, declined and expired
                     if ($licence->status != 4 && $licence->status != 3 && $licence->status != 5) {
                       echo form_open($this->uri->uri_string(), array('class'=>'pull-right action-button mright5 mtop7'));
                       echo form_hidden('licence_action', 3);
                       echo '<button type="submit" data-loading-text="'._l('wait_text').'" autocomplete="off" class="btn btn-default action-button accept"><i class="fa fa-remove"></i> '._l('clients_decline_licence').'</button>';
                       echo form_close();
                     }
                     ?>
                  <?php echo form_open(site_url('licences/pdf/'.$licence->id), array('class'=>'pull-right action-button')); ?>
                  <button type="submit" name="licencepdf" class="btn btn-default action-button download mright5 mtop7" value="licencepdf">
                  <i class="fa fa-file-pdf-o"></i>
                  <?php echo _l('clients_invoice_html_btn_download'); ?>
                  </button>
                  <?php echo form_close(); ?>
                  <?php if((is_client_logged_in() && has_contact_permission('licences'))  || is_staff_member()){ ?>
                  <a href="<?php echo site_url('clients/licences/'); ?>" class="btn btn-default pull-right mright5 mtop7 action-button go-to-portal">
                  <?php echo _l('client_go_to_dashboard'); ?>
                  </a>
                  <?php } ?>
               </div>
            </div>
            <div class="clearfix"></div>
         </div>
      </div>
   </div>
</div>
<div class="clearfix"></div>
<div class="panel_s mtop20">
   <div class="panel-body">
      <div class="col-md-10 col-md-offset-1">
         <div class="row mtop20">
            <div class="col-md-6 col-sm-6 transaction-html-info-col-left">
               <h4 class="bold licence-html-number"><?php echo format_licence_number($licence->id); ?></h4>
               <address class="licence-html-company-info">
                  <?php echo format_organization_info(); ?>
               </address>
            </div>
            <div class="col-sm-6 text-right transaction-html-info-col-right">
               <span class="bold licence_to"><?php echo _l('licence_to'); ?>:</span>
               <address class="licence-html-customer-billing-info">
                  <?php echo format_customer_info($licence, 'licence', 'billing'); ?>
               </address>
               <!-- shipping details -->
               <?php if($licence->include_shipping == 1 && $licence->show_shipping_on_licence == 1){ ?>
               <span class="bold licence_ship_to"><?php echo _l('ship_to'); ?>:</span>
               <address class="licence-html-customer-shipping-info">
                  <?php echo format_customer_info($licence, 'licence', 'shipping'); ?>
               </address>
               <?php } ?>
            </div>
         </div>
         <div class="row">
            <div class="col-md-6">
               <div class="container-fluid">
                  <?php if(!empty($licence_members)){ ?>
                     <strong><?= _l('licence_members') ?></strong>
                     <ul class="licence_members">
                     <?php 
                        foreach($licence_members as $member){
                          echo ('<li style="list-style:auto" class="member">' . $member['firstname'] .' '. $member['lastname'] .'</li>');
                         }
                     ?>
                     </ul>
                  <?php } ?>
               </div>
            </div>
            <div class="col-md-6 text-right">
               <p class="no-mbot licence-html-date">
                  <span class="bold">
                  <?php echo _l('licence_data_date'); ?>:
                  </span>
                  <?php echo _d($licence->date); ?>
               </p>
               <?php if(!empty($licence->duedate)){ ?>
               <p class="no-mbot licence-html-expiry-date">
                  <span class="bold"><?php echo _l('licence_data_expiry_date'); ?></span>:
                  <?php echo _d($licence->duedate); ?>
               </p>
               <?php } ?>
               <?php if(!empty($licence->reference_no)){ ?>
               <p class="no-mbot licence-html-reference-no">
                  <span class="bold"><?php echo _l('reference_no'); ?>:</span>
                  <?php echo $licence->reference_no; ?>
               </p>
               <?php } ?>
               <?php if($licence->program_id != 0 && get_option('show_project_on_licence') == 1){ ?>
               <p class="no-mbot licence-html-project">
                  <span class="bold"><?php echo _l('project'); ?>:</span>
                  <?php echo get_project_name_by_id($licence->program_id); ?>
               </p>
               <?php } ?>
               <?php $pdf_custom_fields = get_custom_fields('licence',array('show_on_pdf'=>1,'show_on_client_portal'=>1));
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
                     $items = get_licence_items_table_data($licence, 'licence');
                     echo $items->table();
                  ?>
               </div>
            </div>


            <div class="row mtop25">
               <div class="col-md-12">
                  <div class="col-md-6 text-center">
                     <div class="bold"><?php echo get_option('invoice_company_name'); ?></div>
                     <div class="qrcode text-center">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_licence_upload_path('licence').$licence->id.'/assigned-'.$licence_number.'.png')); ?>" class="img-responsive center-block licence-assigned" alt="licence-<?= $licence->id ?>">
                     </div>
                     <div class="assigned">
                     <?php if($licence->assigned != 0 && get_option('show_assigned_on_licences') == 1){ ?>
                        <?php echo get_staff_full_name($licence->assigned); ?>
                     <?php } ?>

                     </div>
                  </div>
                     <div class="col-md-6 text-center">
                       <div class="bold"><?php echo $client_company; ?></div>
                       <?php if(!empty($licence->signature)) { ?>
                           <div class="bold">
                              <p class="no-mbot"><?php echo _l('licence_signed_by') . ": {$licence->acceptance_firstname} {$licence->acceptance_lastname}"?></p>
                              <p class="no-mbot"><?php echo _l('licence_signed_date') . ': ' . _dt($licence->acceptance_date) ?></p>
                              <p class="no-mbot"><?php echo _l('licence_signed_ip') . ": {$licence->acceptance_ip}"?></p>
                           </div>
                           <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                           <?php if($licence->signed == 1 && has_permission('licences','','delete')){ ?>
                              <a href="<?php echo admin_url('licences/clear_signature/'.$licence->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                                 <i class="fa fa-remove"></i>
                              </a>
                           <?php } ?>
                           </p>
                           <div class="customer_signature text-center">
                              <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_licence_upload_path('licence').$licence->id.'/'.$licence->signature)); ?>" class="img-responsive center-block licence-signature" alt="licence-<?= $licence->id ?>">
                           </div>
                       <?php } ?>
                     </div>
               </div>
            </div>




            <?php if(!empty($licence->clientnote)){ ?>
            <div class="col-md-12 licence-html-note">
            <hr />
               <b><?php echo _l('licence_order'); ?></b><br /><?php echo $licence->clientnote; ?>
            </div>
            <?php } ?>
            <?php if(!empty($licence->terms)){ ?>
            <div class="col-md-12 licence-html-terms-and-conditions">
               <b><?php echo _l('terms_and_conditions'); ?>:</b><br /><?php echo $licence->terms; ?>
            </div>
            <?php } ?>

         </div>
      </div>
   </div>
</div>
<?php
   if($identity_confirmation_enabled == '1' && $can_be_accepted){
    get_template_part('identity_confirmation_form',array('formData'=>form_hidden('licence_action',4)));
   }
   ?>
<script>
   $(function(){
     new Sticky('[data-sticky]');
   })
</script>
