<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Download extends CI_Controller {

  public $mainTitle = null;
  
  public function __construct()
  {
    parent::__construct();
    $this->mainTitle  = 'DMIS | DISPENSARY MANAGEMENT INFORMATION SYSTEM';
    $this->load->library(array("form_validation", "session"));
    $this->load->helper(array("url", "html", "download", "security"));
  }

  public function investigation_file($file_name)
  {
    $path = FCPATH.'uploads/investigations/'.$file_name;
    force_download($path, NULL);
    // echo 1;
  }

}