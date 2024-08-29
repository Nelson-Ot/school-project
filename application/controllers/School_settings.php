<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : School_settings.php
 * @copyright : Reserved RamomCoder Team
 */

class School_settings extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('school_model');
    }

    public function index()
    {
        if (!get_permission('school_settings', 'is_view')) {
            access_denied();
        }

        $branchID = $this->school_model->getBranchID();
        if ($_POST) {
            if (!get_permission('school_settings', 'is_edit')) {
                ajax_access_denied();
            }
            if ($this->app_lib->licenceVerify() == false) {
                set_alert('error', translate('invalid_license'));
                $array = array('status' => 'access_denied');
                echo json_encode($array);
                exit();
            }

            $this->form_validation->set_rules('branch_name', translate('branch_name'), 'trim|required|callback_unique_branchname');
            $this->form_validation->set_rules('school_name', translate('school_name'), 'trim|required');
            $this->form_validation->set_rules('email', translate('email'), 'trim|required|valid_email');
            $this->form_validation->set_rules('currency', translate('currency'), 'trim|required');
            $this->form_validation->set_rules('currency_symbol', translate('currency_symbol'), 'trim|required');
            $this->form_validation->set_rules('due_days', translate('due_days'), 'trim|required|numeric');

            if (isset($_POST['generate_student'])) {
                $this->form_validation->set_rules('stu_username_prefix', translate('username_prefix'), 'trim|required');
                $this->form_validation->set_rules('stu_default_password', translate('default_password'), 'trim|required');
            }

            if (isset($_POST['generate_guardian'])) {
                $this->form_validation->set_rules('grd_username_prefix', translate('username_prefix'), 'trim|required');
                $this->form_validation->set_rules('grd_default_password', translate('default_password'), 'trim|required');
            }

            if (isset($_POST['reg_prefix_enable'])) {
                $this->form_validation->set_rules('reg_start_from', translate('register_no') . " " . translate('start_from'), 'trim|required|numeric');
                $this->form_validation->set_rules('institution_code', translate('institution_code'), 'trim|required');
                $this->form_validation->set_rules('reg_prefix_digit', translate('register_no') . " " . translate('digit'), 'trim|required');
            }
            $this->form_validation->set_rules('weekends[]', translate('weekends'), 'trim|required');

            if ($this->form_validation->run() == true) {

                $post = $this->input->post();
                $post['brance_id'] = $branchID;
                $this->school_model->branchUpdate($post);
                $id = $branchID;
                if (isset($_FILES["logo_file"]) && !empty($_FILES['logo_file']['name'])) {
                    $fileInfo = pathinfo($_FILES["logo_file"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["logo_file"]["tmp_name"], "uploads/app_image/logo-" . $img_name);
                }
                if (isset($_FILES["text_logo"]) && !empty($_FILES['text_logo']['name'])) {
                    $fileInfo = pathinfo($_FILES["text_logo"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["text_logo"]["tmp_name"], "uploads/app_image/logo-small-" . $img_name);
                }

                if (isset($_FILES["print_file"]) && !empty($_FILES['print_file']['name'])) {
                    $fileInfo = pathinfo($_FILES["print_file"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["print_file"]["tmp_name"], "uploads/app_image/printing-logo-" . $img_name);
                }

                if (isset($_FILES["report_card"]) && !empty($_FILES['report_card']['name'])) {
                    $fileInfo = pathinfo($_FILES["report_card"]["name"]);
                    $img_name = $id . '.' . $fileInfo['extension'];
                    move_uploaded_file($_FILES["report_card"]["tmp_name"], "uploads/app_image/report-card-logo-" . $img_name);
                }

                $message = translate('the_configuration_has_been_updated');
                set_alert('success', $message);
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['branchID'] = $branchID;
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/dropify/css/dropify.min.css',
            ),
            'js' => array(
                'vendor/dropify/js/dropify.min.js',
            ),
        );
        $this->data['school'] = $this->school_model->get('branch', array('id' => $branchID), true);
        $this->data['title'] = translate('school_settings');
        $this->data['sub_page'] = 'school_settings/school';
        $this->data['main_menu'] = 'school_m';
        $this->load->view('layout/index', $this->data);
    }

    public function unique_branchname($name)
    {
        $branchID = $this->school_model->getBranchID();
        $this->db->where_not_in('id', $branchID);
        $this->db->where('name', $name);
        $name = $this->db->get('branch')->num_rows();
        if ($name == 0) {
            return true;
        } else {
            $this->form_validation->set_message("unique_branchname", translate('already_taken'));
            return false;
        }
    }

    public function payment()
    {
        if (!get_permission('payment_settings', 'is_view')) {
            access_denied();
        }

        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['config'] = $this->school_model->get('payment_config', array('branch_id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/payment_gateway';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('payment_control');
        $this->load->view('layout/index', $this->data);
    }

    public function smsconfig()
    {
        if (!get_permission('sms_settings', 'is_view')) {
            access_denied();
        }

        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['api'] = $this->school_model->getSmsConfig($branchID);
        $this->data['title'] = translate('sms_settings');
        $this->data['sub_page'] = 'school_settings/smsconfig';
        $this->data['main_menu'] = 'school_m';
        $this->load->view('layout/index', $this->data);
    }

    public function sms_active()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $providerID = $this->input->post('sms_service_provider');
        $this->db->where('branch_id', $branchID)->update('sms_credential', array('is_active' => 0));
        $this->db->where(array('sms_api_id' => $providerID, 'branch_id' => $branchID))->update('sms_credential', array('is_active' => 1));
        if ($this->db->affected_rows() > 0) {
            $message = translate('information_has_been_saved_successfully');
        } else {
            $message = translate("SMS configuration not found");
        }
        $array = array('status' => 'success', 'message' => $message);
        echo json_encode($array);
    }

    public function twilio()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('twilio_sid', translate('account_sid'), 'trim|required');
        $this->form_validation->set_rules('twilio_auth_token', translate('authentication_token'), 'trim|required');
        $this->form_validation->set_rules('sender_number', translate('sender_number'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayTwilio = array(
                'field_one' => $this->input->post('twilio_sid'),
                'field_two' => $this->input->post('twilio_auth_token'),
                'field_three' => $this->input->post('sender_number'),
            );
            $this->db->where('sms_api_id', 1);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arrayTwilio['sms_api_id'] = 1;
                $arrayTwilio['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arrayTwilio);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arrayTwilio);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function clickatell()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('clickatell_user', translate('username'), 'trim|required');
        $this->form_validation->set_rules('clickatell_password', translate('password'), 'trim|required');
        $this->form_validation->set_rules('clickatell_api', translate('api_key'), 'trim|required');
        $this->form_validation->set_rules('sender_number', translate('sender_number'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayTwilio = array(
                'field_one' => $this->input->post('clickatell_user'),
                'field_two' => $this->input->post('clickatell_password'),
                'field_three' => $this->input->post('clickatell_api'),
                'field_four' => $this->input->post('sender_number'),
            );
            $this->db->where('sms_api_id', 2);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arrayTwilio['sms_api_id'] = 2;
                $arrayTwilio['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arrayTwilio);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arrayTwilio);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function msg91()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('msg91_auth_key', translate('authkey'), 'trim|required');
        $this->form_validation->set_rules('sender_id', translate('sender_id'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayTwilio = array(
                'field_one' => $this->input->post('msg91_auth_key'),
                'field_two' => $this->input->post('sender_id'),
            );
            $this->db->where('sms_api_id', 3);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arrayTwilio['sms_api_id'] = 3;
                $arrayTwilio['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arrayTwilio);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arrayTwilio);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function bulksms()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('bulk_sms_username', translate('username'), 'trim|required');
        $this->form_validation->set_rules('bulk_sms_password', translate('password'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayTwilio = array(
                'field_one' => $this->input->post('bulk_sms_username'),
                'field_two' => $this->input->post('bulk_sms_password'),
            );
            $this->db->where('sms_api_id', 4);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arrayTwilio['sms_api_id'] = 4;
                $arrayTwilio['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arrayTwilio);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arrayTwilio);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function textlocal()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('textlocal_sender_id', translate('sender_name'), 'trim|required');
        $this->form_validation->set_rules('api_key', translate('api_key'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayTwilio = array(
                'field_one' => $this->input->post('textlocal_sender_id'),
                'field_two' => $this->input->post('api_key'),
            );
            $this->db->where('sms_api_id', 5);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arrayTwilio['sms_api_id'] = 5;
                $arrayTwilio['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arrayTwilio);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arrayTwilio);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function sms_country()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('username', translate('username'), 'trim|required');
        $this->form_validation->set_rules('password', translate('password'), 'trim|required');
        $this->form_validation->set_rules('sender_id', translate('sender_id'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arraySMScountry = array(
                'field_one' => $this->input->post('username'),
                'field_two' => $this->input->post('password'),
                'field_three' => $this->input->post('sender_id'),
            );
            $this->db->where('sms_api_id', 6);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arraySMScountry['sms_api_id'] = 6;
                $arraySMScountry['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arraySMScountry);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arraySMScountry);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function bulksmsbd()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('sender_id', translate('sender_id'), 'trim|required');
        $this->form_validation->set_rules('api_key', translate('api_key'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arraySMScountry = array(
                'field_one' => $this->input->post('sender_id'),
                'field_two' => $this->input->post('api_key'),
            );
            $this->db->where('sms_api_id', 7);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arraySMScountry['sms_api_id'] = 7;
                $arraySMScountry['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arraySMScountry);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arraySMScountry);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function customSms()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('api_url', translate('api_url'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arraycustomSms = array(
                'field_one' => $this->input->post('api_url'),
            );
            $this->db->where('sms_api_id', 8);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_credential');
            if ($q->num_rows() == 0) {
                $arraycustomSms['sms_api_id'] = 8;
                $arraycustomSms['branch_id'] = $branchID;
                $this->db->insert('sms_credential', $arraycustomSms);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_credential', $arraycustomSms);
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function smstemplate()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['templatelist'] = $this->app_lib->get_table('sms_template');
        $this->data['title'] = translate('sms_settings');
        $this->data['sub_page'] = 'school_settings/smstemplate';
        $this->data['main_menu'] = 'school_m';
        $this->load->view('layout/index', $this->data);
    }

    public function smsTemplateeSave()
    {
        if (!get_permission('sms_settings', 'is_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('template_body', translate('body'), 'required');
        if ($this->form_validation->run() !== false) {
            $branchID = $this->school_model->getBranchID();
            $templateID = $this->input->post('template_id');
            $dlt_templateID = $this->input->post('dlt_template_id');
            $notify_student = isset($_POST['notify_student']) ? 1 : 0;
            $notify_parent = isset($_POST['notify_parent']) ? 1 : 0;
            $arrayTemplate = array(
                'notify_student' => $notify_student,
                'notify_parent' => $notify_parent,
                'dlt_template_id' => $dlt_templateID,
                'template_body' => $this->input->post('template_body'),
                'template_id' => $templateID,
                'branch_id' => $branchID,
            );

            $this->db->where('template_id', $templateID);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('sms_template_details');
            if ($q->num_rows() == 0) {
                $this->db->insert('sms_template_details', $arrayTemplate);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('sms_template_details', $arrayTemplate);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function emailconfig()
    {
        if (!get_permission('email_settings', 'is_view')) {
            access_denied();
        }
        if ($this->input->post('submit') == 'update') {
            $data = array();
            foreach ($this->input->post() as $key => $value) {
                if ($key == 'submit') {
                    continue;
                }
                $data[$key] = $value;
            }
            $this->db->where('id', 1);
            $this->db->update('email_config', $data);
            set_alert('success', translate('the_configuration_has_been_updated'));
            redirect(base_url('mailconfig/email'));
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['config'] = $this->school_model->get('email_config', array('branch_id' => $branchID), true);
        $this->data['title'] = translate('email_settings');
        $this->data['sub_page'] = 'school_settings/emailconfig';
        $this->data['main_menu'] = 'school_m';
        $this->load->view('layout/index', $this->data);
    }

    public function saveEmailConfig()
    {
        if (!get_permission('email_settings', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $protocol = $this->input->post('protocol');
        $this->form_validation->set_rules('email', 'System Email', 'trim|required');
        $this->form_validation->set_rules('protocol', 'Email Protocol', 'trim|required');
        if ($protocol == 'smtp') {
            $this->form_validation->set_rules('smtp_host', 'SMTP Host', 'trim|required');
            $this->form_validation->set_rules('smtp_user', 'SMTP Username', 'trim|required');
            $this->form_validation->set_rules('smtp_pass', 'SMTP Password', 'trim|required');
            $this->form_validation->set_rules('smtp_port', 'SMTP Port', 'trim|required');
        }
        if ($this->form_validation->run() !== false) {
            $arrayConfig = array(
                'email' => $this->input->post('email'),
                'protocol' => $protocol,
                'branch_id' => $branchID,
            );
            if ($protocol == 'smtp') {
                $arrayConfig['smtp_host'] = $this->input->post("smtp_host");
                $arrayConfig['smtp_user'] = $this->input->post("smtp_user");
                $arrayConfig['smtp_pass'] = $this->input->post("smtp_pass");
                $arrayConfig['smtp_port'] = $this->input->post("smtp_port");
                $arrayConfig['smtp_encryption'] = $this->input->post("smtp_encryption");
                $arrayConfig['smtp_auth'] = $this->input->post("smtp_auth");
            }
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('email_config');
            if ($q->num_rows() == 0) {
                $this->db->insert('email_config', $arrayConfig);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('email_config', $arrayConfig);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function emailtemplate()
    {
        if (!get_permission('email_settings', 'is_view')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['templatelist'] = $this->app_lib->get_table('email_templates');
        $this->data['title'] = translate('email_settings');
        $this->data['sub_page'] = 'school_settings/emailtemplate';
        $this->data['main_menu'] = 'school_m';
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/summernote/summernote.css',
            ),
            'js' => array(
                'vendor/summernote/summernote.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    public function emailTemplateSave()
    {
        if (!get_permission('email_settings', 'is_add')) {
            access_denied();
        }
        $this->form_validation->set_rules('subject', translate('subject'), 'required');
        $this->form_validation->set_rules('template_body', translate('body'), 'required');
        if ($this->form_validation->run() !== false) {
            $branchID = $this->application_model->get_branch_id();
            $notified = isset($_POST['notify_enable']) ? 1 : 0;
            $templateID = $this->input->post('template_id');
            $arrayTemplate = array(
                'template_id' => $templateID,
                'subject' => $this->input->post('subject'),
                'template_body' => $this->input->post('template_body'),
                'notified' => $notified,
                'branch_id' => $branchID,
            );

            $this->db->where('template_id', $templateID);
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('email_templates_details');
            if ($q->num_rows() == 0) {
                $this->db->insert('email_templates_details', $arrayTemplate);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('email_templates_details', $arrayTemplate);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    // transactions links enable / disabled
    public function accounting_links()
    {
        // check access permission
        if (!get_permission('accounting_links', 'is_view')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['transactions'] = $this->school_model->get('transactions_links', array('branch_id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/accounting_links';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('accounting_links');
        $this->load->view('layout/index', $this->data);
    }

    public function accountingLinksSave()
    {
        // check access permission
        if (!get_permission('accounting_links', 'is_edit')) {
            ajax_access_denied();
        }
        if (isset($_POST['status'])) {
            $this->form_validation->set_rules('deposit', translate('deposit'), 'trim|required');
            $this->form_validation->set_rules('expense', translate('expense'), 'trim|required');
        }
        $this->form_validation->set_rules('status', translate('status'), 'trim');
        if ($this->form_validation->run() !== false) {
            $branchID = $this->school_model->getBranchID();
            $array = array();
            if (isset($_POST['status'])) {
                $array['status'] = 1;
                $array['deposit'] = $this->input->post('deposit');
                $array['expense'] = $this->input->post('expense');
            } else {
                $array['status'] = 0;
            }
            $array['branch_id'] = $branchID;
            $this->db->where('branch_id', $branchID);
            $query = $this->db->get('transactions_links');
            if ($query->num_rows() > 0) {
                $this->db->where('id', $query->row()->id);
                $this->db->update('transactions_links', $array);
            } else {
                $this->db->insert('transactions_links', $array);
            }

            $array = array('status' => 'success', 'message' => translate('information_has_been_saved_successfully'));
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function live_class_config()
    {
        // check access permission
        if (!get_permission('live_class_config', 'is_view')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['config'] = $this->school_model->get('live_class_config', array('branch_id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/live_class_config';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('live_class') . " " . translate('settings');
        $this->load->view('layout/index', $this->data);
    }

    public function liveClassSave()
    {
        // check access permission
        if (!get_permission('live_class_config', 'is_edit')) {
            ajax_access_denied();
        }
        $method = $this->input->post('method');
        if ($method == 'zoom') {
            $this->form_validation->set_rules('zoom_api_key', "Zoom API Key", 'trim|required');
            $this->form_validation->set_rules('zoom_api_secret', "Zoom API Secret", 'trim|required');
            if ($this->form_validation->run() !== false) {
                $branchID = $this->school_model->getBranchID();
                $array = array(
                    'zoom_api_key' => $this->input->post('zoom_api_key'),
                    'zoom_api_secret' => $this->input->post('zoom_api_secret'),
                    'staff_api_credential' => !empty($this->input->post('staff_api_credential')) ? 1 : 0,
                    'student_api_credential' => !empty($this->input->post('student_api_credential')) ? 1 : 0,
                    'branch_id' => $branchID,
                );
                $this->db->where('branch_id', $branchID);
                $query = $this->db->get('live_class_config');
                if ($query->num_rows() > 0) {
                    $this->db->where('id', $query->row()->id);
                    $this->db->update('live_class_config', $array);
                } else {
                    $this->db->insert('live_class_config', $array);
                }
                $array = array('status' => 'success', 'message' => translate('information_has_been_saved_successfully'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
        } elseif ($method == 'bbb') {
            $this->form_validation->set_rules('bbb_salt_key', "Salt Key", 'trim|required');
            $this->form_validation->set_rules('bbb_server_base_url', "Server Base URL", 'trim|required');
            if ($this->form_validation->run() !== false) {
                $branchID = $this->school_model->getBranchID();
                $server_base_url = $this->input->post('bbb_server_base_url');
                if (substr($server_base_url, strlen($server_base_url) - 1, 1) != '/') {
                    $server_base_url .= '/';
                }
                $array = array(
                    'bbb_salt_key' => $this->input->post('bbb_salt_key'),
                    'bbb_server_base_url' => $server_base_url,
                    'branch_id' => $branchID,
                );
                $this->db->where('branch_id', $branchID);
                $query = $this->db->get('live_class_config');
                if ($query->num_rows() > 0) {
                    $this->db->where('id', $query->row()->id);
                    $this->db->update('live_class_config', $array);
                } else {
                    $this->db->insert('live_class_config', $array);
                }
                $array = array('status' => 'success', 'message' => translate('information_has_been_saved_successfully'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
        }
        echo json_encode($array);
    }

    public function whatsapp_setting()
    {
        // check access permission
        if (!get_permission('whatsapp_config', 'is_view')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->data['branch_id'] = $branchID;
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-timepicker/css/bootstrap-timepicker.css',
                'vendor/dropify/css/dropify.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-timepicker/bootstrap-timepicker.js',
                'vendor/dropify/js/dropify.min.js',
            ),
        );
        $this->data['whatsapp'] = $this->school_model->get('whatsapp_chat', array('branch_id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/whatsapp_settings';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('whatsapp_settings');
        $this->load->view('layout/index', $this->data);
    }

    public function saveWhatsappConfig()
    {
        if (!get_permission('whatsapp_config', 'is_add')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('header_title', translate('header_title'), 'trim|required');
        $this->form_validation->set_rules('subtitle', translate('subtitle'), 'trim|required');
        $this->form_validation->set_rules('footer_text', translate('footer_text'), 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayConfig = array(
                'header_title' => $this->input->post('header_title'),
                'subtitle' => $this->input->post('subtitle'),
                'footer_text' => $this->input->post('footer_text'),
                'frontend_enable_chat' => isset($_POST['frontend_enable_chat']) ? 1 : 0,
                'backend_enable_chat' => isset($_POST['backend_enable_chat']) ? 1 : 0,
                'branch_id' => $branchID,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('whatsapp_chat');
            if ($q->num_rows() == 0) {
                $this->db->insert('whatsapp_chat', $arrayConfig);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('whatsapp_chat', $arrayConfig);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function saveWhatsappAgent()
    {
        if (!get_permission('whatsapp_config', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
        $this->form_validation->set_rules('designation', translate('designation'), 'trim|required');
        $this->form_validation->set_rules('whataspp_number', translate('whataspp_number'), 'trim|required|numeric');
        $this->form_validation->set_rules('start_time', translate('start_time'), 'trim|required');
        $this->form_validation->set_rules('end_time', translate('end_time'), 'trim|required');
        $this->form_validation->set_rules('user_photo', translate('photo'), 'callback_photoHandleUpload[user_photo]');
        if ($this->form_validation->run() !== false) {
            $arrayConfig = array(
                'agent_name' => $this->input->post('name'),
                'agent_designation' => $this->input->post('designation'),
                'whataspp_number' => $this->input->post('whataspp_number'),
                'start_time' => date("H:i", strtotime($this->input->post('start_time'))),
                'end_time' => date("H:i", strtotime($this->input->post('end_time'))),
                'weekend' => $this->input->post('weekend'),
                'agent_image' => $this->school_model->uploadImage('whatsapp_agent'),
                'enable' => isset($_POST['agent_active']) ? 1 : 0,
                'branch_id' => $branchID,
            );
            $agentID = $this->input->post('agent_id');
            if (empty($agentID)) {
                $this->db->insert('whatsapp_agent', $arrayConfig);
            } else {
                unset($arrayConfig['branch_id']);
                $this->db->where('id', $agentID);
                $this->db->update('whatsapp_agent', $arrayConfig);
            }
            set_alert('success', translate('the_configuration_has_been_updated'));
            $array = array('status' => 'success');
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function whatsappAgent_delete($id)
    {
        if (get_permission('whatsapp_config', 'is_delete')) {
            $agent_image = $this->db->select('agent_image')->where('id', $id)->get('whatsapp_agent')->row()->agent_image;
            $file_name = FCPATH . 'uploads/images/whatsapp_agent/' . $agent_image;
            if (file_exists($file_name)) {
                unlink($file_name);
            }
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('whatsapp_agent');
        }
    }

    public function getWhatsappDetails($id)
    {
        if (get_permission('whatsapp_config', 'is_edit') && !empty($id)) {
            $this->data['whatsapp'] = $this->app_lib->getTable('whatsapp_agent', array('t.id' => $id), true);
            $this->load->view('school_settings/whatsapp_editModal', $this->data);
        }
    }

    public function send_test_email()
    {
        if ($_POST) {
            if (!get_permission('email_settings', 'is_add')) {
                ajax_access_denied();
            }
            $this->form_validation->set_rules('test_email', translate('email'), 'trim|required|valid_email');
            if ($this->form_validation->run() == true) {
                $branchID = $this->school_model->getBranchID();
                $getConfig = $this->db->select('id')->get_where('email_config', array('branch_id' => $branchID))->row();
                if (empty($getConfig)) {
                    $this->session->set_flashdata('test-email-error', 'Email Configuration not found.');
                    $array = array('status' => 'success');
                    echo json_encode($array);
                    exit;
                }

                $recipient = $this->input->post('test_email');
                $this->load->library('mailer');
                $data = array();
                $data['branch_id'] = $branchID;
                $data['recipient'] = $recipient;
                $data['subject'] = 'Ramom School SMTP Config Testing';
                $data['message'] = 'This is test SMTP config email. <br />If you received this message that means that your SMTP settings is set correctly.';
                $r = $this->mailer->send($data, true);
                if ($r == "true") {
                    $this->session->set_flashdata('test-email-success', 1);
                } else {
                    $this->session->set_flashdata('test-email-error', 'Mailer Error: ' . $r);
                }
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function attendance_type()
    {
        // check access permission
        if (!moduleIsEnabled('attendance')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        if ($_POST) {
            $arrayBranch = array(
                'attendance_type' => $this->input->post('attendance_type'),
            );
            $this->db->where('id', $branchID);
            $this->db->update('branch', $arrayBranch);
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
            echo json_encode($array);
            exit;
        }
        $this->data['branch_id'] = $branchID;
        $this->data['school'] = $this->school_model->get('branch', array('id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/attendance_type';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('attendance_type');
        $this->load->view('layout/index', $this->data);
    }

    public function student_parent_panel()
    {
        // check access permission
        if (!get_permission('school_settings', 'is_view')) {
            access_denied();
        }
        $branchID = $this->school_model->getBranchID();
        if ($_POST) {
            $mobile_visible = isset($_POST['teacher_mobile_visible']) ? 1 : 0;
            $email_visible = isset($_POST['teacher_email_visible']) ? 1 : 0;
            $arrayBranch = array(
                'teacher_mobile_visible' => $mobile_visible,
                'teacher_email_visible' => $email_visible,
                'student_login' => $this->input->post('student_login'),
                'parent_login' => $this->input->post('parent_login'),
            );
            $this->db->where('id', $branchID);
            $this->db->update('branch', $arrayBranch);
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
            echo json_encode($array);
            exit;
        }
        $this->data['branch_id'] = $branchID;
        $this->data['school'] = $this->school_model->get('branch', array('id' => $branchID), true);
        $this->data['sub_page'] = 'school_settings/student_parent_panel';
        $this->data['main_menu'] = 'school_m';
        $this->data['title'] = translate('student_parent_panel');
        $this->load->view('layout/index', $this->data);
    }


}
