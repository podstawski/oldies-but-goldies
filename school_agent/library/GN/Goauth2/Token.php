<?php
/**
 * @author <piotr.podstawski@gammanet.pl> Piotr Podstawski
 */

/**
 * @method Zend_Oauth_Token_Access getToken
 * @method GN_GClient_HttpClient setToken
 */
class GN_Goauth2_Token extends Zend_Oauth_Token_Access
{
    
    public function init_token($access_token,$refresh_token,$expire)
    {
        $this->oauth2=true;
        $this->refresh_token=$refresh_token;
        $this->access_token=$access_token;
        $this->expire=$expire;
    }
    
    public function getHttpClient(array $oauthOptions, $uri = null, $config = null, $excludeCustomParamsFromHeader = true)
    {
        $client = new GN_GClient_HttpClient($oauthOptions, $uri, $config, $excludeCustomParamsFromHeader);
        $client->setToken($this);
        return $client;
    }    
    
    public function toHeader($url, Zend_Oauth_Config_ConfigInterface $config, array $customParams = null, $realm = null)
    {     
        return 'OAuth '.$this->access_token;
    }
    
    
    public function get_refresh_token()
    {
        return $this->refresh_token;
    }
    
    public function get_access_token()
    {
        return $this->access_token;
    }
    
    public function get_expire()
    {
        return $this->expire;
    }
    
    public function set_access_token($access_token)
    {
        $this->access_token=$access_token;
        return $this;
    }
}