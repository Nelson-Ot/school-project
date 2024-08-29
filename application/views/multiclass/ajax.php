<?php 
$this->db->select('*');
$this->db->from('enroll');
$this->db->where('student_id', $student_id);
$this->db->where('session_id', get_session_id());
$this->db->order_by('id', 'asc');
$result = $this->db->get()->result();
foreach ($result as $key => $row) {
	?>
<tr class="iadd">
	<td style="min-width: 160px;">
		<div class="form-group">
			<?php
				$arrayClass = $this->app_lib->getClass($row->branch_id);
				echo form_dropdown("multiclass[$key][class_id]", $arrayClass, $row->class_id, "class='form-control' onchange='getSection(this.value, " . $key . ")'
				data-plugin-selectTwo data-width='100%' ");
			?>
			<span class="error"></span>
		</div>
	</td>
	<td style="min-width: 160px;">
		<div style="display: flex; align-items: flex-start;">
			<div class="form-group">
				<?php
					$arraySection = $this->app_lib->getSections($row->class_id);
					echo form_dropdown("multiclass[$key][section_id]", $arraySection, $row->section_id, "class='form-control' 
					data-plugin-selectTwo data-width='100%' id='section_id_" . $key . "' ");
				?>
				<span class="error"></span>
			</div>
<?php if ($key != 0): ?>
			<button type="button" class="btn btn-danger removeTR ml-sm"><i class="fas fa-times"></i> </button>
<?php endif ?>
		</div>
	</td>
</tr>
<?php } ?>
<script type="text/javascript">
	var lenght_div = <?php echo count($result) ?>;
</script>