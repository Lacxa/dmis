<?php

class Employee_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Set searchable column fields
        $this->column_search = array('e.emp_pf', 'c.cat_name', 'e.emp_fname', 'e.emp_mname', 'e.emp_lname');
        $this->column_search_login_history = array('log_ip', 'log_platform', 'log_browser', 'log_time');
    }
    
    public function validate_sign_in($email, $password)
    {
        $this->db->join('employee_category c', 'c.cat_id  = e.emp_category', 'left');
        $this->db->join('user_roles r', 'r.role_id  = e.emp_role', 'left');
        $this->db->where("e.emp_mail", $email);
        
        $result = $this->getUsers($password);
        
        if (!empty($result))
        {
            return $result;
        }
        else 
        {
            return FALSE;
        }
    }
    
    public function getUsers($password)
    {
        $query = $this->db->get('employee e');
        
        if ($query->num_rows() > 0) 
        {
            $result = $query->row_array();
            
            if (password_verify($password, $result['emp_password'])) 
            {
                //We're good
                return $result;
            } 
            else 
            {
                //Wrong password
                return array();
            }
            
        } 
        else 
        {
            return array();
        }
    }

    public function get_db_backup()
    {
        $this->db->select('d.db_file as file_name, d.db_size as size, DATE_FORMAT(d.db_created_at, "%b %d %Y %H:%i") as day, CONCAT(e.emp_lname, ", ", e.emp_fname) as author');
        $this->db->join('employee e', 'e.emp_pf  = d.db_author', 'left');
        return $this->db->get('db_backup d')->row();
    }

    public function update_db_backup($data)
    {
        $this->db->replace('db_backup', $data);
    }
    
    public function is_employee_active($employee_id)
    {
        $this->db->where('emp_id', $employee_id);
        $this->db->where('emp_isActive', 1);
        $query = $this->db->get('employee');
        if ($query->num_rows() > 0) return TRUE;
        else return FALSE;
    }
    
    public function get_employee_by_file_number($file_no)
    {
        $this->db->join('employee_category c', 'c.cat_id  = e.emp_category', 'left');
        $this->db->join('user_roles r', 'r.role_id  = e.emp_role', 'left');
        $this->db->where('e.emp_pf', $file_no);
        $query = $this->db->get('employee e');
        return $query->row_array();
    }

    public function get_employee_by_id($id)
    {
        $this->db->join('employee_category c', 'c.cat_id  = e.emp_category', 'left');
        $this->db->join('user_roles r', 'r.role_id  = e.emp_role', 'left');
        $this->db->where('e.emp_id', $id);
        $query = $this->db->get('employee e');
        return $query->row_array();
    }
    
    ##########################################################
    // datatable issues start
    public function get_list_of_users($postData)
    {   
        $this->_get_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function countAll()
    {
        $this->db->from('employee e');
        $this->db->join('employee_category c', 'c.cat_id = e.emp_category', 'left');
        $this->db->join('user_roles r', 'r.role_id = e.emp_role', 'left');
        $this->db->where_not_in('r.role_alias', ['SUPER']);
        $this->db->where_not_in('e.emp_pf', [''.$this->session->userdata('user_pf').'']);
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    private function _get_datatables_query($postData){
        
        $this->db->from('employee e');
        $this->db->join('employee_category c', 'c.cat_id = e.emp_category', 'left');
        $this->db->join('user_roles r', 'r.role_id = e.emp_role', 'left');
        $this->db->where_not_in('r.role_alias', ['SUPER']);
        $this->db->where_not_in('e.emp_pf', [''.$this->session->userdata('user_pf').'']);
        $this->db->order_by('e.emp_fname ASC, e.emp_regdate DESC');
        
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

    #------------------------------------------------------------
    public function get_login_history_by_pf($postData)
    {   
        $this->_get_datatables_query_login_history($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function countAllLoginHistory()
    {
        $this->db->from('login_history');
        $this->db->where('log_emp_pf', $this->session->userdata('user_pf'));
        return $this->db->count_all_results();
    }
    
    public function countFilteredLoginHistory($postData)
    {
        $this->_get_datatables_query_login_history($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    private function _get_datatables_query_login_history($postData){
        
        $this->db->from('login_history');
        $this->db->where('log_emp_pf', $this->session->userdata('user_pf'));
        $this->db->order_by('log_time', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_login_history as $item){
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
                if(count($this->column_search_login_history) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    // datatable issues end
    ##########################################################
    
    public function checkPFExist($pf)
    {
        $this->db->where('emp_pf', $pf);
        $this->db->from('employee');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkEmailExist($email)
    {
        $this->db->where('emp_mail', $email);
        $this->db->from('employee');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkPhoneExist($phone)
    {
        $this->db->where('emp_phone', $phone);
        $this->db->from('employee');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_employee($data)
    {
        $this->db->insert('employee', $data);
    }
    
    public function setUserState($state, $id)
    {
        $this->db->set('emp_isActive', $state);
        $this->db->where('emp_id', $id);
        $this->db->update('employee');
    }
    
    public function setIncharge($value, $id)
    {
        $this->db->set('emp_isIncharge', $value);
        $this->db->where('emp_id', $id);
        $this->db->update('employee');
    }
    
    public function updateUserData($where, $data)
    {
        $this->db->where_in('emp_id', $where);
        $this->db->update('employee', $data);
    }
    
    public function delete($id)
    {
        $this->db->where('emp_id', $id);
        $this->db->delete('employee');
    }
    
    public function count_login_history_by_file_number($file_no)
    {
        $this->db->from('login_history');
        $this->db->where('log_emp_pf', $file_no);
        return $this->db->count_all_results();
    }
    
    public function save_login_history($data)
    {
        $this->db->insert('login_history', $data);
    }
    
    public function check_if_uuid_exist_login_history($uuid)
    {
        $this->db->where('log_id', $uuid);
        $query = $this->db->get('login_history');
        return $query->num_rows() > 0;
    }

    public function get_old_login_history_by_file_number($file_no)
    {
        $this->db->where('log_emp_pf', $file_no);
        $this->db->order_by('log_time');
        $query = $this->db->get('login_history');
        return $query->row()->log_id;
    }
        
    public function delete_login_history($id)
    {
        $this->db->where('log_id', $id);
        $this->db->delete('login_history');
    }
}