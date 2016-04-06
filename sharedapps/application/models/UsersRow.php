<?php
class Model_UsersRow extends Zend_Db_Table_Row implements GN_Model_IDomainUser {
    protected $_domain;

    /**
     * @var int
     */
    public $fee_type;

	public function getDomain() {
		if ($this->_domain == null) {
			$this->_domain = $this->findParentRow('Model_Domains');
		}
		return $this->_domain;
	}

	public function getEmail() {
		return $this->email;
	}

	public function getGappsClient() {
		return new Zend_Gdata_Gapps(
				$this->getHttpClient(),
				$this->getDomain()->domain_name
				);
	}

	public function getHttpClient($getRealToken = false /* even if we change token in active record, get real */) {
		$token = $this->getAccessToken($getRealToken);
		if (($token === null) or ($token === false)) {
			return null;
		}
		return $token->getHttpClient(Zend_Registry::get('oauth_options'));
	}

	public function getRoleName() {
		return Model_Users::getRoleName($this->role);
	}

	public function setAccessToken(Zend_Oauth_Token_Access $token) {
		$this->token = base64_encode(serialize($token));
		return $this;
	}

	public function getAccessToken($getRealToken = false) {
		if ($getRealToken) {
			$token = $this->getTable()->find($this->id)->current()->token;
		}
		else {
			$token = $this->token;
		}
		return unserialize(base64_decode($token));
	}

	public function resetAccessToken() {
		$modelUsers = new Model_Users();
		$row = $modelUsers->getByID($this->id); #hack kiedy operujemy na joinowanych wierszach

		if (!empty($this->token)) {
			$row->token = $this->token = null;
		}

		$row->disabled = $this->disabled = 'NOW()';
		$row->save();
	}

	public function confirmAccessToken() {
		$modelUsers = new Model_Users();
		if ($this->disabled) {
			$row = $modelUsers->getByID($this->id); #hack kiedy operujemy na joinowanych wierszach
			$row->disabled = null;
			$row->save();

			$modelUserContactGroups = new Model_UserContactGroups();
			foreach ($modelUserContactGroups->getByUserID($this->id) as $userContactGroup) {
				$userContactGroup->fetch_all = 1;
				$userContactGroup->save();
			}
		}
	}

	public function isTester() {
		$bootstrap = Zend_Controller_Front::getInstance()->getParam('bootstrap');
		$miscOptions = $bootstrap->getOption('misc');
		$testers = $miscOptions['testers'];
		$testers = strtolower($testers);
		$testers = str_replace(',', ';', $testers);
		$testers = explode(';', $testers);
		return in_array(strtolower($this->email), $testers);
	}
}
