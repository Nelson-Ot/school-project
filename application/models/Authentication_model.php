<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Authentication_model extends MY_Model
{

    // checking login credential
    public function login_credential($username, $password)
    {
        $this->db->select('*');
        $this->db->from('login_credential');
        $this->db->where('username', $username);
        $this->db->limit(1);
        $query = $this->db->get();
        if ($query->num_rows() == 1) {
            $verify_password = $this->app_lib->verify_password($password, $query->row()->password);
            if ($verify_password) {
                return $query->row();
            }
        }
        return false;
    }

    // password forgotten
    public function lose_password($username)
    {
        if (!empty($username)) {
            $this->db->select('*');
            $this->db->from('login_credential');
            $this->db->where('username', $username);
            $this->db->limit(1);
            $query = $this->db->get();

            if ($query->num_rows() > 0) {
                $login_credential = $query->row();
                $getUser = $this->application_model->getUserNameByRoleID($login_credential->role, $login_credential->user_id);
                $key = hash('sha512', $login_credential->role . $login_credential->username . app_generate_hash());
                $query = $this->db->get_where('reset_password', array('login_credential_id' => $login_credential->id));
                if ($query->num_rows() > 0) {
                    $this->db->where('login_credential_id', $login_credential->id);
                    $this->db->delete('reset_password');
                }
                $arrayReset = array(
                    'key' => $key,
                    'login_credential_id' => $login_credential->id,
                    'username' => $login_credential->username,
                );
                $this->db->insert('reset_password', $arrayReset);
                // send email for forgot password
                $this->load->model('email_model');
                $arrayData = array(
                    'role' => $login_credential->role,
                    'branch_id' => $getUser['branch_id'],
                    'username' => $login_credential->username,
                    'name' => $getUser['name'],
                    'reset_url' => base_url('authentication/pwreset?key=' . $key),
                    'email' => $getUser['email'],
                );
                $this->email_model->sentForgotPassword($arrayData);
                return true;
            }
        }
        return false;
    }

    public function urlaliasToBranch($url_alias)
    {
        $saasExisting = $this->app_lib->isExistingAddon('saas');
        if ($saasExisting && $this->db->table_exists("custom_domain")) {
            $getDomain = $this->getCurrentDomain();
            if (!empty($getDomain)) {
                return $getDomain->school_id;
            }
        }

        $get = $this->db->select('branch_id')->where('url_alias', $url_alias)->get('front_cms_setting')->row_array();
        if (empty($url_alias) || empty($get)) {
            return null;
        } else {
            return $get['branch_id'];
        }
    }

    public function getSegment($id = '')
    {
        $segment = $this->uri->segment($id);
        if (empty($segment)) {
            return '';
        } else {
            return $segment . '/';
        }
    }

    public function getCurrentDomain()
    {
        $url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
        $url = rtrim($url, '/');
        $domain = parse_url($url, PHP_URL_HOST);
        if (substr($domain, 0, 4) == 'www.') {
            $domain = str_replace('www.', '', $domain);
        }
        $getDomain = $this->db->select('school_id')->get_where('custom_domain', array('status' => 1, 'url' => $domain))->row();
        return $getDomain;
    }

    public function getSchoolDeatls($url_alias = '')
    {
        if (!empty($url_alias)) {
            $this->db->select('fs.facebook_url,fs.twitter_url,fs.linkedin_url,fs.youtube_url,branch.address,branch.school_name');
            $this->db->from('front_cms_setting as fs');
            $this->db->join('branch', 'branch.id = fs.branch_id', 'left');
            $this->db->where('fs.url_alias', $url_alias);
            $get = $this->db->get()->row();
            if (empty($get)) {
                return '';
            } else {
                return $get;
            }
        } else {
            return '';
        }
    }

    public function getStudentLoginStatus($id = '')
    {
        $get = $this->db->select('IFNULL(student_login, 1) as login')->where('id', $id)->get('branch')->row()->login;
        return $get;
    }
    
    public function getParentLoginStatus($id = '')
    {
        $get = $this->db->select('IFNULL(parent_login, 1) as login')->where('id', $id)->get('branch')->row()->login;
        return $get;
    }
}
