<?php
class Millionaire_Model_Attempt extends Zend_Db_Table_Abstract {
	protected $_name = 'attempts'; 
	protected $_primary = 'id'; 

	function getAttemptsList($test_pass=false,$page=1,$pageLimit=20) {
		if($test_pass){
            $query = $this->select(); 
            $query->where('test_pass = ?', $test_pass); 
			$query->order('time_started DESC');
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		} else {
			return false;
		}
	} 

	function getAttemptsListByUser($user_id=false,$page=1,$pageLimit=20) {
		if($user_id){
            $query = $this->select(); 
            $query->where('user_id = ?', $user_id); 
			$query->order('time_started DESC');
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		} else {
			return false;
		}
	} 

	function getAttemptsByUser($user_id=false,$page=1,$pageLimit=20) {
		if($user_id){
			$testTable = new Model_Test;
			$query = $testTable->select()->setIntegrityCheck(false);
			$query->from(array('a' => 'attempts'), array('a.id', 'a.test_pass', 'a.nick', 'a.questions', 'a.answers', 'a.answers_time', 'a.time_started', 'a.time_finished', 'a.lifebuoys'));
			$query->join(array('t' => 'tests'), 'a.test_pass = t.pass', array('t.name'));
			$query->where('a.user_id = ?', $user_id);
			$query->order('a.time_started DESC');
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		} else {
			return false;
		}
	} 

	function getHighScores($test_pass=false,$page=1,$pageLimit=20) {
		if($test_pass){
            $query = $this->select(); 
            $query->where('test_pass = ?', $test_pass); 
			$query->order(array('points DESC', 'time_finished ASC'));
			$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query));
			$paginator->setItemCountPerPage($pageLimit); 
			$paginator->setCurrentPageNumber($page);
			return $paginator;
		} else {
			return false;
		}
    } 

	function getAllHighScores($test_pass=false) {
		if($test_pass){
            $query = $this->select(); 
            $query->where('test_pass = ?', $test_pass); 
			$query->order(array('points DESC', 'time_finished ASC'));
			return $this->fetchAll($query);
		} else {
			return false;
		}		
	}

	function getByHash($hash) {
		$query = $this->select(); 
        $query->where('session_hash = ?', $hash); 
        $result = $this->fetchRow($query);
		return $result;
	} 	

	function getById($id) {
		$query = $this->select(); 
        $query->where('id = ?', $id); 
        $result = $this->fetchRow($query);
		return $result;
	}

	function getAllByUserId($user_id) {
		$query = $this->select(); 
        $query->where('user_id = ?', $user_id); 
        $result = $this->fetchAll($query);
		return $result;
	} 

	function getByUserId($user_id) {
		return $this->getAllByUserId($user_id);
	}

	function getByUsersIds($users_ids) {
		$query = $this->select(); 
		$query->where('user_id IN(?)', $users_ids); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByTestsPasses($tests_passes) {
		$query = $this->select(); 
		$query->where('test_pass IN(?)', $tests_passes); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getByTestsPassesComplete($tests_passes) {
		$testTable = new Model_Test;
		$query = $testTable->select()->setIntegrityCheck(false);
		$query->from(array('a' => 'attempts'), array('a.id', 'a.test_pass', 'a.nick', 'a.questions', 'a.answers', 'a.answers_time', 'a.time_started', 'a.time_finished', 'a.lifebuoys'));
		$query->join(array('t' => 'tests'), 'a.test_pass = t.pass', array('t.name','t.author_id'));
		$query->where('a.test_pass IN(?)', $tests_passes); 
		$result = $this->fetchAll($query);
		return $result;
	} 	

	function getTestByUserId($pass,$user_id) {
		$query = $this->select(); 
        $query->where('test_pass = ?', $pass); 
        $query->where('user_id = ?', $user_id); 
        $result = $this->fetchAll($query);
		return $result;
	} 	

	function getAll() {
		$query = $this->select(); 
        $result = $this->fetchAll($query);
		return $result;
	}

	function getByTestPass($pass) {
		$query = $this->select(); 
        $query->where('test_pass = ?', $pass); 
        $result = $this->fetchAll($query);
		return $result;
	} 	

	function getForGroupMode($test_pass) {
		$query = $this->select(); 
        $query->where('test_pass = ?', $test_pass); 
        // $query->order('points DESC');
        $query->order('id ASC');
        $query->limit(12);
        $result = $this->fetchAll($query);
		return $result;
	}
	
}
