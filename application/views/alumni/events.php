<?php 
$arraySession = array("" => translate('select'));
$years = $this->db->get('schoolyear')->result();
foreach ($years as $year){
	$arraySession[$year->id] = $year->school_year;
}
?>
<div class="row">
	<div class="col-md-6">
		<section class="panel pg-fw">
		    <div class="panel-body">
		        <h5 class="chart-title mb-xs"><i class="fa-solid fa-stopwatch"></i> <?php echo translate('events') . " " .  translate('list')?></h5>
		        <div class="panel-btn mr-sm mt-xs">
					<button onclick="addAlumni('', this)" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn-default btn-circle"> <i class="fas fa-plus-circle"></i> <?php echo translate('add') . " " .  translate('events')?></button>
				</div>
		        <div class="mt-lg">
					<table class="table table-bordered table-hover mb-none table_default">
						<thead>
							<tr>
								<th>#</th>
								<th><?=translate('title')?></th>
								<th><?=translate('photo')?></th>
								<th><?=translate('date')?></th>
								<th><?=translate('audience')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							if (!is_superadmin_loggedin()) {
								$this->db->where('branch_id', get_loggedin_branch_id());
							}
							$this->db->order_by('id', 'asc');
							$events = $this->db->get('alumni_events')->result();
							foreach ($events as $event):
							?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $event->title; ?></td>
								<td class="center"><img src="<?php echo get_image_url('alumni_events', $event->photo); ?>" height="60" /></td>
								<td>
									<strong><?php echo translate('date_of_start') ?> :</strong> <?php echo _d($event->from_date);?> </br>
									<strong><?php echo translate('date_of_end') ?> :</strong> <?php echo _d($event->to_date);?> </br>
								</td>
								<td><?php
										$auditions = array(
											"1" => translate("everybody"),
											"2" => translate("class"),
											"3" => translate("section"),
										);
										$audition = $auditions[$event->audience];
										echo "<strong>" . translate($audition) . "</strong>";
										if($event->audience != 1){
											if ($event->audience == 2) {
												$selecteds = json_decode($event->selected_list); 
												foreach ($selecteds as $selected) {
													echo "<br> - " . get_type_name_by_id('class', $selected) ;
												}
											} 
											if ($event->audience == 3) {
												$selecteds = json_decode($event->selected_list); 
												foreach ($selecteds as $selected) {
													$selected = explode('-', $selected);
													echo "<br> - " . get_type_name_by_id('class', $selected[0]) . " (" . get_type_name_by_id('section', $selected[1])  . ')' ;
												}
											}
										}
									?>
									<?php if (!empty($event->session_id)) { ?>
									</br><strong><?php echo translate('passing_session') ?> :</strong> <?php echo $arraySession[$event->session_id];?>
									<?php } ?>
								</td>
								<td class="action">
									<!-- view modal link -->
									<button onclick="viewEventDetails('<?=$event->id?>',this)" data-loading-text="<i class='fas fa-spinner fa-spin'></i>" class="btn btn-circle btn-default icon"> <i class="far fa-eye"></i></button>
								<?php if (get_permission('alumni_events', 'is_edit')) { ?>
									<!-- edit link -->
									<button onclick="addAlumni('<?php echo $event->id ?>', this)" data-loading-text="<i class='fas fa-spinner fa-spin'></i>" class="btn btn-circle btn-default icon"> <i class="fas fa-pen-nib"></i></button>
								<?php } ?>
								<?php if (get_permission('alumni_events', 'is_delete')) { ?>
									<!-- deletion link -->
									<?php echo btn_delete('alumni/event_delete/'.$event->id);?>
								<?php } ?>
								</td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
		        </div>
		    </div>
		</section>
	</div>
	<div class="col-md-6">
		<section class="panel">
			<div class="panel-body">
				<div id="event_calendar"></div>
			</div>
		</section>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-lg modal-block-primary mfp-hide" id="alumniEventModal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="fa-solid fa-person-chalkboard"></i> <?=translate('alumni') . " " . translate('event')?>
			</h4>
		</header>
		<?php echo form_open_multipart('alumni/saveEvents', array('class' => 'frm-submit-data'));?>
		<div class="panel-body">
			<input type="hidden" name="id" value="" id="eventID">
			<input type="hidden" name="old_image" value="" id="eventOld_image">
			<input type="hidden" name="selected_list" value="" id="selectedList">
			<div class="row">
<?php if (is_superadmin_loggedin()): ?>
				<div class="col-md-6 mb-sm mt-md">
					<div class="form-group">
						<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' id='eventBranchID'
							data-plugin-selectTwo id='branch_id'");
						?>
						<span class="error"></span>
					</div>
				</div>
<?php endif; ?>
				<div class="col-md-<?php echo is_superadmin_loggedin() ? 6 : 12; ?> mb-sm mt-md">
					<div class="form-group">
						<label class="control-label"><?php echo translate('events') . " " . translate('title'); ?> <span class="required">*</span></label>
						<input type="text" class="form-control" value="" name="event_title" id="eventTitle" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>
			</div>
			<div class="form-group" id='audienceDiv'>
				<label class="control-label"><?=translate('audience')?> <span class="required">*</span></label>
				<?php
					$arrayaudience = array(
						"" => translate('select'),
						"1" => translate('everybody'),
						"2" => translate('selected_class'),
						"3" => translate('selected_section'),
					);
					echo form_dropdown("audience", $arrayaudience, set_value('audience'), "class='form-control' id='eventAudience'
					data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
				?>
				<span class="error"></span>
			</div>

			<div class="row" id="selected_user" style="display: none;">
				<div class="col-md-6 mb-sm">
					<div class="form-group">
						<label class="control-label" id="selected_label"> <?=translate('audience')?> <span class="required">*</span> </label>
						<?php
							$placeholder = '{"placeholder": "' . translate('select') . '"}';
							echo form_dropdown("selected_audience[]", [], set_value('selected_audience'), "class='form-control' data-plugin-selectTwo multiple
							data-plugin-options='$placeholder' data-plugin-selectTwo data-width='100%' id='selected_audience' ");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="col-md-6 mb-sm">
					<div class="form-group">
						<label class="control-label"> <?=translate('passing_session')?> <span class="required">*</span> </label>
						<?php echo form_dropdown("passing_session", $arraySession, set_value('passing_session', get_session_id()), "class='form-control' id='sessionID' data-plugin-selectTwo data-width='100%'"); ?>
						<span class="error"></span>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-md-6 mb-sm">
					<div class="form-group">
						<label class="control-label"><?php echo translate('date_of_start'); ?></label>
						<div class="input-group">
							<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
							<input type="text" class="form-control" name="from_date" id="eventFrom" value="<?=set_value('from')?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
						</div>
						<span class="error"></span>
					</div>
				</div>
				<div class="col-md-6 mb-sm">
					<div class="form-group">
						<label class="control-label"><?php echo translate('date_of_end'); ?></label>
						<div class="input-group">
							<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
							<input type="text" class="form-control" name="to_date" id="eventTo" value="<?=set_value('to_date')?>" data-plugin-datepicker />
						</div>
						<span class="error"></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="control-label"><?php echo translate('note'); ?></label>
				<textarea name="note" rows="2" class="form-control" id="note" aria-required="true"></textarea>
				<span class="error"></span>
			</div>
			<div class="form-group">
				<label class="control-label"><?=translate('photo')?></label>
				<div class="fileupload fileupload-new" data-provides="fileupload">
					<div class="input-append">
						<div class="uneditable-input">
							<i class="fas fa-file fileupload-exists"></i>
							<span class="fileupload-preview"></span>
						</div>
						<span class="btn btn-default btn-file">
							<span class="fileupload-exists">Change</span>
							<span class="fileupload-new">Select file</span>
							<input type="file" name="user_photo" />
						</span>
						<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
					</div>
				</div>
				<span class="error"></span>
			</div>
			<div class="checkbox-replace mt-lg mb-md">
				<label class="i-checks">
					<input type="checkbox" name="send_sms"> <i></i> <?php echo translate('send_confirmation_sms') ?>
				</label>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
						<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
					</button>
					<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="modalEventDetails">
	<section class="panel">
		<header class="panel-heading">
			<div class="panel-btn">
				<button onclick="fn_printElem('printResult')" class="btn btn-default btn-circle icon" ><i class="fas fa-print"></i></button>
			</div>
			<h4 class="panel-title"><i class="fas fa-info-circle"></i> <?=translate('event_details')?></h4>
		</header>
		<div class="panel-body">
			<div id="printResult" class="pt-sm pb-sm">
				<div class="table-responsive">						
					<table class="table table-bordered table-condensed text-dark tbr-top" id="ev_table"></table>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss">
						<?=translate('close')?>
					</button>
				</div>
			</div>
		</footer>
	</section>
</div>

<script type="text/javascript">
	var selectedList = "";
	(function($) {
		$('#event_calendar').fullCalendar({
			header: {
			left: 'prev,next,today',
			center: 'title',
				right: 'month,agendaWeek,agendaDay,listWeek'
			},
			firstDay: 1,
			droppable: false,
			editable: true,
			timezone: 'UTC',
			lang: '<?php echo $language ?>',
			events: {
				url: "<?=base_url('alumni/getEventsList/')?>"
			},
			eventRender: function(event, element) {
				$(element).on("click", function() {
					viewEventDetails(event.id);
				});
			}
		});
	})(jQuery);

	function viewEventDetails(id) {
		$.ajax({
			url: base_url + "alumni/getEventDetails",
			type: 'POST',
			data: {
				event_id: id
			},
			success: function (data) {
				$('#ev_table').html(data);
				mfp_modal('#modalEventDetails');
			}
		});
	}

	function addAlumni(id, elem) {
	    var btn = $(elem);
	    $('.error').html("");
	    $("#selected_user").hide();
	    $.ajax({
	        url: base_url + 'alumni/eventDetails',
	        type: 'POST',
	        data: {'id': id},
	        dataType: "json",
	        beforeSend: function () {
	            btn.button('loading');
	        },
	        success: function (data) {
	        	$('#eventID').val(data.id);
	        	$("#eventOld_image").val(data.photo);
	        	$('#eventBranchID').val(data.branch_id).trigger('change');
	        	$('#sessionID').val(data.session_id).trigger('change');
	        	$('#eventAudience').val(data.audience).trigger('change');
	        	$('#eventTitle').val(data.title);
	        	$('#eventFrom').val(data.from_date);
	        	$('#eventTo').val(data.to_date);
	        	$('#note').val(data.note);
	        	selectedList = data.selected_list;
	            mfp_modal('#alumniEventModal');
	        },
	        error: function (xhr) {
	            btn.button('reset');
	        },
	        complete: function () {
	            btn.button('reset');
	        }
	    });
	}

	$(document).ready(function () {
		$('#eventAudience').on('change', function() {
			selectedList = "";
			var audience = $(this).val();
			var branchID = ($('#eventBranchID').length ? $('#eventBranchID').val() : "");
			if(audience == "1" || audience == null)
			{
				$("#selected_user").hide("slow");
			}
			if(audience == "2") {
			    $.ajax({
			        url: base_url + 'ajax/getClassByBranch',
			        type: 'POST',
			        data:{
			        	branch_id: branchID
			        },
			        success: function (data){
			            $('#selected_audience').html(data);
			        }
			    });
				$("#selected_user").show('slow');
				$("#selected_label").html("<?=translate('class')?> <span class='required'>*</span>");
			}
			if(audience == "3"){
				$.ajax({
					url: "<?=base_url('event/getSectionByBranch')?>",
					type: 'POST',
					data: {
						branch_id: branchID
					},
					success: function (data) {
						$('#selected_audience').html(data);
					}
				});
				$("#selected_user").show('slow');
				$("#selected_label").html("<?=translate('section')?> <span class='required'>*</span>");
			}

			if (selectedList.length !== "") {
		        setTimeout(function() {
		            var JSONObject = JSON.parse(selectedList);
		            for (var i = 0, l = JSONObject.length; i < l; i++) {
		                $("#selected_audience option[value='" + JSONObject[i] + "']").prop("selected", true);
		            }
		            $('#selected_audience').trigger('change.select2');
		        }, 200);
			}
		});
	});
</script>