<?php

class CreateIndexes2 extends Doctrine_Migration_Base
{
    private $_indexes = array(
        'idxu_lessons_id'      => 'lessons (id)',
        'idx_lessons_fk'      => 'lessons (course_unit_id,room_id,user_id)',
        'idx_lessons_cycle'      => 'lessons (cycle_id)',
        'idx_lessons_time'      => 'lessons (start_date,end_date)',
        
        'idxu_courses_id'      => 'courses (id)',
        'idx_courses_fk'      => 'courses (training_center_id,project_id,group_id)',
        'idx_courses_time'      => 'courses (start_date,end_date)',
        
        'idxu_course_units_id'      => 'course_units (id)',
        'idx_course_units_fk'      => 'course_units (course_id,user_id)',
        
        'idxu_rooms_id'      => 'rooms (id)',
        'idx_rooms_fk'      => 'rooms (training_center_id)',

        'idxu_group_users_id'      => 'group_users (id)',
        'idx_group_users_fk'      => 'group_users (group_id,user_id)',
    
        'idxu_users_id'      => 'users (id)',
        'idx_users_username'      => 'users (username)',    
    );

    public function up()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            $uniqe=substr($idxName,0,4)=='idxu'?'UNIQUE':'';
            Doctrine_Manager::connection()->exec('CREATE '.$uniqe.' INDEX ' . $idxName . ' ON ' . $idxTable);
        }
    }

    public function down()
    {
        foreach ($this->_indexes as $idxName => $idxTable) {
            Doctrine_Manager::connection()->exec('DROP INDEX IF EXISTS ' . $idxName);
        }
    }
}
