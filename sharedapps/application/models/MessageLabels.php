<?php
class Model_MessageLabels extends Model_Abstract {
	protected $_name = 'message_labels';


	public function getByMessageAndLabelID($messageID, $labelID) {
		$select = $this
			->select(true)
			->where('label_id = ?', $labelID)
			->where('message_id = ?', $messageID);
		return $this->fetchRow($select);
	}
}
