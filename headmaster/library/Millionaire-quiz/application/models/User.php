<?php

class Millionaire_Model_User extends Zend_Db_Table_Abstract
{
    protected $_name = 'users';

	/**
     * @param $email
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function findByMail($email) {
        $query = $this->select(); 
        $query->where('email = ?', $email); 
        $result = $this->fetchRow($query);
        return $result;
    }

	/**
     * @param $id
     * @return null|Zend_Db_Table_Row_Abstract
	 */
    public function findById($id) {
        $query = $this->select(); 
        $query->where('id = ?', $id); 
        $result = $this->fetchRow($query);
        return $result;
	}

	public function getById($id) {
		return $this->findById($id);
	}
	
    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAll() {
        $query = $this->select(); 
        $result = $this->fetchAll($query);
        return $result;
    }

    /**
     * @return array
     */
    public function getAllByIdArray($full = false, $emails = false) {
		$query = $this->select(); 
		if(is_array($emails)) {
	        $query->where('id IN(?)', $emails); 
		}
        $result = $this->fetchAll($query);
		$array = array();
		foreach ($result as $value) {
			if($full) {
				$array[$value->id] = $value->toArray(); 
			} else {
				$array[$value->id] = $value->email; 
			}
		}
        return $array;
    }

    /**
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function getAllByMail() {
        $query = $this->select(); 
        $query->order('email ASC');
        $result = $this->fetchAll($query);
        return $result;
    }

    /**
     * @param $user_role
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByRank($user_role) {
        $query = $this->select(); 
        $query->where('user_role = ?', $user_role); 
        $result = $this->fetchAll($query);
        return $result;
    }

    /**
     * @param int $page
     * @param int $pageLimit
     * @return Zend_Paginator
     */
	function getUsersList($page=1,$pageLimit=20) {
		$answerTable = new Model_UserRole;
		$query = $answerTable->select()->setIntegrityCheck(false);
		$query->from(array('u' => 'users'), array('u.id', 'u.email', 'u.user_role')); 
		$query->join(array('r' => 'user_roles'), 'u.user_role = r.id', array('r.name')); 
		$query->order('u.id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	} 

	function userSearch($params) {
		// echo '<pre>'; print_r($params); die;
		if(!isset($params['perPage'])) {
			$params['perPage'] = 20;
		}
		if(!isset($params['page'])) {
			$params['page'] = 1;
		}	
		$query = $this->select();
		if(isset($params['sort'])) {
			switch($params['sort']) {
				case 'id':
					$query->order('id');
					break;
				case 'domain_id':
					$query->order('domain_id');
					break;
				case 'email':
					$query->order('email');
					break;
				case 'active':
					$query->order('active');
					break;
				case 'user_role':
					$query->order('user_role');
					break;
				case 'created':
					$query->order('created');
					break;
				default:
					$query->order('id');
					break;
			}
		} else {
			$query->order('id');
		}
	
		if(isset($params['email'])) {
			$query->where('LOWER(email) LIKE LOWER(?)', '%'.$params['email'].'%');
		}

		if(isset($params['user_role'])) {
			$query->where('user_role = ?', $params['user_role']);
		}
		
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
	
		$paginator->setItemCountPerPage($params['perPage']);
		$paginator->setCurrentPageNumber($params['page']);
		return $paginator;
	}
		
}

