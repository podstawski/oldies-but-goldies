<?php
class Model_Action extends Model_Abstract
{
	const DIRECTION_FORWARD = 'forward';
	const DIRECTION_BACKWARD = 'backward';

	protected $_name = 'actions';
	protected $_rowClass = 'Model_ActionRow';

	public function selectByDomainId($domainId)
	{
		return $this
			->select(true)
			->where('domain_id = ?', intval($domainId))
			;
	}
}
?>
