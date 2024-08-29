<?php
$widget = (is_superadmin_loggedin() ? 4 : 6);
$currency_symbol = $global_config['currency_symbol'];
?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								required data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
								required data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), false);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>
<?php if (isset($invoicelist)): ?>
		<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
			<?php echo form_open('fees/invoicePrint', array('class' => 'printIn')); ?>
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?=translate('due_fees_report');?></h4>
			</header>
			<div class="panel-body">
				<div class="mb-md mt-md">
					<div class="export_title"><?=translate('due_fees_report')?></div>
					<table class="table table-bordered table-condensed table-hover mb-none tbr-top table-export">
						<thead>
							<tr>
								<th><?=translate('sl')?></th>
								<th><?=translate('student')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('roll')?></th>
								<th><?=translate('mobile_no')?></th>
								<th><?=translate('total_fees')?></th>
								<th><?=translate('total_paid')?></th>
								<th><?=translate('total_discount')?></th>
								<th><?=translate('total_fine')?></th>
								<th class="isExport"><?=translate('total_balance')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							$totalfees = 0;
							$totalpaid = 0;
							$totaldiscount = 0;
							$totalfine = 0;
							$totalbalance = 0;
							foreach($invoicelist as $row):
								$paid = $row['payment']['total_paid'] + $row['payment']['total_discount'];
								if ((float)$row['total_fees'] <= (float)$paid) {

								} else {
									$totalfees += $row['total_fees'];
									$totalpaid += $row['payment']['total_paid'];
									$totaldiscount += $row['payment']['total_discount'];
									$totalfine += $row['payment']['total_fine'];
									$totalbalance += ($row['total_fees'] - $paid);
								?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $row['first_name'] . ' ' . $row['last_name'];?></td>
								<td><?php echo $row['register_no'];?></td>
								<td><?php echo $row['roll'];?></td>
								<td><?php echo $row['mobileno'];?></td>
								<td><?php echo currencyFormat($row['total_fees']);?></td>
								<td><?php echo currencyFormat($row['payment']['total_paid']);?></td>
								<td><?php echo currencyFormat($row['payment']['total_discount']);?></td>
								<td><?php echo currencyFormat($row['payment']['total_fine']);?></td>
								<td><?php echo currencyFormat($row['total_fees'] - $paid);?></td>
							</tr>
							<?php } endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th><?php echo currencyFormat($totalfees); ?></th>
								<th><?php echo currencyFormat($totalpaid); ?></th>
								<th><?php echo currencyFormat($totaldiscount); ?></th>
								<th><?php echo currencyFormat($totalfine); ?></th>
								<th><?php echo currencyFormat($totalbalance); ?></th>
							</tr>
						</tfoot>
					</table>
				</div>
			</div>
			<?php echo form_close(); ?>
		</section>
<?php endif; ?>
	</div>
</div>
