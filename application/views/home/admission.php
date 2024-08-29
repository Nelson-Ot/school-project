<!-- Main Banner Starts -->
<div class="main-banner" style="background: url(<?php echo base_url('uploads/frontend/banners/' . $page_data['banner_image']); ?>) center top;">
    <div class="container px-md-0">
        <h2><span><?php echo $page_data['page_title']; ?></span></h2>
    </div>
</div>
<!-- Main Banner Ends -->
<!-- Breadcrumb Starts -->
<div class="breadcrumb">
    <div class="container px-md-0">
        <ul class="list-unstyled list-inline">
            <li class="list-inline-item"><a href="<?php echo base_url('home') ?>">Home</a></li>
            <li class="list-inline-item active"><?php echo $page_data['page_title']; ?></li>
        </ul>
    </div>
</div>
<!-- Breadcrumb Ends -->
<!-- Main Container Starts -->
<div class="container px-md-0 main-container">
    <h3 class="main-heading2 mt-0"><?php echo $page_data['title']; ?></h3>
    <?php echo $page_data['description']; ?>

    <div class="box2 form-box position-relative">
        <button type="submit" class="btn btn-1 admission-status-btn" data-bs-toggle="modal" data-bs-target="#admissionModal"><i class="fa-solid fa-file-lines"></i> Check Addmision Status</button>
        <div class="tabs-panel tabs-product">
            <div class="nav nav-tabs">
                <a class="nav-item nav-link active" data-toggle="tab" href="#new-admission" role="tab" aria-controls="tab-details" aria-selected="true">New Admission</a>
            </div>
            <div class="tab-content clearfix">
                <div class="tab-pane fade show active" id="new-admission" role="tabpanel" aria-labelledby="tab-new-admission">
                    <?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-horizontal frm-submit-data')); ?>
                        <?php $section = $this->student_fields_model->getOnlineStatus('section', $branchID); ?>
                        <div class="headers-line mt-3"><i class="fas fa-school"></i> Academic Details</div>
                        <div class="row">
                            <div class="col-md-<?php echo $section['status'] == 1 ? '4' : '6' ?>">
                                <div class="form-group">
                                    <label>School Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="schoolname" value="<?php echo get_type_name_by_id('branch', $branchID, 'school_name'); ?>" readonly />
                                </div>
                            </div>
                            <div class="col-md-<?php echo $section['status'] == 1 ? '4' : '6' ?>">
                                <div class="form-group">
                                    <label class="control-label">Class <span class="required">*</span></label>
                                    <?php
                                        $arrayClass = $this->app_lib->getClass($branchID);
                                        echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' data-plugin-selectTwo onchange='getSectionByClass(this.value)'");
                                    ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                        <?php if ($section['status']) { ?>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label class="control-label">Section<?php echo $section['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <?php
                                        $arraySection = $this->app_lib->getSections(set_value('class_id'), false);
                                        echo form_dropdown("section", $arraySection, set_value('section'), "class='form-control' data-plugin-selectTwo id='section_id' ");
                                    ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                        <?php } ?>
                        </div>
                        <?php
                        $admission_date = $this->student_fields_model->getOnlineStatus('admission_date', $branchID);
                        $category = $this->student_fields_model->getOnlineStatus('category', $branchID);
                        $v = floatval($admission_date['status']) + floatval($category['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
                        ?>
                        <div class="row">
                            <?php if ($admission_date['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="admission_date">Admission Date<?php echo $admission_date['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" data-plugin-datepicker name="admission_date" readonly value="<?php echo date('Y-m-d') ?>" id="admission_date" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($category['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="admission_date">Category<?php echo $category['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <?php
                                        $arrayCategory = $this->app_lib->getStudentCategory($branchID);
                                        echo form_dropdown("category", $arrayCategory, set_value('category_id'), "class='form-control'
                                        data-plugin-selectTwo data-width='100%' id='category_id' data-minimum-results-for-search='Infinity' ");
                                    ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="headers-line mt-3"><i class="fas fa-user-graduate"></i> Student Details</div>
                        <div class="row">
                            <?php 
                            $last_name = $this->student_fields_model->getOnlineStatus('last_name', $branchID);
                            $gender = $this->student_fields_model->getOnlineStatus('gender', $branchID);

                            $v = (1 + floatval($last_name['status']) + floatval($gender['status']));
                            $div = ($v == 0) ? 12 : floatval(12 / $v);
                            ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">First Name <span class="required">*</span></label>
                                    <input type="text" class="form-control" name="first_name" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php if ($last_name['status']) { ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Last Name<?php echo $last_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="last_name" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($gender['status']) { ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Gender<?php echo ($gender['required'] == 1 ? ' <span class="required">*</span>' : ''); ?></label>
                                    <?php
                                        $arrayGender = array(
                                            '' => translate('select'),
                                            'male' => translate('male'),
                                            'female' => translate('female')
                                        );
                                        echo form_dropdown("gender", $arrayGender, set_value('gender'), "class='form-control' data-plugin-selectTwo ");
                                    ?>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <?php 
                            $birthday = $this->student_fields_model->getOnlineStatus('birthday', $branchID);
                            $blood_group = $this->student_fields_model->getOnlineStatus('blood_group', $branchID);
                            $v = floatval($birthday['status']) + floatval($blood_group['status']);
                            $div = ($v == 0) ? 12 : floatval(12 / $v);
                            if ($birthday['status']) {
                            ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="birthday">Birthday<?php echo $birthday['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" data-plugin-datepicker name="birthday" readonly value="<?php echo set_value('birthday'); ?>" id="birthday" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($blood_group['status']) { ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Blood Group<?php echo $birthday['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <?php
                                        $bloodArray = $this->app_lib->getBloodgroup();
                                        echo form_dropdown("blood_group", $bloodArray, set_value("blood_group"), "class='form-control populate' data-plugin-selectTwo 
                                        data-width='100%' data-minimum-results-for-search='Infinity' ");
                                    ?>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <div class="row">
                            <?php
                            $student_mobileno = $this->student_fields_model->getOnlineStatus('student_mobile_no', $branchID); 
                            $student_email = $this->student_fields_model->getOnlineStatus('student_email', $branchID); 
                            $v = floatval($student_mobileno['status']) + floatval($student_email['status']);
                            $div = ($v == 0) ? 12 : floatval(12 / $v);

                            if ($student_mobileno['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="mobile_no">Student Mobile No<?php echo $student_mobileno['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" name="student_mobile_no" class="form-control" value="<?php echo set_value('student_mobile_no'); ?>" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($student_email['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="email">Student Email<?php echo $student_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" name="student_email" class="form-control" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <?php 
                        $mother_tongue = $this->student_fields_model->getOnlineStatus('mother_tongue', $branchID); 
                        $religion = $this->student_fields_model->getOnlineStatus('religion', $branchID); 
                        $caste = $this->student_fields_model->getOnlineStatus('caste', $branchID); 
                        ?>
                        <div class="row">
                            <?php if ($mother_tongue['status']) { ?>
                            <div class="col-md-4 mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Mother Tongue<?php echo $mother_tongue['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="mother_tongue" value="<?=set_value('mother_tongue')?>" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($religion['status']) { ?>
                            <div class="col-md-4 mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Religion<?php echo $religion['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="religion" value="<?=set_value('religion')?>" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($caste['status']) { ?>
                            <div class="col-md-4 mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Caste<?php echo $caste['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="caste" value="<?=set_value('caste')?>" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>


                        <?php
                        $current_address = $this->student_fields_model->getOnlineStatus('present_address', $branchID);
                        $permanent_address = $this->student_fields_model->getOnlineStatus('permanent_address', $branchID);
                        $div = 6;
                        if ($current_address['status'] == 0 || $permanent_address['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($current_address['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Present Address<?php echo $current_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <textarea class="form-control" name="present_address" rows="2" placeholder="Enter Present Address"><?php echo set_value('address'); ?></textarea>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($permanent_address['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Permanent Address<?php echo $permanent_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <textarea class="form-control" name="permanent_address" rows="2" placeholder="Enter Permanent Address"><?php echo set_value('address'); ?></textarea>
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php
                        $city = $this->student_fields_model->getOnlineStatus('city', $branchID);
                        $state = $this->student_fields_model->getOnlineStatus('state', $branchID);
                        $div = 6;
                        if ($city['status'] == 0 || $state['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($city['status']) { ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">City<?php echo $city['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="city" value="<?=set_value('city')?>" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($state['status']) { ?>
                            <div class="col-md-<?php echo $div ?> mb-sm">
                                <div class="form-group">
                                    <label class="control-label">State<?php echo $state['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="state" value="<?=set_value('state')?>" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>

                        <!--custom fields details-->
                        <div class="row" id="customFields">
                            <?php echo render_online_custom_fields('student', $branchID); ?>
                        </div>

                        <?php
                        $student_photo = $this->student_fields_model->getOnlineStatus('student_photo', $branchID); 
                            if ($student_photo['status']) {
                                ?>
                        <div class="row">
                            <div class="col-md-12 mb-sm">
                                <div class="form-group">
                                    <label for="message">Student Photo<?php echo $student_photo['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <div class="custom-file">
                                        <input type="file" name="student_photo" class="custom-file-input" id="photoFile" accept=".jpg,.jpeg,.png,.bmp" onchange="changeCustomUploader(this)">
                                        <label class="custom-file-label" for="photoFile">Choose Photo file...</label>
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <?php }
                        $previous_school_details = $this->student_fields_model->getOnlineStatus('previous_school_details', $branchID); 
                        if ($previous_school_details['status']) {
                        ?>

                        <!-- previous school details -->
                        <div class="headers-line">
                            <i class="fas fa-bezier-curve"></i> <?=translate('previous_school_details')?>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-sm">
                                <div class="form-group">
                                    <label class="control-label">School Name<?php echo $previous_school_details['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="school_name" value="" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <div class="col-md-6 mb-sm">
                                <div class="form-group">
                                    <label class="control-label">Qualification<?php echo $previous_school_details['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="qualification" value="" />
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-lg">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="control-label"><?=translate('remarks')?></label>
                                    <textarea name="previous_remarks" rows="2" class="form-control"></textarea>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <?php 
                        $guardian_name = $this->student_fields_model->getOnlineStatus('guardian_name', $branchID);
                        $guardian_relation = $this->student_fields_model->getOnlineStatus('guardian_relation', $branchID);
                        $father_name = $this->student_fields_model->getOnlineStatus('father_name', $branchID);
                        $mother_name = $this->student_fields_model->getOnlineStatus('mother_name', $branchID);
                        $guardian_occupation = $this->student_fields_model->getOnlineStatus('guardian_occupation', $branchID);
                        $guardian_income = $this->student_fields_model->getOnlineStatus('guardian_income', $branchID);
                        $guardian_education = $this->student_fields_model->getOnlineStatus('guardian_education', $branchID);
                        $guardian_email = $this->student_fields_model->getOnlineStatus('guardian_email', $branchID);
                        $guardian_mobile_no = $this->student_fields_model->getOnlineStatus('guardian_mobile_no', $branchID);
                        $guardian_address = $this->student_fields_model->getOnlineStatus('guardian_address', $branchID);
                        $guardian_photo = $this->student_fields_model->getOnlineStatus('guardian_photo', $branchID);
                        
                        if ($guardian_name['status'] || $guardian_relation['status'] || $father_name['status'] || $mother_name['status'] || $guardian_occupation['status'] || $guardian_income['status'] || $guardian_education['status'] || $guardian_email['status'] || $guardian_mobile_no['status'] || $guardian_address['status'] || $guardian_photo['status']) {
                        ?>
                        <div class="headers-line mt-3"><i class="fas fa-user-tie"></i> Guardian Details</div>
                        <?php 
                        $div = 6;
                        if ($guardian_name['status'] == 0 || $guardian_relation['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($guardian_name['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Guardian Name<?php echo $guardian_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="guardian_name" value="<?php echo set_value('guardian_name'); ?>" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($guardian_relation['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Relation<?php echo $guardian_relation['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" name="guardian_relation" class="form-control" value="<?php echo set_value('guardian_relation'); ?>" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php 
                        $div = 6;
                        if ($father_name['status'] == 0 || $mother_name['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($father_name['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="father_name">Father Name<?php echo $father_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" name="father_name" class="form-control" value="<?php echo set_value('father_name'); ?>" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($mother_name['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label for="mother_name">Mother Name<?php echo $mother_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" name="mother_name" class="form-control" value="<?php echo set_value('mother_name'); ?>" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php 
                        $div = 6;
                        $v = floatval($guardian_occupation['status']) + floatval($guardian_income['status']) + floatval($guardian_education['status']);
                        $div = ($v == 0) ? 12 : floatval(12 / $v);
                        ?>
                        <div class="row">
                            <?php if ($guardian_occupation['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Occupation<?php echo $guardian_occupation['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="guardian_occupation" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($guardian_income['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Income<?php echo $guardian_income['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input class="form-control" name="guardian_income" value="" type="text" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($guardian_education['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Education<?php echo $guardian_education['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="guardian_education" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php 
                        $div = 6;
                        if ($guardian_email['status'] == 0 || $guardian_mobile_no['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($guardian_email['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Guardian Email<?php echo $guardian_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="guardian_email" value="" autocomplete="off">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($guardian_mobile_no['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Guardian Mobile No<?php echo $guardian_mobile_no['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input type="text" class="form-control" name="guardian_mobile_no" value="" autocomplete="off" />
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <?php $guardian_address = $this->student_fields_model->getOnlineStatus('guardian_address', $branchID); 
                            if ($guardian_address['status']) { 
                                ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Guardian Address <?php echo $guardian_address['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <textarea class="form-control" name="guardian_address" placeholder="Enter Address"><?php echo set_value('grd_address'); ?></textarea>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <?php } ?>

                        <?php
                        $guardian_city = $this->student_fields_model->getOnlineStatus('guardian_city', $branchID);
                        $guardian_state = $this->student_fields_model->getOnlineStatus('guardian_state', $branchID);
                        $div = 6;
                        if ($guardian_city['status'] == 0 || $guardian_state['status'] == 0) {
                            $div = 12;
                        }
                        ?>
                        <div class="row">
                            <?php if ($guardian_city['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Guardian City<?php echo $guardian_city['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input class="form-control" name="guardian_city" value="" type="text">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php } if ($guardian_state['status']) { ?>
                            <div class="col-md-<?php echo $div ?>">
                                <div class="form-group">
                                    <label class="control-label">Guardian State<?php echo $guardian_state['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <input class="form-control" name="guardian_state" value="" type="text">
                                    <span class="error"></span>
                                </div>
                            </div>
                            <?php }?>
                        </div>

                        <?php $guardian_photo = $this->student_fields_model->getOnlineStatus('guardian_photo', $branchID); 
                        if ($guardian_photo['status']) { 
                        ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label for="message">Guardian Photo<?php echo $guardian_photo['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="guardian_photo" id="guardianPhoto" accept=".jpg,.jpeg,.png,.bmp" onchange="changeCustomUploader(this)">
                                        <label class="custom-file-label" for="guardianPhoto">Choose Guardian Photo...</label>
                                    </div>
                                    <span class="error"></span>
                                </div>
                            </div>
                        </div>
                        <?php } } ?>

                        <?php $upload_documents = $this->student_fields_model->getOnlineStatus('upload_documents', $branchID); 
                        if ($upload_documents['status']) { 
                        ?>
                        <div class="headers-line mt-3"><i class="far fa-file-archive"></i> Upload Documents</div>
                        <div class="form-group">
                            <label for="message">Upload Documents<?php echo $upload_documents['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
                            <div class="custom-file">
                                <input type="file" name="upload_documents" class="custom-file-input" id="documentFile" onchange="changeCustomUploader(this)">
                                <label class="custom-file-label" for="documentFile">Choose file...</label>
                            </div>
                            <span class="error"></span>
                        </div>
                        <?php } ?>


                        <?php if ($cms_setting['captcha_status'] == 'enable'): ?>
                        <div class="form-group">
                            <?php echo $recaptcha['widget']; echo $recaptcha['script']; ?>
                            <span class="error"></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($page_data['terms_conditions_title'])) {?>
                        <div class="accordion mb-3" id="accordion-faqs">
                            <div class="card">
                                <div class="card-header" id="faq1">
                                    <h5 class="card-title" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                                        <a><?php echo $page_data['terms_conditions_title']; ?></a>
                                    </h5>
                                </div>
                                <div id="collapseOne" class="collapse" aria-labelledby="faq1" data-parent="#accordion-faqs">
                                    <div class="card-body">
                                        <?php echo $page_data['terms_conditions_description'] ?>
                                    </div>
                                </div>                 
                            </div>
                        </div>
                    <?php } ?>
                        <button type="submit" class="btn btn-1" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><i class="fas fa-plus-circle"></i> <?=translate('submit')?></button>
                    <?php echo form_close(); ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade modal-lg" id="admissionModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <?php echo form_open('home/checkAdmissionStatus', array('class' => 'form-horizontal frm-submit-data')); ?>
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">Check Addmision Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="form-group mt-3 mb-3">
                    <label>Enter Your Reference Number <span class="required">*</span></label>
                    <input type="text" class="form-control" name="refno" id="refno" autocomplete="off">
                    <span class="error"></span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn-primary">Check Now</button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>