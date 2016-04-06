<?php

class Lesson extends AclModel
{
    public $lessonErrors = array('is_creating_lesson_backward' => false, 'is_end_date_before_start' => false);

    static $before_update = array('incrementSequence');

    private $response = null;
    private $colors = array('#FFFFFF', '#000000');
    private $colorsUsed = array();

    private $_recurring = null;

    static $use_view = true;
    static $useCycleId = false;

    static $belongs_to = array(
        array('room'),
        array('course_unit'),
        array('user')
    );

    static $has_many = array(
        array('presence', 'class_name' => 'LessonPresence')
    );

    static $after_save = array(
        'update_parent_course_start_end_dates',
        'RunAcl'
    );

    static $after_destroy = array(
        'update_parent_course_start_end_dates',
    );

    public function changeUseView($value)
    {
        self::$use_view = $value;
    }

    public function incrementSequence()
    {
        $this->sequence += 1;
    }

    public function start_date()
    {
        return $this->read_attribute('start_date')->format('Y-m-d H:i');
    }

    public function end_date()
    {
        return $this->read_attribute('end_date')->format('Y-m-d H:i');
    }

    public function update_parent_course_start_end_dates($cycle_id = -1)
    {
        //TODO move to controller, when multiupdate will be ready
        //RB little unefficient, but deals with all cases

        if (($this->cycle_id > 0 && $cycle_id == -1) || !$this->start_date) return;

        $course = $this->course_unit->course;

        $courseUnitsIds = self::getArrayOfFieldValues(CourseUnit::find('all', array('conditions' => array('course_id IN (?)', $course->id))));
        if (!empty($courseUnitsIds)) {
            $minmax = self::first(array(
                'select' => 'min(start_date) AS start_date, max(end_date) AS end_date',
                'conditions' => array('course_unit_id IN (?)', $courseUnitsIds)
            ));

            $course->start_date = $minmax->start_date;
            $course->end_date   = $minmax->end_date;

        } else {
            $course->start_date = $course->end_date = null;
        }
        $course->save();
    }

    public function normalizeDate($date)
    {
        $date = str_replace("T", " ", $date);
        $date = str_replace(":00.000Z", "", $date);

        return $date;
    }

    public function generateRecurringTypeToken($recurring)
    {
        /**
         * Typical structure:
         *  [repeatHow] => 1
         *  [repeatIn] => Array (
         *    [0] => 1
         *    [1] => 2
         *    [2] => 4
         *  )
         *  [repeatHowMuch] =>
         *  [repeatFinishAfterXTimes] =>
         *  [repeatFinishInDay] => 2011-11-22T23:00:00.000Z
         */

        /**
         *little description of return values
         * $returnValue = [ repeatHow # repeatIn - is not empty # repeatFinishAfterXTimes not null ]
         * So possible values means:
         * # 100 - repeat, but not in week period   # no specific days  # finish in given date
         * # 200 - repeat in week period            # no specifc days   # finish in given date
         * # 101 - repeat, but not in week period   # no specific days  # finish after x times
         * # 201 - repeat in week period            # no specific days  # finish after x times
         * # 110 - repeat, but not in week period   # in declared days  # finish in given date
         * # 210 - repeat in week period            # in declared days  # finish in given date
         * # 111 - repeat, but not in week period   # in declared days  # finish after x times
         * # 211 - repeat in week period            # in declared days  # finish after x times
         * # 0 - invalid recurring array structure, if at least it is array
         */

        if($recurring->repeatFinishAfterXTimes === '') { $recurring->repeatFinishAfterXTimes = null; }
        if($recurring->repeatFinishInDay === '') { $recurring->repeatFinishInDay = null; }

        if (
            !is_object($recurring) || $recurring->repeatHow === null || !is_array($recurring->repeatIn) ||
            (($recurring->repeatFinishAfterXTimes !== null )&& $recurring->repeatFinishInDay !== null)
        ) return 0;

        $returnValue = ($recurring->repeatHow === 4) ? 2 : 1;
        $returnValue .= (int)(count($recurring->repeatIn) > 0);
        $returnValue .= (int)($recurring->repeatFinishAfterXTimes !== null);

        return $returnValue;
    }

    public function getDayNumber($timestamp)
    {
    	return date('N', $timestamp);
    }

    public function isCorrectDate($post, $isEdit)
    {
        $dateNow = new DateTime();
        $dateNow->format($this->dateFormat);

        $dateStart = new DateTime($post['start_date']);
        $dateStart->format($this->dateFormat);

        $dateEnd = new DateTime($post['end_date']);
        $dateEnd->format($this->dateFormat);

        $this->lessonErrors['is_creating_lesson_backward'] = false;
        $this->lessonErrors['is_end_date_before_start'] = false;
        $this->lessonErrors['is_completed_lesson_edit'] = false;

        ($isEdit) ?
            $this->lessonErrors['is_completed_lesson_edit'] = ($dateStart < $dateNow) :

            //Can plan lessons backward
//       $this->lessonErrors['is_creating_lesson_backward']    = ( $dateStart < $dateNow );

            $this->lessonErrors['is_end_date_before_start'] = ($dateEnd < $dateStart);
        return ($this->lessonErrors['is_end_date_before_start'] || $this->lessonErrors['is_creating_lesson_backward']) ? false : true;
    }

    public function noCollisions($lesson, $rowId = 0)
    {
        unset($lesson['controller']);
        unset($lesson['action']);
        unset($lesson['module']);

        $query = '
        ((start_date <= ? AND end_date >= ?) OR
        (start_date <= ? AND end_date >= ?)) AND
        (room_id = ? OR course_unit_id = ? OR user_id = ?)';

        $query .= ($rowId > 0) ? ' AND (id <> ?)' : '';

        $conditions = array(
            $query,
            $lesson['start_date'], $lesson['start_date'],
            $lesson['end_date'], $lesson['end_date'],
            $lesson['room_id'], $lesson['course_unit_id'], $lesson['user_id']);

        ($rowId > 0) ? ($conditions[] = (int)$rowId) : false;

        $collisions = Lesson::find('all',
            array('conditions' => $conditions));

        if (count($collisions) == 0) {
            return true;
        }
        foreach ($collisions as &$collision)
        {
            $collision = $collision->to_array();
            if ($collision['room_id'] == $lesson['room_id']) {
                $this->addCollisionToList($collision, 'room');
            }

            if ($collision['user_id'] == $lesson['user_id']) {
                $this->addCollisionToList($collision, 'coach');
            }

            if ($collision['course_unit_id'] == $lesson['course_unit_id']) {
                $this->addCollisionToList($collision, 'course_unit');
            }
        }

        $this->lessonErrors['collisions'] = $this->collisions;
        return false;
    }

    public function createLessons($post, $recurring)
    {
        $this->_recurring = $recurring;
        $this->_recurring->repeatFinishInDay = $this->normalizeDate($recurring->repeatFinishInDay);

        $params = array();
        $params['method'] = $recurring->repeatHow;
        $params['dateStart'] = $post['start_date'];

        switch ($this->generateRecurringTypeToken($this->_recurring))
        {

            case '100':
            case '200':
                $params['dateEnd'] = $recurring->repeatFinishInDay;
                break;

            case '101':
            case '201':
                $params['howMuch'] = $recurring->repeatHowMuch;
                $params['howMany'] = $recurring->repeatFinishAfterXTimes;
                break;

            case '110':
            case '210':
                $params['dateEnd'] = $recurring->repeatFinishInDay;
                $params['dayNumbers'] = $recurring->repeatIn;
                break;

            case '111':
            case '211':
                $params['howMuch'] = $recurring->repeatHowMuch;
                $params['howMany'] = $recurring->repeatFinishAfterXTimes;
                $params['dayNumbers'] = $recurring->repeatIn;
                break;

            case 0:
            default:
                break;
        }

        $days = $this->getDaysFromPeriod($params);

        $noErrors = true;
        $cycleId = 0;

        $startHours = explode(' ', $post['start_date']);
        $startHours = $startHours[1];

        $endHours = explode(' ', $post['end_date']);
        $endHours = $endHours[1];


        foreach ($days as $key => $day)
        {
            $date = explode(' ', date($this->dateFormat, $day));
            $post['start_date'] = $date[0] . ' ' . $startHours;
            $post['end_date'] = $date[0] . ' ' . $endHours;

            $correctDate = $this->isCorrectDate($post, false);
            $noCollisons = ($correctDate) ? $this->noCollisions($post) : true;

            if ($noCollisons === true && $correctDate === true) {
                try {
                    if ($key === 0) {
                        $row = Lesson::create($post);
                        $row->cycle_id = $row->id;
                        $row->save();

                        $cycleId = $row->cycle_id;
                    }
                    else
                    {
                        $post['cycle_id'] = $cycleId;
                        $row = Lesson::create($post);
                    }
                }
                catch (Exception $e) {
                    echo $e->getMessage();
                }
            }
            else {
                $noErrors = false;
                break;
            }
        }

        if ($cycleId) {
            $row->RunAcl($cycleId);
            $row->update_parent_course_start_end_dates($cycleId);
        }

        return ($noErrors) ? true : $this->lessonErrors;
    }

    public function createLesson($post)
    {
        $correctDate = $this->isCorrectDate($post, false);
        $noCollisons = ($correctDate) ? $this->noCollisions($post) : true;

        if ($noCollisons === true && $correctDate === true) {
            $row = Lesson::create($post);
            if ($row->is_valid()) {
                return true;
            } else
            {
                return $row->errors->get_raw_errors();
            }
        }
        else
        {
            return $this->lessonErrors;
        }
    }

    private function addCollisionToList($collision, $type)
    {
        $arr = array('dateS' => $collision['start_date'], 'dateE' => $collision['end_date'], 'type' => $type);
        $this->collisions[] = $arr;
    }

    private $dateFormat = 'Y-m-d H:i';
    private $collisions = array();

    public function getDaysFromPeriod($params)
    {
    	$dateFormat = 'Y-m-d H:i';

    	if(!is_array($params)) {
    		return;
    	}

    	$counter = 0;
    	$days = array();

    	if(isset($params['dateStart'])) {
    		$dateStart = new DateTime($params['dateStart']);
    		$dateStart->format($dateFormat);
    	}
    	else {
    		$dateStart = null;
    	}

    	if(isset($params['dateEnd'])) {
    		$dateEnd = new DateTime($params['dateEnd']);
    		$dateEnd->format($dateFormat);
    	}
    	else {
    		$dateEnd = null;
    	}

    	$counter = 0;
    	$mainCounter = 0;

    	if($params['method'] === 4) $mainCounter = 1;

    	$modifyStrings = array(
    		0 => '+1 day',
    		4 => '+1 day',
    		5 => '+1 month',
    	);

    	while (($dateEnd !== null && $dateStart <= $dateEnd) || ($params['howMany'] !== null && $counter < $params['howMany']))
    	{
    		$ok = false;
    		$timestamp = $dateStart->getTimestamp();

    		if($params['method'] === 5) {
    			$ok = ($mainCounter % $params['howMuch'] == 0);
    		}
    		else if (in_array($params['method'], array(0, 4))) {
    			if (($params['dayNumbers'] !== null && in_array($this->getDayNumber($timestamp), $params['dayNumbers'])) || $params['dayNumbers'] === null) {
    				$ok = (
    					($params['method'] === 4 && ($mainCounter === 0 || ((ceil($mainCounter / 7) - 1)  % $params['howMuch'] == 0))) ||
    					($params['method'] === 0)
    				);
    			}
    		}
    		else if (in_array($params['method'], array(1, 2, 3))) {
    			$ok = (
    				($params['method'] === 1 && (!in_array($this->getDayNumber($timestamp), array(5, 6)))) ||
    				($params['method'] === 2 && (in_array($this->getDayNumber($timestamp), array(0, 2, 4)))) ||
    				($params['method'] === 3 && (in_array($this->getDayNumber($timestamp), array(1, 3))))
    			);
    		}

    		if ($ok) {
    			$days[] = $timestamp;
    			$counter += 1;
    		}

    		$mainCounter += 1;
    		$dateStart = $dateStart->modify($modifyStrings[$params['method']]);
    	}
    	return $days;
    }

    private function processResponse($fromCount = false)
    {
        $that = $this;

        $forCount = function(&$item) use ($that)
        {
            $item = $item->to_array();
        };


        $forNormal = function(&$item) use ($that)
        {
            $course_unit = $item->course_unit;
            $course = $course_unit->course;

            $roomName = $item->room->name;
            $course_unit = $course_unit->to_array();

            if ($course !== null) {
                $course = $course->to_array();
            }

            $coach = $item->user->to_array();
            $item = $item->to_array();

            $item['roomName'] = $roomName;
            $item['start_date'] = str_replace('T', ' ', $item['start_date']);
            $item['end_date'] = str_replace('T', ' ', $item['end_date']);
            $item['coach_name'] = $coach['first_name'] . ' ' . $coach['last_name'];
            $item['text'] = $course_unit['name'];
            $item['color'] = $course['color'];
            $item['textColor'] = $that->getTextColor($course['color']);
        };
        array_walk($this->response, ($fromCount) ? $forCount : $forNormal);
    }

    /**
     * @param $conditionsArray array
     * @return $this->response array
     */
    public function getLessonBy($conditionsArray)
    {
        ActiveRecord\Serialization::$DATETIME_FORMAT = 'Y-m-d H:i';

        if (!is_array($conditionsArray)) {
            throw new Exception('param is not array!');
        }

        $conditionString = '';
        $condLabels = array(
            'coaches' => 'user_id IN (?)',
            'rooms' => 'room_id IN (?)',
            'courseunits' => 'course_unit_id IN (?)'
        );

        foreach ($condLabels as $key => $label)
        {
            if (isset($conditionsArray[$key]) && count($conditionsArray[$key]) > 0) {
                $conditionString .= (strlen($conditionString) > 0) ? ' OR ' : '(';
                $conditionString .= $condLabels[$key];
            }
        }
        $conditionString .= (substr_count($conditionString, '(') > substr_count($conditionString, ')')) ? ')' : '';

        if (isset($conditionsArray['courseunits_not'])) {
            $conditionString .= (strlen($conditionString) > 0) ? ' AND ' : '';
            $conditionString .= '(course_unit_id NOT IN (?))';
        }

        if (isset($conditionsArray['ds'])) {
            $date = new DateTime($conditionsArray['ds']);
            $conditionString .= (strlen($conditionString) > 0) ? ' AND ' : '';
            $conditionString .= (isset($conditionsArray['de'])) ? '(' : '';
            $conditionString .= "CAST( start_date AS TIMESTAMP ) >= CAST( '" . $date->format('Y-m-d H:i') . "' AS TIMESTAMP )";
        }

        if (isset($conditionsArray['de'])) {
            $date = new DateTime($conditionsArray['de']);
            $conditionString .= (strlen($conditionString) > 0) ? ' AND ' : '';
            $conditionString .= 'CAST( end_date AS TIMESTAMP) <= CAST( \'' . $date->format('Y-m-d H:i') . '\' AS TIMESTAMP ))';
        }

        $conditionString .= (substr_count($conditionString, '(') > substr_count($conditionString, ')')) ? ')' : '';

        $conditions = $conditionsArray;
        $toUnset = array('count', 'ds', 'de', 'group');
        foreach ($toUnset as $unset) {
            unset($conditions[$unset]);
        }
        array_unshift($conditions, $conditionString);
        $token = $this->generateFlagsToken($conditionsArray);
        switch ($token)
        {
            case '11':
                $queryParam = array('conditions' => $conditions, 'group' => 'course_unit_id', 'select' => 'course_unit_id, COUNT(*)');
                $this->response = self::all($queryParam);
                $this->processResponse(true);
                break;

            case '10':
                $this->response = self::count(array('conditions' => $conditions));
                break;

            case '00':
            case '01':
                $this->response = self::find('all', array('conditions' => $conditions));
                $this->processResponse();
                break;
        }

        return $this->response;
    }

    private function generateFlagsToken($array)
    {
        $token = '';

        $token .= (int)(isset($array['count']) && $array['count']);
        $token .= (int)(isset($array['group']) && $array['group']);

        return $token;
    }

    public function getTextColor($color)
    {
        if (in_array($color, $this->colorsUsed)) {
            $brightness = $this->colorsUsed[$color]['brightness'];
        } else {
            $color = preg_replace("/[^0-9A-Fa-f]/", '', $color);
            $colorVal = hexdec($color);
            $rgbArray['red'] = 0xFF & ($colorVal >> 0x10);
            $rgbArray['green'] = 0xFF & ($colorVal >> 0x8);
            $rgbArray['blue'] = 0xFF & $colorVal;

            $brightness = round($rgbArray['red'] * 0.3 + $rgbArray['green'] * 0.59 + $rgbArray['blue'] * 0.11);
            $this->colorsUsed[$color]['brightness'] = $brightness;
        }

        if ($brightness > 150) {
            return $this->colors[1];
        }
        return $this->colors[0];
    }

    public function RunAcl($cycle_id = -1)
    {
        static $lesson_ids;

        if ($this->field_has_changed('cycle_id') && $this->cycle_id > 0 && $cycle_id == -1) {
            if (!is_array(($lesson_ids))) {
                $lesson_ids = array();
                $lesson_ids[$this->cycle_id]['ids'] = array();
            }
            $lesson_ids[$this->cycle_id]['ids'][] = $this->id;
            return;
        }

        $id = $this->id;

        if ($cycle_id > 0 && is_array($lesson_ids[$cycle_id]['ids'])) {
            $id = $lesson_ids[$cycle_id]['ids'];
            $lesson_ids[$cycle_id]['ids'] = array();
        }

        if ($this->field_has_changed('course_unit_id'))
        {
            $group_id = $this->course_unit->course->group_id;
            if ($group_id) {
                $users = GroupUser::user_ids($group_id);
                self::grant(Role::USER, $users, $id);
            }
        }

        if ($this->field_has_changed('user_id')) //coach
        {
            list ($old, $new) = $this->get_field_change('user_id');
            $courseScheduleId=CourseSchedule::findIdsOnLessonIds($id);
            if ($old)
            {
                self::revoke(Role::COACH, $old, $id);
                self::revoke(Role::COACH, $old, $courseScheduleId, 'course_schedule');
                
            }
            if ($new)
            {
                self::grant(Role::COACH, $new, $id);
                self::grant(Role::COACH, $new, $courseScheduleId, 'course_schedule');
                self::grant(Role::COACH, $new, $this->course_unit_id, 'course_units');
                self::grant(Role::COACH, $new, $this->course_unit->course_id, 'courses');
                self::grant(Role::COACH, $new, $this->course_unit->course->training_center_id, 'training_centers');
                
            }
        }
    }

    public function training_center()
    {
        return $this->room->training_center;
    }
    
    public function AfterCreateAcl()
    {
        parent::AfterCreateAcl();
        Project::GrantRevokeRightsToLeaders($this->course_unit->course->project_id);
    }     
}