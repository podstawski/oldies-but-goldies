<?php

class Millionaire_Model_Profile extends Zend_Db_Table_Abstract
{
    protected $_name = 'profiles';
	protected $_primary = 'id';

	function getAll($page=1,$pageLimit=20) {
		$query = $this->select(); 
		$query->order('id');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	}

	function getAllForRanking($page=1,$pageLimit=20,$order=false) {
		$query = $this->select(); 
		$query->from(array('p'=>'profiles'),array('id','user_id','created','points','cash'));
		if($order) {
			$query->order($order);
		} else {
			$query->order('id');
		}
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit); 
		$paginator->setCurrentPageNumber($page); 
		return $paginator;
	}

	function getPlayers() {
		$users = new Model_User;
		$query = $users->select()->setIntegrityCheck(false);
		$app = Zend_Registry::get('app');
		$query->from(array('p' => 'profiles'),array('user_id','points','cash','data'))
			->join(array('u' => 'users'),'p.user_id = u.id')
			->where('p.created > ?', $app['profiles_latest_version']);			
		$result = $this->fetchAll($query);
		return $result;
	}

	function ranking() {
		$query = $this->select();
		$app = Zend_Registry::get('app');
		$query->where('created > ?', $app['profiles_latest_version'])->order('id ASC');
		$result = $this->fetchAll($query);
		return $result;
	}

    /**
     * @param $user_id
     * @return null|Zend_Db_Table_Row_Abstract
     */
	function readLastProfile($user_id)
    {
        return $this->fetchRow(array(
            'user_id = ?' => $user_id
        ), 'id DESC');
    }
	function readProfile($id)
    {
        return $this->fetchRow(array(
            'id = ?' => $id
        ), 'id DESC');
	}
	
	function getRanking($page=1, $pageLimit=20) {
		$query = $this->select(); 				
		$app = Zend_Registry::get('app');
		$query->from(array('p'=>'profiles'),array('id','user_id','created','points','cash'));
		$query->where('created > ?', $app['profiles_latest_version'])->order('points DESC')->order('cash DESC');
		$paginator = new Zend_Paginator(new Zend_Paginator_Adapter_DbTableSelect($query)); 
		$paginator->setItemCountPerPage($pageLimit)->setCurrentPageNumber($page); 
		return $paginator;
	} 

}

