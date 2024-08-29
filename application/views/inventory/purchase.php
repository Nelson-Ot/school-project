<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#productlist" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('purchase') . ' ' . translate('list'); ?></a>
			</li>
<?php if (get_permission('product_purchase', 'is_add')): ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('add') . ' ' . translate('purchase'); ?></a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="productlist" class="tab-pane active mb-md">
				<div class="export_title"><?php echo translate('purchase') . " " . translate('report'); ?></div>
				<table class="table table-bordered table-hover table-condensed table-export nowrap" cellspacing="0" width="100%">
					<thead>
						<tr>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?php echo translate('bill_no'); ?></th>
							<th><?php echo translate('supplier') . " " . translate('name'); ?></th>
							<th><?php echo translate('purchase') . " " . translate('status'); ?></th>
							<th><?php echo translate('payment') . " " . translate('status'); ?></th>
							<th><?php echo translate('purchase') . " " . translate('date'); ?></th>
							<th><?php echo translate('net') . " " . translate('payable'); ?></th>
							<th><?php echo translate('paid'); ?></th>
							<th><?php echo translate('due'); ?></th>
							<th><?php echo translate('remarks'); ?></th>
							<th><?php echo translate('action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($purchaselist)){ foreach ($purchaselist as $row): ?>	
						<tr>
<?php if (is_superadmin_loggedin()): ?>
							<td><?php echo get_type_name_by_id('branch', $row['branch_id']);?></td>
<?php endif; ?>
							<td><?php echo html_escape($row['bill_no']); ?></td>
							<td><?php echo html_escape($row['supplier_name']); ?></td>
							<td>
								<?php
									$status_list = array(
										'1' => translate('ordered'),
										'2' => translate('received'),
										'3' => translate('pending')
									);
									echo $status_list[$row['purchase_status']];
								?>
							</td>
							<td>
								<?php
									$labelMode = "";
									$status = $row['payment_status'];
									if($status == 1) {
										$status = translate('unpaid');
										$labelMode = 'label-danger-custom';
									} elseif($status == 2) {
										$status = translate('partly_paid');
										$labelMode = 'label-info-custom';
									} elseif($status == 3 || $row['due'] == 0) {
										$status = translate('total_paid');
										$labelMode = 'label-success-custom';
									}
									echo "<span class='label " . $labelMode. "'>" . $status . "</span>";
								?>
							</td>
							<td><?php echo _d($row['date']); ?></td>
							<td><?php echo currencyFormat($row['total'] - $row['discount']); ?></td>
							<td><?php echo currencyFormat($row['paid']); ?></td>
							<td><?php echo currencyFormat($row['due']); ?></td>
							<td><?php echo html_escape($row['remarks']); ?></td>
							<td class="min-w-c">
							<?php if (get_permission('purchase_payment', 'is_add')): ?>
								<a href="<?php echo base_url('inventory/purchase_bill/' . $row['id']); ?>" class="btn btn-circle icon btn-default" data-toggle="tooltip" data-original-title="<?php echo translate('bill_view'); ?>"> <i class="fas fa-credit-card"></i></a>
							<?php endif; ?>
							<?php if (get_permission('product_purchase', 'is_edit')): 
								if ($row['purchase_status'] != 2) { ?>
									<button class="btn btn-circle icon btn-default" data-toggle="tooltip" data-original-title="<?php echo translate('make_received'); ?>" onclick="confirmStock('<?php echo $row['id'] ?>')"><i class="far fa-plus-square"></i></button>
								<?php } ?>
								<a href="<?php echo base_url('inventory/purchase_edit/' . $row['id']); ?>" class="btn btn-circle icon btn-default" data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>"><i class="fas fa-pen-nib"></i></a>
							<?php endif; if (get_permission('product_purchase', 'is_delete')): ?>
								<?php echo btn_delete('inventory/purchase_delete/' . $row['id']); ?>
							<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('product_purchase', 'is_add')) { ?>
			<div id="create" class="tab-pane">
				<?php echo form_open('inventory/purchase_save', array('id' => 'frmSubmit')); ?>
					<div class="form-horizontal form-bordered">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, "", "class='form-control' id='branch_id'
									data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('supplier'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$supplierlist = $this->app_lib->getSelectByBranch('product_supplier', $branch_id);
									echo form_dropdown("supplier_id", $supplierlist, set_value("supplier_id"), "class='form-control' data-plugin-selectTwo id='supplierID'
									data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('store'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$storelist = $this->app_lib->getSelectByBranch('product_store', $branch_id);
									echo form_dropdown("store_id", $storelist, set_value("store_id"), "class='form-control' data-plugin-selectTwo id='storeID'
									data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('bill_no'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="bill_no" value="<?php echo $this->app_lib->get_bill_no('purchase_bill'); ?>" id="bill_no" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('purchase') . " " . translate('status'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$status_list = array(
										'' => translate('select'),
										'1' => translate('ordered'),
										'2' => translate('received'),
										'3' => translate('pending')
									);
									echo form_dropdown("purchase_status", $status_list, set_value("purchase_status"), "class='form-control' data-plugin-selectTwo id='purchase_status'
									data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="date" value="<?php echo date('Y-m-d'); ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' id='date' />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
							<div class="col-md-6 mb-lg">
								<textarea class="form-control" rows="2" name="remarks"></textarea>
							</div>
						</div>
					</div>
					<div id="purchaseItems" style="<?php echo (empty($branch_id) ? 'display: none;' : ''); ?>">
						<?php if (!empty($branch_id)) { ?>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mt-md" id="tableID">
								<thead>
									<th><?php echo translate('product'); ?> <span class="required">*</span></th>
									<th><?php echo translate('unit') . " " . translate('price'); ?></th>
									<th><?php echo translate('quantity'); ?> <span class="required">*</span></th>
									<th><?php echo translate('discount'); ?></th>
									<th><?php echo translate('total') . " " . translate('price'); ?></th>
								</thead>
								<tbody>
									<tr id="row_0">
										<td class="min-w-lg">
											<div class="form-group">
												<select data-plugin-selectTwo class="form-control purchase_product" data-width="100%" name="purchases[0][product]" id="product0">
												<option value=""><?php echo translate('select'); ?></option>
												<?php foreach ($productlist as $value) { ?>
													<option value="<?php echo html_escape($value['id']); ?>"><?php echo html_escape($value['name']) . ' ('. $value['code'] . ')'?></option>
												<?php } ?>
												</select>
												<span class="error"></span>
											</div>
										</td>
										<td class="min-w-sm">
											<div class="form-group">
												<input type="text" class="form-control purchase_unit_price" name="purchases[0][unit_price]" readonly value="0.00" />
											</div>
										</td>
										<td class="min-w-sm">
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control purchase_quantity" name="purchases[0][quantity]" value="1" id="quantity0" />
													<span class="input-group-addon">-</span>
												</div>
												<span class="error"></span>
											</div>
										</td>
										<td class="min-w-md">
											<div class="form-group">
												<input type="number" class="form-control purchase_discount" name="purchases[0][discount]" value="0" />
											</div>
										</td>
										<td class="min-w-md">
											<div class="form-group">
												<input type="text" class="form-control net_sub_total" name="purchases[0][net_sub_total]" value="0.00" readonly />
												<input type="hidden" class="sub_total" name="purchases[0][sub_total]" value="0">
											</div>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="1"><button type="button" class="btn btn-default" onclick="addRows()"> <i class="fas fa-plus-circle"></i> <?php echo translate('add_rows'); ?></button></td>
										<td class="text-right" colspan="3"><b><?php echo translate('net_total'); ?> :</b></td>
										<td class="text-right">
											<input type="text" id="netGrandTotal" class="text-right form-control" name="net_grand_total" value="0.00" readonly />
											<input type="hidden" id="grandTotal" name="grand_total" value="0">
											<input type="hidden" id="totalDiscount" name="total_discount" value="0">
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					<?php } ?>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-9 col-md-3">
								<button type="submit" name="purchase" id="savebtn" value="1" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php } ?>
		</div>
	</div>
</section>

<?php if (!empty($branch_id)) { ?>
<script type="text/javascript">
	function getDynamicInput(value) {
		var html_row = "";
		html_row += '<tr id="row_' + value + '">';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="product' + value + '" name="purchases[' + value + '][product]" class="form-control purchase_product" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
<?php foreach ($productlist as $product): ?>
		html_row += '<option value="<?php echo html_escape($product['id']) ?>" ><?php echo html_escape($product['name']) . ' (' . $product['code'] . ')' ?></option>';
<?php endforeach; ?>
		html_row += '</select>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '</div></td>';
		html_row += '<td><div class="form-group">';
		html_row += '<input type="text" name="purchases[' + value + '][unit_price]" class="form-control purchase_unit_price" readonly value="0.00" />';
		html_row += '</div></td>';
		html_row += '<td><div class="form-group">';
		html_row += '<div class="input-group">';
		html_row += '<input id="quantity' + value + '" type="number" name="purchases[' + value + '][quantity]" class="form-control purchase_quantity" value="1" />';
		html_row += '<span class="input-group-addon">-</span></div>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '</div></td>';
		html_row += '<td><div class="form-group">';
		html_row += '<input type="number" name="purchases[' + value + '][discount]" class="form-control purchase_discount" value="0" />';
		html_row += '</div></td>';
		html_row += '<td class="min-w-md">';
		html_row += '<input type="text" class="form-control net_sub_total" name="purchases[' + value + '][net_sub_total]" value="0.00" readonly style="float: left; width: 70%;" />';
		html_row += '<input type="hidden" class="sub_total" name="purchases[' + value + '][sub_total]" value="0" />';
		html_row += '<button type="button" class="btn btn-danger" onclick="deleteRow(' + value + ')" style="float: right; max-width: 30%"><i class="fas fa-times"></i> </button>';
		html_row += '</td>';
		html_row += '</tr>';
		return html_row;
	}
</script>
<?php } ?>

<script type="text/javascript">
	$('#branch_id').on('change', function() {
		var branchID = $(this).val();
		$.ajax({
			url: "<?=base_url('ajax/getDataByBranch')?>",
			type: 'POST',
			data: {
				branch_id: branchID,
				table : 'product_supplier'
			},
			success: function (data) {
				$('#supplierID').html(data);
			}
		});
		$.ajax({
			url: "<?=base_url('ajax/getDataByBranch')?>",
			type: 'POST',
			data: {
				branch_id: branchID,
				table : 'product_store'
			},
			success: function (data) {
				$('#storeID').html(data);
			}
		});

		$.ajax({
			url: "<?=base_url('inventory/purchaseItems')?>",
			type: 'POST',
			data: { branch_id: branchID },
			success: function (data) {
				$('#purchaseItems').html(data).slideDown();
				$(".selectTwo").each(function() {
					var $this = $(this);
					$this.themePluginSelect2({});
				});
			}
		});
	});

	var count = 1;
	$(document).ready(function() {
		$(document).on('change', '.purchase_product', function() {
			var row = $(this).closest('tr');
			var id = $(this).val();
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {'id' : id},
				url: "<?php echo base_url('inventory/getPurchasePrice'); ?>",
				success: function (result) {
					var unit_price = read_number(result.price);
					row.find('.input-group-addon').html(result.unit);
					var quantity = read_number(row.find('.purchase_quantity').val());
					var discount = read_number(row.find('.purchase_discount').val());
					var total_price = unit_price * quantity;
					row.find('.purchase_unit_price').val(unit_price.toFixed(2));
					var after_discount = total_price - discount;
					row.find('.sub_total').val(total_price.toFixed(2));
					row.find('.net_sub_total').val(after_discount.toFixed(2));
					grandTotalCalculatePur();
				}
			});
		});

		$(document).on('change keyup', '.purchase_quantity, .purchase_discount', function() {
			var row = $(this).closest('tr');
			var quantity = read_number(row.find('.purchase_quantity').val());
			var unit_price = read_number(row.find('.purchase_unit_price').val());
			var discount = read_number(row.find('.purchase_discount').val());
			var total_price = unit_price * quantity;
			var after_discount = total_price - discount;
			row.find('.sub_total').val(total_price.toFixed(2));
			row.find('.net_sub_total').val(after_discount.toFixed(2));
			grandTotalCalculatePur();
		});
	});

	function addRows(){
		var tbody = $('#tableID').children('tbody');
		tbody.append(getDynamicInput(count));
		$("#product" + count).select2({
		    theme: "bootstrap",
		    width: "100%"
		});
		count++;
	}

    function deleteRow(id) {
        $("#row_" + id).remove();
        grandTotalCalculatePur();
    }


	// inventory product purchase grand total calculation
	function grandTotalCalculatePur() {
		var netGrandTotal = 0;
		$(".net_sub_total").each(function() {
			netGrandTotal += read_number(this.value)
		});
		$("#netGrandTotal").val(netGrandTotal.toFixed(2));

		var grandTotal = 0;
		$(".sub_total").each(function() {
			grandTotal += read_number(this.value)
		});
		$("#grandTotal").val(grandTotal.toFixed(2));

		var total_discount = 0;
		$(".purchase_discount").each(function() {
			total_discount += read_number(this.value)
		});
		$("#totalDiscount").val(total_discount);
	}

	$("#frmSubmit").on('submit', (function (e) {
	    e.preventDefault();
	    var btn = $("#savebtn");
	    btn.button('loading');
	    $.ajax({
	        url: $(this).attr('action'),
	        type: "POST",
	        data: $(this).serialize(),
	        dataType: 'json',
	        success: function (data) {
	            if (data.status == "fail") {
	            	console.log(data.error);
	                $.each(data.error, function (index, value) {
	                	$('#' + index).parents('.form-group').find('.error').html(value);
	                });
	                btn.button('reset');
	            } else {
	               window.location.href = data.url;
	            }
	        },
	        error: function () {
	            //  alert("Fail")
	        }
	    });
	}));

		function confirmStock(id) {
			swal({
				title: "<?php echo translate('are_you_sure')?>",
				text: "<?php echo translate('add_products_to_stock_list')?>",
				type: "warning",
				showCancelButton: true,
				confirmButtonClass: "btn btn-default swal2-btn-default",
				cancelButtonClass: "btn btn-default swal2-btn-default",
				confirmButtonText: "<?php echo translate('yes_continue')?>",
				cancelButtonText: "<?php echo translate('cancel')?>",
				buttonsStyling: false,
			}).then((result) => {
				if (result.value) {
					$.ajax({
						url: base_url + "inventory/purchaseMakeReceived/" + id,
						type: "POST",
						success:function(data) {
							swal({
							title: "<?php echo translate('successfully')?>",
							text: "Added to stock",
							buttonsStyling: false,
							showCloseButton: true,
							focusConfirm: false,
							confirmButtonClass: "btn btn-default swal2-btn-default",
							type: "success"
							}).then((result) => {
								if (result.value) {
									location.reload();
								}
							});
						}
					});
				}
			});
		}

</script>

