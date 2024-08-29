<?php
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// return translation
function translate($word = '')
{
    $CI = &get_instance();
    if ($CI->session->has_userdata('set_lang')) {
        $set_lang = $CI->session->userdata('set_lang');
    } else {
        $set_lang = get_global_setting('translation');
    }

    if ($set_lang == '') {
        $set_lang = 'english';
    }

    $sql = "SELECT `english`,`" . $set_lang . "` FROM `languages` WHERE `word` = '$word'";
    $query = $CI->db->query($sql);
    if ($query->num_rows() > 0) {
        if (isset($query->row()->$set_lang) && $query->row()->$set_lang != '') {
            return $query->row()->$set_lang;
        } else {
            return $query->row()->english;
        }
    } else {
        $arrayData = array(
            'word' => $word,
            'english' => ucwords(str_replace('_', ' ', $word)),
        );
        $CI->db->insert('languages', $arrayData);
        return ucwords(str_replace('_', ' ', $word));
    }
}

function moduleIsEnabled($prefix)
{
    $ci = &get_instance();
    $role_id = $ci->session->userdata('loggedin_role_id');
    $branchID = $ci->session->userdata('loggedin_branch');
    if ($role_id == 1) {
        return 1;
    }
    $sql = "SELECT IF(`oaf`.`isEnabled` is NULL, 1, `oaf`.`isEnabled`) as `status` FROM `permission_modules` LEFT JOIN `modules_manage` as `oaf` ON `oaf`.`modules_id` = `permission_modules`.`id` AND `oaf`.`branch_id` = " . $ci->db->escape($branchID) . " WHERE `permission_modules`.`prefix` = " . $ci->db->escape($prefix);
    $result = $ci->db->query($sql)->row();
    if (empty($result)) {
        return 1;
    } else {
        return $result->status;
    }
}

function checkSaasLimit($prefix)
{
    $ci = &get_instance();
    $role_id = $ci->session->userdata('loggedin_role_id');
    $branchID = $ci->session->userdata('loggedin_branch');
    if ($role_id == 1) {
        return 1;
    }

    $ci = &get_instance();
    $sql = "SELECT `sb`.`expire_date`, `sb`.`school_id`, `student_limit`, `staff_limit`, `teacher_limit`, `parents_limit` FROM `branch` as `b` LEFT JOIN `saas_subscriptions` AS `sb` ON `sb`.`school_id` = `b`.`id` LEFT JOIN `saas_package` as `sp` ON `sp`.`id` = `sb`.`package_id` WHERE `sb`.`school_id` = " . $ci->db->escape($branchID);
    $row = $ci->db->query($sql)->row();
    if (empty($row)) {
        return 1;
    }

    if ($prefix == 'student') {
        $ci->db->where('branch_id', $branchID);
        $ci->db->group_by('student_id');
        $total_student = $ci->db->count_all_results('enroll');
        if ($total_student > $row->student_limit) {
            return 0;
        } else {
            return 1;
        }
    }

    if ($prefix == 'staff' || $prefix == 'teacher') {
        $ci->db->select('IFNULL(COUNT(staff.id), 0) as snumber');
        $ci->db->from('staff');
        $ci->db->join('login_credential', 'login_credential.user_id = staff.id', 'inner');
        if ($prefix == 'teacher') {
            $ci->db->where('login_credential.role', 3);
        } else {
            $ci->db->where_not_in('login_credential.role', array(1, 3, 6, 7));
        }
        $ci->db->where('staff.branch_id', $branchID);
        $total_staff = $ci->db->get()->row()->snumber;

        if ($prefix == 'teacher') {
            $limit = $row->teacher_limit;
        } else {
            $limit = $row->staff_limit;
        }
        if ($total_staff > $limit) {
            return 0;
        } else {
            return 1;
        }
    }

    if ($prefix == 'parent') {
        $ci->db->where('branch_id', $branchID);
        $total_parents = $ci->db->count_all_results('parent');
        if ($total_parents > $row->parents_limit) {
            return 0;
        } else {
            return 1;
        }
    }
}

function isEnabledSubscription($schoolID = '')
{
    $ci = &get_instance();
    $row = $ci->db->select('id')->where('school_id', $schoolID)->get('saas_subscriptions')->row();
    if (empty($row)) {
        return false;
    } else {
        return true;
    }
}

function get_permission($permission, $can = '')
{
    $ci = &get_instance();
    $role_id = $ci->session->userdata('loggedin_role_id');
    if ($role_id == 1) {
        return true;
    }
    $permissions = get_staff_permissions($role_id);
    foreach ($permissions as $permObject) {
        if ($permObject->permission_prefix == $permission && $permObject->$can == '1') {
            return true;
        }
    }
    return false;
}

function get_staff_permissions($id)
{
    $ci = &get_instance();
    $sql = "SELECT `staff_privileges`.*, `permission`.`id` as `permission_id`, `permission`.`prefix` as `permission_prefix` FROM `staff_privileges` JOIN `permission` ON `permission`.`id`=`staff_privileges`.`permission_id` WHERE `staff_privileges`.`role_id` = " . $ci->db->escape($id);
    $result = $ci->db->query($sql)->result();
    return $result;
}

function get_session_id()
{
    $CI = &get_instance();
    if ($CI->session->has_userdata('set_session_id')) {
        $session_id = $CI->session->userdata('set_session_id');
    } else {
        $session_id = get_global_setting('session_id');
    }
    return $session_id;
}

function is_secure($url)
{
    if ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) {
        $val = 'https://' . $url;
    } else {
        $val = 'http://' . $url;
    }
    return $val;
}

function get_global_setting($name = '')
{
    $CI = &get_instance();
    $name = trim($name);
    $CI->db->where('id', 1);
    $CI->db->select($name);
    $query = $CI->db->get('global_settings');

    if ($query->num_rows() > 0) {
        $row = $query->row();
        return $row->$name;
    }
}

// is superadmin logged in @return boolean
function is_superadmin_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 1) {
        return true;
    }
    return false;
}

// is admin logged in @return boolean
function is_admin_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 2) {
        return true;
    }
    return false;
}

// is teacher logged in @return boolean
function is_teacher_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 3) {
        return true;
    }
    return false;
}

// is accountant logged in @return boolean
function is_accountant_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 4) {
        return true;
    }
    return false;
}

// is librarian logged in @return boolean
function is_librarian_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 5) {
        return true;
    }
    return false;
}

// is parent logged in @return boolean
function is_parent_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 6) {
        return true;
    }
    return false;
}

// is parent logged in @return boolean
function is_student_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->userdata('loggedin_role_id') == 7) {
        return true;
    }
    return false;
}

// get logged in user id - login credential DB id
function get_loggedin_id()
{
    $ci = &get_instance();
    return $ci->session->userdata('loggedin_id');
}

// get staff db id
function get_loggedin_user_id()
{
    $ci = &get_instance();
    return $ci->session->userdata('loggedin_userid');
}

// get session loggedin
function is_loggedin()
{
    $CI = &get_instance();
    if ($CI->session->has_userdata('loggedin')) {
        return true;
    }
    return false;
}

// get loggedin role name
function loggedin_role_name()
{
    $CI = &get_instance();
    $roleID = $CI->session->userdata('loggedin_role_id');
    return $CI->db->select('name')->where('id', $roleID)->get('roles')->row()->name;
}

function loggedin_role_id()
{
    $ci = &get_instance();
    return $ci->session->userdata('loggedin_role_id');
}

// get logged in user type
function get_loggedin_user_type()
{
    $CI = &get_instance();
    return $CI->session->userdata('loggedin_type');
}

// get logged in user type
function get_loggedin_branch_id()
{
    $CI = &get_instance();
    return $CI->session->userdata('loggedin_branch');
}

// get parent selected active children Id
function get_activeChildren_id()
{
    $CI = &get_instance();
    return $CI->session->userdata('myChildren_id');
}

// get table name by type and id
function get_type_name_by_id($table, $type_id = '', $field = 'name')
{
    $CI = &get_instance();
    $get = $CI->db->select($field)->from($table)->where('id', $type_id)->limit(1)->get()->row_array();
    return $get[$field];
}

// set session alert / flashdata
function set_alert($type, $message)
{
    $CI = &get_instance();
    $CI->session->set_flashdata('alert-message-' . $type, $message);
}

// generate md5 hash
function app_generate_hash()
{
    return md5(rand() . microtime() . time() . uniqid());
}

// generate encryption key
function generate_encryption_key()
{
    $CI = &get_instance();
    // In case accessed from my_functions_helper.php
    $CI->load->library('encryption');
    $key = bin2hex($CI->encryption->create_key(16));
    return $key;
}

// generate get image url
function get_image_url($role = '', $file_name = '')
{
    if ($file_name == 'defualt.png' || empty($file_name)) {
        $image_url = base_url('uploads/app_image/defualt.png');
    } else {
        if (file_exists('uploads/images/' . $role . '/' . $file_name)) {
            $image_url = base_url('uploads/images/' . $role . '/' . $file_name);
        } else {
            $image_url = base_url('uploads/app_image/defualt.png');
        }
    }
    return $image_url;
}

// get date format config
function _d($date)
{
    if ($date == '' || is_null($date) || $date == '0000-00-00') {
        return '';
    }
    $formats = 'Y-m-d';
    $get_format = get_global_setting('date_format');
    if ($get_format != '') {
        $formats = $get_format;
    }
    return date($formats, strtotime($date));
}

// delete url
function btn_delete($uri)
{
    return "<button class='btn btn-danger icon btn-circle' onclick=confirm_modal('" . base_url($uri) . "') ><i class='fas fa-trash-alt'></i></button>";
}

// delete url
function csrf_jquery_token()
{
    $csrf = [get_instance()->security->get_csrf_token_name() => get_instance()->security->get_csrf_hash()];
    return $csrf;
}

function check_hash_restrictions($table, $id, $hash)
{
    $CI = &get_instance();
    if (!$table || !$id || !$hash) {
        show_404();
    }

    $query = $CI->db->select('hash')->from($table)->where('id', $id)->get();
    if ($query->num_rows() > 0) {
        $get_hash = $query->row()->hash;
    } else {
        $get_hash = '';
    }
    if (empty($hash) || ($get_hash != $hash)) {
        show_404();
    }
}

function get_nicetime($date)
{
    $get_format = get_global_setting('date_format');
    if (empty($date)) {
        return "Unknown";
    }
    // Current time as MySQL DATETIME value
    $csqltime = date('Y-m-d H:i:s');
    // Current time as Unix timestamp
    $ptime = strtotime($date);
    $ctime = strtotime($csqltime);

    //Now calc the difference between the two
    $timeDiff = floor(abs($ctime - $ptime) / 60);

    //Now we need find out whether or not the time difference needs to be in
    //minutes, hours, or days
    if ($timeDiff < 2) {
        $timeDiff = "Just now";
    } elseif ($timeDiff > 2 && $timeDiff < 60) {
        $timeDiff = floor(abs($timeDiff)) . " minutes ago";
    } elseif ($timeDiff > 60 && $timeDiff < 120) {
        $timeDiff = floor(abs($timeDiff / 60)) . " hour ago";
    } elseif ($timeDiff < 1440) {
        $timeDiff = floor(abs($timeDiff / 60)) . " hours ago";
    } elseif ($timeDiff > 1440 && $timeDiff < 2880) {
        $timeDiff = floor(abs($timeDiff / 1440)) . " day ago";
    } elseif ($timeDiff > 2880) {
        $timeDiff = date($get_format, $ptime);
    }
    return $timeDiff;
}

function bytesToSize($path, $filesize = '')
{
    if (!is_numeric($filesize)) {
        $bytes = sprintf('%u', filesize($path));
    } else {
        $bytes = $filesize;
    }
    if ($bytes > 0) {
        $unit = intval(log($bytes, 1024));
        $units = [
            'B',
            'KB',
            'MB',
            'GB',
        ];
        if (array_key_exists($unit, $units) === true) {
            return sprintf('%d %s', $bytes / pow(1024, $unit), $units[$unit]);
        }
    }
    return $bytes;
}

function array_to_object($array)
{
    if (!is_array($array) && !is_object($array)) {
        return new stdClass();
    }
    return json_decode(json_encode((object) $array));
}

function access_denied()
{
    set_alert('error', translate('access_denied'));
    redirect(site_url('dashboard'));
}

function ajax_access_denied()
{
    set_alert('error', translate('access_denied'));
    $array = array('status' => 'access_denied');
    echo json_encode($array);
    exit();
}

function slugify($text)
{
    // replace non letter or digits by -
    $text = preg_replace('~[^\pL\d]+~u', '_', $text);

    // transliterate
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);

    // remove unwanted characters
    $text = preg_replace('~[^-\w]+~', '', $text);

    // trim
    $text = trim($text, '_');

    // remove duplicated - symbols
    $text = preg_replace('~-+~', '_', $text);

    // lowercase
    $text = strtolower($text);
    return $text;
}

// website menu list
function web_menu_list($publish = '', $default = '', $branchID = '')
{
    $CI = &get_instance();
    if (empty($branchID)) {
        $branchID = $CI->home_model->getDefaultBranch();
    }
    $CI->db->select('*,if(front_cms_menu_visible.name is null, front_cms_menu.title, front_cms_menu_visible.name) as title, front_cms_menu_visible.invisible');
    $CI->db->from('front_cms_menu');
    $CI->db->join('front_cms_menu_visible', 'front_cms_menu_visible.menu_id = front_cms_menu.id and front_cms_menu_visible.branch_id = ' . $branchID, 'left');
    if ($publish != '') {
        $CI->db->where('front_cms_menu.publish', $publish);
    }
    if ($default != '') {
        $CI->db->where('front_cms_menu.system', $default);
    }
    $CI->db->order_by('front_cms_menu.ordering', 'asc');
    $CI->db->where_in('front_cms_menu.branch_id', array(0, $branchID));
    $result = $CI->db->get()->result_array();
    return $result;
}

function get_request_url()
{
    $url = $_SERVER['QUERY_STRING'];
    $url = (!empty($url) ? '?' . $url : '');
    return $url;
}

function delete_dir($dirPath)
{
    if (!is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            delete_dir($file);
        } else {
            unlink($file);
        }
    }
    if (rmdir($dirPath)) {
        return true;
    }
    return false;
}

function currencyFormat($amount = 0)
{
    $CI = &get_instance();
    $array              = $CI->data['global_config'];
    $currency           = $array['currency'];
    $currency_symbol    = $array['currency_symbol'];
    $currency_formats   = $array['currency_formats'];
    $symbol_position    = $array['symbol_position'];

    $amount = empty($amount) ? 0 : $amount;
    $value = $amount;
    if ($currency_formats == 1) {
        $value = number_format($amount, 2, '.', '');
    } elseif ($currency_formats == 2) {
        $value = moneyFormatIndia($amount);
    } elseif ($currency_formats == 3) {
        $value = number_format($amount, 3, '.', ',');
    } elseif ($currency_formats == 4) {
        $value = number_format($amount, 2, ',', '.');
    } elseif ($currency_formats == 5) {
        $value = number_format($amount, 2, '.', ',');
    } elseif ($currency_formats == 6) {
        $value = number_format($amount, 2, ',', ' ');
    } elseif ($currency_formats == 7) {
        $value = number_format($amount, 2, '.', ' ');
    } elseif ($currency_formats == 8) {
        $value = $amount;
    }

    if ($symbol_position == 1) {
        $value = $currency_symbol . $value; 
    } elseif ($symbol_position == 2) {
        $value = $value . $currency_symbol;
    } elseif ($symbol_position == 3) {
        $value = $currency_symbol . " " . $value;
    } elseif ($symbol_position == 4) {
        $value = $value . " " . $currency_symbol;
    } elseif ($symbol_position == 5) {
        $value = $currency . " " . $value;
    } elseif ($symbol_position == 6) {
        $value = $value . " " . $currency;
    }
    return $value;
}

function moneyFormatIndia($num)
{
    $explrestunits = "" ;
    $num = preg_replace('/,+/', '', $num);
    $words = explode(".", $num);
    $des = "00";
    if(count($words)<=2){
        $num=$words[0];
        if(count($words)>=2){$des=$words[1];}
        if(strlen($des)<2){$des="$des";}else{$des=substr($des,0,2);}
    }
    if(strlen($num)>3){
        $lastthree = substr($num, strlen($num)-3, strlen($num));
        $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits
        $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping.
        $expunit = str_split($restunits, 2);
        for($i=0; $i<sizeof($expunit); $i++){
            // creates each of the 2's group and adds a comma to the end
            if($i==0)
            {
                $explrestunits .= (int)$expunit[$i].","; // if is first value , convert into integer
            }else{
                $explrestunits .= $expunit[$i].",";
            }
        }
        $thecash = $explrestunits.$lastthree;
    } else {
        $thecash = $num;
    }
    return "$thecash.$des"; // writes the final format where $currency is the currency symbol.
}

function getEnrollToStudentID($enroll_id = '')
{
    $CI = &get_instance();
    $get = $CI->db->select('student_id')->from('enroll')->where('id', $enroll_id)->limit(1)->get()->row()->student_id;
    return $get;
}
