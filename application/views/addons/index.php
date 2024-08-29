<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="<?=(empty($validation_error) ? 'active' : '') ?>">
				<a href="#list" data-toggle="tab"><i class="fas fa-list-ul"></i> <?=translate('addon') . " " . translate('list')?></a>
			</li>
			<li class="<?=(!empty($validation_error) ? 'active' : '') ?>">
				<a href="#create" data-toggle="tab"><i class="fas fa-boxes"></i> <?=translate('install') . " " . translate('addon')?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div id="list" class="tab-pane <?=(empty($validation_error) ? 'active' : '')?>">
				<div class="mb-md">
					<table class="table table-bordered table-hover table-condensed mb-none table-export nowrap">
						<thead>
							<tr>
								<th width="50"><?=translate('sl')?></th>
								<th><?=translate('name')?></th>
								<th><?=translate('version')?></th>
								<th><?=translate('installed')?></th>
								<th><?=translate('last_upgrade')?></th>
								<th><?=translate('action')?></th>
							</tr>
						</thead>
						<tbody>
						<?php 
							$count = 1;
							foreach($addonList as $row):
							?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $row->name;?></td>
								<td><?php echo wordwrap($row->version, 1, '.', true);?></td>
								<td><?php echo empty($row->created_at) ? '-' : _d($row->created_at);?></td>
								<td><?php echo empty($row->created_at) ? '-' : _d($row->created_at);?></td>
								<td><a href="<?=base_url('addons/update/'.$row->prefix)?>" class="btn btn-default btn-circle"><i class="fas fa-undo"></i> Update Check</a> </td>
							</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
			</div>
			<div class="tab-pane <?=(!empty($validation_error) ? 'active' : '')?>" id="create">
				<?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered addon_install')); ?>
					<div class="row">
					<div class="col-md-offset-2 col-md-8">
						<div id="installed_messages"></div>
					</div>
					</div>
					<div class="form-group mt-md">
						<label class="col-md-3 control-label"><?=translate('addon_purchase_code')?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="purchase_code" autocomplete="off" value="<?=set_value('purchase_code')?>" autocomplete="off" />
							<span class="error"><?=form_error('purchase_code') ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Zip File <span class="required">*</span></label>
						<div class="col-md-6 mb-md">
							<input type="file" name="zip_file" class="dropify" data-height="80" data-allowed-file-extensions="*" data-default-file="" />
							<span class="error"><?=form_error('zip_file') ?></span>
						</div>
					</div>
					<footer class="panel-footer mt-lg">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<button type="submit" class="btn btn-default btn-block" name="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-file-import"></i> <?=translate('install_now')?></button>
							</div>
						</div>	
					</footer>
				<?php echo form_close();?>
			</div>
		</div>
	</div>
</section>

<script type="text/javascript">
    $("form.addon_install").on('submit', function(e)
    {
        e.preventDefault();
        var $this =  $(this);
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
                console.log(data.error);
                $('.error').html("");
                if (data.status == "fail") {
                    $.each(data.error, function (index, value) {
                        $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                    });
                    btn.button('reset');
                } else {
		            $('#installed_messages').append('<p>' + data.message + '</p>');
                    setTimeout(function () {
                        window.location.reload();
                    }, 5000);
                }
            },
		    complete: function (data) {
		        
		    },
            error: function () {
                btn.button('reset');
            }
        });
    });
</script>