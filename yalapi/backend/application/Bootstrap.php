<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initLocalConfig()
    {
        $globalConfig = new Zend_Config($this->getOptions(), true);
        try {
            $localConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/local.ini');
            $globalConfig->merge($localConfig);
            $globalConfig = $globalConfig->toArray();
			$this->setOptions($globalConfig);
        } catch (Zend_Config_Exception $e) {
            throw new Exception('File /configs/local.ini not found. Create it, it can be empty.');
        }
    }

    protected function _initCustomResources()
    {
        $this->getResourceLoader()->addResourceType('report', 'report_templates/classes', 'Report');
        $this->getResourceLoader()->addResourceType('yala', 'yala', 'Yala');
    }

    protected function _initGoogleApps()
    {
        $googleapps = $this->getOption('googleapps');
        Zend_Registry::set('oauth_options', $googleapps);
        return $googleapps;
    }

    protected function _initPHPActiveRecord()
    {
        require_once APPLICATION_PATH . '/../library/php-activerecord/ActiveRecord.php';

//        Zend_Session::destroy();die('die in file ' . __FILE__ . ' at line ' . __LINE__);

        $this->bootstrap('localconfig');
        $dbOptions = $this->getOption('db');

        ActiveRecord\Config::initialize(function($cfg) {
            $cfg->set_model_directory(APPLICATION_PATH . '/models');
        });

        Yala_User::$dbOptions = $dbOptions;
        Yala_User::init();

        require_once APPLICATION_PATH . '/../library/php-activerecord/lib/Serialization.php';

        ActiveRecord\Serialization::$DATETIME_FORMAT = 'd-m-Y';
    }

    protected function _initRoutes()
    {
        /**
         * @var $front Zend_Controller_Front
         */
        $front = $this->bootstrap('frontController')->getResource('frontController');
        $router = $front->getRouter();
        $route = new Zend_Rest_Route($front);
        $router->addRoute('default', $route);

        // RB route for AMF gateway
        $route = new Zend_Controller_Router_Route(
            'quiz_service.php',
            array(
                'controller' => 'quiz',
                'action'     => 'scores'
            )
        );
        $router->addRoute('quiz_amf', $route);

        $route = new Zend_Controller_Router_Route(
            'question_service.php',
            array(
                'controller' => 'quiz',
                'action'     => 'questions'
            )
        );
        $router->addRoute('question_amf', $route);

        $route = new Zend_Controller_Router_Route(
            'questions/update',
            array(
                'controller' => 'quiz',
                'action'     => 'update-questions'
            )
        );
        $router->addRoute('question_update', $route);


        //RB add simple routes
        foreach ($this->_getRoutes() as $alias => $routeParams) {
            $router->addRoute($alias, new Zend_Controller_Router_Route($alias, $routeParams));
        }
    }

    protected function _initAcl()
    {
        require_once APPLICATION_PATH.'/configs/AclRules.php';
        $this->bootstrap('PHPActiveRecord');
        Acl::after_startup();
        /* RB registering shutdown function, becase we're using die() after sending response, so front-controller's
         * dispatchLoopShutdown isn't invoked - can't use plugin though. */
        register_shutdown_function(array('Acl', 'before_end'));
    }

    protected function _getRoutes()
    {
        return array(
            'auth/open-id' => array(
                'controller' => 'auth',
                'action' => 'open-id'
            ),
            'auth/oauth' => array(
                'controller' => 'auth',
                'action' => 'oauth'
            ),
            'auth/rlogout' => array(
                'controller' => 'auth',
                'action' => 'rlogout'
            ),
            'login' => array(
                'controller' => 'auth',
                'action' => 'index',
            ),
            'logout' => array(
                'controller' => 'auth',
                'action' => 'logout',
            ),
            'apps.php' => array(
                'controller' => 'auth',
                'action' => 'open-id',
            ),
            'register' => array(
                'controller' => 'users',
                'action' => 'register',
            ),
            'remind_password' => array(
                'controller' => 'users',
                'action' => 'remind-password',
            ),
            'www/:action' => array(
                'controller' => 'www',
            ),
            'www/szkolenia.php' => array(
                'controller' => 'www',
                'action' => 'list'
            ),
            'acl/recreate' => array(
                'controller' => 'acl',
                'action' => 'recreate'
            ),
            'google-apps/:action' => array(
                'controller' => 'google-apps',
            ),
            'gapi/:action' => array(
                'controller' => 'gapi'
            ),
            'test/:action' => array(
                'controller' => 'test'
            ),
        );
    }

    protected function _initTranslatorAndLocale()
    {
        try {
            $translator = new Zend_Translate(
                array(
                    'adapter' => 'array',
                    'content' => APPLICATION_PATH . '/resources/languages/pl_utf8.php',
                )
            );
            $translator->addTranslation(
                array(
                    'adapter' => 'array',
                    'content' => APPLICATION_PATH . '/resources/languages/Zend_Validate.php'
                )
             );
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if (APPLICATION_ENV === 'production') {
            $cache = $this->bootstrap('cachemanager')
                          ->getResource('cachemanager')
                          ->getCache('default');

            $translator->setCache($cache);

            Zend_Date::setOptions(array('cache' => $cache));
        }


        Zend_Validate_Abstract::setDefaultTranslator($translator);
        Zend_Form::setDefaultTranslator($translator);
        //RB storing translator for view
        Zend_Registry::set('Zend_Translate', $translator);
        //SIM this translator can translate strings with replacements;
        //Zend_Registry::get('Translator')->translate('%s to lamer', 'simer') = 'simer to lamer';
        Zend_Registry::set('Translator', new Zend_View_Helper_Translate($translator));
        //SIM for currency formatter
        Zend_Registry::set('Zend_Currency', new Zend_Currency('pl_PL'));
        Zend_Registry::set('Zend_Locale', new Zend_Locale('pl_PL'));

        Zend_Date::setOptions(array('format_type' => 'php'));

        return $translator;
    }

    protected function _initErrorHandler()
    {
        $logger = new GN_Logger();
        $logger->registerErrorHandler();
        Zend_Registry::set('logger', $logger);
        return $logger;
    }
}

