<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <?php
         echo form_open($this->uri->uri_string(),array('id'=>'inspection-item-form','class'=>'_transaction_form'));
         if(isset($inspection_item)){
            //echo form_hidden('isedit');

         }
         ?>
         <div class="panel_s inspection">
            <div class="panel-body">
               <hr class="hr-panel-heading" />

               <div class="row">
                  <div class="col-md-8">
                    

                        <?php foreach($kelompok_alat as $key => $kelompok){ ?>
                           <?php echo form_hidden('number['.$key.'][institution_id]',$institution_id); ?>
                           <?php echo form_hidden('number['.$key.'][category]',$kelompok['name']); ?>
                              <i class="fa fa-question-circle pull-left rpad5" data-toggle="tooltip" data-title="<?php echo _l('next_licence_number_tooltip'); ?>"></i><?php echo strtoupper($kelompok['name']); ?>
                              <?php $value = (isset($institution_next_numbers[$kelompok['name']]) ? $institution_next_numbers[$kelompok['name']] : ''); ?>
                              <?php echo render_input('number['.$key.'][next_number]','',$value, 'number', ['min'=>1]); ?>
                        <?php } ?>


                    <?php //$this->load->view('admin/inspections/inspection_template/'. $jenis_pesawat); ?>
                  </div>
                  <div class="col-md-4">

                  </div>
                  
                  

               </div>
            </div>
         </div>



         <div class="row">
          <div class="col-md-12 mtop15">
            <div class="btn-bottom-toolbar text-right">
                  <div class="btn-group dropup">
                   <button type="button" class="btn-tr btn btn-info inspection-form-submit transaction-submit">
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
      //validate_licence_format_number_form();
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




