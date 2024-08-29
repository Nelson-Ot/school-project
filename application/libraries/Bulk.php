<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bulk
{
    protected $username;
    protected $password;

    public function __construct()
    {
        $ci = &get_instance();
        if (is_superadmin_loggedin()) {
            $branchID = $ci->input->post('branch_id');
        } else {
            $branchID = get_loggedin_branch_id();
        }
        $bulksms = $ci->db->get_where('sms_credential', array('sms_api_id' => 4, 'branch_id' => $branchID))->row_array();
        $this->username = isset($bulksms['field_one']) ? $bulksms['field_one'] : '';
        $this->password = isset($bulksms['field_two']) ? $bulksms['field_two'] : '';
    }

    public function send($to, $message)
    {
        $username = $this->username;
        $password = $this->password;
        $messages = array(
            array('to' => $to, 'body' => $message),
        );

        $result = $this->send_message(json_encode($messages), 'https://api.bulksms.com/v1/messages?auto-unicode=true', $username, $password);

        if ($result['http_status'] != 201) {
            return false;
        } else {
            return true;
        }
    }

    public function send_message($post_body, $url, $username, $password)
    {
        $ch = curl_init();
        $headers = array(
            'Content-Type:application/json',
            'Authorization:Basic ' . base64_encode("$username:$password"),
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
        // Allow cUrl functions 20 seconds to execute
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);
        // Wait 10 seconds while trying to connect
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        $output = array();
        $output['server_response'] = curl_exec($ch);
        $curl_info = curl_getinfo($ch);
        $output['http_status'] = $curl_info['http_code'];
        $output['error'] = curl_error($ch);
        curl_close($ch);
        return $output;
    }
}
