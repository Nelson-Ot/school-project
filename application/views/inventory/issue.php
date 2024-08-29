<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#productlist" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('issue') . ' ' . translate('list'); ?></a>
			</li>
<?php if (get_permission('product_issue', 'is_add')): ?>
			<li>
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('add') . ' ' . translate('issue'); ?></a>
			</li>
<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div id="productlist" class="tab-pane active mb-md">
				<div class="export_title"><?php echo translate('issue') . " " . translate('report'); ?></div>
				<table class="table table-bordered table-hover table-condensed table-export nowrap" cellspacing="0" width="100%">
					<thead>
						<tr>
							<th><?=translate('sl')?></th>
<?php if (is_superadmin_loggedin()): ?>
							<th><?=translate('branch')?></th>
<?php endif; ?>
							<th><?php echo translate('role'); ?></th>
							<th><?php echo translate('issue_to'); ?></th>
							<th><?php echo translate('mobile_no'); ?></th>
							<th><?php echo translate('date_of_issue'); ?></th>
							<th><?php echo translate('due_date'); ?></th>
							<th><?php echo translate('return_date'); ?></th>
							<th><?php echo translate('issued_by'); ?></th>
							<th><?php echo translate('action'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if (!empty($saleslist)){ 
							$count = 1;
							foreach ($saleslist as $row):
								$user = $this->application_model->getUserNameByRoleID($row['role_id'], $row['user_id']);
								?>	
						<tr>
							<td><?php echo $count++;?></td>
<?php if(is_superadmin_loggedin()): ?>
							<td><?php echo get_type_name_by_id('branch', $row['branch_id']);?></td>
<?php endif; ?>
							<td><?php echo $row['role_name']; ?></td>
							<td><?php echo $user['name']; ?></td>
							<td><?php echo $user['mobileno']; ?></td>
							<td><?php echo _d($row['date_of_issue']); ?></td>
							<td><?php echo _d($row['due_date']); ?></td>
							<td><?php echo $row['status'] == 0 ? '<span class="label label-danger-custom">' . translate('not_returned') . '</span>' : _d($row['return_date']); ?></td>
							<td><?php echo get_type_name_by_id('staff', $row['prepared_by']); ?></td>
							<td>
								<a href="javascript:void(0);" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?php echo translate('details'); ?>" onclick="getIssueDetails('<?=$row['id']?>')">
									<i class="fas fa-bars"></i>
								</a>
								<?php if (get_permission('product_issue', 'is_delete')): ?>
									<?php echo btn_delete('inventory/issue_delete/' . $row['id']); ?>
								<?php endif; ?>
							</td>
						</tr>
						<?php endforeach; }?>
					</tbody>
				</table>
			</div>
<?php if (get_permission('product_issue', 'is_add')){ ?>
			<div id="create" class="tab-pane">
				<?php echo form_open('inventory/issue_save', array('id' => 'frmSubmit')); ?>
					<div class="mt-lg form-horizontal form-bordered">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('branch')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayBranch = $this->app_lib->getSelectList('branch');
									echo form_dropdown("branch_id", $arrayBranch, "", "class='form-control' id='branchID'
									data-plugin-selectTwo data-width='100%'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('role')?> <span class="required">*</span></label>
			                <div class="col-md-6">
				                <?php
				                    $role_list = $this->app_lib->getRoles(1);
				                    echo form_dropdown("role_id", $role_list, set_value('role_id'), "class='form-control' data-plugin-selectTwo id='roleID'
				                    data-width='100%' data-minimum-results-for-search='Infinity' ");
				                ?>
				                <span class="error"></span>
			            	</div>
						</div>
						<div class="form-group class_div" <?php if(empty($class_id)) { ?> style="display: none" <?php } ?>>
							<label class="col-md-3 control-label"><?=translate('class')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayClass = $this->app_lib->getClass($branch_id);
									echo form_dropdown("class_id", $arrayClass, set_value('class_id'), "class='form-control' id='class_id' data-plugin-selectTwo
									data-width='100%' ");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?=translate('sale_to')?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
									$arrayUser = array("" => translate('select'));
									echo form_dropdown("sale_to", $arrayUser, set_value('sale_to'), "class='form-control' id='receiverID' data-plugin-selectTwo data-width='100%'");
								?>
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('date_of_issue'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="date_of_issue" value="<?php echo date('Y-m-d'); ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' id='date_of_issue' />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('due_date'); ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<input type="text" class="form-control" name="due_date" value="" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' id='due_date' />
								<span class="error"></span>
							</div>
						</div>
						<div class="form-group">
							<label class="col-md-3 control-label"><?php echo translate('remark'); ?></label>
							<div class="col-md-6">
								<textarea class="form-control" rows="2" name="remarks"></textarea>
								<span class="error"></span>
							</div>
						</div>
					</div>
					<div id="purchaseItems" class="mt-lg" style="<?php echo (empty($branch_id) ? 'display: none;' : ''); ?>">
						<?php if (!empty($branch_id)) { ?>
						<div class="table-responsive">
							<table class="table table-bordered table-hover mt-md" id="tableID">
								<thead>
									<th><?php echo translate('category'); ?> <span class="required">*</span></th>
									<th><?php echo translate('product'); ?> <span class="required">*</span></th>
									<th><?php echo translate('quantity'); ?> <span class="required">*</span></th>
								</thead>
								<tbody>
									<tr id="row_0">
										<td class="min-w-lg">
											<div class="form-group">
												<?php
												echo form_dropdown("sales[0][category]", $categorylist, "", "class='form-control' onchange='getProductByCategory(this.value, 0)' data-width='100%' id='category0'
												data-plugin-selectTwo");
												?>
												<span class="error"></span>
											</div>
										</td>
										<td class="min-w-lg">
											<div class="form-group">
												<select data-plugin-selectTwo class="form-control sale_product" data-width="100%" name="sales[0][product]" id="product0">
													<option value=""><?php echo translate('first_select_the_category'); ?></option>
												</select>
												<span class="error"></span>
											</div>
										</td>
										<td class="min-w-sm">
											<div class="form-group">
												<div class="input-group">
													<input type="text" class="form-control purchase_quantity" name="sales[0][quantity]" value="1" id="quantity0" autocomplete="off" />
													<span class="input-group-addon">-</span>
												</div>
												<span class="error"></span>
												<span class="quantity_remain"></span>
											</div>
										</td>
									</tr>
								</tbody>
								<tfoot>
									<tr>
										<td colspan="6">
											<button type="button" class="btn btn-default" onclick="addRows()"> <i class="fas fa-plus-circle"></i> <?php echo translate('add_rows'); ?></button>
										</td>
									</tr>
								</tfoot>
							</table>
						</div>
					<?php } ?>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-9 col-md-3">
								<button type="submit" name="purchase" id="savebtn" value="1" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>
					</footer>
				<?php echo form_close(); ?>
			</div>
<?php } ?>
		</div>
	</div>
</section>

<!-- Advance Salary View Modal -->
<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide" id="modal">
	<section class="panel" id='quick_view'></section>
</div>

<?php if (!empty($branch_id)) { ?>
<script type="text/javascript">
	function getDynamicInput(value) {
		var html_row = "";
		html_row += '<tr id="row_' + value + '">';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="category' + value + '" name="sales[' + value + '][category]" class="form-control select2_in"  onchange="getProductByCategory(this.value, ' + value + ')" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
<?php
$categorylist = $this->app_lib->getTable('product_category');
foreach($categorylist as $category):
?>
		html_row += '<option value="<?php echo html_escape($category['id']) ?>"><?php echo html_escape($category['name']); ?></option>';
<?php endforeach; ?>
		html_row += '</select>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '<td><div class="form-group">';
		html_row += '<select id="product' + value + '" name="sales[' + value + '][product]" class="form-control sale_product" >';
		html_row += '<option value=""><?php echo translate('select'); ?></option>';
		html_row += '</select>';
		html_row += '<span class="error"></span></div></td>';
		html_row += '</div></td>';
		html_row += '<td class="min-w-md"><div class="form-group" style="float: left; width: 70% !important;">';
		html_row += '<div class="input-group">';
		html_row += '<input id="quantity' + value + '" type="number" name="sales[' + value + '][quantity]" class="form-control purchase_quantity" autocomplete="off"  value="1" />';
		html_row += '<span class="input-group-addon">-</span></div>';
		html_row += '<span class="quantity_remain"></span>';
		html_row += '<span class="error"></span></div>';
		html_row += '<button type="button" class="btn btn-danger" onclick="deleteRow(' + value + ')" style="float: right; max-width: 30%"><i class="fas fa-times"></i> </button>';
		html_row += '</td>';
		html_row += '</tr>';
		return html_row;
	}
</script>
<?php } ?>

<script type="text/javascript">
	$(document).ready(function () {
		$(document).on('change', '#branchID', function() {
			var branchID = $(this).val();
			getClassByBranch(branchID);
			$('#roleID').val('').trigger('change.select2');
			$('#receiverID').empty().html("<option value=''><?=translate('select_user')?>");
			$.ajax({
				url: "<?=base_url('inventory/issueItems')?>",
				type: 'POST',
				data: { branch_id: branchID },
				success: function (data) {
					$('#purchaseItems').html(data).slideDown();
					$(".selectTwo").each(function() {
						var $this = $(this);
						$this.themePluginSelect2({});
					});
				}
			});
		});
		
		$(document).on('change', '#roleID', function() {
			var roleID = $(this).val();
			var branchID = $('#branchID').val();
			if(roleID == 6){
		        $.ajax({
		            url: base_url + "communication/getParentListBranch",
		            type: 'POST',
		            data: {
		                branch_id: branchID
		            },
		            success: function (data) {
		                $('#receiverID').html(data);
		            }
		        });
				$(".class_div").hide(400);
			} else if(roleID == 7) {
				$(".class_div").show(400);;
				$('#receiverID').empty().html("<option value=''><?=translate('select_user')?>");
			} else {
				$(".class_div").hide(400);
		        $.ajax({
		            url: base_url + "communication/getStafflistRole",
		            type: 'POST',
		            data: {
		                branch_id: branchID,
		                role_id: roleID
		            },
		            success: function (data) {
		                $('#receiverID').html(data);
		            }
		        });	
			}
		});
		
		$(document).on('change', '#class_id', function() {
			var classID = $(this).val();
			var branchID = $('#branchID').val();
	        $.ajax({
	            url: base_url + "communication/getStudentByClass",
	            type: 'POST',
	            data: {
	                branch_id: branchID,
	                class_id: classID
	            },
	            success: function (data) {
	                $('#receiverID').html(data);
	            }
	        });
		});
	});

	var count = 1;
	$(document).ready(function() {
		$(document).on('change', '.sale_product', function() {
			var row = $(this).closest('tr');
			var id = $(this).val();
			$.ajax({
				type: "POST",
				dataType: "json",
				data: {'id' : id},
				url: "<?php echo base_url('inventory/getSaleprice'); ?>",
				success: function (result) {
					row.find('.quantity_remain').html(result.availablestock);
					row.find('.input-group-addon').html(result.unit);
				}
			});
		});

	});

	function addRows(){
		var tbody = $('#tableID').children('tbody');
		tbody.append(getDynamicInput(count));
		$(`#product${count}, .select2_in`).select2({
		    theme: "bootstrap",
		    width: "100%"
		});
		count++;
	}

    function deleteRow(id) {
        $("#row_" + id).remove();
    }

	function getProductByCategory(categoryid, rowid) {
		var branchID = $('#branchID').val();
	    var product_id = 0;
	    $("#product" + rowid).html("<option value=''><?php echo translate('exploring'); ?>...</option>");
	    $.ajax({
	        type: "POST",
	        url: "<?php echo base_url('inventory/getProductByCategory'); ?>",
	        data: {
	        	"selected_id": product_id,
	        	"branch_id": branchID,
	        	"category_id": categoryid
	        },
	        dataType: "html",
	        success: function(data) {
	           $("#product" + rowid).html(data);
	        }
	    });	
	}

	$("#frmSubmit").on('submit', (function (e) {
	    e.preventDefault();
	    var btn = $("#savebtn");
	    btn.button('loading');
	    $.ajax({
	        url: $(this).attr('action'),
	        type: "POST",
	        data: $(this).serialize(),
	        dataType: 'json',
	        success: function (data) {
	            if (data.status == "fail") {
	            	console.log(data.error);
	                $.each(data.error, function (index, value) {
	                	$('#' + index).parents('.form-group').find('.error').html(value);
	                });
	                btn.button('reset');
	            } else {
	               window.location.href = data.url;
	            }
	        },
	        error: function () {
	            //  alert("Fail")
	        }
	    });
	}));

	function getIssueDetails(id) {
	    $.ajax({
	        url: base_url + 'inventory/getIssueDetails',
	        type: 'POST',
	        data: {'id': id},
	        dataType: "html",
	        success: function (data) {
	            $('#quick_view').html(data);
	            mfp_modal('#modal');
	        }
	    });
	}
</script>