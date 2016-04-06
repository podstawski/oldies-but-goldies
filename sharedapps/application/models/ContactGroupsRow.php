<?php
class Model_ContactGroupsRow extends Zend_Db_Table_Row {
	public function getUserContactGroups($limits = false) {
		$model = new Model_UserContactGroups();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->join('users', 'users.id = user_id', 'email')
			->where('contact_group_id = ?', $this->id)
			;
		if ($limits) {
			$select->where('agree_time IS NOT NULL or user_id = ?', $this->user_id);
			$select->where('users.expire>now()');
			$select->where('(users.disabled IS NULL OR users.disabled>now()-interval \'2 hours\')');
		}
		$select->order('email ASC');
		return $model->fetchAll($select);
	}

	public function getUser() {
		$model = new Model_Users();
		return $model->getByID($this->user_id);
	}

	public function getOwnerUserContactGroup() {
		$model = new Model_UserContactGroups();
		$select = $model
			->select(true)
			->where('user_id = ?', $this->user_id)
			->where('contact_group_id = ?', $this->id)
			;
		return $model->fetchRow($select);
	}

	public function getUserContactGroupByUserID($userID) {
		$model = new Model_UserContactGroups();
		$select = $model
			->select(true)
			->where('user_id = ?', $userID)
			->where('contact_group_id = ?', $this->id)
			;
		return $model->fetchRow($select);
	}
	
	
	public function getRelatedContactGroupsActionCount() {
		
		$sql="SELECT count(*) FROM contact_groups WHERE started>finished AND id<>".$this->id;
		$sql.=" AND started+900> ".time()." AND id IN (SELECT contact_group_id FROM user_contact_groups WHERE user_id IN (
				SELECT user_id FROM user_contact_groups WHERE contact_group_id=".$this->id."	
			))";
		$result = $this->getTable()->getAdapter()->fetchOne($sql);
		return $result;

		die("$sql\n".print_r($result,1)."\n");
		
	}
	
	public function finish() {
		$this->finished=time();
		$this->save();
	}

	public function start() {
		$this->started=time();
		$this->save();
	}	
	
	
}
