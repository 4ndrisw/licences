<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
  <div class="panel_s mbot10">
   <div class="panel-body _buttons">
    <?php $this->load->view('admin/licences/licences_top_stats');
    ?>
    <?php if(has_permission('licences','','create')){ ?>
     <a href="<?php echo admin_url('licences/licence'); ?>" class="btn btn-info pull-left new new-licence-btn"><?php echo _l('create_new_licence'); ?></a>
   <?php } ?>
   <a href="<?php echo admin_url('licences/pipeline/'.$switch_pipeline); ?>" class="btn btn-default mleft5 pull-left switch-pipeline hidden-xs"><?php echo _l('switch_to_pipeline'); ?></a>
   <div class="display-block text-right">
     <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
      </button>
      <ul class="dropdown-menu width300">
       <li>
        <a href="#" data-cview="reset" onclick="dt_custom_view('reset','.table-licences','reset'); return false;">
          <?php echo _l('licences_reset_list'); ?>
        </a>
      </li>
      <li class="<?php if($this->input->get('filter') == 'all'){echo 'active'; } ?>">
        <a href="#" data-cview="all" onclick="dt_custom_view('','.table-licences',''); return false;">
          <?php echo _l('licences_list_all'); ?>
        </a>
      </li>
      <li class="divider"></li>
      <li class="<?php if($this->input->get('filter') == 'not_sent'){echo 'active'; } ?>">
        <a href="#" data-cview="not_sent" onclick="dt_custom_view('not_sent','.table-licences','not_sent'); return false;">
          <?php echo _l('not_sent_indicator'); ?>
        </a>
      </li>
      <?php if(has_permission('job_report', '', 'create')){ ?>
      <li>
        <a href="#" data-cview="invoiced" onclick="dt_custom_view('invoiced','.table-licences','invoiced'); return false;">
          <?php echo _l('licence_invoiced'); ?>
        </a>
      </li>
      <li>
        <a href="#" data-cview="not_invoiced" onclick="dt_custom_view('not_invoiced','.table-licences','not_invoiced'); return false;"><?php echo _l('licences_not_invoiced'); ?></a>
      </li>
      <?php }?>

      <li class="divider"></li>
      <?php foreach($licence_statuses as $status){ ?>
        <li class="<?php if($this->input->get('status') == $status){echo 'active';} ?>">
          <a href="#" data-cview="licences_<?php echo $status; ?>" onclick="dt_custom_view('licences_<?php echo $status; ?>','.table-licences','licences_<?php echo $status; ?>'); return false;">
            <?php echo format_licence_status($status,'',false); ?>
          </a>
        </li>
      <?php } ?>
      <div class="clearfix"></div>

      <?php if(count($licences_sale_agents) > 0){ ?>
        <div class="clearfix"></div>
        <li class="divider"></li>
        <li class="dropdown-submenu pull-left">
          <a href="#" tabindex="-1"><?php echo _l('sale_agent_string'); ?></a>
          <ul class="dropdown-menu dropdown-menu-left">
           <?php foreach($licences_sale_agents as $agent){ ?>
             <li>
              <a href="#" data-cview="sale_agent_<?php echo $agent['sale_agent']; ?>" onclick="dt_custom_view(<?php echo $agent['sale_agent']; ?>,'.table-licences','sale_agent_<?php echo $agent['sale_agent']; ?>'); return false;"><?php echo $agent['full_name']; ?>
            </a>
          </li>
        <?php } ?>
      </ul>
    </li>
  <?php } ?>

  <div class="clearfix"></div>
  <?php if(count($licences_years) > 0){ ?>
    <li class="divider"></li>
    <?php foreach($licences_years as $year){ ?>
      <li class="active">
        <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-licences','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
      </a>
    </li>
  <?php } ?>
<?php } ?>
</ul>
</div>


<a href="#" class="btn btn-default btn-with-tooltip toggle-small-view hidden-xs" onclick="toggle_small_view('.table-licences','#licence'); return false;" data-toggle="tooltip" title="<?php echo _l('licences_toggle_table_tooltip'); ?>"><i class="fa fa-angle-double-left"></i></a>
<a href="#" class="btn btn-default btn-with-tooltip licences-total" onclick="slideToggle('#stats-top'); init_licence_total(true); return false;" data-toggle="tooltip" title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
</div>
</div>
</div>
<div class="row">
  <div class="col-md-12" id="small-table">
    <div class="panel_s">
      <div class="panel-body">
        <!-- if licenceid found in url -->
        <?php echo form_hidden('licenceid',$licenceid); ?>
        <?php $this->load->view('admin/licences/table_html'); ?>
      </div>
    </div>
  </div>
  <div class="col-md-7 small-table-right-col">
    <div id="licence" class="hide">
    </div>
  </div>
</div>
</div>