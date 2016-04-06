<?php

class CreateTableCompetenceExams extends Doctrine_Migration_Base
{
    private $_tableName = 'exam_competencies';
    private $_fkName1 = 'fk_exam_competencies_competencies';
    private $_fkName2 = 'fk_exam_competencies_exams';

    function up()
    {
        $this->createTable($this->_tableName, array(
            'competence_id' => array(
                'type' => 'Integer',
                'notnull' => true,
				'primary' => true
            ),
            'exam_id' => array(
                'type' => 'Integer',
                'notnull' => true,
				'primary' => true
            ),

        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
             'local'         => 'competence_id',
             'foreign'       => 'id',
             'foreignTable'  => 'competencies',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
             'local'         => 'exam_id',
             'foreign'       => 'id',
             'foreignTable'  => 'exams',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

    }


    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropTable($this->_tableName);
    }


}
