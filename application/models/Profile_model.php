<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Profile_model extends MY_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    // moderator staff all information
    public function staffUpdate($data)
    {
        $update_data = array(
            'name' => $data['name'],
            'sex' => $data['sex'],
            'religion' => $data['religion'],
            'blood_group' => $data['blood_group'],
            'birthday' => $data["birthday"],
            'mobileno' => $data['mobile_no'],
            'present_address' => $data['present_address'],
            'permanent_address' => $data['permanent_address'],
            'photo' => $this->uploadImage('staff'),
            'email' => $data['email'],
            'facebook_url' => $data['facebook'],
            'linkedin_url' => $data['linkedin'],
            'twitter_url' => $data['twitter'],
        );
        if (is_admin_loggedin()) {
            $update_data['joining_date'] = date("Y-m-d", strtotime($data['joining_date']));
            $update_data['designation'] = $data['designation_id'];
            $update_data['department'] = $data['department_id'];
            $update_data['qualification'] = $data['qualification'];
        }
        // UPDATE ALL INFORMATION IN THE DATABASE
        $this->db->where('id', get_loggedin_user_id());
        $this->db->update('staff', $update_data);
    }

    public function studentUpdate($data)
    {
        
        $arrData = array();
        if (isset($data['admission_date'])) {
            $arrData['admission_date'] = date("Y-m-d", strtotime($data['admission_date']));
        }
        if (isset($data['category_id'])) {
            $arrData['category_id'] = $data['category_id'] ;
        }
        if (isset($data['first_name'])) {
            $arrData['first_name'] = $data['first_name'];
        }
        if (isset($data['last_name'])) {
            $arrData['last_name'] = $data['last_name'];
        }
        if (isset($data['gender'])) {
            $arrData['gender'] = $data['gender'];
        }
        if (isset($data['blood_group'])) {
            $arrData['blood_group'] = $data['blood_group'];
        }
        if (isset($data['birthday'])) {
            $arrData['birthday'] = date("Y-m-d", strtotime($data['birthday']));
        }
        if (isset($data['mother_tongue'])) {
            $arrData['mother_tongue'] = $data['mother_tongue'];
        }
        if (isset($data['religion'])) {
            $arrData['religion'] = $data['religion'];
        }
        if (isset($data['caste'])) {
            $arrData['caste'] = $data['caste'];
        }
        if (isset($data['mobileno'])) {
            $arrData['mobileno'] = $data['mobileno'];
        }
        if (isset($data['email'])) {
            $arrData['email'] = $data['email'];
        }
        if (isset($data['city'])) {
            $arrData['city'] = $data['city'];
        }
        if (isset($data['state'])) {
            $arrData['state'] = $data['state'];
        }
        if (isset($_FILES["user_photo"]) && !empty($_FILES["user_photo"]['name'])) {
            $arrData['photo'] = $this->uploadImage('student');
        }

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

        if (isset($data['school_name'])) {
            $arrData['previous_details'] = $previous_details;
        }

        // update student all information in the database
        $this->db->where('id', get_loggedin_user_id());
        $this->db->update('student', $arrData);
    }

    // moderator staff all information
    public function parentUpdate($data)
    {
        $update_data = array(
            'name' => $data['name'],
            'relation' => $data['relation'],
            'father_name' => $data['father_name'],
            'mother_name' => $data['mother_name'],
            'occupation' => $data['occupation'],
            'income' => $data['income'],
            'education' => $data['education'],
            'email' => $data['email'],
            'mobileno' => $data['mobileno'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'photo' => $this->uploadImage('parent'),
            'facebook_url' => $data['facebook'],
            'linkedin_url' => $data['linkedin'],
            'twitter_url' => $data['twitter'],
        );

        // UPDATE ALL INFORMATION IN THE DATABASE
        $this->db->where('id', get_loggedin_user_id());
        $this->db->update('parent', $update_data);
    }
}
