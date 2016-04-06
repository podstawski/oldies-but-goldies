<?php
class Model_Participants extends Model_Abstract {
	const FLAG_READ = 1;
	const FLAG_WRITE = 2;

	protected $_name = 'participants';
	protected $_rowClass = 'Model_ParticipantsRow';
}
