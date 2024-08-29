<?php if (is_superadmin_loggedin() && empty($branchID)) { ?>
<div class="row">
    <div class="col-md-12">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title"><i class="fas fa-school"></i> <?=translate('school') . " " . translate('list')?></h4>
            </header>
            <div class="panel-body">
                <table class="table table-bordered table-hover table-condensed mb-none table_default">
                    <thead>
                        <tr>
                            <th width="50"><?=translate('sl')?></th>
                            <th><?=translate('branch_name')?></th>
                            <th><?=translate('school_name')?></th>
                            <th><?=translate('email')?></th>
                            <th><?=translate('address')?></th>
                            <th><?=translate('action')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $count = 1;
                            $branchs = $this->db->get('branch')->result();
                            foreach($branchs as $row):
                        ?>
                        <tr>
                            <td><?php echo $count++; ?></td>
                            <td><?php echo $row->name;?></td>
                            <td><?php echo $row->school_name;?></td>
                            <td><?php echo $row->email;?></td>
                            <td><?php echo $row->address;?></td>
                            <td class="min-w-c">
                                <!--update link-->
                                <a href="<?=base_url('school_settings?branch_id='.$row->id)?>" class="btn btn-default btn-circle">
                                    <i class="fas fa-sliders-h"></i> Configuration
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </div>
</div>
<?php } ?>
<?php if (!empty($branchID)) {
    ?>
<div class="row">
	<div class="col-md-3">
        <?php $this->load->view('school_settings/sidebar'); ?>
    </div>
    <div class="col-md-9">
        <section class="panel">
            <header class="panel-heading">
                <h4 class="panel-title"><i class="fas fa-school"></i> <?=translate('school_setting')?></h4>
            </header>
            <?php echo form_open_multipart('school_settings' . get_request_url(), array('class' => 'form-horizontal  frm-submit-data')); ?>
                <div class="panel-body">

                    <!-- General Setting -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('general_setting')?></h5>
                            <div class="mt-lg">
                                <div class="form-group mt-md">
                                    <label class="col-md-3 control-label"><?=translate('branch_name')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="branch_name" value="<?=$school['name']?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('school_name')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="school_name" value="<?=$school['school_name']?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('email')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="email" class="form-control" name="email" value="<?=$school['email']?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('mobile_no')?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="mobileno" value="<?=$school['mobileno']?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('city')?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="city" value="<?=$school['city']?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('state')?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="state" value="<?=$school['state']?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  class="col-md-3 control-label"><?=translate('address')?></label>
                                    <div class="col-md-6">
                                        <textarea type="text" rows="3" class="form-control" name="address"><?=$school['address']?></textarea>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('language');?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <?php
                                        $languages = $this->db->select('id,lang_field,name')->where('status', 1)->get('language_list')->result();
                                        foreach ($languages as $lang) {
                                            $array[$lang->lang_field] = ucfirst($lang->name);
                                        }
                                        echo form_dropdown("translation", $array, set_value('translation', $school['translation']), "class='form-control' data-plugin-selectTwo 
                                        data-width='100%' ");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('timezone');?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <?php
                                        $timezones = $this->app_lib->timezone_list();
                                        echo form_dropdown("timezone", $timezones, set_value('timezone', $school['timezone']), "class='form-control populate' required id='timezones' 
                                        data-plugin-selectTwo data-width='100%'");
                                        ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('weekends');?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <?php
                                            $array = array(
                                                "0" => translate("sunday"),
                                                "1" => translate("monday"),
                                                "2" => translate("tuesday"),
                                                "3" => translate("wednesday"),
                                                "4" => translate("thursday"),
                                                "5" => translate("friday"),
                                                "6" => translate("saturday")
                                            );

                                            if($school['weekends'] !== '') {
                                                $expHoliday = explode(',', $school['weekends']);
                                            } else {
                                                $expHoliday = [];
                                            }
                                            echo form_multiselect("weekends[]", $array, set_value('weekends', $expHoliday), "id='weekends' class='form-control' data-plugin-selectTwo data-width='100%' data-plugin-options='{" . '"placeholder" : "' . translate('select_weekends') . '" ' ."}'");
                                        ?>
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label  class="col-md-3 control-label"><?php echo translate('unique_roll'); ?></label>
                                    <div class="col-md-6">
                                        <div class="radio-custom radio-success radio-inline mb-xs">
                                            <input type="radio" value="1" <?php echo $school['unique_roll'] == 1 ? 'checked' : '' ?> name="unique_roll" id="astatus_1">
                                            <label for="astatus_1"><?=translate('classes_wise')?></label>
                                        </div>

                                        <div class="radio-custom radio-success radio-inline">
                                            <input type="radio" value="2" name="unique_roll" <?php echo $school['unique_roll'] == 2 ? 'checked' : '' ?> id="astatus_2">
                                            <label for="astatus_2"><?=translate('section_wise')?></label>
                                        </div>

                                        <div class="radio-custom radio-danger radio-inline mb-none">
                                            <input type="radio" value="0" name="unique_roll" <?php echo $school['unique_roll'] == 0 ? 'checked' : '' ?> id="astatus_3">
                                            <label for="astatus_3"><?=translate('disabled')?></label>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group mb-md">
                                    <div class="col-md-offset-3 col-md-6">
                                        <div class="checkbox-replace">
                                            <label class="i-checks">
                                                <input type="checkbox" name="teacher_restricted" <?=($school['teacher_restricted'] == 1 ? 'checked' : '');?>>
                                                <i></i> <?=translate('teacher_restricted')?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- currency config -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('currency')?></h5>
                            <div class="mt-lg">
                                <div class="form-group">
                                    <label  class="col-md-3 control-label"><?=translate('currency')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="currency" value="<?=$school['currency']?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('currency_symbol')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="currency_symbol" value="<?=$school['symbol']?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('currency_formats');?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <?php
                                        $digitArray = array(
                                            '' => translate('select'), 
                                            '1' => "12300000.50", 
                                            '2' => "1,23,000,00.50", 
                                            '3' => "123,000,00.50", 
                                            '4' => "123.000.00,50", 
                                            '5' => "123,000,00.50", 
                                            '6' => "123 000 00,50", 
                                            '7' => "123 000 00.50", 
                                            '8' => "12300000", 
                                        );
                                        echo form_dropdown("currency_formats", $digitArray, set_value('currency_formats', $school['currency_formats']), "class='form-control' data-plugin-selectTwo 
                                        data-width='100%' ");
                                        ?>
                                        <span class="error"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('symbol_position');?> <span class="required">*</span></label>
                                    <div class="col-md-6 mb-lg">
                                        <?php
                                        $digitArray = array(
                                            '' => translate('select'), 
                                            '1' => "$123,000.00", 
                                            '2' => "123,000.00$", 
                                            '3' => "$ 123,000.00", 
                                            '4' => "123,000.00 $",  
                                            '5' => "USD 123,000.00",  
                                            '6' => "123,000.00 USD",  
                                        );
                                        echo form_dropdown("symbol_position", $digitArray, set_value('symbol_formats', $school['symbol_position']), "class='form-control' data-plugin-selectTwo 
                                        data-width='100%' ");
                                        ?>
                                        <span class="error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- admission register no prefix -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('register_no') . " " . translate('prefix')?></h5>
                            <div class="mt-lg">
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <div class="checkbox-replace">
                                            <label class="i-checks">
                                                <input type="checkbox" name="reg_prefix_enable" id="reg_prefix_enable" <?=($school['reg_prefix_enable'] == '1' ? 'checked' : '');?>>
                                                <i></i> Enable Student Admission Registration No  Prefix Auto.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('institution_code')?> (<?=translate('prefix')?>) <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="institution_code" value="<?php echo $school['institution_code'] ?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('register_no') . " " . translate('start_from')?> <span class="required">*</span></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="reg_start_from" value="<?php echo $school['reg_start_from'] ?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('register_no') . " " . translate('digit');?> <span class="required">*</span></label>
                                    <div class="col-md-6 mb-lg">
                                        <?php
                                        $digitArray = array(
                                            '' => translate('select'), 
                                        );
                                        for ($i=1; $i < 11; $i++) { 
                                            $digitArray[$i] =  $i;
                                        }
                                        echo form_dropdown("reg_prefix_digit", $digitArray, set_value('reg_prefix_digit', $school['reg_prefix_digit']), "class='form-control' data-plugin-selectTwo 
                                        data-width='100%' ");
                                        ?>
                                        <span class="error"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Fees offline payments setting -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('offline_payments') . " " . translate('setting')?></h5>
                            <div class="mt-lg">
                                <div class="form-group mb-md">
                                    <label class="col-md-3 control-label"><?=translate('offline_payments');?></label>
                                    <div class="col-md-6">
                                        <?php
                                        $offlinePayments = array(
                                            '1' => translate('enabled'), 
                                            '0' => translate('disabled'), 
                                        );
                                        echo form_dropdown("offline_payments", $offlinePayments, set_value('offline_payments', $school['offline_payments']), "class='form-control' id='offline_payments' 
                                        data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Fees offline payments setting -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('online_exam'); ?></h5>
                            <div class="mt-lg">
                                <div class="form-group mb-md">
                                    <label class="col-md-3 control-label">Show Only Own Question</label>
                                    <div class="col-md-6">
                                        <?php
                                        $offlinePayments = array(
                                            '1' => translate('enabled'), 
                                            '0' => translate('disabled'), 
                                        );
                                        echo form_dropdown("show_own_question", $offlinePayments, set_value('show_own_question', $school['show_own_question']), "class='form-control' 
                                        data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Fees Carry Forward Setting -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs"><?=translate('fees_carry_forward_setting')?></h5>
                            <div class="mt-lg">
                                <div class="form-group">
                                    <label class="col-md-3 control-label"><?=translate('due_days')?></label>
                                    <div class="col-md-6">
                                        <input type="text" class="form-control" name="due_days" value="<?php echo $school['due_days'] ?>" />
                                        <span class="error"></span>
                                    </div>
                                </div>
                                <div class="form-group mb-md">
                                    <div class="col-md-offset-3 col-md-6">
                                        <div class="checkbox-replace">
                                            <label class="i-checks">
                                                <input type="checkbox" name="cal_with_fine" <?=($school['due_with_fine'] == 1 ? 'checked' : '');?>>
                                                <i></i> <?=translate('due_fees_calculation_with_fine_')?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Automatically Generate Login Details -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs">Automatically Generate Login Details</h5>
                            <div class="mt-lg">
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <div class="checkbox-replace">
                                            <label class="i-checks">
                                                <input type="checkbox" name="generate_student" id="generate_student_cb" <?=($school['stu_generate'] == 1 ? 'checked' : '');?>>
                                                <i></i> Automatically Generate Student Login Details.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div style="<?=($school['stu_generate'] == 0 ? 'display: none' : '');?>" id="stu_generate">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?=translate('username') . " " . translate('prefix') ?> <span class="required">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="stu_username_prefix" value="<?=$school['stu_username_prefix']?>" />
                                            <span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?=translate('default')  . " " . translate('password')?> <span class="required">*</span></label>
                                        <div class="col-md-6 mb-md">
                                            <input type="text" class="form-control" name="stu_default_password" value="<?=$school['stu_default_password']?>" />
                                            <span class="error"></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-6">
                                        <div class="checkbox-replace">
                                            <label class="i-checks">
                                                <input type="checkbox" name="generate_guardian" id="generate_guardian_cb" <?=($school['grd_generate'] == 1 ? 'checked' : '');?>>
                                                <i></i> Automatically Generate Guardian Login Details.
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                <div style="<?=($school['grd_generate'] == 0 ? 'display: none' : '');?>" id="guardian_generate">
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?=translate('username') . " " . translate('prefix') ?> <span class="required">*</span></label>
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" name="grd_username_prefix" value="<?=$school['grd_username_prefix']?>" />
                                            <span class="error"></span>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-md-3 control-label"><?=translate('default')  . " " . translate('password')?> <span class="required">*</span></label>
                                        <div class="col-md-6 mb-md">
                                            <input type="text" class="form-control" name="grd_default_password" value="<?=$school['grd_default_password']?>" />
                                            <span class="error"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- School Logo -->
                    <section class="panel pg-fw">
                        <div class="panel-body">
                            <h5 class="chart-title mb-xs">Logo Setting</h5>
                            <div class="mt-md">
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-3">
                                        <label class="control-label pt-none"><?=translate('system_logo');?></label>
                                        <input type="file" name="logo_file" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=$this->application_model->getBranchImage($school['id'], 'logo')?>" />
                                    </div>
                                    <div class="col-md-3 mb-md">
                                        <label class="control-label pt-none"><?=translate('text_logo');?></label>
                                        <input type="file" name="text_logo" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=$this->application_model->getBranchImage($school['id'], 'logo-small')?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-offset-3 col-md-3">
                                        <label class="control-label pt-none"><?=translate('printing_logo');?></label>
                                        <input type="file" name="print_file" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=$this->application_model->getBranchImage($school['id'], 'printing-logo')?>" />
                                    </div>
                                    <div class="col-md-3 mb-md">
                                        <label class="control-label pt-none"><?=translate('report_card');?></label>
                                        <input type="file" name="report_card" class="dropify" data-allowed-file-extensions="png" data-default-file="<?=$this->application_model->getBranchImage($school['id'], 'report-card-logo')?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-md-3 col-sm-offset-3">
                            <button type="submit" class="btn btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
                                <i class="fas fa-plus-circle"></i> <?=translate('save');?>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </section>
     </div>
</div>
<?php } ?>

<script type="text/javascript">
    $('#generate_student_cb').on('click', function(){
        if (this.checked) {
            $('#stu_generate').show(300); 
        } else {
           $('#stu_generate').hide(300); 
        }
    });

    $('#generate_guardian_cb').on('click', function(){
        if (this.checked) {
            $('#guardian_generate').show(300); 
        } else {
           $('#guardian_generate').hide(300); 
        }
    });
</script>