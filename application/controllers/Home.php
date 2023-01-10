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
		$this->load->library(array("form_validation", "session"));
		$this->load->helper(array("url", "html", "form", "security", "date"));
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
	}
	
	public function index()
	{
		if(!$this->session->has_userdata('user_id'))
		{
			return redirect(base_url('login'));
		}
		else
		{
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
					else if($is_user_available['emp_isFirstLogin'] == 1)
					{
						return redirect(base_url('password/expired/1'), 'refresh');
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

	public function generate_password()
	{
		echo password_hash("Dmis_2022", PASSWORD_DEFAULT);
	}

	public function password_expired($first_login)
	{
		$data = array(
			'title' => $this->mainTitle,
			'heading' => 'Password Expired',
			'action' => $first_login
		);
		$this->load->view('pages/auth/password_expired', $data);
	}

	public function password_expired_post($first_login)
	{
		$this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email');
		$this->form_validation->set_rules('current', 'Current Password', 'trim|required');
		$this->form_validation->set_rules('new', 'New Password', 'trim|required|callback_valid_password');
		$this->form_validation->set_rules('cnew', 'Confirm New Password', 'trim|required|matches[new]');
		if ($this->form_validation->run() == FALSE)
		{
			$this->password_expired($first_login);
		}
		else
		{
			$email = $this->security->xss_clean($this->input->post('email'));
			$current = $this->security->xss_clean($this->input->post('current'));
			$new = $this->security->xss_clean($this->input->post('new'));

			$is_user_available = $this->employee_model->validate_sign_in($email, $current); 
			if ($is_user_available == FALSE)  
			{
				$this->session->set_flashdata('error', 'The email or current password you have provided is invalid');
				redirect(base_url('password/expired'));
			}
			else
			{
				$format = "%Y-%m-%d %h:%i %s";
				$data = array(
					'emp_password' => password_hash($new, PASSWORD_DEFAULT),
					// 'emp_pwd_changed_at' => date('Y-m-d H:i:s'),
					'emp_pwd_changed_at' => mdate($format),
				);
				if($first_login == 1)
				{
					$data['emp_isFirstLogin'] = 0;
				}

				$this->employee_model->updateUserData($is_user_available['emp_id'], $data);
				$this->session->set_flashdata('success', 'Updated successfully, please log in here');
				redirect(base_url('login'));
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
