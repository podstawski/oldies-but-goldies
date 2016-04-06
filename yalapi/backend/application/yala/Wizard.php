<?php

class Yala_Wizard
{
    const SESSION_NAMESPACE = 'Yala_Wizard';

    /**
     * @var array
     */
    protected static $wizardNames = array(
        'FirstSteps'
    );

    /**
     * @var array
     */
    protected $registeredWizards = array();

    /**
     * @var Yala_Wizard
     */
    protected static $instance;

    /**
     * @var \Zend_Session_Namespace
     */
    protected $session;

    /**
     * @static
     * @return Yala_Wizard
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->session = new Zend_Session_Namespace(self::SESSION_NAMESPACE);
    }

    public function getWizards()
    {
        if (empty($this->registeredWizards)) {
            $lastCheck = $this->session->lastCheck;
            if (!$lastCheck || $lastCheck - 600 < time()) {
                foreach (self::$wizardNames as $wizardName) {
                    $this->registerWizard($wizardName);
                }
            }
        }
        return $this->getRegisteredWizards();

    }

    protected function registerWizard($wizardName)
    {
        $className = 'Yala_Wizard_' . $wizardName;
        $wizard = new $className();
        if ($wizard->check()) {
            $this->registeredWizards[$wizardName] = $wizard;
        }
    }

    protected function getRegisteredWizards()
    {
        
    }
}