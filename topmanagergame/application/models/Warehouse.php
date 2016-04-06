<?php

class Model_Warehouse extends Zend_Db_Table_Abstract
{
    const JUST_PRODUCED = 0;
    const IN_WAREHOUSE  = 1;
    const ON_MARKET     = 2;
    const ARCHIVED      = 3;

	protected $_name = 'warehouse';
    protected $_rowClass = 'Model_WarehouseRow';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
