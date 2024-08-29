<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Multiclass_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getStudentListByClassSection($classID = '', $sectionID = '', $branchID = '')
    {
        $sql = "SELECT `e`.*, `s`.`photo`,CONCAT_WS(' ',`s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`gender`, `s`.`parent_id`, `s`.`mobileno`, `s`.`birthday`, `s`.`admission_date`, `l`.`active`, `l`.`username` as `stu_username` FROM `enroll` as `e` INNER JOIN `student` as `s` ON `e`.`student_id` = `s`.`id` INNER JOIN `login_credential` as `l` ON `l`.`user_id` = `s`.`id` and `l`.`role` = 7 WHERE `e`.`class_id` = " . $this->db->escape($classID) . " AND `e`.`branch_id` = " . $this->db->escape($branchID) . " AND `e`.`session_id` = " . $this->db->escape(get_session_id()) . " AND `e`.`section_id` = " . $this->db->escape($sectionID) . " AND `l`.`active` = 1 ORDER BY `e`.`id` ASC";
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $key => $value) {
            $result[$key]['class_details'] = $this->getClassDetails($value['student_id']);
        }
        return $result;
    }

    function getClassDetails($student_id = '')
    {
        $this->db->select('e.id,class.name as class_name,section.name as section_name');
        $this->db->from('enroll as e');
        $this->db->join('class', 'class.id = e.class_id', 'left');
        $this->db->join('section', 'section.id = e.section_id', 'left');
        $this->db->where('e.student_id', $student_id);
        $this->db->where('e.session_id', get_session_id());
        $this->db->order_by('id', 'asc');
        $r = $this->db->get()->result();
        $nameList = '';
        foreach ($r as $key => $value) {
            $nameList .= $value->class_name . " (" . $value->section_name . ')<br>';
        }
        return $nameList;
    }
}