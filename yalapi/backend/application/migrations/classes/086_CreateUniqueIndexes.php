<?php

class CreateUniqueIndexes extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idx_group_user'      => 'group_users (group_id, user_id)',
        'idx_exam_grades'     => 'exam_grades (exam_id, user_id)',
        'idx_lesson_presence' => 'lesson_presence (lesson_id, user_id)',
    );

    public function up()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            Doctrine_Manager::connection()->exec('CREATE UNIQUE INDEX ' . $idxName . ' ON ' . $idxTable);
        }
    }

    public function down()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            Doctrine_Manager::connection()->exec('DROP INDEX IF EXISTS ' . $idxName);
        }
    }
}
