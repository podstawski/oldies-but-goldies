<?php

class Millionaire_Model_UserRole extends Zend_Db_Table_Abstract
{
    protected $_name = 'user_roles';

    public function getAll() {
        $query = $this->select(); 
        $result = $this->fetchAll($query);
        return $result;
    }    

    public function getByName($name) {
        $query = $this->select(); 
        $query->where('name = ?', $name); 
        $result = $this->fetchRow($query);
        return $result;
    }
    
}

