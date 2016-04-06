<?php

class Millionaire_Model_Invitation extends Zend_Db_Table_Abstract
{

	protected $_name = 'invitations';
	protected $_primary = 'id'; 

    public function getById($id) {
        $query = $this->select(); 
        $query->where('id = ?', $id); 
        $result = $this->fetchRow($query);
        return $result;
    }

    public function getByEmail($email) {
        $query = $this->select(); 
        $query->where('email = ?', $email); 
        $results = $this->fetchAll($query);
        return $results;
    }

    public function getByTestId($test_id, $sort_by_email = true) {
        $query = $this->select(); 
		$query->where('test_id = ?', $test_id); 
		if($sort_by_email) {
			$query->order('email ASC');
		}
        $results = $this->fetchAll($query);
        return $results;
    }

    public function getByTestIdAndEmail($test_id,$email) {
        $query = $this->select(); 
        $query->where('email = ?', $email); 
        $query->where('test_id = ?', $test_id); 
        $results = $this->fetchAll($query);
        return $results;
    }	

}
