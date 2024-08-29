<div class="row">
<?php if (get_permission('product_unit', 'is_add')): ?>
	<div class="col-md-5">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="far fa-edit"></i> <?php echo translate('add') . " " . translate('unit'); ?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string()); ?>
				<div class="panel-body">
				<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
						?>
						<span class="error"><?=form_error('branch_id')?></span>
					</div>
				<?php endif; ?>
					<div class="form-group mb-md <?php if (form_error('unit_name')) echo 'has-error'; ?>">
						<label class="control-label"><?php echo translate('unit') . " " . translate('name'); ?> <span class="required">*</span></label>
						<input type="text" class="form-control" name="unit_name" value="<?php echo set_value('unit_name'); ?>" />
						<span class="error"><?php echo form_error('unit_name'); ?></span>
					</div>
				</div>
				<div class="panel-footer">
					<div class="row">
						<div class="col-md-12">
							<button class="btn btn-default pull-right" type="submit" name="unit" value="1"><i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?></button>
						</div>	
					</div>
				</div>
			<?php echo form_close(); ?>
		</section>
	</div>
<?php endif; ?>

<?php if (get_permission('product_unit', 'is_view')): ?>
	<div class="col-md-<?php if (get_permission('product_unit', 'is_add')){ echo "7"; } else {echo "12";} ?>">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ul"></i> <?php echo translate('unit') . " " . translate('list'); ?></h4>
			</header>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed mb-none">
						<thead>
							<tr>
								<th><?php echo translate('sl'); ?></th>
								<th><?=translate('branch')?></th>
								<th><?php echo translate('name'); ?></th>
								<th><?php echo translate('action'); ?></th>
							</tr>
						</thead>
						<tbody>
						<?php $count = 1; if (!empty($unitlist)){ foreach ($unitlist as $row): ?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo get_type_name_by_id('branch', $row['branch_id']);?></td>
								<td><?php echo html_escape($row['name']); ?></td>
								<td class="min-w-xs">
								<?php if (get_permission('product_unit', 'is_edit')): ?>
									<a class="btn btn-default btn-circle icon" href="javascript:void(0);" data-toggle="tooltip" data-original-title="<?php echo translate('edit');?>"
									onclick="getUnitDetails('<?php echo html_escape($row['id']); ?>')">
										<i class="fas fa-pen-nib"></i>
									</a>
								<?php 
								endif;
								if (get_permission('product_unit', 'is_delete')):
								?>
									<?php echo btn_delete('inventory/unit_delete/' . $row['id']); ?>
								<?php endif; ?>
								</td>
							</tr>
						<?php
							endforeach;
						}else{
							echo '<tr><td colspan="4"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
						}
						?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>
</div>
<?php endif; ?>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('unit'); ?>
			</h4>
		</header>
		<?php echo form_open(base_url('inventory/unit_edit'), array('class' => 'validate', 'method' => 'post')); ?>
			<div class="panel-body">
				<input type="hidden" name="unit_id" id="eunit_id" value="">
			<?php if (is_superadmin_loggedin()): ?>
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' id='ebranch_id'
						data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
					?>
					<span class="error"><?php echo form_error('branch_id'); ?></span>
				</div>
			<?php endif; ?>
				<div class="form-group mb-md">
					<label class="control-label"><?php echo translate('unit') . " " . translate('name'); ?> <span class="required">*</span></label>
					<input type="text" class="form-control" required  value="" name="unit_name" id="eunit_name" />
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-default"><?php echo translate('update'); ?></button>
						<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
					</div>
				</div>
			</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<script type="text/javascript">
	function getUnitDetails(id) {
	    $.ajax({
	        url: base_url + 'inventory/getProductUnitDetails',
	        type: 'POST',
	        data: {'id': id},
	        dataType: "json",
	        success: function (data) {
	            $('.error').html('');
	            $('#eunit_id').val(data.id);
	            $('#eunit_name').val(data.name);
	            if ($('#ebranch_id').length) {
	                $('#ebranch_id').val(data.branch_id).trigger('change');
	            }
	            mfp_modal('#modal');
	        }
	    });
	}
</script>