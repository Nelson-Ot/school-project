<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-list-ul"></i> <?=$exam->title ." - ". translate('question') ." ". translate('list')?></h4>
		<div class="panel-btn">
			<a href="<?=base_url('onlineexam')?>" class="btn btn-default btn-circle">
				<i class="fas fa-display"></i> <?=translate('exam') . " " . translate('list')?>
			</a>
		</div>
	</header>
	<div class="panel-body">
		<section class="panel pg-fw mt-lg">
		   <div class="panel-body">
		      	<h5 class="chart-title mb-xs"><i class="fas fa-display"></i> <?=translate('online_exam') ." ". translate('details')?></h5>
				<div class="table-responsive mt-lg">
					<table class="table table-bordered table-striped table-condensed mb-none">
						<tbody>
							<tr>
								<th><?=translate('start_time')?></th>
								<td><?php echo _d($exam->exam_start) . "<br>" . date("h:i A", strtotime($exam->exam_start)); ?></td>
								<th><?=translate('end_time')?></th>
								<td><?php echo _d($exam->exam_end) . "<br>" . date("h:i A", strtotime($exam->exam_end)); ?></td>
							</tr>
							<tr>
								<th><?=translate('class')?></th>
								<td><?php echo $exam->class_name; ?> (<?php echo $this->onlineexam_model->getSectionDetails($exam->section_id); ?>)</td>
								<th><?=translate('subject')?></th>
								<td><?php echo str_replace('<br>', ' ', $this->onlineexam_model->getSubjectDetails($exam->subject_id)); ?></td>
							</tr>
							<tr>
								<th><?=translate('total') . " " . translate('question')?></th>
								<td><?php echo $exam->questions_qty; ?></td>
								<th><?=translate('duration')?></th>
								<td><?php echo $exam->duration; ?></td>
							</tr>
							<tr>
								<th><?=translate('exam') . " " . translate('total_attempt')?></th>
								<td colspan="3"><?php echo $exam->limits_participation; ?></td>
							</tr>
							<tr>
								<th><?=translate('passing_mark')  ?> </th>
								<td><?php echo $exam->passing_mark . ($exam->mark_type == 1 ? ' (%)' : ''); ?></td>
								<th><?=translate('negative_mark')?></th>
								<td><?php echo ($exam->neg_mark == 1) ? translate('yes') : translate('no'); ?></td>
							</tr>
							<?php if ($exam->exam_type == 1) { ?>
							<tr>
								<th><?=translate('exam') . " " . translate('fees')  ?> </th>
								<td colspan="3"><?php echo currencyFormat($exam->fee); ?></td>
							</tr>
							<?php } ?>
						</tbody>
					</table>
					<div class="mt-lg">
						<span class="text-weight-bold"><?=translate('instruction')?> :</span>
						<p><?php echo $exam->instruction; ?></p>
					</div>
				</div>
		   </div>
		</section>
		<section class="panel pg-fw">
		   <div class="panel-body">
		      <h5 class="chart-title mb-xs"><i class="fas fa-circle-question"></i> <?=translate('assign') ." ". translate('question')?></h5>
				<div class="table-responsive mt-lg">
					<table class="table table-bordered table-condensed table-hover">
						<thead>
							<tr>
								<th><?=translate('sl')?></th>
								<th><?=translate('question')?></th>
								<th><?=translate('group')?></th>
								<th><?=translate('class')?></th>
								<th><?=translate('subject')?></th>
								<th><?=translate('type')?></th>
								<th><?=translate('level')?></th>
								<th><?=translate('mark')?></th>
							<?php if ($exam->neg_mark == 1) { ?>
								<th><?=translate('negative_mark')?></th>
							<?php } ?>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php 
						        $questionType = $this->onlineexam_model->question_type();
						        $arrayLevel = $this->onlineexam_model->question_level();
								$this->db->select('class.name as class_name,subject.name as subject_name,section.name as section_name,questions.question,questions.type,questions.level,questions_manage.id,questions_manage.marks,questions_manage.neg_marks,question_group.name as group_name');
								$this->db->from('questions_manage');
								$this->db->join('questions', 'questions.id = questions_manage.question_id', 'left');
								$this->db->join('question_group', 'question_group.id = questions.group_id', 'left');
								$this->db->join('class', 'class.id = questions.class_id', 'left');
								$this->db->join('section', 'section.id = questions.section_id', 'left');
								$this->db->join('subject', 'subject.id = questions.subject_id', 'left');
								$this->db->where('questions_manage.onlineexam_id', $exam->id);
								$this->db->order_by('questions_manage.id', 'asc');
								$result = $this->db->get()->result();
								$count = 1;
								if (count($result) > 0) { 
									foreach ($result as $key => $value) {
										?>
							<tr>
								<td><?php echo $count++;?></td>
								<td><?php echo $value->question;?></td>
								<td><?php echo $value->group_name;?></td>
								<td><?php echo $value->class_name . " (" . $value->section_name . ")";?></td>
								<td><?php echo $value->subject_name; ?></td>
								<td><?php echo $questionType[$value->type];?></td>
								<td><?php echo $arrayLevel[$value->level];?></td>
								<td><?php echo $value->marks;?></td>
							<?php if ($exam->neg_mark == 1) { ?>
								<td><?php echo $value->neg_marks;?></td>
							<?php } ?>
								<td><?php echo btn_delete('onlineexam/remove_question/' . $value->id);?></td>
							</tr>
						<?php } } else { 
							echo '<tr><td colspan="10"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
						} ?>
						</tbody>
					</table>
				</div>
		    </div>
		</section>
	</div>
</section>