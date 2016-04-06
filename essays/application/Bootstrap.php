<?php

class Bootstrap extends GN_Bootstrap
{
    protected $_sessionNamespace = 'Tests';

	protected function _initOpenID()
	{
		define('Auth_OpenID_RAND_SOURCE', null);
	}

    protected function _initAcl()
    {
        $this->bootstrap('cache')->bootstrap('db');

        $acl = new GN_Plugin_Acl();
        $acl->setRoleName(0);
        if (Zend_Auth::getInstance()->hasIdentity()) {
            $id = Zend_Auth::getInstance()->getIdentity();
            $model = new Model_Users();
            $user = $model->find($id)->current();
            if ($user) {
                $acl->setRoleName($user->role);
            } else {
                Zend_Auth::getInstance()->clearIdentity();
            }
        }
        $this->bootstrap('frontcontroller')->getResource('frontcontroller')->registerPlugin($acl);
        return $acl;
    }

    /**
     * @return Zend_Translate
     * @throws Exception
     */
    public function _initLanguage()
    {
        $translate = new Zend_Translate(array(
            'adapter' => Zend_Translate::AN_ARRAY,
            'content' => APPLICATION_PATH . '/language',
            'scan' => Zend_Translate::LOCALE_FILENAME,
			'disableNotices' => true
        ));
        $adapter = $translate->getAdapter();
        $session = new Zend_Session_Namespace('language');

        $id = Zend_Auth::getInstance()->getIdentity();
        $model = new Model_Users();
        $user = $model->find($id)->current();

		if (isset($_GET['lang'])) {
			$session->language = $_GET['lang'];
		} elseif ($user and $user->language) {
			$session->language = $user->language;
		} elseif (isset($session->preferredLanguage)) {
			$session->language = $session->preferredLanguage;
			unset($session->preferredLanguage);
		} else if (!isset($session->language)) {
			if (isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) and preg_match('/pl/i', $_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
				$session->language = 'pl';
			} else {
				$session->language = 'en';
			}
		}

		if ($user) {
			$user->language = $session->language;
			$user->save();
		}

		if ($adapter->isAvailable($session->language)) {
			$locale = new Zend_Locale($session->language);
		} else {
			$locale = new Zend_Locale('en');
		}
		$adapter->setLocale($locale);

        Zend_Registry::set('Bootstrap', $this);
        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);

        return $translate;
    }
}

