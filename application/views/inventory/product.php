<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#productlist" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('product') . ' ' . translate('list'); ?></a>
			</li>
<?php if (get_permission('product', 'is_add')){ ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('create') . ' ' . translate('product'); ?></a>
			</li>
<?php } ?>
		</ul>
		<div class="tab-content">
			<div id="productlist" class="tab-pane active mb-md">
				<div class="export_title"><?php echo translate('product') . " " . translate('list'); ?></div>
				<table class="table table-bordered table-hover table-condensed table-export" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?php echo translate('sl'); ?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?php echo translate('name'); ?></th>
							<th><?php echo translate('code'); ?></th>
							<th><?php echo translate('category'); ?></th>
							<th><?php echo translate('purchase_unit'); ?></th>
							<th><?php echo translate('sale_unit'); ?></th>
							<th><?php echo translate('unit_ratio'); ?></th>
							<th><?php echo translate('purchase_price'); ?></th>
							<th><?php echo translate('sales_price'); ?></th>
							<th><?php echo translate('remarks'); ?></th>
							<th><?php echo translate('action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						if (!empty($productlist)){
							foreach ($productlist as $row):
							?>	
						<tr>
							<td><?php echo $count++; ?></td>
<?php if (is_superadmin_loggedin()): ?>
							<td><?php echo get_type_name_by_id('branch', $row['branch_id']);?></td>
<?php endif; ?>
							<td><?php echo html_escape($row['name']); ?></td>
							<td><?php echo html_escape($row['code']); ?></td>
							<td><?php echo html_escape($row['category_name']); ?></td>
							<td><?php echo html_escape($row['p_unit_name']); ?></td>
							<td><?php echo html_escape($row['s_unit_name']); ?></td>
							<td><?php echo html_escape($row['unit_ratio']); ?></td>
							<td><?php echo html_escape(currencyFormat($row['purchase_price'])); ?></td>
							<td><?php echo html_escape(currencyFormat($row['sales_price'])); ?></td>
							<td><?php echo html_escape($row['remarks']); ?></td>
							<td class="min-w-xs">
								<?php if (get_permission('product', 'is_edit')): ?>
									<a href="<?php echo base_url('inventory/product_edit/' . $row['id']); ?>" class="btn btn-circle icon btn-default" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>"> 
										<i class="fas fa-pen-nib"></i>
									</a>
								<?php endif; if (get_permission('product', 'is_delete')): ?>
									<?php echo btn_delete('inventory/product_delete/' . $row['id']); ?>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
				</table>
			</div>
		<?php if (get_permission('product', 'is_add')){ ?>
			<div id="create" class="tab-pane">
				<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit')); ?>
					
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' id='branch_id'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('name'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="product_name" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('code'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="product_code" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('category'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$categorylist = $this->app_lib->getSelectByBranch('product_category', $branch_id);
								echo form_dropdown("product_category", $categorylist, set_value("product_category"), "class='form-control' data-plugin-selectTwo id='productCategory'
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('purchase_unit'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								echo form_dropdown("purchase_unit", $unitlist, set_value("purchase_unit"), "class='form-control prounit' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('sales_unit'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								echo form_dropdown("sales_unit", $unitlist, set_value("sales_unit"), "class='form-control prounit' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('unit_ratio'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="unit_ratio" id="unit_ratio" value="" placeholder="Eg. Purchase Unit : KG & Sales Unit : Gram = Ratio : 1000"  />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('purchase_price'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="purchase_price" id="purchase_price" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('sales_price'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="sales_price" id="sales_price" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
						<div class="col-md-6 mb-lg">
							<input type="text" class="form-control" name="remarks" id="remarks" value="" />
						</div>
					</div>
					
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?></button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
			<?php } ?>
		</div>
	</div>
</section>

<script type="text/javascript">
	$('#branch_id').on('change', function() {
		var branchID = $(this).val();
		$.ajax({
			url: "<?=base_url('ajax/getDataByBranch')?>",
			type: 'POST',
			data: {
				branch_id: branchID,
				table : 'product_category'
			},
			success: function (data) {
				$('#productCategory').html(data);
			}
		});

		$.ajax({
			url: "<?=base_url('ajax/getDataByBranch')?>",
			type: 'POST',
			data: {
				branch_id: branchID,
				table : 'product_unit'
			},
			success: function (data) {
				$('.prounit').html(data);
			}
		});
	});
</script>