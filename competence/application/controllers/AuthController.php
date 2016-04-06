<?php

require_once 'CompetenceController.php';

class AuthController extends CompetenceController
{
    /**
     * @var Zend_Session_Namespace
     */
    protected $_auth;

    /**
     * @var array
     */
    protected $_oauthScopes;

    public function init()
    {
        parent::init();

        $this->_auth = new Zend_Session_Namespace('auth');

        $this->_oauthOptions['callbackUrl'] = $this->_getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName();
        $this->_oauthScopes = Zend_Registry::get('oauth_scopes');
    }

    public function indexAction()
    {
        $this->_forward('open-id');
    }

    public function openIdAction()
    {
        try {
            $this->checkOpenId();
        } catch (Exception $e) {
            $this->clearAuth();
            $this->addError($this->view->translate('Failed to authenticate'));
            $this->_redirectExit('auth', 'error');
        }

        try {
            $this->checkDomain();
        } catch (Exception $e) {
            $this->clearAuth();
            $this->addError($this->view->translate('Failed to set domain information'));
            $this->_redirectExit('auth', 'error');
        }

        try {
            $this->checkUser();
        } catch (Exception $e) {
            $this->clearAuth();
            $this->addError($this->view->translate('Failed to set user information'));
            $this->_redirectExit('auth', 'error');
        }

        try {
            $this->checkAccessToken();
        } catch (Exception $e) {
            $this->clearAuth();
            $this->addError($this->view->translate('Failed to get user access token'));
            $this->_redirectExit('auth', 'error');
        }

        $this->_redirectExit('index', 'dashboard');
    }

    /**
     * @throws Exception
     */
    protected function checkOpenId()
    {
        if (!isset($this->_auth->OPENID['email'])) {
            require_once 'GN/LightOpenID.php';

            $openid = new LightOpenID($this->_getBaseUrl() . '/auth/open-id');
            $openid->identity = 'https://www.google.com/accounts/o8/id';
            $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email', 'pref/language');

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
                    $attributes = $openid->getAttributes();

                    $session = new Zend_Session_Namespace('language');
                    $session->preferredLanguage = $attributes['pref/language'];

                    list (, $identity) = explode('=', $openid->data['openid_identity']);

                    $this->_auth->OPENID = array(
                        'first_name' => $attributes['namePerson/first'],
                        'last_name' => $attributes['namePerson/last'],
                        'email' => strtolower($attributes['contact/email']),
                        'identity' => $identity,
                        'language' => $attributes['pref/language']
                    );

                    return true;
                }
            } else if ($this->_hasParam('email')) {

                $modelUser = new Model_Users();
                $user = $modelUser->fetchUser(strtolower($this->_getParam('email')));
                if (isset($user->identity)) {
                    $openid->identity .= '?id=' . $user->identity;
                    header('Location:  ' . $openid->authUrl(true));
                    exit;
                }
            }

            header('Location:  ' . $openid->authUrl());
            exit;
        }
    }

    /**
     * @throws Exception
     * @throws Zend_Gdata_App_Exception
     */
    protected function checkDomain()
    {
        if (!isset($this->_auth->DOMAIN['id'])) {

            list ($userLogin, $userDomain) = explode('@', strtolower($this->_auth->OPENID['email']));

            $modelDomain = new Model_Domains();
            $domain = $modelDomain->fetchDomain($userDomain);
            if ($domain == null || empty($domain->oauth_token)) {

                $accessToken = $this->getAccessToken('admin');
                $googleClient = new Zend_Gdata_Gapps($accessToken->getHttpClient($this->_oauthOptions), $userDomain);
                $googleUser = $googleClient->retrieveUser($userLogin);
                if (!($googleUser && $googleUser->login->admin)) {
                    throw new Zend_Gdata_App_Exception('You are not an application administrator');
                }
                // SIM try updating user to check whether Provisioning API is enabled
                $googleUser = $googleClient->updateUser($userLogin, $googleUser);

                if ($domain == null) {
                    // SIM fetch organization name
                    $entry = $googleClient->get('https://apps-apis.google.com/a/feeds/domain/2.0/' . $userDomain . '/general/organizationName');
                    $organization = new Zend_Gdata_Gapps_Extension_Property();
                    $organization->transferFromXML($entry->getBody());

                    $domain = $modelDomain->createRow();
                    $domain->domain_name = $userDomain;
                    $domain->org_name = $organization->getValue();
                    $domain->admin_email = $this->_auth->OPENID['email'];
                    $domain->create_date = date('c');
                    $domain->marketplace = isset($this->_auth->OPENID['marketplace']) ? 1 : 0;
                }

                if (empty($domain->oauth_token)) {
                    $domain->setAccessToken($accessToken);
                }

                $domain->save();
            }

            $this->_auth->DOMAIN = $domain->toArray();
        }
    }

    /**
     *
     */
    protected function checkUser()
    {
        $modelUsers = new Model_Users();
        $user = $modelUsers->fetchRow(array('email = ?' => strtolower($this->_auth->OPENID['email'])));
        if ($user == null) {
            $user = $modelUsers->createRow();
            $user->email = strtolower($this->_auth->OPENID['email']);
            $user->name = $this->_auth->OPENID['first_name'] . ' ' . $this->_auth->OPENID['last_name'];
            $user->role = $modelUsers::ROLE_STUDENT;
        }

        if (isset($this->_auth->OPENID['identity'])
        && (empty($user->identity) or $user->identity != $this->_auth->OPENID['identity'])
        ) {
            $user->identity = $this->_auth->OPENID['identity'];
        }

        if (empty($user->domain_id)) {
            $user->domain_id = $this->_auth->DOMAIN['id'];
        }

        // pierwszy user domeny = admin
        if ($modelUsers->fetchRow(array('domain_id = ?' => $this->_auth->DOMAIN['id'])) == null) {
            //pierwszy user ever = superadmin
            if ($modelUsers->fetchRow() == null) {
                $user->role = $modelUsers::ROLE_SUPER_ADMINISTRATOR;
            } else {
                $user->role = $modelUsers::ROLE_ADMINISTRATOR;
            }
            if (!isset($this->_auth->OPENID['marketplace'])) {
                // pierwszym user -> token domeny = token usera
                $user->setAccessToken($user->getDomain()->getAccessToken());
            }
        }
        $user->save();

        $this->user = $user;
        Zend_Auth::getInstance()->getStorage()->write($user->id);

        $this->rlogin($this->user->email);
    }

    protected function checkAccessToken()
    {
		if (!isset($this->_auth->OPENID['marketplace'])) {
			if ((($this->user->role == Model_Users::ROLE_SUPER_ADMINISTRATOR) or ($this->user->role == Model_Users::ROLE_ADMINISTRATOR)) and ($this->user->getAccessToken() == null)) {
				$this->user->setAccessToken($this->getAccessToken('admin'));
				$this->user->save();
			} elseif (($this->user->role == Model_Users::ROLE_TEACHER) and ($this->user->getAccessToken() == null)) {
				$this->user->setAccessToken($this->getAccessToken('teacher'));
				$this->user->save();
			}
        }
    }

    /**
     * @return Zend_Oauth_Token_Access
     */
	protected function getAccessToken($namespace = null)
    {
		if (empty($namespace)) {
			assert(!is_array($this->_oauthScopes));
			$scopes = $this->_oauthScopes;
		} else {
			assert(is_array($this->_oauthScopes));
			$scopes = $this->_oauthScopes[$namespace];
		}
        $consumer = new Zend_Oauth_Consumer($this->_oauthOptions);
        try {
            if ($this->_auth->REQUEST_TOKEN == null) {
                $this->_auth->REQUEST_TOKEN = $consumer->getRequestToken(array('scope' => $scopes));
                $consumer->redirect();
            }
            $accessToken = $consumer->getAccessToken($_REQUEST, $this->_auth->REQUEST_TOKEN);
            unset($this->_auth->REQUEST_TOKEN);
            return $accessToken;
        } catch (Exception $e) {
            unset($this->_auth->REQUEST_TOKEN);
            throw $e;
        }
    }

    public function logoutAction()
    {
        $this->clearAuth();
        Zend_Auth::getInstance()->clearIdentity();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array(
            'action' => 'index',
            'controller' => 'index'
        ));
        $this->rlogout($this->user->email, $redirect);
        $this->_redirect($redirect);
    }

    protected function clearAuth()
    {
        unset($this->_auth->OPENID,
              $this->_auth->DOMAIN,
              $this->_auth->REQUEST_TOKEN
        );
    }

    public function marketAction()
    {
        if (!isset($this->_auth->OPENID['email'])) {

            $BASE_URL   = $this->_getBaseUrl() . '/auth/market';
            $RETURN_URL = $BASE_URL . '?return=1';

            $store = new Auth_OpenID_FileStore(APPLICATION_PATH . '/cache');
            $consumer = new Auth_OpenID_Consumer($store);
            new Auth_OpenID_GoogleDiscovery($consumer);

            if ($this->_hasParam('return')) {
                $response = $consumer->complete($RETURN_URL);
                if ($response->status == Auth_OpenID_SUCCESS) {
                    $ax = new Auth_OpenID_AX_FetchResponse();
                    $attributes = $ax->fromSuccessResponse($response)->data;
                    $this->_auth->OPENID = array(
                        'first_name'  => $attributes['http://axschema.org/namePerson/first'][0],
                        'last_name'   => $attributes['http://axschema.org/namePerson/last'][0],
                        'email'       => strtolower($attributes['http://axschema.org/contact/email'][0]),
                        'marketplace' => true
                    );

                } else {
                    throw new Exception('OpenID validation failed');
                }
            } else if ($this->_hasParam('domain')) {
                $domain = $this->_getParam('domain');
                $auth = $consumer->begin($domain);
                $attributes = array(
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 2, 1),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, 1),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, 1),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language', 1, 1),
                );
                $ax = new Auth_OpenID_AX_FetchRequest();
                foreach ($attributes as $attr) {
                    $ax->add($attr);
                }
                $auth->addExtension($ax);
                header('Location: ' . $auth->redirectURL($BASE_URL, $RETURN_URL));
                exit;
            } else {
                throw new Exception('Invalid request. Please do not try it again');
            }
        }

        $this->_redirectExit('open-id');
    }
}
