<div class="zoom-anim-dialog modal-block modal-block-primary" id="editModal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('agent'); ?>
			</h4>
		</header>
		<?php echo form_open_multipart('school_settings/saveWhatsappAgent', array('class' => 'frmsub ')); ?>
			<div class="panel-body">
				<input type="hidden" name="agent_id" value="<?php echo $whatsapp['id'] ?>">
				<div class="form-group">
					<label class="control-label"><?php echo translate('name'); ?> <span class="required">*</span></label>
					<input type="text" name="name" class="form-control" value="<?php echo $whatsapp['agent_name'] ?>" autocomplete="off" />
					<span class="error"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><?php echo translate('designation'); ?> <span class="required">*</span></label>
					<input type="text" class="form-control" value="<?php echo $whatsapp['agent_designation'] ?>" autocomplete="off" name="designation"/>
					<span class="error"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><?php echo translate('whataspp_number'); ?> <span class="required">*</span></label>
					<input type="text" class="form-control" value="<?php echo $whatsapp['whataspp_number'] ?>" placeholder="Enter your WhatsApp number with country code." autocomplete="off" name="whataspp_number"/>
					<span class="error"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><?php echo translate('time_slot'); ?> <span class="required">*</span></label>
					<div class="row">
						<div class="col-xs-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" name="start_time" data-plugin-timepicker class="form-control" value="<?php echo $whatsapp['start_time'] ?>" />
							</div>
						</div>
						<div class="col-xs-6">
							<div class="input-group">
								<span class="input-group-addon"><i class="far fa-clock"></i></span>
								<input type="text" name="end_time" data-plugin-timepicker class="form-control" value="<?php echo $whatsapp['end_time'] ?>" />
							</div>
						</div>
					</div>
					<span class="error"></span>
				</div>
				<div class="form-group">
					<label class="control-label"><?php echo translate('weekend'); ?> <span class="required">*</span></label>
					<?php
						$arrayDay = array(
							"0" => translate('no'),
							"sunday" => "Sunday",
							"monday" => "Monday",
							"tuesday" => "Tuesday",
							"wednesday" => "Wednesday",
							"thursday" => "Thursday",
							"friday" => "Friday",
							"saturday" => "Saturday"
						);
						echo form_dropdown("weekend", $arrayDay, strtolower($whatsapp['weekend']), "class='form-control' required
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
					?>
					<span class="error"></span>
				</div>
				<div class="form-group">
					<label for="input-file-now"><?=translate('photo')?></label>
					<input type="file" name="user_photo" class="dropify" data-default-file="<?=get_image_url('whatsapp_agent', $whatsapp['agent_image'])?>" />
					<span class="error"></span>
					<input type="hidden" name="old_user_photo" value="<?=$whatsapp['agent_image']?>">
				</div>
				<div class="form-group ml-xs mb-lg">
					<label class="control-label"><?=translate('active')?></label>
		            <div class="material-switch mt-xs">
		                <input class="switch_menu" id="agent_active" name="agent_active" <?php echo $whatsapp['enable'] == 1 ? 'checked' : ''; ?> type="checkbox" checked />
		                <label for="agent_active" class="label-primary"></label>
		            </div>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-12 text-right">
						<button type="submit" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
							<i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?>
						</button>
						<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
					</div>
				</div>
			</footer>
		<?php echo form_close(); ?>
	</section>
</div>
<script type="text/javascript">
	$(document).ready(function () {
		$('#editModal [data-plugin-selecttwo]').each(function() {
			var $this = $(this);
			$this.themePluginSelect2({});
		});
		$('#editModal [data-plugin-timepicker]').each(function() {
			var $this = $(this);
			$this.themePluginTimePicker({});
		});
		$("#editModal .dropify").dropify();

        $('.frmsub').on('submit', function(e){
            e.preventDefault();
            $this = $(this);
            var btn = $this.find('[type="submit"]');
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else if (data.status == "access_denied") {
                        window.location.href = base_url + "dashboard";
                    } else {
                        location.reload(true);
                    }
                },
                complete: function (data) {
                    btn.button('reset'); 
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
	});
</script>