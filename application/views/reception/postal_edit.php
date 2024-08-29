
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('reception/postal'); ?>">
				  <i class="fas fa-list-ul"></i> <?=translate('postal_record') ." ". translate('list')?>
				</a>
			</li>
			<li class="active">
				<a href="#add" data-toggle="tab">
				 <i class="far fa-edit"></i> <?=translate('edit') . " ". translate('postal_record')?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="add">
					<?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit-data'));?>
					<input type="hidden" name="id" value="<?php echo $row['id'] ?>">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-8">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, $row['branch_id'], "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('type')?> <span class="required">*</span></label>
						<div class="col-md-8">
								<?php
									$arrayBranch = array(
										'' => translate('select'),
										'1' => translate('dispatch'),
										'2' => translate('receive'),
									);
									echo form_dropdown("type", $arrayBranch, $row['type'], "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('reference_no')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="reference_no" value="<?php echo $row['reference_no'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('sender') . " " . translate('title')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="sender_title" value="<?php echo $row['sender_title'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('receiver') . " " . translate('title')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="receiver_title" value="<?php echo $row['receiver_title'] ?>" />
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('address')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<textarea type="text" rows="3" class="form-control" name="address"><?php echo $row['address'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-8">
							<textarea type="text" rows="3" class="form-control" name="note"><?php echo $row['note'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="date" value="<?=$row['date']?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('document') . " " . translate('file')?></label>
						<div class="col-md-8">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<input type="hidden" name="old_document_file" value="<?php echo $row['file'] ?>">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="document_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Confidential</label>
						<div class="col-md-6 mt-xs mb-lg">
							<div class="material-switch ml-xs">
								<input id="aswitch_1" name="confidential" type="checkbox" <?php echo ($row['confidential'] == 1 ? 'checked' : ''); ?>  />
								<label for="aswitch_1" class="label-primary"></label>
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
