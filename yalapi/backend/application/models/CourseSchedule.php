<?php

class CourseSchedule extends AclModel
{
    static $table_name = 'course_schedule';
    static $use_view = true;

    static $belongs_to = array(
        array('lesson'),
    );

    static $after_save = 'RunAcl';

    public function RunAcl()
    {
        $group_id = $this->lesson->course_unit->course->group_id;
        if ($group_id)
        {
            $users = GroupUser::user_ids($group_id);
            $this->grant(Role::USER, $users, $this->id);
        }
        
        $this->grant(Role::COACH,$this->lesson->user_id,$this->id);
        

    }
    
    public static function findIdsOnLessonIds($lessons)
    {
        if (!is_array($lessons))
        {
            if (!$lessons) return array();
            $lessons=array($lessons);
        }
        
        return self::getArrayOfFieldValues(self::find('all', array('conditions' => array('lesson_id IN (?)', $lessons))) );
    }
}
