<?php
class Model_ContactEmails extends Model_Abstract {
	protected $_name = 'contact_emails';

	public function getByContactID($contactID) {
		$select = $this
			->select(true)
			->where('contact_id = ?', $contactID)
			;
		return $this->fetchAll($select);
	}
}
