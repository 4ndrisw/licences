<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<table class="table dt-table table-licences" data-order-col="1" data-order-type="desc">
    <thead>
        <tr>
            <th><?php echo _l('licence_number'); ?> #</th>
            <th><?php echo _l('licence_list_project'); ?></th>
            <th><?php echo _l('licence_list_date'); ?></th>
            <th><?php echo _l('licence_list_status'); ?></th>

        </tr>
    </thead>
    <tbody>
        <?php foreach($licences as $licence){ ?>
            <tr>
                <td><?php echo '<a href="' . site_url("licences/show/" . $licence["id"] . '/' . $licence["hash"]) . '">' . format_licence_number($licence["id"]) . '</a>'; ?></td>
                <td><?php echo $licence['name']; ?></td>
                <td><?php echo _d($licence['date']); ?></td>
                <td><?php echo format_licence_status($licence['status']); ?></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
