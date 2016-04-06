<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Model_School extends Zend_Db_Table_Abstract
{
    protected $_name = 'school';

    protected $_dependentTables = array('Model_SchoolClass');
}