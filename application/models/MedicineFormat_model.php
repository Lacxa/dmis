<?php

class MedicineFormat_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('format_name', 'format_token', 'format_author', 'format_description');
    }
    
    public function get_all_formats()
    {
        $this->db->select('format_name as title, format_token as token');
        return $this->db->get('medicine_formats')->result();
    }
    
    public function get_formats($postData)
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
        $this->db->from('medicine_formats');
        $this->db->order_by('format_name');
        
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
        $this->db->from('medicine_formats');
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function checkFormatNameExist($title)
    {
        $this->db->where('format_name', $title);
        $this->db->from('medicine_formats');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkFormatTokenExist($token)
    {
        $this->db->where('format_token', $token);
        $this->db->from('medicine_formats');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_formats($data)
    {
        $this->db->insert('medicine_formats', $data);
    }
    
    public function get_format_by_token($token)
    {
        $this->db->select('format_id as id, format_name as title, format_token as token');
        $this->db->where('format_token', $token);
        return $this->db->get("medicine_formats")->row();
    }
}
?>