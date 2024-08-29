<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 6.6
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Exam_progress.php
 * @copyright : Reserved RamomCoder Team
 */

class Exam_progress extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('exam_progress_model');
        $this->load->model('marksheet_template_model');
        $this->load->model('subject_model');
        $this->load->model('sms_model');
        $this->load->model('email_model');
    }

    public function marksheet()
    {
        if (!get_permission('progress_reports', 'is_view')) {
            access_denied();
        }
        $branchID = $this->application_model->get_branch_id();
        if ($_POST) {
            if (is_superadmin_loggedin()) {
                $this->form_validation->set_rules('branch_id', translate('branch'), 'trim|required');
            }
            $this->form_validation->set_rules('class_id', translate('class'), 'required');
            $this->form_validation->set_rules('section_id', translate('section'), 'required');
            $this->form_validation->set_rules('exam_id[]', translate('exam'), 'required');
            $this->form_validation->set_rules('session_id', translate('academic_year'), 'required');
            $this->form_validation->set_rules('template_id', translate('marksheet') . " " . translate('template'), 'required');
            if ($this->form_validation->run() == true) {
                $sessionID = $this->input->post('session_id');
                $examID = $this->input->post('exam_id[]');
                $classID = $this->input->post('class_id');
                $sectionID = $this->input->post('section_id');
                $this->db->select('e.roll,e.id as enrollID,s.*,c.name as category');
                $this->db->from('enroll as e');
                $this->db->join('student as s', 'e.student_id = s.id', 'inner');
                $this->db->join('mark as m', 'm.student_id = s.id', 'inner');
                $this->db->join('student_category as c', 'c.id = s.category_id', 'left');
                $this->db->where('e.session_id', $sessionID);
                $this->db->where('e.class_id', $classID);
                $this->db->where('e.section_id', $sectionID);
                $this->db->where('e.branch_id', $branchID);
                $this->db->where_in('m.exam_id', $examID);
                $this->db->group_by('m.student_id');
                $this->db->order_by('e.id', 'ASC');
                $this->data['examIDArr'] = $examID;
                $this->data['student'] = $this->db->get()->result_array();  
            }
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/bootstrap-select/dist/css/bootstrap-select.min.css',
            ),
            'js' => array(
                'vendor/bootstrap-select/dist/js/bootstrap-select.min.js',
            ),
        );
        $this->data['branch_id'] = $branchID;
        $this->data['sub_page'] = 'exam_progress/marksheet';
        $this->data['main_menu'] = 'exam_reports';
        $this->data['title'] = translate('progress') . " " . translate('progress_reports');
        $this->load->view('layout/index', $this->data);
    }

    public function reportCardPrint()
    {
        if ($_POST) {
            if (!get_permission('progress_reports', 'is_view')) {
                ajax_access_denied();
            }
            $this->data['examArray'] = $this->input->post('exam_id[]');
            $this->data['student_array'] = $this->input->post('student_id');
            $this->data['remarks_array'] = $this->input->post('remarks');
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['print_date'] = $this->input->post('print_date');
            $this->data['sessionID'] = $this->input->post('session_id');
            $this->data['templateID'] = $this->input->post('template_id');
            $this->data['branchID'] = $this->application_model->get_branch_id();
            echo $this->load->view('exam_progress/reportCard', $this->data, true);
        }
    }

    public function reportCardPdf()
    {
        if ($_POST) {
            if (!get_permission('progress_reports', 'is_view')) {
                ajax_access_denied();
            }
            $this->data['examArray'] = $this->input->post('exam_id[]');
            $this->data['student_array'] = $this->input->post('student_id');
            $this->data['remarks_array'] = $this->input->post('remarks');
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['print_date'] = $this->input->post('print_date');
            $this->data['sessionID'] = $this->input->post('session_id');
            $this->data['templateID'] = $this->input->post('template_id');
            $this->data['branchID'] = $this->application_model->get_branch_id();
            $html = $this->load->view('exam_progress/reportCard_PDF', $this->data, true);

            $this->load->library('html2pdf');
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/vendor/bootstrap/css/bootstrap.min.css')), 1);
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/css/custom-style.css')), 1);
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/css/pdf-style.css')), 1);
            $this->html2pdf->mpdf->WriteHTML($html);
            $this->html2pdf->mpdf->SetDisplayMode('fullpage');
            $this->html2pdf->mpdf->autoScriptToLang  = true;
            $this->html2pdf->mpdf->baseScript        = 1;
            $this->html2pdf->mpdf->autoLangToFont    = true;
            return $this->html2pdf->mpdf->Output(time() . '.pdf', "I");
        }
    }


    public function pdf_sendByemail()
    {
        if ($_POST) {
            if (!get_permission('report_card', 'is_view')) {
                ajax_access_denied();
            }
            $enrollID = $this->input->post('enrollID');
            $this->data['examArray'] = $this->input->post('exam_id[]');
            $this->data['student_array'] = [$this->input->post('student_id')];
            $this->data['remarks_array'] = $this->input->post('remarks');
            $this->data['class_id'] = $this->input->post('class_id');
            $this->data['section_id'] = $this->input->post('section_id');
            $this->data['print_date'] = $this->input->post('print_date');
            $this->data['sessionID'] = $this->input->post('session_id');
            $this->data['templateID'] = $this->input->post('template_id');
            $this->data['branchID'] = $this->application_model->get_branch_id();
            $html = $this->load->view('exam_progress/reportCard_PDF', $this->data, true);

            $this->load->library('html2pdf');
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/vendor/bootstrap/css/bootstrap.min.css')), 1);
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/css/custom-style.css')), 1);
            $this->html2pdf->mpdf->WriteHTML(file_get_contents(base_url('assets/css/pdf-style.css')), 1);
            $this->html2pdf->mpdf->WriteHTML($html);
            $this->html2pdf->mpdf->SetDisplayMode('fullpage');
            $this->html2pdf->mpdf->autoScriptToLang  = true;
            $this->html2pdf->mpdf->baseScript        = 1;
            $this->html2pdf->mpdf->autoLangToFont    = true;
           
            $file = $this->html2pdf->mpdf->Output(time() . '.pdf', "S");
            $data['exam_name'] = "Progress Reports";
            $data['file'] = $file;
            $data['enroll_id'] = $enrollID;
            $response = $this->email_model->emailPDFexam_marksheet($data);
            if ($response == true) {
                $array = array('status' => 'success', 'message' => translate('mail_sent_successfully'));

            } else {
                $array = array('status' => 'error', 'message' => translate('something_went_wrong'));

            }
            echo json_encode($array);
        }
    }


    // get exam list based on the branch
    public function getExamByBranch()
    {
        $html = "";
        $branchID = $this->application_model->get_branch_id();
        if (!empty($branchID)) {
            $this->db->select('id,name,term_id');
            $this->db->where(array('branch_id' => $branchID, 'session_id' => get_session_id()));
            $this->db->order_by('id', 'asc'); 
            $result = $this->db->get('exam')->result_array();
            if (count($result)) {
                foreach ($result as $row) {
                    if ($row['term_id'] != 0) {
                        $term = $this->db->select('name')->where('id', $row['term_id'])->get('exam_term')->row()->name;
                        $name = $row['name'] . ' (' . $term . ')';
                    } else {
                        $name = $row['name'];
                    }
                    $html .= '<option value="' . $row['id'] . '">' . $name . '</option>';
                }
            } else {
                $html .= '<option value="">' . translate('no_information_available') . '</option>';
            }
        } else {
            $html .= '<option value="">' . translate('select_branch_first') . '</option>';
        }
        echo $html;
    }

    public function getDistributionByBranch()
    {
        $html = "";
        $table = $this->input->post('table');
        $branch_id = $this->application_model->get_branch_id();
        if (!empty($branch_id)) {
            $result = $this->db->select('id,name')->where('branch_id', $branch_id)->get('exam_mark_distribution')->result_array();
            if (count($result)) {
                foreach ($result as $row) {
                    $html .= '<option value="' . $row['id'] . '">' . $row['name'] . '</option>';
                }
            }
        }
        echo $html;
    }
}
