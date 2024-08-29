<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('onlineexam') ?>">
				  <i class="fas fa-list-ul"></i> <?=translate('online_exam') ." ". translate('list')?>
				</a>
			</li>
			<li class="active">
				<a href="#add" data-toggle="tab">
				 <i class="far fa-edit"></i> <?=translate('edit') ." ". translate('online_exam')?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="add">
					<?php echo form_open('', array('class' => 'form-bordered form-horizontal frm-submit'));?>
					<input type="hidden" name="id" value="<?php echo $onlineexam['id'] ?>">		
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, $onlineexam['branch_id'], "class='form-control' data-width='100%' onchange='getClassByBranch(this.value)'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('title')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="title" value="<?php echo $onlineexam['title'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('class')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = $this->app_lib->getClass($onlineexam['branch_id']);
								echo form_dropdown("class_id", $arrayClass, $onlineexam['class_id'], "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0,1)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('section')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$sel = json_decode($onlineexam['section_id'], true);
								$arraySections = $this->app_lib->getSections($onlineexam['class_id'], false, true);
								echo form_dropdown("section[]", $arraySections, $sel, "class='form-control' id='section_id' data-plugin-selectTwo data-width='100%' multiple
								data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
							<div class="checkbox-replace mt-sm pr-xs pull-right">
								<label class="i-checks"><input type="checkbox" class="chk-sendsmsmail" name="chk_section"><i></i> Select All</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('subject')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								if(!empty($onlineexam['class_id'])) {
									$subject_id = json_decode($onlineexam['subject_id'], true);
									$arraySubject = array();
									$query = $this->onlineexam_model->getSubjectByClass($onlineexam['class_id']);
									$subjects = $query->result_array();
									foreach ($subjects as $row){
										$subjectID = $row['subject_id'];
										$arraySubject[$subjectID] = $row['subjectname'];
									}
								} else {
									$arraySubject = array("" => translate('select_class_first'));
								}
								echo form_dropdown("subject[]", $arraySubject, $subject_id, "class='form-control' id='subject_id'
								data-plugin-selectTwo multiple data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('start_date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="start_date" value="<?=date('Y-m-d', strtotime($onlineexam['exam_start']))?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('end_date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="end_date" value="<?=date('Y-m-d', strtotime($onlineexam['exam_end']))?>" data-plugin-datepicker  />
							</div>
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('start_time')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker class="form-control" name="start_time" value="<?=date('H:i:s', strtotime($onlineexam['exam_start']))?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('end_time')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker class="form-control" name="end_time" id="end_time" value="<?=date('H:i:s', strtotime($onlineexam['exam_end']))?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('duration'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" data-plugin-timepicker data-plugin-options='{"showMeridian" : false, "minuteStep" : 5}' name="duration" value="<?=$onlineexam['duration']?>" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('limits_of_participation'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="participation_limit" autocomplete="off" placeholder="Limits on student participation in exams" value="<?=$onlineexam['limits_participation']?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('mark') . " "  . translate('type'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = array(
									'' => translate('select'),
									1 => translate('percent'),
									0 => translate('fixed'),
								);
								echo form_dropdown("mark_type", $arrayClass, $onlineexam['mark_type'], "class='form-control'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('passing_mark'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="passing_mark" autocomplete="off" value="<?php echo $onlineexam['passing_mark'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('instruction'); ?>  <span class="required">*</span></label>
						<div class="col-md-6">
							<textarea name="instruction" rows="2" class="form-control"><?php echo $onlineexam['instruction'] ?></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('exam') . " " . translate('type')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = array(
									'' => translate('select'),
									0 => translate('free'),
									1 => translate('paid'),
								);
								echo form_dropdown("exam_type", $arrayClass, $onlineexam['exam_type'], "class='form-control' id='examType'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group <?php echo $onlineexam['exam_type'] == 0 ? 'hidden-div' : '';  ?> " id="examFee">
						<label class="col-md-3 control-label"><?=translate('exam') . " " . translate('fees')?>  <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="exam_fee" autocomplete="off" value="<?php echo $onlineexam['fee'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('question') . " " . translate('type')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = array(
									'' => translate('select'),
									0 => translate('fixed'),
									1 => translate('random'),
								);
								echo form_dropdown("question_type", $arrayClass, $onlineexam['question_type'], "class='form-control' id='questionType'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('result_publish')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = array(
									'' => translate('select'),
									1 => "Automatic/Immediate",
									0 => "Manually",
								);
								echo form_dropdown("publish_result", $arrayClass, $onlineexam['publish_result'], "class='form-control' id='publish_result'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('negative_mark')?> Applicable</label>
						<div class="col-md-6">
	                        <div class="material-switch mt-xs">
	                            <input class="switch_menu" id="negative_marking" name="negative_marking" type="checkbox" <?php echo $onlineexam['neg_mark'] == 1 ? 'checked' : ''; ?> />
	                            <label for="negative_marking" class="label-primary"></label>
	                        </div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('marks_display')?></label>
						<div class="col-md-6">
	                        <div class="material-switch mt-xs mb-lg">
	                            <input class="switch_menu" id="marks_display" name="marks_display" type="checkbox" <?php echo $onlineexam['marks_display'] == 1 ? 'checked' : ''; ?> />
	                            <label for="marks_display" class="label-primary"></label>
	                        </div>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?=translate('update')?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	$(document).ready(function () {
		$('.chk-sendsmsmail').on('change', function() {
			if($(this).is(':checked') ){
				$(this).parents('.form-group').find('select > option').prop("selected","selected");
				$(this).parents('.form-group').find('select').trigger("change");
			} else {
				$(this).parents('.form-group').find('select').val(null).trigger('change');
			}
		});

        $('#class_id').on('change', function() {
            var classID = $(this).val();
            $.ajax({
                url: base_url + 'onlineexam/getByClass',
                type: 'POST',
                data: {
                    classID: classID
                },
                success: function (data) {
                    $('#subject_id').html(data);
                }
            });
        });
	});
</script>