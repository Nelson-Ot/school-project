<?php $widget = (is_superadmin_loggedin() ? 3 : 4); ?>
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
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)' id='branchID'
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
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
					 	data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
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
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
					<span class="error"><?=form_error('section_id')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('date')?> <span class="required">*</span></label>
					<div class="input-group">
						<input type="text" class="form-control" name="date" id="attDate" value="<?=set_value('date')?>" autocomplete="off"/>
						<span class="input-group-addon"><i class="icon-event icons"></i></span>
					</div>
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
			<div class="export_title">Period Attendance Sheet on <?=_d($date); ?> <?php 
				echo translate('class') .' : '. get_type_name_by_id('class', $class_id);
				echo ' ( ' .translate('section'). ' : ' .get_type_name_by_id('section', $section_id).' )';
				?></div>
			<div class="row mt-sm">
				<div class="col-md-offset-8 col-md-4">
					<table class="table table-condensed table-bordered text-dark text-center">
						<tbody>
							<tr>
								<td><strong>Present :</strong> <i class="far fa-check-circle hidden-print text-success"></i><span class="visible-print">P</span></td>
								<td><strong>Absent : </strong> <i class="far fa-times-circle hidden-print text-danger"></i><span class="visible-print">A</span></td>
								<td><strong>Late : </strong> <i class="far fa-clock hidden-print text-tertiary"></i><span class="visible-print">L</span></td>
								<td><strong>Half Day : </strong> <i class="fas fa-star-half-alt text-tertiary"></i><span class="visible-print">HD</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="mb-lg">
						<?php 
						if ($subjectByClassSection->num_rows() > 0) {
							$r = $subjectByClassSection->result();
							?>
						<table class="table table-bordered table-hover table-condensed mb-none text-dark table-export">
							<thead>
								<tr>
									<th><?=translate('student_name')?></th>
<?php foreach ($r as $key => $value) { ?>
									<th class="text-center no-sort"><?php echo $value->subjectname . " (" . $value->subject_code . ")<br>" . date("g:i A", strtotime($value->time_start)) . " - " . date("g:i A", strtotime($value->time_end)); ?></th>
<?php } ?>
									<th class="text-center" style="padding-right: 15px !important;">(%)</th>
									<th class="text-center text-success" style="padding-right: 15px !important;">P</th>
									<th class="text-center text-danger" style="padding-right: 15px !important;">A</th>
									<th class="text-center text-tertiary" style="padding-right: 15px !important;">L</th>
									<th class="text-center text-tertiary">HD</th>
								</tr>
							</thead>
							<tbody>
<?php
foreach ($studentlist as $row):
$total_present = 0;
$total_absent = 0;
$total_late = 0;
$total_half_day = 0;
$total_weekends = 0;
$studentID = $row['enroll_id'];
?>
								<tr>
									<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?> <div class="visible-print"> / <?php echo translate('register_no') . " " .  $row['register_no'] ?></div></td>
<?php
foreach ($r as $key => $value) { 
$atten = $this->attendance_period_model->get_attendance_by_date($studentID, $date, $value->id);
?>
							<td class="center">
<?php if (!empty($atten)) { ?>
								<span data-toggle="popover" data-trigger="hover" data-placement="top" data-trigger="hover" data-content="<?php echo $atten['remark']; ?>">
<?php if ($atten['status'] == 'A') { $total_absent++; ?>
									<i class="far fa-times-circle text-danger"></i><span class="visible-print">A</span>
<?php } if ($atten['status'] == 'P') { $total_present++; ?>
									<i class="far fa-check-circle text-success"></i><span class="visible-print">P</span>
<?php } if ($atten['status'] == 'L') { $total_late++; ?>
									<i class="far fa-clock text-info"></i><span class="visible-print">L</span>
<?php } if ($atten['status'] == 'H'){ ?>
									<i class="fas fa-hospital-symbol text-tertiary"></i><span class="visible-print">H</span>
<?php } if ($atten['status'] == 'HD'){ $total_half_day++; ?>
									<i class="fas fa-star-half-alt text-tertiary"></i><span class="visible-print">HD</span>
<?php } ?>
								</span>
<?php } ?>
							</td>
<?php } ?>
									<td class="center"><?php 
									$total_working_days = ($total_present + $total_absent + $total_late + $total_half_day);
									if ($total_working_days == 0) {
										echo "-";
									} else {
										$total_present = ($total_present + $total_late + $total_half_day);
										$percentage = ($total_present / $total_working_days) * 100;
										echo round($percentage);
									}

									?></td>
									<td class="center"><?=$total_present?></td>
									<td class="center"><?=$total_absent?></td>
									<td class="center"><?=$total_late?></td>
									<td class="center"><?=$total_half_day?></td>
									<?php endforeach; ?>
								</tr>
							</tbody>
						</table>
					<?php } else {
						echo '<div class="alert alert-subl mt-md text-center">' .  translate('no_information_available') . '</div>';

					} ?>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<script type="text/javascript">
	var dayOfWeekDisabled = "<?php echo $getWeekends ?>";
	$(document).ready(function () {

		var datePicker = $("#attDate").datepicker({
		    orientation: 'bottom',
		    todayHighlight: true,
		    autoclose: true,
		    format: 'yyyy-mm-dd',
		    daysOfWeekDisabled: dayOfWeekDisabled,
		    datesDisabled: ["<?php echo $getHolidays ?>"],
		});  
    });

	$('select#branchID').change(function() {
		var branchID = $(this).val();
		$.ajax({
			url: base_url + "attendance/getWeekendsHolidays",
			type: 'POST',
			dataType: "json",
			data: {
				branch_id: branchID,
			},
			success: function (data) {
				$('#attDate').val("");
				$('#attDate').datepicker('setDaysOfWeekDisabled', data.getWeekends);
				$('#attDate').datepicker('setDatesDisabled', JSON.parse(data.getHolidays));
			}
		});
	});
</script>