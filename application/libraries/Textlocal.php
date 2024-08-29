<?php (! defined('BASEPATH')) and exit('No direct script access allowed');

class Textlocal
{

	private $apiKey;
	private $senderID;


	function __construct()
	{
        $ci = & get_instance();
        if (is_superadmin_loggedin()) {
            $branchID = $ci->input->post('branch_id');
        } else {
            $branchID = get_loggedin_branch_id();
        }
        $smscountry 	= $ci->db->get_where('sms_credential', array('sms_api_id' => 5, 'branch_id' => $branchID))->row_array();
		$this->senderID	= isset($smscountry['field_one']) ? $smscountry['field_one'] : '';
		$this->apiKey	= isset($smscountry['field_two']) ? $smscountry['field_two'] : '';
	}


	public function sendSms($numbers, $message)
	{
		// apiKey
		$apiKey = urlencode($this->apiKey);
		
		// Message details
		$sender = urlencode($this->senderID);
		$message = rawurlencode($message);
	 
		// Prepare data for POST request
		$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
	 
		// Send the POST request with cURL
		$ch = curl_init('https://api.textlocal.in/send/');
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($ch);
		curl_close($ch);
		// Process your response here
		$r = json_decode($response);
		if ($r->status == 'success') {
			return true;
		} else {
			return false;
		}
	}
}
