<?php

require_once 'GN/LightOpenID.php';
require_once 'Zend/Oauth/Consumer.php';
require_once 'MillionaireController.php';

class MillionaireAuthController extends MillionaireController
{
    protected $_openIdReturnUrl = '';
    protected $_logoutReturnUrl = '';

    public function init()
    {
		parent::init();
        $this->_oauthOptions['callbackUrl'] = $this->_getBaseUrl() . '/auth/token';
    }

    public function indexAction()
    {
    	$this->_forward('open-id');
	}

	public function openIdAction()
    {
        if ($this->user) {
            $this->_redirectExit('index', 'gra');
        }

        $returnUrl = $this->_getBaseUrl() . '/' . $this->getRequest()->getControllerName() . '/' . $this->getRequest()->getActionName();

        $openid = new LightOpenID($returnUrl);
        $openid->identity = 'https://www.google.com/accounts/o8/id';
        $openid->required = array('namePerson/first', 'namePerson/last', 'contact/email');

        $this->ZendSession->OPENID_MODE = $this->getRequest()->getParam('mode');

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

                if (isset($this->_googleapps['verification']) && !empty($this->_googleapps['verification'])) {
                    $verification = json_decode(
                        file_get_contents(
                            sprintf(
                                $this->_googleapps['verification'],
                                $attributes['contact/email'],
                                GN_User::getSig(
                                    $attributes['contact/email'],
                                    $this->_googleapps['json_hash']
                                )
                            )
                        ),
                        true
                    );
					if (!(isset($verification['status']) && $verification['status'])) {
                        $this->_redirect('/brak-dostepu');
                    }
                }

                list (, $identity) = explode('=', $openid->data['openid_identity']);

                $this->ZendSession->OPENID = array(
                    'first_name'    => $attributes['namePerson/first'],
                    'last_name'     => $attributes['namePerson/last'],
                    'email'         => strtolower($attributes['contact/email']),
                    'identity'      => $identity
                );

                $this->rlogin(strtolower($attributes['contact/email']));

                $this->_redirect($this->_openIdReturnUrl);
            }
        }

        $immediate = false;
		if ($this->_hasParam('email')) {
			$email = $this->_getParam('email');
            $modelUser = new Model_User();
			$user = $modelUser->findByMail($email);
            if ($user) {
				if ($immediate = !empty($user->identity)) {
                    $openid->identity .= '?id=' . $user->identity;
                }
            } else {
                Zend_Session::destroy();
                header('Location: ' . $returnUrl);
                exit;
            }
        }
        header('Location:  ' . $openid->authUrl($immediate));
        exit;
    }
	
    public function tokenAction()
    {
        if ($this->user == null) {
            $this->_redirectExit('index', 'index');
        }

        if (!$this->user->getAccessToken()) {
            $this->ZendSession->RETURN_URL = $_SERVER['HTTP_REFERER'];
            $consumer = new Zend_Oauth_Consumer($this->_oauthOptions);
            if ($this->ZendSession->REQUEST_TOKEN == null) {
                $this->ZendSession->REQUEST_TOKEN = $consumer->getRequestToken(array('scope' => $this->getScopes()));
                $consumer->redirect();
                exit;
            } else {
                $accessToken = $consumer->getAccessToken($_REQUEST, $this->ZendSession->REQUEST_TOKEN);
                unset($this->ZendSession->REQUEST_TOKEN);
                $this->user->setAccessToken($accessToken);
                $this->user->save();
            }
        }
        $returnUrl = $this->ZendSession->RETURN_URL;
        unset($this->ZendSession->RETURN_URL);
        $this->_redirect($returnUrl);
	}

    /**
     * @return string
     */
    protected function getScopes()
    {
        $scopes = array();
        foreach (explode(',', $this->_googleapps['scopes']) as $scope) {
            $scopes[] = $this->getScope($scope);
        }
        return implode(' ', $scopes);
    }

    /**
     * @param string $scope
     * @return string
     */
    protected function getScope($scope)
    {
        if (Zend_Uri::check($scope)) {
            return $scope;
        }
        return Zend_Gdata_Gapps::APPS_BASE_FEED_URI . '/' . trim($scope) . '/';
    }

    public function loginAction()
    {
    }

    public function logoutAction()
    {
        $email = $this->ZendSession->OPENID['email'];
		// Zend_Session::destroy(true, false);
		$this->ZendSession->unsetAll();
        $redirect = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array(
            'action' => 'index',
            'controller' => 'index'
        ), 'default', true);
		$this->rlogout($email, $redirect);
		$this->_redirect($redirect);
    }
}
