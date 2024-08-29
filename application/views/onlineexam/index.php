<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
                <a href="#list" data-toggle="tab">
                    <i class="fas fa-list-ul"></i> <?=translate('online_exam') ." ". translate('list')?>
                </a>
			</li>
<?php if (get_permission('online_exam', 'is_add')): ?>
			<li>
                <a href="#add" data-toggle="tab">
                   <i class="far fa-edit"></i> <?=translate('add') ." ". translate('online_exam')?>
                </a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane box active mb-md" id="list">
				<table class="table table-bordered table-hover mb-none table-condensed exam-list" width="100%">
					<thead>
						<tr>
							<th class="no-sort"><?=translate('sl')?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?=translate('title')?></th>
							<th><?=translate('class')?> (<?=translate('section')?>)</th>
							<th><?=translate('questions_qty')?></th>
							<th><?=translate('start_time')?></th>
							<th><?=translate('end_time')?></th>
							<th><?=translate('duration')?></th>
							<th class="no-sort"><?=translate('exam') . " " . translate('fees')?></th>
							<th class="no-sort"><?=translate('exam_status')?></th>
							<th class="no-sort"><?=translate('created_by')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					
				</table>
			</div>
<?php if (get_permission('online_exam', 'is_add')): ?>
			<div class="tab-pane" id="add">
					<?php echo form_open('onlineexam/exam_save', array('class' => 'form-bordered form-horizontal frm-submit'));?>
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' onchange='getClassByBranch(this.value)'
									data-plugin-selectTwo");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('title')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="title" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('class')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0,1)'
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('section')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<select class="form-control" name="section[]" id="section_id" data-plugin-selectTwo multiple >
							</select>
							<span class="error"></span>
							<div class="checkbox-replace mt-sm pr-xs pull-right">
								<label class="i-checks"><input type="checkbox" class="chk-sendsmsmail" name="chk_section"><i></i> Select All</label>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('subject')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<select class="form-control" name="subject[]" id="subject_id" data-plugin-selectTwo multiple >
							</select>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('start_date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="start_date" value="<?=set_value('start_date', date('Y-m-d'))?>" data-plugin-datepicker
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
								<input type="text" class="form-control" name="end_date" value="<?=set_value('end_date', date('Y-m-d'))?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('start_time')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker class="form-control" name="start_time" value="" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('end_time')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker class="form-control" name="end_time" id="end_time" value="" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('duration'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" data-plugin-timepicker data-plugin-options='{"showMeridian" : false, "minuteStep" : 5}' name="duration" value="0.00" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('limits_of_participation'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="participation_limit" autocomplete="off" placeholder="Limits on student participation in exams" value="" />
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
								echo form_dropdown("mark_type", $arrayClass, set_value('mark_type'), "class='form-control'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('passing_mark'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="passing_mark" autocomplete="off" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('instruction'); ?>  <span class="required">*</span></label>
						<div class="col-md-6">
							<textarea name="instruction" rows="2" class="form-control"></textarea>
							<span class="error"></span>
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
								echo form_dropdown("exam_type", $arrayClass, set_value('exam_type'), "class='form-control' id='examType'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group hidden-div" id="examFee">
						<label class="col-md-3 control-label"><?=translate('exam') . " " . translate('fees')?>  <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="exam_fee" autocomplete="off" value="" />
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
								echo form_dropdown("question_type", $arrayClass, set_value('question_type'), "class='form-control' id='questionType'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('result_publish') ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayClass = array(
									'' => translate('select'),
									1 => "Automatic/Immediate",
									0 => "Manually",
								);
								echo form_dropdown("publish_result", $arrayClass, set_value('publish_result'), "class='form-control' id='publish_result'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('negative_mark') . " " . translate('applicable') ?></label>
						<div class="col-md-6">
		                     <div class="material-switch mt-xs">
		                         <input class="switch_menu" id="negative_marking" name="negative_marking" type="checkbox" />
		                         <label for="negative_marking" class="label-primary"></label>
		                     </div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('marks_display')?></label>
						<div class="col-md-6 mb-lg">
		                     <div class="material-switch mt-xs">
		                         <input class="switch_menu" id="marks_display" name="marks_display" checked type="checkbox" />
		                         <label for="marks_display" class="label-primary"></label>
		                     </div>
						</div>
					</div>

					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?=translate('save')?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php endif; ?>
		</div>
	</div>
</section>

<script type="text/javascript">
	$(document).ready(function () {
		// initiate Datatable
		initDatatable('.exam-list', 'onlineexam/getExamListDT', {}, 25);

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

	function confirmModal(publish_url) {
		swal({
			title: "Are You Sure?",
			text: "<?=translate('make') . ' ' . translate('result_publish');?>",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn btn-default swal2-btn-default",
			cancelButtonClass: "btn btn-default swal2-btn-default",
			confirmButtonText: "Yes, Continue",
			cancelButtonText: "Cancel",
			buttonsStyling: false,
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: publish_url,
					type: "POST",
					success:function(data) {
						swal({
						title: "Deleted",
						text: "Successfully result publish.",
						buttonsStyling: false,
						showCloseButton: true,
						focusConfirm: false,
						confirmButtonClass: "btn btn-default swal2-btn-default",
						type: "success"
						}).then((result) => {
							if (result.value) {
								location.reload();
							}
						});
					}
				});
			}
		});
	}
</script>