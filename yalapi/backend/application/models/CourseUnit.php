<?php

class CourseUnit extends AclModel
{
    static $table_name = 'course_units';

    static $belongs_to = array(
        array('course'),
        array('user')
    );

    static $has_many = array(
        array('lessons', 'order' => 'start_date ASC'),
        array('exams', 'order' => 'created_date ASC'),
        array('exam_grades', 'through' => 'exams'),
    );

    static $validates_presence_of = array(
        array('name'),
        array('hour_amount'),
        array('user_id'),
        array('course_id')
    );

    static $before_save = array(
        'check_coach_role_id',
        'check_hour_amount'
    );

    public function check_coach_role_id()
    {
        if ($this->user->role_id != Role::COACH && $this->user->role_id != Role::ADMIN) {
            throw new ActiveRecord\DatabaseException('coach must have admin or coach role');
        }
    }

    public function check_hour_amount()
    {
        if ($this->id && count($this->lessons) > $this->hour_amount) {
            throw new ActiveRecord\DatabaseException('Hour amount cannot be less than the number of already planned lessons');
        }
    }
    
    
    public function findAllLessons($id)
    {
        if (!is_array($id)) $id=array($id);
        return self::getArrayOfFieldValues(Lesson::find('all', array('conditions' => array('course_unit_id IN (?)', $id))) );
    }
    
    

    public function AfterCreateAcl()
    {
        parent::AfterCreateAcl();
        if ($this->course)
            Project::GrantRevokeRightsToLeaders($this->course->project_id);
    }        
    
    
    static $after_save = 'RunAcl';


    public function RunAcl()
    {
        if ($this->field_has_changed('user_id'))
        {
            list($old,$new)=$this->get_field_change('user_id');
            
            if ($old)
            {
                //self::revoke(Role::COACH,$old,$this->course_id,'courses');
                //self::revoke(Role::COACH,$old,$this->id);
            }
            if ($new)
            {
                self::grant(Role::COACH,$new,$this->course_id,'courses');
                self::grant(Role::COACH,$new,$this->course->training_center_id,'training_centers');
                self::grant(Role::COACH,$new,$this->id);                
            }
            
            $lessonsIds=$this->findAllLessons($this->id);
            
            if (!empty($lessonsIds)) {
                $courseScheduleIds=CourseSchedule::findIdsOnLessonIds($lessonsIds);
                if ($old) {
                    self::revoke(Role::COACH,$old,$lessonsIds,'lessons');
                    self::revoke(Role::COACH,$old,$courseScheduleIds,'course_schedule');
                }
                if ($new) {
                    self::grant(Role::COACH,$new,$lessonsIds,'lessons');
                    self::grant(Role::COACH,$new,$courseScheduleIds,'course_schedule');
                }
            }
        }
    }
}
