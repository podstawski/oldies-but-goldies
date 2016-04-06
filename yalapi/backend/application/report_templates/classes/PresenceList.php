<?php

class Report_PresenceList extends Report_Abstract
{
    protected $_filterRules = array(
        'group_id'  => 'Digits',
        'course_id' => 'Digits'
    );

    protected $_validationRules = array(
        'group_id'  => array('presence' => 'optional'),
        'course_id' => array('presence' => 'required')
    );
}
