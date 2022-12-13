<?php

defined('BASEPATH') or exit('No direct script access allowed');

echo form_hidden('licence_item_id', $licence_item->id);
echo form_hidden('rel_id', $licence->id);
echo form_hidden('addedfrom', get_staff_user_id());
?>
<div class="row">

	<div class="panel_s">
	  <div class="panel-body bg-light">
	    <div class="row">
			<div class="col-md-12">
				<?php $value = ((isset($licence_item_data) && !is_null($licence_item_data->nama_pesawat)) ? $licence_item_data->nama_pesawat : $licence_item->nama_pesawat); ?>
				<div class="card bg-info text-white mbot5">
	              <div class="card-header text-white less-padding"><?php echo _l('nama_pesawat'); ?></div>
	              <div class="card-body less-padding text-info">
	                <p class="card-text text-white"><?php echo $value;?></p>
	              </div>
	            </div>

			</div>
			<div class="col-md-6">
				<?php $value = ((isset($licence_item_data) && !is_null($licence_item_data->nomor_seri)) ? $licence_item_data->nomor_seri : $licence_item->nomor_seri); ?>
				<div class="card bg-info text-white mbot5">
	              <div class="card-header less-padding"><?php echo _l('nomor_seri'); ?></div>
	              <div class="card-body less-padding text-info">
	                <p class="card-text text-white"><?php echo $value;?></p>
	              </div>
	            </div>
			</div>
			<div class="col-md-6">
				<?php $value = ((isset($licence_item_data) && !is_null($licence_item_data->nomor_unit)) ? $licence_item_data->nomor_unit : $licence_item->nomor_unit); ?>
				<div class="card bg-info text-white mbot5">
	              <div class="card-header less-padding"><?php echo _l('nomor_unit'); ?></div>
	              <div class="card-body less-padding text-info">
	                <p class="card-text text-white"><?php echo $value;?></p>
	              </div>
	            </div>
			</div>
			<div class="col-md-12">
				<?php $value = ((isset($licence_item_data) && !is_null($licence_item_data->lokasi)) ? $licence_item_data->lokasi : ''); ?>
				<div class="card bg-info mbot5">
	              <div class="card-header less-padding"><?php echo _l('alamat'); ?></div>
	              <div class="card-body less-padding text-info">
	                <p class="card-text text-white"><?php echo $value;?></p>
	              </div>
	            </div>
			</div>
		</div>
	  </div>
	</div>

	<hr />

	<div class="col-md-6 form-group-no-margin tooltip-right">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->nomor_pengesahan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('nomor_pengesahan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->nomor_laporan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('nomor_laporan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->pabrik_pembuat_engine : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('pabrik_pembuat_engine'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->pabrik_pembuat_generator : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('pabrik_pembuat_generator'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->nomor_seri_engine : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('nomor_seri_engine'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->nomor_seri_generator : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('nomor_seri_generator'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->tempat_pembuatan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('tempat_pembuatan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>

	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->tahun_pembuatan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('tahun_pembuatan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->merk_engine : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('merk_engine'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->merk_generator : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('merk_generator'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->kapasitas : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('kapasitas'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->daya : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('daya'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-6">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->digunakan_untuk : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('digunakan_untuk'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>

	<div class="clearfix"></div>
	<hr />

	<div class="col-md-12">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->temuan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('temuan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
	</div>
	<div class="col-md-12">
		<?php $value = (isset($licence_item_data) ? $licence_item_data->kesimpulan : ''); ?>
        <div class="card border-info mbot5">
          <div class="card-header less-padding"><?php echo _l('kesimpulan'); ?></div>
          <div class="card-body less-padding text-info">
            <p class="card-text"><?php echo $value; ?></p>
          </div>
        </div>
 	</div>

</div>
      
