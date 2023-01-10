<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Disease extends CI_Controller {
	
	public $mainTitle = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model(array("disease_model"));  	
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
			'subHeading' => 'Diseases',
			'categories' => $this->disease_model->get_all_disease_categories(),
		);
		$this->load->view('pages/doctor/diseases', $data);  
	} 
	
	public function admin()
	{
		$data = array(
			'title' => $this->mainTitle,
			'header' => 'Admin',
			'heading' => 'Extra',
			'subHeading' => 'Diseases',
		);
		$this->load->view('pages/admin/diseases', $data);  
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
	
	public function checkName($name)
	{
		if ($this->disease_model->checkName($name) == FALSE) {
			return TRUE;
		} else {
			$this->form_validation->set_message('checkName', 'This disease name already exist!');
			return FALSE;
		}
	}
	public function checkAlias($alias)
	{
		if ($this->disease_model->checkAlias($alias) == FALSE) {
			return TRUE;
		} else {
			$this->form_validation->set_message('checkAlias', 'This alias name already exist!');
			return FALSE;
		}
	}
	
	public function checkCategoryTokenExist($token)
	{
		if ($this->disease_model->checkCategoryTokenExist($token) == FALSE) {
			return TRUE;
		} else {
			$this->form_validation->set_message('checkCategoryTokenExist', 'This disease category token already exist!');
			return FALSE;
		}
	}
	
	public function checkDiseaseToken($token)
	{
		if ($this->disease_model->checkDiseaseToken($token) == FALSE) {
			return TRUE;
		} else {
			$this->form_validation->set_message('checkDiseaseToken', 'This disease token already exist!');
			return FALSE;
		}
	}
	
	public function search()
	{
		$keyword = $this->input->post('query');
		echo json_encode($this->disease_model->getDisease($keyword));
	}
	
	public function get_diseases()
	{
		$data = [];
		
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		
		$result = $this->disease_model->get_diseases($this->input->post());
		
		$i = $this->input->post("start");
		foreach($result as $r)
		{			
			$comBtn = '<button type="button" class="btn btn-sm btn-primary" name="notComButton" data-id="' . $r->id . '" data-text="' .$r->text . '" title="Make Non-communicable"> Yes </button>';
			
			$notComBtn = '<button type="button" class="btn btn-sm btn-danger" name="comButton" data-id="' . $r->id . '" data-text="' . $r->text . '" title="Make Communicable"> Not </button>';
			
			$i++;
			$data[] = array(
				$i,
				$r->text,
				$r->code,
				$r->category_code == 200 ? $comBtn : $notComBtn,
				$r->author,
			);
		}
		
		$result = array(
			"draw" => $draw,
			"recordsTotal" => $this->disease_model->countAll(),
			"recordsFiltered" => $this->disease_model->countFiltered($this->input->post()),
			"data" => $data
		);
		
		echo json_encode($result);
		exit();
	}
	
	public function admin_get_diseases()
	{
		$data = [];
		
		$draw = intval($this->input->post("draw"));
		$start = intval($this->input->post("start"));
		$length = intval($this->input->post("length"));
		
		$result = $this->disease_model->get_diseases($this->input->post());
		
		$i = $this->input->post("start");
		foreach($result as $r)
		{
			$i++;

			$comBtn = '<button type="button" class="btn btn-sm btn-primary" name="notComButton" data-id="' . $r->id . '" data-text="' .$r->text . '" title="Make Non-communicable"> Yes </button>';
			
			$notComBtn = '<button type="button" class="btn btn-sm btn-danger" name="comButton" data-id="' . $r->id . '" data-text="' . $r->text . '" title="Make Communicable"> Not </button>';
			
			$deleteBtn = '<button type="button" class="btn btn-sm btn-danger" name="DelButton" data-id="' . $r->id . '" data-text="' . $r->text . '" title="Trash"> <i class="bi bi-trash-fill"></i></button>';
			
			$data[] = array(
				$i,
				$r->text,
				$r->code,
				$r->category_code == 200 ? $comBtn : $notComBtn,
				$deleteBtn,
			);
		}
		
		$result = array(
			"draw" => $draw,
			"recordsTotal" => $this->disease_model->countAll(),
			"recordsFiltered" => $this->disease_model->countFiltered($this->input->get()),
			"data" => $data
		);
		
		echo json_encode($result);
		exit();
	}
	
	public function add_disease()
	{
		$this->form_validation->set_rules('name', 'Disease Name', 'trim|required|callback_checkName');
		$this->form_validation->set_rules('token', 'Disease Token', 'trim|required|callback_checkDiseaseToken');
		$this->form_validation->set_rules('category', 'Disease Category', 'trim|required|numeric');
		// $this->form_validation->set_rules('alias', 'Disease Alias', 'trim|required|callback_checkAlias');
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
		}
		else
		{
			$data = array(
				'dis_title' => $this->security->xss_clean($this->input->post('name')),
				// 'dis_alias' => $this->security->xss_clean($this->input->post('alias')),
				'dis_token' => $this->security->xss_clean($this->input->post('token')),
				'dis_category' => $this->security->xss_clean($this->input->post('category')),
				'dis_author' => $this->session->userdata('user_pf'),
			);
			if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN' || $this->session->userdata('user_role') == 'MO')
			{
				$this->disease_model->save_disease($data);
			}
			echo json_encode(array(
				"status" => TRUE,
				'data' => '<span class="text-success"> Received successifully</span>'
			));
		}   
	}
	
	public function add_disease_category()
	{
		$this->form_validation->set_rules('title', 'Disease Category Name', 'trim|required');
		$this->form_validation->set_rules('token', 'Disease Category Token', 'trim|required|callback_checkCategoryTokenExist');
		$this->form_validation->set_rules('desc', 'Disease Decription', 'trim');
		
		
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE , 'data' => validation_errors()));
		}
		else
		{
			$data = array(
				'discat_name' => $this->security->xss_clean($this->input->post('title')),
				'discat_author' => $this->session->userdata('user_pf'),
				'discat_token' => $this->security->xss_clean($this->input->post('token')),
			);
			if(!empty($this->security->xss_clean($this->input->post('desc')))) $data['discat_description'] = $this->security->xss_clean($this->input->post('desc'));
			
			$this->disease_model->save_disease_category($data);
			echo json_encode(array(
				"status" => TRUE,
				'data' => '<span class="text-success"> Received successifully</span>'
			));
		}  
	}
	
	public function disease_categories()
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			$data = [];
			
			$draw = intval($this->input->post("draw"));
			$start = intval($this->input->post("start"));
			$length = intval($this->input->post("length"));
			
			$result = $this->disease_model->get_disease_categories($this->input->post());
			
			$i = $this->input->post("start");
			foreach($result as $r)
			{
				$i++;
				$data[] = array(
					$i,
					$r->text,
					$r->description,
					$r->token,
					$this->get_timespan($r->entry_date),
				);
			}
			
			$result = array(
				"draw" => $draw,
				"recordsTotal" => $this->disease_model->countAllDiseaseCategories(),
				"recordsFiltered" => $this->disease_model->countFilteredDiseaseCategories($this->input->get()),
				"data" => $data
			);
			
			echo json_encode($result);
			exit();
			
		}
		else
		{
			$data = array(
				'title' => $this->mainTitle,
				'header' => 'Doctor',
				'heading' => 'Extra',
				'subHeading' => 'Disease Categories',
			);
			$this->load->view('pages/doctor/disease_categories', $data); 			
		}
	}
	
	public function communicable_state($state, $id)
	{
		try {
			$state = $this->security->xss_clean($state);
			$id = $this->security->xss_clean($id);
			if($state == 0 || $state == 1)
			{
				$disease_data = $this->disease_model->getDiseaseById($id);
				if(empty($disease_data))
				{
					echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, no such disease </span>'));
					exit();      
				}
				else
				{
					$state = $state == 1 ? 200 : 201;
					if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN' || $this->session->userdata('user_role') == 'MO') 
					{
						$data = array(
							'dis_category' => $state,
						);
						if(!$this->disease_model->update($id, $data))
						{
							echo json_encode(array("status" => FALSE, 'data' => '<span class="text-danger"> An internal server error occured! </span>'));
							exit();
						}
					}
					echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
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
	
	public function delete_disease($id)
	{
		try {
			$disease_id = $this->security->xss_clean($id);
			$disease_data = $this->disease_model->getDiseaseById($disease_id);
			if(empty($disease_data))
			{
				echo json_encode(array("status" => FALSE , 'data' => '<span class="text-danger"> Oops!, no such disease </span>'));
				exit();      
			}
			else
			{
				if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
				{
					$this->disease_model->delete($disease_id);
				}
				echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success"> Success </span>'));
				exit();
			}
		} catch (\Throwable $th) {
			echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
			exit();
		}
	}
	
	public function import_disease()
	{
		$path = FCPATH.'uploads/temporary/';
		$json = [];
		$this->upload_config($path);
		if (!$this->upload->do_upload('file'))
		{
			$json = [
				"status" => FALSE,
				'data' => '<span class="text-danger">'.$this->upload->display_errors().'</span>',
			];
		}
		else
		{
			$file_data 	= $this->upload->data();
			$file_name 	= $path.$file_data['file_name'];
			$arr_file 	= explode('.', $file_name);
			$extension 	= end($arr_file);
			if('csv' == $extension)
			{
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Csv();
			}
			else
			{
				$reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
			}
			$spreadsheet = $reader->load($file_name);
			$sheet_data = $spreadsheet->getActiveSheet()->toArray();
			$list = [];
			foreach($sheet_data as $key => $val)
			{
				if($key != 0)
				{
					$col1 = ucfirst($this->security->xss_clean(trim($val[0])));
					$col2 = $this->security->xss_clean(trim($val[1]));
					
					$title = (Array)$this->disease_model->get(["dis_title" => $col1]);
					// $short = (Array)$this->disease_model->get(["dis_alias" => $col2]);
					$code = (Array)$this->disease_model->get(["dis_token" => $col2]);
					// $category = $this->disease_model->checkCategoryTokenExist($col4);
					if(!empty($title) || !empty($code))
					{} 
					else 
					{
						$list [] = [
							'dis_title'	=> $col1,
							'dis_token' => $col2,
							'dis_author' => $this->session->userdata('user_pf')
						];
					}
				}
			}
			
			if(file_exists($file_name))
			unlink($file_name);
			if(count($list) > 0) 
			{
				$result = $this->disease_model->add_batch($list);
				if($result) 
				{
					$json = [
						"status" => TRUE,
						'data' 	=> '<span class="text-success"> All Entries have been imported successfully.</span>',
					];
				}
				else 
				{
					$json = [
						"status" => FALSE,
						'data' 	=> '<span class="text-danger"> Something went wrong. Please try again.</span>'
					];
				}
			}
			else
			{
				$json = [
					"status" => FALSE,
					'data' => '<span class="text-danger"> No new record is found.</span>',
				];
			}
		}
		echo json_encode($json);
	}
	
	public function upload_config($path)
	{
		if (!is_dir($path))
		mkdir($path, 0777, TRUE);		
		$config['upload_path'] 		= $path;		
		$config['allowed_types'] 	= 'csv|CSV|xlsx|XLSX|xls|XLS';
		$config['max_filename']	 	= '255';
		$config['encrypt_name'] 	= TRUE;
		$config['max_size'] 		= 4096;
		$this->load->library('upload', $config);
	}
	
}

?>