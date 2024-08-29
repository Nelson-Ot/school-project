
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
                <a href="#list" data-toggle="tab">
                    <i class="fas fa-list-ul"></i> <?=translate('call_log') ." ". translate('list')?>
                </a>
			</li>
<?php if (get_permission('call_log', 'is_add')): ?>
			<li>
                <a href="#add" data-toggle="tab">
                   <i class="far fa-edit"></i> <?=translate('add') . " ". translate('call_log')?>
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
							<th><?=translate('name')?></th>
							<th><?=translate('mobile_no')?></th>
							<th><?=translate('calling_purpose')?></th>
							<th><?=translate('call_type')?></th>
							<th><?=translate('date')?></th>
							<th><?=translate('start_time')?></th>
							<th><?=translate('end_Time')?></th>
							<th><?=translate('follow_up')?></th>
							<th><?=translate('duration')?></th>
							<th><?=translate('note')?></th>
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
								<td><?php echo $row['name']; ?></td>
								<td><?php echo $row['number']; ?></td>
								<td><?php echo $row['call_type'] == 1 ? translate('dispatch') : translate('receive'); ?></td>
								<td><?php echo get_type_name_by_id('call_purpose', $row['purpose_id']); ?></td>
								<td><?php echo _d($row['date']); ?></td>
								<td><?php echo date("h:i A", strtotime($row['start_time'])); ?></td>
								<td><?php echo date("h:i A", strtotime($row['end_time'])); ?></td>
								<td><?php echo _d($row['follow_up']); ?></td>
								<td><?php 
								$duration = (strtotime($row['end_time']) - strtotime($row['start_time']));
								$duration = format_duration($duration);
								echo $duration; ?></td>
								<td><?php echo $row['note']; ?></td>
								<td class="action">
									<?php if (get_permission('call_log', 'is_edit')): ?>
										<!-- update link -->
										<a href="<?php echo base_url('reception/call_log_edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon">
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php endif; if (get_permission('call_log', 'is_delete')): ?>
										<!-- delete link -->
										<?php echo btn_delete('reception/call_log_delete/' . $row['id']); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('call_log', 'is_add')): ?>
			<div class="tab-pane" id="add">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit'));?>
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' onchange='getPurposeByBranch(this.value)'
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
									echo form_dropdown("call_type", $arrayBranch, set_value('call_type'), "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('calling_purpose')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectByBranch('call_purpose', $branch_id);
									echo form_dropdown("purpose_id", $arrayBranch, set_value('purpose_id'), "class='form-control' data-width='100%' id='calling_purpose'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="name" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('mobile_no')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="phone_number" value="" />
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
						<label class="col-md-3 control-label"><?php echo translate('time_slot'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="row">
								<div class="col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="start_time" id="time_start" value="<?php echo set_value('time_start'); ?>" />
									</div>
									<span class="error"></span>
								</div>
								<div class="col-xs-6">
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-clock"></i></span>
										<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="end_time" id="time_end" value="<?php echo set_value('time_end'); ?>" />
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
								<input type="text" class="form-control" name="follow_up_date" value="" data-plugin-datepicker
								data-plugin-options='{ "todayHighlight" : true }' />
							</div>
						</div>
						<span class="error"></span>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-6 mb-lg">
							<textarea type="text" rows="3" class="form-control" name="note"></textarea>
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
<?php 
	function format_duration($secs, $delimiter = ':')
	{
		$seconds = $secs % 60;
		$minutes = floor($secs / 60);
		$hours   = floor($secs / 3600);
		$seconds = str_pad($seconds, 2, "0", STR_PAD_LEFT);
		$minutes = str_pad($minutes, 2, "0", STR_PAD_LEFT) . $delimiter;
		$hours   = ($hours > 0) ? str_pad($hours, 2, "0", STR_PAD_LEFT).$delimiter : '00' . $delimiter;
		return "$hours$minutes$seconds";
	}
?>

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