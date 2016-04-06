<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class GN_Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * @var string
     */
    protected $_sessionNamespace;

    /**
     * @param Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     */
    public function __construct($application)
    {
        parent::__construct($application);

        $config = new Zend_Config($this->getOptions(), true);
        try {
            $localConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/local.ini');
            $config->merge($localConfig);
            $this->setOptions($config->toArray());
        } catch (Zend_Config_Exception $e) {
            throw new Exception('File /configs/local.ini not found. Create it, it can be empty.');
        }

        if (!empty($this->_sessionNamespace)) {
            Zend_Auth::getInstance()->setStorage(
                new Zend_Auth_Storage_Session(
                    Zend_Auth_Storage_Session::NAMESPACE_DEFAULT . '_' . $this->_sessionNamespace
                )
            );
        }
    }

    /**
     * @param array $options
     * @return GN_Bootstrap
     */
    public function setOptions(array $options)
    {
        parent::setOptions($options);
        Zend_Registry::set('application_options', $options);
        return $this;
    }

    /**
     * @return Zend_Cache_Core
     */
    protected function _initCache()
    {
        $cache = $this->bootstrap('cachemanager')->getResource('cachemanager')->getCache('default');
        Zend_Registry::set('cache', $cache);
        return $cache;
    }

    protected function _initCustomResources()
    {
        $view = $this->bootstrap('view')->getResource('view');
        $view->addHelperPath('GN/View/Helper', 'GN_View_Helper');
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _initDb()
    {
        $db = $this->getOption('db');
        $adapter = $db['adapter'];
        unset($db['adapter']);
        $db = Zend_Db::factory('pdo_' . $adapter, $db);
        $db->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Db_Table::setDefaultAdapter($db);
        Zend_Db_Table::setDefaultMetadataCache($this->bootstrap('cache')->getResource('cache'));
        Zend_Registry::set('db', $db);
        return $db;
    }

    /**
     * @return array
     */
    protected function _initApps()
    {
        $googleapps = $this->getOption('googleapps');
        Zend_Registry::set('googleapps', $googleapps);

        $oauthOptions = array(
            'consumerKey'           => $googleapps['consumerKey'],
            'consumerSecret'        => $googleapps['consumerSecret'],
            'signatureMethod'       => 'HMAC-SHA1',
            'requestTokenUrl'       => 'https://www.google.com/accounts/OAuthGetRequestToken',
            'userAuthorizationUrl'  => 'https://www.google.com/accounts/OAuthAuthorizeToken',
            'accessTokenUrl'        => 'https://www.google.com/accounts/OAuthGetAccessToken',
        );

        Zend_Registry::set('oauth_options', $oauthOptions);

	$apply = function($scopes) {
		$return = array();
		foreach (preg_split('/[ ,]+/', $scopes) as $scope) {
			if (empty($scope)) {
				continue;
			}
			if (!Zend_Uri::check($scope)) {
				$scope = 'https://apps-apis.google.com/a/feeds' . '/' . $scope . '/';
			}
			$return[] = $scope;
		}
		$return = implode(' ', $return);
		return $return;
	};

        $scopes = array();
	$scopes2 = array();
	if (is_array($googleapps['scopes'])) {
	    foreach ($googleapps['scopes'] as $namespace => $sub) {
		    $scopes[$namespace] = $apply($sub);
	    }
        } else {
	    $scopes = $apply($googleapps['scopes']);
	}
	
	if (isset ($googleapps['scopes2']) && is_array($googleapps['scopes2'])) {
	    foreach ($googleapps['scopes2'] as $namespace => $sub) {
		    $scopes2[$namespace] = $apply($sub);
	    }
        } else {
	    if (isset($googleapps['scopes2'])) $scopes2 = $apply($googleapps['scopes2']);
	}	
	
        Zend_Registry::set('oauth_scopes', $scopes);
	Zend_Registry::set('oauth_scopes2', $scopes2);

        return $oauthOptions;
    }

    /**
     * @return GN_Logger
     */
    protected function _initErrorHandler()
    {
        $logger = new GN_Logger();
        $logger->registerErrorHandler();
        Zend_Registry::set('logger', $logger);
        return $logger;
    }
}
