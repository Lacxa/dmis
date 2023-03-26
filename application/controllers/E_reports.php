<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class E_reports extends CI_Controller {
	
	public $mainTitle = null;
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();	
		$this->mainTitle  = 'DMIS | E-REPORTS';
		$this->load->model(array("employee_model", "patient_model", "category_model", "investigation_model", "medicine_model")); 
		$this->load->library(array("form_validation", "session", "MYPDF"));
		$this->load->helper(array("url", "html", "form", "security", "date"));
		$this->form_validation->set_error_delimiters('<div class="text-danger">', '</div>');
	}

	#######################################################################
	################      PRIVATE METHODS STARTS ##########################
	#######################################################################
	
	private function get_employee_by_file_number($file_number)
	{
		$data = $this->employee_model->get_employee_by_file_number($file_number);
		if(!empty($data)) return $data;
		else return FALSE;
	}

	private function compute_age($dob)
	{
		$date1 = date_create(date("Y-m-d", strtotime($dob)));
		$date2 = date_create(date("Y-m-d"));        
		$diff = date_diff($date1, $date2);
		$age = abs($diff->format("%Y"));
		return $age;
	}

	private function eligibleToResetRecord($record)
	{
		if($this->patient_model->eligibleToResetRecord($record)) return TRUE;
		else return FALSE;
	}

	private function formatSizeUnits($bytes)
	{
		if ($bytes >= 1073741824)
		{
			$bytes = number_format($bytes / 1073741824, 2) . ' GB';
		}
		elseif ($bytes >= 1048576)
		{
			$bytes = number_format($bytes / 1048576, 2) . ' MB';
		}
		elseif ($bytes >= 1024)
		{
			$bytes = number_format($bytes / 1024, 2) . ' KB';
		}
		elseif ($bytes > 1)
		{
			$bytes = $bytes . ' bytes';
		}
		elseif ($bytes == 1)
		{
			$bytes = $bytes . ' byte';
		}
		else
		{
			$bytes = '0 bytes';
		}

		return $bytes;
	}

	#######################################################################
	################      PRIVATE METHODS ENDS ############################
	#######################################################################
	
	public function client_report_get($record_id)
	{
		$client_pf = 'N2-MR-W4-G4';
		// $this->load->library('Pdf');
		
		$client = $this->patient_model->client_report($record_id);
		if(!empty($client))
		{
			$basic_info = $this->patient_model->get_patient_by_recordId($record_id);
			
			$data = array(
				'title' => $basic_info['rec_patient_file'],
				'basic' => $basic_info,
				'data' => $client,
			);
			
			var_dump($client);
			
			// $dompdf = new Pdf();
			// $html= $this->load->view('pages/e_reports/client_report', $data, TRUE);
			// $dompdf->loadHtml($html);
			// $dompdf->setPaper('A4', 'portrait');
			// $dompdf->render();
			// $pdf = $dompdf->output();
			// // TRUE --download, FALSE --preview
			// $dompdf->stream($basic_info['rec_patient_file'].'_'.date('d-m-Y-H:i:s').'.pdf', array("Attachment"=>TRUE));
			// exit();		
		} 
		else
		{
			echo "No data";
		}		
	}
	
	public function client_report_post()
	{
		$this->form_validation->set_rules('pf', 'File Number', 'trim|required');
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			$this->session->set_flashdata('error', validation_errors());
			return redirect($_SERVER['HTTP_REFERER']);
		}
		else
		{
			$pf = $this->security->xss_clean($this->input->post('pf'));
			$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
			$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
			
			$basic = $this->patient_model->get_id_by_file_number($pf);
			if(empty($basic))
			{
				$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, this client is not available </span>');
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$result = $this->patient_model->client_report_post($pf, $start, $end);
				if(empty($result))
				{
					$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, client with PF "'.$pf.'" has got no history form '.$start.' to '.$end.' </span>');
					return redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$data = array(
						'title' => $pf,
						'basic' => $basic,
						'data' => $result,
					);
					
					$dompdf = new Pdf();
					$html= $this->load->view('pages/e_reports/client_report', $data, TRUE);
					$dompdf->loadHtml($html);
					$dompdf->setPaper('A4', 'portrait');
					$dompdf->render();
					$pdf = $dompdf->output();
					$dompdf->stream($pf.'_'.date('d-m-Y-H:i:s').'.pdf', array("Attachment"=>TRUE));
					exit();				
				}
			}
		}
	}
	
	public function doctor_performance()
	{
		$doctor_pf = $this->session->userdata('user_pf');		
		$user_role = $this->session->userdata('user_role');
		if($user_role != 'MO')
		{
			$this->form_validation->set_rules('pf', 'Doctor PF', 'trim|required');
			if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$doctor_pf = $this->security->xss_clean($this->input->post('pf'));
			}
		}
		
		$basic = $this->employee_model->get_employee_by_file_number($doctor_pf);
		if(!empty($basic) && $basic['role_alias'] == 'MO')
		{
			$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
			$this->form_validation->set_rules('end', 'End Date', 'trim|required');
			
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
				$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
				
				$result = $this->patient_model->doctor_performance($doctor_pf, $start, $end);
				if(empty($result))
				{
					$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, currently, a doctor with PF "'.$doctor_pf.'" has got no data.</span>');
					return redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$data = array(
						'title' => $doctor_pf,
						'basic' => $basic,
						'data' => $result,
						'start' => $start,
						'end' => $end,
						'lab' => TRUE,
					);
					
					$html = $this->load->view('pages/e_reports/employee_performance', $data, TRUE);
					
					$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
					
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('DMIS:'.$doctor_pf);
					$pdf->SetTitle($doctor_pf.'Performance Report_');
					$pdf->SetSubject('NIT Dispensary Performance Report');
					$pdf->SetKeywords('DMIS, Performance report, NIT, Dispensary, '.$doctor_pf);
					
					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
					
					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					
					
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					
					// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
					$pdf->SetMargins(20, 14, 16);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
					
					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					
					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
					
					// set font
					$pdf->SetFont('times', '', 12);
					
					// add a page
					$pdf->AddPage('', 'A4');
					
					// print a block of text using Write()
					// $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
					$pdf->writeHTML($html, true, false, true, false, '');
					
					// ---------------------------------------------------------
					ob_clean();
					//Close and output PDF document
					$pdf->Output($doctor_pf.' Performance Report_'.time().'.pdf', 'I');
					exit();
				}
				
			}
			
		}
		else
		{
			$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, this doctor is not available </span>');
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}
	
	public function reception_performance()
	{
		// Remember to make sure that this is receptionist otherwise not
		$receptionist_pf = $this->session->userdata('user_pf');
		$user_role = $this->session->userdata('user_role');
		if($user_role != 'REC')
		{
			$this->form_validation->set_rules('pf', 'Receptionist PF', 'trim|required');
			if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$receptionist_pf = $this->security->xss_clean($this->input->post('pf'));
			}
		}
		$basic = $this->employee_model->get_employee_by_file_number($receptionist_pf);
		if(!empty($basic) && $basic['role_alias'] == 'REC')
		{
			$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
			$this->form_validation->set_rules('end', 'End Date', 'trim|required');
			
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
				$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
				
				$result = $this->patient_model->receptionist_performance($receptionist_pf, $start, $end);
				if(empty($result))
				{
					$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, currently, a receptionist with PF "'.$receptionist_pf.'" has got no data.</span>');
					return redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$data = array(
						'title' => $receptionist_pf,
						'basic' => $basic,
						'data' => $result,
						'start' => $start,
						'end' => $end,
						'lab' => FALSE,
					);
					
					$html = $this->load->view('pages/e_reports/employee_performance', $data, TRUE);
					
					$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
					
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('DMIS:'.$receptionist_pf);
					$pdf->SetTitle($receptionist_pf.'Performance Report_');
					$pdf->SetSubject('NIT Dispensary Performance Report');
					$pdf->SetKeywords('DMIS, Performance report, NIT, Dispensary, '.$receptionist_pf);
					
					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
					
					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					
					
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					
					// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
					$pdf->SetMargins(20, 14, 16);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
					
					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					
					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
					
					// set font
					$pdf->SetFont('times', '', 12);
					
					// add a page
					$pdf->AddPage('', 'A4');
					
					// print a block of text using Write()
					// $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
					$pdf->writeHTML($html, true, false, true, false, '');
					
					// ---------------------------------------------------------
					ob_clean();
					//Close and output PDF document
					$pdf->Output($receptionist_pf.' Performance Report_'.time().'.pdf', 'I');
				}
				
			}
			
		}
		else
		{
			$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, this doctor is not available </span>');
			return redirect($_SERVER['HTTP_REFERER']);
		}
		
	}
	
	public function lab_performance()
	{
		// Remember to make sure that this is receptionist otherwise not
		$lab_pf = $this->session->userdata('user_pf');
		$user_role = $this->session->userdata('user_role');
		if($user_role != 'LAB')
		{
			$this->form_validation->set_rules('pf', 'Receptionist PF', 'trim|required');
			if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$lab_pf = $this->security->xss_clean($this->input->post('pf'));
			}
		}
		$basic = $this->employee_model->get_employee_by_file_number($lab_pf);
		if(!empty($basic) && $basic['role_alias'] == 'LAB')
		{
			$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
			$this->form_validation->set_rules('end', 'End Date', 'trim|required');
			
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
				$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
				
				$res = [];
				$result = $this->patient_model->lab_performance($lab_pf, $start, $end);
				$this->load->model('investigation_model', 'investigation');
				foreach($result as $row)
				{
					$row['age'] = $this->compute_age($row['dob']);

					$dignosis = [];
					$inv = explode("^^", $row['diagnosis']);
					foreach ($inv as $key => $value)
					{						
						$split = explode("~", $value);
						$inv_token = $split[0];
						$inv_results = $split[1];
						$inv_data = $this->investigation->get_investigation_subcategories_by_token($inv_token);
						if($inv_results == 'null')
						{
							$inv_data['results'] = NULL;
						}
						else
						{
							$text = explode("&&", $inv_results);
							$inv_text = str_replace('@text:', '', $text[0]);						
							$inv_data['results'] = $inv_text;
						}
						$dignosis[] = $inv_data;
					}
					$row['diagnosis'] = $dignosis;

					$res[] = $row;
				}

				if(empty($res))
				{
					$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, currently a lab officer with PF "'.$lab_pf.'" has got no data.</span>');
					return redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$data = array(
						'title' => $lab_pf,
						'basic' => $basic,
						'data' => $res,
						'start' => $start,
						'end' => $end,
						'lab' => FALSE,
					);
					
					$html = $this->load->view('pages/e_reports/lab_report', $data, TRUE);
					
					$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
					
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('DMIS:'.$lab_pf);
					$pdf->SetTitle($lab_pf.'Lab Report');
					$pdf->SetSubject('NIT Dispensary Performance Report');
					$pdf->SetKeywords('DMIS, Performance report, NIT, Dispensary, '.$lab_pf);
					
					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
					
					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					
					
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					
					// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
					$pdf->SetMargins(20, 14, 16);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
					
					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					
					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
					
					// set font
					$pdf->SetFont('times', '', 12);
					
					// add a page
					$pdf->AddPage('L', 'A4');
					
					// print a block of text using Write()
					// $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
					$pdf->writeHTML($html, true, false, true, false, '');
					
					// ---------------------------------------------------------
					ob_clean();
					//Close and output PDF document
					$pdf->Output($lab_pf.' Performance Report_'.time().'.pdf', 'I');
					exit();
				}				
			}			
		}
		else
		{
			$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, this lab attendant is not available </span>');
			return redirect($_SERVER['HTTP_REFERER']);
		}		
	}
	
	public function pharmacy_performance()
	{
		// Remember to make sure that this is pharmacist otherwise not
		$ph_pf = $this->session->userdata('user_pf');
		$user_role = $this->session->userdata('user_role');
		if($user_role != 'PH')
		{
			$this->form_validation->set_rules('pf', 'Pharmacist PF', 'trim|required');
			if ($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$ph_pf = $this->security->xss_clean($this->input->post('pf'));
			}
		}
		$basic = $this->employee_model->get_employee_by_file_number($ph_pf);
		if(!empty($basic) && $basic['role_alias'] == 'PH')
		{
			$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
			$this->form_validation->set_rules('end', 'End Date', 'trim|required');
			
			if($this->form_validation->run() == FALSE)
			{
				$this->session->set_flashdata('error', validation_errors());
				return redirect($_SERVER['HTTP_REFERER']);
			}
			else
			{
				$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
				$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));
				
				$result = $this->patient_model->pharmacy_performance($ph_pf, $start, $end);
				if(empty($result))
				{
					$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, currently a pharmacist with PF "'.$ph_pf.'" has got no data.</span>');
					return redirect($_SERVER['HTTP_REFERER']);
				}
				else
				{
					$data = array(
						'title' => $ph_pf,
						'basic' => $basic,
						'data' => $result,
						'start' => $start,
						'end' => $end,
						'lab' => FALSE,
					);
					
					$html = $this->load->view('pages/e_reports/employee_performance', $data, TRUE);
					
					$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'utf-8', false);
					
					$pdf->SetCreator(PDF_CREATOR);
					$pdf->SetAuthor('DMIS:'.$ph_pf);
					$pdf->SetTitle($ph_pf.'Performance Report_');
					$pdf->SetSubject('NIT Dispensary Performance Report');
					$pdf->SetKeywords('DMIS, Performance report, NIT, Dispensary, '.$ph_pf);
					
					// set default header data
					$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
					
					// set header and footer fonts
					$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
					$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
					
					
					$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
					
					// $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
					$pdf->SetMargins(20, 14, 16);
					$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
					$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
					
					// set auto page breaks
					$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
					
					// set image scale factor
					$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
					
					// set font
					$pdf->SetFont('times', '', 12);
					
					// add a page
					$pdf->AddPage('', 'A4');
					
					// print a block of text using Write()
					// $pdf->Write(0, $txt, '', 0, 'C', true, 0, false, false, 0);
					$pdf->writeHTML($html, true, false, true, false, '');
					
					// ---------------------------------------------------------
					ob_clean();
					//Close and output PDF document
					$pdf->Output($ph_pf.' Performance Report_'.time().'.pdf', 'I');
					exit();
				}				
			}			
		}
		else
		{
			$this->session->set_flashdata('error', '<span class="text-danger"> Oops!, this pharmacist is not available </span>');
			return redirect($_SERVER['HTTP_REFERER']);
		}		
	}
	
	public function dmis_monitoring($header)
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				$data = [];
				
				$draw = intval($this->input->post("draw"));
				$start = intval($this->input->post("start"));
				$length = intval($this->input->post("length"));
				
				$result = $this->patient_model->get_monitor_data($this->input->post());
				
				$i = $this->input->post("start");
				foreach($result as $r)
				{
					$i++;
					$name = $r->pat_fname.' '.$r->pat_lname;
					$emergency = $r->pat_em_name.' | '.$r->pat_em_number;;
					
					$receptionist = '';
					$my_doctor = '';
					$lab = '';
					$pharmacy = '';
					
					
					if(empty($r->vs_visit))
					{
						$receptionist = 'Waiting';
					}
					
					if(!empty($r->vs_visit))
					{
						$text = $r->vs_visit;
						$attendants = $r->vs_attendants;
						$dates = $r->vs_time;
						
						$_a = explode("_", $attendants);
						$_b = explode("_", $dates);
						
						if($text == 'nasubiri_daktari' || $text == 'nimerudishwa_kutoka_ph' || $text == 'nimetoka_lab' || $text == 'nimerudishwa_kutoka_lab')
						{
							if($text == 'nasubiri_daktari')
							{
								$my_doctor = 'Waiting <code>(N)</code>';
							}
							else if($text == 'nimerudishwa_kutoka_ph')
							{
								$my_doctor = 'Waiting <code>(P)</code>';
							}
							else if($text == 'nimetoka_lab')
							{
								$my_doctor = 'Waiting <code>(L)</code>';
							}
							else
							{
								$my_doctor = 'Waiting <code>(LR)</code>';
							}
						}
						
						if($text == 'nipo_daktari_1' || $text == 'nipo_daktari_1r' || $text == 'nipo_daktari_2' || $text == 'nipo_daktari_2r')
						{
							if($text == 'nipo_daktari_1')
							{
								$doc_data = $this->get_employee_by_file_number($_a[0]);				
								$doctor = $doc_data['emp_lname'].', '.$doc_data['emp_fname'][0];
								$my_doctor = $doctor . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b[0]) . ')(1N)</code>';
							}
							else if($text == 'nipo_daktari_1r')
							{
								$doc_data = $this->get_employee_by_file_number($_a[0]);				
								$doctor = $doc_data['emp_lname'].', '.$doc_data['emp_fname'][0];
								$my_doctor = $doctor . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b[0]) . ')(1R)</code>';
							}
							else if($text == 'nipo_daktari_2')
							{
								$_a = $_a[count($_a) - 1];
								$_b = $_b[count($_b) - 1];
								$doc_data = $this->get_employee_by_file_number($_a);				
								$doctor = $doc_data['emp_lname'].', '.$doc_data['emp_fname'][0];
								$my_doctor = $doctor . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b) . ')(2N)</code>';
							}
							else 
							{
								$_a = $_a[count($_a) - 2];
								$_b = $_b[count($_b) - 2];
								$doc_data = $this->get_employee_by_file_number($_a);				
								$doctor = $doc_data['emp_lname'].', '.$doc_data['emp_fname'][0];
								$my_doctor = $doctor . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b) . ')(2R)</code>';
							}
						}
						
						if($text == 'nimetoka_daktari' || $text == 'nimetoka_daktari_r')
						{
							$pharmacy = $text == 'nimetoka_daktari' ? 'Waiting <code>(N)</code>' : 'Waiting <code>(R)</code>';
						}
						
						if($text == 'nipo_ph')
						{
							$_a = $_a[count($_a) - 1];
							$_b = $_b[count($_b) - 1];
							$pharmacist_data = $this->get_employee_by_file_number($_a);	
							$pharmacist = $pharmacist_data['emp_lname'].', '.$pharmacist_data['emp_fname'][0];
							$pharmacy = $pharmacist . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b) . ')(N)</code>';
						}
						
						if($text == 'naenda_lab' || $text == 'naenda_lab_r')
						{
							$lab = $text == 'naenda_lab' ? 'Waiting <code>(N)</code>' : 'Waiting <code>(R)</code>';
						}
						
						if($text == 'nipo_lab')
						{
							$_a = $_a[count($_a) - 1];
							$_b = $_b[count($_b) - 1];
							$lab_data = $this->get_employee_by_file_number($_a);	
							$lab_tech = $lab_data['emp_lname'].', '.$lab_data['emp_fname'][0];
							$lab = $lab_tech . '<br/> <code>(' . gmdate("y-m-d, H:i", $_b) . ')</code>';
						}
					}
					
					// $is_init = $r->vs_visit == 'nasubiri_daktari' ? TRUE : FALSE;
					// $init_btn = '<button type="button" class="btn btn-sm btn-primary fw-bold" name="initial_serve" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-box-arrow-in-right me-1"></i>Serve</button>';
					
					// $is_kwanza = $r->vs_visit == 'nipo_daktari_1' ? TRUE : FALSE;
					// $kwanza_btn = '<a href="'.base_url('doctor/my-session').'" type="button" class="btn btn-sm btn-warning fw-bold" name="stop_kwanza" data-id="'.$r->rec_id.'" data-patient="'.$full_name.'" data-file="'.$r->rec_patient_file.'" data-visit="'.$r->vs_id.'"><i class="bi bi-exclamation-triangle me-1"></i>Pending</a>';
					
					$data[] = array(
						// $i,
						$name . ' <code>(' . $r->pat_file_no . ')</code>',
						$receptionist,
						// empty($receptionist) ? 'Waiting' : $receptionist . '<br /> <code>('.date("Y-m-d H:i:s a", strtotime($r->rec_regdate)).')</code>',
						$my_doctor,
						$lab,
						$pharmacy,
						date('y-m-d, H:i', strtotime($r->rec_regdate)),
						$r->pat_address,
						$emergency,
					);
				}
				
				$result = array(
					"draw" => $draw,
					"recordsTotal" => $this->patient_model->countAllForMonitor(),
					"recordsFiltered" => $this->patient_model->countFilteredForMonitor($this->input->get()),
					"data" => $data
				);
				
				echo json_encode($result);
				exit();
			}
		}
		else
		{
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				try {
					$data = array(
						'title' => 'DMIS Monitor',
						'header' => @$header,
						'heading' => 'Pro',
						'subHeading' => 'Monitor',
					);
					$this->load->view('pages/e_reports/patient_monitoring', $data);					
				} catch (\Throwable $th) {
					$this->session->set_flashdata('error', $th->getMessage());
					return redirect($_SERVER['HTTP_REFERER']);         
				}
			}
			else
			{
				$this->session->set_flashdata('error', 'No access');
				return redirect($_SERVER['HTTP_REFERER']);
			}
		}
	}
	
	public function served_patients($header)
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{
			
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				$data = [];
				
				$draw = intval($this->input->post("draw"));
				$start = intval($this->input->post("start"));
				$length = intval($this->input->post("length"));
				
				$result = $this->patient_model->get_served_patients($this->input->post());
				
				$i = $this->input->post("start");
				foreach($result as $r)
				{
					$i++;
					$name = empty($r->pat_mname) ? $r->pat_lname . ', ' . $r->pat_fname : $r->pat_lname . ', ' . $r->pat_fname . ' ' . $r->pat_mname[0] . '.';
					
					$time_in = date('Y-m-d h:i:s a', strtotime($r->pat_regdate));
					
					$time_out = explode("_", $r->vs_time);
					$unix_time_out = $time_out[count($time_out) - 1];
					$time_out = date('Y-m-d h:i:s a', $unix_time_out);

					$dateTime1 = new DateTime($r->pat_regdate);
					$dateTime1 = $dateTime1->format('U');

					$span = timespan($dateTime1, $unix_time_out, 1);
					
					$data[] = array(
						// $i,
						$name,
						$r->pat_file_no,
						$r->pat_phone,
						$r->pat_gender[0],
						$time_in,
						$time_out,
						$span,
						$r->pat_occupation,
						$r->pat_address,
						$r->pat_em_name . ' (' .$r->pat_em_number . ')',
					);
				}
				
				$result = array(
					"draw" => $draw,
					"recordsTotal" => $this->patient_model->countAllServedPatients(),
					"recordsFiltered" => $this->patient_model->countFilteredAllServedPatients($this->input->get()),
					"data" => $data
				);
				
				echo json_encode($result);
				exit();
			}
		}
		else
		{
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				try {
					$data = array(
						'title' => 'Treated Patients',
						'header' => @$header,
						'heading' => 'Pro',
						'subHeading' => 'Treated Patients',
					);
					$this->load->view('pages/e_reports/treated_patients', $data);					
				} catch (\Throwable $th) {
					$this->session->set_flashdata('error', $th->getMessage());
					return redirect($_SERVER['HTTP_REFERER']);         
				}
			}
			else
			{
				$this->session->set_flashdata('error', 'No access');
				return redirect($_SERVER['HTTP_REFERER']);
			}
		}		
	}
	
	public function incomplete_patients($header)
	{
		if($this->input->server('REQUEST_METHOD') === 'POST')
		{			
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				$data = [];
				
				$draw = intval($this->input->post("draw"));
				$start = intval($this->input->post("start"));
				$length = intval($this->input->post("length"));
				
				$result = $this->patient_model->get_incomplete_patients($this->input->post());
				
				$i = $this->input->post("start");
				foreach($result as $r)
				{
					$i++;
					$name = empty($r->pat_mname) ? $r->pat_lname . ', ' . $r->pat_fname : $r->pat_lname . ', ' . $r->pat_fname . ' ' . $r->pat_mname[0] . '.';			
					$time_in = date('Y-m-d H:i', strtotime($r->pat_regdate));
					
					$f_point = '<a class="text-danger">';
					if(empty($r->vs_visit))
					{
						$f_point .= 'Reception';
					}
					else
					{
						$text = $r->vs_visit;						
						if($text == 'nasubiri_daktari' || $text == 'nimerudishwa_kutoka_ph' || $text == 'nimetoka_lab' || $text == 'nimerudishwa_kutoka_lab' || $text == 'nipo_daktari_1' || $text == 'nipo_daktari_1r' || $text == 'nipo_daktari_2' || $text == 'nipo_daktari_2r') $f_point .= 'Doctor';

						else if($text == 'nimetoka_daktari' || $text == 'nimetoka_daktari_r' || $text == 'nipo_ph') $f_point .= 'Pharmacy';

						else $f_point .= 'Lab';
					}
					$f_point .= '</a>';

					$option = '<a href="#" name="resetIncomplete" data-id="'.$r->rec_id.'" class="text-primary">Reset</a>';
					
					$data[] = array(
						// $i,
						$name,
						$r->pat_file_no,
						$r->pat_phone,
						$r->pat_occupation,
						$time_in,
						$f_point,
						$option,
						$r->pat_address,
						$r->pat_gender[0],
						$r->pat_em_name . ' (' .$r->pat_em_number . ')',
					);
				}
				
				$result = array(
					"draw" => $draw,
					"recordsTotal" => $this->patient_model->countAllIncompletePatients(),
					"recordsFiltered" => $this->patient_model->countFilteredAllIncompletePatients($this->input->get()),
					"data" => $data
				);
				
				echo json_encode($result);
				exit();
			}
		}
		else
		{
			if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
			{
				try {
					$data = array(
						'title' => 'Incomplete Patients',
						'header' => @$header,
						'heading' => 'Pro',
						'subHeading' => 'Incomplete Patients',
					);
					$this->load->view('pages/e_reports/incomplete_patients', $data);					
				} catch (\Throwable $th) {
					$this->session->set_flashdata('error', $th->getMessage());
					return redirect($_SERVER['HTTP_REFERER']);         
				}
			}
			else
			{
				$this->session->set_flashdata('error', 'No access');
				return redirect($_SERVER['HTTP_REFERER']);
			}
		}		
	}
	
	public function count_patients($distribution)
	{
		try {
			$dist = (int)$this->security->xss_clean($distribution);
			if($dist == 1 || $dist == 2 || $dist == 3)
			{
				$data = '';
				
				if($dist == 1)
				{
					$date = date("Y-m-d");
					$prev_date = date('Y-m-d', strtotime(date('Y-m-d')." -1 day"));

					$result = $this->patient_model->countAllServedDashboard($dist, $date);
					$prev_result = $this->patient_model->countAllServedDashboard($dist, $prev_date);

					$diff = $result - $prev_result;
					$percent = 100;
					if($result == 0 && $prev_result == 0) $percent = 0;
					if($prev_result != 0) $percent = ($diff / $prev_result) * 100;
					
					$data = array(
						'dist' => $dist,
						'total' => $result,
						'percent' => $percent
					);
				}
				else if($dist == 2)
				{
					$date = date("m");
					$prev_date = date('m', strtotime(date('Y-m')." -1 month"));

					$result = $this->patient_model->countAllServedDashboard($dist, $date);
					$prev_result = $this->patient_model->countAllServedDashboard($dist, $prev_date);

					$diff = $result - $prev_result;
					$percent = 100;
					if($result == 0 && $prev_result == 0) $percent = 0;
					if($prev_result != 0) $percent = ($diff / $prev_result) * 100;
					
					$data = array(
						'dist' => $dist,
						'total' => $result,
						'percent' => $percent
					);
					
				}
				else
				{
					$date = date("Y");
					$prev_date = date('Y', strtotime(date('Y-m')." -1 year"));

					$result = $this->patient_model->countAllServedDashboard($dist, $date);
					$prev_result = $this->patient_model->countAllServedDashboard($dist, $prev_date);

					$diff = $result - $prev_result;
					$percent = 100;
					if($result == 0 && $prev_result == 0) $percent = 0;
					if($prev_result != 0) $percent = ($diff / $prev_result) * 100;
					
					$data = array(
						'dist' => $dist,
						'total' => $result,
						'percent' => $percent
					);
				}
				
				echo json_encode(array("status" => TRUE, 'data' => $data));
				exit();
			}
			else
			{
				echo json_encode(array("status" => FALSE, 'data' => 'Out of range'));
				exit();
			}
		} catch (\Throwable $th) {
			echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
			exit();      
		}
		
	}
	
	public function reset_incomplete_patient($record)
	{
		try {
			$record = $this->security->xss_clean($record);
			if(!$this->eligibleToResetRecord($record))
			{
				echo json_encode(array("status" => FALSE, 'data' => '<code>Action not allowed</code>'));
				exit();
			}
			else
			{
				$symptomTable = $this->patient_model->symptoms_data_by_record_id($record);
				$visitTable = $this->patient_model->visit_data_by_recordId($record);
				
				$this->patient_model->deleteById('patient_record', 'rec_id', $record);
				if(!empty($symptomTable)) $this->patient_model->deleteById('patient_symptoms', 'sy_id', $symptomTable['sy_id']);
				if(!empty($visitTable)) $this->patient_model->deleteById('patient_visit', 'vs_id', $visitTable['vs_id']);

				echo json_encode(array("status" => TRUE, 'data' => '<code>Success</code>'));
				exit();
			}
		} catch (\Throwable $th) {
			echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
			exit();      
		}
	}
	
	public function database_backup($header)
	{
		if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
		{
			if($this->input->server('REQUEST_METHOD') === 'POST')
			{
				$db_table = $this->employee_model->get_db_backup();

				$backup_file = 'DB File';
				$html = '';
				$html .= "<tr>";
				$html .= "<td><code>".$db_table->file_name."</code></td>";
				$html .= "<td>".$db_table->size." (compressed)</td>";
				$html .= "<td>".$db_table->day."</td>";
				$html .= "<td>".$db_table->author."</td>";
				$html .= '<td><a type="button" href="' . base_url('reports/download-db-backup') . '" class="btn btn-outline-secondary btn-sm" name="download"><i class="bi bi-save me-1"></i> Download</a>&nbsp;<a type="button" href="#" class="btn btn-primary btn-sm" name="backup"><i class="bi bi-cloud-arrow-down me-1"></i> Backup</a></td>';
				$html .= "</tr>";
				
				echo json_encode($html);
				exit();
			}
			else
			{
				$data = array(
					'title' => 'Database Backup',
					'header' => @$header,
					'heading' => 'Pro',
					'subHeading' => 'Database Backup',
				);
				$this->load->view('pages/e_reports/db_backup', $data);
			}
		}		
	}

	public function start_database_backup()
	{
		if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
		{
			try {

				ini_set('display_errors', 1);
				ini_set('display_startup_errors', 1);
				error_reporting(E_ALL);

				$database = $this->db->database;
				$user = $this->db->username;
				$pass = $this->db->password;
				$host = $this->db->hostname;
				$dir = FCPATH.'uploads/db-backup/';
				$filename = 'DMIS-Database-Backup.sql.gz';

				$return_var = NULL;
				$output = NULL;
				$command = "/usr/bin/mysqldump -u {$user} -p{$pass} -h {$host} {$database} | gzip > {$dir}{$filename}";
				// exec($command, $output, $return_var);
				exec($command);
				clearstatcache();
				$size = filesize($dir . $filename);
				if($size != FALSE) $size = $this->formatSizeUnits($size);

				$data = array(
					'db_id' => 1,
					'db_file' => $filename,
					'db_size' => $size,
					'db_author' => $this->session->userdata('user_pf'),
				);
				$this->employee_model->update_db_backup($data);
				
				echo json_encode(array("status" => TRUE, 'data' => '<span class="text-success">Success</span>'));
				exit();
			}
			catch (\Throwable $th) {
				echo json_encode(array("status" => FALSE, 'data' => $th->getMessage()));
				exit();      
			}
		}
	}

	public function download_database_backup()
	{
		$this->load->helper('download');
		$dir = FCPATH.'uploads/db-backup/';
		$filename = 'DMIS-Database-Backup.sql.gz';
		$backup_path = $dir . $filename;
		force_download($backup_path, NULL);
	}

	public function general_report($header)
	{
		if($this->session->userdata('user_isIncharge') || $this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
		{
			$data = array(
				'title' => 'General Report',
				'header' => @$header,
				'heading' => 'Pro',
				'subHeading' => 'General Report',
			);
			$this->load->view('pages/e_reports/general_report', $data);
		}
		else
		{
			$this->session->set_flashdata('error', 'No access');
			return redirect($_SERVER['HTTP_REFERER']);
		}
	}

	public function age_gender()
	{
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
		}
		else
		{
			$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
			$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));

			$group_1 = [0, 1];
			$group_2 = [1, 5];
			$group_3 = [5, 60];
			$group_4 = [60, 0];

			$res_group_1 = $this->patient_model->age_gender_report($start, $end, $group_1);
			$res_group_2 = $this->patient_model->age_gender_report($start, $end, $group_2);
			$res_group_3 = $this->patient_model->age_gender_report($start, $end, $group_3);
			$res_group_4 = $this->patient_model->age_gender_report($start, $end, $group_4);

			$result = array(
				'group_1' => $res_group_1,
				'group_2' => $res_group_2,
				'group_3' => $res_group_3,
				'group_4' => $res_group_4,
			);			
			echo json_encode(array("status" => TRUE, 'data' => $result));
			exit();
		}
	}

	public function disease_distribution()
	{
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
		}
		else
		{
			$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
			$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));

			$res_group_1 = $this->patient_model->disease_distribution_report($start, $end);			
			echo json_encode(array("status" => TRUE, 'data' => $res_group_1));
			exit();
		}
	}
	
	public function lab_and_non_lab()
	{
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
		}
		else
		{
			$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
			$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));

			$res_1 = $this->patient_model->lab_and_non_lab_report($start, $end);			
			$res_2 = $this->patient_model->lab_and_non_lab_report($start, $end, FALSE);

			$results = array(
				'lab' => $res_1,
				'nonLab' => $res_2,
			);	
			echo json_encode(array("status" => TRUE, 'data' => $results));
			exit();
		}
	}
	
	public function diagnosis_report()
	{
		$this->form_validation->set_rules('start', 'Start Date', 'trim|required');
		$this->form_validation->set_rules('end', 'End Date', 'trim|required');
		
		if ($this->form_validation->run() == FALSE)
		{
			echo json_encode(array("status" => FALSE, 'data' => validation_errors()));
            exit();
		}
		else
		{
			$start = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('start'))));
			$end = date('Y-m-d', strtotime($this->security->xss_clean($this->input->post('end'))));

			$result = $this->patient_model->diagnosis_report($start, $end);			
			echo json_encode(array("status" => TRUE, 'data' => $result));
			exit();
		}
	}
	
}