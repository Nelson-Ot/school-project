<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Birthday.php
 * @copyright : Reserved RamomCoder Team
 */

class Birthday extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('birthday_model');
        $this->load->model('sms_model');
    }

    public function index()
    {
        redirect(base_url('birthday/student'));
    }

    /* showing student list by birthday */
    public function student()
    {
        // check access permission
        if (!get_permission('student_birthday_wishes', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            $daterange = explode(' - ', $this->input->post('daterange'));
            $start = date("Y-m-d", strtotime($daterange[0]));
            $end = date("Y-m-d", strtotime($daterange[1]));
            $this->data['students'] = $this->birthday_model->getStudentListByBirthday($branchID, $start, $end);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student') . " " . translate('birthday') . " " . translate('list');
        $this->data['main_menu'] = 'sendsmsmail';
        $this->data['sub_page'] = 'birthday/student';
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/daterangepicker/daterangepicker.css',
            ),
            'js' => array(
                'vendor/moment/moment.js',
                'vendor/daterangepicker/daterangepicker.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    public function studentWishes()
    {
        if ($_POST) {
            $status = 'success';
            $message = "All birthday wishes sent via sms.";
            if (get_permission('student_birthday_wishes', 'is_view')) {
                $arrayID = $this->input->post('array_id');
                if (!empty($arrayID)) {
                    foreach ($arrayID as $key => $row) {
                        $this->sms_model->sendBirthdayStudentWishes(['student_id' => $row]);
                    }
                }
            } else {
                $message = translate('access_denied');
                $status = 'error';
            }
            echo json_encode(array('status' => $status, 'message' => $message));
        }
    }

    /* showing staff list by birthday */
    public function staff()
    {
        // check access permission
        if (!get_permission('staff_birthday_wishes', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            $daterange = explode(' - ', $this->input->post('daterange'));
            $start = date("Y-m-d", strtotime($daterange[0]));
            $end = date("Y-m-d", strtotime($daterange[1]));
            $this->data['students'] = $this->birthday_model->getStaffListByBirthday($branchID, $start, $end);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('staff') . " " . translate('birthday') . " " . translate('list');
        $this->data['main_menu'] = 'sendsmsmail';
        $this->data['sub_page'] = 'birthday/staff';
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/daterangepicker/daterangepicker.css',
            ),
            'js' => array(
                'vendor/moment/moment.js',
                'vendor/daterangepicker/daterangepicker.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    public function staffWishes()
    {
        if ($_POST) {
            $status = 'success';
            $message = "All birthday wishes sent via sms.";
            if (get_permission('staff_birthday_wishes', 'is_view')) {
                $arrayID = $this->input->post('array_id');
                if (!empty($arrayID)) {
                    foreach ($arrayID as $key => $row) {
                        $this->sms_model->sendBirthdayStaffWishes(['staff_id' => $row]);
                    }
                }
            } else {
                $message = translate('access_denied');
                $status = 'error';
            }
            echo json_encode(array('status' => $status, 'message' => $message));
        }
    }
}
