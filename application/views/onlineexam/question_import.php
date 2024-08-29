<div class="row">
	<div class="col-md-12">
		<section class="panel">
		<?php echo form_open_multipart('onlineexam/questionCsvImport', array( 'class' => 'form-horizontal form-bordered frm-submit-data'));?>	
			<header class="panel-heading">
				<h4 class="panel-title">
					<i class="fas fa-file-archive"></i> <?=translate('import')?>
				</h4>
				<div class="panel-btn">
					<a href="<?=base_url('onlineexam/question')?>" class="btn btn-default btn-circle">
						<i class="fas fa-list"></i> <?=translate('question') . " " . translate('list')?>
					</a>
				</div>
			</header>
			<div class="panel-body">
			<?php if ($this->session->flashdata('csvimport')): ?>
				<div class="alert-danger p-sm"><?php echo $this->session->flashdata('csvimport'); ?></div>
			<?php endif; ?>
				<div class="form-group mt-md">
					<div class="col-md-12 mb-md">
						<a class="btn btn-default pull-right" href="<?=base_url('onlineexam/csv_Sampledownloader')?>">
							<i class='fas fa-file-download'></i> Download Sample Import File
						</a>
					</div>
					<div class="col-md-12">
						<div class="alert alert-subl">
							<strong>Instructions :</strong><br/>
							1. Download the first sample file.<br/>
							2. Open the downloaded 'csv' file and carefully fill the details of the question. <br/>
							3. Question Type should be used from within these Example : <strong class="text-dark">single_choice, multi_choice, true_false, descriptive</strong><br/>
							4. Question Level should be used from within these Example : <strong class="text-dark">easy, medium, hard</strong><br/>
							5. The Question Group comes from another table, so for the "Group Name", enter Group ID (can be found on the <strong class="text-dark">Question Group</strong> page). <br/>
							6. For Single Choice type questions for answer should be <strong class="text-dark">option_1</strong><br/>
							7. For Multi Choice type questions for answer should be <strong class="text-dark">["option_1","option_2"]</strong>
						</div>
					</div>
				</div>
			<?php if (is_superadmin_loggedin()): ?>
				<div class="form-group">
					<label class="control-label col-md-3"><?php echo translate('branch');?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
							data-plugin-selectTwo data-width='100%'");
						?>
						<span class="error"><?=form_error('branch_id')?></span>
					</div>
				</div>
			<?php endif; ?>
				<div class="form-group">
					<label class="control-label col-md-3"><?=translate('class')?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
							$arrayClass = $this->app_lib->getClass($branch_id);
							echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3"><?=translate('section')?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
							$arraySection = $this->app_lib->getSections(set_value('class_id'));
							echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' 
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3"><?=translate('subject')?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
							$arraySubject = array("" => translate('select_class_first'));
							echo form_dropdown("subject_id", $arraySubject, set_value('subject_id'), "class='form-control' id='subject_id'
							data-plugin-selectTwo data-width='100%' ");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="control-label col-md-3">CSV File <span class="required">*</span></label>
					<div class="col-md-6 mb-lg">
						<input type="file" name="userfile" class="dropify" data-height="140" data-allowed-file-extensions="csv" />
						<span class="error"></span>
					</div>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-3 col-md-2">
						<button type="submit" class="btn btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?=translate('import')?>
						</button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>
	</div>
</div>

<script type="text/javascript">
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
</script>