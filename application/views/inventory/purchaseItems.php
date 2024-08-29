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
						<select data-plugin-selectTwo class="form-control purchase_product selectTwo" data-width="100%" name="purchases[0][product]" id="product0">
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

<script type="text/javascript">
	function getDynamicInput(value) {
		var html_row = "";
		html_row += '<tr id="row_' + value + '">';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="product' + value + '" name="purchases[' + value + '][product]" class="form-control purchase_product" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
<?php foreach ($productlist as $product): ?>
		html_row += '<option value="<?php echo html_escape($product['id']) ?>" ><?php echo html_escape($product['name']) . ' (' . html_escape($product['code']) . ')' ?></option>';
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