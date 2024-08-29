<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Addons_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    public function getList()
    {
        $this->db->select('*');
        $r = $this->db->get('addon')->result();
        return $r;
    }

    public function addonInstalled($prefix = '')
    {
        $this->db->select('count(id) as cid');
        $this->db->where('prefix', $prefix);
        $r = $this->db->get('addon')->row()->cid;
        if ($r == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function copyDirectory($source, $destination)
    {
        if (is_dir($source)) {
            @mkdir($destination, 0777, true);
            $directory = dir($source);
            while (false !== ($readdirectory = $directory->read())) {
                if ($readdirectory == '.' || $readdirectory == '..') {
                    continue;
                }
                $PathDir = $source . '/' . $readdirectory;
                if (is_dir($PathDir)) {
                    $this->copyDirectory($PathDir, $destination . '/' . $readdirectory);
                    continue;
                }
                copy($PathDir, $destination . '/' . $readdirectory);
            }
            $directory->close();
        } else {
            copy($source, $destination);
        }
    }

    public function directoryRecursive($dir)
    {
        if (!file_exists($dir)) {
            return true;
        }

        if (!is_dir($dir)) {
            return unlink($dir);
        }

        foreach (scandir($dir) as $item) {
            if ($item == '.' || $item == '..') {
                continue;
            }

            if (!$this->directoryRecursive($dir . DIRECTORY_SEPARATOR . $item)) {
                return false;
            }
        }
        return rmdir($dir);
    }

    public function call_CurlApi($post_data)
    {
        $url = $this->getVerifyURL();
        if (!$url) {
            return (object) [
                'status' => false,
                'message' => 'No internet connection.',
            ];
        }
        $data = array(
            'domain' => $_SERVER['HTTP_HOST'],
        );
        $mData = array_merge($data, $post_data);
        $data_string = json_encode($mData);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string),
        ]
        );
        $result = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            return (object) [
                'status' => false,
                'message' => "cURL Error : " . $err,
            ];
        } else {
            if (!is_object($result)) {
                $jsonData = json_decode($result);
                return $jsonData;
            } else {
                return (object) [
                    'status' => false,
                    'message' => 'Unknown Error',
                ];
            }
        }
    }

    public function get_update_info($purchase_code)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, UPDATE_ADDON_INFO_URL);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            'item' => $purchase_code->prefix,
            'current_version' => $purchase_code->version,
            'system_version' => $this->get_current_db_version(),
            'purchase_code' => $purchase_code->purchase_code,
        ]);
        $result = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = '';
        if (!$ch || $httpcode != 200) {
            $error = 'Curl Error - Contact your hosting provider with the following error as reference: Error: "' . curl_error($ch) . '" - Code: ' . curl_errno($ch);
        }
        curl_close($ch);
        if ($error != '') {
            return $error;
        }
        return $result;
    }

    public function getVerifyURL()
    {
        if ($this->is_connected()) {
            return 'https://ramomcoder.com/purchase/api/verify_addon';
        }
        return false;
    }

    public function is_connected($host = 'www.google.com')
    {
        $connected = @fsockopen($host, 80);
        //website, port  (try 80 or 443)
        if ($connected) {
            $is_conn = true; //action when connected
            fclose($connected);
        } else {
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

    public function get_current_db_version()
    {
        $this->db->limit(1);
        return $this->db->get('migrations')->row()->version;
    }

    public function getAddonDetails($prefix = '')
    {
        $this->db->limit(1);
        return $this->db->where('prefix', $prefix)->get('addon')->row();
    }
}
