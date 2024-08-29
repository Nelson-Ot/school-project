<?php

class Module_model extends MY_Model {

    public function __construct() {
        parent::__construct();
    }

    public function getStatusArr($branchID)
    {
        $this->db->select('permission_modules.id,permission_modules.prefix,if(oaf.isEnabled is null, 1, oaf.isEnabled) as status');
        $this->db->from('permission_modules');
        $this->db->join('modules_manage as oaf', 'oaf.modules_id = permission_modules.id and oaf.branch_id = ' . $branchID, 'left');
        $this->db->where('permission_modules.in_module', 1);
        $this->db->group_by('permission_modules.id');
        $this->db->order_by('permission_modules.prefix', 'asc');
        $result = $this->db->get()->result();
        return $result;
    }
}
?>