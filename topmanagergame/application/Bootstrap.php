<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Bootstrap extends GN_Bootstrap
{
    /**
     * @var string
     */
    protected $_sessionNamespace = 'topmanager';

    /**
     * @param Zend_Application|Zend_Application_Bootstrap_Bootstrapper $application
     */
    public function __construct($application)
    {
        $gameServerName = Game_Server::resolveName();
        $this->_sessionNamespace .= '_' . $gameServerName;

        parent::__construct($application);
    }

    public function _initCustomResources()
    {
        parent::_initCustomResources();

        $this->getResourceLoader()->addResourceTypes(array(
            'grid' => array(
                'namespace' => 'Grid',
                'path' => 'grids'
            )
        ));
    }

    /**
     * @return Zend_Translate
     * @throws Exception
     */
    public function _initLanguage()
    {
        $options = Zend_Registry::get('application_options');
        $languages = $options['languages'];

        $session  = new Zend_Session_Namespace('language');

        if (isset($_GET['lang']))
            $locale = $_GET['lang'];
        else if (isset($session->language))
            $locale = $session->language;

        if (isset($locale) == false || array_key_exists($locale, $languages) == false)
            $locale = current(array_keys($languages));

        $session->language = $locale;
        $locale = $languages[$locale];
        $locale = new Zend_Locale($locale);
        Zend_Registry::set('Zend_Locale', $locale);

        $path = APPLICATION_PATH . '/language/' . $locale->getLanguage();
        $translate = @new Zend_Translate(Zend_Translate::AN_ARRAY);
        $translate->addTranslation($path . '/lang.php', $locale);
        $translate->addTranslation($path . '/validate.php', $locale);

        Zend_Registry::set('Zend_Currency', new Zend_Currency);
        Zend_Registry::set('Zend_Translate', $translate);

        setlocale(LC_ALL, (string) $locale);

        return $translate;
    }

    protected function _initGameServer()
    {
        Game_Server::bootstrap();
    }

    /**
     * @return Zend_Db_Adapter_Abstract
     */
    protected function _initDb()
    {
        $this->bootstrap('gameServer');
        return Zend_Registry::get('db');
    }

    /**
     * @return GN_Plugin_Acl
     */
    protected function _initAcl()
    {
        $this->bootstrap('cache');
        $acl = new GN_Plugin_Acl;
        $acl->setRoleName(0);
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity())
            $acl->setRoleName($auth->getIdentity()->role);
        $this->bootstrap('frontcontroller')->getResource('frontcontroller')->registerPlugin($acl);
        return $acl;
    }
}
