<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once 'Filenumber.php';

class Pharmacy extends CI_Controller {
    
    public $mainTitle = null;
    public $header = null;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->load->model(array("stock_model", "medicineCategory_model", "medicineFormat_model", "medicineUnit_model", "medicine_model", "patient_model", "employee_model"));
        $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
        $this->header = 'Pharmacy';
        $this->load->library(array("form_validation", "session", "pagination"));
        $this->load->helper(array("url", "html", "form", "security", "date"));
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
        
        $this->is_active();
        $this->is_pharmacist();
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
    
    private function is_active()
    {
        try
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
        catch (\Throwable $th)
        {
            $this->session->set_flashdata('error', $th->getMessage());
            redirect(base_url('login'));         
        }
    }
    
    private function is_pharmacist()
    {
        try
        {
            if($this->session->userdata('user_role') != 'PH')
            {
                // return redirect($_SERVER['HTTP_REFERER']);
                return redirect(base_url('login'));
            }
        } 
        catch (\Throwable $th)
        {
            $this->session->set_flashdata('error', $th->getMessage());
            redirect(base_url('login'));         
        }
    }
    
    public function checkTitleExist($title)
    {
        if ($this->medicineCategory_model->checkTitleExist($title) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkTitleExist', 'This title already exist!');
            return FALSE;
        }
    }
    
    public function checkTokenExist($token)
    {
        if ($this->medicineCategory_model->checkTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkTokenExist', 'This token already exist!');
            return FALSE;
        }
    }
    
    public function checkFormatNameExist($title)
    {
        if ($this->medicineFormat_model->checkFormatNameExist($title) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkFormatNameExist', 'This format title already exist!');
            return FALSE;
        }
    }
    
    public function checkMedicineUnitExist($title, $unit)
    {
        if ($this->medicineUnit_model->checkMedicineUnitExist($title, $unit) == FALSE) {
            return TRUE;
        } else {
            // $this->form_validation->set_message('checkMedicineUnitExist', 'Title and its unit already exist!');
            return FALSE;
        }
    }
    
    public function checkFormatTokenExist($token)
    {
        if ($this->medicineFormat_model->checkFormatTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkFormatTokenExist', 'This format token already exist!');
            return FALSE;
        }
    }
    
    public function checkMedicineUnitTokenExist($token)
    {
        if ($this->medicineUnit_model->checkMedicineUnitTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkMedicineUnitTokenExist', 'This token already exist!');
            return FALSE;
        }
    }
    
    public function checkMedicineTitleExist($title)
    {
        if ($this->medicine_model->checkTitleExist($title) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkMedicineTitleExist', 'This title already exist!');
            return FALSE;
        }
    }
    
    public function checkMedicineSlagExist($slag)
    {
        if ($this->medicine_model->checkSlagExist($slag) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkMedicineSlagExist', 'This medicine title slag already exist!');
            return FALSE;
        }
    }
    
    public function checkMedicineTokenExist($token)
    {
        if ($this->medicine_model->checkTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkMedicineTokenExist', 'This token already exist!');
            return FALSE;
        }
    }
    
    public function checkBatchToPost($token)
    {
        if ($this->stock_model->checkBatchToPost($token) == TRUE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkBatchToPost', 'This batch is not ready to be posted');
            return FALSE;
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
    
    private function checkIfUuidExist($uuid)
    {
        return $this->medicine_model->checkUuidExist($uuid);
    }
    
    private function checkIfStockUsageUuidExist($uuid)
    {
        return $this->stock_model->checkIfStockUsageUuidExist($uuid);
    }
    
    private function checkIfUuidExist2($uuid)
    {
        return $this->stock_model->checkUuidExist($uuid);
    }

    
    private function checkIfUuidBatchExist($uuid)
    {
        return $this->stock_model->checkIfUuidBatchExist($uuid);
    }
    
    private function checkIfBatchIDIsDraft($batch_id)
    {
        return $this->stock_model->checkIfBatchIDIsDraft($batch_id);
    }
    
    private function getMedicineByToken($token)
    {
        return $this->medicine_model->getMedicineByToken($token);
    }
    
    private function getMedicineCategoryByToken($token)
    {
        return $this->medicineCategory_model->get_category_by_token($token);
    }
    
    private function getMedicineFormatByToken($token)
    {
        return $this->medicineFormat_model->get_format_by_token($token);
    }
    
    private function generate_stock_code()
    {
        $stockCodeGenerator = new Filenumber();
        $stock_code = $stockCodeGenerator->generate();
        unset($stockCodeGenerator);
        $stock_code = str_replace('-', '', $stock_code);
        
        return $stock_code;
    }
    
    private function checkIfStockCodeExist($stock_code)
    {
        return $this->stock_model->checkIfStockCodeExist($stock_code);
    }
    
    private function checkIfBatchCodeExist($code)
    {
        return $this->stock_model->checkIfBatchCodeExist($code);
    }
    
    public function checkStockBatchTokenToDeleteExist($token)
    {
        if ($this->stock_model->checkStockBatchTokenToDeleteExist($token)) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkStockBatchTokenToDeleteExist', 'The batch you want to remove is not ready for this action');
            return FALSE;
        }
    }
    
    public function checkStockMedicineIDToDeleteExist($id)
    {
        if ($this->stock_model->checkStockMedicineIDToDeleteExist($id)) return TRUE;
        else return FALSE;
    }
    
    private function getBatchByToken($token)
    {
        return $this->stock_model->getBatchByToken($token);
    }
    
    private function getStockByToken($token)
    {
        return $this->stock_model->getStockByToken($token);
    }  
    
    private function getStockByID($id)
    {
        return $this->stock_model->getStockByID($id);
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
    
    private function isEligibleForPh($record, $user_pf)
    {
        return $this->patient_model->isEligibleForPh($record, $user_pf);
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
    
    private function symptoms_data_by_record_id($record)
    {
        return $this->patient_model->symptoms_data_by_record_id($record);
    }
    
    private function isPhEligibleToReturn($record, $user_pf)
    {
        return $this->patient_model->isPhEligibleToReturn($record, $user_pf);
    }
    
    private function isPhEligibleToRelease($record, $user_pf)
    {
        return $this->patient_model->isPhEligibleToRelease($record, $user_pf);
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
    ###########  PRIVATE METHODS ENDS  ########################
    ###########################################################
  
public function index()
  {
    $data = array(
        'title' => $this->mainTitle,
        'header' => $this->header,
        'heading' => 'Dashboard',
    );
    $this->load->view('pages/pharmacy/dashboard', $data); 
}

public function ajax_count_prescription()
{
    echo json_encode(array("status" => TRUE, 'data' => $this->patient_model->countAllForPh()));
    exit();
}

public function medicine_categories()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->medicineCategory_model->get_categories($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            $i++;
            $data[] = array(
                $i,
                $r->medcat_name,
                $r->medcat_token,
                date('F j, Y, g:i a', strtotime($r->medcat_regdate)),
                $r->medcat_author,
                !empty($r->medcat_description) ? $r->medcat_description : 'Not set',
            );
        }
        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->medicineCategory_model->countAll(),
            "recordsFiltered" => $this->medicineCategory_model->countFiltered($this->input->get()),
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
            'heading' => 'Configurations',
            'subHeading' => 'Medicine Categories',
        );
        $this->load->view('pages/pharmacy/medicine_categories', $data);
    }
}

public function save_medicine_categories()
{
    try{
        $this->form_validation->set_rules('title', 'Title', 'trim|required|callback_checkTitleExist');
        $this->form_validation->set_rules('token', 'Token', 'trim|required|numeric|callback_checkTokenExist');
        $this->form_validation->set_rules('desc', 'Descriptions', 'trim');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $title = $this->security->xss_clean($this->input->post('title'));
            $token = $this->security->xss_clean($this->input->post('token'));
            $desc = $this->security->xss_clean($this->input->post('desc'));
            
            $data = array(
                'medcat_name' => $title,
                'medcat_author' => $this->session->userdata('user_pf'),
                'medcat_token' => $token,
            );
            if(!empty($desc)) $data['medcat_description'] = $desc;
            $this->medicineCategory_model->save_categories($data);
            
            echo json_encode(array(
                "status" => TRUE,
                'data' => '<span class="text-success"> Added successifully</span>'
            ));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }      
}

public function medicine_formats()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->medicineFormat_model->get_formats($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            $i++;
            $data[] = array(
                $i,
                $r->format_name,
                $r->format_token,
                date('Y-m-d', strtotime($r->format_regdate)),
                $r->format_author,
                !empty($r->format_description) ? $r->format_description : 'Not set',
            );
        }
        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->medicineFormat_model->countAll(),
            "recordsFiltered" => $this->medicineFormat_model->countFiltered($this->input->get()),
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
            'heading' => 'Configurations',
            'subHeading' => 'Medicine Forms',
        );
        $this->load->view('pages/pharmacy/medicine_formats', $data);
    }
}

public function save_medicine_formats()
{
    try{
        $this->form_validation->set_rules('title', 'Format Name', 'trim|required|callback_checkFormatNameExist');
        $this->form_validation->set_rules('token', 'Format Token', 'trim|required|numeric|callback_checkFormatTokenExist');
        $this->form_validation->set_rules('desc', 'Descriptions', 'trim|max_length[300]');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $title = $this->security->xss_clean($this->input->post('title'));
            $token = $this->security->xss_clean($this->input->post('token'));
            $desc = $this->security->xss_clean($this->input->post('desc'));
            
            $data = array(
                'format_name' => $title,
                'format_author' => $this->session->userdata('user_pf'),
                'format_token' => $token,
            );
            if(!empty($desc)) $data['format_description'] = $desc;
            $this->medicineFormat_model->save_formats($data);
            
            echo json_encode(array(
                "status" => TRUE,
                'data' => '<span class="text-success"> Added successifully</span>'
            ));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }        
}

public function medicine_units()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->medicineUnit_model->get_units($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            $i++;
            $data[] = array(
                $i,
                $r->name,
                '<code>'.$r->unit.'</code>',
                $r->token,
                $r->author,
                date('Y-m-d', strtotime($r->createdAt)),
            );
        }
        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->medicineUnit_model->countAll(),
            "recordsFiltered" => $this->medicineUnit_model->countFiltered($this->input->get()),
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
            'heading' => 'Configurations',
            'subHeading' => 'Medicine Units',
        );
        $this->load->view('pages/pharmacy/medicine_units', $data);
    }
}

public function save_medicine_units()
{
    try{
        $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('unit', 'Unit', 'trim|required|max_length[20]');
        $this->form_validation->set_rules('token', 'Token', 'trim|required|numeric|max_length[100]|callback_checkMedicineUnitTokenExist');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $title = $this->security->xss_clean($this->input->post('title'));
            $token = $this->security->xss_clean($this->input->post('token'));
            $unit = $this->security->xss_clean($this->input->post('unit'));

            if(!$this->checkMedicineUnitExist($title, $unit))
            {
                echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> The title and its unit already exist!</span>'));
                exit();                    
            }
            else
            {
                $data = array(
                    'mu_name' => ucfirst($title),
                    'mu_unit' => $unit,
                    'mu_token' => $token,
                    'mu_author' => $this->session->userdata('user_pf'),
                );
                $this->medicineUnit_model->save_units($data);
                
                echo json_encode(array(
                    "status" => TRUE,
                    'data' => '<span class="text-success"> Added successifully</span>'
                ));
                exit();
            }
            
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }        
}

public function medicine_names()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->medicine_model->get_names($this->input->post());
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            // $i++;                
            $activeBtn = '<button type="button" class="btn btn-sm btn-primary" name="deactivateButton" data-id="'.$r->med_id.'" data-name="'.$r->med_name.'" title="Click to De-activate"><i class="bi bi-capsule"></i> Active</button>';
            $inactiveBtn = '<button type="button" class="btn btn-sm btn-danger" name="activateButton" data-id="'.$r->med_id.'" data-name="'.$r->med_name.'" title="Click to Activate"> Disabled </button>';

            $optionBtn = '<button type="button" name="editMedicine" data-id="'.$r->med_id.'" data-name="'.$r->med_name.'" title="Edit" class="btn btn-secondary btn-sm"><i class="bi bi-pencil-square"></i></button>';

            $data[] = array(
                // $i,
                $r->med_name,
                $r->med_alias,
                $r->med_token,
                $r->medcat_name,
                $r->format_name,
                $r->med_is_active == 1 ? $activeBtn : $inactiveBtn,
                $optionBtn,
                date('F j, Y, g:i a', strtotime($r->med_regdate)),
                $r->med_author,
                !empty($r->med_descriptions) ? $r->med_descriptions : 'Not set',
            );
        }
        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->medicine_model->countAll(),
            "recordsFiltered" => $this->medicine_model->countFiltered($this->input->get()),
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
            'heading' => 'Configurations',
            'subHeading' => 'Medicine List',
            'categories' => $this->medicineCategory_model->get_all_categories(),
            'formats' => $this->medicineFormat_model->get_all_formats(),
        );
        $this->load->view('pages/pharmacy/medicine_names', $data);
    }
}

public function save_medicine_names()
{
    try{
        $this->form_validation->set_rules('name', 'Title', 'trim|required|callback_checkMedicineTitleExist');
        $this->form_validation->set_rules('slag', 'Slag', 'trim|required|callback_checkMedicineSlagExist');
        $this->form_validation->set_rules('token', 'Token', 'trim|required|numeric|callback_checkMedicineTokenExist');
        $this->form_validation->set_rules('category', 'Category', 'trim|required|numeric');
        $this->form_validation->set_rules('format', 'Format', 'trim|required|numeric');
        $this->form_validation->set_rules('desc', 'Descriptions', 'trim');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $uuid = $this->uuid();
            while($this->checkIfUuidExist($uuid))
            {
                $uuid = $this->uuid();
            }
            
            $name = $this->security->xss_clean($this->input->post('name'));
            $slag = $this->security->xss_clean($this->input->post('slag'));
            $token = $this->security->xss_clean($this->input->post('token'));
            $category = $this->security->xss_clean($this->input->post('category'));
            $format = $this->security->xss_clean($this->input->post('format'));
            $desc = $this->security->xss_clean($this->input->post('desc'));
            
            $data = array(
                'med_id' => $uuid,
                'med_name' => strtoupper($name),
                'med_alias' => strtoupper($slag),
                'med_token' => $token,
                'med_category' => $category,
                'med_format' => $format,
                'med_author' => $this->session->userdata('user_pf'),
            );
            
            if(!empty($desc)) $data['med_descriptions'] = $desc;
            $this->medicine_model->save_medicines($data);
            
            echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Added successifully</span>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }        
}


public function medicine_state($state, $id)
{
    try {
        if($state >= 0 && $state < 2)
        {
            $med_info = $this->medicine_model->getMedicineById($id);
            if(!empty($med_info)) 
            {
                $related_stock = $this->stock_model->getLeafStockByMedicine($med_info['token']);
                $stock_ids = array();
                if(!empty((array) $related_stock))
                {
                    $stock_ids[] = $related_stock->id;
                }
                
                $this->medicine_model->setMedicineState($state, $id, $stock_ids);
                echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
                exit();
            }
            else
            {
                echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Medicine not found </span>'));
                exit();
            }
        }
        else
        {
            echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Invalid inputs </span>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function stock_register_get($record=0)
{
    $recordPerPage = 6;
	if($record != 0)
    {
        $record = ($record-1) * $recordPerPage;
	}

    $recordCount = $this->stock_model->getStockCount();
    $stockRecord = $this->stock_model->get_stock($record,$recordPerPage);
    $config['base_url'] = base_url().'pharmacy/get-stock-register/'.$record;
    $config['use_page_numbers'] = TRUE;
    $config['next_link'] = 'Next';
    $config['prev_link'] = 'Previous';
    $config['total_rows'] = $recordCount;
    $config['per_page'] = $recordPerPage;
    
    $config['full_tag_open'] = '<ul class="pagination justify-content-center mt-2">';        
    $config['full_tag_close'] = '</ul>';        
    $config['first_link'] = 'First';        
    $config['last_link'] = 'Last';        
    $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';        
    $config['first_tag_close'] = '</span></li>';        
    $config['prev_link'] = '&laquo';        
    $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';        
    $config['prev_tag_close'] = '</span></li>';        
    $config['next_link'] = '&raquo';        
    $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';        
    $config['next_tag_close'] = '</span></li>';        
    $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';        
    $config['last_tag_close'] = '</span></li>';        
    $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';        
    $config['cur_tag_close'] = '</a></li>';        
    $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';        
    $config['num_tag_close'] = '</span></li>';
    
    $this->pagination->initialize($config);
    $data['pagination'] = $this->pagination->create_links();
    $data['stockData'] = $stockRecord;
	echo json_encode($data);
    exit();    
}

public function stock_register()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = $this->stock_model->get_stock();                     
        echo json_encode($data);
        exit();
    }
    else
    {
        $data = array(
            'title' => $this->mainTitle,
            'header' => $this->header,
            'heading' => 'Medicine Stock',
            'link' => '<a href="'.base_url('pharmacy/stock-register').'">Medicine Stock</a>',
        );
        $this->load->view('pages/pharmacy/stock_register', $data);
    }
}

public function stock_get_sold($stock_token)
{
    $stock_token = $this->security->xss_clean($stock_token);
    $stock_details = $this->getStockByToken($stock_token);
    if(empty($stock_details))
    {
      $this->session->set_flashdata('error', 'oops!, information not found');
      return redirect($_SERVER['HTTP_REFERER']);
    }
    else
    {
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->stock_model->get_stock_consumption($this->input->post(), $stock_details->id);
        
        $i = $this->input->post("start");
        foreach($result as $r)
        {
            $i++;
            $data[] = array(
                $r->full_name,
                $r->file,
                $r->gender,
                $r->phone,
                $r->occupation,
                $r->entry,
                $r->consumption,
            );
        }        
        $result = array(
            "draw" => $draw,
            "recordsTotal" => $this->stock_model->count_all_stock_consumption($stock_details->id),
            "recordsFiltered" => $this->stock_model->countFilteredStockConsumption($this->input->get(), $stock_details->id),
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
            'heading' => strtoupper($stock_details->medicine1).' CONSUMERS',
            'link' => '<a href="'.base_url('pharmacy/stock-register').'">Medicine Stock</a>',
            'subHeading' => strtoupper($stock_details->medicine1).' Consumers',
            'details' => $stock_details,
        );
        $this->load->view('pages/pharmacy/stock_consumption', $data);
    }
}
}

public function get_medicines_by_category($category)
{
    try {
        $data = $this->medicine_model->get_medicines_by_category_form($category);
        if(!empty($data))
        {
            echo json_encode(array("status" => TRUE, 'data' => $data));
            exit();
        }
        else
        {
            echo json_encode(array("status" => FALSE, 'data' => '<code> Oops!, no any found medicine</code>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function get_medicines_by_category_form($category, $form)
{
    try {
        $data = $this->medicine_model->get_medicines_by_category_form($category, $form);
        if(!empty($data))
        {
            echo json_encode(array("status" => TRUE, 'data' => $data));
            exit();
        }
        else
        {
            echo json_encode(array("status" => FALSE, 'data' => '<code> Oops!, no any found medicine</code>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function showChildStock($data)
{
    $html = ' <ul class="list-group">';
    foreach($data as $child_key => $child)
    {
        if(empty($child->sub) && $child->st_level == 100)
        {
            $html .= '<li class="list-group-item d-flex justify-content-between align-items-center">';
            $medicine_data = $this->getMedicineByToken($child->st_medicine);
            $html .= '<span><span><i class="bi bi-capsule text-danger"></i></span>&nbsp;'.$medicine_data['text'].'<span class="badge bg-light text-dark">#'.$child->st_code.'</span> ('.$child->st_title.') </span><span class="badge bg-primary rounded-pill">'.$child->st_total.'</span>';
                // $html .= ''.$child->st_title.'';
            $html .= '</li>';
        }
        else
        {
            $customTitle = 'Not defined';
            if($child->st_level == 1)
            {
                $category_details = $this->getMedicineCategoryByToken($child->st_title);
                $customTitle = $category_details->title;
            }
            else if($child->st_level == 2)
            {
                $format_details = $this->getMedicineFormatByToken($child->st_title);
                $customTitle = $format_details->title;
            }
            $html .= '<li class="list-group-item">';
            $html .= '<div class="accordion" id="stockAccordion_'.$child->st_id.'">';
            
            $html .= '<div class="accordion-item">';
            $html .= '<h2 class="accordion-header" id="heading_'.$child->st_id.'">';
            $html .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_'.$child->st_id.'" aria-expanded="true" aria-controls="collapseOne"><i class="bi bi-folder"></i>&nbsp;'.$customTitle.'&nbsp;<span class="badge bg-light text-dark">#'.$child->st_code.'</span></button>';
            $html .= '</h2>';
            $html .= '<div id="collapse_'.$child->st_id.'" class="accordion-collapse collapse" aria-labelledby="heading_'.$child->st_id.'" data-bs-parent="#stockAccordion_'.$child->st_id.'">';
            $html .= '<div class="accordion-body">';
            if(!empty($child->sub))
            {
                $html .= $this->showChildStock($child->sub);
            }
            else
            {
                $html .= 'No any added stock';
            }
            
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            
            $html .= '</div>';
            $html .= '</li>';
        }
    }
    
    $html .= ' </ul>';
    return $html;
}

public function create_new_batch()
{
    try{
        $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required|max_length[100]');
        $this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required');
        $this->form_validation->set_rules('description', 'Descriptions', 'trim|max_length[300]');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $user_pf = $this->session->userdata('user_pf');
            $uuid = $this->uuid();
            while($this->checkIfUuidBatchExist($uuid))
            {
                $uuid = $this->uuid();
            }

            $token = $this->generate_stock_code();
            while($this->checkIfBatchCodeExist($token))
            {
                $token = $this->generate_stock_code();
            }
            
            $supplier = $this->security->xss_clean($this->input->post('supplier'));
            $entry_date = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('entry_date'))));
            $description = $this->security->xss_clean($this->input->post('description'));
            
            $insert_data = [
                'sb_id' => $uuid,
                'sb_number' => $token,
                'sb_supplier' => $supplier,
                'sb_entry_date' => $entry_date,
                'sb_author' => $user_pf,
                'sb_active' => 0,
            ];              
            if(!empty($description)) $insert_data['sb_descrptions'] = $description;
            
            $this->stock_model->save_batch($insert_data);
            
            echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Created successifully</span>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }    
}

public function post_stock()
{
    try {
        $this->form_validation->set_rules('token', 'Batch Token', 'trim|required|exact_length[8]|callback_checkBatchToPost');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $batch = $this->security->xss_clean($this->input->post('token'));
            $batch_data = $this->getBatchByToken($batch);
            
            $ids = [];
            $ids[] = $batch_data->id;
            $data = array(
                'sb_active' => 1,
            );
            $this->stock_model->updateBatchArray($ids, $data);
            echo json_encode(array("status" => TRUE, 'data' => '<code> Posted successifully </code>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function remove_stock()
{
    try {
        $this->form_validation->set_rules('token', 'Batch Token', 'trim|required|exact_length[8]|callback_checkStockBatchTokenToDeleteExist');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $token = $this->security->xss_clean($this->input->post('token'));
            $batch_data = $this->getBatchByToken($token);                
            $batch_medicines = $this->stock_model->get_batch_medicines($batch_data->id);
            if(empty($batch_medicines))
            {  
                $this->stock_model->delete_batch_by_id($batch_data->id);
                echo json_encode(array("status" => TRUE, 'data' => '<code> Removed successifully </span>'));
                exit();          
            }
            else
            {  
                $this->stock_model->delete_batch_by_id($batch_data->id);
                $ids = array_column($batch_medicines, 'id');
                $this->stock_model->deleteArray($ids);
                echo json_encode(array("status" => TRUE, 'data' => '<code> Removed successifully </code>'));
                exit();
            }
            
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function remove_stock_medicine($id)
{
    try
    {
        $id = $this->security->xss_clean($id);
        if (!$this->checkStockMedicineIDToDeleteExist($id))
        {
            echo json_encode(array("status" => FALSE , 'data' => '<code>Not available for this action</code>'));
            exit();
        }
        else
        {
            $this->stock_model->delete_by_id($id);
            echo json_encode(array("status" => TRUE, 'data' => '<code>Removed successifully</code>'));
            exit();           
        }
    } 
    catch (\Throwable $th)
    {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function get_draft_batches()
{
    try {
        $data = $this->stock_model->get_draft_batches();
        if(!empty($data))
        {
            echo json_encode(array("status" => TRUE, 'data' => $data));
            exit();
        }
        else 
        {
            echo json_encode(array("status" => FALSE, 'data' => '<code> No any draft batch</span>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    } 
}

public function get_stock_settings()
{
    $data = array(
        'categories' => $this->medicineCategory_model->get_all_categories(),
        'forms' => $this->medicineFormat_model->get_all_formats(),
        'units' => $this->medicineUnit_model->get_all_units());
    echo json_encode(array("status" => TRUE, 'data' => $data));
    exit();
}     

public function get_stock_paths($stock_id)
{
    try {
        $id = $this->security->xss_clean($stock_id);
        $paths = $this->stock_model->get_stock_paths($id);
        if(!empty($paths))
        {
            echo json_encode(array("status" => TRUE, 'data' => $paths));
            exit();
        }
        else
        {
            echo json_encode(array("status" => FALSE, 'data' => '<span class="text-success"> No data</span>'));
            exit();
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }        
}

public function save_medicine_to_stock()
{
    try{
        $this->form_validation->set_rules('stock', 'Batch', 'trim|required|max_length[40]');
        $this->form_validation->set_rules('medicine', 'Medicine', 'trim|required|numeric');
        $this->form_validation->set_rules('unit', 'Unit', 'trim|required|numeric');
        $this->form_validation->set_rules('unit_value', 'Unit Value', 'trim|required|max_length[50]');
        $this->form_validation->set_rules('total', 'Total Count', 'trim|required|less_than_equal_to[500000]');
        
        if ($this->form_validation->run() == FALSE)
        {
            echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
            exit();
        }
        else
        {
            $batch = $this->security->xss_clean($this->input->post('stock'));
            $medicine = $this->security->xss_clean($this->input->post('medicine'));
            $unit = $this->security->xss_clean($this->input->post('unit'));
            $unit_value = $this->security->xss_clean($this->input->post('unit_value'));
            $total = $this->security->xss_clean($this->input->post('total'));

            $is_available_and_draft = $this->checkIfBatchIDIsDraft($batch);
            if(!$is_available_and_draft)
            {
                echo json_encode(array("status" => FALSE , 'data' => '<code>This batch is not available for this action</code>'));
                exit();
            }
            else
            {
                $uuid = $this->uuid();
                while($this->checkIfUuidExist2($uuid))
                {
                    $uuid = $this->uuid();
                }
                $stock_code = $this->generate_stock_code();
                while($this->checkIfStockCodeExist($stock_code))
                {
                    $stock_code = $this->generate_stock_code();
                }
                
                $data = [
                    'st_id' => $uuid,
                    'st_code' => $stock_code,
                    'st_batch' => $batch,
                    'st_medicine' => $medicine,
                    'st_unit' => $unit,
                    'st_unit_value' => $unit_value,
                    'st_total' => $total,
                    'st_author' => $this->session->userdata('user_pf'),
                ];
                
                $this->stock_model->save_stock($data);
                echo json_encode(array("status" => TRUE, 'data' => '<code> Added successifully</code>'));
                exit();
            }
        }
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit(); 
    }
}

public function stock_status()
{
    try
    {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $data = [];
        
            $draw = intval($this->input->post("draw"));
            $start = intval($this->input->post("start"));
            $length = intval($this->input->post("length"));
        
            $result = $this->stock_model->getStockStatus($this->input->post());
        
            $i = $this->input->post("start");
            foreach($result as $r)
            {
                $available = $r->total - $r->used;
                $percent = ($available * 100 / $r->total);
                $percent = number_format((float)$percent, 1, '.', '');
                $color = $percent <= 20 ? 'danger' : ($percent <= 50 ? 'warning' : 'success');
                $data[] = array(
                    '<a href="#">' . $r->sn .'</a',
                    $r->medicine,
                    $r->category,
                    $r->form,
                    $r->unit,
                    // $r->total,
                    $available,
                    '<span class="badge bg-' . $color .'">' . $percent . '%</span>',
                );
            }
        
            $result = array(
                "draw" => $draw,
                "recordsTotal" => $this->stock_model->count_all_stock_status(),
                "recordsFiltered" => $this->stock_model->countFilteredStockStatus($this->input->get()),
                "data" => $data
            );
            echo json_encode($result);
            exit();
        }        
    } catch (\Throwable $th) {
        echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
        exit();
    }
}

public function patients()
{
    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
        $data = [];
        
        $draw = intval($this->input->post("draw"));
        $start = intval($this->input->post("start"));
        $length = intval($this->input->post("length"));
        
        $result = $this->patient_model->get_my_patients_ph($this->input->post());
        
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
            
            $pendingBtn = '<a type="button" class="btn btn-sm btn-warning" 
            href="'.base_url('pharmacy/patient-prescriptions-get/').$r->rec_id.'">
            <i class="bi bi-eye-fill me-1"></i> Pending</a>';
            
            $serveBtn = '<button type="button" name="serveBtn" class="btn btn-sm btn-primary" 
            data-id="'.$r->rec_id.'" data-name="'.$full_name.'" data-pf="'.$r->rec_patient_file.'">
            <i class="bi bi-check-circle me-1"></i> Serve </button>';
            
            $is_pending = $r->vs_visit == 'nipo_ph' ? $pendingBtn : $serveBtn;
            
            $data[] = array(
                $i,
                $full_name . ' <code>('. $r->pat_file_no .')</code>',
                $age,
                $r->pat_address,
                $is_pending,
                $this->get_timespan($r->rec_regdate),
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
            "recordsTotal" => $this->patient_model->countAllForPh(),
            "recordsFiltered" => $this->patient_model->countFilteredForPh($this->input->get()),
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
            'heading' => 'Prescriptions',
        );
        $this->load->view('pages/pharmacy/patients', $data); 
    } 
}

public function serve_patient($record_id)
{        
    $record = $this->security->xss_clean($record_id);
    $user_pf = $this->session->userdata('user_pf');
    $is_served = $this->patient_model->ph_serve_patient($record, $user_pf);
    if($is_served)
    {
        $url = base_url('pharmacy/patient-prescriptions-get/'.$record);
        echo json_encode(array("status" => TRUE, 'data' => $url));
        exit();
    }
    else
    {
        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, action failed</span>'));
        exit();
    }
}

public function patient_prescriptions_get($record_id)
{        
    $record = $this->security->xss_clean($record_id);
    $user_pf = $this->session->userdata('user_pf');
        // $user_pf = 'NIT/PF/0003';    
    if($this->isEligibleForPh($record, $user_pf))
    {
        $patient_data = $this->patientRecord_data_by_pkid($record);
        $prescriptions = $this->patient_model->get_patient_ph_results($record_id, $user_pf);
        $data = array(
            'title' => $this->mainTitle,
            'header' => $this->header,
            'heading' => 'Prescription',
            'subHeading' => $patient_data['rec_patient_file'],
            'patient' => $patient_data,
            'prescriptions' => $prescriptions,
            'user' => $this->session->userdata('user_pf'),
        );
        $this->load->view('pages/pharmacy/prescriptions', $data);
    }
    else
    {
        return redirect(base_url('pharmacy/patients'));
    }
}

public function save_prescriptions($record_id)
{
    try{  
        $record = $this->security->xss_clean($record_id);
        $user_pf = $this->session->userdata('user_pf');
        
        if($this->isEligibleForPh($record, $user_pf))
        {
            $this->form_validation->set_rules('action', 'Action', 'trim|required|numeric|in_list[1,2]');
            $this->form_validation->set_rules('patient', 'Patient', 'trim|required');
            
            if ($this->form_validation->run() == FALSE)
            {
                $this->patient_prescriptions_get($record);
            }
            else
            {
                $this->form_validation->set_rules('stock_id', 'Medicine ID', 'trim|required');
                $this->form_validation->set_rules('token', 'Medicine Token', 'trim|required');
                
                if ($this->form_validation->run() == FALSE)
                {
                    $this->patient_prescriptions_get($record);
                }
                else
                {
                    $id = $this->security->xss_clean($this->input->post('stock_id'));
                    $token = $this->security->xss_clean($this->input->post('token'));
                    $action = $this->security->xss_clean($this->input->post('action'));
                    $patient = $this->security->xss_clean($this->input->post('patient'));
                    if($action == 1)
                    {
                        $max = $this->security->xss_clean($this->input->post('i_max'));
                        $max = (int) $max;
                        if($max > 0)
                        {
                            $this->form_validation->set_rules('count', 'Medicine Count', 'trim|required|less_than_equal_to['.$max.']|numeric');
                            if ($this->form_validation->run() == FALSE)
                            {
                                $this->patient_prescriptions_get($record);
                            }
                            else
                            {
                                $count = $this->security->xss_clean($this->input->post('count'));
                                
                                $record_data = $this->symptoms_data_by_record_id($record);
                                $symptoms = $record_data['sy_medicines'];
                                $substr = $token.'~';
                                $attachment = '+++';
                                $newstring = str_replace($substr, $substr.$attachment, $symptoms);
                                $new_medicine_data = array(
                                    'sy_medicines' => $newstring,
                                );
                                
                                // $visit_data = $this->visit_data_by_recordId($record);
                                // $new_visit_data = array('vs_visit' => 'nimetoka_ph',);
                                
                                $stock = $this->getStockByID($id);
                                $search = array(
                                    'except' => $stock->id,
                                    'medicine_token' => $stock->medicine_token,
                                    'unit_token' => $stock->unit_token,
                                );
                                $similar_stock = $this->stock_model->getSimilarStock($search);
                                $similar_stock[] = $stock;
                                function cmp($a, $b)
                                {
                                    return strcmp($a->entry2, $b->entry2);
                                }
                                usort($similar_stock, "cmp");

                                $count_copy = $count;

                                $usage_increment = [];
                                $usage_history = [];

                                foreach ($similar_stock as $key => $row)
                                {
                                    $available = $row->total - $row->used;
                                    if($count_copy > 0)
                                    {
                                        $uuid = $this->uuid();
                                        while($this->checkIfStockUsageUuidExist($uuid))
                                        {
                                            $uuid = $this->uuid();
                                        }
                                        if($available >= $count_copy)
                                        {
                                            $usage_increment[] = array(
                                                'st_id' => $row->id,
                                                'st_usage' => $row->used + $count_copy
                                            );
                                            $usage_history[] = array(
                                                'su_id' => $uuid,
                                                'su_record' => $record,
                                                'su_stock' => $row->id,
                                                'su_usage' => $count_copy,
                                            );
                                            // echo 'Found: '.$available.'<br>';
                                            $count_copy = 0;
                                            // echo 'Ended looping on: '.$count_copy.'<br>';
                                            break;
                                        }
                                        else
                                        {
                                            $usage_increment[] = array(
                                                'st_id' => $row->id,
                                                'st_usage' => $row->used + $available
                                            );
                                            $usage_history[] = array(
                                                'su_id' => $uuid,
                                                'su_record' => $record,
                                                'su_stock' => $row->id,
                                                'su_usage' => $available,
                                            );
                                            // echo 'Found: '.$available.'<br>';
                                            $count_copy -= $available;
                                            // echo 'Left: '.$count_copy.'<br>';
                                        }
                                    }
                                }

                                // echo '<pre>';
                                // // print_r($similar_stock);
                                // // echo '<br>';
                                // print_r($usage_increment);
                                // echo '<br>';
                                // print_r($usage_history);
                                // exit();




                                // $usage = (int) $stock->used;
                                // $usage = $usage + $count;
                                
                                // $clients = $stock->clients;
                                // $clients = $clients == NULL ? $patient.'+'.$count : $clients.','.$patient.'+'.$count;
                                
                                // $currTime = time();
                                // $sold_time = $stock->sold_time;
                                // $sold_time = $sold_time == NULL ? $currTime : $sold_time.'_'.$currTime;
                                
                                // $new_stock_data = array(
                                //     'st_usage' => $usage
                                // );

                                // $uuid = $this->uuid();
                                // while($this->checkIfStockUsageUuidExist($uuid))
                                // {
                                //     $uuid = $this->uuid();
                                // }

                                // $usage_records = array(
                                //     'su_id' => $uuid,
                                //     'su_record' => $record,
                                //     'su_stock' => $id,
                                //     'su_usage' => $count,
                                // );
                                
                                $this->stock_model->update_stock_batch($usage_increment);
                                $this->stock_model->save_stock_usage_array($usage_history);
                                $this->patient_model->update_patient_diseases($new_medicine_data, $record_data['sy_id']);
                                
                                // $this->patient_model->update_patient_visit($new_visit_data, $visit_data['vs_id']);
                                
                                $this->session->set_flashdata('success', 'Success');
                                return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
                            }
                        }
                        else
                        {
                            $this->session->set_flashdata('error', 'Missing data');
                            return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
                        }
                    }
                    else
                    {
                        $this->form_validation->set_rules('text', 'Prescriptions', 'trim|required');
                        if ($this->form_validation->run() == FALSE)
                        {
                            $this->patient_prescriptions_get($record);
                        }
                        else
                        {
                            $med_string = $this->security->xss_clean($this->input->post('text'));
                            $record_data = $this->symptoms_data_by_record_id($record);
                            $medicines = $record_data['sy_medicines'];
                            $medArray = explode("_", $medicines);
                            $to_replace = '';
                            foreach ($medArray as $key => $value)
                            {
                                $medStr = explode("~", $value);
                                if($medStr[0] == $token)
                                {
                                    $to_replace = $value;
                                    break;
                                }
                            }
                            if($to_replace != '')
                            {
                                $med_string = str_replace('+++', '', $med_string);
                                $med_string = str_replace(' ', '$', $med_string);
                                $replacer = '10000001~'.$med_string;
                                $final_str = str_replace($to_replace, $replacer, $medicines);
                                
                                $record_data = $this->symptoms_data_by_record_id($record); $new_medicine_data = array(
                                    'sy_medicines' => $final_str,
                                );
                                $this->patient_model->update_patient_diseases($new_medicine_data, $record_data['sy_id']);
                                
                                $this->session->set_flashdata('success', 'Success');
                                return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
                            }
                            else 
                            {
                                $this->session->set_flashdata('error', 'Missing inputs');
                                return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
                            }                                
                        }
                    }
                }
            }
        }
        else
        {
            $this->session->set_flashdata('error', 'No access');
            return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
        }
    } catch (\Throwable $th) {
        $this->session->set_flashdata('error', 'Internal server error:, '.$th->getMessage());
        return redirect(base_url('pharmacy/patient-prescriptions-get/').$record);
    }
}

public function return_patient($record_id)
{
    $user_pf = $this->session->userdata('user_pf');
    $record_id = $this->security->xss_clean($record_id);
    if(!$this->isPhEligibleToReturn($record_id, $user_pf))
    {
        $this->session->set_flashdata('error', 'Oops!, action not allowed');
        return redirect(base_url('pharmacy/patient-prescriptions-get/'.$record_id));
    }
    else
    {
        $data_from_visit_table = $this->visit_data_by_recordId($record_id);
        $data_from_symptoms_table = $this->symptoms_data_by_record_id($record_id);
        if(empty($data_from_visit_table) || empty($data_from_symptoms_table))
        {
            $this->session->set_flashdata('error', 'Unfortunately, data mis-match occurred, please contact our administrator');
            return redirect(base_url('pharmacy/patient-prescriptions-get/'.$record_id));
        }
        else
        {
            $restore_usage_data = [];
            $pre_usage = $this->stock_model->getStockUsageByRecord($record_id);
            if(!empty($pre_usage))
            {
                foreach ($pre_usage as $key => $value)
                {                   
                    $restore_usage_data[] = array(
                        'id' => $value->stock,
                        'restore' => $value->used,
                    );
                }
            }

            $v_data = array(
                'vs_visit' => 'nimerudishwa_kutoka_ph',
            );

            $this->patient_model->update_patient_visit($v_data, $data_from_visit_table['vs_id']);
            if(!empty($restore_usage_data)) $this->stock_model->restore_stock_usage($restore_usage_data);
            $this->session->set_flashdata('success', 'Congrats!, you have successfully returned a patient');
            return redirect(base_url('pharmacy/patients'));        
        }
    }
}

public function release_patient($record_id)
{
    try {
        $user_pf = $this->session->userdata('user_pf');
        $record_id = $this->security->xss_clean($record_id);
        if(!$this->isPhEligibleToRelease($record_id, $user_pf))
        {
            $this->session->set_flashdata('error', 'Please clear all prescriptions');
            return redirect(base_url('pharmacy/patient-prescriptions-get/'.$record_id));
        }
        else
        {
            $data_from_visit_table = $this->visit_data_by_recordId($record_id);
            if(empty($data_from_visit_table))
            {
                $this->session->set_flashdata('error', 'Unfortunately, data mis-match occurred, please contact our administrator');
                return redirect(base_url('pharmacy/patient-results-get/'.$record_id));
            }
            else
            { 
                $data = array(
                    'vs_visit' => 'nimetoka_ph',
                );
                $is_released = $this->patient_model->update_patient_visit($data, $data_from_visit_table['vs_id']);
                if(!$is_released)
                {
                    $this->session->set_flashdata('error', 'Oops!, an internal server error occurred, please contact our administrator');
                    return redirect(base_url('pharmacy/patient-results-get/'.$record_id));
                }
                else
                {
                    $this->session->set_flashdata('success', 'Congrats!, you have successfully released a patient');
                    return redirect(base_url('pharmacy/patients'));          
                }
                
            }
        }
    } catch (\Throwable $th) {
        $this->session->set_flashdata('error', $th->getMessage());
        return redirect(base_url('pharmacy/patient-results-get/'.$record_id));     
    }
}

public function patient_history($patient_id, $record=0)
{
    $patient = $this->security->xss_clean($patient_id);
    $patient_data = $this->patient_model->get_patient_by_id($patient);
    if(empty($patient_data))
    {
        echo json_encode(array("status" => FALSE, 'data' => '<code>No such record</code>'));
        exit();
    }
    else
    {    
        $recordPerPage = 6;
        if($record != 0)
        {
            $record = ($record-1) * $recordPerPage;
        }
        $recordCount = $this->patient_model->patient_history_ph_count($patient);
        $results = $this->patient_model->patient_history_ph($record, $recordPerPage, $patient);
        $config['base_url'] = base_url().'pharmacy/patient-history/'.$patient.'/'.$record;
        $config['use_page_numbers'] = TRUE;
        $config['next_link'] = 'Next';
        $config['prev_link'] = 'Previous';
        $config['total_rows'] = $recordCount;
        $config['per_page'] = $recordPerPage;
        $config['num_links'] = 5;
        
        $config['full_tag_open'] = '<ul class="pagination justify-content-center mt-2">';        
        $config['full_tag_close'] = '</ul>';        
        $config['first_link'] = 'First';        
        $config['last_link'] = 'Last';        
        $config['first_tag_open'] = '<li class="page-item"><span class="page-link">';        
        $config['first_tag_close'] = '</span></li>';        
        $config['prev_link'] = '&laquo';        
        $config['prev_tag_open'] = '<li class="page-item"><span class="page-link">';        
        $config['prev_tag_close'] = '</span></li>';        
        $config['next_link'] = '&raquo';        
        $config['next_tag_open'] = '<li class="page-item"><span class="page-link">';        
        $config['next_tag_close'] = '</span></li>';        
        $config['last_tag_open'] = '<li class="page-item"><span class="page-link">';        
        $config['last_tag_close'] = '</span></li>';        
        $config['cur_tag_open'] = '<li class="page-item active"><a class="page-link" href="#">';        
        $config['cur_tag_close'] = '</a></li>';        
        $config['num_tag_open'] = '<li class="page-item"><span class="page-link">';        
        $config['num_tag_close'] = '</span></li>';
        
        $this->pagination->initialize($config);
        $data['pagination'] = $this->pagination->create_links();
        $data['historyData'] = $results;
        echo json_encode(array("status" => TRUE, 'data' => $data));
        exit();
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
    $this->load->view('pages/pharmacy/reports', $data);
}

}
?>