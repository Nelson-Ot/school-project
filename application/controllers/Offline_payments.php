<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system (Saas)
 * @version : 6.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Offline_payments.php
 * @copyright : Reserved RamomCoder Team
 */

class Offline_payments extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('offline_payments_model');
        $this->load->model('fees_model');
    }

    /* offline payments type form validation rules */
    protected function type_validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('type_name', translate('name'), 'trim|required|callback_unique_type');
        $this->form_validation->set_rules('note', translate('note'), 'trim');
    }

    /* offline payments type control */
    public function type()
    {
        if (!get_permission('offline_payments_type', 'is_view')) {
            access_denied();
        }
        if ($_POST) {
            if (!get_permission('offline_payments_type', 'is_add')) {
                ajax_access_denied();
            }
            $this->type_validation();
            if ($this->form_validation->run() !== false) {
                $post = $this->input->post();
                $this->offline_payments_model->typeSave($post);
                set_alert('success', translate('information_has_been_saved_successfully'));
                $array = array('status' => 'success');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/summernote/summernote.css',
            ),
            'js' => array(
                'vendor/summernote/summernote.js',
            ),
        );
        $this->data['categorylist'] = $this->app_lib->getTable('offline_payment_types');
        $this->data['title'] = translate('offline_payments') . " " . translate('type');
        $this->data['sub_page'] = 'offline_payments/type';
        $this->data['main_menu'] = 'offline_payments';
        $this->load->view('layout/index', $this->data);
    }

    public function type_edit($id = '')
    {
        if (!get_permission('offline_payments_type', 'is_edit')) {
            access_denied();
        }

        if ($_POST) {
            $this->type_validation();
            if ($this->form_validation->run() !== false) {
                $post = $this->input->post();
                $this->offline_payments_model->typeSave($post);
                set_alert('success', translate('information_has_been_updated_successfully'));
                $url = base_url('offline_payments/type');
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/summernote/summernote.css',
            ),
            'js' => array(
                'vendor/summernote/summernote.js',
            ),
        );
        $this->data['category'] = $this->app_lib->getTable('offline_payment_types', array('t.id' => $id), true);
        $this->data['title'] = translate('offline_payments') . " " . translate('type');
        $this->data['sub_page'] = 'offline_payments/type_edit';
        $this->data['main_menu'] = 'offline_payments';
        $this->load->view('layout/index', $this->data);
    }

    public function type_delete($id = '')
    {
        if (get_permission('offline_payments_type', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('offline_payment_types');
        }
    }

    public function unique_type($name)
    {
        $branchID = $this->application_model->get_branch_id();
        $typeID = $this->input->post('type_id');
        if (!empty($typeID)) {
            $this->db->where_not_in('id', $typeID);
        }
        $this->db->where(array('name' => $name, 'branch_id' => $branchID));
        $uniform_row = $this->db->get('offline_payment_types')->num_rows();
        if ($uniform_row == 0) {
            return true;
        } else {
            $this->form_validation->set_message("unique_type", translate('already_taken'));
            return false;
        }
    }

    /* offline fees payments  history */
    public function payments()
    {
        if (!get_permission('offline_payments', 'is_view')) {
            access_denied();
        }

        $branchID = $this->application_model->get_branch_id();
        $filter = array();
        if ($this->input->post('search')) {
            $filter['enroll.branch_id'] = $branchID;
            $filter['op.status'] = $this->input->post('payments_status');
        }
        $this->data['paymentslist'] = $this->offline_payments_model->getOfflinePaymentsList($filter);
        $this->data['branch_id'] = $branchID;
        $this->data['title'] = translate('offline_payments');
        $this->data['sub_page'] = 'offline_payments/history';
        $this->data['main_menu'] = 'offline_payments';
        $this->load->view('layout/index', $this->data);
    }

    // get payments details modal
    public function getApprovelDetails()
    {
        if (get_permission('offline_payments', 'is_view')) {
            $this->data['payments_id'] = $this->input->post('id');
            $this->load->view('offline_payments/approvel_modalView', $this->data);
        }
    }

    public function download($id = '', $file = '')
    {
        if (!empty($id) && !empty($file)) {
            $this->db->select('orig_file_name,enc_file_name');
            $this->db->where('id', $id);
            $payments = $this->db->get('offline_fees_payments')->row();
            if ($file != $payments->enc_file_name) {
                access_denied();
            }
            $this->load->helper('download');
            $fileData = file_get_contents('./uploads/attachments/offline_payments/' . $payments->enc_file_name);
            force_download($payments->orig_file_name, $fileData);
        }
    }

    public function approved($id = '', $file = '')
    {
        if ($_POST) {
            if (!get_permission('offline_payments', 'is_view')) {
                access_denied();
            }

            $status = $this->input->post('status');
            if ($status != 1) {
                $arrayLeave = array(
                    'approved_by' => get_loggedin_user_id(),
                    'status' => $status,
                    'comments' => $this->input->post('comments'),
                    'approve_date' => date('Y-m-d H:i:s'),
                );
                $id = $this->input->post('id');
                $this->db->where('id', $id);
                $this->db->update('offline_fees_payments', $arrayLeave);
                if ($status == 2) {
                    $this->offline_payments_model->update($id);
                }
                set_alert('success', translate('information_has_been_updated_successfully'));

            }
            redirect(base_url('offline_payments/payments'));
        }
    }

    public function getTypeInstruction()
    {
        if ($_POST) {
            $typeID = $this->input->post('typeID');
            if (empty($typeID)) {
                echo null;
                exit;
            }
            $r = $this->db->where('id', $typeID)->get('offline_payment_types')->row();
            if (!empty($r->note)) {
                echo $r->note;
            } else {
                echo "";
            }

        }
    }
}
