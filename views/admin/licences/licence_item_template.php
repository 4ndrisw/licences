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
                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('institution'); ?></div>
                          <div class="card-body less-padding text-info">
                            <p class="card-text"><?php echo get_client($licence_item->institution_id)->company;?></p>
                          </div>
                        </div>
                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('inspector'); ?></div>
                          <div class="card-body less-padding text-info">
                            <p class="card-text"><?php echo get_client($licence_item->inspector_id)->company;?></p>
                          </div>
                        </div>
                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('client'); ?></div>
                          <div class="card-body less-padding text-info">
                            <p class="card-text"><?php echo get_client($licence_item->clientid)->company;?></p>
                          </div>
                        </div>


                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('surveyor'); ?></div>
                          <div class="card-body less-padding text-info">
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('surveyor'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo get_client($licence_item->surveyor_id)->company;?></p>
                                </div>
                              </div>

                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('staff'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo ($surveyor_staff->firstname.' '.$surveyor_staff->lastname);?></p>
                                </div>
                              </div>

                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('skp_number'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo ($surveyor_staff->skp_number);?></p>
                                </div>
                              </div>

                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('skp_datestart'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($surveyor_staff->skp_datestart);?></p>
                                </div>
                              </div>

                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('skp_dateend'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($surveyor_staff->skp_dateend);?></p>
                                </div>
                              </div>


                          </div>
                        </div>

                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('inspection'); ?></div>
                          <div class="card-body less-padding text-info">
                              
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('inspection_number'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo format_inspection_number($licence_item->inspection_id);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('inspection_date'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($licence_item->inspection_date);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('tanggal_penerbitan'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($licence_item->tanggal_penerbitan);?></p>
                                </div>
                              </div>


                          </div>
                        </div>
                        

                        <div class="card border-info mbot5">
                          <div class="card-header less-padding"><?php echo _l('licences'); ?></div>
                          <div class="card-body less-padding text-info">
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('licence_id'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo format_licence_number($licence_item->licence_id);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('nomor_suket'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo $licence_item->nomor_suket;?></p>
                                  <p class="card-text"><?php echo format_licence_item_number($licence_item->licence_id, $licence_item->kelompok_alat, $licence_item->nomor_suket, $id);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('tanggal_suket'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($licence_item->tanggal_suket);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('tanggal_kadaluarsa'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo html_date($licence_item->tanggal_kadaluarsa);?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('kepala_dinas_nama'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo $licence_item->kepala_dinas_nama;?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('kepala_dinas_nip'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo $licence_item->kepala_dinas_nip;?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('inspector_staff_nama'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo $licence_item->inspector_staff_nama;?></p>
                                </div>
                              </div>
                              <div class="card border-info mbot5">
                                <div class="card-header less-padding"><?php echo _l('inspector_staff_nip'); ?></div>
                                <div class="card-body less-padding text-info">
                                  <p class="card-text"><?php echo $licence_item->inspector_staff_nip;?></p>
                                </div>
                              </div>


                          </div>
                        </div>
                        
                     <div class="col-md-12 tw-ml-2 tw-mr-2">
                      <hr class="hr-panel-heading" />
                     </div>

                      <?php
                        echo '<pre>';
                        var_dump($licence_item);
                        echo '===================<br />';
                        var_dump($surveyor_staff);
                        echo '</pre>';
                      ?>
                     </div>

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
