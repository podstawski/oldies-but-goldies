<?php
/**
 * Description
 * @author Radosław Benkel
 */
 
class CreateTableLessons extends Doctrine_Migration_Base
{
    private $_tableName = 'lessons';
    private $_fkName1 = 'fk_lesson_course_unit';
    private $_fkName2 = 'fk_lesson_training_room';
    private $_fkName3 = 'fk_lesson_user';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'course_unit_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'room_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => false,
            ),
            'start_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'end_date' => array(
                'type' => 'timestamp',
                'notnull' => true,
            ),
            'rec_type ' => array(
                'type' => 'varchar(256)',
                'notnull' => true,
            ),
            'event_length' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'event_pid' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName1, array(
            'local'         => 'course_unit_id',
            'foreign'       => 'id',
            'foreignTable'  => 'course_units',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName2, array(
            'local'         => 'room_id',
            'foreign'       => 'id',
            'foreignTable'  => 'rooms',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkName3, array(
            'local'         => 'user_id',
            'foreign'       => 'id',
            'foreignTable'  => 'users',
            'onDelete'      => 'CASCADE',
            'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkName1);
        $this->dropForeignKey($this->_tableName, $this->_fkName2);
        $this->dropForeignKey($this->_tableName, $this->_fkName3);
        $this->dropTable($this->_tableName);
    }
}
