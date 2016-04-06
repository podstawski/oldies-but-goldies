<?php

require_once 'RestController.php';

class GroupPresenceController extends RestController
{
    public function init()
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = 'd-m-Y H:i:s';
        parent::init();
    }
    
    public function getAction()
    {
        try {
            $id = $this->_getParam('id');
            $course_unit = CourseUnit::find($id);

            $group = $course_unit->course->group;

            $users    = array();
            $lessons  = array();
            $presence = array();
            $order    = array();

            foreach ($group->users as $user) {
                $users[$user->id] = sprintf('%s %s (%s)', $user->first_name, $user->last_name, $user->username);
            }

            foreach ($course_unit->lessons as $lesson) {
                $order[] = $lesson->id;
                $lessons[$lesson->id] = $lesson->to_array();
                foreach ($lesson->presence as $row) {
                    $presence[$row->user_id][$row->lesson_id] = 1;
                }
            }

            $data = array(
                'group'    => $group->to_array(),
                'users'    => $users,
                'lessons'  => $lessons  ?: null,
                'presence' => $presence ?: null,
                'order'    => $lessons  ? $order : null,
            );

            $this->setRestResponseAndExit($data, self::HTTP_OK);
        } catch (ActiveRecord\ActiveRecordException $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }

    public function postAction()
    {
        $postData = $this->_getRequestData('POST');

        try {
            $present = (bool) $postData['present'];
            foreach (explode(',', $postData['user_id']) as $user_id)
            {
                $row = LessonPresence::find('first', array(
                    'conditions' => array('lesson_id = ? AND user_id = ?', $postData['lesson_id'], $user_id)
                ));
                if ($present && !$row) {
                    LessonPresence::create(array(
                        'user_id'   => $user_id,
                        'lesson_id' => $postData['lesson_id']
                    ));
                } elseif (!$present && $row) {
                    $row->delete();
                }
            }
            $this->setRestResponseAndExit(null, self::HTTP_OK);
        } catch (ActiveRecord\ActiveRecordException $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }
}

