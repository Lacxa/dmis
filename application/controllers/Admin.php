<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Admin extends CI_Controller {
  
  public $mainTitle = null;
  public $header = null;
  
  public function __construct()
  {
    parent::__construct();
    $this->load->database();    
    $this->load->model(array("employee_model", "patient_model", "category_model"));
    $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
    $this->header = 'Admin';
    $this->load->library(array("form_validation", "session"));
    $this->load->helper(array("url", "html", "form", "security"));
    $this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
    
    $this->is_active();
    $this->is_admin();
  }  
  
  #########################################################
  # PRIVATE FUNCTIONS - TO BE ONLY CALLED WITHIN THIS CLASS
  
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
  
  private function is_admin()
  {
    $user_role = $this->session->userdata('user_role');
    if($user_role == 'SUPER' || $user_role == 'ADMIN')
    {}
    else
    {
      // return redirect($_SERVER['HTTP_REFERER']);
      return redirect(base_url('login'));
    }
  }
  
  public function checkPFExist($pf_number)
  {
    if ($this->employee_model->checkPFExist($pf_number) == FALSE) {
      return TRUE;
    } else {
      $this->form_validation->set_message('checkPFExist', 'This employee file number already exist!');
      return FALSE;
    }
  }
  
  public function checkEmailExist($email)
  {
    if ($this->employee_model->checkEmailExist($email) == FALSE) {
      return TRUE;
    } else {
      $this->form_validation->set_message('checkEmailExist', 'This email already exist!');
      return FALSE;
    }
  }
  
  public function checkPhoneExist($phone)
  {
    if ($this->employee_model->checkPhoneExist($phone) == FALSE) {
      return TRUE;
    } else {
      $this->form_validation->set_message('checkPhoneExist', 'This phone number already exist!');
      return FALSE;
    }
  }
  
  public function validate_phone_num($input)
  {
    $this->form_validation->set_message('validate_phone_num', 'This phone number is invalid!');
    return ( ! preg_match("/^[0-9]{10}+$/", $input)) ? FALSE : TRUE;
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
    $this->load->view('pages/admin/dashboard', $data);      
  }
  
  public function users()
  {
    $emp_categories = $this->category_model->all_categories();
    $emp_roles = $this->category_model->all_user_roles();

    if($this->input->server('REQUEST_METHOD') === 'POST')
    {
      $data = [];
      
      $draw = intval($this->input->post("draw"));
      $start = intval($this->input->post("start"));
      $length = intval($this->input->post("length"));
      
      $result = $this->employee_model->get_list_of_users($this->input->post());
      
      $i = $this->input->post("start");
      foreach($result as $r)
      {
        $full_name = empty($r->emp_mname) ? $r->emp_fname . ' ' . $r->emp_lname : $r->emp_fname.' ' . $r->emp_mname[0] . '. ' . $r->emp_lname;

        $activeBtn = '<button type="button" class="btn btn-sm btn-primary" name="deactivateButton" data-id="' . $r->emp_id . '" data-pf="' . $r->emp_pf . '" title="Click to De-activate"><i class="bi bi-person-check"></i> Active</button>';

        $inactiveBtn = '<button type="button" class="btn btn-sm btn-danger" name="activateButton" data-id="' . $r->emp_id . '" data-pf="' .$r->emp_pf . '" title="Click to Activate"> Disabled </button>';

        $deleteBtn = '<button type="button" class="btn btn-sm btn-danger" name="DelButton" data-id="' . $r->emp_id . '" data-pf="' . $r->emp_pf . '" title="Click to Delete"> <i class="bi bi-trash-fill"></i></button>';

        $isInchargeBtn = '<button type="button" class="btn btn-sm btn-primary" name="disableInchargeButton" data-id="' . $r->emp_id . '" data-pf="' .$r->emp_pf . '" title="Discharge"> Yes </button>';

        $isNotInchargeBtn = '<button type="button" class="btn btn-sm btn-danger" name="enableInchargeButton" data-id="' . $r->emp_id . '" data-pf="' . $r->emp_pf . '" title="Make Incharge"> No </button>';

        $categories = '<select name="changeCategory" id="cat_'.$r->emp_id.'" data-emp="' . $r->emp_id . '" class="form-select" style="width:auto;">';
        foreach($emp_categories as $category)
        {
          $selected = $r->cat_id == $category['cat_id'] ? 'selected disabled' : '';
          $categories .= '<option data-name="' . strtoupper($category['cat_name']) . '" value="' . $category['cat_id'] . '" ' . $selected . '>' . strtoupper($category['cat_name']) . '</option>';
        }
        $categories .= '</select">';

        $roles = '<select name="changeRole" id="role_'.$r->emp_id.'" data-emp="' . $r->emp_id . '" class="form-select" style="width:auto;">';
        foreach($emp_roles as $role)
        {
          $selected = $r->role_id == $role['role_id'] ? 'selected disabled' : '';
          $roles .= '<option value="' . $role['role_id'] . '" ' . $selected . '>' . strtoupper($role['role_name']) . '</option>';
        }
        $roles .= '</select">';
        
        $i++;
        $data[] = array(
          $i,
          $full_name,
          $r->emp_pf,
          $categories,
          $roles,
          $r->emp_isActive == 1 ? $activeBtn : $inactiveBtn,
          $deleteBtn,
          $r->emp_isIncharge == 1 ? $isInchargeBtn : $isNotInchargeBtn,
          $r->emp_mail,
          $r->emp_phone,
        );
      }
      
      $result = array(
        "draw" => $draw,
        "recordsTotal" => $this->employee_model->countAll(),
        "recordsFiltered" => $this->employee_model->countFiltered($this->input->get()),
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
        'heading' => 'List of users',
        'categories' => $emp_categories,
        'roles' => $emp_roles,
      );
      $this->load->view('pages/admin/list_of_users', $data); 
    }
  }
  
  public function register_user()
  {
    $this->form_validation->set_rules('file_number', 'Personal File Number', 'trim|required|callback_checkPFExist');
    $this->form_validation->set_rules('fname', 'First Name', 'trim|required');
    $this->form_validation->set_rules('mname', 'Middle Name', 'trim');
    $this->form_validation->set_rules('lname', 'Last Name', 'trim|required');
    $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|callback_checkEmailExist');
    $this->form_validation->set_rules('category', 'Category', 'trim|required|numeric');
    $this->form_validation->set_rules('role', 'Role', 'trim|required|numeric');
    $this->form_validation->set_rules('phone', 'Mobile Phone', 'trim|required|callback_validate_phone_num|callback_checkPhoneExist');
    $this->form_validation->set_rules('password', 'Password', 'trim|required');
    
    if ($this->form_validation->run() == FALSE)
    {
      echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
      exit();
    }
    else
    {
      $file_number = $this->security->xss_clean($this->input->post('file_number'));
      $fname = $this->security->xss_clean($this->input->post('fname'));
      $mname = $this->security->xss_clean($this->input->post('mname'));
      $lname = $this->security->xss_clean($this->input->post('lname'));
      $email = $this->security->xss_clean($this->input->post('email'));
      $category = $this->security->xss_clean($this->input->post('category'));
      $role = $this->security->xss_clean($this->input->post('role'));
      $phone = $this->security->xss_clean($this->input->post('phone'));
      $password = password_hash($this->security->xss_clean($this->input->post('password')), PASSWORD_DEFAULT);
      
      $data = array(
        'emp_pf' => $file_number,
        'emp_category' => $category,
        'emp_role' => $role,
        'emp_fname' => $fname,
        'emp_lname' => $lname,
        'emp_mail' => $email,
        'emp_phone' => $phone,
        'emp_password' => $password,
      );
      if(!empty($mname)) $data['emp_mname'] = $mname;
      $this->employee_model->save_employee($data);
      
      echo json_encode(array(
        "status" => TRUE,
        'data' => '<span class="text-success"> Added successifully</span>'
      ));
      exit();
    }   
  }
  
  public function user_state($state, $id)
  {
    try {
      if($state == 0 || $state == 1)
      {
        if($this->session->userdata('user_role') == 'SUPER')
        {
          $this->employee_model->setUserState($state, $id);
        }
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
        exit();
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
  
  public function delete_user($id)
  {
    try {
      $employee_id = $this->security->xss_clean($id);
      $emp_data = $this->employee_model->get_employee_by_id($employee_id);
      if(empty($emp_data))
      {
        echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, no such user/employee </span>'));
        exit();        
      }
      else
      {
        if($this->session->userdata('user_role') == 'SUPER')
        {
          $this->employee_model->delete($employee_id);
        }
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit();
    }
  }
  
  public function user_incharge($value, $id)
  {
    try {
      if($value == 0 || $value == 1)
      {
        if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
        {
          $this->employee_model->setIncharge($value, $id);
        }
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
        exit();
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

  public function user_change_category($employee, $category)
  {

    try {
      $employee = $this->security->xss_clean($employee);
      $category = $this->security->xss_clean($category);

      $emp_data = $this->employee_model->get_employee_by_id($employee);
      $cat_data = $this->category_model->get_category_by_id($category);

      if(empty($emp_data) && empty($cat_data))
      {
        echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, invalid employee/employee-category </span>'));
        exit();
      }
      else
      {
        if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
        {
          $data = array(
            'emp_category' => $category,
          );
          $this->employee_model->updateUserData($employee, $data);
        }
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit(); 
    }
  }

  public function user_change_role($employee, $role)
  {

    try {
      $employee = $this->security->xss_clean($employee);
      $role = $this->security->xss_clean($role);

      $emp_data = $this->employee_model->get_employee_by_id($employee);
      $role_data = $this->category_model->get_role_by_id($role);

      if(empty($emp_data) && empty($role_data))
      {
        echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, invalid employee/employee-role </span>'));
        exit();
      }
      else
      {
        if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
        {
          $data = array(
            'emp_role' => $role,
          );
          $this->employee_model->updateUserData($employee, $data);
        }
        echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
        exit();
      }
    } catch (\Throwable $th) {
      echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
      exit(); 
    }
  }

}