<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
	<h4 class="customer-profile-group-heading"><?php echo _l('licences'); ?></h4>
	<?php if(has_permission('licences','','create')){ ?>
		<a href="<?php echo admin_url('licences/licence?customer_id='.$client->userid); ?>" class="btn btn-info mbot15<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('create_new_licence'); ?></a>
	<?php } ?>
	<?php if(has_permission('licences','','view') || has_permission('licences','','view_own') || get_option('allow_staff_view_licences_assigned') == '1'){ ?>
		<a href="#" class="btn btn-info mbot15" data-toggle="modal" data-target="#client_zip_licences"><?php echo _l('zip_licences'); ?></a>
	<?php } ?>
	<div id="licences_total"></div>
	<?php
	$this->load->view('admin/licences/table_html', array('class'=>'licences-single-client'));
	//$this->load->view('admin/clients/modals/zip_licences');
	?>
<?php } ?>
