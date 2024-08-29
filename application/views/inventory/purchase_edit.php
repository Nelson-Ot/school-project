<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('inventory/purchase'); ?>"><i class="fas fa-list-ul"></i> <?php echo translate('purchase') . ' ' . translate('list'); ?></a>
			</li>
			<li class="active">
				<a href="#edit" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('edit') . ' ' . translate('purchase'); ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div id="edit" class="tab-pane active">
				<?php echo form_open('inventory/purchase_edit_save', array('id' => 'frmSubmit')); ?>
					<input type="hidden" name="purchase_bill_id" value="<?php echo html_escape($purchaselist['id']); ?>">
					<div class="form-horizontal form-bordered">
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('supplier'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$supplierlist = $this->app_lib->getSelectByBranch('product_supplier', $branch_id);
									echo form_dropdown("supplier_id", $supplierlist, $purchaselist['supplier_id'], "class='form-control' data-plugin-selectTwo id='supplierID'
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
									echo form_dropdown("store_id", $storelist, $purchaselist['store_id'], "class='form-control' data-plugin-selectTwo id='storeID'
									data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('bill_no'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="bill_no" id='bill_no' value="<?php echo html_escape($purchaselist['bill_no']); ?>" />
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
									echo form_dropdown("purchase_status", $status_list, $purchaselist['purchase_status'], "class='form-control' id='purchase_status'
									data-plugin-selectTwo data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="date" id='date' value="<?php echo html_escape($purchaselist['date']); ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
							<div class="col-md-6 mb-lg">
								<textarea class="form-control" rows="2" name="remarks"><?php echo html_escape($purchaselist['remarks']); ?></textarea>
							</div>
						</div>
					</div>

					<div class="table-responsive">
						<table class="table table-bordered mt-md nowrap" id="tableID">
							<thead>
								<th><?php echo translate('product'); ?> <span class="required">*</span></th>
								<th><?php echo translate('unit') . " " . translate('price'); ?></th>
								<th><?php echo translate('quantity'); ?> <span class="required">*</span></th>
								<th><?php echo translate('discount'); ?></th>
								<th><?php echo translate('total') . " " . translate('price'); ?></th>
							</thead>
							<tbody>
								<?php
								$count = 1;
								$this->db->order_by('id', 'ASC');
								$bill_details = $this->db->get_where('purchase_bill_details', array('purchase_bill_id' => $purchaselist['id']))->result();
								if(count($bill_details)){
								foreach ($bill_details as $key => $product):


									$this->db->select('product_unit.name');
									$this->db->from('product');
									$this->db->join('product_unit','product_unit.id = product.purchase_unit_id', 'inner');
									$this->db->where('product.id', $product->product_id);
									$unitName = $this->db->get()->row()->name;

								?>
								<tr>
									<td class="min-w-sm">
										<div class="form-group">
											<input type="hidden" name="purchases[<?php echo $key; ?>][old_bill_details_id]" value="<?php echo html_escape($product->id); ?>">
											<input type="hidden" name="purchases[<?php echo $key; ?>][old_product_id]" value="<?php echo html_escape($product->product_id); ?>">
											<select data-plugin-selectTwo class="form-control purchase_product" data-width="100%" name="purchases[<?php echo $key; ?>][product]" id="product<?php echo $key; ?>">
											<option value=""><?php echo translate('select'); ?></option>
											<?php foreach ($productlist as $value) { ?>
												<option value="<?php echo html_escape($value['id']); ?>" <?php echo ($value['id'] == $product->product_id ? 'selected' : ''); ?>><?php echo html_escape($value['name']) . ' ('. html_escape($value['code']) . ')'; ?></option>
											<?php } ?>
											</select>
											<span class="error"></span>
										</div>
									</td>
									<td class="min-w-sm">
										<div class="form-group">
											<input type="text" class="form-control purchase_unit_price" name="purchases[<?php echo $key; ?>][unit_price]" readonly
											value="<?php echo html_escape($product->unit_price); ?>" />
										</div>
									</td>
									<td class="min-w-xs">
										<div class="form-group">
											<div class="input-group">
												<input type="hidden" name="purchases[<?php echo $key; ?>][old_quantity]" value="<?php echo html_escape($product->quantity); ?>">
												<input type="text" class="form-control purchase_quantity" name="purchases[<?php echo $key; ?>][quantity]" id="quantity<?php echo $key; ?>" value="<?php echo html_escape($product->quantity); ?>" />
												<span class="input-group-addon"><?php echo $unitName; ?></span>
											</div>
											<span class="error"></span>
										</div>
									</td>
									<td class="min-w-md">
										<div class="form-group">
											<input type="number" class="form-control purchase_discount" name="purchases[<?php echo $key; ?>][discount]" value="<?php echo html_escape($product->discount); ?>" />
										</div>
									</td>
									<td class="min-w-md">
										<div class="form-group">
											<input type="text" class="form-control net_sub_total" name="purchases[<?php echo $key; ?>][net_sub_total]" value="<?php echo ($product->sub_total - $product->discount); ?>" readonly />
											<input type="hidden" class="sub_total" name="purchases[<?php echo $key; ?>][sub_total]" value="<?php echo html_escape($product->sub_total); ?>">
										</div>
									</td>
								</tr>
								<?php endforeach; } ?>
							</tbody>
							<tfoot>
								<tr>
									<td colspan="1"><button type="button" class="btn btn-default" onclick="addRows()"> <i class="fas fa-plus-circle"></i> <?php echo translate('add_rows'); ?></button></td>
									<td class="text-right" colspan="3"><b><?php echo translate('net_total'); ?> :</b></td>
									<td class="text-right">
										<input type="text" id="netGrandTotal" class="text-right form-control" name="net_grand_total" value="<?php echo html_escape($purchaselist['total'] - $purchaselist['discount']); ?>" readonly />
										<input type="hidden" id="grandTotal" name="grand_total" value="<?php echo html_escape($purchaselist['total']); ?>">
										<input type="hidden" id="totalDiscount" name="total_discount" value="<?php echo html_escape($purchaselist['discount']); ?>">
										<input type="hidden" name="purchase_paid" value="<?php echo html_escape($purchaselist['paid']); ?>">
									</td>
								</tr>
							</tfoot>
						</table>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-9 col-md-3">
								<button type="submit" name="update" id="savebtn" value="1" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
	var count = <?php echo count($bill_details); ?>;
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
					var unit_price = isNaN(result.price) ? 0 : parseFloat(result.price);
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

	function addRows() {
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

	function getDynamicInput(value) {
		var html_row = "";
		html_row += '<tr id="row_' + value + '">';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="product' + value + '" name="purchases[' + value + '][product]" class="form-control purchase_product">';
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
		html_row += '<input id="quantity' + value + '"type="number" name="purchases[' + value + '][quantity]" class="form-control purchase_quantity" value="1" />';
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

</script>