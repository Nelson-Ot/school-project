<div class="row">
	<div class="col-md-12 mt-md">
		<div class="table-responsive">
			<table class="table table-hover text-dark">
				<tbody>
					<tr class="b-top-none">
						<td colspan="2"><strong><?=translate('branch')?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('branch', $complaint['branch_id']) ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('complaint') . " " . translate('type'); ?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('complaint_type', $complaint['type_id']); ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('complaint') . " " . translate('name'); ?> :</strong></td>
						<td class="text-left"><?php echo $complaint['name']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('complaint') . " " . translate('mobile_no'); ?> :</strong></td>
						<td class="text-left"><?php echo $complaint['number']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('assign_to'); ?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('staff', $complaint['assigned_id']) ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('date'); ?> :</strong></td>
						<td class="text-left"><?php echo _d($complaint['date']); ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('date'); ?> :</strong></td>
						<td class="text-left"><?php echo _d($complaint['date']); ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('date_of_solution'); ?> :</strong></td>
						<td class="text-left"><?php
									if (empty($complaint['date_of_solution']) || $complaint['date_of_solution'] == "0000-00-00") {
										echo '<span class="label label-danger-custom">' . translate('pending') . '</span>';
									} else {
										echo _d($complaint['date_of_solution']);
									}?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('action'); ?> :</strong></td>
						<td class="text-left"><?php echo empty($complaint['action']) ? 'N/A' :  $complaint['action']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('created_by'); ?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('staff', $complaint['created_by']) ?></td>
					</tr>
				<?php if (!empty($complaint['file'])) { ?>
					<tr>
						<td colspan="2"><strong><?php echo translate('document') . " " . translate('file'); ?> :</strong></td>
						<td class="text-left"><a href="<?php echo base_url('reception/download/complaint?file=' . $complaint['file']) ?>" class="btn btn-default btn-circle"><i class="fas fa-cloud-download-alt"></i> <?php echo translate('download') ?></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<hr>
		<p><i class="far fa-clock"></i> <?php echo translate('created_at') . " : " . _d($complaint['created_at']) . " " . date("h:i A", strtotime($complaint['created_at'])); ?>  <span class="pull-right"><i class="far fa-clock"></i> <?php echo translate('updated_at') . " : " . _d($complaint['updated_at']) . " " . date("h:i A", strtotime($complaint['updated_at'])); ?></span></p>
	</div>
</div>