<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendance_period_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }
    public function getStudentAttendence($classID, $sectionID, $date, $subject_timetableID, $branchID)
    {
        $sql = "SELECT `enroll`.`id` as `enroll_id`,`enroll`.`roll`,`student`.`first_name`,`student`.`last_name`,`student`.`id` as `student_id`,`student`.`register_no`,`student_subject_attendance`.`id` as `att_id`,`student_subject_attendance`.`status` as `att_status`,`student_subject_attendance`.`remark` as `att_remark` FROM `enroll` INNER JOIN `student` ON `student`.`id` = `enroll`.`student_id` LEFT JOIN `student_subject_attendance` ON `student_subject_attendance`.`enroll_id` = `enroll`.`id` AND `student_subject_attendance`.`date` = " . $this->db->escape($date) . " AND `student_subject_attendance`.`subject_timetable_id` = " . $this->db->escape($subject_timetableID) . " WHERE `enroll`.`class_id` = " . $this->db->escape($classID) . " AND `enroll`.`section_id` = " . $this->db->escape($sectionID) . " AND `enroll`.`branch_id` = " . $this->db->escape($branchID) . " AND `enroll`.`session_id` = " . $this->db->escape(get_session_id());
        return $this->db->query($sql)->result_array();
    }

    public function getDailyStudentReport($branchID = '', $date = '')
    {
        $sql = "SELECT class.name as `class_name`,section.name as `section_name`, SUM(CASE WHEN `status` = 'P' THEN 1 ELSE 0 END) AS 'present',SUM(CASE WHEN `status` = 'A' THEN 1 ELSE 0 END) AS 'absent',SUM(CASE WHEN `status` = 'L' THEN 1 ELSE 0 END) AS 'late',SUM(CASE WHEN `status` = 'HD' THEN 1 ELSE 0 END) AS 'half_day' FROM `student_attendance` JOIN `enroll` on student_attendance.enroll_id=enroll.id INNER JOIN `sections_allocation` on (enroll.class_id=sections_allocation.class_id and enroll.section_id=sections_allocation.section_id) inner join `class` on class.id=sections_allocation.class_id INNER JOIN `section` on section.id=sections_allocation.section_id WHERE `enroll`.`session_id`=" . $this->db->escape(get_session_id()) . " AND enroll.branch_id = " . $this->db->escape($branchID) . " AND student_attendance.date = " . $this->db->escape($date) . " GROUP BY sections_allocation.id ORDER BY sections_allocation.class_id";
        $query = $this->db->query($sql);
        $count_studentattendance = $query->result();
        return $count_studentattendance;
    }

    // check attendance by staff id and date
    public function get_attendance_by_date($studentID, $date, $timetable_id)
    {
        $sql = "SELECT `remark`,`status` FROM `student_subject_attendance` WHERE `enroll_id` = " . $this->db->escape($studentID) . " AND DATE(`date`) = " . $this->db->escape($date) . " AND `subject_timetable_id` = " . $this->db->escape($timetable_id);
        return $this->db->query($sql)->row_array();
    }

    // check attendance by staff id and date
    public function get_attendance_by_subjectID($studentID, $classID, $sectionID, $date, $day, $subject_id)
    {

        $sql = "SELECT `timetable_class`.`id`,`timetable_class`.`time_start`,`timetable_class`.`time_end` FROM `timetable_class` LEFT JOIN `section` ON `section`.`id` = `timetable_class`.`section_id` INNER JOIN `subject` ON `subject`.`id` = `timetable_class`.`subject_id` WHERE `timetable_class`.`day` = " . $this->db->escape($day) . " AND `timetable_class`.`session_id` = " . get_session_id() . " AND `timetable_class`.`class_id` = " . $classID . " AND `timetable_class`.`section_id` = " . $sectionID;
        if (!empty($subject_id)) {
            $sql .= " AND `timetable_class`.`subject_id` = " . $this->db->escape($subject_id);
        }
        $sql .= " GROUP BY `timetable_class`.`subject_id`";
        $timetable_class = $this->db->query($sql)->row();

        if (!empty($timetable_class)) {
            $sql = "SELECT `remark`,`status` FROM `student_subject_attendance` WHERE `enroll_id` = " . $this->db->escape($studentID) . " AND DATE(`date`) = " . $this->db->escape($date) . " AND `subject_timetable_id` = " . $this->db->escape($timetable_class->id);
            $r = $this->db->query($sql)->row_array();
            if (!empty($r)) {
                $r['time'] = date("g:i A", strtotime($timetable_class->time_start)) . " - " . date("g:i A", strtotime($timetable_class->time_end));
                return $r;
            }
        }
        return false;
    }

    public function getSubjectByClassSection($classID = '', $sectionID = '', $day = '')
    {
        $subject_condition = "";
        if (loggedin_role_id() == 3) {
            $restricted = $this->getSingle('branch', get_loggedin_branch_id(), true)->teacher_restricted;
            if ($restricted == 1) {
                $getClassTeacher = $this->subject_model->getClassTeacherByClassSection($classID, $sectionID);
                if ($getClassTeacher != true) {
                    $subject_condition = " AND `timetable_class`.`teacher_id` = " . get_loggedin_user_id();
                }
            }
        }
        $sql = "SELECT `timetable_class`.`id`, `timetable_class`.`time_start`,`timetable_class`.`time_end`,`subject`.`name` as `subjectname`,`subject`.`subject_code` FROM `timetable_class` LEFT JOIN `section` ON `section`.`id` = `timetable_class`.`section_id` INNER JOIN `subject` ON `subject`.`id` = `timetable_class`.`subject_id` WHERE `timetable_class`.`day` = " . $this->db->escape($day) . " AND `timetable_class`.`session_id` = " . get_session_id() . " AND `timetable_class`.`class_id` = " . $classID . " AND `timetable_class`.`section_id` = " . $sectionID . $subject_condition . " GROUP BY `timetable_class`.`subject_id`";
        return $this->db->query($sql);
    }

    public function getSubjectAttendanceReport($classID = '', $sectionID = '', $date = '', $subject_timetableID = '', $branchID = '')
    {
        $sql = "SELECT `ssa`.*, `s`.`first_name`, `s`.`last_name`, `s`.`register_no`, `s`.`category_id`, `s`.`mobileno`,`e`.`roll` FROM `student_subject_attendance` as `ssa` INNER JOIN `enroll` as `e` ON `e`.`id` = `ssa`.`enroll_id` LEFT JOIN `student` as `s` ON `s`.`id` = `e`.`student_id` WHERE `ssa`.`subject_timetable_id` = " . $this->db->escape($subject_timetableID) . " AND `ssa`.`branch_id` = " . $this->db->escape($branchID) . " AND `e`.`class_id` = " . $this->db->escape($classID) . " AND `e`.`section_id` = " . $this->db->escape($sectionID) . " AND `e`.`session_id` = " . $this->db->escape(get_session_id());
        return $this->db->query($sql)->result_array();
    }

    public function getSubjectBytimetableID($id = '')
    {
        $this->db->select('subject.name');
        $this->db->from('timetable_class');
        $this->db->join('subject', 'subject.id = timetable_class.subject_id', 'inner');
        $this->db->where('timetable_class.id', $id);
        $name  = $this->db->get()->row()->name;
        return $name;
    }
}
