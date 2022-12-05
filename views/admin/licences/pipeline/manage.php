<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s no-margin">
                    <div class="panel-body _buttons">
                       <?php if(has_permission('licences','','create')){
                        $this->load->view('admin/licences/licences_top_stats');
                    } ?>
                    <div class="row">
                        <div class="col-md-8">
                            <?php if(has_permission('licences','','create')){ ?>
                            <a href="<?php echo admin_url('licences/licence'); ?>" class="btn btn-info pull-left new"><?php echo _l('create_new_licence'); ?></a>
                            <div class="display-block pull-left mleft5">
                                <a href="#" class="btn btn-default licences-total" onclick="slideToggle('#stats-top'); init_licence_total(true); return false;" data-toggle="tooltip" title="<?php echo _l('view_stats_tooltip'); ?>"><i class="fa fa-bar-chart"></i></a>
                            </div>
                            <?php } ?>
                            <a href="<?php echo admin_url('licences/pipeline/'.$switch_pipeline); ?>" class="btn btn-default mleft5 pull-left"><?php echo _l('switch_to_list_view'); ?></a>
                        </div>
                        <div class="col-md-4" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                            <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'licence_pipeline();'),array(),'no-margin') ?>
                            <?php echo form_hidden('sort_type'); ?>
                            <?php echo form_hidden('sort',(get_option('default_licences_pipeline_sort') != '' ? get_option('default_licences_pipeline_sort_type') : '')); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel_s animated mtop5 fadeIn">
                <?php echo form_hidden('licenceid',$licenceid); ?>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="kanban-leads-sort">
                                <span class="bold"><?php echo _l('licences_pipeline_sort'); ?>: </span>
                                <a href="#" onclick="licences_pipeline_sort('datecreated'); return false" class="datecreated">
                                    <?php if(get_option('default_licences_pipeline_sort') == 'datecreated'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_licences_pipeline_sort_type')).'"></i> ';} ?>
                                    <?php echo _l('licences_sort_datecreated'); ?>
                                    </a>
                                |
                                <a href="#" onclick="licences_pipeline_sort('date'); return false" class="date">
                                    <?php if(get_option('default_licences_pipeline_sort') == 'date'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_licences_pipeline_sort_type')).'"></i> ';} ?>
                                    <?php echo _l('licences_sort_licence_date'); ?>
                                    </a>
                                |
                                <a href="#" onclick="licences_pipeline_sort('pipeline_order');return false;" class="pipeline_order">
                                    <?php if(get_option('default_licences_pipeline_sort') == 'pipeline_order'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_licences_pipeline_sort_type')).'"></i> ';} ?>
                                    <?php echo _l('licences_sort_pipeline'); ?>
                                    </a>
                                |
                                <a href="#" onclick="licences_pipeline_sort('duedate');return false;" class="duedate">
                                    <?php if(get_option('default_licences_pipeline_sort') == 'duedate'){echo '<i class="kanban-sort-icon fa fa-sort-amount-'.strtolower(get_option('default_licences_pipeline_sort_type')).'"></i> ';} ?>
                                    <?php echo _l('licences_sort_expiry_date'); ?>
                                    </a>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div id="licence-pipeline">
                            <div class="container-fluid">
                                <div id="kan-ban"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<div id="licence">
</div>
<?php $this->load->view('admin/includes/modals/sales_attach_file'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
      licence_pipeline();
  });
</script>
</body>
</html>
