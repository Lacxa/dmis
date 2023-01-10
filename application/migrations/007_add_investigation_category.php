<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_investigation_category extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'icat_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'icat_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100'
            ),
            'icat_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => TRUE
            ),
            'icat_token' => array(
                'type' => 'INT',
                'constraint' => 11,
            ),
            'icat_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('icat_id', TRUE);
        $this->dbforge->create_table('investigation_category');
    }

    public function down()
    {
        $this->dbforge->drop_table('investigation_category');
    }
}