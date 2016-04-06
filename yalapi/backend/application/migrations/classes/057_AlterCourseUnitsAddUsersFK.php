<?php

class AlterCourseUnitsAddUserFK extends Doctrine_Migration_Base
{
    private $_tableName = 'course_units';
    private $_fkName = 'fk_course_units_users';

    public function up()
    {
        $this->createForeignKey($this->_tableName, $this->_fkName, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
    }
}
