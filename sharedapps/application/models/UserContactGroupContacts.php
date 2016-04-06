<?php
class Model_UserContactGroupContacts extends Model_Abstract {
	protected $_name = 'user_contact_group_contacts';
	protected $_rowClass = 'Model_UserContactGroupContactsRow';

	public function getByContactID($id) {
		$select = $this
			->select(true)
			->where('contact_id = ?', $id)
			;
		return $this->fetchAll($select);
	}

	public function getByID($id) {
		$select = $this
			->select(true)
			->where('id = ?', $id)
			;
		return $this->fetchRow($select);
	}

	public function getByUserContactGroupAndGoogleContact($userContactGroupID, $googleContact) {
		$select = $this
			->select(true)
			->where('user_contact_group_id = ?', $userContactGroupID)
			->where('google_contact_id = ?', $googleContact->id)
			;
		return $this->fetchRow($select);
	}
}
