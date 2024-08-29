<?php
defined('BASEPATH') or exit('No direct script access allowed');

class School_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getBranchID()
    {
        if (is_superadmin_loggedin()) {
            return $this->input->get('branch_id', true);
        } else {
            return get_loggedin_branch_id();
        }
    }

    public function branchUpdate($data)
    {
        $calWithFine = isset($data['cal_with_fine']) ? 1 : 0;

        $arrayBranch = array(
            'name' => $data['branch_name'],
            'school_name' => $data['school_name'],
            'email' => $data['email'],
            'mobileno' => $data['mobileno'],
            'currency' => $data['currency'],
            'symbol' => $data['currency_symbol'],
            'city' => $data['city'],
            'state' => $data['state'],
            'address' => $data['address'],
            'teacher_restricted' => isset($data['teacher_restricted']) ? 1 : 0,
            'stu_generate' =>  isset($data['generate_student']) ? 1 : 0,
            'stu_username_prefix' => $data['stu_username_prefix'],
            'stu_default_password' => $data['stu_default_password'],
            'grd_generate' => isset($data['generate_guardian']) ? 1 : 0,
            'grd_username_prefix' => $data['grd_username_prefix'],
            'grd_default_password' => $data['grd_default_password'],
            'due_days' => $data['due_days'],
            'translation' => $data['translation'],
            'timezone' => $data['timezone'],
            'weekends' => (isset($data['weekends']) ? implode(',', $data['weekends']) : "") ,
            'reg_prefix_enable' => (isset($data['reg_prefix_enable']) ? 1 : 0) ,
            'reg_start_from' => $this->input->post('reg_start_from'),
            'institution_code' => $this->input->post('institution_code'),
            'reg_prefix_digit' => $this->input->post('reg_prefix_digit'),
            'due_with_fine' => $calWithFine,
            'offline_payments' => $data['offline_payments'],
            'unique_roll' => $data['unique_roll'],
            'currency_formats' => $data['currency_formats'],
            'symbol_position' => $data['symbol_position'],
            'show_own_question' => $data['show_own_question'],
        );
        $this->db->where('id', $data['brance_id']);
        $this->db->update('branch', $arrayBranch);
        if (!empty($data['translation'])) {
            if (!is_superadmin_loggedin()) {
                $isRTL = $this->app_lib->getRTLStatus($data['translation']);
                $this->session->set_userdata(['set_lang' => $data['translation']]);
                $this->session->set_userdata(['is_rtl' => $isRTL]);
            }
        }
    }

    function getSmsConfig()
    {
        if (is_superadmin_loggedin()) {
            $branch_id = $this->input->get('branch_id');
        } else {
            $branch_id = get_loggedin_branch_id();
        }

        $api = array();
        $result = $this->db->get('sms_api')->result();
        foreach ($result as $key => $value) {
            $api[$value->name] = $this->db->where(array('sms_api_id' => $value->id, 'branch_id' => $branch_id))->get('sms_credential')->row_array();
        }
        return $api;
    }

}
