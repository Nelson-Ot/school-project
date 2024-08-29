<div class="row">
	<div class="col-md-12">
<?php if (is_superadmin_loggedin()): ?>
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('select_ground')?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate'));?>
			<div class="panel-body">
				<div class="row mb-sm">
					<div class="col-md-offset-3 col-md-6">
						<div class="form-group">
							<label class="control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' onchange='getClassByBranch(this.value)'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
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
<?php endif; ?>
		<?php if (!empty($branch_id)):?>
		<section class="panel appear-animation" data-appear-animation="<?=$global_config['animations'] ?>" data-appear-animation-delay="100">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-user-graduate"></i> <?php echo translate('class_&_section') . " " . translate('reports') ;?></h4>
			</header>
			<div class="panel-body mb-md">
				<table class="table table-bordered table-condensed table-hover table-export">
					<thead>
						<tr>
							<th><?=translate('sl')?></th>
							<th><?=translate('class')?></th>
							<th><?=translate('section')?></th>
							<th><?=translate('total') . " " . translate('students') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						$classes = $this->db->where('branch_id', $branch_id)->get('class')->result();
						foreach ($classes as $key => $class) {
							$total_student = $this->db->select('count(id) as total')->where(array('class_id' => $class->id, 'branch_id' => $branch_id, 'session_id' => get_session_id()))->get('enroll')->row()->total;
							?>
						<tr>
							<td><?php echo $count++; ?></td>
							<td><?php echo $class->name; ?></td>
							<td><?php
								$sections = $this->db->get_where("sections_allocation", array('class_id' => $class->id))->result();
								foreach ($sections as $section) {
									$students = $this->db->select('count(id) as total')->where(array('class_id' => $class->id, 'section_id' =>  $section->section_id, 'branch_id' => $branch_id, 'session_id' => get_session_id()))->get('enroll')->row()->total;
									echo get_type_name_by_id('section', $section->section_id) . " (" . ($students == 0 ? $students : str_pad($students, 2, "0", STR_PAD_LEFT)) .")<br>";
								}
								?></td>
							<td><?php echo ($total_student == 0 ? $total_student : str_pad($total_student, 2, "0", STR_PAD_LEFT)); ?></td>
						</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>
		</section>
		<?php endif;?>
	</div>
</div>