<?php
$total_paid = number_format($billdata['paid'], 2, '.', '');
$total_amount = number_format($billdata['total'], 2, '.', '');
$total_discount = number_format($billdata['discount'], 2, '.', '');
$currency = $global_config['currency'];
$currency_symbol = $global_config['currency_symbol'];
$due_amount = number_format($billdata['due'], 2, '.', '');
$active_tab = $this->session->flashdata('active_tab');
?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="<?php echo (empty($active_tab) || $active_tab == 1 ? 'active' : ''); ?>">
				<a href="#invoice" data-toggle="tab"><i class="far fa-credit-card"></i> <?php echo translate('invoice'); ?></a>
			</li>
<?php if($total_paid > 1 && get_permission('purchase_payment', 'is_view')):?>
			<li class="<?php echo ($active_tab == 2 ? 'active' : ''); ?>">
				<a href="#payment_history" data-toggle="tab"><i class="fas fa-dollar-sign"></i> <?php echo translate('payment_history'); ?></a>
			</li>
<?php endif; if($billdata['payment_status'] != 3 && get_permission('purchase_payment', 'is_add')): ?>
			<li class="<?php echo ($active_tab == 3 ? 'active' : ''); ?>">
				<a href="#add_payment" data-toggle="tab"><i class="far fa-money-bill-alt"></i> <?php echo translate('add_payment'); ?></a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="invoice" class="tab-pane <?php echo (empty($active_tab) || $active_tab == 1 ? 'active' : ''); ?>">
				<div id="invoice_print">
					<div class="invoice">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?php echo base_url('uploads/app_image/printing-logo.png'); ?>" alt="Img" />
									</div>
								</div>
								<div class="col-xs-6 text-right">
									<h4 class="mt-none mb-none text-dark">Bill No #<?php echo html_escape($billdata['bill_no']); ?></h4>
									<p class="mb-none">
										<span class="text-dark"><?php echo translate('payment') . " " . translate('status'); ?> : </span>
										<?php 
											$status = $billdata['payment_status'];
											$payment_a = array(
												'1' => translate('unpaid'),
												'2' => translate('partly_paid'),
												'3' => translate('total_paid')
											);
											echo ($payment_a[$status]);
										?>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?php echo translate('date'); ?> : </span>
										<span class="value"><?php echo _d($billdata['date']); ?></span>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold"><?php echo translate('sale_to'); ?> :</p>
										<address>
											<?php
											$stuDetails = $this->application_model->getUserNameByRoleID($billdata['role_id'], $billdata['user_id']);
											echo $stuDetails['name'] . '<br>';
											echo translate('roles') . " : " . $billdata['role_name'] . '<br>';
											echo empty($stuDetails['email']) ? '' : translate('email') . " : " . ($stuDetails['email'] . '<br>');
											echo empty($stuDetails['mobileno']) ? '' : (translate('mobile_no') . " : " . $stuDetails['mobileno'] . '<br>');
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">From :</p>
										<address>
											<?php 
											echo $global_config['institute_name'] . "<br/>";
											echo $global_config['address'] . "<br/>";
											echo $global_config['mobileno'] . "<br/>";
											echo $global_config['institute_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table invoice-items table-hover mb-none">
								<thead>
									<tr class="text-dark">
										<th id="cell-id" class="text-weight-semibold">#</th>
										<th id="cell-item" class="text-weight-semibold"><?php echo translate("product"); ?></th>
										<th id="cell-price" class="text-weight-semibold"><?php echo translate("unit") . " " . translate("price"); ?></th>
										<th id="cell-qty" class="text-weight-semibold"><?php echo translate("quantity"); ?></th>
										<th id="cell-qty" class="text-weight-semibold"><?php echo translate("discount"); ?></th>
										<th id="cell-total" class="text-center text-weight-semibold"><?php echo translate("sub") . " " . translate("total"); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$count = 1;
										$productlist = $this->inventory_model->get('sales_bill_details', array('sales_bill_id' => $billdata['id']));
										foreach ($productlist as $product) {
											$sub_total = $product['sub_total'];
											$discount = $product['discount'];
									?>
									<tr>
										<td><?php echo $count++; ?></td>
										<td class="text-weight-semibold text-dark"><?php echo get_type_name_by_id('product', $product['product_id']); ?></td>
										<td><?php echo html_escape(currencyFormat($product['unit_price'])); ?></td>
										<td><?php echo html_escape($product['quantity']); ?></td>
										<td><?php echo html_escape(currencyFormat($discount)); ?></td>
										<td class="text-center"><?php echo html_escape(currencyFormat($sub_total - $discount)); ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg">
							<div class="row">
								<div class="col-md-5 pull-right">
									<ul class="amounts">
										<li><?php echo translate("sub") . " " . translate('total'); ?> : <?php echo currencyFormat($total_amount); ?></li>
										<li><?php echo translate('discount'); ?> : <?php echo currencyFormat($total_discount); ?></li>
<?php if ($status == 3){ 
	$g = ($total_amount - $total_discount);
	$grandtotal = number_format($g, 2, '.', '') ?>
										<li>
											<strong><?php echo translate('grand') . " " . translate('total'); ?>  : </strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo currencyFormat($grandtotal) . " </br>( ". ucwords($f->format($grandtotal)) . " $currency )";
											?>
										</li>
<?php }else{ ?>
										<li><?php echo translate('paid_amount'); ?> : <?php echo currencyFormat($total_paid); ?></li>
										<li>
											<strong><?php echo translate('due'); ?> :</strong> 
											<?php
											$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
											echo currencyFormat($due_amount) . ' </br>( ' . ucwords($f->format($due_amount)) . ' )';
											?>
										</li>
<?php } ?>
									</ul>
								</div>
							</div>
						</div>
						<div class="row mt-xxlg">
							<div class="col-xs-6">
								<div class="text-left">
									<?php echo translate('prepared_by') . " - " . $billdata['biller_name']; ?>
								</div>
							</div>
							<div class="col-md-6">
								<div class="auth-signatory">
									<?php echo translate('authorised_by'); ?>
								</div>
							</div>
						</div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<button class="btn btn-default ml-sm" onClick="fn_printElem('invoice_print')"><i class="fas fa-print"></i> <?php echo translate('print'); ?></button>
					</div>
				</div>
			</div>
<?php if($total_paid > 1 && get_permission('purchase_payment', 'is_view')): ?>
			<div class="tab-pane <?php echo ($active_tab == 2 ? 'active' : ''); ?>" id="payment_history">
				<div id="payment_print">
					<div class="invoice">
						<header class="clearfix">
							<div class="row">
								<div class="col-xs-6">
									<div class="ib">
										<img src="<?php echo base_url('uploads/app_image/printing-logo.png'); ?>" alt="Img" />
									</div>
								</div>
								<div class="col-md-6 text-right">
									<h4 class="mt-none mb-none text-dark">Bill No #<?php echo html_escape($billdata['bill_no']); ?></h4>
									<p class="mb-none">
										<span class="text-dark"><?php echo translate('payment') . " " . translate('status'); ?> : </span>
										<?php 
											$status = $billdata['payment_status'];
											$payment_a = array(
												'1' => translate('unpaid'),
												'2' => translate('partly_paid'),
												'3' => translate('total_paid')
											);
											echo ($payment_a[$status]);
										?>
									</p>
									<p class="mb-none">
										<span class="text-dark"><?php echo translate('date'); ?> : </span>
										<span class="value"><?php echo _d($billdata['date']); ?></span>
									</p>
								</div>
							</div>
						</header>
						<div class="bill-info">
							<div class="row">
								<div class="col-xs-6">
									<div class="bill-data">
										<p class="h5 mb-xs text-dark text-weight-semibold"><?php echo translate("to"); ?> :</p>
										<address>
											<?php
											$stuDetails = $this->application_model->getUserNameByRoleID($billdata['role_id'], $billdata['user_id']);
											echo $stuDetails['name'] . '<br>';
											echo translate('roles') . " : " . $billdata['role_name'] . '<br>';
											echo empty($stuDetails['email']) ? '' : translate('email') . " : " . ($stuDetails['email'] . '<br>');
											echo empty($stuDetails['mobileno']) ? '' : (translate('mobile_no') . " : " . $stuDetails['mobileno'] . '<br>');
											?>
										</address>
									</div>
								</div>
								<div class="col-xs-6">
									<div class="bill-data text-right">
										<p class="h5 mb-xs text-dark text-weight-semibold">From :</p>
										<address>
											<?php 
											echo $global_config['institute_name'] . "<br/>";
											echo $global_config['address'] . "<br/>";
											echo $global_config['mobileno'] . "<br/>";
											echo $global_config['institute_email'] . "<br/>";
											?>
										</address>
									</div>
								</div>
							</div>
						</div>

						<div class="table-responsive">
							<table class="table invoice-items table-hover mb-none">
								<thead>
									<tr class="text-dark">
										<th id="cell-id" class="text-weight-semibold">#</th>
										<th id="cell-item" class="text-weight-semibold"><?php echo translate("payment_by"); ?></th>
										<th id="cell-price" class="text-weight-semibold"><?php echo translate("pay_via"); ?></th>
										<th id="cell-qty" class="text-weight-semibold"><?php echo translate("remarks"); ?></th>
										<th id="cell-qty" class="text-weight-semibold"><?php echo translate("paid_on"); ?></th>
										<th id="cell-total" class="text-center text-weight-semibold"><?php echo translate("amount"); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$count = 1;
										$paymentlist = $this->inventory_model->get('sales_payment_history', array('sales_bill_id' => $billdata['id']));
										foreach($paymentlist as $payment) {
									?>
									<tr>
										<td><?php echo $count++; ?></td>
										<td class="text-weight-semibold text-dark"><?php echo get_type_name_by_id('staff', $payment['payment_by']) ; ?></td>
										<td><?php echo get_type_name_by_id('payment_types', $payment['pay_via']) ; ?></td>
										<td><?php echo html_escape($payment['remarks']); ?></td>
										<td><?php echo _d($payment['paid_on']); ?></td>
										<td class="text-center"><?php echo html_escape($currency_symbol . $payment['amount']); ?></td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
						<div class="invoice-summary text-right mt-lg">
							<div class="row">
								<div class="col-lg-5 pull-right">
									<ul class="amounts">
										<li><?php echo translate('total'); ?> : <?php echo currencyFormat($total_amount); ?></li>
										<li><?php echo translate('discount'); ?> : <?php echo currencyFormat($total_discount); ?></li>
										<li><?php echo translate('paid_amount'); ?> : <?php echo currencyFormat($total_paid); ?></li>
										<li>
											<strong><?php echo translate('due'); ?> :</strong> 
											<?php
												$f = new NumberFormatter("en", NumberFormatter::SPELLOUT);
												echo currencyFormat($due_amount) . ' </br>( ' . ucwords($f->format($due_amount)) . ' )';
											?>
										</li>
									</ul>
								</div>
							</div>
						</div>
					</div>
					<div class="text-right mr-lg hidden-print">
						<button class="btn btn-default" onClick="fn_printElem('payment_print')"><i class="fas fa-print"></i> <?php echo translate('print'); ?></button>
					</div>
				</div>
			</div>
<?php endif; ?>
			
<?php if($billdata['payment_status'] != 3 && get_permission('purchase_payment', 'is_add')): ?>
			<!-- add payment form -->
			<div id="add_payment" class="tab-pane <?php echo ($active_tab == 3 ? 'active' : ''); ?>">
				<?php echo form_open_multipart('inventory/add_sales_payment', array('class' => 'form-horizontal form-bordered frm-submit-data')); ?>
					<input type="hidden" name="sales_bill_id" value="<?php echo html_escape($billdata['id']); ?>">
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('paid_on'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" data-plugin-datepicker data-plugin-options='{"todayHighlight" : true}' name="paid_date" value="<?php echo date('Y-m-d'); ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('amount'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="number" class="form-control" name="payment_amount" value="<?php echo set_value('payment_amount', $due_amount); ?>"
							placeholder="<?php echo translate('enter_payment_amount'); ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('pay_via'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label" for="input-file-now"><?php echo translate('attach_document'); ?></label>
						<div class="col-md-6">
							<input type="file" name="attach_document" class="dropify" data-height="80" data-allowed-file-extensions="*" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
						<div class="col-md-6 mb-md">
							<textarea name="remarks" rows="2" class="form-control" placeholder="<?php echo translate('write_your_remarks'); ?>"></textarea>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-3">
								<button class="btn btn-default" type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><?php echo translate('payment'); ?></button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php endif; ?>
		</div>
	</div>
</section>