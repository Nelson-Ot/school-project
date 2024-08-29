<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Student_promotion.php
 * @copyright : Reserved RamomCoder Team
 */

class Student_promotion extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('fees_model');
    }

    public function index()
    {
        // check access permission
        if (!get_permission('student_promotion', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($this->input->post()) {
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['students'] = $this->application_model->getStudentListByClassSection($this->data['class_id'], $this->data['section_id'], $branchID, false, true, false);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('student_promotion');
        $this->data['sub_page'] = 'student_promotion/index';
        $this->data['main_menu'] = 'transfer';
        $this->load->view('layout/index', $this->data);
    }

    public function transfersave()
    {
        // check access permission
        if (!get_permission('student_promotion', 'is_add')) {
            ajax_access_denied();
        }

        if ($_POST) {
            $dueForward = (isset($_POST['due_forward']) ? 1 : 0);
            $this->form_validation->set_rules('promote_session_id', translate('promote_to_session'), 'required');
            $this->form_validation->set_rules('promote_class_id', translate('promote_to_class'), 'required|callback_validClass');
            $this->form_validation->set_rules('promote_section_id', translate('promote_section_id'), 'required|callback_validSection');
            $items = $this->input->post('promote');
            foreach ($items as $key => $value) {
                if (isset($value['enroll_id'])) {
                    $this->form_validation->set_rules('promote[' . $key . '][roll]', translate('roll'), 'callback_unique_prom_roll');
                }
            }
            if ($this->form_validation->run() !== false) {
                $promotion_historys = array();
                $pre_class_id = $this->input->post('class_id');
                $pre_section_id = $this->input->post('section_id');
                $pre_session_id = get_session_id();
                $promote_session_id = $this->input->post('promote_session_id');
                
                $promote_classID = $this->input->post('promote_class_id');
                $promote_sectionID = $this->input->post('promote_section_id');

                $branchID = $this->application_model->get_branch_id();
                $promote = $this->input->post('promote');

                $school = $this->fees_model->get('branch', array('id' => $branchID), true);
                $due_days = empty($school['due_days']) ? 1 : $school['due_days'];

                foreach ($promote as $key => $value) {
                    if (isset($value['enroll_id'])) {

                        $leaveStatus = (isset($value['leave']) ? 1 : 0);
                        if ($leaveStatus == 1) {
                            $promote_class_id = $pre_class_id;
                            $promote_section_id = $pre_section_id;
                        } else {
                            if ($value['class_status'] == 'running') {
                                $promote_class_id = $pre_class_id;
                                $promote_section_id = $pre_section_id;
                            } else {
                                $promote_class_id = $promote_classID;
                                $promote_section_id = $promote_sectionID;
                            }
                        }

                        $promotion_history                  = array();
                        $promotion_history['student_id']    = $value['student_id'];
                        $promotion_history['pre_class']     = $pre_class_id;
                        $promotion_history['pre_section']   = $pre_section_id;
                        $promotion_history['pre_session']   = $pre_session_id;
                        $promotion_history['pro_class']     = $promote_class_id;
                        $promotion_history['pro_section']   = $promote_section_id;
                        $promotion_history['pro_session']   = ($leaveStatus == 1 ? $pre_session_id : $promote_session_id);
                        $promotion_history['date']          = date('Y-m-d H:i:s');
                        $promotion_history['prev_due']      = 0;
                        $promotion_history['is_leave']     = 0;

                        $enroll_id = $value['enroll_id'];
                        $student_id = $value['student_id'];

                        if ($leaveStatus == 1) {
                            $this->db->where('id', $enroll_id);
                            $this->db->update('enroll', ['is_alumni' => 1]);
                            $promotion_history['is_leave'] = 1;
                        } else {
                            $roll = empty($value['roll']) ? 0 : $value['roll'];
                            // check existing data
                            $this->db->where('student_id', $student_id);
                            $this->db->where('session_id', $promote_session_id);
                            $query = $this->db->get('enroll');

                            // insert promotion data
                            $arrayData = array(
                                'student_id'    => $student_id,
                                'class_id'      => $promote_class_id,
                                'roll'          => $roll,
                                'section_id'    => $promote_section_id,
                                'session_id'    => $promote_session_id,
                                'branch_id'     => $branchID,
                            );
                            if ($query->num_rows() > 0) {
                                $this->db->where('id', $query->row()->id);
                                $this->db->update('enroll', $arrayData);
                                $enroll_id = $query->row()->id;
                            } else {
                                $this->db->insert('enroll', $arrayData);
                                $enroll_id = $this->db->insert_id();
                            }

                            // insert carry forward due data
                            if ($dueForward == 1) {
                                if (!empty($value['due_amount']) && $value['due_amount'] != 0) {
                                    $promotion_history['prev_due'] = $value['due_amount'];
                                    $arrayForwardDue = array(
                                        'branch_id'     => $branchID, 
                                        'session_id'    => $promote_session_id, 
                                        'student_id'    => $enroll_id, 
                                        'prev_due'      => $value['due_amount'], 
                                        'due_date'      => date('Y-m-d', strtotime("+$due_days Days")), 
                                    );
                                    $this->fees_model->carryForwardDue($arrayForwardDue);
                                }
                            }
                        }

                        $promotion_historys[] = $promotion_history;
                    }
                }

                $this->db->insert_batch('promotion_history', $promotion_historys);
                set_alert('success', translate('information_has_been_updated_successfully'));
                $url = base_url('student_promotion');
                $array = array('status' => 'success', 'url' => $url, 'error' => '');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'url' => '', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function getPromotionStatus()
    {
        if ($_POST) {
            // check access permission
            if (!get_permission('student_promotion', 'is_add')) {
                ajax_access_denied();
            }
            $class_id = $this->input->post('class_id');
            $section_id = $this->input->post('section_id');
            $session_id = $this->input->post('session_id');
            if (empty($class_id) || empty($section_id) || empty($session_id)) {
                $array = array('status' => 2);
                echo json_encode($array);
                exit();
            }
            $r = $this->db->select('student_id')->where(array('class_id' => $class_id, 'section_id' => $section_id, 'session_id' => $session_id))->get('enroll')->result_array();
            if (empty($r) ) {
                $array = array('status' => 0);
            } else {
                $r = array_column($r, 'student_id');
                $array = array('status' => 1, 'msg' => '<i class="far fa-check-circle"></i> Mark students have already been promoted, you can only update now.', 'stu_arr' => $r);
            }
            echo json_encode($array);
        }
    }

    public function unique_prom_roll($roll)
    {
        if (!empty($roll)) {
            $promote_session_id = $this->input->post('promote_session_id');
            $promote_class_id = $this->input->post('promote_class_id');
            $promote_section_id = $this->input->post('promote_section_id');
            $branchID = $this->application_model->get_branch_id();
            $schoolSettings = $this->fees_model->get('branch', array('id' => $branchID), true, false, 'unique_roll');
            $unique_roll = $schoolSettings['unique_roll'];
            if (!empty($unique_roll) && $unique_roll != 0) {
                $this->db->select('id');
                if ($unique_roll == 2) {
                    $this->db->where('section_id', $promote_section_id);
                }
                $this->db->where(array('roll' => $roll, 'class_id' => $promote_class_id, 'session_id' => $promote_session_id, 'branch_id' => $branchID));
                $r = $this->db->get('enroll');

                if ($r->num_rows() == 0) {
                    return true;
                } else {
                    $this->form_validation->set_message('unique_prom_roll', "The %s is already exists.");
                    return false;
                }
            }
        }
        return true;
    }

    function validClass($classID) {
        if (!empty($classID)) {
            $pre_class_id = $this->input->post('class_id');
            $promote_session_id = $this->input->post('promote_session_id');
            if ($pre_class_id == $classID && $promote_session_id == get_session_id()) {
                $this->form_validation->set_message('validClass', translate("wrong_command"));
                return false;
            }
        }
        return true; 
    }

    function validSection($sectionID) {
        if (!empty($sectionID)) {
            $pre_class_id = $this->input->post('class_id');
            $pre_section_id = $this->input->post('section_id');
            $promote_session_id = $this->input->post('promote_session_id');
            $promote_class_id = $this->input->post('promote_class_id');
            if (($promote_session_id == get_session_id()) && ($pre_class_id == $promote_class_id) && ($pre_section_id == $sectionID)) {
                $this->form_validation->set_message('validSection', translate("wrong_command"));
                return false;
            }
        }
        return true; 
    }
}
