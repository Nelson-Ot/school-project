<div class="table-responsive">
	<table class="table table-bordered table-hover mt-md" id="tableID">
		<thead>
			<th><?php echo translate('category'); ?> <span class="required">*</span></th>
			<th><?php echo translate('product'); ?> <span class="required">*</span></th>
			<th><?php echo translate('quantity'); ?> <span class="required">*</span></th>
		</thead>
		<tbody>
			<tr id="row_0">
				<td class="min-w-lg">
					<div class="form-group">
						<?php
						echo form_dropdown("sales[0][category]", $categorylist, "", "class='form-control selectTwo' onchange='getProductByCategory(this.value, 0)' data-width='100%' id='category0'
						data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
						?>
						<span class="error"></span>
					</div>
				</td>
				<td class="min-w-lg">
					<div class="form-group">
						<select data-plugin-selectTwo class="form-control sale_product selectTwo" data-width="100%" name="sales[0][product]" id="product0">
							<option value=""><?php echo translate('first_select_the_category'); ?></option>
						</select>
						<span class="error"></span>
					</div>
				</td>
				<td class="min-w-sm">
					<div class="form-group">
						<div class="input-group">
							<input type="text" class="form-control purchase_quantity" name="sales[0][quantity]" value="1" id="quantity0" autocomplete="off" />
							<span class="input-group-addon">-</span>
						</div>
						<span class="quantity_remain"></span>
						<span class="error"></span>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="6">
					<button type="button" class="btn btn-default" onclick="addRows()"> <i class="fas fa-plus-circle"></i> <?php echo translate('add_rows'); ?></button>
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
		html_row += '<select id="category' + value + '" name="sales[' + value + '][category]" class="form-control select2_in"  onchange="getProductByCategory(this.value, ' + value + ')" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
<?php
$categorylist = $this->db->where('branch_id', $branch_id)->get('product_category')->result_array();;
foreach($categorylist as $category): ?>
		html_row += '<option value="<?php echo html_escape($category['id']) ?>"><?php echo html_escape($category['name']); ?></option>';
<?php endforeach; ?>
		html_row += '</select>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="product' + value + '" name="sales[' + value + '][product]" class="form-control sale_product" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
		html_row += '</select>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '</div></td>';
		html_row += '<td class="min-w-md"><div class="form-group" style="float: left; width: 70% !important;">';
		html_row += '<div class="input-group">';
		html_row += '<input id="quantity' + value + '" type="number" name="sales[' + value + '][quantity]" class="form-control purchase_quantity" autocomplete="off"  value="1" />';
		html_row += '<span class="input-group-addon">-</span></div>';
		html_row += '<span class="quantity_remain"></span>';
		html_row += '<span class="error"></span></div>';
		html_row += '<button type="button" class="btn btn-danger" onclick="deleteRow(' + value + ')" style="float: right; max-width: 30%"><i class="fas fa-times"></i> </button>';
		html_row += '</td>';
		html_row += '</tr>';
		return html_row;
	}
</script>