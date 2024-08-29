<li class="nav-parent <?php if ($main_menu == 'qr_attendance' || $main_menu == 'qr_attendance_report') echo 'nav-expanded nav-active';?>">
	<a>
		<i class="fas fa-qrcode"></i><span><?=translate('qr_code') . " " . translate('attendance')?></span>
	</a>
	<ul class="nav nav-children">
		<?php if(get_permission('qr_code_student_attendance', 'is_add')) { ?>
		<li class="<?php if ($sub_page == 'qrcode_attendance/student_entries') echo 'nav-active';?>">
			<a href="<?=base_url('qrcode_attendance/student_entry')?>">
				<span><i class="fas fa-caret-right"></i><?=translate('student')?></span>
			</a>
		</li>
		<?php } if(get_permission('qr_code_employee_attendance', 'is_add')) { ?>
		<li class="<?php if ($sub_page == 'qrcode_attendance/staff_entries') echo 'nav-active';?>">
			<a href="<?=base_url('qrcode_attendance/staff_entry')?>">
				<span><i class="fas fa-caret-right"></i><?=translate('employee')?></span>
			</a>
		</li>
		<?php }  ?>

		<li class="nav-parent <?php if ($main_menu == 'qr_attendance_report') echo 'nav-expanded nav-active'; ?>">
			<a><i class="fas fa-print"></i><span><?php echo translate('reports'); ?></span></a>
			<ul class="nav nav-children">
				<li class="<?php if ($sub_page == 'qrcode_attendance/studentbydate') echo 'nav-active';?>">
					<a href="<?=base_url('qrcode_attendance/studentbydate')?>">Student Reports By Date</a>
				</li>
				<li class="<?php if ($sub_page == 'qrcode_attendance/staffbydate') echo 'nav-active';?>">
					<a href="<?=base_url('qrcode_attendance/staffbydate')?>">Employee Reports By Date</a>
				</li>
			</ul>
		</li>
	</ul>
</li>