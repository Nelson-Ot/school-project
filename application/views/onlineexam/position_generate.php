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
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getExamByClass(this.value)'
								required data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('exam')?> <span class="required">*</span></label>
							<?php
								$arrayExam = $this->onlineexam_model->getSelectExamList(set_value('class_id'));
								echo form_dropdown("exam_id", $arrayExam, set_value('exam_id'), "class='form-control' id='examID' required  
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
		<?php if(isset($result) && !empty($exam)): ?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<?php echo form_open('onlineexam/save_position', array('class' => 'frm-submit-msg'));
				$data = array('exam_id' => $exam->id);
				echo form_hidden($data);
			?>
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-users-viewfinder"></i> <?php echo translate('student_list');?></h4>
			</header>
			<div class="panel-body mb-md">
			<?php if($exam->position_generated == 1) { ?>
				<div class="alert alert-danger text-center">The position has already been generated.</div>
			<?php } ?>
				<div class="table-responsive mt-md mb-sm">
					<table class="table table-bordered table-condensed table-hover mb-none">
						<thead>
							<tr>
								<th class="no-sort"><?=translate('sl')?></th>
								<th><?=translate('student') . " " . translate('name')?></th>
								<th><?=translate('class')?></th>
								<th><?=translate('subject')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('status')?></th>
								<th><?=translate('mark')?></th>
								<th><?=translate('score')?></th>
								<th><?=translate('position')?></th>
								<th><?=translate('remark')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							$totalamount = 0;
							if (!empty($result)) {
								foreach($result as $key => $row): 
									?>
							<tr>
								<input type="hidden" name="remark[<?php echo $key ?>][student_id]" value="<?php echo $row['student_id'] ?>">
								<input type="hidden" name="" value="">
								<td><?php echo $count;?></td>
								<td><?php echo $row['first_name'] . " " . $row['last_name'];?></td>
								<td><?php echo $row['class_name'] . " (" . $this->onlineexam_model->getSectionDetails($row['section_id']) . ")";?></td>
								<td><?php echo $this->onlineexam_model->getSubjectDetails($row['subject_id']);?></td>
								<td><?php echo $row['register_no'];?></td>
								<td><?php echo $row['result'] == 1 ? "<span class='label label-success-custom'>" . translate('passed') . "</span>" : "<span class='label label-danger-custom'>" . translate('failed') . "</span>" ;?></td>
								<td><?php echo $row['mark'];?> / <?php echo $row['totalmark'];?></td>
								<td><?php echo $row['score'];?>%</td>
								<td class="min-w-sm">
									<div class="form-group">
										<input class="form-control" type="text" autocomplete="off" name="remark[<?php echo $key ?>][position]" value="<?php echo (empty($row['position']) ? $count : $row['position']); $count++; ?>">
										<span class="error"></span>
									</div>
								</td>
								<td class="min-w-sm"><input style="width: 100%" class="form-control" type="text" autocomplete="off" name="remark[<?php echo $key ?>][remark]" value="<?php echo $row['remark'] ?>"></td>
								<td class="action">
									<a href="javascript:void(0);" onclick="getAdminStudentResult('<?php echo $row['id'] ?>','<?php echo $row['student_id'] ?>')" class="btn btn-circle btn-default">
										<i class="fas fa-users-viewfinder"></i> <?php echo translate('view') . " " . translate('result') ?>
									</a>
								</td>
							</tr>
							<?php endforeach; } else {
								echo '<tr><td colspan="11"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>'; 
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?=translate('save')?>
						</button>
					</div>
				</div>
			</div>
		</section>
		<?php endif;?>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide payroll-t-modal" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-users-between-lines"></i> <?php echo translate('exam_result'); ?></h4>
		</header>
		<div class="panel-body">
			<div id="quick_view"></div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
				</div>
			</div>
		</footer>
	</section>
</div>