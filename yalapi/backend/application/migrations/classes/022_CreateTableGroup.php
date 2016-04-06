<?php
/**
 * Description
 * @author Marcin Wawrzyniak
 */

class CreateTableGroup extends Doctrine_Migration_Base
{
    private $_tableName = 'groups';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'name' => array(
                'type' => 'varchar(256)'
            ),
            'advance_level' => array(
                'type' => 'varchar(256)'
            )
        ));

        $this->addColumn('courses', 'group_id', 'integer', null, array('notnull' => false));
        $this->createForeignKey('courses', 'fk_courses_groups', array(
            'local'         => 'group_id',
            'foreign'       => 'id',
            'foreignTable'  => 'groups',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey('courses', 'fk_courses_groups');
        $this->removeColumn('courses', 'group_id');
        $this->dropTable($this->_tableName);
    }
}
