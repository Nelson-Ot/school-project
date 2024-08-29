<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Attendance_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getStudentAttendence($classID, $sectionID, $date, $branchID)
    {
        $sql = "SELECT `enroll`.`id` as `enroll_id`,`enroll`.`roll`,`student`.`first_name`,`student`.`last_name`,`student`.`id` as `student_id`,`student`.`register_no`,`student_attendance`.`id` as `att_id`,`student_attendance`.`status` as `att_status`,`student_attendance`.`remark` as `att_remark` FROM `enroll` INNER JOIN `student` ON `student`.`id` = `enroll`.`student_id` LEFT JOIN `student_attendance` ON `student_attendance`.`enroll_id` = `enroll`.`id` AND `student_attendance`.`date` = " . $this->db->escape($date) . " WHERE `enroll`.`class_id` = " . $this->db->escape($classID) . " AND `enroll`.`section_id` = " . $this->db->escape($sectionID) . " AND `enroll`.`branch_id` = " . $this->db->escape($branchID) . " AND `enroll`.`session_id` = " . $this->db->escape(get_session_id());
        return $this->db->query($sql)->result_array();
    }

    public function getStaffAttendence($roleID, $date, $branchID)
    {
        $sql = "SELECT `staff`.*, `lc`.`role`, `sa`.`id` as `atten_id`, IFNULL(`sa`.`status`, 0) as `att_status`, `sa`.`remark` as `att_remark` FROM `staff` LEFT JOIN `login_credential` as `lc` ON `lc`.`user_id` = `staff`.`id` and `lc`.`role` != '6' and `lc`.`role` != '7' LEFT JOIN `staff_attendance` as `sa` ON `sa`.`staff_id` = `staff`.`id` and `sa`.`date` = " . $this->db->escape($date) . " WHERE `staff`.`branch_id` = " . $this->db->escape($branchID) . " AND `lc`.`role` = " . $this->db->escape($roleID) . " AND `lc`.`active` = '1' ORDER BY `staff`.`id` ASC";
        return $this->db->query($sql)->result_array();
    }

    public function getExamAttendence($classID, $sectionID, $examID, $subjectID, $branchID)
    {
        $sql = "SELECT enroll.student_id,enroll.roll,student.first_name,student.last_name,student.register_no,exam_attendance.id as `atten_id`, exam_attendance.status as `att_status`,exam_attendance.remark as `att_remark` FROM `enroll` LEFT JOIN student ON student.id = enroll.student_id LEFT JOIN exam_attendance ON exam_attendance.student_id = student.id AND exam_attendance.exam_id = " . $this->db->escape($examID) . " AND exam_attendance.subject_id = " . $this->db->escape($subjectID) . " WHERE enroll.class_id = " . $this->db->escape($classID) . " AND enroll.section_id = " . $this->db->escape($sectionID) . " AND enroll.branch_id = " . $this->db->escape($branchID) . " AND enroll.session_id = " . $this->db->escape(get_session_id());
        return $this->db->query($sql)->result_array();
    }

    public function getStudentList($branch_id, $class_id, $section_id)
    {
        $this->db->select('e.id as enroll_id,e.roll,s.first_name,s.last_name,s.register_no');
        $this->db->from('enroll as e');
        $this->db->join('student as s', 's.id = e.student_id', 'inner');
        $this->db->join('login_credential', 'login_credential.user_id = s.id and login_credential.role = "7"', 'inner');
        $this->db->where('e.class_id', $class_id);
        $this->db->where('e.section_id', $section_id);
        $this->db->where('e.branch_id', $branch_id);
        $this->db->where('login_credential.active', 1);
        $this->db->where('e.session_id', get_session_id());
        return $this->db->get()->result_array();
    }

    // GET STAFF ALL DETAILS
    public function getStaffList($branch_id = '', $role_id = '', $active = 1)
    {
        $this->db->select('staff.*,login_credential.role as role_id, roles.name as role');
        $this->db->from('staff');
        $this->db->join('login_credential', 'login_credential.user_id = staff.id and login_credential.role != "6" and login_credential.role != "7"', 'inner');
        $this->db->join('roles', 'roles.id = login_credential.role', 'left');
        if (!empty($branch_id)) {
            $this->db->where('staff.branch_id', $branch_id);
        }
        $this->db->where('login_credential.role', $role_id);
        $this->db->where('login_credential.active', $active);
        $this->db->order_by('staff.id', 'ASC');
        return $this->db->get()->result_array();
    }

    public function getExamReport($data)
    {
        $sql = "SELECT `ea`.*, `s`.`first_name`, `s`.`last_name`, `s`.`register_no`, `s`.`category_id`, `e`.`roll`, `sb`.`name` as `subject_name` FROM `exam_attendance` as `ea` LEFT JOIN `enroll` as `e` ON `e`.`student_id` = `ea`.`student_id` LEFT JOIN `student` as `s` ON `s`.`id` = `ea`.`student_id` LEFT JOIN `subject` as `sb` ON `sb`.`id` = `ea`.`subject_id` WHERE `ea`.`exam_id` = " . $this->db->escape($data['exam_id']) . " AND `ea`.`subject_id` = " . $this->db->escape($data['subject_id']) . " AND `ea`.`branch_id` = " . $this->db->escape($data['branch_id']) . " AND `e`.`class_id` = " . $this->db->escape($data['class_id']) . " AND `e`.`section_id` = " . $this->db->escape($data['section_id']) . " AND `e`.`session_id` = " . $this->db->escape(get_session_id());
        return $this->db->query($sql)->result_array();
    }

    // check attendance by staff id and date
    public function get_attendance_by_date($studentID, $date)
    {
        $sql = "SELECT `student_attendance`.* FROM `student_attendance` WHERE `enroll_id` = " . $this->db->escape($studentID) . " AND DATE(`date`) = " . $this->db->escape($date);
        return $this->db->query($sql)->row_array();
    }

    public function getWeekendDaysSession($branch_id = '')
    {
        $date_from = strtotime(date("Y-01-01"));
        $date_to = strtotime(date("Y-12-31"));
        $oneDay = 60 * 60 * 24;
        $allDays = array(
            '0' => 'Sunday',
            '1' => 'Monday',
            '2' => 'Tuesday',
            '3' => 'Wednesday',
            '4' => 'Thursday',
            '5' => 'Friday',
            '6' => 'Saturday',
        );

        $weekendDay = $this->application_model->getWeekends($branch_id);
        $weekendArrays = explode(',', $weekendDay);
        $weekendDateArrays = array();
        for ($i = $date_from; $i <= $date_to; $i = $i + $oneDay) {
            if ($weekendDay != "") {
                foreach ($weekendArrays as $weekendValue) {
                    if ($weekendValue >= 0 && $weekendValue <= 6) {
                        if (date('l', $i) == $allDays[$weekendValue]) {
                            $weekendDateArrays[] = date('Y-m-d', $i);
                        }
                    }
                }
            }
        }
        return $weekendDateArrays;
    }

    public function getHolidays($school_id = '')
    {
        $this->db->where('branch_id', $school_id);
        $this->db->where('session_id', get_session_id());
        $this->db->where(['status' => 1, 'type' => 'holiday']);
        $this->db->order_by('start_date', 'asc');
        $holidays = $this->db->get('event')->result();
        $allHolidayList = array();
        if (!empty($holidays)) {
            foreach ($holidays as $holiday) {
                $from_date = strtotime($holiday->start_date);
                $to_date = strtotime($holiday->end_date);
                $oneday = 60 * 60 * 24;
                for ($i = $from_date; $i <= $to_date; $i = $i + $oneday) {
                    $allHolidayList[] = date('Y-m-d', $i);
                }
            }
        }
        $uniqueHolidays = array_unique($allHolidayList);
        if (!empty($uniqueHolidays)) {
            $uniqueHolidays = implode('","', $uniqueHolidays);
        } else {
            $uniqueHolidays = '';
        }
        return $uniqueHolidays;
    }

    public function getDailyStudentReport($branchID = '', $date = '')
    {
        $sql = "SELECT class.name as `class_name`,section.name as `section_name`, SUM(CASE WHEN `status` = 'P' THEN 1 ELSE 0 END) AS 'present',SUM(CASE WHEN `status` = 'A' THEN 1 ELSE 0 END) AS 'absent',SUM(CASE WHEN `status` = 'L' THEN 1 ELSE 0 END) AS 'late',SUM(CASE WHEN `status` = 'HD' THEN 1 ELSE 0 END) AS 'half_day' FROM `student_attendance` JOIN `enroll` on student_attendance.enroll_id=enroll.id INNER JOIN `sections_allocation` on (enroll.class_id=sections_allocation.class_id and enroll.section_id=sections_allocation.section_id) inner join `class` on class.id=sections_allocation.class_id INNER JOIN `section` on section.id=sections_allocation.section_id WHERE `enroll`.`session_id`=" . $this->db->escape(get_session_id()) . " AND enroll.branch_id = " . $this->db->escape($branchID) . " AND student_attendance.date = " . $this->db->escape($date) . " GROUP BY sections_allocation.id ORDER BY sections_allocation.class_id";
        $query = $this->db->query($sql);
        $count_studentattendance = $query->result();
        return $count_studentattendance;
    }

    public function stuAttendanceCount_by_date($enroll_id = '', $start = '', $end = '', $type = '')
    {
        $sql = "SELECT count(`student_attendance`.`id`) as `status_count` FROM `student_attendance` WHERE `enroll_id` = " . $this->db->escape($enroll_id) . " AND DATE(`date`) >= " . $this->db->escape($start) . " AND DATE(`date`) <= " . $this->db->escape($end) . " AND `status` = " . $this->db->escape($type);
        return $this->db->query($sql)->row()->status_count;
    }
}
