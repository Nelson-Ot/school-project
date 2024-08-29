<?php $widget = (is_superadmin_loggedin() ? 2 : 3); ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<?php echo form_open($this->uri->uri_string());?>
	<div class="panel-body">
		<div class="row mb-sm">
		<?php if (is_superadmin_loggedin() ): ?>
			<div class="col-md-3 mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)' id='branchID'
						data-plugin-selectTwo data-width='100%' ");
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
						data-plugin-selectTwo data-width='100%'  ");
					?>
					<span class="error"><?=form_error('section_id')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group <?php if (form_error('date')) echo 'has-error'; ?>">
					<label class="control-label"><?=translate('date')?> <span class="required">*</span></label>
					<div class="input-group">
						<input type="text" class="form-control" name="date" id="attDate" value="<?=set_value('date')?>" autocomplete="off"/>
						<span class="input-group-addon"><i class="icon-event icons"></i></span>
					</div>
					<span class="error"><?=form_error('date')?></span>
				</div>
			</div>
			<div class="col-md-<?php echo $widget + (is_superadmin_loggedin() ? 1 : 0); ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
					<?php
						$arraySubject = ['' => translate('select')];
						echo form_dropdown("subject_timetable_id", $arraySubject, set_value('subject_timetable_id'), "class='form-control' id='subjectID' data-plugin-selectTwo data-width='100%'");
					?>
					<span class="error"><?=form_error('subject_timetable_id')?></span>
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

<?php if (isset($attendencelist)): 
	$subject_name = $this->attendance_period_model->getSubjectBytimetableID(set_value('subject_timetable_id'));
	$class_name = get_type_name_by_id('class', $class_id);
	$section_name = get_type_name_by_id('section', $section_id);
	?>
	<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-users"></i> <?=translate('attendance_report')?></h4>
		</header>
		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					<div class="mb-sm mt-xs">
			<div class="export_title">Period Attendance Sheet on Subject : (<?php echo $subject_name; ?>) - Date: (<?=_d($date); ?>) - <?php 
				echo translate('class') .' : '. $class_name;
				echo ' ( ' .translate('section'). ' : ' . $section_name . ' )';
				?></div>
						<table class="table table-bordered table-hover table-condensed table-export mb-none">
							<thead>
								<tr>
									<th>#</th>
									<th><?=translate('name')?></th>
									<th><?=translate('register_no')?></th>
									<th><?=translate('roll')?></th>
									<th><?=translate('subject')?></th>
									<th><?=translate('mobile_no')?></th>
									<th width="100"><?=translate('status')?></th>
									<th><?=translate('remarks')?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								if(count($attendencelist)) {
									foreach ($attendencelist as $key => $row):
										?>
								<tr>
									<td><?php echo $count++; ?></td>
									<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
									<td><?php echo $row['register_no']; ?></td>
									<td><?php echo $row['roll']; ?></td>
									<td><?php echo $subject_name; ?></td>
									<td><?php echo $row['mobileno']; ?></td>
									<td>
										<?php
										if($row['status'] == "P")
											echo '<span class="label label-primary">'.strtoupper(translate('present')).'</span>';
										if($row['status'] == "A")
											echo '<span class="label label-danger">'.strtoupper(translate('absent')).'</span>';
										 if($row['status'] == "L")
											echo '<span class="label label-warning">'.strtoupper(translate('late')).'</span>';
										 if($row['status'] == "HD")
											echo '<span class="label label-warning">'.strtoupper(translate('half_day')).'</span>';
										?>
									</td>
									<td><?php echo html_escape(!empty($row['remark']) ? $row['remark']: 'N/A');?></td>
								</tr>
									<?php 
								endforeach;
							} else {
								echo '<tr><td colspan="7"><h5 class="text-danger text-center">'.translate('no_information_available').'</td></tr>';
							} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</section>
<?php endif; ?>

<script type="text/javascript">
	var dayOfWeekDisabled = "<?php echo $getWeekends ?>";
	$(document).ready(function () {
        var class_id_post = "<?php echo set_value('class_id'); ?>";
        var section_id_post = "<?php echo set_value('section_id'); ?>";
        var date_post = "<?php echo set_value('date'); ?>";
        var subject_id = "<?php echo set_value('subject_timetable_id', 0); ?>";
        getSubjects(class_id_post, section_id_post, date_post, subject_id);

		var datePicker = $("#attDate").datepicker({
		    orientation: 'bottom',
		    todayHighlight: true,
		    autoclose: true,
		    format: 'yyyy-mm-dd',
		    daysOfWeekDisabled: dayOfWeekDisabled,
		    datesDisabled: ["<?php echo $getHolidays ?>"],
		}).on('changeDate', function(ev) {
            var class_id = $('#class_id').val();
            var section_id = $('#section_id').val();
            var date = $(this).val();
            getSubjects(class_id, section_id, date);
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

    function getSubjects(class_id, section_id, date, subject_id = 0) {
        if (section_id != "" && class_id != "" && date != "") {
            var div_data = '<option value=""><?php echo translate('select'); ?></option>';
            $.ajax({
                type: "POST",
                url: base_url + "attendance_period/getByClassSection",
                data: {
                    'classID': class_id,
                    'sectionID': section_id,
                    'selectPOST': subject_id,
                    'date': date
                },
                dataType: "html",
	            beforeSend: function () {
	                $('#select2-subjectID-container').parent().addClass('select2loading');
	            },
                success: function(data) {
                    $('#subjectID').html(data);
                },
	            complete: function () {
	                $('#select2-subjectID-container').parent().removeClass('select2loading');
	            }
            });
        }
    }
</script>