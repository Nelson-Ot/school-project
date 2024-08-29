<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="<?php echo (!isset($validation_error) ? 'active' : ''); ?>">
				<a href="#list" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('news') . " " . translate('list'); ?></a>
			</li>
	<?php if (get_permission('frontend_news', 'is_add')) { ?>
			<li class="<?php echo (isset($validation_error) ? 'active' : ''); ?>">
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('add') . " " . translate('news'); ?></a>
			</li>
	<?php } ?>
		</ul>
		<div class="tab-content">
			<div id="list" class="tab-pane <?php echo (!isset($validation_error) ? 'active' : ''); ?>">
				<table class="table table-bordered table-hover table-condensed table_default">
					<thead>
						<tr>
							<th><?php echo translate('sl'); ?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?php echo translate('image'); ?></th>
							<th><?php echo translate('title'); ?></th>
							<th class="no-sort"><?=translate('show_website')?></th>
							<th><?php echo translate('action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						if (!empty($newslist)) {
							foreach ($newslist as $row):
								?>
						<tr>
							<td><?php echo $count++; ?></td>
<?php if (is_superadmin_loggedin()): ?>
							<td><?php echo $row['branch_name'];?></td>
<?php endif; ?>
							<td><img class="img-border" src="<?php echo $this->news_model->get_image_url($row['image']); ?>" width="80"/></td>
							<td><?php echo $row['title']; ?></td>
							<td>
							<?php if (get_permission('event', 'is_edit')) { ?>
								<div class="material-switch ml-xs">
									<input class="news_website" id="websiteswitch_<?=$row['id']?>" data-id="<?=$row['id']?>" name="evt_switch_website<?=$row['id']?>" 
									type="checkbox" <?php echo ($row['show_web'] == 1 ? 'checked' : ''); ?> />
									<label for="websiteswitch_<?=$row['id']?>" class="label-primary"></label>
								</div>
							<?php } ?>
							</td>
							<td class="action">
								<?php if (get_permission('frontend_news', 'is_edit')): ?>
									<a href="<?php echo base_url('frontend/news/edit/' . $row['id']); ?>" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>"> 
										<i class="fas fa-pen-nib"></i>
									</a>
								<?php endif; if (get_permission('frontend_news', 'is_delete')): ?>
									<?php echo btn_delete('frontend/news/delete/' . $row['id']); ?>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
				</table>
			</div>
	<?php if (get_permission('frontend_news', 'is_add')) { ?>
			<div class="tab-pane <?php echo (isset($validation_error) ? 'active' : ''); ?>" id="create">
			    <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit-data')); ?>
					<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<div class="col-md-7">
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, "", "class='form-control' data-plugin-selectTwo id='branch_id'
								data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('title'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="news_title" value="<?php echo set_value('news_title'); ?>" />
							<span class="error"><?php echo form_error('news_title'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="date" value="<?=set_value('date')?>" autocomplete="off" data-plugin-datepicker
							data-plugin-options='{ "todayHighlight" : true }' />
							<span class="error"><?php echo form_error('date'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('description'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<textarea name="description" class="summernote"><?php echo set_value('description'); ?></textarea>
							<span class="error"><?php echo form_error('description'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('image'); ?> <span class="required">*</span></label>
						<div class="col-md-4">
							<input type="file" name="image" class="dropify" data-height="150" />
							<span class="error"><?php echo form_error('image'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('show_website')?></label>
						<div class="col-md-7">
							<div class="material-switch ml-xs">
								<input id="switch_1" name="show_website" checked type="checkbox" />
								<label for="switch_1" class="label-primary"></label>
							</div>
						</div>
					</div>
					<footer class="panel-footer mt-lg">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>	
					</footer>
				<?php echo form_close(); ?>
			</div>
	<?php } ?>
		</div>
	</div>
</section>

<script type="text/javascript">
	$(".news_website").on("change", function() {
		var state = $(this).prop('checked');
		var id = $(this).data('id');
		if (state != null) {
			$.ajax({
				type: 'POST',
				url: base_url + "frontend/news/show_website",
				data: {
					id: id,
					status: state
				},
				dataType: "json",
				success: function (data) {
					if(data.status == true) {
						alertMsg(data.msg);
					}
				}
			});
		}
	});
</script>