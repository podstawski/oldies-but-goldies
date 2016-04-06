<?php
class Model_UserLabelsRow extends Zend_Db_Table_Row {
	public function getUser() {
		$model = new Model_Users();
		$select = $model
			->select(true)
			->where('id = ?', $this->user_id);
		return $model->fetchRow($select);
	}

	public function getLabel() {
		$model = new Model_Labels();
		$select = $model
			->select(true)
			->where('id = ?', $this->label_id);
		return $model->fetchRow($select);
	}

	public function getName() {
		if ($this->local_name != '') {
			return $this->local_name;
		}
		return $this->getLabel()->name;
	}

	public function isConfirmed() {
		return (($this->agree_time != null) or ($this->user_id == $this->getLabel()->user_id));
	}

	public function isPaid() {
		return strtotime($this->getUser()->expire) > time();
	}
}
