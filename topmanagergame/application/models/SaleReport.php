<?php
/**
 * @author Radosław Szczepaniak
 */

class Model_SaleReport extends Zend_Db_Table_Abstract
{
    protected $_name = 'sale_report';

    protected $_referenceMap = array(
        'Model_Company' => array(
            'columns' => 'company_id',
            'refTableClass' => 'Model_Company',
            'refColumns' => 'id'
        )
    );
}
