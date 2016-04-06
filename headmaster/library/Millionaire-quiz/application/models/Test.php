<?php
class Millionaire_Model_Test extends Zend_Db_Table_Abstract {
	protected $_name = 'tests'; 
	protected $_primary = 'id'; 

	function isUnique($pass = false) {
		if($pass) {
			$query = $this->select(); 
			$query->where('pass = ?', $pass); 
			$result = $this->fetchAll($query);
			if(count($result)>0) {
				return false;
			} else {
				return true;	
			}
		} else {
			return false;
		}
	}		    

	function getTestList($author_id=false,$page=1,$pageLimit=20) {
		if($author_id){
			$answerTable = new Model_UserRole;
			$query = $this->select(); 
			$query->where('author_id = ?', $author_id); 
			$query->order('id DESC');
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		} else {
			return false;
		}
	} 

	function getById($id) {
		$query = $this->select(); 
		$query->where('id = ?', $id); 
		$result = $this->fetchRow($query);
		return $result;
	} 	

	function getByUsersIds($users_ids) {
		$query = $this->select(); 
		$query->where('author_id IN(?)', $users_ids); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByTestsIds($tests_ids, $active=false, $limit=false) {
		$query = $this->select(); 
		$query->where('id IN(?)', $tests_ids); 
		if($active) {
			$query->where('ia IN(?)', $tests_ids) 
			->where('status = ?', 1)
		 	->where('(active_to > now()) OR (active_to is NULL)');
		} else {
			$query->where('pass IN(?)', $tests_passes);
		}
		if($limit) {
			$query->limit($limit);
		}
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByTestsPasses($tests_passes, $active=false, $limit=false) {
		$query = $this->select(); 
		if($active) {
			$query->where('pass IN(?)', $tests_passes) 
			->where('status = ?', 1)
		 	->where('(active_to > now()) OR (active_to is NULL)');
		} else {
			$query->where('pass IN(?)', $tests_passes);
		}
		if($limit) {
			$query->limit($limit);
		}
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getPassesArrayByTestsIds($tests_ids) {
		$query = $this->select(); 
		$query->where('id IN(?)', $tests_ids); 
		$result = $this->fetchAll($query);
		$passes = array();
		foreach($result as $r) {
			$passes[] = $r->pass;
		}
		return $passes;
	} 	

	function getByUserId($user_id) {
		$query = $this->select(); 
		$query->where('author_id = ?', $user_id); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByPass($pass) {
		$query = $this->select();
		$query->where('pass = ?', $pass); 
		$result = $this->fetchRow($query);
		return $result;
	} 	
	
	
	public function findAllToExport()
	{
		$query = $this->select()->setIntegrityCheck(false);
		$query->from(array('t'=>$this->_name),array('pass'));
		$query->join(array('a'=>'attempts'), 't.pass = a.test_pass',array('max(a.time_started)'));
		$query->where('a.server_finished>t.time_exported');
		$query->group('pass');
		$result = $this->fetchAll($query);
		return $result;
	}

	function getAll() {
		$query = $this->select(); 
        $result = $this->fetchAll($query);
		return $result;
	}
	
}
