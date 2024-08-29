<?php
$this->db->select('product_issues.*,roles.name as role_name');
$this->db->from('product_issues');
$this->db->join('roles', 'roles.id = product_issues.role_id', 'left');
$this->db->where('product_issues.id', $salary_id);
$result = $this->db->get()->row();
$user = $this->application_model->getUserNameByRoleID($result->role_id, $result->user_id);
?>
	<header class="panel-heading">
		<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?=translate('issue') . " " . translate('details')?></h4>
	</header>
	<div class="panel-body">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 mt-md">
				<div class="table-responsive">
					<table class="table table-condensed text-dark">
						<tbody>
							<tr class="b-top-none">
								<td colspan="2"><strong><?=translate('issue_to')?> :</strong></td>
								<td class="text-left"><?php echo $user['name'];?> (<?php echo $result->role_name; ?>)</td>
							</tr>
							<tr>
								<td colspan="2"><strong><?php echo translate('email'); ?> :</strong></td>
								<td class="text-left"><?php echo $user['email']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><strong><?php echo translate('mobile_no'); ?> :</strong></td>
								<td class="text-left"><?php echo $user['mobileno']; ?></td>
							</tr>
							<tr>
								<td colspan="2"><strong><?php echo translate('date_of_issue'); ?> :</strong></td>
								<td class="text-left"><?php echo _d($result->date_of_issue); ?></td>
							</tr>
							<tr>
								<td colspan="2"><strong><?php echo translate('due_date'); ?> :</strong></td>
								<td class="text-left"><?php echo _d($result->due_date); ?></td>
							</tr>
						<?php if ($result->status == 1) { ?>
							<tr>
								<td colspan="2"><strong><?php echo translate('return_date'); ?> :</strong></td>
								<td class="text-left"><?php echo empty($result->return_date) ? "-" : _d($result->return_date); ?></td>
							</tr>
						<?php } else { ?>
							<tr>
								<td colspan="2"><strong><?php echo translate('status'); ?> :</strong></td>
								<td class="text-left"><span class="label label-danger-custom"><?php echo translate('not_returned'); ?></span></td>
							</tr>
						<?php } ?>
							<tr>
								<td colspan="2"><strong><?php echo translate('remark'); ?> :</strong></td>
								<td class="text-left"><?php echo empty($result->remarks) ? "-" : html_escape($result->remarks); ?></td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12 mt-lg">
				<section class="panel">
					<header class="panel-heading">
						<h4 class="panel-title"><?php echo translate('product') . " " . translate('list'); ?></h4>
					</header>
					<div class="panel-body">
						<div class="table-responsive">
							<table class="table table-bordered">
								<thead>
									<tr class="text-dark">
										<th><?php echo translate('sl'); ?></th>
										<th><?php echo translate('category'); ?></th>
										<th><?php echo translate('name'); ?></th>
										<th class="text-right"><?php echo translate('quantity'); ?></th>
									</tr>
								</thead>
								<?php
								$count = 1;
								$this->db->select('product_issues_details.*,product.name as product,product_category.name as category,sales_unit_id');
								$this->db->from('product_issues_details');
								$this->db->join('product', 'product.id = product_issues_details.product_id', 'inner');
								$this->db->join('product_category', 'product_category.id = product.category_id', 'left');
								$this->db->where('product_issues_details.issues_id', $salary_id);
								$this->db->order_by('product_issues_details.id', 'asc');
								$allowances = $this->db->get()->result_array();
								if(!empty($allowances)){
									foreach ($allowances as $value):
										?>
									<tr>
										<td><?php echo $count++; ?></td>
										<td><?php echo $value['category']; ?></td>
										<td><?php echo $value['product']; ?></td>
										<td class="text-right"><?php echo $value['quantity'] . " " . get_type_name_by_id('product_unit', $value['sales_unit_id']) ; ?></td>
									</tr>
								<?php 
								endforeach;
								}else{
									echo '<tr> <td colspan="3"> <h5 class="text-danger text-center">' . translate('no_information_available') .  '</h5> </td></tr>';
								}
								?>
								</tbody>
							</table>
						</div>
					</div>
				</section>
			</div>
		</div>
	</div>
	<footer class="panel-footer">
		<div class="row">
			<div class="col-md-12 text-right">
		<?php if (get_permission('product_issue', 'is_add')):
			if ($result->status == 0) {
				?>
			<button class="btn btn-primary mr-xs btn-return" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
				<i class="fas fa-exchange-alt"></i> <?=translate('return')?>
			</button>
		<?php } endif; ?>
				<button class="btn btn-default modal-dismiss"><?=translate('close')?></button>
			</div>
		</div>
	</footer>

<script type="text/javascript">
    $(document).on('click', '.btn-return', function () {
        var $this = $(this);
        $this.button('loading');
        var issue_id = "<?php echo $salary_id ?>";
        $.ajax({
            url: "<?php echo site_url('inventory/returnProduct') ?>",
            type: "POST",
            data: {'issue_id': issue_id},
            dataType: 'Json',
            success: function (data, textStatus, jqXHR)
            {
                if (data.status == "success") {
                    location.reload();
                }
                $this.button('reset');
            },
            error: function (jqXHR, textStatus, errorThrown)
            {
                $this.button('reset');
            }
        });
    });
</script>