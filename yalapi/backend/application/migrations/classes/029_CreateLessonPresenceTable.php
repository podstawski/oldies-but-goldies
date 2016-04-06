<?php

class CreateLessonPresenceTable extends Doctrine_Migration_Base
{
    private $_tableName = 'lesson_presence';
    private $_fkLessons = 'fk_lesson_presence_lessons';
    private $_fkUsers   = 'fk_lesson_presence_users';

    public function up()
    {
        $this->createTable($this->_tableName, array(
            'id' => array(
                'type' => 'integer',
                'primary' => true,
                'autoincrement' => true,
            ),
            'lesson_id' => array(
                'type' => 'integer',
                'notnull' => true,
            ),
            'user_id' => array(
                'type' => 'integer',
                'notnull' => true,
            )
        ));

        $this->createForeignKey($this->_tableName, $this->_fkLessons, array(
             'local'         => 'lesson_id',
             'foreign'       => 'id',
             'foreignTable'  => 'lessons',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));

        $this->createForeignKey($this->_tableName, $this->_fkUsers, array(
             'local'         => 'user_id',
             'foreign'       => 'id',
             'foreignTable'  => 'users',
             'onDelete'      => 'CASCADE',
             'onUpdate'      => 'CASCADE'
        ));
    }

    public function down()
    {
        $this->dropForeignKey($this->_tableName, $this->_fkUsers);
        $this->dropForeignKey($this->_tableName, $this->_fkLessons);
        $this->dropTable($this->_tableName);
    }
}
