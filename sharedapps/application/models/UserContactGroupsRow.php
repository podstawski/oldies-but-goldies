<?php
class Model_UserContactGroupsRow extends Zend_Db_Table_Row {
	public function getUser() {
		$model = new Model_Users();
		$select = $model
			->select(true)
			->where('id = ?', $this->user_id);
		return $model->fetchRow($select);
	}

	public function getContactGroup() {
		$model = new Model_ContactGroups();
		$select = $model
			->select(true)
			->where('id = ?', $this->contact_group_id);
		return $model->fetchRow($select);
	}

	public function getName() {
		if ($this->local_name != '') {
			return $this->local_name;
		}
		return $this->getContactGroup()->name;
	}

	public function getUserContacts() {
		$model = new Model_UserContactGroupContacts();
		$select = $model
			->select(true)
			->order('contact_id ASC')
			->where('user_contact_group_id = ?', $this->id)
			;
		return $model->fetchAll($select);
	}

	public function isConfirmed() {
		return (($this->agree_time != null) or ($this->user_id == $this->getContactGroup()->user_id));
	}

	public function isPaid() {
		return strtotime($this->getUser()->expire) > time();
	}
}
