<?php (!defined('BASEPATH')) and exit('No direct script access allowed');

class Custom_sms
{

    private $apiURL;

    public function __construct()
    {
        $ci = &get_instance();
        if (is_superadmin_loggedin()) {
            $branchID = $ci->input->post('branch_id');
        } else {
            $branchID = get_loggedin_branch_id();
        }
        $smscountry = $ci->db->get_where('sms_credential', array('sms_api_id' => 8, 'branch_id' => $branchID))->row_array();
        $this->apiURL = isset($smscountry['field_one']) ? $smscountry['field_one'] : '';
    }

    public function send($numbers, $message, $dlt_template_id = '')
    {
        $message = rawurlencode($message);
        $url = $this->apiURL;
        $url = str_replace('[app_number]', $numbers, $url);
        $url = str_replace('[app_message]', $message, $url);
        $url = str_replace('[dlt_template_id]', $dlt_template_id, $url);

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        $response = curl_exec($curl);
        curl_close($curl);
        return true;
    }
}
