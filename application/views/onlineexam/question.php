<?php $widget = (is_superadmin_loggedin() ? 3 : 4); ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-file-circle-question"></i> <?=translate('question') . " " . translate('list')?></h4>
			<?php if(get_permission('question_bank', 'is_add')) { ?>
				<div class="panel-btn">
					<a href="<?=base_url('onlineexam/question_add')?>" class="btn btn-default btn-circle">
						<i class="fas fa-circle-question"></i> <?=translate('add') . " " . translate('question')?>
					</a>
					<a href="<?=base_url('onlineexam/question_import')?>" class="btn btn-default btn-circle">
						<i class="fas fa-plus"></i> <?=translate('question') . " " . translate('import')?>
					</a>
				</div>
			<?php } ?>
			</header>
			<div class="panel-body">
				<table class="table table-bordered table-hover table-condensed table-question"  cellpadding="0" cellspacing="0" width="100%" >
					<thead>
						<tr>
							<th class="no-sort"><?=translate('sl')?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?=translate('question')?></th>
							<th><?=translate('group')?></th>
							<th><?=translate('class')?></th>
							<th><?=translate('subject')?></th>
							<th><?=translate('type')?></th>
							<th><?=translate('level')?></th>
							<th><?=translate('action')?></th>
						</tr>
					</thead>
				</table>
			</div>
		</section>
	</div>
</div>

<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide payroll-t-modal" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-file-circle-question"></i> <?php echo translate('question') . " " . translate('view'); ?></h4>
		</header>
		<div class="panel-body">
			<div id="quick_view"></div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
				</div>
			</div>
		</footer>
	</section>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		initDatatable('.table-question', 'onlineexam/getQuestionListDT');
	});
</script>