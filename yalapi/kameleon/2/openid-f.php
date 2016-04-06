<?php


function handleError($errno, $errstr, $errfile, $errline, array $errcontext)
{
	if ($errno==2 && isset($errcontext['url']) )
	{
		echo "<script> location.href='".$errcontext['url']."';</script>";
		return true;
	}


	return false;

}



	
require_once(dirname(__FILE__).'/socialOauth.php');

class social extends socialOauth
{
	public $app_id='237840856238338';
	public $app_secret='2fcee50636fea04b11fbc1fd254f6c22';

	private $__url;


	public function __construct($url)
	{
		$this->__url=$url;
		$ret = parent::__construct('facebook');
		

		return $ret;
	}


	public function getSettings($settings=false)
	{

		$ret=parent::getSettings($settings);
		
		$ret['facebook']['consumer_key'] = $this->app_id;
		$ret['facebook']['consumer_secret'] = $this->app_secret;
		$ret['facebook']['callback_url'] = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];



		return $ret;
	}




	public function getUrl()
	{
		set_error_handler('handleError',E_WARNING);
		return $this->login();
	}
}
