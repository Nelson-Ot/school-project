<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#productlist" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('store') . ' ' . translate('list'); ?></a>
			</li>
<?php if (get_permission('product_store', 'is_add')){ ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('create') . ' ' . translate('store'); ?></a>
			</li>
<?php } ?>
		</ul>
		<div class="tab-content">
			<div id="productlist" class="tab-pane active mb-md">
				<div class="export_title"><?php echo translate('store') . " " . translate('list'); ?></div>
				<table class="table table-bordered table-hover table-condensed table-export" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th width="60"><?php echo translate('sl'); ?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th class="min-w-md"><?php echo translate('store') . " " . translate('name'); ?></th>
							<th><?php echo translate('store_code'); ?></th>
							<th class="min-w-md"><?php echo translate('mobile_no'); ?></th>
							<th class="min-w-md"><?php echo translate('address'); ?></th>
							<th><?php echo translate('description'); ?></th>
							<th class="min-w-md"><?php echo translate('action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						if (!empty($storelist)) {
							foreach ($storelist as $row):
						?>	
						<tr>
							<td><?php echo $count++; ?></td>
<?php if (is_superadmin_loggedin()): ?>
							<td><?php echo $row['branch_name'];?></td>
<?php endif; ?>
							<td><?php echo html_escape($row['name']); ?></td>
							<td><?php echo html_escape($row['code']); ?></td>
							<td><?php echo html_escape($row['mobileno']); ?></td>
							<td><?php echo html_escape($row['address']); ?></td>
							<td><?php echo html_escape($row['description']); ?></td>
							<td class="min-w-xs">
								<?php if (get_permission('product_store', 'is_edit')){ ?>
									<a href="<?php echo base_url('inventory/store_edit/' . $row['id']); ?>" class="btn btn-circle icon btn-default" data-placement="top" 
									data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>"> <i class="fas fa-pen-nib"></i>
									</a>
								<?php 
								};
								if (get_permission('product_store', 'is_delete')){
								?>
									<?php echo btn_delete('inventory/store_delete/' . $row['id']); ?>
								<?php } ?>
							</td>
						</tr>
						<?php endforeach; } ?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('product_store', 'is_add')){ ?>
			<div id="create" class="tab-pane">
				<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit')); ?>
					<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, "", "class='form-control' id='branch_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('store') . " " . translate('name'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="store_name" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('store_code'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="store_code" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('mobile_no'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="mobileno" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('address'); ?></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="address" value="" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('description'); ?></label>
						<div class="col-md-6 mb-md">
							<textarea class="form-control" rows="3" name="description" placeholder="<?php echo translate('description'); ?>"><?php echo set_value('description'); ?></textarea>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn-default btn-block">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php } ?>
		</div>
	</div>
</section>