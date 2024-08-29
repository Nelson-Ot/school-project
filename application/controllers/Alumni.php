<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Alumni.php
 * @copyright : Reserved RamomCoder Team
 */

class Alumni extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('sms_model');
        $this->load->model('alumni_model');
        $this->load->model('dashboard_model');
    }

    public function index()
    {
        if (!get_permission('manage_alumni', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        if ($this->input->post()) {
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['passing_session'] = $this->input->post('passing_session');
            $this->data['students'] = $this->alumni_model->getStudentListByClassSection($this->data['class_id'], $this->data['section_id'], $branchID, $this->data['passing_session']);
        }
        $this->data['branch_id'] = $branchID;
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['title'] = translate('alumni');
        $this->data['sub_page'] = 'alumni/index';
        $this->data['main_menu'] = 'alumni';
        $this->load->view('layout/index', $this->data);
    }

    // student alumni details send by ajax
    public function alumniDetails()
    {
        if (get_permission('manage_alumni', 'is_view')) {
            $id = $this->input->post('id');
            $this->db->where('enroll_id', $id);
            $query = $this->db->get('alumni_students');
            $result = $query->row_array();

            if (empty($result)) {
                $result = array(
                    'id'            => '',
                    'enroll_id'     => '',
                    'email'         => '',
                    'mobile_no'     => '',
                    'profession'    => '',
                    'address'       => '',
                    'photo'         => '',
                    'image_url'     => base_url('uploads/app_image/defualt.png'),
                );
            } else {
                $result['image_url'] = get_image_url('alumni', $result['photo']);
            }
            echo json_encode($result);
        }
    }

    public function save()
    {
        if ($_POST) {
            $this->form_validation->set_rules('mobile_no', translate('mobile_no'), 'trim|required');
            $this->form_validation->set_rules('email', translate('email'), 'trim|valid_email');
            // checking profile photo format
            $this->form_validation->set_rules('user_photo', translate('photo'), 'callback_photoHandleUpload[user_photo]');
            if ($this->form_validation->run() == true) {
                $insertData = array(
                    'enroll_id'     => $this->input->post('enroll_id'),
                    'email'         => $this->input->post('email'),
                    'mobile_no'     => $this->input->post('mobile_no'),
                    'profession'    => $this->input->post('profession'),
                    'address'       => $this->input->post('address'),
                );

                $id = $this->input->post('id');
                if (!empty($id) && $id != '') {
                    if (!get_permission('manage_alumni', 'is_edit')) {
                        ajax_access_denied();
                    }
                    $alumniImage = $this->input->post('old_image');
                    if (isset($_FILES["user_photo"]) && $_FILES['user_photo']['name'] != '' && (!empty($_FILES['user_photo']['name']))) {
                        $alumniImage = ($alumniImage == 'defualt.png' ? '' : $alumniImage);
                        $alumniImage = $this->alumni_model->fileupload("user_photo", "./uploads/images/alumni/", $alumniImage, true);
                    }
                    $insertData['photo'] = $alumniImage;

                    $this->db->where('id', $id);
                    $this->db->update('alumni_students', $insertData);
                } else {
                    if (!get_permission('manage_alumni', 'is_add')) {
                        ajax_access_denied();
                    }
                    $alumniImage = 'defualt.png';
                    if (isset($_FILES["user_photo"]) && $_FILES['user_photo']['name'] != '' && (!empty($_FILES['user_photo']['name']))) {
                        $alumniImage = $this->alumni_model->fileupload("user_photo", "./uploads/images/alumni/",'', true);
                    }
                    $insertData['photo'] = $alumniImage;
                    $this->db->insert('alumni_students', $insertData);
                }
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function delete($id)
    {
        if (get_permission('manage_alumni', 'is_delete')) {
            $photo = $this->db->select('photo')->where('id', $id)->get('alumni_students')->row()->photo;
            $file_name = FCPATH . '/uploads/images/alumni/' . $photo;
            if (file_exists($file_name)) {
                unlink($file_name);
            }
            $this->db->where('id', $id);
            $this->db->delete('alumni_students');
        }
    } 


    public function event()
    {
        if (!get_permission('alumni_events', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        $this->data['branch_id'] = $branchID;

        $language = 'en';
        $jsArray = array(
            'vendor/moment/moment.js',
            'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            'vendor/fullcalendar/fullcalendar.js',
        ); 
        if ($this->session->userdata('set_lang') != 'english') {
            $language = $this->dashboard_model->languageShortCodes($this->session->userdata('set_lang'));
            $jsArray[] = "vendor/fullcalendar/locale/$language.js";
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
                'vendor/fullcalendar/fullcalendar.css',
            ),
            'js' => $jsArray,
        );
        $this->data['language'] = $language;
        $this->data['title'] = translate('alumni');
        $this->data['sub_page'] = 'alumni/events';
        $this->data['main_menu'] = 'alumni';
        $this->load->view('layout/index', $this->data);
    }


    public function saveEvents()
    {
        if ($_POST) {
            $branchID = $this->application_model->get_branch_id();
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
            }
            $this->form_validation->set_rules('audience', translate('audience'), 'trim|required');
            $audience = $this->input->post('audience');
            if ($audience == 2) {
                $this->form_validation->set_rules('selected_audience[]', translate('class'), 'trim|required');
            } elseif ($audience == 3) {
                $this->form_validation->set_rules('selected_audience[]', translate('section'), 'trim|required');
            }
            if ($audience != 1) {
                $this->form_validation->set_rules('passing_session', translate('passing_session') . " " . translate('title'), 'trim|required');
            }

            $this->form_validation->set_rules('event_title', translate('events') . " " . translate('title'), 'trim|required');
            $this->form_validation->set_rules('from_date', translate('date_of_start'), 'trim|required');
            $this->form_validation->set_rules('to_date', translate('date_of_end'), 'trim|required');
            $this->form_validation->set_rules('note', translate('note'), 'trim|required');
            // checking profile photo format
            $this->form_validation->set_rules('user_photo', translate('photo'), 'callback_photoHandleUpload[user_photo]');
            if ($this->form_validation->run() == true) {
                $passing_session = "";
                if ($audience != 1) {
                    $selectedList = array();
                    $passing_session = $this->input->post('passing_session');
                    foreach ($this->input->post('selected_audience') as $user) {
                        array_push($selectedList, $user);
                    }
                } else {
                    $selectedList = null;
                }
                $insertData = array(
                    'title' => $this->input->post('event_title'),
                    'audience' => $this->input->post('audience'),
                    'session_id' => $passing_session,
                    'selected_list' => json_encode($selectedList),
                    'from_date' => $this->input->post('from_date'),
                    'to_date' => $this->input->post('to_date'),
                    'note' => $this->input->post('note'),
                    'branch_id' => $branchID,
                );

                $id = $this->input->post('id');
                if (!empty($id) && $id != '') {
                    if (!get_permission('alumni_events', 'is_edit')) {
                        ajax_access_denied();
                    }
                    $alumniImage = $this->input->post('old_image');
                    if (isset($_FILES["user_photo"]) && $_FILES['user_photo']['name'] != '' && (!empty($_FILES['user_photo']['name']))) {
                        $alumniImage = ($alumniImage == 'defualt.png' ? '' : $alumniImage);
                        $alumniImage = $this->alumni_model->fileupload("user_photo", "./uploads/images/alumni_events/", $alumniImage, true);
                    }
                    $insertData['photo'] = $alumniImage;

                    $this->db->where('id', $id);
                    $this->db->update('alumni_events', $insertData);
                } else {
                    if (!get_permission('alumni_events', 'is_add')) {
                        ajax_access_denied();
                    }
                    $alumniImage = 'defualt.png';
                    if (isset($_FILES["user_photo"]) && $_FILES['user_photo']['name'] != '' && (!empty($_FILES['user_photo']['name']))) {
                        $alumniImage = $this->alumni_model->fileupload("user_photo", "./uploads/images/alumni_events/",'', true);
                    }
                    $insertData['photo'] = $alumniImage;
                    $this->db->insert('alumni_events', $insertData);
                }

                // send sms to student
                if (isset($_POST['send_sms'])) {
                    $studentsArray = [];
                    if ($audience == 1) {
                        $students = $this->alumni_model->getlist($branchID);
                        foreach ($students as $student) {
                            $arraySMS = array(
                                'name' => $student['fullname'], 
                                'mobile_no' => $student['mobile_no'], 
                                'from_date' => _d($insertData['from_date']), 
                                'to_date' => _d($insertData['to_date']), 
                                'branch_id' => $branchID, 
                            );
                            $studentsArray[] = $arraySMS;
                        }
                    } elseif ($audience == 2) {
                        foreach ($this->input->post('selected_audience') as $user) {
                            $classID = $user;
                            $students = $this->alumni_model->getList($branchID, $classID, "", $passing_session);
                            foreach ($students as $student) {
                                $arraySMS = array(
                                    'name' => $student['fullname'], 
                                    'mobile_no' => $student['mobile_no'], 
                                    'from_date' => _d($insertData['from_date']), 
                                    'to_date' => _d($insertData['to_date']), 
                                    'branch_id' => $branchID, 
                                );
                                $studentsArray[] = $arraySMS;
                            }
                        }
                    } elseif ($audience == 3) {
                        foreach ($this->input->post('selected_audience') as $user) {
                            $array = explode('-', $user);
                            $students = $this->alumni_model->getList($branchID, $array[0], $array[1], $passing_session);
                            foreach ($students as $student) {
                                $arraySMS = array(
                                    'name' => $student['fullname'], 
                                    'event_title' => $insertData['title'], 
                                    'mobile_no' => $student['mobile_no'], 
                                    'from_date' => _d($insertData['from_date']), 
                                    'to_date' => _d($insertData['to_date']), 
                                    'branch_id' => $branchID,
                                );
                                $studentsArray[] = $arraySMS;
                            }
                        }
                    }
                    foreach ($studentsArray as $key => $value) {
                        $this->sms_model->alumniEvent($value);
                    }
                }
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function getEventsList()
    {
        if (get_permission('alumni_events', 'is_view')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('status', 1);
            $events = $this->db->get('alumni_events')->result();
            if (!empty($events)) {
                foreach ($events as $row) {
                    $arrayData = array(
                        'id' => $row->id,
                        'title' => $row->title,
                        'start' => $row->from_date,
                        'end' => date('Y-m-d', strtotime($row->to_date . "+1 days")),
                    );
                    $eventdata[] = $arrayData;
                }
                echo json_encode($eventdata);
            }
        }
    }

    public function event_delete($id)
    {
        if (get_permission('alumni_events', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $photo = $this->db->select('photo')->where('id', $id)->get('alumni_events')->row()->photo;
            $file_name = FCPATH . '/uploads/images/alumni_events/' . $photo;
            if (file_exists($file_name)) {
                unlink($file_name);
            }
            $this->db->where('id', $id);
            $this->db->delete('alumni_events');
        }
    } 

    public function eventDetails()
    {
        if (get_permission('alumni_events', 'is_view')) {
            $id = $this->input->post('id');
            $this->db->where('id', $id);
            $query = $this->db->get('alumni_events');
            $result = $query->row_array();

            if (empty($result)) {
                $result = array(
                    'id'            => '',
                    'title'         => '',
                    'audience'      => '',
                    'session_id'    => '',
                    'selected_list' => '',
                    'from_date'     => '',
                    'to_date'       => '',
                    'note'          => '',
                    'photo'         => '',
                    'show_web'      => '',
                    'branch_id'     => '',
                );
            }
            echo json_encode($result);
        }
    }

    public function getEventDetails()
    {
        if (get_permission('alumni_events', 'is_view')) {
            $id = $this->input->post('event_id');
            if (empty($id)) {
                redirect(base_url(), 'refresh');
            }

            $auditions = array("1" => "everybody", "2" => "class", "3" => "section");
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $ev = $this->db->get('alumni_events')->row_array();

            $type = '<img alt="" class="user-img-circle" src="' . get_image_url('alumni_events', $ev['photo']) . '" width="110" height="110">';
            $remark = (empty($ev['note']) ? 'N/A' : $ev['note']);
            $html = "<tbody><tr>";
            $html .= "<td>" . translate('title') . "</td>";
            $html .= "<td>" . $ev['title'] . "</td>";
            $html .= "</tr><tr>";
            $html .= "<td>" . translate('photo') . "</td>";
            $html .= "<td>" . $type . "</td>";
            $html .= "</tr><tr>";
            $html .= "<td>" . translate('date_of_start') . "</td>";
            $html .= "<td>" . _d($ev['from_date']) . "</td>";
            $html .= "</tr><tr>";
            $html .= "<td>" . translate('date_of_end') . "</td>";
            $html .= "<td>" . _d($ev['to_date']) . "</td>";
            $html .= "</tr><tr>";
            $html .= "<td>" . translate('audience') . "</td>";
            $audience = $auditions[$ev['audience']];
            $html .= "<td>" . translate($audience);
            if ($ev['audience'] != 1) {
                $selecteds = json_decode($ev['selected_list']);
                if ($ev['audience'] == 2) {
                    foreach ($selecteds as $selected) {
                        $html .= "<br> <small> - " .  get_type_name_by_id('class', $selected) . '</small>';
                    }
                }
                if ($ev['audience'] == 3) {
                    foreach ($selecteds as $selected) {
                        $selected = explode('-', $selected);
                        $html .= "<br> <small> - " .  get_type_name_by_id('class', $selected[0]) . " (" . get_type_name_by_id('section', $selected[1])  .  ')</small>';
                    }
                }
            }
            $html .= "</td>";
            $html .= "</tr><tr>";
            $html .= "<td>" . translate('note') . "</td>";
            $html .= "<td>" . $remark . "</td>";
            $html .= "</tr></tbody>";
            echo $html;
        }
    }
}
