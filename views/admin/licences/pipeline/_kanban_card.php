<?php defined('BASEPATH') or exit('No direct script access allowed');
   if ($licence['status'] == $status) { ?>
<li data-licence-id="<?php echo $licence['id']; ?>" class="<?php if($licence['invoiceid'] != NULL){echo 'not-sortable';} ?>">
   <div class="panel-body">
      <div class="row">
         <div class="col-md-12">
            <h4 class="bold pipeline-heading"><a href="<?php echo admin_url('licences/list_licences/'.$licence['id']); ?>" onclick="licence_pipeline_open(<?php echo $licence['id']; ?>); return false;"><?php echo format_licence_number($licence['id']); ?></a>
               <?php if(has_permission('licences','','edit')){ ?>
               <a href="<?php echo admin_url('licences/licence/'.$licence['id']); ?>" target="_blank" class="pull-right"><small><i class="fa fa-pencil-square-o" aria-hidden="true"></i></small></a>
               <?php } ?>
            </h4>
            <span class="inline-block full-width mbot10">
            <a href="<?php echo admin_url('clients/client/'.$licence['clientid']); ?>" target="_blank">
            <?php echo $licence['company']; ?>
            </a>
            </span>
         </div>
         <div class="col-md-12">
            <div class="row">
               <div class="col-md-8">
                  <span class="bold">
                  <?php echo _l('licence_total') . ':' . app_format_money($licence['total'], $licence['currency_name']); ?>
                  </span>
                  <br />
                  <?php echo _l('licence_data_date') . ': ' . _d($licence['date']); ?>
                  <?php if(is_date($licence['duedate']) || !empty($licence['duedate'])){
                     echo '<br />';
                     echo _l('licence_data_expiry_date') . ': ' . _d($licence['duedate']);
                     } ?>
               </div>
               <div class="col-md-4 text-right">
                  <small><i class="fa fa-paperclip"></i> <?php echo _l('licence_notes'); ?>: <?php echo total_rows(db_prefix().'notes', array(
                     'rel_id' => $licence['id'],
                     'rel_type' => 'licence',
                     )); ?></small>
               </div>
               <?php $tags = get_tags_in($licence['id'],'licence');
                  if(count($tags) > 0){ ?>
               <div class="col-md-12">
                  <div class="mtop5 kanban-tags">
                     <?php echo render_tags($tags); ?>
                  </div>
               </div>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</li>
<?php } ?>
