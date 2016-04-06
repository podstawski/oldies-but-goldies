<?php
	
require_once(dirname(__FILE__).'/openid.php');


class social extends LightOpenID
{
	function getUrl()
	{
		$this->identity = 'http://me.yahoo.com';
		$this->required = array( 'namePerson/first','namePerson/last','contact/email');
		return $this->authUrl();
	}
}
