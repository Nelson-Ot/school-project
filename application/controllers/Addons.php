<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.8
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Addons.php
 * @copyright : Reserved RamomCoder Team
 */

class Addons extends MY_Controller
{
    protected $extractPath = "";
    protected $initClassPath = "";
    private $tmp_dir;
    private $tmp_update_dir;
    private $purchase_code;
    private $latest_version;
    public function __construct()
    {
        parent::__construct();
        $this->load->model('addons_model');
        if (!is_superadmin_loggedin()) {
            access_denied();
        }
    }

    public function index()
    {
        $this->manage();
    }

    /* addons manager */
    public function manage()
    {
        if ($_POST) {
            $this->form_validation->set_rules('purchase_code', translate('purchase_code'), 'trim|required');
            $this->form_validation->set_rules('zip_file', 'Addon Zip File', 'callback_zipfileHandleUpload[zip_file]');
            if (isset($_FILES["zip_file"]) && empty($_FILES['zip_file']['name'])) {
                $this->form_validation->set_rules('zip_file', 'Addon Zip File', 'required');
            }
            if ($this->form_validation->run() == true) {
                $result = $this->fileUpload();

                if ($result['status'] == 'success') {
                    $array = array('status' => 'success', 'message' => $result['message']);
                } elseif ($result['status'] == 'fail') {
                    $array = array('status' => 'fail', 'error' => ['zip_file' => $result['message']]);
                } elseif ($result['status'] == 'purchase_code') {
                    $array = array('status' => 'fail', 'error' => ['purchase_code' => $result['message']]);
                }
                echo json_encode($array);
                exit();
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['validation_error'] = '';
        $this->data['addonList'] = $this->addons_model->getList();
        $this->data['title'] = translate('addon_manager');
        $this->data['sub_page'] = 'addons/index';
        $this->data['main_menu'] = 'addon';
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/dropify/css/dropify.min.css',
            ),
            'js' => array(
                'vendor/dropify/js/dropify.min.js',
            ),
        );
        $this->load->view('layout/index', $this->data);
    }

    public function zipfileHandleUpload($str, $fields)
    {
        $allowedExts = array_map('trim', array_map('strtolower', explode(',', 'zip')));
        if (isset($_FILES["$fields"]) && !empty($_FILES["$fields"]['name'])) {
            $file_size = $_FILES["$fields"]["size"];
            $file_name = $_FILES["$fields"]["name"];
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES["$fields"]['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('zipfileHandleUpload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
            } else {
                $this->form_validation->set_message('zipfileHandleUpload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }

    /* addons zip upload */
    private function fileUpload()
    {
        if ($_FILES["zip_file"]['name'] != "") {
            $dir = 'uploads/addons';
            if (!is_dir($dir)) {
                mkdir($dir, 0777, true);
                fopen($dir . '/index.html', 'w');
            }

            $purchaseCode = $this->input->post('purchase_code');
            $uploadPath = "uploads/addons/";
            $config = array();
            $config['upload_path'] = './uploads/addons/';
            $config['allowed_types'] = 'zip';
            $config['overwrite'] = TRUE;
            $config['encrypt_name'] = FALSE;
            $this->upload->initialize($config);
            if ($this->upload->do_upload("zip_file")) {
                $zipped_fileName = $this->upload->data('file_name');
                $random_dir = generate_encryption_key();
                $this->extractPath = FCPATH . "{$uploadPath}{$random_dir}";

                // unzip uploaded update file and remove zip file.
                $zip = new ZipArchive;
                $res = $zip->open($uploadPath . $zipped_fileName);
                if ($res === true) {
                    $fileName = trim($zip->getNameIndex(0), '/');
                    $res = $zip->extractTo($uploadPath . $random_dir);
                    $zip->close();
                    unlink($uploadPath . $zipped_fileName);
                    $configPath = "{$uploadPath}{$random_dir}/{$fileName}/config.json";
                    if (file_exists($configPath)) {
                        $config = file_get_contents($configPath);
                        if (!empty($config)) {
                            $json = json_decode($config);
                            if (!empty($json->name) && !empty($json->version) && !empty($json->unique_prefix) && !empty($json->items_code) && !empty($json->last_update) && !empty($json->system_version)) {

                                $current_version = $this->addons_model->get_current_db_version();
                                if ($json->system_version > $current_version) {
                                    $this->addons_model->directoryRecursive($this->extractPath);
                                    $requiredSystem = wordwrap($json->system_version, 1, '.', true);
                                    $current_version = wordwrap($current_version, 1, '.', true);
                                    return ['status' => 'fail', 'message' => "Minimum System required version {$requiredSystem}, your running version {$current_version}"];
                                }
                                if ($this->addons_model->addonInstalled($json->unique_prefix)) {
                                    $array = [];
                                    $array['product_name'] = $json->name;
                                    $array['version'] = $json->version;
                                    $array['system_version'] = $json->system_version;
                                    $array['unique_prefix'] = $json->unique_prefix;
                                    $array['purchase_code'] = $purchaseCode;
                                    $apiResult = $this->addons_model->call_CurlApi($array);
                                    if (isset($apiResult->status) && $apiResult->status) {
                                        if (!empty($apiResult->sql)) {
                                            $sqlContent = $apiResult->sql;
                                            $this->db->query('USE ' . $this->db->database . ';');
                                            foreach (explode(";\n", $sqlContent) as $sql) {
                                                $sql = trim($sql);
                                                if ($sql) {
                                                    $this->db->query($sql);
                                                }
                                            }

                                            // handel addon all directory and files
                                            $this->addons_model->copyDirectory("{$uploadPath}{$random_dir}/{$fileName}/", './');
                                            if (file_exists('./config.json')) {
                                                unlink('./config.json');
                                            }

                                            // initClass script execute
                                            if (!empty($json->initClass)) {
                                                $initClassPath = FCPATH . "{$uploadPath}{$random_dir}/{$fileName}/{$json->initClass}";
                                                if (file_exists($initClassPath) && is_readable($initClassPath) && include ($initClassPath)) {
                                                    $init = new InitClass();
                                                    $init->up();
                                                    unlink("./{$json->initClass}");
                                                }
                                            }

                                            //Insert addon details in DB
                                            $arrayAddon = array(
                                                'name' => $json->name,
                                                'prefix' => $json->unique_prefix,
                                                'version' => $json->version,
                                                'purchase_code' => $purchaseCode,
                                                'items_code' => $json->items_code,
                                                'created_at' => date('Y-m-d H:i:s'),
                                            );
                                            $this->db->insert('addon', $arrayAddon);

                                            $message = "<div class='alert alert-success mt-lg'><div>
                                                <h4>Congratulations your {$json->name} has been successfully Installed.</h4>
                                                <p>
                                                    This window will reload automatically in 5 seconds. You are strongly recommended to manually clear your browser cache.
                                                </p>
                                            </div></div>";
                                            $this->addons_model->directoryRecursive($this->extractPath);
                                            return ['status' => 'success', 'message' => $message];
                                        } else {
                                            $this->addons_model->directoryRecursive($this->extractPath);
                                            return ['status' => 'purchase_code', 'message' => 'SQL not found'];
                                        }
                                    } else {
                                        $this->addons_model->directoryRecursive($this->extractPath);
                                        return ['status' => 'purchase_code', 'message' => $apiResult->message];
                                    }
                                } else {
                                    // This addon already installed
                                    $this->addons_model->directoryRecursive($this->extractPath);
                                    return ['status' => 'fail', 'message' => "This addon already installed."];
                                }
                            } else {
                                // Invalid JSON
                                $this->addons_model->directoryRecursive($this->extractPath);
                                return ['status' => 'fail', 'message' => "Invalid config JSON."];
                            }
                        } else {
                            // JSON content is empty
                            $this->addons_model->directoryRecursive($this->extractPath);
                            return ['status' => 'fail', 'message' => "JSON content is empty."];
                        }
                    } else {
                        // Config file does not exist
                        $this->addons_model->directoryRecursive($this->extractPath);
                        return ['status' => 'fail', 'message' => "Config file does not exist."];
                    }
                } else {
                    unlink($uploadPath . $zipped_fileName);
                    return ['status' => 'fail', 'message' => "Zip extract fail."];
                }
            } else {
                return ['status' => 'fail', 'message' => $this->upload->display_errors('<p>', '</p>')];
            }
        }
    }

    public function update($items = '')
    {
        $addon = $this->addons_model->getAddonDetails($items);
        if (empty($addon)) {
            set_alert('error', translate('addon_not_found'));
            redirect(base_url('addons/manage'));
        }
        $this->data['status'] = 1;
        if (!extension_loaded('curl')) {
            $this->data['curl_extension'] = 0;
        } else {
            if (!empty($addon->purchase_code)) {
                $this->data['purchase_code'] = true;
                if ($this->addons_model->is_connected()) {
                    $this->data['internet'] = true;
                    $this->data['curl_extension'] = 1;
                    $get_update_info = $this->addons_model->get_update_info($addon);
                    if (strpos($get_update_info, 'Curl Error -') !== false) {
                        $this->data['update_errors'] = $get_update_info;
                        $this->data['latest_version'] = "0.0.0";
                        $this->data['support_expiry_date'] = "-/-/-";
                        $this->data['purchase_code'] = "-";
                        $this->data['block'] = 0;
                    } else { $get_update_info = json_decode($get_update_info);
                        $this->data['update_errors'] = "";
                        $this->data['get_update_info'] = $get_update_info;
                        $this->data['latest_version'] = $get_update_info->latest_version;
                        $this->data['support_expiry_date'] = $get_update_info->support_expiry_date;
                        $this->data['purchase_code'] = $get_update_info->purchase_code;
                        $this->data['block'] = $get_update_info->block;
                        $this->data['status'] = $get_update_info->status;
                    }
                } else {
                    $this->data['internet'] = false;
                }
            } else {
                $this->data['latest_version'] = "0";
                $this->data['purchase_code'] = false;
            }
        }
        if (!extension_loaded('zip')) {
            $this->data['zip_extension'] = 0;
        } else {
            $this->data['zip_extension'] = 1;
        }
        $this->data['addon'] = $addon;
        $this->data['current_version'] = $addon->version;
        $this->data['items'] = $addon->prefix;
        $this->data['title'] = translate('addon_update');
        $this->data['sub_page'] = 'addons/addon_update';
        $this->data['main_menu'] = 'addon';
        $this->load->view('layout/index', $this->data);
    }

    public function update_install()
    {
        $latest_version = $this->input->post('latest_version');
        $items = $this->input->post('items');
        $system_version = $this->addons_model->get_current_db_version();
        $addon = $this->addons_model->getAddonDetails($items);
        if (empty($addon)) {
            echo json_encode(['status' => 0, 'message' => translate('addon_not_found')]);
            exit();
        }

        $this->latest_version = $latest_version;
        $this->purchase_code = $addon->purchase_code;
        $tmp_dir = @ini_get('upload_tmp_dir');
        if (!$tmp_dir) {
            $tmp_dir = @sys_get_temp_dir();
            if (!$tmp_dir) {
                $tmp_dir = FCPATH . 'temp';
            }
        }

        $tmp_dir = rtrim($tmp_dir, '/') . '/';
        if (!is_writable($tmp_dir)) {
            $message = "Temporary directory not writable - <b>$tmp_dir</b><br />Please contact your hosting provider make this directory writable. The directory needs to be writable for the update files.";
            echo json_encode(['status' => 0, 'message' => $message]);
            exit();
        }

        $this->tmp_dir = $tmp_dir;
        $tmp_dir = $tmp_dir . 'v' . $latest_version . '/';
        $this->tmp_update_dir = $tmp_dir;

        if (!is_dir($tmp_dir)) {
            mkdir($tmp_dir);
            fopen($tmp_dir . 'index.html', 'w');
        }

        $zipFile = $tmp_dir . $latest_version . '.zip'; // Local Zip File Path
        $zipResource = fopen($zipFile, "w+");
        // Get The Zip File From Server
        $url = UPDATE_INSTALL_ADDON_URL;
        $curl_handle = curl_init();
        curl_setopt($curl_handle, CURLOPT_URL, $url);
        curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($curl_handle, CURLOPT_FAILONERROR, true);
        curl_setopt($curl_handle, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl_handle, CURLOPT_AUTOREFERER, true);
        curl_setopt($curl_handle, CURLOPT_BINARYTRANSFER, true);
        curl_setopt($curl_handle, CURLOPT_TIMEOUT, 50);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_handle, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curl_handle, CURLOPT_FILE, $zipResource);
        curl_setopt($curl_handle, CURLOPT_POSTFIELDS, array(
            'purchase_code' => $this->purchase_code,
            'item' => $addon->prefix,
            'current_version' => $addon->version,
            'system_version' => $system_version,
            'url' => base_url(), // please do not change the URL this is mandatory to setup the software
        ));
        $success = curl_exec($curl_handle);
        if (!$success) {
            fclose($zipResource);
            $this->cleanTmpFiles();
            $error = $this->getErrorByStatusCode(curl_getinfo($curl_handle, CURLINFO_HTTP_CODE));
            if ($error == '') {
                // Uknown error
                $error = curl_error($curl_handle);
            }
            echo json_encode(['status' => 0, 'message' => $error]);
            exit();
        }
        curl_close($curl_handle);

        $zip = new ZipArchive;
        if ($zip->open($zipFile) === true) {
            if (!$zip->extractTo('./')) {
                echo json_encode(['status' => 0, 'message' => 'Failed to extract downloaded zip file']);
                exit();
            } else {
                $initClassPath = FCPATH . "uploads/addons/{$latest_version}/initClass.php";
                if (file_exists($initClassPath) && is_readable($initClassPath) && include ($initClassPath)) {
                    $init = new InitClass();
                    $init->up();
                    @delete_dir(FCPATH . "uploads/addons/{$latest_version}");
                }
            }
            $zip->close();
        } else {
            echo json_encode(['status' => 0, 'message' => 'Failed to open downloaded zip file']);
            exit();
        }
        fclose($zipResource);
        $this->cleanTmpFiles();
        $message = '<div>
            <h4>Congratulations your Ramom software has been successfully updated ' . config_item('version') . '.</h4>
            <p>
                This window will reload automatically in 5 seconds. You are strongly recommended to manually clear your browser cache.
            </p>
        </div>';
        set_alert('success', translate('you_are_now_using_the_latest_version'));
        echo json_encode(['status' => '1', 'message' => $message]);
    }

    private function cleanTmpFiles()
    {
        if (is_dir($this->tmp_update_dir)) {
            if (@!delete_dir($this->tmp_update_dir)) {
                @rename($this->tmp_update_dir, $this->tmp_dir . 'delete_this_' . uniqid());
            }
        }
    }

    private function getErrorByStatusCode($statusCode)
    {
        $error = '';
        if ($statusCode == 505) {
            $mailBody = 'Hello. I tried to upgrade to the latest version but for some reason the upgrade failed. Please remove the key from the upgrade log so i can try again. My installation URL is: ' . base_url() . '. Regards.';
            $mailSubject = 'Purchase Key Removal Request - [' . $this->purchase_code . ']';
            $error = 'Purchase key already used to download upgrade files for version ' . wordwrap($this->latest_version, 1, '.', true) . '. Performing multiple auto updates to the latest version with one purchase key is not allowed. If you have multiple installations you must buy another license.<br /><br /> If you have staging/testing installation and auto upgrade is performed there, <b>you should perform manually upgrade</b> in your production area<br /><br /> <h4 class="bold">Upgrade failed?</h4> The error can be shown also if the update failed for some reason, but because the purchase key is already used to download the files, you won’t be able to re-download the files again.<br /><br />Click <a href="mailto:ramomcoder@yahoo.com?subject=' . $mailSubject . '&body=' . $mailBody . '"><b>here</b></a> to send an mail and get your purchase key removed from the upgrade log.';
        } elseif ($statusCode == 506) {
            $error = 'This is not a valid purchase code.';
        } elseif ($statusCode == 507) {
            $error = 'Purchase key empty.';
        } elseif ($statusCode == 508) {
            $error = 'This purchase code is blocked.';
        } elseif ($statusCode == 509) {
            $error = 'This purchase code is not valid for this item.';
        }
        return $error;
    }

    public function update_purchase_code()
    {
        if ($_POST) {
            $this->form_validation->set_rules('purchase_code', translate('purchase_code'), 'trim|required');
            if ($this->form_validation->run() == true) {
                $post = $this->input->post();
                $this->db->where('prefix', $post['items']);
                $this->db->update('addon', ['purchase_code' => trim($post['purchase_code'])]);
                set_alert('success', translate('information_has_been_updated_successfully'));
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit;
        }
    }
}
