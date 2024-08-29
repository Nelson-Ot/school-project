<?php  $widget = (is_superadmin_loggedin() ? 4 : 6); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,1)'
								required data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), true);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
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

		<?php if (isset($students)):?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-user-graduate"></i> <?php echo translate('login_credential') . " " . translate('reports');?></h4>
			</header>
			<div class="panel-body mb-md">
				<table class="table table-bordered table-condensed table-hover table-export">
					<thead>
						<tr>
							<th class="no-sort"><?=translate('photo')?></th>
							<th><?=translate('name')?></th>
							<th><?=translate('class')?></th>
							<th><?=translate('section')?></th>
							<th><?=translate('register_no')?></th>
							<th width="80"><?=translate('roll')?></th>
							<th><?=translate('guardian_name')?></th>
							<th><?=translate('student') . " " . translate('username')?></th>
							<th><?=translate('parent') . " " . translate('username')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($students as $row):
						?>
						<tr>
							<td class="center"><img src="<?php echo get_image_url('student', $row['photo']); ?>" height="50"></td>
							<td class="<?=($row['active'] == 0 ? 'text-danger' : '')?>"><?php echo $row['fullname'];?></td>
							<td><?php echo $row['class_name'];?></td>
							<td><?php echo $row['section_name'];?></td>
							<td><?php echo $row['register_no'];?></td>
							<td><?php echo $row['roll'];?></td>
							<td><?php echo (!empty($row['parent_id']) ? get_type_name_by_id('parent', $row['parent_id']) : 'N/A');?></td>
							<td><?php echo $row['stu_username'];?></td>
							<td><?php 
							if (empty($row['parent_id'])) {
								echo '-';
							} else {
								$parentID = $row['parent_id'];
								echo $this->db->where(['user_id' => $parentID, 'role' => 6])->get('login_credential')->row()->username;
							}?></td>
							<td class="action">
								<!-- quick view -->
								<a href="javascript:void(0);" onclick="studentQuickView('<?=$row['student_id']?>', '<?=$row['parent_id']?>');" class="btn btn-default btn-circle" data-toggle="tooltip"
								data-original-title="<?=translate('quick_view')?>">
									<i class="fa-solid fa-lock-open"></i> <?=translate('reset_password')?>
								</a>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</section>
		<?php endif;?>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="quickView">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="fa-solid fa-lock-open"></i> <?=translate('reset_password')?>
			</h4>
		</header>
		<div class="panel-body">
			<section class="panel pg-fw">
			    <div class="panel-body">
			        <h5 class="chart-title mb-xs"><?=translate('student') . " " . translate('change') . " " . translate('password')?></h5>
					<div class="mt-lg">
						<?php echo form_open('student/password_reset/student', array('class' => 'frm-submit'));?>
							<input type="hidden" name="student_id" id="studentID" value="">
							<div class="form-group">
								<label class="control-label"><?php echo translate('new_password'); ?> <span class="required">*</span></label>
								<input type="password" class="form-control" name="new_password" value="" aria-autocomplete="list">
								<span class="error"></span>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo translate('confirm_password'); ?> <span class="required">*</span></label>
								<input type="password" class="form-control" name="confirm_password" value="">
								<span class="error"></span>
							</div>

							<div class="row">
							    <div class="col-md-12 text-right">
							        <button type="submit" class="btn btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?></button>
							    </div>
							</div>
						<?php echo form_close(); ?>
					</div>
			    </div>
			</section>
			<section class="panel pg-fw">
			    <div class="panel-body">
			        <h5 class="chart-title mb-xs"><?=translate('parent') . " " . translate('change') . " " . translate('password')?></h5>
					<div class="mt-lg">
						<?php echo form_open('student/password_reset/parent', array('class' => 'frm-submit'));?>
							<input type="hidden" name="parent_id" id="parentID" value="">
							<div class="form-group">
								<label class="control-label"><?php echo translate('new_password'); ?> <span class="required">*</span></label>
								<input type="password" class="form-control" name="new_password" value="" autocomplete="off">
								<span class="error"></span>
							</div>
							<div class="form-group">
								<label class="control-label"><?php echo translate('confirm_password'); ?> <span class="required">*</span></label>
								<input type="password" class="form-control" name="confirm_password" value="" autocomplete="off">
								<span class="error"></span>
							</div>
							<div class="row">
							    <div class="col-md-12 text-right">
							        <button type="submit" class="btn btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-plus-circle"></i> Update</button>
							    </div>
							</div>
						<?php echo form_close(); ?>
					</div>
			    </div>
			</section>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss"><?=translate('close')?></button>
				</div>
			</div>
		</footer>
	</section>
</div>

<script type="text/javascript">
	function studentQuickView(studentID, parentID) {
		$('#quickView').find("input[type=password], textarea").val("");
		$('#quickView').find(".error").html("");
		$('#studentID').val(studentID);
		$('#parentID').val(parentID);
		mfp_modal('#quickView');
	}
</script>