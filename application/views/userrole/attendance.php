<section class="panel">
	<?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<div class="panel-body">
		<div class="row mb-sm">
			<div class="col-md-offset-3 col-md-6 mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('month')?> <span class="required">*</span></label>
					<div class="input-group">
						<input type="text" class="form-control" name="timestamp" value="<?=set_value('timestamp', date('Y-F'))?>" required data-plugin-datepicker data-plugin-options='{ "format": "yyyy-MM", "minViewMode": "months", "orientation": "bottom"}' />
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

<?php if (isset($student)) { ?>
<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-users"></i> <?=translate('attendance_report')?></h4>
	</header>
	<div class="panel-body">
			<style type="text/css">
				table.dataTable.table-condensed > thead > tr > th {
				  padding-right: 3px !important;
				}
			</style>
			<div class="row mt-sm">
				<div class="col-md-offset-8 col-md-4">
					<table class="table table-condensed table-bordered text-dark text-center">
						<tbody>
							<tr>
								<td><strong>Weekends :</strong> W<span class="visible-print">W</span></td>
								<td><strong>Present :</strong> <i class="far fa-check-circle hidden-print text-success"></i><span class="visible-print">P</span></td>
								<td><strong>Absent : </strong> <i class="far fa-times-circle hidden-print text-danger"></i><span class="visible-print">A</span></td>
								<td><strong>Holiday : </strong> <i class="fas fa-hospital-symbol hidden-print text-info"></i><span class="visible-print">H</span></td>
								<td><strong>Late : </strong> <i class="far fa-clock hidden-print text-tertiary"></i><span class="visible-print">L</span></td>
								<td><strong>Half Day : </strong> <i class="fas fa-star-half-alt text-tertiary"></i><span class="visible-print">HD</span></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="export_title">Monthly Attendance Sheet on <?=date("F Y", strtotime($year . '-' . $month)); ?></div>
					<table class="table table-bordered table-hover table-condensed mb-none text-dark table-export" data-ordering="false">
						<thead>
							<tr>
								<th><?=translate('student_name')?></th>
<?php
$weekends = $this->attendance_model->getWeekendDaysSession($student['branch_id']);
$getHolidays = $this->attendance_model->getHolidays($student['branch_id']);
$getHolidays = explode('","', $getHolidays);
for($i = 1; $i <= $days; $i++){
$date = date('Y-m-d', strtotime($year . '-' . $month . '-' . $i));
?>
								<th <?php if(in_array($date, $weekends)) { echo "style='background-color: #f99'"; } ?> class="text-center"><?php echo date('D', strtotime($date)); ?> <br> <?php echo date('d', strtotime($date)); ?></th>
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
$total_present = 0;
$total_absent = 0;
$total_late = 0;
$total_half_day = 0;
$total_weekends = 0;
?>
							<tr>
								<td><?php echo $student['fullname']; ?></td>
<?php
for ($i = 1; $i <= $days; $i++) { 
$date = date('Y-m-d', strtotime($year . '-' . $month . '-' . $i));
$atten = $this->userrole_model->get_attendance_by_date($student['enroll_id'], $date);
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
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		
	</div>
</section>
<?php } ?>