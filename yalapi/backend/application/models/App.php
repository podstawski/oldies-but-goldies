<?php

class App extends AclModel
{
    static $table_name = 'apps';
    static $connection = 'yala';

    public function set_access_token(Zend_Oauth_Token_Access $token)
    {
        $this->assign_attribute('token', base64_encode(serialize($token)));
    }

    public function get_access_token()
    {
        return unserialize(base64_decode($this->read_attribute('token')));
    }

    public static function get_access_token_by_domain($domain)
    {
        if ($app = self::find_by_domain($domain)) {
            return $app->get_access_token();
        }
        return null;
    }

    /**
     * @static
     * @param string $email
     * @return Zend_Oauth_Token_Access|null
     */
    public static function get_access_token_by_email($email)
    {
        list (, $domain) = @explode('@', $email);
        return self::get_access_token_by_domain($domain);
    }
}