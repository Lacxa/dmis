<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Medicine extends CI_Controller {

    public $mainTitle = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->model(array("medicine_model"));  	
		// $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
		$this->load->library(array("form_validation", "session"));
		$this->load->helper(array("url", "html", "form", "security"));
		$this->form_validation->set_error_delimiters('<div class="invalid-feedback">', '</div>');
	}
    
    public function search()
    {
        $keyword = $this->input->post('query');
        echo json_encode($this->medicine_model->get_Medicines($keyword));
    }

	public function getMedicineById($id)
	{
		return $this->medicine_model->getMedicineById($id);
	}
}

    ?>