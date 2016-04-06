<?php
class Model_ContactGroups extends Model_Abstract {
	protected $_name = 'contact_groups';
	protected $_rowClass = 'Model_ContactGroupsRow';

	public function getByGoogleGroupIDAndUserID($groupID, $userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('group_id = ?', $groupID)
			;
		return $this->fetchRow($select);
	}

	public function getByNameAndUserID($name, $userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('name = ?', $name)
			;
		return $this->fetchRow($select);
	}

	public function getByGoogleID($googleID) {
		$select = $this
			->select(true)
			->where('group_id = ?', $googleID)
			;
		return $this->fetchRow($select);
	}

	public function getByUserID($userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			;
		return $this->fetchAll($select);
	}

	public function getByID($id) {
		$select = $this
			->select(true)
			->where('id = ?', intval($id))
			;
		return $this->fetchRow($select);
	}
}
