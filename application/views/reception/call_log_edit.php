
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('reception/call_log'); ?>">
				  <i class="fas fa-list-ul"></i> <?=translate('call_log') ." ". translate('list')?>
				</a>
			</li>
			<li class="active">
				<a href="#add" data-toggle="tab">
				 <i class="far fa-edit"></i> <?=translate('edit') . " ". translate('call_log')?>
				</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="add">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit'));?>
					<input type="hidden" name="id" value="<?php echo $row['id'] ?>">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, $row['branch_id'], "class='form-control' data-width='100%' onchange='getPurposeByBranch(this.value)'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('call_type')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = array(
										'' => translate('select'),
										'1' => translate('outgoing'),
										'2' => translate('incoming'),
									);
									echo form_dropdown("call_type", $arrayBranch, $row['call_type'], "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('calling_purpose')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = array('' => translate('select'));
									if (!empty($row['branch_id'])) {
										$this->db->where('branch_id', $row['branch_id']);
										$purposeArr = $this->db->get('call_purpose')->result();
										foreach ($purposeArr as $key => $value) {
											$arrayBranch[$value->id] = $value->name;
										}
									}
									echo form_dropdown("purpose_id", $arrayBranch, $row['purpose_id'], "class='form-control' data-width='100%' id='calling_purpose'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="name" value="<?php echo $row['name'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('mobile_no')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="phone_number" value="<?php echo $row['number'] ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="date" value="<?=$row['date']?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('time_slot'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="row">
								<div class="col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="start_time" id="time_start" value="<?php echo $row['start_time']; ?>" />
									</div>
									<span class="error"></span>
								</div>
								<div class="col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="end_time" id="time_end" value="<?php echo $row['end_time']; ?>" />
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('follow_up') . " " . translate('date')?></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
								<input type="text" class="form-control" name="follow_up_date" value="<?php echo $row['follow_up']; ?>" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-6 mb-lg">
							<textarea type="text" rows="3" class="form-control" name="note"><?php echo $row['note']; ?></textarea>
							<span class="error"></span>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?=translate('update')?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	function getPurposeByBranch(id) {
	    $.ajax({
	        url: base_url + 'ajax/getDataByBranch',
	        type: 'POST',
	        data: {
	            table: "call_purpose",
	            branch_id: id
	        },
	        success: function (response) {
	            $('#calling_purpose').html(response);
	        }
	    });
	}
</script>