<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_disease extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'dis_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'dis_title' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE,
            ),
            'dis_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'unique' => TRUE,
            ), 
            'dis_token' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE,
            ),
            'dis_category' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'dis_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'dis_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('dis_id', TRUE);
        $this->dbforge->add_key('dis_category');
        $this->dbforge->add_key('dis_author');
        $this->dbforge->create_table('diseases');
    }

    public function down()
    {
        $this->dbforge->drop_table('diseases');
    }
}