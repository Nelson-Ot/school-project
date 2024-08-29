<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.8
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Attendance.php
 * @copyright : Reserved RamomCoder Team
 */

class Attendance extends Admin_Controller
{
    protected $getAttendanceType;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('attendance_model');
        $this->load->model('subject_model');
        $this->load->model('sms_model');
        if (!moduleIsEnabled('attendance')) {
            access_denied();
        }
        $this->getAttendanceType = $this->app_lib->getAttendanceType();
    }

    public function index()
    {
        if (get_loggedin_id()) {
            redirect(base_url('dashboard'), 'refresh');
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    // student submitted attendance all data are prepared and stored in the database here
    public function student_entry()
    {
        if (!get_permission('student_attendance', 'is_add')) {
            access_denied();
        }

        if ($this->getAttendanceType != 2 && $this->getAttendanceType != 0) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('class_id', translate('class'), 'required');
            $this->form_validation->set_rules('section_id', translate('section'), 'required');
            $this->form_validation->set_rules('date', translate('date'), 'trim|required|callback_check_weekendday|callback_check_holiday|callback_get_valid_date');
            if ($this->form_validation->run() == true) {
                $classID = $this->input->post('class_id');
                $sectionID = $this->input->post('section_id');
                $date = $this->input->post('date');
                $this->data['date'] = $date;
                $this->data['attendencelist'] = $this->attendance_model->getStudentAttendence($classID, $sectionID, $date, $branchID);
            }
        }
        $this->data['getWeekends'] = $this->application_model->getWeekends($branchID);
        $this->data['getHolidays'] = $this->attendance_model->getHolidays($branchID);
        if (isset($_POST['save'])) {
            $attendance = $this->input->post('attendance');
            $date = $this->input->post('date');
            foreach ($attendance as $key => $value) {
                $attStatus = (isset($value['status']) ? $value['status'] : "");
                $studentID = $value['student_id'];
                $arrayAttendance = array(
                    'enroll_id' => $value['enroll_id'],
                    'status' => $attStatus,
                    'remark' => $value['remark'],
                    'date' => $date,
                    'branch_id' => $branchID,
                );
                if (empty($value['attendance_id'])) {
                    $this->db->insert('student_attendance', $arrayAttendance);
                } else {
                    $this->db->where('id', $value['attendance_id']);
                    $this->db->update('student_attendance', array('status' => $attStatus, 'remark' => $value['remark']));
                }
                // send student absent then sms
                if ($attStatus == 'A') {
                    $arrayAttendance['student_id'] = $studentID;
                    $this->sms_model->send_sms($arrayAttendance, 3);
                }
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            redirect(current_url());
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student_attendance');
        $this->data['sub_page'] = 'attendance/student_entries';
        $this->data['main_menu'] = 'attendance';
        $this->load->view('layout/index', $this->data);
    }

    public function getWeekendsHolidays()
    {
        if (!get_permission('student_attendance', 'is_add')) {
            ajax_access_denied();
        }
        if ($_POST) {
            $branchID = $this->input->post('branch_id');
            $getWeekends = $this->application_model->getWeekends($branchID);
            $getHolidays = $this->attendance_model->getHolidays($branchID);
            echo json_encode(['getWeekends' => $getWeekends, 'getHolidays' => '["' . $getHolidays . '"]']);
        }
    }

    // employees submitted attendance all data are prepared and stored in the database here
    public function employees_entry()
    {
        if (!get_permission('employee_attendance', 'is_add')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('staff_role', translate('role'), 'required');
            $this->form_validation->set_rules('date', translate('date'), 'trim|required|callback_check_weekendday|callback_check_holiday|callback_get_valid_date');
            if ($this->form_validation->run() == true) {
                $roleID = $this->input->post('staff_role');
                $date = $this->input->post('date');
                $this->data['date'] = $date;
                $this->data['attendencelist'] = $this->attendance_model->getStaffAttendence($roleID, $date, $branchID);
            }
        }
        $this->data['getWeekends'] = $this->application_model->getWeekends($branchID);
        if (isset($_POST['save'])) {
            $attendance = $this->input->post('attendance');
            $date = $this->input->post('date');
            foreach ($attendance as $key => $value) {
                $attStatus = (isset($value['status']) ? $value['status'] : "");
                $arrayAttendance = array(
                    'staff_id' => $value['staff_id'],
                    'status' => $attStatus,
                    'remark' => $value['remark'],
                    'date' => $date,
                    'branch_id' => $branchID,
                );
                if (empty($value['attendance_id'])) {
                    $this->db->insert('staff_attendance', $arrayAttendance);
                } else {
                    $this->db->where('id', $value['attendance_id']);
                    $this->db->update('staff_attendance', array('status' => $attStatus, 'remark' => $value['remark']));
                }
                // send student absent then sms
                if ($attStatus == 'A') {
                    $this->sms_model->send_sms($arrayAttendance, 3);
                }
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            redirect(current_url());
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('employee_attendance');
        $this->data['sub_page'] = 'attendance/employees_entries';
        $this->data['main_menu'] = 'attendance';
        $this->load->view('layout/index', $this->data);
    }

    // exam submitted attendance all data are prepared and stored in the database here
    public function exam_entry()
    {
        if (!get_permission('exam_attendance', 'is_add')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('exam_id', translate('exam'), 'required');
            $this->form_validation->set_rules('class_id', translate('class'), 'required');
            $this->form_validation->set_rules('section_id', translate('section'), 'required');
            $this->form_validation->set_rules('subject_id', translate('subject'), 'required');

            if ($this->form_validation->run() == true) {
                $classID = $this->input->post('class_id');
                $sectionID = $this->input->post('section_id');
                $examID = $this->input->post('exam_id');
                $subjectID = $this->input->post('subject_id');
                $this->data['class_id'] = $classID;
                $this->data['section_id'] = $sectionID;
                $this->data['exam_id'] = $examID;
                $this->data['subject_id'] = $subjectID;
                $this->data['attendencelist'] = $this->attendance_model->getExamAttendence($classID, $sectionID, $examID, $subjectID, $branchID);
            }
        }

        if (isset($_POST['save'])) {
            $attendance = $this->input->post('attendance');
            $subjectID = $this->input->post('subject_id');
            $examID = $this->input->post('exam_id');
            foreach ($attendance as $key => $value) {
                $attStatus = (isset($value['status']) ? $value['status'] : "");
                $arrayAttendance = array(
                    'student_id' => $value['student_id'],
                    'status' => $attStatus,
                    'remark' => $value['remark'],
                    'exam_id' => $examID,
                    'subject_id' => $subjectID,
                    'branch_id' => $branchID,
                );
                if (empty($value['attendance_id'])) {
                    $this->db->insert('exam_attendance', $arrayAttendance);
                } else {
                    $this->db->where('id', $value['attendance_id']);
                    $this->db->update('exam_attendance', array('status' => $attStatus, 'remark' => $value['remark']));
                }
                // send student absent then sms
                if ($attStatus == 'A') {
                    $this->sms_model->send_sms($arrayAttendance, 4);
                }
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            redirect(current_url());
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('exam_attendance');
        $this->data['sub_page'] = 'attendance/exam_entries';
        $this->data['main_menu'] = 'attendance';
        $this->load->view('layout/index', $this->data);
    }

    // student attendance reports are produced here
    public function studentwise_report()
    {
        if (!get_permission('student_attendance_report', 'is_view')) {
            access_denied();
        }

        if ($this->getAttendanceType != 2 && $this->getAttendanceType != 0) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($_POST) {
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['month'] = date('m', strtotime($this->input->post('timestamp')));
            $this->data['year'] = date('Y', strtotime($this->input->post('timestamp')));
            $this->data['days'] = date('t', strtotime($this->data['year'] . "-" . $this->data['month']));
            $this->data['studentlist'] = $this->attendance_model->getStudentList($branchID, $this->data['class_id'], $this->data['section_id']);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student_attendance');
        $this->data['sub_page'] = 'attendance/student_report';
        $this->data['main_menu'] = 'attendance_report';
        $this->load->view('layout/index', $this->data);
    }

    public function student_classreport()
    {
        if (!get_permission('student_attendance_report', 'is_view')) {
            access_denied();
        }

        if ($this->getAttendanceType != 2 && $this->getAttendanceType != 0) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($_POST) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('date', translate('date'), 'trim|required|callback_get_valid_date');
            if ($this->form_validation->run() == true) {
                $this->data['date'] = $this->input->post('date');
                $this->data['attendancelist'] = $this->attendance_model->getDailyStudentReport($branchID, $this->data['date']);
            }
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student') . ' ' . translate('daily_reports');
        $this->data['sub_page'] = 'attendance/student_classreport';
        $this->data['main_menu'] = 'attendance_report';
        $this->load->view('layout/index', $this->data);
    }

    public function studentwise_overview()
    {
        if (!get_permission('student_attendance_report', 'is_view')) {
            access_denied();
        }

        if ($this->getAttendanceType != 2 && $this->getAttendanceType != 0) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($_POST) {

            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('attendance_type', translate('attendance_type'), 'required');
            $this->form_validation->set_rules('class_id', translate('class'), 'required');
            $this->form_validation->set_rules('section_id', translate('section'), 'required');
            $this->form_validation->set_rules('daterange', translate('date'), 'required');

            if ($this->form_validation->run() == true) {
                $daterange = explode(' - ', $this->input->post('daterange'));
                $start = date("Y-m-d", strtotime($daterange[0]));
                $end = date("Y-m-d", strtotime($daterange[1]));

                $this->data['class_id'] = $this->input->post('class_id');
                $this->data['section_id'] = $this->input->post('section_id');
                $this->data['start'] = $start;
                $this->data['end'] = $end;
                $this->data['studentlist'] = $this->application_model->getStudentListByClassSection($this->data['class_id'], $this->data['section_id'], $branchID);
            }
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/daterangepicker/daterangepicker.css',
            ),
            'js' => array(
                'vendor/moment/moment.js',
                'vendor/daterangepicker/daterangepicker.js',
            ),
        );
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student_attendance');
        $this->data['sub_page'] = 'attendance/studentwise_overview';
        $this->data['main_menu'] = 'attendance_report';
        $this->load->view('layout/index', $this->data);
    }

    /* employees attendance reports are produced here */
    public function employeewise_report()
    {
        if (!get_permission('employee_attendance_report', 'is_view')) {
            access_denied();
        }

        if ($_POST) {
            $this->data['branch_id'] = $this->application_model->get_branch_id();
            $this->data['role_id'] = $this->input->post('staff_role');
            $this->data['month'] = date('m', strtotime($this->input->post('timestamp')));
            $this->data['year'] = date('Y', strtotime($this->input->post('timestamp')));
            $this->data['days'] = date('t', strtotime($this->data['year'] . "-" . $this->data['month']));
            $this->data['stafflist'] = $this->attendance_model->getStaffList($this->data['branch_id'], $this->data['role_id']);
        }
        $this->data['title'] = translate('employee_attendance');
        $this->data['sub_page'] = 'attendance/employees_report';
        $this->data['main_menu'] = 'attendance_report';
        $this->load->view('layout/index', $this->data);
    }

    /* student exam attendance reports are produced here */
    public function examwise_report()
    {
        if (!get_permission('exam_attendance_report', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($_POST) {
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['exam_id'] = $this->input->post('exam_id');
            $this->data['subject_id'] = $this->input->post('subject_id');
            $this->data['branch_id'] = $this->application_model->get_branch_id();
            $this->data['examreport'] = $this->attendance_model->getExamReport($this->data);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('exam_attendance');
        $this->data['sub_page'] = 'attendance/exam_report';
        $this->data['main_menu'] = 'attendance_report';
        $this->load->view('layout/index', $this->data);
    }

    public function get_valid_date($date)
    {
        $present_date = date('Y-m-d');
        $date = date("Y-m-d", strtotime($date));
        if ($date > $present_date) {
            $this->form_validation->set_message("get_valid_date", "Please Enter Correct Date");
            return false;
        } else {
            return true;
        }
    }

    public function check_holiday($date)
    {
        $branchID = $this->application_model->get_branch_id();
        $getHolidays = $this->attendance_model->getHolidays($branchID);
        $getHolidaysArray = explode('","', $getHolidays);

        if (!empty($getHolidaysArray)) {
            if (in_array($date, $getHolidaysArray)) {
                $this->form_validation->set_message('check_holiday', 'You have selected a holiday.');
                return false;
            } else {
                return true;
            }
        }
    }

    public function check_weekendday($date)
    {
        $branchID = $this->application_model->get_branch_id();
        $getWeekendDays = $this->attendance_model->getWeekendDaysSession($branchID);
        if (!empty($getWeekendDays)) {
            if (in_array($date, $getWeekendDays)) {
                $this->form_validation->set_message('check_weekendday', "You have selected a weekend date.");
                return false;
            } else {
                return true;
            }
        }
        return true;
    }
}
