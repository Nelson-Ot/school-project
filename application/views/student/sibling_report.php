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
				<h4 class="panel-title"><i class="fas fa-user-graduate"></i> <?php echo translate('sibling_report');?></h4>
			</header>
			<div class="panel-body mb-md">
				<table class="table table-bordered table-condensed nowrap table-hover table-export">
					<thead>
						<tr>
							<th class="no-sort">#</th>
							<th><?=translate('guardian_name')?></th>
							<th><?=translate('mobile_no')?></th>
							<th><?=translate('father_name')?></th>
							<th><?=translate('mother_name')?></th>
							<th><?=translate('occupation')?></th>
							<th><?=translate('sibling')?></th>
							<th><?=translate('register_no')?></th>
							<th><?=translate('class')?></th>
							<th><?=translate('gender')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						foreach($students as $row):
						?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $row['g_name'];?></td>
							<td><?php echo $row['mobileno'];?></td>
							<td><?php echo $row['father_name'];?></td>
							<td><?php echo $row['mother_name'];?></td>
							<td><?php echo $row['occupation'];?></td>
							<td class="p-none"><ul class="sibling">
								<?php foreach ($row['student'] as $key => $value) { ?>
									<li class="hidden-print"><a target="_blank" class="au-none" href="<?php echo base_url("student/profile/" . $value->enroll_id) ?>"><?php echo $value->fullname ?></a></li>
									<li class="visible-print-block"><?php echo $value->fullname ?></li>
								<?php } ?>
								</ul></td>
							<td class="p-none"><ul class="sibling">
								<?php foreach ($row['student'] as $key => $value) { ?>
									<li><?php echo $value->register_no ?></li>
								<?php } ?>
								</ul></td>
							<td class="p-none"><ul class="sibling">
								<?php foreach ($row['student'] as $key => $value) { ?>
									<li><?php echo $value->class_name . " (" . $value->section_name ?>)</li>
								<?php } ?>
								</ul></td>
							<td class="p-none"><ul class="sibling">
								<?php foreach ($row['student'] as $key => $value) { ?>
									<li><?php echo translate($value->gender) ?></li>
								<?php } ?>
								</ul></td>
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