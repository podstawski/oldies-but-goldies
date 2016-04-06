<?php

require 'RestController.php';
require_once 'GN/LightOpenID.php';

class AuthController extends RestController
{
    /**
     * @var Array
     */
    protected $oauthOptions;

    /**
     * @var Zend_Session_Namespace
     */
    protected $oauth;

    /**
     * @var Zend_Oauth_Consumer
     */
    protected $consumer;

    public function init()
    {
        parent::init();

        $this->oauthOptions = (array) Zend_Registry::get('oauth_options');
        $this->oauthOptions['callbackUrl'] = $this->_getBaseUrl() . '/auth/oauth';

        $this->oauth = new Zend_Session_Namespace('oauth');
    }
    
    public function indexAction()
    {
        $userData = Yala_User::getInstance()->getIdentity();
        if (array_key_exists('hello', $_GET)) {
            $this->setRestResponseAndExit($userData, self::HTTP_OK);
        } elseif (array_key_exists('rest',  $_GET)) {
            $postData = $this->getRequest()->getParams();
            if (!isset($postData['username']) || !isset($postData['password'])) {
                $this->setRestResponseAndExit('Username or password missing', self::HTTP_NOT_ACCEPTABLE);
            }
            Yala_User::setIdentity('admin');

            $userData = User::find_by_username($postData['username']);
            if (!$userData) {
                $this->setRestResponseAndExit(null, self::HTTP_UNAUTHORIZED);
            }
            $this->setRestResponseAndExit($this->_login($userData, $postData['password']), self::HTTP_OK);
        } elseif (isset($userData->id)) {
            $this->view->userData = $userData;
        } else {
            $flashMessenger = $this->getHelper('FlashMessenger');
            $form = new Form_Simple(new Zend_Config_Ini(APPLICATION_PATH . '/forms/login.ini'));
            if ($this->getRequest()->isPost() && $form->isValid($this->getRequest()->getPost())) {
                $postData = $form->getValues();
                Yala_User::setIdentity('admin');
                $userRow = User::find_by_username($postData['username']);
                if (!$userRow) {
                    $flashMessenger->addMessage('Nieprawidłowy login lub hasło');
                } else {
                    try {
                        $this->view->userData = $this->_login($userRow, $postData['password']);
                    } catch (Exception $e) {
                        $this->view->userData = null;
                        Yala_User::getInstance()->clearIdentity();
                        Yala_User::init();
                        Yala_User::setIdentity('own');
                        $flashMessenger->addMessage('Nieprawidłowy login lub hasło');
                    }
                }
                Yala_User::setIdentity('own');
            }
            $this->view->form = $form;
        }
        $this->renderScript('index/login.phtml');
    }

    public function logoutAction()
    {
        $email = Yala_User::getEmail();
        Zend_Session::forgetMe();
        Zend_Session::destroy();
        $this->rlogout($email);
        $this->setRestResponseAndExit(null, self::HTTP_OK);
    }

    public function openIdAction()
    {
        $openid = new LightOpenID($this->_getBaseUrl() . '/auth/open-id');
        $openid->identity = 'https://www.google.com/accounts/o8/id';
        $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');

        $_SESSION['OPENID_MODE'] = $this->getRequest()->getParam('mode');

        if (isset($_REQUEST['openid_ns'])) {
            if ($openid->mode === 'cancel') {
                throw new Exception('OpenID canceled');
            } elseif (!$openid->validate()) {
                if ($this->_hasParam('email')) {
                    header('Location:  ' . $openid->authUrl());
                    exit;
                }
                throw new Exception('OpenID validation failed');
            } else {
                $attribues = $openid->getAttributes();
                list (, $identity) = explode('=', $openid->data['openid_identity']);
                $_SESSION['OPENID'] = $openidData = array(
                    'first_name'    => $attribues['namePerson/first'],
                    'last_name'     => $attribues['namePerson/last'],
                    'email'         => $attribues['contact/email'],
                    'identity'      => $identity
                );
                if ($this->oauthOptions['enabled']) {
                    header('Location: ' . $this->oauthOptions['callbackUrl']);
                    exit;
                } else {
                    Yala_User::setIdentity('admin');
                    $userData = User::find_by_username(strtolower(array_pop(explode('@', $openidData['email']))));
                    if (!$userData) {
                        $userData = User::createUser($openidData);
                    }
                }
            }
        } else {
            $immediate = false;
            if ($this->_hasParam('email')) {
                list ($login, $domain) = explode('@', $email = $this->_getParam('email'));
                try {
                    Yala_User::init(null, $domain);
                    Yala_User::setIdentity('admin');
                    if ($userRow = User::find_by_username(strtolower($login))) {
                        if ($immediate = !empty($userRow->identity)) {
                            $openid->identity .= '?id=' . $userRow->identity;
                        }
                    }
                } catch (ActiveRecord\DatabaseException $e) {
                    Zend_Session::destroy();
                    header('Location:' . $this->_getBaseUrl() . '/auth/open-id');
                    exit;
                }
                Yala_User::setIdentity('own');
            }
            header('Location:  ' . $openid->authUrl($immediate));
            exit;
        }
    }

    public function oauthAction()
    {
        if (!$this->oauthOptions['enabled']) {
            throw new Exception('Google Apps are disabled');
        }

        if (!isset($_SESSION['OPENID'])) {
            throw new Exception('Please login using Google OpenID');
        }

        if ($this->oauth->renewToken !== true) {
            if ($this->_getParam('renew-access-token')
            &&  Yala_User::getRoleId() == Role::ADMIN
            ) {
                $this->oauth->unsetAll();
                $this->oauth->renewToken = true;
            } else if ($token = App::get_access_token_by_email($_SESSION['OPENID']['email'])) {
                $this->oauth->accessToken = $token;
            }
        }

        if ($this->oauth->accessToken == null) {
            $consumer = $this->getConsumer();
            if ($this->oauth->requestToken == null) {
                $this->oauth->requestToken = $consumer->getRequestToken(array('scope' => $this->getScopes()));
                header('Location: ' . $consumer->getRedirectUrl());
                exit;
            } else {
                $this->oauth->accessToken = $consumer->getAccessToken($_REQUEST, $this->oauth->requestToken);
            }
        }

        $this->_login(
            $this->prepareDbAndUser()
        );
    }

    /**
     * @return Zend_Oauth_Consumer
     */
    protected function getConsumer()
    {
        if ($this->consumer == null) {
            $this->consumer = new Zend_Oauth_Consumer($this->oauthOptions);
        }

        return $this->consumer;
    }

    /**
     * @param string $scope
     * @return string
     */
    protected function getScope($scope)
    {
        $scopeBase = 'https://apps-apis.google.com/a/feeds';
        return $scopeBase . '/' . trim($scope) . '/';
    }

    /**
     * @return string
     */
    protected function getScopes()
    {
        $scopes = array();
        foreach (explode(',', $this->oauthOptions['scopes']) as $scope) {
            $scopes[] = $this->getScope($scope);
        }
        return implode(' ', $scopes);
    }

    /**
     * @return User
     */
    protected function prepareDbAndUser()
    {
        $openidData  = $_SESSION['OPENID'];

        list ($login, $domain) = explode('@', $openidData['email']);
        $dbname = Yala_User::getDbname($domain);

        $app = App::find_by_domain($domain);

        $httpClient = $this->oauth->accessToken->getHttpClient($this->oauthOptions);
        $gApps = new Zend_Gdata_Gapps($httpClient, $domain);

        try {
            $gAppsUser = $gApps->retrieveUser($login);
        } catch (Zend_Gdata_App_HttpException $e) {
            if ($app && $e->getResponse()->getStatus() == self::HTTP_UNAUTHORIZED) {
                $this->oauth->tokenInvalid = true;
            }
            $gAppsUser = null;
        }

        if (!$app)
        {
            if (!$gAppsUser || !$gAppsUser->login->admin) {
                throw new Exception('You are not a google domain administrator');
            }

            if (!$this->oauthOptions['opened']) {
                throw new Exception('Sorry. Registering new domains is disabled');
            }

            Yala_User::setIdentity('yala');

            if ($this->oauthOptions['singledb'] == false) {
                try {
                    ActiveRecord\Model::connection()->query('CREATE DATABASE ' . $dbname);

                    $connection = Yala_User::getConnections();
                    $connection = explode('/', $connection['yala']);
                    array_pop($connection);
                    array_push($connection, $dbname);
                    $connection = implode('/', $connection);

                    require_once APPLICATION_PATH . '/../library/Doctrine/Doctrine.php';
                    spl_autoload_register(array('Doctrine', 'autoload'));
                    $connection = Doctrine_Manager::connection($connection);
                    $migration = new Doctrine_Migration(APPLICATION_PATH . '/migrations/classes', $connection);
                    $migration->setTableName('doctrine_migration_version');
                    $migration->migrate();
                } catch (Exception $e) {
                    ActiveRecord\Model::connection()->query('DROP DATABASE IF EXISTS ' . $dbname);
                    Yala_User::setIdentity('own');
                    throw $e;
                }
            }

            $app = new App();
            $app->domain = $domain;
            $app->set_access_token($this->oauth->accessToken);
            $app->save();
        }

        if ($this->oauthOptions['singledb'] == true) {
            $login = $openidData['username'] = strtolower(GN_User::cleanString($openidData['email']));
        }

        $roleId = Role::USER;
        if ($gAppsUser && $gAppsUser->login->admin) {
            $roleId = Role::ADMIN;
            if ($this->oauth->renewToken === true) {
                $app->set_access_token($this->oauth->accessToken);
                $app->save();
                unset($this->oauth->renewToken);
            }
        }

        Yala_User::init(null, $domain);
        Yala_User::setIdentity('admin');

        $userRow = User::find_by_username(strtolower($login));
        if (!$userRow) {
            $openidData['is_google'] = 1;
            $userRow = User::createUser($openidData, $roleId);
        }

        if (isset($openidData['identity'])
        && (empty($userRow->identity) or $userRow->identity != $openidData['identity'])
        ) {
            $userRow->identity = $openidData['identity'];
            $userRow->save();
        }

        Yala_User::setIdentity('own');

        return $userRow;
    }

    /**
     * @param User $userRow
     * @param string $password
     * @return void
     */
    protected function _login(User $userRow, $password = false)
    {
        Yala_User::getInstance()->clearIdentity();
        $userData = $userRow->to_array();
        if ($password === false) {
            $userData['plain_password'] = User::generatePassword($userData['email']);
        } else {
            $userData['plain_password'] = $password;
        }

        Yala_User::init($userData);
        Yala_User::setIdentity('own');
        Zend_Session::rememberMe();

        if ($password === false) {
            $url = $this->_getBaseUrl(true);
            if ($url[strlen($url) - 1] != '/') {
                $url .= '/';
            }
            require_once 'Browser.php';
            $browser = new Browser();
            if ($browser->getBrowser() == Browser::BROWSER_IE) {
                $url .= 'index.html';
            } else if (APPLICATION_ENV === 'development') {
                $url .= 'source';
            }
            $this->rlogin($userData['email']);
            header('Location: ' . $url);
            exit;
        }

        $userRow->reload();

        return $userData;
    }

    /**
     * @return bool
     */
	protected function checkRemoteLogin()
    {
        return (isset($this->oauthOptions['remote_login']) && !empty($this->oauthOptions['remote_login'])
             && isset($this->oauthOptions['json_link']) && !empty($this->oauthOptions['json_link'])
        );
    }

    /**
     * @param string $email
     * @param string $rlogoutAction
     * @param string $rlogoutController
     */
	protected function rlogin($email, $rlogoutAction = 'rlogout', $rlogoutController = 'auth')
    {
        if ($this->checkRemoteLogin()) {
            $logoutUrl = sprintf('%s/%s/%s?mail=%s&sig=%s&sid=%s',
                $this->_getBaseUrl(),
                $rlogoutController,
                $rlogoutAction,
                $email,
                GN_User::getSig($email, $this->oauthOptions['json_hash']),
                session_id()
            );
            GN_Gapps::login($logoutUrl, $email, $this->oauthOptions['json_link'], $this->oauthOptions['json_hash']);
        }
    }

    /**
     * @param string $email
     * @param string $redirect
     */
    protected function rlogout($email, $redirect = '')
    {
        if ($this->checkRemoteLogin()) {
            GN_Gapps::logout($redirect, $email, $this->oauthOptions['json_link'], $this->oauthOptions['json_hash'], $this->oauthOptions['google_logout']);
        }
    }

    public function rlogoutAction()
    {
        if ($this->checkRemoteLogin()) {
            $params = $this->_request->getParams();
            $sig = GN_User::getSig($params['mail'], $this->oauthOptions['json_hash']);
            if ($sig == $params['sig'])
                GN_Gapps::remoteLogout($params['sid']);
        }
        die();
    }
}