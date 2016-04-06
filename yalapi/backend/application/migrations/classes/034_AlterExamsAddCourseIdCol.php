<?php

class AlterExamsAddCourseIdCol extends Doctrine_Migration_Base
{
    private $_tableName = 'exams';
    private $_colName   = 'course_id';
    private $_fkName    = 'fk_exams_courses';

    public function up()
    {
//        $this->addColumn($this->_tableName, $this->_colName, 'integer', null, array(
//            'notnull' => true
//        ));
//
//        $this->createForeignKey($this->_tableName, $this->_fkName, array(
//             'local'         => $this->_colName,
//             'foreign'       => 'id',
//             'foreignTable'  => 'courses',
//             'onDelete'      => 'CASCADE',
//             'onUpdate'      => 'CASCADE'
//        ));
//
//        $this->removeColumn($this->_tableName, 'type');
//
//        $this->addColumn($this->_tableName, 'type', 'varchar(256)', null, array(
//            'notnull' => true
//        ));
    }

    public function down()
    {
//        $this->dropForeignKey($this->_tableName, $this->_fkName);
//        $this->removeColumn($this->_tableName, $this->_colName);
    }
}
