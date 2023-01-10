<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_user_roles extends CI_Migration { 
    public function up(){ 
            $this->dbforge->add_field(array(
            'role_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'role_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'unique' => TRUE
            ),
            'role_alias' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20',
                    'unique' => TRUE
            ),
            'role_descriptions' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('role_id', TRUE);
        $this->dbforge->create_table('user_roles');
    }

    public function down()
    {
        $this->dbforge->drop_table('user_roles');
    }
}