<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Model_Domain extends Zend_Db_Table_Abstract
{
    protected $_name = 'domains';
    protected $_rowClass = 'GN_Model_DomainRow';

    /**
     * @param $name
     * @return GN_Model_DomainRow
     */
    public function fetchDomain($name)
    {
        return $this->fetchRow(array(
            'domain_name = ?' => $name
        ));
    }
}