<?php

class AlterLessonsAddGroupId extends Doctrine_Migration_Base
{
//    private $_tableName = 'lessons';
//    private $_colName   = 'group_id';
//    private $_fkName    = 'fk_lessons_groups';

    public function up()
    {
//        $this->addColumn($this->_tableName, $this->_colName, 'integer', null, array(
//            'notnull' => true
//        ));
//
//        $this->createForeignKey($this->_tableName, $this->_fkName, array(
//             'local'         => $this->_colName,
//             'foreign'       => 'id',
//             'foreignTable'  => 'groups',
//             'onDelete'      => 'CASCADE',
//             'onUpdate'      => 'CASCADE'
//        ));
    }

    public function down()
    {
//        $this->dropForeignKey($this->_tableName, $this->_fkName);
//        $this->removeColumn($this->_tableName, $this->_colName);
    }
}
