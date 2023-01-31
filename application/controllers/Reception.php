<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Filenumber.php';

class Reception extends CI_Controller {
    
    public $mainTitle = null;
    public $header = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(array("employee_model", "patient_model"));
        $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
        $this->header = 'Reception';
        $this->load->library(array("form_validation", "session"));
        $this->load->helper(array("url", "html", "form", "security", "date"));
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
        
        $this->is_active();
        $this->is_receptionist();
        $this->is_first_login();
        $this->is_pwd_expired();
        
    }
    
    #########################################################
    # PRIVATE FUNCTIONS - TO BE ONLY CALLED WITHIN THIS CLASS
    #########################################################
  
    private function is_first_login()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
        $is_first_login = $this->session->userdata('user_first_login');
        if($is_first_login)
        {
        redirect(base_url('password/change/1/'.@$this->header), 'refresh');
        }
    }

    private function is_pwd_expired()
    {
        if(session_status() == PHP_SESSION_NONE) session_start();
        $reg_date = $this->session->userdata('user_reg_date');
        $last_pwd_update = $this->session->userdata('user_last_pwd_update');
        $last_pwd_update1 = !empty($last_pwd_update) ? $last_pwd_update : $reg_date;
        
        $date1 = date_create(date("Y-m-d", strtotime($last_pwd_update1)));
        $date2 = date_create(date("Y-m-d"));

        $diff = date_diff($date1, $date2);
        $days = abs($diff->format("%R%a"));       
        if($days >= 90) return redirect(base_url('password/change/0/'.@$this->header), 'refresh');
        
    }
    
    private function is_receptionist()
    {
        if($user_role = $this->session->userdata('user_role') != 'REC')
        {
            // return redirect($_SERVER['HTTP_REFERER']);
            return redirect(base_url('login'));
        }
    }
    
    private function is_active()
    {
        $user_id = $this->session->userdata('user_id');
        $is_active = $this->employee_model->is_employee_active($user_id);
        if(!$is_active)
        {
            if(session_status() == PHP_SESSION_NONE) session_start();
            $this->session->set_userdata('user_isActive', FALSE);
            $this->session->set_flashdata('error', 'Your session is expired/de-actived');
            redirect(base_url('login'));
        }
    }
    
    private function generate_file_number()
    {
        $FileNumberGenerator = new Filenumber();
        $file_number = $FileNumberGenerator->generate();
        unset($FileNumberGenerator);
        
        return $file_number;
    }
    
    private function checkIfFileNumberExist($number)
    {
        return $this->patient_model->checkIfFileNumberExist($number);
    }
    
    private function get_id_by_file_number($file_number)
    {
        $data = $this->patient_model->get_id_by_file_number($file_number);
        if(!empty($data)) return $data;
        else return FALSE;
    }
    
    private function isEligibleToDelete($record_id)
    {
        return $this->patient_model->checkIfReceptionCanDeleteRecord($record_id);
    }
    
    private function isEligibleToDelete2($record_id)
    {
        return $this->patient_model->checkIfReceptionCanDeleteRecord2($record_id);
    }
    
    private function get_timespan($post_date)
    {
        $dateTime = new DateTime($post_date);
        $unixTime = $dateTime->format('U');
        $now = time();
        return timespan($unixTime, $now, 1) . ' ago';
    }
    
    public function uuid() 
    {
        $this->load->library('uuid');
        //Output a v4 UUID 
        $uuid4 = $this->uuid->v4();
        $uuid4 = str_replace('-', '', $uuid4);
        return $uuid4;
    }
    
    private function check_if_uuid_exist_patient_table($uuid)
    {
        return $this->patient_model->check_if_uuid_exist_patient_table($uuid);
    }

    private function check_if_uuid_exist_record_table($uuid)
    {
        return $this->patient_model->check_if_uuid_exist_record_table($uuid);
    }

    private function check_if_uuid_exist_visit_table($uuid)
    {
        return $this->patient_model->check_if_uuid_exist_visit_table($uuid);
    }
    ###########################################################
    ###########################################################
    
    
    
    public function index()
    {
        $data = array(
            'title' => $this->mainTitle,
            'header' => $this->header,
            'heading' => 'Dashboard',
        );
        $this->load->view('pages/reception/dashboard', $data);        
    }
    
    public function my_patients()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $data = [];
            
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
            
            $result = $this->patient_model->get_my_patients($this->input->post());
            
            $i = $this->input->post("start");
            foreach($result as $r)
            {
                $i++;
                $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
                $is_not_pending = !empty($r->rec_blood_pressure) && !empty($r->rec_pulse_rate) && !empty($r->rec_weight) && !empty($r->rec_height) && !empty($r->rec_temeperature);
                
                $deleteBtn = '<button type="button" class="btn btn-danger btn-sm" name="deleteBtn" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" title="Remove"><i class="bi bi-trash3-fill"></i></button>';
                
                $pendingBtn = '<button type="button" class="btn btn-sm btn-warning" name="statusButton" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" title="Finalize this entry"><i class="bi bi-exclamation-octagon"></i> Pending</button>';
                
                $data[] = array(
                    $i,
                    $full_name,
                    $r->pat_file_no,
                    $r->pat_phone,
                    $this->get_timespan($r->rec_regdate),
                    $is_not_pending ? '' : $pendingBtn.'|'.$deleteBtn,
                    !empty($r->rec_blood_pressure) ? $r->rec_blood_pressure.'&nbsp;mmHg' : 'NILL',
                    !empty($r->rec_pulse_rate) ? $r->rec_pulse_rate.'&nbsp;bpm' : 'NILL',
                    !empty($r->rec_respiration) ? $r->rec_respiration.'&nbsp;bpm' : 'NILL',
                    !empty($r->rec_weight) ? $r->rec_weight.'&nbsp;kg' : 'NILL',
                    !empty($r->rec_height) ? $r->rec_height.'&nbsp;cm' : 'NILL',
                    !empty($r->rec_temeperature) ? $r->rec_temeperature.'&deg;C' : 'NILL',
                );
            }
            
            $result = array(
                "draw" => $draw,
                "recordsTotal" => $this->patient_model->countAll(),
                "recordsFiltered" => $this->patient_model->countFiltered($this->input->get()),
                "data" => $data
            );
            
            echo json_encode($result);
            exit();
        }
        else
        {
            $data = array(
                'title' => $this->mainTitle,
                'header' => $this->header,
                'heading' => 'My Patients',
            );
            $this->load->view('pages/reception/my_patients', $data); 
        }  
    }
    
    
    public function register_patient()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            try
            {                
                $this->form_validation->set_rules('file_no', 'File Number', 'trim');
                $this->form_validation->set_rules('first_name', 'First Name', 'trim|required|max_length[20]');
                $this->form_validation->set_rules('middle_name', 'Middle Name', 'trim|max_length[20]');
                $this->form_validation->set_rules('last_name', 'Last Name', 'trim|required|max_length[20]');
                $this->form_validation->set_rules('dob', 'Birt Date', 'trim|required');
                $this->form_validation->set_rules('gender', 'Gender', 'trim|required');
                $this->form_validation->set_rules('occupation', 'Occupation', 'trim|required');
                $this->form_validation->set_rules('phone', 'Phone Number', 'trim|required|exact_length[10]');
                $this->form_validation->set_rules('address', 'Address', 'trim|required|max_length[40]');
                $this->form_validation->set_rules('em_name', 'Emergency Contact Name', 'trim|required|max_length[40]');
                $this->form_validation->set_rules('em_phone', 'Emergency Contact Number', 'trim|required|exact_length[10]');
                $this->form_validation->set_rules('nhif_card', 'NHIF Card Number', 'trim');
                $this->form_validation->set_rules('nhif_auth', 'NHIF Authorization Number', 'trim');
                $this->form_validation->set_rules('vote_no', 'Vote Number', 'trim');
                
                if ($this->form_validation->run() == FALSE)
                {
                    echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
                    exit();
                }
                else
                {
                    $pat_file_no = $this->security->xss_clean($this->input->post('file_no'));
                    $pat_fname = $this->security->xss_clean($this->input->post('first_name'));
                    $pat_mname = $this->security->xss_clean($this->input->post('middle_name'));
                    $pat_lname = $this->security->xss_clean($this->input->post('last_name'));
                    $pat_dob = $this->security->xss_clean($this->input->post('dob'));
                    $pat_gender = $this->security->xss_clean($this->input->post('gender'));
                    $pat_occupation = $this->security->xss_clean($this->input->post('occupation'));
                    $pat_phone = $this->security->xss_clean($this->input->post('phone'));
                    $pat_address = $this->security->xss_clean($this->input->post('address'));
                    $pat_em_name = $this->security->xss_clean($this->input->post('em_name'));
                    $pat_em_phone = $this->security->xss_clean($this->input->post('em_phone'));
                    $pat_nhif_card = $this->security->xss_clean($this->input->post('nhif_card'));
                    $pat_nhif_auth = $this->security->xss_clean($this->input->post('nhif_auth'));
                    $pat_vote = $this->security->xss_clean($this->input->post('vote_no'));
                    $patient_file_no = $this->security->xss_clean($this->input->post('file_no'));
                    
                    if(!empty($patient_file_no))
                    {                        
                        $patient_to_update = $this->get_id_by_file_number($patient_file_no);
                        if($patient_to_update == FALSE)
                        {
                            echo json_encode(array("status" => FALSE , 'data' => '<code> Oops!, patient is not available </code>'));
                            exit();
                        }
                        else
                        {
                            $record_id = $this->uuid();
                            while($this->check_if_uuid_exist_record_table($record_id))
                            {
                                $record_id = $this->uuid();
                            }

                            $update_data = array();
                            $update_data['pat_file_no'] = $pat_file_no;
                            $update_data['pat_fname'] = strtoupper($pat_fname);
                            if(!empty($pat_mname)) $update_data['pat_mname'] = strtoupper($pat_mname);
                            $update_data['pat_lname'] = strtoupper($pat_lname);
                            $update_data['pat_dob'] = $pat_dob;
                            $update_data['pat_gender'] = strtoupper($pat_gender);
                            $update_data['pat_occupation'] = strtoupper($pat_occupation);
                            $update_data['pat_phone'] = $pat_phone;
                            $update_data['pat_address'] = strtoupper($pat_address);
                            $update_data['pat_em_name'] = strtoupper($pat_em_name);
                            $update_data['pat_em_number'] = $pat_em_phone;
                            if(!empty($pat_nhif_card)) $update_data['pat_nhif_card_no'] = $pat_nhif_card;
                            if(!empty($pat_nhif_auth)) $update_data['pat_nhif_auth_no'] = $pat_nhif_auth;
                            if(!empty($pat_vote)) $update_data['pat_vote_no'] = $pat_vote;
                            
                            $do_update = $this->patient_model->update_patient_records($update_data, $patient_to_update['pat_id']);
                            if($do_update)
                            {
                                $instance_data = array(
                                    'rec_id' => $record_id,
                                    'rec_patient_id' => $patient_to_update['pat_id'],
                                    'rec_patient_file' => $patient_to_update['pat_file_no'],
                                    'rec_regdate' => date('Y-m-d H:i:s'),
                                );
                                $create_instance = $this->patient_model->create_patient_instance($instance_data);
                                echo json_encode(array("status" => TRUE , 'data' => '<code> Registered successifully </code>'));
                                exit();
                            }
                            else
                            {
                                echo json_encode(array("status" => FALSE , 'data' => '<code> Internal server error </code>'));
                                exit();
                            }                            
                        }
                    }
                    else
                    {
                        if($this->patient_model->checkPhoneExist($pat_phone))
                        {
                            echo json_encode(array("status" => FALSE , 'data' => '<code> Phone number arleady exists! </code>'));
                            exit();
                        }
                        else
                        {                            
                            $file_no = $this->generate_file_number();
                            while($this->checkIfFileNumberExist($file_no))
                            {
                                $file_no = $this->generate_file_number();
                            }

                            $patient_id = $this->uuid();
                            while($this->check_if_uuid_exist_patient_table($patient_id))
                            {
                                $patient_id = $this->uuid();
                            }

                            $record_id = $this->uuid();
                            while($this->check_if_uuid_exist_record_table($record_id))
                            {
                                $record_id = $this->uuid();
                            }
                            
                            $insert_data = array();
                            $insert_data['pat_id'] = $patient_id;
                            $insert_data['pat_file_no'] = $file_no;
                            $insert_data['pat_fname'] = strtoupper($pat_fname);
                            if(!empty($pat_mname)) $insert_data['pat_mname'] = strtoupper($pat_mname);
                            $insert_data['pat_lname'] = strtoupper($pat_lname);
                            $insert_data['pat_dob'] = $pat_dob;
                            $insert_data['pat_gender'] = strtoupper($pat_gender);
                            $insert_data['pat_occupation'] = strtoupper($pat_occupation);
                            $insert_data['pat_phone'] = $pat_phone;
                            $insert_data['pat_address'] = strtoupper($pat_address);
                            $insert_data['pat_em_name'] = strtoupper($pat_em_name);
                            $insert_data['pat_em_number'] = $pat_em_phone;
                            if(!empty($pat_nhif_card)) $insert_data['pat_nhif_card_no'] = $pat_nhif_card;
                            if(!empty($pat_nhif_auth)) $insert_data['pat_nhif_auth_no'] = $pat_nhif_auth;
                            if(!empty($pat_vote)) $insert_data['pat_vote_no'] = $pat_vote;
                            $insert_id = $this->patient_model->insert_new_patient($insert_data);
                            
                            $instance_data = array(
                                'rec_id' => $record_id,
                                'rec_patient_id' => $patient_id,
                                'rec_patient_file' => $file_no,
                                'rec_regdate' => date('Y-m-d H:i:s'),
                            );
                            $this->patient_model->create_patient_instance($instance_data);
                            
                            echo json_encode(array("status" => TRUE , 'data' => '<code> Registered successifully </code>'));
                            exit();
                        }
                    }
                }
            } catch (\Throwable $th) {
                echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
                exit();          
            }
        }
        else
        {
            $data = array(
                'title' => $this->mainTitle,
                'header' => $this->header,
                'heading' => 'Registration',
            );
            $this->load->view('pages/reception/patient_registration', $data);
        }
    }
    
    
    public function search_patient()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $search_string = $this->input->post('search_keyword');
            $search_string = $this->security->xss_clean($search_string);
            if(!empty($search_string))
            {
                $data = $this->patient_model->search_patient_by_keyword($search_string);
                echo json_encode($data);
                exit();
            }
            else
            {
                echo json_encode(array());
                exit();
            }
        }
    }
    
    public function get_patient_by_id()
    {
        $this->form_validation->set_rules('patient_id', 'Patient', 'trim|required');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
        }
        else
        {
            $patient_id = $this->security->xss_clean($this->input->post('patient_id'));
            $patient_data = $this->patient_model->get_patient_by_id($patient_id);
            
            if(!empty($patient_data))
            {
                echo json_encode(array("status" => TRUE , 'data' => $patient_data));
                exit();
            }
            else
            {
                echo json_encode(array("status" => FALSE , 'data' => '<code> Oops!, no such entry </code>'));
                exit();
            }
        }
    }
    
    public function patient_preliminaries()
    {
        try
        {
            if($this->input->server('REQUEST_METHOD') === 'POST')
            {
                $this->form_validation->set_rules('record_id', 'Record', 'trim|required');
                $this->form_validation->set_rules('patient_file', 'Patient File Number', 'trim|required|exact_length[11]');
                $this->form_validation->set_rules('blood_pressure', 'Blood Pressure', 'trim|required|min_length[5]|max_length[7]');
                $this->form_validation->set_rules('pulse_rate', 'Pulse Rate', 'trim|required|numeric|greater_than_equal_to[40]|less_than_equal_to[171]');
                $this->form_validation->set_rules('weight', 'Weight', 'trim|required|numeric|greater_than_equal_to[1.5]|less_than_equal_to[727]');
                $this->form_validation->set_rules('height', 'Height', 'trim|required|numeric|greater_than_equal_to[22]|less_than_equal_to[500]');
                $this->form_validation->set_rules('temperature', 'Temperature', 'trim|required|numeric|greater_than_equal_to[28]|less_than_equal_to[41]');
                $this->form_validation->set_rules('respiration', 'Respiration Rate', 'trim|required|numeric|greater_than_equal_to[9]|less_than_equal_to[61]');
                $this->form_validation->set_rules('care', 'Patient Care', 'trim|required|numeric|greater_than_equal_to[0]|less_than_equal_to[1]');
                $this->form_validation->set_rules('modify', 'Modify', 'trim');
                
                if ($this->form_validation->run() == FALSE)
                {
                    echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
                    exit();
                }
                else
                {
                    $record_id = $this->security->xss_clean($this->input->post('record_id'));
                    $patient_file = $this->security->xss_clean($this->input->post('patient_file'));
                    $blood_pressure = $this->security->xss_clean($this->input->post('blood_pressure'));
                    $pulse_rate = $this->security->xss_clean($this->input->post('pulse_rate'));
                    $weight = $this->security->xss_clean($this->input->post('weight'));
                    $height = $this->security->xss_clean($this->input->post('height'));
                    $temperature = $this->security->xss_clean($this->input->post('temperature'));
                    $respiration = $this->security->xss_clean($this->input->post('respiration'));
                    $care = $this->security->xss_clean($this->input->post('care'));
                    $modify = $this->security->xss_clean($this->input->post('modify'));

                    $visit_id = $this->uuid();
                    while($this->check_if_uuid_exist_visit_table($visit_id))
                    {
                        $visit_id = $this->uuid();
                    }
                    
                    $data = array(
                        'rec_attendant_file_no' => $this->session->userdata('user_pf'),
                        'rec_blood_pressure' => $blood_pressure,
                        'rec_pulse_rate' => $pulse_rate,
                        'rec_weight' => $weight,
                        'rec_height' => $height,
                        'rec_temeperature' => $temperature,
                        'rec_respiration' => $respiration,
                        'rec_care' => $care,
                    );
                    $this->patient_model->set_patient_preliminary_tests($data, $record_id);
                    
                    if(empty($modify))
                    {
                        $initial_visit_data = array(
                            'vs_id' => $visit_id,
                            'vs_record_id' => $record_id,
                            'vs_record_patient_pf' => $patient_file,
                            'vs_visit' => 'nasubiri_daktari',
                        );
                        $this->patient_model->initiate_visit($initial_visit_data);
                    }
                    
                    echo json_encode(array("status" => TRUE , 'data' => '<code> Success</code>'));
                    exit();
                }
            } 
        }
        catch (\Throwable $th)
        {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit();
        }
    }
    
    public function delete_patient($record_id)
    {
        try{
            if(!$this->isEligibleToDelete($record_id))
            {
                echo json_encode(array("status" => FALSE, 'data' => '<code> Oops!, action not allowed </code>'));
                exit();
            }
            else
            {
                $this->patient_model->deleteRecord($record_id);
                echo json_encode(array("status" => TRUE , 'data' => '<code> Removed successifully</code>'));
                exit();
            }
        }
        catch (\Throwable $th)
        {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit();
        }
    }
    
    public function delete_patient_2($record_id)
    {
        try{
            if(!$this->isEligibleToDelete2($record_id))
            {
                echo json_encode(array("status" => FALSE, 'data' => '<code> Oops!, action not allowed </code>'));
                exit();
            }
            else
            {
                $this->patient_model->deleteRecord($record_id);
                echo json_encode(array("status" => TRUE , 'data' => '<code> Removed successifully</code>'));
                exit();
            }
        }
        catch (\Throwable $th)
        {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit();
        }
    }
    
    public function reports()
    {
        $data = array(
            'title' => $this->mainTitle,
            'header' => $this->header,
            'heading' => 'Reports',
        );
        $this->load->view('pages/reception/reports', $data);
    }
    
    public function modifications()
    {
        
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $search_string = $this->input->post('search_keyword');
            $search_string = $this->security->xss_clean($search_string);
            if(!empty($search_string))
            {
                $data = $this->patient_model->search_patient_instance_by_keyword($search_string);
                echo json_encode($data);
                exit();
            }
            else
            {
                echo json_encode(array());
                exit();
            }
        }
        else 
        {
            $data = array(
                'title' => $this->mainTitle,
                'header' => $this->header,
                'heading' => 'Extra',
                'subHeading' => 'Modifications',
            );
            $this->load->view('pages/reception/patient_modifications', $data);           
        }
    }
    
}

?>