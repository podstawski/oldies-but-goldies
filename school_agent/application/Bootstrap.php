<?php
/**
 * @author <radoslaw.szczepaniak@gammanet.pl> Radosław Szczepaniak
 */

class Bootstrap extends GN_Bootstrap
{
    /**
     * @var string
     */
    protected $_sessionNamespace = 'ClassGroup';

    /**
     * @return Zend_Translate
     * @throws Exception
     */
    public function _initLanguage()
    {
        $translate = new Zend_Translate(array(
            'adapter' => Zend_Translate::AN_ARRAY,
            'content' => APPLICATION_PATH . '/language',
            'scan'    => Zend_Translate::LOCALE_FILENAME,
			'disableNotices' => true
        ));
        $adapter = $translate->getAdapter();
        $session = new Zend_Session_Namespace('language');
		if (isset($_GET['lang'])) {
			$session->language = $_GET['lang'];
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

		if ($adapter->isAvailable($session->language)) {
			$locale = new Zend_Locale($session->language);
		} else {
			$locale = new Zend_Locale('en');
			$session->language = 'en';
		}
		$adapter->setLocale($locale);

        Zend_Registry::set('Zend_Locale', $locale);
        Zend_Registry::set('Zend_Translate', $translate);

        return $translate;
    }
}

