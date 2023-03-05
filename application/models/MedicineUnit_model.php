<?php

class MedicineUnit_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('mu_name', 'mu_unit', 'mu_token', 'mu_author');
    }
    
    public function get_all_units()
    {
        $this->db->select('mu_name as title, mu_unit as unit, mu_token as token');
        $this->db->order_by('mu_name', 'ASC');
        return $this->db->get('medicine_units')->result();
    }
    
    public function get_units($postData)
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
        $this->db->select('mu_id as id, mu_name as name, mu_unit as unit, mu_token as token, mu_author as author, mu_created_at as createdAt');
        $this->db->from('medicine_units');
        $this->db->order_by('mu_token DESC', 'mu_name ASC');
        
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
        $this->db->from('medicine_units');
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function checkMedicineUnitExist($title, $unit)
    {
        $this->db->where('mu_name', $title);
        $this->db->where('mu_unit', $unit);
        $query = $this->db->get('medicine_units');
        return $query->num_rows() > 0;
    }
    
    public function checkMedicineUnitTokenExist($token)
    {
        $this->db->where('mu_token', $token);
        $this->db->from('medicine_units');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_units($data)
    {
        $this->db->insert('medicine_units', $data);
    }
    
    public function get_format_by_token($token)
    {
        $this->db->select('format_id as id, format_name as title, format_token as token');
        $this->db->where('format_token', $token);
        return $this->db->get("medicine_formats")->row();
    }
}
?>