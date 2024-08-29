<?php $widget = (is_superadmin_loggedin() ? 2 : 3); ?>
<section class="panel">
	<?php echo form_open($this->uri->uri_string());?>
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<div class="panel-body">
		<div class="row mb-sm">
		<?php if (is_superadmin_loggedin() ): ?>
			<div class="col-md-3 mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
						data-plugin-selectTwo data-width='100%'");
					?>
				</div>
				<span class="error"><?=form_error('branch_id')?></span>
			</div>
		<?php endif; ?>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
					<?php
						$arrayClass = $this->app_lib->getClass($branch_id);
						echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
					 	data-plugin-selectTwo data-width='100%'  ");
					?>
					<span class="error"><?=form_error('class_id')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
					<?php
						$arraySection = $this->app_lib->getSections(set_value('class_id'), false);
						echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id'
						data-plugin-selectTwo data-width='100%' ");
					?>
					<span class="error"><?=form_error('section_id')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('attendance_type')?> <span class="required">*</span></label>
					<?php
						$arrayType = array(
							'' => translate('select'),
							'P' => translate('present'),
							'A' => translate('absent'),
							'L' => translate('late'),
							'H' => translate('holiday'),
							'HD' => translate('half_day'),
						);;
						echo form_dropdown("attendance_type", $arrayType, set_value('attendance_type'), "class='form-control'
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
					<span class="error"><?=form_error('attendance_type')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget + (is_superadmin_loggedin() ? 1 : 0); ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('date')?> <span class="required">*</span></label>
					<div class="input-group">
						<input type="text" class="form-control daterange" name="daterange" value="<?php echo set_value('daterange', date("Y/m/d") . ' - ' . date("Y/m/d")); ?>" required />
						<span class="input-group-addon"><i class="icon-event icons"></i></span>
					</div>
					<span class="error"><?=form_error('daterange')?></span>
				</div>
			</div>
		</div>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-md-offset-10 col-md-2">
				<button type="submit" name="submit" value="search" class="btn btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
			</div>
		</div>
	</footer>
    <?php echo form_close();?>
</section>

<?php if (isset($studentlist)): ?>
	<section class="panel appear-animation mt-sm" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-users"></i> <?=translate('attendance_report')?></h4>
		</header>
		<div class="panel-body">
			<style type="text/css">
				table.dataTable.table-condensed > thead > tr > th {
				  padding-right: 3px !important;
				}
			</style>
			<!-- hidden school information prints -->
			<div class="export_title">Student Attendance Report [<?php 
				echo translate('class') .' : '. get_type_name_by_id('class', $class_id);
				echo ' ( ' .translate('section'). ' : ' .get_type_name_by_id('section', $section_id).' )]';
				?> - [Type : <?php echo $arrayType[set_value('attendance_type')] ?>] - [Date : <?php echo _d($start) . " To " . _d($end) ?>]</div>

			<div class="row">
				<div class="col-md-12">
					<div class="mb-lg">
						<table class="table table-bordered table-hover table-condensed mb-none text-dark table-export">
							<thead>
								<tr>
									<th><?=translate('student_name')?></th>
									<th><?=translate('register_no')?></th>
									<th><?=translate('admission_date')?></th>
									<th><?=translate('category')?></th>
									<th><?=translate('class')?></th>
									<th><?=translate('gender')?></th>
									<th><?=translate('mobile_no')?></th>
									<th class="isExport"><?=translate('count')?></th>
								</tr>
							</thead>
							<tbody>
<?php
foreach ($studentlist as $row):
	$enrollID = $row['id'];
?>
								<tr>
									<td><?php echo $row['fullname']; ?></td>
									<td><?php echo $row['category']; ?></td>
									<td><?php echo $row['register_no']; ?></td>
									<td><?php echo _d($row['admission_date']); ?></td>
									<td><?php echo $row['class_name'] . " (" . $row['section_name'] . ")" ; ?></td>
									<td><?php echo translate($row['gender']); ?></td>
									<td><?php echo $row['mobileno']; ?></td>
									<td><?php echo $this->attendance_model->stuAttendanceCount_by_date($enrollID, $start, $end, set_value('attendance_type')); ?></td>
									<?php endforeach; ?>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>