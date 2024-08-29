<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
             <a href="#reference" data-toggle="tab">
                 <i class="fas fa-list-ul"></i> <?=translate('reference')?>
             </a>
			</li>
			<li>
             <a href="<?php echo base_url('reception_config/response') ?>">
                <i class="far fa-edit"></i> <?=translate('response')?>
             </a>
			</li>
			<li>
             <a href="<?php echo base_url('reception_config/calling_purpose') ?>">
                <i class="far fa-edit"></i> <?=translate('calling_purpose')?>
             </a>
			</li>
			<li>
             <a href="<?php echo base_url('reception_config/visiting_purpose') ?>">
                <i class="far fa-edit"></i> <?=translate('visiting_purpose')?>
             </a>
			</li>
			<li>
             <a href="<?php echo base_url('reception_config/complaint_type') ?>">
                <i class="far fa-edit"></i> <?=translate('complaint') . " " . translate('type')?>
             </a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane box active mb-md" id="reference">
				<div class="row">
<?php if (get_permission('config_reception', 'is_add')): ?>
					<div class="col-md-5 pr-xs">
						<section class="panel panel-custom">
							<div class="panel-heading panel-heading-custom">
								<h4 class="panel-title"><i class="far fa-edit"></i> <?=translate('add') . " " . translate('reference')?></h4>
							</div>
							<?php echo form_open($this->uri->uri_string(), array('class' => 'frm-submit'));?>
							<div class="panel-body panel-body-custom">
								<?php if (is_superadmin_loggedin()): ?>
									<div class="form-group">
										<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
										<?php
											$arrayBranch = $this->app_lib->getSelectList('branch');
											echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%'
											onchange='getSectionByBranch(this.value)' data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
										?>
										<span class="error"></span>
									</div>
								<?php endif; ?>
								<div class="form-group">
									<label class="control-label"><?=translate('name')?> <span class="required">*</span></label>
									<input type="text" class="form-control" name="name" value="" />
									<span class="error"></span>
								</div>
							</div>
							<footer class="panel-footer panel-footer-custom">
								<div class="text-right">
					                <button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
					                    <i class="fas fa-plus-circle"></i> <?=translate('save')?>
					                </button>
								</div>
							</footer>
							<?php echo form_close();?>
						</section>
					</div>
<?php endif; ?>
					<div class="col-md-<?php if (get_permission('config_reception', 'is_add')){ echo "7 pl-xs"; }else{ echo "12"; } ?>">
						<section class="panel panel-custom">
							<header class="panel-heading panel-heading-custom">
								<h4 class="panel-title"><i class="fas fa-list-ul"></i> <?=translate('reference') . " " . translate('list')?></h4>
							</header>
							<div class="panel-body panel-body-custom">
								<div class="table-responsive">
									<table class="table table-bordered table-hover table-condensed tbr-top mb-none">
										<thead>
											<tr>
												<th><?=translate('sl')?></th>
											<?php if (is_superadmin_loggedin()): ?>
												<th><?=translate('branch')?></th>
											<?php endif; ?>
												<th><?=translate('name')?></th>
												<th><?=translate('action')?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											$count = 1;
											if (count($result)){
												foreach($result as $row):
													?>
											<tr>
												<td><?php echo $count++;?></td>
											<?php if (is_superadmin_loggedin()): ?>
												<td><?php echo $row['branch_name'];?></td>
											<?php endif; ?>
												<td><?php echo $row['name'];?></td>
												<td>
												<?php if (get_permission('config_reception', 'is_edit')): ?>
													<!--update link-->
													<a href="javascript:void(0);" onclick="getReference('<?php echo $row['id']; ?>')" class="btn btn-default btn-circle icon">
														<i class="fas fa-pen-nib"></i>
													</a>
												<?php endif; if (get_permission('config_reception', 'is_delete')): ?>
													<!--delete link-->
													<?php echo btn_delete('reception_config/delete/enquiry_reference/' . $row['id']);?>
												<?php endif; ?>
												</td>
											</tr>
										<?php
											endforeach;
										}else{
											echo '<tr><td colspan="4"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
										}
										?>
										</tbody>
									</table>
								</div>
							</div>
						</section>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>

<?php if (get_permission('config_reception', 'is_edit')): ?>
<div class="zoom-anim-dialog modal-block modal-block-primary mfp-hide" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('reference'); ?>
			</h4>
		</header>
		<?php echo form_open('reception_config/edit/enquiry_reference', array('class' => 'frm-submit')); ?>
			<div class="panel-body">
				<input type="hidden" name="id" id="eid" value="">
				<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='ebranch_id'
							data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
						?>
						<span class="error"></span>
					</div>
				<?php endif; ?>
				<div class="form-group mb-md">
					<label class="control-label"><?php echo translate('name'); ?> <span class="required">*</span></label>
					<input type="text" class="form-control" value="" name="name" id="ename" autocomplete="off" />
					<span class="error"></span>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?>
						</button>
						<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
					</div>
				</div>
			</footer>
		<?php echo form_close(); ?>
	</section>
</div>
<?php endif; ?>

<script type="text/javascript">
	function getReference(id) {
		$.ajax({
			url: base_url + 'reception_config/getDetails',
			type: 'POST',
			data: {
				'id': id,
				'table': 'enquiry_reference'
			},
			dataType: "json",
			success: function (data) {
				$('#eid').val(data.id);
				$('#ename').val(data.name);
				$('#ebranch_id').val(data.branch_id).trigger('change');
				mfp_modal('#modal');
			}
		});
	}
</script>