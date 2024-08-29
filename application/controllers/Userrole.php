<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.8
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Userrole.php
 * @copyright : Reserved RamomCoder Team
 */

class Userrole extends User_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('userrole_model');
        $this->load->model('leave_model');
        $this->load->model('fees_model');
        $this->load->model('exam_model');
    }

    public function index()
    {
        redirect(base_url(), 'refresh');
    }

    /* getting all teachers list */
    public function teacher()
    {
        $this->data['title'] = translate('teachers');
        $this->data['getSchoolConfig'] = $this->app_lib->getSchoolConfig('','teacher_mobile_visible,teacher_email_visible');
        $this->data['sub_page'] = 'userrole/teachers';
        $this->data['main_menu'] = 'teachers';
        $this->load->view('layout/index', $this->data);
    }

    public function subject()
    {
        $this->data['title'] = translate('subject');
        $this->data['sub_page'] = 'userrole/subject';
        $this->data['main_menu'] = 'academic';
        $this->load->view('layout/index', $this->data);
    }

    /*student or parent timetable preview page*/
    public function class_schedule()
    {
        $stu = $this->userrole_model->getStudentDetails();
        $arrayTimetable = array(
            'class_id' => $stu['class_id'],
            'section_id' => $stu['section_id'],
            'session_id' => get_session_id(),
        );
        $this->db->order_by('time_start', 'asc');
        $this->data['timetables'] = $this->db->get_where('timetable_class', $arrayTimetable)->result();
        $this->data['student'] = $stu;
        $this->data['title'] = translate('class') . " " . translate('schedule');
        $this->data['sub_page'] = 'userrole/class_schedule';
        $this->data['main_menu'] = 'academic';
        $this->load->view('layout/index', $this->data);
    }

    public function leave_request()
    {
        $stu = $this->userrole_model->getStudentDetails();
        if (isset($_POST['save'])) {
            $this->form_validation->set_rules('leave_category', translate('leave_category'), 'required|callback_leave_check');
            $this->form_validation->set_rules('daterange', translate('leave_date'), 'trim|required|callback_date_check');
            $this->form_validation->set_rules('attachment_file', translate('attachment'), 'callback_fileHandleUpload[attachment_file]');
            if ($this->form_validation->run() !== false) {
                $leave_type_id = $this->input->post('leave_category');
                $branch_id = $this->application_model->get_branch_id();
                $daterange = explode(' - ', $this->input->post('daterange'));
                $start_date = date("Y-m-d", strtotime($daterange[0]));
                $end_date = date("Y-m-d", strtotime($daterange[1]));
                $reason = $this->input->post('reason');
                $apply_date = date("Y-m-d H:i:s");
                $datetime1 = new DateTime($start_date);
                $datetime2 = new DateTime($end_date);
                $leave_days = $datetime2->diff($datetime1)->format("%a") + 1;
                $orig_file_name = '';
                $enc_file_name = '';
                // upload attachment file
                if (isset($_FILES["attachment_file"]) && !empty($_FILES['attachment_file']['name'])) {
                    $config['upload_path'] = './uploads/attachments/leave/';
                    $config['allowed_types'] = "*";
                    $config['max_size'] = '2024';
                    $config['encrypt_name'] = true;
                    $this->upload->initialize($config);
                    $this->upload->do_upload("attachment_file");
                    $orig_file_name = $this->upload->data('orig_name');
                    $enc_file_name = $this->upload->data('file_name');
                }
                $arrayData = array(
                    'user_id' => $stu['student_id'],
                    'role_id' => 7,
                    'session_id' => get_session_id(),
                    'category_id' => $leave_type_id,
                    'reason' => $reason,
                    'branch_id' => $branch_id,
                    'start_date' => date("Y-m-d", strtotime($start_date)),
                    'end_date' => date("Y-m-d", strtotime($end_date)),
                    'leave_days' => $leave_days,
                    'status' => 1,
                    'orig_file_name' => $orig_file_name,
                    'enc_file_name' => $enc_file_name,
                    'apply_date' => $apply_date,
                );
                $this->db->insert('leave_application', $arrayData);
                set_alert('success', translate('information_has_been_saved_successfully'));
                redirect(base_url('userrole/leave_request'));
            }
        }
        $where = array('la.user_id' => $stu['student_id'], 'la.role_id' => 7);
        $this->data['leavelist'] = $this->leave_model->getLeaveList($where);
        $this->data['title'] = translate('leaves');
        $this->data['sub_page'] = 'userrole/leave_request';
        $this->data['main_menu'] = 'leave';
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/dropify/css/dropify.min.css',
                'vendor/daterangepicker/daterangepicker.css',
            ),
            'js' => array(
                'vendor/dropify/js/dropify.min.js',
                'vendor/moment/moment.js',
                'vendor/daterangepicker/daterangepicker.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    // date check for leave request
    public function date_check($daterange)
    {
        $daterange = explode(' - ', $daterange);
        $start_date = date("Y-m-d", strtotime($daterange[0]));
        $end_date = date("Y-m-d", strtotime($daterange[1]));
        $today = date('Y-m-d');
        if ($today == $start_date) {
            $this->form_validation->set_message('date_check', "You can not leave the current day.");
            return false;
        }
        if ($this->input->post('applicant_id')) {
            $applicant_id = $this->input->post('applicant_id');
            $role_id = $this->input->post('user_role');
        } else {
            $applicant_id = get_loggedin_user_id();
            $role_id = loggedin_role_id();
        }
        $getUserLeaves = $this->db->get_where('leave_application', array('user_id' => $applicant_id, 'role_id' => $role_id))->result();
        if (!empty($getUserLeaves)) {
            foreach ($getUserLeaves as $user_leave) {
                $get_dates = $this->user_leave_days($user_leave->start_date, $user_leave->end_date);
                $result_start = in_array($start_date, $get_dates);
                $result_end = in_array($end_date, $get_dates);
                if (!empty($result_start) || !empty($result_end)) {
                    $this->form_validation->set_message('date_check', 'Already have leave in the selected time.');
                    return false;
                }
            }
        }
        return true;
    }

    public function leave_check($type_id)
    {
        if (!empty($type_id)) {
            $daterange = explode(' - ', $this->input->post('daterange'));
            $start_date = date("Y-m-d", strtotime($daterange[0]));
            $end_date = date("Y-m-d", strtotime($daterange[1]));

            if ($this->input->post('applicant_id')) {
                $applicant_id = $this->input->post('applicant_id');
                $role_id = $this->input->post('user_role');
            } else {
                $applicant_id = get_loggedin_user_id();
                $role_id = loggedin_role_id();
            }
            if (!empty($start_date) && !empty($end_date)) {
                $leave_total = get_type_name_by_id('leave_category', $type_id, 'days');
                $total_spent = $this->db->select('IFNULL(SUM(leave_days), 0) as total_days')
                    ->where(array('user_id' => $applicant_id, 'role_id' => $role_id, 'category_id' => $type_id, 'status' => '2'))
                    ->get('leave_application')->row()->total_days;

                $datetime1 = new DateTime($start_date);
                $datetime2 = new DateTime($end_date);
                $leave_days = $datetime2->diff($datetime1)->format("%a") + 1;
                $left_leave = ($leave_total - $total_spent);
                if ($left_leave < $leave_days) {
                    $this->form_validation->set_message('leave_check', "Applyed for $leave_days days, get maximum $left_leave Days days.");
                    return false;
                } else {
                    return true;
                }
            } else {
                $this->form_validation->set_message('leave_check', "Select all required field.");
                return false;
            }
        }
    }

    public function user_leave_days($start_date, $end_date)
    {
        $dates = array();
        $current = strtotime($start_date);
        $end_date = strtotime($end_date);
        while ($current <= $end_date) {
            $dates[] = date('Y-m-d', $current);
            $current = strtotime('+1 day', $current);
        }
        return $dates;
    }

    public function attachments()
    {
        $this->data['title'] = translate('attachments');
        $this->data['sub_page'] = 'userrole/attachments';
        $this->data['main_menu'] = 'attachments';
        $this->load->view('layout/index', $this->data);
    }

    public function playVideo()
    {
        $id = $this->input->post('id');
        $file = get_type_name_by_id('attachments', $id, 'enc_name');
        echo '<video width="560" controls id="attachment_video">';
        echo '<source src="' . base_url('uploads/attachments/' . $file) . '" type="video/mp4">';
        echo 'Your browser does not support HTML video.';
        echo '</video>';
    }

    // file downloader
    public function download()
    {
        $encrypt_name = urldecode($this->input->get('file'));
        if (preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $encrypt_name)) {
            $file_name = $this->db->select('file_name')->where('enc_name', $encrypt_name)->get('attachments')->row()->file_name;
            if (!empty($file_name)) {
                $this->load->helper('download');
                force_download($file_name, file_get_contents('uploads/attachments/' . $encrypt_name));
            }
        }
    }

    /* exam timetable preview page */
    public function exam_schedule()
    {
        $stu = $this->userrole_model->getStudentDetails();
        $this->data['student'] = $stu;
        $this->db->select('*');
        $this->db->from('timetable_exam');
        $this->db->where('class_id', $stu['class_id']);
        $this->db->where('section_id', $stu['section_id']);
        $this->db->where('session_id', get_session_id());
        $this->db->group_by('exam_id');
        $this->db->order_by('exam_id', 'asc');
        $results = $this->db->get()->result_array();
        $this->data['exams'] = $results;
        $this->data['title'] = translate('exam') . " " . translate('schedule');
        $this->data['sub_page'] = 'userrole/exam_schedule';
        $this->data['main_menu'] = 'exam';
        $this->load->view('layout/index', $this->data);
    }

    /* hostels user interface */
    public function hostels()
    {
        $this->data['student'] = $this->userrole_model->getStudentDetails();
        $this->data['title'] = translate('hostels');
        $this->data['sub_page'] = 'userrole/hostels';
        $this->data['main_menu'] = 'supervision';
        $this->load->view('layout/index', $this->data);
    }

    /* route user interface */
    public function route()
    {
        $stu = $this->userrole_model->getStudentDetails();
        $this->data['route'] = $this->userrole_model->getRouteDetails($stu['route_id'], $stu['vehicle_id']);
        $this->data['title'] = translate('route_master');
        $this->data['sub_page'] = 'userrole/transport_route';
        $this->data['main_menu'] = 'supervision';
        $this->load->view('layout/index', $this->data);
    }

    /* after login students or parents produced reports here */
    public function attendance()
    {
        $this->load->model('attendance_model');
        if ($this->input->post('submit') == 'search') {
            $this->data['month'] = date('m', strtotime($this->input->post('timestamp')));
            $this->data['year'] = date('Y', strtotime($this->input->post('timestamp')));
            $this->data['days'] = cal_days_in_month(CAL_GREGORIAN, $this->data['month'], $this->data['year']);
            $this->data['student'] = $this->userrole_model->getStudentDetails();
        }
        $this->data['title'] = translate('student_attendance');
        $this->data['sub_page'] = 'userrole/attendance';
        $this->data['main_menu'] = 'attendance';
        $this->load->view('layout/index', $this->data);
    }

    // book page
    public function book()
    {
        $this->data['booklist'] = $this->app_lib->getTable('book');
        $this->data['title'] = translate('books');
        $this->data['sub_page'] = 'userrole/book';
        $this->data['main_menu'] = 'library';
        $this->load->view('layout/index', $this->data);
    }

    public function book_request()
    {
        $stu = $this->userrole_model->getStudentDetails();
        if ($_POST) {
            $this->form_validation->set_rules('book_id', translate('book_title'), 'required|callback_validation_stock');
            $this->form_validation->set_rules('date_of_issue', translate('date_of_issue'), 'trim|required');
            $this->form_validation->set_rules('date_of_expiry', translate('date_of_expiry'), 'trim|required|callback_validation_date');
            if ($this->form_validation->run() !== false) {
                $arrayIssue = array(
                    'branch_id' => $stu['branch_id'],
                    'book_id' => $this->input->post('book_id'),
                    'user_id' => $stu['student_id'],
                    'role_id' => 7,
                    'date_of_issue' => date("Y-m-d", strtotime($this->input->post('date_of_issue'))),
                    'date_of_expiry' => date("Y-m-d", strtotime($this->input->post('date_of_expiry'))),
                    'issued_by' => get_loggedin_user_id(),
                    'status' => 0,
                    'session_id' => get_session_id(),
                );
                $this->db->insert('book_issues', $arrayIssue);
                set_alert('success', translate('information_has_been_saved_successfully'));
                $url = base_url('userrole/book_request');
                $array = array('status' => 'success', 'url' => $url, 'error' => '');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'url' => '', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['stu'] = $stu;
        $this->data['title'] = translate('library');
        $this->data['sub_page'] = 'userrole/book_request';
        $this->data['main_menu'] = 'library';
        $this->load->view('layout/index', $this->data);
    }

    // book date validation
    public function validation_date($date)
    {
        if ($date) {
            $date = strtotime($date);
            $today = strtotime(date('Y-m-d'));
            if ($today >= $date) {
                $this->form_validation->set_message("validation_date", translate('today_or_the_previous_day_can_not_be_issued'));
                return false;
            } else {
                return true;
            }
        }
    }

    // validation book stock
    public function validation_stock($book_id)
    {
        $query = $this->db->select('total_stock,issued_copies')->where('id', $book_id)->get('book')->row_array();
        $stock = $query['total_stock'];
        $issued = $query['issued_copies'];
        if ($stock == 0 || $issued >= $stock) {
            $this->form_validation->set_message("validation_stock", translate('the_book_is_not_available_in_stock'));
            return false;
        } else {
            return true;
        }
    }

    public function event()
    {
        $branchID = $this->application_model->get_branch_id();
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('events');
        $this->data['sub_page'] = 'userrole/event';
        $this->data['main_menu'] = 'event';
        $this->load->view('layout/index', $this->data);
    }

    /* invoice user interface with information are controlled here */
    public function invoice()
    {
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/dropify/css/dropify.min.css',
            ),
            'js' => array(
                'vendor/dropify/js/dropify.min.js',
            ),
        );
        $stu = $this->userrole_model->getStudentDetails();
        $this->data['config'] = $this->get_payment_config();
        $this->data['getUser'] = $this->userrole_model->getUserDetails();
        $this->data['getOfflinePaymentsConfig'] = $this->userrole_model->getOfflinePaymentsConfig();
        $this->data['invoice'] = $this->fees_model->getInvoiceStatus($stu['enroll_id']);
        $this->data['basic'] = $this->fees_model->getInvoiceBasic($stu['enroll_id']);
        $this->data['title'] = translate('fees_history');
        $this->data['main_menu'] = 'fees';
        $this->data['sub_page'] = 'userrole/collect';
        $this->load->view('layout/index', $this->data);
    }

    /* invoice user interface with information are controlled here */
    public function report_card()
    {
        $this->data['stu'] = $this->userrole_model->getStudentDetails();
        $this->data['title'] = translate('exam_master');
        $this->data['main_menu'] = 'exam';
        $this->data['sub_page'] = 'userrole/report_card';
        $this->load->view('layout/index', $this->data);
    }

    public function homework()
    {
        $stu = $this->userrole_model->getStudentDetails();
        $this->data['homeworklist'] = $this->userrole_model->getHomeworkList($stu['enroll_id']);
        $this->data['title'] = translate('homework');
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['main_menu'] = 'homework';
        $this->data['sub_page'] = 'userrole/homework';
        $this->load->view('layout/index', $this->data);
    }

    public function getHomeworkAssignment()
    {
        if (!is_student_loggedin()) {
            access_denied();
        }
        $id = $this->input->post('id');
        $r = $this->db->where(array('homework_id' => $id, 'student_id' => get_loggedin_user_id()))->get('homework_submit')->row_array();
        $array = array(
            'id' => $r['id'],
            'message' => $r['message'],
            'file_name' => $r['enc_name'],
        );
        echo json_encode($array);
    }

    /* homework form validation rules */
    protected function homework_validation()
    {
        $this->form_validation->set_rules('message', translate('message'), 'trim|required');
        $this->form_validation->set_rules('attachment_file', translate('attachment'), 'callback_assignment_handle_upload');
    }

    // upload file form validation
    public function assignment_handle_upload()
    {
        if (isset($_FILES["attachment_file"]) && !empty($_FILES['attachment_file']['name'])) {
            $allowedExts = array_map('trim', array_map('strtolower', explode(',', $this->data['global_config']['file_extension'])));
            $allowedSizeKB = $this->data['global_config']['file_size'];
            $allowedSize = floatval(1024 * $allowedSizeKB);
            $file_size = $_FILES["attachment_file"]["size"];
            $file_name = $_FILES["attachment_file"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["attachment_file"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('handle_upload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > $allowedSize) {
                    $this->form_validation->set_message('handle_upload', translate('file_size_shoud_be_less_than') . " $allowedSizeKB KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        } else {
            if (!empty($_POST['old_file'])) {
                return true;
            }

            $this->form_validation->set_message('assignment_handle_upload', "The Attachment field is required.");
            return false;
        }
    }

    public function assignment_upload()
    {
        if ($_POST) {
            $this->homework_validation();
            if ($this->form_validation->run() !== false) {
                $message = $this->input->post('message');
                $homeworkID = $this->input->post('homework_id');
                $assigmentID = $this->input->post('assigment_id');
                $arrayDB = array(
                    'homework_id' => $homeworkID,
                    'student_id' => get_loggedin_user_id(),
                    'message' => $message,
                );

                if (isset($_FILES["attachment_file"]) && !empty($_FILES['attachment_file']['name'])) {
                    $config = array();
                    $config['upload_path'] = 'uploads/attachments/homework_submit/';
                    $config['encrypt_name'] = true;
                    $config['allowed_types'] = '*';
                    $this->upload->initialize($config);
                    if ($this->upload->do_upload("attachment_file")) {
                        $encrypt_name = $this->input->post('old_file');
                        if (!empty($encrypt_name)) {
                            $file_name = $config['upload_path'] . $encrypt_name;
                            if (file_exists($file_name)) {
                                unlink($file_name);
                            }
                        }

                        $orig_name = $this->upload->data('orig_name');
                        $enc_name = $this->upload->data('file_name');
                        $arrayDB['enc_name'] = $enc_name;
                        $arrayDB['file_name'] = $orig_name;
                    } else {
                        set_alert('error', $this->upload->display_errors());
                    }
                }

                if (empty($assigmentID)) {
                    $this->db->insert('homework_submit', $arrayDB);
                } else {
                    $this->db->where('id', $assigmentID);
                    $this->db->update('homework_submit', $arrayDB);
                }
                set_alert('success', translate('information_has_been_saved_successfully'));
                $url = base_url('userrole/homework');
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
    }

    public function live_class()
    {
        if (!is_student_loggedin()) {
            access_denied();
        }
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('live_class_rooms');
        $this->data['sub_page'] = 'userrole/live_class';
        $this->data['main_menu'] = 'live_class';
        $this->load->view('layout/index', $this->data);
    }

    public function joinModal()
    {
        if (!is_student_loggedin()) {
            access_denied();
        }
        $this->data['meetingID'] = $this->input->post('meeting_id');
        echo $this->load->view('userrole/live_classModal', $this->data, true);
    }

    public function livejoin()
    {
        if (!is_student_loggedin()) {
            access_denied();
        }
        $meetingID = $this->input->get('meeting_id', true);
        $liveID = $this->input->get('live_id', true);
        if (empty($meetingID) || empty($liveID)) {
            access_denied();
        }

        $getMeeting = $this->userrole_model->get('live_class', array('id' => $liveID, 'meeting_id' => $meetingID), true);
        if ($getMeeting['live_class_method'] == 1) {
            $this->load->view('userrole/livejoin', $this->data);
        } else {
            $getStudent = $this->application_model->getStudentDetails(get_loggedin_user_id());
            $bbb_config = json_decode($getMeeting['bbb'], true);
            // get BBB api config
            $getConfig = $this->userrole_model->get('live_class_config', array('branch_id' => $getMeeting['branch_id']), true);
            $api_keys = array(
                'bbb_security_salt' => $getConfig['bbb_salt_key'],
                'bbb_server_base_url' => $getConfig['bbb_server_base_url'],
            );
            $this->load->library('bigbluebutton_lib', $api_keys);

            $arrayBBB = array(
                'meeting_id' => $getMeeting['meeting_id'],
                'title' => $getMeeting['title'],
                'attendee_password' => $bbb_config['attendee_password'],
                'presen_name' => $getStudent['first_name'] . ' ' . $getStudent['last_name'] . ' (Roll - ' . $getStudent['roll'] . ')',
            );

            $response = $this->bigbluebutton_lib->joinMeeting($arrayBBB);
            redirect($response);
        }
    }

    public function live_atten()
    {
        $stu_id = get_loggedin_user_id();
        $id = $this->input->post('live_id');
        $arrayInsert = array(
            'live_class_id' => $id,
            'student_id' => $stu_id,
        );

        $this->db->where($arrayInsert);
        $query = $this->db->get('live_class_reports');
        if ($query->num_rows() > 0) {
            $arrayInsert['created_at'] = date("Y-m-d H:i:s");
            $this->db->where('id', $query->row()->id);
            $this->db->update('live_class_reports', $arrayInsert);
        } else {
            $this->db->insert('live_class_reports', $arrayInsert);
        }
        $array = array('status' => 1);
        echo json_encode($array);
    }

    /* Online exam controller */
    public function online_exam()
    {
        if (!is_student_loggedin()) {
            access_denied();
        }

        $this->load->model('onlineexam_model');
        $this->data['headerelements'] = array(
            'js' => array(
                'js/online-exam.js',
            ),
        );
        $this->data['title'] = translate('online_exam');
        $this->data['sub_page'] = 'userrole/online_exam';
        $this->data['main_menu'] = 'onlineexam';
        $this->load->view('layout/index', $this->data);
    }

    public function getExamListDT()
    {
        if ($_POST) {
            $this->load->model('onlineexam_model');
            $postData = $this->input->post();
            $currencySymbol = $this->data['global_config']['currency_symbol'];
            echo $this->userrole_model->examListDT($postData, $currencySymbol);
        }
    }

    /* Online exam controller */
    public function onlineexam_take($id = '')
    {
        if (!is_student_loggedin()) {
            access_denied();
        }
        $this->load->model('onlineexam_model');
        $this->data['headerelements'] = array(
            'js' => array(
                'js/online-exam.js',
            ),
        );
        $exam = $this->userrole_model->getExamDetails($id);
        if (empty($exam)) {
            redirect(base_url('userrole/online_exam'));
        }

        if ($exam->exam_type == 1 && $exam->payment_status == 0) {
            set_alert('error', "You have to make payment to attend this exam !");
            redirect(base_url('userrole/online_exam'));
        }

        $this->data['studentSubmitted'] = $this->onlineexam_model->getStudentSubmitted($exam->id);
        $this->data['exam'] = $exam;
        $this->data['title'] = translate('online_exam');
        $this->data['sub_page'] = 'onlineexam/take';
        $this->data['main_menu'] = 'onlineexam';
        $this->load->view('layout/index', $this->data);
    }

    public function ajaxQuestions()
    {
        $status = 0;
        $totalQuestions = 0;
        $message = "";
        $this->load->model('onlineexam_model');
        $examID = $this->input->post('exam_id');
        $exam = $this->userrole_model->getExamDetails($examID);
        $totalQuestions = $exam->questions_qty;
        $studentAttempt = $this->onlineexam_model->getStudentAttempt($exam->id);
        $examSubmitted = $this->onlineexam_model->getStudentSubmitted($exam->id);
        if (!empty($exam)) {
            $startTime = strtotime($exam->exam_start);
            $endTime = strtotime($exam->exam_end);
            $now = strtotime("now");
            if (($startTime <= $now && $now <= $endTime) && (empty($examSubmitted)) && $exam->publish_status == 1) {
                if ($exam->limits_participation > $studentAttempt) {
                    $this->onlineexam_model->addStudentAttemts($exam->id);
                    $message = "";
                    $status = 1;
                } else {
                    $status = 0;
                    $message = "You already reach max exam attempt.";
                }
            } else {
                $message = "Maybe the test has expired or something wrong.";
            }
        }
        $data['exam'] = $exam;
        $data['questions'] = $this->onlineexam_model->getExamQuestions($exam->id, $exam->question_type);
        $pag_content = $this->load->view('onlineexam/ajax_take', $data, true);
        echo json_encode(array('status' => $status, 'total_questions' => $totalQuestions, 'message' => $message, 'page' => $pag_content));
    }

    public function getStudent_result()
    {
        if ($_POST) {
            $examID = $this->input->post('id');
            $this->load->model('onlineexam_model');
            $exam = $this->onlineexam_model->getExamDetails($examID);
            $data['exam'] = $exam;
            echo $this->load->view('userrole/onlineexam_result', $data, true);
        }
    }

    public function getExamPaymentForm()
    {
        if ($_POST) {
            $this->load->model('onlineexam_model');
            $status = 1;
            $page_data = "";
            $examID = $this->input->post('examID');
            $exam = $this->userrole_model->getExamDetails($examID);
            $message = "";
            if (empty($exam)) {
                $status = 0;
                $message = 'Exam not found.';
                echo json_encode(array('status' => $status, 'message' => $message));
                exit;
            }
            $data['config'] = $this->get_payment_config();
            $data['global_config'] = $this->data['global_config'];
            $data['getUser'] = $this->userrole_model->getUserDetails();
            $data['exam'] = $exam;
            if ($exam->payment_status == 0) {
                $status = 1;
                $page_data = $this->load->view('userrole/getExamPaymentForm', $data, true);
            } else {
                $status = 0;
                $message = 'The fee has already been paid.';
            }
            echo json_encode(array('status' => $status, 'message' => $message, 'data' => $page_data));
        }
    }

    public function onlineexam_submit_answer()
    {
        if ($_POST) {
            if (!is_student_loggedin()) {
                access_denied();
            }
            $studentID = get_loggedin_user_id();
            $online_examID = $this->input->post('online_exam_id');
            $variable = $this->input->post('answer');
            if (!empty($variable)) {
                $saveAnswer = array();
                foreach ($variable as $key => $value) {
                    if (isset($value[1])) {
                        $saveAnswer[] = array(
                            'student_id' => $studentID,
                            'online_exam_id' => $online_examID,
                            'question_id' => $key,
                            'answer' => $value[1],
                            'created_at' => date('Y-m-d H:i:s'),
                        );
                    }
                    if (isset($value[2])) {
                        $saveAnswer[] = array(
                            'student_id' => $studentID,
                            'online_exam_id' => $online_examID,
                            'question_id' => $key,
                            'answer' => json_encode($value[2]),
                            'created_at' => date('Y-m-d H:i:s'),
                        );
                    }
                    if (isset($value[3])) {
                        $saveAnswer[] = array(
                            'student_id' => $studentID,
                            'online_exam_id' => $online_examID,
                            'question_id' => $key,
                            'answer' => $value[3],
                            'created_at' => date('Y-m-d H:i:s'),
                        );
                    }
                    if (isset($value[4])) {
                        $saveAnswer[] = array(
                            'student_id' => $studentID,
                            'online_exam_id' => $online_examID,
                            'question_id' => $key,
                            'answer' => $value[4],
                            'created_at' => date('Y-m-d H:i:s'),
                        );
                    }
                }
                $this->db->insert_batch('online_exam_answer', $saveAnswer);
                $this->db->insert('online_exam_submitted', ['student_id' => get_loggedin_user_id(), 'online_exam_id' => $online_examID, 'created_at' => date('Y-m-d H:i:s')]);
            }
            set_alert('success', translate('your_exam_has_been_successfully_submitted'));
            redirect(base_url('userrole/online_exam'));
        }
    }

    public function offline_payments()
    {
        if ($_POST) {
            $this->form_validation->set_rules('fees_type', translate('fees_type'), 'trim|required');
            $this->form_validation->set_rules('date_of_payment', translate('date_of_payment'), 'trim|required');
            $this->form_validation->set_rules('fee_amount', translate('amount'), array('trim', 'required', 'numeric', 'greater_than[0]', array('deposit_verify', array($this->fees_model, 'depositAmountVerify'))));
            $this->form_validation->set_rules('payment_method', translate('payment_method'), 'trim|required');
            $this->form_validation->set_rules('note', translate('note'), 'trim|required');
            $this->form_validation->set_rules('proof_of_payment', translate('proof_of_payment'), 'callback_fileHandleUpload[proof_of_payment]');
            if ($this->form_validation->run() !== false) {
                $feesType = explode("|", $this->input->post('fees_type'));
                $date_of_payment = $this->input->post('date_of_payment');
                $payment_method = $this->input->post('payment_method');
                $invoice_no = $this->input->post('invoice_no');

                $enc_name = null;
                $orig_name = null;
                $config = array();
                $config['upload_path'] = 'uploads/attachments/offline_payments/';
                $config['encrypt_name'] = true;
                $config['allowed_types'] = '*';
                $this->upload->initialize($config);
                if ($this->upload->do_upload("proof_of_payment")) {
                    $orig_name = $this->upload->data('orig_name');
                    $enc_name = $this->upload->data('file_name');
                }

                $stu = $this->userrole_model->getStudentDetails();
                $arrayFees = array(
                    'fees_allocation_id' => $feesType[0],
                    'fees_type_id' => $feesType[1],
                    'invoice_no' => $invoice_no,
                    'student_enroll_id' => $stu['enroll_id'],
                    'amount' => $this->input->post('fee_amount'),
                    'fine' => $this->input->post('fine_amount'),
                    'payment_method' => $payment_method,
                    'reference' => $this->input->post('reference'),
                    'note' => $this->input->post('note'),
                    'payment_date' => date('Y-m-d', strtotime($date_of_payment)),
                    'submit_date' => date('Y-m-d H:i:s'),
                    'enc_file_name' => $enc_name,
                    'orig_file_name' => $orig_name,
                    'status' => 1,
                );
                $this->db->insert('offline_fees_payments', $arrayFees);
                set_alert('success', "We will review and notify your of your payment.");
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'url' => '', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    // get payments details modal
    public function getOfflinePaymentslDetails()
    {
        if ($_POST) {
            $this->data['payments_id'] = $this->input->post('id');
            $this->load->view('userrole/getOfflinePaymentslDetails', $this->data);
        }
    }

    public function getBalanceByType()
    {
        $input = $this->input->post('typeID');
        if (empty($input)) {
            $balance = 0;
            $fine = 0;
        } else {
            $feesType = explode("|", $input);
            $fine = $this->fees_model->feeFineCalculation($feesType[0], $feesType[1]);
            $b = $this->fees_model->getBalance($feesType[0], $feesType[1]);
            $balance = $b['balance'];
            $fine = abs($fine - $b['fine']);
        }
        echo json_encode(array('balance' => $balance, 'fine' => $fine));
    }

    public function switchClass($enrollID = '')
    {
        $enrollID = $this->security->xss_clean($enrollID);
        if (!empty($enrollID) && is_student_loggedin()) {
            $getRow = $this->db->where('id', $enrollID)->get('enroll')->row();
            if (!empty($getRow) && ($getRow->student_id == get_loggedin_user_id())) {

                $this->db->where('student_id', $getRow->student_id);
                $this->db->where('session_id', $getRow->session_id);
                $this->db->update('enroll', ['default_login' => 0]);
               
                $this->db->where('id', $enrollID);
                $this->db->update('enroll', ['default_login' => 1]);

                $this->session->set_userdata('enrollID', $enrollID);
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    redirect(base_url('dashboard'), 'refresh');
                }
            } else {
                redirect(base_url('dashboard'), 'refresh');
            }
        } else {
            redirect(base_url(), 'refresh');
        }
    }

    public function subject_wise_attendance()
    {
        $getAttendanceType = $this->app_lib->getAttendanceType();
        if ($getAttendanceType != 2 && $getAttendanceType != 1) {
            access_denied();
        }
        $this->load->model('attendance_period_model');
        $this->load->model('subject_model');
        $this->load->model('attendance_model');
        $getStudentDetails = $this->userrole_model->getStudentDetails();
        $branchID = $getStudentDetails['branch_id'];
        $this->data['class_id'] = $getStudentDetails['class_id'];
        $this->data['section_id'] = $getStudentDetails['section_id'];
        if ($_POST) {
            $this->data['subject_id'] = $this->input->post('subject_id');
            $this->data['month'] = date('m', strtotime($this->input->post('timestamp')));
            $this->data['year'] = date('Y', strtotime($this->input->post('timestamp')));
            $this->data['days'] = date('t', strtotime($this->data['year'] . "-" . $this->data['month']));
            $this->data['studentDetails'] = $getStudentDetails;
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('subject_wise_attendance');
        $this->data['sub_page'] = 'userrole/subject_wise_attendance';
        $this->data['main_menu'] = 'attendance';
        $this->load->view('layout/index', $this->data);
    }
}