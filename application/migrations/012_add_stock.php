<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_stock extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'st_id' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40
            ),
            'st_parent' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'default' => 0,
            ),
            'st_title' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => TRUE,
            ),
            'st_code' => array(
                    'type' => 'VARCHAR',
                    'constraint' => 40,
                    'uniques' => TRUE,
            ),
            'st_level' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
            ),
            'st_medicine' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '40',
                    'null' => TRUE,
            ),
            'st_supplier' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
            ),
            'st_desc' => array(
                'type' => 'TEXT',
                'null' => TRUE,
            ),
            'st_entry_date DATE NULL',
            'st_reg_date DATETIME DEFAULT CURRENT_TIMESTAMP',
            'st_author' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50',
            ),
            'st_is_active' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
            ),
            'st_total' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
            ),
            'st_is_sold' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
            ),
            'st_client' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '50',
                    'null' => TRUE,
            ),
            'st_sold_date DATETIME NULL',
        ));
        $this->dbforge->add_key('st_id', TRUE);
        $this->dbforge->add_key('st_parent');
        $this->dbforge->add_key('st_medicine');
        $this->dbforge->add_key('st_author');
        $this->dbforge->add_key('st_client');
        $this->dbforge->create_table('stock');
    }

    public function down()
    {
        $this->dbforge->drop_table('stock');
    }
}