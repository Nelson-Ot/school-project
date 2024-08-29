<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Home.php
 * @copyright : Reserved RamomCoder Team
 */

class Home extends Frontend_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helpers('custom_fields');
        $this->load->model('student_fields_model');
        $this->load->model('email_model');
        $this->load->model('testimonial_model');
        $this->load->model('gallery_model');
        $this->load->library('mailer');
    }

    public function index()
    {
        $this->home();
    }

    public function home()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['sliders'] = $this->home_model->getCmsHome('slider', $branchID, 1, false);
        $this->data['features'] = $this->home_model->getCmsHome('features', $branchID, 1, false);
        $this->data['wellcome'] = $this->home_model->getCmsHome('wellcome', $branchID);
        $this->data['teachers'] = $this->home_model->getCmsHome('teachers', $branchID);
        $this->data['testimonial'] = $this->home_model->getCmsHome('testimonial', $branchID);
        $this->data['services'] = $this->home_model->getCmsHome('services', $branchID);
        $this->data['cta_box'] = $this->home_model->getCmsHome('cta', $branchID);
        $this->data['statistics'] = $this->home_model->getCmsHome('statistics', $branchID);
        $this->data['page_data'] = $this->home_model->get('front_cms_home_seo', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/index', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function about()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_about', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/about', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function faq()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_faq', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/faq', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function events()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $url_alias = $this->data['cms_setting']['url_alias'];
        $getLatestEventList = $this->home_model->getLatestEventList($branchID);
        $page = html_escape(urldecode($this->input->get('page')));
        if (is_numeric($page)) {
            $page = !empty($page) ? $page : 0;
        } else {
            $page = 0;
        }
        if (!empty($getLatestEventList)) {
            $total_records = count($getLatestEventList);
        } else {
            $total_records = 0;
        }
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config["base_url"] = base_url() . $url_alias . '/events';
        $config["total_rows"] = $total_records;
        $config["per_page"] = 12;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '<i class="fas fa-angle-double-left"></i>';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '<i class="fas fa-angle-double-right"></i>';
        $config['last_tag_open'] = '<li class="next">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '<i class="fas fa-angle-right"></i>';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open'] = '<li class="previous">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</il>';
        $this->pagination->initialize($config);
        $conditions['limit'] = $config["per_page"];
        $conditions['start'] = $page;
        $this->data["links"] = $this->pagination->create_links();
        $this->data["results"] = $this->home_model->getLatestEventList($branchID, $conditions);
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_events', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/events', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function event_view($id)
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['event'] = $this->home_model->get('event', array('id' => $id, 'branch_id' => $branchID, 'status' => 1, 'show_web' => 1), true);
        if (empty($this->data['event']['id'])) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['page_data'] = $this->home_model->get('front_cms_events', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/event_view', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function news_view($alias = '')
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['event'] = $this->home_model->get('front_cms_news_list', array('alias' => $alias, 'branch_id' => $branchID, 'show_web' => 1), true);
        if (empty($this->data['event']['id'])) {
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['page_data'] = $this->home_model->get('front_cms_news', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/news_view', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function teachers()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_teachers', array('branch_id' => $branchID), true);
        $this->data['departments'] = $this->home_model->get_teacher_departments($branchID);
        $this->data['doctor_list'] = $this->home_model->get_teacher_list("", $branchID);
        $this->data['main_contents'] = $this->load->view('home/teachers', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function admission()
    {
        if (!$this->data['cms_setting']['online_admission']) {
            redirect(site_url('home'));
        }
        $branchID = $this->home_model->getDefaultBranch();
        $captcha = $this->data['cms_setting']['captcha_status'];
        if ($captcha == 'enable') {
            $this->load->library('recaptcha', array('site_key' => $this->data['cms_setting']['recaptcha_site_key'], 'secret_key' => $this->data['cms_setting']['recaptcha_secret_key']));
            $this->data['recaptcha'] = array(
                'widget' => $this->recaptcha->getWidget(),
                'script' => $this->recaptcha->getScriptTag(),
            );
        }
        if ($_POST) {
            $this->form_validation->set_rules("first_name", "First Name", "trim|required");
            $this->form_validation->set_rules("class_id", "Class", "trim|required");
            $this->form_validation->set_rules("guardian_photo", "Guardian Photo", "callback_handle_upload[guardian_photo]");
            $this->form_validation->set_rules("student_photo", "Student Photo", "callback_handle_upload[student_photo]");

            $validationArr = $this->student_fields_model->getOnlineStatusArr($branchID);
            unset($validationArr[0]);
            foreach ($validationArr as $key => $value) {
                if ($value->status && $value->required) {
                    if ($value->prefix == 'student_email' || $value->prefix == 'guardian_email') {
                        $this->form_validation->set_rules("$value->prefix", "Email", 'trim|required|valid_email');
                    } else if($value->prefix == 'student_mobile_no' || $value->prefix == 'guardian_mobile_no') {
                        $this->form_validation->set_rules("$value->prefix", "Mobile No", 'trim|required|numeric');
                    } else if($value->prefix == 'student_photo' || $value->prefix == 'guardian_photo' || $value->prefix == 'upload_documents') {
                        if (isset($_FILES["$value->prefix"]) && empty($_FILES["$value->prefix"]['name'])) {
                            $this->form_validation->set_rules("$value->prefix", ucwords(str_replace('_', ' ', $value->prefix)), "required" );
                        }
                    } else if($value->prefix == 'previous_school_details') {
                        $this->form_validation->set_rules("school_name", "School Name", "trim|required" );
                        $this->form_validation->set_rules("qualification", "Qualification", "trim|required" );
                    } else {
                        $this->form_validation->set_rules($value->prefix, ucwords(str_replace('_', ' ', $value->prefix)), 'trim|required');
                    }
                }  
            }

            if ($captcha == 'enable') {
                $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'trim|required');
            }
            // custom fields validation rules
            $customFields = getOnlineCustomFields('student', $branchID);
            foreach ($customFields as $fields_key => $fields_value) {
                if ($fields_value['required']) {
                    $fieldsID = $fields_value['id'];
                    $fieldLabel = $fields_value['field_label'];
                    $this->form_validation->set_rules("custom_fields[student][" . $fieldsID . "]", $fieldLabel, 'trim|required');
                }
            }

            if ($this->form_validation->run() == true) {
                $admissionDate = !empty($_POST['admission_date']) ? date("Y-m-d", strtotime($this->input->post('admission_date'))) : "";
                $birthday = !empty($_POST['birthday']) ? date("Y-m-d", strtotime($this->input->post('birthday'))) : "";
                
                $previous_details = $this->input->post('school_name');
                if (!empty($previous_details)) {
                    $previous_details = array(
                        'school_name' => $this->input->post('school_name'),
                        'qualification' => $this->input->post('qualification'),
                        'remarks' => $this->input->post('previous_remarks'),
                    );
                    $previous_details =  json_encode($previous_details);
                } else {
                    $previous_details = "";
                }

                do {
                    $reference_no = mt_rand(0000001, 99999999);
                    $refence_status = $this->home_model->checkAdmissionReferenceNo($reference_no);
                } while ($refence_status);

                $arrayData = array(
                    'reference_no' => $reference_no,
                    'first_name' => $this->input->post('first_name'),
                    'last_name' => $this->input->post('last_name'),
                    'gender' => $this->input->post('gender'),
                    'birthday' => $birthday,
                    'admission_date' => $admissionDate,
                    'religion' => $this->input->post('religion'),
                    'caste' => $this->input->post('caste'),
                    'blood_group' => $this->input->post('blood_group'),
                    'mobile_no' => $this->input->post('student_mobile_no'),
                    'mother_tongue' => $this->input->post('mother_tongue'),
                    'present_address' => $this->input->post('present_address'),
                    'permanent_address' => $this->input->post('permanent_address'),
                    'city' => $this->input->post('city'),
                    'state' => $this->input->post('state'),
                    'category_id' => $this->input->post('category'),
                    'email' => $this->input->post('student_email'),
                    'student_photo' => $this->uploadImage('images/student', 'student_photo'),
                    'previous_school_details' => $previous_details,
                    'guardian_name' => $this->input->post('guardian_name'),
                    'guardian_relation' => $this->input->post('guardian_relation'),
                    'father_name' => $this->input->post('father_name'),
                    'mother_name' => $this->input->post('mother_name'),
                    'grd_occupation' => $this->input->post('guardian_occupation'),
                    'grd_income' => $this->input->post('guardian_income'),
                    'grd_education' => $this->input->post('guardian_education'),
                    'grd_email' => $this->input->post('guardian_email'),
                    'grd_mobile_no' => $this->input->post('guardian_mobile_no'),
                    'grd_address' => $this->input->post('guardian_address'),
                    'grd_city' => $this->input->post('guardian_city'),
                    'grd_state' => $this->input->post('guardian_state'),
                    'grd_photo' => $this->uploadImage('images/parent', 'guardian_photo'),
                    'status' => 1,
                    'branch_id' => $branchID,
                    'class_id' => $this->input->post('class_id'),
                    'section_id' => $this->input->post('section'),
                    'doc' => $this->uploadImage('online_ad_documents', 'upload_documents'),
                    'apply_date' => date("Y-m-d H:i:s"),
                    'created_date' => date("Y-m-d H:i:s"),
                );
                $this->db->insert('online_admission', $arrayData);
                $studentID = $this->db->insert_id();

                // handle custom fields data
                $class_slug = 'student';
                $customField = $this->input->post("custom_fields[$class_slug]");
                if (!empty($customField)) {
                    saveCustomFieldsOnline($customField, $studentID);
                }
                // check out admission payment status
                $this->load->model('admissionpayment_model');
                $getStudent = $this->admissionpayment_model->getStudentDetails($studentID);
                if ($getStudent['fee_elements']['status'] == 0) {
                    $url = base_url("home/admission_confirmation/" . $reference_no);
                    if (empty($arrayData['section_id'])) {
                       $section_name = "N/A";
                    } else {
                       $section_name = get_type_name_by_id('section', $arrayData['section_id']);
                    }
                    // applicant email send 
                    $arrayData['institute_name'] = get_type_name_by_id('branch', $arrayData['branch_id']);
                    $arrayData['reference_no'] = $reference_no;
                    $arrayData['student_name'] = $arrayData['first_name'] . " " . $arrayData['last_name'];
                    $arrayData['class_name'] = get_type_name_by_id('class', $arrayData['class_id']);
                    $arrayData['section_name'] = $section_name;
                    $arrayData['payment_url'] = base_url("admissionpayment/index/" . $reference_no);
                    $arrayData['admission_copy_url'] = $url;
                    $arrayData['paid_amount'] = 0;
                    $this->email_model->onlineAdmission($arrayData);
                    
                    $success = "Thank you for submitting the online registration form. Please you can print this copy.";
                    $this->session->set_flashdata('success', $success);
                } else {
                    $url = base_url("admissionpayment/index/" . $reference_no);
                }
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }

        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_admission', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/admission', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function checkAdmissionStatus()
    {
        if ($_POST) {
            $this->form_validation->set_rules("refno", "Enter Your Reference Number", "trim|required|callback_admissionstatus");
            if ($this->form_validation->run() == true) {
                $reference_no = $this->input->post("refno");
                $url = base_url("home/admission_confirmation/" . $reference_no);
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);   
            }
            echo json_encode($array);
            exit(); 
        }
    }

    public function admissionstatus($reference_no)
    {
        if (!empty($reference_no)) {
            $this->db->where('reference_no', $reference_no);
            $query = $this->db->get('online_admission')->num_rows();
            if ($query < 1) {
                $this->form_validation->set_message('admissionstatus', "Invalid Reference Number.");
                return false;
            }
        }
        return true;
    }

    public function handle_upload($str, $fields)
    {
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $allowedExts = array('jpg', 'jpeg', 'png');
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["$fields"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('handle_upload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_upload', translate('file_size_shoud_be_less_than') . " 2048KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }

    public function uploadImage($role, $fields) {
        $return_photo = '';
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $config['upload_path'] = './uploads/' . $role . '/';
            $config['overwrite'] = FALSE;
            $config['encrypt_name'] = TRUE;
            $config['allowed_types'] = '*';
            $this->upload->initialize($config);
            if ($this->upload->do_upload("$fields")) {
                $return_photo = $this->upload->data('file_name');
            }
        }
        return $return_photo;
    }

    public function admission_confirmation($studentID = '')
    {
        $this->load->model('admissionpayment_model');
        $getStudent = $this->admissionpayment_model->getStudentDetails($studentID);
        if (empty($getStudent['id'])) {
            set_alert('error', "This application was not found.");
            redirect($_SERVER['HTTP_REFERER']);
        }
        $this->data['student'] = $getStudent;
        $this->data['branchID'] = $this->data['student']['branch_id'];
        $this->data['page_data'] = $this->home_model->get('front_cms_admission', array('branch_id' => $this->data['student']['branch_id']), true);
        $this->data['main_contents'] = $this->load->view('home/admission_confirmation', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function contact()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $captcha = $this->data['cms_setting']['captcha_status'];
        if ($captcha == 'enable') {
            $this->load->library('recaptcha', array('site_key' => $this->data['cms_setting']['recaptcha_site_key'], 'secret_key' => $this->data['cms_setting']['recaptcha_secret_key']));
            $this->data['recaptcha'] = array(
                'widget' => $this->recaptcha->getWidget(),
                'script' => $this->recaptcha->getScriptTag(),
            );
        }

        if ($_POST) {
            $this->form_validation->set_rules('name', 'Name', 'trim|required');
            $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
            $this->form_validation->set_rules('phoneno', 'Phone', 'trim|required');
            $this->form_validation->set_rules('subject', 'Subject', 'trim|required');
            $this->form_validation->set_rules('message', 'Message', 'trim|required');
            if ($captcha == 'enable') {
                $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'trim|required');
            }
            if ($this->form_validation->run() !== false) {
                if ($captcha == 'enable') {
                    $captchaResponse = $this->recaptcha->verifyResponse($this->input->post('g-recaptcha-response'));
                } else {
                    $captchaResponse = array('success' => true);
                }
                if ($captchaResponse['success'] == true) {
                    $name = $this->input->post('name');
                    $email = $this->input->post('email');
                    $phoneno = $this->input->post('phoneno');
                    $subject = $this->input->post('subject');
                    $message = $this->input->post('message');
                    $msg = '<h3>Sender Information</h3>';
                    $msg .= '<br><br><b>Name: </b> ' . $name;
                    $msg .= '<br><br><b>Email: </b> ' . $email;
                    $msg .= '<br><br><b>Phone: </b> ' . $phoneno;
                    $msg .= '<br><br><b>Subject: </b> ' . $subject;
                    $msg .= '<br><br><b>Message: </b> ' . $message;
                    $data = array(
                        'branch_id' => $branchID,
                        'recipient' => $this->data['cms_setting']['receive_contact_email'],
                        'subject' => 'Contact Form Email',
                        'message' => $msg,
                    );
                    $send = $this->mailer->send($data, true);
                    if ($send == true) {
                        $this->session->set_flashdata('msg_success', 'Message Successfully Sent. We will contact you shortly.');
                    } else {
                        $this->session->set_flashdata('msg_error',  'Message Not Successfully Sent. Error - ' . $send);
                    }
                } else {
                    $error = 'Captcha is invalid';
                    $this->session->set_flashdata('error', $error);
                }
                redirect(base_url('home/contact'));
            }
        }
        $this->data['page_data'] = $this->home_model->get('front_cms_contact', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/contact', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function admit_card()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_admitcard', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/admit_card', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function admitCardprintFn()
    {
        if ($_POST) {
            $this->load->model('card_manage_model');
            $this->load->model('timetable_model');
            $this->load->library('ciqrcode', array('cacheable' => false));
            $this->form_validation->set_rules('exam_id', translate('exam'), 'trim|required');
            $this->form_validation->set_rules('register_no', translate('register_no'), 'trim|required');
            if ($this->form_validation->run() == true) {
                //get all QR Code file
                $files = glob('uploads/qr_code/*');
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file); //delete file
                    }
                }
                $registerNo = $this->input->post('register_no');
                $userID = $this->db->select('id')->where('register_no', $registerNo)->get('student')->row_array();
                if (empty($userID)) {
                    $array = array('status' => '0', 'error' => "Register No Not Found.");
                    echo json_encode($array);
                    exit();
                }
                $templateID = $this->input->post('templete_id');
                if (empty($templateID) || $templateID == 0) {
                    $array = array('status' => '0', 'error' => "No Default Template Set.");
                    echo json_encode($array);
                    exit();
                }
                $this->data['exam_id'] = $this->input->post('exam_id');
                $this->data['userID'] = $userID;
                $this->data['template'] = $this->card_manage_model->get('card_templete', array('id' => $templateID), true);
                $this->data['print_date'] = date('Y-m-d');
                $card_data = $this->load->view('home/admitCardprintFn', $this->data, true);
                $array = array('status' => 'success', 'card_data' => $card_data);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function exam_results()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_exam_results', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/exam_results', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function examResultsPrintFn()
    {
        $this->load->model('exam_model');
        if ($_POST) {
            $this->form_validation->set_rules('exam_id', translate('exam'), 'trim|required');
            $this->form_validation->set_rules('register_no', translate('register_no'), 'trim|required');
            $this->form_validation->set_rules('session_id', translate('academic_year'), 'trim|required');
            if ($this->form_validation->run() == true) {
                $sessionID = $this->input->post('session_id');
                $registerNo = $this->input->post('register_no');
                $examID = $this->input->post('exam_id');

                $this->db->select('student.id,enroll.class_id,enroll.section_id');
                $this->db->from('enroll');
                $this->db->join('student', 'student.id = enroll.student_id', 'inner');
                $this->db->where('student.register_no', $registerNo);
                $this->db->where('enroll.session_id', $sessionID);
                $userID = $this->db->get()->row_array();

                if (empty($userID)) {
                    $array = array('status' => '0', 'error' => "Register No Not Found.");
                    echo json_encode($array);
                    exit();
                }
                $result = $this->exam_model->getStudentReportCard($userID['id'], $examID, $sessionID, $userID['class_id'], $userID['section_id']);
                if (empty($result['exam'])) {
                    $array = array('status' => '0', 'error' => "Exam Results Not Found.");
                    echo json_encode($array);
                    exit();
                }
                $this->data['result'] = $result;
                $this->data['sessionID'] = $sessionID;
                $this->data['userID'] = $userID['id'];
                $this->data['examID'] = $examID;
                $this->data['grade_scale'] = $this->input->post('grade_scale');
                $this->data['attendance'] = $this->input->post('attendance');
                $this->data['print_date'] = date('Y-m-d');
                $card_data = $this->load->view('home/reportCard', $this->data, true);
                $array = array('status' => 'success', 'card_data' => $card_data);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function certificates()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_certificates', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/certificates', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function certificatesPrintFn()
    {
        if ($_POST) {
            $this->load->model('certificate_model');
            $this->load->library('ciqrcode', array('cacheable' => false));
            //get all QR Code file
            $files = glob('uploads/qr_code/*');
            foreach ($files as $file) {
                if (is_file($file)) {
                    unlink($file); //delete file
                }
            }

            $this->form_validation->set_rules('templete_id', translate('certificate'), 'trim|required');
            $this->form_validation->set_rules('register_no', translate('register_no'), 'trim|required');
            if ($this->form_validation->run() == true) {

                $registerNo = $this->input->post('register_no');
                $examID = $this->input->post('exam_id');
                $userID = $this->db->select('id')->where('register_no', $registerNo)->get('student')->row_array();
                if (empty($userID)) {
                    $array = array('status' => '0', 'error' => "Register No Not Found.");
                    echo json_encode($array);
                    exit();
                }

                $this->data['user_type'] = 1;
                $templateID = $this->input->post('templete_id');
                $this->data['template'] = $this->certificate_model->get('certificates_templete', array('id' => $templateID), true);
                $this->data['userID'] = $userID['id'];
                $this->data['print_date'] = date('Y-m-d');
                $card_data = $this->load->view('home/certificatesPrintFn', $this->data, true);
                $array = array('status' => 'success', 'card_data' => $card_data);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function gallery()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_gallery', array('branch_id' => $branchID), true);
        $this->data['category'] = $this->home_model->getGalleryCategory($branchID);
        $this->data['galleryList'] = $this->home_model->getGalleryList($branchID);
        $this->data['main_contents'] = $this->load->view('home/gallery', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function gallery_view($alias = '')
    {
        $branchID = $this->home_model->getDefaultBranch();
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_gallery', array('branch_id' => $branchID), true);
        $this->data['gallery'] = $this->home_model->get('front_cms_gallery_content', array('branch_id' => $branchID, 'alias' => $alias), true);
        $this->data['category'] = $this->home_model->getGalleryCategory($branchID);
        $this->data['galleryList'] = $this->home_model->getGalleryList($branchID);
        $this->data['main_contents'] = $this->load->view('home/gallery_view', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function page($url = '')
    {
        $this->db->select('front_cms_menu.title as menu_title,front_cms_menu.alias,front_cms_pages.*');
        $this->db->from('front_cms_menu');
        $this->db->join('front_cms_pages', 'front_cms_pages.menu_id = front_cms_menu.id', 'inner');
        $this->db->where('front_cms_menu.alias', $url);
        $this->db->where('front_cms_menu.publish', 1);
        $getData = $this->db->get()->row_array();
      
        $this->data['page_data'] = $getData;
        $this->data['active_menu'] = 'page';
        $this->data['main_contents'] = $this->load->view('home/page', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }

    public function getSectionByClass()
    {
        $html = "";
        $classID = $this->input->post("class_id");
        if (!empty($classID)) {
            $result = $this->db->select('sections_allocation.section_id,section.name')
                ->from('sections_allocation')
                ->join('section', 'section.id = sections_allocation.section_id', 'left')
                ->where('sections_allocation.class_id', $classID)
                ->get()->result_array();
            if (is_array($result) && count($result)) {
                $html .= '<option value="">' . translate('select') . '</option>';
                foreach ($result as $row) {
                    $html .= '<option value="' . $row['section_id'] . '">' . $row['name'] . '</option>';
                }
            } else {
                $html .= '<option value="">' . translate('no_selection_available') . '</option>';
            }
        } else {
            $html .= '<option value="">' . translate('select_class_first') . '</option>';
        }
        echo $html;
    }

    public function get_branch_url()
    {
        $branch_id = $this->input->post("branch_id");
        $url = $this->db->select('url_alias')->where('branch_id', $branch_id)->get('front_cms_setting')->row_array();
        $school = "";
        if ($this->uri->segment(4)) {
            $school = $this->uri->segment(4);
        } else {
            $school = $this->uri->segment(3);
        }
        echo json_encode(array('url_alias' => base_url($url['url_alias'])));
    }

    public function news()
    {
        $branchID = $this->home_model->getDefaultBranch();
        $url_alias = $this->data['cms_setting']['url_alias'];
        $getLatestNewsList = $this->home_model->getLatestNewsList($branchID);
        $page = html_escape(urldecode($this->input->get('page')));
        if (is_numeric($page)) {
            $page = !empty($page) ? $page : 0;
        } else {
            $page = 0;
        }
        if (!empty($getLatestNewsList)) {
            $total_records = count($getLatestNewsList);
        } else {
            $total_records = 0;
        }
        $config = array();
        $config['page_query_string'] = TRUE;
        $config['query_string_segment'] = 'page';
        $config["base_url"] = base_url() . $url_alias . '/news';
        $config["total_rows"] = $total_records;
        $config["per_page"] = 12;
        $config['full_tag_open'] = '<ul class="pagination justify-content-center">';
        $config['full_tag_close'] = '</ul>';
        $config['first_link'] = '<i class="fas fa-angle-double-left"></i>';
        $config['first_tag_open'] = '<li class="previous">';
        $config['first_tag_close'] = '</li>';
        $config['last_link'] = '<i class="fas fa-angle-double-right"></i>';
        $config['last_tag_open'] = '<li class="next">';
        $config['last_tag_close'] = '</li>';
        $config['next_link'] = '<i class="fas fa-angle-right"></i>';
        $config['next_tag_open'] = '<li class="next">';
        $config['next_tag_close'] = '</li>';
        $config['prev_link'] = '<i class="fas fa-angle-left"></i>';
        $config['prev_tag_open'] = '<li class="previous">';
        $config['prev_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="active"><span>';
        $config['cur_tag_close'] = '</span></li>';
        $config['num_tag_open'] = '<li>';
        $config['num_tag_close'] = '</il>';
        $this->pagination->initialize($config);
        $conditions['limit'] = $config["per_page"];
        $conditions['start'] = $page;
        $this->data["links"] = $this->pagination->create_links();
        $this->data["results"] = $this->home_model->getLatestNewsList($branchID, $conditions);
        $this->data['branchID'] = $branchID;
        $this->data['page_data'] = $this->home_model->get('front_cms_news', array('branch_id' => $branchID), true);
        $this->data['main_contents'] = $this->load->view('home/news', $this->data, true);
        $this->load->view('home/layout/index', $this->data);
    }
}
