<?php

class Report_LoginsReceiveConfirmation extends Report_Abstract
{
    protected $_filterRules = array(
        'group_id' => 'Digits'
    );

    protected $_validationRules = array(
        'group_id' => array('presence' => 'required')
    );
}
