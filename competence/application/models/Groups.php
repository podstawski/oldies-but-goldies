<?php
/**
 * @author <marcin.kurczewski@gammanet.pl> Marcin Kurczewski
 */

class Model_Groups extends Model_Abstract
{
    protected $_name = 'groups';
	protected $_rowClass = 'Model_GroupsRow';

	public function selectDomain($domainId)
	{
		return $this
			->select(true)
			->where('domain_id = ?', $domainId)
			;
	}

}
