<section class="panel">
	<header class="panel-heading">
		<h4 class="panel-title"><?=translate('select_ground')?></h4>
	</header>
	<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
	<div class="panel-body">
		<div class="row mb-sm">
			<div class="col-md-offset-3 col-md-6">
				<div class="form-group">
					<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
					<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' required data-plugin-selectTwo data-width='100%'");
					?>
				</div>
			</div>
		</div>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-md-offset-10 col-md-2">
				<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
			</div>
		</div>
	</footer>
	<?php echo form_close();?>
</section>
<?php if (!empty($branch_id)): ?>
<div class="row appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
	<div class="col-md-12">
		<section class="panel">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#admission" data-toggle="tab"><i class="fas fa-sliders-h"></i> Modules List</a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="admission" class="tab-pane active">
						<?php echo form_open('modules/save', array('class' => 'frm-submit-msg')); ?>
							<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<table class="table table-bordered table-hover table-condensed mt-sm" cellspacing="0" width="100%">
								<thead>
									<tr>
										<th><?php echo translate('name') ?></th>
										<th> 
											<div class="checkbox-replace"> 
												<label class="i-checks"><input type="checkbox" id="all_view" value="1"><i></i> <?php echo translate('active'); ?></label> 
											</div>
										</th>
									</tr>
								</thead>
								<tbody>
									<?php
									$result = $this->module_model->getStatusArr($branch_id);
									foreach ($result as $key => $value) {
										?>
									<input type="hidden" name="system_fields[<?php echo $value->id ?>][modules_id]" value="<?php echo $value->id ?>">
									<tr>
										<td class="pl-xl"><i class="far fa-arrow-alt-circle-right text-md"></i> <?php echo ucwords(str_replace('_', ' ', $value->prefix)) ?></td>
										<td>
											<div class="checkbox-replace"> 
												<label class="i-checks"><input type="checkbox" class="cb_view" name="system_fields[<?php echo $value->id ?>][status]" <?php echo $value->status == 1 ? 'checked' : '' ?> value="1" >
													<i></i>
												</label>
											</div>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>

							<?php if (get_permission('system_student_field', 'is_edit')) { ?>
							<footer class="panel-footer">
								<div class="row">
									<div class="col-md-offset-10 col-md-2">
										<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
											<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
										</button>
									</div>
								</div>
							</footer>
							<?php } ?>
						<?php echo form_close(); ?>
					</div>
					
				</div>
			</div>
		</section>
	</div>
</div>
<?php endif; ?>

<script type="text/javascript">
    $('#all_view').on('click', function(){
        var cbRequired = $('.cb_add');
        if (this.checked) {
            cbRequired.prop('disabled', false);
        } else {
            cbRequired.prop('disabled', true);
        }
    });
</script>