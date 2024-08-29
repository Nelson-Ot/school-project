<?php $currency_symbol = $global_config['currency_symbol']; ?>
<?php $widget = (is_superadmin_loggedin() ? '3' : '4'); ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"> <?php echo translate('select_ground'); ?></h4>
	</header>
    <?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
		<div class="panel-body">
			<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-3">
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
				<div class="col-md-<?php echo $widget ?> mb-sm">		
					<div class="form-group">
						<label class="control-label"><?php echo translate('supplier'); ?> <span class="required">*</span></label>
						<?php
							echo form_dropdown("supplier_id", $supplierlist, set_value('supplier_id'), "class='form-control' required
							data-plugin-selectTwo data-width='100%' id='supplierID'");
						?>
					</div>
				</div>
				<div class="col-md-<?php echo $widget ?> mb-sm">		
					<div class="form-group">
						<label class="control-label"><?php echo translate('payment') . " " . translate('status'); ?> <span class="required">*</span></label>
						<?php
							$statusList = array(
								'' => translate('select'),
								'all' => translate('all_select'),
								'1' => translate('unpaid'),
								'2' => translate('partly_paid'),
								'3' => translate('total_paid')
							);
							echo form_dropdown("payment_status", $statusList, set_value('payment_status'), "class='form-control' required
							data-plugin-selectTwo data-width='100%'");
						?>
					</div>
				</div>
				<div class="col-md-<?php echo $widget ?> mb-sm">
					<div class="form-group">
						<label class="control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
						<div class="input-group">
							<span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
							<input type="text" class="form-control daterange" name="daterange" value="<?php echo set_value('daterange', date("Y/m/d") . ' - ' . date("Y/m/d")); ?>" required />
						</div>
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
		<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?php echo translate('purchase') . " " . translate('report'); ?></h4>
	</header>
	<div class="panel-body">
		<div class="export_title">Purchase Report : <?php echo _d($daterange[0]); ?> To <?php echo _d($daterange[1]); ?></div>
		<table class="table table-bordered table-hover table-condensed table-export" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php echo translate('sl'); ?></th>
					<th><?php echo translate('bill_no'); ?></th>
					<th><?php echo translate('supplier'); ?></th>
					<th><?php echo translate('store'); ?></th>
					<th><?php echo translate('purchase') . " " . translate('status'); ?></th>
					<th><?php echo translate('payment') . " " . translate('status'); ?></th>
					<th><?php echo translate('date'); ?></th>
					<th><?php echo translate('net') . " " . translate('payable'); ?></th>
					<th><?php echo translate('total') . " " . translate('paid'); ?></th>
					<th class="isExport"><?php echo translate('total') . " " . translate('due'); ?></th>

				</tr>
			</thead>
			<tbody>
				<?php 
				$count = 1;
				$total_amount = 0;
				$total_paid = 0;
				$total_due = 0;
				if (!empty($results)){
					foreach ($results as $row):
						$total_amount += $row['net_payable'];
						$total_paid += $row['paid'];
						$total_due += $row['due'];
				?>	
				<tr>
					<td><?php echo $count++; ?></td>
					<td><?php echo html_escape($row['bill_no']); ?></td>
					<td><?php echo html_escape($row['supplier_name']); ?></td>
					<td><?php echo html_escape($row['store_name']); ?></td>
					<td>
						<?php
							$status_list = array(
								'1' => translate('ordered'),
								'2' => translate('received'),
								'3' => translate('pending')
							);
							echo html_escape($status_list[$row['purchase_status']]);
						?>
					</td>
					<td>
						<?php
							$labelMode = "";
							$status = $row['payment_status'];
							if($status == 1) {
								$status = translate('unpaid');
								$labelMode = 'label-danger-custom';
							} elseif($status == 2) {
								$status = translate('partly_paid');
								$labelMode = 'label-info-custom';
							} elseif($status == 3 || $row['due'] == 0) {
								$status = translate('total_paid');
								$labelMode = 'label-success-custom';
							}
							echo "<span class='label " . $labelMode. "'>" . $status . "</span>";
						?>
					</td>
					<td><?php echo _d($row['date']); ?></td>
					<td><?php echo html_escape(currencyFormat($row['net_payable'])); ?></td>
					<td><?php echo html_escape(currencyFormat($row['paid'])); ?></td>
					<td><?php echo html_escape(currencyFormat($row['due'])); ?></td>
				</tr>
				<?php endforeach; }?>
			</tbody>
			<tfoot>
				<tr>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<th></th>
					<td><?php echo html_escape(currencyFormat($total_amount)); ?></td>
					<td><?php echo html_escape(currencyFormat($total_paid)); ?></td>
					<td><?php echo html_escape(currencyFormat($total_due)); ?></td>
				</tr>
			</tfoot>
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
				'table': "product_supplier",
			},
			success: function (data) {
				$('#supplierID').html(data);
			}
		});
	});
</script>