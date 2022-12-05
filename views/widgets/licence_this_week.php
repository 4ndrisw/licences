<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
    $CI = &get_instance();
    $CI->load->model('licences/licences_model');
    $licences = $CI->licences_model->get_licences_this_week(get_staff_user_id());

?>

<div class="widget" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('licence_this_week'); ?>">
    <?php if(staff_can('view', 'licences') || staff_can('view_own', 'licences')) { ?>
    <div class="panel_s licences-expiring">
        <div class="panel-body padding-10">
            <p class="padding-5"><?php echo _l('licence_this_week'); ?></p>
            <hr class="hr-panel-heading-dashboard">
            <?php if (!empty($licences)) { ?>
                <div class="table-vertical-scroll">
                    <a href="<?php echo admin_url('licences'); ?>" class="mbot20 inline-block full-width"><?php echo _l('home_widget_view_all'); ?></a>
                    <table id="widget-<?php echo create_widget_id(); ?>" class="table dt-table dt-inline dataTable no-footer" data-order-col="3" data-order-type="desc">
                        <thead>
                            <tr>
                                <th><?php echo _l('licence_number'); ?> #</th>
                                <th class="<?php echo (isset($client) ? 'not_visible' : ''); ?>"><?php echo _l('licence_list_client'); ?></th>
                                <th><?php echo _l('licence_list_project'); ?></th>
                                <th><?php echo _l('licence_list_date'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($licences as $licence) { ?>
                                <tr class="<?= 'licence_status_' . $licence['status']?>">
                                    <td>
                                        <?php echo '<a href="' . admin_url("licences/licence/" . $licence["id"]) . '">' . format_licence_number($licence["id"]) . '</a>'; ?>
                                    </td>
                                    <td>
                                        <?php echo '<a href="' . admin_url("clients/client/" . $licence["userid"]) . '">' . $licence["company"] . '</a>'; ?>
                                    </td>
                                    <td>
                                        <?php echo '<a href="' . admin_url("projects/view/" . $licence["projects_id"]) . '">' . $licence['name'] . '</a>'; ?>
                                    </td>
                                    <td>
                                        <?php echo _d($licence['date']); ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } else { ?>
                <div class="text-center padding-5">
                    <i class="fa fa-check fa-5x" aria-hidden="true"></i>
                    <h4><?php echo _l('no_licence_this_week',["7"]) ; ?> </h4>
                </div>
            <?php } ?>
        </div>
    </div>
    <?php } ?>
</div>
