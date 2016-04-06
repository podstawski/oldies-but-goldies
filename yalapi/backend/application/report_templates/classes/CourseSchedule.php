<?php

class Report_CourseSchedule extends Report_Abstract
{
    protected $_filterRules = array(
        'course_id' => 'Digits'
    );

    protected $_validationRules = array(
        'course_id' => array('presence' => 'required')
    );
}
