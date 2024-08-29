<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<div class="tabs-custom">
				<ul class="nav nav-tabs">
					<li class="<?php echo $role == "staff" ? 'active' : '' ?>">
		                <a href="<?php echo base_url('user_login_log/index/staff') ?>">
		                    <i class="fas fa-list-ul"></i> <?=translate('staff')?>
		                </a>
					</li>
					<li class="<?php echo $role == "student" ? 'active' : '' ?>">
		                <a href="<?php echo base_url('user_login_log/index/student') ?>">
		                    <i class="fas fa-list-ul"></i> <?=translate('student') ?>
		                </a>
					</li>
					<li class="<?php echo $role == "parent" ? 'active' : '' ?>">
		                <a href="<?php echo base_url('user_login_log/index/parent') ?>">
		                    <i class="fas fa-list-ul"></i> <?=translate('parent')?>
		                </a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane box active mb-md">
					<?php if (get_permission('user_login_log', 'is_delete')) { ?>
						<button class="btn btn-danger btn-circle mb-md" onclick="confirm_modal('<?php echo base_url('user_login_log/clear') ?>')"><i class="fas fa-trash-alt"></i> <?php echo translate('clear_userlog') ?></button>
					<?php } ?>
						<table class="table table-bordered table-hover table-condensed table-question"  cellpadding="0" cellspacing="0" width="100%" >
							<thead>
								<tr>
									<th class="no-sort"><?=translate('sl')?></th>
		<?php if (is_superadmin_loggedin()): ?>
									<th><?=translate('branch')?></th>
		<?php endif; ?>
									<th><?=translate('user')?></th>
									<th><?=translate('role')?></th>
									<th>IP <?=translate('address')?></th>
									<th><?=translate('browser')?></th>
									<th><?=translate('login_date_time')?></th>
									<th><?=translate('platform')?></th>
								</tr>
							</thead>
						</table>

					</div>
				</div>
			</div>
		</section>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		initDatatable('.table-question', "user_login_log/getLogListDT/<?php echo $role ?>");
	});
</script>