<div class="row">
	<div class="col-md-12 mt-md">
		<div class="table-responsive">
			<table class="table table-hover text-dark">
				<tbody>
					<tr class="b-top-none">
						<td colspan="2"><strong><?=translate('branch')?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('branch', $postal['branch_id']) ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('type'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['type'] == 1 ? translate('dispatch') : translate('receive'); ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('sender') . " " . translate('title'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['sender_title']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('receiver') . " " . translate('title'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['receiver_title']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('reference_no'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['reference_no']; ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('date'); ?> :</strong></td>
						<td class="text-left"><?php echo _d($postal['date']); ?></td>
					</tr>

					<tr>
						<td colspan="2"><strong><?php echo translate('address'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['address'] ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('created_by'); ?> :</strong></td>
						<td class="text-left"><?php echo get_type_name_by_id('staff', $postal['created_by']) ?></td>
					</tr>
					<tr>
						<td colspan="2"><strong><?php echo translate('confidential'); ?> :</strong></td>
						<td class="text-left"><?php echo $postal['confidential'] == 1 ? translate('yes') . ' <i class="fas fa-circle-check"></i>' : translate('no'); ?></td>
					</tr>
				<?php if (!empty($postal['file'])) { ?>
					<tr>
						<td colspan="2"><strong><?php echo translate('document') . " " . translate('file'); ?> :</strong></td>
						<td class="text-left"><a href="<?php echo base_url('reception/download/postal?file=' . $postal['file']) ?>" class="btn btn-default btn-circle"><i class="fas fa-cloud-download-alt"></i> <?php echo translate('download') ?></a></td>
					</tr>
				<?php } ?>
				</tbody>
			</table>
		</div>
		<hr>
		<p><i class="far fa-clock"></i> <?php echo translate('created_at') . " : " . _d($postal['created_at']) . " " . date("h:i A", strtotime($postal['created_at'])); ?>  <span class="pull-right"><i class="far fa-clock"></i> <?php echo translate('updated_at') . " : " . _d($postal['updated_at']) . " " . date("h:i A", strtotime($postal['updated_at'])); ?></span></p>
	</div>
</div>