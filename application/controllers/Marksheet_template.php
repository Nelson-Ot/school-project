<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.6
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Marksheet_template.php
 * @copyright : Reserved RamomCoder Team
 */

class Marksheet_template extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('marksheet_template_model');
    }

    /* form validation rules */
    protected function _validation()
    {
        if (is_superadmin_loggedin()) {
            $this->form_validation->set_rules('branch_id', translate('branch'), 'required');
        }
        $this->form_validation->set_rules('marksheet_template_name', translate('template') . " " . translate('name'), 'trim|required');
        $this->form_validation->set_rules('page_layout', translate('page_layout'), 'trim|required');
        $this->form_validation->set_rules('top_space', "Top Space", 'trim|numeric');
        $this->form_validation->set_rules('bottom_space', "Bottom Space", 'trim|numeric');
        $this->form_validation->set_rules('right_space', "Right Space", 'trim|numeric');
        $this->form_validation->set_rules('left_space', "Left Space", 'trim|numeric');
        $this->form_validation->set_rules('photo_size', "Photo Size", 'trim|numeric');
        $this->form_validation->set_rules('header_content', translate('header') . " " . translate('content'), 'trim|required');
        $this->form_validation->set_rules('footer_content', translate('footer') . " " . translate('content'), 'trim|required');

        $this->form_validation->set_rules('background_file', translate('background_file'), 'trim|callback_photoHandleUpload[background_file]');
        $this->form_validation->set_rules('left_signature_file', translate('left') . " " . translate('signature'), 'trim|callback_photoHandleUpload[left_signature_file]');
        $this->form_validation->set_rules('middle_signature_file', translate('middle') . " " . translate('signature'), 'trim|callback_photoHandleUpload[middle_signature_file]');
        $this->form_validation->set_rules('right_signature_file', translate('right') . " " . translate('signature'), 'trim|callback_photoHandleUpload[right_signature_file]');
        $this->form_validation->set_rules('logo_file', translate('logo') . " " . translate('image'), 'trim|callback_photoHandleUpload[logo_file]');
    }

    public function index()
    {
        if (!get_permission('marksheet_template', 'is_view')) {
            access_denied();
        }
        if ($_POST) {
            if (get_permission('marksheet_template', 'is_add')) {
                $this->_validation();
                if ($this->form_validation->run() !== false) {
                    // SAVE INFORMATION IN THE DATABASE FILE
                    $this->marksheet_template_model->save($this->input->post());
                    set_alert('success', translate('information_has_been_saved_successfully'));
                    $array = array('status' => 'success');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/summernote/summernote.css',
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/summernote/summernote.js',
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['branch_id'] = $this->application_model->get_branch_id();
        $this->data['certificatelist'] = $this->marksheet_template_model->getList();
        $this->data['title'] = translate('marksheet') . " " . translate('template');
        $this->data['sub_page'] = 'marksheet_template/index';
        $this->data['main_menu'] = 'marksheet_template';
        $this->load->view('layout/index', $this->data);
    }

    public function edit($id = '')
    {
        if (!get_permission('marksheet_template', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->_validation();
            if ($this->form_validation->run() !== false) {
                // save all information in the database file
                $this->marksheet_template_model->save($this->input->post());
                set_alert('success', translate('information_has_been_updated_successfully'));
                $url = base_url('marksheet_template/index');
                $array = array('status' => 'success', 'url' => $url);
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['certificate'] = $this->app_lib->getTable('marksheet_template', array('t.id' => $id), true);
        if (empty($this->data['certificate'])) {
            redirect(base_url('marksheet_template/index'));
        }
        $this->data['title'] = translate('marksheet') . " " . translate('template');
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/summernote/summernote.css',
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.css',
            ),
            'js' => array(
                'vendor/summernote/summernote.js',
                'vendor/bootstrap-fileupload/bootstrap-fileupload.min.js',
            ),
        );
        $this->data['sub_page'] = 'marksheet_template/edit';
        $this->data['main_menu'] = 'marksheet_template';
        $this->load->view('layout/index', $this->data);
    }

    public function delete($id = '')
    {
        if (get_permission('marksheet_template', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $getRow = $this->db->get('marksheet_template')->row_array();
            if (!empty($getRow)) {
                $path = 'uploads/marksheet/';
                if (file_exists($path . $getRow['background'])) {
                    unlink($path . $getRow['background']);
                }
                if (file_exists($path . $getRow['logo'])) {
                    unlink($path . $getRow['logo']);
                }
                if (file_exists($path . $getRow['left_signature'])) {
                    unlink($path . $getRow['left_signature']);
                }
                if (file_exists($path . $getRow['middle_signature'])) {
                    unlink($path . $getRow['middle_signature']);
                }
                if (file_exists($path . $getRow['right_signature'])) {
                    unlink($path . $getRow['right_signature']);
                }
                $this->db->where('id', $id);
                $this->db->delete('marksheet_template');
            }
        }
    }

    public function getCertificate()
    {
        if (get_permission('marksheet_template', 'is_view')) {
            $templateID = $this->input->post('id');
            $this->data['marksheet_template'] = $this->marksheet_template_model->get('marksheet_template', array('id' => $templateID), true);
            $this->load->view('marksheet_template/viewTemplete', $this->data);
        }
    }
}