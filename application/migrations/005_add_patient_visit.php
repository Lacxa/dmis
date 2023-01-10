<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_patient_visit extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'vs_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'vs_record_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'vs_record_patient_pf' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'vs_visit' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'vs_attendants' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => TRUE
            ),
            'vs_time' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '255',
                    'null' => TRUE
            ),
        ));
        $this->dbforge->add_key('vs_id', TRUE);
        $this->dbforge->add_key('vs_record_id');
        $this->dbforge->add_key('vs_record_patient_pf');
        $this->dbforge->create_table('patient_visit');
    }

    public function down()
    {
        $this->dbforge->drop_table('patient_visit');
    }
}