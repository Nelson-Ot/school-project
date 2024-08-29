<?php $widget = (is_superadmin_loggedin() ? 3 : 4); ?>
<?php if (is_superadmin_loggedin() ): ?>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
	<div class="panel-body">
		<div class="row mb-sm">
			<div class="col-md-offset-3 col-md-6">
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control'
						data-plugin-selectTwo data-width='100%'");
					?>
				</div>
			</div>
		</div>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-md-offset-10 col-md-2">
				<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
			</div>
		</div>
	</footer>
	<?php echo form_close();?>
</section>
<?php 
endif;
	if (!empty($branch_id)): 
 		if (is_superadmin_loggedin()) { ?>
<div class="row appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
<?php } else { ?>
<div class="row">
<?php } ?>	
	<div class="col-md-12">
		<section class="panel">
			<?php echo form_open_multipart('student/save', array('class' => 'frm-submit-data' )); ?>
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-graduation-cap"></i> <?=translate('student_admission')?></h4>
			</header>
			<div class="panel-body">
				
				<!-- academic details-->
				<div class="headers-line">
					<i class="fas fa-school"></i> <?=translate('academic_details')?>
				</div>
				
				<?php
				$academic_year = get_session_id(); 
				$roll = $this->student_fields_model->getStatus('roll', $branch_id);
				$admission_date = $this->student_fields_model->getStatus('admission_date', $branch_id);
                $v = (2 + floatval($roll['status']) + floatval($admission_date['status']));
                $div = floatval(12 / $v);
				?>
				<div class="row">
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('academic_year')?> <span class="required">*</span></label>
							<?php
								$arrayYear = array("" => translate('select'));
								$years = $this->db->get('schoolyear')->result();
								foreach ($years as $year){
									$arrayYear[$year->id] = $year->school_year;
								}
								echo form_dropdown("year_id", $arrayYear, set_value('year_id', $academic_year), "class='form-control' id='academic_year_id'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"></span>
						</div>
					</div>
					
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('register_no')?> <span class="required">*</span></label>
							<input type="text" class="form-control" name="register_no" value="<?=set_value('register_no', $register_id)?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php if ($roll['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('roll')?><?php echo $roll['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="roll" value="<?=set_value('roll')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($admission_date['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('admission_date')?><?php echo $admission_date['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="admission_date" value="<?=set_value('admission_date', date('Y-m-d'))?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>
				<?php
				$category = $this->student_fields_model->getStatus('category', $branch_id);
                $v = (2 + floatval($category['status']));
                $div = floatval(12 / $v);
				?>
				<div class="row mb-md">
					<?php if (is_superadmin_loggedin()): ?>
						<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
					<?php endif; ?>
					<div class="col-md-<?php echo $div; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="col-md-<?php echo $div; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), false);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' 
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php if ($category['status']) { ?>
					<div class="col-md-<?php echo $div; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('category')?><?php echo $category['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<?php
								$arrayCategory = $this->app_lib->getStudentCategory($branch_id);
								echo form_dropdown("category_id", $arrayCategory, set_value('category_id'), "class='form-control'
								data-plugin-selectTwo data-width='100%' id='category_id' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<!-- student details -->
				<div class="headers-line mt-md">
					<i class="fas fa-user-check"></i> <?=translate('student_details')?>
				</div>
				<?php
				$last_name = $this->student_fields_model->getStatus('last_name', $branch_id);
				$gender = $this->student_fields_model->getStatus('gender', $branch_id);
                $v = (1 + floatval($last_name['status']) + floatval($gender['status']));
                $div = floatval(12 / $v);
				?>
				<div class="row">
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"> <?=translate('first_name')?> <span class="required">*</span></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
								<input type="text" class="form-control" name="first_name" value="<?=set_value('first_name')?>"/>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php if ($last_name['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"> <?=translate('last_name')?><?php echo $last_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
								<input type="text" class="form-control" name="last_name" value="<?=set_value('last_name')?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($gender['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"> <?=translate('gender')?><?php echo $gender['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<?php
								$arrayGender = array(
									'male' => translate('male'),
									'female' => translate('female')
								);
								echo form_dropdown("gender", $arrayGender, set_value('gender'), "class='form-control' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="row">
					<?php 
					$blood_group = $this->student_fields_model->getStatus('blood_group', $branch_id);
					$birthday = $this->student_fields_model->getStatus('birthday', $branch_id);
					$v = floatval($blood_group['status']) + floatval($birthday['status']);
					$div = ($v == 0) ? 12 : floatval(12 / $v);

					if ($blood_group['status']) {
					?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('blood_group')?><?php echo $blood_group['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<?php
								$bloodArray = $this->app_lib->getBloodgroup();
								echo form_dropdown("blood_group", $bloodArray, set_value("blood_group"), "class='form-control populate' data-plugin-selectTwo 
								data-width='100%' data-minimum-results-for-search='Infinity' ");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($birthday['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('birthday')?><?php echo $birthday['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
								<input type="text" autocomplete="off" class="form-control" name="birthday" value="<?=set_value('birthday')?>" data-plugin-datepicker
								data-plugin-options='{ "startView": 2 }' />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="row">
					<?php 
					$mother_tongue = $this->student_fields_model->getStatus('mother_tongue', $branch_id);
					$religion = $this->student_fields_model->getStatus('religion', $branch_id);
					$caste = $this->student_fields_model->getStatus('caste', $branch_id);
					
					$v = floatval($mother_tongue['status']) + floatval($religion['status']) + floatval($caste['status']);
					$div = ($v == 0) ? 12 : floatval(12 / $v);
					if ($mother_tongue['status']) {
					?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('mother_tongue')?><?php echo $mother_tongue['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="mother_tongue" value="<?=set_value('mother_tongue')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($religion['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('religion')?><?php echo $religion['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="religion" value="<?=set_value('religion')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($caste['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('caste')?><?php echo $caste['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="caste" value="<?=set_value('caste')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="row">
					<?php 
					$student_mobile_no = $this->student_fields_model->getStatus('student_mobile_no', $branch_id);
					$student_email = $this->student_fields_model->getStatus('student_email', $branch_id);
					$city = $this->student_fields_model->getStatus('city', $branch_id);
					$state = $this->student_fields_model->getStatus('state', $branch_id);

					$v = floatval($student_mobile_no['status']) + floatval($student_email['status']) + floatval($city['status'])  + floatval($state['status']);
					$div = ($v == 0) ? 12 : floatval(12 / $v);
					if ($student_mobile_no['status']) {
					?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('mobile_no')?><?php echo $student_mobile_no['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
								<input type="text" class="form-control" name="mobileno" value="<?=set_value('mobileno')?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($student_email['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('email')?><?php echo $student_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
								<input type="text" class="form-control" name="email" id="email" value="<?=set_value('email')?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($city['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('city')?><?php echo $city['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="city" value="<?=set_value('city')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($state['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('state')?><?php echo $state['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="state" value="<?=set_value('state')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="row">
					<?php 
					$present_address = $this->student_fields_model->getStatus('present_address', $branch_id);
					$permanent_address = $this->student_fields_model->getStatus('permanent_address', $branch_id);
					$v = floatval($present_address['status']) + floatval($permanent_address['status']);
					$div = ($v == 0) ? 12 : floatval(12 / $v);

					if ($present_address['status']) {
						?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('present_address')?><?php echo $present_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<textarea name="current_address" rows="2" class="form-control" aria-required="true"><?=set_value('current_address')?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<?php } if ($permanent_address['status']) { ?>
					<div class="col-md-<?php echo $div ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('permanent_address')?><?php echo $permanent_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<textarea name="permanent_address" rows="2" class="form-control" aria-required="true"><?=set_value('permanent_address')?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<!--custom fields details-->
				<div class="row" id="customFields">
					<?php echo render_custom_Fields('student'); ?>
				</div>

				<div class="row">
					<?php 
					$student_photo = $this->student_fields_model->getStatus('student_photo', $branch_id);
					if ($student_photo['status']) {
					?>
					<div class="col-md-12 mb-sm">
						<div class="form-group">
							<label for="input-file-now"><?=translate('profile_picture')?><?php echo $student_photo['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="file" name="user_photo" class="dropify" data-default-file="<?=get_image_url('student')?>" />
							<span class="error"></span>
						</div>
					</div>
					<?php } ?>
				</div>

				<div class="<?=$getBranch['stu_generate'] == 1 || $getBranch['stu_generate'] == "" ? 'hidden-div' : '' ?>" id="stuLogin">
					<!-- login details -->
					<div class="headers-line mt-md">
						<i class="fas fa-user-lock"></i> <?=translate('login_details')?>
					</div>
					<div class="row mb-md">
						<div class="col-md-6 mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('username')?> <span class="required">*</span></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="far fa-user"></i></span>
									<input type="text" class="form-control" name="username" id="username" value="<?=set_value('username')?>" />
								</div>
								<span class="error"><?=form_error('username')?></span>
							</div>
						</div>
						<div class="col-md-3 mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('password')?> <span class="required">*</span></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
									<input type="password" class="form-control" name="password" value="<?=set_value('password')?>" />
								</div>
								<span class="error"><?=form_error('password')?></span>
							</div>
						</div>
						<div class="col-md-3 mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('retype_password')?> <span class="required">*</span></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
									<input type="password" class="form-control" name="retype_password" value="<?=set_value('retype_password')?>" />
								</div>
								<span class="error"><?=form_error('retype_password')?></span>
							</div>
						</div>
					</div>
				</div>

				<?php 
				$guardian_name = $this->student_fields_model->getStatus('guardian_name', $branch_id);
				$guardian_relation = $this->student_fields_model->getStatus('guardian_relation', $branch_id);
				$father_name = $this->student_fields_model->getStatus('father_name', $branch_id);
				$mother_name = $this->student_fields_model->getStatus('mother_name', $branch_id);
				$guardian_occupation = $this->student_fields_model->getStatus('guardian_occupation', $branch_id);
				$guardian_income = $this->student_fields_model->getStatus('guardian_income', $branch_id);
				$guardian_education = $this->student_fields_model->getStatus('guardian_education', $branch_id);
				$guardian_city = $this->student_fields_model->getStatus('guardian_city', $branch_id);
				$guardian_state = $this->student_fields_model->getStatus('guardian_state', $branch_id);
				$guardian_mobile_no = $this->student_fields_model->getStatus('guardian_mobile_no', $branch_id);
				$guardian_email = $this->student_fields_model->getStatus('guardian_email', $branch_id);
				$guardian_address = $this->student_fields_model->getStatus('guardian_address', $branch_id);
				$guardian_photo = $this->student_fields_model->getStatus('guardian_photo', $branch_id);
				?>
				<!--guardian details-->
				<div class="headers-line  mt-md">
					<i class="fas fa-user-tie"></i> <?=translate('guardian_details')?>
				</div>
				
				<div class="mb-sm checkbox-replace">
					<label class="i-checks">
						<input type="checkbox" name="guardian_chk" id="chkGuardian" value="true" <?php if(set_value('guardian_chk') == true) echo 'checked';?>><i></i> 
						<?=translate('guardian_already_exist')?>
					</label>
				</div>

				<div class="row" id="exist_list" <?php if(set_value('guardian_chk') != true) echo 'style="display: none;"'; ?>>
					<div class="col-md-12 mb-md">
						<label class="control-label"><?=translate('guardian')?> <span class="required">*</span></label>
						<div class="form-group">
							<?php
								$arrayParent = $this->app_lib->getSelectByBranch('parent', $branch_id);
								echo form_dropdown("parent_id", $arrayParent, set_value('parent_id'), "class='form-control' id='parent_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"><?=form_error('parent_id')?></span>
						</div>
					</div>
				</div>

				<?php if ($guardian_name['status'] || $guardian_relation['status'] || $father_name['status'] || $mother_name['status'] || $guardian_occupation['status'] || $guardian_income['status'] || $guardian_education['status'] || $guardian_email['status'] || $guardian_mobile_no['status'] || $guardian_address['status'] || $guardian_photo['status']) { ?>
				<div id="guardian_form" <?php if(set_value('guardian_chk') == true) echo 'style="display: none;"'; ?>>
					<div class="row">
						<?php 
                        $v = floatval($guardian_name['status']) + floatval($guardian_relation['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
						if ($guardian_name['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('name')?><?php echo $guardian_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_name" type="text" value="<?=set_value('grd_name')?>">
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_relation['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('relation')?><?php echo $guardian_relation['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input type="text" class="form-control" name="grd_relation" value="<?=set_value('grd_relation')?>" />
								<span class="error"></span>
							</div>
						</div>
						<?php } ?>
					</div>
					<div class="row">
						<?php 
                        $v = floatval($father_name['status']) + floatval($mother_name['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
						if ($father_name['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('father_name')?><?php echo $father_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input type="text" class="form-control" name="father_name" value="<?=set_value('father_name')?>" />
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($mother_name['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('mother_name')?><?php echo $mother_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input type="text" class="form-control" name="mother_name" value="<?=set_value('mother_name')?>" />
								<span class="error"></span>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="row">
						<?php 
                        $v = floatval($guardian_occupation['status']) + floatval($guardian_income['status']) + floatval($guardian_education['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
						if ($guardian_occupation['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('occupation')?><?php echo $guardian_occupation['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_occupation" value="<?=set_value('grd_occupation')?>" type="text">
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_income['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('income')?><?php echo $guardian_income['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_income" value="<?=set_value('grd_income')?>" type="text">
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_education['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('education')?><?php echo $guardian_education['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_education" value="<?=set_value('grd_education')?>" type="text">
								<span class="error"></span>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="row">
						<?php 
                        $v = floatval($guardian_city['status']) + floatval($guardian_state['status']) + floatval($guardian_mobile_no['status']) + floatval($guardian_email['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
						if ($guardian_city['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('city')?><?php echo $guardian_city['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_city" value="<?=set_value('grd_city')?>" type="text">
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_state['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('state')?><?php echo $guardian_state['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input class="form-control" name="grd_state" value="<?=set_value('grd_state')?>" type="text">
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_mobile_no['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('mobile_no')?><?php echo $guardian_mobile_no['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
									<input class="form-control" name="grd_mobileno" type="text" value="<?=set_value('grd_mobileno')?>">
								</div>
								<span class="error"></span>
							</div>
						</div>
						<?php } if ($guardian_email['status']) { ?>
						<div class="col-md-<?php echo $div ?> mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('email')?><?php echo $guardian_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<div class="input-group">
									<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
									<input type="email" class="form-control" name="grd_email" id="grd_email" value="<?=set_value('grd_email')?>" />
								</div>
								<span class="error"></span>
							</div>
						</div>
						<?php } ?>
					</div>

					<?php if ($guardian_address['status']) { ?>
					<div class="row">
						<div class="col-md-12 mb-sm">
							<div class="form-group">
								<label class="control-label"><?=translate('address')?><?php echo $guardian_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<textarea name="grd_address" rows="2" class="form-control" aria-required="true"><?=set_value('grd_address')?></textarea>
								<span class="error"></span>
							</div>
						</div>
					</div>
					<?php } ?>

					<div class="row">
						<?php if ($guardian_photo['status']) { ?>
						<div class="col-md-12 mb-sm">
							<div class="form-group">
								<label for="input-file-now"><?=translate('guardian_picture')?><?php echo $guardian_photo['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
								<input type="file" name="guardian_photo" class="dropify" data-default-file="<?=get_image_url('parent')?>" />
								<span class="error"></span>
							</div>
						</div>
						<?php } ?>
					</div>

					<div class="<?=$getBranch['grd_generate'] == 1 || $getBranch['grd_generate'] == "" ? 'hidden-div' : ''?>" id="grdLogin">
						<div class="row mb-lg">
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('usename')?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-user"></i></span>
										<input type="text" class="form-control" name="grd_username" id="grd_username" value="<?=set_value('grd_username')?>" />
									</div>
									<span class="error"><?=form_error('grd_username')?></span>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('password')?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
										<input type="password" class="form-control" name="grd_password" value="<?=set_value('grd_password')?>" />
									</div>
									<span class="error"><?=form_error('grd_password')?></span>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('retype_password')?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
										<input type="password" class="form-control" name="grd_retype_password" value="<?=set_value('grd_retype_password')?>" />
									</div>
									<span class="error"><?=form_error('grd_retype_password')?></span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<?php } ?>
				
				<?php if (moduleIsEnabled('transport')) { ?>
				<!-- transport details -->
				<div class="headers-line  mt-md">
					<i class="fas fa-bus-alt"></i> <?=translate('transport_details')?>
				</div>

				<div class="row mb-md">
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('transport_route')?></label>
							<?php
								$arrayRoute = $this->app_lib->getSelectByBranch('transport_route', $branch_id);
								echo form_dropdown("route_id", $arrayRoute, set_value('route_id'), "class='form-control' id='route_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('vehicle_no')?></label>
							<?php
								$arrayVehicle = $this->app_lib->getVehicleByRoute(set_value('route_id'));
								echo form_dropdown("vehicle_id", $arrayVehicle, set_value('vehicle_id'), "class='form-control' id='vehicle_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
				</div>
				<?php } ?>
				<?php if (moduleIsEnabled('hostel')) { ?>
				<!-- hostel details -->
				<div class="headers-line">
					<i class="fas fa-hotel"></i> <?=translate('hostel_details')?>
				</div>
				
				<div class="row mb-md">
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('hostel_name')?></label>
							<?php
								$arrayHostel = $this->app_lib->getSelectByBranch('hostel', $branch_id);
								echo form_dropdown("hostel_id", $arrayHostel, set_value('hostel_id'), "class='form-control' id='hostel_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('room_name')?></label>
							<?php
								$arrayRoom = $this->app_lib->getRoomByHostel(set_value('hostel_id'));
								echo form_dropdown("room_id", $arrayRoom, set_value('room_id'), "class='form-control' id='room_id'
								data-plugin-selectTwo data-width='100%' ");
							?>
						</div>
					</div>
				</div>
				<?php } ?>
				<!-- previous school details -->
				<?php
				$previous_school_details = $this->student_fields_model->getStatus('previous_school_details', $branch_id);
				if ($previous_school_details['status']) {
					?>
				<!-- previous school details -->
				<div class="headers-line">
					<i class="fas fa-bezier-curve"></i> <?=translate('previous_school_details')?>
				</div>
				<div class="row">
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('school_name')?><?php echo $previous_school_details['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="school_name" value="<?=set_value('school_name')?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="col-md-6 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('qualification')?><?php echo $previous_school_details['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
							<input type="text" class="form-control" name="qualification" value="<?=set_value('qualification')?>" />
							<span class="error"></span>
						</div>
					</div>
				</div>
				<div class="row mb-lg">
					<div class="col-md-12">
						<div class="form-group">
							<label class="control-label"><?=translate('remarks')?></label>
							<textarea name="previous_remarks" rows="2" class="form-control"><?=set_value('previous_remarks')?></textarea>
						</div>
					</div>
				</div>
				<?php } ?>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn btn-default btn-block">
							<i class="fas fa-plus-circle"></i> <?=translate('save')?>
						</button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>
	</div>
</div>
<?php endif; ?>
