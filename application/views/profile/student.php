<style type="text/css">
	#my_details .text-dark {
		font-weight: 600;
	}
</style>
<?php
$widget = (is_superadmin_loggedin() ? 3 : 4);
$getParent = $this->student_model->get('parent', array('id' => $student['parent_id']), true);
$branchID = $student['branch_id'];
if (empty($student['previous_details'])) {
	$previous_details = ['school_name' => '', 'qualification' => '', 'remarks' => ''];
} else {
	$previous_details = json_decode($student['previous_details'], true);
}
$currency_symbol = $global_config['currency_symbol'];

$last_name = $this->student_fields_model->getStatusProfile('last_name', $branchID);
$gender = $this->student_fields_model->getStatusProfile('gender', $branchID);
$blood_group = $this->student_fields_model->getStatusProfile('blood_group', $branchID);
$birthday = $this->student_fields_model->getStatusProfile('birthday', $branchID);
$religion = $this->student_fields_model->getStatusProfile('religion', $branchID);
$mother_tongue = $this->student_fields_model->getStatusProfile('mother_tongue', $branchID);
$caste = $this->student_fields_model->getStatusProfile('caste', $branchID);
$present_address = $this->student_fields_model->getStatusProfile('present_address', $branchID); 
$permanent_address = $this->student_fields_model->getStatusProfile('permanent_address', $branchID);
$student_mobile_no = $this->student_fields_model->getStatusProfile('student_mobile_no', $branchID); 
$student_email = $this->student_fields_model->getStatusProfile('student_email', $branchID);
$city = $this->student_fields_model->getStatusProfile('city', $branchID); 
$state = $this->student_fields_model->getStatusProfile('state', $branchID);
$student_photo = $this->student_fields_model->getStatusProfile('student_photo', $branchID);
$previous_school_details = $this->student_fields_model->getStatusProfile('previous_school_details', $branchID);

$personal = false;
if ($last_name['status'] == 1 || $gender['status'] == 1 || $blood_group['status'] == 1 || $birthday['status'] == 1 || $religion['status'] == 1 || $mother_tongue['status'] == 1 || $caste['status'] == 1 || $present_address['status'] == 1 || $permanent_address['status'] == 1 || $student_mobile_no['status'] == 1 || $student_email['status'] == 1 || $city['status'] == 1 || $state['status'] == 1 || $student_photo['status'] == 1) {
	$personal = true;
}
?>
<div class="row">
	<div class="col-md-12 mb-lg">
		<div class="profile-head">
			<div class="col-md-12 col-lg-4 col-xl-3">
				<div class="image-content-center user-pro">
					<div class="preview">
						<img src="<?php echo get_image_url('student', $student['photo']);?>">
					</div>
				</div>
			</div>
			<div class="col-md-12 col-lg-5 col-xl-5">
				<h5><?=$student['first_name'] . ' ' . $student['last_name']?></h5>
				<p><?=translate('student')  . " / " . $student['category_name']?></p>
				<ul>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('guardian_name')?>"><i class="fas fa-users"></i></div> <?=(!empty($getParent['name']) ? $getParent['name'] : 'N/A'); ?></li>
				<?php if (!empty($student['birthday'])) { ?>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('birthday')?>"><i class="fas fa-birthday-cake"></i></div> <?=_d($student['birthday'])?></li>
				<?php } ?>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('class')?>"><i class="fas fa-school"></i></div> <?=$student['class_name'] . ' ('.$student['section_name'] . ')'?></li>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('mobile_no')?>"><i class="fas fa-phone-volume"></i></div> <?=(!empty($student['mobileno']) ? $student['mobileno'] : 'N/A'); ?></li>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('email')?>"><i class="far fa-envelope"></i></div> <?=$student['email']?></li>
					<li><div class="icon-holder" data-toggle="tooltip" data-original-title="<?=translate('present_address')?>"><i class="fas fa-home"></i></div> <?=(!empty($student['current_address']) ? $student['current_address'] : 'N/A'); ?></li>
				</ul>
			</div>
		</div>
	</div>

	<div class="col-md-12">
		<section class="panel">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="active">
						<a href="#my_details" data-toggle="tab"><i class="far fa-user-circle"></i> <?=translate('profile') . " " . translate('details')?></a>
					</li>
					<li>
						<a href="#promotion_history" data-toggle="tab"><i class="fas fa-arrow-trend-up"></i> <?=translate('promotion_history')?></a>
					</li>
					<li>
						<a href="#fees" data-toggle="tab"><i class="fas fa-money-check"></i> <?=translate('fees')?></a>
					</li>
					<li>
						<a href="#parents" data-toggle="tab"><i class="fas fa-users"></i> <?=translate('parent_information')?></a>
					</li>
					<li>
						<a href="#book_issue" data-toggle="tab"><i class="fas fa-book-reader"></i> <?=translate('book_issue')?></a>
					</li>
					<li>
						<a href="#documents" data-toggle="tab"><i class="fas fa-folder-open"></i> <?=translate('documents')?></a>
					</li>
					<?php if ($personal == true || $previous_school_details['status'] == 1) { ?>
					<li class="">
						<a href="#profile" data-toggle="tab"><i class="far fa-edit"></i> <span class="hidden-xs">Edit Profile</span></a>
					</li>
					<?php } ?>
				</ul>
				<div class="tab-content">
					<div id="my_details" class="tab-pane active">
						<!-- academic details-->
						<div class="headers-line">
							<i class="fas fa-school"></i> <?=translate('academic_details')?>
						</div>
						<div class="row">
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('branch')?></label>
									<p class="text-dark"><?php
										$arrayBranch = $this->app_lib->getSelectList('branch');
										echo $arrayBranch[$branchID];
									?></p>
								</div>
							</div>

							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('academic_year')?></label>
									<p class="text-dark"><?php 
									$row = $this->db->where('id', $student['session_id'])->get('schoolyear')->row();
									echo $row->school_year;
									?></p>
								</div>
							</div>

							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('register_no')?></label>
									<p class="text-dark"><?=$student['register_no']?></p>
								</div>
							</div>

							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('roll')?></label>
									<p class="text-dark"><?php echo empty($student['roll']) ? "N/A" : $student['roll'] ?></p>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('admission_date')?></label>
									<p class="text-dark"><?php echo $student['admission_date'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('class')?></label>
									<p class="text-dark"><?php $arrayClass = $this->app_lib->getClass($branchID); echo $arrayClass[$student['class_id']] ?></p>
									
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('section')?> <span class="required">*</span></label>
									<p class="text-dark"><?php
										$arraySection = $this->app_lib->getSections( $student['class_id'], true);
										echo $arraySection[$student['section_id']];
									?></p>
								</div>
							</div>
							
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('category')?></label>
									<p class="text-dark"><?php
										$arrayCategory = $this->app_lib->getStudentCategory($branchID);
										echo $arrayCategory[$student['category_id']];
									?></p>
								</div>
							</div>
						</div>

						<!-- student details -->
						<div class="headers-line mt-md">
							<i class="fas fa-user-check"></i> <?=translate('student_details')?>
						</div>

						<div class="row">
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('first_name')?> <span class="required">*</span></label>
									<p class="text-dark"><?php echo $student['first_name'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('last_name')?></label>
									<p class="text-dark"><?php echo $student['last_name'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('gender')?></label>
									<p class="text-dark"><?php
									$arrayGender = array(
									'male' => translate('male'),
									'female' => translate('female')
									);
									echo $arrayGender[$student['gender']];
									?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('blood_group')?></label>
									<p class="text-dark"><?php
										$bloodArray = $this->app_lib->getBloodgroup();
										echo $bloodArray[$student['blood_group']];
									?></p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('birthday')?></label>
									<p class="text-dark"><?php echo $student['birthday'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('mother_tongue')?></label>
									<p class="text-dark"><?php echo $student['mother_tongue'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('religion')?></label>
									<p class="text-dark"><?php echo $student['religion'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('caste')?></label>
									<p class="text-dark"><?php echo $student['caste'] ?></p>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('mobile_no')?></label>
									<p class="text-dark"><?php echo $student['mobileno'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('email')?></label>
									<p class="text-dark"><?php echo $student['email'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('city')?></label>
									<p class="text-dark"><?php echo $student['city'] ?></p>
								</div>
							</div>
							<div class="col-md-3 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('state')?></label>
									<p class="text-dark"><?php echo $student['state'] ?></p>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('present_address')?></label>
									<p class="text-dark"><?php echo $student['current_address'] ?></p>
								</div>
							</div>
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('permanent_address')?></label>
									<p class="text-dark"><?php echo $student['permanent_address'] ?></p>
								</div>
							</div>
						</div>
					</div>

					<div id="promotion_history" class="tab-pane">
						<div class="table-responsive mb-md">
							<table class="table table-bordered table-hover table-condensed mb-none">
								<thead>
									<tr>
										<th width="50">#</th>
										<th><?=translate('from_class') . " / " . translate('section')?></th>
										<th><?=translate('from_session')?></th>
										<th><?=translate('promoted_class') . " / " . translate('section')?></th>
										<th><?=translate('promoted_session')?></th>
										<th><?=translate('due_amount')?></th>
										<th><?=translate('promoted_date')?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$label_leave = "<span class='text-danger'><b>" . translate('leave') . "</b></span>";
									$count = 1;
									$this->db->order_by('id', 'asc');
									$this->db->where(array('student_id' => $student['id']));
									$historys = $this->db->get('promotion_history')->result();
										if (count($historys)) {
											foreach($historys as $history):
												?>
										<tr>
											<td><?php echo $count++;?></td>
											<td><?php echo get_type_name_by_id('class', $history->pre_class) . " (" . get_type_name_by_id('section', $history->pre_section) . ")"; ?></td>
											<td><?php echo get_type_name_by_id('schoolyear', $history->pre_session, 'school_year'); ?></td>
											<td><?php echo get_type_name_by_id('class', $history->pro_class) . " (" . get_type_name_by_id('section', $history->pro_section) . ")"; ?></td>
											<td><?php echo $history->is_leave == 1 ? $label_leave : get_type_name_by_id('schoolyear', $history->pro_session, 'school_year'); ?></td>
											<td><?php echo currencyFormat($history->prev_due); ?></td>
											<td><?php echo _d($history->date);?></td>
											
										</tr>
									<?php
										endforeach;
									} else {
										echo '<tr><td colspan="7"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>

					<div id="fees" class="tab-pane">
						<div class="table-responsive mb-md">
							<table class="table table-bordered table-condensed table-hover mb-none tbr-top">
								<thead>
									<tr class="text-dark">
										<th>#</th>
										<th><?=translate("fees_type")?></th>
										<th><?=translate("due_date")?></th>
										<th><?=translate("status")?></th>
										<th><?=translate("amount")?></th>
										<th><?=translate("discount")?></th>
										<th><?=translate("fine")?></th>
										<th><?=translate("paid")?></th>
										<th><?=translate("balance")?></th>
									</tr>
								</thead>
								<tbody>
									<?php
										$count = 1;
										$total_fine = 0;
										$total_discount = 0;
										$total_paid = 0;
										$total_balance = 0;
										$total_amount = 0;
										$allocations = $this->fees_model->getInvoiceDetails($student['enrollid']);
										if (!empty($allocations)) {
										foreach ($allocations as $fee) {
											$deposit = $this->fees_model->getStudentFeeDeposit($fee['allocation_id'], $fee['fee_type_id']);
											$type_discount = $deposit['total_discount'];
											$type_fine = $deposit['total_fine'];
											$type_amount = $deposit['total_amount'];
											$balance = $fee['amount'] - ($type_amount + $type_discount);
											$total_discount += $type_discount;
											$total_fine += $type_fine;
											$total_paid += $type_amount;
											$total_balance += $balance;
											$total_amount += $fee['amount'];
			
										?>
									<tr>
										<td><?php echo $count++;?></td>
										<td><?=$fee['name']?></td>
										<td><?=_d($fee['due_date'])?></td>
										<td><?php 
											$status = 0;
											$labelmode = '';
											if($type_amount == 0) {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif($balance == 0) {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											} else {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											}
											echo "<span class='label ".$labelmode." '>".$status."</span>";
										?></td>
										<td><?php echo currencyFormat($fee['amount']);?></td>
										<td><?php echo currencyFormat($type_discount);?></td>
										<td><?php echo currencyFormat($type_fine);?></td>
										<td><?php echo currencyFormat($type_amount);?></td>
										<td><?php echo currencyFormat($balance);?></td>
									</tr>
									<?php } } else { 
										echo '<tr><td colspan="9"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
									} ?>
								</tbody>
								<tfoot>
									<tr class="text-dark">
										<th></th>
										<th></th>
										<th></th>
										<th></th>
										<th><?php echo currencyFormat($total_amount); ?></th>
										<th><?php echo currencyFormat($total_discount); ?></th>
										<th><?php echo currencyFormat($total_fine); ?></th>
										<th><?php echo currencyFormat($total_paid); ?></th>
										<th><?php echo currencyFormat($total_balance); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>

					<div id="parents" class="tab-pane">
						<div class="table-responsive mb-md">
							<table class="table table-striped table-bordered table-condensed mb-none">
								<tbody>
									<tr>
										<th><?=translate('guardian_name')?></th>
										<td><?php echo $getParent['name']?></td>
										<th><?=translate('relation')?></th>
										<td><?php echo $getParent['relation']?></td>
									</tr>
									<tr>
										<th><?=translate('father_name')?></th>
										<td><?php echo $getParent['father_name']?></td>
										<th><?=translate('mother_name')?></th>
										<td><?php echo $getParent['mother_name']?></td>
									</tr>
									<tr>
										<th><?=translate('occupation')?></th>
										<td><?php echo $getParent['occupation']?></td>
										<th><?=translate('income')?></th>
										<td><?php echo $global_config['currency_symbol'] . $getParent['income']?></td>
									</tr>
									<tr>
										<th><?=translate('education')?></th>
										<td><?php echo $getParent['education']?></td>
										<th><?=translate('city')?></th>
										<td><?php echo $getParent['city']?></td>
									</tr>
									<tr>
										<th><?=translate('state')?></th>
										<td><?php echo $getParent['state']?></td>
										<th><?=translate('mobile_no')?></th>
										<td><?php echo $getParent['mobileno']?></td>
									</tr>
									<tr>
										<th><?=translate('email')?></th>
										<td colspan="3"><?php echo $getParent['email']?></td>
									</tr>
									<tr class="quick-address">
										<th><?=translate('address')?></th>
										<td colspan="3" height="80px;"><?php echo $getParent['address']?></td>
									</tr>
									<tr>
										<th><?=translate('guardian_picture')?></th>
										<td colspan="3"><img class="img-border" width="100" height="100" src="<?=get_image_url('parent', $getParent['photo'])?>"></td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<div id="book_issue" class="tab-pane">
						<div class="table-responsive mb-md">
							<table class="table table-bordered table-hover table-condensed mb-none">
								<thead>
									<tr>
										<th width="50">#</th>
										<th><?=translate('book_title')?></th>
										<th><?=translate('date_of_issue')?></th>
										<th><?=translate('date_of_expiry')?></th>
										<th><?=translate('fine')?></th>
										<th><?=translate('status')?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									$this->db->order_by('id', 'desc');
									$this->db->where(array('session_id' => get_session_id(),'role_id' => 7, 'user_id' => $student['id']));
									$book_result = $this->db->get('book_issues')->result_array();
										if (count($book_result)) {
											foreach($book_result as $book):
												?>
										<tr>
											<td><?php echo $count++;?></td>
											<td><?php echo get_type_name_by_id('book', $book['book_id'], 'title');?></td>
											<td><?php echo _d($book['date_of_issue']);?></td>
											<td><?php echo _d($book['date_of_expiry']);?></td>
											<td>
												<?php
												if(empty($book['fine_amount'])){ 
													echo $global_config['currency_symbol'] . "0.00";
												} else {
													echo $global_config['currency_symbol'] . $book['fine_amount'];
												}
												?>
											</td>
											<td>
												<?php
												if($book['status'] == 0)
													echo '<span class="label label-warning-custom">' . translate('pending') . '</span>';
												if ($book['status'] == 1)
													echo '<span class="label label-success-custom">' . translate('issued') . '</span>';
												if($book['status'] == 2)
													echo '<span class="label label-danger-custom">' . translate('rejected') . '</span>';
												if($book['status'] == 3)
													echo '<span class="label label-primary-custom">' . translate('returned') . '</span>';
												?>
											</td>
										</tr>
									<?php
										endforeach;
									}else{
										echo '<tr><td colspan="6"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
					<div id="documents" class="tab-pane">
                        <div class="table-responsive mb-md">
                            <table class="table table-bordered table-hover table-condensed mb-none">
                            <thead>
                                <tr>
                                    <th><?php echo translate('sl'); ?></th>
                                    <th><?php echo translate('title'); ?></th>
                                    <th><?php echo translate('document') . " " . translate('type'); ?></th>
                                    <th><?php echo translate('file'); ?></th>
                                    <th><?php echo translate('remarks'); ?></th>
                                    <th><?php echo translate('created_at'); ?></th>
                                    <th><?php echo translate('actions'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $count = 1;
                                $this->db->where('student_id', $student['id']);
                                $documents = $this->db->get('student_documents')->result();
                                if (count($documents)) {
                                    foreach($documents as $row):
                                    	?>
                                <tr>
                                    <td><?php echo $count++?></td>
                                    <td><?php echo $row->title; ?></td>
                                    <td><?php echo $row->type; ?></td>
                                    <td><?php echo $row->file_name; ?></td>
                                    <td><?php echo $row->remarks; ?></td>
                                    <td><?php echo _d($row->created_at); ?></td>
                                    <td class="min-w-c">
                                        <a href="<?php echo base_url('student/documents_download?file=' . $row->enc_name); ?>" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?=translate('download')?>">
                                            <i class="fas fa-cloud-download-alt"></i>
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    endforeach;
                                }else{
                                    echo '<tr> <td colspan="7"> <h5 class="text-danger text-center">' . translate('no_information_available') . '</h5> </td></tr>';
                                }
                                ?>
                            </tbody>
                            </table>
                        </div>
					</div>
					<?php if ($personal == true || $previous_school_details['status'] == 1) { ?>
					<div id="profile" class="tab-pane">
						<?php 
						echo form_open_multipart($this->uri->uri_string(), array('class' => 'frm-submit-data'));
						$category = $this->student_fields_model->getStatusProfile('category', $branchID);
						$admission_date = $this->student_fields_model->getStatusProfile('admission_date', $branchID);
						if ($category['status'] == 1 || $admission_date['status'] == 1) {
			                $v = (floatval($category['status']) + floatval($admission_date['status']));
			                $div = floatval(12 / $v);
						?>
						<input type="hidden" name="student_id" value="<?php echo get_loggedin_user_id() ?>">
						<!-- academic details-->
						<div class="headers-line">
							<i class="fas fa-school"></i> <?=translate('academic_details')?>
						</div>
						<div class="row">
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('admission_date')?><?php echo $admission_date['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
										<input type="text" class="form-control" name="admission_date" value="<?=set_value('admission_date', $student['admission_date'])?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
									</div>
									<span class="error"><?=form_error('admission_date')?></span>
								</div>
							</div>


							<?php if ($category['status'] == 1) { ?>
							<div class="col-md-<?php echo $div; ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('category')?><?php echo $category['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<?php
										$arrayCategory = $this->app_lib->getStudentCategory($branchID);
										echo form_dropdown("category_id", $arrayCategory, set_value('category_id', $student['category_id']), "class='form-control'
										data-plugin-selectTwo data-width='100%' id='category_id' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"><?=form_error('category_id')?></span>
								</div>
							</div>
							<?php } ?>
						</div>
					<?php } ?>

					<?php if ($personal == true) { ?>
						<!-- student details -->
						<div class="headers-line mt-md">
							<i class="fas fa-user-check"></i> <?=translate('student_details')?>
						</div>

						<?php
						$v = (1 + floatval($last_name['status']) + floatval($gender['status']));
						$div = ($v == 0) ? 12 : floatval(12 / $v);
						?>
						<div class="row">
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('first_name')?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="first_name" value="<?=set_value('first_name', $student['first_name'])?>"/>
									</div>
									<span class="error"></span>
								</div>
							</div>
							<?php if ($last_name['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('last_name')?><?php echo $last_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="last_name" value="<?=set_value('last_name', $student['last_name'])?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>
							<?php } if ($gender['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('gender')?><?php echo $gender['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<?php
										$arrayGender = array(
											'male' => translate('male'),
											'female' => translate('female')
										);
										echo form_dropdown("gender", $arrayGender, set_value('gender', $student['gender']), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php 
							$v = (floatval($blood_group['status']) + floatval($birthday['status']));
							$div = ($v == 0) ? 12 : floatval(12 / $v);
							if ($blood_group['status'] == 1) {
								?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('blood_group')?><?php echo $blood_group['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<?php
										$bloodArray = $this->app_lib->getBloodgroup();
										echo form_dropdown("blood_group", $bloodArray, set_value("blood_group", $student['blood_group']), "class='form-control populate' data-plugin-selectTwo 
										data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<?php } if ($birthday['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('birthday')?><?php echo $birthday['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
										<input type="text" class="form-control" name="birthday" value="<?=set_value('birthday', $student['birthday'])?>" data-plugin-datepicker
										data-plugin-options='{ "startView": 2 }' />
									</div>
									<span class="error"></span>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php
							$v = (floatval($religion['status']) + floatval($mother_tongue['status']) + floatval($caste['status']));
							$div = ($v == 0) ? 12 : floatval(12 / $v);

							if ($mother_tongue['status'] == 1) {
								?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('mother_tongue')?><?php echo $mother_tongue['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="mother_tongue" value="<?=set_value('mother_tongue', $student['mother_tongue'])?>" />
									<span class="error"></span>
								</div>
							</div>
							<?php } if ($religion['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('religion')?><?php echo $religion['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="religion" value="<?=set_value('religion', $student['religion'])?>" />
									<span class="error"></span>
								</div>
							</div>
							<?php } if ($mother_tongue['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('caste')?><?php echo $caste['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="caste" value="<?=set_value('caste', $student['caste'])?>" />
									<span class="error"></span>
								</div>
							</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php
							$v = (floatval($student_mobile_no['status']) + floatval($student_email['status']) + floatval($city['status']) + floatval($state['status']));
							$div = ($v == 0) ? 12 : floatval(12 / $v);
							if ($student_mobile_no['status'] == 1) {
								?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('mobile_no')?><?php echo $student_mobile_no['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
										<input type="text" class="form-control" name="mobileno" value="<?=set_value('mobileno', $student['mobileno'])?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>
						<?php } if ($student_email['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('email')?><?php echo $student_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
										<input type="text" class="form-control" name="email" id="email" value="<?=set_value('email', $student['email'])?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>
						<?php } if ($city['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('city')?><?php echo $city['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="city" value="<?=set_value('city', $student['city'])?>" />
									<span class="error"></span>
								</div>
							</div>
						<?php } if ($state['status'] == 1) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('state')?><?php echo $state['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="state" value="<?=set_value('state', $student['state'])?>" />
									<span class="error"></span>
								</div>
							</div>
						<?php } ?>
						</div>
						<div class="row">
							<?php 
							$v = (floatval($present_address['status']) + floatval($permanent_address['status']));
							$div = ($v == 0) ? 12 : floatval(12 / $v);
							if ($present_address['status'] == 1) {
								?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('present_address')?><?php echo $present_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<textarea name="current_address" rows="2" class="form-control" aria-required="true"><?=set_value('current_address', $student['current_address'])?></textarea>
									<span class="error"></span>
								</div>
							</div>
							<?php } if ($permanent_address['status']) { ?>
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('permanent_address')?><?php echo $permanent_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<textarea name="permanent_address" rows="2" class="form-control" aria-required="true"><?=set_value('permanent_address', $student['permanent_address'])?></textarea>
									<span class="error"></span>
								</div>
							</div>
							<?php } ?>
						</div>
						<?php if ($student_photo['status'] == 1) { ?>
						<div class="row mb-md">
							<div class="col-md-12">
								<div class="form-group">
									<label for="input-file-now"><?=translate('profile_picture')?><?php echo $student_photo['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="file" name="user_photo" class="dropify" data-default-file="<?=get_image_url('student', $student['photo'])?>" />
									<input type="hidden" name="old_user_photo" value="<?php echo $student['photo']; ?>" />
									<span class="error"></span>
								</div>
							</div>
						</div>
						<?php } ?>
					
						<?php } if ($previous_school_details['status'] == 1) { ?>
						<!-- previous school details -->
						<div class="headers-line">
							<i class="fas fa-bezier-curve"></i> <?=translate('previous_school_details')?>
						</div>
						<div class="row">
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('school_name')?></label>
									<input type="text" class="form-control" name="school_name" value="<?=$previous_details['school_name']?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?=translate('qualification')?></label>
									<input type="text" class="form-control" name="qualification" value="<?=$previous_details['qualification']?>" />
								</div>
							</div>
						</div>
						<div class="row mb-lg">
							<div class="col-md-12">
								<div class="form-group">
									<label class="control-label"><?=translate('remarks')?></label>
									<textarea name="previous_remarks" rows="2" class="form-control"><?=$previous_details['remarks']?></textarea>
								</div>
							</div>
						</div>
						<?php } ?>
						<div class="panel-footer">
							<div class="row">
								<div class="col-md-offset-9 col-md-3">
									<button class="btn btn-default btn-block" type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?></button>
								</div>	
							</div>
						</div>
					<?php echo form_close(); ?>
					</div>
					<?php } ?>
				</div>
			</div>
			
		</section>
	</div>
</div>