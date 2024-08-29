<?php  $widget = (is_superadmin_loggedin() ? 4 : 6); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
				<?php if (is_superadmin_loggedin() ): ?>
					<div class="col-md-4">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
				<?php endif; ?>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('class')?> <span class="required">*</span></label>
							<?php
								$arrayClass = $this->app_lib->getClass($branch_id);
								echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' onchange='getSectionByClass(this.value)'
								required data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
					<div class="col-md-<?php echo $widget; ?> mb-sm">
						<div class="form-group">
							<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
							<?php
								$arraySection = $this->app_lib->getSections(set_value('class_id'));
								echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='section_id' required
								data-plugin-selectTwo data-width='100%'");
							?>
						</div>
					</div>
				</div>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?=translate('filter')?></button>
					</div>
				</div>
			</footer>
			<?php echo form_close();?>
		</section>

		<?php if (isset($students)):?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-user-graduate"></i> <?php echo translate('student_list');?></h4>
			</header>
			<div class="panel-body mb-md">
				<table class="table table-bordered table-condensed table-hover table-export">
					<thead>
						<tr>
							<th class="no-sort"><?=translate('photo')?></th>
							<th><?=translate('name')?></th>
							<th><?=translate('class')?></th>
							<th><?=translate('register_no')?></th>
							<th><?=translate('mobile_no')?></th>
							<th><?=translate('guardian_name')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach($students as $row): ?>
						<tr>
							<td class="center"><img src="<?php echo get_image_url('student', $row['photo']); ?>" height="50"></td>
							<td class="<?=($row['active'] == 0 ? 'text-danger' : '')?>"><?php echo $row['fullname'];?></td>
							<td><?php echo $row['class_details']; ?></td>
							<td><?php echo $row['register_no'];?></td>
							<td><?php echo $row['mobileno'];?></td>
							<td><?php echo (!empty($row['parent_id']) ? get_type_name_by_id('parent', $row['parent_id']) : 'N/A');?></td>
							<td class="action">
								<button class="btn btn-circle icon btn-default" data-toggle="tooltip" data-original-title="<?=translate('add_class')?>" data-loading-text="<i class='fas fa-spinner fa-spin'></i>" onclick="multiClassView('<?=$row['student_id']?>', this)"><i class="fa fa-plus"></i></button>
							</td>
						</tr>
						<?php endforeach;?>
					</tbody>
				</table>
			</div>
		</section>
		<?php endif;?>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-lg modal-block-primary mfp-hide" id="quickView">
	<section class="panel">
	<?php echo form_open('multiclass/saveData', array('class' => 'frm-submit' )); ?>
		<header class="panel-heading">
			<h4 class="panel-title">
				<i class="fa-solid fa-sitemap"></i> <?=translate('class') . " " . translate('list')?>
			</h4>
		</header>
		<div class="panel-body">
			<input type="hidden" name="student_id" id="studentID_multi" value="" />
			<input type="hidden" name="branch_id" id="branchID_multi" value="<?php echo $branch_id ?>" />
			<div class="table-responsive">
				<table class="table table-bordered mt-md nowrap" id="tableID">
					<thead>
						<th><?php echo translate('class'); ?> <span class="required">*</span></th>
						<th><?php echo translate('section'); ?></th>
					</thead>
					<tbody id="class_entry_append">
					</tbody>
				</table>
			</div>
			<button type="button" class="btn btn-default mt-xs mb-md" onclick="append_timetable_entry()">
				<i class="fas fa-plus-circle"></i> <?=translate('add_more')?>
			</button>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" type="submit"><?=translate('save')?></button>
					<button class="btn btn-default modal-dismiss"><?=translate('close')?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close();?>
	</section>
</div>

<script type="text/javascript">
	function multiClassView(id, elem) {
		$('#studentID_multi').val(id);
		var btn = $(elem);
	    $.ajax({
	        url: base_url + 'multiclass/ajaxClassList',
	        type: 'POST',
	        data: {student_id: id},
	        dataType: 'html',
	        beforeSend: function () {
	            btn.button('loading');
	        },
	        success: function (res) {
	            $('#class_entry_append').html(res);
				$('#class_entry_append [data-plugin-selecttwo]').each(function() {
					var $this = $(this);
					$this.themePluginSelect2({});
				});
	            mfp_modal('#quickView');
	            btn.tooltip("hide");
	        },
	        error: function (xhr) {
	            btn.button('reset');
	        },
	        complete: function () {
	            btn.button('reset');
	        }
	    });
	}

	$("#class_entry_append").on('click', '.removeTR', function () {
		$(this).parent().parent().parent().remove();
	});

	function append_timetable_entry(){
		$("#class_entry_append").append(getDynamicInput(lenght_div));
		lenght_div++;
		
		$(".selectTwo").each(function() {
			var $this = $(this);
			$this.themePluginSelect2({});
		});
	}

	function getDynamicInput(value) {
		var row = "";
		row += '<tr class="iadd">';
		row += '<input type="hidden" name="old_id[' + value + ']" class="form-control" value="0" >';
		row += '<td style="min-width: 160px;"><div class="form-group">';
		row += '<select id="class_id_' + value + '" name="multiclass[' + value + '][class_id]" class="form-control selectTwo" data-width="100%" onchange="getSection(this.value, ' + value + ')" >';
<?php 
$arrayClass = $this->app_lib->getClass($branch_id);
foreach ($arrayClass as $key => $row): ?>
		row += '<option value="<?php echo $key ?>"><?php echo html_escape($row) ?></option>';
<?php endforeach; ?>
		row += '</select>';
		row += '<span class="error"></span></div></td>';
		row += '<td style="min-width: 160px;"><div style="display: flex; align-items: flex-start;"><div class="form-group">';
		row += '<select id="section_id_' + value + '" name="multiclass[' + value + '][section_id]" class="form-control selectTwo" data-width="100%">';
		row += '<option value=""><?php echo translate("select_class_first") ?></option>';
		row += '</select><span class="error"></span></div>';
		row += '<button type="button" class="btn btn-danger removeTR ml-sm"><i class="fas fa-times"></i> </button>';
		row += '</div></td>';
		row += '</tr>';
		return row;
	}

	function getSection(classID, rowid) {
    	$("#section_id_" + rowid).html("<option value=''><?php echo translate('exploring'); ?>...</option>");
        $.ajax({
            url: base_url + 'ajax/getSectionByClass',
            type: 'POST',
            data: {
                class_id: classID,
                all : 0,
                multi : 0
            },
            beforeSend: function () {
                //some code here
            },
            success: function (response) {
                $("#section_id_" + rowid).html(response);
            },
            complete: function () {
               //some code here 
            }
        });	
	}
</script>
