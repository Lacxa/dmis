<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_medicine_format extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'format_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'format_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE

            ),
            'format_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'format_token' => array(
                'type' => 'INT',
                'constraint' => 11,
                'unique' => TRUE
            ),
            'format_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'format_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('format_id', TRUE);
        $this->dbforge->add_key('format_author');
        $this->dbforge->create_table('medicine_formats');
    }

    public function down()
    {
        $this->dbforge->drop_table('medicine_formats');
    }
}