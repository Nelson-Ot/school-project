<input type="hidden" name="exam_id" id="examID" value="<?php echo $exam->id ?>">
<div class="form-group text-center col-md-12">
	<h4><?php echo translate('payment_amount')?>: <strong><?php echo currencyFormat($exam->fee) ?></strong></h4>
</div>
<div class="form-group mt-md">
	<div class="col-md-12">
		<label class="control-label"><?=translate('payment_method')?> <span class="required">*</span></label>
		<?php
			$payvia_list = array('' => translate('select_payment_method'));
			if ($config['paypal_status'] == 1)
				$payvia_list['paypal'] = 'Paypal';
			if ($config['stripe_status'] == 1)
				$payvia_list['stripe'] = 'Stripe';
			if ($config['payumoney_status'] == 1)
				$payvia_list['payumoney'] = 'PayUmoney';
			if ($config['paystack_status'] == 1)
				$payvia_list['paystack'] = 'Paystack';
			if ($config['razorpay_status'] == 1)
				$payvia_list['razorpay'] = 'Razorpay';
			if ($config['sslcommerz_status'] == 1)
				$payvia_list['sslcommerz'] = 'SSLcommerz';
			if ($config['jazzcash_status'] == 1)
				$payvia_list['jazzcash'] = 'Jazzcash';
			if ($config['midtrans_status'] == 1)
				$payvia_list['midtrans'] = 'Midtrans';
			if ($config['flutterwave_status'] == 1)
				$payvia_list['flutterwave'] = 'Flutter Wave';
            if ($config['paytm_status'] == 1)
                $payvia_list['paytm'] = 'Paytm';
            if ($config['toyyibpay_status'] == 1)
                $payvia_list['toyyibpay'] = 'toyyibPay';
            if ($config['payhere_status'] == 1)
                $payvia_list['payhere'] = 'Payhere';
			if ($config['nepalste_status'] == 1)
				$payvia_list['nepalste'] = 'Nepalste';
			echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' data-width='100%' id='payVia'
			data-minimum-results-for-search='Infinity' ");
		?>
		<span class="error"></span>
	</div>
</div>

<div class="form-group payu"  style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Name <span class="required">*</span></label>
		<input type="text" class="form-control" name="payer_name" value="<?php echo $getUser['name'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group payu" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Email <span class="required">*</span></label>
		<input type="email" class="form-control" name="email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group payu" style="display: none;">
	<div class="col-md-12">
	<label class="control-label">Phone <span class="required">*</span></label>
		<input type="text" class="form-control" name="phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>

<div class="form-group toyyibpay" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Email <span class="required">*</span></label>
		<input type="email" class="form-control" name="toyyibpay_email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group toyyibpay" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Phone <span class="required">*</span></label>
		<input type="text" class="form-control" name="toyyibpay_phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>

<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Name <span class="required">*</span></label>
		<input type="text" class="form-control" name="sslcommerz_name" value="<?php echo $getUser['name'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Email <span class="required">*</span></label>
		<input type="email" class="form-control" name="sslcommerz_email" value="<?php echo $getUser['email'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Address <span class="required">*</span></label>
		<input type="text" class="form-control" name="sslcommerz_address" value="<?php echo $getUser['address'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">Post Code <span class="required">*</span></label>
		<input type="text" class="form-control" name="sslcommerz_postcode" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12">
		<label class="control-label">State <span class="required">*</span></label>
		<input type="text" class="form-control" name="sslcommerz_state" value="<?php echo $getUser['state'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>
<div class="form-group sslcommerz" style="display: none;">
	<div class="col-md-12 mb-md">
		<label class="control-label">Phone <span class="required">*</span></label>
		<input type="text" class="form-control" name="sslcommerz_phone" value="<?php echo $getUser['mobileno'] ?>" autocomplete="off" />
		<span class="error"></span>
	</div>
</div>