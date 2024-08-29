<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="far fa-folder"></i>  <?php echo translate('homework') . "  " . translate('list'); ?></h4>
			</header>
			<div class="panel-body">
				<section class="panel-group mt-md" id="accordion">
					<?php foreach ( $homeworklist as $key => $row ): ?>
					<div class="panel panel-accordion">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#<?php echo $row['id']; ?>">
									<i class="far fa-sticky-note"></i> <?php echo $row['subject_name']?> - <?=_d($row['date_of_homework'])?>
								</a>
							</h4>
						</div>
						<div id="<?php echo $row['id']; ?>" class="accordion-body collapse">
							<div class="panel-body">
								<div class="row">
									<div class="col-md-8">
										<p><?php echo $row['description']; ?></p>
									</div>
									<div class="col-md-4">
										<ul class="nav nav-stacked">
											<li><i class="far fa-calendar"></i> <span class="text-weight-semibold"><?=translate('date_of_homework')?></span> : <?=_d($row['date_of_homework'])?></li>
											<li><i class="far fa-calendar"></i> <span class="text-weight-semibold"><?=translate('date_of_submission')?></span> : <?=_d($row['date_of_submission'])?></li>
											<li><i class="far fa-calendar"></i> <span class="text-weight-semibold"><?=translate('evaluation_date')?></span> : <?=$row['evaluation_date'] != null ? _d($row['evaluation_date']) : "N/A";?></li>
											<li><span class="text-weight-semibold"><?=translate('created_by')?></span> : <?=$row['created_by'] != null ? get_type_name_by_id('staff',$row['created_by']) : "N/A";?></li>
											<li><span class="text-weight-semibold"><?=translate('status')?></span> : <?php 
											if ($row['ev_status'] == 'u' || $row['ev_status'] == '') {
												$submitStatus = $this->db->select('id')->where(['student_id' => $row['student_id'], 'homework_id' => $row['id']])->get('homework_submit')->row();
												if (empty($submitStatus) || $row['ev_status'] == 'u') {
													$labelmode = 'label-danger-custom';
													$status = translate('incomplete');
												} else {
													$labelmode = 'label-info-custom';
													$status = translate('submitted');
												}
											} else {
												$status = translate('complete');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
											 ?></li>
											<li><span class="text-weight-semibold"><?=translate('evaluated_by')?></span> : <?=!empty($row['evaluated_by']) ? get_type_name_by_id('staff',$row['evaluated_by']) : "N/A";?></li>
											<li><span class="text-weight-semibold"><?=translate('rank_out_of_5')?></span> : <?=!empty($row['rank']) ? $row['rank'] : "N/A";?></li>
											<li><span class="text-weight-semibold"><?=translate('remarks')?></span> : <?=!empty($row['ev_remarks']) ? $row['ev_remarks'] : "N/A";?></li>
										</ul>
										<ul class="nav nav-stacked mt-md">
											<li><span class="text-weight-semibold"><?=translate('subject')?></span> : <?=$row['subject_name']?></li>
											<li><span class="text-weight-semibold"><?=translate('class')?></span> : <?=$row['class_name']?></li>
											<li><span class="text-weight-semibold"><?=translate('section')?></span> : <?=$row['section_name']?></li>
											<li><span class="text-weight-semibold"><?=translate('documents')?></span> : <a href="<?=base_url('homework/download/' . $row['id'])?>" style="display: initial;" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?=translate('download')?>"><i class="fas fa-cloud-download-alt"></i></a></li>
										</ul>
									<?php if (!empty($row['enc_name'])) { ?>
										<ul class="nav nav-stacked mt-md">
											<li><span class="text-weight-semibold"><?=translate('submitted_file')?></span> : <a href="<?=base_url('homework/download_submitted?file=' . $row['enc_name'])?>" style="display: initial;" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?=translate('download')?>"><i class="fas fa-cloud-download-alt"></i></a></li>
										</ul>
									<?php 
										}
										$date_of_submission = strtotime($row['date_of_submission']);
										$today = strtotime(date('Y-m-d'));
										if($row['ev_status'] !== 'c' && $date_of_submission >= $today){
										?>
										<ul class="nav nav-stacked mt-md">
											<li><span class="text-weight-semibold"><?=translate('assignment')?></span> : <button onclick="showModal('<?php echo $row['id']?>')" class="btn btn-default btn-sm"><i class="fas fa-upload"></i> <?=translate('upload')?></button></li>
										</ul>
									<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; ?>
				</section>
			</div>
		</section>
	</div>
</div>

<div class="zoom-anim-dialog modal-block mfp-hide" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-bars"></i> <?php echo translate('assignment'); ?></h4>
		</header>
		<?php echo form_open_multipart('userrole/assignment_upload', array('class' => 'frm-submit-data'));?>
        <input type="hidden" id="homeworkID"  name="homework_id">
        <input type="hidden" id="assigmentID" name="assigment_id">
		<div class="panel-body">
			<div class="form-group">
				<label class="control-label"><?php echo translate('attachment_file') ?> <span class="required">*</span></label>
				<div class="row">
					<div class="col-md-12">
						<div class="fileupload fileupload-new" data-provides="fileupload">
							<div class="input-append">
								<div class="uneditable-input">
									<i class="fas fa-file fileupload-exists"></i>
									<span class="fileupload-preview"></span>
								</div>
								<span class="btn btn-default btn-file">
									<span class="fileupload-exists">Change</span>
									<span class="fileupload-new">Select file</span>
									<input type="file" name="attachment_file" value="ghgg.com" />
								</span>
								<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
							</div>
						</div>
						<span class="error"></span>
					</div>
				</div>
				<input type="hidden" id="old_file" name="old_file">
			</div>
			<div class="form-group mb-md">
				<label class="control-label"><?php echo translate('message') ?></label>
				<textarea name="message" id="message" class="form-control"></textarea>
				<span class="error"></span>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><?php echo translate('submit'); ?></button>
					<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<script type="text/javascript">
	function showModal(id)
	{
		$(".error").html("");
		$("#message").val("");
		$("#homeworkID").val(id);
		$.ajax({
			url: base_url + 'userrole/getHomeworkAssignment',
			type: 'POST',
			dataType: "json",
			data: { 'id': id },
			success: function (data) {
				$("#old_file").val(data.file_name);
				$("#assigmentID").val(data.id);
				$("#message").val(data.message);
			}
		});
		mfp_modal('#modal');
	}
</script>	
