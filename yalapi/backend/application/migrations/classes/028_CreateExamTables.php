<?php

class CreateExamTables extends Doctrine_Migration_Base
{
    private $_tableExams      = 'exams';
    private $_tableExamGrades = 'exam_grades';
    
    private $_fkExamsCourseUnits = 'fk_exams_course_units';
    private $_fkExamGradesExams  = 'fk_exam_grades_exams';
    private $_fkExamGradesUsers  = 'fk_exam_grades_users';

    public function up()
    {
        $this->createTable($this->_tableExams, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'course_unit_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'name' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'type' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'created_date' => array(
                'type' => 'date',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableExams, $this->_fkExamsCourseUnits, array(
             'local'         => 'course_unit_id',
             'foreign'       => 'id',
             'foreignTable'  => 'course_units',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createTable($this->_tableExamGrades, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'exam_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'grade' => array(
                'type' => 'numeric(4,2)',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableExamGrades, $this->_fkExamGradesExams, array(
             'local'         => 'exam_id',
             'foreign'       => 'id',
             'foreignTable'  => 'exams',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableExamGrades, $this->_fkExamGradesUsers, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableExamGrades, $this->_fkExamGradesUsers);
        $this->dropForeignKey($this->_tableExamGrades, $this->_fkExamGradesExams);
        $this->dropTable($this->_tableExamGrades);

        $this->dropForeignKey($this->_tableExams, $this->_fkExamsCourseUnits);
        $this->dropTable($this->_tableExams);
    }
}
