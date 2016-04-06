<?php
class Model_Tests extends Model_Abstract {
	protected $_name = 'tests';
	protected $_rowClass = 'Model_TestsRow';

	const STATUS_UNOPENED = 0;
	const STATUS_OPENED = 1;
	const STATUS_FINISHED = 2;

	public function selectManager($userID) {
		return $this
			->select(true)
			->where('tests.user_id = ?', $userID)
			;
	}

	public function selectDomain($domainID) {
		return $this
			//->setIntegrityCheck(false)
			->select(true)
			->join('users', 'tests.user_id = users.id', array())
			->where('domain_id = ?', $domainID)
			;
	}

	public static function getCountForUser($user_id)
	{
		$model = new self();

		$rowset = $model->getAdapter()->fetchRow("SELECT count(*) AS all_tests,
							 count(date_opened) AS opened_tests,
							 count(date_closed) AS closed_tests
							 FROM tests WHERE user_id=$user_id");

		$wynik=array();
		foreach($rowset AS $k=>$v) $wynik[$k]=$v;
		return $wynik;
	}


}
