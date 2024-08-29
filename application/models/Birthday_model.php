<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Birthday_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getStudentListByBirthday($branchID, $start, $end)
    {
        $sql = "SELECT `e`.*, `s`.`photo`, CONCAT_WS(' ',`s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`parent_id`, `s`.`email`, `s`.`mobileno`, `s`.`birthday`, `c`.`name` as `class_name`, `se`.`name` as `section_name` FROM `enroll` as `e` INNER JOIN `student` as `s` ON `e`.`student_id` = `s`.`id` LEFT JOIN `class` as `c` ON `e`.`class_id` = `c`.`id` LEFT JOIN `section` as `se` ON `e`.`section_id`=`se`.`id` WHERE `e`.`session_id` = " . $this->db->escape(get_session_id());
        if (!empty($start)) {
            $sql .= " AND MONTH(`s`.`birthday`) >= " . $this->db->escape(date('m', strtotime($start))) .  " AND DAY(`s`.`birthday`) >= " . $this->db->escape(date('d', strtotime($start)));
            $sql .= " AND MONTH(`s`.`birthday`) <= " . $this->db->escape(date('m', strtotime($end))) .  " AND DAY(`s`.`birthday`) <= " . $this->db->escape(date('d', strtotime($end)));
        }
        if (!empty($branchID)) {
            $sql .= " AND `e`.`branch_id` = " . $this->db->escape($branchID);
        }
        $sql .= " ORDER BY `s`.`id` ASC";
        return $this->db->query($sql)->result_array();
    }

    public function getStaffListByBirthday($branchID, $start, $end)
    {
        $sql = "SELECT `s`.*,`sd`.`name` as `designation_name`,`roles`.`name` as `role_name` FROM `staff` as `s` INNER JOIN `login_credential` as `lc` ON `lc`.`user_id` = `s`.`id` AND `lc`.`role` != '7' AND `lc`.`role` != '6' LEFT JOIN `staff_designation` as `sd` ON `sd`.`id` = `s`.`designation` LEFT JOIN `roles` ON `roles`.`id` = `lc`.`role`";
        $sql .= " WHERE MONTH(`s`.`birthday`) >= " . $this->db->escape(date('m', strtotime($start))) .  " AND DAY(`s`.`birthday`) >= " . $this->db->escape(date('d', strtotime($start)));
        $sql .= " AND MONTH(`s`.`birthday`) <= " . $this->db->escape(date('m', strtotime($end))) .  " AND DAY(`s`.`birthday`) <= " . $this->db->escape(date('d', strtotime($end)));
        
        if (!empty($branchID)) {
            $sql .= " AND `s`.`branch_id` = " . $this->db->escape($branchID);
        }
        $sql .= " ORDER BY `s`.`id` ASC";
        return $this->db->query($sql)->result_array();
    }
}
