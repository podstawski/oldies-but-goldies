<?php

/**
 * @author Radosław Szczepaniak
 */

class Model_Product extends Zend_Db_Table_Abstract
{
    const TYPE_PC       = 1;
    const TYPE_TABLET   = 2;
    const TYPE_CONSOLE  = 3;
    const TYPE_LAPTOP   = 4;

    const MAX_TECHNOLOGY = 5;
    const MAX_QUALITY    = 5;

    public static $types = array(self::TYPE_PC, self::TYPE_TABLET, self::TYPE_CONSOLE, self::TYPE_LAPTOP);

    protected $_name        = 'product';
    protected $_rowClass    = 'Model_ProductRow';
    protected $_rowsetClass = 'Model_ProductRowset';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
