<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_Employee extends Zend_Db_Table_Abstract
{
    const MAX_SKILL_LEVEL = 5;
    
    protected $_name = 'employee';
    protected $_rowClass = 'Model_EmployeeRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
