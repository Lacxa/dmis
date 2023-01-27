<?php

class Medicine_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->column_search = array('m.med_name', 'm.med_alias', 'm.med_token', 'm.med_author', 'c.medcat_name', 'f.format_name');
    }
    
    public function get_Medicines($str)
    {
        $this->db->select('med_id as id, med_name as text, med_alias as short');
        $this->db->where('med_is_available', 1);
        $this->db->like('med_name', $str);
        $this->db->or_like('med_alias', $str);
        $query = $this->db->get('medicines');
        return $query->result();
    }
    
    public function get_all_medicines()
    {
        $this->db->select('m.med_id as id, m.med_name as text, med_token as token, m.med_alias as short, c.medcat_name as category');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->order_by('m.med_token');
        $query = $this->db->get('medicines m');
        return $query->result();
    }
    
    public function get_medicines_by_category_form($category, $form=NULL)
    {
        $this->db->select('m.med_id as id, m.med_name as name, med_token as token, m.med_alias as name2, c.medcat_name as category, f.format_name as form');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('m.med_category', $category);
        if(!empty($form)) $this->db->where('m.med_format', $form);
        $this->db->order_by('m.med_name');
        $query = $this->db->get('medicines m');
        return $query->result();
    }
    
    public function getMedicineById($id)
    {
        $this->db->select('med_id as id, med_name as text, med_alias as short, med_token as token');
        $this->db->where('med_id', $id);
        // $this->db->where('med_is_available', 1);
        $query = $this->db->get('medicines');
        return $query->row_array();
    }
    
    public function getMedicineByToken($token)
    {
        $this->db->select('m.med_id as id, m.med_name as text, m.med_token as token, m.med_alias as short, c.medcat_name as category, f.format_name as format');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('m.med_token', $token);
        // $this->db->where('med_is_available', 1);
        $query = $this->db->get('medicines m');
        return $query->row_array();
    }
    
    public function get_names($postData)
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
        $this->db->from('medicines m');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->order_by('m.med_token', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search as $item){
            // if datatable send POST for search
            if(isset($postData['search']) && $postData['search']['value']){
                // first loop
                if($i===0) {
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
        $this->db->from('medicines');
        return $this->db->count_all_results();
    }
    
    public function countFiltered($postData)
    {
        $this->_get_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function checkUuidExist($uuid)
    {
        $this->db->where('med_id', $uuid);
        $this->db->from('medicines');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkTitleExist($title)
    {
        $this->db->where('med_name', $title);
        $this->db->from('medicines');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkSlagExist($alias)
    {
        $this->db->where('med_alias', $alias);
        $this->db->from('medicines');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkTokenExist($token)
    {
        $this->db->where('med_token', $token);
        $this->db->from('medicines');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_medicines($data)
    {
        $this->db->insert('medicines', $data);
    }
    
    public function setMedicineState($state, $id, $stock_ids)
    {
        $this->db->set('med_is_active', $state);
        $this->db->where('med_id', $id);
        $query = $this->db->update('medicines');
        if($query)
        {
            if(!empty($stock_ids))
            {
                $this->load->model('stock_model', 'stock');
                $data = array('st_is_active' => $state);
                $this->stock->updateArray($stock_ids, $data);
            }
        }
    }
    
}