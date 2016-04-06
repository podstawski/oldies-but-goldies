<?php

class AlterCourseScheduleAddSubject extends Doctrine_Migration_Base
{
    private $_tableName = 'course_schedule';
    private $_colName   = 'subject';

    public function up()
    {
//        $this->addColumn($this->_tableName, $this->_colName, 'varchar(256)', null, array(
//            'notnull' => true
//        ));
    }

    public function down()
    {
//        $this->removeColumn($this->_tableName, $this->_colName);
    }
}
