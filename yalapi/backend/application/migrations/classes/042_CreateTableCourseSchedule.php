<?php

class CreateTableCourseSchedule extends Doctrine_Migration_Base
{
    private $_tableName = 'course_schedule';
    private $_fkName    = 'fk_course_schedule_group';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true
            ),
            'course_unit_id' => array(
                'type' => 'integer',
                'notnull' => true
            ),
            'lesson_date' => array(
                'type' => 'date',
                'notnull' => true
            ),
            'schedule' => array(
                'type' => 'text',
                'notnull' => true
            ),
            'subject' => array(
                'type' => 'varchar(256)',
                'notnull' => true
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName, array(
            'local'         => 'course_unit_id',
            'foreign'       => 'id',
            'foreignTable'  => 'course_units',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName);
        $this->dropTable($this->_tableName);
    }

    public function preDown()
    {
       Doctrine_Manager::connection()->exec("SELECT drop_acl_view('".$this->_tableName."')");
    }
}
