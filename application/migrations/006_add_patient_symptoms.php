<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class Migration_Add_patient_symptoms extends CI_Migration { 
    public function up() { 
            $this->dbforge->add_field(array(
            'sy_id' => array(
                    'type' => 'INT',
                    'constraint' => 11,
                    'unsigned' => TRUE,
                    'auto_increment' => TRUE
            ),
            'sy_record_id' => array(
                    'type' => 'INT',
                    'constraint' => '11',
            ),
            'sy_record_patient_pf' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'sy_complaints' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'sy_descriptions' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'sy_lab' => array(
                    'type' => 'INT',
                    'constraint' => '11',
                    'default' => 0,
            ),
            'sy_investigations' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'sy_diseases' => array(
                    'type' => 'VARCHAR',
                    'constraint' => '100',
                    'null' => TRUE
            ),
            'sy_medicines' => array(
                    'type' => 'TEXT',
                    'null' => TRUE
            ),
            'sy_time DATETIME DEFAULT CURRENT_TIMESTAMP',
        ));
        $this->dbforge->add_key('sy_id', TRUE);
        $this->dbforge->add_key('sy_record_id');
        $this->dbforge->add_key('sy_record_patient_pf');
        $this->dbforge->create_table('patient_symptoms');
    }

    public function down()
    {
        $this->dbforge->drop_table('patient_symptoms');
    }
}