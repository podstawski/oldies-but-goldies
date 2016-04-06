<?php

require_once 'GN/LightOpenID.php';

class ProvisioningAPIException extends Zend_Gdata_App_Exception { }

class AuthController extends GN_Controller
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
        if (isset($this->_auth->MARKETPLACE) == false)
            $this->_auth->MARKETPLACE = false;

//        $this->_oauthOptions['callbackUrl'] = $this->_getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName();
        $this->_oauthOptions['callbackUrl'] = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url();

        $this->messenger = $this->view->messenger = new ClassGroup_Messenger();
    }

    public function indexAction()
    {
        $this->_forward('open-id');
    }

	public function openIdAction()
	{
		try {
			$this->checkOpenId();
			$this->checkDomain();
			$this->checkUser();
		} catch (ProvisioningAPIException $e) {
			$this->clearAuth(false);
			$this->addError($this->view->translate('provisioning_api_error'));
		} catch (Exception $e) {
			$this->clearAuth(false);
			$this->addError($e->getMessage());
		}
		$this->_redirectExit('index', 'index');
	}

    /**
     * @throws Exception
     */
    protected function checkOpenId()
    {
        if (!isset($this->_auth->OPENID['email'])) {

            $action = $this->_request->getActionName();
            if ($this->_auth->MARKETPLACE)
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
                switch ($response->status)
                {
                    case Auth_OpenID_SUCCESS:
                        $ax = new Auth_OpenID_AX_FetchResponse();
                        $attributes = $ax->fromSuccessResponse($response)->data;
                        $this->_auth->OPENID = array(
                            'first_name'  => $attributes['http://axschema.org/namePerson/first'][0],
                            'last_name'   => $attributes['http://axschema.org/namePerson/last'][0],
                            'email'       => strtolower($attributes['http://axschema.org/contact/email'][0]),
                            'identity'    => $identity,
                            'marketplace' => $this->_auth->MARKETPLACE ? 1 : 0
                        );
                        break;

                    case Auth_OpenID_CANCEL:
                        $error = 'OpenID validation canceled';

                    case Auth_OpenID_FAILURE:
                        $error = 'OpenID validation failed';

                    case Auth_OpenID_SETUP_NEEDED:
                        $error = 'OpenID validation setup needed';

                    case Auth_OpenID_PARSE_ERROR:
                        $error = 'OpenID validation parse error';

                    default:
                        if (!isset($error))
                            $error = 'OpenID unknown error';

                        $this->addError($error);
                        $this->_redirectExit('index', 'index');
                        break;
                }
            } else {

                if (isset($domain))
                    $auth = $consumer->begin($domain);
                else
                    $auth = $consumer->beginWithoutDiscovery(Auth_OpenID_ServiceEndpoint::fromOPEndpointURL('https://www.google.com/accounts/o8/ud'));

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

                header('Location: ' . $auth->redirectURL($BASE_URL, $BASE_URL));
                exit;
            }
        }
    }

	/**
	 * @throws Exception
	 * @throws Zend_Gdata_App_Exception
	 */
	protected function checkDomain()
	{
		if (!isset($this->_auth->DOMAIN['id'])) {

			list ($userLogin, $userDomain) = explode('@', $this->_auth->OPENID['email']);

			$modelDomain = new Model_Domain();
			$domain = $modelDomain->fetchDomain($userDomain);
			if ($domain == null || empty($domain->oauth_token)) {
				if ($domain == null) {
					$domain = $modelDomain->createRow();
					$domain->domain_name = $userDomain;
					$domain->admin_email = $this->_auth->OPENID['email'];
					$domain->marketplace = $this->_auth->OPENID['marketplace'];
					$domain->create_date = date('c');
				}

                $accessToken = $this->getAccessToken();
				$googleClient = new Zend_Gdata_Gapps($accessToken->getHttpClient($this->_oauthOptions), $domain->domain_name);
				try {
					$googleUser = $googleClient->retrieveUser($userLogin);
					// SIM try updating user to check whether Provisioning API is enabled
					$googleUser = $googleClient->updateUser($userLogin, $googleUser);
				} catch (Exception $e) {
					$googleUser = null;
				}
				if ((!$googleUser) or (!$googleUser->login->admin)) {
					$_SESSION['non-admin'] = true;
					//throw new ProvisioningAPIException('You are not an application administrator');
				} else {
					unset($_SESSION['non-admin']);
					// SIM fetch organization name
					$entry = $googleClient->get('https://apps-apis.google.com/a/feeds/domain/2.0/' . $userDomain . '/general/organizationName');
					$organization = new Zend_Gdata_Gapps_Extension_Property();
					$organization->transferFromXML($entry->getBody());
					$domain->org_name = $organization->getValue();
					if (empty($domain->oauth_token)) {
						$domain->setAccessToken($accessToken);
					}
				}

				$domain->save();
			} else {
				$googleClient = new Zend_Gdata_Gapps($domain->getAccessToken()->getHttpClient($this->_oauthOptions), $userDomain);
				$googleUser = $googleClient->retrieveUser($userLogin);
				if (!($googleUser && $googleUser->login->admin)) {
					$_SESSION['non-admin'] = true;
					//throw new Zend_Gdata_App_Exception('You are not an application administrator');
				}

				if ($domain->marketplace == 0 && $this->_auth->OPENID['marketplace']) {
					$domain->marketplace = 1;
					$domain->save();
				}
			}

			$this->_auth->DOMAIN = $domain->toArray();
		}
	}

	protected function checkUser()
	{
		$modelUser = new Model_User();
		$user = $modelUser->fetchRow(array('email = ?' => $this->_auth->OPENID['email']));
		if ($user == null) {
			$user = $modelUser->createRow();
			$user->email = $this->_auth->OPENID['email'];
			$user->name  = $this->_auth->OPENID['first_name'] . ' ' . $this->_auth->OPENID['last_name'];
		}
		if (empty($user->domain_id)) {
			$user->domain_id = $this->_auth->DOMAIN['id'];
		}
		if (isset($this->_auth->OPENID['identity'])
		&& (empty($user->identity) or $user->identity != $this->_auth->OPENID['identity'])
		) {
		    $user->identity = $this->_auth->OPENID['identity'];
		}
		$user->admin = empty($_SESSION['non-admin']) ? '1' : '0';
		$user->save();

		$this->user = $user;
		Zend_Auth::getInstance()->getStorage()->write($this->_auth->OPENID['email']);

		$this->rlogin($this->user->email);
	}

    /**
     * @return Zend_Oauth_Token_Access
     */
    protected function getAccessToken()
    {
        $scopes = Zend_Registry::get('oauth_scopes');
        if ($this->_auth->MARKETPLACE)
            $this->_oauthScopes = $scopes['less'];
        else
            $this->_oauthScopes = $scopes['more'];

        $consumer = new Zend_Oauth_Consumer($this->_oauthOptions);
        try {
            if ($this->_auth->REQUEST_TOKEN == null) {
                $this->_auth->REQUEST_TOKEN = $consumer->getRequestToken(array('scope' => $this->_oauthScopes));
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

    protected function clearAuth($clearMarketplace = true)
    {
        unset($this->_auth->OPENID,
              $this->_auth->DOMAIN,
              $this->_auth->REQUEST_TOKEN
        );

        if ($clearMarketplace)
            unset($this->_auth->MARKETPLACE);
    }

    public function marketAction()
    {
        $this->_auth->MARKETPLACE = true;
        $this->_forward('open-id');
    }

    /**
	 * @param string $name
	 * @param array $arguments
	 */
    public function __call($name, $arguments)
    {
        if (substr($name, 0, 3) == 'add') {
            $message = $arguments[0];
            $type = strtolower(substr($name, 3));
            if ($type == 'error') {
                $this->view->errors = true;
            }
            if (in_array($type, ClassGroup_Messenger::$messageTypes)) {
                $this->messenger->addMessage($message, $type);
            }
        }
    }
}
