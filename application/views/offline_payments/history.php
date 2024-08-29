<?php $widget = (is_superadmin_loggedin() ? 6 : 12); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-6">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('payments') . " " . translate('status')?></label>
							<?php
								$arrayClass = array(
									'' => translate('select'), 
									'1' => translate('pending'), 
									'2' => translate('approved'), 
									'3' => translate('suspended'), 
								);
								echo form_dropdown("payments_status", $arrayClass, set_value('payments_status'), "class='form-control' id='payments_status'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
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
<?php if (isset($paymentslist)): ?>
		<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?=translate('offline_payments') . " " . translate('list')?>
				</h4>
			</header>
			<div class="panel-body">
				<div class="mb-md mt-md">
					<div class="export_title"><?=translate('offline_payments') . " " . translate('list')?></div>
					<table class="table table-bordered table-condensed table-hover mb-none tbr-top table-export">
						<thead>
							<tr>
								<th><?=translate('trx_id')?></th>
								<th><?=translate('student')?></th>
								<th><?=translate('class')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('payment_date')?></th>
								<th><?=translate('submit_date')?></th>
								<th><?=translate('amount')?></th>
								<th><?=translate('status')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$count = 1;
							foreach($paymentslist as $row):
								?>
							<tr>
								<td><?php echo $row->id;?></td>
								<td><?php echo $row->fullname;?></td>
								<td><?php echo $row->class_name . " (" . $row->section_name . ")";?></td>
								<td><?php echo $row->register_no;?></td>
								<td><?php echo _d($row->payment_date);?></td>
								<td><?php echo _d($row->submit_date);?></td>
								<td><?php echo currencyFormat($row->amount);?></td>
								<td>
									<?php
										$labelmode = '';
										$status = $row->status;
										if($status == 1) {
											$status = translate('pending');
											$labelmode = 'label-info-custom';
										} elseif($status == 2) {
											$status = translate('approved');
											$labelmode = 'label-success-custom';
										} elseif($status == 3) {
											$status = translate('suspended');
											$labelmode = 'label-danger-custom';
										}
										echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
									?>
								</td>
								<td>
								<?php if (get_permission('offline_payments', 'is_view')) { ?>
									<a href="javascript:void(0);" class="btn btn-circle icon btn-default" onclick="getApprovelOfflinePayments('<?=$row->id?>')">
										<i class="fas fa-bars"></i>
									</a>
								<?php } ?>
								</td>
							</tr>
							<?php  endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
		</section>
<?php endif; ?>
	</div>
</div>

<!-- offline payments view modal -->
<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide" id="modal">
	<section class="panel" id='quick_view'></section>
</div>

<script type="text/javascript">
	// get payments approvel details
	function getApprovelOfflinePayments(id) {
	    $.ajax({
	        url: base_url + 'offline_payments/getApprovelDetails',
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
