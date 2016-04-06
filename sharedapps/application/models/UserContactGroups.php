<?php
class Model_UserContactGroups extends Model_Abstract {
	protected $_name = 'user_contact_groups';
	protected $_rowClass = 'Model_UserContactGroupsRow';

	public function getByID($id) {
		$select = $this
			->select(true)
			->where('id = ?', $id)
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

	public function getByNameAndUserID($name, $userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('local_name = ?', $name)
			;
		return $this->fetchRow($select);
	}

	public function getByContactGroupIDAndUserID($contactGroupID, $userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('contact_group_id = ?', $contactGroupID)
			;
		return $this->fetchRow($select);
	}

	public function getByGoogleGroupNameAndUserID($googleGroupName, $userID) {
		$select = $this
			->select(true)
			->where('user_contact_groups.user_id = ?', $userID)
			->setIntegrityCheck(false)
			->join('contact_groups', 'contact_Groups.id = contact_group_id', array('name'))
			->where('local_name = ? OR name = ?', $googleGroupName)
			;
		$row = $this->fetchRow($select);
		if (!empty($row)) {
			return $this->getByID($row->id);
		}
		return null;
	}

	public function getAllUnconfirmedByUserID($userID) {
		$select = $this
			->select(true)
			->setIntegrityCheck(false)
			->join('contact_groups', 'contact_groups.id = contact_group_id', array())
			->where('user_contact_groups.user_id = ?', $userID)
			->where('agree_time IS NULL AND contact_groups.user_id != user_contact_groups.user_id')
			;
		return $this->fetchAll($select);
	}
}
