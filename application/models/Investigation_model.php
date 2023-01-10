<?php

class Investigation_model extends CI_Model {

	public function __construct()
    {
		parent::__construct();
		$this->load->database();
        $this->column_search = array('sub.isub_token', 'sub.isub_name', 'sub.isub_alias', 'sub.isub_description', 'cat.icat_name', 'cat.icat_alias');
	}

    public function get_all_investigation_categories()
    {
        $this->db->order_by('icat_name');
        return $this->db->get("investigation_category")->result();
    }
    
    public function get_investigation_category_by_id($category_token)
    {
        $this->db->select('icat_id as id, icat_name as name, icat_token as token');
        $this->db->where('icat_token', $category_token);
        return $this->db->get("investigation_category")->row_array();
    }

    public function get_all_investigation_subcategories()
    {
        $this->db->order_by('isub_name');
        return $this->db->get("investigation_subcategory")->result();
    }
    
    public function get_investigation_subategories_by_category($category)
    {
        $this->db->select('isub_id as id, isub_name as name, isub_token as token');
        $this->db->where('isub_category', $category);
        return $this->db->get("investigation_subcategory")->result_array();
    }
        
    public function get_investigation_subcategories_by_token($token)
    {
        $this->db->select('sub.isub_id as id, sub.isub_name as name, sub.isub_alias as alias, sub.isub_token as token, cat.icat_name as parent');
        $this->db->join('investigation_category cat', 'cat.icat_token = sub.isub_category', 'left');
        $this->db->where('sub.isub_token', $token);
        $this->db->order_by('cat.icat_name', 'ASC');
        return $this->db->get("investigation_subcategory sub")->row_array();
    }
        
    public function list_of_lab_diagnosis($postData)
    {
        $this->_get_diagnosis_datatables_query($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
    }

    private function _get_diagnosis_datatables_query($postData)
    {
        $this->db->select('sub.isub_id as id, sub.isub_name as name, sub.isub_alias as unit, sub.isub_token as code, cat.icat_name as parent, cat.icat_alias as parent_alias');
        $this->db->from('investigation_subcategory sub');
        $this->db->join('investigation_category cat', 'cat.icat_token = sub.isub_category', 'left');        
        $this->db->order_by('cat.icat_name ASC, cat.icat_alias ASC, sub.isub_name ASC');
        
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

    public function countLabDiagnosis()
    {
        $this->db->from('investigation_subcategory sub');
        $this->db->join('investigation_category cat', 'cat.icat_token = sub.isub_category', 'left');
        return $this->db->count_all_results();
    }

    public function countFilteredLabDiagnosis($postData)
    {
        $this->_get_diagnosis_datatables_query($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }

}