<?php

require_once 'RestController.php';

class GroupGradesController extends RestController
{
    public function getAction()
    {
        try {
            $id = $this->_getParam('id');
            $course_unit = CourseUnit::find($id);

            $group = $course_unit->course->group;
            
            $users  = array();
            $exams  = array();
            $grades = array();
            $order  = array();

            foreach ($group->users as $user) {
                $users[$user->id] = sprintf('%s %s (%s)', $user->first_name, $user->last_name, $user->username);
            }

            foreach ($course_unit->exams as $exam)
            {
                $order[] = $exam->id;
                $exams[$exam->id] = $exam->to_array();
                $exam_grades = ExamGrade::find_all_by_exam_id($exam->id) ?: array();
                foreach ($exam_grades as $grade) {
                    $grades[$grade->user_id][$exam->id] = $grade->grade;
                }
            }
            
            $data = array(
                'group'  => $group->to_array(),
                'users'  => $users,
                'exams'  => $exams  ?: null,
                'grades' => $grades ?: null,
                'order'  => $exams  ? $order : null,
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
            $row = ExamGrade::find_by_exam_id_and_user_id($postData['exam_id'], $postData['user_id']);
            if ($row) {
                $row->grade = $postData['grade'];
            } else {
                $row = ExamGrade::create($postData);
            }
            if ($row->is_valid()) {
                $row->save();
                $this->setRestResponseAndExit(null, self::HTTP_OK);
            } else {
                $this->setRestResponseAndExit($row->errors->get_raw_errors(), self::HTTP_NOT_ACCEPTABLE);
            }
        } catch (Exception $e) {
            $this->setRestResponseAndExit($e->getMessage(), self::HTTP_NOT_FOUND);
        }
    }
}

