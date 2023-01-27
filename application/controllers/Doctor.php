<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Doctor extends CI_Controller {
  
  public $mainTitle = null;
  public $header = null;
  
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->model(array("employee_model", "patient_model", "investigation_model", "medicine_model", "stock_model", "complaints_model"));
    $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
    $this->header = 'Doctor';
    $this->load->library(array("form_validation", "session"));
    $this->load->helper(array("url", "html", "form", "security", "date"));
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    
    $this->is_active();
    $this->is_doctor();
    $this->is_first_login();
    $this->is_pwd_expired();
  } 
  
  #########################################################
  # PRIVATE FUNCTIONS - TO BE ONLY CALLED WITHIN THIS CLASS
    
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
  
  private function is_doctor()
  {
    if($this->session->userdata('user_role') != 'MO')
    {
      // return redirect($_SERVER['HTTP_REFERER']);
      return redirect(base_url('login'));
    }
  }
  
  // if(session_status() == PHP_SESSION_NONE) session_start();
  
  private function session_patients_min_info($record)
  {
    return $this->patient_model->get_session_patients_min_info($this->session->userdata('user_pf'), $record);
    // return $this->patient_model->get_doctor_client('NIT/PF/0004');
  }
  
  private function get_employee_by_file_number($file_number)
  {
    $data = $this->employee_model->get_employee_by_file_number($file_number);
    if(!empty($data)) return $data;
    else return FALSE;
  }
  
  private function check_Patient_Is_Init($patient_record)
  {
    return $this->patient_model->check_Patient_Is_Init($patient_record);
  }
  
  private function check_Patient_Is_FromLab($patient_record)
  {
    return $this->patient_model->check_Patient_Is_FromLab($patient_record);
  }
  
  private function check_Patient_Is_LabReturn($patient_record)
  {
    return $this->patient_model->check_Patient_Is_LabReturn($patient_record);
  }
  
  private function check_Patient_Is_PhReturn($patient_record)
  {
    return $this->patient_model->check_Patient_Is_PhReturn($patient_record);
  }
  
  private function patientRecord_data_by_pkid($id)
  {
    return $this->patient_model->patientRecord_data_by_pkid($id);
  }
  
  private function visit_data_by_pkid($id)
  {
    return $this->patient_model->visit_data_by_pkid($id);
  }
  
  private function visit_data_by_recordId($record)
  {
    return $this->patient_model->visit_data_by_recordId($record);
  }
  
  private function symptoms_data_by_pkid($id)
  {
    return $this->patient_model->symptoms_data_by_pkid($id);
  }
  
  private function symptoms_data_by_record_id($id)
  {
    return $this->patient_model->symptoms_data_by_record_id($id);
  }
  
  private function checkIfEligibleToFillSymptoms($symptom_id)
  {
    return $this->patient_model->checkIfEligibleToFillSymptoms($symptom_id);
  }
  
  private function checkSymptomIfEligibleToFillComplaint($symptom_id)
  {
    return $this->patient_model->checkSymptomIfEligibleToFillComplaint($symptom_id);
  }
  
  private function checkSymptomIfEligibleToDeleteComplaint($symptom_id)
  {
    return $this->patient_model->checkSymptomIfEligibleToDeleteComplaint($symptom_id);
  }
  
  private function checkSymptomIfEligibleToFillInvestigation($symptom_id)
  {
    return $this->patient_model->checkSymptomIfEligibleToFillInvestigation($symptom_id);
  }

  private function isELigiblePostUpdateInvestigation($record_id)
  {
    return $this->patient_model->isELigiblePostUpdateInvestigation($record_id);    
  }
  
  private function checkSymptomIfEligibleToFillDisease($symptom_id)
  {
    return $this->patient_model->checkSymptomIfEligibleToFillDisease($symptom_id);
  }
  
  private function checkSymptomIfEligibleToFillMedicine($symptom_id)
  {
    return $this->patient_model->checkSymptomIfEligibleToFillMedicine($symptom_id);
  }
  
  private function isDoctorEligibleToRelease($record, $user_pf)
  {
    return $this->patient_model->isDoctorEligibleToRelease($record, $user_pf);
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

  private function bmi_calculator($weight, $height)
  {
    $output = '';
    $height = (float)($height / 100);
    $bmi = $weight / ($height * $height);
    
    if($bmi <= 18.5)
    {
      $output = 'UNDERWEIGHT';
    }
    else if($bmi > 18.5 && $bmi <= 24.9)
    {
      $output = 'NORMAL WEIGHT';
    }
    else if($bmi > 24.9 && $bmi <= 29.9)
    {
      $output = 'OVERWEIGHT';
    }
    else if($bmi > 29.9 && $bmi <= 34.9)
    {
      $output = 'OBESITY CLASS I';
    }
    else if($bmi > 34.9 && $bmi <= 39.9)
    {
      $output = 'OBESITY CLASS II';
    }
    else if($bmi > 39.9)
    {
      $output = 'OBESITY CLASS III';
    }
    return $output;
  }

  public function checkIfComplaintTokenIsValid($token)
  {
    if($this->complaints_model->checkIfTokenExist($token) == FALSE)
    {
      $this->form_validation->set_message('checkIfComplaintTokenIsValid', 'This complaint does not exist!');
      return FALSE;
    }
    else
    {
      return TRUE;
    }
  }

  public function uuid() 
  {
    $this->load->library('uuid');
        //Output a v4 UUID 
    $uuid4 = $this->uuid->v4();
    $uuid4 = str_replace('-', '', $uuid4);
    return $uuid4;
  }

  private function check_if_uuid_exist_symptoms_table($uuid)
  {
    return $this->patient_model->check_if_uuid_exist_symptoms_table($uuid);
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
    $this->load->view('pages/doctor/dashboard', $data);
    // $this->ajax_session();
    // exit();
  }
  
  public function ajax_count_session_patients()
  {
    echo json_encode(array("status" => TRUE, 'data' => $this->patient_model->count_All_Session_Patients()));
    exit();
  }
  
  public function patient_history()
  {
    $data = array();
    $data['title'] = $this->mainTitle;
    $data['header'] = 'Doctor';
    $data['heading'] = 'Extra';
    $data['subHeading'] = 'Patient History';
    
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      try {
        $this->form_validation->set_rules('pf', 'File Number', 'trim|required|exact_length[11]');
        $this->form_validation->set_rules('start', 'Start Date', 'trim|required');
        $this->form_validation->set_rules('end', 'End Date', 'trim|required');
        
        if ($this->form_validation->run() == FALSE)
        {
          echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
          exit();
        }
        else
        {
          $pf = $this->security->xss_clean($this->input->post('pf'));
          $start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
          $end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
          
          $basic = $this->patient_model->get_id_by_file_number($pf);
          if(empty($basic))
          {
            echo json_encode(array("status" => FALSE , 'data' => '<code> Oops!, this patient is not available </code>'));
            exit();
          }
          else
          {
            $result = $this->patient_model->client_report_post($pf, $start, $end);
            if(empty($result))
            {
              echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, a patient with PF <code>'.$pf.'</code> has got no history in the date range <code>'.$start.'</code> - <code>'.$end.'</code></span>'));
              exit();
            }
            else
            {
              echo json_encode(array("status" => TRUE, 'data' => $result));
              exit();	
            }
          }
        }
      } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit();          
      }
    }
    $this->load->view('pages/doctor/patient_history', $data);
  }
  
  public function session_patients()
  {
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      $data = [];
      
      $draw = intval($this->input->post("draw"));
      $start = intval($this->input->post("start"));
      $length = intval($this->input->post("length"));
      
      $result = $this->patient_model->get_doctor_session_patients($this->input->post());
      
      $i = $this->input->post("start");
      foreach($result as $r)
      {
        $i++;
        $full_name = $r->pat_fname.' '.$r->pat_lname;
        
        $my_date = $r->pat_dob;
        $date1 = date_create(date("Y-m-d", strtotime($my_date)));
        $date2 = date_create(date("Y-m-d"));        
        $diff = date_diff($date1, $date2);
        $age = abs($diff->format("%Y"));

        $status = '';        
        if(!empty($r->vs_visit))
        {
          $text = $r->vs_visit;
          if($text == 'nipo_daktari_1' || $text == 'nipo_daktari_1r' || $text == 'nipo_daktari_2' || $text == 'nipo_daktari_2r')
          {
            if($text == 'nipo_daktari_1') $status = 'Reception';
            else if($text == 'nipo_daktari_1r') $status = 'Lab - Returned';
            else if($text == 'nipo_daktari_2') $status = 'Lab - Success';
            else $status = 'Pharmacy - Returned';
          }
        }
        
        $viewBtn = '<a type="button" class="btn btn-sm btn-primary fw-bold" 
        href="'.base_url('doctor/session-patients/').$r->rec_id.'">View <i class="bi bi-eye-fill me-1"></i></a>';
        
        $data[] = array(
          $i,
          $full_name . ' <code>('. $r->pat_file_no .')</code>',
          $age,
          $r->pat_address,
          $this->get_timespan($r->rec_regdate),
          $viewBtn,
          $status
        );
      }
      
      $result = array(
        "draw" => $draw,
        "recordsTotal" => $this->patient_model->count_All_Session_Patients(),
        "recordsFiltered" => $this->patient_model->countFilteredSessionPatients($this->input->get()),
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
        'heading' => 'My Session',
      );
      $this->load->view('pages/doctor/session_patients', $data); 
    }  
  }
  
  public function reports()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => @$this->header,
      'heading' => 'Extra',
      'subHeading' => 'Reports',
    );
    $this->load->view('pages/doctor/reports', $data);
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
  
  public function patients()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => $this->header,
      'heading' => 'Patients',
    );
    $this->load->view('pages/doctor/patients', $data);
  }
  
  public function patients_from_reception()
  {
    $data = [];
    
    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    
    $result = $this->patient_model->get_doctor_patients_from_reception($this->input->post());
    
    // $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    // $query = $this->db->get("patient_record r");
    
    $i = $this->input->post("start");
    foreach($result as $r)
    {
      $i++;
      $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
      
      $attendant = $this->get_employee_by_file_number($r->rec_attendant_file_no);
      $attendant_name = $attendant['emp_lname'].', '.$attendant['emp_fname'][0];
      
      $is_init = $r->vs_visit == 'nasubiri_daktari' ? TRUE : FALSE;
      $init_btn = '<button type="button" class="btn btn-sm btn-primary fw-bold" name="initial_serve" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Serve</button>';
      
      $is_kwanza = $r->vs_visit == 'nipo_daktari_1' ? TRUE : FALSE;
      $kwanza_btn = '<a href="'.base_url('doctor/my-session').'" type="button" class="btn btn-sm btn-warning fw-bold" name="stop_kwanza" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>Pending</a>';
      
      $data[] = array(
        $i,
        $full_name,
        $r->pat_file_no,
        $attendant_name,
        $this->get_timespan($r->rec_regdate),
        $is_init ? $init_btn : ($is_kwanza ? $kwanza_btn : "Wait"),
      );
    }
    
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $this->patient_model->countAllForDoctorOne(),
      "recordsFiltered" => $this->patient_model->countFilteredForDoctorOne($this->input->get()),
      "data" => $data
    );
    
    echo json_encode($result);
    exit();
  }
  
  public function count_patients_from_reception()
  {
    try {
      $result = $this->patient_model->countAllForDoctorOne();
      if($result >= 0)
      {
        echo json_encode(array("status" => TRUE, 'data' => $result));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function patients_from_lab()
  {
    $data = [];
    
    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    
    $result = $this->patient_model->get_doctor_patients_from_lab($this->input->post());  
    
    $i = $this->input->post("start");
    foreach($result as $r)
    {
      $i++;
      $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
      
      $reception_attendant = $this->get_employee_by_file_number($r->rec_attendant_file_no);
      $reception_attendant_name = $reception_attendant['emp_lname'].', '.$reception_attendant['emp_fname'][0];
      
      $other_attendants_string = $r->vs_attendants;
      $other_attendants_array = explode("_", $other_attendants_string);
      $lab_attendant = $other_attendants_array[1];
      $lab_attendant_full_data = $this->get_employee_by_file_number($lab_attendant);
      $lab_attendant_name = $lab_attendant_full_data['emp_lname'].', '.$lab_attendant_full_data['emp_fname'][0];
      
      $entry_time_since_at_doctor_string = $r->vs_time;
      $entry_time_since_at_doctor_array = explode("_", $entry_time_since_at_doctor_string);
      $lab_entry_time = $entry_time_since_at_doctor_array[1];
      
      $is_fromLab = $r->vs_visit == 'nimetoka_lab' ? TRUE : FALSE;
      $fromLabBtn = '<button type="button" class="btn btn-sm btn-primary fw-bold" name="serve_fromLab" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Serve</button>';
      
      $with_doctor_fromLab = $r->vs_visit == 'nipo_daktari_2' ? TRUE : FALSE;
      $withDoctorFromLabBtn = '<a href="'.base_url('doctor/my-session').'" type="button" class="btn btn-sm btn-warning fw-bold" name="manage_after_doctorFromLab" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>Pending</a>';
      
      $data[] = array(
        $i,
        $full_name,
        $r->pat_file_no,
        $reception_attendant_name,
        $lab_attendant_name,
        $this->get_timespan($r->rec_regdate),
        $is_fromLab ? $fromLabBtn : $withDoctorFromLabBtn,
      );
    }
    
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $this->patient_model->countAllForDoctorTwo(),
      "recordsFiltered" => $this->patient_model->countFilteredForDoctorTwo($this->input->get()),
      "data" => $data
    );
    
    echo json_encode($result);
    exit();
  }
  
  public function count_patients_from_lab()
  {
    try {
      $result = $this->patient_model->countAllForDoctorTwo();
      if($result >= 0)
      {
        echo json_encode(array("status" => TRUE, 'data' => $result));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function patients_lab_returns()
  {
    $data = [];
    
    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    
    $result = $this->patient_model->get_doctor_patients_lab_returns($this->input->post());
    
    $i = $this->input->post("start");
    foreach($result as $r)
    {
      $i++;
      $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
      
      $reception_attendant = $this->get_employee_by_file_number($r->rec_attendant_file_no);
      $reception_attendant_name = $reception_attendant['emp_lname'].', '.$reception_attendant['emp_fname'][0];
      
      $other_attendants_string = $r->vs_attendants;
      $other_attendants_array = explode("_", $other_attendants_string);
      $lab_attendant = $other_attendants_array[count($other_attendants_array)-1];
      $lab_attendant_full_data = $this->get_employee_by_file_number($lab_attendant);
      $lab_attendant_name = $lab_attendant_full_data['emp_lname'].', '.$lab_attendant_full_data['emp_fname'][0];
      
      $modifyLab = $r->vs_visit == 'nimerudishwa_kutoka_lab' ? TRUE : FALSE;
      $modifyLabBtn = '<button type="button" class="btn btn-sm btn-primary fw-bold" name="serve_LabReturn" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Update</button>';
      
      $withDoctorFromLabBtn = '<a href="'.base_url('doctor/my-session').'" type="button" class="btn btn-sm btn-warning" name="manage_after_doctorReturnLab" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>Pending</a>';
      
      $data[] = array(
        $i,
        $full_name,
        $r->pat_file_no,
        $reception_attendant_name,
        $lab_attendant_name,
        $this->get_timespan($r->rec_regdate),
        $modifyLab ? $modifyLabBtn : $withDoctorFromLabBtn,
      );
    }
    
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $this->patient_model->countAllForDoctorLabReturn(),
      "recordsFiltered" => $this->patient_model->countFilteredForDoctorLabReturn($this->input->get()),
      "data" => $data
    );
    
    echo json_encode($result);
    exit();
  }
  
  public function count_patients_lab_returns()
  {
    try {
      $result = $this->patient_model->countAllForDoctorLabReturn();
      if($result >= 0)
      {
        echo json_encode(array("status" => TRUE, 'data' => $result));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function patients_pharmacy_returns()
  {
    $data = [];
    
    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    
    $result = $this->patient_model->get_doctor_patients_ph_returns($this->input->post());
    
    $i = $this->input->post("start");
    foreach($result as $r)
    {
      $i++;
      $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
      
      $reception_attendant = $this->get_employee_by_file_number($r->rec_attendant_file_no);
      $reception_attendant_name = $reception_attendant['emp_lname'].', '.$reception_attendant['emp_fname'][0];
      
      $other_attendants_string = $r->vs_attendants;
      $other_attendants_array = explode("_", $other_attendants_string);
      $pharmacist = $other_attendants_array[count($other_attendants_array)-1];    
      $employee_data = $this->get_employee_by_file_number($pharmacist);
      $pharmacist_name = $employee_data['emp_lname'].', '.$employee_data['emp_fname'][0];
      
      $modifyPh = $r->vs_visit == 'nimerudishwa_kutoka_ph' ? TRUE : FALSE;
      $modifyPhBtn = '<button type="button" class="btn btn-sm btn-primary  fw-bold" name="serve_PhReturn" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Update</button>';
      
      $withDoctorFromPhBtn = '<a href="'.base_url('doctor/my-session').'" type="button" class="btn btn-sm btn-warning" name="manage_after_doctorReturnLab" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>Pending</a>';
      
      $data[] = array(
        $i,
        $full_name,
        $r->pat_file_no,
        $reception_attendant_name,
        $pharmacist_name,
        $this->get_timespan($r->rec_regdate),
        $modifyPh ? $modifyPhBtn : $withDoctorFromPhBtn,
      );
    }
    
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $this->patient_model->countAllForDoctorPhReturn(),
      "recordsFiltered" => $this->patient_model->countFilteredForDoctorPhReturn($this->input->get()),
      "data" => $data
    );
    
    echo json_encode($result);
    exit();
  }
  
  public function count_patients_ph_returns()
  {
    try {
      $result = $this->patient_model->countAllForDoctorPhReturn();
      if($result >= 0)
      {
        echo json_encode(array("status" => TRUE, 'data' => $result));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function get_inpatients()
  {
    $data = [];
    
    $draw = intval($this->input->post("draw"));
    $start = intval($this->input->post("start"));
    $length = intval($this->input->post("length"));
    
    $result = $this->patient_model->get_doctor_inpatients($this->input->post());
    
    $i = $this->input->post("start");
    foreach($result as $r)
    {
      $i++;
      $full_name = empty($r->pat_mname) ? $r->pat_fname.' '.$r->pat_lname : $r->pat_fname.' '.$r->pat_mname.' '.$r->pat_lname;
      
      $attendant = $this->get_employee_by_file_number($r->rec_attendant_file_no);
      $attendant_name = $attendant['emp_lname'].', '.$attendant['emp_fname'][0];
      
      $is_init = $r->vs_visit == 'nasubiri_daktari' ? TRUE : FALSE;
      $init_btn = '<button type="button" class="btn btn-sm btn-primary fw-bold" name="serve_inpatient" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Serve</button>';
      
      $is_wodini = $r->vs_visit == 'nipo_wodini' ? TRUE : FALSE;
      $is_wodini_btn = '<a href="'.base_url('doctor/inpatients/1').'" type="button" class="btn btn-sm btn-warning fw-bold" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>View</a>';
      
      $data[] = array(
        $i,
        $full_name,
        $r->pat_file_no,
        $attendant_name,
        $this->get_timespan($r->rec_regdate),
        $is_init ? $init_btn : ($is_kwanza ? $kwanza_btn : "Wait"),
      );
    }
    
    $result = array(
      "draw" => $draw,
      "recordsTotal" => $this->patient_model->count_All_InPatients(),
      "recordsFiltered" => $this->patient_model->countFiltered_InPatients($this->input->get()),
      "data" => $data
    );
    
    echo json_encode($result);
    exit();
  }
  
  public function count_inpatients()
  {
    try {
      $result = $this->patient_model->count_All_InPatients();
      if($result >= 0)
      {
        echo json_encode(array("status" => TRUE, 'data' => $result));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function serve_initial()
  {
    $this->form_validation->set_rules('record', 'Patient', 'trim|required');
    $this->form_validation->set_rules('visit', 'Visit', 'trim|required');
    
    if ($this->form_validation->run() == FALSE)
    {
      echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
      exit();
    }
    else
    {
      $patient_instance = $this->security->xss_clean($this->input->post('record'));
      $patient_visit_id = $this->security->xss_clean($this->input->post('visit'));
      
      if(!$this->check_Patient_Is_Init($patient_instance))
      {
        echo json_encode(array("status" => FALSE, 'data' => '<code> Action not allowed</code>'));
        exit();
      }
      else
      { 
        $visit_data = $this->visit_data_by_pkid($patient_visit_id);
        if(empty($visit_data))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<code> Invalid action: not a fresh client </code>'));
          exit();
        }
        else
        {
          $current_user_pf = $this->session->userdata('user_pf');
          $current_attendance = $visit_data['vs_attendants'];
          $new_attendance = $current_attendance == NULL ? $current_user_pf : $current_attendance.'_'.$current_user_pf;
          
            // $current_time = time();
          $current_time = strtotime(date('Y-m-d H:i:s'));
          $time_to_append = $visit_data['vs_time'];
          $new_time = $time_to_append == NULL ? $current_time : $time_to_append.'_'.$current_time;
          
          $data = array(
            'vs_visit' => 'nipo_daktari_1',
            'vs_attendants' => $new_attendance,
            'vs_time' => $new_time,
          );
          
          $symptom_id = $this->uuid();
          while($this->check_if_uuid_exist_symptoms_table($symptom_id))
          {
            $symptom_id = $this->uuid();
          }
          $this->patient_model->update_patient_visit($data, $patient_visit_id);
          $initial_symptoms = array(
            'sy_id' => $symptom_id,
            'sy_record_id' => $patient_instance,
            'sy_record_patient_pf' => $visit_data['vs_record_patient_pf'],
          );
          $this->patient_model->initiate_symptoms($initial_symptoms);
          echo json_encode(array("status" => TRUE, 'data' => '<code>Success</code>', 'redirect' => base_url('doctor/session-patients/'.$patient_instance)));
          exit();
        }
      }
    }
  }
  
  public function serve_from_lab()
  {
    $this->form_validation->set_rules('record', 'Patient', 'trim|required');
    $this->form_validation->set_rules('visit', 'Visit', 'trim|required');
    
    if ($this->form_validation->run() == FALSE)
    {
      echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
      exit();
    }
    else
    {
      $patient_instance = $this->security->xss_clean($this->input->post('record'));
      $patient_visit_id = $this->security->xss_clean($this->input->post('visit'));
      
      if($this->check_Patient_Is_FromLab($patient_instance))
      { 
        $visit_data = $this->visit_data_by_pkid($patient_visit_id);
        if(empty($visit_data))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Invalid action: lab activity is missing</span>'));
          exit();
        }
        else
        {
          $current_user_pf = $this->session->userdata('user_pf');
          $current_attendance = $visit_data['vs_attendants'];
          $new_attendance = $current_attendance == NULL ? $current_user_pf : $current_attendance.'_'.$current_user_pf;
          
          $current_time = time();
          $time_to_append = $visit_data['vs_time'];
          $new_time = $time_to_append == NULL ? $current_time : $time_to_append.'_'.$current_time;
          
          $data = array(
            'vs_visit' => 'nipo_daktari_2',
            'vs_attendants' => $new_attendance,
            'vs_time' => $new_time,
          );
          $this->patient_model->update_patient_visit($data, $patient_visit_id);
          echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>', 'redirect' => base_url('doctor/session-patients/'.$patient_instance)));
          exit();       
        }
      }
      else
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Action not allowed</span>'));
      }
    }
  }
  
  public function serve_lab_return()
  {
    $this->form_validation->set_rules('record', 'Patient', 'trim|required');
    $this->form_validation->set_rules('visit', 'Visit', 'trim|required');
    
    if ($this->form_validation->run() == FALSE)
    {
      echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
      exit();
    }
    else
    {
      $patient_instance = $this->security->xss_clean($this->input->post('record'));
      $patient_visit_id = $this->security->xss_clean($this->input->post('visit'));
      
      if($this->check_Patient_Is_LabReturn($patient_instance))
      { 
        $visit_data = $this->visit_data_by_pkid($patient_visit_id);
        if(empty($visit_data))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Invalid action: lab activity is missing</span>'));
          exit();
        }
        else
        {            
          $data = array(
            'vs_visit' => 'nipo_daktari_1r',
          );
          $this->patient_model->update_patient_visit($data, $patient_visit_id);
          echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>', 'redirect' => base_url('doctor/session-patients/'.$patient_instance)));
          exit();       
        }
      }
      else
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Action not allowed</span>'));
      }
    }
  }
  
  public function serve_pharmacy_return()
  {
    $this->form_validation->set_rules('record', 'Patient', 'trim|required');
    $this->form_validation->set_rules('visit', 'Visit', 'trim|required');
    
    if ($this->form_validation->run() == FALSE)
    {
      echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
      exit();
    }
    else
    {
      $patient_instance = $this->security->xss_clean($this->input->post('record'));
      $patient_visit_id = $this->security->xss_clean($this->input->post('visit'));
      
      if($this->check_Patient_Is_PhReturn($patient_instance))
      { 
        $visit_data = $this->visit_data_by_pkid($patient_visit_id);
        if(empty($visit_data))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Invalid action: pharmacy activity is missing</span>'));
          exit();
        }
        else
        {            
          $data = array(
            'vs_visit' => 'nipo_daktari_2r',
          );
          $this->patient_model->update_patient_visit($data, $patient_visit_id);
          echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>', 'redirect' => base_url('doctor/session-patients/'.$patient_instance)));
          exit();  
        }
      }
      else
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Action not allowed</span>'));
      }
    }
  }
  
  public function session_patient($record)
  {
    $patient_records = $this->session_patients_min_info($record);
    if(empty($patient_records))
    {
      $this->session->set_flashdata('error', 'oops!, this patient does not exist');
      return redirect($_SERVER['HTTP_REFERER']);
    }
    $data = array(
      'title' => $this->mainTitle,
      'header' => $this->header,
      'heading' => 'My Session',
      'subHeading' => $patient_records['file'],
      'my_session' => $patient_records['record'],
      'categories' => $this->investigation_model->get_all_investigation_categories(),
      'subcategories' => $this->investigation_model->get_all_investigation_subcategories(),
    );
    $this->load->view('pages/doctor/my_session', $data);
  }
  
  public function get_full_session_info($record)
  {
    $record = $this->security->xss_clean($record);
    // check if record is available in patient-record table
    $data_from_patientRecordTable = $this->patientRecord_data_by_pkid($record);
    if(empty($data_from_patientRecordTable))
    {
      echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, invalid input </span>'));
      exit();
    }
    else
    {
      $data = $this->patient_model->get_full_session_info($record);
      if(!empty($data))
      {
        $my_date = $data['pat_dob'];
        
        $date1 = date_create(date("Y-m-d", strtotime($my_date)));
        $date2 = date_create(date("Y-m-d"));
        
        $diff = date_diff($date1, $date2);
        $age = abs($diff->format("%Y")); 
        
        $data['pat_dob'] = $age;
        $data['bmi'] = $this->bmi_calculator($data['rec_weight'], $data['rec_height']);
        echo json_encode(array("status" => TRUE, 'data' => $data));
        exit();
      }
      else
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No data</span>'));
        exit();
      }
    }
  }
  
  public function update_sypmtoms($symptom_id, $record_id)
  {
    try{
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkIfEligibleToFillSymptoms($symptom_id, $record_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, no access</span>'));
          exit();
        }
        else
        {
          $this->form_validation->set_rules('exam_text', 'Physical Examination', 'trim|required');
          if ($this->form_validation->run() == FALSE)
          {
            echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
          }
          else
          {
            $symptoms = $this->security->xss_clean($this->input->post('exam_text'));
            $this->patient_model->update_patient_symptoms($symptoms, $symptom_id);
            echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function update_investigations($symptom_id, $record_id, $visit_id)
  {
    try
    {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToFillInvestigation($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no access</span>'));
          exit();
        }
        else
        {
          if(empty($_POST['investigation_ids']))
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No data supplied </span>'));
            exit();
          }
          else
          {
              // $data_by_visit_table = $this->visit_data_by_pkid($visit_id);
            $arrayOfSelectedCategories = $this->input->post('investigation_ids');
            array_walk($arrayOfSelectedCategories, function(&$value, $key) { $value .= '~null'; } );
            $string_to_insert = implode("^^", $arrayOfSelectedCategories);
            
            $visit_text = 'naenda_lab';
            $_data = $this->symptoms_data_by_pkid($symptom_id);
            if(!empty($_data) && $_data['sy_lab'] == 1)
            {
              $visit_text = 'naenda_lab_r';
            }
            
            $new_visit_data = array(
              'vs_visit' => $visit_text,
            );
            $this->patient_model->update_patient_investigations($string_to_insert, $symptom_id);
            $this->patient_model->update_patient_visit($new_visit_data, $visit_id);
            echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>', 'redirect' => base_url('doctor/session-patients')));
            exit();
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function update_investigations_2($record_id)
  {
    try
    {
      $is_eligible = $this->isELigiblePostUpdateInvestigation($record_id);
      if(!$is_eligible)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no access</span>'));
        exit();
      }
      else
      {
        if(empty($_POST['investigation_ids']))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No data supplied </span>'));
          exit();
        }
        else
        {
          $data_from_symptoms_table = $this->symptoms_data_by_record_id($record_id);
          if(!empty($data_from_symptoms_table))
          {
            $arrayOfSelectedCategories = $this->input->post('investigation_ids');
            array_walk($arrayOfSelectedCategories, function(&$value, $key) { $value .= '~null'; } );
            $string_to_insert = implode("^^", $arrayOfSelectedCategories);

            $this->patient_model->update_patient_investigations($string_to_insert, $data_from_symptoms_table['sy_id']);

            echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>'));
            exit();
          }
          else
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-success">The server has loosed some data, please contact our adminstrator</span>'));
            exit();
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function save_patient_disease($symptom_id, $record_id, $visit_id)
  {
    try {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToFillDisease($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, no access to this entry </span>'));
          exit();
        }
        else
        {
          if(empty($this->input->post('disease')))
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No data supplied</span>'));
            exit();
          }
          else
          {
            $disease_id = $this->security->xss_clean($this->input->post('disease'));
            $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
            if(empty($disease_id))
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Invalid action </span>'));
              exit();
            }
            else
            {
              $current_diseases = $symptoms_data['sy_diseases'];
              if(empty($current_diseases))
              {
                $updated_diseases = $disease_id;
              }
              else
              {
                if (strpos($current_diseases, $disease_id) !== false)
                {
                  echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, arleady exist </span>'));
                  exit();
                }
                else
                {
                  $updated_diseases = $current_diseases.'_'.$disease_id;
                }
              }
              $data = array(
                'sy_diseases' => $updated_diseases,
              );
              $this->patient_model->update_patient_diseases($data, $symptom_id);
              echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>'));
              exit();
            }
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  
  public function delete_myclient_disease($symptom_id, $record_id, $visit_id)
  {
    try
    {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No matching records</span>'));
        exit();
      }
      else
      {
        if(empty($this->input->post('disease')))
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No data supplied</span>'));
          exit();
        }
        else
        {
          $disease_id = $this->security->xss_clean($this->input->post('disease'));            
          $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
          if(empty($disease_id))
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Invalid action </span>'));
            exit();
          }
          else
          {
            $current_diseases = $symptoms_data['sy_diseases'];
            if(empty($current_diseases))
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No any added disease </span>'));
              exit();
            }
            else
            {
              $current_diseases_to_array = explode("_", $current_diseases);
              if (($key = array_search($disease_id, $current_diseases_to_array)) !== false)
              {
                unset($current_diseases_to_array[$key]);
              }
              $updated_diseases = implode("_", $current_diseases_to_array);
              $updated_diseases = $updated_diseases != '' ? $updated_diseases : NULL;
              $data = array(
                'sy_diseases' => $updated_diseases,
              );
              $this->patient_model->update_patient_diseases($data, $symptom_id);
              echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
              exit();
            }
          } 
        }
      }
    }
    catch (\Throwable $th)
    {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function save_patient_complaint($symptom_id, $record_id, $visit_id)
  {
    try {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToFillComplaint($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No access </span>'));
          exit();
        }
        else
        {
          $this->form_validation->set_rules('complaint', 'Complaint', 'trim|required|callback_checkIfComplaintTokenIsValid');
          $this->form_validation->set_rules('duration', 'Complaint Duration', 'trim|required|max_length[50]');
          
          if ($this->form_validation->run() == FALSE)
          {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
          }
          else
          {
            $complaint_token = $this->security->xss_clean($this->input->post('complaint'));
            $duration = $this->security->xss_clean($this->input->post('duration'));
            $duration = str_replace(':', '', $duration);
            $duration = str_replace('$$$', '', $duration);
            $duration = str_replace(' ', '$$$', $duration);

            $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
            if(empty($symptoms_data) || empty($duration))
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Invalid action, missing data </span>'));
              exit();
            }
            else
            {
              $current_complaints = $symptoms_data['sy_complaints'];
              $suffix = '~null';
              $duration = ':' . $duration;
              if(empty($current_complaints))
              {
                $updated_complaints = $complaint_token . $duration . $suffix;
              }
              else
              {
                if (strpos($current_complaints, $complaint_token) !== false)
                {
                  echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, arleady exist </span>'));
                  exit();
                }
                else
                {
                  $updated_complaints = $current_complaints.'_'.$complaint_token . $duration . $suffix;
                }
              }
              $data = array(
                'sy_complaints' => $updated_complaints,
              );
              $this->patient_model->update_patient_diseases($data, $symptom_id);
              echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>'));
              exit();
            }
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function delete_patient_complaint($symptom_id, $record_id, $visit_id)
  {
    try
    {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToDeleteComplaint($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, no access </span>'));
          exit();
        }
        else
        {
          if(empty($this->input->post('complaint')))
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no data supplied</span>'));
            exit();
          }
          else
          {
            $comp_token = $this->security->xss_clean($this->input->post('complaint'));            
            $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
            if(empty($symptoms_data))
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, invalid action </span>'));
              exit();
            }
            else
            {
              $current_complaints = $symptoms_data['sy_complaints'];
              if(empty($current_complaints))
              {
                echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no any available complaint </span>'));
                exit();
              }
              else
              {
                $current_complaints_to_array = explode("_", $current_complaints);
                foreach ($current_complaints_to_array as $key => $value)
                {
                  $complaints_with_history = $value;
                  if(substr($complaints_with_history, 0, strlen($comp_token)) === $comp_token)
                  {
                    if (($key = array_search($complaints_with_history, $current_complaints_to_array)) !== false)
                    {
                      unset($current_complaints_to_array[$key]);
                    }
                    $updated_comps = implode("_", $current_complaints_to_array);
                    $updated_comps = $updated_comps != '' ? $updated_comps : NULL;
                    
                    $data = array(
                      'sy_complaints' => $updated_comps,
                    );
                    $this->patient_model->update_patient_diseases($data, $symptom_id);
                    echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success </span>'));
                    break;
                    exit();
                  }
                }
              }
            } 
          }
        }
      }
    }
    catch (\Throwable $th)
    {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function save_complaint_history($symptom_id, $record_id, $visit_id)
  {
    try {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Oops!, no matching records</span>'));
        exit();
      }
      else
      {
        $this->form_validation->set_rules('complaint', 'Complaint', 'trim|required|callback_checkIfComplaintTokenIsValid');
        $this->form_validation->set_rules('history', 'Amplification', 'trim|required|max_length[300]');

        if ($this->form_validation->run() == FALSE)
        {
          echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
          exit();
        }
        else
        {
          $complaint = $this->security->xss_clean($this->input->post('complaint'));
          $history = $this->security->xss_clean($this->input->post('history'));
          $history = str_replace('~', '', $history);
          $history = str_replace('null', '', $history);
          $history = str_replace('_', '', $history);
          $history = str_replace('$$$', '', $history);
          $history = str_replace(' ', '$$$', $history);

          $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);            
          $current_complaints = $symptoms_data['sy_complaints'];

          $complaints_array = explode("_", $current_complaints);
          foreach ($complaints_array as $key => $value)
          {
            $string = $value;
            $character = $complaint;
            if(strpos($string, $character) === 0)
            {
              $new = str_replace("null", $history, $string);
              $complaints_array[$key] = $new;
              break;
            }
          }

          $current_complaints = implode("_", $complaints_array);
          $data = array(
            'sy_complaints' => $current_complaints,
          );
          $this->patient_model->update_patient_diseases($data, $symptom_id);
          echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Saved successifully</span>'));
          exit();
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function save_patient_medicine($symptom_id, $record_id, $visit_id)
  {
    try {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<code> No matching records </code>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToFillMedicine($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<code> No access </code>'));
          exit();
        }
        else
        {            
          $this->form_validation->set_rules('stock', 'Medicine', 'trim|required');
          if ($this->form_validation->run() == FALSE)
          {
            echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
          }
          else
          {
            $stock_token = $this->security->xss_clean($this->input->post('stock'));
            $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
            
              // check if the selected stock is available
              // $medicine_data = $this->medicine_model->getMedicineById($medicine_id);
            $stock_data = $this->stock_model->getStockByToken($stock_token);
            if(!empty($stock_data) || $stock_token == '10000001')
            { 
              if($stock_token != '10000001')
              {
                if($stock_data->used >= $stock_data->total)
                {
                  echo json_encode(array("status" => FALSE, 'data' => '<code>Out of stock (O/S)</code>'));
                  exit();                    
                }
              }
              $current_medicines = $symptoms_data['sy_medicines'];
              if(empty($current_medicines))
              {
                $updated_medicines = $stock_token.'~null';
              }
              else
              {
                if (strpos($current_medicines, $stock_token) !== false)
                {
                  echo json_encode(array("status" => FALSE, 'data' => '<code>Oops!, arleady exist in a list</code>'));
                  exit();
                }
                else
                {
                  $updated_medicines = $current_medicines.'_'.$stock_token.'~null';
                }
              }
              $data = array(
                'sy_medicines' => $updated_medicines,
              );
              $this->patient_model->update_patient_diseases($data, $symptom_id);
              echo json_encode(array("status" => TRUE, 'data' => '<code>Success</code>'));
              exit();                
            }
            else
            {
              echo json_encode(array("status" => FALSE, 'data' => '<code> This medicine is not available in our stock </code>'));
              exit();
            }
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function save_patient_medicine_description($symptom_id, $record_id, $visit_id)
  {
    try {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No matching records</span>'));
        exit();
      }
      else
      {
        $is_eligible = $this->checkSymptomIfEligibleToFillMedicine($symptom_id);
        if(!$is_eligible)
        {
          echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">This session is not ready for this action</span>'));
          exit();
        }
        else
        {
          
          $this->form_validation->set_rules('stock', 'Stock Token', 'trim|required');
          $this->form_validation->set_rules('string', 'Desriptions', 'trim|required');
          if ($this->form_validation->run() == FALSE)
          {
            echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
          }
          else
          {
            $stock = $this->security->xss_clean($this->input->post('stock'));
            $desc = $this->security->xss_clean($this->input->post('string'));
            $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
            
              // check if medicine in stock is available for sale
            $stock_data = $this->stock_model->getStockByToken($stock);
            if(!empty($stock_data) || $stock == '10000001')
            {
              if($stock != '10000001')
              {
                if($stock_data->used >= $stock_data->total)
                {
                  echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Running out of stock</span>'));
                  exit();                    
                }
              }
              $desc = str_replace('$', '', $desc);
              $desc = str_replace('~', '', $desc);
              $desc = str_replace('+++', '', $desc);
              $desc = str_replace(' ', '$', $desc);
              $current_medicines = $symptoms_data['sy_medicines'];
              $to_be_replaced = $stock.'~null';
              $replacer = $stock.'~'.$desc;
              $new = str_replace($to_be_replaced, $replacer, $current_medicines);                
              $data = array(
                'sy_medicines' => $new,
              );
              $this->patient_model->update_patient_diseases($data, $symptom_id);
              echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>'));
              exit();
            }
            else
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">This medicine is not available in our stock</span>'));
              exit();
            }
          }
        }
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  
  public function delete_myclient_medicine($symptom_id, $record_id, $visit_id)
  {
    try
    {
      $check = $this->patient_model->verify_symptoms_record_id($symptom_id, $record_id);
      if(!$check)
      {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No matching records </span>'));
        exit();
      }
      else
      {
        $this->form_validation->set_rules('stock', 'Medicine Stock', 'trim|required');
        if ($this->form_validation->run() == FALSE)
        {
          echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
          exit();
        }
        else
        {
          $stock = $this->security->xss_clean($this->input->post('stock'));            
          $symptoms_data = $this->symptoms_data_by_pkid($symptom_id);
          if(empty($symptoms_data))
          {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">Invalid action </span>'));
            exit();
          }
          else
          {
            $current_medicines = $symptoms_data['sy_medicines'];
            if(empty($current_medicines))
            {
              echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger">No any added medicine </span>'));
              exit();
            }
            else
            {
              $current_medicines_to_array = explode("_", $current_medicines);
              foreach ($current_medicines_to_array as $key => $value)
              {
                $medicine_with_desc = $value;
                if(substr($medicine_with_desc, 0, strlen($stock)) === $stock)
                {
                  if (($key = array_search($medicine_with_desc, $current_medicines_to_array)) !== false)
                  {
                    unset($current_medicines_to_array[$key]);
                  }
                  $updated_medicines = implode("_", $current_medicines_to_array);
                  $updated_medicines = $updated_medicines != '' ? $updated_medicines : NULL;
                  
                  $data = array(
                    'sy_medicines' => $updated_medicines,
                  );
                  $this->patient_model->update_patient_diseases($data, $symptom_id);
                  echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success </span>'));
                  break;
                  exit();
                }
              }
            }
          } 
        }
      }
    }
    catch (\Throwable $th)
    {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();          
    }
  }
  
  public function release_patient($record_id)
  {
    try {
      $user_pf = $this->session->userdata('user_pf');
      $record_id = $this->security->xss_clean($record_id);
      
      if(!$this->isDoctorEligibleToRelease($record_id, $user_pf))
      {
        $this->session->set_flashdata('error', 'Please, fill in all important informations');
        return redirect(base_url('doctor/my-session'));
      }
      else
      {
        $data_from_visit_table = $this->visit_data_by_recordId($record_id);
        if(empty($data_from_visit_table))
        {
          $this->session->set_flashdata('error', 'Unfortunately, data mis-match occurred, please contact our administrator');
          return redirect(base_url('doctor/my-session'));
        }
        else
        {
          $visit_state = $data_from_visit_table['vs_visit'];
          $new_visit = '';

          if($visit_state == 'nipo_daktari_2r') $new_visit = 'nimetoka_daktari_r';
          else $new_visit = 'nimetoka_daktari';

          $data = array(
            'vs_visit' => $new_visit,
          );
          
          $is_released = $this->patient_model->update_patient_visit($data, $data_from_visit_table['vs_id']);
          if(!$is_released)
          {
            $this->session->set_flashdata('error', 'Oops!, an internal server error occurred, please contact our administrator');
            return redirect(base_url('doctor/my-session'));
          }
          else
          {
            $this->session->set_flashdata('success', 'Congrats!, you have successfully released a patient');          
            return redirect(base_url('doctor/patients'));          
              // return redirect(base_url('reports/client/'.$record_id)); 
          }
        }
      }     
    } catch (\Throwable $th) { 
      $this->session->set_flashdata('error', $th->getMessage());
      return redirect(base_url('doctor/my-session'));   
    }
  }
  
  public function search_medicines()
  { 
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      $this->form_validation->set_rules('query', 'Keyword', 'trim|required');
      
      if ($this->form_validation->run() == FALSE)
      {
        echo json_encode(array(''));
      }
      else 
      {        
        $keyword = $this->security->xss_clean($this->input->post('query'));
        echo json_encode($this->stock_model->get_Stock_Medicines($keyword));
      }
    }
  }

  public function lab_diagnostics()
  {
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      try{
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->investigation_model->list_of_lab_diagnosis($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
          $i++;
          
          $data[] = array(
            $i,
            $r->name,
            $r->code,
            $r->unit,
            empty($r->parent_alias) ? $r->parent : $r->parent . ' (' . $r->parent_alias . ')',
          );
        }
        
        $result = array(
          "draw" => $draw,
          "recordsTotal" => $this->investigation_model->countLabDiagnosis(),
          "recordsFiltered" => $this->investigation_model->countFilteredLabDiagnosis($this->input->get()),
          "data" => $data
        );
        
        echo json_encode($result);
        exit();
      }
      catch (\Throwable $th)
      {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit();          
      }
    }
    else
    {
      $data = array(
        'title' => $this->mainTitle,
        'header' => @$this->header,
        'heading' => 'Extra',
        'subHeading' => 'Lab Diagnostics',
      );
      $this->load->view('pages/doctor/lab_diagnostics', $data);
    }
  }
  
  public function edit_investigations()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => @$this->header,
      'heading' => 'Extra',
      'subHeading' => 'Edit Investigations',
    );
    
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      try
      {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->patient_model->get_list_of_editable_lab_diagnosis($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
          $i++;

          $editBtn = '<button type="button" name="editBtn" class="btn btn-sm btn-primary fw-bold" data-id="'.$r->record.'" data-name="'.$r->full_name.'" data-pf="'.$r->pf.'"> <i class="bi bi-pencil-square me-1"></i> Edit </button>';
          
          $data[] = array(
            $i,
            $r->full_name,
            $r->pf,
            $r->address,
            $r->gender,
            $r->entry,
            $editBtn,
          );
        }
        
        $result = array(
          "draw" => $draw,
          "recordsTotal" => $this->patient_model->count_All_Editable_Patients(),
          "recordsFiltered" => $this->patient_model->countFilteredEditablePatients($this->input->get()),
          "data" => $data
        );
        
        echo json_encode($result);
        exit();        
      } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit();
      }
    }
    $this->load->view('pages/doctor/edit_investigations', $data);
  }

  public function get_record_investigations($record)
  {
    try {
      $record = $this->security->xss_clean($record);
      $data_from_symptoms_table = $this->symptoms_data_by_record_id($record);
      $string = $data_from_symptoms_table['sy_investigations'];

      $exploded = explode("^^", $string);
      $ids = [];
      foreach ($exploded as $value){
        $sub = explode("~", $value);
        $ids[] = $sub[0];
      }

      $data = array(
        'categories' => $this->investigation_model->get_all_investigation_categories(),
        'subcategories' => $this->investigation_model->get_all_investigation_subcategories(),
        'posted' => $ids
      );

      echo json_encode(array("status" => TRUE, 'data' => $data));
      exit();
      
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();  
    }
  }
  
}
?>