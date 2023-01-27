<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Home extends CI_Controller {

	public $mainTitle = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model(array("employee_model"));  	
		$this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
		$this->load->library(array("form_validation", "session", "user_agent"));
		$this->load->helper(array("url", "html", "form", "security", "date"));
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
	}
	
	public function uuid() 
	{
		$this->load->library('uuid');
			//Output a v4 UUID 
		$uuid4 = $this->uuid->v4();
		$uuid4 = str_replace('-', '', $uuid4);
		return $uuid4;
	}
	
	public function index()
	{
		if(!$this->session->has_userdata('user_id'))
		{
			return redirect(base_url('login'));
		}
		else
		{
			// Login history
			$default_times = 50;
			$pf = $this->session->userdata('user_pf');
			$log_id = $this->uuid();
			while($this->employee_model->check_if_uuid_exist_login_history($log_id))
			{
				$log_id = $this->uuid();
			}
			$data = array(
				'log_id' => $log_id,
				'log_emp_pf' => $pf,
				'log_ip' => $this->input->ip_address(),
				'log_platform' => $this->agent->platform(),
				'log_browser' => $this->agent->browser() . ' - ' . $this->agent->version(),
				);
			$log_count = $this->employee_model->count_login_history_by_file_number($pf);
			if($log_count <= $default_times)
			{
				$this->employee_model->save_login_history($data);
			}
			else
			{
				$old_log_id = $this->employee_model->get_old_login_history_by_file_number($pf);
				$this->employee_model->delete_login_history($old_log_id);
				$this->employee_model->save_login_history($data);
			}

			if($this->session->userdata('user_role') == 'REC') return redirect(base_url('reception'));
			else if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN') return redirect(base_url('admin'));
			else if($this->session->userdata('user_role') == 'MO') return redirect(base_url('doctor'));
			else if($this->session->userdata('user_role') == 'LAB') return redirect(base_url('lab'));
			else if($this->session->userdata('user_role') == 'PH') return redirect(base_url('pharmacy'));
			else echo 'coming soon...';
		}

	}
	
	public function checkCapture($capture)
	{
		$captcha_answer=$this->session->userdata('captchaword');
		if ($capture == $captcha_answer) {
			return TRUE;
		} else {
			$this->form_validation->set_message('checkCapture', 'Captcha does not match.');
			return FALSE;
		}
	}

	public function login()
	{
		$this->load->helper("captcha");

		$vals = array(
			// 'word'          => 'Random word',
			'img_path'      => './captcha/images/',
			'img_url'       => base_url().'captcha/images/',
			'font_path'     => BASEPATH.'fonts/texb.ttf',
			'img_width'     => '200',
			'img_height'    => 35,
			'expiration'    => 7200,
			'word_length'   => 6,
			'font_size'     => 18,
			'img_id'        => 'Imageid',
			// 'pool'          => '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
			'pool'          => '0123ABCDEFGHIJKL456789mnopqrstu012vwxyzMNOPQRSTUVWXYZabcde789fghijkl',
			// 'pool'          => '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ',
			
			// White background and border, black text and red grid
			'colors'        => array(
				'background' => array(255, 255, 255),
				'border' => array(255, 255, 255),
				'text' => array(0, 0, 0),
				'grid' => array(255, 200, 200)
			)
		);
		
		$cap = create_captcha($vals);
		$image = $cap['image'];
		
		$captchaword= $cap['word'];
		$this->session->set_userdata('captchaword',$captchaword);
		
		$data = array(
			'title' => $this->mainTitle,
			'heading' => 'Login',
			'captcha_image' => $image,
		); 
		$this->load->view('pages/auth/login', $data);
	}

	public function auth()
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
			$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[20]|callback_valid_password',
				array('required' => 'You must provide a %s.'));
			$this->form_validation->set_rules('captcha', 'Captcha', 'trim|required|callback_checkCapture');
			if ($this->form_validation->run() == FALSE)
			{
				$this->login();
			}
			else
			{
				$email = $this->security->xss_clean($this->input->post('email'));
				$password = $this->security->xss_clean($this->input->post('password'));
				$is_user_available = $this->employee_model->validate_sign_in($email, $password); 
				if ($is_user_available == FALSE)  
				{
					$this->session->set_flashdata('error', 'Invalid email or password');
					redirect(base_url('login'));
				}
				else
				{
					if($is_user_available['emp_isActive'] == 0)
					{
						$this->session->set_flashdata('error', 'Oops!, your account is not active');
						redirect(base_url('login'));
					}
					else
					{
						$session_data = array(
							'user_id'  => $is_user_available['emp_id'],
							'user_pf'  => $is_user_available['emp_pf'],
							'user_role'  => $is_user_available['role_alias'],
							'user_role_name'  => $is_user_available['role_name'],
							'user_category'  => $is_user_available['cat_name'],
							'user_mail' => $is_user_available['emp_mail'],
							'user_fname' => $is_user_available['emp_fname'],
							'user_mname' => $is_user_available['emp_mname'],
							'user_lname' => $is_user_available['emp_lname'],
							'user_mobile' => $is_user_available['emp_phone'],
							'user_isActive' => $is_user_available['emp_isActive'] == 1 ? TRUE : FALSE,
							'user_isIncharge' => $is_user_available['emp_isIncharge'] == 1 ? TRUE : FALSE,
							'user_reg_date' => $is_user_available['emp_regdate'],
							'user_first_login' => $is_user_available['emp_isFirstLogin'] == 1 ? TRUE : FALSE,
							'user_last_pwd_update' => $is_user_available['emp_pwd_changed_at'],
						);
						$this->session->set_userdata($session_data);
						redirect(base_url());
					}
				}
			}
		}
		else
		{
			return redirect(base_url('login'));
		}
	}

	public function logout()
	{
		$this->session->sess_destroy();
		redirect(base_url());

	}

	public function login_history($header)
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			// $pf = $this->session->userdata('user_pf');
			$data = [];
				
			$draw = intval($this->input->post("draw"));
			$start = intval($this->input->post("start"));
			$length = intval($this->input->post("length"));
			
			$result = $this->employee_model->get_login_history_by_pf($this->input->post());
				
			$i = $this->input->post("start");
			foreach($result as $r)
			{
				$i++;
				$data[] = array(
					$i,
					$r->log_ip,
					$r->log_platform,
					$r->log_browser,
					$r->log_time,
				);
			}
				
			$result = array(
				"draw" => $draw,
				"recordsTotal" => $this->employee_model->countAllLoginHistory(),
				"recordsFiltered" => $this->employee_model->countFilteredLoginHistory($this->input->get()),
				"data" => $data
			);
				
			echo json_encode($result);
			exit();
		}
		else
		{
			$data = array(
				'title' => 'Sign In History',
				'header' => @$header,
				'heading' => 'Sign In History',
			);
			$this->load->view('pages/auth/login_history', $data);					
		}		
	}

	public function generate_password()
	{
		echo password_hash("Dmis_2022", PASSWORD_DEFAULT);
	}

	public function change_password($change_type, $header)
	{
		$data = array(
			'title' => $this->mainTitle,
			'header' => @$header,
			'heading' => 'Change Password',
			'action' => $change_type
		);
		$this->load->view('pages/auth/change_password', $data);
	}

	public function change_password_post($change_type, $header)
	{
		$this->form_validation->set_rules('password', 'Current Password', 'trim|required');
		$this->form_validation->set_rules('newpassword', 'New Password', 'trim|required|callback_valid_password');
		$this->form_validation->set_rules('renewpassword', 'Re-enter New Password', 'trim|required|matches[newpassword]');
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
			exit();
		}
		else
		{
			$user_email = $this->session->userdata('user_mail');;
			$password = $this->security->xss_clean($this->input->post('password'));
			$newpassword = $this->security->xss_clean($this->input->post('newpassword'));

			$is_user_available = $this->employee_model->validate_sign_in($user_email, $password); 
			if ($is_user_available == FALSE)  
			{
				echo json_encode(array("status" => FALSE , 'data' => '<code> Current password is not valid </code>'));
            	exit();
			}
			else
			{
				if($password == $newpassword) 
				{
					echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Please formulate a new password </span>'));
					exit();
				}
				else
				{
					$format = "%Y-%m-%d %H:%i %s";
					$change_date = mdate($format);
					$change_date2 = date('Y-m-d H:i:s');
					$data = array(
						'emp_password' => password_hash($newpassword, PASSWORD_DEFAULT),
						// 'emp_pwd_changed_at' => date('Y-m-d H:i:s'),
						'emp_pwd_changed_at' => $change_date,
					);

					if($change_type == 1)
					{
						$data['emp_isFirstLogin'] = 0;
						$this->employee_model->updateUserData($is_user_available['emp_id'], $data);
						$this->session->set_userdata('user_first_login', FALSE);
						$this->session->set_userdata('user_last_pwd_update', $change_date2);
					}
					else
					{
						$this->employee_model->updateUserData($is_user_available['emp_id'], $data);
						$this->session->set_userdata('user_last_pwd_update', $change_date2);
					}

					echo json_encode(array("status" => FALSE , 'data' => '<code> Changed successfully </code>'));
					exit();
				}
			}
		}
	}

	public function valid_password($password = '')
	{
		$password = trim($password);

		$regex_lowercase = '/[a-z]/';
		$regex_uppercase = '/[A-Z]/';
		$regex_number = '/[0-9]/';
		$regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';

		if (empty($password))
		{
			$this->form_validation->set_message('valid_password', 'The {field} field is required.');

			return FALSE;
		}

		if (preg_match_all($regex_lowercase, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field must have at least one lowercase letter.');

			return FALSE;
		}

		if (preg_match_all($regex_uppercase, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field must have at least one uppercase letter.');

			return FALSE;
		}

		if (preg_match_all($regex_number, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field must have at least one number.');

			return FALSE;
		}

		if (preg_match_all($regex_special, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field must have at least one special character.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));

			return FALSE;
		}

		if (strlen($password) < 6)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field must be at least 6 characters in length.');

			return FALSE;
		}

		if (strlen($password) > 32)
		{
			$this->form_validation->set_message('valid_password', 'The {field} field cannot exceed 32 characters in length.');

			return FALSE;
		}

		return TRUE;
	}

}
