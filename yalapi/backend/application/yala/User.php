<?php

class Yala_User extends Zend_Auth
{
    const ROLE_ADMIN = 1;

    /**
     * @var Array
     */
    public static $dbOptions;

    /**
     * @var Array
     */
    protected static $identities;

    /**
     * @var Array
     */
    protected static $connections;

    /**
     * @var string
     */
    protected static $current;

    public static function init(array $userData = null, $domain = null)
    {
        if ($userData) {
            $identity = $userData;
            $identity['password'] = $userData['plain_password'];
            unset($userData['plain_password']);
        } else {
            $identity = (array) self::getInstance()->getIdentity();
        }

        if ($domain) {
            $identity['domain'] = $domain;
        } elseif (array_key_exists('domain', $identity)) {
            $domain = $identity['domain'];
        } elseif (array_key_exists('is_google', $identity) && $identity['is_google']) {
            list (, $domain) = explode('@', $identity['email']);
            $identity['domain'] = $domain;
        }

        self::$identities = array(
            'own' => $identity,
            'admin' => array(
                'username' => self::$dbOptions['username'],
                'password' => self::$dbOptions['password'],
                'email'    => null,
                'role_id'  => self::ROLE_ADMIN,
                'domain'   => $domain
            )
        );
        self::$connections = null;
        ActiveRecord\Config::instance()->set_connections(self::getConnections());
        // SIM this is the solution to acl wrong doings...
        Acl::reestablish_connection();
        self::setIdentity('own');
    }

    public static function getConnections()
    {
        if (self::$connections == null) {
            $domain = @self::$identities['own']['domain'];
            $dbname = self::getDbname($domain);
            if (isset(self::$identities['own']['username']) && self::$identities['own']['username'] === self::$dbOptions['username']) {
                $dbusername = self::$dbOptions['username'];
            } else {
                $dbusername = $dbname . '_' . @self::$identities['own']['username'];
            }
            self::$connections = array(
                'own' => sprintf('%s://%s:%s@%s/%s',
                    self::$dbOptions['adapter'],
                    $dbusername,
                    @self::$identities['own']['password'],
                    self::$dbOptions['host'],
                    $dbname
                ),
                'admin' => sprintf('%s://%s:%s@%s/%s',
                    self::$dbOptions['adapter'],
                    @self::$identities['admin']['username'],
                    @self::$identities['admin']['password'],
                    self::$dbOptions['host'],
                    $dbname
                ),
                'yala' => sprintf('%s://%s:%s@%s/%s',
                    self::$dbOptions['adapter'],
                    @self::$identities['admin']['username'],
                    @self::$identities['admin']['password'],
                    self::$dbOptions['host'],
                    self::$dbOptions['dbname']
                ),
            );
        }
        return self::$connections;
    }

    public static function setIdentity($name)
    {
        ActiveRecord\Config::instance()->set_default_connection($name);
        // SIM drop connection to force model to reestablish new connection
        ActiveRecord\ConnectionManager::drop_connection($name);
        if ($name == 'yala') {
            $name = 'admin';
        }
        $identity = (object) self::$identities[self::$current = $name];
        self::getInstance()->getStorage()->write($identity);
        return $identity;
    }

    public static function updateIdentity(array $userData, $name = 'own')
    {
        self::$identities[$name] = $userData;
        if (self::$current == $name) {
            self::getInstance()->getStorage()->write((object) $userData);
        }
    }

    public static function getDbOptions()
    {
        return self::$dbOptions;
    }

    /**
     * @static
     * @return Zend_Oauth_Token_Access|null
     */
    public static function getAccessToken()
    {
        return App::get_access_token_by_email(
            self::getEmail()
        );
    }

    /**
     * @static
     * @return null|Zend_Gdata_Gapps
     */
    public static function getGappsClient()
    {
        if ($accessToken = self::getAccessToken()) {
            $gappsClient = new Zend_Gdata_Gapps(
                $accessToken->getHttpClient(Zend_Registry::get('oauth_options')),
                self::getDomain()
            );
            return $gappsClient;
        }
        return null;
    }

    public static function getUsername()
    {
        return static::_getValue('username');
    }

    public static function getPassword()
    {
        return static::_getValue('password');
    }

    public static function getUid()
    {
        return static::_getValue('id');
    }

    public static function getRoleName()
    {
        return static::_getValue('role');
    }

    public static function getRoleId()
    {
        return static::_getValue('role_id');
    }

    public static function loggedIn()
    {
        return static::getInstance()->hasIdentity();
    }

    public static function getEmail()
    {
        return static::_getValue('email');
    }

    public static function getIsGoogle()
    {
        return static::_getValue('is_google');
    }

    public static function getDomain()
    {
        return static::_getValue('domain');
    }

    private static function _getValue($key)
    {
        $identity = (array) static::getInstance()->getIdentity();

        if (array_key_exists($key, $identity)) {
            return $identity[$key];
        }
        return null;
    }

    public static function cleanString($text)
    {
        return preg_replace('/[^a-zA-Z0-9]/', '_', $text);
    }

    public static function getDbname($domain = null)
    {
        $googleapps = Zend_Registry::get('oauth_options');
        if ($googleapps['singledb'] == false && $domain) {
            $dbname = self::cleanString($domain);
            if (!empty(self::$dbOptions['prefix'])) {
                $dbname = self::$dbOptions['prefix'] . '_' . $dbname;
            }
            return $dbname;
        }
        return self::$dbOptions['dbname'];
    }

    /**
     * @static
     * @param null $domain
     * @return Zend_Db_Adapter_Abstract
     */
    public static function getZendAdapter($domain = null)
    {
        $db = self::$dbOptions;
        $adapter = $db['adapter'];
        unset($db['adapter'], $db['prefix']);
        $db['dbname'] = self::getDbname($domain);
        return Zend_Db::factory('pdo_' . $adapter, $db);
    }
}
