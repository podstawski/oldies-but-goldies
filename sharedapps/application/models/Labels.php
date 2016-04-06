<?php
class Model_Labels extends Model_Abstract {
	protected $_name = 'labels';
	protected $_rowClass = 'Model_LabelsRow';

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
			->where('name = ?', $googleID)
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
