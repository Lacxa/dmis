<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_medicine_category extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'medcat_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'medcat_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE

            ),
            'medcat_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'medcat_token' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unique' => TRUE
            ),
            'medcat_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'medcat_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('medcat_id', TRUE);
        $this->dbforge->add_key('medcat_author');
        $this->dbforge->create_table('medicine_categories');
    }

    public function down()
    {
        $this->dbforge->drop_table('medicine_categories');
    }
}