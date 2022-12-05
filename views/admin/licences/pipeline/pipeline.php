<?php defined('BASEPATH') or exit('No direct script access allowed');
$i = 0;
$has_permission_edit = has_permission('licences','','edit');
foreach ($licence_statuses as $status) {
  $kanBan = new \app\services\licences\LicencesPipeline($status);
  $kanBan->search($this->input->get('search'))
    ->sortBy($this->input->get('sort_by'),$this->input->get('sort'));
    if($this->input->get('refresh')) {
        $kanBan->refresh($this->input->get('refresh')[$status] ?? null);
    }
  $licences = $kanBan->get();
  $total_licences = count($licences);
  $total_pages = $kanBan->totalPages();
 ?>
 <ul class="kan-ban-col" data-col-status-id="<?php echo $status; ?>" data-total-pages="<?php echo $total_pages; ?>" data-total="<?php echo $total_licences; ?>">
  <li class="kan-ban-col-wrapper">
    <div class="border-right panel_s no-mbot">
      <div class="panel-heading-bg <?php echo licence_status_color_class($status); ?>-bg licence-status-pipeline-<?php echo licence_status_color_class($status); ?>">
        <div class="kan-ban-step-indicator<?php if($i == count($licence_statuses) -1){ echo ' kan-ban-step-indicator-full'; } ?>"></div>
        <?php echo licence_status_by_id($status); ?> - <?php echo $kanBan->countAll() . ' ' . _l('licences') ?>
      </div>
      <div class="kan-ban-content-wrapper">
        <div class="kan-ban-content">
          <ul class="sortable<?php if($has_permission_edit){echo ' status pipeline-status';} ?>" data-status-id="<?php echo $status; ?>">
            <?php
            foreach ($licences as $licence) {
              $this->load->view('admin/licences/pipeline/_kanban_card',array('licence'=>$licence,'status'=>$status));
            } ?>
            <?php if($total_licences > 0 ){ ?>
              <li class="text-center not-sortable kanban-load-more" data-load-status="<?php echo $status; ?>">
                <a href="#" class="btn btn-default btn-block<?php if($total_pages <= 1 || $kanBan->getPage() === $total_pages){echo ' disabled';} ?>" data-page="<?php echo $kanBan->getPage(); ?>" onclick="kanban_load_more(<?php echo $status; ?>,this,'licences/pipeline_load_more',310,360); return false;";><?php echo _l('load_more'); ?></a>
              </li>
            <?php } ?>
            <li class="text-center not-sortable mtop30 kanban-empty<?php if($total_licences > 0){echo ' hide';} ?>">
              <h4>
                <i class="fa fa-circle-o-notch" aria-hidden="true"></i><br /><br />
                <?php echo _l('no_licences_found'); ?></h4>
              </li>
            </ul>
          </div>
        </div>
      </li>
    </ul>
    <?php $i++; } ?>
