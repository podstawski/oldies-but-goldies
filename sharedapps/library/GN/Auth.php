<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Auth
{
    /**
     * @var string
     */
    public static $SESSION_NAMESPACE = 'OPENID';

    /**
     * @var array
     */
    public static $attributes = array(
        'first_name' => 'namePerson/first',
        'last_name' => 'namePerson/last',
        'email' => 'contact/email'
    );

    /**
     * @var string
     */
    public static $identity;

    /**
     * @return string
     */
    protected static function getBaseURL()
    {
        $request = Zend_Controller_Front::getInstance()->getRequest();
        return GN_Tools::getBaseURL() . '/' . $request->getControllerName() . '/' . $request->getActionName();
    }

    /**
     * @return Zend_Session_Namespace
     * @throws Exception
     */
    public static function authorize()
    {
        $session = new Zend_Session_Namespace(self::$SESSION_NAMESPACE);

        if (isset($session->email))
            return $session;

        require_once 'GN/LightOpenID.php';

        $lightOpenID = new LightOpenID(self::getBaseURL());
        $lightOpenID->identity = 'https://www.google.com/accounts/o8/id';
        $lightOpenID->required = array_values(self::$attributes);

        if (isset($_REQUEST['openid_ns']))
        {
            if ($lightOpenID->mode === 'cancel')
                throw new Exception('OpenID canceled');

            if ($lightOpenID->validate() == false)
                throw new Exception('OpenID validation failed');

            list (, $identity) = explode('=', $lightOpenID->data['openid_identity']);

            $attributes = $lightOpenID->getAttributes();

            foreach (self::$attributes as $key => $name)
                $session->$key = $attributes[$name];

            $session->identity = $identity;

            return $session;
        }

        if (self::$identity)
            $lightOpenID->identity .= '?id=' . self::$identity;

        header('Location:  ' . $lightOpenID->authUrl(!!self::$identity));
        exit;
    }

    public static function forget()
    {
        $session = new Zend_Session_Namespace(self::$SESSION_NAMESPACE);
        $session->unsetAll();
    }

    /**
     * @param string $callbackUrl
     * @return Zend_Oauth_Token_Access
     */
    public static function getToken($callbackUrl = null)
    {
        $openidData = self::authorize();

        $scopes  = Zend_Registry::get('oauth_scopes');
        $options = Zend_Registry::get('oauth_options');
        $options['callbackUrl'] = $callbackUrl ?: self::getBaseURL();

        $consumer = new Zend_Oauth_Consumer($options);
        try {
            if ($openidData->REQUEST_TOKEN == null) {
                $openidData->REQUEST_TOKEN = $consumer->getRequestToken(array('scope' => $scopes));
                $consumer->redirect();
            }
            $accessToken = $consumer->getAccessToken($_REQUEST, $openidData->REQUEST_TOKEN);
            unset($openidData->REQUEST_TOKEN);
            return $accessToken;
        } catch (Exception $e) {
            unset($openidData->REQUEST_TOKEN);
            throw $e;
        }
    }
}