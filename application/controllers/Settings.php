<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.5
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Settings.php
 * @copyright : Reserved RamomCoder Team
 */

class Settings extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        redirect(base_url(), 'refresh');
    }

    /* global settings controller */
    public function universal()
    {
        if (!get_permission('global_settings', 'is_view')) {
            access_denied();
        }

        if ($_POST) {
            if (!get_permission('global_settings', 'is_edit')) {
                
            }
            if ($this->app_lib->licenceVerify() == false) {
                set_alert('error', translate('invalid_license'));
                redirect(site_url('dashboard'));
            }
        }

        $config = array();
        if ($this->input->post('submit') == 'setting') {
            foreach ($this->input->post() as $input => $value) {
                if ($input == 'submit') {
                    continue;
                }
                $config[$input] = $value;
            }
            if (empty($config['reg_prefix'])) {
                $config['reg_prefix'] = false;
            }
            $this->db->where('id', 1);
            $this->db->update('global_settings', $config);

            $isRTL = $this->app_lib->getRTLStatus($config['translation']);
            $this->session->set_userdata(['set_lang' => $config['translation'], 'is_rtl' => $isRTL]);
            
            set_alert('success', translate('the_configuration_has_been_updated'));
            redirect(current_url());
        }

        if ($this->input->post('submit') == 'theme') {
            foreach ($this->input->post() as $input => $value) {
                if ($input == 'submit') {
                    continue;
                }
                $config[$input] = $value;
            }
            $this->db->where('id', 1);
            $this->db->update('theme_settings', $config);
            set_alert('success', translate('the_configuration_has_been_updated'));
            $this->session->set_flashdata('active', 2);
            redirect(current_url());
        }

        if ($this->input->post('submit') == 'logo') {
            move_uploaded_file($_FILES['logo_file']['tmp_name'], 'uploads/app_image/logo.png');
            move_uploaded_file($_FILES['text_logo']['tmp_name'], 'uploads/app_image/logo-small.png');
            move_uploaded_file($_FILES['print_file']['tmp_name'], 'uploads/app_image/printing-logo.png');
            move_uploaded_file($_FILES['report_card']['tmp_name'], 'uploads/app_image/report-card-logo.png');

            move_uploaded_file($_FILES['slider_1']['tmp_name'], 'uploads/login_image/slider_1.jpg');
            move_uploaded_file($_FILES['slider_2']['tmp_name'], 'uploads/login_image/slider_2.jpg');
            move_uploaded_file($_FILES['slider_3']['tmp_name'], 'uploads/login_image/slider_3.jpg');

            move_uploaded_file($_FILES['sidebox_1']['tmp_name'], 'assets/login_page/image/sidebox.jpg');
            move_uploaded_file($_FILES['profile_bg']['tmp_name'], 'assets/images/profile_bg.jpg');

            set_alert('success', translate('the_configuration_has_been_updated'));
            $this->session->set_flashdata('active', 3);
            redirect(current_url());
        }


        $this->data['title'] = translate('global_settings');
        $this->data['sub_page'] = 'settings/universal';
        $this->data['main_menu'] = 'settings';
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

    public function file_types_save() {
        if ($_POST) {
            if (!get_permission('global_settings', 'is_view')) {
                ajax_access_denied();
            }
            $this->form_validation->set_rules('image_extension', translate('image_extension'), 'trim|required');
            $this->form_validation->set_rules('image_size', translate('image_size'), 'trim|required|numeric');
            $this->form_validation->set_rules('file_extension', translate('file_extension'), 'trim|required');
            $this->form_validation->set_rules('file_size', translate('file_size'), 'trim|required|numeric');
            if ($this->form_validation->run() == true) {
                $arrayType = array(
                    'image_extension' => $this->input->post('image_extension'), 
                    'image_size' => $this->input->post('image_size'), 
                    'file_extension' => $this->input->post('file_extension'), 
                    'file_size' => $this->input->post('file_size'), 
                );

                $this->db->where('id', 1);
                $this->db->update('global_settings', $arrayType);
                $array = array('status' => 'success', 'message' => translate('the_configuration_has_been_updated'));
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
        }
    }

    public function unique_branchname($name)
    {
        $this->db->where_not_in('id', get_loggedin_branch_id());
        $this->db->where('name', $name);
        $name = $this->db->get('branch')->num_rows();
        if ($name == 0) {
            return true;
        } else {
            $this->form_validation->set_message("unique_branchname", translate('already_taken'));
            return false;
        }
    }

    public function payment()
    {
        if (!get_permission('payment_settings', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        $this->data['branch_id'] = $branchID;
        $this->data['config'] = $this->get_payment_config();
        $this->data['sub_page'] = 'settings/payment_gateway';
        $this->data['main_menu'] = 'settings';
        $this->data['title'] = translate('payment_control');
        $this->load->view('layout/index', $this->data);
    }

    public function paypal_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('paypal_username', 'Paypal Username', 'trim|required');
        $this->form_validation->set_rules('paypal_password', 'Paypal Password', 'trim|required');
        $this->form_validation->set_rules('paypal_signature', 'Paypal Signature', 'trim|required');
        $this->form_validation->set_rules('paypal_email', 'Paypal Email', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $paypal_sandbox = isset($_POST['paypal_sandbox']) ? 1 : 2;
            $arrayPaypal = array(
                'paypal_username' => $this->input->post('paypal_username'),
                'paypal_password' => $this->input->post('paypal_password'),
                'paypal_signature' => $this->input->post('paypal_signature'),
                'paypal_email' => $this->input->post('paypal_email'),
                'paypal_sandbox' => $paypal_sandbox,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaypal['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaypal);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaypal);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function stripe_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('stripe_publishiable_key', 'Stripe Publishiable Key', 'trim|required');
        $this->form_validation->set_rules('stripe_secret', 'Stripe Secret Key', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $stripe_demo = isset($_POST['stripe_demo']) ? 1 : 2;
            $arrayPaypal = array(
                'stripe_publishiable' => $this->input->post('stripe_publishiable_key'),
                'stripe_secret' => $this->input->post('stripe_secret'),
                'stripe_demo' => $stripe_demo,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaypal['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaypal);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaypal);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function payumoney_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('payumoney_key', 'Payumoney Key', 'trim|required');
        $this->form_validation->set_rules('payumoney_salt', 'Payumoney Salt', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $payumoney_demo = isset($_POST['payumoney_demo']) ? 1 : 2;
            $arrayPayumoney = array(
                'payumoney_key' => $this->input->post('payumoney_key'),
                'payumoney_salt' => $this->input->post('payumoney_salt'),
                'payumoney_demo' => $payumoney_demo,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPayumoney['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPayumoney);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPayumoney);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function paystack_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('paystack_secret_key', 'Paystack API Key', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayPaystack = array(
                'paystack_secret_key' => $this->input->post('paystack_secret_key'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayMollie['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaystack);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaystack);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function razorpay_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('razorpay_key_id', 'Key Id', 'trim|required');
        $this->form_validation->set_rules('razorpay_key_secret', 'Key Secret', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $razorpay_demo = isset($_POST['razorpay_demo']) ? 1 : 2;
            $arrayRazorpay = array(
                'razorpay_key_id' => $this->input->post('razorpay_key_id'),
                'razorpay_key_secret' => $this->input->post('razorpay_key_secret'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayRazorpay['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayRazorpay);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayRazorpay);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function payment_active()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $paypal_status = isset($_POST['paypal_status']) ? 1 : 0;
        $stripe_status = isset($_POST['stripe_status']) ? 1 : 0;
        $payumoney_status = isset($_POST['payumoney_status']) ? 1 : 0;
        $paystack_status = isset($_POST['paystack_status']) ? 1 : 0;
        $razorpay_status = isset($_POST['razorpay_status']) ? 1 : 0;
        $midtrans_status = isset($_POST['midtrans_status']) ? 1 : 0;
        $sslcommerz_status = isset($_POST['sslcommerz_status']) ? 1 : 0;
        $jazzcash_status = isset($_POST['jazzcash_status']) ? 1 : 0;
        $flutterwave_status = isset($_POST['flutterwave']) ? 1 : 0;
        $paytm_status = isset($_POST['paytm_status']) ? 1 : 0;
        $toyyibpay_status = isset($_POST['toyyibpay_status']) ? 1 : 0;
        $payhere_status = isset($_POST['payhere_status']) ? 1 : 0;
        $nepalste_status = isset($_POST['nepalste_status']) ? 1 : 0;
        $arrayData = array(
            'paypal_status' => $paypal_status,
            'stripe_status' => $stripe_status,
            'payumoney_status' => $payumoney_status,
            'paystack_status' => $paystack_status,
            'razorpay_status' => $razorpay_status,
            'midtrans_status' => $midtrans_status,
            'sslcommerz_status' => $sslcommerz_status,
            'jazzcash_status' => $jazzcash_status,
            'flutterwave_status' => $flutterwave_status,
            'paytm_status' => $paytm_status,
            'toyyibpay_status' => $toyyibpay_status,
            'payhere_status' => $payhere_status,
            'nepalste_status' => $nepalste_status,
        );

        $this->db->where('branch_id', $branchID);
        $q = $this->db->get('payment_config');
        if ($q->num_rows() == 0) {
            $arrayData['branch_id'] = $branchID;
            $this->db->insert('payment_config', $arrayData);
        } else {
            $this->db->where('id', $q->row()->id);
            $this->db->update('payment_config', $arrayData);
        }
        $message = translate('the_configuration_has_been_updated');
        $array = array('status' => 'success', 'message' => $message);
        echo json_encode($array);
    }

    public function midtrans_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('midtrans_client_key', 'Client Key', 'trim|required');
        $this->form_validation->set_rules('midtrans_server_key', 'Server Key', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $sandbox = isset($_POST['midtrans_sandbox']) ? 1 : 2;
            $arrayMidtrans = array(
                'midtrans_client_key' => $this->input->post('midtrans_client_key'),
                'midtrans_server_key' => $this->input->post('midtrans_server_key'),
                'midtrans_sandbox' => $sandbox,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayMidtrans['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayMidtrans);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayMidtrans);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function sslcommerz_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('sslcz_store_id', 'Store ID', 'trim|required');
        $this->form_validation->set_rules('sslcz_store_passwd', 'Store Password', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $sandbox = isset($_POST['sslcommerz_sandbox']) ? 1 : 2;
            $arraySSLcommerz = array(
                'sslcz_store_id' => $this->input->post('sslcz_store_id'),
                'sslcz_store_passwd' => $this->input->post('sslcz_store_passwd'),
                'sslcommerz_sandbox' => $sandbox,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arraySSLcommerz['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arraySSLcommerz);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arraySSLcommerz);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }
    
    public function jazzcash_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('jazzcash_merchant_id', 'Jazzcash Merchant ID', 'trim|required');
        $this->form_validation->set_rules('jazzcash_passwd', 'Jazzcash Password', 'trim|required');
        $this->form_validation->set_rules('jazzcash_integerity_salt', 'Jazzcash Integerity Salt', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $sandbox = isset($_POST['jazzcash_sandbox']) ? 1 : 2;
            $arraySSLcommerz = array(
                'jazzcash_merchant_id' => $this->input->post('jazzcash_merchant_id'),
                'jazzcash_passwd' => $this->input->post('jazzcash_passwd'),
                'jazzcash_integerity_salt' => $this->input->post('jazzcash_integerity_salt'),
                'jazzcash_sandbox' => $sandbox,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arraySSLcommerz['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arraySSLcommerz);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arraySSLcommerz);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function flutterwave_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('flutterwave_public_key', 'Public Key', 'trim|required');
        $this->form_validation->set_rules('flutterwave_secret_key', 'Secret Key', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $sandbox = isset($_POST['flutterwave_sandbox']) ? 1 : 2;
            $arrayFlutterwave = array(
                'flutterwave_public_key' => $this->input->post('flutterwave_public_key'),
                'flutterwave_secret_key' => $this->input->post('flutterwave_secret_key'),
                'flutterwave_sandbox' => $sandbox,
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayFlutterwave['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayFlutterwave);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayFlutterwave);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function paytm_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('paytm_merchantmid', 'Merchant MID', 'trim|required');
        $this->form_validation->set_rules('paytm_merchantkey', 'Merchant Key', 'trim|required');
        $this->form_validation->set_rules('paytm_merchant_website', 'Website', 'trim|required');
        $this->form_validation->set_rules('paytm_industry_type', 'Industry Type', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayPaytm = array(
                'paytm_merchantmid' => $this->input->post('paytm_merchantmid'),
                'paytm_merchantkey' => $this->input->post('paytm_merchantkey'),
                'paytm_merchant_website' => $this->input->post('paytm_merchant_website'),
                'paytm_industry_type' => $this->input->post('paytm_industry_type'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaytm['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaytm);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaytm);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function toyyibPay_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('toyyibpay_secretkey', 'Secret key', 'trim|required');
        $this->form_validation->set_rules('toyyibpay_categorycode', 'Category Code', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayPaytm = array(
                'toyyibpay_secretkey' => $this->input->post('toyyibpay_secretkey'),
                'toyyibpay_categorycode' => $this->input->post('toyyibpay_categorycode'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaytm['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaytm);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaytm);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function payhere_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('payhere_merchant_id', 'Merchant ID', 'trim|required');
        $this->form_validation->set_rules('payhere_merchant_secret', 'Merchant Secret', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayPaytm = array(
                'payhere_merchant_id' => $this->input->post('payhere_merchant_id'),
                'payhere_merchant_secret' => $this->input->post('payhere_merchant_secret'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaytm['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaytm);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaytm);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function nepalste_save()
    {
        if (!get_permission('payment_settings', 'is_add')) {
            ajax_access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        $this->form_validation->set_rules('nepalste_public_key', 'Public Key', 'trim|required');
        $this->form_validation->set_rules('nepalste_secret_key', 'Secret Key', 'trim|required');
        if ($this->form_validation->run() !== false) {
            $arrayPaytm = array(
                'nepalste_public_key' => $this->input->post('nepalste_public_key'),
                'nepalste_secret_key' => $this->input->post('nepalste_secret_key'),
            );
            $this->db->where('branch_id', $branchID);
            $q = $this->db->get('payment_config');
            if ($q->num_rows() == 0) {
                $arrayPaytm['branch_id'] = $branchID;
                $this->db->insert('payment_config', $arrayPaytm);
            } else {
                $this->db->where('id', $q->row()->id);
                $this->db->update('payment_config', $arrayPaytm);
            }
            $message = translate('the_configuration_has_been_updated');
            $array = array('status' => 'success', 'message' => $message);
        } else {
            $error = $this->form_validation->error_array();
            $array = array('status' => 'fail', 'error' => $error);
        }
        echo json_encode($array);
    }

    public function branchUpdate($data)
    {
        $arrayBranch = array(
            'name' => $data['branch_name'],
            'school_name' => $data['school_name'],
            'email' => $data['email'],
            'mobileno' => $data['mobileno'],
            'currency' => $data['currency'],
            'symbol' => $data['currency_symbol'],
            'city' => $data['city'],
            'state' => $data['state'],
            'address' => $data['address'],
        );
        $this->db->where('id', get_loggedin_branch_id());
        $this->db->update('branch', $arrayBranch);
    }
}
