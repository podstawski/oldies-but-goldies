<?php
class Model_Messages extends Model_Abstract {
	protected $_name = 'messages';
	protected $_rowClass = 'Model_MessagesRow';

	public function getByMessageID($messageID) {
		$select = $this
			->select(true)
			->where('message_id = ?', $messageID);
		return $this->fetchRow($select);
	}
	
	public function getByMessageIdLabel($messageID,$labelID)
	{
		$select = $this->select(true)->
			setIntegrityCheck(false)->
			joinLeft(array('message_labels'),'message_labels.message_id=messages.id')->
			where('messages.message_id = ?', $messageID)->
			where('message_labels.label_id = ?',$labelID);
			
		return $this->fetchRow($select);
	}



}
