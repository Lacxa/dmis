<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_patient_record extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'rec_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'rec_patient_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'rec_attendant_file_no' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_patient_file' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
            ),
            'rec_blood_pressure' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_pulse_rate' => array(
                    'type' => 'INT',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_weight' => array(
                    'type' => 'INT',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_height' => array(
                    'type' => 'INT',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_temeperature' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'rec_regdate DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('rec_id', TRUE);
        $this->dbforge->add_key('rec_patient_id');
        $this->dbforge->add_key('rec_patient_file');
        $this->dbforge->add_key('rec_attendant_file_no');
        $this->dbforge->create_table('patient_record');
    }

    public function down()
    {
        $this->dbforge->drop_table('patient_record');
    }
}