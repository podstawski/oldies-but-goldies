<?

class GN_Facebook {

	const URL = 'https://graph.facebook.com';

	public static function getAccessToken($appId,$ret,$scope)
	{
		$url=self::URL."/oauth/authorize?type=user_agent&client_id=".$appId;
		$url.='&redirect_uri='.urlencode($ret);
		$url.='&scope='.$scope;

		Header('Location: '.$url);
		die($url);		
	}
	
	public static function aboutMe($access_token)
	{
		
		$url=self::URL."/me?access_token=".$access_token;
		return json_decode(file_get_contents($url));
	}
	
	public static function commentPost($access_token,$post_id,$message)
	{
		$url=self::URL."/".$post_id.'/comments?access_token='.$access_token;
		
		return self::post($url,array('message'=>$message));
	}

	public function publishPost($access_token,$link,$message)
	{
		$url=self::URL."/me/feed?access_token=".$access_token;
	
		return self::post($url,array('link'=>$link,'message'=>$message));
	}
	
	
	protected static function post($url,$data,$method='POST')
	{
		$client = new Zend_Http_Client($url);
		$client->setMethod($method);
		
		$client->setParameterPost($data);

		
		$resp=$client->request();
		
		return json_decode($resp->getBody());
		
	}
}
