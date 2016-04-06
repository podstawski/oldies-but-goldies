<?php

class Model_Rank extends Zend_Db_Table_Abstract
{
	protected $_name = 'rank';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
