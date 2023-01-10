<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_employee_category extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'cat_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'cat_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE
            ),
            'cat_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'unique' => TRUE
            ),
            'cat_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('cat_id', TRUE);
        $this->dbforge->create_table('employee_category');
    }

    public function down()
    {
        $this->dbforge->drop_table('employee_category');
    }
}