<?php $row = $this->userrole_model->getOfflinePaymentsList(array('op.id' => $payments_id), true);
$groupID = $this->db->select('group_id')->where('id', $row['fees_allocation_id'])->get('fee_allocation')->row();
$currency_symbol = $global_config['currency_symbol'];
?>
<header class="panel-heading">
	<h4 class="panel-title"><i class="fas fa-bars"></i> <?php echo translate('details'); ?></h4>
</header>
<div class="panel-body">
    <section class="panel pg-fw mt-sm">
        <div class="panel-body">
            <h5 class="chart-title mb-xs"><?=translate('payment_details')?></h5>
            <div class="mt-lg">
				<div class="table-responsive">
					<table class="table borderless mb-none">
						<tbody>
							<tr>
								<th><?php echo translate('trx_id'); ?> : </th>
								<td><?php echo $row['id'] ?></td>
							</tr>
							<tr>
								<th width="120"><?=translate('reviewed_by')?> :</th>
								<td>
									<?php
			                            if(!empty($row['approved_by'])){
			                                echo get_type_name_by_id('staff', $row['approved_by']);
			                            }else{
			                                echo translate('unreviewed');
			                            }
									?>
								</td>
							</tr>
							<tr>
								<th><?php echo translate('payment_method'); ?> : </th>
								<td><?php echo get_type_name_by_id('offline_payment_types', $row['payment_method']); ?></td>
							</tr>
							<tr>
								<th><?php echo translate('fees_group'); ?> : </th>
								<td><?php 
								if (!empty($groupID)) {
									echo get_type_name_by_id('fee_groups', $groupID->group_id);
								}?></td>
							</tr>
							<tr>
								<th><?php echo translate('fees_type'); ?> : </th>
								<td><?php echo get_type_name_by_id('fees_type', $row['fees_type_id']); ?></td>
							</tr>
							<tr>
								<th><?php echo translate('date_of_submission '); ?> : </th>
								<td><?php echo _d($row['submit_date']); ?></td>
							</tr>
							<tr>
								<th><?php echo translate('date_of_payment'); ?> : </th>
								<td><?php echo _d($row['payment_date']); ?></td>
							</tr>
							<tr class="text-nowrap">
								<th>Approved / Rejected Date : </th>
								<td><?php echo (empty($row['approve_date']) ? '-' : $row['approve_date']); ?></td>
							</tr>
							<tr>
								<th><?php echo translate('reference'); ?> : </th>
								<td><?php echo (empty($row['reference']) ? 'N/A' : $row['reference']); ?></td>
							</tr>
							<tr>
								<th><?php echo translate('user') . " " . translate('note'); ?> : </th>
								<td><?php echo (empty($row['note']) ? 'N/A' : $row['note']); ?></td>
							</tr>
<?php if (!empty($row['enc_file_name'])) { ?>
							<tr>
								<th><?php echo translate('proof_of_payment'); ?> : </th>
								<td><a class="btn btn-default btn-sm" target="_blank" href="<?=base_url('offline_payments/download/' . $row['id'] . '/' . $row['enc_file_name'])?>"><i class="far fa-arrow-alt-circle-down"></i> <?php echo translate('download'); ?></a></td>
							</tr>
<?php } ?>
<?php if ($row['fine'] != 0) { ?>
							<tr>
								<th><?php echo translate('fee') . " " . translate('amount'); ?> : </th>
								<td><b><?php echo currencyFormat($row['amount']); ?></b></td>
							</tr>
							<tr>
								<th><?php echo translate('fine') . " " . translate('amount'); ?> : </th>
								<td><b><?php echo currencyFormat($row['fine']); ?></b></td>
							</tr>
							<tr>
								<th><?php echo translate('total_paid'); ?> : </th>
								<td><b><?php echo currencyFormat($row['amount'] + $row['fine']); ?></b></td>
							</tr>
<?php } else { ?>
							<tr>
								<th><?php echo translate('paid') . " " . translate('amount'); ?> : </th>
								<td><b><?php echo currencyFormat($row['amount']); ?></b></td>
							</tr>
<?php }  ?>
							<tr>
				                <th><?php echo translate('status'); ?> : </th>
								<th>
								<?php
									$labelmode = '';
									$status = $row['status'];
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
								</th>
							</tr>
							<tr>
								<th><?php echo translate('comments'); ?> / <?php echo translate('reason'); ?> : </th>
								<td><?php echo $row['comments']; ?></td>
							</tr>
						</tbody>
					</table>
				</div>
            </div>
        </div>
    </section>
</div>
<footer class="panel-footer">
	<div class="row">
		<div class="col-md-12 text-right">
			<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
		</div>
	</div>
</footer>
