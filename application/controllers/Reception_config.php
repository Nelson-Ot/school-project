<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Reception_config.php
 * @copyright : Reserved RamomCoder Team
 */

class Reception_config extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect(base_url('reception_config/reference'));
    }

    /* form validation rules */
    protected function f_Validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
    }

    public function reference()
    {
        if ($_POST) {
            if (get_permission('config_reception', 'is_add')) {
                $this->f_Validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayReference = array(
                        'name' => $this->input->post('name'),
                        'branch_id' => $this->application_model->get_branch_id(),
                    );
                    $this->db->insert('enquiry_reference', $arrayReference);
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

        if (!get_permission('config_reception', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('enquiry_reference');
        $this->data['title'] = translate('reference');
        $this->data['sub_page'] = 'reception_config/reference';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function response()
    {
        if ($_POST) {
            if (get_permission('config_reception', 'is_add')) {
                $this->f_Validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayReference = array(
                        'name' => $this->input->post('name'),
                        'branch_id' => $this->application_model->get_branch_id(),
                    );
                    $this->db->insert('enquiry_response', $arrayReference);
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

        if (!get_permission('config_reception', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('enquiry_response');
        $this->data['title'] = translate('response');
        $this->data['sub_page'] = 'reception_config/response';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function calling_purpose()
    {
        if ($_POST) {
            if (get_permission('config_reception', 'is_add')) {
                $this->f_Validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayReference = array(
                        'name' => $this->input->post('name'),
                        'branch_id' => $this->application_model->get_branch_id(),
                    );
                    $this->db->insert('call_purpose', $arrayReference);
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

        if (!get_permission('config_reception', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('call_purpose');
        $this->data['title'] = translate('calling_purpose');
        $this->data['sub_page'] = 'reception_config/calling_purpose';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function visiting_purpose()
    {
        if ($_POST) {
            if (get_permission('config_reception', 'is_add')) {
                $this->f_Validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayReference = array(
                        'name' => $this->input->post('name'),
                        'branch_id' => $this->application_model->get_branch_id(),
                    );
                    $this->db->insert('visitor_purpose', $arrayReference);
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

        if (!get_permission('config_reception', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('visitor_purpose');
        $this->data['title'] = translate('visiting_purpose');
        $this->data['sub_page'] = 'reception_config/visiting_purpose';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function complaint_type()
    {
        if ($_POST) {
            if (get_permission('config_reception', 'is_add')) {
                $this->f_Validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $arrayReference = array(
                        'name' => $this->input->post('name'),
                        'branch_id' => $this->application_model->get_branch_id(),
                    );
                    $this->db->insert('complaint_type', $arrayReference);
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

        if (!get_permission('config_reception', 'is_view')) {
            access_denied();
        }
        $this->data['result'] = $this->app_lib->getTable('complaint_type');
        $this->data['title'] = translate('complaint') . " " . translate('type');
        $this->data['sub_page'] = 'reception_config/complaint_type';
        $this->data['main_menu'] = 'reception';
        $this->load->view('layout/index', $this->data);
    }

    public function edit($table = '')
    {
        if (!get_permission('config_reception', 'is_edit')) {
            ajax_access_denied();
        }
        $this->f_Validation();
        if ($this->form_validation->run() !== false) {
            $id = $this->input->post('id');
            $arrayData = array(
                'name' => $this->input->post('name'),
                'branch_id' => $this->application_model->get_branch_id(),
            );
            $this->db->where('id', $id);
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->update($table, $arrayData);
            set_alert('success', translate('information_has_been_updated_successfully'));
            $array = array('status' => 'success');
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    // get details send by ajax
    public function getDetails()
    {
        if (get_permission('config_reception', 'is_edit')) {
            $id = $this->input->post('id');
            $table = $this->input->post('table');
            $this->db->where('id', $id);
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $query = $this->db->get($table);
            $result = $query->row_array();
            echo json_encode($result);
        }
    }

    public function delete($table = '', $id = '')
    {
        if (get_permission('config_reception', 'is_delete') & !empty($table)) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete($table);
        }
    }
}
