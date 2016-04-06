<?php
	
require_once(dirname(__FILE__).'/openid.php');


class social extends LightOpenID
{
	function getUrl()
	{
		$this->identity = 'https://openid.wp.pl';
		$this->required = array( 'namePerson/first','namePerson/last','contact/email');
		return $this->authUrl();
	}
}
