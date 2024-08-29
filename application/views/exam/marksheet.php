<?php $widget = (is_superadmin_loggedin() ? 2 : 3); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<?php echo form_open('exam/marksheet', array('class' => 'validate')); ?>
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"><?php echo form_error('branch_id'); ?></span>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?=$widget?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('academic_year')?> <span class="required">*</span></label>
							<?php
								$arrayYear = array("" => translate('select'));
								$years = $this->db->get('schoolyear')->result();
								foreach ($years as $year){
									$arrayYear[$year->id] = $year->school_year;
								}
								echo form_dropdown("session_id", $arrayYear, set_value('session_id', get_session_id()), "class='form-control'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"><?php echo form_error('session_id'); ?></span>
						</div>
					</div>
					<div class="col-md-<?=$widget?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('exam')?> <span class="required">*</span></label>
							<?php
								
								if(!empty($branch_id)){
									$arrayExam = array("" => translate('select'));
									$exams = $this->db->get_where('exam', array('branch_id' => $branch_id,'session_id' => get_session_id()))->result();
									foreach ($exams as $exam){
										$arrayExam[$exam->id] = $this->application_model->exam_name_by_id($exam->id);
									}
								} else {
									$arrayExam = array("" => translate('select_branch_first'));
								}
								echo form_dropdown("exam_id", $arrayExam, set_value('exam_id'), "class='form-control' id='exam_id'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"><?php echo form_error('exam_id'); ?></span>
						</div>
					</div>

					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value,0)'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"><?php echo form_error('class_id'); ?></span>
						</div>
					</div>
					<div class="col-md-<?=$widget?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'), false);
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"><?php echo form_error('section_id'); ?></span>
						</div>
					</div>
					<div class="col-md-3">
						<div class="form-group mt-xs">
							<label class="control-label"><?=translate('marksheet') . " " . translate('template'); ?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSelectByBranch('marksheet_template', $branch_id);
								echo form_dropdown("template_id", $arraySection, set_value('template_id'), "class='form-control' id='templateID'
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"><?php echo form_error('template_id'); ?></span>
						</div>
					</div>
				</div>
			</div>
			<div class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="submit" value="search" class="btn btn-default btn-block"><i class="fas fa-filter"></i> <?=translate('filter')?></button>
					</div>
				</div>
			</div>
			<?php echo form_close();?>
		</section>

		<?php if (isset($student)): ?>
			<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']?>" data-appear-animation-delay="100">
				<?php echo form_open('exam/reportCardPdf', array('class' => 'printIn')); ?>
				<input type="hidden" name="exam_id" value="<?=set_value('exam_id')?>">
				<input type="hidden" name="class_id" value="<?=set_value('class_id')?>">
				<input type="hidden" name="section_id" value="<?=set_value('section_id')?>">
				<input type="hidden" name="session_id" value="<?=set_value('session_id')?>">
				<input type="hidden" name="branch_id" value="<?=$branch_id?>">
				<input type="hidden" name="template_id" value="<?=set_value('template_id')?>">
				<header class="panel-heading">
					<h4 class="panel-title">
						<i class="fas fa-users"></i> <?=translate('student_list')?>
					</h4>
					<div class="panel-btn">
						<button type="submit" class="btn btn-default btn-circle" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" >
							<i class="fa-solid fa-file-pdf"></i> <?=translate('download')?> PDF
						</button>
						<button type="button" id="printBtn" class="btn btn-default btn-circle" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-print"></i> <?=translate('print')?>
						</button>
					</div>
				</header>
				<div class="panel-body">
					<div class="row mb-lg">
						<div class="col-md-3">
							<div class="form-group mt-xs">
								<label class="control-label"><?=translate('print_date')?></label>
								<input type="text" name="print_date" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' class="form-control" autocomplete="off" value="<?=date('Y-m-d')?>">
							</div>
						</div>
					</div>
					<table class="table table-bordered table-hover table-condensed mb-none mt-sm mb-md" id="marksheet">
						<thead>
							<tr>
								<th><?=translate('sl')?></th>
								<th> 
									<div class="checkbox-replace">
										<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
											<input type="checkbox" name="select-all" id="selectAllchkbox"> <i></i>
										</label>
									</div>
								</th>
								<th><?=translate('student_name')?></th>
								<th><?=translate('category')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('roll')?></th>
								<th><?=translate('mobile_no')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							if (count($student)){
							foreach ($student as $row):
								?>
							<tr>
								<td><?=$count++?></td>
								<td class="hidden-print checked-area hidden-print" width="30">
									<div class="checkbox-replace">
										<label class="i-checks"><input type="checkbox" name="student_id[]" value="<?=$row['id']?>"><i></i></label>
									</div>
								</td>
								<td><?=$row['first_name'] . " " . $row['last_name']?></td>
								<td><?=$row['category']?></td>
								<td><?=$row['register_no']?></td>
								<td><?=$row['roll']?></td>
								<td><?=$row['mobileno']?></td>
								<td>
									<button type="button" data-loading-text="<i class='fas fa-spinner fa-spin'></i>" data-placement="top" data-toggle="tooltip" data-original-title="<?php echo translate('email') . " " . translate('marksheet') ?>" class="btn btn-default icon btn-circle" onclick="pdf_sendByemail('<?=$row['id']?>', '<?=$row['enrollID']?>', this)"><i class="fa-solid fa-envelope"></i></button>
								</td>
							</tr>
						<?php 
							endforeach; 
						}else{
							echo '<tr><td colspan="8"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
						}
						?>
						</tbody>
					</table>
				</div>
				<?php echo form_close(); ?>
			</section>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
	var exam_id = "<?=set_value('exam_id')?>";
	var class_id = "<?=set_value('class_id')?>";
	var section_id = "<?=set_value('section_id')?>";
	var session_id = "<?=set_value('session_id')?>";
	var branch_id = "<?=$branch_id?>";
	var template_id = "<?=set_value('template_id')?>";

	$(document).ready(function () {
		// DataTable Config
		$('#marksheet').DataTable({
			"dom": '<"row"<"col-sm-6"l><"col-sm-6"f>><"table-responsive"t>p',
			"lengthMenu": [ [10, 25, 50, -1], [10, 25, 50, "All"] ],
			"pageLength": -1,
			"columnDefs": [
				{targets: [1,-1], orderable: false}
			],
		});
	
		$('#branch_id').on("change", function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			getExamByBranch(branchID);

			$.ajax({
		        url: base_url + 'ajax/getDataByBranch',
		        type: 'POST',
		        data: {
		            table: 'marksheet_template',
		            branch_id: branchID
		        },
		        success: function (data) {
					$('#templateID').html(data);
		        }
		    });
		});
	});

    $('form.printIn').on('submit', function(e) {
        e.preventDefault();
        var btn = $(this).find('[type="submit"]');
        var countRow = $(this).find('input[name="student_id[]"]:checked').length;
        if (countRow > 0) {
	        var exam_name = $('#exam_id').find('option:selected').text();
	        var class_name = $('#class_id').find('option:selected').text();
	        var section_name = $('#section_id').find('option:selected').text();
	        var fileName = exam_name + '-' + class_name + ' (' + section_name + ")-Marksheet.pdf";
	        $.ajax({
	            url: $(this).attr('action'),
	            type: "POST",
	            data: $(this).serialize(),
	            cache: false,
				xhr: function () {
                    var xhr = new XMLHttpRequest();
                    xhr.onreadystatechange = function () {
                        if (xhr.readyState == 2) {
                            if (xhr.status == 200) {
                                xhr.responseType = "blob";
                            } else {
                                xhr.responseType = "text";
                            }
                        }
                    };
                    return xhr;
				},
	            beforeSend: function () {
	                btn.button('loading');
	            },
	            success: function (data, jqXHR, response) {
					var blob = new Blob([data], {type: 'application/pdf'});
					var link = document.createElement('a');
					link.href = window.URL.createObjectURL(blob);
					link.download = fileName;
					document.body.appendChild(link);
					link.click();
					document.body.removeChild(link);
					btn.button('reset');
	            },
	            error: function () {
	                btn.button('reset');
	                alert("An error occured, please try again");
	            },
	            complete: function () {
	                btn.button('reset');
	            }
	        });
    	} else {
    		popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
    	}
    });

   $(document).on('click','#printBtn',function(){
		btn = $(this);
		var arrayData = [];
		$('form.printIn input[name="student_id[]"]').each(function() {
			if($(this).is(':checked')) {
				studentID = $(this).val();
	            arrayData.push(studentID);
        	}
		});
        if (arrayData.length === 0) {
            popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
            btn.button('reset');
        } else {
            $.ajax({
                url: "<?php echo base_url('exam/reportCardPrint') ?>",
                type: "POST",
                data: {
                	'exam_id' : exam_id,
                	'class_id' : class_id,
                	'section_id' : section_id,
                	'session_id' : session_id,
                	'branch_id' : branch_id,
                	'template_id' : template_id,
                	'student_id[]' : arrayData,
                },
                dataType: 'html',
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                	fn_printElem(data, true);
                },
                error: function () {
	                btn.button('reset');
	                alert("An error occured, please try again");
                },
	            complete: function () {
	                btn.button('reset');
	            }
            });
        }
    });

   function pdf_sendByemail(studentID = '', enrollID = '', ele) 
   {
   		var btn = $(ele);
		if (studentID !== '') {
	        $.ajax({
	            url: "<?php echo base_url('exam/pdf_sendByemail') ?>",
	            type: "POST",
	            data: {
	            	'exam_id' : exam_id,
	            	'class_id' : class_id,
	            	'section_id' : section_id,
	            	'session_id' : session_id,
	            	'branch_id' : branch_id,
	            	'template_id' : template_id,
	            	'student_id' : studentID,
	            	'enrollID' : enrollID,
	            },
	            dataType: 'JSON',
	            beforeSend: function () {
	                btn.button('loading');
	            },
	            success: function (data) {
	            	popupMsg(data.message, data.status);
	            },
	            error: function () {
	                btn.button('reset');
	                alert("An error occured, please try again");
	            },
	            complete: function () {
	                btn.button('reset');
	            }
	        });
		}
   }
</script>