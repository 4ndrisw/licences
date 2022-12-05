<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php
         echo form_open($this->uri->uri_string(),array('id'=>'licence-item-form','class'=>'_transaction_form'));
         if(isset($licence_item)){
            //echo form_hidden('isedit');

         }
         ?>
         <div class="panel_s licence">
            <div class="panel-body">
               <?php if(isset($licence)){ ?>
               <?php echo _l('licence_status') .' '. format_licence_status($licence->status); ?>
               <hr class="hr-panel-heading" />
               <?php } ?>
               <div class="row">
                  <div class="col-md-8">
                    <?php $this->load->view('admin/licences/licence_template/'. $jenis_pesawat); ?>
                  </div>
                  <div class="col-md-4">
                     <h4><?=_l('company') ?><span class="badge badge-success mbot5 mtop5"><?= $licence->client->company ?></span></h4>
                     <h5><?=_l('item') ?><span class="badge badge-success mbot5 mtop5"><?= $licence_item->jenis_pesawat ?></span></h5>


                  </div>
                  
                  

               </div>
            </div>
         </div>



         <div class="row">
          <div class="col-md-12 mtop15">
            <div class="btn-bottom-toolbar text-right">
                  <div class="btn-group dropup">
                   <button type="button" class="btn-tr btn btn-info licence-form-submit transaction-submit">
                       <?php echo _l('submit'); ?>
                   </button>
                  </div>
               </div>
            <div class="btn-bottom-pusher"></div>
          </div>
         </div>



         <?php echo form_close(); ?>

      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<script>
   $(function(){
      validate_licence_form();
      // Init accountacy currency symbol
      //init_currency();
      // Project ajax search
      //init_ajax_project_search_by_customer_id();
      // Maybe items ajax search
       //init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
   });
</script>
</body>
</html>
