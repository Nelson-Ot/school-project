<div class="row">
    <div class="col-md-3">
        <?php include 'sidebar.php'; ?>
    </div>
    <div class="col-md-9">
        <section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?=translate('attendance_type')?></h4>
			</header>
			<?php echo form_open('school_settings/attendance_type' . get_request_url(), array('class' => 'form-horizontal form-bordered frm-submit-msg')); ?>
            <div class="panel-body">
                <div class="alert alert-info">Note: This change will not affect Super-admin role. You must log in as another role (Like: Admin, Teacher, etc) to check this affect.</div>
                <div class="mt-lg mb-lg">
                    <div class="form-group mb-md">
                        <label class="col-md-3 control-label"><?=translate('attendance_type');?></label>
                        <div class="col-md-6">
                            <?php
                            $attendanceType = array(
                                '0' => translate('day_wise'), 
                                '1' => translate('subject_wise'), 
                            );
                            echo form_dropdown("attendance_type", $attendanceType, set_value('attendance_type', $school['attendance_type']), "class='form-control' id='attendanceType' 
                            data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <div class="row">
                    <div class="col-md-2 col-sm-offset-3">
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