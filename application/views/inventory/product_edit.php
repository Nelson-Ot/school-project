<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('inventory/product'); ?>"><i class="fas fa-list-ul"></i> <?php echo translate('product') . " " . translate('list'); ?></a>
			</li>
			<li class="active">
				<a href="#edit" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('product'); ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div id="edit" class="tab-pane active">
	            <?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit')); ?>
		            <input type="hidden" name="product_id" value="<?php echo html_escape($product['id']); ?>">
				<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id', $product['branch_id']), "class='form-control' data-width='100%' id='branch_id'
								data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
				<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('name'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="product_name" value="<?php echo html_escape($product['name']); ?>" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('code'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="product_code" value="<?php echo html_escape($product['code']); ?>" autocomplete="off" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('product') . " " . translate('category'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								echo form_dropdown("product_category", $categorylist, set_value('product_category', $product['category_id']), "class='form-control' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('purchase_unit'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								echo form_dropdown("purchase_unit", $unitlist, set_value('purchase_unit', $product['purchase_unit_id']), "class='form-control prounit' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('sale_unit'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								echo form_dropdown("sales_unit", $unitlist, set_value('sales_unit', $product['sales_unit_id']), "class='form-control prounit' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('unit_ratio'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="number" class="form-control" name="unit_ratio" id="unit_ratio" value="<?php echo html_escape($product['unit_ratio']); ?>" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('purchase_price'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="purchase_price" id="purchase_price" value="<?php echo html_escape($product['purchase_price']); ?>" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('sales_price'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="sales_price" id="sales_price" value="<?php echo html_escape($product['sales_price']); ?>" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
						<div class="col-md-6 mb-lg">
							<input type="text" class="form-control" name="remarks" id="remarks" value="<?php echo html_escape($product['remarks']); ?>" />
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-edit"></i> <?php echo translate('update'); ?></button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
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