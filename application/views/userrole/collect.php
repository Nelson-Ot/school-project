<?php
$currency_symbol = $global_config['currency_symbol'];
$allocations = $this->fees_model->getInvoiceDetails($basic['enroll_id']);
$extINTL = extension_loaded('intl');
if ($extINTL == true) {
	$spellout = new NumberFormatter("en", NumberFormatter::SPELLOUT);
}
if (count($allocations)) {
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
	<?php if ($invoice['status'] != 'total'): ?>
				<li>
					<a href="#collect_fees" data-toggle="tab"><i class="far fa-credit-card"></i> <?=translate('online_pay')?></a>
				</li>
			<?php if ($getOfflinePaymentsConfig == 1) { ?>
				<li>
					<a href="#offline_payments" data-toggle="tab"><i class="far fa-credit-card"></i> <?=translate('offline_payments')?></a>
				</li>
			<?php } ?>
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
												echo $basic['student_address'] . '<br>';
												echo translate('class') . ' : ' . $basic['class_name'] . '<br>';
												echo translate('email') . ' : ' . $basic['student_email']; 
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

							<div class="table-responsive br-none">
								<table class="table invoice-items table-hover mb-none">
									<thead>
										<tr class="text-dark">
											<th id="cell-id" class="text-weight-semibold">#</th>
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
											$total_discount = 0;
											$total_paid = 0;
											$total_balance = 0;
											$total_amount = 0;
											$typeData = array('' => translate('select'));
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
												}
											?>
										<?php if(!in_array($row['group_id'], $group)) { 
											$group[] = $row['group_id'];
											?>
										<tr>
											<td class="group" colspan="9"><strong><?php echo get_type_name_by_id('fee_groups', $row['group_id']) ?></strong><img class="group" src="<?php echo base_url('assets/images/arrow.png') ?>"></td>
										</tr>
										<?php } ?>
										<tr>
											<td><?php echo $count++;?></td>
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
							<div class="invoice-summary text-right mt-lg">
								<div class="row">
									<div class="col-md-5 col-xs-12 pull-right">
										<ul class="amounts">
											<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
											<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
											<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
											<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
											<?php if ($total_balance != 0): ?>
											<li>
												<strong><?=translate('balance')?> : </strong> 
												<?php
												$numberSPELL = "";
												if ($extINTL == true) {
													$numberSPELL = ' </br>( ' . ucwords($spellout->format($total_balance)) . ' )';
												}
												echo currencyFormat($total_balance) . $numberSPELL;
												?>
											</li>
											<?php else: ?>
											<li>
												<strong><?=translate('total_paid')?> : </strong> 
												<?php
												$numberSPELL = "";
												if ($extINTL == true) {
													$numberSPELL = ' </br>( ' . ucwords($spellout->format(($total_paid + $total_fine))) . ' )';
												}
												echo currencyFormat(($total_paid + $total_fine)) . $numberSPELL;
												?>
											</li>
											<?php endif; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="text-right mr-lg hidden-print">
							<button onClick="fn_printElem('invoice_print')" class="btn btn-default ml-sm"><i class="fas fa-print"></i> <?=translate('print')?></button>
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
											<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="Img" />
										</div>
									</div>
									<div class="col-md-6 text-right">
										<h4 class="mt-none mb-none text-dark">Invoice No #<?= $invoice['invoice_no']?></h4>
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
												echo $basic['first_name'] . ' '. $basic['last_name'] . '<br>';
												echo $basic['student_address'] . '<br>';
												echo translate('class').' : '. $basic['class_name'] . '<br>';
												echo translate('email').' : '. $basic['student_email']; 
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
							<div class="table-responsive br-none">
								<table class="table invoice-items" id="invoice">
									<thead>
										<tr class="h5 text-dark">
											<th id="cell-id" class="text-weight-semibold hidden-print">
												<div class="checkbox-replace" >
													<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
														<input type="checkbox" name="select-all" id="selectAllchkbox" checked> <i></i>
													</label>
												</div>
											</th>
											<th id="cell-item" class="text-weight-semibold"><?=translate('fees_type')?></th>
											<th id="cell-item" class="text-weight-semibold"><?=translate('fees_code')?></th>
											<th id="cell-item" class="text-weight-semibold"><?=translate('date')?></th>
											<th id="cell-item" class="text-weight-semibold hidden-print"><?=translate('collect_by')?></th>
											<th id="cell-desc" class="text-weight-semibold"><?=translate('remarks')?></th>
											<th id="cell-qty" class="text-weight-semibold"><?=translate('method')?></th>
											<th id="cell-price" class="text-weight-semibold"><?=translate('amount')?></th>
											<th id="cell-price" class="text-weight-semibold"><?=translate('discount')?></th>
											<th id="cell-price" class="text-weight-semibold"><?=translate('fine')?></th>
										</tr>
									</thead>
									<tbody>
										<?php
										$allocations = $this->db->where(array('student_id' => $basic['id'], 'session_id' => get_session_id()))->get('fee_allocation')->result_array();
										foreach ($allocations as $allRow) {
											$historys = $this->fees_model->getPaymentHistory($allRow['id'], $allRow['group_id']);
											foreach ($historys as $row) {
										?>
										<tr>
											<td class="hidden-print checked-area">
												<div class="checkbox-replace">
													<label class="i-checks"><input type="checkbox" name="chkPrint" checked ><i></i></label>
												</div>
											</td>
											<td class="text-weight-semibold text-dark"><?=$row['name']?></td>
											<td><?=$row['fee_code']?></td>
											<td><?=_d($row['date'])?></td>
											<td class="hidden-print">
												<?php
													if ($row['collect_by'] == 'online') {
														echo translate('online');
													}else{
														echo get_type_name_by_id('staff', $row['collect_by']);
													}
												?>
											</td>
											<td><?=$row['remarks']?></td>
											<td><?=$row['payvia']?></td>
											<td><?=currencyFormat($row['amount'])?></td>
											<td><?=currencyFormat($row['discount'])?></td>
											<td><?=currencyFormat($row['fine'])?></td>
										</tr>
										 <?php } } ?>
									</tbody>
								</table>
							</div>
							<div class="invoice-summary text-right mt-lg">
								<div class="row">
									<div class="col-lg-5 pull-right">
										<ul class="amounts">
											<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
											<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
											<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
											<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
											<?php if ($total_balance != 0): ?>
											<li>
												<strong><?=translate('balance')?> : </strong> 
												<?php
												$numberSPELL = "";
												if ($extINTL == true) {
													$numberSPELL = ' </br>( ' . ucwords($spellout->format($total_balance)) . ' )';
												}
												echo currencyFormat($total_balance) . $numberSPELL;
												?>
											</li>
											<?php else: ?>
											<li>
												<strong><?=translate('total_paid')?> : </strong> 
												<?php
												$numberSPELL = "";
												if ($extINTL == true) {
													$numberSPELL = ' </br>( ' . ucwords($spellout->format(($total_paid + $total_fine))) . ' )';
												}
												echo currencyFormat($total_paid + $total_fine) . $numberSPELL;
												?>
											</li>
											<?php endif; ?>
										</ul>
									</div>
								</div>
							</div>
						</div>
						<div class="text-right mr-lg hidden-print">
							<button onClick="fn_printElem('payment_print')" class="btn btn-default"><i class="fas fa-print"></i> <?=translate('print')?></button>
						</div>
					</div>
				</div>
				<?php endif; ?>
				
				<!--add fees form-->
				<?php if($invoice['status'] != 'total'): ?>
					<div id="collect_fees" class="tab-pane">
						<div class="mb-xlg">
						<?php echo form_open('feespayment/checkout', array('class' => 'form-horizontal frm-submit' )); ?>
							<input type="hidden" name="invoice_no" value="<?=$invoice['invoice_no']?>">
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('fees_type')?> <span class="required">*</span></label>
								<div class="col-md-6">
								<?php
									echo form_dropdown("fees_type", $typeData, set_value('fees_type'), "class='form-control' onchange='getBalanceByType(this)' 
									data-plugin-selectTwo data-width='100%' ");
								?>
								<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="fee_amount" id="feeAmount" value="" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('fine')?></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="0" autocomplete="off" readonly="" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group">
								<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
								<div class="col-md-6">
		    						<?php
										$payvia_list = array('' => translate('select_payment_method'));
										if ($config['paypal_status'] == 1)
											$payvia_list['paypal'] = 'Paypal';
										if ($config['stripe_status'] == 1)
											$payvia_list['stripe'] = 'Stripe';
										if ($config['payumoney_status'] == 1)
											$payvia_list['payumoney'] = 'PayUmoney';
										if ($config['paystack_status'] == 1)
											$payvia_list['paystack'] = 'Paystack';
										if ($config['razorpay_status'] == 1)
											$payvia_list['razorpay'] = 'Razorpay';
										if ($config['sslcommerz_status'] == 1)
											$payvia_list['sslcommerz'] = 'SSLcommerz';
										if ($config['jazzcash_status'] == 1)
											$payvia_list['jazzcash'] = 'Jazzcash';
										if ($config['midtrans_status'] == 1)
											$payvia_list['midtrans'] = 'Midtrans';
										if ($config['flutterwave_status'] == 1)
											$payvia_list['flutterwave'] = 'Flutter Wave';
	                                    if ($config['paytm_status'] == 1)
	                                        $payvia_list['paytm'] = 'Paytm';
	                                    if ($config['toyyibpay_status'] == 1)
	                                        $payvia_list['toyyibpay'] = 'toyyibPay';
	                                    if ($config['payhere_status'] == 1)
	                                        $payvia_list['payhere'] = 'Payhere';
	                                    if ($config['nepalste_status'] == 1)
	                                        $payvia_list['nepalste'] = 'Nepalste';
		    							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-plugin-selectTwo data-width='100%' id='pay_via'
		    							data-minimum-results-for-search='Infinity' ");
		    						?>
									<span class="error"></span>
								</div>
							</div>
						
							<div class="form-group payu"  style="display: none;">
								<label class="col-md-3 control-label">Name <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="payer_name" value="<?php echo $getUser['name'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group payu" style="display: none;">
								<label class="col-md-3 control-label">Email <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="email" class="form-control" name="email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group payu" style="display: none;">
								<label class="col-md-3 control-label">Phone <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>

							<div class="form-group toyyibpay" style="display: none;">
								<label class="col-md-3 control-label">Email <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="email" class="form-control" name="toyyibpay_email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group toyyibpay" style="display: none;">
								<label class="col-md-3 control-label">Phone <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="toyyibpay_phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>

							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">Name <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="sslcommerz_name" value="<?php echo $getUser['name'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">Email <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="email" class="form-control" name="sslcommerz_email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">Address <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="sslcommerz_address" value="<?php echo $getUser['address'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">Post Code <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="sslcommerz_postcode" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">State <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="sslcommerz_state" value="<?php echo $getUser['state'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							<div class="form-group sslcommerz" style="display: none;">
								<label class="col-md-3 control-label">Phone <span class="required">*</span></label>
								<div class="col-md-6">
									<input type="text" class="form-control" name="sslcommerz_phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
									<span class="error"></span>
								</div>
							</div>
							</div>
							<footer class="panel-footer">
								<div class="row">
									<div class="col-md-offset-3 col-md-3">
										<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
											<i class="fas fa-credit-card"></i> <?=translate('fees_pay_now')?>
										</button>
									</div>
								</div>
							</footer>
						<?php echo form_close();?>
					</div>
					<?php if ($getOfflinePaymentsConfig == 1) { ?>
					<div id="offline_payments" class="tab-pane">
						<div class="mb-xlg">
							<!-- offline payments add -->
					        <section class="panel pg-fw">
					            <div class="panel-body">
					                <h5 class="chart-title mb-xs"><i class="fas fa-credit-card"></i> <?=translate('payment')?></h5>
					                <div class="mt-lg">
										<?php echo form_open_multipart('userrole/offline_payments', array('class' => 'form-horizontal frm-submit-data' )); ?>
											<input type="hidden" name="invoice_no" value="<?=$invoice['invoice_no']?>">
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('fees_type')?> <span class="required">*</span></label>
												<div class="col-md-5">
												<?php
													echo form_dropdown("fees_type", $typeData, set_value('fees_type'), "class='form-control' onchange='getBalanceByType(this)'
													data-plugin-selectTwo data-width='100%' ");
												?>
												<span class="error"></span>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
												<div class="col-md-5">
						    						<?php
														$payvia_list = array('' => translate('select_payment_method'));
														$paymentTypes = $this->db->where('branch_id', $basic['branch_id'])->get('offline_payment_types')->result();
														foreach ($paymentTypes as $key => $value) {
															$payvia_list[$value->id] = $value->name;
														}
						    							echo form_dropdown("payment_method", $payvia_list, set_value('payment_method'), "class='form-control' data-plugin-selectTwo data-width='100%' id='paymentMethod'
						    							data-minimum-results-for-search='Infinity' ");
						    						?>
													<span class="error"></span>
												</div>
											</div>
											<div class="form-group hidden-div" id="instructionDiv">
												<label class="col-md-3 control-label"><?=translate('instructions')?></label>
												<div class="col-md-5">
						    						<div class="alert alert-info mb-none" id="instruction"></div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('date_of_payment')?> <span class="required">*</span></label>
												<div class="col-md-5">
													<input type="text" class="form-control" name="date_of_payment" value="" autocomplete="off" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
													<span class="error"></span>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('amount')?> <span class="required">*</span></label>
												<div class="col-md-5">
													<input type="text" class="form-control" name="fee_amount" id="feeAmount" value="" autocomplete="off" />
													<span class="error"></span>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('fine')?></label>
												<div class="col-md-5">
													<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="0" autocomplete="off" readonly="" />
													<span class="error"></span>
												</div>
											</div>

											<div class="form-group hidden-div" id="totalPayableDiv">
												<label class="col-md-3 control-label"><?=translate('total_payable')?></label>
												<div class="col-md-5">
													<div class="alert alert-success mb-none" id="totalPayable"></div>
												</div>
											</div>


											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('reference')?></label>
												<div class="col-md-5">
													<input type="text" class="form-control" name="reference" value="" autocomplete="off" />
													<span class="error"></span>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label"><?=translate('note')?> <span class="required">*</span></label>
												<div class="col-md-5">
													<textarea class="form-control" name="note" rows="3"></textarea>
													<span class="error"></span>
												</div>
											</div>
			                                <div class="form-group">
			                                    <label class="col-md-3 control-label"><?php echo translate('proof_of_payment');?></label>
			                                    <div class="col-md-4">
			                                        <input type="file" name="proof_of_payment" class="dropify" data-height="150" data-default-file="" />
			                                        <span class="error"></span>
			                                    </div>
			                                </div>
			                                <div class="form-group mb-lg">
												<div class="col-md-offset-3 col-md-3">
													<button type="submit" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
														<i class="fas fa-credit-card"></i> <?=translate('pay')?>
													</button>
												</div>
											</div>
										<?php echo form_close();?>	
					                </div>
					            </div>
					        </section>
					        <!-- offline payments list -->
					        <section class="panel pg-fw">
					            <div class="panel-body">
					                <h5 class="chart-title mb-xs"><?=translate('offline_payments')?></h5>
					                <div class="mt-lg">
										<table class="table table-bordered table-condensed table-hover mb-none tbr-top table-export">
											<thead>
												<tr>
													<th><?=translate('trx_id')?></th>
													<th><?=translate('student')?></th>
													<th><?=translate('payment_date')?></th>
													<th><?=translate('submit_date')?></th>
													<th><?=translate('amount')?></th>
													<th><?=translate('fine')?></th>
													<th><?=translate('status')?></th>
													<th><?=translate('action')?></th>
												</tr>
											</thead>
											<tbody>
												<?php
												$count = 1;
												$paymentslist = $this->userrole_model->getOfflinePaymentsList();
												foreach($paymentslist as $row):
													?>
												<tr>
													<td><?php echo $row->id;?></td>
													<td><?php echo $row->fullname;?></td>
													<td><?php echo _d($row->payment_date);?></td>
													<td><?php echo _d($row->submit_date);?></td>
													<td><?php echo currencyFormat($row->amount);?></td>
													<td><?php echo currencyFormat($row->fine);?></td>
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
														<a href="javascript:void(0);" class="btn btn-circle icon btn-default" onclick="getApprovelOfflinePayments('<?=$row->id ?>')">
															<i class="fas fa-bars"></i>
														</a>
													</td>
												</tr>
												<?php  endforeach; ?>
											</tbody>
										</table>
					                </div>
					            </div>
					        </section>
						</div>
					</div>
					<?php } ?>
				<?php endif; ?>
			</div>
		</div>
	</section>
<?php }else{ ?>
	<section class="panel">
		<div class="panel-body">
			<div class="alert alert-subl mb-none text-center">
				<i class="fas fa-exclamation-triangle"></i> <?=translate('no_fees_have_been_allocated')?>
			</div>
		</div>
	</section>
<?php } ?>

<?php if ($getOfflinePaymentsConfig == 1) { ?>
<!-- offline payments view modal -->
<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide" id="modal">
	<section class="panel" id='quick_view'></section>
</div>

<script type="text/javascript">
	// get payments approvel details
	function getApprovelOfflinePayments(id) {
	    $.ajax({
	        url: base_url + 'userrole/getOfflinePaymentslDetails',
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
<?php } ?>

<script type="text/javascript">
	function getBalanceByType(sel)
	{
        var typeID = $(sel).val();
	    $.ajax({
	        url: base_url + 'userrole/getBalanceByType',
	        type: 'POST',
	        data: {
	        	'typeID': typeID
	        },
	        dataType: "json",
	        success: function (data) {
	            $(sel).closest('form').find('#feeAmount').val(data.balance.toFixed(2));
	            $(sel).closest('form').find('#fineAmount').val(data.fine.toFixed(2));
	        	if (parseFloat(data.fine) == 0) {
	        		$('#totalPayableDiv').hide(500);
	        	} else {
	        		$('#totalPayable').html(parseFloat(data.balance + data.fine).toFixed(2));
	        		$('#totalPayableDiv').show(500);
	        	}
	        }
	    });
	}

    $('#paymentMethod').on("change", function(){
        var typeID = $(this).val();
	    $.ajax({
	        url: base_url + 'offline_payments/getTypeInstruction',
	        type: 'POST',
	        data: {
	        	'typeID': typeID
	        },
	        dataType: "html",
	        success: function (str) {
	        	if (!str || str.length === 0) {
	        		$('#instructionDiv').hide(500);
	        	} else {
	        		$('#instruction').html(str);
	        		$('#instructionDiv').show(500);
	        	}
	        }
	    });
    });

	$(document).ready(function () {
		$(document).on('change', '#pay_via', function(){
			var method = $(this).val();
			if (method =="payumoney") {
				$('.payu').show(400);
				$('.sslcommerz').hide(400);
				$('.toyyibpay').hide(400);
			} else if (method =="sslcommerz") {
				$('.sslcommerz').show(400);
				$('.payu').hide(400);
				$('.toyyibpay').hide(400);
			} else if (method == "toyyibpay" || method == "payhere") {
				$('.toyyibpay').show(400);
				$('.sslcommerz').hide(400);
				$('.payu').hide(400);
			} else if (method =="toyyibpay") {
				$('.toyyibpay').show(400);
				$('.sslcommerz').hide(400);
				$('.payu').hide(400);
			} else{
				$('.sslcommerz').hide(400);
				$('.toyyibpay').hide(400);
				$('.payu').hide(400);
			}
		});
	});
</script>