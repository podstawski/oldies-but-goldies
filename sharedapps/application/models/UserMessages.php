<?php
class Model_UserMessages extends Model_Abstract {
	protected $_name = 'user_messages';


	public function getByUserAndMessageID($userID, $messageID) {
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('message_id = ?', $messageID);
		return $this->fetchRow($select);
	}
	
	
	public function getByLabelAndUserAndThrID($labelID,$userID,$thrID) {
	
		//die("$labelID,$userID,$thrID");
		$select = $this->select(true)->
			setIntegrityCheck(false)->
			joinLeft(array('message_labels'),'message_labels.message_id=user_messages.message_id')->
			where('user_messages.user_id = ?',$userID)->
			where('user_messages.thrid = ?',$thrID)->
			where('message_labels.label_id = ?',$labelID)
			;
			
		return $this->fetchRow($select);	
	}
	
}
