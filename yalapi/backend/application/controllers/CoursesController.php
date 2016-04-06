<?php

require_once 'RestController.php';

class CoursesController extends RestController
{
    public function init()
    {
        parent::init();
    }

    public function postAction()
    {
        $postData = $this->_getRequestData('POST');
        
        if (!isset($postData['course_units']) || !$postData['course_units']) {
            $this->setRestResponseAndExit('Course units are missing', self::HTTP_NOT_ACCEPTABLE);
        }

        $courseUnits = json_decode($postData["course_units"]);
        unset($postData["course_units"]);

        $connection = ActiveRecord\Model::connection();
        $connection->transaction();

        try {
            $course = new Course((array) $postData);
            if (!$course->is_valid()) {
                $this->setRestResponseAndExit($course->errors->get_raw_errors() , self::HTTP_NOT_ACCEPTABLE);
            }
            $course->save();
            $course->hash = md5($course->id . Course::$_hashSalt);
            $course->save();

            foreach ($courseUnits as $postunit) {
                $postunit->course_id = $course->id;
                $courseUnitRow = new CourseUnit((array) $postunit);
                if (!$courseUnitRow->is_valid()) {
                    $this->setRestResponseAndExit($courseUnitRow->errors->get_raw_errors(), self::HTTP_NOT_ACCEPTABLE);
                }
                $courseUnitRow->save();
            }
            $connection->commit();
            $this->setRestResponseAndExit($course->to_array(), self::HTTP_CREATED);
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    public function putAction()
    {
        $postData = $this->_getRequestData('PUT');

        if (!isset($postData['course_units']) || !$postData['course_units']) {
            $this->setRestResponseAndExit('Course units are missing', self::HTTP_NOT_ACCEPTABLE);
        }

        $courseUnits = json_decode($postData["course_units"]);
        unset($postData["course_units"], $postData["_method"]);

        $connection = ActiveRecord\Model::connection();
        $connection->transaction();
        
        try {
            $course = $this->_getById(true);
            $course->set_attributes($postData);
            $course->save();

            $newUnits = array();
            
            foreach ($courseUnits as $postunit)
            {
                if ($postunit->id) {
                    $row = CourseUnit::find($postunit->id);
                    if ($row->course_id != $course->id) {
                        continue;
                    }
                } else {
                    $row = new CourseUnit();
                }
                $row->set_attributes((array) $postunit);
                $row->course_id = $course->id;
                $row->save();

                $newUnits[$row->id] = $row;
            }

            foreach ($course->units as $dbunit)
            {
                if (!in_array($dbunit->id, array_keys($newUnits))) {
                    $dbunit->delete();
                }
            }

            $connection->commit();
            $this->setRestResponseAndExit(null, self::HTTP_OK);
        } catch (Exception $e) {
            $connection->rollback();
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_ACCEPTABLE);
        }
    }

    protected function _getPagerOptionsForModel()
    {
        $options   = parent::_getPagerOptionsForModel();
        $tableName = $options['from'] = $this->_getTableNameFromModelClass($this->_modelName);

        if (!array_key_exists('total_records', $options)) {
            $options['joins']  = "LEFT JOIN projects p ON p.id = project_id
                                  LEFT JOIN training_centers tc ON tc.id = training_center_id
                                  LEFT JOIN groups g ON $tableName.group_id = g.id";

            $options['select'] = "$tableName.*, p.name AS project_name, p.code AS project_code, tc.name AS training_center_name,
                                  tc.code AS training_center_code, tc.street, tc.zip_code, tc.city, g.name as group_name";
        }

        return $options;
    }
}

