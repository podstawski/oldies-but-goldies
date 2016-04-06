<?php

class Millionaire_Model_Domain extends Zend_Db_Table_Abstract
{
    protected $_name = 'domains';
    protected $_rowClass = 'Model_DomainRow';

    public static function getByName($domain)
    {
        $domainModel = new self();
        return $domainModel->fetchRow(array('domain_name = ?' => $domain));
	}

	public function getById($id) {
        $query = $this->select(); 
        $query->where('id = ?', $id); 
        $result = $this->fetchRow($query);
        return $result;
	}
	
    public function getAll() {
        $query = $this->select(); 
		$query->order('id ASC');
		$result = $this->fetchAll($query);
        return $result;
    }
	
}
