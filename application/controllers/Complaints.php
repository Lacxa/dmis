
<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Complaints extends CI_Controller {
    
    public $mainTitle = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(array("complaints_model", "employee_model"));  	
        $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
        $this->load->library(array("form_validation", "session"));
        $this->load->helper(array("url", "html", "form", "security", "date"));
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    }
    
    public function index()
    {
        $data = array(
            'title' => $this->mainTitle,
            'header' => 'Doctor',
            'heading' => 'Extra',
            'subHeading' => 'Chief Complaints',
        );
        $this->load->view('pages/doctor/complaints', $data);  
    }  
    
    private function get_timespan($post_date, $is_unix=FALSE)
    {
        $unixTime = $post_date;
        $now = time();
        if(!$is_unix)
        {
            $dateTime = new DateTime($post_date);
            $unixTime = $dateTime->format('U');
        }
        return timespan($unixTime, $now, 1) . ' ago';
    } 
    
    public function checkNameIfExist($name)
    {
        if ($this->complaints_model->checkNameIfExist($name) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkNameIfExist', 'This complaint name already exist!');
            return FALSE;
        }
    }
    
    public function checkIfTokenExist($token)
    {
        if ($this->complaints_model->checkIfTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkIfTokenExist', 'This complaint token already exist!');
            return FALSE;
        }
    }
    
    public function search()
    {
        $keyword = $this->input->post('query');
        echo json_encode($this->complaints_model->getComplaint($keyword));
    }
    
    public function get_complaints()
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->complaints_model->get_complaints($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            $author = $r->author;
            $author_data = $this->employee_model->get_employee_by_file_number($r->author);
            if(!empty($author_data))
            {
                $author = $author_data['emp_lname'] . ', ' . $author_data['emp_fname'][0];
            }
            $i++;
            $data[] = array(
                $i,
                $r->text,
                $r->token,
                $author,
                empty($r->desc) ? 'Not set' : $r->desc,
            );
        }
        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->complaints_model->countAll(),
            "recordsFiltered" => $this->complaints_model->countFiltered($this->input->get()),
            "data" => $data
        );
        
        echo json_encode($result);
        exit();
    }
    
    public function add_complaint()
    {
        $this->form_validation->set_rules('name', 'Complaint Name', 'trim|required|callback_checkNameIfExist');
        $this->form_validation->set_rules('token', 'Complaint Token', 'trim|required|callback_checkIfTokenExist');
        $this->form_validation->set_rules('desc', 'Complaint Description', 'trim');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
        }
        else
        {
            $data = array(
                'comp_name' => ucfirst($this->security->xss_clean($this->input->post('name'))),
                'comp_token' => $this->security->xss_clean($this->input->post('token')),
                'comp_author' => $this->session->userdata('user_pf'),
            );
            $desc = $this->security->xss_clean($this->input->post('desc'));
            if(!empty($desc))
            {
                $data['comp_descriptions'] = $desc;
            }			
            $this->complaints_model->save_complaints($data);
            echo json_encode(array(
                "status" => TRUE,
                'data' => '<span class="text-success"> Received successifully</span>'
            ));
        }   
    }
    
}

?>