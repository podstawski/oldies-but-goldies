<?php
class CRM_Mail extends Zend_Mail_Message {
	protected $_raw;
	public function __construct(array $params) {
		if (isset($params['raw'])) {
			$this->_raw = $params['raw'];
		}
		parent::__construct($params);
	}

	public function getRaw() {
		return $this->_raw;
	}
}

