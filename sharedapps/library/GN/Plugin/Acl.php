<?php
/*
 * @author Radosław Szczepaniak <simer@gammanet.pl>
 */

class GN_Plugin_Acl extends Zend_Controller_Plugin_Abstract
{
    /**
     * @var \Zend_Acl
     */
    protected $_acl;

    /**
     * @var array
     */
    protected $_roles = array();

    /**
     * @var string
     */
    protected $_roleName;

    /**
     * @var Zend_Controller_Request_Abstract
     */
    protected $_request;

    public function __construct()
    {
        if (APPLICATION_ENV === 'development' || (Zend_Registry::isRegistered('cache') && $this->_acl = Zend_Registry::get('cache')->load('acl')) === false) {
            $this->_acl = new Zend_Acl();

            $config = new Zend_Config_Yaml(APPLICATION_PATH . '/configs/acl.yaml');
            foreach ($config->toArray() as $role => $definition) {
                if ($this->_acl->hasRole($role) == false) {
                    $this->_acl->addRole($role);
                }
                foreach ($definition as $resource => $allows) {
                    if ($this->_acl->has($resource) == false) {
                        $this->_acl->addResource($resource);
                    }
                    if ($allows == '*') {
                        $allows = null;
                    } else {
                        $allows = array_map('trim', explode(',', $allows));
                    }
                    $this->_acl->allow($role, $resource, $allows);
                }
            }
            if (Zend_Registry::isRegistered('cache')) {
                Zend_Registry::get('cache')->save($this->_acl, 'acl');
            }
        }
        $this->_roles = $this->_acl->getRoles();
    }

    /**
     * @return Zend_Acl
     */
    public function getAcl()
    {
        return $this->_acl;
    }

    /**
     * @param string|int $roleName
     * @throws Exception
     */
    public function setRoleName($roleName)
    {
        if (is_null($roleName)) {
            $roleName = 0;
        }
        if (is_numeric($roleName) && array_key_exists($roleName, $this->_roles)) {
            $roleName = $this->_roles[$roleName];
        } else if (array_search($roleName, $this->_roles) === false) {
            throw new Exception('Invalid role');
        }
        $this->_roleName = $roleName;
    }

    /**
     * @return string
     */
    public function getRoleName()
    {
        return $this->_roleName;
    }

    /**
     * @param Zend_Controller_Request_Abstract $request
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $this->_request = $request;
        $controllerName = str_replace('-', '_', $request->getControllerName());
        if ($this->_acl->has($controllerName) == false) {
            $this->denyAccess(true);
        } else if ($this->_acl->isAllowed($this->getRoleName(), $controllerName, $request->getActionName()) == false) {
            $this->denyAccess();
        }
    }

    /**
     * @param bool $force
     */
    public function denyAccess($force = false)
	{
		$flashMessenger = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
		$redirector     = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
        
		if ($force || Zend_Auth::getInstance()->hasIdentity()) {
            $message = is_string($force)
                     ? $force
                     : 'you dont have access to this page';
			$fc = Zend_Controller_Front::getInstance();
			$redirector->setGotoSimple($fc->getDefaultAction(), $fc->getDefaultControllerName());
		}
		else {
			$message = 'you have to log in';
			$redirector->setGotoSimple('login', 'user');
		}

		if (Zend_Registry::isRegistered('Zend_Translate') && $translator = Zend_Registry::get('Zend_Translate')) {
			$message = $translator->translate($message);
		}
		$flashMessenger->addMessage($message);
		$redirector->redirectAndExit();
	}
}

?>
