<?php
class Model_UsersRow extends Zend_Db_Table_Row implements GN_Model_IDomainUser {
    protected $_domain;

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

	public function setAccessToken($token) {
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

		$t=unserialize(base64_decode($token));

		$oauth2=@$t->oauth2;
		
		if ($oauth2 && $t->get_expire()<time())
		{
		    
		    $opt=Zend_Registry::get('application_options');
		    $t2=GN_Goauth2::refreshAccessToken($opt['googleapps']['clientId'],$opt['googleapps']['clientSecret'],$t);
		    $this->setAccessToken($t2)->save();
		    return $t2;
		}
		
		return $t;
	}

	public function getTrialCount() {
		$domain = $this->getDomain();
		if ($domain->isSpecial()) {
			return $this->trial_count;
		}
		return $domain->trial_count;
	}
}
