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
        $this->load->model(array("stock_model", "medicineCategory_model", "medicineFormat_model", "medicine_model", "patient_model", "employee_model"));
        $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
        $this->header = 'Pharmacy';
        $this->load->library(array("form_validation", "session"));
        $this->load->helper(array("url", "html", "form", "security", "date"));
        $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
        
        $this->is_active();
        $this->is_pharmacist();
        $this->is_pwd_expired();
    }
    
    #########################################################
    # PRIVATE FUNCTIONS - TO BE ONLY CALLED WITHIN THIS CLASS
    #########################################################
    
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
        if($days >= 90) return redirect(base_url('password/expired/0'), 'refresh');
        
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
                $this->session->set_flashdata('error', 'Oops!, your account is not active');
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
    
    public function checkFormatTokenExist($token)
    {
        if ($this->medicineFormat_model->checkFormatTokenExist($token) == FALSE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkFormatTokenExist', 'This format token already exist!');
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
    
    public function checkTokenPost($token)
    {
        if ($this->stock_model->checkTokenPost($token) == TRUE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkTokenPost', 'This stock can not be posted');
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
    
    private function checkIfUuidExist2($uuid)
    {
        return $this->stock_model->checkUuidExist($uuid);
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
    
    public function checkTokenToDeleteExist($token)
    {
        if ($this->checkIfStockCodeExist($token) == TRUE) {
            return TRUE;
        } else {
            $this->form_validation->set_message('checkTokenToDeleteExist', 'The stock token you want to remove does not exist!');
            return FALSE;
        }
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
                'subHeading' => 'Medicine Formats',
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
                $i++;                
                $activeBtn = '<button type="button" class="btn btn-sm btn-primary" name="deactivateButton" data-id="'.$r->med_id.'" data-name="'.$r->med_name.'" title="Click to De-activate"><i class="bi bi-capsule"></i> Active</button>';
                $inactiveBtn = '<button type="button" class="btn btn-sm btn-danger" name="activateButton" data-id="'.$r->med_id.'" data-name="'.$r->med_name.'" title="Click to Activate"> Disabled </button>';
                $data[] = array(
                    $i,
                    $r->med_name.'&nbsp;('.$r->med_alias.')',
                    $r->med_token,
                    $r->medcat_name,
                    $r->format_name,
                    $r->med_is_active == 1 ? $activeBtn : $inactiveBtn,
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
                'subHeading' => 'Medicine Names',
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
    
    public function stock_register()
    {
        if($this->input->server('REQUEST_METHOD') === 'POST')
        {
            $data = $this->stock_model->get_stock();
            $html = '';
            if(empty($data))
            {
                $html .= '<div class="alert alert-warning alert-dismissible fade show" role="alert"><i class="bi bi-info-circle me-1"></i>Oops!, stock is empty!</div>';
            }
            else 
            {
                $html .= '<div class="accordion" id="stockAccordion">';
                
                foreach ($data as $parent_key => $parent)
                {
                    $active = $parent->st_is_active == 0 ? '&nbsp;<span class="badge bg-danger">Draft</span>': '';
                    $html .= '<div class="accordion-item">';
                    $html .= '<h2 class="accordion-header" id="heading_'.$parent->st_id.'">';
                    $html .= '<button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse_'.$parent->st_id.'" aria-expanded="true" aria-controls="collapseOne"><i class="bi bi-collection"></i>&nbsp;'.$parent->st_title.'&nbsp;<span class="badge bg-light text-dark">#'.$parent->st_code.'</span>'.$active.'&nbsp;('.date('Y-m-d',strtotime($parent->st_entry_date)).')</button>';
                    $html .= '</h2>';
                    $html .= '<div id="collapse_'.$parent->st_id.'" class="accordion-collapse collapse" aria-labelledby="heading_'.$parent->st_id.'" data-bs-parent="#stockAccordion">';
                    $html .= '<div class="accordion-body">';
                    if(empty($parent->sub))
                    {
                        $html .= 'No any added stock';
                    }
                    else
                    {
                        $html .= $this->showChildStock($parent->sub);
                    }                  
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }          
            echo json_encode($html);
            exit();
        }
        else
        {
            $data = array(
                'title' => $this->mainTitle,
                'header' => $this->header,
                'heading' => 'Stock Register',
            );
            $this->load->view('pages/pharmacy/stock_register', $data);
        }
    }
    
    public function show_medicines_by_cat_format($category, $format)
    {
        try {
            $data = $this->medicine_model->get_medicines_by_cat_format($category, $format);
            if(!empty($data))
            {
                echo json_encode(array("status" => TRUE, 'data' => $data));
                exit();
            }
            else
            {
                echo json_encode(array("status" => FALSE, 'data' => '<span class="text-success"> No medicines</span>'));
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
    
    public function create_new_stock()
    {
        try{
            $this->form_validation->set_rules('supplier', 'Supplier', 'trim|required|max_length[12]');
            $this->form_validation->set_rules('entry_date', 'Entry Date', 'trim|required');
            $this->form_validation->set_rules('description', 'Descriptions', 'trim|max_length[300]');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
                exit();
            }
            else
            {
                // $user_pf = 'YTYG';
                $user_pf = $this->session->userdata('user_pf');
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
                
                $supplier = $this->security->xss_clean($this->input->post('supplier'));
                $entry_date = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('entry_date'))));
                $description = $this->security->xss_clean($this->input->post('description'));
                
                $insert_data = [];
                
                $root_node = [
                    'st_id' => $uuid,
                    'st_parent' => 0,
                    'st_title' => 'Stock Entry',
                    'st_code' => $stock_code,
                    'st_level' => 0,
                    'st_supplier' => $supplier,
                    'st_entry_date' => $entry_date,
                    'st_author' => $user_pf,
                    'st_is_active' => 0,
                    'st_is_sold' => 0,
                ];                
                if(!empty($description)) $root_node['st_desc'] = $description;
                $insert_data[] = $root_node;
                
                $medicine_categories = $this->medicineCategory_model->get_all_categories();
                $medicine_formats = $this->medicineFormat_model->get_all_formats();
                foreach($medicine_categories as $cat)
                {
                    $sub_uuid = $this->uuid();
                    while($this->checkIfUuidExist2($sub_uuid))
                    {
                        $sub_uuid = $this->uuid();
                    }
                    $sub_stock_code = $this->generate_stock_code();
                    while($this->checkIfStockCodeExist($sub_stock_code))
                    {
                        $sub_stock_code = $this->generate_stock_code();
                    }                    
                    $first_level_node = [
                        'st_id' => $sub_uuid,
                        'st_parent' => $uuid,
                        'st_title' => $cat->token,
                        'st_code' => $sub_stock_code,
                        'st_level' => 1,
                        'st_entry_date' => $entry_date,
                        'st_author' => $user_pf,
                        'st_is_active' => 0,
                        'st_is_sold' => 0,
                    ];
                    $insert_data[] = $first_level_node;
                    
                    foreach($medicine_formats as $format)
                    {
                        $sub2_uuid = $this->uuid();
                        while($this->checkIfUuidExist2($sub2_uuid))
                        {
                            $sub2_uuid = $this->uuid();
                        }
                        $sub2_stock_code = $this->generate_stock_code();
                        while($this->checkIfStockCodeExist($sub2_stock_code))
                        {
                            $sub2_stock_code = $this->generate_stock_code();
                        }                    
                        $second_level_node = [
                            'st_id' => $sub2_uuid,
                            'st_parent' => $sub_uuid,
                            'st_title' => $format->token,
                            'st_code' => $sub2_stock_code,
                            'st_level' => 2,
                            'st_entry_date' => $entry_date,
                            'st_author' => $user_pf,
                            'st_is_active' => 0,
                            'st_is_sold' => 0,
                        ];
                        $insert_data[] = $second_level_node;
                    }
                }
                
                $this->stock_model->save_new_stock_batch($insert_data);
                
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
            $this->form_validation->set_rules('token', 'Stock Token', 'trim|required|exact_length[8]|callback_checkTokenPost');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
                exit();
            }
            else
            {
                $token = $this->security->xss_clean($this->input->post('token'));
                $token_data = $this->getStockByToken($token);
                $full_stock = $this->stock_model->get_full_stock($token_data->id);
                
                $level_col = array_column((array) $full_stock, 'st_level');
                $counts = array_count_values($level_col);
                
                $leaf_node = "100";                
                if (array_key_exists($leaf_node, $counts))
                {
                    $count_leaf_node = $counts['100'];                    
                    if($count_leaf_node <= 0)
                    { 
                        echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> To post a stock, please ensure your stock has medicines </span>'));
                        exit();         
                    }
                    else
                    {
                        $ids = array_column((array) $full_stock, 'st_id');
                        array_push($ids, $token_data->id);
                        $data = array(
                            'st_is_active' => 1,
                        );
                        $this->stock_model->updateArray($ids, $data);
                        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Posted successifully </span>'));
                        exit();
                    }
                }
                else
                {
                    echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> To post a stock, please ensure your stock has medicines </span>'));
                    exit(); 
                }                
            }
        } catch (\Throwable $th) {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit(); 
        }
    }
    
    public function remove_stock()
    {
        try {
            $this->form_validation->set_rules('token', 'Stock Token', 'trim|required|exact_length[8]|callback_checkTokenToDeleteExist');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
                exit();
            }
            else
            {
                $token = $this->security->xss_clean($this->input->post('token'));
                $token_data = $this->getStockByToken($token);
                if($token_data->active == 0 && $token_data->sold == 0)
                {
                    $full_stock = $this->stock_model->get_full_stock($token_data->id);
                    if(empty($full_stock))
                    {    
                        $this->stock_model->delete_by_id($token_data->id);
                        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Removed successifully </span>'));
                        exit();             
                    }
                    else
                    {
                        $ids = array_column($full_stock, 'st_id');
                        array_push($ids, $token_data->id);
                        $this->stock_model->deleteArray($ids);
                        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Removed successifully </span>'));
                        exit();
                    }
                }
                else
                {
                    echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> Oops!, this entry can not be removed </span>'));
                    exit();
                }
                
            }
        } catch (\Throwable $th) {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit(); 
        }
    }
    
    public function get_draft()
    {
        try {
            $data = $this->stock_model->get_draft_stock();
            if(!empty($data))
            {
                echo json_encode(array("status" => TRUE, 'data' => $data));
                exit();
            }
            else 
            {
                echo json_encode(array("status" => FALSE, 'data' => '<span class="text-success"> No stock</span>'));
                exit();
            }
        } catch (\Throwable $th) {
            echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
            exit(); 
        } 
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
            $this->form_validation->set_rules('stock', 'Stock', 'trim|required');
            $this->form_validation->set_rules('path', 'Medicine Path', 'trim|required');
            $this->form_validation->set_rules('medicine', 'Medicine Name', 'trim|required');
            $this->form_validation->set_rules('description', 'Medicine Descriptions', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('total', 'Medicine Token', 'trim|required|less_than[500001]');
            
            if ($this->form_validation->run() == FALSE)
            {
                echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
                exit();
            }
            else
            {
                $stock = $this->security->xss_clean($this->input->post('stock'));
                $path = $this->security->xss_clean($this->input->post('path'));
                $medicine = $this->security->xss_clean($this->input->post('medicine'));
                $description = $this->security->xss_clean($this->input->post('description'));
                $total = $this->security->xss_clean($this->input->post('total'));
                
                $user_pf = $this->session->userdata('user_pf');
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
                    'st_parent' => $path,
                    'st_title' => $description,
                    'st_code' => $stock_code,
                    'st_level' => 100,
                    'st_medicine' => $medicine,
                    'st_author' => $user_pf,
                    'st_is_active' => 0,
                    'st_total' => $total,
                    'st_is_sold' => 0,
                ];
                
                $this->stock_model->save_new_stock($data);
                
                echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Medicine added successifully</span>'));
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
                'heading' => 'Prescriptions',
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
                                    $usage = (int) $stock->used;
                                    $usage = $usage + $count;
                                    
                                    $clients = $stock->clients;
                                    $clients = $clients == NULL ? $patient.'+'.$count : $clients.','.$patient.'+'.$count;
                                    
                                    $currTime = time();
                                    $sold_time = $stock->sold_time;
                                    $sold_time = $sold_time == NULL ? $currTime : $sold_time.'_'.$currTime;
                                    
                                    $new_stock_data = array(
                                        'st_usage' => $usage,
                                        'st_client' => $clients,
                                        'st_time' => $sold_time,
                                    );
                                    
                                    $this->stock_model->update_stock($new_stock_data, $id);
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
                                    
                                    $record_data = $this->symptoms_data_by_record_id($record);                                        
                                    $new_medicine_data = array(
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
                $v_data = array(
                    'vs_visit' => 'nimerudishwa_kutoka_ph',
                );
                $this->patient_model->update_patient_visit($v_data, $data_from_visit_table['vs_id']);
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