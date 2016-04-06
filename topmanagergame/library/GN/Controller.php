<?php

abstract class GN_Controller extends Zend_Controller_Action
{
    const REDIRECT_BACK = 1;

    /**
     * @var Zend_Db_Adapter_Abstract
     */
    protected $_db;

    /**
     * @var Zend_Controller_Action_Helper_FlashMessenger
     */
    protected $_flashMessenger;

    /**
     * @var Zend_View_Helper_Translate
     */
    protected $_translator;

    /**
     * @var array
     */
    protected $_inputFilters = array();

    /**
     * @var array
     */
    protected $_inputValidators = array();

    /**
     * @var array
     */
	protected $_oauthOptions;

	/**
     * @var Zend_Session_Namespace
     */
	protected $_oauth;

    /**
     * @var array
     */
	protected $_googleapps;

    public function init()
    {
        $this->_flashMessenger = $this->getHelper('FlashMessenger');

        if (Zend_Registry::isRegistered('Translator')) {
            $this->_translator = Zend_Registry::get('Translator');
        } else if (Zend_Registry::isRegistered('Zend_Translate')) {
            Zend_Registry::set(
                'Translator',
                $this->_translator = new Zend_View_Helper_Translate(
                    Zend_Registry::get('Zend_Translate')
                )
            );
        }

        if (Zend_Registry::isRegistered('oauth_options')) {
		    $this->_oauthOptions = Zend_Registry::get('oauth_options');
        }

        if (Zend_Registry::isRegistered('googleapps')) {
            $this->_googleapps = Zend_Registry::get('googleapps');
        } else {
            $this->_googleapps = $this->getInvokeArg('bootstrap')->getOption('googleapps');
        }

        $this->_oauth = new Zend_Session_Namespace('oauth');

        if (Zend_Registry::isRegistered('db')) {
            $this->_db = Zend_Registry::get('db');
        } else {
            $this->_db = Zend_Db_Table::getDefaultAdapter();
        }

        if ($this->_db)
            $this->_db->setFetchMode(Zend_Db::FETCH_OBJ);
    }

    /**
     * @return Zend_Filter_Input
     */
    protected function _filterInput($paramNames)
    {
        $input = new Zend_Filter_Input($this->_inputFilters, $this->_inputValidators);
        $data = array();
        foreach (func_get_args() as $paramName) {
            $data[$paramName] = $this->_getParam($paramName);
        }
        if ($data) {
            $input->setData($data);
        }
        return $input;
    }

    protected function _flash($message, $namespace = 'default')
    {
        if (is_array($message)) {
            $message = call_user_func_array(
                array(
                     $this->_translator,
                     'translate'
                ), $message
            );
        } else {
            $message = $this->_translator->translate($message);
        }
        $this->_flashMessenger
             ->setNamespace($namespace)
             ->addMessage($message);
    }

    protected function _redirectBack()
    {
        $this->_redirectUrlExit($_SERVER['HTTP_REFERER']);
    }

    protected function _redirectExit($action, $controller = null, array $params = array())
    {
		$this->_helper->redirector->gotoSimpleAndExit($action, $controller, null, $params);
    }

    protected function _redirectUrlExit($url)
    {
        $this->_helper->redirector->gotoUrlAndExit($url);
    }

    protected function _getBaseUrl()
    {
        $secure = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 's' : '';
        return "http$secure://" . $_SERVER['HTTP_HOST'] . $this->_request->getBaseUrl();
    }

	protected $gApps = array();

    protected function getGappsClient($domain = null)
    {
        $domainRow = null;
        if ($domain instanceof Model_DomainRow) {
            $domainRow = $domain;
            $domain = $domainRow->domain_name;
        } else {
            if (!$domain || !is_string($domain)) {
                $domain = Yala_User::get('domain');
            }
            $domainModel = new Model_Domain();
            $domainRow = $domainModel->fetchRow(array('domain_name = ?' => $domain));
        }

        if (!$domainRow) {
            throw new Zend_Gdata_App_Exception('Coult not resolve domain');
        }

        if (!array_key_exists($domain, $this->gApps)) {
            $accessToken = $domainRow->getAccessToken();
            $httpClient = $accessToken->getHttpClient($this->_oauthOptions);
            $this->gApps[$domain] = new Zend_Gdata_Gapps($httpClient, $domain);
        }
        
        return $this->gApps[$domain];
    }

    /**
     * @return bool
     */
	protected function checkRemoteLogin()
    {
        return (isset($this->_googleapps['remote_login']) && !empty($this->_googleapps['remote_login'])
             && isset($this->_googleapps['json_link']) && !empty($this->_googleapps['json_link'])
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
            $logoutUrl = 'http://' . $_SERVER['HTTP_HOST'] . $this->view->url(array(
                'action' => $rlogoutAction,
                'controller' => $rlogoutController,
                'mail' => $email,
                'sig' => GN_User::getSig($email, $this->_googleapps['json_hash']),
                'sid' => session_id()
            ), null, true);
            GN_Gapps::login($logoutUrl, $email, $this->_googleapps['json_link'], $this->_googleapps['json_hash']);
        }
    }

    /**
     * @param string $email
     * @param string $redirect
     */
    protected function rlogout($email, $redirect = '')
    {
        if ($this->checkRemoteLogin()) {
            GN_Gapps::logout($redirect, $email, $this->_googleapps['json_link'], $this->_googleapps['json_hash'], $this->_googleapps['google_logout']);
        }
    }

    public function rlogoutAction()
    {
        if ($this->checkRemoteLogin()) {
            $params = $this->_request->getParams();
            $sig = GN_User::getSig($params['mail'], $this->_googleapps['json_hash']);
            if ($sig == $params['sig'])
                GN_Gapps::remoteLogout($params['sid']);
        }
        die();
    }
}