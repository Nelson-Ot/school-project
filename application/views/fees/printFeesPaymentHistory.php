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
			<li><strong><?=translate('sub_total')?> :</strong> <?=currencyFormat($total_paid + $total_discount); ?></li>
			<li><strong><?=translate('discount')?> :</strong> <?=currencyFormat($total_discount); ?></li>
			<li><strong><?=translate('paid')?> :</strong> <?=currencyFormat($total_paid); ?></li>
			<li><strong><?=translate('fine')?> :</strong> <?=currencyFormat($total_fine); ?></li>
			<li>
				<strong><?=translate('total_paid')?> (<?=translate('with_fine')?>) : </strong> 
				<?php
				$numberSPELL = "";
				$grand_paid = number_format($total_paid + $total_fine, 2, '.', '');
				if ($extINTL == true) {
					$numberSPELL = ' </br>( ' . ucwords($spellout->format($grand_paid)) . ' )';
				}
				echo currencyFormat(($total_paid + $total_fine)). $numberSPELL;
				?>
			</li>
		</ul>
	</div>
</div>
