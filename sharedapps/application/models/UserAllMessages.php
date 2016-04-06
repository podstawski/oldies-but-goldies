<?php
class Model_UserAllMessages extends Model_Abstract {
	protected $_name = 'user_all_messages';


	public function getByUserAndImapID($userID, $imapID, $labelID) {
		$select = $this->select(true)
			->where('user_id = '.$userID.' AND imap_id= '.$imapID.' AND label_id='.$labelID);
		
		return $this->fetchRow($select);
	}
	
	
}
