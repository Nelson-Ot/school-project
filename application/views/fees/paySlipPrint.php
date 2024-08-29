<style type="text/css">
@media print {
	.invoice-summary ul.amounts li {
		padding: 1px !important;
		border-bottom: #444444 1px solid;
	}
	.invoice table.table > tbody tr > td, 
	.invoice table.table > thead tr > th {
	    border-color: #444444 !important;
	    border-width: 1px !important;
	}
}
</style>
<?php
$record_array = json_decode($record);
$currency_symbol = $global_config['currency_symbol'];
$basic = $this->fees_model->getInvoiceBasic($studentID);
?>
<div class="row">
<?php for ($i=0; $i < 3; $i++) { ?>
<div class="col-xs-4">
	<div class="invoice">
		<?php if ($i == 0) {
			echo "<h4 class='text-center mb-none'>Student Copy</h4>";
		} elseif($i == 1) {
			echo "<h4 class='text-center mb-none'>Bank Copy</h4>";
		} elseif($i == 2) {
			echo "<h4 class='text-center mb-none'>Office Copy</h4>";
		}
		?>
		
		<div class="bill-info">
			<div class="row">
				<div class="col-xs-12">
					<div class="bill-data">
						<address style="text-align: center;">
							<?php 
							echo "<strong>" . $basic['school_name'] . "</strong><br/>";
							echo $basic['school_address'] . "<br/>";
							echo $basic['school_mobileno'] . "<br/>";
							echo $basic['school_email'] . "<br/>";
							?>
						</address>
						<div class="row">
							<div class="invoice-summary text-left mt-xs">
								<ul class="amounts">
									<li><?php echo "<strong>" . translate('date') . ' :</strong> ' . _d(date('Y-m-d')); ?></li>
									<li><?php echo "<strong>" . translate('student_name') . ' :</strong> ' . $basic['first_name'] . ' ' . $basic['last_name'] ?></li>
									<li><?php echo "<strong>" . translate('register_no') . ' :</strong> ' . $basic['register_no'] ?></li>
									<li><?php echo "<strong>" . translate('class') . ' :</strong> ' . $basic['class_name'] . " (" . $basic['section_name'] . ')'; ?></li>
									<li><?php echo "<strong>" . translate('father_name') . ' :</strong> ' . $basic['father_name'] ?></li>
								</ul>
							</div>
						</div>
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
						<th id="cell-price" class="text-weight-semibold"><?=translate("amount")?></th>
					</tr>
				</thead>
				<tbody>
					<?php
						$count = 1;
						$total_fine = 0;
						$total_discount = 0;
						$total_paid = 0;
						$total_balance = 0;
						$total_amount = 0;
			            $this->db->where_in('h.id', array_column($record_array, 'payment_id'));
				        $this->db->select('h.*,t.name');
				        $this->db->from('fee_payment_history as h');
				        $this->db->join('fees_type as t', 't.id = h.type_id', 'left');
			            $paymentHistory = $this->db->get()->result();
						foreach ($paymentHistory as $key => $row) {
							$paid = $row->amount;
							$discount = $row->discount;
							$fine = $row->fine;
							$total_paid += $paid;
							$total_discount += $discount;
							$total_fine += $fine;
							?>
					<tr>
						<td><?php echo $count++;?></td>
						<td class="text-weight-semibold text-dark"><?php echo $row->name ?></td>
						<td><?php echo currencyFormat($paid);?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
		</div>
		<div class="invoice-summary text-right mt-lg">
			<div class="row">
				<div class="col-lg-8">
					<ul class="amounts">
						<li><strong><?=translate('sub_total')?> :</strong> <?=currencyFormat($total_paid + $total_discount); ?></li>
						<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
						<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
						<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
						<li>
							<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
							<?php
							$grand_paid = currencyFormat($total_paid + $total_fine);
							echo $grand_paid;
							?>
						</li>
					</ul>
				</div>
			</div>
		</div>
		<div class="text-center mt-md">Generated at <?php echo _d(date("Y-m-d")) . ", " . date("h:i A"); ?></div>
	</div>
</div>
<?php } ?>
</div>
