<?php

require_once 'RestController.php';

class CourseUnitsController extends RestController
{
    protected $_modelName = 'CourseUnit';

    public function indexAction()
    {
        $courseID = $this->_getParam('course_id');
        if ($courseID != null) {
            $courseUnits = CourseUnit::connection()->query(
            "SELECT course_units.*,
            CAST (COUNT(lessons.id) AS INTEGER) AS planned,
            CAST(course_units.hour_amount - CAST (COUNT(lessons.id) AS INTEGER) AS INTEGER) AS remaining_hours,
            first_name || ' ' || last_name || ' (' || username || ')' AS trainer_name
                FROM course_units
                LEFT JOIN lessons ON course_unit_id = course_units.id
                LEFT JOIN users ON users.id = course_units.user_id
                WHERE course_id = " . $courseID . "
                GROUP BY course_units.id, name, hour_amount, course_id, course_units.user_id, first_name, last_name, username
                ORDER BY course_units.id ASC"
            )->fetchAll(PDO::FETCH_OBJ);
            $this->setRestResponseAndExit($courseUnits, self::HTTP_OK);
        }
        parent::indexAction();
    }
}

