<?php

class Report_Ejournal extends Report_Abstract
{
    protected $_filterRules = array(
        'course_id' => 'Digits'
    );

    protected $_validationRules = array(
        'course_id' => array('presence' => 'required'),
        'date_from' => array('presence' => 'optional'),
        'date_to'   => array('presence' => 'optional')
    );
}
