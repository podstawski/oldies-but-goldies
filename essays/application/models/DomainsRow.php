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
		
		$t=unserialize(base64_decode($token));
		
		$oauth2=@$t->oauth2;
		
		if ($oauth2 && $t->get_expire()<time())
		{
			$opt=Zend_Registry::get('application_options');
			$t2=GN_Goauth2::refreshAccessToken($opt['googleapps']['clientId'],$opt['googleapps']['clientSecret'],$t);

			$this->oauth_token=base64_encode(serialize($t2));
			$this->save();
			return $t2;
		}		
		
		return $t;
	}

	public function isSpecial() {
		return Essays_Misc::isSpecialDomain($this->domain_name);
	}
}
?>
