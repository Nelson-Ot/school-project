<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Online_admission_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // moderator student all information
    public function save($data = array(), $getBranch = array())
    {
        $existStudent_photo = $this->input->post('exist_student_photo');
        $existGuardian_photo = $this->input->post('exist_guardian_photo');
        if (empty($existStudent_photo)) {
            $studentPhoto = $this->uploadImage('student', 'student_photo');
        } else {
            $studentPhoto = $existStudent_photo;
        }
        if (empty($existGuardian_photo)) {
            $guardianPhoto = $this->uploadImage('parent', 'guardian_photo');
        } else {
            $guardianPhoto = $existGuardian_photo;
        }

        $hostelID = empty($data['hostel_id']) ? 0 : $data['hostel_id'];
        $roomID = empty($data['room_id']) ? 0 : $data['room_id'];
        $previous_details = array(
            'school_name' => $this->input->post('school_name'),
            'qualification' => $this->input->post('qualification'),
            'remarks' => $this->input->post('previous_remarks'),
        );
        if (empty($previous_details)) {
            $previous_details = "";
        } else {
            $previous_details = json_encode($previous_details);
        }
        $inser_data1 = array(
            'register_no' => $this->input->post('register_no'),
            'admission_date' => (isset($data['admission_date']) ? date("Y-m-d", strtotime($data['admission_date'])) : ""),
            'first_name' => $this->input->post('first_name'),
            'last_name' => $this->input->post('last_name'),
            'gender' => $this->input->post('gender'),
            'birthday' => (isset($data['birthday']) ? date("Y-m-d", strtotime($data['birthday'])) : ""),
            'religion' => $this->input->post('religion'),
            'caste' => $this->input->post('caste'),
            'blood_group' => $this->input->post('blood_group'),
            'mother_tongue' => $this->input->post('mother_tongue'),
            'current_address' => $this->input->post('current_address'),
            'permanent_address' => $this->input->post('permanent_address'),
            'city' => $this->input->post('city'),
            'state' => $this->input->post('state'),
            'mobileno' => $this->input->post('mobileno'),
            'category_id' => (isset($data['category_id']) ? $data['category_id'] : 0),
            'email' => $this->input->post('email'),
            'parent_id' => "",
            'route_id' => $this->input->post('route_id'),
            'vehicle_id' => $this->input->post('vehicle_id'),
            'hostel_id' => $hostelID,
            'room_id' => $roomID,
            'previous_details' => $previous_details,
            'photo' => $studentPhoto,
        );

        // add new guardian all information in db
        if (!empty($data['grd_name']) || !empty($data['father_name'])) {
            $arrayParent = array(
                'name' => $this->input->post('grd_name'),
                'relation' => $this->input->post('grd_relation'),
                'father_name' => $this->input->post('father_name'),
                'mother_name' => $this->input->post('mother_name'),
                'occupation' => $this->input->post('grd_occupation'),
                'income' => $this->input->post('grd_income'),
                'education' => $this->input->post('grd_education'),
                'email' => $this->input->post('grd_email'),
                'mobileno' => $this->input->post('grd_mobileno'),
                'address' => $this->input->post('grd_address'),
                'city' => $this->input->post('grd_city'),
                'state' => $this->input->post('grd_state'),
                'branch_id' => $getBranch['id'],
                'photo' => $guardianPhoto,
            );
            $this->db->insert('parent', $arrayParent);
            $parentID = $this->db->insert_id();
            // save guardian login credential information in the database
            if ($getBranch['grd_generate'] == 1) {
                $grd_username = $getBranch['grd_username_prefix'] . $parentID;
                $grd_password = $getBranch['grd_default_password'];
            } else {
                $grd_username = $this->input->post('grd_username');
                $grd_password = $this->input->post('grd_password');
            }
            $parent_credential = array(
                'username' => $grd_username,
                'role' => 6,
                'user_id' => $parentID,
                'password' => $this->app_lib->pass_hashed($grd_password),
            );
            $this->db->insert('login_credential', $parent_credential);

            // insert student all information in the database
            $inser_data1['parent_id'] = $parentID;
        } else {
            $inser_data1['parent_id'] = 0;     
        }

        $this->db->insert('student', $inser_data1);
        $student_id = $this->db->insert_id();
        // save student login credential information in the database
        if ($getBranch['stu_generate'] == 1) {
            $stu_username = $getBranch['stu_username_prefix'] . $student_id;
            $stu_password = $getBranch['stu_default_password'];
        } else {
            $stu_username = $this->input->post('username');
            $stu_password = $this->input->post('password');
        }
        $inser_data2 = array(
            'user_id' => $student_id,
            'username' => $stu_username,
            'role' => 7,
            'password' => $this->app_lib->pass_hashed($stu_password),
        );
        $this->db->insert('login_credential', $inser_data2);

        // return student information
        $studentData = array(
            'student_id' => $student_id,
            'email' => $this->input->post('email'),
            'username' => $stu_username,
            'password' => $stu_password,
        );

        if (!empty($data['grd_name']) || !empty($data['father_name'])) {
            // send parent account activate email
            $emailData = array(
                'name' => $this->input->post('grd_name'),
                'username' => $grd_username,
                'password' => $grd_password,
                'user_role' => 6,
                'email' => $this->input->post('grd_email'),
            );
            $this->email_model->sentStaffRegisteredAccount($emailData);
        }
        return $studentData;
    }

    public function getOnlineAdmission($class_id = '', $branch_id = '')
    {
        $this->db->select('oa.*,c.name as class_name,se.name as section_name');
        $this->db->from('online_admission as oa');
        $this->db->join('class as c', 'oa.class_id = c.id', 'left');
        $this->db->join('section as se', 'oa.section_id = se.id', 'left');
        $this->db->where('oa.class_id', $class_id);
        $this->db->where('oa.branch_id', $branch_id);
        $this->db->order_by('oa.id', 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function regSerNumber($school_id = '')
    {
        $registerNoPrefix = '';
        if (!empty($school_id)) {
            $schoolconfig = $this->db->select('reg_prefix_enable,reg_start_from,institution_code,reg_prefix_digit')->where(array('id' => $school_id))->get('branch')->row();
            if ($schoolconfig->reg_prefix_enable == 1) {
                $registerNoPrefix = $schoolconfig->institution_code . $schoolconfig->reg_start_from;
                $last_registerNo = $this->app_lib->studentLastRegID($school_id);
                if (!empty($last_registerNo)) {
                    $last_registerNo_digit = str_replace($schoolconfig->institution_code, "", $last_registerNo->register_no);
                    if (!is_numeric($last_registerNo_digit)) {
                        $last_registerNo_digit = $schoolconfig->reg_start_from;
                    } else {
                        $last_registerNo_digit = $last_registerNo_digit + 1;
                    }
                    $registerNoPrefix = $schoolconfig->institution_code . sprintf("%0" . $schoolconfig->reg_prefix_digit . "d", $last_registerNo_digit);
                } else {
                    $registerNoPrefix = $schoolconfig->institution_code . sprintf("%0" . $schoolconfig->reg_prefix_digit . "d", $schoolconfig->reg_start_from);
                }
            }
            return $registerNoPrefix;
        } else {
            $config = $this->db->select('institution_code,reg_prefix')->where(array('id' => 1))->get('global_settings')->row();
            if ($config->reg_prefix == 'on') {
                $prefix = $config->institution_code;
            }
            $result = $this->db->select("max(id) as id")->get('student')->row_array();
            $id = $result["id"];
            if (!empty($id)) {
                $maxNum = str_pad($id + 1, 5, '0', STR_PAD_LEFT);
            } else {
                $maxNum = '00001';
            }
            return ($prefix . $maxNum);
        }
    }
}
