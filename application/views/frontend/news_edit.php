<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?php echo base_url('frontend/news/index'); ?>"><i class="fas fa-list-ul"></i> <?php echo translate('news') . " " . translate('list'); ?></a>
			</li>
			<li class="active">
				<a href="#edit" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('news'); ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="edit">
			    <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit-data')); ?>
					<input type="hidden" name="news_id" value="<?php echo $gallery['id']; ?>">
					<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('branch')?> <span class="required">*</span></label>
						<div class="col-md-7">
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, $gallery['branch_id'], "class='form-control' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<?php endif; ?>

					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('title'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="news_title" value="<?php echo set_value('news_title', $gallery['title']); ?>" />
							<span class="error"><?php echo form_error('news_title'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<input type="text" class="form-control" name="date" value="<?=set_value('date', $gallery['date'])?>" autocomplete="off" data-plugin-datepicker
							data-plugin-options='{ "todayHighlight" : true }' />
							<span class="error"><?php echo form_error('date'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('description'); ?> <span class="required">*</span></label>
						<div class="col-md-7">
							<textarea name="description" class="summernote"><?php echo set_value('description', $gallery['description']); ?></textarea>
							<span class="error"><?php echo form_error('description'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('image'); ?> <span class="required">*</span></label>
						<div class="col-md-4">
							<input type="hidden" name="old_photo" value="<?php echo $gallery['image']; ?>">
							<input type="file" name="image" class="dropify" data-height="150" data-default-file="<?php echo $this->news_model->get_image_url($gallery['image']); ?>" />
							<span class="error"><?php echo form_error('image'); ?></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?=translate('show_website')?></label>
						<div class="col-md-7">
							<div class="material-switch ml-xs">
								<input id="switch_1" name="show_website" type="checkbox" <?php echo $gallery['show_web'] == 1 ? 'checked' : ""; ?> />
								<label for="switch_1" class="label-primary"></label>
							</div>
						</div>
					</div>
					<footer class="panel-footer mt-lg">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-edit"></i> <?php echo translate('update'); ?>
								</button>
							</div>
						</div>	
					</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>