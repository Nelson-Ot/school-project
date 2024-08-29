<style type="text/css">
	.checkbox-replace {
		padding-left: 0px !important;
	}
	.note-editor .modal-content {
		padding: 10px;
	}
	.note-editor .modal {
		z-index: 1150;
	}
</style>
<?php $widget = (is_superadmin_loggedin() ? 3 : 4); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
		<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
				<div class="panel-btn">
					<a href="<?=base_url('onlineexam/question')?>" class="btn btn-default btn-circle">
						<i class="fas fa-list"></i> <?=translate('question') . " " . translate('list')?>
					</a>
				</div>
			</header>
			<div class="panel-body">
				<div class="row mb-sm">
					<?php if (is_superadmin_loggedin()): ?>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
						</div>
					</div>
					<?php endif; ?>

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
								$arraySection = $this->app_lib->getSections(set_value('class_id'), false);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?>">
						<div class="form-group">
							<label class="control-label"><?=translate('subject')?> <span class="required">*</span></label>
							<?php
								if(!empty(set_value('class_id'))) {
									$arraySubject = array("" => translate('select'));
									$query = $this->subject_model->getSubjectByClassSection(set_value('class_id'), set_value('section_id'));
									$subjects = $query->result_array();
									foreach ($subjects as $row){
										$subjectID = $row['subject_id'];
										$arraySubject[$subjectID] = $row['subjectname'];
									}
								} else {
									$arraySubject = array("" => translate('select_class_first'));
								}
								echo form_dropdown("subject_id", $arraySubject, set_value('subject_id'), "class='form-control' id='subject_id'
								required data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
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
		
<?php if (!empty($branch_id) && !empty(set_value('class_id'))) { ?>
		<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
	                <a href="#single" data-toggle="tab">
	                    <i class="far fa-edit"></i> Single Choice
	                </a>
					</li>
					<li>
	                <a href="#multiple" data-toggle="tab">
	                   <i class="far fa-edit"></i> Multiple Choice
	                </a>
					</li>
					<li>
	                <a href="#true_false" data-toggle="tab">
	                   <i class="far fa-edit"></i> True/False
	                </a>
					</li>
					<li>
	                <a href="#descriptive" data-toggle="tab">
	                   <i class="far fa-edit"></i> Descriptive
	                </a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane box active" id="single">
						<?php echo form_open_multipart('onlineexam/question_save', array('class' => 'form-bordered form-horizontal frm-submit-question'));?>
							<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<input type="hidden" name="class_id" value="<?php echo set_value('class_id') ?>">
							<input type="hidden" name="section_id" value="<?php echo set_value('section_id') ?>">
							<input type="hidden" name="subject_id" value="<?php echo set_value('subject_id') ?>">
							<input type="hidden" name="question_type" value="1">
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question_level')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										$arrayLevel = $this->onlineexam_model->question_level();
										echo form_dropdown("question_level", $arrayLevel, set_value('question_level'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question') . " " . translate('group')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										$arrayGroup = $this->onlineexam_model->question_group($branch_id);
										echo form_dropdown("group_id", $arrayGroup, set_value('group_id'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('mark')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="mark" autocomplete="off" value="">
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="question" rows="2" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>

							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 1 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option1" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 2 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option2" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 3</label>
								<div class="col-md-6">
									<textarea name="option3" class="question_note"></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 4</label>
								<div class="col-md-6">
									<textarea name="option4" class="question_note"></textarea>
								</div>
							</div>
							<div class="form-group">
							   <label class="col-md-3 control-label"><?=translate('answer')?> <span class="required">*</span></label>
							   <div class="col-md-6 mb-lg">
							       <div class="radio-custom radio-success radio-inline mb-xs">
							           <input type="radio" value="1" name="answer" id="opt1">
							           <label for="opt1"><?=translate('option')?> 1</label>
							       </div>
							       <div class="radio-custom radio-success radio-inline">
							           <input type="radio" value="2" name="answer" id="opt2">
							           <label for="opt2"><?=translate('option')?> 2</label>
							       </div>
							       <div class="radio-custom radio-success radio-inline">
							           <input type="radio" value="3" name="answer" id="opt3">
							           <label for="opt3"><?=translate('option')?> 3</label>
							       </div>
							       <div class="radio-custom radio-success radio-inline mb-none">
							           <input type="radio" value="4" name="answer" id="opt4">
							           <label for="opt4"><?=translate('option')?> 4</label>
							       </div>
							       <div>
							       	<span class="error"></span>
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
					<div class="tab-pane box" id="multiple">
						<?php echo form_open_multipart('onlineexam/question_save', array('class' => 'form-bordered form-horizontal frm-submit-question'));?>
							<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<input type="hidden" name="class_id" value="<?php echo set_value('class_id') ?>">
							<input type="hidden" name="section_id" value="<?php echo set_value('section_id') ?>">
							<input type="hidden" name="subject_id" value="<?php echo set_value('subject_id') ?>">
							<input type="hidden" name="question_type" value="2">
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question_level')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("question_level", $arrayLevel, set_value('question_level'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question') . " " . translate('group')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("group_id", $arrayGroup, set_value('group_id'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('mark')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="mark" autocomplete="off" value="">
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="question" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 1 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option1" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 2 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option2" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 3 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option3" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('option')?> 4 <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="option4" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
							   <label class="col-md-3 control-label">Answer <span class="required">*</span></label>
							   <div class="col-md-6 mb-lg">
									<div class="checkbox-replace radio-inline">
										<label class="i-checks"><input type="checkbox" name="answer[]" value="1"><i></i> <?=translate('option')?> 1</label>
									</div>
									<div class="checkbox-replace radio-inline">
										<label class="i-checks"><input type="checkbox" name="answer[]" value="2"><i></i> <?=translate('option')?> 2</label>
									</div>
									<div class="checkbox-replace radio-inline">
										<label class="i-checks"><input type="checkbox" name="answer[]" value="3"><i></i> <?=translate('option')?> 3</label>
									</div>
									<div class="checkbox-replace radio-inline">
										<label class="i-checks"><input type="checkbox" name="answer[]" value="4"><i></i> <?=translate('option')?> 4</label>
									</div>
									<div>
							      	<span class="error"></span>
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
					<div class="tab-pane box" id="true_false">
						<?php echo form_open_multipart('onlineexam/question_save', array('class' => 'form-bordered form-horizontal frm-submit-question'));?>
							<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<input type="hidden" name="class_id" value="<?php echo set_value('class_id') ?>">
							<input type="hidden" name="section_id" value="<?php echo set_value('section_id') ?>">
							<input type="hidden" name="subject_id" value="<?php echo set_value('subject_id') ?>">
							<input type="hidden" name="question_type" value="3">

							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question_level')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("question_level", $arrayLevel, set_value('question_level'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question') . " " . translate('group')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("group_id", $arrayGroup, set_value('group_id'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('mark')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="mark" autocomplete="off" value="">
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="question" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
							   <label class="col-md-3 control-label"><?=translate('answer')?> <span class="required">*</span></label>
							   <div class="col-md-6 mb-lg">
									<?php
										$arrayAnswer = array(
											'' => translate('select'), 
											'1' => translate('true'), 
											'2' => translate('false'), 
										);
										echo form_dropdown("answer", $arrayAnswer, set_value('answer'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
							      <span class="error"></span>
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
					<div class="tab-pane box" id="descriptive">
						<?php echo form_open_multipart('onlineexam/question_save', array('class' => 'form-bordered form-horizontal frm-submit-question'));?>
							<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<input type="hidden" name="class_id" value="<?php echo set_value('class_id') ?>">
							<input type="hidden" name="section_id" value="<?php echo set_value('section_id') ?>">
							<input type="hidden" name="subject_id" value="<?php echo set_value('subject_id') ?>">
							<input type="hidden" name="question_type" value="4">

							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question_level')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("question_level", $arrayLevel, set_value('question_level'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question') . " " . translate('group')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<?php
										echo form_dropdown("group_id", $arrayGroup, set_value('group_id'), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('mark')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="mark" autocomplete="off" value="">
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('question')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<textarea name="question" class="question_note"></textarea>
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('answer')?> <span class="required">*</span></label>
								<div class="col-md-6 mb-md">
									<textarea name="answer" rows="4" class="form-control"></textarea>
									<span class="error"></span>
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
				</div>
			</div>
		</section>
	<?php } ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		$("form.frm-submit-question").each(function(i, el)
		{
		  var $this = $(el);
		  $this.on('submit', function(e){
		      e.preventDefault();
		      var btn = $this.find('[type="submit"]');
		      $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
		          success: function (data) {
		              $('.error').html("");
		              if (data.status == "fail") {
		                  $.each(data.error, function (index, value) {
		                      $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
		                  });
		                  btn.button('reset');
		              } else if (data.status == "access_denied") {
		                  window.location.href = base_url + "dashboard";
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
		                  // reset all form field
		                  $(el).trigger("reset");
		                  $("select[name='question_level'], select[name='answer']").val('').trigger('change');
		                	// reset all summernote field
		                  $(".question_note").each(function(c, elm) {
		                  	$(elm).summernote('code', '<p><br></p>');
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

		$('#branch_id').on('change', function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			$('#subject_id').html('').append('<option value=""><?=translate("select")?></option>');
		});
		
		$('#section_id').on('change', function() {
			var classID = $('#class_id').val();
			var sectionID =$(this).val();
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