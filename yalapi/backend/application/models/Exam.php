<?php

class Exam extends AclModel
{
    static $table_name = 'exams';

    static $belongs_to = array(
        array('course_unit')
    );

    static $has_many = array(
        array('exam_grades')
    );

    static $after_save = 'RunAcl';

    public function RunAcl()
    {
        $group_id = $this->course_unit->course->group_id;
        if (!$group_id) {
            return;
        }

        $users = GroupUser::user_ids($group_id);
        $this->grant(Role::USER, $users, $this->id);
    }
}
