<?php
class Model_UserContactGroupContactsRow extends Zend_Db_Table_Row {
	public function getUser() {
		return $this->getUserContactGroup()->getUser();
	}

	public function getUserContactGroup() {
		$model = new Model_UserContactGroups();
		$select = $model
			->select(true)
			->where('id = ?', $this->user_contact_group_id);
		return $model->fetchRow($select);
	}

	public function getContact() {
		$model = new Model_Contacts();
		$select = $model
			->select(true)
			->where('id = ?', $this->contact_id);
		return $model->fetchRow($select);
	}

}
