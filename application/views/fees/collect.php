<?php
$currency_symbol = $global_config['currency_symbol'];
$extINTL = extension_loaded('intl');
if ($extINTL == true) {
	$spellout = new NumberFormatter("en", NumberFormatter::SPELLOUT);
}
?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#invoice" data-toggle="tab"><i class="far fa-credit-card"></i> <?=translate('invoice')?></a>
			</li>
<?php if ($invoice['status'] != 'unpaid'): ?>
			<li>
				<a href="#history" data-toggle="tab"><i class="fas fa-dollar-sign"></i> <?=translate('payment_history')?></a>
			</li>
<?php endif; ?>
<?php if (get_permission('collect_fees', 'is_add') && $invoice['status'] != 'total'): ?>
			<li>
				<a href="#collect_fees" data-toggle="tab"><i class="fas fa-hand-holding-usd"></i> <?=translate('collect_fees')?></a>
			</li>
<?php endif; ?>
<?php if (get_permission('collect_fees', 'is_add') && $invoice['status'] != 'total'): ?>
			<li>
				<a href="#fully_paid" data-toggle="tab"><i class="far fa-credit-card"></i> Fully Paid</a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="invoice" class="tab-pane <?=empty($this->session->flashdata('pay_tab')) ? 'active' : ''; ?>">
				<div id="invoice_print">
					<div class="invoice">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="RamomCoder Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Invoice No #<?=$invoice['invoice_no']?></h4>
									<p class="mb-none">
										<span class="text-dark"><?=translate('date')?> : </span>
										<span class="value"><?=_d(date('Y-m-d'))?></span>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?=translate('status')?> : </span><?php
											$labelmode = '';
											if($invoice['status'] == 'unpaid') {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($invoice['status'] == 'partly') {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif($invoice['status'] == 'total') {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
										?>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold">Invoice To :</p>
										<address>
											<?php 
											echo $basic['first_name'] . ' ' . $basic['last_name'] . '<br>';
											echo translate('register_no') . ' : ' . $basic['register_no'] . '<br>';
											echo (empty($basic['student_address']) ? "" : nl2br($basic['student_address']) . '<br>');
											echo translate('class') . ' : ' . $basic['class_name'] . " (" . $basic['section_name'] . ')<br>';
											if (!empty($basic['father_name'])) {
												echo translate('father_name') . ' : ' . $basic['father_name'];
											}
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">Academic :</p>
										<address>
											<?php 
											echo $basic['school_name'] . "<br/>";
											echo $basic['school_address'] . "<br/>";
											echo $basic['school_mobileno'] . "<br/>";
											echo $basic['school_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>
					<?php if (get_permission('collect_fees', 'is_add')) { ?>
						<button type="button" class="btn btn-default btn-sm mb-sm hidden-print" id="collectFees" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-coins fa-fw"></i> Selected Fees Collect
						</button>
					<?php } ?>
						<div class="table-responsive br-none">
							<table class="table invoice-items table-hover mb-none" id="invoiceSummary">
								<thead>
									<tr class="text-dark">
										<th id="cell-count" class="text-weight-semibold hidden-print">
											<div class="checkbox-replace" >
												<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
													<input type="checkbox" class="fee-selectAll" checked><i></i>
												</label>
											</div>
										</th>
										<th id="cell-count" class="text-weight-semibold hidden-print">#</th>
										<th id="cell-item" class="text-weight-semibold"><?=translate("fees_type")?></th>
										<th id="cell-id" class="text-weight-semibold"><?=translate("due_date")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("status")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("amount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("discount")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("fine")?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate("paid")?></th>
										<th id="cell-total" class="text-center text-weight-semibold"><?=translate("balance")?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$group = array();
										$count = 1;
										$total_fine = 0;
										$fully_total_fine = 0;
										$total_discount = 0;
										$total_paid = 0;
										$total_balance = 0;
										$total_amount = 0;
										$typeData = array('' => translate('select'));
										$allocations = $this->fees_model->getInvoiceDetails($basic['enroll_id']);
										foreach ($allocations as $row) {
											$deposit = $this->fees_model->getStudentFeeDeposit($row['allocation_id'], $row['fee_type_id']);
											$type_discount = $deposit['total_discount'];
											$type_fine = $deposit['total_fine'];
											$type_amount = $deposit['total_amount'];
											$balance = $row['amount'] - ($type_amount + $type_discount);
											$total_discount += $type_discount;
											$total_fine += $type_fine;
											$total_paid += $type_amount;
											$total_balance += $balance;
											$total_amount += $row['amount'];
											if ($balance != 0) {
											 	$typeData[$row['allocation_id'] . "|" . $row['fee_type_id']] = $row['name'];
									            $fine = $this->fees_model->feeFineCalculation($row['allocation_id'], $row['fee_type_id']);
									            $b = $this->fees_model->getBalance($row['allocation_id'], $row['fee_type_id']);
									            $fine = abs($fine - $b['fine']);
									            $fully_total_fine += $fine;
											}
										?>
										<?php if(!in_array($row['group_id'], $group)) { 
											$group[] = $row['group_id'];
											?>
										<tr>
											<td class="group" colspan="10"><strong><?php echo get_type_name_by_id('fee_groups', $row['group_id']) ?></strong><img class="group" src="<?php echo base_url('assets/images/arrow.png') ?>"></td>
										</tr>
									<?php } ?>
									<tr>
										<td class="hidden-print checked-area">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="cb_invoice" value="<?php echo $row['amount']; ?>" data-allocation-id="<?php echo $row['allocation_id'] ?>" data-fee-type-id="<?php echo $row['fee_type_id'] ?>" checked><i></i></label>
											</div>
										</td>
										<td class="hidden-print"><?php echo $count++;?></td>
										<td class="text-dark"><?=$row['name']?></td>
										<td><?=_d($row['due_date'])?></td>
										<td><?php 
											$status = 0;
											$labelmode = '';
											if($type_amount == 0) {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($balance == 0) {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											} else {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											}
											echo "<span class='label ".$labelmode." '>".$status."</span>";
										?></td>
										<td><?php echo currencyFormat($row['amount']);?></td>
										<td><?php echo currencyFormat($type_discount);?></td>
										<td><?php echo currencyFormat($type_fine);?></td>
										<td><?php echo currencyFormat($type_amount);?></td>
										<td class="text-center"><?php echo currencyFormat($balance);?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg hidden-print">
							<div class="row">
								<div class="col-md-5 col-xs-12 pull-right">
									<ul class="amounts">
										<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
										<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
										<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
										<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
										<?php if ($total_balance != 0): ?>
										<li><strong><?=translate('total_paid')?> (with fine) :</strong> <?=currencyFormat($total_paid + $total_fine); ?></li>
										<li>
											<strong><?=translate('balance')?> : </strong> 
											<?php
											$numberSPELL = "";
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format(number_format($total_balance, 2, '.', ''))) . ' )';
											}
											echo currencyFormat($total_balance) . $numberSPELL;
											?>
										</li>
										<?php else: 
											$paidWithFine = ($total_paid + $total_fine);
											?>
										<li>
											<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
											<?php
											$numberSPELL = "";
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format(number_format($paidWithFine, 2, '.', ''))) . ' )';
											}
											echo currencyFormat($paidWithFine) . $numberSPELL;
											?>
										</li>
										<?php endif; ?>
									</ul>
								</div>
							</div>
						</div>
						<div class="invoice-summary text-right mt-lg visible-print-block" id="invDetailsPrint"></div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<button id="invoicePrint" class="btn btn-default ml-sm" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> <?=translate('print')?></button>
					</div>
				</div>
			</div>
			<?php if ($invoice['status'] != 'unpaid'): ?>
			<div class="tab-pane" id="history">
				<div id="payment_print">
					<div class="invoice payment">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="RamomCoder Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Invoice No #<?php echo $invoice['invoice_no']?></h4>
									<p class="mb-none">
										<span class="text-dark"><?=translate('date')?> : </span>
										<span class="value"><?php echo _d(date('Y-m-d'));?></span>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?=translate('status')?> : </span>
										<?php
											$labelmode = '';
											if($invoice['status'] == 'unpaid') {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($invoice['status'] == 'partly') {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif($invoice['status'] == 'total') {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label ".$labelmode." '>".$status."</span>";
										?>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold">Invoice To :</p>
										<address>
											<?php 
											echo $basic['first_name'] . ' ' . $basic['last_name'] . '<br>';
											echo translate('register_no') . ' : ' . $basic['register_no'] . '<br>';
											echo (empty($basic['student_address']) ? "" : nl2br($basic['student_address']) . '<br>');
											echo translate('class') . ' : ' . $basic['class_name'] . " (" . $basic['section_name'] . ')<br>';
											if (!empty($basic['father_name'])) {
												echo translate('father_name') . ' : ' . $basic['father_name'];
											}
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">Academic :</p>
										<address>
											<?php 
											echo $basic['school_name'] . "<br/>";
											echo $basic['school_address'] . "<br/>";
											echo $basic['school_mobileno'] . "<br/>";
											echo $basic['school_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>
					<?php if (get_permission('fees_revert', 'is_delete')): ?>
						<button type="button" class="btn btn-default btn-sm mb-sm hidden-print" id="selected_revert" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-trash-restore-alt"></i> <?php echo translate('selected_revert'); ?>
						</button>
					<?php endif; ?>
						<div class="table-responsive">
							<table class="table invoice-items" id="paymentHistory">
								<thead>
									<tr class="h5 text-dark">
										<th id="cell-count" class="text-weight-semibold hidden-print">
											<div class="checkbox-replace" >
												<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
													<input type="checkbox" class="fee-selectAll" checked> <i></i>
												</label>
											</div>
										</th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('fees_type')?></th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('fees_code')?></th>
										<th id="cell-item" class="text-weight-semibold"><?=translate('date')?></th>
										<th id="cell-item" class="text-weight-semibold">Payment Id</th>
										<th id="cell-item" class="text-weight-semibold hidden-print"><?=translate('collect_by')?></th>
										<th id="cell-desc" class="text-weight-semibold"><?=translate('remarks')?></th>
										<th id="cell-qty" class="text-weight-semibold"><?=translate('method')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('amount')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('discount')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('fine')?></th>
										<th id="cell-price" class="text-weight-semibold"><?=translate('paid')?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$allocations = $this->db->where(array('student_id' => $basic['enroll_id'], 'session_id' => get_session_id()))->get('fee_allocation')->result_array();
									foreach ($allocations as $allRow) {
										$historys = $this->fees_model->getPaymentHistory($allRow['id'], $allRow['group_id']);
										foreach ($historys as $row) {
									?>
									<tr>
										<td class="hidden-print checked-area">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="cb_feePay" value="<?php echo $row['id']; ?>" checked><i></i></label>
											</div>
										</td>
										<td class="text-weight-semibold text-dark"><?php echo $row['name']; ?></td>
										<td><?php echo $row['fee_code']; ?></td>
										<td><?php echo _d($row['date']); ?></td>
										<td><?php echo $row['id']; ?></td>
										<td class="hidden-print">
											<?php
												if ($row['collect_by'] == 'online') {
													echo translate('online');
												}else{
													echo get_type_name_by_id('staff', $row['collect_by']);
												}
											?>
										</td>
										<td><?php echo $row['remarks']; ?></td>
										<td><?php echo $row['payvia']; ?></td>
										<td><?php echo currencyFormat($row['amount'] + $row['discount']); ?></td>
										<td><?php echo currencyFormat($row['discount']); ?></td>
										<td><?php echo currencyFormat($row['fine']); ?></td>
										<td><?php echo currencyFormat($row['amount']); ?></td>
									</tr>
									 <?php } } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg hidden-print">
							<div class="row">
								<div class="col-md-5 col-xs-12 pull-right">
									<ul class="amounts">
										<li><strong><?=translate('sub_total')?> :</strong> <?=currencyFormat($total_paid + $total_discount); ?></li>
										<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
										<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
										<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
										<li>
											<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
											<?php
											$numberSPELL = "";
											$grand_paid = number_format($total_paid + $total_fine, 2, '.', '');
											if ($extINTL == true) {
												$numberSPELL = ' </br>( ' . ucwords($spellout->format($grand_paid)) . ' )';
											}
											echo currencyFormat($total_paid + $total_fine) . $numberSPELL;
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="invoice-summary text-right mt-lg visible-print-block" id="invPaymentHistory"></div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<button id="payReceiptPrint" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> Selected Pay Receipt</button>
						<button id="paymentPrint" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-print"></i> <?=translate('print')?></button>
					</div>
				</div>
			</div>
			<?php endif; ?>
			
			<!-- add fees form -->
			<?php if($invoice['status'] != 'total'): ?>
				<div id="collect_fees" class="tab-pane">
					<?php echo form_open('fees/fee_add', array('class' => 'form-horizontal frm-submit' )); ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fees_type')?> <span class="required">*</span></label>
							<div class="col-md-6">
							<?php
								echo form_dropdown("fees_type", $typeData, set_value('fees_type'), "class='form-control' id='fees_type'
								data-plugin-selectTwo data-width='100%' ");
							?>
							<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" data-plugin-datepicker
								data-plugin-options='{"todayHighlight" : true, "endDate": "today"}' name="date" value="<?=date('Y-m-d')?>" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="amount" id="feeAmount" value="" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('discount')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="discount_amount" value="0" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fine')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="0" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
							<div class="col-md-6">
	    						<?php
	    							$payvia_list = $this->app_lib->getSelectList('payment_types');
	    							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-plugin-selectTwo data-width='100%'
	    							data-minimum-results-for-search='Infinity' ");
	    						?>
								<span class="error"></span>
							</div>
						</div>
                        <?php
                        $links = $this->fees_model->get('transactions_links', array('branch_id' => $basic['branch_id']), true);
                        if ($links['status'] == 1) {
                        ?>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php echo translate('account'); ?> <span class="required">*</span></label>
                               	<div class="col-md-6">
                                <?php
                                    $accounts_list = $this->app_lib->getSelectByBranch('accounts', $basic['branch_id']);
                                    echo form_dropdown("account_id", $accounts_list, $links['deposit'], "class='form-control' id='account_id' required data-plugin-selectTwo data-width='100%'");
                                ?>
                            	</div>
                            </div>
                        <?php } ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('remarks')?></label>
							<div class="col-md-6 mb-md">
								<textarea name="remarks" rows="2" class="form-control" placeholder="<?=translate('write_your_remarks')?>"></textarea>
								<div class="checkbox-replace mt-lg">
									<label class="i-checks">
										<input type="checkbox" name="guardian_sms" checked> <i></i> Guardian Confirmation Sms
									</label>
								</div>
							</div>
						</div>
						<input type="hidden" name="branch_id" value="<?=$basic['branch_id']?>">
						<input type="hidden" name="student_id" value="<?=$basic['id']?>">
						<footer class="panel-footer">
							<div class="row">
								<div class="col-md-offset-3 col-md-3">
									<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
										<?=translate('fee_payment')?>
									</button>
								</div>
							</div>
						</footer>
					<?php echo form_close();?>
				</div>
			<?php endif; ?>
			<!--fully paid form-->
			<?php if($invoice['status'] != 'total'): ?>
				<div id="fully_paid" class="tab-pane">
					<?php echo form_open('fees/fee_fully_paid', array('class' => 'form-horizontal frm-submit' )); ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('date')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" data-plugin-datepicker
								data-plugin-options='{"todayHighlight" : true, "endDate":"today"}' name="date" value="<?=date('Y-m-d')?>" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="amount" id="feeAmount" value="<?=number_format($total_balance, 2, '.', '')?>" autocomplete="off" disabled />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('fine')?></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="<?=number_format($fully_total_fine, 2, '.', '')?>" autocomplete="off" disabled />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
							<div class="col-md-6">
	    						<?php
	    							$payvia_list = $this->app_lib->getSelectList('payment_types');
	    							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-plugin-selectTwo data-width='100%'
	    							data-minimum-results-for-search='Infinity' ");
	    						?>
								<span class="error"></span>
							</div>
						</div>
                        <?php
                        $links = $this->fees_model->get('transactions_links', array('branch_id' => $basic['branch_id']), true);
                        if ($links['status'] == 1) {
                        ?>
                            <div class="form-group">
                                <label class="col-md-3 control-label"><?php echo translate('account'); ?> <span class="required">*</span></label>
                               	<div class="col-md-6">
                                <?php
                                    $accounts_list = $this->app_lib->getSelectByBranch('accounts', $basic['branch_id']);
                                    echo form_dropdown("account_id", $accounts_list, $links['deposit'], "class='form-control' id='account_id' required data-plugin-selectTwo data-width='100%'");
                                ?>
                            	</div>
                            </div>
                        <?php } ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('remarks')?></label>
							<div class="col-md-6 mb-md">
								<textarea name="remarks" rows="2" class="form-control" placeholder="<?=translate('write_your_remarks')?>"></textarea>
								<div class="checkbox-replace mt-lg">
									<label class="i-checks">
										<input type="checkbox" name="guardian_sms" checked> <i></i> Guardian Confirmation Sms
									</label>
								</div>
							</div>
						</div>
						<input type="hidden" name="invoice_id" value="<?php echo $basic['enroll_id']; ?>">
						<input type="hidden" name="branch_id" value="<?=$basic['branch_id']?>">
						<input type="hidden" name="student_id" value="<?=$basic['id']?>">
						<footer class="panel-footer">
							<div class="row">
								<div class="col-md-offset-3 col-md-3">
									<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
										<?=translate('fee_payment')?>
									</button>
								</div>
							</div>
						</footer>
					<?php echo form_close();?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="zoom-anim-dialog modal-block mfp-hide modal-block-full" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-coins fa-fw"></i> <?=translate('collect_fees')?>
				<button type="button" class="close modal-dismiss" aria-label="Close">
					<span aria-hidden="true">×</span>
				</button>
			</h4>
		</header>
		<?php echo form_open('fees/selectedFeesPay', array('class' => 'frm-submit' )); ?>
		<div class="panel-body">
			<div id="printResult" class="pt-sm pb-sm">
				<div class="table-responsive">						
					<table class="table table-bordered table-condensed text-dark" id="feeCollect">

					</table>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">Fee Payment</button>
				</div>
			</div>
		</footer>
		<?php echo form_close();?>
	</section>
</div>

<script type="text/javascript">
	var branchID = "<?php echo $basic['branch_id']; ?>";
	var studentID = "<?php echo $basic['enroll_id']; ?>";
	$(".fee-selectAll").on("change", function(ev)
	{
		var $chcks = $(this).parents("table").find("tbody input[type='checkbox']");
		if($(this).is(':checked'))
		{
			$chcks.prop('checked', true).trigger('change');
		} else {
			$chcks.prop('checked', false).trigger('change');
		}
	});

	$('#collectFees').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#invoiceSummary tbody input[name='cb_invoice']:checked").each(function() {
			var allocationID = $(this).data("allocation-id");
			var feeTypeID = $(this).data("fee-type-id");
			var feeAmount = $(this).val();
            array = {};
            array ["feeAmount"] = feeAmount;
            array ["allocationID"] = allocationID;
            array ["feeTypeID"] = feeTypeID;
            arrayData.push(array);
		});
        if (arrayData.length === 0) {
            alert("No Rows Selected.");
            $btn.button('reset');
        } else {
            $.ajax({
                url: base_url + "fees/selectedFeesCollect",
                type: 'POST',
                data: {
                	'data': JSON.stringify(arrayData),
                	'branch_id': branchID,
                	'student_id' : studentID,
                },
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#feeCollect").html(response);
                },
                complete: function () {
					$(".selectTwo").each(function() {
						var $this = $(this);
						$this.themePluginSelect2({});
					});
					$(".datepicker").each(function() {
						var $this = $(this);
						$this.themePluginDatePicker({
							"todayHighlight" : true,
							"endDate" : "today"
						});
					});
                	mfp_modal('#modal');
                	$btn.button('reset');
                }
            });
        }
	});

	$('#invoicePrint').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#invoiceSummary tbody input[name='cb_invoice']").each(function() {
			if($(this).is(':checked')) {
				var allocationID = $(this).data("allocation-id");
				var feeTypeID = $(this).data("fee-type-id");
				var feeAmount = $(this).val();
	            array = {};
	            array ["feeAmount"] = feeAmount;
	            array ["allocationID"] = allocationID;
	            array ["feeTypeID"] = feeTypeID;
	            arrayData.push(array);
	            $(this).parents('tr').removeClass("hidden-print");
        	} else {
        		$(this).parents('tr').addClass("hidden-print");
        	}
		});
        if (arrayData.length === 0) {
            popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
            $btn.button('reset');
        } else {
        	$("#invDetailsPrint").html("");
            $.ajax({
                url: base_url + "fees/printFeesInvoice",
                type: 'POST',
                data: {'data': JSON.stringify(arrayData)},
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#invDetailsPrint").html(response);
                },
                complete: function () {
                	fn_printElem('invoice_print');
                	$btn.button('reset');
                }
            });
        }
	});

	$('#paymentPrint').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#paymentHistory tbody input[name='cb_feePay']").each(function() {
			if($(this).is(':checked')) {
				var paymentID = $(this).val();
	            array = {};
	            array ["payment_id"] = paymentID;
	            arrayData.push(array);
	            $(this).parents('tr').removeClass("hidden-print");
        	} else {
        		$(this).parents('tr').addClass("hidden-print");
        	}
		});
        if (arrayData.length === 0) {
            popupMsg("<?php echo translate('no_row_are_selected') ?>", "error");
            $btn.button('reset');
        } else {
        	$("#invPaymentHistory").html("");
            $.ajax({
                url: base_url + "fees/printFeesPaymentHistory",
                type: 'POST',
                data: {'data': JSON.stringify(arrayData)},
                dataType: "html",
                cache: false,
                success: function (response) {
                    $("#invPaymentHistory").html(response);
                },
                complete: function () {
                	fn_printElem('payment_print');
                	$btn.button('reset');
                }
            });
        }
	});

	$('#payReceiptPrint').on('click', function(e) {
		var $btn = $(this);
		$btn.button('loading');
		var arrayData = [];
		$("#paymentHistory tbody input[name='cb_feePay']").each(function() {
			if($(this).is(':checked')) {
				var allocationID = $(this).data("allocation-id");
				var feeTypeID = $(this).data("fee-type-id");
				var paymentID = $(this).val();
	            array = {};
	            array ["payment_id"] = paymentID;
	            array ["allocationID"] = allocationID;
	            array ["feeTypeID"] = feeTypeID;
	            arrayData.push(array);
        	}
		});
        if (arrayData.length === 0) {
            alert("No Rows Selected.");
            $btn.button('reset');
        } else {
        	$("#invDetailsPrint").html("");
            $.ajax({
                url: base_url + "fees/payReceiptPrint",
                type: 'POST',
                data: {
					'student_id' : studentID,
					'data': JSON.stringify(arrayData)
            	},
                dataType: "html",
                cache: false,
                success: function (response) {
                    fn_printElem(response, true);
                },
                complete: function () {
                	$btn.button('reset');
                }
            });
        }
	});

    $('#selected_revert').on('click', function(e){
    	var $this = $(this);
		var paymentID = [];
		$("#paymentHistory tbody input[name='cb_feePay']:checked").each(function() {
			paymentID.push($(this).val());
		});
		swal({
			title: "<?php echo translate('are_you_sure')?>",
			text: "<?php echo translate('delete_this_information')?>",
			type: "warning",
			showCancelButton: true,
			confirmButtonClass: "btn btn-default swal2-btn-default",
			cancelButtonClass: "btn btn-default swal2-btn-default",
			confirmButtonText: "<?php echo translate('yes_continue')?>",
			cancelButtonText: "<?php echo translate('cancel')?>",
			buttonsStyling: false,
			footer: "<?php echo translate('deleted_note')?>"
		}).then((result) => {
			if (result.value) {
				$.ajax({
					url: base_url + 'fees/paymentRevert',
					type: "POST",
					data: {'id': paymentID},
					dataType: "JSON",
	                beforeSend: function () {
	                    $this.button('loading');
	                },
					success:function(data) {
						swal({
						title: "<?php echo translate('deleted')?>",
						text: data.message,
						buttonsStyling: false,
						showCloseButton: true,
						focusConfirm: false,
						confirmButtonClass: "btn btn-default swal2-btn-default",
						type: data.status
						}).then((result) => {
							if (result.value) {
								location.reload();
							}
						});
					},
	                complete: function () {
	                    $this.button('reset');
	                }
				});
			}
		});
    });


    $('#fees_type').on("change", function(){
        var typeID = $(this).val();
	    $.ajax({
	        url: base_url + 'fees/getBalanceByType',
	        type: 'POST',
	        data: {
	        	'typeID': typeID
	        },
	        dataType: "json",
	        success: function (data) {
	            $('#feeAmount').val(data.balance.toFixed(2));
	            $('#fineAmount').val(data.fine.toFixed(2));
	        }
	    });
    });
</script>