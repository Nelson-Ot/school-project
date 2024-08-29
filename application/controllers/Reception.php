<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Reception.php
 * @copyright : Reserved RamomCoder Team
 */

class Reception extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('reception_model');
        if (!moduleIsEnabled('reception')) {
            access_denied();
        }
    }

    public function index()
    {
        redirect(base_url('reception/postal'));
    }

    /* postal form validation rules */
    protected function postal_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('type', translate('type'), 'trim|required');
        $this->form_validation->set_rules('reference_no', translate('reference_no'), 'trim|required');
        $this->form_validation->set_rules('sender_title', translate('sender') . " " . translate('title'), 'trim|required');
        $this->form_validation->set_rules('receiver_title', translate('receiver') . " " . translate('title'), 'trim|required');
        $this->form_validation->set_rules('address', translate('address'), 'trim|required');
        $this->form_validation->set_rules('date', translate('date'), 'trim|required');
        $this->form_validation->set_rules('document_file', translate('document') . " " . translate('file'), 'callback_photoHandleUpload[document_file]');
    }

    public function postal()
    {
        if ($_POST) {
            if (get_permission('postal_record', 'is_add')) {
                $this->postal_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->reception_model->postalSave($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }
        if (!get_permission('postal_record', 'is_view')) {
            access_denied();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['result'] = $this->app_lib->getTable('postal_record');
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('postal_record');
        $this->data['sub_page'] = 'reception/postal';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function postal_edit($id = '')
    {
        if (!get_permission('postal_record', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->postal_validation();
            if ($this->form_validation->run() !== false) {
                // save information in the database file
                $this->reception_model->postalSave($this->input->post());
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success', 'url' => base_url('reception/postal'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['row'] = $this->app_lib->getTable('postal_record', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('postal_record');
        $this->data['sub_page'] = 'reception/postal_edit';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function postal_delete($id)
    {
        if (get_permission('postal_record', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('postal_record');
        }
    }

    public function getPostalRecord()
    {
        if (get_permission('postal_record', 'is_view')) {
            $templateID = $this->input->post('id');
            $this->data['postal'] = $this->reception_model->get('postal_record', array('id' => $templateID), true);
            $this->load->view('reception/viewPostalRecord', $this->data);
        }
    }

    // file downloader
    public function download($type = '')
    {
        $encrypt_name = urldecode($this->input->get('file'));
        if(preg_match('/^[^.][-a-z0-9_.]+[a-z]$/i', $encrypt_name)) {
            $this->load->helper('download');
            force_download($encrypt_name, file_get_contents("uploads/reception/$type/" . $encrypt_name));
        }
    }

    /* call log form validation rules */
    protected function callLog_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('call_type', translate('call_type'), 'trim|required');
        $this->form_validation->set_rules('purpose_id', translate('calling_purpose'), 'trim|required');
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
        $this->form_validation->set_rules('phone_number', translate('phone'), 'trim|required');
        $this->form_validation->set_rules('date', translate('date'), 'trim|required');
        $this->form_validation->set_rules('start_time', translate('start_time'), 'trim|required');
        $this->form_validation->set_rules('end_time', translate('end_time'), 'trim|required');
    }

    public function call_log()
    {
        if ($_POST) {
            if (get_permission('call_log', 'is_add')) {
                $this->callLog_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->reception_model->call_logSave($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        if (!get_permission('call_log', 'is_view')) {
            access_denied();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-timepicker/css/bootstrap-timepicker.css',
            ),
            'js' => array(
                'vendor/bootstrap-timepicker/bootstrap-timepicker.js',
            ),
        );
        $this->data['result'] = $this->app_lib->getTable('call_log');
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('call_log');
        $this->data['sub_page'] = 'reception/call_log';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function call_log_edit($id = '')
    {
        if (!get_permission('call_log', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->callLog_validation();
            if ($this->form_validation->run() !== false) {
                // save information in the database file
                $this->reception_model->call_logSave($this->input->post());
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success', 'url' => base_url('reception/call_log'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-timepicker/css/bootstrap-timepicker.css',
            ),
            'js' => array(
                'vendor/bootstrap-timepicker/bootstrap-timepicker.js',
            ),
        );
        $this->data['row'] = $this->app_lib->getTable('call_log', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('call_log');
        $this->data['sub_page'] = 'reception/call_log_edit';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function call_log_delete($id)
    {
        if (get_permission('call_log', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('call_log');
        }
    }

    /* visitor form validation rules */
    protected function visitor_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('purpose_id', translate('visiting_purpose'), 'trim|required');
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
        $this->form_validation->set_rules('phone_number', translate('phone'), 'trim|numeric');
        $this->form_validation->set_rules('date', translate('date'), 'trim|required');
        $this->form_validation->set_rules('entry_time', translate('entry_time'), 'trim|required');
        $this->form_validation->set_rules('exit_time', translate('exit_time'), 'trim|required');
        $this->form_validation->set_rules('number_of_visitor', translate('number_of_visitor'), 'trim|required|numeric');
    }

    public function visitor_log()
    {
        if ($_POST) {
            if (get_permission('visitor_log', 'is_add')) {
                $this->visitor_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->reception_model->visitor_logSave($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        if (!get_permission('visitor_log', 'is_view')) {
            access_denied();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-timepicker/css/bootstrap-timepicker.css',
            ),
            'js' => array(
                'vendor/bootstrap-timepicker/bootstrap-timepicker.js',
            ),
        );
        $this->data['result'] = $this->app_lib->getTable('visitor_log');
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('visitor_log');
        $this->data['sub_page'] = 'reception/visitor';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function visitor_edit($id = '')
    {
        if (!get_permission('visitor_log', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->visitor_validation();
            if ($this->form_validation->run() !== false) {
                // save information in the database file
                $this->reception_model->visitor_logSave($this->input->post());
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success', 'url' => base_url('reception/visitor_log'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-timepicker/css/bootstrap-timepicker.css',
            ),
            'js' => array(
                'vendor/bootstrap-timepicker/bootstrap-timepicker.js',
            ),
        );
        $this->data['row'] = $this->app_lib->getTable('visitor_log', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('visitor_log');
        $this->data['sub_page'] = 'reception/visitor_edit';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function visitor_delete($id)
    {
        if (get_permission('visitor_log', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('visitor_log');
        }
    }

    /* complaint form validation rules */
    protected function complaint_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('type_id', translate('type'), 'trim|required');
        $this->form_validation->set_rules('staff_id', translate('assign_to'), 'trim|required');
        $this->form_validation->set_rules('complainant_name', translate('complainant') . " " . translate('name'), 'trim|required');
        $this->form_validation->set_rules('date', translate('date'), 'trim|required');
        $this->form_validation->set_rules('phone_number', translate('complainant') . " " . translate('mobile_no'), 'trim|numeric');
        $this->form_validation->set_rules('document_file', translate('document') . " " . translate('file'), 'callback_photoHandleUpload[document_file]');
    }

    public function complaint()
    {
        if ($_POST) {
            if (get_permission('complaint', 'is_add')) {
                $this->complaint_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->reception_model->complaintSave($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        if (!get_permission('complaint', 'is_view')) {
            access_denied();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['result'] = $this->app_lib->getTable('complaint');
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('complaint');
        $this->data['sub_page'] = 'reception/complaint';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function complaint_edit($id = '')
    {
        if (!get_permission('complaint', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->complaint_validation();
            if ($this->form_validation->run() !== false) {
                // save information in the database file
                $this->reception_model->complaintSave($this->input->post());
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success', 'url' => base_url('reception/complaint'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['row'] = $this->app_lib->getTable('complaint', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('complaint');
        $this->data['sub_page'] = 'reception/complaint_edit';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function getComplaintDetails()
    {
        if (get_permission('complaint', 'is_view')) {
            $templateID = $this->input->post('id');
            $this->data['complaint'] = $this->reception_model->get('complaint', array('id' => $templateID), true, true);
            $this->load->view('reception/viewComplaintDetails', $this->data);
        }
    }

    public function complaint_delete($id)
    {
        if (get_permission('complaint', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('complaint');
        }
    }

    public function getComplaintAction()
    {
        if (get_permission('complaint', 'is_view')) {
            $templateID = $this->input->post('id');
            $complaint = $this->reception_model->get('complaint', array('id' => $templateID), true, true, 'id,date_of_solution,action');
            if ($complaint['date_of_solution'] == "0000-00-00" || empty($complaint['date_of_solution'])) {
                $complaint['date_of_solution'] = "";
            }
            echo json_encode($complaint);
        }
    }

    public function complaint_action_taken()
    {
        if (get_permission('complaint', 'is_edit')) {
            if ($_POST) {
                $this->form_validation->set_rules('date_of_solution', translate('date_of_solution'), 'trim|required');
                $this->form_validation->set_rules('action', translate('action_taken'), 'trim|required');
                if ($this->form_validation->run() == true) {
                    $complaint_id = $this->input->post('complaint_id');
                    $date_of_solution = $this->input->post('date_of_solution');
                    $action = $this->input->post('action');
                    $arrayComplaint = array(
                        'date_of_solution' => date("Y-m-d", strtotime($date_of_solution)),
                        'action' => $action,
                    );
                    if (!is_superadmin_loggedin()) {
                        $this->db->where('branch_id', get_loggedin_branch_id());
                    }
                    $this->db->where('id', $complaint_id);
                    $this->db->update('complaint', $arrayComplaint);
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
            }
        }
    }

    /* enquiry form validation rules */
    protected function enquiry_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
        $this->form_validation->set_rules('gender', translate('gender'), 'trim|required');
        $this->form_validation->set_rules('father_name', translate('father_name'), 'trim|required');
        $this->form_validation->set_rules('mother_name', translate('mother_name'), 'trim|required');
        $this->form_validation->set_rules('mobile_no', translate('mobile_no'), 'trim|required|numeric');
        $this->form_validation->set_rules('no_of_child', translate('no_of_child'), 'trim|required|numeric');
        $this->form_validation->set_rules('staff_id', translate('assigned'), 'trim|required');
        $this->form_validation->set_rules('reference', translate('reference'), 'trim|required');
        $this->form_validation->set_rules('response_id', translate('reference'), 'trim|required');
        $this->form_validation->set_rules('email', translate('email'), 'trim|valid_email');
        $this->form_validation->set_rules('address', translate('address'), 'trim|required');
        $this->form_validation->set_rules('date', translate('date'), 'trim|required');
        $this->form_validation->set_rules('class_id', translate('class_applying_for'), 'trim|required');
    }

    public function enquiry()
    {
        if ($_POST) {
            if (get_permission('enquiry', 'is_add')) {
                $this->enquiry_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->reception_model->enquirySave($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }

        if (!get_permission('enquiry', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('enquiry');
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('admission') . " " . translate('enquiry');
        $this->data['sub_page'] = 'reception/enquiry';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function enquiry_edit($id = '')
    {
        if (!get_permission('enquiry', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->enquiry_validation();
            if ($this->form_validation->run() !== false) {
                // save information in the database file
                $this->reception_model->enquirySave($this->input->post());
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success', 'url' => base_url('reception/enquiry'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['row'] = $this->app_lib->getTable('enquiry', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('admission') . " " . translate('enquiry');
        $this->data['sub_page'] = 'reception/enquiry_edit';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function enquiry_delete($id)
    {
        if (get_permission('enquiry', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('enquiry');
        }
    }

    protected function follow_up_validation()
    {
        $this->form_validation->set_rules('date', translate('follow_up') . " " . translate('date'), 'trim|required');
        $this->form_validation->set_rules('follow_up_date', translate('next') . " " . translate('follow_up') . " " . translate('date'), 'trim|required');
        $this->form_validation->set_rules('status', translate('status'), 'trim|required');
    }

    public function enquiry_details($id)
    {
        if ($_POST) {
            if (get_permission('follow_up', 'is_add')) {
                $this->follow_up_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayInsert = array(
                        'enquiry_id' => $this->input->post('enquiry_id'),
                        'date' => $this->input->post('date'),
                        'next_date' => $this->input->post('follow_up_date'),
                        'response' => $this->input->post('response'),
                        'note' => $this->input->post('note'),
                        'status' => $this->input->post('status'),
                        'follow_up_by' => get_loggedin_user_id(),
                        'created_at' => date('Y-m-d'),
                    );
                    $this->db->insert('enquiry_follow_up', $arrayInsert);
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }
        if (!get_permission('follow_up', 'is_view')) {
            access_denied();
        }
        $this->data['row'] = $this->app_lib->getTable('enquiry', array('t.id' => $id), true);
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('admission') . " " . translate('enquiry');
        $this->data['sub_page'] = 'reception/enquiry_details';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function follow_up_delete($id)
    {
        if (get_permission('follow_up', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('enquiry_follow_up');
        }
    }
}
