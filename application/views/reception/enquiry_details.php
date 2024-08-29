<?php $followUP = $this->reception_model->follow_up_details($row['id']); ?>
<div class="row">
	<div class="col-md-4">
		<section class="panel">
			<div class="panel-heading">
                 <div class="panel-btn">
                 </div>
				<h4 class="panel-title">
					<i class="fas fa-info-circle"></i> <?=translate('enquiry') . " " . translate('details')?>
				</h4>
			</div>
			<div class="panel-body">
				<div class="table-responsive">
					<table class="table text-dark">
						<tbody>
							<tr class="b-top-none">
								<td colspan="2">Status</td>
								<td class="text-left">
									<?php 
									if (!empty($followUP)) {
										$status = $followUP['status'];
									} else {
										$status = 1;
									}
									$getStatus = $this->reception_model->getStatus();
									if ($status == 1) { ?>
										<span class="label label-success-custom"><i class="far fa-check-square"></i> <?php echo $getStatus[$status]; ?></span>
									<?php } else { ?>
										<span class="label label-danger-custom"><i class="far fa-check-square"></i> <?php echo $getStatus[$status]; ?></span>
									<?php } ?>
								</td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('enquiry') . " " . translate('date')?> :</td>
								<td class="text-left"><?php echo $row['date'];?></td>
							</tr>
						<?php if (!empty($followUP['date'])) { ?>
							<tr>
								<td colspan="2"><?=translate('last') . " " .  translate('follow_up') . " " . translate('date')?> :</td>
								<td class="text-left"><?php echo _d($followUP['date']); ?></td>
							</tr>
						<?php } ?>
						<?php if (!empty($followUP['next_date'])) { ?>
							<tr>
								<td colspan="2"><?=translate('next') . " " .  translate('follow_up') . " " . translate('date')?> :</td>
								<td class="text-left"><?php echo _d($followUP['next_date']); ?></td>
							</tr>
						<?php } ?>
							<tr>
								<td colspan="2"><?=translate('name')?> :</td>
								<td class="text-left"><?php echo $row['name']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('gender')?> :</td>
								<td class="text-left"><?php echo ($row['gender'] == 1 ? translate('male') : translate('female')); ?></td>
							</tr>
						<?php if (!empty($row['birthday'])) { ?>
							<tr>
								<td colspan="2"><?=translate('birthday')?> :</td>
								<td class="text-left"><?php echo _d($row['birthday']); ?></td>
							</tr>
						<?php } ?>
							<tr>
								<td colspan="2"><?=translate('class_applying_for')?> :</td>
								<td class="text-left"><?php echo get_type_name_by_id('class', $row['class_id']); ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('father_name')?> :</td>
								<td class="text-left"><?php echo $row['father_name']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('mother_name')?> :</td>
								<td class="text-left"><?php echo $row['mother_name']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('mobile_no')?> :</td>
								<td class="text-left"><?php echo $row['mobile_no']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('email')?> :</td>
								<td class="text-left"><?php echo empty($row['email']) ? "N/A" : $row['email']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('no_of_child')?> :</td>
								<td class="text-left"><?php echo $row['no_of_child']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('enquiry') . " " . translate('reference')?> :</td>
								<td class="text-left"><?php echo get_type_name_by_id('enquiry_reference', $row['reference_id']) ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('response') . " " . translate('type')?> :</td>
								<td class="text-left"><?php echo get_type_name_by_id('enquiry_response', $row['response_id']) ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('enquiry') . " " . translate('response')?> :</td>
								<td class="text-left"><?php echo empty($row['response']) ? "N/A" : $row['response']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('note')?> :</td>
								<td class="text-left"><?php echo empty($row['note']) ? "N/A" : $row['note']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('previous_school')?> :</td>
								<td class="text-left"><?php echo empty($row['previous_school']) ? "N/A" : $row['previous_school']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('assigned')?> :</td>
								<td class="text-left"><?php echo get_type_name_by_id('staff', $row['assigned_id']); ?></td>
							</tr>
							<tr>
								<td colspan="2"><?=translate('created_by')?> :</td>
								<td class="text-left"><?php echo get_type_name_by_id('staff', $row['created_by']); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</section>
	</div>
	<div class="col-md-8">
	<?php if (get_permission('follow_up', 'is_add')): ?>
		<div class="row">
			<div class="col-md-12">
				<section class="panel">
					<div class="panel-heading">
						<h4 class="panel-title">
							<i class="fas fa-plus-circle"></i> <?=translate('add') . " " . translate('follow_up')?>
						</h4>
					</div>
					<?php echo form_open($this->uri->uri_string(), array('class' => 'frm-submit'));?>
					<input type="hidden" name="enquiry_id" value="<?php echo $row['id'] ?>">
					<div class="panel-body">
						<div class="row mb-lg mt-sm">
							<div class="col-sm-6 mt-sm">
							  <div class="form-group">
							      <label class="control-label"><?=translate('follow_up') . " " . translate('date')?> <span class="required">*</span></label>
							      <input type="text" name="date" class="form-control date" value="<?php echo date('Y-m-d') ?>" data-plugin-datepicker autocomplete="off">
							      <span class="error"></span>
							  </div>
							</div>
							<div class="col-sm-6 mt-sm">
							  <div class="form-group">
							       <label class="control-label"><?=translate('next') . " " .  translate('follow_up') . " " . translate('date')?> <span class="required">*</span></label>
							      <input type="text" name="follow_up_date" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' autocomplete="off" class="form-control date" value="" >
							  	  <span class="error"></span>
							  </div>
							</div>
							<div class="col-sm-12 mt-sm">
							  <div class="form-group">
							      <label class="control-label"><?=translate('response')?></label>
							      <textarea name="response" class="form-control"></textarea>   
							  </div>
							</div>
							<div class="col-sm-6 mt-sm">
							  <div class="form-group">
							      <label class="control-label"><?=translate('status')?> <span class="required">*</span></label>
									<?php
										$enquiryResponse = $this->reception_model->getStatus();
										echo form_dropdown("status", $enquiryResponse, set_value('status'), "class='form-control' data-width='100%' id='responseID'
										data-plugin-selectTwo data-minimum-results-for-search='Infinity'");
									?>
									<span class="error"></span>
							  </div>
							</div>
							<div class="col-sm-6 mt-sm">
							  <div class="form-group">
							      <label class="control-label"><?=translate('note')?></label> 
							      <textarea name="note" class="form-control"></textarea>
							  </div>
							</div>
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-10 col-md-2">
								<button type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn-default btn-block"> <i class="fas fa-plus-circle"></i> <?=translate('save')?></button>
							</div>
						</div>
					</footer>
					<?php echo form_close(); ?>
				</section>
			</div>
		</div>
	<?php endif; ?>
		<div class="row">
			<div class="col-md-12">
				<section class="panel">
					<div class="panel-heading">
						<h4 class="panel-title">
							<i class="fas fa-phone"></i> <?=translate('follow_up') . " " . translate('list')?>
						</h4>
					</div>
					<div class="panel-body">
						<table class="table table-bordered table-hover mb-none table-condensed table-export">
							<thead>
								<tr>
									<th><?=translate('sl')?></th>
									<th><?=translate('follow_up') . " " . translate('date')?></th>
									<th><?=translate('next') . " " . translate('follow_up') . " " . translate('date')?></th>
									<th><?=translate('response')?></th>
									<th><?=translate('note')?></th>
									<th><?=translate('status')?></th>
									<th><?=translate('action')?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$this->db->where('enquiry_id', $row['id']);
								$this->db->order_by('enquiry_id', 'asc');
								$result = $this->db->get('enquiry_follow_up')->result();
								if (!empty($result)) { 
									$count = 1;
									
									foreach ($result as $key => $r) {
									?>
									<tr>
										<td><?php echo $count++; ?></td>
										<td><?php echo _d($r->date); ?></td>
										<td><?php echo _d($r->next_date); ?></td>
										<td><?php echo $r->response; ?></td>
										<td><?php echo $r->note; ?></td>
										<td>
										<?php if ($r->status == 1) { ?>
											<span class="label label-success-custom"><i class="far fa-check-square"></i> <?php echo $getStatus[$r->status]; ?></span>
										<?php } else { ?>
											<span class="label label-danger-custom"><i class="far fa-check-square"></i> <?php echo $getStatus[$r->status]; ?></span>
										<?php } ?>
										</td>
										<td>
										<?php if (get_permission('follow_up', 'is_delete')): ?>
											<?php echo btn_delete('reception/follow_up_delete/' . $r->id);?>
										<?php endif; ?>
										</td>
									</tr>
								<?php } } ?>
							</tbody>
						</table>
					</div>
				</section>
			</div>
		</div>
	</div>

</div>