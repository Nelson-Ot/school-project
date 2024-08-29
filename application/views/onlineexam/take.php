<style type="text/css">
	.radio-custom p {
		margin: 0;
	}
</style>
<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-list-ul"></i> <?=translate('online_exam') ." ". translate('list')?></h4>
	</header>
	<div class="panel-body">
		<h4 class="text-center mb-lg mt-lg"><span class="text-weight-bold"><?=translate('exam') ." ". translate('name')?> </span> : <?php echo $exam->title; ?></h4>
		<div class="table-responsive mb-md">
			<table class="table table-striped table-condensed mb-none">
				<tbody>
					<tr>
						<th><?=translate('start_time')?></th>
						<td><?php echo _d($exam->exam_start) . "<p class='text-muted'>" . date("h:i A", strtotime($exam->exam_start)); ?></p></td>
						<th><?=translate('end_time')?></th>
						<td><?php echo _d($exam->exam_end) . "<p class='text-muted'>" . date("h:i A", strtotime($exam->exam_end)); ?></p></td>
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
						<td><?php echo $exam->limits_participation; ?></td>
						<th><?=translate('your') . " " . translate('total_attempt')?></th>
						<td><?php echo $this->onlineexam_model->getStudentAttempt($exam->id); ?></td>
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
						<td><?php echo currencyFormat($exam->fee); ?></td>
						<th><?=translate('payment_informations')?></th>
						<td width="270"><?php echo $this->db->select('transaction_id')->where(array('student_id' => get_loggedin_user_id(), 'exam_id' => $exam->id))->get('online_exam_payment')->row()->transaction_id; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<span class="text-weight-bold"><?=translate('instruction')?> :</span>
		<p><?php echo $exam->instruction; ?></p>
<?php 
$startTime = strtotime($exam->exam_start);
$endTime = strtotime($exam->exam_end);
$now =  strtotime("now");
if (empty($studentSubmitted)) {
	if (($startTime <= $now && $now <= $endTime) && $exam->publish_status == 1) {
	?>
		<div class="text-center">
			<button class="btn btn-default btn-lg mt-lg start_btn" data-examid="<?php echo $exam->id; ?>" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-computer-mouse"></i> <?=translate('start_exam')?></button>
		</div>
	<?php 
	}
} elseif($exam->publish_result == 1) {
	echo '<div class="alert alert-subl mt-lg text-center">Exam Results Published.</div>';
} else { 
	echo '<div class="alert alert-subl mt-lg text-center">You have already submitted.</div>'; 	
} ?>
	</div>
</section>
<div class="questionmodal">
      <div id="ans_modalBox" class="modal fade" role="dialog">
         <div class="modal-dialog modal-dialogfullwidth">
            <!-- Modal content-->
            <div class="modal-content modal-contentfull">
               <div class="modal-header">
                  <button type="button" class="close questionclose" data-dismiss="modal">&times;</button>
                  <h4 class="modal-title"><i class="fas fa-users-between-lines"></i> <?php echo $exam->title ?></h4>
               </div>
               <div class="modal-body">
               	<div id="online_questions"></div>
               </div>
            </div>
         </div>
      </div>
</div>

<script type="text/javascript">
	var examDuration = "<?php echo $exam->duration; ?>";
	var totalQuestions = 0;
	var currentStep = 1;
	$(document).on('click', '.start_btn', function() {
	    elapsed_seconds = 0;
	    var $this = $(this);
	    var examID = $this.attr("data-examid");
	    $.ajax({
	        type: 'POST',
	        url: base_url + "userrole/ajaxQuestions",
	        data: { 'exam_id': examID },
	        dataType: 'JSON',
	        beforeSend: function() {
	            $this.button('loading');
	            clearInterval(interval);
	        },
	        success: function(data) {
		        if (data.status == 1) {
		        	if ($('#online_questions').length) {
		        		totalQuestions = parseInt(data.total_questions);
		            $('#online_questions').html(data.page);
		            $('#fueluxWizard').on('actionclicked.fu.wizard', function(e, data) {
		                var steps = 0;
		                if (data.direction == "next") {
		                    steps = data.step + 1;
		                } else {
		                    steps = data.step - 1;
		                }
		                var btn = $('#question' + steps).addClass('active');
		                $(".que_btn").not(btn).removeClass('active');

		                if (steps == totalQuestions) {
		                    $('#nextbutton i').remove();
		                    $('#nextbutton').append(' <i class="fas fa-check"></i>');
		                    $('#finishedbutton').hide();
		                } else if (steps == totalQuestions + 1) {
		                    $('#answerForm').submit();
		                } else {
		                    $('#nextbutton i').remove();
		                    $('#nextbutton').append(' <i class="fa fa-angle-right"></i>');
		                    $('#finishedbutton').show();
		                }
		                currentStep = steps;
		                makeAnswered(data.step);
		            });
		            timer();
		            $('#ans_modalBox').modal({
		                show: true,
		                backdrop: 'static',
		                keyboard: false
		            });
		        	}
		        } else {
		        		alertMsg(data.message, "error", "<?php echo translate('error') ?>", "");
		        }
	        },
	        error: function(xhr) { // if error occured
	            alert("Error occured.please try again");
	            $this.button('reset');
	        },
	        complete: function() {
	            $this.button('reset');
	        }
	    });
	});

	function changeQuestion(questionID) {
		makeAnswered(currentStep);
		currentStep = questionID;
		makeAnswered(questionID);

		$('#fueluxWizard').wizard('selectedItem', {
			step: questionID
		});

		var btn = $('#question' + questionID).addClass('active');
		$(".que_btn").not(btn).removeClass('active');

		if (questionID == totalQuestions) {
			$('#nextbutton i').remove();
			$('#nextbutton').append(' <i class="fas fa-check"></i>');
			$('#finishedbutton').hide();
		} else {
			$('#nextbutton i').remove();
			$('#nextbutton').append(' <i class="fa fa-angle-right"></i>');
			$('#finishedbutton').show();
		}
	}

	function completeExams() {
	   $('#answerForm').submit();
	}

	// remain duration update
	var interval;
	var timer = function() {
		interval = setInterval(function() {
			$('.remain_duration').text(durationUpdate());
		}, 1000);
	};
</script>