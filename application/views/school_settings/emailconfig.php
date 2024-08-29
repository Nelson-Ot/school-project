<div class="row">
	<div class="col-md-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-md-9">
		<section class="panel">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#email_config" data-toggle="tab"><i class="far fa-envelope"></i> <?=translate('email_config')?></a>
					</li>
					<li>
						<a href="<?=base_url('school_settings/emailtemplate' . $url)?>"><i class="fas fa-sitemap"></i> <?=translate('email_triggers')?></a>
					</li>
				</ul>
				<div class="tab-content">
					<div id="email_config" class="tab-pane active">
						<?php echo form_open('school_settings/saveEmailConfig' . $url, array('class' => 'form-horizontal form-bordered frm-submit-msg')); ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('system_email')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input class="form-control" value="<?=$config['email']?>" name="email" type="email" autocomplete="off" placeholder="All Outgoing Email Will be sent from This Email Address.">
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">Email Protocol</label>
							<div class="col-md-6">
								<?php
								$array = array(
									"mail" => "PHP Mail",
									"smtp" => "SMTP Mail"
								);
								echo form_dropdown("protocol", $array, $config['protocol'], "class='form-control' data-plugin-selectTwo id='emailProtocol'
								data-width='100%' data-minimum-results-for-search='Infinity' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">SMTP Host <span class="required">*</span></label>
							<div class="col-md-6">
								<input class="form-control smtp" value="<?=$config['smtp_host']?>" name="smtp_host" type="text" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">SMTP Username <span class="required">*</span></label>
							<div class="col-md-6">
								<input class="form-control smtp" value="<?=$config['smtp_user']?>" name="smtp_user" type="text" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							 <label class="col-md-3 control-label">SMTP Password <span class="required">*</span></label>
							<div class="col-md-6">
								<input name="smtp_pass" value="<?=$config['smtp_pass']?>" class="form-control smtp" type="password" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">SMTP Port <span class="required">*</span></label>
							<div class="col-md-6">
								<input class="form-control smtp" value="<?=$config['smtp_port']?>" name="smtp_port" type="text" autocomplete="off" />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">SMTP Secure</label>
							<div class="col-md-6">
								<?php
								$array = array(
									"" 		=> "No",
									"tls" 	=> "TLS",
									"ssl" 	=> "SSL"
								);
								echo form_dropdown("smtp_encryption", $array, $config['smtp_encryption'], "class='form-control smtp' data-plugin-selectTwo data-width='100%'
								data-minimum-results-for-search='Infinity' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label">SMTP Auth</label>
							<div class="col-md-6 mb-md">
								<?php
								$array = array(
									"true" => "Yes",
									"false" => "No"
								);
								echo form_dropdown("smtp_auth", $array, $config['smtp_auth'], "class='form-control smtp' data-plugin-selectTwo data-width='100%'
								data-minimum-results-for-search='Infinity' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<footer class="panel-footer">
							<div class="row">
								<div class="col-md-2 col-sm-offset-3">
									<button type="submit" class="btn btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
										<i class="fas fa-plus-circle"></i> <?=translate('save')?>
									</button>
								</div>
							</div>
						</footer>
						<?php echo form_close(); ?>
					</div>
				</div>
			</div>
		</section>

        <section class="panel pg-fw">
            <div class="panel-body">
            	<?php echo form_open('school_settings/send_test_email' . $url, array('class' => 'form-horizontal form-bordered frm-submit')); ?>
	                <h5 class="chart-title mb-xs">Send Test Email</h5>
	                <div class="mt-lg">
						<div class="form-group">
							<label class="col-md-3 control-label">Email <span class="required">*</span></label>
							<div class="col-md-6">
								<input class="form-control" value="" name="test_email" type="text" placeholder="Email Address" autocomplete="off" />
								<span class="error"></span>
							</div>
							<div class="col-md-offset-3 col-md-6 mb-md mt-sm text-muted">
								<span>* You can use this function to make sure your SMTP settings are set correctly.</span>
							<?php if ($this->session->flashdata('test-email-success')) { ?>
								<div class="alert alert-success mt-md"><i class="far fa-check-circle"></i> It looks like your SMTP settings are set correctly. Please check your email now.</div>
							<?php } if ($this->session->flashdata('test-email-error')) { ?>
								<div class="alert alert-danger mt-md"><i class="fas fa-bug"></i> <?php echo $this->session->flashdata('test-email-error'); ?></div>
							<?php } ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-offset-3 col-md-6">
								<button type="submit" class="btn btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"> <i class="far fa-envelope"></i> Test Now</button>
							</div>
						</div>
	                </div>
            	</form>
            </div>
        </section>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function () {
		var protocol = "<?=$config['protocol']?>"
		if (protocol !== "smtp") {
			$(".smtp").prop('disabled', true);
		}
		
		$('#emailProtocol').on('change', function(){
			var mode = $(this).val();
			if(mode == 'smtp'){
				$(".smtp").prop('disabled', false);
			} else {
				$(".smtp").prop('disabled', true);
			}
		});
	});
</script>