<?php

class Report_RegistrationForm extends Report_Abstract
{
    protected $_filterRules = array(
        'user_id' => 'Digits',
        'course_id' => 'Digits'
    );

    protected $_validationRules = array(
        'user_id' => array('presence' => 'required'),
        'course_id' => array('presence' => 'required')
    );
}
