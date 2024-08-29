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

<?php if (isset($attendencelist)): ?>
	<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
		<?php
			echo form_open($this->uri->uri_string());
			$data = array('date' => $date, 'subject_timetable_id' => set_value('subject_timetable_id'), 'branch_id' => $branch_id);
			echo form_hidden($data);
		?>
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-users"></i> <?=translate('students_list')?></h4>
		</header>
		<div class="panel-body">
			<?php if (!empty($attendencelist[0]['att_status'])) { ?>
			 <div class="alert alert-success">Today's attendance has already been submitted, would you like to edit it?</div>
			<?php } ?>
			<div class="row">
				<div class="col-md-offset-9 col-md-3">
					<div class="form-group mb-sm">
						<label class="control-label"><?=translate('select_for_everyone')?> <span class="required">*</span></label>
						<?php
							$array = array(
								"" => translate('not_selected'),
								"P" 	=> translate('present'),
								"A" 	=> translate('absent'),
								"L" 	=> translate('late'),
								"HD" 	=> translate('half_day'),
							);
							echo form_dropdown("mark_all_everyone", $array, set_value('mark_all_everyone'), "class='form-control' 
							onchange='selAtten_all(this.value)' data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-12">
					<div class="table-responsive mb-sm mt-xs">
						<table class="table table-bordered table-hover table-condensed mb-none">
							<thead>
								<tr>
									<th>#</th>
									<th><?=translate('name')?></th>
									<th><?=translate('roll')?></th>
									<th><?=translate('register_no')?></th>
									<th width="400"><?=translate('status')?></th>
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
									<input type="hidden" name="attendance[<?=$key?>][attendance_id]" value="<?=$row['att_id']?>" >
									<input type="hidden" name="attendance[<?=$key?>][enroll_id]" value="<?=$row['enroll_id']?>" >
									<input type="hidden" name="attendance[<?=$key?>][student_id]" value="<?=$row['student_id']?>" >
									<td><?php echo $count++; ?></td>
									<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
									<td><?php echo $row['roll']; ?></td>
									<td><?php echo $row['register_no']; ?></td>
									<td>
										<div class="radio-custom radio-success radio-inline mt-xs">
											<input type="radio" value="P" <?=(empty($row['att_status']) ? 'checked' : '')?> <?=($row['att_status'] == 'P' ? 'checked' : '')?> name="attendance[<?=$key?>][status]" id="pstatus_<?=$key?>">
											<label for="pstatus_<?=$key?>"><?=translate('present')?></label>
										</div>
										<div class="radio-custom radio-danger radio-inline mt-xs">
											<input type="radio" value="A" <?=($row['att_status'] == 'A' ? 'checked' : '')?> name="attendance[<?=$key?>][status]" id="astatus_<?=$key?>">
											<label for="astatus_<?=$key?>"><?=translate('absent')?></label>
										</div>
										<div class="radio-custom radio-inline mt-xs">
											<input type="radio" value="L" <?=($row['att_status'] == 'L' ? 'checked' : '')?> name="attendance[<?=$key?>][status]" id="lstatus_<?=$key?>">
											<label for="lstatus_<?=$key?>"><?=translate('late')?></label>
										</div>
										<div class="radio-custom radio-inline mt-xs">
											<input type="radio" value="HD" <?=($row['att_status'] == 'HD' ? 'checked' : '')?> name="attendance[<?=$key?>][status]" id="hdstatus_<?=$key?>">
											<label for="hdstatus_<?=$key?>"><?=translate('half_day')?></label>
										</div>
									</td>
									<td>
										<input class="form-control" style="min-width: 110px;" name="attendance[<?=$key?>][remark]" type="text" placeholder="<?=translate('remarks')?>" value="<?=$row['att_remark']?>" >
									</td>
								</tr>
									<?php 
								endforeach;
							} else {
								echo '<tr><td colspan="6"><h5 class="text-danger text-center">'.translate('no_information_available').'</td></tr>';
							} ?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
		<div class="panel-footer">
			<div class="row">
				<div class="col-md-offset-10 col-md-2">
					<button type="submit" class="btn btn-default btn-block" name="save" value="1">
						<i class="fas fa-plus-circle"></i> <?=translate('save')?>
					</button>
				</div>
			</div>
		</div>
		<?php echo form_close();?>
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