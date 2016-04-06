<?php

class Report_SurveyResults extends Report_Abstract
{
    protected $_filterRules = array(
        'group_id' => 'Digits',
        'survey_id' => 'Digits'
    );

    protected $_validationRules = array(
        'group_id' => array('presence' => 'required'),
        'survey_id' => array('presence' => 'required')
    );
}
