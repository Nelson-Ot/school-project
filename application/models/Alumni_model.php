<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Alumni_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function getStudentListByClassSection($classID = '', $sectionID = '', $branchID = '', $session_id ='')
    {
        $sql = "SELECT `e`.`id`, CONCAT_WS(' ',`s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`gender`,`alumni`.`id` as `alumni_id`, `alumni`.`email`, `alumni`.`mobile_no`,`alumni`.`address`,`alumni`.`profession`,`alumni`.`photo`,`c`.`name` as `class_name`, `se`.`name` as `section_name` FROM `enroll` as `e` INNER JOIN `student` as `s` ON `e`.`student_id` = `s`.`id` LEFT JOIN `class` as `c` ON `e`.`class_id` = `c`.`id` LEFT JOIN `alumni_students` as `alumni` ON `alumni`.`enroll_id` = `e`.`id` LEFT JOIN `section` as `se` ON `e`.`section_id`=`se`.`id` WHERE `e`.`class_id` = " . $this->db->escape($classID) . " AND `e`.`branch_id` = " . $this->db->escape($branchID) . " AND `e`.`session_id` = " . $this->db->escape($session_id);
        if (!empty($sectionID)) {
            $sql .= " AND `e`.`section_id` = " . $this->db->escape($sectionID);
        }
        $sql .= " AND `e`.`is_alumni` = '1'  ORDER BY `s`.`id` ASC";
        
        return $this->db->query($sql)->result_array();
    }

    public function getList($branchID = '', $classID = '', $sectionID = '',  $session_id = '')
    {
        $sql = "SELECT `e`.`id`, CONCAT_WS(' ',`s`.`first_name`, `s`.`last_name`) as `fullname`,`alumni`.`id` as `alumni_id`, `alumni`.`email`, `alumni`.`mobile_no` FROM `enroll` as `e` INNER JOIN `student` as `s` ON `e`.`student_id` = `s`.`id` INNER JOIN `alumni_students` as `alumni` ON `alumni`.`enroll_id` = `e`.`id` WHERE `e`.`branch_id` = " . $this->db->escape($branchID);
        if (!empty($classID)) {
            $sql .= " AND `e`.`class_id` = " . $this->db->escape($classID) . " AND `e`.`session_id` = " . $this->db->escape($session_id);
        }
        if (!empty($sectionID)) {
            $sql .= " AND `e`.`section_id` = " . $this->db->escape($sectionID);
        }
        $sql .= " AND `e`.`is_alumni` = '1'  ORDER BY `s`.`id` ASC";
        return $this->db->query($sql)->result_array();
    }

}
