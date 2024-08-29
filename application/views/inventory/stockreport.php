<?php $widget = (is_superadmin_loggedin() ? '' : 'col-md-offset-3'); ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"> <?php echo translate('select_ground'); ?></h4>
	</header>
    <?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
		<div class="panel-body">
			<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id' onchange='getClassByBranch(this.value)'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
						</div>
					</div>
				<?php endif; ?>
				<div class="<?=$widget?> col-md-6 mb-lg">		
					<div class="form-group">
						<label class="control-label"><?php echo translate('category'); ?> <span class="required">*</span></label>
						<?php
							echo form_dropdown("category_id", $categorylist, set_value('category_id'), "class='form-control' required id='categoryID'
							data-plugin-selectTwo data-width='100%'");
						?>
					</div>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-offset-10 col-md-2">
					<button type="submit" name="search" value="1" class="btn btn btn-default btn-block"> <i class="fas fa-filter"></i> <?php echo translate('filter'); ?></button>
				</div>
			</div>
		</footer>
	<?php echo form_close(); ?>
</section>
<?php if (isset($results)): ?>
<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?php echo translate('product_stock') . " " . translate('report'); ?></h4>
	</header>
	<div class="panel-body">
		<div class="export_title"><?php echo translate('product_stock') . " " . translate('report'); ?></div>
		<table class="table table-bordered table-hover table-condensed tbr-top table-export" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php echo translate('sl'); ?></th>
					<th><?php echo translate('category'); ?></th>
					<th><?php echo translate('product') . " " . translate('name'); ?></th>
					<th><?php echo translate('product') . " " . translate('code'); ?></th>
					<th><?php echo translate('supplier'); ?></th>
					<th><?php echo translate('store'); ?></th>
					<th><?php echo translate('purchase_qty'); ?></th>
					<th><?php echo translate('total_issued'); ?></th>
					<th><?php echo translate('total_sales'); ?></th>
					<th class="isExport"><?php echo translate('current') . " " . translate('stock'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$count = 1;
				if (!empty($results)){ 
					foreach ($results as $row):
					$total_issued = empty($row['total_issued']) ? 0 : $row['total_issued'];
					$total_sales = empty($row['total_sales']) ? 0 : $row['total_sales'];
					$purchaseUnit = $row['in_stock'] . " " . get_type_name_by_id('product_unit', $row['purchase_unit_id']);
					$salesUnit = $row['in_stock'] * $row['unit_ratio'];
				?>	
				<tr>
					<td><?php echo $count++ ; ?></td>
					<td><?php echo html_escape($row['category_name']); ?></td>
					<td><?php echo html_escape($row['name']); ?></td>
					<td><?php echo html_escape($row['code']); ?></td>
					<td><?php echo html_escape($row['supplier_name']); ?></td>
					<td><?php echo html_escape($row['store_name']); ?></td>
					<td><?php 
					if ($salesUnit == $row['in_stock']) {
						echo $purchaseUnit;
					} else {
						echo  "$salesUnit " . get_type_name_by_id('product_unit', $row['sales_unit_id']) . " ($purchaseUnit)"; 
					} ?></td>
					<td><?php echo html_escape($total_issued); ?></td>
					<td><?php echo html_escape($total_sales); ?></td>
					<td><?php echo html_escape($row['available_stock']); ?></td>
				</tr>
				<?php endforeach; }?>
			</tbody>
		</table>
	</div>
</section>
<?php endif; ?>

<script type="text/javascript">
	$('#branch_id').on('change', function() {
		var branchID = $(this).val();
		$.ajax({
			url: "<?=base_url('inventory/getDataByBranch')?>",
			type: 'POST',
			data: {
				'branch_id': branchID,
				'table': "product_category",
			},
			success: function (data) {
				$('#categoryID').html(data);
			}
		});
	});
</script>