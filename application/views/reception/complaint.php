<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab">
				  <i class="fas fa-list-ul"></i> <?=translate('complaint') ." ". translate('list')?>
				</a>
			</li>
<?php if (get_permission('complaint', 'is_add')): ?>
			<li>
				<a href="#add" data-toggle="tab">
				 <i class="far fa-edit"></i> <?=translate('add') . " ". translate('complaint')?>
				</a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane box active mb-md" id="list">
				<table class="table table-bordered table-hover mb-none table-condensed table-export">
					<thead>
						<tr>
							<th><?=translate('sl')?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?=translate('complaint') . " " . translate('type')?></th>
							<th><?=translate('complainant') . " " . translate('name')?></th>
							<th><?=translate('mobile_no')?></th>
							<th><?=translate('date') ?></th>
							<th><?=translate('date_of_solution') ?></th>
							<th><?=translate('assign_to') ?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					<tbody>
						<?php 
						if (!empty($result)) { 
							$count = 1;
							foreach ($result as $key => $row) {
							?>
							<tr>
								<td><?php echo $count++; ?></td>
<?php if (is_superadmin_loggedin()): ?>
								<td><?php echo $row['branch_name']; ?></td>
<?php endif; ?>		
								<td><?php echo get_type_name_by_id('complaint_type', $row['type_id']) ; ?></td>
								<td><?php echo $row['name']; ?></td>
								<td><?php echo $row['number']; ?></td>
								<td><?php echo _d($row['date']); ?></td>
								<td><?php 
									if (empty($row['date_of_solution']) || $row['date_of_solution'] == "0000-00-00") {
										echo '<span class="label label-danger-custom">' . translate('pending') . '</span>';
									} else {
										echo _d($row['date_of_solution']);
									}?>
								</td>
								<td><?php echo get_type_name_by_id('staff', $row['type_id']); ?></td>
								<td>
								<?php if ($row['assigned_id'] == get_loggedin_user_id()) { ?>
									<a onclick="getComplaintAction('<?php echo $row['id'] ?>')" href="javascript:void(0);" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="Report">
										<i class="fas fa-location-crosshairs"></i>
									</a>
								<?php } ?>
									<button class="btn btn-circle btn-default icon" data-loading-text="<i class='fas fa-spinner fa-spin'></i>" onclick="getPostalRecord('<?php echo $row['id'] ?>', this)"><i class="fas fa-mattress-pillow"></i></button>
									<?php if (get_permission('postal_record', 'is_edit')): ?>
										<!-- update link -->
										<a href="<?php echo base_url('reception/complaint_edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon">
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php endif; if (get_permission('postal_record', 'is_delete')): ?>
										<!-- delete link -->
										<?php echo btn_delete('reception/complaint_delete/' . $row['id']); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('postal_record', 'is_add')): ?>
			<div class="tab-pane" id="add">
					<?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit-data'));?>
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' id='branchID'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('type')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = $arrayBranch = $this->app_lib->getSelectByBranch('complaint_type', $branch_id);
									echo form_dropdown("type_id", $arrayBranch, set_value('type_id'), "class='form-control' data-width='100%' id='typeID'
									data-plugin-selectTwo ");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('assign_to')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getStaffList($branch_id);
									echo form_dropdown("staff_id", $arrayBranch, set_value('staff_id'), "class='form-control' data-width='100%' id='staff_id'
									data-plugin-selectTwo ");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('complainant') . " " . translate('name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="complainant_name" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('complainant') . " " . translate('mobile_no')?></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="phone_number" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-6">
							<textarea type="text" rows="3" class="form-control" name="note"></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="date" value="<?=set_value('date', date('Y-m-d'))?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('document') . " " . translate('file')?></label>
						<div class="col-md-6 mb-lg">
							<div class="fileupload fileupload-new" data-provides="fileupload">
								<div class="input-append">
									<div class="uneditable-input">
										<i class="fas fa-file fileupload-exists"></i>
										<span class="fileupload-preview"></span>
									</div>
									<span class="btn btn-default btn-file">
										<span class="fileupload-exists">Change</span>
										<span class="fileupload-new">Select file</span>
										<input type="file" name="document_file" />
									</span>
									<a href="#" class="btn btn-default fileupload-exists" data-dismiss="fileupload">Remove</a>
								</div>
							</div>
							<span class="error"></span>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?=translate('save')?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php endif; ?>
		</div>
	</div>
</section>

<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-mattress-pillow"></i> <?php echo translate('complaint'); ?></h4>
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

<div class="zoom-anim-dialog modal-block mfp-hide" id="modalAction">
	<section class="panel">
		<?php echo form_open('reception/complaint_action_taken', array('class' => 'frm-submit'));?>
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-location-crosshairs"></i> <?php echo translate('complaint'); ?></h4>
		</header>
		<div class="panel-body">
			<div class="row">
				<input type="hidden" name="complaint_id" id="complaintID" value="">
				<div class="form-group">
					<div class="col-md-12 mt-sm">
					<label class="control-label"><?=translate('date_of_solution')?> <span class="required">*</span></label>
						<div class="input-group">
							<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
							<input type="text" class="form-control" name="date_of_solution" id="dateOfsolution" value="" data-plugin-datepicker
							data-plugin-options='{ "todayHighlight" : true }' />
						</div>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<div class="col-md-12 mb-lg">
					<label class="control-label"><?=translate('action_taken')?> <span class="required">*</span></label>
						<textarea type="text" rows="3" class="form-control" name="action" id="actionTaken"></textarea>
						<span class="error"></span>
					</div>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" class="btn btn-default ml-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
						<i class="fas fa-plus-circle"></i> <?=translate('update')?>
					</button>
					<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close();?>
	</section>
</div>

<script type="text/javascript">
	$('#branchID').on('change', function(){
		var branchID = this.value;
		getPurposeByBranch(branchID);
		$.ajax({
		   url: base_url + "ajax/getStafflistRole",
		   type: 'POST',
		   data: {
				branch_id: branchID,
				role_id: ''
		   },
		   success: function (data) {
		       $('#staff_id').html(data);
		   }
		});
	});

	function getPurposeByBranch(id) {
	    $.ajax({
	        url: base_url + 'ajax/getDataByBranch',
	        type: 'POST',
	        data: {
	            table: "complaint_type",
	            branch_id: id
	        },
	        success: function (response) {
	            $('#typeID').html(response);
	        }
	    });
	}

	function getPostalRecord(id, elem) {
		var btn = $(elem);
		$.ajax({
			url: base_url + 'reception/getComplaintDetails',
			type: 'POST',
			data: {'id': id},
			dataType: "html",
            beforeSend: function () {
                btn.button('loading');
            },
			success: function (data) {
				$('#quick_view').html(data);
				mfp_modal('#modal');
			},
            error: function (xhr) {
                btn.button('reset');
            },
            complete: function () {
                btn.button('reset');
            }
		});
	}

	function getComplaintAction(id) {
		$.ajax({
			url: base_url + 'reception/getComplaintAction',
			type: 'POST',
			data: {'id': id},
			dataType: "json",
			success: function (data) {
				$('#complaintID').val(data.id);
				$('#dateOfsolution').val(data.date_of_solution);
				$('#actionTaken').html(data.action);
				mfp_modal('#modalAction');
			}
		});
	}
</script>