<?php

class Stock_model extends CI_Model {
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $this->consumption_column_search = array('c.su_id', 'c.su_record', 'c.su_stock', 'c.su_usage');
        $this->column_search_stock_status = array('m.med_name', 'm.med_alias', 'c.medcat_name', 'f.format_name', 's.st_unit_value', 'u.mu_unit');
    }
    
    public function get_stock($rowno, $rowperpage)
    {
        $this->db->select('sb_id as id, sb_number as token, sb_supplier as supplier, sb_entry_date as entry, sb_active as state');
        $this->db->order_by('sb_entry_date DESC', 'sb_created_at DESC');
        $this->db->limit($rowperpage, $rowno);        
        $query = $this->db->get('stock_batches');        
        $parents = $query->result();
        $i=0;
        foreach($parents as $parent)
        {
            $parents[$i]->sub = $this->get_stock_children($parent->id);
            $i++;
        }
        return $parents;
    }

    public function getStockCount()
    {
    	$this->db->select('count(*) as allcount');
      	$this->db->from('stock_batches');
      	$query = $this->db->get();
      	$result = $query->result_array();      
      	return $result[0]['allcount'];
    }
    
    public function get_stock_children($parent)
    {
        $this->db->select('s.st_id as stock_id, s.st_code as stock_token, s.st_unit_value as unit_value, s.st_total as total, s.st_usage as usage, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form');
        $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
        $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('s.st_batch', $parent);
        $this->db->order_by('f.format_name ASC', 's.st_date_created DESC');
        
        $query = $this->db->get('stock s');
        $children = $query->result();
        $new_children = [];
        foreach($children as $child)
        {
            if(!array_key_exists($child->category, $new_children))
            {
                $new_children[$child->category] = [];
            }
            $new_children[$child->category][] = $child;
        }
        return $new_children;       
    }
    
    public function checkIfStockUsageUuidExist($uuid)
    {
        $this->db->where('su_id', $uuid);
        $this->db->from('stock_usage');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }

    public function getStockUsageByRecord($record)
    {
        $this->db->select('su_id as id, su_stock as stock, su_usage as used');
        $this->db->where('su_record', $record);
        $query = $this->db->get('stock_usage');
        return $query->result();
    }

    public function getStockUsageByRecordAndStock($record, $stock)
    {
        $this->db->select('su_id as id, su_stock as stock, su_usage as used');
        $this->db->where('su_record', $record);
        $this->db->where('su_stock', $stock);
        $query = $this->db->get('stock_usage');
        return $query->result();
    } 
    
    public function checkUuidExist($uuid)
    {
        $this->db->where('st_id', $uuid);
        $this->db->from('stock');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function checkIfUuidBatchExist($uuid)
    {
        $this->db->where('sb_id', $uuid);
        $this->db->from('stock_batches');
        $query = $this->db->get();
        return $query->num_rows() > 0;
    }
    
    public function getBatchByToken($token)
    {
        $this->db->select('sb_id as id');
        $this->db->where('sb_number', $token);
        $query = $this->db->get('stock_batches');
        return $query->row();
    } 

    public function getStockByID($id)
    {
        $this->db->select('s.st_id as id, s.st_code as token, s.st_unit_value as unit_value, s.st_total as total, s.st_usage as used, s.st_date_created as entry, s.st_medicine as medicine_token, s.st_unit as unit_token, b.sb_entry_date as entry2, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form');
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
        $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('s.st_id', $id);
        $query = $this->db->get('stock s');
        return $query->row();
    } 
    
    public function getStockByToken($token)
    {
        $this->db->select('s.st_id as id, s.st_code as token, s.st_unit_value as unit_value, s.st_total as total, s.st_usage as used, s.st_date_created as entry, s.st_medicine as medicine_token, s.st_unit as unit_token, b.sb_entry_date as entry2, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form, CONCAT(u.mu_name, ": ",s.st_unit_value, " ",u.mu_unit) as unit2');
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
        $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('s.st_code', $token);
        $query = $this->db->get('stock s');
        return $query->row();
    }

    public function getSimilarStock(Array $base_data)
    {
        $this->db->select('s.st_id as id, s.st_code as token, s.st_unit_value as unit_value, s.st_total as total, s.st_usage as used, s.st_date_created as entry, s.st_medicine as medicine_token, b.sb_entry_date as entry2, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form');
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
        $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('s.st_id !=', $base_data['except']);
        $this->db->where('s.st_medicine', $base_data['medicine_token']);
        $this->db->where('s.st_unit', $base_data['unit_token']);
        $this->db->where('s.st_unit_value', $base_data['unit_value']);
        $this->db->where('b.sb_active', 1);
        $this->db->order_by('b.sb_entry_date', 'DESC');
        $query = $this->db->get('stock s');
        $data = [];
        if($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                if($row->used < $row->total) $data[] = $row;
            }
        }
        return $data;        
    }

    public function get_stock_consumption($postData, $stock_id)
    {   
        $this->_get_datatables_query_for_stock_consumption($postData, $stock_id);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
        
    }

    private function _get_datatables_query_for_stock_consumption($postData, $stock_id)
    {
        $this->db->select('c.su_id as id, CONCAT(p.pat_fname, " ", p.pat_lname) as full_name, p.pat_file_no as file, p.pat_gender as gender, p.pat_occupation as occupation, p.pat_phone as phone, c.su_usage as consumption, DATE_FORMAT(c.su_time, "%b %d %Y %H:%i") as entry');
        $this->db->from('stock_usage c');
        $this->db->join('patient_record r', 'r.rec_id = c.su_record', 'left');
        $this->db->join('patient p', 'p.pat_id = r.rec_patient_id', 'left');
        $this->db->where('c.su_stock', $stock_id);
        $this->db->order_by('c.su_time', 'DESC');
        
        $i = 0;
        // loop searchable columns 
        foreach($this->consumption_column_search as $item){
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
                if(count($this->consumption_column_search) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    public function count_all_stock_consumption($stock_id)
    {
        $this->db->from("stock_usage");
        $this->db->where('su_stock', $stock_id);
        return $this->db->count_all_results();
    }

    public function countFilteredStockConsumption($postData, $stock_id)
    {
        $this->_get_datatables_query_for_stock_consumption($postData, $stock_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function getStockStatus($postData)
    {   
        $this->_get_datatables_query_for_stock_status($postData);
        if(isset($postData['length']) && $postData['length'] != -1){
            $this->db->limit($postData['length'], $postData['start']);
        }
        $query = $this->db->get();
        return $query->result();
        
    }

    public function _get_datatables_query_for_stock_status($postData)
    {
        $this->db->select('SUM(s.st_total) as total, SUM(s.st_usage) as used, s.st_code as sn, s.st_medicine as medicine_token, CONCAT(m.med_name, " (", m.med_alias,")") as medicine, CONCAT(u.mu_name, ": ",s.st_unit_value, " ",u.mu_unit) as unit, c.medcat_name as category, f.format_name as form');
        $this->db->from('stock s');
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
        $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
        $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
        $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
        $this->db->where('b.sb_active', 1);
        $this->db->where('s.st_usage < s.st_total');
        $this->db->group_by(array("s.st_medicine", "s.st_unit", "s.st_unit_value"));
        
        $i = 0;
        // loop searchable columns 
        foreach($this->column_search_stock_status as $item){
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
                if(count($this->column_search_stock_status) - 1 == $i){
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
    }

    public function count_all_stock_status()
    {
        $this->db->from("stock s");
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->where('b.sb_active', 1);
        $this->db->where('s.st_usage < s.st_total');
        $this->db->group_by(array("s.st_medicine", "s.st_unit", "s.st_unit_value"));
        return $this->db->count_all_results();
    }

    public function countFilteredStockStatus($postData)
    {
        $this->_get_datatables_query_for_stock_status($postData);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function checkStockMedicineIDToDeleteExist($id)
    {
        $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
        $this->db->where('s.st_id', $id);
        $this->db->where('b.sb_active', 0);
        $query = $this->db->get('stock s');
        return $query->num_rows() > 0;
    }
    
    public function checkStockBatchTokenToDeleteExist($token)
    {
        $this->db->where('sb_number', $token);
        $this->db->where('sb_active', 0);
        $query = $this->db->get('stock_batches');
        return $query->num_rows() > 0;
    }

    public function checkBatchToPost($token)
    {
        $this->db->where('sb_number', $token);
        $this->db->where('sb_active', 0);
        $query = $this->db->get('stock_batches');
        if($query->num_rows() > 0)
        {
            $data = $query->row();
            $this->db->select('st_id as stock_id');
            $this->db->where('st_batch', $data->sb_id);
            $children = $this->db->get('stock')->num_rows();
            if($children > 0) return TRUE;
            else return FALSE;
        }
        else
        {
         return FALSE;
     }
 }

 public function getLeafStockByMedicine($medicine_token)
 {
    $this->db->select('st_id as id, st_parent as parent, st_title as title, st_is_sold as sold, st_code as token, st_medicine as medicine, st_is_active as active, st_total as total, st_usage as used, st_client as clients, st_time as sold_time');
    $this->db->where('st_medicine', $medicine_token);
    $this->db->where('st_level', 100);
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

public function checkIfBatchCodeExist($stock_code)
{
    $this->db->where('sb_number', $stock_code);
    $this->db->from('stock_batches');
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

public function save_stock_array($data)
{
    foreach ($data as $value)
    {
        $this->db->insert('stock', $value);
    }
}

public function save_stock($data)
{
    $this->db->insert('stock', $data);
}

public function save_stock_usage($data)
{
    $this->db->insert('stock_usage', $data);
}

public function save_stock_usage_array($data)
{
    foreach ($data as $value)
    {
        $this->db->insert('stock_usage', $value);
    }
}

public function save_batch($data)
{
    $this->db->insert('stock_batches', $data);
}

public function get_draft_batches()
{
    $this->db->select('sb_id as id, sb_number as code, sb_supplier as supplier, sb_entry_date as entry');
    $this->db->from('stock_batches');
    $this->db->where('sb_active', 0);
    $this->db->order_by('sb_entry_date', 'DESC');
    $query = $this->db->get();
    return $query->result();
}

public function checkIfBatchIDIsDraft($batch_id)
{
    $this->db->where('sb_id', $batch_id);
    $this->db->where('sb_active', 0);
    $query = $this->db->get('stock_batches');
    return ($query->num_rows() > 0);
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

public function get_batch_medicines($batch_id)
{
    $this->db->select('st_id as id');
    $this->db->where('st_batch', $batch_id);
    $query = $this->db->get('stock');
    return $query->result();
}

public function delete_batch_by_id($id)
{
    $this->db->where('sb_id', $id);
    $this->db->delete('stock_batches');
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

public function updateBatchArray($array_ids, $update_data)
{
    $this->db->where_in('sb_id', $array_ids);
    $this->db->update('stock_batches', $update_data);
}

public function updateArray($array_ids, $update_data)
{
    $this->db->where_in('st_id', $array_ids);
    $this->db->update('stock', $update_data);
}

public function get_Stock_Medicines($keyword)
{
    // $this->db->select('s.st_id as stock_id, s.st_code as stock_token, s.st_unit_value as unit_value, s.st_total as total, s.st_usage as usage, DATE_FORMAT(s.st_date_created, "%M %d %Y") as entry, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form');
    $this->db->select('s.st_id as stock_id, s.st_code as stock_token, s.st_unit_value as unit_value, SUM(s.st_total) as total, SUM(s.st_usage) as usage_, DATE_FORMAT(s.st_date_created, "%M %d %Y") as entry, u.mu_name as unit_title, u.mu_unit as unit_name, m.med_name as medicine1, m.med_alias as medicine2, c.medcat_name as category, f.format_name as form');
    $this->db->join('stock_batches b', 'b.sb_id = s.st_batch', 'left');
    $this->db->join('medicine_units u', 'u.mu_token = s.st_unit', 'left');
    $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
    $this->db->join('medicine_categories c', 'c.medcat_token = m.med_category', 'left');
    $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
    $this->db->where('b.sb_active', 1);
    
    $this->db->group_start();
    $this->db->like('m.med_name', $keyword);
    
    $this->db->or_group_start();
    $this->db->like('m.med_alias', $keyword);
    
    $this->db->or_group_start();
    $this->db->like('c.medcat_name', $keyword);
    
    $this->db->or_group_start();
    $this->db->like('f.format_name', $keyword);

    $this->db->or_group_start();
    $this->db->like('u.mu_unit', $keyword);

    $this->db->or_group_start();
    $this->db->like('u.mu_name', $keyword);

    $this->db->or_group_start();
    $this->db->like('s.st_unit_value', $keyword);
    
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_end();
    $this->db->group_by(array("s.st_medicine", "s.st_unit", "s.st_unit_value"));

    // $this->db->order_by('s.st_date_created', "DESC");

    $query = $this->db->get('stock s');
    $data = array();
    foreach($query->result() as $row)
    {
        if($row->usage_ < $row->total)
        {
            $row->available = $row->total - $row->usage_;
            $data[] = $row;
        }
    }
    $data[] = array(
        'stock_token' => '10000001',
        'medicine1' => 'Out of stock',
        'medicine2' => 'O/S',
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

public function update_stock_batch($data)
{
    $this->db->update_batch('stock', $data, 'st_id');
}

public function restore_stock_usage($arrayData)
{
    foreach ($arrayData as $key => $row)
    {
        $this->db->set('st_usage', 'st_usage-'.$row['restore'], FALSE);
        $this->db->where('st_id', $row['id']);
        $this->db->update('stock'); 
    }
}

public function stock_usage_by_record($record)
{
    $this->db->select('u.su_usage as consumption, CONCAT(un.mu_name, ": ",s.st_unit_value, " ",un.mu_unit) as unit, m.med_name as medicine, f.format_name as form');
    $this->db->join('stock s', 's.st_id = u.su_stock', 'left');
    $this->db->join('medicine_units un', 'un.mu_token = s.st_unit', 'left');
    $this->db->join('medicines m', 'm.med_token = s.st_medicine', 'left');
    $this->db->join('medicine_formats f', 'f.format_token = m.med_format', 'left');
    $this->db->where('u.su_record', $record);
    $query = $this->db->get('stock_usage u');
    return $query->result();    
}

}