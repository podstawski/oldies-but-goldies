<?php
	
require_once(dirname(__FILE__).'/openid.php');

class social extends LightOpenID
{
	public function getUrl()
	{
		$this->identity = 'https://www.google.com/accounts/o8/id';
		$this->required = array( 'contact/email');
		return $this->authUrl();
	}
}