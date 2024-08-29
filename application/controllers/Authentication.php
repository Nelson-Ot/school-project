<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Authentication.php
 * @copyright : Reserved RamomCoder Team
 */

class Authentication extends Authentication_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->library('user_agent');
    }

    /* email is okey lets check the password now */
    public function index($url_alias = '')
    {
        if (is_loggedin()) {
            redirect(base_url('dashboard'));
        }

        if ($_POST) {
            $rules = array(
                array(
                    'field' => 'email',
                    'label' => "Email",
                    'rules' => 'trim|required',
                ),
                array(
                    'field' => 'password',
                    'label' => "Password",
                    'rules' => 'trim|required',
                ),
            );
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() !== false) {
                $email = $this->input->post('email');
                $password = $this->input->post('password');
                // username is okey lets check the password now
                $login_credential = $this->authentication_model->login_credential($email, $password);
                if ($login_credential) {
                    if ($login_credential->active) {
                        $getUser = $this->application_model->getUserNameByRoleID($login_credential->role, $login_credential->user_id);
                        $getConfig = $this->db->select('translation,session_id')->get_where('global_settings', array('id' => 1))->row();
                        $language = $getConfig->translation;
                        if($this->app_lib->isExistingAddon('saas')) {
                            if ($login_credential->role != 1) {
                                $schoolSettings = $this->db->select('id,translation')->where(array('id' => $getUser['branch_id'], 'status' => 1))->get('branch')->row();
                                if (empty($schoolSettings)) {
                                    set_alert('error', translate('inactive_school'));
                                    redirect(base_url('authentication'));
                                    exit();
                                }
                            }
                            if ($login_credential->role != 1) {
                                $language = $schoolSettings->translation;
                            }
                        }
                        // login user type
                        if ($login_credential->role == 6) {
                            $userType = 'parent';
                            // check parent login status
                            $getParentLoginStatus = $this->authentication_model->getParentLoginStatus($getUser['branch_id']);
                            if ($getParentLoginStatus == 0) {
                                set_alert('error', translate('parent_login_has_been_disabled'));
                                redirect(base_url('authentication'));
                                exit();
                            }
                        } elseif($login_credential->role == 7) {
                            // check student login status
                            $getStudentLoginStatus = $this->authentication_model->getStudentLoginStatus($getUser['branch_id']);
                            if ($getStudentLoginStatus == 0) {
                                set_alert('error', translate('student_login_has_been_disabled'));
                                redirect(base_url('authentication'));
                                exit();
                            }
                            $getEnrollID = $this->application_model->getEnrollID($getUser['id'], $getConfig->session_id);
                            $this->session->set_userdata('enrollID', $getEnrollID);
                            $userType = 'student';
                        } else {
                            $userType = 'staff';
                        }
                        $isRTL = $this->app_lib->getRTLStatus($language);
                        // get logger name
                        $sessionData = array(
                            'name' => $getUser['name'],
                            'logger_photo' => $getUser['photo'],
                            'loggedin_branch' => $getUser['branch_id'],
                            'loggedin_id' => $login_credential->id,
                            'loggedin_userid' => $login_credential->user_id,
                            'loggedin_role_id' => $login_credential->role,
                            'loggedin_type' => $userType,
                            'set_lang' =>  $language,
                            'is_rtl' => $isRTL,
                            'set_session_id' => $getConfig->session_id,
                            'loggedin' => true,
                        );
                        $this->session->set_userdata($sessionData);
                        $this->db->update('login_credential', array('last_login' => date('Y-m-d H:i:s')), array('id' => $login_credential->id));
                        // login Log details save in DB here
                        $this->loginLog($login_credential->user_id, $login_credential->role, $getUser['branch_id']);
                        // is logged in
                        if ($this->session->has_userdata('redirect_url')) {
                            redirect($this->session->userdata('redirect_url'));
                        } else {
                            redirect(base_url('dashboard'));
                        }
                    } else {
                        set_alert('error', translate('inactive_account'));
                        redirect(base_url('authentication'));
                    }
                } else {
                    set_alert('error', translate('username_password_incorrect'));
                    redirect(base_url('authentication'));
                }
            }
        }
        $this->data['branch_id'] = $this->authentication_model->urlaliasToBranch($url_alias);
        $schoolDeatls = $this->authentication_model->getSchoolDeatls($url_alias);
        if (!empty($schoolDeatls) && is_object($schoolDeatls)) {
            $this->data['global_config']['institute_name'] = $schoolDeatls->school_name;
            $this->data['global_config']['address'] = $schoolDeatls->address;
            $this->data['global_config']['facebook_url'] = $schoolDeatls->facebook_url;
            $this->data['global_config']['twitter_url'] = $schoolDeatls->twitter_url;
            $this->data['global_config']['linkedin_url'] = $schoolDeatls->linkedin_url;
            $this->data['global_config']['youtube_url'] = $schoolDeatls->youtube_url;
        }
        $this->load->view('authentication/login', $this->data);
    }

    // forgot password
    public function forgot($url_alias = '')
    {
        if (is_loggedin()) {
            redirect(base_url('dashboard'), 'refresh');
        }

        if ($_POST) {
            $config = array(
                array(
                    'field' => 'username',
                    'label' => 'Email',
                    'rules' => 'trim|required',
                ),
            );
            $this->form_validation->set_rules($config);
            if ($this->form_validation->run() !== false) {
                $username = $this->input->post('username');
                $res = $this->authentication_model->lose_password($username);
                if ($res == true) {
                    $this->session->set_flashdata('reset_res', 'true');
                    redirect(base_url('authentication/forgot'));
                } else {
                    $this->session->set_flashdata('reset_res', 'false');
                    redirect(base_url('authentication/forgot'));
                }
            }
        }
        $this->data['branch_id'] = $this->authentication_model->urlaliasToBranch($url_alias);
        $schoolDeatls = $this->authentication_model->getSchoolDeatls($url_alias);
        if (!empty($schoolDeatls) && is_object($schoolDeatls)) {
            $this->data['global_config']['institute_name'] = $schoolDeatls->school_name;
            $this->data['global_config']['address'] = $schoolDeatls->address;
            $this->data['global_config']['facebook_url'] = $schoolDeatls->facebook_url;
            $this->data['global_config']['twitter_url'] = $schoolDeatls->twitter_url;
            $this->data['global_config']['linkedin_url'] = $schoolDeatls->linkedin_url;
            $this->data['global_config']['youtube_url'] = $schoolDeatls->youtube_url;
        }
        $this->load->view('authentication/forgot', $this->data);
    }

    /* password reset */
    public function pwreset()
    {
        if (is_loggedin()) {
            redirect(base_url('dashboard'), 'refresh');
        }

        $key = $this->input->get('key');
        if (!empty($key)) {
            $query = $this->db->get_where('reset_password', array('key' => $key));
            if ($query->num_rows() > 0) {
                if ($this->input->post()) {
                    $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[4]|matches[c_password]');
                    $this->form_validation->set_rules('c_password', 'Confirm Password', 'trim|required|min_length[4]');
                    if ($this->form_validation->run() !== false) {
                        $password = $this->app_lib->pass_hashed($this->input->post('password'));
                        $this->db->where('id', $query->row()->login_credential_id);
                        $this->db->update('login_credential', array('password' => $password));
                        $this->db->where('login_credential_id', $query->row()->login_credential_id);
                        $this->db->delete('reset_password');
                        set_alert('success', 'Password Reset Successfully');
                        redirect(base_url('authentication'));
                    }
                }
                $this->load->view('authentication/pwreset', $this->data);
            } else {
                set_alert('error', 'Token Has Expired');
                redirect(base_url('authentication'));
            }
        } else {
            set_alert('error', 'Token Has Expired');
            redirect(base_url('authentication'));
        }
    }

    /* session logout */
    public function logout()
    {
        $webURL = base_url();
        if (!is_superadmin_loggedin()) {
            $cmsRow = $this->db->select('cms_active,url_alias')
            ->where('branch_id', get_loggedin_branch_id())
            ->get('front_cms_setting')->row_array();
            if (isset($cmsRow['cms_active']) && $cmsRow['cms_active'] == 1) {
                $webURL = base_url((isset($cmsRow['url_alias']) ? $cmsRow['url_alias'] : '') );
            }
        }

        $this->session->unset_userdata('name');
        $this->session->unset_userdata('logger_photo');
        $this->session->unset_userdata('loggedin_id');
        $this->session->unset_userdata('loggedin_userid');
        $this->session->unset_userdata('loggedin_type');
        $this->session->unset_userdata('set_lang');
        $this->session->unset_userdata('set_session_id');
        $this->session->unset_userdata('loggedin_branch');
        $this->session->unset_userdata('loggedin');
        $this->session->sess_destroy();
        redirect($webURL, 'refresh');
    }

    private function loginLog($userID = 0, $role = 0, $branchID = '')
    {
        if ($this->agent->is_browser()) {
            $browser = $this->agent->browser() . ' ' . $this->agent->version();
        } elseif ($this->agent->is_robot()) {
            $browser = $this->agent->robot();
        } elseif ($this->agent->is_mobile()) {
            $browser = $this->agent->mobile();
        } else {
            $browser = 'Unknown';
        }
        $ip_address = $this->input->ip_address();
        $data = array(
            'user_id' => $userID,
            'role' => $role,
            'ip' =>  ($ip_address == "::1" ? "127.0.0.1" : $ip_address),
            'platform' => $this->agent->platform(),
            'browser' => $browser,
            'timestamp'   => date('Y-m-d H:i:s'),
            'branch_id'   => $branchID,
        );
        $this->db->insert('login_log', $data);
    }
}
