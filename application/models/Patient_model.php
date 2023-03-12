<?php

class Patient_model extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->load->database();

        // Set searchable column fields for receptionist
        $this->column_search = array('p.pat_file_no', 'p.pat_fname', 'p.pat_mname', 'p.pat_lname', 'p.pat_phone');
        
        // Set searchable column fields for doctor
        $this->column_search_doctor = array('p.pat_file_no', 'p.pat_fname', 'p.pat_mname', 'p.pat_lname', 'p.pat_phone');
        
        //  Set searchable column fields for lab
        $this->column_search_lab = array('p.pat_file_no', 'p.pat_fname', 'p.pat_mname', 'p.pat_lname', 'p.pat_phone');
        
        //  Set searchable column fields for pharmacy
        $this->column_search_ph = array('p.pat_file_no', 'p.pat_fname', 'p.pat_mname', 'p.pat_lname', 'p.pat_phone');
    }

    public function check_if_uuid_exist_patient_table($uuid)
    {
        $this->db->where('pat_id', $uuid);
        $query = $this->db->get('patient');
        return $query->num_rows() > 0;
    }
    
    public function check_if_uuid_exist_record_table($uuid)
    {
        $this->db->where('rec_id', $uuid);
        $query = $this->db->get('patient_record');
        return $query->num_rows() > 0;
    }

    public function check_if_uuid_exist_visit_table($uuid)
    {
        $this->db->where('vs_id', $uuid);
        $query = $this->db->get('patient_visit');
        return $query->num_rows() > 0;
    }

    public function check_if_uuid_exist_symptoms_table($uuid)
    {
        $this->db->where('sy_id', $uuid);
        $query = $this->db->get('patient_symptoms');
        return $query->num_rows() > 0;
    }
    
    public function search_patient_by_keyword($keyword)
    {
        $this->db->select('pat_id as id, pat_fname as first_name, pat_mname as middle_name, pat_lname as last_name, 
            pat_file_no as pf, pat_phone as phone, pat_address as address, pat_nhif_card_no as nhif');
        $this->db->like('pat_file_no', $keyword, 'both');
        $this->db->or_like('pat_fname', $keyword, 'both');
        $this->db->or_like('pat_lname', $keyword, 'both');
        $this->db->or_like('pat_phone', $keyword, 'both');
        $this->db->or_like('pat_nhif_card_no', $keyword, 'both');
        $query = $this->db->get('patient');
        return $query->result_array();
    }
    
    public function search_patient_instance_by_keyword($keyword)
    {
        $this->db->select('p.pat_id as id, p.pat_fname as first_name, p.pat_mname as middle_name, p.pat_lname as last_name, p.pat_address as address, p.pat_nhif_card_no as nhif, r.rec_regdate as entry, r.rec_id as record, r.rec_patient_file as pf, r.rec_blood_pressure as bp, r.rec_pulse_rate as pr, r.rec_weight as weight, r.rec_height as height, r.rec_temeperature as temp, r.rec_respiration as respiration, v.vs_visit as status');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_blood_pressure IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature IS NOT NULL', NULL, FALSE);
        $this->db->like('r.rec_patient_file', $keyword, 'both');
        $this->db->or_like('p.pat_fname', $keyword, 'both');
        $this->db->or_like('p.pat_lname', $keyword, 'both');
        $this->db->or_like('p.pat_nhif_card_no', $keyword, 'both');
        $this->db->order_by('r.rec_regdate', 'DESC');
        $query = $this->db->get('patient_record r');
        $data = array();
        foreach ($query->result_array() as $row)
        {
            if(isset($row['status']))
            {
                $data[] = $row;
            }
        }
        return $data;
        // return $query->result_array();
    }
    
    public function get_patient_by_id($id)
    {
        $this->db->where('pat_id', $id);
        $query = $this->db->get('patient');
        return $query->row_array();      
    }
    
    public function get_patient_by_recordId($id)
    {
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->where('rec_id', $id);
        $query = $this->db->get('patient_record r');
        return $query->row_array();     
    }
    
    public function get_id_by_file_number($file_no)
    {
        $this->db->where('pat_file_no', $file_no);
        $query = $this->db->get('patient');
        return $query->row_array();
    }
    
    public function checkPhoneExist($phone)
    {
        $this->db->where('pat_phone', $phone);
        $this->db->from('patient');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkIfFileNumberExist($file_number)
    {
        $this->db->where('pat_file_no', $file_number);
        $query = $this->db->get('patient');
        return $query->num_rows() > 0;
    }

    public function getLastFileNumber()
    {
        // SELECT MAX(CAST(SUBSTRING(invoice_number, 4, length(invoice_number)-3) AS UNSIGNED)) FROM table

        $this->db->select_max('pat_file_no', 'last_pf');
        $query = $this->db->get('patient');
        $data = $query->result_array();
        return $data[0]['last_pf'];
    }
    
    public function update_patient_records($data, $id)
    {
        $this->db->where('pat_id', $id);
        $query = $this->db->update('patient', $data);
        if($query) return TRUE;
        else return FALSE;
    }
    
    public function create_patient_instance($data)
    {
        $this->db->insert('patient_record', $data);
    }
    
    public function insert_new_patient($data)
    {
        $this->db->insert('patient', $data);
        $insert_id = $this->db->insert_id();
        return $insert_id;
    }
    
    
    ##########################################################
    // datatable issues start
    
    // for receptionist
    public function get_my_patients($postData)
    {   
        $this->_get_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    // for doctor
    public function get_doctor_patients_from_reception($postData)
    {   
        $this->_get_doctor_patients_from_reception_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function get_doctor_patients_from_lab($postData)
    {   
        $this->_get_doctor_patients_from_lab_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function get_doctor_patients_lab_returns($postData)
    {   
        $this->_get_doctor_patients_lab_returns_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        
        $query = $this->db->get();
        return $query->result();  
        
        // $data = array();
        // foreach ($query->result() as $key => $row)
        // {
        //     $attendants = $row->vs_attendants;
        //     $attendants_array = explode("_", $attendants);
        //     $doctor = $attendants_array[count($attendants_array)-2];
        //     if($doctor == $this->session->userdata('user_pf'))
        //     {
        //         $data[] = $row;
        //     }
        // }
        // return $data;
    }
    public function get_doctor_patients_ph_returns($postData)
    {   
        $this->_get_doctor_patients_ph_returns_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
        
        // $data = array();
        // foreach ($query->result() as $key => $row)
        // {
        //     $attendants = $row->vs_attendants;
        //     $attendants_array = explode("_", $attendants);
        //     $doctor = $attendants_array[count($attendants_array)-2];
        //     if($doctor == $this->session->userdata('user_pf'))
        //     {
        //         $data[] = $row;
        //     }
        // }
        // return $data;
    }

    public function get_list_of_editable_lab_diagnosis($postData)
    {   
        $this->_get_list_of_editable_lab_diagnosis_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    public function get_doctor_inpatients($postData)
    {   
        $this->_get_doctor_inpatients_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    public function get_doctor_session_patients($postData)
    {   
        $this->_get_datatables_query_doctor_session_patients($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
        
    }
    
    // for lab 
    public function get_my_patients_lab($postData)
    {   
        $this->_get_datatables_query_for_lab($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
        
    }
    
    // for pharmacy 
    public function get_my_patients_ph($postData)
    {   
        $this->_get_datatables_query_for_ph($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    // for receptionist
    public function countAll()
    {
        $this->db->from("patient_record");
        $this->db->where('rec_blood_pressure IS NULL', NULL, FALSE);
        $this->db->or_where('rec_pulse_rate is NULL', NULL, FALSE);
        $this->db->or_where('rec_weight is NULL', NULL, FALSE);
        $this->db->or_where('rec_height is NULL', NULL, FALSE);
        $this->db->or_where('rec_temeperature is NULL', NULL, FALSE);
        return $this->db->count_all_results();
    }
    
    // for doctor
    public function countAllForDoctorOne()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nasubiri_daktari');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_1');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        // $this->db->group_end();
        // $this->db->group_end();
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NULL', NULL, FALSE);
        return $this->db->count_all_results();
    }
    public function countAllForDoctorTwo()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->where('v.vs_visit', 'nimetoka_lab');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimetoka_lab');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();      
        return $this->db->count_all_results();
    }
    public function countAllForDoctorLabReturn()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_lab');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();
        return $this->db->count_all_results();
        // $query = $this->db->get();
        // $data = array();
        // foreach ($query->result() as $key => $row)
        // {
        //     $attendants = $row->vs_attendants;
        //     $attendants_array = explode("_", $attendants);
        //     $doctor = $attendants_array[count($attendants_array)-2];
        //     if($doctor == $this->session->userdata('user_pf'))
        //     {
        //         $data[] = $row;
        //     }
        // }
        // return count($data);
    }
    
    public function countAllForDoctorPhReturn()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_ph');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_ph');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();
        return $this->db->count_all_results();
        // $query = $this->db->get();
        // $data = array();
        // foreach ($query->result() as $key => $row)
        // {
        //     $attendants = $row->vs_attendants;
        //     $attendants_array = explode("_", $attendants);
        //     $doctor = $attendants_array[count($attendants_array)-2];
        //     if($doctor == $this->session->userdata('user_pf'))
        //     {
        //         $data[] = $row;
        //     }
        // }
        // return count($data);
    }
    public function count_All_Editable_Patients()
    {      
        $this->db->from('patient_record r');
        // $this->db->select('CONCAT(p.pat_fname, " ", p.pat_lname) as full_name, p.pat_address as address, p.pat_file_no as pf, r.rec_regdate as entry, r.rec_id as record, v.vs_visit as status');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_blood_pressure IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        return $this->db->count_all_results();
    }
    public function count_All_InPatients()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 10 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NULL', NULL, FALSE);
        return $this->db->count_all_results();
    }
    public function count_All_Session_Patients()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);

        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('v.vs_attendants', $this->session->userdata('user_pf'));
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        return $this->db->count_all_results();
    }
    
    // for lab
    public function countAllForLab()
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_care', 0);
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        return $this->db->count_all_results();
    }
    
    // for pharmacy
    public function countAllForPh()
    {        
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_care', 0);
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        return $this->db->count_all_results();
    }
    
    // for receptionist
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    // for doctor
    public function countFilteredForDoctorOne($postData)
    {
        $this->_get_doctor_patients_from_reception_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFilteredForDoctorTwo($postData)
    {
        $this->_get_doctor_patients_from_lab_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFilteredForDoctorLabReturn($postData)
    {
        $this->_get_doctor_patients_lab_returns_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFilteredForDoctorPhReturn($postData)
    {
        $this->_get_doctor_patients_ph_returns_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFiltered_InPatients($postData)
    {
        $this->_get_doctor_inpatients_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFilteredEditablePatients($postData)
    {
        $this->_get_list_of_editable_lab_diagnosis_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function countFilteredSessionPatients($postData)
    {
        $this->_get_datatables_query_doctor_session_patients($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    // for lab
    public function countFilteredForLab($postData)
    {
        $this->_get_datatables_query_for_lab($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    // for pharmacy
    public function countFilteredForPh($postData)
    {
        $this->_get_datatables_query_for_ph($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    // for receptionist
    private function _get_datatables_query($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        
        $this->db->where('r.rec_blood_pressure IS NULL', NULL, FALSE);
        $this->db->or_where('r.rec_pulse_rate is NULL', NULL, FALSE);
        $this->db->or_where('r.rec_weight is NULL', NULL, FALSE);
        $this->db->or_where('r.rec_height is NULL', NULL, FALSE);
        $this->db->or_where('r.rec_temeperature is NULL', NULL, FALSE);
        $this->db->order_by('r.rec_regdate', 'ASC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    // for doctor
    private function _get_doctor_patients_from_reception_datatables_query($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NULL', NULL, FALSE);
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nasubiri_daktari');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_1');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        // $this->db->group_end();
        // $this->db->group_end();
        
        $this->db->order_by('r.rec_regdate');
        // $this->db->order_by('r.rec_regdate', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }  
    
    private function _get_doctor_patients_from_lab_datatables_query($postData){

        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->where('v.vs_visit', 'nimetoka_lab');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimetoka_lab');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();
        $this->db->order_by('r.rec_regdate');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    private function _get_doctor_patients_lab_returns_datatables_query($postData){

        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_lab');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();
        $this->db->order_by('r.rec_regdate');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    private function _get_list_of_editable_lab_diagnosis_datatables_query($postData)
    {        
        $this->db->from('patient_record r');
        $this->db->select('CONCAT(p.pat_fname, " ", p.pat_lname) as full_name, p.pat_address as address, p.pat_file_no as pf, p.pat_gender as gender, r.rec_regdate as entry, r.rec_id as record, v.vs_visit as status');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_blood_pressure IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature IS NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->order_by('r.rec_regdate', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    private function _get_doctor_patients_ph_returns_datatables_query($postData)
    {        
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_ph');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_start();
        // $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_ph');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->or_group_start();
        // $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        // $this->db->group_end();
        // $this->db->group_end();
        $this->db->order_by('r.rec_regdate');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    private function _get_doctor_inpatients_datatables_query($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 10 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 1);
        $this->db->where('s.sy_investigations is NULL', NULL, FALSE);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_wodini');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        
        // $this->db->order_by('r.rec_regdate');
        $this->db->order_by('r.rec_regdate', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    private function _get_datatables_query_doctor_session_patients($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);

        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('v.vs_attendants', $this->session->userdata('user_pf'));
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        
        // $this->db->order_by('r.rec_regdate');
        $this->db->order_by('r.rec_regdate', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_doctor as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_doctor) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    ##############################################
    ###   DOCTOR END #############################
    ##############################################
    
    // for lab      
    private function _get_datatables_query_for_lab($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_care', 0);
        $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->order_by('r.rec_regdate');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_lab as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_lab) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    // for pharmacy      
    private function _get_datatables_query_for_ph($postData)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_care', 0);
        $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
        // $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->order_by('r.rec_regdate');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_ph as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0){
                    // open bracket
                    $this->db->group_start();
                    $this->db->like($item, $postData['search']['value']);
                }else{
                    $this->db->or_like($item, $postData['search']['value']);
                }
                
                // last loop
                if(count($this->column_search_ph) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    // datatable issues end
    ##########################################################
    
    
    public function set_patient_preliminary_tests($data, $id)
    {
        $this->db->where('rec_id', $id);
        $query = $this->db->update('patient_record', $data);
        if($query) return TRUE;
        else return FALSE;
    }
    
    public function initiate_visit($data)
    {
        $this->db->insert('patient_visit', $data);
    }
    public function initiate_symptoms($data)
    {
        $this->db->insert('patient_symptoms', $data);
    }
    
    public function checkIfReceptionCanDeleteRecord($record_id)
    {
        $this->db->from("patient_record");
        $this->db->where('rec_id', $record_id);
        $this->db->group_start();
        $this->db->where('rec_blood_pressure IS NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('rec_pulse_rate is NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('rec_weight is NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('rec_height is NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('rec_temeperature is NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    
    public function checkIfReceptionCanDeleteRecord2($record_id)
    {
        $this->db->from("patient_record r");
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        $this->db->where('r.rec_blood_pressure IS NOT NULL', NULL, FALSE);
        $this->db->or_where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->or_where('r.rec_weight is NOT NULL', NULL, FALSE);
        $this->db->or_where('r.rec_height is NOT NULL', NULL, FALSE);
        $this->db->or_where('r.rec_temeperature is NOT NULL', NULL, FALSE);
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function check_Patient_Is_Init($record_id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nasubiri_daktari');
        $this->db->from('patient_record r');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function check_Patient_Is_FromLab($record_id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nimetoka_lab');
        $this->db->where('s.sy_lab', 1);
        $this->db->where('s.sy_investigations IS NOT NULL', NULL);
        $this->db->from('patient_record r');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function check_Patient_Is_LabReturn($record_id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_lab');
        $this->db->where('s.sy_lab', 1);
        $this->db->where('s.sy_investigations IS NOT NULL', NULL);
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->from('patient_record r');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function check_Patient_Is_PhReturn($record_id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nimerudishwa_kutoka_ph');
        // $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'after');
        $this->db->where('s.sy_diseases IS NOT NULL', NULL);
        $this->db->from('patient_record r');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function patientRecord_data_by_pkid($id)
    {
        $this->db->where('rec_id', $id);
        $this->db->from('patient_record');
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function visit_data_by_pkid($id)
    {
        $this->db->where('vs_id', $id);
        $this->db->from('patient_visit');
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function visit_data_by_recordId($record)
    {
        $this->db->where('vs_record_id', $record);
        $this->db->from('patient_visit');
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function symptoms_data_by_pkid($id)
    {
        $this->db->where('sy_id', $id);
        $this->db->from('patient_symptoms');
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function symptoms_data_by_record_id($id)
    {
        $this->db->where('sy_record_id', $id);
        $this->db->from('patient_symptoms');
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function update_patient_visit($data, $id)
    {
        $this->db->where('vs_id', $id);
        $query = $this->db->update('patient_visit', $data);
        if($query) return TRUE;
        else return FALSE;
    }
    
    public function check_doctor_with_client($doctor_pf)
    {
        $this->db->group_start();
        $this->db->where('vs_visit', 'nipo_daktari_1');
        $this->db->where('vs_attendants', $doctor_pf);
        $this->db->or_group_start();
        $this->db->where('vs_visit', 'nipo_daktari_1r');
        $this->db->like('vs_attendants', $doctor_pf, 'after');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('vs_visit', 'nipo_daktari_2');
        $this->db->like('vs_attendants', $doctor_pf, 'before');
        $this->db->or_group_start();
        $this->db->where('vs_visit', 'nipo_daktari_2r');
        $this->db->like('vs_attendants', $doctor_pf, 'after');
        // ssss
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->from('patient_visit');
        $query = $this->db->get();
        return $query->num_rows() > 0;
        // $data = array();
        // foreach ($query->result_array() as $key => $row)
        // {
        //     if($row['vs_visit'] == 'nipo_daktari_2r')
        //     {
        //         $attendants = $row['vs_attendants'];
        //         $attendants_array = explode("_", $attendants);
        //         $doctor = $attendants_array[count($attendants_array)-2];
        //         if($doctor == $doctor_pf)
        //         {
        //             $data[] = $row;
        //         }
        //     }
        //     else
        //     {
        //         $data[] = $row;
        //     }
        // }
        // return count($data) > 0;
    }
    
    public function get_session_patients_min_info($doctor_pf, $record)
    {
        $this->db->select('r.rec_id as record, rec_patient_file as file, rec_patient_id as patient');
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('v.vs_attendants', $doctor_pf);
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->like('v.vs_attendants', $doctor_pf, 'after');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $doctor_pf, 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        $this->db->like('v.vs_attendants', $doctor_pf, 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        return $query->row_array();
    }
    
    public function get_full_session_info($record_id)
    { 
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $query = $this->db->get();
        $row = $query->row_array();
        
        $row['complaints'] = array();
        $row['diseases'] = array();
        $row['medicines'] = array();
        $row['inv_results'] = array();
        
        // Configure complaints data
        if($row['sy_complaints'] != NULL)
        {
            $this->load->model('complaints_model', 'complaints');
            $complaints_array = explode("_", $row['sy_complaints']);
            foreach ($complaints_array as $value)
            {
                $comp_arr = explode("~", $value);
                $token = explode(":", $comp_arr[0]);
                $comp_token = $token[0];
                $comp_duration = $token[1];
                $comp_text = $comp_arr[1];
                $comp_data = $this->complaints->getComplaintByToken($comp_token);
                if(!empty((Array)$comp_data))
                {
                    $comp_data->history = $comp_text == 'null' ? NULL : str_replace('$$$', ' ', $comp_text);
                    $comp_data->duration = str_replace('$$$', ' ', $comp_duration);
                    $row['complaints'][] = $comp_data;
                }
            }
        }
        
        // Configure diseases data
        if($row['sy_diseases'] != NULL)
        {
            $this->load->model('disease_model', 'disease');
            $diseases_array = explode("_", $row['sy_diseases']);
            foreach ($diseases_array as $value)
            {
                $disease_data = $this->disease->getDiseaseByToken(trim($value));
                $row['diseases'][] = $disease_data;
            }
        }
        
        // Configure medicines data
        if($row['sy_medicines'] != NULL)
        {
            // $this->load->model('medicine_model', 'medicine');
            $this->load->model('stock_model', 'stock');
            $medicine_array = explode("_", $row['sy_medicines']);
            foreach ($medicine_array as $value)
            {
                $arryStr = explode("~", $value);
                $stock_token = $arryStr[0];
                $doctor_desc = $arryStr[1];
                $doctor_desc = str_replace('$', ' ', $doctor_desc);
                
                if($stock_token == '10000001')
                {
                    $nill_stock = array(
                        'token' => '10000001',
                        'medicine1' => 'Out of stock',
                        'medicine2' => 'O/S',
                        'doctor_desc' => $doctor_desc,
                    );
                    $row['medicines'][] = $nill_stock; 
                }
                else
                {
                    $stock = $this->stock->getStockByToken($stock_token);
                    $stock->doctor_desc = $doctor_desc;
                    $row['medicines'][] = $stock;
                }
            }
        }
        
        // Configure investigations data
        if($row['sy_investigations'] != NULL)
        {
            $inv_data = explode("^^", $row['sy_investigations']);
            // $category_investigations = explode("~", $inv_data[0]);
            // $investigations = $category_investigations[1];
            // if($investigations != 'null')
            // {
            foreach ($inv_data as $value)
            {
                $row['inv_results'][] = $value;
            }
                // }
        }            
        return $row;
    }

    public function patient_history_ph($rowno, $rowperpage, $patient)
    {
        $this->db->select('r.rec_id as record, DATE_FORMAT(r.rec_regdate, "%b %d %Y %H:%i") as entry, s.sy_medicines as medicines');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_id', $patient);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $this->db->order_by('r.rec_regdate', 'DESC');
        $this->db->limit($rowperpage, $rowno);
        $query = $this->db->get('patient_record r');
        $data = [];
        if($query->num_rows() > 0)
        {
            $this->load->model('stock_model', 'stockModel');
            foreach ($query->result() as $row)
            {
                $patient_medicines = []; 
                if(!empty($row->medicines))
                {     
                    $medicines = explode("_", $row->medicines);
                    foreach ($medicines as $stock)
                    {
                        $arryStr = explode("~", $stock);
                        $med_stock_code = $arryStr[0];
                        $doctor_desc = $arryStr[1];
                        $doctor_desc = str_replace('$', ' ', $doctor_desc);
                        $doctor_desc = str_replace('+++', '', $doctor_desc);
                        if($med_stock_code != 10000001)
                        {
                            $stock_data = $this->stockModel->getStockByToken($med_stock_code);
                            if(!empty($stock_data))
                            {
                                // $medicine_data = $this->medicineModel->getMedicineByToken($stock_data->medicine);
                                $usage = $this->stockModel->getStockUsageByRecordAndStock($row->record, $stock_data->id);
                                $total_usage = 0;
                                foreach ($usage as $cons)
                                {
                                    $total_usage += $cons->used;
                                }
                                    // $medicine_data['unit'] = $stock_data->title;
                                $stock_data->doctor_desc = $doctor_desc;
                                $stock_data->consumption = $total_usage;
                                $patient_medicines[] = $stock_data;
                            }
                            else
                            {
                                $medicine_data['text'] = NULL;
                                $medicine_data['doctor_desc'] = '<em>(Medicine/stock removed)</em>&nbsp;'.$doctor_desc;
                                $patient_medicines[] = $medicine_data;
                            }                                    
                        }
                        else
                        {
                            $medicine_data['text'] = NULL;
                            $medicine_data['doctor_desc'] = $doctor_desc;
                            $patient_medicines[] = $medicine_data;
                        }
                    }                        
                }
                // $usage = $this->stock->stock_usage_by_record($row->record);
                $row->patient_medicines = $patient_medicines;
                $data[] = $row;
            }
        } 
        return $data;
    }
    
    public function patient_history_ph_count($patient)
    {
        $this->db->select('count(*) as allcount');
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_id', $patient);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $query = $this->db->get();
        $result = $query->result_array();      
        return $result[0]['allcount'];
    }

    public function verify_symptoms_record_id($symptom, $record)
    {
        $this->db->where('sy_id', $symptom);
        $this->db->where('sy_record_id', $record);
        $this->db->from('patient_symptoms');
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function update_patient_symptoms($data, $id)
    {
        $this->db->set('sy_descriptions', $data);
        $this->db->where('sy_id', $id);
        $this->db->update('patient_symptoms');
    }

    public function update_patient_investigations($data, $id)
    {
        $this->db->set('sy_investigations', $data);
        $this->db->set('sy_lab', 1);
        $this->db->where('sy_id', $id);
        $this->db->update('patient_symptoms');
    } 

    public function checkIfEligibleToFillSymptoms($symptom)
    {
        $this->db->from('patient_symptoms s');
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('s.sy_id', $symptom);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('s.sy_complaints IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations IS NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->where('s.sy_complaints IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function checkSymptomIfEligibleToFillComplaint($symptom)
    {
        $this->db->from('patient_symptoms s');
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('s.sy_id', $symptom);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('s.sy_investigations IS NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->where('s.sy_investigations IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function checkSymptomIfEligibleToDeleteComplaint($symptom)
    {
        $this->db->from('patient_symptoms s');
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('s.sy_id', $symptom);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('s.sy_investigations IS NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->where('s.sy_investigations IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function checkSymptomIfEligibleToFillInvestigation($id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('s.sy_id', $id);
        $this->db->where('s.sy_complaints IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NULL', NULL, FALSE);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1r');
        $this->db->where('s.sy_lab', 1);
        $this->db->group_end();
        $this->db->group_end();
        $this->db->from('patient_symptoms s');
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function isELigiblePostUpdateInvestigation($record_id)
    {
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('s.sy_record_id', $record_id);
        $this->db->where('s.sy_lab', 1);
        $this->db->where('s.sy_investigations IS NOT NULL', NULL, FALSE);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->group_end();
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->from('patient_symptoms s');
        $query = $this->db->get();
        return ($query->num_rows() > 0);
    }

    public function checkSymptomIfEligibleToFillDisease($id)
    {
        $this->db->where('sy_id', $id);
        $this->db->where('sy_complaints IS NOT NULL', NULL);
        $this->db->where('sy_medicines IS NULL', NULL);
        $this->db->from('patient_symptoms');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $data = $query->row_array();
            if($data['sy_lab'] == 0)
            {
                return TRUE;
            }
            else
            {
                if(!empty($data['sy_investigations'])) return TRUE;
                else return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function checkSymptomIfEligibleToFillMedicine($id)
    {
        $this->db->where('sy_id', $id);
        $this->db->where('sy_complaints IS NOT NULL', NULL);
        $this->db->where('sy_diseases IS NOT NULL', NULL);
        $this->db->from('patient_symptoms');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $data = $query->row_array();
            if($data['sy_lab'] == 0)
            {
                return TRUE;
            }
            else
            {
                if(!empty($data['sy_investigations'])) return TRUE;
                else return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }

    public function update_patient_diseases($data, $symptom_id)
    {
        $this->db->where('sy_id', $symptom_id);
        $query = $this->db->update('patient_symptoms', $data);
        if($query) return TRUE;
        else return FALSE;
    }

    public function lab_serve_patient($record_id, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'naenda_lab');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'naenda_lab_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row_data = $query->row_array();
            $data = '';
            if($row_data['vs_visit'] == 'naenda_lab')
            {
                $current_attendance = $row_data['vs_attendants'];
                $new_attendance = $current_attendance == NULL ? $user_pf : $current_attendance.'_'.$user_pf;

                $current_time = time();
                $time_to_append = $row_data['vs_time'];
                $new_time = $time_to_append == NULL ? $current_time : $time_to_append.'_'.$current_time;

                $data = array(
                    'vs_visit' => 'nipo_lab',
                    'vs_attendants' => $new_attendance,
                    'vs_time' => $new_time,
                );
            }
            else 
            {
                $data = array(
                    'vs_visit' => 'nipo_lab',
                );
            }
            $this->db->where('vs_id', $row_data['vs_id']);
            $query2 = $this->db->update('patient_visit', $data);
            if($query2) return TRUE;
            else return FALSE;
        }
        else
        {
            return FALSE;
        } 
    }

    public function ph_serve_patient($record_id, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines is NOT NULL', NULL, FALSE);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nimetoka_daktari_r');
        $this->db->like('v.vs_attendants', $this->session->userdata('user_pf'), 'before');
        $this->db->group_end();
        $this->db->group_end();

        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row_data = $query->row_array();
            $data = '';
            if($row_data['vs_visit'] == 'nimetoka_daktari')
            {                
                $current_attendance = $row_data['vs_attendants'];
                $new_attendance = $current_attendance == NULL ? $user_pf : $current_attendance.'_'.$user_pf;

                $current_time = time();
                $time_to_append = $row_data['vs_time'];
                $new_time = $time_to_append == NULL ? $current_time : $time_to_append.'_'.$current_time;

                $data = array(
                    'vs_visit' => 'nipo_ph',
                    'vs_attendants' => $new_attendance,
                    'vs_time' => $new_time,
                );
            }
            else 
            {
                $data = array(
                    'vs_visit' => 'nipo_ph',
                );
            }

            $this->db->where('vs_id', $row_data['vs_id']);
            $query2 = $this->db->update('patient_visit', $data);
            if($query2) return TRUE;
            else return FALSE;
        }
        else
        {
            return FALSE;
        } 
    }

    public function isEligibleToFillResults($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function isEligibleForPh($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines is NOT NULL', NULL, FALSE);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function isEligibleToReturn($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function isPhEligibleToReturn($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines is NOT NULL', NULL, FALSE);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $this->db->not_like('s.sy_medicines', '+++', 'both');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function isEligibleToRelease($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_investigations is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $this->db->not_like('s.sy_investigations', '~null');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function isPhEligibleToRelease($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines is NOT NULL', NULL, FALSE);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        $result = FALSE;
        if($query->num_rows() > 0)
        {
            $result = TRUE;
            $data = $query->row();
            $medicines = explode("_", $data->sy_medicines);
            foreach ($medicines as $key => $row)
            {
                $segments = explode("~", $row);
                $med_id = $segments[0];
                $med_string = $segments[1];
                if($med_id != '10000001' && !empty($med_string))
                {
                    if (strpos($med_string, '+++') !== false)
                    {
                        $result = TRUE;
                    }
                    else 
                    {
                        $result = FALSE;
                        break;
                    }
                }
            }
        }
        return $result;
    }

    public function isDoctorEligibleToRelease($record, $user_pf)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
            // $this->db->where('s.sy_descriptions is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NOT NULL', NULL);
        $this->db->where('s.sy_medicines IS NOT NULL', NULL);
        $this->db->group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_1');
        $this->db->where('v.vs_attendants', $user_pf);
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2');
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $this->db->or_group_start();
        $this->db->where('v.vs_visit', 'nipo_daktari_2r');
        $this->db->like('v.vs_attendants', $user_pf, 'after');
        $this->db->group_end();
        $this->db->group_end();
        $this->db->group_end();
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $data = $query->row_array();
            if($data['sy_lab'] == 0)
            {
                return TRUE;
            }
            else
            {
                $inv = $data['sy_investigations'];
                if(!empty($inv) && strpos($inv, '@text:') !== false) return TRUE;
                else return FALSE;
            }
        }
        else
        {
            return FALSE;
        }
    }


    public function get_patient_lab_results($record_id, $user_pf)
    {
        $this->db->select('s.sy_investigations');
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->where('s.sy_lab', 1);
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row_array();
            $inv = explode("^^", $row['sy_investigations']);
            $this->load->model('investigation_model', 'investigation');
            $data = array();
            foreach ($inv as $key => $value)
            {
                $split = explode("~", $value);
                $inv_token = $split[0];
                $inv_results = $split[1];
                $inv_data = $this->investigation->get_investigation_subcategories_by_token($inv_token);
                    // $sub_cat_data = $this->investigation->get_investigation_subategories_by_category($cat_data['id']);         
                    // $cat_data['sub'] = $cat_data;
                if($inv_results == 'null')
                {
                    $inv_data['results'] = NULL;
                }
                else
                {
                    $text = explode("&&", $inv_results);

                    $inv_text = str_replace('@text:', '', $text[0]);
                    $inv_file = str_replace('@file:', '', $text[1]);
                    if($inv_file == 'null') $res['file'] = NULL;
                    else $res['file'] = $inv_file;
                    $res['text'] = $inv_text;

                    $inv_data['results'] = $res;
                }
                $data[] = $inv_data;
            }
            return $data;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_patient_ph_results($record_id, $user_pf)
    {
        $this->db->select('s.sy_medicines');
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nipo_ph');
        $this->db->like('v.vs_attendants', $user_pf, 'before');
        $query = $this->db->get();
        if($query->num_rows() > 0)
        {
            $row = $query->row_array();
            $medicine = explode("_", $row['sy_medicines']);
            $this->load->model('stock_model', 'stock');
            $this->load->model('medicineFormat_model', 'format');
            $this->load->model('medicineCategory_model', 'category');
            $this->load->model('Medicine_model', 'medicine');
            $data = array();
            foreach ($medicine as $key => $value)
            {
                $split = explode("~", $value);
                $medicine_token = $split[0];
                $medicine_text = $split[1];
                if($medicine_token == '10000001')
                {
                    $data[] = (object) array(
                        'id' => 10000001,
                        'token' => 10000001,
                        'in' => 0,
                        'text'=> str_replace('$', ' ', $medicine_text),
                        'medicine1'=> str_replace('$', ' ', $medicine_text),
                    );                        
                }
                else
                {
                    $medicine_data = $this->stock->getStockByToken($medicine_token);
                    $search = array(
                        'except' => $medicine_data->id,
                        'medicine_token' => $medicine_data->medicine_token, 
                        'unit_token' => $medicine_data->unit_token,
                        'unit_value' => $medicine_data->unit_value,
                    );
                    $similar_stock = $this->stock->getSimilarStock($search);
                    if(!empty($similar_stock))
                    {
                        $total = 0;
                        $used = 0;
                        foreach ($similar_stock as $key => $row)
                        {
                            $total += $row->total;
                            $used += $row->used;
                        }
                        $medicine_data->total += $total;
                        $medicine_data->used += $used;
                    }

                        // This is a medicine format
                        // $parent = $this->stock->getStockByID($medicine_data->parent);
                        // $parent_detailed = $this->format->get_format_by_token($parent->title);

                        // // This is a medicine category
                        // $grand = $this->stock->getStockByID($parent->parent);
                        // $grand_detailed = $this->category->get_category_by_token($grand->title);

                        // // This is medicine name
                        // $med_name = $this->medicine->getMedicineByToken($medicine_data->medicine);

                        // $medicine_data->parent = $parent_detailed->title;
                        // $medicine_data->grand = $grand_detailed->title;
                    $medicine_data->text = str_replace('$', ' ', $medicine_text);
                        // $medicine_data->medicine_name = $med_name['text'];
                    $medicine_data->in = 1;
                    $data[] = $medicine_data;
                }
            }

                // echo '<pre>';
                // print_r($data);
                // exit;
            return $data;
        }
        else
        {
            return FALSE;
        }
    }

    public function get_patient_lab_results_short($record_id)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nipo_lab');
        $this->db->where('s.sy_lab', 1);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function update_ivestigations($data, $id)
    {
        $this->db->where('sy_id', $id);
        $query = $this->db->update('patient_symptoms', $data);

    }

    public function get_latest_vitals($patient_id)
    {
        $this->db->select('rec_patient_id as id, rec_blood_pressure as bp, rec_pulse_rate as pr, rec_weight as weight, rec_height as height, rec_temeperature as temp, rec_respiration as resp');
        $this->db->where('rec_patient_id', $patient_id);
        $this->db->where('rec_blood_pressure is NOT NULL', NULL, FALSE);
        $this->db->where('rec_pulse_rate is NOT NULL', NULL, FALSE);
        $this->db->where('rec_weight is NOT NULL', NULL, FALSE);
        $this->db->where('rec_height is NOT NULL', NULL, FALSE);
        $this->db->where('rec_temeperature is NOT NULL', NULL, FALSE);
        $this->db->where('rec_respiration is NOT NULL', NULL, FALSE);
        $this->db->order_by('rec_regdate', 'DESC');
        $query = $this->db->get('patient_record')->row_array();
        return $query;
    }

    public function client_report($record_id)
    {
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_id', $record_id);
        $this->db->where('v.vs_visit', 'nimetoka_daktari');
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NOT NULL', NULL, FALSE);
            // $query = $this->db->get();
            // return $query->result_array();

        $query = $this->db->get();
        $data = array();

        if($query->num_rows() > 0)
        {
            foreach ($query->result_array() as $row) 

            {
                if(!empty($row['sy_investigations']))
                {
                    $inv_category = explode("^^", $row['sy_investigations']);
                    $this->load->model('investigation_model', 'investigation');
                    $inv_data = array();
                    foreach ($inv_category as $value)
                    {
                        $split = explode("~", $value);
                        $category_token = $split[0];
                        $category_results = $split[1];
                        $spli_res = explode("?##?", $category_results);
                        foreach ($spli_res as $key => $res) 
                        {
                            $res = explode("@", $res);
                            $sub_cat = $res[0];
                            $sub_cat_res = $res[1];
                            $res_data = $this->investigation->get_investigation_subcategories_by_token($sub_cat);
                            $res_data['result'] = str_replace('text:', ' ', $sub_cat_res);
                            $row['inv'][] = $res_data;
                        }
                    }
                }

                if(!empty($row['sy_diseases']))
                {
                    $diseases = explode("_", $row['sy_diseases']);
                    $this->load->model('disease_model', 'disease');
                    foreach ($diseases as $value)
                    {
                        $row['diseases'][] = $this->disease->getDiseaseById($value);
                    }
                }

                if(!empty($row['sy_medicines']))
                {                
                    $this->load->model('medicine_model', 'medicine');
                    $medicines = explode("_", $row['sy_medicines']);
                    foreach ($medicines as $value)
                    {
                        $arryStr = explode("~", $value);
                        $medicine_id = (int)$arryStr[0];
                        $doctor_desc = $arryStr[1];
                        $doctor_desc = str_replace('$', ' ', $doctor_desc);
                        $medicine_data = $this->medicine->getMedicineById($medicine_id);
                        $medicine_data['doctor_desc'] = $doctor_desc;
                        $row['medicines'][] = $medicine_data;
                    }

                }
                if(!empty($row['vs_attendants']))
                {
                    $doctor_pf = $row['vs_attendants'];
                    if($row['sy_lab'] == 1)
                    {
                        $arryStr = explode("_", $row['vs_attendants']);
                        $doctor_pf = $arryStr[2];
                    }
                    $this->load->model('employee_model', 'employee');
                    $employee_data = $this->employee->get_employee_by_file_number($doctor_pf);
                    $row['doctor'] = $employee_data['emp_lname'].',&nbsp;'.$employee_data['emp_fname'];

                }

                $data[] = $row;

            }
        }
        return $data;
    }


    public function client_report_post($pf, $start, $end)
    {
        $this->db->select('CONCAT(p.pat_fname, " ", p.pat_lname) as full_name, p.pat_file_no as pf, r.rec_id as instance, r.rec_attendant_file_no as receptionist, r.rec_care as care, r.rec_regdate as day, v.vs_attendants as attendants, s.sy_lab as islab, s.sy_complaints as complaints, s.sy_descriptions as examination, s.sy_investigations as diagnosis, s.sy_diseases as diseases, s.sy_medicines as medicines');           
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_file', $pf);
        $this->db->where('DATE(r.rec_regdate) >=', $start);
        $this->db->where('DATE(r.rec_regdate) <=', $end);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_diseases IS NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_medicines IS NOT NULL', NULL, FALSE);
        $ignore = array('nipo_daktari_1', 'nipo_daktari_2', 'nasubiri_daktari', 'nimerudishwa_kutoka_lab', 'nimerudishwa_kutoka_ph');
            // $this->db->where_not_in('v.vs_visit', $ignore);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $this->db->order_by('r.rec_regdate', 'DESC');

        $query = $this->db->get();
        $data = array();

        if($query->num_rows() > 0)
        {
            $this->load->model('complaints_model', 'complaintsModel');
            $this->load->model('investigation_model', 'investigationModel');
            $this->load->model('disease_model', 'diseaseModel');
            $this->load->model('stock_model', 'stockModel');
                // $this->load->model('medicine_model', 'medicineModel');
            $this->load->model('employee_model', 'employeeModel');

            foreach ($query->result_array() as $row)                
            {
                if(!empty($row['complaints']))
                {                
                    $complaints = explode("_", $row['complaints']);
                    foreach ($complaints as $value)
                    {
                        $arryStr = explode("~", $value);
                        $token_and_duration = explode(":", $arryStr[0]);

                        $comp_token = $token_and_duration[0];
                        $comp_duration =  str_replace('$$$', ' ', $token_and_duration[1]);
                        $comp_amplification = str_replace('$$$', ' ', $arryStr[1]);

                        $comp_data = $this->complaintsModel->getComplaintByToken($comp_token);
                        $comp_data->duration = $comp_duration;
                        $comp_data->amplification = $comp_amplification  == 'null' ?  NULL : $comp_amplification;
                        $row['patient_complaints'][] = $comp_data;
                    }                        
                }

                if(!empty($row['diagnosis']))
                {
                    $inv = explode("^^", $row['diagnosis']);

                    foreach ($inv as $value)
                    {						
                        $split = explode("~", $value);
                        $inv_token = $split[0];
                        $inv_results = $split[1];
                        $inv_data = $this->investigationModel->get_investigation_subcategories_by_token($inv_token);
                        if($inv_results == 'null')
                        {
                            $inv_data['results'] = NULL;
                            $inv_data['file'] = NULL;
                        }
                        else
                        {
                            $text = explode("&&", $inv_results);
                            $inv_text = str_replace('@text:', '', $text[0]);						
                            $inv_file = str_replace('@file:', '', $text[1]);
                            if($inv_file == 'null') $inv_file = NULL;					
                            $inv_data['results'] = $inv_text;
                            $inv_data['file'] = $inv_file;
                        }
                        $row['patient_dignostics'][] = $inv_data;
                    }
                }

                if(!empty($row['diseases']))
                {
                    $diseases = explode("_", $row['diseases']);
                    foreach ($diseases as $code)
                    {
                        $row['patient_diseases'][] = $this->diseaseModel->getDiseaseByToken(trim($code));
                    }
                }

                if(!empty($row['medicines']))
                {                
                    $medicines = explode("_", $row['medicines']);
                    foreach ($medicines as $stock)
                    {
                        $arryStr = explode("~", $stock);
                        $med_stock_code = $arryStr[0];
                        $doctor_desc = $arryStr[1];
                        $doctor_desc = str_replace('$', ' ', $doctor_desc);
                        $doctor_desc = str_replace('+++', '', $doctor_desc);
                        if($med_stock_code != 10000001)
                        {
                            $stock_data = $this->stockModel->getStockByToken($med_stock_code);
                            if(!empty($stock_data))
                            {
                                    // $medicine_data = $this->medicineModel->getMedicineByToken($stock_data->medicine);
                                $usage = $this->stockModel->getStockUsageByRecordAndStock($row['instance'], $stock_data->id);
                                $total_usage = 0;
                                foreach ($usage as $cons)
                                {
                                    $total_usage += $cons->used;
                                }
                                    // $medicine_data['unit'] = $stock_data->title;
                                $stock_data->doctor_desc = $doctor_desc;
                                $stock_data->consumption = $total_usage;
                                $row['patient_medicines'][] = $stock_data;
                            }
                            else
                            {
                                $medicine_data['text'] = NULL;
                                $medicine_data['doctor_desc'] = $doctor_desc;
                                $row['patient_medicines'][] = $medicine_data;
                            }                                    
                        }
                        else
                        {
                            $medicine_data['text'] = NULL;
                            $medicine_data['doctor_desc'] = $doctor_desc;
                            $row['patient_medicines'][] = $medicine_data;
                        }
                    }                        
                }

                if(!empty($row['attendants']))
                {
                    $arryStr = explode("_", $row['attendants']);
                    if((int)$row['islab'] == 1)
                    {
                        $doctor_pf = $arryStr[0];
                        $lab_pf = $arryStr[1];
                        $pharmacist_pf = array_key_exists(3, $arryStr) ? $arryStr[3]: NULL;

                        $doctor_data = $this->employeeModel->get_employee_by_file_number($doctor_pf);
                        $lab_data = $this->employeeModel->get_employee_by_file_number($lab_pf);

                        $row['doctor'] = $doctor_data['emp_fname'].'&nbsp;'.$doctor_data['emp_lname'];
                        $row['lab'] = $lab_data['emp_fname'].'&nbsp;'.$lab_data['emp_lname'];

                        if(!empty($pharmacist_pf))
                        {
                            $pharmacy_data = $this->employeeModel->get_employee_by_file_number($pharmacist_pf);
                            $row['pharmacy'] = $pharmacy_data['emp_fname'].'&nbsp;'.$pharmacy_data['emp_lname'];
                        }                            
                    }
                    else
                    {
                        $doctor_pf = $arryStr[0];
                        $pharmacist_pf = array_key_exists(1, $arryStr) ? $arryStr[1] : NULL;

                        $doctor_data = $this->employeeModel->get_employee_by_file_number($doctor_pf);
                        $row['doctor'] = $doctor_data['emp_fname'].'&nbsp;'.$doctor_data['emp_lname'];

                        if(!empty($pharmacist_pf))
                        {
                            $pharmacy_data = $this->employeeModel->get_employee_by_file_number($pharmacist_pf);
                            $row['pharmacy'] = $pharmacy_data['emp_fname'].'&nbsp;'.$pharmacy_data['emp_lname'];
                        } 
                    }

                    $receptionist_data = $this->employeeModel->get_employee_by_file_number($row['receptionist']);
                    $row['reception'] = $receptionist_data['emp_fname'].'&nbsp;'.$receptionist_data['emp_lname'];
                }                    
                $data[] = $row;                    
            }
        }
        return $data;
    }
    
    public function client_report_post2($rowno, $rowperpage, $patient)
    {
        $this->db->select('CONCAT(p.pat_fname, " ", p.pat_lname) as full_name, p.pat_file_no as pf, r.rec_id as instance, r.rec_attendant_file_no as receptionist, r.rec_care as care, DATE_FORMAT(r.rec_regdate, "%b %d %Y %H:%i") as day, v.vs_attendants as attendants, s.sy_lab as islab, s.sy_complaints as complaints, s.sy_descriptions as examination, s.sy_investigations as diagnosis, s.sy_diseases as diseases, s.sy_medicines as medicines');           
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_id', $patient);
        // $ignore = array('nipo_daktari_1', 'nipo_daktari_2', 'nasubiri_daktari', 'nimerudishwa_kutoka_lab', 'nimerudishwa_kutoka_ph');
            // $this->db->where_not_in('v.vs_visit', $ignore);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $this->db->order_by('r.rec_regdate', 'DESC');
        $this->db->limit($rowperpage, $rowno);
        $query = $this->db->get();
        $data = array();

        if($query->num_rows() > 0)
        {
            $this->load->model('complaints_model', 'complaintsModel');
            $this->load->model('investigation_model', 'investigationModel');
            $this->load->model('disease_model', 'diseaseModel');
            $this->load->model('stock_model', 'stockModel');
                // $this->load->model('medicine_model', 'medicineModel');
            $this->load->model('employee_model', 'employeeModel');

            foreach ($query->result_array() as $row)                
            {
                if(!empty($row['complaints']))
                {                
                    $complaints = explode("_", $row['complaints']);
                    foreach ($complaints as $value)
                    {
                        $arryStr = explode("~", $value);
                        $token_and_duration = explode(":", $arryStr[0]);

                        $comp_token = $token_and_duration[0];
                        $comp_duration =  str_replace('$$$', ' ', $token_and_duration[1]);
                        $comp_amplification = str_replace('$$$', ' ', $arryStr[1]);

                        $comp_data = $this->complaintsModel->getComplaintByToken($comp_token);
                        $comp_data->duration = $comp_duration;
                        $comp_data->amplification = $comp_amplification  == 'null' ?  NULL : $comp_amplification;
                        $row['patient_complaints'][] = $comp_data;
                    }                        
                }

                if(!empty($row['diagnosis']))
                {
                    $inv = explode("^^", $row['diagnosis']);

                    foreach ($inv as $value)
                    {						
                        $split = explode("~", $value);
                        $inv_token = $split[0];
                        $inv_results = $split[1];
                        $inv_data = $this->investigationModel->get_investigation_subcategories_by_token($inv_token);
                        if($inv_results == 'null')
                        {
                            $inv_data['results'] = NULL;
                            $inv_data['file'] = NULL;
                        }
                        else
                        {
                            $text = explode("&&", $inv_results);
                            $inv_text = str_replace('@text:', '', $text[0]);						
                            $inv_file = str_replace('@file:', '', $text[1]);
                            if($inv_file == 'null') $inv_file = NULL;					
                            $inv_data['results'] = $inv_text;
                            $inv_data['file'] = $inv_file;
                        }
                        $row['patient_dignostics'][] = $inv_data;
                    }
                }

                if(!empty($row['diseases']))
                {
                    $diseases = explode("_", $row['diseases']);
                    foreach ($diseases as $code)
                    {
                        $row['patient_diseases'][] = $this->diseaseModel->getDiseaseByToken(trim($code));
                    }
                }

                if(!empty($row['medicines']))
                {                
                    $medicines = explode("_", $row['medicines']);
                    foreach ($medicines as $stock)
                    {
                        $arryStr = explode("~", $stock);
                        $med_stock_code = $arryStr[0];
                        $doctor_desc = $arryStr[1];
                        $doctor_desc = str_replace('$', ' ', $doctor_desc);
                        $doctor_desc = str_replace('+++', '', $doctor_desc);
                        if($med_stock_code != 10000001)
                        {
                            $stock_data = $this->stockModel->getStockByToken($med_stock_code);
                            if(!empty($stock_data))
                            {
                                // $medicine_data = $this->medicineModel->getMedicineByToken($stock_data->medicine);
                                $usage = $this->stockModel->getStockUsageByRecordAndStock($row['instance'], $stock_data->id);
                                $total_usage = 0;
                                foreach ($usage as $cons)
                                {
                                    $total_usage += $cons->used;
                                }
                                    // $medicine_data['unit'] = $stock_data->title;
                                $stock_data->doctor_desc = $doctor_desc;
                                $stock_data->consumption = $total_usage;
                                $row['patient_medicines'][] = $stock_data;
                            }
                            else
                            {
                                $medicine_data['text'] = NULL;
                                $medicine_data['doctor_desc'] = $doctor_desc;
                                $row['patient_medicines'][] = $medicine_data;
                            }                                    
                        }
                        else
                        {
                            $medicine_data['text'] = NULL;
                            $medicine_data['doctor_desc'] = $doctor_desc;
                            $row['patient_medicines'][] = $medicine_data;
                        }
                    }                        
                }

                if(!empty($row['attendants']))
                {
                    $arryStr = explode("_", $row['attendants']);
                    if((int)$row['islab'] == 1)
                    {
                        $doctor_pf = $arryStr[0];
                        $lab_pf = $arryStr[1];
                        $pharmacist_pf = array_key_exists(3, $arryStr) ? $arryStr[3]: NULL;

                        $doctor_data = $this->employeeModel->get_employee_by_file_number($doctor_pf);
                        $lab_data = $this->employeeModel->get_employee_by_file_number($lab_pf);

                        $row['doctor'] = $doctor_data['emp_fname'].'&nbsp;'.$doctor_data['emp_lname'];
                        $row['lab'] = $lab_data['emp_fname'].'&nbsp;'.$lab_data['emp_lname'];

                        if(!empty($pharmacist_pf))
                        {
                            $pharmacy_data = $this->employeeModel->get_employee_by_file_number($pharmacist_pf);
                            $row['pharmacy'] = $pharmacy_data['emp_fname'].'&nbsp;'.$pharmacy_data['emp_lname'];
                        }                            
                    }
                    else
                    {
                        $doctor_pf = $arryStr[0];
                        $pharmacist_pf = array_key_exists(1, $arryStr) ? $arryStr[1] : NULL;

                        $doctor_data = $this->employeeModel->get_employee_by_file_number($doctor_pf);
                        $row['doctor'] = $doctor_data['emp_fname'].'&nbsp;'.$doctor_data['emp_lname'];

                        if(!empty($pharmacist_pf))
                        {
                            $pharmacy_data = $this->employeeModel->get_employee_by_file_number($pharmacist_pf);
                            $row['pharmacy'] = $pharmacy_data['emp_fname'].'&nbsp;'.$pharmacy_data['emp_lname'];
                        } 
                    }

                    $receptionist_data = $this->employeeModel->get_employee_by_file_number($row['receptionist']);
                    $row['reception'] = $receptionist_data['emp_fname'].'&nbsp;'.$receptionist_data['emp_lname'];
                }                    
                $data[] = $row;                    
            }
        }
        return $data;
    }

    public function client_report_post2_count($patient)
    {
        $this->db->select('count(*) as allcount');
        $this->db->from('patient_record r');
        // $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_id', $patient);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $query = $this->db->get();
        $result = $query->result_array();      
        return $result[0]['allcount'];
    }

    public function client_history_lab($pf, $start, $end)
    {
        $this->db->select('CONCAT(p.pat_fname, " ",p.pat_lname) as full_name, p.pat_file_no as pf, r.rec_regdate as day, s.sy_investigations as diagnosis');            
        $this->db->from('patient_record r');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
        $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
        $this->db->where('r.rec_patient_file', $pf);
        $this->db->where('DATE(r.rec_regdate) >=', $start);
        $this->db->where('DATE(r.rec_regdate) <=', $end);
        $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
        $this->db->where('s.sy_lab', 1);
        $ignore = array('nipo_lab', 'naenda_lab', 'naenda_lab_r');
        $this->db->where_not_in('v.vs_visit', $ignore);
        $this->db->order_by('r.rec_regdate', 'DESC');

        $query = $this->db->get();
        $data = array();

        if($query->num_rows() > 0)
        {
            $this->load->model('investigation_model', 'investigation');
            foreach ($query->result_array() as $row)
            {
                $inv = explode("^^", $row['diagnosis']);
                $dignosis = [];
                foreach ($inv as $value)
                {						
                  $split = explode("~", $value);
                  $inv_token = $split[0];
                  $inv_results = $split[1];
                  $inv_data = $this->investigation->get_investigation_subcategories_by_token($inv_token);
                  if($inv_results == 'null')
                  {
                   $inv_data['results'] = NULL;
                   $inv_data['file'] = NULL;
               }
               else
               {
                   $text = explode("&&", $inv_results);
                   $inv_text = str_replace('@text:', '', $text[0]);

                   $inv_file = str_replace('@file:', '', $text[1]);
                   if($inv_file == 'null') $inv_file = NULL;

                   $inv_data['results'] = $inv_text;
                   $inv_data['file'] = $inv_file;
               }
               $dignosis[] = $inv_data;
           }
           $row['diagnosis'] = $dignosis;
           $data[] = $row;			
       }
   }
   return $data;
}

public function client_history_lab2($rowno, $rowperpage, $patient)
{
    $this->db->select('r.rec_id as record, p.pat_file_no as pf, DATE_FORMAT(r.rec_regdate, "%b %d %Y %H:%i") as day, s.sy_investigations as diagnosis');            
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('r.rec_patient_id', $patient);
    $this->db->where('s.sy_lab', 1);
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    $this->db->order_by('r.rec_regdate', 'DESC');
    $this->db->limit($rowperpage, $rowno);
    $query = $this->db->get();

    $data = array();
    if($query->num_rows() > 0)
    {
        $this->load->model('investigation_model', 'investigation');
        foreach ($query->result_array() as $row)
        {
            $inv = explode("^^", $row['diagnosis']);
            $dignosis = [];
            foreach ($inv as $value)
            { 
                $split = explode("~", $value);
                $inv_token = $split[0];
                $inv_results = $split[1];
                $inv_data = $this->investigation->get_investigation_subcategories_by_token($inv_token);
                if($inv_results == 'null'){
                    $inv_data['results'] = NULL;
                    $inv_data['file'] = NULL;
                }
                else
                {
                   $text = explode("&&", $inv_results);
                   $inv_text = str_replace('@text:', '', $text[0]);

                   $inv_file = str_replace('@file:', '', $text[1]);
                   if($inv_file == 'null') $inv_file = NULL;

                   $inv_data['results'] = $inv_text;
                   $inv_data['file'] = $inv_file;
               }
               $dignosis[] = $inv_data;
           }
           $row['diagnosis'] = $dignosis;
           $data[] = $row;          
       }
   }
   return $data;
}

public function client_history_lab2_count($patient)
{
    $this->db->select('count(*) as allcount');
    $this->db->from('patient_record r');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->where('r.rec_patient_id', $patient);
    $this->db->where('s.sy_lab', 1);
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    $query = $this->db->get();
    $result = $query->result_array();      
    return $result[0]['allcount'];
}

public function doctor_performance($pf, $start, $end)
{
    $this->db->select('r.rec_regdate, p.pat_fname, p.pat_lname, p.pat_file_no, p.pat_gender, p.pat_occupation, s.sy_lab');
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('DATE(r.rec_regdate) >=', $start);
    $this->db->where('DATE(r.rec_regdate) <=', $end);
    $this->db->where('v.vs_visit', 'nimetoka_daktari');
    $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
    $this->db->where('s.sy_diseases IS NOT NULL', NULL);
    $this->db->where('s.sy_medicines IS NOT NULL', NULL);
    $this->db->like('v.vs_attendants', $pf);
    $query = $this->db->get();
    if($query->num_rows() > 0) return $query->result_array();
    return array();
}

public function receptionist_performance($pf, $start, $end)
{
            // $this->db->select('r.rec_regdate, p.pat_fname, p.pat_lname, p.pat_file_no, p.pat_gender, p.pat_occupation, s.sy_lab');
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->where('DATE(r.rec_regdate) >=', $start);
    $this->db->where('DATE(r.rec_regdate) <=', $end);
    $this->db->where('r.rec_attendant_file_no', $pf);
    $this->db->where('r.rec_blood_pressure is NOT NULL', NULL, FALSE);
    $this->db->where('r.rec_pulse_rate is NOT NULL', NULL, FALSE);
    $this->db->where('r.rec_weight is NOT NULL', NULL, FALSE);
    $this->db->where('r.rec_height is NOT NULL', NULL, FALSE);
    $this->db->where('r.rec_temeperature is NOT NULL', NULL, FALSE);
    $query = $this->db->get();
    if($query->num_rows() > 0) return $query->result_array();
    return array();

}

public function lab_performance($pf, $start, $end)
{

    $this->db->select('r.rec_regdate as day, p.pat_fname as fname, p.pat_lname as lname, p.pat_file_no as pf, p.pat_address as address, p.pat_gender as gender, p.pat_occupation as occupation, p.pat_dob as dob, s.sy_investigations as diagnosis');
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('DATE(r.rec_regdate) >=', $start);
    $this->db->where('DATE(r.rec_regdate) <=', $end);
    $this->db->where('v.vs_visit !=', 'nipo_lab');
    $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
    $this->db->where('s.sy_lab', 1);
    $this->db->like('v.vs_attendants', $pf);
    $query = $this->db->get();
    if($query->num_rows() > 0) return $query->result_array();
    else return array();
}

public function pharmacy_performance($pf, $start, $end)
{            
    $this->db->select('r.rec_regdate, p.pat_fname, p.pat_lname, p.pat_file_no, p.pat_gender, p.pat_occupation, s.sy_lab, v.vs_attendants, v.vs_time');
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
            // $this->db->where('DATE(r.rec_regdate) >=', $start);
            // $this->db->where('DATE(r.rec_regdate) <=', $end);
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
    $this->db->where('s.sy_complaints is NOT NULL', NULL, FALSE);
    $this->db->like('v.vs_attendants', $pf);
    $query = $this->db->get();
    $data = array();
    if($query->num_rows() > 0)
    {
        foreach ($query->result_array() as $key => $row)
        {
            $attendants = explode("_", $row['vs_attendants']);
            $pharmacist_pf = $attendants[count($attendants)-1];
            if($pharmacist_pf == $pf)
            {
                $dates = explode("_", $row['vs_time']);
                $pharmacist_unix_date = $dates[count($dates)-1];
                $pharmacist_date = gmdate("Y-m-d", $pharmacist_unix_date);
                if($pharmacist_date >= $start && $pharmacist_date <= $end)
                {
                    $row['rec_regdate'] = gmdate("Y-m-d h:i a", $pharmacist_unix_date);
                    $data[] = $row;
                }
            }
        }
    }
    return $data;
}

public function deleteRecord($id)
{
    $this->db->where('rec_id', $id);
    $this->db->delete('patient_record');
}

public function deleteById($table, $column_pk, $pk_data)
{
    $this->db->where($column_pk, $pk_data);
    $this->db->delete($table);
}

public function get_monitor_data($postData)
{
    $this->_get_monitor_data_datatables_query($postData);
    if(isset($postData['length']) && $postData['length'] != -1){
        $this->db->limit($postData['length'], $postData['start']);
    }
    $query = $this->db->get();
    return $query->result();
}

public function get_served_patients($postData)
{
    $this->_get_served_patients_data_datatables_query($postData);
    if(isset($postData['length']) && $postData['length'] != -1){
        $this->db->limit($postData['length'], $postData['start']);
    }
    $query = $this->db->get();
    return $query->result();
}

public function get_incomplete_patients($postData)
{
    $this->_get_incomplete_patients_data_datatables_query($postData);
    if(isset($postData['length']) && $postData['length'] != -1){
        $this->db->limit($postData['length'], $postData['start']);
    }
    $query = $this->db->get();
    return $query->result();
}

private function _get_monitor_data_datatables_query($postData)
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');$this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
    $this->db->group_start();
    $this->db->where('r.rec_blood_pressure is NULL', NULL, FALSE);
    $this->db->where('r.rec_pulse_rate is NULL', NULL, FALSE);
    $this->db->where('r.rec_weight is NULL', NULL, FALSE);
    $this->db->where('r.rec_height is NULL', NULL, FALSE);
    $this->db->where('s.sy_complaints is NULL', NULL, FALSE);
    $this->db->or_group_start();
    $this->db->where_not_in('v.vs_visit', ['nimetoka_ph']);
    $this->db->group_end();
    $this->db->group_end();
    $this->db->order_by('r.rec_regdate');

    $i = 0;
            // loop searchable columns 
    foreach($this->column_search_doctor as $item){
                // if datatable send POST for search
        if(isset($postData['search']) && $postData['search']['value']){
                    // first loop
            if($i===0){
                        // open bracket
                $this->db->group_start();
                $this->db->like($item, $postData['search']['value']);
            }else{
                $this->db->or_like($item, $postData['search']['value']);
            }

                    // last loop
            if(count($this->column_search_doctor) - 1 == $i){
                        // close bracket
                $this->db->group_end();
            }
        }
        $i++;
    }
}

private function _get_served_patients_data_datatables_query($postData)
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    $this->db->order_by('r.rec_regdate', 'DESC');

    $i = 0;
            // loop searchable columns 
    foreach($this->column_search_doctor as $item){
                // if datatable send POST for search
        if(isset($postData['search']) && $postData['search']['value']){
                    // first loop
            if($i===0){
                        // open bracket
                $this->db->group_start();
                $this->db->like($item, $postData['search']['value']);
            }else{
                $this->db->or_like($item, $postData['search']['value']);
            }

                    // last loop
            if(count($this->column_search_doctor) - 1 == $i){
                        // close bracket
                $this->db->group_end();
            }
        }
        $i++;
    }
}

public function eligibleToResetRecord($record)
{
    $this->db->from('patient_record r');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY < NOW()');
    $this->db->where('v.vs_visit !=', 'nimetoka_ph');
    $this->db->where('r.rec_id', $record);
    $query = $this->db->get();
    return ($query->num_rows() > 0);
}

private function _get_incomplete_patients_data_datatables_query($postData)
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');$this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY < NOW()');
    $this->db->where('v.vs_visit !=', 'nimetoka_ph');
    $this->db->order_by('r.rec_regdate', 'ASC');

    $i = 0;
            // loop searchable columns 
    foreach($this->column_search_doctor as $item){
                // if datatable send POST for search
        if(isset($postData['search']) && $postData['search']['value']){
                    // first loop
            if($i===0){
                        // open bracket
                $this->db->group_start();
                $this->db->like($item, $postData['search']['value']);
            }else{
                $this->db->or_like($item, $postData['search']['value']);
            }

                    // last loop
            if(count($this->column_search_doctor) - 1 == $i){
                        // close bracket
                $this->db->group_end();
            }
        }
        $i++;
    }
}

public function countAllForMonitor()
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');$this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY > NOW()');
    $this->db->group_start();
    $this->db->where('r.rec_blood_pressure is NULL', NULL, FALSE);
    $this->db->where('r.rec_pulse_rate is NULL', NULL, FALSE);
    $this->db->where('r.rec_weight is NULL', NULL, FALSE);
    $this->db->where('r.rec_height is NULL', NULL, FALSE);
    $this->db->where('s.sy_complaints is NULL', NULL, FALSE);
    $this->db->or_group_start();
    $this->db->where_not_in('v.vs_visit', ['nimetoka_ph']);
    $this->db->group_end();
    $this->db->group_end();
    return $this->db->count_all_results();
}

public function countAllServedPatients()
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    return $this->db->count_all_results();
}

public function countAllIncompletePatients()
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('DATE(r.rec_regdate) + INTERVAL 2 DAY < NOW()');
    $this->db->where('v.vs_visit !=', 'nimetoka_ph');
    return $this->db->count_all_results();
}

public function countAllServedDashboard($distribution, $date)
{
    $this->db->from('patient_record r');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    $this->db->join('patient_symptoms s', 's.sy_record_id = r.rec_id', 'left');
    $this->db->where('v.vs_visit', 'nimetoka_ph');

    if($distribution == 1) $this->db->where('DATE(r.rec_regdate)', $date);
    if($distribution == 2) $this->db->where('MONTH(r.rec_regdate)', $date);
    if($distribution == 3) $this->db->where('YEAR(r.rec_regdate)', $date);

    return $this->db->count_all_results();
}

public function countFilteredForMonitor($postData)
{
    $this->_get_monitor_data_datatables_query($postData);
    $query = $this->db->get();
    return $query->num_rows();
}

public function countFilteredAllServedPatients($postData)
{
    $this->_get_served_patients_data_datatables_query($postData);
    $query = $this->db->get();
    return $query->num_rows();
}

public function countFilteredAllIncompletePatients($postData)
{
    $this->_get_incomplete_patients_data_datatables_query($postData);
    $query = $this->db->get();
    return $query->num_rows();
}

public function age_gender_report($start, $end, $group)
{
    $this->db->select('COUNT(CASE WHEN p.pat_gender = "MALE" THEN r.rec_id END) AS males, COUNT(CASE WHEN p.pat_gender = "FEMALE" THEN r.rec_id END) AS females, COUNT(*) AS total');
    $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = r.rec_id', 'left');
    if($group[0] != 0)
    $this->db->where('ABS(TIMESTAMPDIFF(YEAR, CURRENT_DATE(), p.pat_dob)) >=', $group[0]);
    if($group[1] != 0)
    $this->db->where('ABS(TIMESTAMPDIFF(YEAR, CURRENT_DATE(), p.pat_dob)) <=', $group[1]);
    $this->db->where('DATE(r.rec_regdate) >=', $start);
    $this->db->where('DATE(r.rec_regdate) <=', $end);
    $this->db->where('v.vs_visit', 'nimetoka_ph');

    $query = $this->db->get('patient_record r');
    $data = $query->result();
     
    return $data;
}

public function disease_distribution_report($start, $end)
{
    $this->db->select('dis_title as name, dis_token as code');
    $query = $this->db->get('diseases')->result_array();
    $data = [];
    foreach ($query as $key => $row)
    {
        $this->db->select('COUNT(CASE WHEN p.pat_occupation = "STUDENT" THEN s.sy_id END) AS students, COUNT(CASE WHEN p.pat_occupation = "EMPLOYEE" THEN s.sy_id END) AS employees, COUNT(CASE WHEN p.pat_occupation = "OTHER" THEN s.sy_id END) AS others, COUNT(*) AS total');
        $this->db->join('patient p', 'p.pat_file_no = s.sy_record_patient_pf', 'left');
        $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
        $this->db->where('DATE(s.sy_time) >=', $start);
        $this->db->where('DATE(s.sy_time) <=', $end);
        $this->db->where('v.vs_visit', 'nimetoka_ph');
        $this->db->like('s.sy_diseases', $row['code']);
        $sub_query = $this->db->get('patient_symptoms s')->result_array();
        if($sub_query[0]['students'] > 0 || $sub_query[0]['employees'] > 0 || $sub_query[0]['others'] > 0)
        {
            $row['patients'] = $sub_query;
            $data[] = $row;
        }
    }     
    return $data;
}

public function lab_and_non_lab_report($start, $end, $lab=TRUE)
{
    $this->db->select('COUNT(CASE WHEN p.pat_occupation = "STUDENT" THEN s.sy_id END) AS students, COUNT(CASE WHEN p.pat_occupation = "EMPLOYEE" THEN s.sy_id END) AS employees, COUNT(CASE WHEN p.pat_occupation = "OTHER" THEN s.sy_id END) AS others, COUNT(*) AS total');
    $this->db->join('patient p', 'p.pat_file_no = s.sy_record_patient_pf', 'left');
    $this->db->join('patient_visit v', 'v.vs_record_id = s.sy_record_id', 'left');
    $this->db->where('DATE(s.sy_time) >=', $start);
    $this->db->where('DATE(s.sy_time) <=', $end);
    $this->db->where('v.vs_visit', 'nimetoka_ph');
    if($lab)
    {
        $this->db->where('s.sy_lab', 1);
    }
    $data = $this->db->get('patient_symptoms s')->result_array();             
    return $data;
}

}
?>