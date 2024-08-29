<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"> <?php echo translate('select_ground'); ?></h4>
	</header>
    <?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
		<div class="panel-body">
			<div class="row mb-sm">		
				<div class="col-md-6 mb-sm">		
					<div class="form-group">
						<label class="control-label"><?php echo translate('supplier'); ?> <span class="required">*</span></label>
						<?php
							echo form_dropdown("supplier_id", $supplierlist, set_value('supplier_id'), "class='form-control' required
							data-plugin-selectTwo data-width='100%'");
						?>
					</div>
				</div>
				<div class="col-md-6 mb-sm">		
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
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?php echo translate('payment') . " " . translate('report'); ?></h4>
	</header>
	<div class="panel-body">
		<div class="export_title">Payment Report : <?php echo _d($daterange[0]); ?> To <?php echo _d($daterange[1]); ?></div>
		<table class="table table-bordered table-hover table-condensed" cellspacing="0" width="100%" id="table-export">
			<thead>
				<tr>
					<th><?php echo translate('sl'); ?></th>
					<th><?php echo translate('bill_no'); ?></th>
					<th><?php echo translate('supplier'); ?></th>
					<th><?php echo translate('paid_via'); ?></th>
					<th><?php echo translate('date'); ?></th>
					<th><?php echo translate('amount'); ?></th>
					<th class="isExport"><?php echo translate('action'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$count = 1;
				$total_amount = 0;
				if (!empty($results)){ foreach ($results as $row):
					$total_amount += $row['amount'];
				
				?>	
				<tr>
					<td><?php echo $count++ ; ?></td>
					<td><?php echo html_escape($row['bill_no']); ?></td>
					<td><?php echo html_escape($row['supplier_name']); ?></td>
					<td><?php echo html_escape($row['paidvia']); ?></td>
					<td><?php echo html_escape(_d($row['paid_on'])); ?></td>
					<td><?php echo html_escape(currencyFormat($row['amount'])); ?></td>
					<td>
<?php if (get_permission('product_purchase', 'is_view')) { ?>
						<!-- invoice view -->
						<a href="<?php echo base_url('inventory/purchase_bill/' . $row['bill_id'] . "/" . $row['hash']); ?>" class="btn btn-circle icon btn-default" data-toggle="tooltip" data-original-title="<?php echo translate('bill_view'); ?>"> <i class="fas fa-eye"></i></a>
<?php } ?>
					</td>
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
					<th><?php echo html_escape(currencyFormat($total_amount)); ?></th>
					<th></th>
				</tr>
			</tfoot>
		</table>
	</div>
</section>
<?php endif; ?>