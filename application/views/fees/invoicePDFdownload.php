<style type="text/css">
	@page {
		background: #fff;
	}
    .invoice {
		background: #fff;
		width: 1000px;
		position: relative;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        margin-bottom: 20px;
    }

    .table thead tr {
        border: 1.5px solid #4c4c4c;
        border-right: 0;
        border-left: 0;
    }

    strong {
    	font-weight: bold;
    }

    .table td {
        background: #fff;
        border-bottom: 1px solid #ddd !important;
    }

	.table tbody tr td {
	    line-height: 1.6;
	}

    .text-weight-semibold {
    	font-weight: bold;
    }

    .table tr th {
        font-weight: bold;
    }

    .text-center {
        text-align: center !important;
    }

    .invoice-col {
        position: relative;
        min-height: 1px;
        padding-right: 15px;
        padding-left: 15px;
    }

    .invoice-col {
        float: left;
        width: 45%;
    }

    .invoice-col.right {
        float: right;
        text-align: right;
    }

    .text-right {
        text-align: right;
    }

	.invoice-summary ul.amounts li {
		background: #fff;
	}
	
	.label1 {
		border: 0;
	}
</style>
<?php
$extINTL = extension_loaded('intl');
if ($extINTL == true) {
	$spellout = new NumberFormatter("en", NumberFormatter::SPELLOUT);
}

if (count($student_array)) {
	foreach ($student_array as $key => $value) {
		$invoice = $this->fees_model->getInvoiceStatus($value);
		$basic = $this->fees_model->getInvoiceBasic($value);
?>
<div class="invoice">
	<header class="clearfix">
		<div class="row">
			<div class="invoice-col">
				<div class="ib">
					<img src="<?=$this->application_model->getBranchImage($basic['branch_id'], 'printing-logo')?>" alt="RamomCoder Img" />
				</div>
			</div>
			<div class="invoice-col text-right">
				<h5 class="mt-none mb-none text-dark">Invoice No #<?=$invoice['invoice_no']?></h5>
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
						echo "<span class='label1 " . $labelmode . " '>" . $status . "</span>";
					?>
				</p>
			</div>
		</div>
	</header>
	<div class="bill-info">
		<div class="row">
			<div class="invoice-col">
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
			<div class="invoice-col right">
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
		<table class="table invoice-items table-hover mb-none" id="invoiceSummary">
			<thead>
				<tr class="text-dark" style="border-top: 0;">
					<th id="cell-count" class="text-weight-semibold">#</th>
					<th id="cell-item" class="text-weight-semibold"><?=translate("fees_type")?></th>
					<th class="text-weight-semibold"><?=translate("due_date")?></th>
					<th class="text-weight-semibold"><?=translate("status")?></th>
					<th class="text-weight-semibold"><?=translate("amount")?></th>
					<th class="text-weight-semibold"><?=translate("discount")?></th>
					<th class="text-weight-semibold"><?=translate("fine")?></th>
					<th class="text-weight-semibold"><?=translate("paid")?></th>
					<th class="text-center text-weight-semibold"><?=translate("balance")?></th>
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
						echo "<span class='label1 " . $labelmode . " '>" . $status . "</span>";
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
		<div style="width: 260px; float: right;">
			<ul class="amounts">
				<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
				<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
				<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
				<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
				<?php if ($total_balance != 0): ?>
				<li><strong><?=translate('total_paid')?> (with fine) :</strong> <?=currencyFormat($total_paid + $total_fine); ?></li>
				<li style="border-bottom: 0;">
					<strong><?=translate('balance')?> : </strong> 
					<?php
					$numberSPELL = "";
					if ($extINTL == true) {
						$numberSPELL = ' <p>( ' . ucwords($spellout->format(number_format($total_balance, 2, '.', ''))) . ' )</p>';
					}
					echo currencyFormat($total_balance) . $numberSPELL;
					?>
				</li>
				<?php else: 
					$paidWithFine = ($total_paid + $total_fine);
					?>
				<li style="border-bottom: 0;">
					<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
					<?php
					$numberSPELL = "";
					if ($extINTL == true) {
						$numberSPELL = ' <p>( ' . ucwords($spellout->format(number_format($paidWithFine, 2, '.', ''))) . ' )</p>';
					}
					echo currencyFormat($paidWithFine) . $numberSPELL;
					?>
				</li>
				<?php endif; ?>
			</ul>
		</div>
	</div>
</div>
<?php } } ?>
