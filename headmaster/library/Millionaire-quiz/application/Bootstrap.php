<?php

class MillionaireBootstrap extends GN_Bootstrap
{
    protected function _initApps()
    {
        parent::_initApps();

        $googleapps = $this->getOption('googleapps');

        Zend_Registry::set('apps_url', $googleapps['appsUrl']);
		Zend_Registry::set('app', $this->getOption('app'));
    }

    protected function _initTranslatorAndLocale()
    {
        try {
			$translator = new Zend_Translate(
			    array(
			        'adapter' => 'gettext',
			        'content' => APPLICATION_PATH . '/resources/languages/pl.mo',
			        'locale'  => 'pl'
			    )
			);
        } catch (Exception $e) {
            die($e->getMessage());
        }

        if (APPLICATION_ENV === 'production') {
            $cache = $this->bootstrap('cache')->getResource('cache');
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

    protected function _initRoutes()
    {
        /**
         * @var $front Zend_Controller_Front
         */
        $front = $this->bootstrap('frontController')->getResource('frontController');
        $router = $front->getRouter();

        foreach ($this->_getRoutes() as $alias => $routeParams) {
            $router->addRoute($alias, new Zend_Controller_Router_Route($alias, $routeParams));
        }
    }

    protected function _getRoutes()
    {
        return array(
            'demo' => array(
                'controller' => 'gra',
				'action' => 'demo',
            )
        );
    }

}

