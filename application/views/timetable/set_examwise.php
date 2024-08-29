<?php $widget = (is_superadmin_loggedin() ? 3 : 4); ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
	<div class="panel-body">
		<div class="row mb-sm">
			<?php if (is_superadmin_loggedin()): ?>
			<div class="col-md-3 mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' required id='branch_id'
						data-width='100%' data-plugin-selectTwo data-minimum-results-for-search='Infinity'");
					?>
				</div>
			</div>
			<?php endif; ?>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('exam_name')?> <span class="required">*</span></label>
					<?php
						$arrayExam = array("" => translate('select_branch_first'));
						if(!empty($branch_id)){
							$exams = $this->db->get_where('exam', array('branch_id' => $branch_id,'session_id' => get_session_id()))->result();
							foreach ($exams as $exam){
								$arrayExam[$exam->id] = $this->application_model->exam_name_by_id($exam->id);
							}
						}
						echo form_dropdown("exam_id", $arrayExam, set_value('exam_id'), "class='form-control' id='exam_id' required
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
					<?php
						$arrayClass = $this->app_lib->getClass($branch_id);
						echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
						required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
				</div>
			</div>
			<div class="col-md-<?php echo $widget; ?> mb-sm">
				<div class="form-group">
					<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
					<?php
						$arraySection = $this->app_lib->getSections(set_value('class_id'));
						echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required 
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
				</div>
			</div>
		</div>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-md-offset-10 col-md-2">
				<button type="submit" class="btn btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
			</div>
		</div>
	</footer>
	<?php echo form_close();?>
</section>

<?php if(isset($subjectassign)):
$examDistribution = $this->db->where('id', $exam_id)->get('exam')->row()->mark_distribution;
	?>
	<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="far fa-clock"></i> <?=translate('add') . " " . translate('schedule')?></h4>
		</header>
		<div class="panel-body">
			<section class="panel pg-fw">
			    <div class="panel-body">
			    	<form action="#" method="POST" id="formSchedule">
			        <h5 class="chart-title mb-md"><?=translate('set_parameters_to_quickly_create_schedule')?></h5>
					<div class="table-responsive">
						<table class="table table-bordered table-condensed text-nowrap">
							<thead>
								<th><?=translate('starting_date')?> <span class="required">*</span></th>
								<th><?=translate('starting_time')?> <span class="required">*</span></th>
								<th><?=translate('exam') . " " . translate('duration')?> (<?=translate('minutes')?>) <span class="required">*</span></th>
								<th><?=translate('hall_room')?> <span class="required">*</span></th>
<?php

$distribution = json_decode($examDistribution, true);
foreach ($distribution as $id) {
?>
								<th><?=get_type_name_by_id('exam_mark_distribution', $id)?> <span class="required">*</span></th>
<?php } ?>
							</thead>
							<tbody>
								<td class="min-w-sm">
									<div class="form-group mb-none">
										<input type="text" class="form-control" required data-plugin-datepicker data-plugin-options='{"todayHighlight" : true}' autocomplete="off" name="days" id="qStart_day" value="<?php echo date("Y-m-d") ?>" />
									</div>
								</td>
								<td class="min-w-sm">
									<div class="form-group mb-none">
										<div class="input-group">
											<span class="input-group-addon"><i class="far fa-clock"></i></span>
											<input type="text" class='form-control' name="q_starting_time" id="qStartingTime" required data-plugin-timepicker class="form-control" autocomplete="off" data-plugin-options='{ "minuteStep": 5 }' value="">
										</div>
									</div>
								</td>
								<td class="min-w-sm">
									<div class="form-group mb-none">
										<input type="number" class='form-control' name="duration" id="qDuration" min="1" required autocomplete="off" value="">
									</div>
								</td>
								<td class="min-w-sm">
									<div class="form-group mb-none">
										<?php
										if(!empty($branch_id)){
											$hall_array = array("" => translate('not_selected'));
											$halls = $this->db->get_where('exam_hall', array('branch_id' => $branch_id))->result();
											foreach ($halls as $hall){
												$hall_array[$hall->id] = $hall->hall_no;
											}
										}else{
											$hall_array = array("" => translate('select_branch_first'));
										}
										echo form_dropdown("hall_id", $hall_array, "", "class='form-control' data-plugin-selectTwo data-width='100%' required data-minimum-results-for-search='Infinity' id='qHall_ID' ");
										?>
									</div>
								</td>
<?php
$distribution = json_decode($examDistribution, true);
foreach ($distribution as $id) {
	?>
								<td class="min-w-sm">
									<div class="mark-inline">
										<div class="form-group mb-none mr-xs">
											<input type="text" class="form-control q_full_mark" data-id="<?php echo $id ?>" style="min-width: 87px" autocomplete="off" required placeholder="Full Mark" name="full_mark_<?php echo $id ?>" value="" />
											<span class="error"></span>
										</div>
										<div class="form-group mb-none">
											<input type="text" class="form-control" id="q_passMark<?php echo $id ?>" style="min-width: 87px" autocomplete="off" required placeholder="Pass Mark" name="pass_mark_<?php echo $id ?>" value="" />
											<span class="error"></span>
										</div>
									</div>
								</td>
<?php } ?>
							</tbody>
						</table>
					</div>
					<div class="row">
						<div class="col-md-offset-10 col-md-2">
							<button class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-plus-circle"></i> <?=translate('apply')?>
							</button>
						</div>
					</div>
					<?php echo form_close();?>
				</div>
			</section>
		<?php
			echo form_open('timetable/exam_create', array('id' => 'scheduleForm'));
			$data = array(
				'exam_id' => $exam_id,
				'class_id' => $class_id,
				'section_id' => $section_id,
				'branch_id' => $branch_id
			);
			echo form_hidden($data);
			?>

			<div class="table-responsive mb-md">
				<table class="table table-bordered mt-md text-nowrap">
					<thead>
						<th><?=translate('subject')?> <span class="required">*</span></th>
						<th><?=translate('date')?> <span class="required">*</span></th>
						<th><?=translate('starting_time')?> <span class="required">*</span></th>
						<th><?=translate('ending_time')?> <span class="required">*</span></th>
						<th><?=translate('hall_room')?> <span class="required">*</span></th>
<?php
// getting exist exam distribution
$distribution = json_decode($examDistribution, true);
foreach ($distribution as $id) {
?>
						<th><?=get_type_name_by_id('exam_mark_distribution', $id)?> <span class="required">*</span></th>
<?php } ?>	
					</thead>
					<tbody>
						<?php
						if (count($subjectassign)){
							foreach ($subjectassign as $key => $row):
								$subjectID = $row['subject_id'];
						?>
						<tr>
							<td class="min-w-sm">
								<input type="hidden" name="timetable[<?=$key?>][subject_id]" value="<?=$subjectID?>"><?=get_type_name_by_id('subject', $subjectID)?>
							</td>
							<td class="min-w-sm">
								<div class="form-group mb-none">
									<input type="text" class="form-control exam-date" data-plugin-datepicker data-plugin-options='{"todayHighlight" : true}' autocomplete="off" name="timetable[<?=$key?>][date]" value="<?=$row['exam_date']?>" />
									<span class="error"></span>
								</div>
							</td>
							<td class="min-w-sm">
								<div class="form-group mb-none">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" name="timetable[<?=$key?>][time_start]" data-plugin-timepicker class="form-control time_start" autocomplete="off"
										data-plugin-options='{ "minuteStep": 5 }' value="<?=$row['time_start']?>">
									</div>
									<span class="error"></span>
								</div>
							</td>
							<td class="min-w-sm">
								<div class="form-group mb-none">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" name="timetable[<?=$key?>][time_end]" data-plugin-timepicker class="form-control time_end" autocomplete="off"
										data-plugin-options='{ "minuteStep": 5 }' value="<?=$row['time_end']?>">
									</div>
									<span class="error"></span>
								</div>
							</td>
							<td class="min-w-sm">
								<div class="form-group mb-none">
									<?php
										if(!empty($branch_id)){
											$hall_array = array("" => translate('not_selected'));
											$halls = $this->db->get_where('exam_hall', array('branch_id' => $branch_id))->result();
											foreach ($halls as $hall){
												$hall_array[$hall->id] = $hall->hall_no;
											}
										}else{
											$hall_array = array("" => translate('select_branch_first'));
										}
										echo form_dropdown("timetable[$key][hall_id]", $hall_array, $row['hall_id'], "class='form-control hall_id' data-plugin-selectTwo
										data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</td>
<?php
// getting exist mark
$getMark = json_decode($row['mark_distribution'], true);
foreach ($distribution as $id) {
	$full_mark = isset($getMark[$id]['full_mark']) ? $getMark[$id]['full_mark'] : "";
	$pass_mark = isset($getMark[$id]['pass_mark']) ? $getMark[$id]['pass_mark'] : "";
	?>

							<td>
								<div class="mark-inline">
								<div class="form-group mb-none mr-xs">
									<input type="text" class="form-control full-mark-id-<?php echo $id ?>" style="min-width: 86px" autocomplete="off" placeholder="Full Mark" name="timetable[<?=$key?>][full_mark][<?=$id?>]" value="<?=$full_mark?>" />
									<span class="error"></span>
								</div>
								<div class="form-group mb-none">
									<input type="text" class="form-control pass-mark-id-<?php echo $id ?>" style="min-width: 86px" autocomplete="off" placeholder="Pass Mark" name="timetable[<?=$key?>][pass_mark][<?=$id?>]" value="<?=$pass_mark?>" />
									<span class="error"></span>
								</div>
								</div>
							</td>
<?php } ?>
						</tr>
						<?php  
							endforeach; 
						} else {
							echo '<tr><td colspan="7"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-offset-10 col-md-2">
					<button type="submit" id="scheduleBtn" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
						<i class="fas fa-plus-circle"></i> <?=translate('save')?>
					</button>
				</div>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
<?php endif;?>

<script type="text/javascript">
	$(document).ready(function () {
		$('#branch_id').on("change", function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			getExamByBranch(branchID);
		});

		$("form#formSchedule").validate({
			highlight: function( label ) {
				$(label).closest('.form-group').removeClass('has-success').addClass('has-error');
			},
			success: function( label ) {
				$(label).closest('.form-group').removeClass('has-error');
				label.remove();
			},
			errorPlacement: function( error, element ) {
				var placement = element.closest('.input-group');
				if (!placement.get(0)) {
					placement = element;
				}
				if (error.text() !== '') {
					if(element.parent('.checkbox, .radio').length || element.parent('.input-group').length) {
						placement.after(error);
					} else {
						var placement = element.closest('div');
						placement.append(error);
					}
				}
			},
			submitHandler: function(form) {
				$(".q_full_mark").each(function() {
				 	let markID = $(this).data("id");
				 	let fullMark = $(this).val();
				 	let passMark = $('#q_passMark' + markID).val();
				 	$(".full-mark-id-" + markID ).val(fullMark); 
				 	$(".pass-mark-id-" + markID ).val(passMark);
				});

				let start_day= $('#qStart_day').val();
				$('#scheduleForm tbody > tr').each(function() {
					var new_day = moment(start_day, "YYYY-MM-DD").add(1, 'days').format('YYYY-MM-DD');
					$(this).find(".exam-date").datepicker('setDate',start_day);
  					start_day = new_day;
				});
				
				let starting_time= $('#qStartingTime').val();
				$('#scheduleForm .time_start').timepicker('setTime', starting_time);
             	
             	let duration= $('#qDuration').val();
             	
             	var new_time = moment(starting_time, "hh:mm A").add(duration, 'minutes').format('hh:mm A');
				$('#scheduleForm .time_end').timepicker('setTime', new_time);

				let hall_id = $('#qHall_ID').val();
				$('#scheduleForm .hall_id').val(hall_id).trigger('change.select2'); 
			}
		});

        $("form#scheduleForm").on('submit', function(e){
            e.preventDefault();
            var btn = $("#scheduleBtn");
            var $this = $(this);
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else {
                        swal({
                            toast: true,
                            position: 'top-end',
                            type: 'success',
                            title: data.message,
                            confirmButtonClass: 'btn btn-default',
                            buttonsStyling: false,
                            timer: 8000
                        });
                    }
                },
                complete: function (data) {
                    btn.button('reset'); 
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
	});
</script>