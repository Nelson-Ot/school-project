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

		<?php if(isset($result) && !empty($exam)): 
			$currency_symbol = $global_config['currency_symbol'];
			?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-users-viewfinder"></i> <?php echo translate('student_list');?></h4>
			</header>
			<div class="panel-body mb-md">
				<table class="table table-bordered table-condensed table-hover table-export">
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
						<?php if($exam->exam_type == 1) { ?>
							<th><?=translate('exam') . " " . translate('fees')?></th>
						<?php } ?>
							<th><?=translate('exam') . " " . translate('position')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						$totalamount = 0;
						if (!empty($result)) {
							foreach($result as $row): 
								?>
						<tr>
							<td><?php echo $count++;?></td>
							<td><?php echo $row['first_name'] . " " . $row['last_name'];?></td>
							<td><?php echo $row['class_name'] . " (" . $this->onlineexam_model->getSectionDetails($row['section_id']) . ")";?></td>
							<td><?php echo $this->onlineexam_model->getSubjectDetails($row['subject_id']);?></td>
							<td><?php echo $row['register_no'];?></td>
							<td><?php echo $row['result'] == 1 ? "<span class='label label-success-custom'>" . translate('passed') . "</span>" : "<span class='label label-danger-custom'>" . translate('failed') . "</span>" ;?></td>
							<td><?php echo $row['mark'];?> / <?php echo $row['totalmark'];?></td>
							<td><?php echo $row['score'];?>%</td>
						<?php if($exam->exam_type == 1) { ?>
							<td><?php echo currencyFormat($exam->fee); $totalamount += $exam->fee; ?></td>
						<?php } ?>
							<td><?php echo ($exam->position_generated == 1 ? $row['position'] : "<span class='label label-danger-custom'>" . translate("not_generated") . "</span>");?></td>
							<td class="action">
								<a href="javascript:void(0);" onclick="getAdminStudentResult('<?php echo $row['id'] ?>','<?php echo $row['student_id'] ?>')" class="btn btn-circle btn-default">
									<i class="fas fa-users-viewfinder"></i> <?php echo translate('view') . " " . translate('result') ?>
								</a>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
					<?php if($exam->exam_type == 1) { ?>
					<tfoot>
						<tr>
							<th><?=translate('total') . " " . translate('fees')?></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th></th>
							<th><?php echo currencyFormat($totalamount); ?></th>
							<th></th>
						</tr>
					</tfoot>
					<?php } ?>
				</table>
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


