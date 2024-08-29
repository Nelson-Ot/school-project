<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
                <a href="<?=base_url('marksheet_template/index')?>">
                    <i class="fas fa-list-ul"></i> <?=translate('template') ." ". translate('list')?>
                </a>
			</li>
			<li class="active">
                <a href="#edit" data-toggle="tab">
                   <i class="far fa-edit"></i> <?=translate('edit') . " " . translate('template')?>
                </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="edit">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit-data'));?>
					<input type="hidden" name="marksheet_template_id" value="<?=$certificate['id']?>">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-8">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, $certificate['branch_id'], "class='form-control' data-width='100%' onchange='getClassByBranch(this.value)'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('template') . " " . translate('name')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="marksheet_template_name" value="<?=$certificate['name']?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3">Page Layout <span class="required">*</span></label>
						<div class="col-md-8">
							<?php
								$arrayType = array(
									'' => translate('select'),
									'1' => "Portrait",
									'2' => "Landscape"
								);
								echo form_dropdown("page_layout", $arrayType, $certificate['page_layout'], "class='form-control' data-width='100%'
								data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">User Photo Style <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="row">
								<div class="col-xs-6">
									<?php
										$arrayType = array(
											'1' => "Square",
											'2' => "Round"
										);
										echo form_dropdown("photo_style", $arrayType, $certificate['photo_style'], "class='form-control' data-width='100%'
										data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
									?>
								</div>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="photo_size" value="<?=$certificate['photo_size']?>" placeholder="Photo Size (px)" />
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label">Layout Spacing <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="row">
								<div class="col-xs-6">
									<input type="text" class="form-control" name="top_space" value="<?=$certificate['top_space']?>" placeholder="Top Space (px)" />
								</div>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="bottom_space" value="<?=$certificate['bottom_space']?>" placeholder="Bottom Space (px)" />
								</div>
							</div>
						</div>
						<div class="mt-md col-md-offset-3 col-md-8">
							<div class="row">
								<div class="col-xs-6">
									<input type="text" class="form-control" name="right_space" value="<?=$certificate['right_space']?>" placeholder="Right Space (px)" />
								</div>
								<div class="col-xs-6">
									<input type="text" class="form-control" name="left_space" value="<?=$certificate['left_space']?>" placeholder="Left Space (px)" />
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>

					<input type="hidden" name="old_background_file" value="<?=$certificate['background']?>">
					<input type="hidden" name="old_logo_file" value="<?=$certificate['logo']?>">
					<input type="hidden" name="old_left_signature_file" value="<?=$certificate['left_signature']?>">
					<input type="hidden" name="old_middle_signature_file" value="<?=$certificate['middle_signature']?>">
					<input type="hidden" name="old_right_signature_file" value="<?=$certificate['right_signature']?>">

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('left') . " " . translate('signature')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="left_signature_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('middle') . " " . translate('signature')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="middle_signature_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('right') . " " . translate('signature')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="right_signature_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('logo') . " " . translate('image')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="logo_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('background') . " " . translate('image')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="background_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('header') . " " . translate('content') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<textarea name="header_content" class="form-control texteEditor" id="texteEditor1" rows="10"><?php echo $certificate['header_content'] ?></textarea>
							<span class="error"></span>
							<div class="studenttags">
							<?php 
							$tagsList = $this->marksheet_template_model->tagsList(); 
							foreach ($tagsList as $key => $value) {
								?>
								<a data-value=" <?=$value?> " class="btn btn-default mt-sm btn-xs btn_tag1"><?=$value?></a>
							<?php } ?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('footer') . " " . translate('content') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<textarea name="footer_content" class="form-control texteEditor" id="texteEditor2" rows="10"><?php echo $certificate['footer_content'] ?></textarea>
							<span class="error"></span>
							<div class="studenttags">
							<?php 
							foreach ($tagsList as $key => $value) {
								?>
								<a data-value=" <?=$value?> " class="btn btn-default mt-sm btn-xs btn_tag2"><?=$value?></a>
							<?php } ?>
							</div>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-offset-3 col-md-8">
							<div class="checkbox-replace">
								<label class="i-checks">
									<input type="checkbox" name="attendance_percentage" value="true" <?php echo $certificate['attendance_percentage'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('attendance') . " " . translate('percentage'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="grading_scale" value="true" <?php echo $certificate['grading_scale'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('grading_scale'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="position" value="true" <?php echo $certificate['position'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('position'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="cumulative_average" value="true" <?php echo $certificate['cumulative_average'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('cumulative') . " " . translate('average'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="class_average" value="true" <?php echo $certificate['class_average'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('class') . " " . translate('average'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="subject_position" value="true" <?php echo $certificate['subject_position'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('subject') . " " . translate('position'); ?> 
								</label>
							</div>
							<div class="checkbox-replace mt-sm">
								<label class="i-checks">
									<input type="checkbox" name="remark" value="true" <?php echo $certificate['remark'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('remark'); ?>
								</label>
							</div>
							<div class="checkbox-replace mt-sm mb-lg">
								<label class="i-checks">
									<input type="checkbox" name="result" value="true" <?php echo $certificate['result'] == 1 ? 'checked' : ''; ?>><i></i> <?php echo translate('result'); ?>
								</label>
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
		if ($(".texteEditor").length) {
			$('.texteEditor').summernote({
				fontNames: ['Arial', 'Arial Black', 'Consolas','Tahoma', 'Times New Roman', 'Great Vibes', 'Pinyon Script', 'Parisienne'],
				fontNamesIgnoreCheck: ['Great Vibes', 'Pinyon Script', 'Parisienne'],
				fontSizes: ['8', '9', '10', '11', '12', '14', '18', '24', '28', '36', '48' , '64', '82'],
				height: 220,
				toolbar: [
					["style", ["style"]],
					["name", ["fontname","fontsize","height"]],
					["font", ["bold","italic","underline", "clear"]],
					["color", ["color"]],
					["para", ["ul", "ol", "paragraph"]],
					["insert", ["link","table"]],
					["misc", ["fullscreen", "undo", "codeview"]]
				]
			});
		}

		$('.btn_tag1').on('click', function() {
			var txtToAdd = $(this).data("value");
			$('#texteEditor1').summernote('editor.insertText', txtToAdd);
		});
		$('.btn_tag2').on('click', function() {
			var txtToAdd = $(this).data("value");
			$('#texteEditor2').summernote('editor.insertText', txtToAdd);
		});
	});
</script>