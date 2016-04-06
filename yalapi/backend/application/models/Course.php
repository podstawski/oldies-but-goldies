<?php

class Course extends AclModel
{
    //CYF: should be same as in migration
    static $_hashSalt = 'sxGFuk5PMaOZjYZV3hy3y/n6Ul3bRc+Q';

    static $use_view = true;    
    
    static $has_many = array(
        array('units', 'class_name' => 'CourseUnit')
    );

    static $belongs_to = array(
        array('project'),
        array('training_center'),
        array('group')
    );

    static $validates_presence_of = array(
        array('code'),
        array('name'),
        array('training_center_id'),
        array('price'),
        array('project_id'),
    );

    public static function findAllOnProject($project_id)
    {
        return self::getArrayOfFieldValues(self::find('all', array('conditions' => array('project_id = ?', $project_id))));
    }

    public static function findAllObjects($id,$obj)
    {
        if (!is_array($id)) {
            if (!$id) return array();
            $id=array($id);
        }
        if (!count($id)) return array();

        $courseUnitsIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $id))));
        if (count($courseUnitsIds))
        {
            $res=self::getArrayOfFieldValues($obj::find('all', array('conditions' => array('course_unit_id IN (?)', $courseUnitsIds))) );
            return $res;
        }
        return array();        
    }




    static public function findAllLessons($id)
    {
        return self::findAllObjects($id,'Lesson');
    }

    static $after_save = 'RunAcl';

    public function RunAcl()
    {
        
        if ($this->field_has_changed('group_id'))
        {
            list ($old,$new) = $this->get_field_change('group_id');

            $lessonsIds = self::findAllLessons($this->id);
            $courseUnitIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $this->id))));

            $old_users = $old ? GroupUser::user_ids($old) : array();
            $new_users = $new ? GroupUser::user_ids($new) : array();
            
            foreach ($old_users AS $ou) {
                if (in_array($ou,$new_users)) {
                    unset($old_users[array_search($ou,$old_users)]);
                    unset($old_users[array_search($ou,$old_users)]);
                }
            }            

            $this->revoke(Role::USER,$old_users,$this->id);
            $this->grant(Role::USER,$new_users,$this->id);
            
            if (!empty($courseUnitIds)) {
                $this->revoke(Role::USER,$old_users,$courseUnitIds,'course_units');
                $this->grant(Role::USER,$new_users,$courseUnitIds,'course_units');

                $examsIds = self::getArrayOfFieldValues(Exam::find('all', array('conditions' => array('course_unit_id IN (?)', $courseUnitIds))));
                if (!empty($examsIds)) {
                    $this->revoke(Role::USER,$old_users,$examsIds,'exams');
                    $this->grant(Role::USER,$new_users,$examsIds,'exams');
                }
            }
            
            if (!empty($lessonsIds)) {
                $this->revoke(Role::USER,$old_users,$lessonsIds,'lessons');
                $this->grant(Role::USER,$new_users,$lessonsIds,'lessons');

                $courseScheduleIds = self::getArrayOfFieldValues(CourseSchedule::find('all', array('conditions' => array('lesson_id IN (?)', $lessonsIds))));
                if (!empty($courseScheduleIds)) {
                    $this->revoke(Role::USER,$old_users,$courseScheduleIds,'course_schedule');
                    $this->grant(Role::USER,$new_users,$courseScheduleIds,'course_schedule');
                }
            }
        }
        
        if ($this->field_has_changed('project_id'))
        {
            list ($old,$new) = $this->get_field_change('project_id');
            
            if ($old) Project::GrantRevokeRightsToLeaders($old);
            Project::GrantRevokeRightsToLeaders($new);
        }
        
        if ($this->field_has_changed('training_center_id'))
        {
            Project::GrantRevokeRightsToLeaders($this->project_id);
        }        
    }

    public function subscribe(array $formData)
    {
        if (array_key_exists('id', $formData)) {
            $userData = User::find($formData['id']);
            if ($userData) {
                $userData = $userData->to_array();
            } else {
                throw new Exception('could not find user');
            }
        } elseif (array_key_exists('username', $formData)) {
            $userData = User::find_by_username($formData['username']);
            if ($userData && $userData->plain_password == $formData['password']) {
                $userData = $userData->to_array();
            } else {
                throw new Exception('username and password not recognized');
            }
        } else {
            $userData = array_intersect_key($formData, User::table()->columns);

            $i = 0;
            $username = strtolower(substr($userData['first_name'], 0, 3))
                      . strtolower(substr($userData['last_name'],  0, 3));

            while (1) {
                $checkUsername = $username . $i++;
                if (User::find_by_username($checkUsername) == null) {
                    break;
                }
            }
            
            $userData['username'] = $checkUsername;
            $userData = User::createUser($userData)->to_array();
            $userData['new_user'] = true;
        }

        if ($this->group_id == null) {
            $row = new Group();
            $row->name = $this->name;
            $row->save();

            $this->group_id = $row->id;
            $this->save();
        }

        $row = new GroupUser();
        $row->group_id = $this->group_id;
        $row->user_id  = $userData['id'];
        $row->save();

        Project::updateUserExtraFields($this->project, $userData['id'], $formData);

        return $userData;
    }
}
