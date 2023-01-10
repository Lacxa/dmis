<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_investigation_subcategory extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'isub_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'isub_category' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'isub_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'isub_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'null' => TRUE
            ),
            'isub_token' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'isub_description' => array(
                    'type' => 'TEXT',
                    'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('isub_id', TRUE);
        $this->dbforge->add_key('isub_category');
        $this->dbforge->create_table('investigation_subcategory');
    }

    public function down()
    {
        $this->dbforge->drop_table('investigation_subcategory');
    }
}