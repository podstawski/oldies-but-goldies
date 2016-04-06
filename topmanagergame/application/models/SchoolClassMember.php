<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_SchoolClassMember extends Zend_Db_Table_Abstract
{
    protected $_name = 'school_class_member';

    protected $_referenceMap = array(
        'Model_SchoolClass' => array(
            'columns' => 'class_id',
            'refTableClass' => 'Model_SchoolClass',
            'refColumns' => 'id'
        ),
        'Model_User' => array(
            'columns' => 'user_id',
            'refTableClass' => 'Model_User',
            'refColumns' => 'id'
        ),
    );
}