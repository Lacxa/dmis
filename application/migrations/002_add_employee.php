<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_employee extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'emp_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'emp_pf' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => true,
            ),
            'emp_category' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'emp_role' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'emp_fname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20'
            ),
            'emp_mname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => TRUE,
            ),
            'emp_lname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20'
            ),
            'emp_mail' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => true,
            ),
            'emp_phone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '15',
                    'unique' => true,
            ),
            'emp_password' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'default' => '123456'
            ),
            'emp_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
            'emp_pwd_changed_at DATETIME NULL',
        ));
        $this->dbforge->add_key('emp_id', TRUE);
        $this->dbforge->add_key('emp_category');
        $this->dbforge->add_key('emp_role');
        $this->dbforge->create_table('employee');
    }

    public function down()
    {
        $this->dbforge->drop_table('employee');
    }
}