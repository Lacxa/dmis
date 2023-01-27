<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lab extends CI_Controller {
  
  public $mainTitle = null;
  public $header = null;
  
  public function __construct()
  {
    parent::__construct();
    $this->load->database();
    $this->load->model(array("employee_model", "patient_model", "investigation_model"));
    $this->mainTitle  = 'DMIS | LAB';
    $this->header = 'Laboratory';
    $this->load->library(array("form_validation", "session"));
    $this->load->helper(array("url", "html", "form", "security", "date"));
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    
    $this->is_active();
    $this->is_lab();
    $this->is_first_login();
    $this->is_pwd_expired();
  }
  
  #########################################################
  # PRIVATE FUNCTIONS - TO BE ONLY ACCESSED WITHIN THIS CLASS
  ######################################################### 
  
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
  
  private function is_lab()
  {
    if($this->session->userdata('user_role') != 'LAB')
    {
      // return redirect($_SERVER['HTTP_REFERER']);
      return redirect(base_url('login'));
    }
  }
  
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
  
  private function get_employee_by_file_number($file_number)
  {
    $data = $this->employee_model->get_employee_by_file_number($file_number);
    if(!empty($data)) return $data;
    else return FALSE;
  }
  
  private function isEligibleToFillResults($record, $user_pf)
  {
    return $this->patient_model->isEligibleToFillResults($record, $user_pf);
  }
  
  private function patientRecord_data_by_pkid($id)
  {
    return $this->patient_model->patientRecord_data_by_pkid($id);
  }
  
  private function isEligibleToRelease($record, $user_pf)
  {
    return $this->patient_model->isEligibleToRelease($record, $user_pf);
  }
  
  private function isEligibleToReturn($record, $user_pf)
  {
    return $this->patient_model->isEligibleToReturn($record, $user_pf);
  }
  
  private function visit_data_by_recordId($record)
  {
    return $this->patient_model->visit_data_by_recordId($record);
  }
  
  private function symptoms_data_by_record_id($id)
  {
    return $this->patient_model->symptoms_data_by_record_id($id);
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

  
  ###########################################################
  #####     PRIVATE FUNCTIONS ENDS                 ##########
  ###########################################################
  
  public function index()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => $this->header,
      'heading' => 'Dashboard',
    );
    $this->load->view('pages/lab/dashboard', $data);        
  }

  public function ajax_count_patients()
  {
      echo json_encode(array("status" => TRUE, 'data' => $this->patient_model->countAllForLab()));
      exit();
  }
  
  public function my_patients()
  {
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      $data = [];
      
      $draw = intval($this->input->post("draw"));
      $start = intval($this->input->post("start"));
      $length = intval($this->input->post("length"));
      
      $result = $this->patient_model->get_my_patients_lab($this->input->post());
      
      $i = $this->input->post("start");
      foreach($result as $r)
      {
        $i++;
        $full_name = $r->pat_fname.' '.$r->pat_lname;
        $attendants = explode("_", $r->vs_attendants);
        $doctor_pf = $attendants[0];
        $doctor = $this->get_employee_by_file_number($doctor_pf);
        
        $my_date = $r->pat_dob;
        $date1 = date_create(date("Y-m-d", strtotime($my_date)));
        $date2 = date_create(date("Y-m-d"));        
        $diff = date_diff($date1, $date2);
        $age = abs($diff->format("%Y"));
        $bmi = $this->bmi_calculator($r->rec_weight, $r->rec_height);
        
        $pendingBtn = '<a type="button" class="btn btn-sm btn-warning fw-bold" 
        href="'.base_url('lab/patient-results-get/').$r->rec_id.'">Pending <i class="bi bi-eye-fill me-1"></i></a>';
        
        $serveBtn1 = '<button type="button" name="serveBtn" class="btn btn-sm btn-primary fw-bold" data-id="'.$r->rec_id.'" data-name="'.$full_name.'" data-pf="'.$r->rec_patient_file.'"> <i class="bi bi-box-arrow-in-right me-1"></i> Serve </button>';

        $serveBtn2 = '<button type="button" name="serveBtn" class="btn btn-sm btn-primary fw-bold" data-id="'.$r->rec_id.'" data-name="'.$full_name.'" data-pf="'.$r->rec_patient_file.'"> Serve <i class="bi bi-star me-1"></i><i class="bi bi-star me-1"></i> </button>';
        
        $is_pending = $r->vs_visit == 'nipo_lab' ? $pendingBtn : ($r->vs_visit == 'naenda_lab_r' ? $serveBtn2 : $serveBtn1);
        
        $data[] = array(
          $i,
          $full_name . ' <code>('. $r->pat_file_no .')</code>',
          $age,
          $r->pat_address,
          $this->get_timespan($r->rec_regdate),
          $is_pending,
          $r->rec_blood_pressure.'&nbsp;mmHg',
          $r->rec_pulse_rate.'&nbsp;bpm',
          $r->rec_weight.'&nbsp;kg',
          $r->rec_height.'&nbsp;cm',
          $r->rec_temeperature.'&deg;C',
          $r->rec_respiration.'&nbsp;bpm',
          $bmi,
          $doctor['emp_lname'].'&nbsp;(MD)',
        );
      }
      
      $result = array(
        "draw" => $draw,
        "recordsTotal" => $this->patient_model->countAllForLab(),
        "recordsFiltered" => $this->patient_model->countFilteredForLab($this->input->get()),
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
      $this->load->view('pages/lab/my_patients', $data); 
    }  
  }
  
  public function serve_patient($record_id)
  {
    $record = $this->security->xss_clean($record_id);
    $user_pf = $this->session->userdata('user_pf');
    $is_served = $this->patient_model->lab_serve_patient($record, $user_pf);
    if($is_served)
    {
      echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>', 'redirect' => base_url('lab/patient-results-get/'.$record)));
      exit();
    }
    else
    {
      echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, action failed</span>'));
      exit();
    }
  }
  
  public function patient_results_post($record_id)
  {
    $record = $this->security->xss_clean($record_id);
    $user_pf = $this->session->userdata('user_pf');
    
    if(!$this->isEligibleToFillResults($record, $user_pf))
    {
      echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> No access </span>'));
      exit();    
    }
    else
    {      
      $this->form_validation->set_rules('investigation', 'Investigation Component', 'trim|required|numeric');
      $this->form_validation->set_rules('results', 'Investigation Results', 'trim|required|max_length[200]');
      if($this->form_validation->run() == FALSE)
      {
        echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
        exit();
      }
      else
      {
        $investigation = $this->security->xss_clean($this->input->post('investigation'));
        $results = $this->security->xss_clean($this->input->post('results'));

        $uploadData = '';
        if(!empty($_FILES['file']['name']))
        {              
          // Set preference
          $config['upload_path'] = FCPATH.'uploads/investigations/'; 
          $config['allowed_types'] = 'jpg|jpeg|png|pdf';
          $config['max_size'] = 1024 * 10;
          $config['overwrite'] = TRUE;
          // $config['file_name'] = $_FILES['file']['name'];
          // $config['file_name'] = $new_name;
          $config['encrypt_name'] = TRUE;
          
          //Load upload library
          $this->load->library('upload', $config);
          
          // File upload
          if($this->upload->do_upload('file'))
          {
            // Get data about the file
            $uploadData = $this->upload->data();
          }
          else
          {
            echo json_encode(array("status" => FALSE, 'data' => $this->upload->display_errors()));
            exit();
          }          
        }        
        
        $inv_data = $this->patient_model->get_patient_lab_results_short($record);
        $current_inv = $inv_data['sy_investigations'];
      
        $results = str_replace('^^', '', $results);
        $results = str_replace('$$$', '', $results);
        $results = str_replace('@text', '', $results);
        $results = str_replace('@file', '', $results);
        $results = str_replace('~null', '', $results);
        $results = str_replace('~', '', $results);
        $results = str_replace('&&', '', $results);
        $results = str_replace(' ', '$$$', $results);
        $results = '@text:'.$results;

        $file = '@file:null';
        if(isset($uploadData['file_name']) && !empty($uploadData['file_name']))
        {
          $file = '@file:'.$uploadData['file_name'];
        }

        $formatted_string = $investigation . '~' . $results . '&&' . $file;
        $new_inv = str_replace($investigation.'~null', $formatted_string, $current_inv);
      
        $data = array(
          'sy_investigations' => $new_inv,
        );
        
        $this->patient_model->update_ivestigations($data, $inv_data['sy_id']);
        
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success</span>'));
        exit();
      }
    }    
  }

  public function reset_patient_results($record_id)
  {
    $record = $this->security->xss_clean($record_id);
    $inv_token = $this->security->xss_clean($this->input->post('token'));

    $inv_data = $this->patient_model->get_patient_lab_results_short($record);
    if(empty($inv_data))
    {
      echo json_encode(array("status" => FALSE, 'data' => '<code>Not found</code>'));
      exit();
    }
    else
    {
      $current_inv = $inv_data['sy_investigations'];
  
      $explode = explode("^^", $current_inv);
      $myArray = [];
      foreach ($explode as $key => $value)
      {
        if(strpos($value, $inv_token) === 0)
        {
          $new_str = $inv_token . '~null';
          $myArray[] = $new_str;
        }
        else
        {
          $myArray[] = $value;
        }          
      }
      $myArray = implode("^^", $myArray);
      
      $data = array(
        'sy_investigations' => $myArray,
      );
      $this->patient_model->update_ivestigations($data, $inv_data['sy_id']);
  
      echo json_encode(array("status" => TRUE, 'data' => '<code>Success</code>'));
      exit();
    }
  }
  
  public function patient_results_get($record_id)
  {
    $record = $this->security->xss_clean($record_id);
    $user_pf = $this->session->userdata('user_pf');
    // $user_pf = 'NIT/PF/0003';    
    if($this->isEligibleToFillResults($record, $user_pf))
    {
      $patient_data = $this->patientRecord_data_by_pkid($record);
      $inv_data = $this->patient_model->get_patient_lab_results($record_id, $user_pf);

      $new_inv_data = [];
      foreach ($inv_data as $key => $sub)
      {
        $parent = $sub['parent'];
        if(!array_key_exists($parent, $new_inv_data)){
          $new_inv_data[$parent] = [];
        }
        $new_inv_data[$parent][] = $sub;        
      }

      $data = array(
        'title' => $this->mainTitle,
        'header' => $this->header,
        'heading' => 'My Patients',
        'subHeading' => $patient_data['rec_patient_file'],
        'patient' => $patient_data,
        'diagnostics' => $new_inv_data,
        'investigations' => $inv_data,
        'user' => $this->session->userdata('user_pf'),
      );

      $this->load->view('pages/lab/patient_results', $data);
    }
    else
    {
      return redirect(base_url('lab/my-patients'));
    }    
  }

  public function patient_results_get2($record_id)
  {
    $inv_data = $this->patient_model->get_patient_lab_results($record_id, $this->session->userdata('user_pf'));
    $new_inv_data = [];
    foreach ($inv_data as $key => $sub)
    {
      $parent = $sub['parent'];
      if(!array_key_exists($parent, $new_inv_data))
      {
        $new_inv_data[$parent] = [];
      }
      $new_inv_data[$parent][] = $sub;        
    }
    echo json_encode(array("status" => TRUE, 'data' => $new_inv_data));
    exit();
  }
  
  public function release_patient($record_id)
  {
    $user_pf = $this->session->userdata('user_pf');
    $record_id = $this->security->xss_clean($record_id);
    if(!$this->isEligibleToRelease($record_id, $user_pf))
    {
      $this->session->set_flashdata('error', 'Please fill in all lab investigation results');
      return redirect(base_url('lab/patient-results-get/'.$record_id));
    }
    else
    {
      $data_from_visit_table = $this->visit_data_by_recordId($record_id);
      if(empty($data_from_visit_table))
      {
        $this->session->set_flashdata('error', 'Unfortunately, data mis-match occurred, please contact our administrator');
        return redirect(base_url('lab/patient-results-get/'.$record_id));
      }
      else
      { 
        $data = array(
          'vs_visit' => 'nimetoka_lab',
        );
        $is_released = $this->patient_model->update_patient_visit($data, $data_from_visit_table['vs_id']);
        if(!$is_released)
        {
          $this->session->set_flashdata('error', 'Oops!, an internal server error occurred, please contact our administrator');
          return redirect(base_url('lab/patient-results-get/'.$record_id));
        }
        else
        {
          $this->session->set_flashdata('success', 'Congrats!, you have successfully released a patient');
          return redirect(base_url('lab/my-patients'));          
        }
        
      }
    }
  }
  
  public function return_patient($record_id)
  {
    $user_pf = $this->session->userdata('user_pf');
    $record_id = $this->security->xss_clean($record_id);
    if(!$this->isEligibleToReturn($record_id, $user_pf))
    {
      $this->session->set_flashdata('error', 'Oops!, action not allowed');
      return redirect(base_url('lab/patient-results-get/'.$record_id));
    }
    else
    {
      $data_from_visit_table = $this->visit_data_by_recordId($record_id);
      $data_from_symptoms_table = $this->symptoms_data_by_record_id($record_id);
      if(empty($data_from_visit_table) || empty($data_from_symptoms_table))
      {
        $this->session->set_flashdata('error', 'Unfortunately, data mis-match occurred, please contact our administrator');
        return redirect(base_url('lab/patient-results-get/'.$record_id));
      }
      else
      { 
        $symptom_id = $data_from_symptoms_table['sy_id'];
        $current_inv = $data_from_symptoms_table['sy_investigations'];
        $inv_ids_arr = array();
        $inv_data = explode("^^", $current_inv);
        foreach($inv_data as $row)
        {
          $inv = explode("~", $row);
          $inv_ids_arr[] = $inv[0];
        }
        array_walk($inv_ids_arr, function(&$value, $key) { $value .= '~null'; } );
        $s_data = implode("^^", $inv_ids_arr);
        
        $v_data = array(
          'vs_visit' => 'nimerudishwa_kutoka_lab',
        );
        
        $this->patient_model->update_patient_investigations($s_data, $symptom_id);
        $this->patient_model->update_patient_visit($v_data, $data_from_visit_table['vs_id']);
        $this->session->set_flashdata('success', 'Congrats!, you have successfully returned a patient');
        return redirect(base_url('lab/my-patients'));        
      }
    }
  }
  
  public function reports()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => @$this->header,
      'heading' => 'Reports',
    );
    $this->load->view('pages/lab/reports', $data);
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
  
  public function patient_history()
  {
    $data = array(
      'title' => $this->mainTitle,
      'header' => @$this->header,
      'heading' => 'Patient History',
    );
    
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      try
      {
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
          echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, this patient is not available </span>'));
          exit();
        }
        else
        {
          $result = $this->patient_model->client_history_lab($pf, $start, $end);
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
    $this->load->view('pages/lab/patient_history', $data);
  }
  
  public function lab_diagnostics()
  {
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      try
      {
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
        'header' => $this->header,
        'heading' => 'Lab Diagnostics',
      );
      $this->load->view('pages/lab/lab_diagnostics', $data);
    }
  }
  
}