<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_hidden('licences_settings'); ?>
<div class="horizontal-scrollable-tabs mbot15">
   <div role="tabpanel" class="tab-pane" id="licences">
      <div class="form-group">
         <label class="control-label" for="licence_prefix"><?php echo _l('licence_prefix'); ?></label>
         <input type="text" name="settings[licence_prefix]" class="form-control" value="<?php echo get_option('licence_prefix'); ?>">
      </div>
      <hr />
      <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('next_licence_number_tooltip'); ?>"></i>
      <?php echo render_input('settings[next_licence_number]','next_licence_number',get_option('next_licence_number'), 'number', ['min'=>1]); ?>
      <hr />
      <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('due_after_help'); ?>"></i>
      <?php echo render_input('settings[licence_qrcode_size]', 'licence_qrcode_size', get_option('licence_qrcode_size')); ?>
      <hr />
      <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('due_after_help'); ?>"></i>
      <?php echo render_input('settings[licence_due_after]','licence_due_after',get_option('licence_due_after')); ?>
      <hr />
      <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('licence_number_of_date_tooltip'); ?>"></i>
      <?php echo render_input('settings[licence_number_of_date]','licence_number_of_date',get_option('licence_number_of_date'), 'number', ['min'=>0]); ?>
      <hr />
      <?php render_yes_no_option('licence_send_telegram_message','licence_send_telegram_message'); ?>
      <hr />
      <?php render_yes_no_option('delete_only_on_last_licence','delete_only_on_last_licence'); ?>
      <hr />
      <?php render_yes_no_option('licence_number_decrement_on_delete','decrement_licence_number_on_delete','decrement_licence_number_on_delete_tooltip'); ?>
      <hr />
      <?php echo render_yes_no_option('allow_staff_view_licences_assigned','allow_staff_view_licences_assigned'); ?>
      <hr />
      <?php render_yes_no_option('view_licence_only_logged_in','require_client_logged_in_to_view_licence'); ?>
      <hr />
      <?php render_yes_no_option('show_assigned_on_licences','show_assigned_on_licences'); ?>
      <hr />
      <?php render_yes_no_option('show_project_on_licence','show_project_on_licence'); ?>
      <hr />

      <?php
      $staff = $this->staff_model->get('', ['active' => 1]);
      $selected = get_option('default_licence_assigned');
      foreach($staff as $member){
       
         if($selected == $member['staffid']) {
           $selected = $member['staffid'];
         
       }
      }
      echo render_select('settings[default_licence_assigned]',$staff,array('staffid',array('firstname','lastname')),'default_licence_assigned_string',$selected);
      ?>
      <hr />
      <?php render_yes_no_option('exclude_licence_from_client_area_with_draft_status','exclude_licence_from_client_area_with_draft_status'); ?>
      <hr />   
      <?php render_yes_no_option('licence_accept_identity_confirmation','licence_accept_identity_confirmation'); ?>
      <hr />
      <?php echo render_input('settings[licence_year]','licence_year',get_option('licence_year'), 'number', ['min'=>2020]); ?>
      <hr />
      
      <div class="form-group">
         <label for="licence_number_format" class="control-label clearfix"><?php echo _l('licence_number_format'); ?></label>
         <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[licence_number_format]" value="1" id="e_number_based" <?php if(get_option('licence_number_format') == '1'){echo 'checked';} ?>>
            <label for="e_number_based"><?php echo _l('licence_number_format_number_based'); ?></label>
         </div>
         <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[licence_number_format]" value="2" id="e_year_based" <?php if(get_option('licence_number_format') == '2'){echo 'checked';} ?>>
            <label for="e_year_based"><?php echo _l('licence_number_format_year_based'); ?> (YYYY.000001)</label>
         </div>
         <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[licence_number_format]" value="3" id="e_short_year_based" <?php if(get_option('licence_number_format') == '3'){echo 'checked';} ?>>
            <label for="e_short_year_based">000001-YY</label>
         </div>
         <div class="radio radio-primary radio-inline">
            <input type="radio" name="settings[licence_number_format]" value="4" id="e_year_month_based" <?php if(get_option('licence_number_format') == '4'){echo 'checked';} ?>>
            <label for="e_year_month_based">000001.MM.YYYY</label>
         </div>
         <hr />
      </div>
      <div class="row">
         <div class="col-md-12">
            <?php echo render_input('settings[licences_pipeline_limit]','pipeline_limit_status',get_option('licences_pipeline_limit')); ?>
         </div>
         <div class="col-md-7">
            <label for="default_proposals_pipeline_sort" class="control-label"><?php echo _l('default_pipeline_sort'); ?></label>
            <select name="settings[default_licences_pipeline_sort]" id="default_licences_pipeline_sort" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
               <option value="datecreated" <?php if(get_option('default_licences_pipeline_sort') == 'datecreated'){echo 'selected'; }?>><?php echo _l('licences_sort_datecreated'); ?></option>
               <option value="date" <?php if(get_option('default_licences_pipeline_sort') == 'date'){echo 'selected'; }?>><?php echo _l('licences_sort_licence_date'); ?></option>
               <option value="pipeline_order" <?php if(get_option('default_licences_pipeline_sort') == 'pipeline_order'){echo 'selected'; }?>><?php echo _l('licences_sort_pipeline'); ?></option>
               <option value="duedate" <?php if(get_option('default_licences_pipeline_sort') == 'duedate'){echo 'selected'; }?>><?php echo _l('licences_sort_expiry_date'); ?></option>
            </select>
         </div>
         <div class="col-md-5">
            <div class="mtop30 text-right">
               <div class="radio radio-inline radio-primary">
                  <input type="radio" id="k_desc_licence" name="settings[default_licences_pipeline_sort_type]" value="asc" <?php if(get_option('default_licences_pipeline_sort_type') == 'asc'){echo 'checked';} ?>>
                  <label for="k_desc_licence"><?php echo _l('order_ascending'); ?></label>
               </div>
               <div class="radio radio-inline radio-primary">
                  <input type="radio" id="k_asc_licence" name="settings[default_licences_pipeline_sort_type]" value="desc" <?php if(get_option('default_licences_pipeline_sort_type') == 'desc'){echo 'checked';} ?>>
                  <label for="k_asc_licence"><?php echo _l('order_descending'); ?></label>
               </div>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
      <hr  />
      <?php echo render_textarea('settings[predefined_clientnote_licence]','predefined_clientnote',get_option('predefined_clientnote_licence'),array('rows'=>6)); ?>
      <?php echo render_textarea('settings[predefined_terms_licence]','predefined_terms',get_option('predefined_terms_licence'),array('rows'=>6)); ?>
   </div>
 <?php hooks()->do_action('after_licences_tabs_content'); ?>
</div>
