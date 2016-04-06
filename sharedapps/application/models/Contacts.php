<?php
class Model_Contacts extends Model_Abstract {
	protected $_name = 'contacts';
	protected $_rowClass = 'Model_ContactsRow';

	public function getByContactGroupID($contactGroupID) {
		$select = $this
			->select(true)
			->where('contact_group_id = ?', $contactGroupID)
			;
		return $this->fetchAll($select);
	}

	public function getByUserID($userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', intval($userID))
			;
		return $this->fetchRow($select);
	}

	public function getByContactGroupAndGoogleContact($userContactGroupID, $googleContact) {
		$select = $this
			->select(true)
			->where('user_contact_group_id = ?', $userContactGroupID)
			->where('google_contact_Id = ?', $googleContact->id)
			;
		return $this->fetchRow($select);
	}
}
