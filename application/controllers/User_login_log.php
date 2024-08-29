<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : User_login_log.php
 * @copyright : Reserved RamomCoder Team
 */

class User_login_log extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_login_log_model');
    }

    public function index($role = 'staff')
    {
        if (!get_permission('user_login_log', 'is_view')) {
            access_denied();
        }
        $roleArr = array("staff", "student", "parent");
        if (!in_array($role, $roleArr)) {
            $role = 'staff';
        }
        $this->data['role'] = $role;
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['title'] = translate('user_login_log');
        $this->data['sub_page'] = 'user_login_log/index';
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }

    public function getLogListDT($role = 'staff')
    {
        if ($_POST) {
            $postData = $this->input->post();
            echo $this->user_login_log_model->getLogListDT($postData, $role);
        }
    }

    public function clear()
    {
        if (get_permission('user_login_log', 'is_delete')) {
            if (is_superadmin_loggedin()) {
                $this->db->truncate('login_log');
            } else {
                $this->db->where('branch_id', get_loggedin_branch_id());
                $this->db->delete('login_log'); 
            }
        }
    }
}
