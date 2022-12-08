<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
    $CI = &get_instance();
    $CI->load->model('programs/programs_model');
    $programs = $CI->programs_model->get_client_type(get_staff_user_id());

?>

<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('program_this_week'); ?>">
    <?php if(staff_can('view', 'programs') || staff_can('view_own', 'programs')) { ?>
    <div class="panel_s programs-expiring">
        <div class="panel-body padding-10">
            <p class="padding-5"><?php echo _l('program_this_week'); ?></p>
            <hr class="hr-panel-heading-dashboard">
            <?php if (!empty($programs)) { ?>
                <div class="table-vertical-scroll">

                    <a href="<?php echo admin_url('programs'); ?>"
                        class="mbot20 inline-block full-width"><?php echo _l('home_widget_view_all'); ?></a>
                    <div class="clearfix"></div>
                    <?php render_datatable([
                        _l('program_name'),
                        _l('program_start_date'),
                        _l('program_deadline'),
                        _l('program_status'),
                        ], 'my-programs');
                        ?>
                </div>
            <?php } else { ?>
                <div class="text-center padding-5">
                    <i class="fa fa-check fa-5x" aria-hidden="true"></i>
                    <h4><?php echo _l('no_program_this_week',["7"]) ; ?> </h4>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
