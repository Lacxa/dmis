<?php

class Disease_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('d.dis_title', 'd.dis_alias', 'd.dis_token', 'c.discat_name', 'g.dg_range', 'g.dg_keywords', 'g.dg_description');
        $this->category_column_search = array('discat_name', 'discat_token', 'discat_description');
        $this->groups_column_search = array('dg_range', 'dg_token', 'dg_keywords', 'dg_description');        
    }
    
    public function getDisease($str)
    {
        $this->db->select('d.dis_id as id, d.dis_title as text, d.dis_alias as short, d.dis_token as code');
        $this->db->join('disease_groups g', 'g.dg_token = d.dis_group', 'left');
        $this->db->like('d.dis_title', $str);
        $this->db->or_like('d.dis_alias', $str);
        $this->db->or_like('d.dis_token', $str);
        $this->db->or_like('g.dg_keywords', $str);
        $this->db->or_like('g.dg_range', $str);
        $this->db->or_like('g.dg_description', $str);
        $query = $this->db->get('diseases d');
        return $query->result();
    }
    
    public function getDiseaseById($id)
    {
        $this->db->select('dis_id as id, dis_title as text, dis_alias as short, dis_token as code');
        $this->db->where('dis_id', $id);
        $query = $this->db->get('diseases');
        return $query->row_array();
    }
    
    public function getDiseaseByToken($token)
    {
        $this->db->select('dis_id as id, dis_title as text, dis_alias as short, dis_token as code');
        $this->db->where('dis_token', $token);
        $query = $this->db->get('diseases');
        return $query->row_array();
    }
    
    public function get($where = 0)
    {
        if($where != 0) $this->db->where($where);
        $query = $this->db->get('diseases');
        return $query->row();
    }
    
    public function delete($id)
    {
        $this->db->where('dis_id', $id);
        $this->db->delete('diseases');
    }
    
    public function get_diseases($postData)
    {   
        $this->_get_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_disease_groups($postData)
    {
        $this->_get_disease_groups_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_disease_categories($postData)
    {
        $this->_get_disease_categories_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    public function countAll()
    {
        $this->db->from("diseases");
        return $this->db->count_all_results();
    }
    
    public function countAllDiseaseGroups()
    {
        $this->db->from("disease_groups");
        return $this->db->count_all_results();
    }
    
    public function countAllDiseaseCategories()
    {
        $this->db->from("disease_categories");
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function countFilteredDiseaseGroups($postData)
    {
        $this->_get_disease_groups_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function countFilteredDiseaseCategories($postData)
    {
        $this->_get_disease_categories_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function setUserState($state, $id)
    {
        $this->db->set('emp_isActive', $state);
        $this->db->where('emp_id', $id);
        $this->db->update('employee');
    }
    
    private function _get_datatables_query($postData)
    {
        $this->db->select('d.dis_id as id, d.dis_title as text, d.dis_alias as short, d.dis_token as code, d.dis_regdate as entry_date, c.discat_token as category_code, g.dg_range as group_range, c.discat_name as category, CONCAT(e.emp_fname, " ", e.emp_lname) as author');
        $this->db->from('diseases d');
        $this->db->join('disease_categories c', 'c.discat_token = d.dis_category', 'left');
        $this->db->join('disease_groups g', 'g.dg_token = d.dis_group', 'left');
        $this->db->join('employee e', 'e.emp_pf = d.dis_author', 'left');
        
        $this->db->order_by('d.dis_token', 'ASC');
        
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

     private function _get_disease_groups_datatables_query($postData)
    {
        $this->db->select('dg_range as group_range, dg_token as token, dg_keywords as keywords, dg_description as descriptions');
        $this->db->from('disease_groups');
        $this->db->order_by('dg_range');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->groups_column_search as $item){
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
                if(count($this->groups_column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    private function _get_disease_categories_datatables_query($postData)
    {
        $this->db->select('discat_id as id, discat_name as text, discat_token as token, discat_description as description, discat_regdate as entry_date');
        $this->db->from('disease_categories');
        $this->db->order_by('discat_name');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->category_column_search as $item){
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
                if(count($this->category_column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }
    
    public function checkName($name)
    {
        $this->db->where('dis_title', $name);
        $this->db->from('diseases');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkAlias($alias)
    {
        $this->db->where('dis_alias', $alias);
        $this->db->from('diseases');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkDiseaseToken($token)
    {
        $this->db->where('dis_token', $token);
        $this->db->from('diseases');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkCategoryTokenExist($token)
    {
        $this->db->where('discat_token', $token);
        $this->db->from('disease_categories');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_disease($data)
    {
        $this->db->insert('diseases', $data);
    }
    
    public function add_batch($data)
    {
        if($this->session->userdata('user_role') == 'SUPER' || $this->session->userdata('user_role') == 'ADMIN')
        {
            return $this->db->insert_batch('diseases', $data);
        }
        return TRUE;
    }
    
    public function save_disease_category($data)
    {
        $this->db->insert('disease_categories', $data);
    }
    
    public function get_all_disease_categories()
    {
        $this->db->select('discat_id as id, discat_name as text, discat_token as token');
        $query = $this->db->get('disease_categories');
        return $query->result();
    }

    public function update($where, $data)
    {
        $this->db->where('dis_id', $where);
        return $this->db->update('diseases', $data);
    }
}