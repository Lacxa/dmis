<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_patient extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'pat_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'pat_file_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'pat_fname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100'
            ),
            'pat_mname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'pat_lname' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100'
            ),
            'pat_dob DATE',
            'pat_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
            'pat_gender' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '20'
            ),
            'pat_occupation' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'pat_phone' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '15',
            ),
            'pat_address' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'pat_em_number' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '15',
            ),
            'pat_em_name' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100'
            ),
            'pat_nhif_card_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
            ),
            'pat_nhif_auth_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
            ),
            'pat_vote_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE,
            ),
        ));
        $this->dbforge->add_key('pat_id', TRUE);
        $this->dbforge->create_table('patient');
    }

    public function down()
    {
        $this->dbforge->drop_table('patient');
    }
}