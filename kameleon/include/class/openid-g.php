<?php
	

	
require_once(dirname(__FILE__).'/openid.php');

class social extends LightOpenID
{
	function getUrl()
	{
		$this->identity = 'https://www.google.com/accounts/o8/id';
		$this->required = array( 'namePerson/first','namePerson/last','contact/email');
		return $this->authUrl();
	}
}
