<?php

class Employee_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        
        // Set searchable column fields
        $this->column_search = array('e.emp_pf', 'c.cat_name', 'e.emp_fname', 'e.emp_mname', 'e.emp_lname');
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
}