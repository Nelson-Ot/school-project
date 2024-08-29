<div class="table-responsive mt-md mb-md">
	<table class="table table-striped table-bordered table-condensed mb-none">
		<tbody>
			<tr>
				<th><?=translate('question')?></th>
				<td colspan="3"><?php echo $questions['question'] ?></td>
	
			</tr>
			<tr>
				<th><?=translate('class')?></th>
				<td><?php echo get_type_name_by_id('class', $questions['class_id']) ?> (<?php echo get_type_name_by_id('section', $questions['section_id']) ?>)</td>
				<th><?=translate('subject')?></th>
				<td><?php echo (!empty($questions['subject_id']) ? get_type_name_by_id('subject', $questions['subject_id']) : 'N/A'); ?></td>
			</tr>
			<tr>
				<th><?=translate('type')?></th>
				<td><?php $question_type = $this->onlineexam_model->question_type(); echo $question_type[$questions['type']]; ?></td>
				<th><?=translate('level')?></th>
				<td><?php $arrayLevel = $this->onlineexam_model->question_level(); echo $arrayLevel[$questions['level']]; ?></td>
			</tr>
			<tr>
				<th><?=translate('default_mark')?></th>
				<td colspan="3"><?php echo $questions['mark']; ?></td>
			</tr>

		</tbody>
	</table>
</div>

<h5 class="mt-lg text-weight-bold"><?php echo translate('answer')?> : </h5>
<?php
$optionArr = array (
	'opt_1' => '1',
	'opt_2' => '2',
	'opt_3' => '3',
	'opt_4' => '4',
);
if ($questions['type'] == 1) { 
	$answer = $questions['answer'];
	?>
<div class="table-responsive mt-md mb-md">
	<table class="table table-striped table-bordered table-condensed mb-none">
		<thead>
			<tr>
				<th><?=translate('option')?></th>
				<th><?=translate('correct')?></th>
				<th><?=translate('details')?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($optionArr as $key => $value) {
		if (!empty($questions[$key])) { ?>
			<tr>
				<th><?=translate('option') . " " . $value ?></th>
				<td><?php if ($value == $answer) { ?><i class="far fa-circle-check text-xl"></i><?php } else { ?><i class="fas fa-ellipsis text-xl"></i><?php } ?></td>
				<td><?php echo $questions[$key] ?></td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
</div>
<?php } elseif($questions['type'] == 2) { 
$answer = json_decode($questions['answer'], true );
	?>
<div class="table-responsive mt-md mb-md">
	<table class="table table-striped table-bordered  table-condensed mb-none">
		<thead>
			<tr>
				<th><?=translate('option')?></th>
				<th><?=translate('correct')?></th>
				<th><?=translate('details')?></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach ($optionArr as $key => $value) {
		if (!empty($questions[$key])) { ?>
			<tr>
				<th><?=translate('option') . " " . $value ?></th>
				<td><?php if (in_array($value, $answer)) { ?><i class="far fa-circle-check text-xl"></i><?php } else { ?><i class="fas fa-ellipsis text-xl"></i><?php } ?></td>
				<td><?php echo $questions[$key] ?></td>
			</tr>
		<?php } } ?>
		</tbody>
	</table>
</div>
<?php } elseif($questions['type'] == 3) { 
	echo $questions['answer'] == 1 ? strtoupper(translate("true")) : strtoupper(translate("false"));
} elseif($questions['type'] == 4) {
	echo $questions['answer'];
}
?>