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
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
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
							data-plugin-selectTwo data-width='100%' ");
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
						<label class="control-label"><?=translate('month')?> <span class="required">*</span></label>
						<div class="input-group">
							<input type="text" class="form-control" name="timestamp" value="<?=set_value('timestamp', date('Y-F'))?>" data-plugin-datepicker required
							data-plugin-options='{ "format": "yyyy-MM", "minViewMode": "months", "orientation": "bottom"}' />
							<span class="input-group-addon"><i class="icon-event icons"></i></span>
						</div>
						<span class="error"><?=form_error('month')?></span>
					</div>
				</div>
				<div class="col-md-<?php echo $widget + (is_superadmin_loggedin() ? 1 : 0); ?>">
					<div class="form-group">
						<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
						<?php
							if(!empty(set_value('class_id'))) {
								$arraySubject = array("" => translate('select'));
								$query = $this->subject_model->getSubjectByClassSection(set_value('class_id'), set_value('section_id'));
								$subjects = $query->result_array();
								foreach ($subjects as $row){
									$subjectID = $row['subject_id'];
									$arraySubject[$subjectID] = $row['subjectname'] . " (" . $row['subject_code'] . ")";
								}
							} else {
								$arraySubject = array("" => translate('select_class_first'));
							}
							echo form_dropdown("subject_id", $arraySubject, set_value('subject_id'), "class='form-control' id='subject_id'
							data-plugin-selectTwo data-width='100%' ");
						?>
						<span class="error"><?=form_error('subject_id')?></span>
					</div>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-offset-10 col-md-2">
					<button type="submit" name="search" value="1" class="btn btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close();?>
	</section>

<?php 
if (isset($studentlist)): 
	$subject_name = get_type_name_by_id('subject', $subject_id);
	$class_name = get_type_name_by_id('class', $class_id);
	$section_name = get_type_name_by_id('section', $section_id);
	?>
	<section class="panel mt-sm">
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
			<div class="export_title">Period Attendance Sheet on <?php echo $subject_name; ?> - <?=date("F Y", strtotime($year.'-'.$month)); ?> <?php 
				echo translate('class') .' : '. $class_name;
				echo ' ( ' .translate('section'). ' : ' . $section_name . ' )';
				?></div>
			<div class="row mt-sm">
				<div class="col-md-offset-4 col-md-4">
					<section class="panel pg-fw">
					    <div class="panel-body">
					        <h5 class="chart-title mb-xs">Period Attendance Sheet of <?=date("F Y", strtotime($year.'-'.$month)); ?></h5>
					        <div class="mt-sm">
					        	<ul class="stu-disabled" style="padding-left: 7px;">
					        		<li style="padding: 5px 5px 5px 0;">
					        			<div class="main-r">
						        			<div class="r-1"><?php echo translate('subject') ?> :</div>
						        			<div><?php echo $subject_name ?></div>
					        			</div>
					        		</li>
					        		<li style="padding: 5px 5px 5px 0;">
					        			<div class="main-r">
						        			<div class="r-1"><?php echo translate('class') ?> :</div>
						        			<div><?php echo $class_name ?></div>
					        			</div>
					        		</li>
					        		<li style="padding: 5px 5px 5px 0;">
					        			<div class="main-r">
						        			<div class="r-1"><?php echo translate('section') ?> :</div>
						        			<div><?php echo $section_name ?></div>
					        			</div>
					        		</li>
					        	</ul>
					        </div>
					    </div>
					</section>
				</div>
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
						<table class="table table-bordered table-hover nowrap table-condensed mb-none text-dark table-export">
							<thead>
								<tr>
									<th><?=translate('student_name')?></th>
<?php
$weekends = $this->attendance_model->getWeekendDaysSession($branch_id);
$getHolidays = $this->attendance_model->getHolidays($branch_id);
$getHolidays = explode('","', $getHolidays);
for($i = 1; $i <= $days; $i++) {
$date = date('Y-m-d', strtotime($year . '-' . $month . '-' . $i));
?>
									<th <?php if(in_array($date, $weekends)) { echo "style='background-color: #f99'"; } ?> class="text-center no-sort"><?php echo date('D', strtotime($date)); ?> <br> <?php echo date('d', strtotime($date)); ?></th>
<?php } ?>
									<th class="text-center" style="padding-right: 15px !important;">(%)</th>
									<th class="text-center" style="padding-right: 15px !important;">W</th>
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
for ($i = 1; $i <= $days; $i++) { 
$date = date('Y-m-d', strtotime($year . '-' . $month . '-' . $i));
$day = date('l', strtotime($date));
$atten = $this->attendance_period_model->get_attendance_by_subjectID($studentID, $class_id, $section_id, $date, $day, $subject_id);
?>
							<td class="center">

<?php if (!empty($atten)) { ?>
								<span data-toggle="popover" data-trigger="hover" data-placement="top" data-trigger="hover" data-content="<?php echo $atten['remark']; ?>">
									<?php echo $atten['time'] ?><br>
<?php if ($atten['status'] == 'A') { $total_absent++; ?>
									<i class="far fa-times-circle text-danger"></i><span class="visible-print">- A</span>
<?php } if ($atten['status'] == 'P') { $total_present++; ?>
									<i class="far fa-check-circle text-success"></i><span class="visible-print">- P</span>
<?php } if ($atten['status'] == 'L') { $total_late++; ?>
									<i class="far fa-clock text-info"></i><span class="visible-print">-L</span>
<?php } if ($atten['status'] == 'H'){ ?>
									<i class="fas fa-hospital-symbol text-tertiary"></i><span class="visible-print">- H</span>
<?php } if ($atten['status'] == 'HD'){ $total_half_day++; ?>
									<i class="fas fa-star-half-alt text-tertiary"></i><span class="visible-print">- HD</span>
<?php } ?>
								</span>
<?php } else {

	if(in_array($date, $getHolidays)) {
		echo '<i class="fas fa-hospital-symbol text-tertiary"></i><span class="visible-print">H</span>';
	} else {
		if(in_array($date, $weekends)) {
			$total_weekends++;
			echo '<span class="text-success">W</span>';
		}
	}
} ?>
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
									<td class="center"><?=$total_weekends?></td>
									<td class="center"><?=$total_present?></td>
									<td class="center"><?=$total_absent?></td>
									<td class="center"><?=$total_late?></td>
									<td class="center"><?=$total_half_day?></td>
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

<script type="text/javascript">
	$(document).ready(function () {
		$('#branch_id').on('change', function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			$('#subject_id').html('').append('<option value=""><?=translate("select")?></option>');
		});

		$('#section_id').on('change', function() {
			var classID = $('#class_id').val();
			var sectionID = $(this).val();
			$.ajax({
				url: base_url + 'subject/getByClassSection',
				type: 'POST',
				data: {
					classID: classID,
					sectionID: sectionID
				},
				success: function (data) {
					$('#subject_id').html(data);
				}
			});
		});
	});
</script>