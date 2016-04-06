<?php
class Model_UserLabels extends Model_Abstract {
	protected $_name = 'user_labels';
	protected $_rowClass = 'Model_UserLabelsRow';

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

	public function getByLabelIDAndUserID($labelID, $userID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('label_id = ?', $labelID)
			;
		return $this->fetchRow($select);
	}

	public function getAllUnconfirmedByUserID($userID) {
		$select = $this
			->select(true)
			->setIntegrityCheck(false)
			->join('labels', 'labels.id = label_id', array())
			->where('user_labels.user_id = ?', $userID)
			->where('agree_time IS NULL AND labels.user_id != user_labels.user_id')
			;
		return $this->fetchAll($select);
	}

}
