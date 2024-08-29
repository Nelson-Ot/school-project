<?php $branch_id = $row['branch_id']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('reception/enquiry'); ?>">
				  <i class="fas fa-list-ul"></i> <?=translate('enquiry') ." ". translate('list')?>
				</a>
			</li>
			<li class="active">
				<a href="#add" data-toggle="tab">
				 <i class="far fa-edit"></i> <?=translate('edit') . " ". translate('enquiry')?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="add">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit'));?>
					<input type="hidden" name="id" value="<?php echo $row['id'] ?>">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
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
						<label class="col-md-3 control-label"><?=translate('name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="name" value="<?php echo $row['name'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('gender')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayGender = array(
									'' => translate('select'),
									'1' => translate('male'),
									'2' => translate('female')
								);
								echo form_dropdown("gender", $arrayGender, $row['gender'], "class='form-control' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('birthday')?></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
								<input type="text" class="form-control" name="birthday" value="<?php echo $row['birthday'] ?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('previous_school')?></label>
						<div class="col-md-6">
							<textarea type="text" rows="3" class="form-control" name="previous_school"><?php echo $row['previous_school'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('father_name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="father_name" value="<?php echo $row['father_name'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('mother_name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="mother_name" value="<?php echo $row['mother_name'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('mobile_no')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="mobile_no" value="<?php echo $row['mobile_no'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('email')?></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="email" value="<?php echo $row['email'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('address')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<textarea type="text" rows="3" class="form-control" name="address"><?php echo $row['address'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('no_of_child')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="no_of_child" value="<?php echo $row['no_of_child'] ?>" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('assigned')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
								$arrayBranch = $this->app_lib->getStaffList($branch_id);
								echo form_dropdown("staff_id", $arrayBranch, $row['assigned_id'], "class='form-control' data-width='100%' id='staff_id'
								data-plugin-selectTwo");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('reference')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$enquiryReference = $this->app_lib->getSelectByBranch('enquiry_reference', $branch_id);
									echo form_dropdown("reference", $enquiryReference, $row['reference_id'], "class='form-control' data-width='100%' id='referenceID'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('response')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$enquiryResponse = $this->app_lib->getSelectByBranch('enquiry_response', $branch_id);
									echo form_dropdown("response_id", $enquiryResponse, $row['response_id'], "class='form-control' data-width='100%' id='responseID'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('response')?></label>
						<div class="col-md-6">
							<textarea type="text" rows="3" class="form-control" name="response"><?php echo $row['response'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-6">
							<textarea type="text" rows="3" class="form-control" name="note"><?php echo $row['note'] ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="date" value="<?=$row['date']?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('class_applying_for')?> <span class="required">*</span></label>
						<div class="col-md-6 mb-lg">
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, $row['class_id'], "class='form-control' id='class_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
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
