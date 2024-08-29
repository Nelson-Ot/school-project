<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : system_student_field.php
 * @copyright : Reserved RamomCoder Team
 */

class System_student_field extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('student_fields_model');
    }

    public function index()
    {
        // check access permission
        if (!get_permission('system_student_field', 'is_view')) {
            access_denied();
        }

        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['sub_page'] = 'system_student_field/index';
        $this->data['title'] = translate('system_student_field');
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }

    public function save(){
        if ($_POST) {
            if (!get_permission('system_student_field', 'is_edit')) {
                ajax_access_denied();
            }
            $branchID = $this->application_model->get_branch_id();
            $systemFields = $this->input->post('system_fields');
            foreach ($systemFields as $key => $value) {
                $is_status= (isset($value['status']) ? 1 : 0);
                $is_required = (isset($value['required']) ? 1 : 0);
                $arrayData = array(
                    'fields_id' => $key,
                    'branch_id' => $branchID,
                    'status' => $is_status,
                    'required' => $is_required,
                );
                $exist_privileges = $this->db->select('id')->limit(1)->where(array('branch_id' => $branchID, 'fields_id' => $key))->get('student_admission_fields')->num_rows();
                if ($exist_privileges > 0) {
                    $this->db->update('student_admission_fields', $arrayData, array('fields_id' => $key, 'branch_id' => $branchID));
                } else {
                    $this->db->insert('student_admission_fields', $arrayData);
                }
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
            echo json_encode($array);
        }
    }

    public function save_profile(){
        if ($_POST) {
            if (!get_permission('system_student_field', 'is_edit')) {
                ajax_access_denied();
            }
            $branchID = $this->application_model->get_branch_id();
            $systemFields = $this->input->post('system_fields');
            foreach ($systemFields as $key => $value) {
                $is_status= (isset($value['status']) ? 1 : 0);
                $is_required = (isset($value['required']) ? 1 : 0);
                $arrayData = array(
                    'fields_id' => $key,
                    'branch_id' => $branchID,
                    'status' => $is_status,
                    'required' => $is_required,
                );
                $exist_privileges = $this->db->select('id')->limit(1)->where(array('branch_id' => $branchID, 'fields_id' => $key))->get('student_profile_fields')->num_rows();
                if ($exist_privileges > 0) {
                    $this->db->update('student_profile_fields', $arrayData, array('fields_id' => $key, 'branch_id' => $branchID));
                } else {
                    $this->db->insert('student_profile_fields', $arrayData);
                }
            }
            $message = translate('information_has_been_saved_successfully');
            $array = array('status' => 'success', 'message' => $message);
            echo json_encode($array);
        }
    }


}
