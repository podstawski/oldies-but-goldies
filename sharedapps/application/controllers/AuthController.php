<?php
require_once 'AbstractController.php';

class ProvisioningAPIException extends Zend_Gdata_App_Exception { }

class AuthController extends AbstractController
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
			$this->checkUser1();
			$this->checkDomain();
			$this->checkUser2();
		/*} catch (ProvisioningAPIException $e) {
			$this->clearAuth();
			$this->_redirectExit('provisioning-api', 'index');*/
		} catch (Exception $e) {
			$this->clearAuth();
			$this->addError('misc_auth_error', $e->getFile(), $e->getLine(), $e->getMessage(), $e->getTraceAsString());
		}

        if (isset($this->_auth->DISPLAY_MARKETPLACE_WELCOME)) {
            unset($this->_auth->DISPLAY_MARKETPLACE_WELCOME);
            $this->_redirectExit('market-welcome', 'index');
        } else {
            $this->_redirectExit('index', 'index');
        }
    }

    /**
     * @throws Exception
     */
    protected function checkOpenId()
    {
		$debug = $this->getInvokeArg('bootstrap')->getOption('debug');
		if (isset($debug['user_email'])) {
			$this->_auth->OPENID = array(
				'first_name' => @$debug['user_first_name'],
				'last_name' => @$debug['user_last_name'],
				'email' => @$debug['user_email'],
				'marketplace' => @$debug['marketplace'],
			);
		}

        if (!isset($this->_auth->OPENID['email'])) {

            $action = $this->_request->getActionName();
            if (isset($this->_auth->MARKETPLACE))
                $action = 'market';

            $BASE_URL = $this->view->absoluteUrl(array('controller' => $this->_request->getControllerName(), 'action' => $action), null, true);

            $store = new Auth_OpenID_FileStore(APPLICATION_PATH . '/cache');
            $consumer = new Auth_OpenID_Consumer($store);

            if ($this->_hasParam('email'))
                list (, $domain) = explode('@', $this->_getParam('email'));
            else if ($this->_hasParam('domain'))
                $domain = $this->_getParam('domain');

            @new Auth_OpenID_GoogleDiscovery($consumer);

            if ($this->_hasParam('openid_ns')) {
                $response = $consumer->complete($BASE_URL);
                list (, $identity) = explode('?id=', $response->getDisplayIdentifier());
                switch ($response->status) {
                    case Auth_OpenID_SUCCESS:
                        $ax = new Auth_OpenID_AX_FetchResponse();
                        $attributes = $ax->fromSuccessResponse($response)->data;
                        $this->_auth->OPENID = array(
                            'first_name' => $attributes['http://axschema.org/namePerson/first'][0],
                            'last_name' => $attributes['http://axschema.org/namePerson/last'][0],
                            'email' => strtolower($attributes['http://axschema.org/contact/email'][0]),
                            'identity' => $identity,
                            'marketplace' => isset($this->_auth->MARKETPLACE),
                        );
                        break;

                    case Auth_OpenID_CANCEL:
                        $error = 'OpenID validation canceled';
                        break;

                    case Auth_OpenID_FAILURE:
			$error = 'OpenID validation failed' . print_r($response, true);
                        break;

                    case Auth_OpenID_SETUP_NEEDED:
                        $error = 'OpenID validation setup needed';
                        break;

                    case Auth_OpenID_PARSE_ERROR:
                        $error = 'OpenID validation parse error';
                        break;

                    default:
                        $error = 'OpenID unknown error';
                        break;
                }

                if (isset($error)) {
                    $this->addError($error);
                    $this->_redirectExit('index', 'index');
                }
            } else {

                if (isset($domain))
                    $auth = $consumer->begin($domain);
                else
                    $auth = $consumer->beginWithoutDiscovery(Auth_OpenID_ServiceEndpoint::fromOPEndpointURL('https://www.google.com/accounts/o8/ud'));

                $attributes = array(
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/contact/email', 2, 1, 'email'),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/first', 1, 1, 'firstname'),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/namePerson/last', 1, 1, 'lastname'),
                    Auth_OpenID_AX_AttrInfo::make('http://axschema.org/pref/language', 1, 1, 'language'),
                );

                $ax = new Auth_OpenID_AX_FetchRequest();
                foreach ($attributes as $attr) {
                    $ax->add($attr);
                }
                $auth->addExtension($ax);

                header('Location: ' . $auth->redirectURL($BASE_URL, $BASE_URL));
                exit;
            }
        }

    }

	protected function checkUser1() {
		$modelUsers = new Model_Users();
		$user = $modelUsers->fetchRow(array('email = ?' => strtolower($this->_auth->OPENID['email'])));
		if ($user !== null) {
			$this->user = $user;
			Zend_Auth::getInstance()->getStorage()->write($user->id);

			$this->login();
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
			$userLogin = strtolower($userLogin);
			$userDomain = strtolower($userDomain);

			$modelDomain = new Model_Domains();
			$domain = $modelDomain->fetchDomain($userDomain);

			if ($domain == null) {
				$domain = $modelDomain->createRow();
				$domain->domain_name = $domain->org_name = $userDomain;
				if (!$domain->isSpecial())
					$domain->admin_email = $this->_auth->OPENID['email'];
				$domain->create_date = date('c');
				$domain->marketplace = (int) $this->_auth->OPENID['marketplace'];
				if ($domain->marketplace) {
					$this->_auth->DISPLAY_MARKETPLACE_WELCOME = true;
				}
			}

			if ((!$domain->marketplace) and ((int) $this->_auth->OPENID['marketplace'])) {
				$domain->marketplace = 1;
				$domain->oauth_token = null;
				$domain->save();
			}

			if ((!$domain->marketplace) and (!@$_SESSION['force-personal-domain']) and (!$domain->isSpecial()) and empty($domain->oauth_token)) {
				$accessToken = $this->getAccessToken('domain-full');
				$googleClient = new Zend_Gdata_Gapps($accessToken->getHttpClient($this->_oauthOptions), $domain->domain_name);
				/*try {
					$googleUser = $googleClient->retrieveUser($userLogin);
					// SIM try updating user to check whether Provisioning API is enabled
					$googleUser = $googleClient->updateUser($userLogin, $googleUser);
				} catch (Exception $e) {
					$googleUser = null;
				}
				if ((!$googleUser) or (!$googleUser->login->admin)) {
					$_SESSION['domain-name'] = $userDomain;
					throw new ProvisioningAPIException('You are not an application administrator');
				}

				// SIM fetch organization name
				$entry = $googleClient->get('https://apps-apis.google.com/a/feeds/domain/2.0/' . $domain->domain_name . '/general/organizationName');
				$organization = new Zend_Gdata_Gapps_Extension_Property();
				$organization->transferFromXML($entry->getBody());*/

				$domain->setAccessToken($accessToken);
				$_SESSION['domain-just-created'] = true;
				//$domain->org_name = $organization->getValue();
			}

			$domain->save();

			$this->_auth->DOMAIN = $domain->toArray();
		}
	}

	protected function checkUser2()
	{
		$newuser=false;
		$modelUsers = new Model_Users();
		$user = $this->user;
		if ($user == null) {
			$user = $modelUsers->createRow();
			$user->email = strtolower($this->_auth->OPENID['email']);
			$user->name = $this->_auth->OPENID['first_name'] . ' ' . $this->_auth->OPENID['last_name'];
			$user->role = $modelUsers::ROLE_USER;
			$user->referer = GN_SessionCache::get('referrer');
			$newuser = true;
		}

		if (isset($this->_auth->OPENID['identity'])
				&& (empty($user->identity) or $user->identity != $this->_auth->OPENID['identity'])
		   ) {
			$user->identity = $this->_auth->OPENID['identity'];
		}

		if (empty($user->domain_id)) {
			$user->domain_id = $this->_auth->DOMAIN['id'];
		}

		if (!$user->getDomain()->isSpecial() and !(@$_SESSION['force-personal-domain'])) {
			// pierwszy user domeny = admin
			if ($modelUsers->fetchRow(array('domain_id = ?' => $this->_auth->DOMAIN['id'])) == null) {
				//pierwszy user ever = superadmin
				if ($modelUsers->fetchRow() == null) {
					$user->role = $modelUsers::ROLE_SUPER_ADMINISTRATOR;
				} else {
					$user->role = $modelUsers::ROLE_ADMINISTRATOR;
				}
			}
		}
		if (!$this->_auth->DOMAIN['marketplace']) {
			if ($user->getAccessToken() == null) {
				if (@$_SESSION['domain-just-created'] and $user->getDomain()->getAccessToken() != null) {
					$user->setAccessToken($user->getDomain()->getAccessToken());
				} elseif ($user->getDomain()->isSpecial()) {
					$user->setAccessToken($this->getAccessToken('personal'));
				} else {
					$user->setAccessToken($this->getAccessToken('domain-light'));
				}
			}
		}
		if ($user->save())
		{
		    if ($newuser)
		    {
			$this->user = $user;
			$this->initObserver();
			if ($this->observer) $this->observer->observe('welcome',1,$this->_auth->OPENID);
		    }
		}
		unset($_SESSION['domain-just-created']);
		unset($_SESSION['force-personal-domain']);

		$this->user = $user;
		Zend_Auth::getInstance()->getStorage()->write($user->id);

		$this->login();
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

    protected function login()
    {
        $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
        $email = $this->user->email;
        $sig = GN_User::getSig($email, $googleapps['json_hash']);
        $logout_url = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array('controller' => 'auth', 'action' => 'rlogout', 'mail' => $email, 'sig' => $sig, 'sid' => session_id()), 'default', true);
        GN_Gapps::login($logout_url, $email, $googleapps['json_link'], $googleapps['json_hash']);
    }

    public function rlogoutAction()
    {
        $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
        $params = $this->_request->getParams();
        $sig = GN_User::getSig($params['mail'], $googleapps['json_hash']);

        if ($sig == $params['sig']) GN_Gapps::remoteLogout($params['sid']);
        die();
    }

    public function logoutAction()
    {
		GN_SessionCache::delete('autocomplete');
		GN_SessionCache::delete('folders' . $this->user->id);
		GN_SessionCache::delete('contacts' . $this->user->id);
		GN_SessionCache::delete('referrer');
        $googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
        $email = $this->user->email;
        $redirect = $this->view->url(array('controller' => 'index', 'action' => 'index'));

        $this->clearAuth();
        Zend_Auth::getInstance()->clearIdentity();
        GN_Gapps::logout($redirect, $email, $googleapps['json_link'], $googleapps['json_hash']);
        $this->_redirectExit('index', 'index');
    }

    protected function clearAuth()
    {
        $this->_auth->unsetAll();
//        unset($this->_auth->OPENID,
//        $this->_auth->DOMAIN,
//        $this->_auth->REQUEST_TOKEN
//        );
    }

	public function fakeUserAction() {
		header('Content-Type: application/json');
		$ret = false;
		if (isset($_SESSION['fake-user'])) {
			unset($_SESSION['fake-user']);
		}
		if ($this->realUser and $this->realUser->role == Model_Users::ROLE_SUPER_ADMINISTRATOR) {
			$userEmail = null;
			if (isset($_GET['user-email'])) {
				$userEmail = $_GET['user-email'];
			}
			$modelUsers = new Model_Users();
			$user = $modelUsers->getByEmail($userEmail);
			$ret = !empty($user);
			$_SESSION['fake-user'] = $user->email;
		}
		echo json_encode($ret);
		die;
	}

    public function marketAction()
    {
        $this->_auth->MARKETPLACE = true;
        $this->_forward('open-id');
    }
}
