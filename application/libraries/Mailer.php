<?php
defined('BASEPATH') or exit('No direct script access allowed');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
require_once(APPPATH . 'third_party/phpmailer/autoload.php');

class Mailer
{
    private $CI;
    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function send($data = array(), $err = false)
    {
        $getConfig = $this->CI->db->get_where('email_config', array('branch_id' => $data['branch_id']))->row();
		$school_name = get_global_setting('institute_name');
        $mail = new PHPMailer();
        if ($getConfig->protocol == 'smtp') {
            $smtp_encryption = $getConfig->smtp_encryption;
            $mail->isSMTP();
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->Host = trim($getConfig->smtp_host);
            $mail->Port = trim($getConfig->smtp_port);
            if (!empty($getConfig->smtp_encryption)) {
                $mail->SMTPSecure =  $getConfig->smtp_encryption;
            }
            $mail->SMTPAuth = $getConfig->smtp_auth;
            $mail->Username = trim($getConfig->smtp_user);
            $mail->Password = trim($getConfig->smtp_pass);
        } else {
            $mail->isSendmail();
        }

        if (!empty($data['file'])) {
           $mail->addStringAttachment($data['file'], $data['file_name']);
        }

        $mail->setFrom($getConfig->email, $school_name);
        $mail->addReplyTo($getConfig->email, $school_name);
        $mail->addAddress($data['recipient']);
        $mail->Subject = $data['subject'];
        $mail->AltBody = $data['message'];
        $mail->Body = $data['message'];
        if ($mail->send()) {
            return true;
        } else {
            if ($err == false) {
                return false;
            } else {
                return $mail->ErrorInfo;
            }
            
        }
    }
}
