<?php

require_once 'RestController.php';

class GroupCoursesController extends RestController
{
    public function getAction()
    {
        try {
            $group_id = $this->_getParam('id');

            $group = Group::find($group_id);

            $courses = array();
            foreach (Course::find('all', array(
                'select'     => 'DISTINCT id, name, code',
                'from'       => 'courses',
                'conditions' => array('group_id = ?', $group_id)
            )) as $course) {
                $row = new stdClass;
                $row->id    = $course->id;
                $row->label = $course->name . ' (' . $course->code . ')';
                $courses[]  = $row;
            }
            $this->setRestResponseAndExit($courses, self::HTTP_OK);
        } catch (ActiveRecord\ActiveRecordException $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }
}

