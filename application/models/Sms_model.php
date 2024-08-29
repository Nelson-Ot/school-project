<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Sms_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library("clickatell");
        $this->load->library("twilio");
        $this->load->library("msg91");
        $this->load->library("bulk");
        $this->load->library("textlocal");
        $this->load->library("smscountry");
        $this->load->library("bulksmsbd");
        $this->load->library("custom_sms");
    }

    // common function for sending sms
    public function send_sms($data = '', $id = '')
    {
        $branchID = $this->application_model->get_branch_id();
        $sms_api = $this->application_model->smsServiceProvider($branchID);
        $template = $this->db->get_where('sms_template_details', array('template_id' => $id, 'branch_id' => $branchID))->row_array();
        if (!empty($template) && ($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
            $student = $this->application_model->getstudentdetails($data['student_id']);
            $text = str_replace('{name}', $student['first_name'] . ' ' . $student['last_name'], $template['template_body']);
            $text = str_replace('{register_no}', $student['register_no'], $text);
            $text = str_replace('{admission_date}', $student['admission_date'], $text);
            $text = str_replace('{class}', $student['class_name'], $text);
            $text = str_replace('{section}', $student['section_name'], $text);
            $text = str_replace('{roll}', $student['roll'], $text);

            if ($id == 2) {
                $text = str_replace('{paid_amount}', $data['amount'], $text);
                $text = str_replace('{paid_date}', _d($data['paid_date']), $text);
            }

            if ($id == 4 || $id == 5) {
                $exam = $this->db->select('name,term_id')->where('id', $data['exam_id'])->get('exam')->row();
                $subject_name = $this->db->select('name')->where('id', $data['subject_id'])->get('subject')->row()->name;
                if (!empty($exam->term_id)) {
                    $term_name = $this->db->select('name')->where('id', $exam->term_id)->get('exam_term')->row()->name;
                }
                $text = str_replace('{exam_name}', $exam->name, $text);
                $text = str_replace('{term_name}', $term_name, $text);
                $text = str_replace('{subject}', $subject_name, $text);
                if (!empty($data['mark'])) {
                    $text = str_replace('{marks}', $data['mark'], $text);
                }
            }

            if ($template['notify_student'] == 1) {
                if (!empty($student['mobileno'])) {
                    $this->_send($sms_api, $student['mobileno'], $text, $template['dlt_template_id']);
                }
            }

            if ($template['notify_parent'] == 1) {
                if (!empty($student['parent_id'])) {
                    $parent = $this->db->select('mobileno')->where('id', $student['parent_id'])->get('parent')->row_array();
                    if (!empty($parent['mobileno'])) {
                        $this->_send($sms_api, $parent['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
            }
        }
    }

    public function alumniEvent($arrayData = [])
    {
        $sms_api = $this->application_model->smsServiceProvider($arrayData['branch_id']);
        $template = $this->db->get_where('sms_template_details', array('template_id' => 11, 'branch_id' => $arrayData['branch_id']))->row_array();
        if (!empty($template) && $template['notify_student'] == 1 && $sms_api != 'disabled') {
            $text = str_replace('{student_name}', $arrayData['name'], $template['template_body']);
            $text = str_replace('{start_date}', $arrayData['from_date'], $text);
            $text = str_replace('{end_date}', $arrayData['to_date'], $text);
            $text = str_replace('{event_title}', $arrayData['event_title'], $text);
            if (!empty($arrayData['mobile_no'])) {
                $this->_send($sms_api, $arrayData['mobile_no'], $text, $template['dlt_template_id']);
            }
        }
    }

    public function feeReminder($stuData, $remData)
    {
        $sms_api = $this->application_model->smsServiceProvider($remData['branch_id']);
        if ($sms_api != 'disabled') {
            $text = str_replace('{guardian_name}', $stuData['guardian_name'], $remData['message']);
            $text = str_replace('{child_name}', $stuData['child_name'], $text);
            $text = str_replace('{due_date}', $stuData['due_date'], $text);
            $text = str_replace('{due_amount}', $stuData['balance_amount'], $text);
            $text = str_replace('{fee_type}', $stuData['type_name'], $text);
            if ($remData['student'] == 1) {
                if (!empty($stuData['child_mobileno'])) {
                    $this->_send($sms_api, $stuData['child_mobileno'], $text, $remData['dlt_template_id']);
                }
            }
            if ($remData['guardian'] == 1) {
                if (!empty($stuData['guardian_mobileno'])) {
                    $this->_send($sms_api, $stuData['guardian_mobileno'], $text, $remData['dlt_template_id']);
                }
            }
        }
    }

    public function sendHomework($data)
    {
        $template = $this->db->get_where('sms_template_details', array('template_id' => 6, 'branch_id' => $data['branch_id']))->row_array();
        $sms_api = $this->application_model->smsServiceProvider($data['branch_id']);
        if (($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
            $text = str_replace('{name}', $data['fullname'], $template['template_body']);
            $text = str_replace('{register_no}', $data['register_no'], $text);
            $text = str_replace('{admission_date}', $data['admission_date'], $text);
            $text = str_replace('{class}', $data['class_name'], $text);
            $text = str_replace('{section}', $data['section_name'], $text);
            $text = str_replace('{date_of_homework}', $data['date_of_homework'], $text);
            $text = str_replace('{date_of_submission}', $data['date_of_submission'], $text);
            $text = str_replace('{subject}', get_type_name_by_id('subject', $data['subject_id']), $text);
            if ($template['notify_student'] == 1) {
                if (!empty($data['mobileno'])) {
                    $this->_send($sms_api, $data['mobileno'], $text, $template['dlt_template_id']);
                }
            }
            if ($template['notify_parent'] == 1) {
                if (!empty($data['parent_id'])) {
                    $parent = $this->db->select('mobileno')->where('id', $data['parent_id'])->get('parent')->row_array();
                    if (!empty($parent['mobileno'])) {
                        $this->_send($sms_api, $parent['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
            }
        }
    }

    public function sendLiveClass($data)
    {
        $template = $this->db->get_where('sms_template_details', array('template_id' => 7, 'branch_id' => $data['branch_id']))->row_array();
        $sms_api = $this->application_model->smsServiceProvider($data['branch_id']);
        if (($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
            $text = str_replace('{name}', $data['fullname'], $template['template_body']);
            $text = str_replace('{roll}', $data['roll'], $text);
            $text = str_replace('{register_no}', $data['register_no'], $text);
            $text = str_replace('{admission_date}', $data['admission_date'], $text);
            $text = str_replace('{class}', $data['class_name'], $text);
            $text = str_replace('{section}', $data['section_name'], $text);
            $text = str_replace('{date_of_live_class}', $data['date_of_live_class'], $text);
            $text = str_replace('{start_time}', $data['start_time'], $text);
            $text = str_replace('{end_time}', $data['end_time'], $text);
            $text = str_replace('{host_by}', $data['host_by'], $text);
            if ($template['notify_student'] == 1) {
                if (!empty($data['mobileno'])) {
                    $this->_send($sms_api, $data['mobileno'], $text, $template['dlt_template_id']);
                }
            }
            if ($template['notify_parent'] == 1) {
                if (!empty($data['parent_id'])) {
                    $parent = $this->db->select('mobileno')->where('id', $data['parent_id'])->get('parent')->row_array();
                    if (!empty($parent['mobileno'])) {
                        $this->_send($sms_api, $parent['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
            }
        }
    }

    public function sendOnlineExam($data)
    {
        $template = $this->db->get_where('sms_template_details', array('template_id' => 8, 'branch_id' => $data['branch_id']))->row_array();
        $sms_api = $this->application_model->smsServiceProvider($data['branch_id']);
        if (($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
            $text = str_replace('{name}', $data['fullname'], $template['template_body']);
            $text = str_replace('{roll}', $data['roll'], $text);
            $text = str_replace('{register_no}', $data['register_no'], $text);
            $text = str_replace('{admission_date}', $data['admission_date'], $text);
            $text = str_replace('{class}', $data['class_name'], $text);
            $text = str_replace('{section}', $data['section_name'], $text);
            $text = str_replace('{exam_title}', $data['exam_title'], $text);
            $text = str_replace('{start_time}', $data['start_time'], $text);
            $text = str_replace('{end_time}', $data['end_time'], $text);
            $text = str_replace('{time_duration}', $data['time_duration'], $text);
            $text = str_replace('{attempt}', $data['attempt'], $text);
            $text = str_replace('{passing_mark}', $data['passing_mark'], $text);
            $text = str_replace('{exam_fee}', $data['exam_fee'], $text);
            if ($template['notify_student'] == 1) {
                if (!empty($data['mobileno'])) {
                    $this->_send($sms_api, $data['mobileno'], $text, $template['dlt_template_id']);
                }
            }
            if ($template['notify_parent'] == 1) {
                if (!empty($data['parent_id'])) {
                    $parent = $this->db->select('mobileno')->where('id', $data['parent_id'])->get('parent')->row_array();
                    if (!empty($parent['mobileno'])) {
                        $this->_send($sms_api, $parent['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
            }
        }
    }

    public function sendBirthdayStudentWishes($data)
    {
        $student = $this->application_model->getstudentdetails($data['student_id']);
        if (!empty($student)) {
            $template = $this->db->get_where('sms_template_details', array('template_id' => 9, 'branch_id' => $student['branch_id']))->row_array();
            $sms_api = $this->application_model->smsServiceProvider($student['branch_id']);
            if (!empty($template) && ($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
                $text = str_replace('{name}', $student['first_name'] . ' ' . $student['last_name'], $template['template_body']);
                $text = str_replace('{register_no}', $student['register_no'], $text);
                $text = str_replace('{admission_date}', $student['admission_date'], $text);
                $text = str_replace('{class}', $student['class_name'], $text);
                $text = str_replace('{section}', $student['section_name'], $text);
                $text = str_replace('{roll}', $student['roll'], $text);
                $text = str_replace('{birthday}', _d($student['birthday']), $text);
                if ($template['notify_student'] == 1) {
                    if (!empty($student['mobileno'])) {
                        $this->_send($sms_api, $student['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
                if ($template['notify_parent'] == 1) {
                    if (!empty($student['parent_id'])) {
                        $parent = $this->db->select('mobileno')->where('id', $student['parent_id'])->get('parent')->row_array();
                        if (!empty($parent['mobileno'])) {
                            $this->_send($sms_api, $parent['mobileno'], $text, $template['dlt_template_id']);
                        }
                    }
                }
            }
        }
    }

    public function sendBirthdayStaffWishes($data)
    {
        $branchID = $this->application_model->get_branch_id();
        $sql = "SELECT `name`,`birthday`,`joining_date`,`mobileno`,`branch_id` FROM `staff` WHERE `id` = " . $this->db->escape($data['staff_id']) . " AND `branch_id` = " . $this->db->escape($branchID);
        $staff = $this->db->query($sql)->row_array();
        if (!empty($staff)) {
            $template = $this->db->get_where('sms_template_details', array('template_id' => 10, 'branch_id' => $staff['branch_id']))->row_array();
            $sms_api = $this->application_model->smsServiceProvider($staff['branch_id']);
            if (!empty($template) && ($template['notify_student'] == 1 || $template['notify_parent'] == 1) && $sms_api != 'disabled') {
                $text = str_replace('{name}', $staff['name'], $template['template_body']);
                $text = str_replace('{joining_date}', $staff['joining_date'], $text);
                $text = str_replace('{birthday}', _d($staff['birthday']), $text);
                if ($template['notify_student'] == 1) {
                    if (!empty($staff['mobileno'])) {
                        $this->_send($sms_api, $staff['mobileno'], $text, $template['dlt_template_id']);
                    }
                }
            }
        }
    }

    public function _send($sms_api, $receiver, $text, $dlt_template_id = '')
    {
        if ($sms_api == 2) {
            $res = $this->clickatell->send_message($receiver, $text);
        } elseif ($sms_api == 1) {
            $res = $this->twilio->sms($receiver, $text);
        } elseif ($sms_api == 4) {
            $res = $this->bulk->send($receiver, $text);
        } elseif ($sms_api == 3) {
            $res = $this->msg91->send($receiver, $text, $dlt_template_id);
        } elseif ($sms_api == 5) {
            $res = $this->textlocal->sendSms($receiver, $text);
        } elseif ($sms_api == 6) {
            $res = $this->smscountry->send($receiver, $text);
        } elseif ($sms_api == 7) {
            $res = $this->bulksmsbd->send($receiver, $text);
        } elseif ($sms_api == 8) {
            $res = $this->custom_sms->send($receiver, $text, $dlt_template_id);
        }
    }
}
