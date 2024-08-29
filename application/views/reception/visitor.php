
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
                <a href="#list" data-toggle="tab">
                    <i class="fas fa-list-ul"></i> <?=translate('visitor') ." ". translate('list')?>
                </a>
			</li>
<?php if (get_permission('visitor_log', 'is_add')): ?>
			<li>
                <a href="#add" data-toggle="tab">
                   <i class="far fa-edit"></i> <?=translate('add') . " ". translate('visitor')?>
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
							<th><?=translate('visiting_purpose')?></th>
							<th><?=translate('date')?></th>
							<th><?=translate('entry_time')?></th>
							<th><?=translate('exit_time')?></th>
							<th><?=translate('number_of_visitor')?></th>
							<th><?=translate('token/pass')?></th>
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
								<td>
									<?=translate('name')?> :  <?php echo $row['name']; ?> <br>
									<?=(empty($row['number']) ? '' : translate('mobile_no') . ' : ' . $row['number']); ?> <br>
									<?=(empty($row['id_number']) ? '' : translate('id_number') . ' : ' . $row['id_number']); ?>
								</td>
								<td><?php echo get_type_name_by_id('visitor_purpose', $row['purpose_id']); ?></td>
								<td><?php echo _d($row['date']); ?></td>
								<td><?php echo date("h:i A", strtotime($row['entry_time'])); ?></td>
								<td><?php echo date("h:i A", strtotime($row['exit_time'])); ?></td>
								<td><?php echo $row['number_of_visitor']; ?></td>
								<td><?php echo $row['token_pass']; ?></td>
								<td><?php echo $row['note']; ?></td>
								<td class="action">
									<?php if (get_permission('visitor_log', 'is_edit')): ?>
										<!-- update link -->
										<a href="<?php echo base_url('reception/visitor_edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon">
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php endif; if (get_permission('visitor_log', 'is_delete')): ?>
										<!-- delete link -->
										<?php echo btn_delete('reception/visitor_delete/' . $row['id']); ?>
									<?php endif; ?>
								</td>
							</tr>
						<?php } } ?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('visitor_log', 'is_add')): ?>
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
						<label class="col-md-3 control-label"><?=translate('visiting_purpose')?> <span class="required">*</span></label>
						<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectByBranch('visitor_purpose', $branch_id);
									echo form_dropdown("purpose_id", $arrayBranch, set_value('purpose_id'), "class='form-control' data-width='100%' id='calling_purpose'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('name')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="name" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('mobile_no')?></label>
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
						<label class="col-md-3 control-label"><?php echo translate('entry_time'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="entry_time" value="<?php echo set_value('time_end'); ?>" />
								<span class="error"></span>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('exit_time'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" data-plugin-timepicker data-plugin-options='{ "minuteStep" : 1 }' class="form-control" name="exit_time" value="<?php echo set_value('time_end'); ?>" />
							</div>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('number_of_visitor')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="number_of_visitor" value="" autocomplete="off" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('id_number')?></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="id_number" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('token/pass')?></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="token_pass" value="" />
							<span class="error"></span>
						</div>
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

<script type="text/javascript">
	function getPurposeByBranch(id) {
	    $.ajax({
	        url: base_url + 'ajax/getDataByBranch',
	        type: 'POST',
	        data: {
	            table: "visitor_purpose",
	            branch_id: id
	        },
	        success: function (response) {
	            $('#calling_purpose').html(response);
	        }
	    });
	}
</script>