<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_SchoolClass extends Zend_Db_Table_Abstract
{
    protected $_name = 'school_class';

    protected $_dependentTables = array('Model_SchoolClassMember');

    protected $_referenceMap = array(
        'Model_School' => array(
            'columns' => 'school_id',
            'refTableClass' => 'Model_School',
            'refColumns' => 'id'
        )
    );
}