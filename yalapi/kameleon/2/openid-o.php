<?php
	
require_once(dirname(__FILE__).'/openid.php');

class social extends LightOpenID
{
	public function getUrl()
	{
		$this->identity = 'https://www.myopenid.com';
		$this->required = array( 'contact/email');
		return $this->authUrl();
	}
}