<?php $currency_symbol = $global_config['currency_symbol']; ?>
<?php $widget = (is_superadmin_loggedin() ? 'col-md-6' : 'col-md-offset-3 col-md-6'); ?>
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
				<div class="<?php echo $widget ?> mb-sm">
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
		<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?php echo translate('issues') . " " . translate('report'); ?></h4>
	</header>
	<div class="panel-body">
		<div class="export_title">Purchase Report : <?php echo _d($daterange[0]); ?> To <?php echo _d($daterange[1]); ?></div>
		<table class="table table-bordered table-hover table-condensed table-export" cellspacing="0" width="100%">
			<thead>
				<tr>
					<th><?php echo translate('sl'); ?></th>
					<th><?php echo translate('issue_to'); ?></th>
					<th><?php echo translate('role'); ?></th>
					<th><?php echo translate('mobile_no'); ?></th>
					<th><?php echo translate('category'); ?></th>
					<th><?php echo translate('product'); ?></th>
					<th><?php echo translate('date_of_issue'); ?></th>
					<th><?php echo translate('due_date'); ?></th>
					<th><?php echo translate('return_date'); ?></th>
					<th><?php echo translate('quantity'); ?></th>
					<th class="isExport"><?php echo translate('status'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php 
				$count = 1;
				if (!empty($results)){
					foreach ($results as $row):
						$user = $this->application_model->getUserNameByRoleID($row['role_id'], $row['user_id']);
						?>	
				<tr>
					<td><?php echo $count++; ?></td>
					
					<td><?php echo $user['name'] ?></td>
					<td><?php echo html_escape($row['role_name']); ?></td>
					<td><?php echo html_escape($user['mobileno']); ?></td>
					<td><?php echo html_escape($row['category_name']); ?></td>
					<td><?php echo html_escape($row['product_name']); ?></td>
					<td><?php echo _d($row['date_of_issue']); ?></td>
					<td><?php echo _d($row['due_date']); ?></td>
					<td><?php echo empty($row['return_date']) ? '-' : _d($row['return_date']); ?></td>
					<td><?php echo $row['quantity']; ?></td>
					<td><?php
							$labelMode = "";
							$status = $row['status'];
							if($status == 0) {
								$status = translate('not_returned');
								$labelMode = 'label-danger-custom';
							} elseif($status == 1) {
								$status = translate('returned');
								$labelMode = 'label-success-custom';
							}
							echo "<span class='label " . $labelMode. "'>" . $status . "</span>";
						?></td>
				</tr>
				<?php endforeach; }?>
			</tbody>
		</table>
	</div>
</section>
<?php endif; ?>