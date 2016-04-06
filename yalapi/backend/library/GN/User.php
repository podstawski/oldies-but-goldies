<?php

class GN_User
{
    /**
     * @var Zend_Auth
     */
    protected static $auth;

    /**
     * @var Zend_Oauth_Token_Access
     */
    protected static $accessToken;

    public static function init()
    {
        if (self::$auth === null) {
            self::$auth = Zend_Auth::getInstance();
        }
    }

    /**
     * @static
     * @return mixed|null
     */
    public static function getIdentity()
    {
        return self::$auth->getIdentity();
    }

    /**
     * @static
     * @param Model_UserRow $userRow
     */
    public static function setIdentity(Model_UserRow $userRow)
    {
        if (!$userRow->email) {
            throw new InvalidArgumentException('Email required');
        }

        list (, $domain) = explode('@', $userRow->email);
        $identity = (object) $userRow->toArray();
        $identity->domain = $domain;

        self::$accessToken = null;
        self::$auth->getStorage()->write($identity);
    }

    /**
     * @static
     */
    public static function clearIdentity()
    {
        self::$auth->clearIdentity();
    }

    /**
     * @static
     * @param string $name
     * @return mixed
     */
    public static function get($name)
    {
        return self::getIdentity()->{$name};
    }

    /**
     * @static
     * @return Zend_Oauth_Token_Access
     */
    public static function getAccessToken()
    {
        if (self::$accessToken === null) {
            $domainModel = new Model_Domain();
            $domainRow = $domainModel->find(self::get('domain_id'))->current();
            self::$accessToken = $domainRow->getAccessToken();
        }
        return self::$accessToken;
    }

    /**
     * @static
     * @param string $text
     * @return string
     */
    public static function cleanString($text, $unpolish = false)
    {
        if ($unpolish) {
            $text = GN_Validate_Gapps_String::unpolish($text);
        }
        return preg_replace('/[^a-zA-Z0-9]/', '_', $text);
    }

    /**
     * @static
     * @param string $email
     * @param string $hash
     * @return string
     */
    public static function getSig($email, $hash)
    {
        return md5($email . $hash);
    }
}

