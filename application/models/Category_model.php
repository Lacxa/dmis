<?php

class Category_model extends CI_Model {

	public function __construct()
    {
		parent::__construct();
		$this->load->database();
	}

    public function all_categories()
    {
        $exclusions = array('SUPER', 'ADMIN');
        $this->db->where_not_in('cat_alias', $exclusions);
        $this->db->order_by('cat_name', 'ASC');
        return $this->db->get("employee_category")->result_array();
    }

    public function all_user_roles()
    {
        $exclusions = array('SUPER');
        if($this->session->userdata('user_role') == 'ADMIN')
        {
            $exclusions = array('SUPER', 'ADMIN');
        }
        $this->db->where_not_in('role_alias', $exclusions);
        $this->db->order_by('role_name', 'ASC');
        return $this->db->get("user_roles")->result_array();
    }

    public function all_user_roles2()
    {
        $exclusions = array('SUPER');
        $this->db->where_not_in('role_alias', $exclusions);
        $this->db->order_by('role_name', 'ASC');
        return $this->db->get("user_roles")->result_array();
    }

    public function get_category_by_id($id)
    {
        $this->db->where('cat_id', $id);
        return $this->db->get("employee_category")->result_array();
    }

    public function get_role_by_id($id)
    {
        $this->db->where('role_id', $id);
        return $this->db->get("user_roles")->result_array();
    }

}