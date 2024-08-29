<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Student_fields_model extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    // test save and update function
    public function getOnlineStatus($prefix, $branchID)
    {
        $this->db->select('if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('online_admission_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.system = 1 and oaf.branch_id = ' . $branchID, 'left');
        $this->db->where('student_fields.prefix', $prefix);
        $result = $this->db->get()->row_array();
        return $result;
    }

    public function getOnlineStatusArr($branchID)
    {
        $this->db->select('student_fields.id,student_fields.prefix,if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('online_admission_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.system = 1 and oaf.branch_id = ' . $branchID, 'left');
        $this->db->order_by('student_fields.id', 'asc');
        $result = $this->db->get()->result();
        return $result;
    }

    public function getStatus($prefix, $branchID)
    {
        $this->db->select('if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('student_admission_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.branch_id = ' . $branchID, 'left');
        $this->db->where('student_fields.prefix', $prefix);
        $result = $this->db->get()->row_array();
        return $result;
    }

    public function getStatusArr($branchID)
    {
        $this->db->select('student_fields.id,student_fields.prefix,if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('student_admission_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.branch_id = ' . $branchID, 'left');
         $this->db->order_by('student_fields.id', 'asc');
        $result = $this->db->get()->result();
        return $result;
    }

    public function getOnlineCustomFields($branchID)
    {
        $this->db->select('custom_field.*,if(oaf.status is null, custom_field.status, oaf.status) as fstatus,if(oaf.required is null, custom_field.required, oaf.required) as required');
        $this->db->from('custom_field');
        $this->db->join('online_admission_fields as oaf', 'oaf.fields_id = custom_field.id and oaf.system = 0 and oaf.branch_id = ' . $branchID, 'left');
        $this->db->where('custom_field.form_to', 'student');
        $this->db->where('custom_field.branch_id', $branchID);
        $this->db->order_by('custom_field.field_order','asc');
        $fields = $this->db->get()->result();
        return $fields;
    }


    public function getStatusProfile($prefix, $branchID)
    {
        $this->db->select('if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('student_profile_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.branch_id = ' . $branchID, 'left');
        $this->db->where('student_fields.prefix', $prefix);
        $result = $this->db->get()->row_array();
        return $result;
    }

    public function getStatusProfileArr($branchID)
    {
        $this->db->select('student_fields.id,student_fields.prefix,if(oaf.status is null, student_fields.default_status, oaf.status) as status,if(oaf.required is null, student_fields.default_required, oaf.required) as required');
        $this->db->from('student_fields');
        $this->db->join('student_profile_fields as oaf', 'oaf.fields_id = student_fields.id and oaf.branch_id = ' . $branchID, 'left');
         $this->db->order_by('student_fields.id', 'asc');
        $result = $this->db->get()->result();
        return $result;
    }

}
