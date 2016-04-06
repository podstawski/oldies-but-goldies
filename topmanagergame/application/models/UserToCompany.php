<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_UserToCompany extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_to_company';

    protected $_referenceMap = array(
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        ),
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}