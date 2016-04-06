<?php

class GroupUser extends AclModel
{
    static $table_name = 'group_users';

    static $belongs_to = array(
        array('user'),
        array('group')
    );

    static function user_ids($group_id)
    {
        return self::getArrayOfFieldValues(self::find('all', array('conditions' => array('group_id = ?', $group_id))), 'user_id');
    }

    static $after_save = 'RunAcl';

    static function RunAclDelete($group_id, $user_id)
    {
        $lessons = Course::findAllLessons(Group::findAllCourseIds($group_id));
        self::revoke(Role::USER, $user_id, $lessons, 'lessons');
        self::revoke(Role::USER, $user_id, $group_id, 'groups');
        $courseIds = Group::findAllCourseIds($group_id);

        if (count($courseIds)) {
            self::revoke(Role::USER, $user_id, $courseIds, 'courses');
            $courseUnitIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $courseIds))));
            if (count($courseUnitIds)) self::revoke(Role::USER, $user_id, $courseUnitIds, 'course_units');
        }
    }

    public function RunAcl()
    {
        if ($this->field_has_changed('user_id')) {
            $user_id = $this->user_id;
            $group_id = $this->group_id;

            $courseIds = Group::findAllCourseIds($group_id);
            if (!empty($courseIds)) {
                self::grant(Role::USER, $user_id, $courseIds, 'courses');
                $courseUnitIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $courseIds))));
                if (!empty($courseUnitIds)) {
                    self::grant(Role::USER, $user_id, $courseUnitIds, 'exams');
                    self::grant(Role::USER, $user_id, $courseUnitIds, 'course_units');
                }
            }

            $lessons = Course::findAllLessons(Group::findAllCourseIds($group_id));
            if (!empty($lessons)) {
                self::grant(Role::USER, $user_id, $lessons, 'lessons');
                self::grant(Role::USER, $user_id, $group_id, 'groups');
                $courseScheduleIds = self::getArrayOfFieldValues(CourseSchedule::find('all', array('conditions' => array('lesson_id IN (?)', $lessons))));
                if (!empty($courseScheduleIds)) {
                    self::grant(Role::USER, $user_id, $courseScheduleIds, 'course_schedule');
                }
            }
        }
    }
}
