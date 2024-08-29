<?php $marksheet_template = $this->marksheet_template_model->getTemplate($templateID, $branchID); ?>
<style type="text/css">
	.mark-container {
	    padding: <?=$marksheet_template['top_space'] . 'px ' . $marksheet_template['right_space'] . 'px ' . $marksheet_template['bottom_space'] . 'px ' . $marksheet_template['left_space'] . 'px'?>;
	}

	.background {
		width: 100%;
		height: 100%;
	<?php if (empty($marksheet_template['background'])) { ?>
		background: #fff;
	<?php } else { ?>
		background-image: url("<?=base_url('uploads/marksheet/' . $marksheet_template['background'])?>") !important;
		background-repeat: no-repeat !important;
		background-size: 100% 100% !important;
	<?php } ?>
	}
</style>
<?php
$extINTL = extension_loaded('intl');
if (!empty($student_array)) {
	foreach ($student_array as $sc => $studentID) {
		$result = $this->exam_progress_model->getStudentReportCard($studentID, $sessionID, $class_id, $section_id);
		$student = $result['student'];
		$schoolYear = get_type_name_by_id('schoolyear', $sessionID, 'school_year');
		
		$extendsData = [];
		$extendsData['print_date'] = $print_date;
		$extendsData['schoolYear'] = $schoolYear;
		$extendsData['teacher_comments'] = $remarks_array[$studentID];
		$header_content = $this->marksheet_template_model->tagsReplace($student, $marksheet_template, $extendsData, 'header_content');
		$footer_content = $this->marksheet_template_model->tagsReplace($student, $marksheet_template, $extendsData, 'footer_content');
		?>
<div style="position: relative; width: 100%; height: 100%;"> 
	<div class="mark-container background">
		<?php echo $header_content; ?>
		<table class="table table-condensed table-bordered mt-lg">
			<thead>
				<tr>
					<th>Subject</th>
				<?php foreach ($examArray as $id) { ?>
					<th style="white-space: normal;"><?php echo get_type_name_by_id('exam',$id)  ?></th>
				<?php } ?>
<?php if ($marksheet_template['cumulative_average'] == 1) { ?>
					<th style="white-space: normal;">Cumulative Average</th>
<?php } ?>
					<th>Grade</th>
<?php if ($marksheet_template['remark'] == 1) { ?>
					<th>Remark</th>
<?php } if ($marksheet_template['class_average'] == 1) { ?>				
					<th>Class Average</th>
<?php } if ($marksheet_template['subject_position'] == 1) { ?>
					<th>Subject Position</th>
<?php } ?>
				</tr>
			</thead>
			<tbody>
			<?php
			$colspan = count($examArray) + 5;
			$total_grade_point = 0;
			$grand_obtain_marks = 0;
			$grand_full_marks = 0;
			$result_status = 1;
			$getSubjectsList = $this->subject_model->getSubjectByClassSection($student['class_id'], $student['section_id']);
			$getSubjectsList = $getSubjectsList->result_array();
			foreach ($getSubjectsList as $row) {
				$subTotalObtain = 0;
				$subTotalFull = 0;
				?>
				<tr>
					<td valign="middle"><?=$row['subjectname']?></td>
					<?php foreach ($examArray as $id) { ?>
					<td valign="middle"><?php 
					$getExamTotalMark = $this->exam_progress_model->getExamTotalMark($studentID, $sessionID, $row['subject_id'], $id, $student['class_id'], $student['section_id']);
					$subTotalObtain += $getExamTotalMark['grand_obtain_marks'];
					$subTotalFull += $getExamTotalMark['grand_full_marks'];
					echo $getExamTotalMark['grand_obtain_marks'] ." / ". $getExamTotalMark['grand_full_marks'];
					?></td>
					<?php } ?>
					<?php 
					if (empty($subTotalObtain)) {
						$cumulative_Average = 0;
					} else {
						$grand_obtain_marks += $subTotalObtain;
						$grand_full_marks += $subTotalFull;
						$cumulative_Average = (($subTotalObtain * 100) / $subTotalFull);
					}
if ($marksheet_template['cumulative_average'] == 1) { ?>
					<td valign="middle"><?php 
					echo number_format($cumulative_Average, 1, '.', '') . "%";
				?></td>
<?php } ?>
					<td valign="middle"><?php $grade = $this->exam_progress_model->get_grade($cumulative_Average, 1); $total_grade_point += $grade['grade_point']; echo $grade['name'];  ?></td>
<?php if ($marksheet_template['remark'] == 1) { ?>
					<td valign="middle"><?php echo $grade['remark']; ?></td>
<?php } if ($marksheet_template['class_average'] == 1) { ?>
					<td valign="middle"><?php echo $this->exam_progress_model->getClassAverage($examArray, $sessionID, $row['subject_id']); ?></td>
<?php } if ($marksheet_template['subject_position'] == 1) {?>
					<td valign="middle"><?php echo $this->exam_progress_model->getSubjectPosition($student['class_id'], $student['section_id'],  $examArray, $sessionID, $row['subject_id'], $subTotalObtain); ?></td>
<?php } ?>
				</tr>
			<?php } ?>
				<tr>
					<th valign="top">GRAND TOTAL :</th>
					<td valign="top" colspan="<?=$colspan?>"><?=$grand_obtain_marks . '/' . $grand_full_marks; ?>, Average : <?php $percentage = ($grand_obtain_marks * 100) / $grand_full_marks; echo number_format($percentage, 2, '.', '')?>%</td>
				</tr>
			<?php if ($extINTL == true) { ?>
				<tr>
					<th valign="top">GRAND TOTAL IN WORDS :</th>
					<td valign="top" colspan="<?=$colspan?>">
						<?php
						$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
						echo ucwords($f->format($grand_obtain_marks));
						?>
					</td>
				</tr>
			<?php } ?>
				<tr>
					<th valign="top">GPA :</th>
					<td valign="top" colspan="<?=$colspan?>"><?=number_format(($total_grade_point / count($getSubjectsList)), 2, '.', '')?>%</td>
				</tr>
			</tbody>
		</table>
		<div style="width: 100%; display: flex;">
			<div style="width: 48%;  float: left;">
<?php
if ($marksheet_template['attendance_percentage'] == 1) {
					$year = explode('-', $schoolYear);
					$getTotalWorking = $this->db->where(array('enroll_id' => $student['enrollID'], 'year(date)' => $year[0]))->get('student_attendance')->num_rows();
					$getTotalAttendance = $this->db->where(array('enroll_id' => $student['enrollID'], 'status' => 'P', 'year(date)' => $year[0]))->get('student_attendance')->num_rows();
					$attenPercentage = empty($getTotalWorking) ? '0.00' : ($getTotalAttendance * 100) / $getTotalWorking;
					?>
				<table class="table table-bordered table-condensed">
					<tbody>
						<tr>
							<th colspan="2" class="text-center">Attendance</th>
						</tr>
						<tr>
							<th style="width: 65%;">No. of working days</th>
							<td><?=$getTotalWorking?></td>
						</tr>
						<tr>
							<th style="width: 65%;">No. of days attended</th>
							<td><?=$getTotalAttendance?></td>
						</tr>
						<tr>
							<th style="width: 65%;">Attendance Percentage</th>
							<td><?=number_format($attenPercentage, 2, '.', '') ?>%</td>
						</tr>
					</tbody>
				</table>
				<?php } ?>
			</div>
	<?php if ($marksheet_template['grading_scale'] == 1) { ?>
			<div style="width: 48%; float: right;">
				<table class="table table-condensed table-bordered">
					<tbody>
						<tr>
							<th colspan="3" class="text-center">Grading Scale</th>
						</tr>
						<tr>
							<th>Grade</th>
							<th>Min Percentage</th>
							<th>Max Percentage</th>
						</tr>
					<?php 
					$grade = $this->db->where('branch_id', $branchID)->get('grade')->result_array();
					foreach ($grade as $key => $row) {
					?>
						<tr>
							<td style="width: 30%;"><?=$row['name']?></td>
							<td style="width: 30%;"><?=$row['lower_mark']?>%</td>
							<td style="width: 30%;"><?=$row['upper_mark']?>%</td>
						</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
	<?php } ?>
		</div>
		<?php echo $footer_content; ?>
	</div>
</div>
<?php } } ?>
