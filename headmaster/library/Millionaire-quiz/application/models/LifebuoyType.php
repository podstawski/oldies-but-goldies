<?php
class Millionaire_Model_LifebuoyType extends Zend_Db_Table_Abstract 
{ 
	protected $_name = 'lifebuoy_types'; 
	protected $_primary = 'id'; 

	function getById($id) {
		$query = $this->select(); 
		$query->where('id = ?', $id); 
		$result = $this->fetchRow($query);
		return $result;
	} 	

}


