<?php

class Project extends AclModel
{
    const DATE_FORMAT = 'Y-m-d';

    const STATUS_CURRENT = 1;
    const STATUS_PLANNED = 2;
    const STATUS_ARCHIVE = 3;
    
    static $use_view = true;

    static $has_many = array(
        array('courses'),
        array('project_leaders', 'class_name' => 'ProjectLeaders'),
        array('leaders', 'through' => 'project_leaders', 'class_name' => 'User', 'select' => 'users.id, username, first_name, last_name, email, role_id')
    );


    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true)
    {
        /* RB this is needed, because on callbacks before_save or before_create this values are null. Why? In
           Model::__constructor, model is casting values to database field (in this case, it's date), so later
           (at the callback time) these values are zeros and we cannot do anything with them */
        if ($new_record) {
            $attributes = $this->_setDates($attributes, $new_record);
        }
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
    }

    static $validates_presence_of = array(
        array('code'),
        array('name'),
        array('start_date'),
        array('end_date'),
        array('status')
    );

    public function set_attributes(array $attributes)
    {
        $attributes = $this->_setDates($attributes);
        parent::set_attributes($attributes);
    }

    /**
     * Sets dates in valid format
     * @param $attributes
     * @return array
     */
    private function _setDates($attributes)
    {
        foreach (array('start_date', 'end_date') as $field) {
            if (array_key_exists($field, $attributes)) {
                $attributes[$field] = date(self::DATE_FORMAT, strtotime($attributes[$field]));
            }
        }
        return $attributes;
    }

    static $after_save = array('check_status', 'check_is_default');

    public function check_status()
    {
        if ($this->field_has_changed('status') && $this->read_attribute('status') == self::STATUS_ARCHIVE) {
            Course::update_all(array(
                'set' => array(
                    'status' => self::STATUS_ARCHIVE
                ),
                'conditions' => array(
                    'project_id' => $this->id
                )
            ));
        }
    }

    public function check_is_default()
    {
        if ($this->field_has_changed('is_default') && $this->read_attribute('is_default') == 1) {
            self::update_all(array(
                'set' => array(
                    'is_default' => 0
                ),
                'conditions' => array(
                    'id <> ?', $this->read_attribute('id')
                )
            ));
        }
    }

    static $before_save = array('check_extra_fields');

    public function check_extra_fields()
    {

    }

    /**
     * @param string $delimiter
     * @param string $spec
     * @return array
     */
    private static function _getExtraFieldSpec($spec, $delimiter)
    {
        if (strpos($spec, $delimiter) === false)
            return false;

        $spec = array_map('trim', explode($delimiter, $spec));
        return array_combine($spec, $spec);
    }

    /**
     * @return Zend_Form_Element[]
     */
    public static function getExtraFields($extra_fields, $onlyNames = false)
    {
        $ret = array();

        if ($extra_fields) {
            foreach (array_map('trim', explode(PHP_EOL, $extra_fields)) as $field) {
                $element = null;
                list ($field, $required, $multi) = @explode(':', $field);

                if (empty($multi)) {
                    $element = new Zend_Form_Element_Text($field);
                } else if ($spec = self::_getExtraFieldSpec($multi, ',')) {
                    $element = new Zend_Form_Element_Select($field);
                    $element->setMultiOptions($spec);
                } else if ($spec = self::_getExtraFieldSpec($multi, '#')) {
                    $element = new Zend_Form_Element_MultiCheckbox($field);
                    $element->setMultiOptions($spec);
                } else if ($spec = self::_getExtraFieldSpec($multi, '|')) {
                    $element = new Zend_Form_Element_Radio($field);
                    $element->setMultiOptions($spec);
                }

                if ($element) {
                    $element->setLabel($field);
                    $element->setRequired($required);
                    $ret[$field] = $element;
                }
            }

            if ($onlyNames)
                $ret = array_keys($ret);
        }

        return $ret;
    }

    /**
     * @param Project|int $project
     * @param int $userID
     * @param array $formData
     */
    public static function updateUserExtraFields($project, $userID, array $formData)
    {
        if (is_int($project))
            $project = Project::find($project);

        if ($project == null || empty($project->extra_fields))
            return;

        $extra_fields = self::getExtraFields($project->extra_fields);
        $extra_fields = array_intersect_key($formData, $extra_fields);
        if (empty($extra_fields))
            return;

        /**
         * @var String $field_name
         * @var Zend_Form_Element $element
         * @var UserProfileExtra $row
         */
        foreach ($extra_fields as $field_name => $element) {
            if (array_key_exists($field_name, $formData)) {
                $value =& $formData[$field_name];
                $row = UserProfileExtra::find_or_create_by_user_id_and_field_name($userID, $field_name);
                if (empty($value))
                    $row->field_value = null;
                else if (is_array($value))
                    $row->field_value = implode(', ', $value);
                else
                    $row->field_value = $value;
                $row->save();
            }
        }
    }

    //static $after_create = 'AfterCreateAcl';
    
    public function AfterCreateAcl()
    {
        parent::AfterCreateAcl();

        if (Yala_User::getRoleId()==Role::PROJECT_LEADER)
        {
            $project_leaders=new ProjectLeaders();
            $project_leaders->project_id=$this->id;
            $project_leaders->user_id=Yala_User::getUid();
    
            $project_leaders->save();
        }
    }
    
    public static function findAllTrainingCenters($project_id)
    {
        $tc=self::getArrayOfFieldValues(Course::find('all', array('conditions' => array('project_id IN (?)', $project_id))),'training_center_id');
        
        return array_unique($tc);
    }    
    
    
    public static function GrantRevokeRightsToLeaders($project_id,$revoke_leaders=null)
    {
        static $training_centers_users;
        
        $usersToBeRevoked=ProjectLeaders::findLeaderIds();
        if (is_array($revoke_leaders)) $usersToBeRevoked=array_merge($usersToBeRevoked,$revoke_leaders);
        elseif ($revoke_leaders)
        {
            $usersToBeRevoked[]=$revoke_leaders;
            $revoke_leaders=array($revoke_leaders);
        }
        $usersToBeRevoked=array_unique($usersToBeRevoked);
        
        $usersToBeGranted=ProjectLeaders::findLeaderIds($project_id);

        foreach($usersToBeGranted AS $ig=>$user)
        {
            $ir=array_search($user,$usersToBeRevoked);
            
            if (is_array($revoke_leaders) && in_array($user,$revoke_leaders))
            {
                if ($ir===false) $usersToBeRevoked[]=$user;
                unset($usersToBeGranted[$ig]);
            }
            else
            {
                if ($ir!==false) unset($usersToBeRevoked[$ir]);
            }
        }
        

        //projekt
        self::_debug("PROJECT[$project_id]: +".implode(',',$usersToBeGranted).'; -'.implode(',',$usersToBeRevoked));
        self::revoke(Role::PROJECT_LEADER,$usersToBeRevoked,$project_id);
        self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$project_id);
        
        
        //kursy
        $courses=Course::findAllOnProject($project_id);
        self::_debug("COURSES: ".implode(',',$courses));
        self::revoke(Role::PROJECT_LEADER,$usersToBeRevoked,$courses,'courses');
        self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$courses,'courses');
        
        if (count($courses))
        {
            $courseUnitsIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $courses))));
            self::_debug("COURSE_UNITS: ".implode(',',$courseUnitsIds));
            self::revoke(Role::PROJECT_LEADER,$usersToBeRevoked,$courseUnitsIds,'course_units');
            self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$courseUnitsIds,'course_units');
            
            $lessons=Course::findAllLessons($courses);
            self::_debug("LESSONS: ".implode(',',$lessons));
            self::revoke(Role::PROJECT_LEADER,$usersToBeRevoked,$lessons,'lessons');
            self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$lessons,'lessons');            
        
            $training_centers=self::findAllTrainingCenters($project_id);
            self::_debug("TRAINING_CENTERS: ".implode(',',$training_centers));
            self::revoke(Role::PROJECT_LEADER,array_diff($usersToBeRevoked,TrainingCenter::projectLeaders($training_centers)),$training_centers,'training_centers');
            self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$training_centers,'training_centers');
            
            $rooms=TrainingCenter::findAllRooms($training_centers);
            self::_debug("ROOMS: ".implode(',',$rooms));
            self::revoke(Role::PROJECT_LEADER,$usersToBeRevoked,$rooms,'rooms');
            self::grant(Role::PROJECT_LEADER,$usersToBeGranted,$rooms,'rooms');

        }
        
    }

    /**
     * @return Project
     */
    public static function findDefault()
    {
        return self::find('first', array(
            'conditions' => array(
                'is_default' => 1
            )
        ));
    }

    /*
        todo:
        zmiany praw przy:
          - dodaniu e-dziennika
    */
}
