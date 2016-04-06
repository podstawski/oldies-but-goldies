<?php

require 'RestController.php';

class UserInfoController extends RestController
{
    public function getAction()
    {
        $id      = intval($this->_getParam('id'));
        $status  = intval($this->_getParam('status', 1));
        $userRow = User::find_by_id($id);

        $stmt = User::connection()->connection->query('SELECT courses.id course_id, courses.name course_name, courses.code course_code, courses.start_date course_start_date, courses.end_date course_end_date,
                course_units.id unit_id, course_units.name unit_name, course_units.hour_amount unit_hour_amount,
                groups.id group_id, groups.name group_name,
                projects.id project_id, projects.name project_name, projects.code project_code, projects.created_date project_created_date,
                lessons.id lesson_id, lessons.start_date lesson_start_date, lessons.end_date lesson_end_date,
                tc.id tc_id, tc.name tc_name, street tc_street, zip_code tc_zip_code, city tc_city, phone_number tc_phone_number,
                rooms.id room_id, rooms.name lesson_room,
                users.id trainer_id, first_name trainer_first_name, last_name trainer_last_name, email trainer_email,
                course_schedule.subject lesson_subject,
                lesson_presence.id lesson_present
            FROM courses
            INNER JOIN course_units ON course_units.course_id = courses.id
            INNER JOIN groups ON groups.id = courses.group_id
            INNER JOIN group_users ON group_users.group_id = courses.group_id
            INNER JOIN projects ON projects.id = courses.project_id
            LEFT JOIN lessons ON lessons.course_unit_id = course_units.id
            LEFT JOIN training_centers tc ON tc.id = courses.training_center_id
            LEFT JOIN rooms ON rooms.id = lessons.room_id
            LEFT JOIN users ON users.id = COALESCE(lessons.user_id, course_units.user_id)
            LEFT JOIN course_schedule ON course_schedule.lesson_id = lessons.id
            LEFT JOIN lesson_presence ON lesson_presence.lesson_id = lessons.id AND lesson_presence.user_id = group_users.user_id
            WHERE group_users.user_id = ' . $id . '
            AND courses.status = ' . $status . '
            ORDER BY projects.created_date ASC, lessons.start_date ASC');
//        echo "<pre>";print_r($stmt->fetchAll(PDO::FETCH_OBJ));die();
        $data = array();
        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row)
        {
            if (!array_key_exists($row->project_id, $data)) {
                $data[$row->project_id] = $this->extractData($row, 'project');
                $data[$row->project_id]->courses = array();
            }

            $courses = & $data[$row->project_id]->courses;
            if (!array_key_exists($row->course_id, $courses)) {
                $courses[$row->course_id] = $this->extractData($row, 'course');
                $courses[$row->course_id]->training_center = $this->extractData($row, 'tc');
                $courses[$row->course_id]->group = $this->extractData($row, 'group');
                $courses[$row->course_id]->units = array();
            }

            $units = & $courses[$row->course_id]->units;
            if (!array_key_exists($row->unit_id, $units)) {
                $units[$row->unit_id] = $this->extractData($row, 'unit');
                $units[$row->unit_id]->lessons = array();
            }

            $lessons = & $units[$row->unit_id]->lessons;
            if ($row->lesson_id && !array_key_exists($row->lesson_id, $lessons)) {
                $lessons[$row->lesson_id] = $this->extractData($row, 'lesson');
                $lessons[$row->lesson_id]->trainer = $this->extractData($row, 'trainer');
            }
        }
//        echo "<pre>";print_r($data);die();
        $this->setRestResponseAndExit($data, self::HTTP_OK);
    }

    protected function extractData($row, $prefix)
    {
        $data = array();
        foreach ((array) $row as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $data[substr($key, strlen($prefix) + 1)] = $value;
            }
        }
        return (object) $data;
    }
}