<?php 
if (!empty($questions)) {
	$totalQuestions = count($questions); 
	?>
<div class="row mt-lg">
	<div class="col-md-5">
		<div class="row">
		   <div class="col-sm-12">
				<section class="panel pg-fw">
				   <div class="panel-body">
				       <h5 class="chart-title mb-xs"><i class="fas fa-clock"></i> <?=translate('time') . " " . translate('status')?></h5>
				       <div class="time_status mt-md">
		               <div class="row">
		                   <div class="col-sm-6">
		                       <h4><?=translate('total') . " " . translate('time')?> :</h4>
		                   </div>
		                   <div class="col-sm-6">
		                       <h4 class="text-dark"><?=$exam->duration?></h4>
		                   </div>
		               </div>
		               <div class="row">
		                   <div class="col-sm-6">
		                       <h4><?=translate('remain_time')?> :</h4>
		                   </div>
		                   <div class="col-sm-6">
		                       <h4 class="remain_duration text-dark"><?=$exam->duration?></h4>
		                   </div>
		               </div>
				       </div>
				   </div>
				</section>
		   </div>
		   <div class="col-sm-12">
				<section class="panel pg-fw">
				   <div class="panel-body">
				       <h5 class="chart-title mb-xs"><i class="fas fa-circle-question"></i> <?=translate('total_questions_map')?></h5>
				       <div class="mt-lg">
							<nav>
								<ul class="on_answer_box questionColor">
								<?php foreach ($questions as $key => $question) {  ?>
									<li><a class="que_btn <?=$key == 0 ? 'active' : '' ?>" id="question<?php echo $key+1 ?>" href="javascript:void(0);" onclick="changeQuestion(<?php echo $key+1 ?>)"><?php echo $key+1 ?></a></li>
								<?php } ?>
								</ul>
							</nav>
				       </div>
				   </div>
				</section>
		   </div>
		</div>
	</div>
	<div class="col-md-7">
		<div class="box wizard" data-initialize="wizard" id="fueluxWizard">
				<div class="steps-container">
					<ul class="steps hidden" style="margin-left: 0;">
		           	<?php foreach (range(1, $totalQuestions) as $value) { ?>
						<li data-step="<?=$value?>" class="<?=$value == 1 ? 'active' : ''?>"></li>
					<?php } ?>
					</ul>
				</div>
				<?php echo form_open('userrole/onlineexam_submit_answer', array('id' => 'answerForm')); ?>
				<input type="hidden" name="online_exam_id" value="<?=$exam->id?>">
				<div class="box-body step-content">
					<?php foreach ($questions as $key => $question) {  ?>
					<div class="clearfix step-pane <?=$key == 0 ? 'active' : '' ?>" data-step="<?=$key+1?>">
						<section class="panel pg-fw">
						   <div class="panel-body">
						       <h5 class="chart-title mb-xs"><i class="fas fa-clipboard-question"></i> <?=translate('question')?> <?=$key+1?> of <?=$totalQuestions?></h5>
						       <div class="mt-lg">
						       	<p><?=$question->question?></p>
	 								<div class="mt-lg mb-sm" id="step<?=$key+1?>">
						       	<?php 
							       	$quesOption = array(
							       		'opt_1' => 1, 
							       		'opt_2' => 2, 
							       		'opt_3' => 3, 
							       		'opt_4' => 4,
							       	);
						       	if ($question->type == 1) {
							       	foreach ($quesOption as $quesOption_key => $quesOption_value) {
							       		if (!empty($question->{$quesOption_key})) {
						       	 ?>
								       <div class="radio-custom radio-success mt-md">
								           <input type="radio" value="<?=$quesOption_value?>" name="answer[<?=$question->question_id?>][<?=$question->type?>]" id="opt<?=$key . $quesOption_value?>">
								           <label for="opt<?=$key . $quesOption_value?>"><?=$question->{$quesOption_key}?></label>
								       </div>
									<?php } } } elseif ($question->type == 2) { 
										foreach ($quesOption as $quesOption_key => $quesOption_value) {
										if (!empty($question->{$quesOption_key})) {
									?>
										<div class="checkbox-replace mt-lg">
											<label class="i-checks"><input type="checkbox" name="answer[<?=$question->question_id?>][<?=$question->type?>][]" value="<?=$quesOption_value?>"><i></i><?=$question->{$quesOption_key}?></label>
										</div>
						          <?php } } } elseif ($question->type == 3) { ?>
								       <div class="radio-custom radio-success mt-md">
								           <input type="radio" value="1" name="answer[<?=$question->question_id?>][<?=$question->type?>]" id="tf1<?=$key?>">
								           <label for="tf1<?=$key?>">TRUE</label>
								       </div>
								       <div class="radio-custom radio-success mt-md">
								           <input type="radio" value="0" name="answer[<?=$question->question_id?>][<?=$question->type?>]" id="tf0<?=$key?>">
								           <label for="tf0<?=$key?>">FALSE</label>
								       </div>
						          <?php } elseif ($question->type == 4) { ?>
	                            <div class="form-group">
	                              <label class="control-label">Answer</label>
	                              <input type="text" class="form-control" rows="4" name="answer[<?=$question->question_id?>][<?=$question->type?>]" ></input>
	                            </div>
						         <?php } ?>
						         <?php if ($exam->marks_display == 1 || $exam->neg_mark == 1) { ?>
						          <div class="ques-marks mt-lg">
						          	<div class="row">
						         <?php if ($exam->marks_display == 1) { ?>
						         	<div class="col-xs-6"><span>Marks : <strong><?=$question->marks?></strong></span></div>
						         <?php } if ($exam->neg_mark == 1) { ?>
						          	<div class="col-xs-6 <?php echo $exam->marks_display == 1 ? 'text-right' : ''; ?>"><span><?=translate('negative_marks')?> : <strong><?=$question->neg_marks?></strong></span></div>
									<?php } ?>
						          </div>
						          </div>
						       	<?php } ?>
									</div>
								</div>
						   </div>
						</section>
					</div>
					<?php } ?>
				<div class="question-answer-button">
					<button class="btn btn-default btn-prev mr-xs mt-sm" type="button" name="" id="prevbutton" disabled="disabled"><i class="fa fa-angle-left"></i> Previous</button>
					<button class="btn btn-default btn-next mr-xs mt-sm" type="button" name="" id="nextbutton" data-last="Complete "><?=translate('next')?> <i class="fa fa-angle-right"></i></button>
					<button class="btn btn-danger mr-xs mt-sm" type="button" name="" onclick="clearAnswer()"><i class="fas fa-xmark"></i> Clear Answer</button>
					<?php if ($exam->questions_qty > 2) { ?>
						<button class="btn btn-default mt-sm" type="button" name="" id="finishedbutton" onclick="completeExams()"><i class="fas fa-check"></i> <?=translate('submit')?></button>
					<?php } ?>
				</div>
			</div>
			</form>
		</div>
	</div>
</div>
<?php } else { 
	echo '<div class="alert alert-subl mt-lg text-center">' . translate('no_questions_have_been_assigned') . ' !</div>';
} ?>
