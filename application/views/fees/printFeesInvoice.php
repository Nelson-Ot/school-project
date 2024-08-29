<?php
$currency_symbol = $global_config['currency_symbol'];
$extINTL = extension_loaded('intl');
if ($extINTL == true) {
	$spellout = new NumberFormatter("en", NumberFormatter::SPELLOUT);
}
?>
<div class="row">
	<div class="col-lg-5 pull-right">
		<ul class="amounts">
			<li><strong><?=translate('grand_total')?> :</strong> <?=currencyFormat($total_amount); ?></li>
			<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
			<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
			<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
			<?php if ($total_balance != 0): ?>
			<li><strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) :</strong> <?=currencyFormat($total_paid + $total_fine); ?></li>
			<li>
				<strong><?=translate('balance')?> : </strong> 
				<?php
				$numberSPELL = "";
				$total_balance = number_format($total_balance, 2, '.', '');
				if ($extINTL == true) {
					$numberSPELL = ' </br>( ' . ucwords($spellout->format($total_balance)) . ' )';
				}
				echo currencyFormat($total_balance) . $numberSPELL;
				?>
			</li>
			<?php else:
				$paidWithFine = number_format(($total_paid + $total_fine), 2, '.', '');
				?>
			<li>
				<strong><?=translate('total_paid')?> (with fine) : </strong> 
				<?php
				$numberSPELL = "";
				if ($extINTL == true) {
					$numberSPELL = ' </br>( ' . ucwords($spellout->format($paidWithFine)) . ' )';
				}
				echo currencyFormat(($total_paid + $total_fine)) . $numberSPELL;
				?>
			</li>
			<?php endif; ?>
		</ul>
	</div>
</div>
