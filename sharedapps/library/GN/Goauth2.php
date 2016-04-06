<?php
/**
 * @author <piotr.podstawski@gammanet.pl> Piotr Podstawski
 */

/**
 * @method getAccessToken
 * @method refreshAccessToken
 */
class GN_Goauth2 
{
    const TOKEN_ENDPOINT = 'https://accounts.google.com/o/oauth2/token';
    const AUTH_ENDPOINT = 'https://accounts.google.com/o/oauth2/auth';


    public static function getAccessToken($clientID,$clientSecret,$scope,$callback=null)
    {
	if (is_null($callback)) $callback='http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	
	if (isset($_GET['code']))
	{
	    $client = new Zend_Http_Client(self::TOKEN_ENDPOINT);
	    $client->setMethod('POST');
	    $client->setParameterPost('code',$_GET['code']);
	    $client->setParameterPost('redirect_uri',$callback);
	    $client->setParameterPost('client_id',$clientID);
	    $client->setParameterPost('client_secret',$clientSecret);
	    $client->setParameterPost('grant_type','authorization_code');
	    

	    $resp=$client->request();
	    
	    $t=json_decode($resp->getBody());
	    
	    if (isset($t->expires_in)) $t->expire=$t->expires_in+time();
	       
	    $token=new GN_Goauth2_Token();
	    $token->init_token($t->access_token,$t->refresh_token,$t->expire);
	    //die('<pre>'.print_r($token,1));	    
	    return $token;
	}
	else
	{
	    $url=self::AUTH_ENDPOINT.'?redirect_uri='.urlencode($callback).'&response_type=code&client_id='.$clientID.'&approval_prompt=force&scope='.urlencode($scope).'&access_type=offline'; 

	    Header('Location: '.$url);
	    die("<a href='$url'>$url</a>");
	}
	
    }
    
    
   
    public static function refreshAccessToken($clientID,$clientSecret,$token)
    {
	$client = new Zend_Http_Client(self::TOKEN_ENDPOINT);
	$client->setMethod('POST');
	$client->setParameterPost('refresh_token',$token->get_refresh_token());
	$client->setParameterPost('client_id',$clientID);
	$client->setParameterPost('client_secret',$clientSecret);
	$client->setParameterPost('grant_type','refresh_token');



	$resp=$client->request();
	
	
	
	$t=json_decode($resp->getBody());
	
	
	if (isset($t->expires_in)) $t->expire=$t->expires_in+time();
	
	$token2=new GN_Goauth2_Token();
	$token2->init_token($t->access_token,$token->get_refresh_token(),$t->expire);
	
	return $token2;	
	
    }
    
}
