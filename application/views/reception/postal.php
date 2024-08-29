<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
                <a href="#list" data-toggle="tab">
                    <i class="fas fa-list-ul"></i> <?=translate('postal_record') ." ". translate('list')?>
                </a>
			</li>
<?php if (get_permission('postal_record', 'is_add')): ?>
			<li>
                <a href="#add" data-toggle="tab">
                   <i class="far fa-edit"></i> <?=translate('add') . " ". translate('postal_record')?>
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
							<th><?=translate('type')?></th>
							<th><?=translate('sender') . " " . translate('title')?></th>
							<th><?=translate('receiver') . " " . translate('title')?></th>
							<th><?=translate('date') ?></th>
							<th><?=translate('confidential') ?></th>
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
								<td><?php echo $row['type'] == 1 ? translate('dispatch') : translate('receive'); ?></td>
								<td><?php echo $row['sender_title']; ?></td>
								<td><?php echo $row['receiver_title']; ?></td>
								<td><?php echo _d($row['date']); ?></td>
								<td><?php echo $row['confidential'] == 1 ? translate('yes') . ' <i class="fas fa-circle-check"></i>' : translate('no'); ?></td>
								<td>
									<a onclick="getPostalRecord('<?php echo $row['id'] ?>')" href="#" class="btn btn-default btn-circle icon">
										<i class="fas fa-envelope"></i>
									</a>
									<?php if (get_permission('postal_record', 'is_edit')): ?>
										<!-- update link -->
										<a href="<?php echo base_url('reception/postal_edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon">
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php endif; if (get_permission('postal_record', 'is_delete')): ?>
										<!-- delete link -->
										<?php echo btn_delete('reception/postal_delete/' . $row['id']); ?>
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
							<div class="col-md-8">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('type')?> <span class="required">*</span></label>
						<div class="col-md-8">
								<?php
									$arrayBranch = array(
										'' => translate('select'),
										'1' => translate('dispatch'),
										'2' => translate('receive'),
									);
									echo form_dropdown("type", $arrayBranch, set_value('type'), "class='form-control' data-width='100%'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('reference_no')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="reference_no" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('sender') . " " . translate('title')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="sender_title" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('receiver') . " " . translate('title')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" name="receiver_title" value="" />
							<span class="error"></span>
						</div>
					</div>

					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('address')?> <span class="required">*</span></label>
						<div class="col-md-8">
							<textarea type="text" rows="3" class="form-control" name="address"></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('note')?></label>
						<div class="col-md-8">
							<textarea type="text" rows="3" class="form-control" name="note"></textarea>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
						<div class="col-md-8">
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
						<div class="col-md-8">
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
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('confidential')?></label>
						<div class="col-md-6 mt-xs mb-lg">
							<div class="material-switch ml-xs">
								<input id="aswitch_1" name="confidential" type="checkbox" />
								<label for="aswitch_1" class="label-primary"></label>
							</div>
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
			<h4 class="panel-title"><i class="fas fa-envelope"></i> <?php echo translate('postal_record'); ?></h4>
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

<script type="text/javascript">
	function getPostalRecord(id) {
		$.ajax({
			url: base_url + 'reception/getPostalRecord',
			type: 'POST',
			data: {'id': id},
			dataType: "html",
			success: function (data) {
				$('#quick_view').html(data);
				mfp_modal('#modal');
			}
		});
	}
</script>