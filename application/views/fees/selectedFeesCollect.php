<thead>
	<th><?=translate('fees_type')?> <span class="required">*</span></th>
	<th><?=translate('date')?> <span class="required">*</span></th>
	<th><?=translate('amount')?> <span class="required">*</span></th>
	<th><?=translate('discount')?> <span class="required">*</span></th>
	<th><?=translate('fine')?> <span class="required">*</span></th>
	<th><?=translate('payment_method')?> <span class="required">*</span></th>
<?php
$colspan = 7;
$links = $this->fees_model->get('transactions_links', array('branch_id' => $branch_id), true);
if ($links['status'] == 1) {
	$colspan +=1;
?>
	<th><?=translate('account')?> <span class="required">*</span></th>
<?php } ?>
	<th><?=translate('remarks')?></th>
</thead>
<tbody>
	<input type="hidden" name="branch_id" value="<?php echo $branch_id; ?>">
	<input type="hidden" name="student_id" value="<?php echo $student_id; ?>">
<?php
$total_fine = 0;
$total_discount = 0;
$total_paid = 0;
$total_balance = 0;
$total_amount = 0;
$count = 0;
foreach ($record_array as $key => $value) {
	$b = $this->fees_model->getBalance($value->allocationID, $value->feeTypeID);
	$balance = $b['balance'];
	if ($balance != 0) {
	$count++;
	$fine = $this->fees_model->feeFineCalculation($value->allocationID, $value->feeTypeID);
	$fine = abs($fine - $b['fine']);
	$typeDetails = $this->db->select('name,fee_code')->where('id', $value->feeTypeID)->get('fees_type')->row();
 ?>
	<tr>
		
		<input type="hidden" name="collect_fees[<?php echo $key ?>][allocation_id]" value="<?php echo $value->allocationID; ?>">
		<input type="hidden" name="collect_fees[<?php echo $key ?>][type_id]" value="<?php echo $value->feeTypeID; ?>">
		<td class="fee-modal">
			<p style="margin-bottom: 2px; margin-left:5px"><?php echo $typeDetails->name; ?></p>
			<span style="color: #606060; margin-left: 8px;">- <?php echo $typeDetails->fee_code; ?></span>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control datepicker" name="collect_fees[<?php echo $key ?>][date]" value="<?=date('Y-m-d')?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control" name="collect_fees[<?php echo $key ?>][amount]" value="<?=number_format($balance, 2, '.', '')?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control" name="collect_fees[<?php echo $key ?>][discount_amount]" value="0" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<input type="text" class="form-control" name="collect_fees[<?php echo $key ?>][fine_amount]" value="<?php echo number_format($fine, 2, '.', ''); ?>" autocomplete="off" />
				<span class="error"></span>
			</div>
		</td>
		<td class="fee-modal">
			<div class="form-group">
				<?php
					$payvia_list = $this->app_lib->getSelectList('payment_types');
					echo form_dropdown("collect_fees[$key][pay_via]", $payvia_list, 1, "class='form-control selectTwo' data-width='100%'
					data-minimum-results-for-search='Infinity' ");
				?>
				<span class="error"></span>
			</div>
		</td>
    <?php if ($links['status'] == 1) { ?>
		<td class="fee-modal">
			<div class="form-group">
	            <?php
	            $accounts_list = $this->app_lib->getSelectByBranch('accounts', $branch_id);
	            echo form_dropdown("collect_fees[$key][account_id]", $accounts_list, $links['deposit'], "class='form-control selectTwo' data-width='100%'");
	            ?>
	            <span class="error"></span>
        	</div>
		</td>
    <?php } ?>
		<td class="fee-modal">
			<textarea name="collect_fees[<?php echo $key ?>][remarks]" rows="1" class="form-control" placeholder="<?=translate('write_your_remarks')?>"></textarea>
		</td>
	</tr>
<?php } }
if ($count == 0) {
	echo '<tr><td colspan="'.$colspan.'"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
}
?>

</tbody>

<script type="text/javascript">
	
$(function() {
  $( ".adatepicker" ).datepicker({ 

        format: "yyyy-mm-dd",
        autoclose: true,
        orientation: "bottom",
        endDate: "today"

   });
});


</script>