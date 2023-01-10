<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_medicine extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'med_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40
            ),
            'med_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'unique' => TRUE,
            ),
            'med_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'unique' => TRUE,
            ),
            'med_token' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unique' => TRUE,
            ),
            'med_category' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'med_format' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'med_is_active' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 1,
            ),
            'med_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'med_descriptions' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'med_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('med_id', TRUE);
        $this->dbforge->add_key('med_category');
        $this->dbforge->add_key('med_category');
        $this->dbforge->add_key('med_format');
        $this->dbforge->create_table('medicines');
    }

    public function down()
    {
        $this->dbforge->drop_table('medicines');
    }
}