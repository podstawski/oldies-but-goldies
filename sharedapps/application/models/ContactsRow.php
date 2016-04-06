<?php
class Model_ContactsRow extends Zend_Db_Table_Row {
	public function getEmails() {
		$modelContactEmails = new Model_ContactEmails();
		$select = $modelContactEmails
			->select(true)
			->where('contact_id = ?', $this->id)
			;
		return $modelContactEmails->fetchAll($select);
	}

	public function getUserContactByUserID($userID) {
		$model = new Model_UserContactGroupContacts();
		$select = $model
			->select(true)
			->setIntegrityCheck(false)
			->join('user_contact_groups', 'user_contact_group_id = user_contact_groups.id', array())
			->where('user_contact_group_contacts.contact_id = ?', $this->id)
			->where('user_contact_groups.user_id = ?', $userID)
			;
		$row = $model->fetchRow($select);
		if (empty($row)) {
			return null;
		}
		return $model->fetchRow($model->select(true)->where('id = ?', $row->id));
	}

	public function getIdent() {
		$ident = array();
		$modelContactEmails = new Model_ContactEmails();
		$dbIdent = $modelContactEmails->getByContactID($this->id);
		foreach ($dbIdent as $dbIdentRow) {
			$ident []= $dbIdentRow->email;
		}
		return $ident;
	}

	public function updateIdent($newIdent) {
		$ident = $this->getIdent();
		$toDelete = array();
		$toAdd = array();
		foreach ($newIdent as $i) {
			if (!in_array($i, $ident)) {
				$toAdd []= $i;
			}
		}
		foreach ($ident as $i) {
			if (!in_array($i, $newIdent)) {
				$toDelete []= $i;
			}
		}
		$modelContactEmails = new Model_ContactEmails();
		foreach ($toDelete as $i) {
			foreach ($modelContactEmails->fetchAll($modelContactEmails->select()->where('email = ?', $i)) as $row) {
				GN_Debug::debug('Removing ' . $row->email);
				$row->delete();
			}
		}
		foreach ($toAdd as $i) {
			GN_Debug::debug('Adding ' . $i);
			$row = $modelContactEmails->createRow();
			$row->contact_id = $this->id;
			$row->email = $i;
			$row->save();
		}
	}

}
