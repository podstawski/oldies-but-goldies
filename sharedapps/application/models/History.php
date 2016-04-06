<?php
class Model_History extends Model_Abstract {
	protected $_name = 'history';
    protected $_rowClass = 'Model_HistoryRow';

	public function getByUserIDWithLimit($userID, $keyFilter, $limit = null) {
		if ($limit === null) {
			$options = Zend_Registry::get('application_options');
			$limit = $options['sharedapps']['history_limit'];
		}
		$select = $this
			->select(true)
			->where('user_id = ?', $userID)
			->where('STRPOS(LOWER(key), LOWER(?)) > 0', $keyFilter)
			->order('date DESC')
			->limit($limit)
			;
		return $this->fetchAll($select);
	}
}
