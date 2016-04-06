<?php

require_once 'RestController.php';

class CourseScheduleController extends RestController
{
    protected $_modelName = 'CourseSchedule';

    public function init()
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = 'd-m-Y H:i';
        parent::init();
    }

    protected function _getPagerOptionsForModel()
    {
        $options   = parent::_getPagerOptionsForModel();
        $tableName = $this->_getTableNameFromModelClass($this->_modelName);
        
        if (!array_key_exists('total_records', $options)) {
            $options['select'] = "lessons.start_date AS lesson_date, lessons.id AS lesson_id,
                                  course_units.id AS course_unit_id, course_units.name AS unit_name,
                                  courses.id AS course_id, courses.name AS course_name,
                                  $tableName.subject, $tableName.id AS schedule_id,
                                  users.id AS user_id, username, first_name, last_name";
        }
        $options['from']   = "lessons";
        $options['joins']  = "INNER JOIN course_units ON course_units.id = lessons.course_unit_id
                              INNER JOIN courses ON courses.id = course_units.course_id
                              LEFT JOIN $tableName ON $tableName.lesson_id = lessons.id
                              LEFT JOIN users ON users.id = COALESCE(lessons.user_id, course_units.user_id)";

        return $options;
    }
}