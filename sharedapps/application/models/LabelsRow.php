<?php
class Model_LabelsRow extends Zend_Db_Table_Row {
	public function getUserLabels($limits = false) {
		$model = new Model_UserLabels();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->join('users', 'users.id = user_id', 'email')
			->where('label_id = ?', $this->id)
			;
		if ($limits) {
			$select->where('agree_time IS NOT NULL OR user_id = ?',$this->user_id);
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

	public function getOwnerUserLabel() {
		$model = new Model_UserLabels();
		$select = $model
			->select(true)
			->where('user_id = ?', $this->user_id)
			->where('label_id = ?', $this->id)
			;
		return $model->fetchRow($select);
	}

	public function getUserLabelByUserID($userID) {
		$model = new Model_UserLabels();
		$select = $model
			->select(true)
			->where('user_id = ?', $userID)
			->where('label_id = ?', $this->id)
			;
		return $model->fetchRow($select);
	}
	
	
	public function getRelatedLabelsActionCount() {
		
		$sql="SELECT count(*) FROM labels WHERE started>finished AND id<>".$this->id;
		$sql.=" AND started+900> ".time()." AND id IN (SELECT label_id FROM user_labels WHERE user_id IN (
				SELECT user_id FROM user_labels WHERE label_id=".$this->id."	
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
