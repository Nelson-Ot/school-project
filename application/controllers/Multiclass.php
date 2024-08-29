<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Multiclass.php
 * @copyright : Reserved RamomCoder Team
 */

class Multiclass extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('multiclass_model');
        if (!moduleIsEnabled('multi_class')) {
            access_denied();
        }
    }

    public function index()
    {
        // check access permission
        if (!get_permission('multi_class', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if (isset($_POST['search'])) {
            $classID = $this->input->post('class_id');
            $sectionID = $this->input->post('section_id');
            $this->data['students'] = $this->multiclass_model->getStudentListByClassSection($classID, $sectionID, $branchID, false, true);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student_list');
        $this->data['main_menu'] = 'admission';
        $this->data['sub_page'] = 'multiclass/index';
        $this->data['headerelements'] = array(
            'js' => array(
                'js/student.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    // student details
    public function ajaxClassList()
    {
        $id = $this->input->post('student_id');
        $this->data['student_id'] = $id;
        echo $this->load->view('multiclass/ajax', $this->data, true);
    }

    public function saveData()
    {
        if (!get_permission('multi_class', 'is_add')) {
            ajax_access_denied();
        }
        $items = $this->input->post('multiclass');
        $student_id = $this->input->post('student_id');
        $branchID = $this->application_model->get_branch_id();
        if (!empty($items)) {
            foreach ($items as $key => $value) {
                $this->form_validation->set_rules('multiclass[' . $key . '][class_id]', translate('class'), "required|callback_validClasss[$key]");
                $this->form_validation->set_rules('multiclass[' . $key . '][section_id]', translate('section'), 'required');
            }
        }
        if ($this->form_validation->run() == true) {
            if (!empty($items)) {
                $not_delarray = array();
                foreach ($items as $key => $value) {

                    $arrayInsert = array(
                        'class_id' => $value['class_id'],
                        'section_id' => $value['section_id'],
                        'session_id' => get_session_id(),
                        'student_id' => $student_id,
                        'branch_id' => $branchID,
                    );

                    $this->db->where($arrayInsert);
                    $q = $this->db->get('enroll');
                    if ($q->num_rows() > 0) {
                        $not_delarray[] = $q->row()->id;
                    } else {
                        $this->db->insert('enroll', $arrayInsert);
                        $not_delarray[] = $this->db->insert_id();
                    }
                }

                if (!empty($not_delarray)) {
                    $this->db->where('session_id', get_session_id());
                    $this->db->where('student_id', $student_id);
                    $this->db->where('branch_id', $branchID);
                    $this->db->where_not_in('id', $not_delarray);
                    $this->db->delete('enroll');
                }
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            $array = array('status' => 'success', 'url' => '', 'error' => '');
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'url' => '', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function validClasss($id, $row)
    {
        $duplicate_array = array();
        $multiClass = $this->input->post('multiclass');
        foreach ($multiClass as $key => $value) {
            $duplicate_array[] = $value['class_id'] . "-" . $value['section_id'];
        }

        $duplicate_record = 0;
        foreach (array_count_values($duplicate_array) as $val => $c) {
            if ($c > 1) {
                $duplicate_record = 1;
                break;
            }
        }
        if ($duplicate_record) {
            if (count($multiClass) == $row + 1) {
                $this->form_validation->set_message("validClasss", "Duplicate Class Select.");
                return false;
            }
        }
        return true;
    }
}
