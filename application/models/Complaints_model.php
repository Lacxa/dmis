<?php

class Complaints_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('comp_token', 'comp_name', 'comp_descriptions', 'comp_author');
    }
    
    public function getComplaint($str)
    {
        $this->db->select('comp_id as id, comp_name as text, comp_token as token, comp_author as author');
        $this->db->like('comp_name', $str);
        $this->db->or_like('comp_token', $str);
        $this->db->or_like('comp_author', $str);
        $query = $this->db->get('chief_complaints');
        return $query->result();
    }
    
    public function getComplaintHavingHistory($str)
    {
        $this->db->select('comp_id as id, comp_name as text, comp_token as token, comp_author as author');
        $this->db->like('comp_name', $str);
        $this->db->or_like('comp_token', $str);
        $this->db->or_like('comp_author', $str);
        $query = $this->db->get('chief_complaints');
        return $query->result();
    }
    
    public function getComplaintById($id)
    {
        $this->db->select('comp_id as id, comp_name as text, comp_token as token, comp_author as author');
        $this->db->where('comp_id', $id);
        $query = $this->db->get('chief_complaints');
        return $query->row();
    }
    
    public function getComplaintByToken($token)
    {
        $this->db->select('comp_id as id, comp_name as text, comp_token as token, comp_author as author');
        $this->db->where('comp_token', $token);
        $query = $this->db->get('chief_complaints');
        return $query->row();
    }
    
    public function get_complaints($postData)
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
        $this->db->from("chief_complaints");
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    private function _get_datatables_query($postData)
    {
        $this->db->select('comp_id as id, comp_name as text, comp_token as token, comp_descriptions as desc, comp_author as author');
        $this->db->from('chief_complaints');
        
        $this->db->order_by('comp_token', 'desc');
        
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
    
    public function checkNameIfExist($name)
    {
        $this->db->where('comp_name', $name);
        $this->db->from('chief_complaints');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function checkIfTokenExist($token)
    {
        $this->db->where('comp_token', $token);
        $this->db->from('chief_complaints');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_complaints($data)
    {
        $this->db->insert('chief_complaints', $data);
    }
}