<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_disease_category extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'discat_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'discat_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE

            ),
            'discat_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'discat_token' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unique' => TRUE
            ),
            'discat_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'discat_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('discat_id', TRUE);
        $this->dbforge->add_key('discat_author');
        $this->dbforge->create_table('disease_categories');
    }

    public function down()
    {
        $this->dbforge->drop_table('disease_categories');
    }
}