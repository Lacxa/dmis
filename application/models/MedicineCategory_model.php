<?php

class MedicineCategory_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('medcat_name', 'medcat_token', 'medcat_author', 'medcat_description');
    }

    public function get_all_categories()
    {
        $this->db->select('medcat_name as title, medcat_token as token');
        $this->db->order_by('medcat_name', 'ASC');
        return $this->db->get('medicine_categories')->result();
    }
    
    public function get_categories($postData)
    {
        $this->_get_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }
    
    private function _get_datatables_query($postData)
    {
        $this->db->from('medicine_categories');
        $this->db->order_by('medcat_name');
        
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
    
    public function countAll()
    {
        $this->db->from('medicine_categories');
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function get_category_by_id($id)
    {
        $this->db->select('icat_id as id, icat_name as name, icat_token as token');
        $this->db->where('icat_token', $category_token);
        return $this->db->get("investigation_category")->row_array();
    }
    
    public function get_category_by_token($token)
    {
        $this->db->select('medcat_id as id, medcat_name as title, medcat_token as token');
        $this->db->where('medcat_token', $token);
        return $this->db->get("medicine_categories")->row();
    }
    
    public function checkTitleExist($title)
    {
        $this->db->where('medcat_name', $title);
        $this->db->from('medicine_categories');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkTokenExist($token)
    {
        $this->db->where('medcat_token', $token);
        $this->db->from('medicine_categories');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_categories($data)
    {
        $this->db->insert('medicine_categories', $data);
    }
    
}