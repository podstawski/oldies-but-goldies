<?php

class Report_NewStudent extends Report_Abstract
{
    protected $_filterRules = array(
        'user_id' => 'Digits',
    );

    protected $_validationRules = array(
        'user_id' => array('presence' => 'required'),
    );
}
