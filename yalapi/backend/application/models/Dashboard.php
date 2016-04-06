<?php

class Dashboard
{
    protected static function getConnection()
    {
        return ActiveRecord\Model::connection()->connection;
    }
    /**
     * @static
     * @param int $day
     * @param int $month
     * @param int $year
     * @return array
     */
    public static function getNewEventsData($day, $month, $year)
    {
        $data = array(
            'lessons'  => array(),
            'courses'  => array(),
            'units'    => array(),
            'tcs'      => array(),
            'rooms'    => array(),
            'trainers' => array(),
            'groups'   => array()
        );

        $stmt = self::getConnection()->prepare('SELECT
                lessons.id lesson_id, lessons.start_date lesson_start_date, lessons.end_date lesson_end_date, lessons.room_id lesson_room_id, COALESCE(lessons.user_id, course_units.user_id) lesson_trainer_id, lessons.course_unit_id lesson_unit_id,
                course_units.id unit_id, course_units.name unit_name, course_units.hour_amount unit_hour_amount, course_units.course_id unit_course_id,
                courses.id course_id, courses.name course_name, courses.code course_code, courses.start_date course_start_date, courses.end_date course_end_date, courses.group_id course_group_id, courses.training_center_id course_tc_id,
                groups.id group_id, groups.name group_name,
                users.id trainer_id, users.first_name trainer_first_name, users.last_name trainer_last_name, users.email trainer_email,
                rooms.id room_id, rooms.name room_name, rooms.training_center_id room_tc_id,
                tc.id tc_id, tc.name tc_name, street tc_street, zip_code tc_zip_code, city tc_city, phone_number tc_phone_number
            FROM lessons
            INNER JOIN course_units ON course_units.id = lessons.course_unit_id
            INNER JOIN courses ON courses.id = course_units.course_id
            INNER JOIN users ON users.id = COALESCE(lessons.user_id, course_units.user_id)
            INNER JOIN rooms ON rooms.id = lessons.room_id
            INNER JOIN training_centers tc ON tc.id = courses.training_center_id
            LEFT JOIN groups ON groups.id = courses.group_id
            LEFT JOIN group_users ON group_users.group_id = groups.id
            WHERE ? IN (group_users.user_id, lessons.user_id, course_units.user_id)
            AND CAST (lessons.start_date AS DATE) >= CAST(? AS DATE)
            ORDER BY lessons.start_date ASC
            LIMIT 20');

//        AND EXTRACT(year FROM lessons.start_date) = ?
//        AND EXTRACT(month FROM lessons.start_date) = ?
//        AND EXTRACT(day FROM lessons.start_date) >= ?
//        $stmt->execute(array(Yala_User::getUid(), $year, $month, $day));

        $stmt->execute(array(Yala_User::getUid(), date('Y-m-d', mktime(0, 0, 0, $month, $day, $year))));
        $keys = array_keys($data);
        foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $row)
        {
            foreach ($keys as $key) {
                $prefix = substr($key, 0, -1);
                $d = self::extractData($row, $prefix);
                $data[$key][$d->id] = $d;
            }
        }

        return $data;
    }

    /**
     * @static
     * @param mixed $row
     * @param string $prefix
     * @return object
     */
    protected static function extractData($row, $prefix)
    {
        $data = array();
        foreach ((array)$row as $key => $value) {
            if (substr($key, 0, strlen($prefix)) == $prefix) {
                $data[substr($key, strlen($prefix) + 1)] = $value;
            }
        }
        return (object)$data;
    }

    /**
     * @static
     * @return int
     */
    public static function getNewMessagesCount()
    {
        return intval(MessageUser::count(array(
            'conditions' => array('user_id = ? AND read_date IS NULL AND folder = ?', Yala_User::getUid(), MessageUser::FOLDER_INBOX),
        )));
    }

    /**
     * @static
     * @return array
     */
    public static function getNewSurveysCount()
    {
        $stmt = self::getConnection()->prepare('SELECT surveys.type, COUNT(*)
            FROM survey_users
            INNER JOIN surveys ON surveys.id = survey_users.survey_id
            WHERE survey_users.user_id = ?
            AND survey_users.filled = 0
            GROUP BY surveys.type');

        $data = array();
        if ($stmt->execute(array(Yala_User::getUid()))) {
            foreach ($stmt->fetchAll(PDO::FETCH_OBJ) as $survey) {
                $data[$survey->type] = $survey->count;
            }
        }
        return $data;
    }
}
