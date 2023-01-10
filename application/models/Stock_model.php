<?php

class Stock_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
    }
    
    public function get_stock()
    {
        $this->db->select('*');
        $this->db->from('stock s');
        $this->db->where('s.st_parent', '0');
        $this->db->order_by('s.st_entry_date', 'DESC');
        $this->db->order_by('s.st_reg_date', 'DESC');
        $this->db->limit(6);
        
        $query = $this->db->get();        
        $parents = $query->result();
        $i=0;
        foreach($parents as $parent)
        {
            $parents[$i]->sub = $this->get_stock_children($parent->st_id);
            $i++;
        }
        return $parents;
    }
    
    public function get_stock_children($parent)
    {
        $this->db->select('*');
        $this->db->from('stock s');
        $this->db->where('s.st_parent', $parent);
        
        $query = $this->db->get();
        $children = $query->result();
        $i=0;
        foreach($children as $child){
            
            $children[$i]->sub = $this->get_stock_children($child->st_id);
            $i++;
        }
        return $children;       
    }
    
    public function checkUuidExist($uuid)
    {
        $this->db->where('st_id', $uuid);
        $this->db->from('stock');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }   
    
    public function getStockByToken($token)
    {
        $this->db->select('st_id as id, st_parent as parent, st_title as title, st_is_sold as sold, st_code as token, st_medicine as medicine, st_is_active as active, st_total as total, st_usage as used, st_client as clients, st_time as sold_time');
        $this->db->where('st_code', $token);
        $query = $this->db->get('stock');
        return $query->row();
    }

    public function getLeafStockByMedicine($medicine_token)
    {
        $this->db->select('st_id as id, st_parent as parent, st_title as title, st_is_sold as sold, st_code as token, st_medicine as medicine, st_is_active as active, st_total as total, st_usage as used, st_client as clients, st_time as sold_time');
        $this->db->where('st_medicine', $medicine_token);
        $this->db->where('st_level', 100);
        $query = $this->db->get('stock');
        return $query->row();
    } 
    
    public function getStockByID($id)
    {
        $this->db->select('st_id as id, st_parent as parent, st_title as title, st_is_sold as sold, st_code as token, st_medicine as medicine, st_is_active as active, st_total as total, st_usage as used, st_client as clients, st_time as sold_time');
        $this->db->where('st_id', $id);
        $query = $this->db->get('stock');
        return $query->row();
    }    
    
    public function checkIfStockCodeExist($stock_code)
    {
        $this->db->where('st_code', $stock_code);
        $this->db->from('stock');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkTokenPost($stock_code)
    {
        $this->db->where('st_code', $stock_code);
        $this->db->where('st_parent', '0');
        $this->db->where('st_is_active', 0);
        $this->db->from('stock');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function save_new_stock_batch($data)
    {
        foreach ($data as $value)
        {
            $this->db->insert('stock', $value);
        }
        // $this->db->insert_batch('stock', $data);
    }
    
    public function save_new_stock($data)
    {
        $this->db->insert('stock', $data);
    }
    
    public function get_draft_stock()
    {
        $this->db->select('st_id as id, st_title as text, st_code as code, st_entry_date as entry');
        $this->db->where('st_parent', '0');
        $this->db->where('st_is_active', 0);
        $this->db->from('stock');
        $query = $this->db->get();
        return $query->result();
    }
    
    public function get_stock_paths($root_node)
    {
        $sql = "WITH RECURSIVE category_path (st_id, st_title, st_level, path) AS
        (
            SELECT st_id, st_title, st_level, st_title as path
            FROM stock
            WHERE st_parent = '$root_node' UNION ALL
            SELECT c.st_id, c.st_title, c.st_level, CONCAT(cp.path, ' > ', c.st_title)
            FROM category_path AS cp JOIN stock AS c
            ON cp.st_id = c.st_parent
            )
            SELECT * FROM category_path WHERE st_level = '2'
            ORDER BY path;";
            $query_data = $this->db->query($sql)->result();
            
            $data = array();
            foreach ($query_data as $key => $row)
            {
                $row->cf = $row->path;
                $strArr = explode(" > ", $row->path);
                $cat_token = trim($strArr[0]);
                $format_token = trim($strArr[1]);
                $this->load->model('medicineCategory_model', 'category');
                $this->load->model('medicineFormat_model', 'format');
                $cat_data =  $this->category->get_category_by_token($cat_token);
                $format_data =  $this->format->get_format_by_token($format_token);
                
                $new_pathArr = array();
                $new_pathArr[] = $cat_data->title;
                $new_pathArr[] = $format_data->title;
                
                $new_path = implode(" > ", $new_pathArr);
                $row->path = $new_path;
                $data[] = $row;
            }
            return $data;
        }
        
        public function get_full_stock($parent)
        {
            $sql = "WITH RECURSIVE category_path (st_id, st_title, st_level, path) AS
            (
                SELECT st_id, st_title, st_level, st_title as path
                FROM stock
                WHERE st_parent = '$parent' UNION ALL
                SELECT c.st_id, c.st_title, c.st_level, CONCAT(cp.path, ' > ', c.st_title)
                FROM category_path AS cp JOIN stock AS c
                ON cp.st_id = c.st_parent
                )
                SELECT * FROM category_path ORDER BY path";
                $query_data = $this->db->query($sql)->result();
                return $query_data;
            }
            
            public function delete_by_id($id)
            {
                $this->db->where('st_id', $id);
                $this->db->delete('stock');
            }
            
            public function deleteArray($array_data)
            {
                $this->db->where_in('st_id', $array_data);
                $this->db->delete('stock');
            }
            
            public function updateArray($array_ids, $update_data)
            {
                $this->db->where_in('st_id', $array_ids);
                $this->db->update('stock', $update_data);
            }
            
            public function get_Stock_Medicines($keyword)
            {
                $this->db->select('s.st_code as id, m.med_name as name, s.st_title as text, m.med_alias as short, c.medcat_name as category, f.format_name as format, s.st_is_active as active, s.st_total as total, s.st_usage as sold');
                $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
                $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
                $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
                $this->db->where('s.st_level', 100);
                $this->db->where('s.st_is_active', 1);
                $this->db->where('s.st_is_sold', 0);
                
                $this->db->group_start();
                $this->db->like('m.med_name', $keyword);
                
                $this->db->or_group_start();
                $this->db->like('m.med_alias', $keyword);
                
                $this->db->or_group_start();
                $this->db->like('s.st_title', $keyword);
                
                $this->db->or_group_start();
                $this->db->like('c.medcat_name', $keyword);
                
                $this->db->or_group_start();
                $this->db->like('f.format_name', $keyword);
                
                $this->db->group_end();
                $this->db->group_end();
                $this->db->group_end();
                $this->db->group_end();
                $this->db->group_end();
                
                $query = $this->db->get('stock s');
                $data = array();
                foreach($query->result() as $row)
                {
                    if($row->sold < $row->total)
                    {
                        $row->available = $row->total - $row->sold;
                        $data[] = $row;
                    }
                }
                $data[] = array(
                    'id' => '10000001',
                    'name' => 'Out of stock',
                    'short' => 'O/S',
                    'text' => 'O/S'
                );
                return $data;
            }
            
            public function update_stock($data, $id)
            {
                $this->db->where('st_id', $id);
                $query = $this->db->update('stock', $data);
                if($query) return TRUE;
                else return FALSE;
            }             
        }