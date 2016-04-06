<?php
class Model_DomainsRow extends GN_Model_DomainRow {
	public function getGappsClient() {
		return new Zend_Gdata_Gapps(
				$this->getHttpClient(),
				$this->domain_name
				);
	}

	public function getHttpClient($getRealToken = false /* even if we change token in active record, get real */) {
		$token = $this->getAccessToken($getRealToken);
		if (($token === null) or ($token === false)) {
			return null;
		}
		return $token->getHttpClient(Zend_Registry::get('oauth_options'));
	}

	public function getAccessToken($getRealToken = false) {
		if ($getRealToken) {
			$token = $this->getTable()->find($this->id)->current()->token;
		} else {
			$token = $this->oauth_token;
		}
		return unserialize(base64_decode($token));
	}

	public function isSpecial() {
		return CRM_Misc::isSpecialDomain($this->domain_name);
	}

	public function getUsers() {
		$modelUsers = new Model_Users();
		return $modelUsers->getByDomainID($this->id);
	}

	public function allUsersDisabled() {
		$users = $this->getUsers();
		if (!count($users)) {
			return false;
		}
		foreach ($users as $user) {
			if (!$user->disabled) {
				return false;
			}
		}
		return true;
	}

	public function resetAccessToken() {
		if ($this->marketplace) {
			$this->disabled = 'NOW()';
		}
		$this->oauth_token = null;
		$this->save();
	}

	public function confirmAccessToken() {
		if ($this->marketplace and $this->disabled) {
			$this->disabled = null;
			$this->save();
		}
	}
}
