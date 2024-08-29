<?php

if (!defined('BASEPATH')) exit('No direct script access allowed');

class Bulksmsbd
{
    protected $sender_id;
    protected $api_key;

    public function __construct()
    {
        $ci = &get_instance();
        if (is_superadmin_loggedin()) {
            $branchID = $ci->input->post('branch_id');
        } else {
            $branchID = get_loggedin_branch_id();
        }
        $bulksms = $ci->db->get_where('sms_credential', array('sms_api_id' => 7, 'branch_id' => $branchID))->row_array();
        $this->sender_id = isset($bulksms['field_one']) ? $bulksms['field_one'] : '';
        $this->api_key = isset($bulksms['field_two']) ? $bulksms['field_two'] : '';
    }

    public function send($to, $message)
    {
        $username = $this->sender_id;
        $password = $this->api_key;
		$url = "https://bulksmsbd.net/api/smsapi";
		$api_key = $this->api_key;
		$senderid = $this->sender_id;
		$number = $to;
		$message = $message;
	 
		$data = [
			"api_key" => $api_key,
			"senderid" => $senderid,
			"number" => $number,
			"message" => $message
		];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		$response = curl_exec($ch);
		curl_close($ch);
		return true;
    }
}
