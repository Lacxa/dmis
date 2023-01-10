<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_chief_complaints extends CI_Migration { 
    public function up(){ 
            $this->dbforge->add_field(array(
            'comp_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'comp_token' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unique' => TRUE,
            ),
            'comp_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE
            ),
            'comp_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'comp_descriptions' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('comp_id', TRUE);
        $this->dbforge->add_key('comp_author');
        $this->dbforge->create_table('chief_complaints');
    }

    public function down()
    {
        $this->dbforge->drop_table('chief_complaints');
    }
}